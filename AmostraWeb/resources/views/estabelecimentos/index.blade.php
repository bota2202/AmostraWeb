@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/estabelecimento/index.css') }}">
@endpush

@push('styles')
<link rel="stylesheet" href="{{ asset('css/layout/table.css') }}">
@endpush

@section('title','Estabelecimentos - '. config('amostraweb.name'))

@section('texto','Estabelecimentos')

@section('content')
<div class="toolbar">
    <h1>Estabelecimentos</h1>
    <a href="{{ route('estabelecimentos.create') }}" class="botao-toolbar">
        <i class="fa-solid fa-plus"></i> Novo Estabelecimento
    </a>
</div>

<table>
    <thead>
        <tr>
            <th>Código</th>
            <th>Razão Social</th>
            <th>CNPJ</th>
            <th>UF</th>
            <th>Cidade</th>
            <th>Gestor</th>
            <th>Status</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($estabelecimentos as $estab)
        <tr>
            <td>{{ $estab->id }}</td>
            <td>{{ $estab->razao_social }}</td>
            <td>{{ $estab->cnpj }}</td>
            <td>{{ $estab->uf }}</td>
            <td>{{ $estab->cidade }}</td>
            <td class="{{ $estab->gestor_id ? '' : 'sem-gestor' }}">{{ $estab->gestor_id ? $estab->gestor->codigo_usuario : '-' }}</td>
            <td><span class="status status-{{ $estab->status == 1 ? 'ativo' : 'inativo' }}">{{ $estab->status == 1 ? 'Ativo' : 'Inativo' }}</span></td>
            <td class="acoes">
                <a href="{{ route('estabelecimentos.show', $estab) }}" title="Ver detalhes">
                    <i class="fa-solid fa-eye"></i>
                </a>
                <a href="{{ route('estabelecimentos.edit', $estab) }}" title="Editar">
                    <i class="fa-solid fa-pen"></i>
                </a>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
{{$estabelecimentos->links()}}
@endsection