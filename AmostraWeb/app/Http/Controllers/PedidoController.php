<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePedidoRequest;
use App\Http\Requests\UpdatePedidoRequest;
use App\Models\Cliente;
use App\Models\Pedido;
use App\Models\Produto;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PedidoController extends Controller
{
    /**
     * Lista com filtros (RF006 / RF007), respeitando o escopo do usuário (RN017).
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $filtros = $request->only(['busca', 'status', 'vendedor', 'de', 'ate']);

        $pedidos = Pedido::query()
            ->visivelPara($user)
            ->with(['cliente', 'estabelecimento', 'user'])
            ->withCount([
                'amostras',
                'amostras as amostras_em_aberto_count' => fn (Builder $q) => $q->emAberto(),
            ])
            ->when($filtros['busca'] ?? null, fn (Builder $q, $termo) => $q->where(fn (Builder $q) => $q
                ->where('codigo', 'like', "%{$termo}%")
                ->orWhereHas('cliente', fn (Builder $c) => $c->busca($termo))))
            ->when($filtros['status'] ?? null, fn (Builder $q, $status) => $q->where('status', $status))
            ->when($filtros['vendedor'] ?? null, fn (Builder $q, $id) => $q->where('user_id', $id))
            ->when($filtros['de'] ?? null, fn (Builder $q, $data) => $q->whereDate('data_emissao', '>=', $data))
            ->when($filtros['ate'] ?? null, fn (Builder $q, $data) => $q->whereDate('data_emissao', '<=', $data))
            ->latest('data_emissao')
            ->latest('id')
            ->paginate($this->porPagina())
            ->withQueryString();

        return view('pedidos.index', [
            'pedidos' => $pedidos,
            'filtros' => $filtros,
            'vendedores' => $user->isVendedor() ? collect() : $this->vendedores($user),
        ]);
    }

    /**
     * Cadastro do pedido com as amostras (um item por produto).
     */
    public function create(Request $request)
    {
        return view('pedidos.create', [
            'clientes' => Cliente::where('status', true)->orderBy('razao_social')->get(),
            'produtos' => Produto::where('status', true)->orderBy('codigo_produto')->get(),
            'vendedores' => $this->vendedores($request->user()),
        ]);
    }

    /**
     * Cria o pedido e uma amostra para cada produto informado.
     * O estabelecimento vem do vendedor responsável (RN018).
     */
    public function store(StorePedidoRequest $request)
    {
        $dados = $request->validated();
        $responsavel = User::findOrFail($dados['user_id']);
        $this->garantirVendedorNoEscopo($request->user(), $responsavel);

        $pedido = DB::transaction(function () use ($dados, $responsavel) {
            $pedido = Pedido::create([
                'cliente_id' => $dados['cliente_id'],
                'user_id' => $responsavel->id,
                'estabelecimento_id' => $responsavel->estabelecimento_id,
                'observacao' => $dados['observacao'] ?? null,
                'data_emissao' => $dados['data_emissao'],
                'status' => 'aberto',
            ]);

            foreach ($dados['amostras'] as $item) {
                $pedido->amostras()->create([
                    'produto_id' => $item['produto_id'],
                    'quantidade' => $item['quantidade'],
                    'prazo_avaliacao' => $item['prazo_avaliacao'],
                    'status' => 'em_analise',
                ]);
            }

            return $pedido;
        });

        $total = count($dados['amostras']);
        return redirect()->route('pedidos.show', $pedido)
            ->with('success', 'Pedido '.$pedido->codigo.' cadastrado com '.$total.' '.($total === 1 ? 'amostra' : 'amostras').'.');
    }

    /**
     * Detalhes com todas as amostras (RF008). O histórico fica no módulo de logs.
     */
    public function show(Pedido $pedido)
    {
        $this->autorizarEscopo($pedido);
        $pedido->load(['cliente', 'estabelecimento', 'user', 'amostras' => fn ($q) => $q->with('produto')->orderBy('codigo')]);

        return view('pedidos.show', [
            'pedido' => $pedido,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Pedido $pedido)
    {
        $this->autorizarEscopo($pedido);

        return view('pedidos.edit', [
            'pedido' => $pedido,
            'clientes' => Cliente::where('status', true)->orderBy('razao_social')->get(),
            'vendedores' => $this->vendedores($request->user()),
            'podeTrocarResponsavel' => ! $request->user()->isVendedor(),
        ]);
    }

    /**
     * Altera as informações permitidas (RF009). O status é calculado pelas amostras.
     */
    public function update(UpdatePedidoRequest $request, Pedido $pedido)
    {
        $this->autorizarEscopo($pedido);
        $dados = $request->validated();

        // trocou o responsável: o estabelecimento acompanha o novo vendedor (RN004 / RN018)
        if (isset($dados['user_id']) && (int) $dados['user_id'] !== (int) $pedido->user_id) {
            $novo = User::findOrFail($dados['user_id']);
            $this->garantirVendedorNoEscopo($request->user(), $novo);
            $dados['estabelecimento_id'] = $novo->estabelecimento_id;
        }

        $pedido->update($dados);
        return redirect()->route('pedidos.show', $pedido)->with('success', 'Pedido '.$pedido->codigo.' atualizado.');
    }

    /**
     * Vendedores que o usuário pode escolher como responsável:
     * admin - todos; gestor - os dos seus estabelecimentos; vendedor - ele mesmo.
     */
    private function vendedores(User $user)
    {
        return User::with('estabelecimento')
            ->where('cargo', 'representante')
            ->where('status', true)
            ->when($user->isGestor(), fn (Builder $q) => $q->whereIn('estabelecimento_id', $user->estabelecimentosVisiveis()))
            ->when($user->isVendedor(), fn (Builder $q) => $q->whereKey($user->id))
            ->orderBy('name')
            ->get();
    }

    private function garantirVendedorNoEscopo(User $user, User $vendedor): void
    {
        if ($user->isGestor() && ! in_array((int) $vendedor->estabelecimento_id, $user->estabelecimentosVisiveis(), true)) {
            abort(403, 'Este vendedor não pertence a um estabelecimento sob sua gestão.');
        }
    }
}
