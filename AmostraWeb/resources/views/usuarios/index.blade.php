@extends('layouts.app')

@section('title','Usuários - '.config('amostraweb.name'))

@section('texto','Usuários')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/layout/table.css') }}">
@endpush

@section('content')
<div class="toolbar">
    <h1>Usuários</h1>
    <a href="{{ route('usuarios.create') }}" class="botao-toolbar">
        <i class="fa-solid fa-plus"></i> Novo Usuário
    </a>
</div>

<table>
    <thead>
        <tr>
            <th>Código do estabelecimento</th>
            <th>Código de usuário</th>
            <th>Nome do usuário</th>
            <th>E-mail</th>
            <th>Cargo</th>
            <th>Status</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($users as $user)
        <tr>
            <td class="centro">{{ $user->estabelecimento_id }}</td>
            <td class="maiusculo">{{ $user->codigo_usuario }}</td>
            <td>{{ $user->name }}</td>
            {{-- o e-mail só quebra linha depois do @ --}}
            <td>{!! str_replace('@', '@<wbr>', e($user->email)) !!}</td>
            <td class="centro">{{ \App\Models\User::CARGOS[$user->cargo] ?? $user->cargo }}</td>
            <td class="centro"><span class="status status-{{ $user->status ? 'ativo' : 'inativo' }}">{{ $user->status ? 'Ativo' : 'Inativo' }}</span></td>
            <td class="acoes">
                <a href="{{ route('usuarios.show',$user) }}" title="Ver detalhes"><i class="fa-solid fa-eye"></i></a>
                <a href="{{ route('usuarios.edit',$user) }}" title="Editar"><i class="fa-solid fa-pen"></i></a>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="7" class="vazio">Nenhum usuário cadastrado</td>
        </tr>
        @endforelse
    </tbody>
</table>
{{ $users->links() }}
@endsection
