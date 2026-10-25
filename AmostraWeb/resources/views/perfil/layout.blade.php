<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - {{ config('amostraweb.name') }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.3.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/perfil/perfil.css') }}">
</head>

<body>
    <main>
        {{-- ============ LADO ESQUERDO (igual nas telas do perfil) ============ --}}
        <section id="perfil-esquerda">
            @yield('voltar')

            <div class="perfil-identidade">
                <div class="avatar">{{ $user->iniciais }}</div>
                <h1>{{ $user->name }}</h1>
                <p class="codigo">{{ $user->codigo_usuario }}</p>
                <div class="etiquetas">
                    <span class="etiqueta">{{ \App\Models\User::CARGOS[$user->cargo] ?? $user->cargo }}</span>
                    <span class="etiqueta {{ $user->status ? 'ativo' : 'inativo' }}">
                        <i class="fa-solid fa-circle"></i> {{ $user->status ? 'Ativo' : 'Inativo' }}
                    </span>
                </div>

                @if ($user->estabelecimento)
                <div class="vinculo">
                    <i class="fa-solid fa-shop"></i>
                    <div>
                        <small>Atribuído ao estabelecimento</small>
                        <strong>{{ $user->estabelecimento->razao_social }}</strong>
                        <span>{{ $user->estabelecimento->cidade }} / {{ strtoupper($user->estabelecimento->uf) }}</span>
                    </div>
                </div>
                @endif
            </div>

            <img src="{{ asset(config('amostraweb.logo_laranja')) }}" alt="{{ config('amostraweb.name') }}" class="logo">
        </section>

        {{-- ============ LADO DIREITO ============ --}}
        <section id="perfil-direita">
            @if (session('success'))
            <div class="alerta alerta-sucesso"><i class="fa-solid fa-circle-check"></i> {{ session('success') }}</div>
            @endif

            @yield('conteudo')
        </section>
    </main>
</body>

</html>
