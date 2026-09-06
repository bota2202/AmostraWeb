<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuário</title>
</head>

<body>

    <form action="{{ route('usuarios.update', $user) }}" method="post">

        @csrf
        @method('PUT')

        <input
            type="text"
            name="estabelecimento_id"
            value="{{ $user->estabelecimento_id }}"
        >

        <input
            type="text"
            name="name"
            value="{{ $user->name }}"
        >

        <input
            type="email"
            name="email"
            value="{{ $user->email }}"
        >

        <select name="cargo" id="cargo_usuarios_edit">

            <option value="representante"
                @selected($user->cargo === 'representante')>
                Representante
            </option>

            <option value="gestor"
                @selected($user->cargo === 'gestor')>
                Gestor
            </option>

            <option value="admin"
                @selected($user->cargo === 'admin')>
                Administrador
            </option>

        </select>

        <button type="submit">Salvar alterações</button>

    </form>

</body>
</html>