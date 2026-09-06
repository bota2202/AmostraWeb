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
    <a href="{{ route('produtos.create') }}">Novo</a>
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
                <a href="{{ route('produtos.show',$produto) }}">Expandir</a>
                <a href="{{ route('produtos.edit',$produto) }}">Editar</a>
            </td>
        </tr>
        @endforeach
        {{ $produtos->links() }}
    </tbody>
</table>
</body>

</html>