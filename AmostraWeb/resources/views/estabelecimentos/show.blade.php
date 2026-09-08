@extends('layouts.app')

@section('title','Estabelecimentos - '.config('amostraweb.name'))

@section('texto','Estabelecimentos')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/estabelecimento/show.css') }}">
@endpush

@section('content')
<div class="toolbar">
    <h1><a href="{{ route('estabelecimentos.index') }}">Estabelecimentos </a>> #{{ $estabelecimento->id }}</h1>
    <a class="botao-toolbar" href="{{ route('estabelecimentos.edit',$estabelecimento) }}"><i class="fa-solid fa-pen"></i> Editar Estabelecimento</a>
</div>

<div id="dados">
    <section class="card-secao" id="identificacao">
        <h3><i class="fa-solid fa-id-card"></i> Identificação</h3>
        <div class="campos-secao">
            <div class="label-input">
                <label for="razao_social">Razão Social</label>
                <input type="text" id="razao_social" disabled value="{{ $estabelecimento->razao_social }}">
            </div>
            <div class="label-input">
                <label for="cnpj">CNPJ</label>
                <input type="text" id="cnpj" disabled value="{{ $estabelecimento->cnpj }}">
            </div>
            <span class="badge-status {{ $estabelecimento->status == 1 ? 'ativo' : 'inativo' }}">
                <i class="fa-solid fa-circle"></i>
                {{ $estabelecimento->status == 1 ? 'Ativo' : 'Inativo' }}
            </span>
        </div>
    </section>

    <section class="card-secao" id="gestao">
        <h3><i class="fa-solid fa-user-tie"></i> Gestão</h3>
        <div class="campos-secao">
            <div class="label-input">
                <label for="gestor_id">ID</label>
                <input type="text" id="gestor_id" disabled value="{{ $estabelecimento->gestor_id ? $estabelecimento->gestor_id : '-' }}">
            </div>
            <div class="label-input">
                <label for="codigo_gestor">Código de usuário</label>
                <input type="text" id="codigo_gestor" disabled value="{{ $estabelecimento->gestor?->codigo_usuario ?? "-" }}">
            </div>
            <div class="label-input">
                <label for="gestor_nome">Nome</label>
                <input type="text" id="gestor_nome" disabled value="{{ $estabelecimento->gestor?->name ?? "-" }}">
            </div>
        </div>
    </section>

    <section class="card-secao" id="endereco">
        <h3><i class="fa-solid fa-location-dot"></i> Endereço</h3>
        <div class="campos-secao">
            <div class="label-input">
                <label for="uf">Estado</label>
                <input type="text" id="uf" disabled value="{{ $estabelecimento->uf }}">
            </div>
            <div class="label-input">
                <label for="cidade">Cidade</label>
                <input type="text" id="cidade" disabled value="{{ $estabelecimento->cidade }}">
            </div>
            <div class="label-input">
                <label for="bairro">Bairro</label>
                <input type="text" id="bairro" disabled value="{{ $estabelecimento->bairro }}">
            </div>
            <div class="label-input">
                <label for="rua">Rua</label>
                <input type="text" id="rua" disabled value="{{ $estabelecimento->rua }}">
            </div>
            <div class="label-input">
                <label for="numero">Número</label>
                <input type="text" id="numero" disabled value="{{ $estabelecimento->numero }}">
            </div>
            <div class="label-input">
                <label for="cep">CEP</label>
                <input type="text" id="cep" disabled value="{{ $estabelecimento->cep }}">
            </div>
        </div>
    </section>
</div>
@endsection