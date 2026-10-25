@extends('layouts.app')

@section('title','Produtos - '.config('amostraweb.name'))

@section('texto','Produtos')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/layout/table.css') }}">
<link rel="stylesheet" href="{{ asset('css/layout/filtros.css') }}">
@endpush

@php $temFiltro = array_filter($filtros, fn ($v) => $v !== null && $v !== ''); @endphp

@section('content')
<div class="toolbar">
    <h1>Produtos</h1>
    <div class="acoes-toolbar">
        @permite('produtos', 'importar')
        <a href="{{ route('produtos.importar') }}" class="botao-toolbar botao-secundario">
            <i class="fa-solid fa-file-import" aria-hidden="true"></i> Importar do ERP
        </a>
        @endpermite
        @permite('produtos', 'criar')
        <a href="{{ route('produtos.create') }}" class="botao-toolbar">
            <i class="fa-solid fa-plus" aria-hidden="true"></i> Novo produto
        </a>
        @endpermite
    </div>
</div>

<form method="GET" action="{{ route('produtos.index') }}" class="filtros" role="search" aria-label="Filtrar produtos">
    <div class="filtro filtro-busca">
        <label for="busca">Buscar</label>
        <input type="search" id="busca" name="busca" value="{{ $filtros['busca'] ?? '' }}" placeholder="Código, descrição ou marca">
    </div>
    @if ($familias->isNotEmpty())
    <div class="filtro">
        <label for="familia">Família</label>
        <select id="familia" name="familia">
            <option value="">Todas</option>
            @foreach ($familias as $familia)
            <option value="{{ $familia }}" @selected(($filtros['familia'] ?? '') === $familia)>{{ $familia }}</option>
            @endforeach
        </select>
    </div>
    @endif
    <div class="filtro">
        <label for="status">Status</label>
        <select id="status" name="status">
            <option value="">Todos</option>
            <option value="1" @selected(($filtros['status'] ?? '') === '1')>Ativos</option>
            <option value="0" @selected(($filtros['status'] ?? '') === '0')>Inativos</option>
        </select>
    </div>
    <div class="filtro-acoes">
        <button type="submit"><i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i> Filtrar</button>
        @if ($temFiltro)
        <a href="{{ route('produtos.index') }}">Limpar</a>
        @endif
    </div>
</form>

<table>
    <caption class="sr-only">Lista de produtos</caption>
    <thead>
        <tr>
            <th scope="col">Código</th>
            <th scope="col">Descrição</th>
            <th scope="col">Família</th>
            <th scope="col">Marca</th>
            <th scope="col">NCM</th>
            <th scope="col">Status</th>
            <th scope="col" class="acoes">Ações</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($produtos as $produto)
        <tr>
            <td class="centro nao-quebra">{{ $produto->codigo_produto }}</td>
            <td>{{ $produto->descricao }}</td>
            <td>{{ $produto->familia ?? '-' }}</td>
            <td class="centro">{{ $produto->marca ?? '-' }}</td>
            <td class="centro">{{ $produto->ncm ?? '-' }}</td>
            <td class="centro"><span class="status status-{{ $produto->status ? 'ativo' : 'inativo' }}">{{ $produto->status ? 'Ativo' : 'Inativo' }}</span></td>
            <td class="acoes">
                <a href="{{ route('produtos.show', $produto) }}" title="Ver produto {{ $produto->codigo_produto }}"><i class="fa-solid fa-eye" aria-hidden="true"></i><span class="sr-only">Ver produto {{ $produto->codigo_produto }}</span></a>
                @permite('produtos', 'editar')
                <a href="{{ route('produtos.edit', $produto) }}" title="Editar produto {{ $produto->codigo_produto }}"><i class="fa-solid fa-pen" aria-hidden="true"></i><span class="sr-only">Editar produto {{ $produto->codigo_produto }}</span></a>
                @endpermite
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="7" class="vazio">{{ $temFiltro ? 'Nenhum produto encontrado com esses filtros.' : 'Nenhum produto cadastrado.' }}</td>
        </tr>
        @endforelse
    </tbody>
</table>
{{ $produtos->links() }}
@endsection
