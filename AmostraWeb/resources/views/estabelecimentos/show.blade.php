<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estabelecimento Show</title>
</head>

<body>
    <table>
        <thead>
            <tr>
                <td>Código</td>
                <td>Razão Social</td>
                <td>CNPJ</td>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $estabelecimento->id }}</td>
                <td>{{ $estabelecimento->razao_social }}</td>
                <td>{{ $estabelecimento->cnpj }}</td>
            </tr>
        </tbody>
    </table>
    <form action="{{ route('estabelecimentos.destroy',$estabelecimento) }}" method="post">
        @csrf
        @method('DELETE')
        <button type="submit">Excluir</button>
    </form>
</body>

</html>