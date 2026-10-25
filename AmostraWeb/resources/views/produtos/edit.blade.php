@extends('layouts.app')

@section('title','Editar produto '.$produto->codigo_produto.' - '.config('amostraweb.name'))

@section('texto','Produtos')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/layout/detalhes.css') }}">
@endpush

@section('content')
<div class="toolbar">
    <h1>
        <a href="{{ route('produtos.index') }}">Produtos</a> › <a href="{{ route('produtos.show', $produto) }}">{{ $produto->codigo_produto }}</a> › Editar
    </h1>
    <div class="acoes-toolbar">
        <a class="botao-toolbar botao-secundario" href="{{ route('produtos.show', $produto) }}">
            <i class="fa-solid fa-xmark" aria-hidden="true"></i> Cancelar
        </a>
        <button type="submit" form="form-produto" class="botao-toolbar">
            <i class="fa-solid fa-check" aria-hidden="true"></i> Salvar alterações
        </button>
    </div>
</div>

<form id="form-produto" action="{{ route('produtos.update', $produto) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="dados">
        <section class="card-secao" aria-labelledby="titulo-identificacao">
            <h2 id="titulo-identificacao"><i class="fa-solid fa-tag" aria-hidden="true"></i> Identificação</h2>
            <div class="campos-secao">
                <div class="label-input">
                    <label for="codigo_produto">Código</label>
                    <input type="text" id="codigo_produto" readonly value="{{ $produto->codigo_produto }}">
                </div>
                <div class="label-input">
                    <label for="marca">Marca</label>
                    <input type="text" id="marca" name="marca" maxlength="255" value="{{ old('marca', $produto->marca) }}">
                    @error('marca')
                    <span class="erro-campo">{{ $message }}</span>
                    @enderror
                </div>
                <span class="badge-status {{ $produto->status ? 'ativo' : 'inativo' }}">
                    <i class="fa-solid fa-circle" aria-hidden="true"></i>
                    {{ $produto->status ? 'Ativo' : 'Inativo' }}
                </span>
                <div class="label-input campo-duplo">
                    <label for="descricao">Descrição</label>
                    <input type="text" id="descricao" name="descricao" maxlength="255" required value="{{ old('descricao', $produto->descricao) }}">
                    @error('descricao')
                    <span class="erro-campo">{{ $message }}</span>
                    @enderror
                </div>
                <div class="label-input">
                    <label for="data_revisao">Data de revisão</label>
                    <input type="date" id="data_revisao" name="data_revisao" value="{{ old('data_revisao', $produto->data_revisao?->format('Y-m-d')) }}">
                    @error('data_revisao')
                    <span class="erro-campo">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </section>

        @foreach (\App\Models\Produto::SECOES as $titulo => $secao)
        <section class="card-secao" aria-labelledby="titulo-{{ \Illuminate\Support\Str::slug($titulo) }}">
            <h2 id="titulo-{{ \Illuminate\Support\Str::slug($titulo) }}"><i class="fa-solid {{ $secao['icone'] }}" aria-hidden="true"></i> {{ $titulo }}</h2>
            <div class="campos-secao">
                @foreach ($secao['campos'] as $campo => $rotulo)
                <div class="label-input">
                    <label for="{{ $campo }}">{{ $rotulo }}</label>
                    <input type="text" id="{{ $campo }}" name="{{ $campo }}" maxlength="255" value="{{ old($campo, $produto->$campo) }}">
                    @error($campo)
                    <span class="erro-campo">{{ $message }}</span>
                    @enderror
                </div>
                @endforeach
            </div>
        </section>
        @endforeach
    </div>
</form>

@include('partials.acoes-registro', [
    'rotaStatus' => route('produtos.status', $produto),
    'ativo' => $produto->status,
    'rotaExcluir' => auth()->user()->temPermissao('produtos', 'excluir') ? route('produtos.destroy', $produto) : null,
    'descricao' => 'o produto '.$produto->codigo_produto,
])
@endsection
