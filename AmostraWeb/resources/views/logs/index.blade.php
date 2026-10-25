@extends('layouts.app')

@section('title','Logs - '.config('amostraweb.name'))

@section('texto','Logs')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/layout/filtros.css') }}">
<link rel="stylesheet" href="{{ asset('css/log/log.css') }}">
@endpush

@php
    $temFiltro = array_filter($filtros, fn ($v) => $v !== null && $v !== '');
    $hoje = now()->toDateString();
    $ontem = now()->subDay()->toDateString();
@endphp

@section('content')
<div class="toolbar">
    <div>
        <h1>Logs</h1>
        <p class="toolbar-subtitulo">Tudo o que foi feito no sistema: quem fez, o quê e quando. Clique numa ação para ver os detalhes.</p>
    </div>
</div>

<form method="GET" action="{{ route('logs.index') }}" class="filtros" role="search" aria-label="Filtrar logs">
    @if ($registro)
    <input type="hidden" name="registro" value="{{ $filtros['registro'] }}">
    @endif
    <div class="filtro">
        <label for="usuario">Quem fez</label>
        <select id="usuario" name="usuario">
            <option value="">Todos</option>
            <option value="sistema" @selected(($filtros['usuario'] ?? '') === 'sistema')>Sistema (automático)</option>
            @foreach ($usuarios as $usuario)
            <option value="{{ $usuario->id }}" @selected(($filtros['usuario'] ?? '') == $usuario->id)>{{ $usuario->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="filtro">
        <label for="tipo">Tipo de ação</label>
        <select id="tipo" name="tipo">
            <option value="">Todas</option>
            @foreach (\App\Support\EventoLog::TIPOS as $valor => [$nome])
            <option value="{{ $valor }}" @selected(($filtros['tipo'] ?? '') === $valor)>{{ $nome }}</option>
            @endforeach
        </select>
    </div>
    <div class="filtro">
        <label for="modulo">Módulo</label>
        <select id="modulo" name="modulo">
            <option value="">Todos</option>
            @foreach ($modulos as $valor => $nome)
            <option value="{{ $valor }}" @selected(($filtros['modulo'] ?? '') === $valor)>{{ $nome }}</option>
            @endforeach
        </select>
    </div>
    <div class="filtro">
        <label for="de">De</label>
        <input type="date" id="de" name="de" value="{{ $filtros['de'] ?? '' }}">
    </div>
    <div class="filtro">
        <label for="ate">Até</label>
        <input type="date" id="ate" name="ate" value="{{ $filtros['ate'] ?? '' }}">
    </div>
    <div class="filtro-acoes">
        <button type="submit"><i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i> Filtrar</button>
        @if ($temFiltro)
        <a href="{{ route('logs.index') }}">Limpar</a>
        @endif
    </div>
</form>

@if ($registro)
<div class="log-filtro-registro" role="status">
    <i class="fa-solid fa-clock-rotate-left" aria-hidden="true"></i>
    <span>Histórico de <strong>{{ $registro['nome'] }}</strong></span>
    @if ($registro['url'])
    <a href="{{ $registro['url'] }}">Abrir registro</a>
    @endif
    <a href="{{ route('logs.index', \Illuminate\Support\Arr::except($filtros, 'registro')) }}" class="log-filtro-remover">
        <i class="fa-solid fa-xmark" aria-hidden="true"></i> Ver todos os logs
    </a>
</div>
@endif

<div class="log-lista">
    @if ($lotes->total() > 0)
    <p class="log-total">
        {{ $lotes->total() }} {{ $lotes->total() === 1 ? 'ação encontrada' : 'ações encontradas' }}
        @if ($lotes->lastPage() > 1) · página {{ $lotes->currentPage() }} de {{ $lotes->lastPage() }} @endif
    </p>
    @endif

    @forelse ($dias as $dia => $eventos)
    @php
        $data = \Illuminate\Support\Carbon::parse($dia);
        $rotulo = match ($dia) { $hoje => 'Hoje', $ontem => 'Ontem', default => ucfirst($data->translatedFormat('l')) };
    @endphp
    <section class="log-dia" aria-labelledby="dia-{{ $dia }}">
        <h2 id="dia-{{ $dia }}">
            {{ $rotulo }}
            <span class="log-dia-data">{{ $data->translatedFormat('d \d\e F \d\e Y') }}</span>
        </h2>

        <ol class="log-eventos">
            @foreach ($eventos as $evento)
            @php
                $frase = $evento->frase();
                $resumo = $evento->resumo();
            @endphp
            <li>
                <a class="log-evento" href="{{ route('logs.show', $evento->id()) }}">
                    <span class="log-icone {{ $evento->categoria() }}" aria-hidden="true"><i class="fa-solid {{ $evento->icone() }}"></i></span>

                    <span class="log-corpo">
                        <span class="log-frase">
                            <strong class="{{ $evento->feitoPeloSistema() ? 'log-sistema' : '' }}">{{ $evento->autor() }}</strong>
                            {{ $frase['texto'] }}
                            @if ($frase['destaque'])<strong>{{ $frase['destaque'] }}</strong>@endif
                            {{ $frase['complemento'] }}
                            @if ($evento->registroExcluido() && $evento->acao() !== 'excluir')<span class="log-excluido">(excluído)</span>@endif
                        </span>

                        @if ($resumo['itens'] || $resumo['texto'])
                        <span class="log-resumo">
                            @foreach ($resumo['itens'] as $item)
                            <span class="log-mudanca">{{ $item }}</span>
                            @endforeach
                            @if ($resumo['mais'] > 0)
                            <span class="log-mais">+{{ $resumo['mais'] }} {{ $resumo['mais'] === 1 ? 'alteração' : 'alterações' }}</span>
                            @endif
                            @if ($resumo['texto'])
                            <span class="log-texto">{{ $resumo['texto'] }}</span>
                            @endif
                        </span>
                        @endif
                    </span>

                    <span class="log-lado">
                        <time datetime="{{ $evento->quando->toIso8601String() }}"><span class="sr-only">às </span>{{ $evento->quando->format('H:i') }}</time>
                        <span class="log-modulo">{{ $evento->moduloNome() }}</span>
                    </span>
                    <i class="fa-solid fa-chevron-right log-seta" aria-hidden="true"></i>
                </a>
            </li>
            @endforeach
        </ol>
    </section>
    @empty
    <div class="log-vazio">
        <i class="fa-solid fa-clock-rotate-left" aria-hidden="true"></i>
        <p>{{ $temFiltro ? 'Nenhuma ação encontrada com esses filtros.' : 'Nenhuma ação registrada ainda.' }}</p>
    </div>
    @endforelse
</div>

{{ $lotes->links() }}
@endsection
