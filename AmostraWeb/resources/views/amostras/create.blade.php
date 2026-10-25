@extends('layouts.app')

@section('title','Nova Amostra - '.config('amostraweb.name'))

@section('texto','Amostras')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/layout/form.css') }}">
@endpush

@section('content')
<div class="toolbar">
    <h1><a href="{{ route('amostras.index') }}">Amostras</a> > Nova</h1>
</div>

<form action="{{ route('amostras.store') }}" method="post" class="form-cadastro">
    @csrf

    <div class="form-grupo grupo-full">
        <label for="produto_id">Produto</label>
        <select name="produto_id" id="produto_id" required>
            <option value="">Selecione</option>
            @foreach ($produtos as $produto)
            <option value="{{ $produto->id }}" @selected(old('produto_id')==$produto->id)>{{ $produto->codigo_produto }} - {{ $produto->descricao }}</option>
            @endforeach
        </select>
        @error('produto_id')
        <p class="erro-campo">{{ $message }}</p>
        @enderror
    </div>

    <div class="form-grupo">
        <label for="pedido_id">Pedido</label>
        <select name="pedido_id" id="pedido_id">
            <option value="">Sem pedido</option>
            @foreach ($pedidos as $pedido)
            <option value="{{ $pedido->id }}" @selected(old('pedido_id', $pedidoSelecionado)==$pedido->id)>{{ $pedido->codigo }} - {{ $pedido->cliente->razao_social }}</option>
            @endforeach
        </select>
        @error('pedido_id')
        <p class="erro-campo">{{ $message }}</p>
        @enderror
    </div>

    <div class="form-grupo">
        <label for="quantidade">Quantidade</label>
        <input type="number" name="quantidade" required id="quantidade" min="1" step="1" value="{{ old('quantidade', 1) }}">
        @error('quantidade')
        <p class="erro-campo">{{ $message }}</p>
        @enderror
    </div>

    <div class="form-grupo">
        <label for="prazo_avaliacao">Prazo de avaliação</label>
        <input type="date" name="prazo_avaliacao" required id="prazo_avaliacao" min="{{ today()->format('Y-m-d') }}" value="{{ old('prazo_avaliacao') }}">
        @error('prazo_avaliacao')
        <p class="erro-campo">{{ $message }}</p>
        @enderror
    </div>

    <div class="form-acoes">
        <a href="{{ route('amostras.index') }}" class="botao-cancelar">Cancelar</a>
        <button type="submit" class="botao-salvar">Salvar</button>
    </div>
</form>
@endsection
