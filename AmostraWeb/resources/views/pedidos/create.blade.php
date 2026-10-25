@extends('layouts.app')

@section('title','Novo Pedido - '.config('amostraweb.name'))

@section('texto','Pedidos')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/layout/form.css') }}">
@endpush

@section('content')
<div class="toolbar">
    <h1><a href="{{ route('pedidos.index') }}">Pedidos</a> > Novo</h1>
</div>

<form action="{{ route('pedidos.store') }}" method="post" class="form-cadastro">
    @csrf

    <div class="form-grupo">
        <label for="codigo">Código do pedido</label>
        <input type="text" name="codigo" required id="codigo" maxlength="6" placeholder="000000" value="{{ old('codigo') }}">
        @error('codigo')
        <p class="erro-campo">{{ $message }}</p>
        @enderror
    </div>

    <div class="form-grupo">
        <label for="data_emissao">Data de emissão</label>
        <input type="date" name="data_emissao" required id="data_emissao" value="{{ old('data_emissao', today()->format('Y-m-d')) }}">
        @error('data_emissao')
        <p class="erro-campo">{{ $message }}</p>
        @enderror
    </div>

    <div class="form-grupo">
        <label>Representante</label>
        <input type="text" disabled value="{{ auth()->user()->codigo_usuario }}">
    </div>

    <div class="form-grupo grupo-duplo">
        <label for="cliente_id">Cliente</label>
        <select name="cliente_id" id="cliente_id" required>
            <option value="">Selecione</option>
            @foreach ($clientes as $cliente)
            <option value="{{ $cliente->id }}" @selected(old('cliente_id')==$cliente->id)>{{ $cliente->codigo_empresa }} - {{ $cliente->razao_social }}</option>
            @endforeach
        </select>
        @error('cliente_id')
        <p class="erro-campo">{{ $message }}</p>
        @enderror
    </div>

    <div class="form-grupo">
        <label for="estabelecimento_id">Estabelecimento</label>
        <select name="estabelecimento_id" id="estabelecimento_id" required>
            <option value="">Selecione</option>
            @foreach ($estabelecimentos as $estab)
            <option value="{{ $estab->id }}" @selected(old('estabelecimento_id', auth()->user()->estabelecimento_id)==$estab->id)>#{{ $estab->id }} - {{ $estab->razao_social }}</option>
            @endforeach
        </select>
        @error('estabelecimento_id')
        <p class="erro-campo">{{ $message }}</p>
        @enderror
    </div>

    <div class="form-grupo grupo-full">
        <label for="observacao">Observação</label>
        <textarea name="observacao" id="observacao" maxlength="255" placeholder="Informações adicionais sobre o pedido">{{ old('observacao') }}</textarea>
        @error('observacao')
        <p class="erro-campo">{{ $message }}</p>
        @enderror
    </div>

    <div class="form-acoes">
        <a href="{{ route('pedidos.index') }}" class="botao-cancelar">Cancelar</a>
        <button type="submit" class="botao-salvar">Salvar</button>
    </div>
</form>
@endsection
