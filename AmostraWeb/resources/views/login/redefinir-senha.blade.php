<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redefinir senha - {{ config('amostraweb.name') }}</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    <link rel="stylesheet" href="{{ asset('css/login/login.css') }}">
</head>

<body class="form-longo">
    <main>
        <section id="section_esquerda_login" aria-hidden="true">
            <div class="marca">
                <img src="{{ asset(config('amostraweb.logo_laranja')) }}" alt="" class="logo_marca">
                <p class="marca_slogan">Dê um 360 na sua proteção e sinalização!</p>
            </div>
        </section>

        <section id="section_direita_login">
            <form action="{{ route('senha.atualizar') }}" method="post" novalidate>
                @csrf

                <div class="form_cabecalho">
                    <h1>Redefinir senha</h1>
                    <p>Digite o código de 6 dígitos que enviamos para o seu e-mail e escolha uma nova senha.</p>
                </div>

                @if (session('success'))
                <p class="mensagem_sucesso" role="status">{{ session('success') }}</p>
                @endif

                <div class="campo_grupo">
                    <label for="email">E-mail</label>
                    <input type="email" id="email" name="email" required autocomplete="email"
                        placeholder="Ex.: nome@empresa.com.br" value="{{ $email }}"
                        @if (! $email) autofocus @endif
                        @error('email') aria-invalid="true" aria-describedby="erro-email" @enderror>
                    @error('email')<p class="erro_campo" id="erro-email">{{ $message }}</p>@enderror
                </div>

                <div class="campo_grupo">
                    <label for="codigo">Código recebido</label>
                    <input type="text" id="codigo" name="codigo" required inputmode="numeric" maxlength="6"
                        autocomplete="one-time-code" placeholder="000000" class="campo_codigo"
                        @if ($email) autofocus @endif
                        @error('codigo') aria-invalid="true" aria-describedby="erro-codigo" @enderror>
                    @error('codigo')<p class="erro_campo" id="erro-codigo" role="alert">{{ $message }}</p>@enderror
                </div>

                <div class="campo_grupo">
                    <label for="password">Nova senha</label>
                    <input type="password" id="password" name="password" required autocomplete="new-password"
                        placeholder="Mínimo de 6 caracteres"
                        @error('password') aria-invalid="true" aria-describedby="erro-password" @enderror>
                    @error('password')<p class="erro_campo" id="erro-password">{{ $message }}</p>@enderror
                </div>

                <div class="campo_grupo">
                    <label for="password_confirmation">Confirme a nova senha</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required
                        autocomplete="new-password" placeholder="Digite a nova senha de novo">
                </div>

                <button type="submit">Redefinir senha</button>
                <p class="ajuda_login">
                    <a href="{{ route('senha.esqueci') }}">Enviar outro código</a>
                    <span aria-hidden="true">·</span>
                    <a href="{{ route('showLogin') }}">Voltar para o login</a>
                </p>
            </form>
        </section>
    </main>
</body>

</html>
