@extends('layouts.app')

@section('title','Novo Produto - '.config('amostraweb.name'))

@section('texto','Produtos')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/layout/form.css') }}">
@endpush

@php
    // campos opcionais de texto: nome => [rótulo, tamanho máximo]
    $camposOpcionais = [
        'familia' => ['Família', 20],
        'subfamilia' => ['Subfamília', 20],
        'grupo' => ['Grupo', 20],
        'tipo' => ['Tipo', 20],
        'ncm' => ['NCM', 50],
        'marca' => ['Marca', 255],
        'aplicacao_entrada' => ['Aplicação de entrada', 20],
        'aplicacao_saida' => ['Aplicação de saída', 20],
        'origem_mercadoria' => ['Origem da mercadoria', 20],
        'controle_verba' => ['Controle de verba', 20],
    ];
@endphp

@section('content')
<div class="toolbar">
    <h1><a href="{{ route('produtos.index') }}">Produtos</a> > Novo</h1>
</div>

<form action="{{ route('produtos.store') }}" method="post" class="form-cadastro">
    @csrf

    <div class="form-grupo">
        <label for="codigo_produto">Código do produto</label>
        <input type="text" name="codigo_produto" required id="codigo_produto" maxlength="6" placeholder="000000" value="{{ old('codigo_produto') }}">
        @error('codigo_produto')
        <p class="erro-campo">{{ $message }}</p>
        @enderror
    </div>

    <div class="form-grupo grupo-duplo">
        <label for="descricao">Descrição</label>
        <input type="text" name="descricao" required id="descricao" maxlength="255" placeholder="Digite a descrição do produto" value="{{ old('descricao') }}">
        @error('descricao')
        <p class="erro-campo">{{ $message }}</p>
        @enderror
    </div>

    @foreach ($camposOpcionais as $campo => [$rotulo, $max])
    <div class="form-grupo">
        <label for="{{ $campo }}">{{ $rotulo }}</label>
        <input type="text" name="{{ $campo }}" id="{{ $campo }}" maxlength="{{ $max }}" value="{{ old($campo) }}">
        @error($campo)
        <p class="erro-campo">{{ $message }}</p>
        @enderror
    </div>
    @endforeach

    <div class="form-grupo">
        <label for="data_revisao">Data de revisão</label>
        <input type="date" name="data_revisao" id="data_revisao" value="{{ old('data_revisao') }}">
        @error('data_revisao')
        <p class="erro-campo">{{ $message }}</p>
        @enderror
    </div>

    <div class="form-grupo grupo-full grupo-checkbox">
        <input type="hidden" name="status" value="0">
        <label for="status" class="label-checkbox">
            <input type="checkbox" name="status" id="status" value="1" @checked(old('status', true))>
            Ativo
        </label>
    </div>

    <div class="form-acoes">
        <a href="{{ route('produtos.index') }}" class="botao-cancelar">Cancelar</a>
        <button type="submit" class="botao-salvar">Salvar</button>
    </div>
</form>
@endsection
