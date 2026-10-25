@extends('layouts.app')

@section('title','Usuários - '.config('amostraweb.name'))

@section('texto','Usuários')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/layout/detalhes.css') }}">
@endpush

@section('content')
<div class="toolbar">
    <h1><a href="{{ route('usuarios.index') }}">Usuários</a> › {{ $user->codigo_usuario }}</h1>
    @permite('usuarios', 'editar')
    <a class="botao-toolbar" href="{{ route('usuarios.edit',$user) }}"><i class="fa-solid fa-pen" aria-hidden="true"></i> Editar usuário</a>
    @endpermite
</div>

<div class="dados">
    <section class="card-secao">
        <h2><i class="fa-solid fa-id-card" aria-hidden="true"></i> Identificação</h2>
        <div class="campos-secao">
            <div class="label-input">
                <label for="name">Nome</label>
                <input type="text" id="name" readonly value="{{ $user->name }}">
            </div>
            <div class="label-input">
                <label for="codigo_usuario">Código de usuário</label>
                <input type="text" id="codigo_usuario" readonly value="{{ $user->codigo_usuario }}">
            </div>
            <span class="badge-status {{ $user->status ? 'ativo' : 'inativo' }}">
                <i class="fa-solid fa-circle" aria-hidden="true"></i>
                {{ $user->status ? 'Ativo' : 'Inativo' }}
            </span>
            <div class="label-input">
                <label for="email">E-mail</label>
                <input type="text" id="email" readonly value="{{ $user->email }}">
            </div>
            <div class="label-input">
                <label for="cargo">Cargo</label>
                <input type="text" id="cargo" readonly value="{{ \App\Models\User::CARGOS[$user->cargo] ?? $user->cargo }}">
            </div>
        </div>
    </section>

    <section class="card-secao">
        <h2><i class="fa-solid fa-shop" aria-hidden="true"></i> Estabelecimento</h2>
        <div class="campos-secao">
            <div class="label-input">
                <label for="estabelecimento_id">ID</label>
                <input type="text" id="estabelecimento_id" readonly value="{{ $user->estabelecimento_id }}">
            </div>
            <div class="label-input">
                <label for="estabelecimento_razao">Razão social</label>
                <input type="text" id="estabelecimento_razao" readonly value="{{ $user->estabelecimento?->razao_social ?? '-' }}">
            </div>
            <div class="label-input">
                <label for="estabelecimento_cidade">Cidade / UF</label>
                <input type="text" id="estabelecimento_cidade" readonly value="{{ $user->estabelecimento ? $user->estabelecimento->cidade.' / '.strtoupper($user->estabelecimento->uf) : '-' }}">
            </div>
        </div>
    </section>

    <section class="card-secao">
        <h2>
            <i class="fa-solid fa-users" aria-hidden="true"></i> Grupos
            @permite('permissoes', 'editar')
            <a class="h3-acao" href="{{ route('grupos.index') }}"><i class="fa-solid fa-pen" aria-hidden="true"></i> Gerenciar grupos</a>
            @endpermite
        </h2>
        @if ($user->grupos->isEmpty())
        <p class="texto-vazio">O usuário não participa de nenhum grupo.</p>
        @else
        <div class="chips">
            @foreach ($user->grupos as $grupo)
            <a class="chip chip-grupo" href="{{ route('grupos.show', $grupo) }}"><i class="fa-solid fa-users" aria-hidden="true"></i> {{ $grupo->nome }}</a>
            @endforeach
        </div>
        @endif
    </section>

    <section class="card-secao">
        <h2>
            <i class="fa-solid fa-unlock" aria-hidden="true"></i> Permissões ({{ count($efetivas) }})
        </h2>
        @if ($user->isAdmin())
        <p class="subtitulo-bloco espacado">Administrador: tem todas as permissões do sistema.</p>
        @else
        <p class="subtitulo-bloco espacado">Permissões do usuário: a soma das permissões dos grupos em que ele está. Para mudar, edite o grupo ou os membros dele.</p>
        @endif
        @include('partials.permissoes-modulos', [
            'permissoesPorModulo' => $permissoesPorModulo,
            'selecionadas' => $efetivas,
            'somenteLeitura' => true,
        ])
    </section>
</div>
@endsection
