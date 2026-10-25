<?php

namespace App\Http\Controllers;

use App\Models\Produto;
use App\Http\Requests\ChangeStatusRequest;
use App\Http\Requests\StoreProdutoRequest;
use App\Http\Requests\UpdateProdutoRequest;

class ProdutoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $produtos = Produto::orderBy('codigo_produto')->paginate(20);
        return view('produtos.index', [
            'produtos' => $produtos,
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
        Produto::create($request->validated());

        return redirect('/produtos')->with('success', 'Produto cadastrado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Produto $produto)
    {
        return view('produtos.show', [
            'produto' => $produto,
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
        return redirect('/produtos')->with('success', 'Produto de código ' . $produto->codigo_produto . ' atualizado com sucesso');
    }

    /**
     * Ativa ou inativa o produto.
     */
    public function alterarStatus(ChangeStatusRequest $request, Produto $produto)
    {
        $produto->update($request->validated());
        $texto = $produto->status ? 'ativado' : 'inativado';
        return redirect()->route('produtos.edit', $produto)->with('success', 'Produto '.$texto.' com sucesso');
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

        return redirect('/produtos')->with('success','Produto de código '. $codigo. ' excluído com sucesso');
    }
}
