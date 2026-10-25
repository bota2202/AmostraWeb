@extends('layouts.app')

@section('title','Novo produto - '.config('amostraweb.name'))

@section('texto','Produtos')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/layout/form.css') }}">
@endpush

@section('content')
<div class="toolbar">
    <h1><a href="{{ route('produtos.index') }}">Produtos</a> › Novo</h1>
    @permite('produtos', 'importar')
    <a href="{{ route('produtos.importar') }}" class="botao-toolbar botao-secundario">
        <i class="fa-solid fa-file-import" aria-hidden="true"></i> Importar vários do ERP
    </a>
    @endpermite
</div>

<form action="{{ route('produtos.store') }}" method="post" class="form-cadastro">
    @csrf

    <h2 class="form-titulo-secao"><i class="fa-solid fa-tag" aria-hidden="true"></i> Identificação</h2>

    <div class="form-grupo">
        <label for="codigo_produto">Código do produto <span class="obrigatorio" aria-hidden="true">*</span></label>
        <input type="text" name="codigo_produto" required id="codigo_produto" maxlength="30" placeholder="Ex.: 110.30949" value="{{ old('codigo_produto') }}" aria-describedby="dica-codigo">
        <p class="dica-campo" id="dica-codigo">O mesmo código usado no ERP.</p>
        @error('codigo_produto')
        <p class="erro-campo">{{ $message }}</p>
        @enderror
    </div>

    <div class="form-grupo grupo-duplo">
        <label for="descricao">Descrição <span class="obrigatorio" aria-hidden="true">*</span></label>
        <input type="text" name="descricao" required id="descricao" maxlength="255" value="{{ old('descricao') }}">
        @error('descricao')
        <p class="erro-campo">{{ $message }}</p>
        @enderror
    </div>

    <div class="form-grupo">
        <label for="marca">Marca</label>
        <input type="text" name="marca" id="marca" maxlength="255" value="{{ old('marca') }}">
        @error('marca')
        <p class="erro-campo">{{ $message }}</p>
        @enderror
    </div>

    <div class="form-grupo">
        <label for="data_revisao">Data de revisão</label>
        <input type="date" name="data_revisao" id="data_revisao" value="{{ old('data_revisao') }}">
        @error('data_revisao')
        <p class="erro-campo">{{ $message }}</p>
        @enderror
    </div>

    <div class="form-grupo grupo-checkbox">
        <input type="hidden" name="status" value="0">
        <label for="status" class="label-checkbox">
            <input type="checkbox" name="status" id="status" value="1" @checked(old('status', true))>
            Produto ativo
        </label>
    </div>

    @foreach (\App\Models\Produto::SECOES as $titulo => $secao)
    <h2 class="form-titulo-secao"><i class="fa-solid {{ $secao['icone'] }}" aria-hidden="true"></i> {{ $titulo }}</h2>
        @foreach ($secao['campos'] as $campo => $rotulo)
        <div class="form-grupo">
            <label for="{{ $campo }}">{{ $rotulo }}</label>
            <input type="text" name="{{ $campo }}" id="{{ $campo }}" maxlength="255" value="{{ old($campo) }}">
            @error($campo)
            <p class="erro-campo">{{ $message }}</p>
            @enderror
        </div>
        @endforeach
    @endforeach

    <div class="form-acoes">
        <a href="{{ route('produtos.index') }}" class="botao-cancelar">Cancelar</a>
        <button type="submit" class="botao-salvar">Salvar produto</button>
    </div>
</form>
@endsection
