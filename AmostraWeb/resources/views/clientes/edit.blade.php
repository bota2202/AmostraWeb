@extends('layouts.app')

@section('title','Editar Cliente - '.config('amostraweb.name'))

@section('texto','Clientes')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/layout/detalhes.css') }}">
@endpush

@section('content')
<div class="toolbar">
    <h1>
        <a href="{{ route('clientes.index') }}">Clientes</a> >
        <a href="{{ route('clientes.show',$cliente) }}">{{ $cliente->codigo_empresa }}</a> >
        Editar
    </h1>
    <div class="acoes-toolbar">
        <a class="botao-toolbar botao-secundario" href="{{ route('clientes.show',$cliente) }}">
            <i class="fa-solid fa-xmark"></i> Cancelar
        </a>
        <button type="submit" form="form-cliente" class="botao-toolbar">
            <i class="fa-solid fa-check"></i> Salvar Alterações
        </button>
    </div>
</div>

<form id="form-cliente" action="{{ route('clientes.update',$cliente) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="dados">
        <section class="card-secao">
            <h3><i class="fa-solid fa-id-card"></i> Identificação</h3>
            <div class="campos-secao">
                <div class="label-input">
                    <label for="codigo_empresa">Código da empresa</label>
                    <input type="text" id="codigo_empresa" disabled value="{{ $cliente->codigo_empresa }}">
                </div>
                <div class="label-input">
                    <label for="cnpj_cpf">CNPJ / CPF</label>
                    <input type="text" id="cnpj_cpf" disabled value="{{ $cliente->cnpj_cpf }}">
                </div>
                <span class="badge-status {{ $cliente->status ? 'ativo' : 'inativo' }}">
                    <i class="fa-solid fa-circle"></i>
                    {{ $cliente->status ? 'Ativo' : 'Inativo' }}
                </span>
                <div class="label-input campo-duplo">
                    <label for="razao_social">Razão social</label>
                    <input type="text" id="razao_social" name="razao_social" maxlength="255" required value="{{ old('razao_social', $cliente->razao_social) }}">
                    @error('razao_social')
                    <span class="erro-campo">{{ $message }}</span>
                    @enderror
                </div>
                <div class="label-input">
                    <label for="nome_fantasia">Nome fantasia</label>
                    <input type="text" id="nome_fantasia" name="nome_fantasia" maxlength="255" value="{{ old('nome_fantasia', $cliente->nome_fantasia) }}">
                    @error('nome_fantasia')
                    <span class="erro-campo">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </section>

        <section class="card-secao">
            <h3><i class="fa-solid fa-layer-group"></i> Classificação</h3>
            <div class="campos-secao">
                <div class="label-input">
                    <label for="tipo">Tipo</label>
                    <input type="text" id="tipo" name="tipo" maxlength="50" required value="{{ old('tipo', $cliente->tipo) }}">
                    @error('tipo')
                    <span class="erro-campo">{{ $message }}</span>
                    @enderror
                </div>
                <div class="label-input">
                    <label for="tipo_empresa">Tipo de empresa</label>
                    <input type="text" id="tipo_empresa" name="tipo_empresa" maxlength="50" value="{{ old('tipo_empresa', $cliente->tipo_empresa) }}">
                    @error('tipo_empresa')
                    <span class="erro-campo">{{ $message }}</span>
                    @enderror
                </div>
                <div class="label-input">
                    <label for="grupo_fiscal">Grupo fiscal</label>
                    <input type="text" id="grupo_fiscal" name="grupo_fiscal" maxlength="50" value="{{ old('grupo_fiscal', $cliente->grupo_fiscal) }}">
                    @error('grupo_fiscal')
                    <span class="erro-campo">{{ $message }}</span>
                    @enderror
                </div>
                <div class="label-input">
                    <label for="grupo_economico">Grupo econômico</label>
                    <input type="text" id="grupo_economico" name="grupo_economico" maxlength="100" value="{{ old('grupo_economico', $cliente->grupo_economico) }}">
                    @error('grupo_economico')
                    <span class="erro-campo">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </section>

        <section class="card-secao">
            <h3><i class="fa-solid fa-address-book"></i> Contato</h3>
            <div class="campos-secao">
                <div class="label-input">
                    <label for="telefone">Telefone</label>
                    <input type="text" id="telefone" name="telefone" maxlength="20" value="{{ old('telefone', $cliente->telefone) }}">
                    @error('telefone')
                    <span class="erro-campo">{{ $message }}</span>
                    @enderror
                </div>
                <div class="label-input campo-duplo">
                    <label for="email">E-mail</label>
                    <input type="email" id="email" name="email" maxlength="255" value="{{ old('email', $cliente->email) }}">
                    @error('email')
                    <span class="erro-campo">{{ $message }}</span>
                    @enderror
                </div>
                <div class="label-input campo-full">
                    <label for="endereco_completo">Endereço completo</label>
                    <textarea id="endereco_completo" name="endereco_completo" maxlength="500">{{ old('endereco_completo', $cliente->endereco_completo) }}</textarea>
                    @error('endereco_completo')
                    <span class="erro-campo">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </section>
    </div>
</form>

@include('partials.acoes-registro', [
    'rotaStatus' => route('clientes.status', $cliente),
    'ativo' => $cliente->status,
    'rotaExcluir' => route('clientes.destroy', $cliente),
    'descricao' => 'o cliente '.$cliente->codigo_empresa,
])
@endsection
