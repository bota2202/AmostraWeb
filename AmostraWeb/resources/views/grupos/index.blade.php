@extends('layouts.app')

@section('title','Permissões - '.config('amostraweb.name'))

@section('texto','Permissões')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/grupo/index.css') }}">
@endpush

@section('content')
<div class="toolbar">
    <h1>Grupos de permissões</h1>
    @permite('permissoes', 'criar')
    <a href="{{ route('grupos.create') }}" class="botao-toolbar">
        <i class="fa-solid fa-plus" aria-hidden="true"></i> Novo grupo
    </a>
    @endpermite
</div>

@if ($grupos->isEmpty())
<div class="sem-grupos">
    <i class="fa-solid fa-users" aria-hidden="true"></i>
    <span>Nenhum grupo criado ainda.</span>
</div>
@else
<div class="grade-grupos">
    @foreach ($grupos as $grupo)
    <div class="cartao-grupo">
        <div class="topo">
            <span class="icone" aria-hidden="true"><i class="fa-solid fa-users" aria-hidden="true"></i></span>
            <h2>{{ $grupo->nome }}</h2>
        </div>
        <p>{{ $grupo->descricao ?: 'Sem descrição' }}</p>
        <div class="numeros">
            <span><i class="fa-solid fa-user" aria-hidden="true"></i> <strong>{{ $grupo->users_count }}</strong> {{ $grupo->users_count === 1 ? 'membro' : 'membros' }}</span>
            <span><i class="fa-solid fa-unlock" aria-hidden="true"></i> <strong>{{ $grupo->permissoes_count }}</strong> de {{ $totalPermissoes }} permissões</span>
        </div>
        <div class="rodape">
            <a href="{{ route('grupos.show', $grupo) }}"><i class="fa-solid fa-eye" aria-hidden="true"></i> Ver</a>
            @permite('permissoes', 'editar')
            <a href="{{ route('grupos.edit', $grupo) }}"><i class="fa-solid fa-pen" aria-hidden="true"></i> Editar</a>
            @endpermite
        </div>
    </div>
    @endforeach
</div>
@endif
@endsection
