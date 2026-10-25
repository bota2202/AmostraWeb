@extends('layouts.app')

@section('title','Novo Cliente - '.config('amostraweb.name'))

@section('texto','Clientes')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/layout/form.css') }}">
@endpush

@section('content')
<div class="toolbar">
    <h1><a href="{{ route('clientes.index') }}">Clientes</a> > Novo</h1>
</div>

<form action="{{ route('clientes.store') }}" method="post" class="form-cadastro">
    @csrf

    <div class="form-grupo">
        <label for="codigo_empresa">Código da empresa</label>
        <input type="text" name="codigo_empresa" required id="codigo_empresa" maxlength="20" placeholder="Código no sistema" value="{{ old('codigo_empresa') }}">
        @error('codigo_empresa')
        <p class="erro-campo">{{ $message }}</p>
        @enderror
    </div>

    <div class="form-grupo grupo-duplo">
        <label for="razao_social">Razão social</label>
        <input type="text" name="razao_social" required id="razao_social" maxlength="255" placeholder="Digite a Razão Social" value="{{ old('razao_social') }}">
        @error('razao_social')
        <p class="erro-campo">{{ $message }}</p>
        @enderror
    </div>

    <div class="form-grupo">
        <label for="cnpj_cpf">CNPJ / CPF</label>
        <input type="text" name="cnpj_cpf" required id="cnpj_cpf" maxlength="18" placeholder="00.000.000/0000-00 ou 000.000.000-00" value="{{ old('cnpj_cpf') }}">
        @error('cnpj_cpf')
        <p class="erro-campo">{{ $message }}</p>
        @enderror
    </div>

    <div class="form-grupo grupo-duplo">
        <label for="nome_fantasia">Nome fantasia</label>
        <input type="text" name="nome_fantasia" id="nome_fantasia" maxlength="255" placeholder="Digite o nome fantasia" value="{{ old('nome_fantasia') }}">
        @error('nome_fantasia')
        <p class="erro-campo">{{ $message }}</p>
        @enderror
    </div>

    <div class="form-grupo">
        <label for="tipo">Tipo</label>
        <input type="text" name="tipo" required id="tipo" maxlength="50" placeholder="Ex.: Revenda" value="{{ old('tipo') }}">
        @error('tipo')
        <p class="erro-campo">{{ $message }}</p>
        @enderror
    </div>

    <div class="form-grupo">
        <label for="tipo_empresa">Tipo de empresa</label>
        <input type="text" name="tipo_empresa" id="tipo_empresa" maxlength="50" value="{{ old('tipo_empresa') }}">
        @error('tipo_empresa')
        <p class="erro-campo">{{ $message }}</p>
        @enderror
    </div>

    <div class="form-grupo">
        <label for="grupo_fiscal">Grupo fiscal</label>
        <input type="text" name="grupo_fiscal" id="grupo_fiscal" maxlength="50" value="{{ old('grupo_fiscal') }}">
        @error('grupo_fiscal')
        <p class="erro-campo">{{ $message }}</p>
        @enderror
    </div>

    <div class="form-grupo">
        <label for="grupo_economico">Grupo econômico</label>
        <input type="text" name="grupo_economico" id="grupo_economico" maxlength="100" value="{{ old('grupo_economico') }}">
        @error('grupo_economico')
        <p class="erro-campo">{{ $message }}</p>
        @enderror
    </div>

    <div class="form-grupo">
        <label for="telefone">Telefone</label>
        <input type="text" name="telefone" id="telefone" maxlength="20" placeholder="(00) 00000-0000" value="{{ old('telefone') }}">
        @error('telefone')
        <p class="erro-campo">{{ $message }}</p>
        @enderror
    </div>

    <div class="form-grupo">
        <label for="email">E-mail</label>
        <input type="email" name="email" id="email" maxlength="255" placeholder="contato@cliente.com.br" value="{{ old('email') }}">
        @error('email')
        <p class="erro-campo">{{ $message }}</p>
        @enderror
    </div>

    <div class="form-grupo grupo-full">
        <label for="endereco_completo">Endereço completo</label>
        <textarea name="endereco_completo" id="endereco_completo" maxlength="500" placeholder="Rua, número, bairro, cidade - UF, CEP">{{ old('endereco_completo') }}</textarea>
        @error('endereco_completo')
        <p class="erro-campo">{{ $message }}</p>
        @enderror
    </div>

    <div class="form-grupo grupo-full grupo-checkbox">
        <input type="hidden" name="status" value="0">
        <label for="status" class="label-checkbox">
            <input type="checkbox" name="status" id="status" value="1" @checked(old('status', true))>
            Ativo
        </label>
    </div>

    <div class="form-acoes">
        <a href="{{ route('clientes.index') }}" class="botao-cancelar">Cancelar</a>
        <button type="submit" class="botao-salvar">Salvar</button>
    </div>
</form>
@endsection
