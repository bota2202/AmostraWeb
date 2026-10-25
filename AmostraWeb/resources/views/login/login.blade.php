<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entrar - {{ config('amostraweb.name') }}</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    <link rel="stylesheet" href="{{ asset('css/login/login.css') }}">
</head>

@php
    $erro = session('error') ?? $errors->first('codigo_usuario') ?: $errors->first('password');
@endphp

<body>
    <main>
        <section id="section_esquerda_login" aria-hidden="true">
            <div class="marca">
                <img src="{{ asset(config('amostraweb.logo_laranja')) }}" alt="" class="logo_marca">
                <p class="marca_slogan">Dê um 360 na sua proteção e sinalização!</p>
            </div>
        </section>

        <section id="section_direita_login">
            <form action="{{ route('login') }}" method="post" novalidate>
                @csrf

                <div class="form_cabecalho">
                    <h1>Bem-vindo de volta</h1>
                    <p>Entre no {{ config('amostraweb.name') }} com seu código de usuário e senha.</p>
                </div>

                @if (session('success'))
                <p class="mensagem_sucesso" role="status">{{ session('success') }}</p>
                @endif

                @if ($erro)
                <p class="mensagem_erro" id="erro-login" role="alert">{{ $erro }}</p>
                @endif

                <div class="campo_grupo">
                    <label for="codigo_usuario">Código de usuário</label>
                    <input type="text" id="codigo_usuario" name="codigo_usuario" required autofocus
                        autocomplete="username" autocapitalize="none" spellcheck="false"
                        placeholder="Ex.: nome.sobrenome" value="{{ old('codigo_usuario') }}"
                        @if ($erro) aria-invalid="true" aria-describedby="erro-login" @endif>
                </div>

                <div class="campo_grupo">
                    <label for="password">Senha</label>
                    <input type="password" id="password" name="password" required autocomplete="current-password" placeholder="Digite sua senha"
                        @if ($erro) aria-invalid="true" aria-describedby="erro-login" @endif>
                </div>

                <button type="submit">Entrar</button>
                <p class="ajuda_login"><a href="{{ route('senha.esqueci') }}">Esqueci minha senha</a></p>
            </form>
        </section>
    </main>
</body>

</html>
