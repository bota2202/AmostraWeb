@extends('layouts.app');

@section('texto','Usuários')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/layout/table.css') }}">
@endpush

@push('styles')
<link rel="stylesheet" href="{{ asset('css/usuario/index.css') }}">
@endpush

@section('title','Usuários - '.config('amostraweb.name'))

@section('content')
<div class="toolbar">
    <h1>Estabelecimentos</h1>
    <a href="{{ route('usuarios.create') }}" class="botao-toolbar">
        <i class="fa-solid fa-plus"></i> Novo Usuário
    </a>
</div>
<table>
    <thead>
        <tr>
            <th>Código do estabelecimento</th>
            <th>Cóigo de usuário</th>
            <th>Nome do usuário</th>
            <th>E-mail</th>
            <th>Cargo</th>
            <th>Status</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($users as $user)
        <tr>
            <td>{{ $user->estabelecimento->id }}</td>
            <td>{{ $user->codigo_usuario }}</td>
            <td>{{ $user->name }}</td>
            <td>{{ $user->email }}</td>
            <td>{{ $user->cargo }}</td>
            <td><span class="status status-{{ $user->status == 1 ? 'ativo' : 'inativo' }}">{{ $user->status == 1 ? 'Ativo' : 'Inativo' }}</span></td>
            <td class="acoes">
                <a href="{{ route('usuarios.show',$user) }}" title="Ver detalhes"><i class="fa-solid fa-eye"></i></a>
                <a href="{{ route('usuarios.edit',$user) }}" title="Editar"><i class="fa-solid fa-pen"></i></a>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection










<!-- <body>
    @if (session('success'))
        <p>{{ session('success') }}</p>
    @endif
    <a href="{{ route('usuarios.create') }}">Novo</a>
    <h1>Usuários:</h1>
    <table>
        <thead>
            <tr>
                <th>Código de usuário</th>
                <th>Nome</th>
                <th>Cargo</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($users as $user)
            <tr>
                <td>{{ $user->codigo_usuario }}</td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->cargo }}</td>
                <td>
                    <a href="{{ route('usuarios.show',$user) }}">Expandir</a>
                    <a href="{{ route('usuarios.edit',$user) }}">Editar</a>
                </td>
            </tr>
            @endforeach
            {{ $users->links() }}
        </tbody>
    </table>
</body> -->