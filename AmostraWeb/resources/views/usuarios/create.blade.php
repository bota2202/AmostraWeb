<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Usuário</title>
</head>

<body>
    <form action="{{ route('usuarios.store') }}" method="post">
        @csrf

        <input type="text" name="estabelecimento_id" placeholder="ID do estabelecimento">
        <input type="text" name="name" placeholder="Nome">
        <input type="text" name="email" placeholder="E-mail">
        <input type="text" name="codigo_usuario" placeholder="Código de usuário">
        <select name="cargo" id="cargo_users_create">
            <option value="" disabled selected hidden>Selecione o cargo do usuário</option>
            <option value="representante">Representante</option>
            <option value="gestor">Gestor</option>
            <option value="admin">Administrador</option>
        </select>
        <input type="password" name="password" placeholder="Senha">
        <button type="submit">Cadastrar</button>
    </form>
</body>

</html>