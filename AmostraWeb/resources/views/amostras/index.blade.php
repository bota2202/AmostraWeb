@extends('layouts.app')

@section('title','Amostras - '.config('amostraweb.name'))

@section('texto','Amostras')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/layout/table.css') }}">
@endpush

@section('content')
<div class="toolbar">
    <h1>Amostras</h1>
    <a href="{{ route('amostras.create') }}" class="botao-toolbar">
        <i class="fa-solid fa-plus"></i> Nova Amostra
    </a>
</div>

<table>
    <thead>
        <tr>
            <th>Código</th>
            <th>Produto</th>
            <th>Pedido</th>
            <th>Quantidade</th>
            <th>Prazo de avaliação</th>
            <th>Status</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($amostras as $amostra)
        <tr>
            <td class="centro">{{ $amostra->codigo }}</td>
            <td>{{ $amostra->produto->codigo_produto }} - {{ $amostra->produto->descricao }}</td>
            <td class="centro">{{ $amostra->pedido?->codigo ?? '-' }}</td>
            <td class="centro">{{ $amostra->quantidade }}</td>
            <td class="centro {{ $amostra->prazoVencido() ? 'prazo-vencido' : '' }}" @if ($amostra->prazoVencido()) title="Prazo vencido" @endif>
                {{ $amostra->prazo_avaliacao->format('d/m/Y') }}
            </td>
            <td class="centro"><span class="status status-{{ $amostra->status }}">{{ \App\Models\Amostra::STATUS[$amostra->status] }}</span></td>
            <td class="acoes">
                <a href="{{ route('amostras.show', $amostra) }}" title="Ver detalhes"><i class="fa-solid fa-eye"></i></a>
                <a href="{{ route('amostras.edit', $amostra) }}" title="Editar"><i class="fa-solid fa-pen"></i></a>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="7" class="vazio">Nenhuma amostra cadastrada</td>
        </tr>
        @endforelse
    </tbody>
</table>
{{ $amostras->links() }}
@endsection
