@extends('layouts.app')

@section('title','Produtos - '.config('amostraweb.name'))

@section('texto','Produtos')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/layout/table.css') }}">
@endpush

@section('content')
<div class="toolbar">
    <h1>Produtos</h1>
    <a href="{{ route('produtos.create') }}" class="botao-toolbar">
        <i class="fa-solid fa-plus"></i> Novo Produto
    </a>
</div>

<table>
    <thead>
        <tr>
            <th>Código</th>
            <th>Descrição</th>
            <th>Família</th>
            <th>Marca</th>
            <th>NCM</th>
            <th>Status</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($produtos as $produto)
        <tr>
            <td class="centro">{{ $produto->codigo_produto }}</td>
            <td>{{ $produto->descricao }}</td>
            <td class="centro">{{ $produto->familia ?? '-' }}</td>
            <td class="centro">{{ $produto->marca ?? '-' }}</td>
            <td class="centro">{{ $produto->ncm ?? '-' }}</td>
            <td class="centro"><span class="status status-{{ $produto->status ? 'ativo' : 'inativo' }}">{{ $produto->status ? 'Ativo' : 'Inativo' }}</span></td>
            <td class="acoes">
                <a href="{{ route('produtos.show',$produto) }}" title="Ver detalhes"><i class="fa-solid fa-eye"></i></a>
                <a href="{{ route('produtos.edit',$produto) }}" title="Editar"><i class="fa-solid fa-pen"></i></a>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="7" class="vazio">Nenhum produto cadastrado</td>
        </tr>
        @endforelse
    </tbody>
</table>
{{ $produtos->links() }}
@endsection
