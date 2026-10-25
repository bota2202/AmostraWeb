@extends('perfil.layout')

@section('title', 'Meu perfil')

@section('voltar')
<a href="{{ route('dashboard') }}" class="link-voltar">
    <i class="fa-solid fa-arrow-left" aria-hidden="true"></i> Voltar ao sistema
</a>
@endsection

@section('conteudo')
<div class="cabecalho">
    <div>
        <h2>Meu perfil</h2>
        <p>Seus dados de acesso ao {{ config('amostraweb.name') }}</p>
    </div>
    <div class="acoes">
        <a href="{{ route('perfil.editar') }}" class="botao botao-editar">
            <i class="fa-solid fa-pen" aria-hidden="true"></i> Editar perfil
        </a>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="botao botao-sair">
                <i class="fa-solid fa-right-from-bracket" aria-hidden="true"></i> Sair
            </button>
        </form>
    </div>
</div>

<div class="conteudo">
    <h3 class="titulo-secao">Informações pessoais</h3>
    <div class="info-grid">
        <div class="info">
            <i class="fa-solid fa-user" aria-hidden="true"></i>
            <div>
                <span>Nome completo</span>
                <strong>{{ $user->name }}</strong>
            </div>
        </div>
        <div class="info">
            <i class="fa-solid fa-envelope" aria-hidden="true"></i>
            <div>
                <span>E-mail</span>
                <strong>{{ $user->email }}</strong>
            </div>
        </div>
        <div class="info">
            <i class="fa-solid fa-at" aria-hidden="true"></i>
            <div>
                <span>Código de usuário</span>
                <strong>{{ $user->codigo_usuario }}</strong>
            </div>
        </div>
        <div class="info">
            <i class="fa-solid fa-briefcase" aria-hidden="true"></i>
            <div>
                <span>Cargo</span>
                <strong>{{ \App\Models\User::CARGOS[$user->cargo] ?? $user->cargo }}</strong>
            </div>
        </div>
        <div class="info">
            <i class="fa-solid fa-calendar-check" aria-hidden="true"></i>
            <div>
                <span>Usuário desde</span>
                <strong>{{ $user->created_at?->format('d/m/Y') ?? '-' }}</strong>
            </div>
        </div>
    </div>

    <h3 class="titulo-secao">Segurança</h3>
    <div class="cartao-seguranca">
        <div class="icone-seguranca"><i class="fa-solid fa-lock" aria-hidden="true"></i></div>
        <div>
            <strong>Senha</strong>
            <span>Use uma senha que só você conheça. Troque se suspeitar que alguém a descobriu.</span>
        </div>
        <a href="{{ route('perfil.editar') }}#senha" class="botao botao-secundario">Alterar senha</a>
    </div>
</div>
@endsection
