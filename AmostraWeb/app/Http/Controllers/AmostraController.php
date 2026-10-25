<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAmostraRequest;
use App\Http\Requests\UpdateAmostraRequest;
use App\Models\Amostra;
use App\Models\Pedido;
use App\Models\Produto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AmostraController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $amostras = Amostra::with(['produto', 'pedido'])
            ->orderBy('prazo_avaliacao')
            ->paginate(20);

        return view('amostras.index', [
            'amostras' => $amostras,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        return view('amostras.create', [
            ...$this->dadosFormulario(),
            // permite abrir o cadastro já vinculado a um pedido (?pedido_id=)
            'pedidoSelecionado' => $request->integer('pedido_id') ?: null,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAmostraRequest $request)
    {
        // transação para o lock do próximo código valer até o insert
        $amostra = DB::transaction(fn () => Amostra::create([
            ...$request->validated(),
            'status' => 'em_analise',
        ]));

        return redirect()->route('amostras.show', $amostra)->with('success', 'Amostra '.$amostra->codigo.' cadastrada com sucesso');
    }

    /**
     * Display the specified resource.
     */
    public function show(Amostra $amostra)
    {
        $amostra->load([
            'produto',
            'pedido.cliente',
            'solicitacoesPrazo' => fn ($q) => $q->with(['solicitante', 'aprovador'])->latest('data_solicitacao'),
        ]);

        return view('amostras.show', [
            'amostra' => $amostra,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Amostra $amostra)
    {
        return view('amostras.edit', [
            'amostra' => $amostra,
            ...$this->dadosFormulario(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     * O prazo de avaliação só muda por solicitação de prazo aprovada.
     */
    public function update(UpdateAmostraRequest $request, Amostra $amostra)
    {
        $amostra->update($request->validated());
        return redirect()->route('amostras.show', $amostra)->with('success', 'Amostra '.$amostra->codigo.' atualizada com sucesso');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Amostra $amostra)
    {
        if ($amostra->solicitacoesPrazo()->exists()) {
            return back()->with('error', 'Não é possível excluir: a amostra possui solicitações de prazo. Cancele-a em vez de excluir.');
        }

        $codigo = $amostra->codigo;
        $amostra->delete();
        return redirect()->route('amostras.index')->with('success', 'Amostra '.$codigo.' excluída com sucesso');
    }

    private function dadosFormulario(): array
    {
        return [
            'pedidos' => Pedido::with('cliente')->where('status', 'aberto')->latest('data_emissao')->get(),
            'produtos' => Produto::where('status', true)->orderBy('codigo_produto')->get(),
        ];
    }
}
