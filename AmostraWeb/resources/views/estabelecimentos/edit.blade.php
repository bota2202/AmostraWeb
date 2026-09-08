@extends('layouts.app')

@section('title','Editar Estabelecimento - '.config('amostraweb.name'))

@section('texto','Estabelecimentos')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/estabelecimento/show.css') }}">
<link rel="stylesheet" href="{{ asset('css/estabelecimento/edit.css') }}">
@endpush

@section('content')
<div class="toolbar">
    <h1>
        <a href="{{ route('estabelecimentos.index') }}">Estabelecimentos</a> >
        <a href="{{ route('estabelecimentos.show',$estabelecimento) }}">#{{ $estabelecimento->id }}</a> >
        Editar
    </h1>
    <div class="acoes-toolbar">
        <a class="botao-toolbar botao-secundario" href="{{ route('estabelecimentos.show',$estabelecimento) }}">
            <i class="fa-solid fa-xmark"></i> Cancelar
        </a>
        <button type="submit" form="form-estabelecimento" class="botao-toolbar">
            <i class="fa-solid fa-check"></i> Salvar Alterações
        </button>
    </div>
</div>

<form id="form-estabelecimento" action="{{ route('estabelecimentos.update',$estabelecimento) }}" method="POST">
    @csrf
    @method('PUT')

    <div id="dados">
        <section class="card-secao" id="identificacao">
            <h3><i class="fa-solid fa-id-card"></i> Identificação</h3>
            <div class="campos-secao">
                <div class="label-input">
                    <label for="razao_social">Razão Social</label>
                    <input
                        type="text"
                        id="razao_social"
                        name="razao_social"
                        value="{{ old('razao_social', $estabelecimento->razao_social) }}"
                        maxlength="255"
                        required>
                    @error('razao_social')
                    <span class="erro-campo">{{ $message }}</span>
                    @enderror
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
                <div class="label-input campo-full">
                    <label for="gestor_id">Gestor</label>
                    <select id="gestor_id" name="gestor_id">
                        <option value="">Sem gestor</option>
                        @foreach($gestores as $gestor)
                        <option
                            value="{{ $gestor->id }}"
                            {{ old('gestor_id', $estabelecimento->gestor_id) == $gestor->id ? 'selected' : '' }}>
                            #{{ $gestor->id }} - {{ $gestor->codigo_usuario }} - {{ $gestor->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('gestor_id')
                    <span class="erro-campo">{{ $message }}</span>
                    @enderror
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
                    <input
                        type="text"
                        id="bairro"
                        name="bairro"
                        value="{{ old('bairro', $estabelecimento->bairro) }}"
                        maxlength="255">
                    @error('bairro')
                    <span class="erro-campo">{{ $message }}</span>
                    @enderror
                </div>
                <div class="label-input">
                    <label for="rua">Rua</label>
                    <input
                        type="text"
                        id="rua"
                        name="rua"
                        value="{{ old('rua', $estabelecimento->rua) }}"
                        maxlength="255">
                    @error('rua')
                    <span class="erro-campo">{{ $message }}</span>
                    @enderror
                </div>
                <div class="label-input">
                    <label for="numero">Número</label>
                    <input
                        type="text"
                        id="numero"
                        name="numero"
                        value="{{ old('numero', $estabelecimento->numero) }}"
                        maxlength="255">
                    @error('numero')
                    <span class="erro-campo">{{ $message }}</span>
                    @enderror
                </div>
                <div class="label-input">
                    <label for="cep">CEP</label>
                    <input
                        type="text"
                        id="cep"
                        name="cep"
                        value="{{ old('cep', $estabelecimento->cep) }}"
                        placeholder="00000-000"
                        maxlength="9">
                    @error('cep')
                    <span class="erro-campo">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </section>
    </div>
</form>
@endsection