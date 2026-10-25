<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAmostraRequest;
use App\Http\Requests\UpdateAmostraRequest;
use App\Models\Amostra;
use App\Models\Log;
use App\Models\Pedido;
use App\Models\Produto;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AmostraController extends Controller
{
    /**
     * Lista com filtros (RF011 / RF012), respeitando o escopo do usuário (RN017).
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $filtros = $request->only(['busca', 'status', 'prazo']);
        $diasAlerta = (int) config('amostraweb.dias_alerta_prazo', 7);

        $amostras = Amostra::query()
            ->visivelPara($user)
            ->with(['produto', 'pedido.cliente'])
            ->when($filtros['busca'] ?? null, fn (Builder $q, $termo) => $q->where(fn (Builder $q) => $q
                ->where('codigo', 'like', "%{$termo}%")
                ->orWhereHas('pedido', fn (Builder $p) => $p->where('codigo', 'like', "%{$termo}%")
                    ->orWhereHas('cliente', fn (Builder $c) => $c->busca($termo)))
                ->orWhereHas('produto', fn (Builder $p) => $p->busca($termo))))
            ->when($filtros['status'] ?? null, fn (Builder $q, $status) => $q->where('status', $status))
            ->when(($filtros['prazo'] ?? null) === 'vencido', fn (Builder $q) => $q->atrasadas())
            ->when(($filtros['prazo'] ?? null) === 'proximo', fn (Builder $q) => $q->emAberto()
                ->whereDate('prazo_avaliacao', '>=', today())
                ->whereDate('prazo_avaliacao', '<=', today()->addDays($diasAlerta)))
            // em aberto primeiro, pelo prazo mais próximo
            ->orderByRaw("CASE WHEN status IN ('em_analise', 'atrasada') THEN 0 ELSE 1 END")
            ->orderBy('prazo_avaliacao')
            ->paginate($this->porPagina())
            ->withQueryString();

        return view('amostras.index', [
            'amostras' => $amostras,
            'filtros' => $filtros,
            'diasAlerta' => $diasAlerta,
        ]);
    }

    /**
     * Adiciona uma amostra a um pedido em aberto (RF010).
     * Normalmente aberto pela tela do pedido (?pedido_id=).
     */
    public function create(Request $request)
    {
        return view('amostras.create', [
            'pedidos' => Pedido::query()->visivelPara($request->user())->with('cliente')
                ->where('status', 'aberto')->latest('data_emissao')->get(),
            'produtos' => Produto::where('status', true)->orderBy('codigo_produto')->get(),
            'pedidoSelecionado' => $request->integer('pedido_id') ?: null,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAmostraRequest $request)
    {
        $pedido = Pedido::findOrFail($request->validated('pedido_id'));
        $this->autorizarEscopo($pedido);

        // transação para o lock do próximo código valer até o insert
        $amostra = DB::transaction(fn () => Amostra::create([
            ...$request->validated(),
            'status' => 'em_analise',
        ]));

        return redirect()->route('amostras.show', $amostra)->with('success', 'Amostra '.$amostra->codigo.' adicionada ao pedido '.$pedido->codigo.'.');
    }

    /**
     * Detalhes, solicitações de prazo e histórico (RF013 / RF020).
     */
    public function show(Request $request, Amostra $amostra)
    {
        $this->autorizarEscopo($amostra);

        $amostra->load([
            'produto',
            'pedido.cliente',
            'pedido.user',
            'solicitacoesPrazo' => fn ($q) => $q->with(['solicitante', 'aprovador'])->latest('data_solicitacao'),
        ]);

        $pendente = $amostra->solicitacoesPrazo->firstWhere('situacao', 'pendente');

        return view('amostras.show', [
            'amostra' => $amostra,
            'pendente' => $pendente,
            'motivoNaoDecidir' => $pendente?->motivoParaNaoDecidir($request->user()),
            // quem finalizou a amostra (aprovação, reprovação ou cancelamento)
            'finalizacao' => $amostra->finalizada()
                ? Log::doRegistro($amostra)->where('campo_alterado', 'status')->with('user')->latest('id')->first()
                : null,
        ]);
    }

    /**
     * Só amostras em avaliação podem ser editadas (RN013).
     */
    public function edit(Amostra $amostra)
    {
        $this->autorizarEscopo($amostra);

        if (! $amostra->emAberto()) {
            return redirect()->route('amostras.show', $amostra)
                ->with('error', 'A amostra '.$amostra->codigo.' está '.mb_strtolower($amostra->status_nome).' e não pode mais ser alterada.');
        }

        $amostra->load('pedido.cliente');

        return view('amostras.edit', [
            'amostra' => $amostra,
            'produtos' => Produto::where('status', true)->orderBy('codigo_produto')->get(),
        ]);
    }

    /**
     * Altera produto e quantidade (RF014). Status e prazo têm fluxos próprios.
     */
    public function update(UpdateAmostraRequest $request, Amostra $amostra)
    {
        $this->autorizarEscopo($amostra);

        if (! $amostra->emAberto()) {
            return redirect()->route('amostras.show', $amostra)
                ->with('error', 'A amostra '.$amostra->codigo.' está '.mb_strtolower($amostra->status_nome).' e não pode mais ser alterada.');
        }

        $amostra->update($request->validated());
        return redirect()->route('amostras.show', $amostra)->with('success', 'Amostra '.$amostra->codigo.' atualizada.');
    }

    /**
     * Exclusão permanente: só canceladas e sem deixar o pedido vazio (RN014 / RN023).
     * A exclusão fica registrada na auditoria.
     */
    public function destroy(Amostra $amostra)
    {
        $this->autorizarEscopo($amostra);

        if ($motivo = $amostra->motivoParaNaoExcluir()) {
            return back()->with('error', 'Não é possível excluir: '.$motivo);
        }

        $codigo = $amostra->codigo;
        $pedido = $amostra->pedido;

        DB::transaction(function () use ($amostra) {
            // solicitações de prazo fazem parte da amostra e saem junto (ficam na auditoria)
            $amostra->solicitacoesPrazo()->each(fn ($s) => $s->delete());
            $amostra->observacaoAuditoria = 'Amostra cancelada excluída permanentemente';
            $amostra->delete();
        });

        return redirect()->route('pedidos.show', $pedido)->with('success', 'Amostra '.$codigo.' excluída permanentemente.');
    }
}
