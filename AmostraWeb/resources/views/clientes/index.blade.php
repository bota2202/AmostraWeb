@extends('layouts.app')

@section('title','Clientes - '.config('amostraweb.name'))

@section('texto','Clientes')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/layout/table.css') }}">
@endpush

@section('content')
<div class="toolbar">
    <h1>Clientes</h1>
    <a href="{{ route('clientes.create') }}" class="botao-toolbar">
        <i class="fa-solid fa-plus"></i> Novo Cliente
    </a>
</div>

<table>
    <thead>
        <tr>
            <th>Código</th>
            <th>Razão Social</th>
            <th>Nome Fantasia</th>
            <th>CNPJ / CPF</th>
            <th>Tipo</th>
            <th>Telefone</th>
            <th>Status</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($clientes as $cliente)
        <tr>
            <td class="centro">{{ $cliente->codigo_empresa }}</td>
            <td>{{ $cliente->razao_social }}</td>
            <td>{{ $cliente->nome_fantasia ?? '-' }}</td>
            <td class="centro">{{ $cliente->cnpj_cpf }}</td>
            <td class="centro">{{ $cliente->tipo }}</td>
            <td class="centro">{{ $cliente->telefone ?? '-' }}</td>
            <td class="centro"><span class="status status-{{ $cliente->status ? 'ativo' : 'inativo' }}">{{ $cliente->status ? 'Ativo' : 'Inativo' }}</span></td>
            <td class="acoes">
                <a href="{{ route('clientes.show', $cliente) }}" title="Ver detalhes"><i class="fa-solid fa-eye"></i></a>
                <a href="{{ route('clientes.edit', $cliente) }}" title="Editar"><i class="fa-solid fa-pen"></i></a>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="8" class="vazio">Nenhum cliente cadastrado</td>
        </tr>
        @endforelse
    </tbody>
</table>
{{ $clientes->links() }}
@endsection
