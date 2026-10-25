@extends('layouts.app')

@section('title','Permissões - '.config('amostraweb.name'))

@section('texto','Permissões')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/grupo/index.css') }}">
@endpush

@section('content')
<div class="toolbar">
    <h1>Grupos de permissões</h1>
    <a href="{{ route('grupos.create') }}" class="botao-toolbar">
        <i class="fa-solid fa-plus"></i> Novo Grupo
    </a>
</div>

@if ($grupos->isEmpty())
<div class="sem-grupos">
    <i class="fa-solid fa-users"></i>
    <span>Nenhum grupo criado ainda.</span>
</div>
@else
<div class="grade-grupos">
    @foreach ($grupos as $grupo)
    <div class="cartao-grupo">
        <div class="topo">
            <span class="icone"><i class="fa-solid fa-users"></i></span>
            <h3>{{ $grupo->nome }}</h3>
        </div>
        <p>{{ $grupo->descricao ?: 'Sem descrição' }}</p>
        <div class="numeros">
            <span><i class="fa-solid fa-user"></i> <strong>{{ $grupo->users_count }}</strong> {{ $grupo->users_count === 1 ? 'membro' : 'membros' }}</span>
            <span><i class="fa-solid fa-unlock"></i> <strong>{{ $grupo->permissoes_count }}</strong> de {{ $totalPermissoes }} permissões</span>
        </div>
        <div class="rodape">
            <a href="{{ route('grupos.show', $grupo) }}"><i class="fa-solid fa-eye"></i> Ver</a>
            <a href="{{ route('grupos.edit', $grupo) }}"><i class="fa-solid fa-pen"></i> Editar</a>
        </div>
    </div>
    @endforeach
</div>
@endif
@endsection
