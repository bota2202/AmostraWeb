<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - {{ config('amostraweb.name') }}</title>
    <link rel="stylesheet" href="{{ asset('css/login/login.css') }}">
</head>

<body>
    <main>
        <section id="section_esquerda_login">
            <div class="marca">
                <img src="{{ config('amostraweb.logo_laranja') }}" alt="{{ config('amostraweb.name') }}" class="logo_marca">
                <p class="marca_slogan">Dê um 360 na sua proteção e sinalização!</p>
            </div>
        </section>

        <section id="section_direita_login">
            <form action="{{ route('login') }}" method="post">
                @csrf

                <div class="form_cabecalho">
                    <h1>Bem-vindo de volta</h1>
                    <p>Entre com suas credenciais para continuar</p>
                </div>

                @if (session('error'))
                <p class="mensagem_erro">{{ session('error') }}</p>
                @endif

                @if ($errors->has('codigo_usuario') || $errors->has('password'))
                <p class="mensagem_erro">
                    {{ $errors->first('codigo_usuario') ?? $errors->first('password') }}
                </p>
                @endif

                <div class="campo_grupo">
                    <label for="codigo_usuario">Código de usuário</label>
                    <input type="text" id="codigo_usuario" name="codigo_usuario" placeholder="Digite seu código" value="{{ old('codigo_usuario') }}">
                </div>

                <div class="campo_grupo">
                    <label for="password">Senha</label>
                    <input type="password" id="password" name="password" placeholder="Digite sua senha">
                </div>

                <button type="submit">Conectar-se</button>
            </form>
        </section>
    </main>
</body>

</html>