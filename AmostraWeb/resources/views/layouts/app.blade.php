<!DOCTYPE html>

<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('amostraweb.name'))</title>

    <link rel="icon" href="{{ asset('favicon.ico') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.3.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/layout/app.css') }}">
    @stack('styles')
</head>

<body>
    <a href="#conteudo" class="pular-conteudo">Pular para o conteúdo</a>

    <header class="topo">
        <button type="button" class="botao-menu" data-abrir-menu aria-controls="menu-principal" aria-expanded="false">
            <i class="fa-solid fa-bars" aria-hidden="true"></i>
            <span class="sr-only">Abrir menu</span>
        </button>
        <a href="{{ route('dashboard') }}" class="logo-topo">
            <img src="{{ config('amostraweb.logo_azul') }}" alt="{{ config('amostraweb.empresa') }} - ir para o início">
        </a>
        <p class="titulo-modulo">@yield('texto')</p>
        <a href="{{ route('perfil') }}" class="avatar-topo" title="Meu perfil">
            <span aria-hidden="true">{{ auth()->user()->iniciais }}</span>
            <span class="sr-only">Meu perfil ({{ auth()->user()->name }})</span>
        </a>
    </header>

    <aside class="menu-lateral" id="menu-principal">
        <div class="marca-menu">
            <a href="{{ route('dashboard') }}">{{ config('amostraweb.name') }}</a>
        </div>
        <nav aria-label="Menu principal">
            @foreach ($menu as $i => $secao)
                @php
                    $temAtivo = collect($secao['itens'])->contains('ativo', true);
                    $alertas = collect($secao['itens'])->sum('contador');
                @endphp

                @if ($secao['titulo'])
                {{-- categoria expansível: abre sozinha quando a página atual está nela --}}
                <details class="menu-grupo" data-menu-grupo="{{ \Illuminate\Support\Str::slug($secao['titulo']) }}" @if ($temAtivo) open data-ativo @endif>
                    <summary class="menu-secao">
                        {{ $secao['titulo'] }}
                        @if ($alertas > 0)
                        <span class="contador-menu contador-grupo">{{ $alertas }}<span class="sr-only"> alertas</span></span>
                        @endif
                        <i class="fa-solid fa-chevron-down seta-grupo" aria-hidden="true"></i>
                    </summary>
                @endif

                <ul>
                    @foreach ($secao['itens'] as $item)
                    <li>
                        <a href="{{ route($item['rota']) }}" class="botao-aside {{ $item['ativo'] ? 'section-ativa' : '' }}" @if ($item['ativo']) aria-current="page" @endif>
                            <i class="fa-solid {{ $item['icone'] }}" aria-hidden="true"></i>
                            {{ $item['texto'] }}
                            @if ($item['contador'] > 0)
                            <span class="contador-menu">{{ $item['contador'] }}<span class="sr-only"> {{ $item['contador_texto'] }}</span></span>
                            @endif
                        </a>
                    </li>
                    @endforeach
                </ul>

                @if ($secao['titulo'])
                </details>
                @endif
            @endforeach
        </nav>
    </aside>
    <div class="menu-fundo" data-fechar-menu></div>

    <main id="conteudo" tabindex="-1">
        @if (session('success'))
        <div class="alerta alerta-sucesso" role="status"><i class="fa-solid fa-circle-check" aria-hidden="true"></i> <span>{{ session('success') }}</span></div>
        @endif
        @if (session('error'))
        <div class="alerta alerta-erro" role="alert"><i class="fa-solid fa-circle-exclamation" aria-hidden="true"></i> <span>{{ session('error') }}</span></div>
        @endif
        @yield('content')
    </main>

    <script src="{{ asset('js/app.js') }}"></script>
    @stack('scripts')
</body>

</html>
