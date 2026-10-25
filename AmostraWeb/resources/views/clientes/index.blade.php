@extends('layouts.app')

@section('title','Clientes - '.config('amostraweb.name'))

@section('texto','Clientes')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/layout/table.css') }}">
<link rel="stylesheet" href="{{ asset('css/layout/filtros.css') }}">
@endpush

@section('content')
<div class="toolbar">
    <h1>Clientes</h1>
    <div class="acoes-toolbar">
        @permite('clientes', 'importar')
        <a href="{{ route('clientes.importar') }}" class="botao-toolbar botao-secundario">
            <i class="fa-solid fa-file-import" aria-hidden="true"></i> Importar do ERP
        </a>
        @endpermite
        @permite('clientes', 'criar')
        <a href="{{ route('clientes.create') }}" class="botao-toolbar">
            <i class="fa-solid fa-plus" aria-hidden="true"></i> Novo cliente
        </a>
        @endpermite
    </div>
</div>

<form method="GET" action="{{ route('clientes.index') }}" class="filtros" role="search" aria-label="Filtrar clientes">
    <div class="filtro filtro-busca">
        <label for="busca">Buscar</label>
        <input type="search" id="busca" name="busca" value="{{ $filtros['busca'] ?? '' }}" placeholder="Razão social, nome fantasia, código ou CNPJ/CPF">
    </div>
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
        @if (array_filter($filtros, fn ($v) => $v !== null && $v !== ''))
        <a href="{{ route('clientes.index') }}">Limpar</a>
        @endif
    </div>
</form>

<table>
    <caption class="sr-only">Lista de clientes</caption>
    <thead>
        <tr>
            <th scope="col">Razão social</th>
            <th scope="col">Nome fantasia</th>
            <th scope="col">CNPJ / CPF</th>
            <th scope="col">Tipo</th>
            <th scope="col">Telefone</th>
            <th scope="col">Status</th>
            <th scope="col" class="acoes">Ações</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($clientes as $cliente)
        <tr>
            <td>
                {{ $cliente->razao_social }}
                <span class="texto-secundario">Cód. {{ $cliente->codigo_empresa }}</span>
            </td>
            <td>{{ $cliente->nome_fantasia ?? '-' }}</td>
            <td class="centro nao-quebra">{{ $cliente->cnpj_cpf }}</td>
            <td class="centro">{{ $cliente->tipo }}</td>
            <td class="centro nao-quebra">{{ $cliente->telefone ?? '-' }}</td>
            <td class="centro"><span class="status status-{{ $cliente->status ? 'ativo' : 'inativo' }}">{{ $cliente->status ? 'Ativo' : 'Inativo' }}</span></td>
            <td class="acoes">
                <a href="{{ route('clientes.show', $cliente) }}" title="Ver {{ $cliente->razao_social }}"><i class="fa-solid fa-eye" aria-hidden="true"></i><span class="sr-only">Ver {{ $cliente->razao_social }}</span></a>
                @permite('clientes', 'editar')
                <a href="{{ route('clientes.edit', $cliente) }}" title="Editar {{ $cliente->razao_social }}"><i class="fa-solid fa-pen" aria-hidden="true"></i><span class="sr-only">Editar {{ $cliente->razao_social }}</span></a>
                @endpermite
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="7" class="vazio">
                {{ array_filter($filtros, fn ($v) => $v !== null && $v !== '') ? 'Nenhum cliente encontrado com esses filtros.' : 'Nenhum cliente cadastrado.' }}
            </td>
        </tr>
        @endforelse
    </tbody>
</table>
{{ $clientes->links() }}
@endsection
