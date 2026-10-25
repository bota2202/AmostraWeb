@extends('layouts.app')

@section('title', 'Dashboard - ' . config('amostraweb.name'))

@section('texto')
    Olá, {{ explode(' ', auth()->user()->name)[0] }}!
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/layout/detalhes.css') }}">
    <link rel="stylesheet" href="{{ asset('css/layout/table.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard/dashboard.css') }}">
@endpush

@php
    $escopo = auth()->user()->isAdmin() ? 'todos os estabelecimentos'
        : (auth()->user()->isGestor() ? 'estabelecimentos sob sua gestão' : 'seus pedidos');
@endphp

@section('content')
<div class="toolbar">
    <div>
        <h1>Dashboard</h1>
        <p class="subtitulo-pagina">Visão de {{ $escopo }} · {{ now()->translatedFormat('d \d\e F \d\e Y') }}</p>
    </div>
    @permite('pedidos', 'criar')
    <a href="{{ route('pedidos.create') }}" class="botao-toolbar">
        <i class="fa-solid fa-plus" aria-hidden="true"></i> Novo pedido
    </a>
    @endpermite
</div>

{{-- ============ RESUMO ============ --}}
<section aria-labelledby="titulo-resumo">
    <h2 id="titulo-resumo" class="sr-only">Resumo</h2>
    <ul class="cards-resumo">
        <li>
            <a class="card-resumo" href="{{ route('amostras.index', ['status' => 'em_analise']) }}">
                <span class="icone-resumo azul" aria-hidden="true"><i class="fa-solid fa-flask"></i></span>
                <span><strong>{{ $resumo['em_analise'] }}</strong> amostras em análise</span>
            </a>
        </li>
        <li>
            <a class="card-resumo {{ $resumo['atrasadas'] > 0 ? 'destaque-vermelho' : '' }}" href="{{ route('amostras.index', ['prazo' => 'vencido']) }}">
                <span class="icone-resumo vermelho" aria-hidden="true"><i class="fa-solid fa-triangle-exclamation"></i></span>
                <span><strong>{{ $resumo['atrasadas'] }}</strong> amostras atrasadas</span>
            </a>
        </li>
        <li>
            <a class="card-resumo" href="{{ route('amostras.index', ['prazo' => 'proximo']) }}">
                <span class="icone-resumo laranja" aria-hidden="true"><i class="fa-solid fa-hourglass-half"></i></span>
                <span><strong>{{ $resumo['vencendo'] }}</strong> vencem em até {{ $diasAlerta }} dias</span>
            </a>
        </li>
        <li>
            <a class="card-resumo" href="{{ route('pedidos.index', ['status' => 'aberto']) }}">
                <span class="icone-resumo cinza" aria-hidden="true"><i class="fa-solid fa-cart-shopping"></i></span>
                <span><strong>{{ $resumo['pedidos_abertos'] }}</strong> pedidos em aberto</span>
            </a>
        </li>
        <li>
            <a class="card-resumo {{ $decideSolicitacoes && $resumo['solicitacoes'] > 0 ? 'destaque-laranja' : '' }}" href="{{ route('solicitacoes-prazo.index') }}">
                <span class="icone-resumo laranja" aria-hidden="true"><i class="fa-solid fa-calendar-plus"></i></span>
                <span>
                    <strong>{{ $resumo['solicitacoes'] }}</strong>
                    {{ $decideSolicitacoes ? 'solicitações aguardando sua decisão' : 'solicitações de prazo suas em análise' }}
                </span>
            </a>
        </li>
    </ul>
</section>

