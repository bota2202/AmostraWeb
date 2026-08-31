<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produtos - Create - Teste</title>
</head>
<body>
    <form action="/produtos/store" method="post">
        @csrf
        <h1>Cadastrar produto</h1>
        @error('codigo_produto')
            <p>{{ $message }}</p>
        @enderror
        <input type="text" name="codigo_produto" placeholder="Digite o código do produto" value="{{ old('codigo_produto') }}">
        @error('descricao')
            <p>{{ $message }}</p>
        @enderror
        <input type="text" name="descricao" placeholder="Digite a descrição do produto" value="{{ old('descricao') }}">
        <button type="submit">Cadatrar</button>
    </form>
</body>
</html>