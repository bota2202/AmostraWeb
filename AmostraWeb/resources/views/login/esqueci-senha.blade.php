<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Esqueci minha senha - {{ config('amostraweb.name') }}</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    <link rel="stylesheet" href="{{ asset('css/login/login.css') }}">
</head>

@php
    $erro = session('error') ?? $errors->first('email');
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
            <form action="{{ route('senha.enviar') }}" method="post" novalidate>
                @csrf

                <div class="form_cabecalho">
                    <h1>Esqueci minha senha</h1>
                    <p>Informe o e-mail cadastrado na sua conta. Vamos enviar um código para você criar uma nova senha.</p>
                </div>

                @if ($erro)
                <p class="mensagem_erro" id="erro-senha" role="alert">{{ $erro }}</p>
                @endif

                <div class="campo_grupo">
                    <label for="email">E-mail</label>
                    <input type="email" id="email" name="email" required autofocus autocomplete="email"
                        placeholder="Ex.: nome@empresa.com.br" value="{{ old('email') }}"
                        @if ($erro) aria-invalid="true" aria-describedby="erro-senha" @endif>
                </div>

                <button type="submit">Enviar código</button>
                <p class="ajuda_login">
                    <a href="{{ route('senha.redefinir') }}">Já tenho um código</a>
                    <span aria-hidden="true">·</span>
                    <a href="{{ route('showLogin') }}">Voltar para o login</a>
                </p>
            </form>
        </section>
    </main>
</body>

</html>
