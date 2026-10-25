<?php

namespace App\Http\Controllers;

use App\Models\Produto;
use App\Http\Requests\ChangeStatusRequest;
use App\Http\Requests\StoreProdutoRequest;
use App\Http\Requests\UpdateProdutoRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ProdutoController extends Controller
{
    /**
     * Lista com busca e filtros (RF033).
     */
    public function index(Request $request)
    {
        $filtros = $request->only(['busca', 'familia', 'status']);

        $produtos = Produto::query()
            ->busca($filtros['busca'] ?? null)
            ->when($filtros['familia'] ?? null, fn (Builder $q, $familia) => $q->where('familia', $familia))
            ->when(isset($filtros['status']) && $filtros['status'] !== '', fn (Builder $q) => $q->where('status', (bool) $filtros['status']))
            ->orderBy('codigo_produto')
            ->paginate($this->porPagina())
            ->withQueryString();

        return view('produtos.index', [
            'produtos' => $produtos,
            'filtros' => $filtros,
            'familias' => Produto::whereNotNull('familia')->distinct()->orderBy('familia')->pluck('familia'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('produtos.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProdutoRequest $request)
    {
        $produto = Produto::create($request->validated());

        return redirect()->route('produtos.show', $produto)->with('success', 'Produto '.$produto->codigo_produto.' cadastrado.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Produto $produto)
    {
        return view('produtos.show', [
            'produto' => $produto->loadCount('amostras'),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Produto $produto)
    {
        return view('produtos.edit', [
            'produto' => $produto,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProdutoRequest $request, Produto $produto)
    {
        $produto->update($request->validated());
        return redirect()->route('produtos.show', $produto)->with('success', 'Produto '.$produto->codigo_produto.' atualizado.');
    }

    /**
     * Ativa ou inativa o produto (RF034). Inativos não aparecem em novos pedidos.
     */
    public function alterarStatus(ChangeStatusRequest $request, Produto $produto)
    {
        $produto->update($request->validated());
        $texto = $produto->status ? 'ativado' : 'inativado';
        return redirect()->route('produtos.edit', $produto)->with('success', 'Produto '.$texto.'.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Produto $produto)
    {
        if ($produto->amostras()->exists()) {
            return back()->with('error', 'Não é possível excluir: o produto possui amostras. Inative-o em vez de excluir.');
        }

        $codigo = $produto->codigo_produto;
        $produto->delete();

        return redirect()->route('produtos.index')->with('success', 'Produto '.$codigo.' excluído.');
    }
}
