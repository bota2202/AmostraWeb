@extends('layouts.app')

@section('title', 'Dashboard - ' . config('amostraweb.name'))

@section('texto')
    Olá {{ explode(' ', auth()->user()->name)[0] }}!
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/layout/detalhes.css') }}">
    <link rel="stylesheet" href="{{ asset('css/layout/table.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard/dashboard.css') }}">
@endpush

@section('content')
<div class="toolbar">
    <h1>Dashboard</h1>
    <a href="{{ route('amostras.create') }}" class="botao-toolbar">
        <i class="fa-solid fa-plus"></i> Nova Amostra
    </a>
</div>

<div class="cards-resumo">
    <a class="card-resumo" href="{{ route('amostras.index') }}">
        <i class="fa-solid fa-flask"></i>
        <div>
            <strong>{{ $resumo['em_analise'] }}</strong>
            <span>Amostras em análise</span>
        </div>
    </a>
    <a class="card-resumo alerta-card" href="{{ route('amostras.index') }}">
        <i class="fa-solid fa-triangle-exclamation"></i>
        <div>
            <strong>{{ $resumo['atrasadas'] }}</strong>
            <span>Amostras atrasadas</span>
        </div>
    </a>
    <a class="card-resumo" href="{{ route('pedidos.index') }}">
        <i class="fa-solid fa-cart-shopping"></i>
        <div>
            <strong>{{ $resumo['pedidos_abertos'] }}</strong>
            <span>Pedidos abertos</span>
        </div>
    </a>
    <div class="card-resumo">
        <i class="fa-solid fa-calendar-plus"></i>
        <div>
            <strong>{{ $resumo['solicitacoes_pendentes'] }}</strong>
            <span>Solicitações de prazo pendentes</span>
        </div>
    </div>
</div>

<div class="dados paineis">
    <section class="card-secao">
        <h3><i class="fa-solid fa-hourglass-half"></i> Prazos vencidos ou nos próximos 7 dias</h3>
        @if ($prazosProximos->isEmpty())
        <p class="texto-vazio">Nenhuma amostra com prazo próximo.</p>
        @else
        <table>
            <thead>
                <tr>
                    <th>Amostra</th>
                    <th>Produto</th>
                    <th>Cliente</th>
                    <th>Prazo</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($prazosProximos as $amostra)
                <tr>
                    <td class="centro">{{ $amostra->codigo }}</td>
                    <td>{{ $amostra->produto->descricao }}</td>
                    <td>{{ $amostra->pedido?->cliente->razao_social ?? '-' }}</td>
                    <td class="centro {{ $amostra->prazoVencido() ? 'prazo-vencido' : '' }}">{{ $amostra->prazo_avaliacao->format('d/m/Y') }}</td>
                    <td class="acoes">
                        <a href="{{ route('amostras.show', $amostra) }}" title="Ver amostra"><i class="fa-solid fa-eye"></i></a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </section>

    <section class="card-secao">
        <h3><i class="fa-solid fa-calendar-plus"></i> Solicitações de prazo pendentes</h3>
        @if ($solicitacoesPendentes->isEmpty())
        <p class="texto-vazio">Nenhuma solicitação aguardando resposta.</p>
        @else
        <table>
            <thead>
                <tr>
                    <th>Amostra</th>
                    <th>Solicitante</th>
                    <th>Prazo atual</th>
                    <th>Novo prazo</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($solicitacoesPendentes as $solicitacao)
                <tr>
                    <td class="centro">{{ $solicitacao->amostra->codigo }}</td>
                    <td class="centro">{{ $solicitacao->solicitante->codigo_usuario }}</td>
                    <td class="centro">{{ $solicitacao->prazo_atual->format('d/m/Y') }}</td>
                    <td class="centro">{{ $solicitacao->novo_prazo->format('d/m/Y') }}</td>
                    <td class="acoes">
                        <a href="{{ route('amostras.show', $solicitacao->amostra_id) }}" title="Responder"><i class="fa-solid fa-reply"></i></a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </section>
</div>
@endsection
