<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produto - Edit - Teste</title>
</head>
<body>
    <h1>Editar produto</h1>
    <form action="/produtos/{{ $produto->id }}/update" method="post">
        @csrf
        @error('codigo_produto')
            <p>{{ $message }}</p>
        @enderror
        <input type="text" value="{{ $produto->codigo_produto }}" name="codigo_produto" readonly>
        @error('descricao')
            <p>{{ $message }}</p>
        @enderror
        <input type="text" value="{{ $produto->descricao }}" name="descricao">
        <button type="submit">Atualizar</button>
    </form>
</body>
</html>