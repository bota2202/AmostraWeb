@extends('layouts.app')

@section('title','Editar amostra '.$amostra->codigo.' - '.config('amostraweb.name'))

@section('texto','Amostras')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/layout/detalhes.css') }}">
@endpush

@section('content')
<div class="toolbar">
    <h1>
        <a href="{{ route('amostras.index') }}">Amostras</a> ›
        <a href="{{ route('amostras.show', $amostra) }}">{{ $amostra->codigo }}</a> ›
        Editar
    </h1>
    <div class="acoes-toolbar">
        <a class="botao-toolbar botao-secundario" href="{{ route('amostras.show', $amostra) }}">
            <i class="fa-solid fa-xmark" aria-hidden="true"></i> Cancelar
        </a>
        <button type="submit" form="form-amostra" class="botao-toolbar">
            <i class="fa-solid fa-check" aria-hidden="true"></i> Salvar alterações
        </button>
    </div>
</div>

<div class="alerta alerta-info" role="note">
    <i class="fa-solid fa-circle-info" aria-hidden="true"></i>
    <span>O resultado (aprovada/reprovada) e o cancelamento são registrados na tela da amostra. O prazo muda por solicitação de prazo aprovada.</span>
</div>

<form id="form-amostra" action="{{ route('amostras.update', $amostra) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="dados">
        <section class="card-secao" aria-labelledby="titulo-amostra">
            <h2 id="titulo-amostra"><i class="fa-solid fa-flask" aria-hidden="true"></i> Amostra</h2>
            <div class="campos-secao">
                <div class="label-input">
                    <label for="codigo">Código</label>
                    <input type="text" id="codigo" readonly value="{{ $amostra->codigo }}">
                </div>
                <div class="label-input">
                    <label for="pedido">Pedido</label>
                    {{-- a amostra não pode trocar de pedido (RN024) --}}
                    <input type="text" id="pedido" readonly value="{{ $amostra->pedido?->codigo ?? "Sem pedido" }} - {{ $amostra->pedido?->cliente?->razao_social ?? "-" }}">
                </div>
                <span class="badge-status {{ $amostra->status }}">
                    <i class="fa-solid fa-circle" aria-hidden="true"></i>
                    {{ $amostra->status_nome }}
                </span>
                <div class="label-input campo-duplo">
                    <label for="produto_id">Produto</label>
                    <select id="produto_id" name="produto_id" required>
                        @unless ($produtos->contains('id', $amostra->produto_id))
                        <option value="{{ $amostra->produto_id }}" selected>{{ $amostra->produto->codigo_produto }} - {{ $amostra->produto->descricao }} (inativo)</option>
                        @endunless
                        @foreach ($produtos as $produto)
                        <option value="{{ $produto->id }}" @selected(old('produto_id', $amostra->produto_id) == $produto->id)>{{ $produto->codigo_produto }} - {{ $produto->descricao }}</option>
                        @endforeach
                    </select>
                    @error('produto_id')
                    <span class="erro-campo">{{ $message }}</span>
                    @enderror
                </div>
                <div class="label-input">
                    <label for="quantidade">Quantidade</label>
                    <input type="number" id="quantidade" name="quantidade" min="1" step="1" required value="{{ old('quantidade', $amostra->quantidade) }}">
                    @error('quantidade')
                    <span class="erro-campo">{{ $message }}</span>
                    @enderror
                </div>
                <div class="label-input">
                    <label for="prazo_avaliacao">Prazo de avaliação</label>
                    <input type="text" id="prazo_avaliacao" readonly value="{{ $amostra->prazo_avaliacao->format('d/m/Y') }}">
                </div>
            </div>
        </section>
    </div>
</form>
@endsection
