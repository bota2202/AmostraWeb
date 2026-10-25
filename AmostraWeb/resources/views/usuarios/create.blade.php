@extends('layouts.app')

@section('title','Novo Usuário - '.config('amostraweb.name'))

@section('texto','Usuários')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/layout/form.css') }}">
@endpush

@section('content')
<div class="toolbar">
    <h1><a href="{{ route('usuarios.index') }}">Usuários</a> › Novo</h1>
</div>

<form action="{{ route('usuarios.store') }}" method="post" class="form-cadastro">
    @csrf

    <div class="form-grupo">
        <label for="name">Nome</label>
        <input type="text" name="name" required id="name" placeholder="Digite o nome completo" value="{{ old('name') }}">
        @error('name')
        <p class="erro-campo">{{ $message }}</p>
        @enderror
    </div>

    <div class="form-grupo">
        <label for="codigo_usuario">Código de usuário</label>
        <input type="text" name="codigo_usuario" required id="codigo_usuario" autocomplete="off" placeholder="Ex.: nome.sobrenome" value="{{ old('codigo_usuario') }}">
        @error('codigo_usuario')
        <p class="erro-campo">{{ $message }}</p>
        @enderror
    </div>

    <div class="form-grupo">
        <label for="email">E-mail</label>
        <input type="email" name="email" required id="email" placeholder="email@empresa.com.br" value="{{ old('email') }}">
        @error('email')
        <p class="erro-campo">{{ $message }}</p>
        @enderror
    </div>

    <div class="form-grupo">
        <label for="estabelecimento_id">Estabelecimento</label>
        <select name="estabelecimento_id" id="estabelecimento_id" required>
            <option value="">Selecione</option>
            @foreach ($estabelecimentos as $estab)
            <option value="{{ $estab->id }}" @selected(old('estabelecimento_id')==$estab->id)>{{ $estab->razao_social }}</option>
            @endforeach
        </select>
        @error('estabelecimento_id')
        <p class="erro-campo">{{ $message }}</p>
        @enderror
    </div>

    <div class="form-grupo">
        <label for="cargo">Cargo</label>
        <select name="cargo" id="cargo" required aria-describedby="dica-cargo">
            <option value="">Selecione</option>
            @foreach (\App\Models\User::CARGOS as $valor => $nome)
            <option value="{{ $valor }}" @selected(old('cargo')==$valor)>{{ $nome }}</option>
            @endforeach
        </select>
        <p class="dica-campo" id="dica-cargo">Vendedor vê os próprios pedidos; gestor, os do estabelecimento; administrador, tudo.</p>
        @error('cargo')
        <p class="erro-campo">{{ $message }}</p>
        @enderror
    </div>

    <div class="form-grupo">
        <label for="password">Senha</label>
        <input type="password" name="password" required id="password" minlength="6" autocomplete="new-password" aria-describedby="dica-senha">
        <p class="dica-campo" id="dica-senha">Mínimo de 6 caracteres. O usuário pode trocar depois em "Meu perfil".</p>
        @error('password')
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
        <a href="{{ route('usuarios.index') }}" class="botao-cancelar">Cancelar</a>
        <button type="submit" class="botao-salvar">Salvar</button>
    </div>
</form>
@endsection
