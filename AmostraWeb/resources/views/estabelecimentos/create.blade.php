<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Estabelecimento</title>
</head>

<body>
    <form action="{{ route('estabelecimentos.store') }}" method="post">
        @csrf
        @error('razao_social')
        <p>{{ $message }}</p>
        @enderror
        <input type="text" name="razao_social" placeholder="Razão social">
        @error('cnpj')
        <p>{{ $message }}</p>
        @enderror
        <input type="text" name="cnpj" placeholder="CNPJ">
        @error('uf')
        <p>{{ $message }}</p>
        @enderror
        <select name="uf" id="uf_estabelecimento_create">
            <option value="" selected disabled hidden>Insira seu estado</option>
            <option value="ac">Acre</option>
            <option value="al">Alagoas</option>
            <option value="ap">Amapá</option>
            <option value="am">Amazonas</option>
            <option value="ba">Bahia</option>
            <option value="ce">Ceará</option>
            <option value="df">Distrito Federal</option>
            <option value="es">Espírito Santo</option>
            <option value="go">Goiás</option>
            <option value="ma">Maranhão</option>
            <option value="mt">Mato Grosso</option>
            <option value="ms">Mato Grosso do Sul</option>
            <option value="mg">Minas Gerais</option>
            <option value="pa">Pará</option>
            <option value="pb">Paraíba</option>
            <option value="pr">Paraná</option>
            <option value="pe">Pernambuco</option>
            <option value="pi">Piauí</option>
            <option value="rj">Rio de Janeiro</option>
            <option value="rn">Rio Grande do Norte</option>
            <option value="rs">Rio Grande do Sul</option>
            <option value="ro">Rondônia</option>
            <option value="rr">Roraima</option>
            <option value="sc">Santa Catarina</option>
            <option value="sp">São Paulo</option>
            <option value="se">Sergipe</option>
            <option value="to">Tocantins</option>
        </select>
        @error('cidade')
        <p>{{ $message }}</p>
        @enderror
        <input type="text" name="cidade" placeholder="Cidade">
        <button type="submit">Cadastrar</button>
    </form>
</body>

</html>