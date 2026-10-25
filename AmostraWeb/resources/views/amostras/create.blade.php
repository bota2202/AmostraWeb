@extends('layouts.app')

@section('title','Adicionar amostra - '.config('amostraweb.name'))

@section('texto','Amostras')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/layout/form.css') }}">
@endpush

@php
    $voltar = $pedidoSelecionado ? route('pedidos.show', $pedidoSelecionado) : route('amostras.index');
@endphp

@section('content')
<div class="toolbar">
    <h1>
        @if ($pedidoSelecionado)
        <a href="{{ route('pedidos.index') }}">Pedidos</a> › <a href="{{ $voltar }}">Pedido</a> › Adicionar amostra
        @else
        <a href="{{ route('amostras.index') }}">Amostras</a> › Adicionar amostra a um pedido
        @endif
    </h1>
</div>

@if ($pedidos->isEmpty())
<div class="alerta alerta-aviso" role="note">
    <i class="fa-solid fa-circle-info" aria-hidden="true"></i>
    <span>Não há pedidos em aberto para você. As amostras são cadastradas junto com o pedido:
        <a href="{{ route('pedidos.create') }}">cadastrar um novo pedido</a>.</span>
</div>
@endif

<form action="{{ route('amostras.store') }}" method="post" class="form-cadastro">
    @csrf

    <div class="form-grupo grupo-full">
        <label for="pedido_id">Pedido <span class="obrigatorio" aria-hidden="true">*</span></label>
        <select name="pedido_id" id="pedido_id" required>
            <option value="">Selecione o pedido</option>
            @foreach ($pedidos as $pedido)
            <option value="{{ $pedido->id }}" @selected(old('pedido_id', $pedidoSelecionado)==$pedido->id)>{{ $pedido->codigo }} - {{ $pedido->cliente->razao_social }} ({{ $pedido->data_emissao->format('d/m/Y') }})</option>
            @endforeach
        </select>
        <p class="dica-campo">Só aparecem pedidos em aberto. A amostra não poderá ser transferida para outro pedido depois.</p>
        @error('pedido_id')
        <p class="erro-campo">{{ $message }}</p>
        @enderror
    </div>

    <div class="form-grupo grupo-full">
        <label for="produto_id">Produto <span class="obrigatorio" aria-hidden="true">*</span></label>
        <select name="produto_id" id="produto_id" required>
            <option value="">Selecione o produto</option>
            @foreach ($produtos as $produto)
            <option value="{{ $produto->id }}" @selected(old('produto_id')==$produto->id)>{{ $produto->codigo_produto }} - {{ $produto->descricao }}</option>
            @endforeach
        </select>
        @error('produto_id')
        <p class="erro-campo">{{ $message }}</p>
        @enderror
    </div>

    <div class="form-grupo">
        <label for="quantidade">Quantidade <span class="obrigatorio" aria-hidden="true">*</span></label>
        <input type="number" name="quantidade" required id="quantidade" min="1" step="1" value="{{ old('quantidade', 1) }}">
        @error('quantidade')
        <p class="erro-campo">{{ $message }}</p>
        @enderror
    </div>

    <div class="form-grupo">
        <label for="prazo_avaliacao">Prazo de avaliação <span class="obrigatorio" aria-hidden="true">*</span></label>
        <input type="date" name="prazo_avaliacao" required id="prazo_avaliacao" min="{{ today()->format('Y-m-d') }}" value="{{ old('prazo_avaliacao') }}">
        @error('prazo_avaliacao')
        <p class="erro-campo">{{ $message }}</p>
        @enderror
    </div>

    <div class="form-acoes">
        <a href="{{ $voltar }}" class="botao-cancelar">Cancelar</a>
        <button type="submit" class="botao-salvar">Adicionar amostra</button>
    </div>
</form>
@endsection
