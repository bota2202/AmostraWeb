@extends('layouts.app')

@section('title','Pedido '.$pedido->codigo.' - '.config('amostraweb.name'))

@section('texto','Pedidos')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/layout/detalhes.css') }}">
<link rel="stylesheet" href="{{ asset('css/layout/table.css') }}">
@endpush

@php
    $porStatus = $pedido->amostras->countBy('status');
@endphp

@section('content')
<div class="toolbar">
    <h1><a href="{{ route('pedidos.index') }}">Pedidos</a> › {{ $pedido->codigo }}</h1>
    <div class="acoes-toolbar">
        @permite('logs')
        <a class="botao-toolbar botao-secundario" href="{{ route('logs.index', ['registro' => 'Pedido-'.$pedido->id]) }}"><i class="fa-solid fa-clock-rotate-left" aria-hidden="true"></i> Histórico</a>
        @endpermite
        @permite('pedidos', 'editar')
        <a class="botao-toolbar" href="{{ route('pedidos.edit', $pedido) }}"><i class="fa-solid fa-pen" aria-hidden="true"></i> Editar pedido</a>
        @endpermite
    </div>
</div>

<div class="dados">
    <section class="card-secao" aria-labelledby="titulo-pedido">
        <h2 id="titulo-pedido"><i class="fa-solid fa-receipt" aria-hidden="true"></i> Pedido</h2>
        <div class="campos-secao">
            <div class="label-input">
                <label for="codigo">Código</label>
                <input type="text" id="codigo" readonly value="{{ $pedido->codigo }}">
            </div>
            <div class="label-input">
                <label for="data_emissao">Data de envio</label>
                <input type="text" id="data_emissao" readonly value="{{ $pedido->data_emissao->format('d/m/Y') }}">
            </div>
            <span class="badge-status {{ $pedido->status }}">
                <i class="fa-solid fa-circle" aria-hidden="true"></i>
                {{ $pedido->status_nome }}
            </span>
            <div class="label-input">
                <label for="vendedor">Vendedor responsável</label>
                <input type="text" id="vendedor" readonly value="{{ $pedido->user->codigo_usuario }} - {{ $pedido->user->name }}">
            </div>
            <div class="label-input campo-duplo">
                <label for="estabelecimento">Estabelecimento</label>
                <input type="text" id="estabelecimento" readonly value="{{ $pedido->estabelecimento->razao_social }}">
            </div>
            <div class="label-input campo-full">
                <label for="observacao">Observação</label>
                <textarea id="observacao" readonly>{{ $pedido->observacao ?: 'Sem observações.' }}</textarea>
            </div>
        </div>
    </section>

    <section class="card-secao" aria-labelledby="titulo-cliente">
        <h2 id="titulo-cliente">
            <i class="fa-solid fa-people-group" aria-hidden="true"></i> Cliente
            @permite('clientes')
            <a class="h3-acao" href="{{ route('clientes.show', $pedido->cliente) }}">Ver cliente</a>
            @endpermite
        </h2>
        <div class="campos-secao">
            <div class="label-input">
                <label for="cliente_codigo">Código</label>
                <input type="text" id="cliente_codigo" readonly value="{{ $pedido->cliente->codigo_empresa }}">
            </div>
            <div class="label-input">
                <label for="cliente_razao">Razão social</label>
                <input type="text" id="cliente_razao" readonly value="{{ $pedido->cliente->razao_social }}">
            </div>
            <div class="label-input">
                <label for="cliente_documento">CNPJ / CPF</label>
                <input type="text" id="cliente_documento" readonly value="{{ $pedido->cliente->cnpj_cpf }}">
            </div>
        </div>
    </section>

    <section class="card-secao" aria-labelledby="titulo-amostras">
        <h2 id="titulo-amostras">
            <i class="fa-solid fa-flask" aria-hidden="true"></i> Amostras ({{ $pedido->amostras->count() }})
            @if ($pedido->status === 'aberto')
            @permite('amostras', 'criar')
            <a class="h3-acao" href="{{ route('amostras.create', ['pedido_id' => $pedido->id]) }}"><i class="fa-solid fa-plus" aria-hidden="true"></i> Adicionar amostra</a>
            @endpermite
            @endif
        </h2>
        <p class="texto-vazio resumo-amostras">
            @foreach (\App\Models\Amostra::STATUS as $status => $nome)
                @if ($porStatus[$status] ?? 0)
                <span class="status status-{{ $status }}">{{ $porStatus[$status] }} {{ mb_strtolower($nome) }}</span>
                @endif
            @endforeach
        </p>
        <table>
            <caption class="sr-only">Amostras do pedido {{ $pedido->codigo }}</caption>
            <thead>
                <tr>
                    <th scope="col">Código</th>
                    <th scope="col">Produto</th>
                    <th scope="col">Quantidade</th>
                    <th scope="col">Prazo de avaliação</th>
                    <th scope="col">Status</th>
                    <th scope="col" class="acoes">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($pedido->amostras as $amostra)
                <tr>
                    <td class="centro">{{ $amostra->codigo }}</td>
                    <td>{{ $amostra->produto->codigo_produto }} - {{ $amostra->produto->descricao }}</td>
                    <td class="centro">{{ $amostra->quantidade }}</td>
                    <td class="centro nao-quebra {{ $amostra->prazoVencido() ? 'prazo-vencido' : '' }}">
                        @if ($amostra->prazoVencido())<i class="fa-solid fa-circle-exclamation" aria-hidden="true"></i>@endif
                        {{ $amostra->prazo_avaliacao->format('d/m/Y') }}
                        @if ($amostra->prazoVencido())<span class="sr-only"> (vencido)</span>@endif
                    </td>
                    <td class="centro"><span class="status status-{{ $amostra->status }}">{{ $amostra->status_nome }}</span></td>
                    <td class="acoes">
                        <a href="{{ route('amostras.show', $amostra) }}" title="Ver amostra {{ $amostra->codigo }}"><i class="fa-solid fa-eye" aria-hidden="true"></i><span class="sr-only">Ver amostra {{ $amostra->codigo }}</span></a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </section>

</div>
@endsection
