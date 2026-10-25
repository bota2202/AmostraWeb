@extends('layouts.app')

@section('title','Editar Amostra - '.config('amostraweb.name'))

@section('texto','Amostras')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/layout/detalhes.css') }}">
@endpush

@section('content')
<div class="toolbar">
    <h1>
        <a href="{{ route('amostras.index') }}">Amostras</a> >
        <a href="{{ route('amostras.show',$amostra) }}">{{ $amostra->codigo }}</a> >
        Editar
    </h1>
    <div class="acoes-toolbar">
        <a class="botao-toolbar botao-secundario" href="{{ route('amostras.show',$amostra) }}">
            <i class="fa-solid fa-xmark"></i> Cancelar
        </a>
        <button type="submit" form="form-amostra" class="botao-toolbar">
            <i class="fa-solid fa-check"></i> Salvar Alterações
        </button>
    </div>
</div>

<form id="form-amostra" action="{{ route('amostras.update',$amostra) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="dados">
        <section class="card-secao">
            <h3><i class="fa-solid fa-flask"></i> Amostra</h3>
            <div class="campos-secao">
                <div class="label-input">
                    <label for="codigo">Código</label>
                    <input type="text" id="codigo" disabled value="{{ $amostra->codigo }}">
                </div>
                <div class="label-input">
                    <label for="quantidade">Quantidade</label>
                    <input type="number" id="quantidade" name="quantidade" min="1" step="1" required value="{{ old('quantidade', $amostra->quantidade) }}">
                    @error('quantidade')
                    <span class="erro-campo">{{ $message }}</span>
                    @enderror
                </div>
                <div class="label-input">
                    <label for="status">Status</label>
                    <select id="status" name="status" required>
                        @foreach (\App\Models\Amostra::STATUS as $valor => $nome)
                        <option value="{{ $valor }}" @selected(old('status', $amostra->status) === $valor)>{{ $nome }}</option>
                        @endforeach
                    </select>
                    @error('status')
                    <span class="erro-campo">{{ $message }}</span>
                    @enderror
                </div>
                <div class="label-input">
                    <label for="prazo_avaliacao">Prazo de avaliação</label>
                    <input type="text" id="prazo_avaliacao" disabled value="{{ $amostra->prazo_avaliacao->format('d/m/Y') }}" title="Para alterar o prazo, faça uma solicitação de prazo na tela da amostra">
                </div>
            </div>
        </section>

        <section class="card-secao">
            <h3><i class="fa-solid fa-link"></i> Vínculos</h3>
            <div class="campos-secao">
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
                    <label for="pedido_id">Pedido</label>
                    <select id="pedido_id" name="pedido_id">
                        <option value="">Sem pedido</option>
                        @if ($amostra->pedido && ! $pedidos->contains('id', $amostra->pedido_id))
                        <option value="{{ $amostra->pedido_id }}" @selected(old('pedido_id', $amostra->pedido_id) == $amostra->pedido_id)>{{ $amostra->pedido->codigo }} (fechado)</option>
                        @endif
                        @foreach ($pedidos as $pedido)
                        <option value="{{ $pedido->id }}" @selected(old('pedido_id', $amostra->pedido_id) == $pedido->id)>{{ $pedido->codigo }} - {{ $pedido->cliente->razao_social }}</option>
                        @endforeach
                    </select>
                    @error('pedido_id')
                    <span class="erro-campo">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </section>
    </div>
</form>

@include('partials.acoes-registro', [
    'rotaExcluir' => route('amostras.destroy', $amostra),
    'descricao' => 'a amostra '.$amostra->codigo,
])
@endsection
