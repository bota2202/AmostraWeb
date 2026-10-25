@extends('perfil.layout')

@section('title', 'Editar perfil')

@section('voltar')
<a href="{{ route('perfil') }}" class="link-voltar">
    <i class="fa-solid fa-arrow-left" aria-hidden="true"></i> Voltar ao perfil
</a>
@endsection

@section('conteudo')
<div class="cabecalho">
    <div>
        <h2>Editar perfil</h2>
        <p>Atualize seus dados pessoais e sua senha</p>
    </div>
</div>

<div class="conteudo">
    {{-- ============ DADOS PESSOAIS ============ --}}
    <form action="{{ route('perfil.update') }}" method="POST" class="cartao-form">
        @csrf
        @method('PUT')

        <h3><i class="fa-solid fa-user" aria-hidden="true"></i> Informações pessoais</h3>
        <div class="campos">
            <div class="campo">
                <label for="name">Nome completo</label>
                <input type="text" id="name" name="name" maxlength="255" required value="{{ old('name', $user->name) }}">
                @error('name')
                <span class="erro-campo">{{ $message }}</span>
                @enderror
            </div>
            <div class="campo">
                <label for="email">E-mail</label>
                <input type="email" id="email" name="email" maxlength="255" required value="{{ old('email', $user->email) }}">
                @error('email')
                <span class="erro-campo">{{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="rodape-form">
            <a href="{{ route('perfil') }}" class="botao botao-secundario">Cancelar</a>
            <button type="submit" class="botao botao-editar"><i class="fa-solid fa-check" aria-hidden="true"></i> Salvar dados</button>
        </div>
    </form>

    {{-- ============ SENHA ============ --}}
    <form action="{{ route('perfil.senha') }}" method="POST" class="cartao-form" id="senha">
        @csrf
        @method('PATCH')

        <h3><i class="fa-solid fa-lock" aria-hidden="true"></i> Alterar senha</h3>
        <div class="campos campos-3">
            <div class="campo">
                <label for="senha_atual">Senha atual</label>
                <input type="password" id="senha_atual" name="senha_atual" required autocomplete="current-password">
                @error('senha_atual', 'senha')
                <span class="erro-campo">{{ $message }}</span>
                @enderror
            </div>
            <div class="campo">
                <label for="password">Nova senha</label>
                <input type="password" id="password" name="password" required minlength="6" autocomplete="new-password" placeholder="Mínimo de 6 caracteres">
                @error('password', 'senha')
                <span class="erro-campo">{{ $message }}</span>
                @enderror
            </div>
            <div class="campo">
                <label for="password_confirmation">Confirme a nova senha</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required autocomplete="new-password">
            </div>
        </div>
        <div class="rodape-form">
            <button type="submit" class="botao botao-editar"><i class="fa-solid fa-key" aria-hidden="true"></i> Alterar senha</button>
        </div>
    </form>
</div>
@endsection
