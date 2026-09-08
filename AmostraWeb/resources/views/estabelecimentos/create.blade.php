@extends('layouts.app')

@section('title',"Estabelecimentos - ".config('amostraweb.name'))

@section('texto','Estabelecimentos')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/estabelecimento/create.css') }}">
@endpush

@section('content')
<div class="toolbar">
    <h1><a href="{{ route('estabelecimentos.index') }}">Estabelecimentos</a> > Novo</h1>
</div>

<form action="{{ route('estabelecimentos.store') }}" method="post" class="form-estabelecimento">
    @csrf

    <div class="form-grupo grupo-full">
        <label for="razao_social">Razão social</label>
        <input type="text" name="razao_social" required id="razao_social" placeholder="Digite a Razão Social" value="{{ old('razao_social') }}">
        @error('razao_social')
        <p class="erro-campo">{{ $message }}</p>
        @enderror
    </div>

    <div class="form-grupo">
        <label for="cnpj">CNPJ</label>
        <input type="text" name="cnpj" required id="cnpj" placeholder="00.000.000/0000-00" value="{{ old('cnpj') }}">
        @error('cnpj')
        <p class="erro-campo">{{ $message }}</p>
        @enderror
    </div>

    <div class="form-grupo">
        <label for="gestor_id">Gestor</label>
        <select name="gestor_id" id="gestor_id">
            <option value="">Sem gestor</option>
            @foreach ($gestores as $gestor)
            <option value="{{ $gestor->id }}" @selected(old('gestor_id')==$gestor->id)>{{ $gestor->codigo_usuario }}</option>
            @endforeach
        </select>
        @error('gestor_id')
        <p class="erro-campo">{{ $message }}</p>
        @enderror
    </div>

    <div class="form-grupo">
        <label for="cep">CEP</label>
        <input type="text" name="cep" id="cep" placeholder="00000-000" value="{{ old('cep') }}">
        @error('cep')
        <p class="erro-campo">{{ $message }}</p>
        @enderror
    </div>

    <div class="form-grupo">
        <label for="uf">UF</label>
        <select name="uf" id="uf" required>
            <option value="">Selecione</option>
            @foreach (['AC','AL','AP','AM','BA','CE','DF','ES','GO','MA','MT','MS','MG','PA','PB','PR','PE','PI','RJ','RN','RS','RO','RR','SC','SP','SE','TO'] as $sigla)
            <option value="{{ $sigla }}" @selected(old('uf')==$sigla)>{{ $sigla }}</option>
            @endforeach
        </select>
        @error('uf')
        <p class="erro-campo">{{ $message }}</p>
        @enderror
    </div>

    <div class="form-grupo">
        <label for="cidade">Cidade</label>
        <input type="text" name="cidade" required id="cidade" placeholder="Digite a cidade" value="{{ old('cidade') }}">
        @error('cidade')
        <p class="erro-campo">{{ $message }}</p>
        @enderror
    </div>

    <div class="form-grupo">
        <label for="bairro">Bairro</label>
        <input type="text" name="bairro" id="bairro" placeholder="Digite o bairro" value="{{ old('bairro') }}">
        @error('bairro')
        <p class="erro-campo">{{ $message }}</p>
        @enderror
    </div>

    <div class="form-grupo">
        <label for="rua">Rua</label>
        <input type="text" name="rua" id="rua" placeholder="Digite a rua" value="{{ old('rua') }}">
        @error('rua')
        <p class="erro-campo">{{ $message }}</p>
        @enderror
    </div>

    <div class="form-grupo">
        <label for="numero">Número</label>
        <input type="text" name="numero" id="numero" placeholder="Nº" value="{{ old('numero') }}">
        @error('numero')
        <p class="erro-campo">{{ $message }}</p>
        @enderror
    </div>

    <div class="form-grupo grupo-full grupo-checkbox">
        <input type="hidden" name="status" value="0">
        <label for="status" class="label-checkbox">
            <input
                type="checkbox"
                name="status"
                id="status"
                value="1"
                @checked(old('status', true))>
            Ativo
        </label>
    </div>

    <div class="form-acoes">
        <a href="{{ route('estabelecimentos.index') }}" class="botao-cancelar">Cancelar</a>
        <button type="submit" class="botao-salvar">Salvar</button>
    </div>
</form>
@endsection