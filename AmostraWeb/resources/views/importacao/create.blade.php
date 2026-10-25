@extends('layouts.app')

@section('title','Importar '.mb_strtolower($config['titulo']).' - '.config('amostraweb.name'))

@section('texto', $config['titulo'])

@push('styles')
<link rel="stylesheet" href="{{ asset('css/layout/detalhes.css') }}">
<link rel="stylesheet" href="{{ asset('css/layout/table.css') }}">
<link rel="stylesheet" href="{{ asset('css/importacao/importacao.css') }}">
@endpush

@section('content')
<div class="toolbar">
    <h1><a href="{{ route($tipo.'.index') }}">{{ $config['titulo'] }}</a> › Importar do ERP</h1>
    <a href="{{ route($tipo.'.importar.modelo') }}" class="botao-toolbar botao-secundario">
        <i class="fa-solid fa-file-arrow-down" aria-hidden="true"></i> Baixar arquivo-modelo
    </a>
</div>

<div class="dados">
    @if ($resultado)
    {{-- ===================== RESULTADO DA ÚLTIMA IMPORTAÇÃO ===================== --}}
    <section class="card-secao" aria-labelledby="titulo-resultado">
        <h2 id="titulo-resultado"><i class="fa-solid fa-square-poll-vertical" aria-hidden="true"></i> Resultado da importação</h2>
        <ul class="resultado-importacao">
            <li class="verde"><strong>{{ $resultado['criados'] }}</strong> {{ $resultado['criados'] === 1 ? $config['singular'].' novo' : $config['singular'].'s novos' }}</li>
            <li class="azul"><strong>{{ $resultado['atualizados'] }}</strong> atualizado(s)</li>
            <li class="cinza"><strong>{{ $resultado['sem_alteracao'] }}</strong> sem alteração</li>
            <li class="{{ $resultado['com_erro'] ? 'vermelho' : 'cinza' }}"><strong>{{ $resultado['com_erro'] }}</strong> linha(s) com erro</li>
        </ul>
        @if ($resultado['erros'])
        <table>
            <caption class="sr-only">Linhas com erro (não importadas)</caption>
            <thead>
                <tr>
                    <th scope="col">Linha do arquivo</th>
                    <th scope="col">Problema</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($resultado['erros'] as $erro)
                <tr>
                    <td class="centro">{{ $erro['linha'] }}</td>
                    <td>{{ $erro['mensagem'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @if ($resultado['com_erro'] > count($resultado['erros']))
        <p class="texto-vazio">Mostrando os {{ count($resultado['erros']) }} primeiros erros de {{ $resultado['com_erro'] }}.</p>
        @endif
        @endif
    </section>
    @endif

    {{-- ===================== ENVIO ===================== --}}
    <section class="card-secao" aria-labelledby="titulo-envio">
        <h2 id="titulo-envio"><i class="fa-solid fa-file-csv" aria-hidden="true"></i> Enviar arquivo</h2>
        <form method="POST" action="{{ route($tipo.'.importar.store') }}" enctype="multipart/form-data" class="form-importacao">
            @csrf
            <div class="label-input">
                <label for="arquivo">Arquivo CSV exportado do ERP <span class="obrigatorio" aria-hidden="true">*</span></label>
                <input type="file" id="arquivo" name="arquivo" accept=".csv,.txt,text/csv" required aria-describedby="dica-arquivo">
                <p class="dica-campo" id="dica-arquivo">Até 10 MB e {{ number_format($maxLinhas, 0, ',', '.') }} linhas. Separador ponto e vírgula ou vírgula.</p>
                @error('arquivo')
                <span class="erro-campo">{{ $message }}</span>
                @enderror
            </div>
            <button type="submit" class="botao-toolbar">
                <i class="fa-solid fa-upload" aria-hidden="true"></i> Importar {{ mb_strtolower($config['titulo']) }}
            </button>
        </form>
    </section>

    {{-- ===================== INSTRUÇÕES ===================== --}}
    <section class="card-secao" aria-labelledby="titulo-instrucoes">
        <h2 id="titulo-instrucoes"><i class="fa-solid fa-circle-info" aria-hidden="true"></i> Como funciona</h2>
        <ol class="passos">
            <li>No ERP, exporte o cadastro de {{ mb_strtolower($config['titulo']) }} para o Excel.</li>
            <li>No Excel, use <strong>Arquivo › Salvar como › CSV (separado por ponto e vírgula)</strong>.</li>
            <li>Envie o arquivo aqui. Cada linha é identificada pela coluna <strong>{{ $config['chave'] }}</strong>:
                se o {{ $config['singular'] }} já existe, os dados são <strong>atualizados</strong>; senão, ele é <strong>criado</strong>.</li>
            <li>Linhas com problema são ignoradas e aparecem no relatório; as demais são importadas normalmente.</li>
        </ol>
        <details class="colunas-esperadas">
            <summary>Colunas reconhecidas ({{ count($colunas) }})</summary>
            <p>Os nomes são reconhecidos sem diferenciar acentos e maiúsculas. Colunas extras são ignoradas.</p>
            <ul>
                @foreach ($colunas as $coluna)
                <li>{{ $coluna }}</li>
                @endforeach
            </ul>
        </details>
    </section>
</div>
@endsection
