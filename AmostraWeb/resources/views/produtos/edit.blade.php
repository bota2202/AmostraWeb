@extends('layouts.app')

@section('title','Editar Produto - '.config('amostraweb.name'))

@section('texto','Produtos')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/layout/detalhes.css') }}">
@endpush

@php
    $secoes = [
        ['fa-layer-group', 'Classificação', [
            'familia' => ['Família', 20],
            'subfamilia' => ['Subfamília', 20],
            'grupo' => ['Grupo', 20],
            'tipo' => ['Tipo', 20],
        ]],
        ['fa-file-invoice', 'Fiscal', [
            'ncm' => ['NCM', 50],
            'origem_mercadoria' => ['Origem da mercadoria', 20],
            'controle_verba' => ['Controle de verba', 20],
            'aplicacao_entrada' => ['Aplicação de entrada', 20],
            'aplicacao_saida' => ['Aplicação de saída', 20],
        ]],
    ];
@endphp

@section('content')
<div class="toolbar">
    <h1>
        <a href="{{ route('produtos.index') }}">Produtos</a> >
        <a href="{{ route('produtos.show',$produto) }}">{{ $produto->codigo_produto }}</a> >
        Editar
    </h1>
    <div class="acoes-toolbar">
        <a class="botao-toolbar botao-secundario" href="{{ route('produtos.show',$produto) }}">
            <i class="fa-solid fa-xmark"></i> Cancelar
        </a>
        <button type="submit" form="form-produto" class="botao-toolbar">
            <i class="fa-solid fa-check"></i> Salvar Alterações
        </button>
    </div>
</div>

<form id="form-produto" action="{{ route('produtos.update',$produto) }}" method="POST">
    @csrf
    @method('PUT')

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
                    <input type="text" id="marca" name="marca" maxlength="255" value="{{ old('marca', $produto->marca) }}">
                    @error('marca')
                    <span class="erro-campo">{{ $message }}</span>
                    @enderror
                </div>
                <span class="badge-status {{ $produto->status ? 'ativo' : 'inativo' }}">
                    <i class="fa-solid fa-circle"></i>
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

        @foreach ($secoes as [$icone, $titulo, $campos])
        <section class="card-secao">
            <h3><i class="fa-solid {{ $icone }}"></i> {{ $titulo }}</h3>
            <div class="campos-secao">
                @foreach ($campos as $campo => [$rotulo, $max])
                <div class="label-input">
                    <label for="{{ $campo }}">{{ $rotulo }}</label>
                    <input type="text" id="{{ $campo }}" name="{{ $campo }}" maxlength="{{ $max }}" value="{{ old($campo, $produto->$campo) }}">
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
    'rotaExcluir' => route('produtos.destroy', $produto),
    'descricao' => 'o produto '.$produto->codigo_produto,
])
@endsection
