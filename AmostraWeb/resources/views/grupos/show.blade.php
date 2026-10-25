@extends('layouts.app')

@section('title','Grupos - '.config('amostraweb.name'))

@section('texto','Permissões')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/layout/detalhes.css') }}">
@endpush

@section('content')
<div class="toolbar">
    <h1><a href="{{ route('grupos.index') }}">Grupos </a>> {{ $grupo->nome }}</h1>
    <a class="botao-toolbar" href="{{ route('grupos.edit',$grupo) }}"><i class="fa-solid fa-pen"></i> Editar Grupo</a>
</div>

<div class="dados">
    <section class="card-secao">
        <h3><i class="fa-solid fa-users"></i> Grupo</h3>
        <div class="campos-secao">
            <div class="label-input">
                <label for="nome">Nome</label>
                <input type="text" id="nome" disabled value="{{ $grupo->nome }}">
            </div>
            <div class="label-input campo-duplo">
                <label for="descricao">Descrição</label>
                <input type="text" id="descricao" disabled value="{{ $grupo->descricao ?: '-' }}">
            </div>
        </div>
    </section>

    <section class="card-secao">
        <h3>
            <i class="fa-solid fa-user"></i> Membros ({{ $grupo->users->count() }})
            <a class="h3-acao" href="{{ route('grupos.edit', $grupo) }}#membros"><i class="fa-solid fa-pen"></i> Gerenciar</a>
        </h3>
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
        <h3>
            <i class="fa-solid fa-unlock"></i> Permissões ({{ $grupo->permissoes->count() }})
            <a class="h3-acao" href="{{ route('grupos.edit', $grupo) }}#permissoes"><i class="fa-solid fa-pen"></i> Gerenciar</a>
        </h3>
        @include('partials.permissoes-modulos', [
            'permissoesPorModulo' => $permissoesPorModulo,
            'selecionadas' => $grupo->permissoes->pluck('id')->all(),
            'somenteLeitura' => true,
        ])
    </section>
</div>
@endsection
