<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
    <h1>Login</h1>
    <form action="{{ route('login') }}" method="post">
        @csrf

        @if (session('error'))
            <p>{{ session('error') }}</p>
        @endif
        
        @error('codigo_usuario')
            <p>{{ $message }}</p>
        @enderror
        <input type="text" name="codigo_usuario" placeholder="Código de usuário" value="{{ old('codigo_usuario') }}">
        @error('password')
            <p>{{ $message }}</p>
        @enderror
        <input type="password" name="password" placeholder="Senha">

        <button type="submit">Conectar-se</button>
    </form>
</body>
</html>