<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChangeStatusRequest;
use App\Http\Requests\StoreClienteRequest;
use App\Http\Requests\UpdateClienteRequest;
use App\Models\Cliente;

class ClienteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $clientes = Cliente::orderBy('razao_social')->paginate(20);
        return view('clientes.index', [
            'clientes' => $clientes,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('clientes.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreClienteRequest $request)
    {
        Cliente::create($request->validated());
        return redirect()->route('clientes.index')->with('success', 'Cliente cadastrado com sucesso');
    }

    /**
     * Display the specified resource.
     */
    public function show(Cliente $cliente)
    {
        $pedidos = $cliente->pedidos()
            ->with(['estabelecimento', 'user'])
            ->latest('data_emissao')
            ->limit(10)
            ->get();

        return view('clientes.show', [
            'cliente' => $cliente,
            'pedidos' => $pedidos,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Cliente $cliente)
    {
        return view('clientes.edit', [
            'cliente' => $cliente,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateClienteRequest $request, Cliente $cliente)
    {
        $cliente->update($request->validated());
        return redirect()->route('clientes.index')->with('success', 'Cliente de código '.$cliente->codigo_empresa.' atualizado com sucesso');
    }

    /**
     * Ativa ou inativa o cliente.
     */
    public function alterarStatus(ChangeStatusRequest $request, Cliente $cliente)
    {
        $cliente->update($request->validated());
        $texto = $cliente->status ? 'ativado' : 'inativado';
        return redirect()->route('clientes.edit', $cliente)->with('success', 'Cliente '.$texto.' com sucesso');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cliente $cliente)
    {
        if ($cliente->pedidos()->exists()) {
            return back()->with('error', 'Não é possível excluir: o cliente possui pedidos. Inative-o em vez de excluir.');
        }

        $codigo = $cliente->codigo_empresa;
        $cliente->delete();
        return redirect()->route('clientes.index')->with('success', 'Cliente de código '.$codigo.' excluído com sucesso');
    }
}
