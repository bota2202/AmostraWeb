<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('titulo') - {{ config('amostraweb.name') }}</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.3.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/erro/erro.css') }}">
</head>

<body>
    <main class="erro">
        <img src="{{ config('amostraweb.logo_azul') }}" alt="{{ config('amostraweb.empresa') }}" class="erro-logo">
        <p class="erro-codigo" aria-hidden="true">@yield('codigo')</p>
        <h1>@yield('titulo')</h1>
        <p class="erro-mensagem">@yield('mensagem')</p>
        <div class="erro-acoes">
            @auth
            <a href="{{ route('dashboard') }}" class="erro-botao"><i class="fa-solid fa-house" aria-hidden="true"></i> Ir para o início</a>
            @else
            <a href="{{ route('showLogin') }}" class="erro-botao"><i class="fa-solid fa-right-to-bracket" aria-hidden="true"></i> Ir para o login</a>
            @endauth
            <a href="javascript:history.back()" class="erro-botao secundario"><i class="fa-solid fa-arrow-left" aria-hidden="true"></i> Voltar</a>
        </div>
    </main>
</body>

</html>
