@extends('layouts.app')

@section('title','Editar Pedido - '.config('amostraweb.name'))

@section('texto','Pedidos')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/layout/detalhes.css') }}">
@endpush

@section('content')
<div class="toolbar">
    <h1>
        <a href="{{ route('pedidos.index') }}">Pedidos</a> >
        <a href="{{ route('pedidos.show',$pedido) }}">{{ $pedido->codigo }}</a> >
        Editar
    </h1>
    <div class="acoes-toolbar">
        <a class="botao-toolbar botao-secundario" href="{{ route('pedidos.show',$pedido) }}">
            <i class="fa-solid fa-xmark"></i> Cancelar
        </a>
        <button type="submit" form="form-pedido" class="botao-toolbar">
            <i class="fa-solid fa-check"></i> Salvar Alterações
        </button>
    </div>
</div>

<form id="form-pedido" action="{{ route('pedidos.update',$pedido) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="dados">
        <section class="card-secao">
            <h3><i class="fa-solid fa-receipt"></i> Pedido</h3>
            <div class="campos-secao">
                <div class="label-input">
                    <label for="codigo">Código</label>
                    <input type="text" id="codigo" disabled value="{{ $pedido->codigo }}">
                </div>
                <div class="label-input">
                    <label for="data_emissao">Data de emissão</label>
                    <input type="date" id="data_emissao" name="data_emissao" required value="{{ old('data_emissao', $pedido->data_emissao->format('Y-m-d')) }}">
                    @error('data_emissao')
                    <span class="erro-campo">{{ $message }}</span>
                    @enderror
                </div>
                <div class="label-input">
                    <label for="status">Status</label>
                    <select id="status" name="status" required>
                        @foreach (\App\Models\Pedido::STATUS as $valor => $nome)
                        <option value="{{ $valor }}" @selected(old('status', $pedido->status) === $valor)>{{ $nome }}</option>
                        @endforeach
                    </select>
                    @error('status')
                    <span class="erro-campo">{{ $message }}</span>
                    @enderror
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
                    <label for="estabelecimento_id">Estabelecimento</label>
                    <select id="estabelecimento_id" name="estabelecimento_id" required>
                        @unless ($estabelecimentos->contains('id', $pedido->estabelecimento_id))
                        <option value="{{ $pedido->estabelecimento_id }}" selected>#{{ $pedido->estabelecimento_id }} - {{ $pedido->estabelecimento->razao_social }} (inativo)</option>
                        @endunless
                        @foreach ($estabelecimentos as $estab)
                        <option value="{{ $estab->id }}" @selected(old('estabelecimento_id', $pedido->estabelecimento_id) == $estab->id)>#{{ $estab->id }} - {{ $estab->razao_social }}</option>
                        @endforeach
                    </select>
                    @error('estabelecimento_id')
                    <span class="erro-campo">{{ $message }}</span>
                    @enderror
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

@include('partials.acoes-registro', [
    'rotaExcluir' => route('pedidos.destroy', $pedido),
    'descricao' => 'o pedido '.$pedido->codigo,
])
@endsection
