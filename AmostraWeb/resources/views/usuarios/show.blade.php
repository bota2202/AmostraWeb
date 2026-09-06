<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Show Users</title>
</head>

<body>
    <h1>{{ $user->codigo_usuario }}</h1>
    <table>
        <thead>
            <tr>
                <th>Nome</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $user->name }}</td>
            </tr>
        </tbody>
    </table>
    <form action="{{ route('usuarios.destroy',$user) }}" method="post">
        @csrf
        @method('DELETE')

        <button type="submit">Excluir</button>
    </form>
</body>
</html>