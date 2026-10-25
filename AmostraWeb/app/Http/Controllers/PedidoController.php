<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePedidoRequest;
use App\Http\Requests\UpdatePedidoRequest;
use App\Models\Cliente;
use App\Models\Estabelecimento;
use App\Models\Pedido;

class PedidoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pedidos = Pedido::with(['cliente', 'estabelecimento', 'user'])
            ->withCount('amostras')
            ->latest('data_emissao')
            ->paginate(20);

        return view('pedidos.index', [
            'pedidos' => $pedidos,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pedidos.create', $this->dadosFormulario());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePedidoRequest $request)
    {
        $pedido = Pedido::create([
            ...$request->validated(),
            'user_id' => $request->user()->id,
            'status' => 'aberto',
        ]);

        return redirect()->route('pedidos.show', $pedido)->with('success', 'Pedido '.$pedido->codigo.' cadastrado com sucesso');
    }

    /**
     * Display the specified resource.
     */
    public function show(Pedido $pedido)
    {
        $pedido->load(['cliente', 'estabelecimento', 'user', 'amostras.produto']);

        return view('pedidos.show', [
            'pedido' => $pedido,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pedido $pedido)
    {
        return view('pedidos.edit', [
            'pedido' => $pedido,
            ...$this->dadosFormulario(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePedidoRequest $request, Pedido $pedido)
    {
        $pedido->update($request->validated());
        return redirect()->route('pedidos.show', $pedido)->with('success', 'Pedido '.$pedido->codigo.' atualizado com sucesso');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pedido $pedido)
    {
        if ($pedido->amostras()->exists()) {
            return back()->with('error', 'Não é possível excluir: o pedido possui amostras vinculadas.');
        }

        $codigo = $pedido->codigo;
        $pedido->delete();
        return redirect()->route('pedidos.index')->with('success', 'Pedido '.$codigo.' excluído com sucesso');
    }

    private function dadosFormulario(): array
    {
        return [
            'clientes' => Cliente::where('status', true)->orderBy('razao_social')->get(),
            'estabelecimentos' => Estabelecimento::where('status', true)->orderBy('razao_social')->get(),
        ];
    }
}