<div class="paineis">
    {{-- ============ STATUS DAS AMOSTRAS ============ --}}
    <section class="card-secao" aria-labelledby="titulo-status">
        <h2 id="titulo-status"><i class="fa-solid fa-chart-simple" aria-hidden="true"></i> Amostras por status</h2>
        @if ($totalAmostras === 0)
        <p class="texto-vazio">Nenhuma amostra cadastrada ainda.</p>
        @else
        <ul class="barras">
            @foreach ($porStatus as $status => $quantidade)
            @php $porcentagem = round($quantidade / $totalAmostras * 100); @endphp
            <li>
                <a href="{{ route('amostras.index', ['status' => $status]) }}" class="barra-linha">
                    <span class="barra-rotulo">{{ \App\Models\Amostra::STATUS[$status] }}</span>
                    <span class="barra-trilho" aria-hidden="true"><span class="barra barra-{{ $status }}" style="width: {{ max($porcentagem, $quantidade > 0 ? 2 : 0) }}%"></span></span>
                    <span class="barra-valor">{{ $quantidade }} <span class="texto-secundario-inline">({{ $porcentagem }}%)</span></span>
                </a>
            </li>
            @endforeach
        </ul>
        <div class="indicador-aprovacao">
            @if ($taxaAprovacao === null)
                <p>Ainda não há amostras aprovadas ou reprovadas nos últimos 90 dias.</p>
            @else
                <p class="indicador-numero">{{ $taxaAprovacao }}%</p>
                <p>de aprovação nos últimos 90 dias <span class="texto-secundario-inline">({{ $avaliadas90 }} {{ $avaliadas90 === 1 ? 'amostra avaliada' : 'amostras avaliadas' }})</span></p>
            @endif
        </div>
        @endif
    </section>

    {{-- ============ PRAZOS ============ --}}
    <section class="card-secao" aria-labelledby="titulo-prazos">
        <h2 id="titulo-prazos">
            <i class="fa-solid fa-hourglass-half" aria-hidden="true"></i> Prazos vencidos ou nos próximos {{ $diasAlerta }} dias
            @if ($prazosProximos->isNotEmpty())
            <a class="h3-acao" href="{{ route('amostras.index', ['prazo' => 'vencido']) }}">Ver atrasadas</a>
            @endif
        </h2>
        @if ($prazosProximos->isEmpty())
        <p class="texto-vazio"><i class="fa-solid fa-circle-check" aria-hidden="true"></i> Nenhuma amostra com prazo vencido ou próximo.</p>
        @else
        <table>
            <caption class="sr-only">Amostras com prazo vencido ou próximo</caption>
            <thead>
                <tr>
                    <th scope="col">Amostra</th>
                    <th scope="col">Produto / cliente</th>
                    <th scope="col">Prazo</th>
                    <th scope="col" class="acoes"><span class="sr-only">Ações</span></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($prazosProximos as $amostra)
                <tr>
                    <td class="centro">{{ $amostra->codigo }}</td>
                    <td>
                        {{ $amostra->produto->descricao }}
                        <span class="texto-secundario">{{ $amostra->pedido?->cliente?->razao_social ?? "-" }}</span>
                    </td>
                    <td class="centro nao-quebra {{ $amostra->prazoVencido() ? 'prazo-vencido' : '' }}">
                        @if ($amostra->prazoVencido())<i class="fa-solid fa-circle-exclamation" aria-hidden="true"></i>@endif
                        {{ $amostra->prazo_avaliacao->format('d/m/Y') }}
                        @if ($amostra->prazoVencido())<span class="sr-only"> (vencido)</span>@endif
                    </td>
                    <td class="acoes">
                        <a href="{{ route('amostras.show', $amostra) }}" title="Ver amostra {{ $amostra->codigo }}"><i class="fa-solid fa-eye" aria-hidden="true"></i><span class="sr-only">Ver amostra {{ $amostra->codigo }}</span></a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </section>

    {{-- ============ SOLICITAÇÕES ============ --}}
    <section class="card-secao painel-largo" aria-labelledby="titulo-solicitacoes">
        <h2 id="titulo-solicitacoes">
            <i class="fa-solid fa-calendar-plus" aria-hidden="true"></i>
            {{ $decideSolicitacoes ? 'Solicitações de prazo aguardando sua decisão' : 'Minhas solicitações de prazo em análise' }}
            @permite('solicitacoes_prazo')
            <a class="h3-acao" href="{{ route('solicitacoes-prazo.index') }}">Ver todas</a>
            @endpermite
        </h2>
        @if ($solicitacoesLista->isEmpty())
        <p class="texto-vazio"><i class="fa-solid fa-circle-check" aria-hidden="true"></i> Nenhuma solicitação pendente.</p>
        @else
        <table>
            <caption class="sr-only">Solicitações de prazo pendentes</caption>
            <thead>
                <tr>
                    <th scope="col">Amostra</th>
                    <th scope="col">Solicitante</th>
                    <th scope="col">Pedida em</th>
                    <th scope="col">Prazo atual</th>
                    <th scope="col">Novo prazo</th>
                    <th scope="col" class="acoes"><span class="sr-only">Ações</span></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($solicitacoesLista as $solicitacao)
                <tr>
                    <td class="centro">{{ $solicitacao->amostra->codigo }}</td>
                    <td>{{ $solicitacao->solicitante->name }}</td>
                    <td class="centro">{{ $solicitacao->data_solicitacao->format('d/m/Y') }}</td>
                    <td class="centro">{{ $solicitacao->prazo_atual->format('d/m/Y') }}</td>
                    <td class="centro">{{ $solicitacao->novo_prazo->format('d/m/Y') }}</td>
                    <td class="acoes">
                        <a href="{{ route('amostras.show', $solicitacao->amostra_id) }}#solicitacoes" title="{{ $decideSolicitacoes ? 'Responder' : 'Ver' }} solicitação da amostra {{ $solicitacao->amostra->codigo }}">
                            <i class="fa-solid {{ $decideSolicitacoes ? 'fa-reply' : 'fa-eye' }}" aria-hidden="true"></i>
                            <span class="sr-only">{{ $decideSolicitacoes ? 'Responder' : 'Ver' }} solicitação da amostra {{ $solicitacao->amostra->codigo }}</span>
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </section>
</div>
@endsection
