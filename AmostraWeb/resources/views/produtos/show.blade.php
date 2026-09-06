<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produto - Show - Teste</title>
</head>
<body>
    <p>{{ $produto->codigo_produto }}</p>
    <p>{{ $produto->descricao }}</p>

    <form action="{{ route('produtos.destroy',$produto) }}" method="post">
        @csrf
        @method('DELETE')
        
        <button type="submit">Excluir</button>
    </form>
    
</body>
</html>