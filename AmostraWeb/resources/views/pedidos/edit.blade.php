@extends('layouts.app')

@section('title','Editar pedido '.$pedido->codigo.' - '.config('amostraweb.name'))

@section('texto','Pedidos')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/layout/detalhes.css') }}">
@endpush

@section('content')
<div class="toolbar">
    <h1>
        <a href="{{ route('pedidos.index') }}">Pedidos</a> ›
        <a href="{{ route('pedidos.show', $pedido) }}">{{ $pedido->codigo }}</a> ›
        Editar
    </h1>
    <div class="acoes-toolbar">
        <a class="botao-toolbar botao-secundario" href="{{ route('pedidos.show', $pedido) }}">
            <i class="fa-solid fa-xmark" aria-hidden="true"></i> Cancelar
        </a>
        <button type="submit" form="form-pedido" class="botao-toolbar">
            <i class="fa-solid fa-check" aria-hidden="true"></i> Salvar alterações
        </button>
    </div>
</div>

<form id="form-pedido" action="{{ route('pedidos.update', $pedido) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="dados">
        <section class="card-secao" aria-labelledby="titulo-pedido">
            <h2 id="titulo-pedido"><i class="fa-solid fa-receipt" aria-hidden="true"></i> Pedido</h2>
            <div class="campos-secao">
                <div class="label-input">
                    <label for="codigo">Código</label>
                    <input type="text" id="codigo" readonly value="{{ $pedido->codigo }}">
                </div>
                <div class="label-input">
                    <label for="data_emissao">Data de envio</label>
                    <input type="date" id="data_emissao" name="data_emissao" required value="{{ old('data_emissao', $pedido->data_emissao->format('Y-m-d')) }}">
                    @error('data_emissao')
                    <span class="erro-campo">{{ $message }}</span>
                    @enderror
                </div>
                <div class="label-input">
                    <label for="situacao">Situação</label>
                    <input type="text" id="situacao" readonly value="{{ $pedido->status_nome }}" aria-describedby="dica-situacao">
                    <p class="dica-campo" id="dica-situacao">Calculada pelas amostras: concluído quando todas forem finalizadas.</p>
                </div>
                <div class="label-input campo-duplo">
                    <label for="cliente_id">Cliente</label>
                    <select id="cliente_id" name="cliente_id" required>
                        @unless ($clientes->contains('id', $pedido->cliente_id))
                        {{-- cliente inativo continua aparecendo para o pedido atual --}}
                        <option value="{{ $pedido->cliente_id }}" selected>{{ $pedido->cliente->codigo_empresa }} - {{ $pedido->cliente->razao_social }} (inativo)</option>
                        @endunless
                        @foreach ($clientes as $cliente)
                        <option value="{{ $cliente->id }}" @selected(old('cliente_id', $pedido->cliente_id) == $cliente->id)>{{ $cliente->codigo_empresa }} - {{ $cliente->razao_social }}</option>
                        @endforeach
                    </select>
                    @error('cliente_id')
                    <span class="erro-campo">{{ $message }}</span>
                    @enderror
                </div>
                <div class="label-input">
                    <label for="estabelecimento">Estabelecimento</label>
                    <input type="text" id="estabelecimento" readonly value="{{ $pedido->estabelecimento->razao_social }}" aria-describedby="dica-estabelecimento">
                    <p class="dica-campo" id="dica-estabelecimento">Definido pelo vendedor responsável.</p>
                </div>
                <div class="label-input campo-full">
                    <label for="user_id">Vendedor responsável</label>
                    @if ($podeTrocarResponsavel)
                    <select id="user_id" name="user_id" required>
                        @unless ($vendedores->contains('id', $pedido->user_id))
                        <option value="{{ $pedido->user_id }}" selected>{{ $pedido->user->codigo_usuario }} - {{ $pedido->user->name }}</option>
                        @endunless
                        @foreach ($vendedores as $vendedor)
                        <option value="{{ $vendedor->id }}" @selected(old('user_id', $pedido->user_id) == $vendedor->id)>{{ $vendedor->codigo_usuario }} - {{ $vendedor->name }} ({{ $vendedor->estabelecimento?->razao_social }})</option>
                        @endforeach
                    </select>
                    @error('user_id')
                    <span class="erro-campo">{{ $message }}</span>
                    @enderror
                    @else
                    <input type="text" id="user_id" readonly value="{{ $pedido->user->codigo_usuario }} - {{ $pedido->user->name }}" aria-describedby="dica-responsavel">
                    <p class="dica-campo" id="dica-responsavel">Só gestores e administradores podem trocar o responsável.</p>
                    @endif
                </div>
                <div class="label-input campo-full">
                    <label for="observacao">Observação</label>
                    <textarea id="observacao" name="observacao" maxlength="255">{{ old('observacao', $pedido->observacao) }}</textarea>
                    @error('observacao')
                    <span class="erro-campo">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </section>
    </div>
</form>
@endsection
