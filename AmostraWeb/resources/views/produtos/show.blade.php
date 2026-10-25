@extends('layouts.app')

@section('title','Produto '.$produto->codigo_produto.' - '.config('amostraweb.name'))

@section('texto','Produtos')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/layout/detalhes.css') }}">
@endpush

@section('content')
<div class="toolbar">
    <h1><a href="{{ route('produtos.index') }}">Produtos</a> › {{ $produto->codigo_produto }}</h1>
    @permite('produtos', 'editar')
    <a class="botao-toolbar" href="{{ route('produtos.edit', $produto) }}"><i class="fa-solid fa-pen" aria-hidden="true"></i> Editar produto</a>
    @endpermite
</div>

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
                <input type="text" id="marca" readonly value="{{ $produto->marca ?? '-' }}">
            </div>
            <span class="badge-status {{ $produto->status ? 'ativo' : 'inativo' }}">
                <i class="fa-solid fa-circle" aria-hidden="true"></i>
                {{ $produto->status ? 'Ativo' : 'Inativo' }}
            </span>
            <div class="label-input campo-duplo">
                <label for="descricao">Descrição</label>
                <input type="text" id="descricao" readonly value="{{ $produto->descricao }}">
            </div>
            <div class="label-input">
                <label for="data_revisao">Data de revisão</label>
                <input type="text" id="data_revisao" readonly value="{{ $produto->data_revisao?->format('d/m/Y') ?? '-' }}">
            </div>
            <div class="label-input">
                <label for="amostras">Amostras enviadas</label>
                <input type="text" id="amostras" readonly value="{{ $produto->amostras_count }}">
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
                <input type="text" id="{{ $campo }}" readonly value="{{ $produto->$campo ?? '-' }}">
            </div>
            @endforeach
        </div>
    </section>
    @endforeach
</div>
@endsection
