@extends('layouts.app')

@section('title','Usuários - '.config('amostraweb.name'))

@section('texto','Usuários')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/layout/detalhes.css') }}">
@endpush

@section('content')
<div class="toolbar">
    <h1><a href="{{ route('usuarios.index') }}">Usuários </a>> {{ $user->codigo_usuario }}</h1>
    <a class="botao-toolbar" href="{{ route('usuarios.edit',$user) }}"><i class="fa-solid fa-pen"></i> Editar Usuário</a>
</div>

<div class="dados">
    <section class="card-secao">
        <h3><i class="fa-solid fa-id-card"></i> Identificação</h3>
        <div class="campos-secao">
            <div class="label-input">
                <label for="name">Nome</label>
                <input type="text" id="name" disabled value="{{ $user->name }}">
            </div>
            <div class="label-input">
                <label for="codigo_usuario">Código de usuário</label>
                <input type="text" id="codigo_usuario" disabled value="{{ $user->codigo_usuario }}">
            </div>
            <span class="badge-status {{ $user->status ? 'ativo' : 'inativo' }}">
                <i class="fa-solid fa-circle"></i>
                {{ $user->status ? 'Ativo' : 'Inativo' }}
            </span>
            <div class="label-input">
                <label for="email">E-mail</label>
                <input type="text" id="email" disabled value="{{ $user->email }}">
            </div>
            <div class="label-input">
                <label for="cargo">Cargo</label>
                <input type="text" id="cargo" disabled value="{{ \App\Models\User::CARGOS[$user->cargo] ?? $user->cargo }}">
            </div>
        </div>
    </section>

    <section class="card-secao">
        <h3><i class="fa-solid fa-shop"></i> Estabelecimento</h3>
        <div class="campos-secao">
            <div class="label-input">
                <label for="estabelecimento_id">ID</label>
                <input type="text" id="estabelecimento_id" disabled value="{{ $user->estabelecimento_id }}">
            </div>
            <div class="label-input">
                <label for="estabelecimento_razao">Razão social</label>
                <input type="text" id="estabelecimento_razao" disabled value="{{ $user->estabelecimento?->razao_social ?? '-' }}">
            </div>
            <div class="label-input">
                <label for="estabelecimento_cidade">Cidade / UF</label>
                <input type="text" id="estabelecimento_cidade" disabled value="{{ $user->estabelecimento ? $user->estabelecimento->cidade.' / '.strtoupper($user->estabelecimento->uf) : '-' }}">
            </div>
        </div>
    </section>

    <section class="card-secao">
        <h3>
            <i class="fa-solid fa-users"></i> Grupos
            <a class="h3-acao" href="{{ route('usuarios.edit', $user) }}#permissoes"><i class="fa-solid fa-pen"></i> Gerenciar</a>
        </h3>
        @if ($user->grupos->isEmpty())
        <p class="texto-vazio">O usuário não participa de nenhum grupo.</p>
        @else
        <div class="chips">
            @foreach ($user->grupos as $grupo)
            <a class="chip chip-grupo" href="{{ route('grupos.show', $grupo) }}"><i class="fa-solid fa-users"></i> {{ $grupo->nome }}</a>
            @endforeach
        </div>
        @endif
    </section>

    <section class="card-secao">
        <h3>
            <i class="fa-solid fa-unlock"></i> Permissões ({{ count($efetivas) }})
            <a class="h3-acao" href="{{ route('usuarios.edit', $user) }}#permissoes"><i class="fa-solid fa-pen"></i> Gerenciar</a>
        </h3>
        @if ($user->isAdmin())
        <p class="subtitulo-bloco espacado">Administrador: tem todas as permissões do sistema.</p>
        @else
        <p class="subtitulo-bloco espacado">Permissões finais do usuário: as dos grupos somadas às liberadas só para ele.</p>
        @endif
        @include('partials.permissoes-modulos', [
            'permissoesPorModulo' => $permissoesPorModulo,
            'selecionadas' => $efetivas,
            'somenteLeitura' => true,
        ])
    </section>
</div>
@endsection
