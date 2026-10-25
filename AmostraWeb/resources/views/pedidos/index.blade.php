@extends('layouts.app')

@section('title','Pedidos - '.config('amostraweb.name'))

@section('texto','Pedidos')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/layout/table.css') }}">
<link rel="stylesheet" href="{{ asset('css/layout/filtros.css') }}">
@endpush

@section('content')
<div class="toolbar">
    <h1>Pedidos</h1>
    @permite('pedidos', 'criar')
    <a href="{{ route('pedidos.create') }}" class="botao-toolbar">
        <i class="fa-solid fa-plus" aria-hidden="true"></i> Novo pedido
    </a>
    @endpermite
</div>

<form method="GET" action="{{ route('pedidos.index') }}" class="filtros" role="search" aria-label="Filtrar pedidos">
    <div class="filtro filtro-busca">
        <label for="busca">Buscar</label>
        <input type="search" id="busca" name="busca" value="{{ $filtros['busca'] ?? '' }}" placeholder="Código do pedido, cliente ou CNPJ/CPF">
    </div>
    <div class="filtro">
        <label for="status">Situação</label>
        <select id="status" name="status">
            <option value="">Todas</option>
            @foreach (\App\Models\Pedido::STATUS as $valor => $nome)
            <option value="{{ $valor }}" @selected(($filtros['status'] ?? '') === $valor)>{{ $nome }}</option>
            @endforeach
        </select>
    </div>
    @if ($vendedores->isNotEmpty())
    <div class="filtro">
        <label for="vendedor">Vendedor</label>
        <select id="vendedor" name="vendedor">
            <option value="">Todos</option>
            @foreach ($vendedores as $vendedor)
            <option value="{{ $vendedor->id }}" @selected(($filtros['vendedor'] ?? '') == $vendedor->id)>{{ $vendedor->name }}</option>
            @endforeach
        </select>
    </div>
    @endif
    <div class="filtro">
        <label for="de">Enviado de</label>
        <input type="date" id="de" name="de" value="{{ $filtros['de'] ?? '' }}">
    </div>
    <div class="filtro">
        <label for="ate">até</label>
        <input type="date" id="ate" name="ate" value="{{ $filtros['ate'] ?? '' }}">
    </div>
    <div class="filtro-acoes">
        <button type="submit"><i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i> Filtrar</button>
        @if (array_filter($filtros))
        <a href="{{ route('pedidos.index') }}">Limpar</a>
        @endif
    </div>
</form>

<table>
    <caption class="sr-only">Lista de pedidos</caption>
    <thead>
        <tr>
            <th scope="col">Código</th>
            <th scope="col">Envio</th>
            <th scope="col">Cliente</th>
            <th scope="col">Vendedor</th>
            <th scope="col">Estabelecimento</th>
            <th scope="col">Amostras</th>
            <th scope="col">Situação</th>
            <th scope="col" class="acoes">Ações</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($pedidos as $pedido)
        <tr>
            <td class="centro">{{ $pedido->codigo }}</td>
            <td class="centro nao-quebra">{{ $pedido->data_emissao->format('d/m/Y') }}</td>
            <td>{{ $pedido->cliente->razao_social }}</td>
            <td>{{ $pedido->user->name }}</td>
            <td>{{ $pedido->estabelecimento->razao_social }}</td>
            <td class="centro">
                {{ $pedido->amostras_count }}
                @if ($pedido->amostras_em_aberto_count > 0)
                <span class="texto-secundario">{{ $pedido->amostras_em_aberto_count }} em avaliação</span>
                @endif
            </td>
            <td class="centro"><span class="status status-{{ $pedido->status }}">{{ $pedido->status_nome }}</span></td>
            <td class="acoes">
                <a href="{{ route('pedidos.show', $pedido) }}" title="Ver pedido {{ $pedido->codigo }}"><i class="fa-solid fa-eye" aria-hidden="true"></i><span class="sr-only">Ver pedido {{ $pedido->codigo }}</span></a>
                @permite('pedidos', 'editar')
                <a href="{{ route('pedidos.edit', $pedido) }}" title="Editar pedido {{ $pedido->codigo }}"><i class="fa-solid fa-pen" aria-hidden="true"></i><span class="sr-only">Editar pedido {{ $pedido->codigo }}</span></a>
                @endpermite
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="8" class="vazio">
                {{ array_filter($filtros) ? 'Nenhum pedido encontrado com esses filtros.' : 'Nenhum pedido cadastrado.' }}
            </td>
        </tr>
        @endforelse
    </tbody>
</table>
{{ $pedidos->links() }}
@endsection
