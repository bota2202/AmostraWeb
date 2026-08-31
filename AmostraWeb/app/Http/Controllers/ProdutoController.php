<?php

namespace App\Http\Controllers;

use App\Models\Produto;
use Illuminate\Http\Request;

class ProdutoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $produtos = Produto::all();
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
    public function store(Request $request)
    {
        $dados = $request->validate([
            'codigo_produto' => 'required|unique:produtos,codigo_produto',
            'descricao' => 'required',
            'familia' => 'nullable',
            'subfamilia' => 'nullable',
            'grupo' => 'nullable',
            'categoria_produto' => 'nullable',
            'nbs' => 'nullable',
            'tipo' => 'nullable',
            'ncm' => 'nullable',
            'servico' => 'nullable',
            'cest' => 'nullable',
            'anp' => 'nullable',
            'aplicacao_entrada' => 'nullable',
            'aplicacao_saida' => 'nullable',
            'origem_mercadoria' => 'nullable',
            'controle_verba' => 'nullable',
            'status' => 'boolean|nullable',
            'marca' => 'nullable',
            'data_revisao' => 'nullable',
            'classe' => 'nullable',
            'ca' => 'nullable',
            'id_categoria' => 'nullable',
            'id_preco' => 'nullable',
            'tributacao_municipio' => 'nullable',
        ]);

        Produto::create($dados);

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
    public function update(Request $request, Produto $produto)
    {
        $dados = $request->validate([
            'descricao' => 'required',
            'familia' => 'nullable',
            'subfamilia' => 'nullable',
            'grupo' => 'nullable',
            'categoria_produto' => 'nullable',
            'nbs' => 'nullable',
            'tipo' => 'nullable',
            'ncm' => 'nullable',
            'servico' => 'nullable',
            'cest' => 'nullable',
            'anp' => 'nullable',
            'aplicacao_entrada' => 'nullable',
            'aplicacao_saida' => 'nullable',
            'origem_mercadoria' => 'nullable',
            'controle_verba' => 'nullable',
            'status' => 'boolean|nullable',
            'marca' => 'nullable',
            'data_revisao' => 'nullable',
            'classe' => 'nullable',
            'ca' => 'nullable',
            'id_categoria' => 'nullable',
            'id_preco' => 'nullable',
            'tributacao_municipio' => 'nullable',
        ]);

        $produto->update($dados);

        return redirect('/produtos')->with('success', 'Produto de código ' . $produto->codigo_produto . ' atualizado com sucesso');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Produto $produto)
    {
        $codigo = $produto->codigo_produto;
        $produto->delete();

        return redirect('/produtos')->with('success','Produto de código '. $codigo. ' excluido com sucesso');
    }
}
