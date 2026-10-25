@extends('layouts.app')

@section('title','Detalhes do log - '.config('amostraweb.name'))

@section('texto','Logs')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/log/log.css') }}">
@endpush

@php
    $frase = $evento->frase();
    $alteracoes = $evento->alteracoes();
    $listas = $evento->listas();
    $dados = $evento->dados();
    $observacao = $evento->observacao();
    $quemVe = auth()->user();
    $urlRegistro = $evento->urlRegistro($quemVe);
    $usuario = $evento->usuario;
    $tituloDados = match ($evento->acao()) {
        'excluir' => 'Dados no momento da exclusão',
        'importar' => 'Resultado da importação',
        default => 'Dados cadastrados',
    };
@endphp

@section('content')
<div class="toolbar">
    <h1><a href="{{ url()->previous() !== url()->current() && str_contains(url()->previous(), '/logs') ? url()->previous() : route('logs.index') }}">Logs</a> › Detalhes</h1>
    @if ($evento->filtroRegistro())
    <div class="acoes-toolbar">
        <a class="botao-toolbar botao-secundario" href="{{ route('logs.index', ['registro' => $evento->filtroRegistro()]) }}">
            <i class="fa-solid fa-clock-rotate-left" aria-hidden="true"></i> Histórico deste registro
        </a>
    </div>
    @endif
</div>

