@extends('layouts.app')

@section('title','Novo Grupo - '.config('amostraweb.name'))

@section('texto','Permissões')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/layout/form.css') }}">
@endpush

@section('content')
<div class="toolbar">
    <h1><a href="{{ route('grupos.index') }}">Grupos</a> › Novo</h1>
</div>

<form action="{{ route('grupos.store') }}" method="post" class="form-cadastro">
    @csrf

    <div class="form-grupo">
        <label for="nome">Nome do grupo</label>
        <input type="text" name="nome" required id="nome" maxlength="100" placeholder="Ex.: Vendedores" value="{{ old('nome') }}">
        @error('nome')
        <p class="erro-campo">{{ $message }}</p>
        @enderror
    </div>

    <div class="form-grupo grupo-duplo">
        <label for="descricao">Descrição</label>
        <input type="text" name="descricao" id="descricao" maxlength="255" placeholder="Para que serve este grupo" value="{{ old('descricao') }}">
        @error('descricao')
        <p class="erro-campo">{{ $message }}</p>
        @enderror
    </div>

    <div class="form-acoes">
        <a href="{{ route('grupos.index') }}" class="botao-cancelar">Cancelar</a>
        <button type="submit" class="botao-salvar">Criar e escolher membros</button>
    </div>
</form>
@endsection
