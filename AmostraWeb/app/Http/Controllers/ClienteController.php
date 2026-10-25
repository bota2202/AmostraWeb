<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChangeStatusRequest;
use App\Http\Requests\StoreClienteRequest;
use App\Http\Requests\UpdateClienteRequest;
use App\Models\Cliente;
use App\Models\Pedido;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    /**
     * Lista com busca e filtro de status (RF038).
     */
    public function index(Request $request)
    {
        $filtros = $request->only(['busca', 'status']);

        $clientes = Cliente::query()
            ->busca($filtros['busca'] ?? null)
            ->when(isset($filtros['status']) && $filtros['status'] !== '', fn (Builder $q) => $q->where('status', (bool) $filtros['status']))
            ->orderBy('razao_social')
            ->paginate($this->porPagina())
            ->withQueryString();

        return view('clientes.index', [
            'clientes' => $clientes,
            'filtros' => $filtros,
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
        $cliente = Cliente::create($request->validated());
        return redirect()->route('clientes.show', $cliente)->with('success', 'Cliente '.$cliente->razao_social.' cadastrado.');
    }

    /**
     * Detalhes + últimos pedidos que o usuário pode ver.
     */
    public function show(Request $request, Cliente $cliente)
    {
        $pedidos = Pedido::query()
            ->visivelPara($request->user())
            ->where('cliente_id', $cliente->id)
            ->with(['estabelecimento', 'user'])
            ->withCount('amostras')
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
        return redirect()->route('clientes.show', $cliente)->with('success', 'Cliente '.$cliente->razao_social.' atualizado.');
    }

    /**
     * Ativa ou inativa o cliente (RF039). Inativos não aparecem em novos pedidos.
     */
    public function alterarStatus(ChangeStatusRequest $request, Cliente $cliente)
    {
        $cliente->update($request->validated());
        $texto = $cliente->status ? 'ativado' : 'inativado';
        return redirect()->route('clientes.edit', $cliente)->with('success', 'Cliente '.$texto.'.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cliente $cliente)
    {
        if ($cliente->pedidos()->exists()) {
            return back()->with('error', 'Não é possível excluir: o cliente possui pedidos. Inative-o em vez de excluir.');
        }

        $nome = $cliente->razao_social;
        $cliente->delete();
        return redirect()->route('clientes.index')->with('success', 'Cliente '.$nome.' excluído.');
    }
}
