<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Index Estabelecimentos</title>
</head>
<body>
    @if (session('success'))
        <p>{{ session('success') }}</p>
    @endif
    <a href="{{ route('estabelecimentos.create') }}">Novo</a>
    <h1>Estabelecimentos:</h1>
    <table>
        <thead>
            <tr>
                <td>Código</td>
                <td>Razão Social</td>
                <td>CNPJ</td>
                <td>Ações</td>
            </tr>
        </thead>
        <tbody>
            @foreach ($estabelecimentos as $estabelecimento)
            <tr>
                <td>{{ $estabelecimento->id }}</td>
                <td>{{ $estabelecimento->razao_social }}</td>
                <td>{{ $estabelecimento->cnpj }}</td>
                <td>
                    <a href="{{ route('estabelecimentos.show',$estabelecimento) }}">Expandir</a>
                    <a href="{{ route('estabelecimentos.edit',$estabelecimento) }}">Editar</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>