<div class="log-detalhe">
    {{-- ===================== RESUMO ===================== --}}
    <section class="log-cabecalho" aria-labelledby="titulo-acao">
        <span class="log-icone grande {{ $evento->categoria() }}" aria-hidden="true"><i class="fa-solid {{ $evento->icone() }}"></i></span>
        <div>
            <h2 id="titulo-acao" class="log-frase">
                <strong>{{ $evento->autor() }}</strong>
                {{ $frase['texto'] }}
                @if ($frase['destaque'])<strong>{{ $frase['destaque'] }}</strong>@endif
                {{ $frase['complemento'] }}
            </h2>
            <p class="log-cabecalho-meta">
                <span><i class="fa-regular fa-calendar" aria-hidden="true"></i>
                    <time datetime="{{ $evento->quando->toIso8601String() }}">{{ $evento->quando->translatedFormat('d \d\e F \d\e Y \à\s H:i:s') }}</time>
                    <span class="log-relativo">({{ $evento->quando->diffForHumans() }})</span>
                </span>
                <span class="log-etiqueta {{ $evento->categoria() }}">{{ $evento->acaoNome() }}</span>
                <span class="log-etiqueta">{{ $evento->moduloNome() }}</span>
            </p>
        </div>
    </section>

    <div class="log-grade">
        {{-- quem fez --}}
        <section class="log-card" aria-labelledby="titulo-quem">
            <h3 id="titulo-quem">Quem fez</h3>
            @if ($usuario)
            <div class="log-pessoa">
                <span class="log-avatar" aria-hidden="true">{{ $usuario->iniciais }}</span>
                <div>
                    @if ($quemVe->temPermissao('usuarios'))
                    <a href="{{ route('usuarios.show', $usuario) }}" class="log-nome">{{ $usuario->name }}</a>
                    @else
                    <span class="log-nome">{{ $usuario->name }}</span>
                    @endif
                    <span class="log-secundario">{{ $usuario->codigo_usuario }} · {{ $usuario->cargo_nome }}</span>
                </div>
            </div>
            @elseif ($evento->feitoPeloSistema())
            <div class="log-pessoa">
                <span class="log-avatar sistema" aria-hidden="true"><i class="fa-solid fa-gear"></i></span>
                <div>
                    <span class="log-nome">Sistema</span>
                    <span class="log-secundario">Ação automática, sem usuário</span>
                </div>
            </div>
            @else
            <div class="log-pessoa">
                <span class="log-avatar negado" aria-hidden="true"><i class="fa-solid fa-user-lock"></i></span>
                <div>
                    <span class="log-nome">{{ $evento->autor() }}</span>
                    <span class="log-secundario">Não chegou a entrar no sistema</span>
                </div>
            </div>
            @endif
        </section>

        {{-- registro afetado --}}
        <section class="log-card" aria-labelledby="titulo-registro">
            <h3 id="titulo-registro">Registro afetado</h3>
            @if ($evento->temRegistro())
            <p class="log-nome">{{ ucfirst(preg_replace('/^(o|a) /', '', $evento->tipoRegistroTexto())) }} {{ $evento->nomeRegistro() }}</p>
            @if ($urlRegistro)
            <a href="{{ $urlRegistro }}" class="log-link"><i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i> Abrir registro</a>
            @elseif ($evento->registroExcluido())
            <span class="log-secundario"><i class="fa-solid fa-trash-can" aria-hidden="true"></i> Este registro foi excluído</span>
            @endif
            @else
            <p class="log-secundario">Nenhum: {{ in_array($evento->acao(), ['importar']) ? 'importação de arquivo' : 'ação de acesso ao sistema' }}.</p>
            @endif
        </section>

        {{-- origem --}}
        <section class="log-card" aria-labelledby="titulo-origem">
            <h3 id="titulo-origem">De onde foi feito</h3>
            @if ($evento->ip())
            <dl class="log-origem">
                <dt>IP</dt>
                <dd>{{ $evento->ip() }}</dd>
                @if ($evento->navegador())
                <dt>Navegador</dt>
                <dd title="{{ $evento->navegadorUserAgent() }}">{{ $evento->navegador() }}</dd>
                @endif
            </dl>
            @else
            <p class="log-secundario">{{ $evento->feitoPeloSistema() ? 'Servidor (ação automática)' : 'Não registrado' }}</p>
            @endif
        </section>
    </div>

    {{-- ===================== O QUE MUDOU ===================== --}}
    @if ($alteracoes->isNotEmpty())
    <section class="log-card" aria-labelledby="titulo-mudancas">
        <h3 id="titulo-mudancas">O que mudou <span class="log-contagem">{{ $alteracoes->count() }} {{ $alteracoes->count() === 1 ? 'campo' : 'campos' }}</span></h3>
        <table class="log-tabela">
            <caption class="sr-only">Campos alterados, com o valor anterior e o novo</caption>
            <thead>
                <tr>
                    <th scope="col">Campo</th>
                    <th scope="col">Antes</th>
                    <th scope="col"><span class="sr-only">para</span></th>
                    <th scope="col">Depois</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($alteracoes as $alteracao)
                <tr>
                    <th scope="row">{{ $alteracao['campo'] }}</th>
                    <td><span class="log-antes">{{ $alteracao['antes'] ?? '(vazio)' }}</span></td>
                    <td class="log-flecha" aria-hidden="true"><i class="fa-solid fa-arrow-right"></i></td>
                    <td><span class="log-depois">{{ $alteracao['depois'] ?? '(vazio)' }}</span></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </section>
    @endif

    @foreach ($listas as $lista)
    <section class="log-card" aria-labelledby="titulo-lista-{{ $loop->index }}">
        <h3 id="titulo-lista-{{ $loop->index }}">{{ $lista['campo'] }}</h3>
        <div class="log-listas">
            <div>
                <h4><i class="fa-solid fa-circle-plus" aria-hidden="true"></i> Incluídos ({{ count($lista['incluidos']) }})</h4>
                @if ($lista['incluidos'])
                <ul class="log-chips incluidos">
                    @foreach ($lista['incluidos'] as $item)<li>{{ $item }}</li>@endforeach
                </ul>
                @else
                <p class="log-secundario">Nenhum</p>
                @endif
            </div>
            <div>
                <h4><i class="fa-solid fa-circle-minus" aria-hidden="true"></i> Retirados ({{ count($lista['retirados']) }})</h4>
                @if ($lista['retirados'])
                <ul class="log-chips retirados">
                    @foreach ($lista['retirados'] as $item)<li>{{ $item }}</li>@endforeach
                </ul>
                @else
                <p class="log-secundario">Nenhum</p>
                @endif
            </div>
        </div>
    </section>
    @endforeach

    @if ($dados)
    <section class="log-card" aria-labelledby="titulo-dados">
        <h3 id="titulo-dados">{{ $tituloDados }}</h3>
        <dl class="log-dados">
            @foreach ($dados as $campo => $valor)
            <div>
                <dt>{{ $campo }}</dt>
                <dd>{{ $valor }}</dd>
            </div>
            @endforeach
        </dl>
    </section>
    @endif

    @if ($observacao)
    <section class="log-card" aria-labelledby="titulo-obs">
        <h3 id="titulo-obs">{{ $evento->acao() === 'login_falhou' ? 'Motivo' : 'Observação' }}</h3>
        <p class="log-observacao">{{ $observacao }}</p>
    </section>
    @endif
</div>
@endsection
