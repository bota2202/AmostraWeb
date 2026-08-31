<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produtos - Index - Teste</title>
</head>

<body>
    @if (session('success'))
    <p>{{ session('success') }}</p>
    @endif
    <a href="/produtos/create">Novo</a>
    <h1>Produtos:</h1>
    <table>
    <thead>
        <tr>
            <th>Código</th>
            <th>Descrição</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($produtos as $produto)
        <tr>
            <td>{{ $produto->codigo_produto }}</td>
            <td>{{ $produto->descricao }}</td>
            <td>
                <a href="/produtos/{{ $produto->id }}">Expandir</a>
                <a href="/produtos/{{ $produto->id }}/edit">Editar</a>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
</body>

</html>