<!DOCTYPE html>

<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', config('amostraweb.name'))</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.3.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/layout/app.css') }}">
    @stack('styles')
</head>

<body>

    <header>
        <img
            src="{{ config('amostraweb.logo_azul') }}"
            alt="{{ config('amostraweb.name') }}">
        <p>@yield('texto')</p>
        <a href="{{ route('usuarios.edit', auth()->user()) }}">
            <i class="fa-regular fa-circle-user"></i>
        </a>
    </header>

    <aside>
        <section>
            <p>{{ config('amostraweb.name') }}</p>
        </section>
        <section>
            <a href="{{ route('amostras.index') }}" class="botao-aside {{ request()->routeIs('amostras.*') ? 'section-ativa' : '' }}"><i class="fa-solid fa-flask"></i> Amostras</a>
            <a href="{{ route('clientes.index') }}" class="botao-aside {{ request()->routeIs('clientes.*') ? 'section-ativa' : '' }}"><i class="fa-solid fa-people-group"></i> Clientes</a>
            <a href="{{ route('dashboard') }}" class="botao-aside {{ request()->routeIs('dashboard') ? 'section-ativa' : '' }}"><i class="fa-solid fa-chart-line"></i> Dashboard</a>
            <a href="{{ route('estabelecimentos.index') }}" class="botao-aside {{ request()->routeIs('estabelecimentos.*') ? 'section-ativa' : '' }}"><i class="fa-solid fa-shop"></i> Estabelecimentos</a>
            <a href="{{ route('pedidos.index') }}" class="botao-aside {{ request()->routeIs('pedidos.*') ? 'section-ativa' : '' }}"><i class="fa-solid fa-cart-shopping"></i> Pedidos</a>
            <a href="{{ route('permissoes.index') }}" class="botao-aside {{ request()->routeIs('permissoes.*') ? 'section-ativa' : '' }}"><i class="fa-solid fa-unlock"></i> Permissões</a>
            <a href="{{ route('produtos.index') }}" class="botao-aside {{ request()->routeIs('produtos.*') ? 'section-ativa' : '' }}"><i class="fa-solid fa-tags"></i> Produtos</a>
            <a href="{{ route('usuarios.index') }}" class="botao-aside {{ request()->routeIs('usuarios.*') ? 'section-ativa' : '' }}"><i class="fa-solid fa-user"></i> Usuários</a>
        </section>
    </aside>

    <main>
        @yield('content')
    </main>
</body>

</html>