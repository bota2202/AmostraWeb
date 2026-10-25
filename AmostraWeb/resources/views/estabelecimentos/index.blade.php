@extends('layouts.app')

@section('title','Estabelecimentos - '. config('amostraweb.name'))

@section('texto','Estabelecimentos')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/layout/table.css') }}">
<link rel="stylesheet" href="{{ asset('css/layout/filtros.css') }}">
@endpush

@php $temFiltro = array_filter($filtros, fn ($v) => $v !== null && $v !== ''); @endphp

@section('content')
<div class="toolbar">
    <h1>Estabelecimentos</h1>
    @permite('estabelecimentos', 'criar')
    <a href="{{ route('estabelecimentos.create') }}" class="botao-toolbar">
        <i class="fa-solid fa-plus" aria-hidden="true"></i> Novo estabelecimento
    </a>
    @endpermite
</div>

<form method="GET" action="{{ route('estabelecimentos.index') }}" class="filtros" role="search" aria-label="Filtrar estabelecimentos">
    <div class="filtro filtro-busca">
        <label for="busca">Buscar</label>
        <input type="search" id="busca" name="busca" value="{{ $filtros['busca'] ?? '' }}" placeholder="Razão social, CNPJ ou cidade">
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
        @if ($temFiltro)
        <a href="{{ route('estabelecimentos.index') }}">Limpar</a>
        @endif
    </div>
</form>

<table>
    <caption class="sr-only">Lista de estabelecimentos</caption>
    <thead>
        <tr>
            <th scope="col">Código</th>
            <th scope="col">Razão social</th>
            <th scope="col">CNPJ</th>
            <th scope="col">Cidade / UF</th>
            <th scope="col">Gestor</th>
            <th scope="col">Usuários</th>
            <th scope="col">Status</th>
            <th scope="col" class="acoes">Ações</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($estabelecimentos as $estab)
        <tr>
            <td class="centro">{{ $estab->id }}</td>
            <td>{{ $estab->razao_social }}</td>
            <td class="centro nao-quebra">{{ $estab->cnpj }}</td>
            <td class="centro"><span class="capitalizado-inline">{{ $estab->cidade }}</span> / {{ strtoupper($estab->uf) }}</td>
            <td>{{ $estab->gestor?->name ?? 'Sem gestor' }}</td>
            <td class="centro">{{ $estab->users_count }}</td>
            <td class="centro"><span class="status status-{{ $estab->status ? 'ativo' : 'inativo' }}">{{ $estab->status ? 'Ativo' : 'Inativo' }}</span></td>
            <td class="acoes">
                <a href="{{ route('estabelecimentos.show', $estab) }}" title="Ver {{ $estab->razao_social }}"><i class="fa-solid fa-eye" aria-hidden="true"></i><span class="sr-only">Ver {{ $estab->razao_social }}</span></a>
                @permite('estabelecimentos', 'editar')
                <a href="{{ route('estabelecimentos.edit', $estab) }}" title="Editar {{ $estab->razao_social }}"><i class="fa-solid fa-pen" aria-hidden="true"></i><span class="sr-only">Editar {{ $estab->razao_social }}</span></a>
                @endpermite
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="8" class="vazio">{{ $temFiltro ? 'Nenhum estabelecimento encontrado com esses filtros.' : 'Nenhum estabelecimento cadastrado.' }}</td>
        </tr>
        @endforelse
    </tbody>
</table>
{{ $estabelecimentos->links() }}
@endsection
