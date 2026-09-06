<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Estabelecimento</title>
</head>

<body>
    <form action="{{ route('estabelecimentos.update',$estabelecimento) }}" method="post">
        @csrf
        @method('PUT')
        @error('razao_social')
        <p>{{ $message }}</p>
        @enderror
        <input type="text" name="razao_social" placeholder="Razão social">
        @error('status')
        <p>{{ $message }}</p>
        @enderror
        <button type="submit">Atualizar</button>
    </form>
</body>

</html>