@extends('layouts.app')

@section('title','Produtos - '.config('amostraweb.name'))

@section('texto','Produtos')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/layout/detalhes.css') }}">
@endpush

@section('content')
<div class="toolbar">
    <h1><a href="{{ route('produtos.index') }}">Produtos </a>> {{ $produto->codigo_produto }}</h1>
    <a class="botao-toolbar" href="{{ route('produtos.edit',$produto) }}"><i class="fa-solid fa-pen"></i> Editar Produto</a>
</div>

<div class="dados">
    <section class="card-secao">
        <h3><i class="fa-solid fa-tag"></i> Identificação</h3>
        <div class="campos-secao">
            <div class="label-input">
                <label for="codigo_produto">Código</label>
                <input type="text" id="codigo_produto" disabled value="{{ $produto->codigo_produto }}">
            </div>
            <div class="label-input">
                <label for="marca">Marca</label>
                <input type="text" id="marca" disabled value="{{ $produto->marca ?? '-' }}">
            </div>
            <span class="badge-status {{ $produto->status ? 'ativo' : 'inativo' }}">
                <i class="fa-solid fa-circle"></i>
                {{ $produto->status ? 'Ativo' : 'Inativo' }}
            </span>
            <div class="label-input campo-full">
                <label for="descricao">Descrição</label>
                <input type="text" id="descricao" disabled value="{{ $produto->descricao }}">
            </div>
        </div>
    </section>

    <section class="card-secao">
        <h3><i class="fa-solid fa-layer-group"></i> Classificação</h3>
        <div class="campos-secao">
            <div class="label-input">
                <label for="familia">Família</label>
                <input type="text" id="familia" disabled value="{{ $produto->familia ?? '-' }}">
            </div>
            <div class="label-input">
                <label for="subfamilia">Subfamília</label>
                <input type="text" id="subfamilia" disabled value="{{ $produto->subfamilia ?? '-' }}">
            </div>
            <div class="label-input">
                <label for="grupo">Grupo</label>
                <input type="text" id="grupo" disabled value="{{ $produto->grupo ?? '-' }}">
            </div>
            <div class="label-input">
                <label for="tipo">Tipo</label>
                <input type="text" id="tipo" disabled value="{{ $produto->tipo ?? '-' }}">
            </div>
            <div class="label-input">
                <label for="data_revisao">Data de revisão</label>
                <input type="text" id="data_revisao" disabled value="{{ $produto->data_revisao?->format('d/m/Y') ?? '-' }}">
            </div>
        </div>
    </section>

    <section class="card-secao">
        <h3><i class="fa-solid fa-file-invoice"></i> Fiscal</h3>
        <div class="campos-secao">
            <div class="label-input">
                <label for="ncm">NCM</label>
                <input type="text" id="ncm" disabled value="{{ $produto->ncm ?? '-' }}">
            </div>
            <div class="label-input">
                <label for="origem_mercadoria">Origem da mercadoria</label>
                <input type="text" id="origem_mercadoria" disabled value="{{ $produto->origem_mercadoria ?? '-' }}">
            </div>
            <div class="label-input">
                <label for="controle_verba">Controle de verba</label>
                <input type="text" id="controle_verba" disabled value="{{ $produto->controle_verba ?? '-' }}">
            </div>
            <div class="label-input">
                <label for="aplicacao_entrada">Aplicação de entrada</label>
                <input type="text" id="aplicacao_entrada" disabled value="{{ $produto->aplicacao_entrada ?? '-' }}">
            </div>
            <div class="label-input">
                <label for="aplicacao_saida">Aplicação de saída</label>
                <input type="text" id="aplicacao_saida" disabled value="{{ $produto->aplicacao_saida ?? '-' }}">
            </div>
        </div>
    </section>
</div>
@endsection
