@extends('layouts.app')

@section('title','Pedidos - '.config('amostraweb.name'))

@section('texto','Pedidos')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/layout/detalhes.css') }}">
<link rel="stylesheet" href="{{ asset('css/layout/table.css') }}">
@endpush

@section('content')
<div class="toolbar">
    <h1><a href="{{ route('pedidos.index') }}">Pedidos </a>> {{ $pedido->codigo }}</h1>
    <a class="botao-toolbar" href="{{ route('pedidos.edit',$pedido) }}"><i class="fa-solid fa-pen"></i> Editar Pedido</a>
</div>

<div class="dados">
    <section class="card-secao">
        <h3><i class="fa-solid fa-receipt"></i> Pedido</h3>
        <div class="campos-secao">
            <div class="label-input">
                <label for="codigo">Código</label>
                <input type="text" id="codigo" disabled value="{{ $pedido->codigo }}">
            </div>
            <div class="label-input">
                <label for="data_emissao">Data de emissão</label>
                <input type="text" id="data_emissao" disabled value="{{ $pedido->data_emissao->format('d/m/Y') }}">
            </div>
            <span class="badge-status {{ $pedido->status }}">
                <i class="fa-solid fa-circle"></i>
                {{ \App\Models\Pedido::STATUS[$pedido->status] }}
            </span>
            <div class="label-input">
                <label for="representante">Representante</label>
                <input type="text" id="representante" disabled value="{{ $pedido->user->codigo_usuario }} - {{ $pedido->user->name }}">
            </div>
            <div class="label-input campo-duplo">
                <label for="estabelecimento">Estabelecimento</label>
                <input type="text" id="estabelecimento" disabled value="#{{ $pedido->estabelecimento->id }} - {{ $pedido->estabelecimento->razao_social }}">
            </div>
            <div class="label-input campo-full">
                <label for="observacao">Observação</label>
                <textarea id="observacao" disabled>{{ $pedido->observacao ?? '-' }}</textarea>
            </div>
        </div>
    </section>

    <section class="card-secao">
        <h3>
            <i class="fa-solid fa-people-group"></i> Cliente
            <a class="h3-acao" href="{{ route('clientes.show', $pedido->cliente) }}"><i class="fa-solid fa-eye"></i> Ver cliente</a>
        </h3>
        <div class="campos-secao">
            <div class="label-input">
                <label for="cliente_codigo">Código</label>
                <input type="text" id="cliente_codigo" disabled value="{{ $pedido->cliente->codigo_empresa }}">
            </div>
            <div class="label-input">
                <label for="cliente_razao">Razão social</label>
                <input type="text" id="cliente_razao" disabled value="{{ $pedido->cliente->razao_social }}">
            </div>
            <div class="label-input">
                <label for="cliente_documento">CNPJ / CPF</label>
                <input type="text" id="cliente_documento" disabled value="{{ $pedido->cliente->cnpj_cpf }}">
            </div>
        </div>
    </section>

    <section class="card-secao">
        <h3>
            <i class="fa-solid fa-flask"></i> Amostras
            @if ($pedido->status === 'aberto')
            <a class="h3-acao" href="{{ route('amostras.create', ['pedido_id' => $pedido->id]) }}"><i class="fa-solid fa-plus"></i> Nova amostra</a>
            @endif
        </h3>
        @if ($pedido->amostras->isEmpty())
        <p class="texto-vazio">Nenhuma amostra vinculada a este pedido.</p>
        @else
        <table>
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Produto</th>
                    <th>Quantidade</th>
                    <th>Prazo de avaliação</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($pedido->amostras as $amostra)
                <tr>
                    <td class="centro">{{ $amostra->codigo }}</td>
                    <td>{{ $amostra->produto->codigo_produto }} - {{ $amostra->produto->descricao }}</td>
                    <td class="centro">{{ $amostra->quantidade }}</td>
                    <td class="centro {{ $amostra->prazoVencido() ? 'prazo-vencido' : '' }}">{{ $amostra->prazo_avaliacao->format('d/m/Y') }}</td>
                    <td class="centro"><span class="status status-{{ $amostra->status }}">{{ \App\Models\Amostra::STATUS[$amostra->status] }}</span></td>
                    <td class="acoes">
                        <a href="{{ route('amostras.show', $amostra) }}" title="Ver detalhes"><i class="fa-solid fa-eye"></i></a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </section>
</div>
@endsection
