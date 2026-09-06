<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
</head>
<body>
    <form action="{{ route('logout') }}" method="post">
        @csrf

        <button type="submit">Sair</button>
    </form>
</body>
</html>