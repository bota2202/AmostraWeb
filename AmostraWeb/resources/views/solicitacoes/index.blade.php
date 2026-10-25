@extends('layouts.app')

@section('title','Solicitações de prazo - '.config('amostraweb.name'))

@section('texto','Solicitações de prazo')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/layout/table.css') }}">
<link rel="stylesheet" href="{{ asset('css/layout/filtros.css') }}">
<link rel="stylesheet" href="{{ asset('css/layout/detalhes.css') }}">
@endpush

@section('content')
<div class="toolbar">
    <div>
        <h1>Solicitações de prazo</h1>
        <p class="subtitulo-pagina">Pedidos de prorrogação do prazo de avaliação das amostras.</p>
    </div>
</div>

<nav class="abas" aria-label="Visões das solicitações">
    @if ($podeDecidir)
    <a class="aba" href="{{ route('solicitacoes-prazo.index', ['aba' => 'pendentes']) }}" @if ($aba === 'pendentes') aria-current="page" @endif>
        <i class="fa-solid fa-inbox" aria-hidden="true"></i> Aguardando minha decisão
        <span class="contador-aba">{{ $totalPendentes }}</span>
    </a>
    @endif
    <a class="aba" href="{{ route('solicitacoes-prazo.index', ['aba' => 'todas']) }}" @if ($aba === 'todas') aria-current="page" @endif>
        <i class="fa-solid fa-list" aria-hidden="true"></i> Todas
    </a>
</nav>

@if ($aba === 'todas')
<form method="GET" action="{{ route('solicitacoes-prazo.index') }}" class="filtros" aria-label="Filtrar solicitações">
    <input type="hidden" name="aba" value="todas">
    <div class="filtro">
        <label for="situacao">Situação</label>
        <select id="situacao" name="situacao">
            <option value="">Todas</option>
            @foreach (\App\Models\Solicitacao_Prazo::SITUACOES as $valor => $nome)
            <option value="{{ $valor }}" @selected($situacao === $valor)>{{ $nome }}</option>
            @endforeach
        </select>
    </div>
    <div class="filtro-acoes">
        <button type="submit"><i class="fa-solid fa-filter" aria-hidden="true"></i> Filtrar</button>
        @if ($situacao)
        <a href="{{ route('solicitacoes-prazo.index', ['aba' => 'todas']) }}">Limpar</a>
        @endif
    </div>
</form>
@endif

<table>
    <caption class="sr-only">{{ $aba === 'pendentes' ? 'Solicitações aguardando sua decisão' : 'Todas as solicitações de prazo' }}</caption>
    <thead>
        <tr>
            <th scope="col">Amostra</th>
            <th scope="col">Cliente / produto</th>
            <th scope="col">Solicitante</th>
            <th scope="col">Prazo atual</th>
            <th scope="col">Novo prazo</th>
            <th scope="col">Justificativa</th>
            <th scope="col">Situação</th>
            <th scope="col" class="acoes">Ações</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($solicitacoes as $solicitacao)
        <tr>
            <td class="centro">
                {{ $solicitacao->amostra->codigo }}
                <span class="texto-secundario">Pedido {{ $solicitacao->amostra?->pedido?->codigo ?? "-" }}</span>
            </td>
            <td>
                {{ $solicitacao->amostra?->pedido?->cliente?->razao_social ?? "-" }}
                <span class="texto-secundario">{{ $solicitacao->amostra->produto->descricao }}</span>
            </td>
            <td>
                {{ $solicitacao->solicitante->name }}
                <span class="texto-secundario">{{ $solicitacao->data_solicitacao->format('d/m/Y H:i') }}</span>
            </td>
            <td class="centro nao-quebra">{{ $solicitacao->prazo_atual->format('d/m/Y') }}</td>
            <td class="centro nao-quebra"><strong>{{ $solicitacao->novo_prazo->format('d/m/Y') }}</strong></td>
            <td>{{ \Illuminate\Support\Str::limit($solicitacao->justificativa, 120) }}</td>
            <td class="centro">
                <span class="status status-{{ $solicitacao->situacao }}">{{ $solicitacao->situacao_nome }}</span>
                @if ($solicitacao->aprovador)
                <span class="texto-secundario">por {{ $solicitacao->aprovador->name }}</span>
                @endif
            </td>
            <td class="acoes">
                @if ($aba === 'pendentes')
                <form method="POST" action="{{ route('solicitacoes-prazo.update', $solicitacao) }}"
                    data-confirmar="O prazo da amostra {{ $solicitacao->amostra?->codigo }} passa a ser {{ $solicitacao->novo_prazo->format('d/m/Y') }}."
                    data-confirmar-titulo="Aprovar novo prazo?" data-confirmar-botao="Aprovar prazo" data-confirmar-tipo="sucesso">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="situacao" value="aprovada">
                    <button type="submit" title="Aprovar novo prazo da amostra {{ $solicitacao->amostra->codigo }}">
                        <i class="fa-solid fa-check" aria-hidden="true"></i><span class="sr-only">Aprovar novo prazo da amostra {{ $solicitacao->amostra->codigo }}</span>
                    </button>
                </form>
                @endif
                <a href="{{ route('amostras.show', $solicitacao->amostra_id) }}#solicitacoes" title="{{ $aba === 'pendentes' ? 'Responder (aprovar ou reprovar com motivo)' : 'Ver amostra' }} {{ $solicitacao->amostra->codigo }}">
                    <i class="fa-solid {{ $aba === 'pendentes' ? 'fa-reply' : 'fa-eye' }}" aria-hidden="true"></i>
                    <span class="sr-only">{{ $aba === 'pendentes' ? 'Responder' : 'Ver amostra' }} {{ $solicitacao->amostra->codigo }}</span>
                </a>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="8" class="vazio">
                @if ($aba === 'pendentes')
                <i class="fa-solid fa-circle-check" aria-hidden="true"></i> Nenhuma solicitação aguardando sua decisão.
                @else
                Nenhuma solicitação de prazo encontrada.
                @endif
            </td>
        </tr>
        @endforelse
    </tbody>
</table>
{{ $solicitacoes->links() }}
@endsection
