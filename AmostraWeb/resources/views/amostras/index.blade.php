@extends('layouts.app')

@section('title','Amostras - '.config('amostraweb.name'))

@section('texto','Amostras')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/layout/table.css') }}">
<link rel="stylesheet" href="{{ asset('css/layout/filtros.css') }}">
@endpush

@section('content')
<div class="toolbar">
    <h1>Amostras</h1>
    @permite('pedidos', 'criar')
    <a href="{{ route('pedidos.create') }}" class="botao-toolbar" title="As amostras são cadastradas junto com o pedido">
        <i class="fa-solid fa-plus" aria-hidden="true"></i> Novo pedido com amostras
    </a>
    @endpermite
</div>

<form method="GET" action="{{ route('amostras.index') }}" class="filtros" role="search" aria-label="Filtrar amostras">
    <div class="filtro filtro-busca">
        <label for="busca">Buscar</label>
        <input type="search" id="busca" name="busca" value="{{ $filtros['busca'] ?? '' }}" placeholder="Código da amostra ou do pedido, produto ou cliente">
    </div>
    <div class="filtro">
        <label for="status">Status</label>
        <select id="status" name="status">
            <option value="">Todos</option>
            @foreach (\App\Models\Amostra::STATUS as $valor => $nome)
            <option value="{{ $valor }}" @selected(($filtros['status'] ?? '') === $valor)>{{ $nome }}</option>
            @endforeach
        </select>
    </div>
    <div class="filtro">
        <label for="prazo">Prazo</label>
        <select id="prazo" name="prazo">
            <option value="">Qualquer</option>
            <option value="vencido" @selected(($filtros['prazo'] ?? '') === 'vencido')>Vencido (atrasadas)</option>
            <option value="proximo" @selected(($filtros['prazo'] ?? '') === 'proximo')>Vence em até {{ $diasAlerta }} dias</option>
        </select>
    </div>
    <div class="filtro-acoes">
        <button type="submit"><i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i> Filtrar</button>
        @if (array_filter($filtros))
        <a href="{{ route('amostras.index') }}">Limpar</a>
        @endif
    </div>
</form>

<table>
    <caption class="sr-only">Lista de amostras</caption>
    <thead>
        <tr>
            <th scope="col">Código</th>
            <th scope="col">Produto</th>
            <th scope="col">Cliente</th>
            <th scope="col">Pedido</th>
            <th scope="col">Qtd.</th>
            <th scope="col">Prazo</th>
            <th scope="col">Status</th>
            <th scope="col" class="acoes">Ações</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($amostras as $amostra)
        <tr>
            <td class="centro">{{ $amostra->codigo }}</td>
            <td>
                {{ $amostra->produto->descricao }}
                <span class="texto-secundario">{{ $amostra->produto->codigo_produto }}</span>
            </td>
            <td>{{ $amostra->pedido?->cliente?->razao_social ?? "-" }}</td>
            <td class="centro">@if ($amostra->pedido)<a href="{{ route('pedidos.show', $amostra->pedido) }}">{{ $amostra->pedido->codigo }}</a>@else Sem pedido @endif</td>
            <td class="centro">{{ $amostra->quantidade }}</td>
            <td class="centro nao-quebra {{ $amostra->prazoVencido() ? 'prazo-vencido' : '' }}">
                @if ($amostra->prazoVencido())<i class="fa-solid fa-circle-exclamation" aria-hidden="true"></i>@endif
                {{ $amostra->prazo_avaliacao->format('d/m/Y') }}
                @if ($amostra->prazoVencido())<span class="sr-only"> (vencido)</span>@endif
            </td>
            <td class="centro"><span class="status status-{{ $amostra->status }}">{{ $amostra->status_nome }}</span></td>
            <td class="acoes">
                <a href="{{ route('amostras.show', $amostra) }}" title="Ver amostra {{ $amostra->codigo }}"><i class="fa-solid fa-eye" aria-hidden="true"></i><span class="sr-only">Ver amostra {{ $amostra->codigo }}</span></a>
                @if ($amostra->emAberto())
                @permite('amostras', 'editar')
                <a href="{{ route('amostras.edit', $amostra) }}" title="Editar amostra {{ $amostra->codigo }}"><i class="fa-solid fa-pen" aria-hidden="true"></i><span class="sr-only">Editar amostra {{ $amostra->codigo }}</span></a>
                @endpermite
                @endif
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="8" class="vazio">
                {{ array_filter($filtros) ? 'Nenhuma amostra encontrada com esses filtros.' : 'Nenhuma amostra cadastrada. As amostras são criadas no cadastro do pedido.' }}
            </td>
        </tr>
        @endforelse
    </tbody>
</table>
{{ $amostras->links() }}
@endsection
