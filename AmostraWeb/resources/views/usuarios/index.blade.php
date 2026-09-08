@extends('layouts.app');

@section('texto','Usuários')

@section('')











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
