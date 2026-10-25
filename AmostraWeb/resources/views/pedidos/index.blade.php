@extends('layouts.app')

@section('title','Pedidos - '.config('amostraweb.name'))

@section('texto','Pedidos')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/layout/table.css') }}">
@endpush

@section('content')
<div class="toolbar">
    <h1>Pedidos</h1>
    <a href="{{ route('pedidos.create') }}" class="botao-toolbar">
        <i class="fa-solid fa-plus"></i> Novo Pedido
    </a>
</div>

<table>
    <thead>
        <tr>
            <th>Código</th>
            <th>Emissão</th>
            <th>Cliente</th>
            <th>Estabelecimento</th>
            <th>Representante</th>
            <th>Amostras</th>
            <th>Status</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($pedidos as $pedido)
        <tr>
            <td class="centro">{{ $pedido->codigo }}</td>
            <td class="centro">{{ $pedido->data_emissao->format('d/m/Y') }}</td>
            <td>{{ $pedido->cliente->razao_social }}</td>
            <td>{{ $pedido->estabelecimento->razao_social }}</td>
            <td class="centro">{{ $pedido->user->codigo_usuario }}</td>
            <td class="centro">{{ $pedido->amostras_count }}</td>
            <td class="centro"><span class="status status-{{ $pedido->status }}">{{ \App\Models\Pedido::STATUS[$pedido->status] }}</span></td>
            <td class="acoes">
                <a href="{{ route('pedidos.show', $pedido) }}" title="Ver detalhes"><i class="fa-solid fa-eye"></i></a>
                <a href="{{ route('pedidos.edit', $pedido) }}" title="Editar"><i class="fa-solid fa-pen"></i></a>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="8" class="vazio">Nenhum pedido cadastrado</td>
        </tr>
        @endforelse
    </tbody>
</table>
{{ $pedidos->links() }}
@endsection
