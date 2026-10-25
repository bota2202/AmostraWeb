@extends('layouts.app')

@section('title','Grupos - '.config('amostraweb.name'))

@section('texto','Permissões')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/layout/detalhes.css') }}">
@endpush

@section('content')
<div class="toolbar">
    <h1><a href="{{ route('grupos.index') }}">Grupos</a> › {{ $grupo->nome }}</h1>
    @permite('permissoes', 'editar')
    <a class="botao-toolbar" href="{{ route('grupos.edit',$grupo) }}"><i class="fa-solid fa-pen" aria-hidden="true"></i> Editar grupo</a>
    @endpermite
</div>

<div class="dados">
    <section class="card-secao">
        <h2><i class="fa-solid fa-users" aria-hidden="true"></i> Grupo</h2>
        <div class="campos-secao">
            <div class="label-input">
                <label for="nome">Nome</label>
                <input type="text" id="nome" readonly value="{{ $grupo->nome }}">
            </div>
            <div class="label-input campo-duplo">
                <label for="descricao">Descrição</label>
                <input type="text" id="descricao" readonly value="{{ $grupo->descricao ?: '-' }}">
            </div>
        </div>
    </section>

    <section class="card-secao">
        <h2>
            <i class="fa-solid fa-user" aria-hidden="true"></i> Membros ({{ $grupo->users->count() }})
            @permite('permissoes', 'editar')
            <a class="h3-acao" href="{{ route('grupos.edit', $grupo) }}#membros"><i class="fa-solid fa-pen" aria-hidden="true"></i> Gerenciar</a>
            @endpermite
        </h2>
        @if ($grupo->users->isEmpty())
        <p class="texto-vazio">Nenhum usuário neste grupo.</p>
        @else
        <div class="chips">
            @foreach ($grupo->users as $membro)
            <a class="chip" href="{{ route('usuarios.show', $membro) }}">
                <span class="avatar-mini">{{ $membro->iniciais }}</span>
                {{ $membro->name }}
            </a>
            @endforeach
        </div>
        @endif
    </section>

    <section class="card-secao">
        <h2>
            <i class="fa-solid fa-unlock" aria-hidden="true"></i> Permissões ({{ $grupo->permissoes->count() }})
            @permite('permissoes', 'editar')
            <a class="h3-acao" href="{{ route('grupos.edit', $grupo) }}#permissoes"><i class="fa-solid fa-pen" aria-hidden="true"></i> Gerenciar</a>
            @endpermite
        </h2>
        @include('partials.permissoes-modulos', [
            'permissoesPorModulo' => $permissoesPorModulo,
            'selecionadas' => $grupo->permissoes->pluck('id')->all(),
            'somenteLeitura' => true,
        ])
    </section>
</div>
@endsection
