@extends('layouts.app')

@section('title','Clientes - '.config('amostraweb.name'))

@section('texto','Clientes')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/layout/detalhes.css') }}">
<link rel="stylesheet" href="{{ asset('css/layout/table.css') }}">
@endpush

@section('content')
<div class="toolbar">
    <h1><a href="{{ route('clientes.index') }}">Clientes</a> › {{ $cliente->codigo_empresa }}</h1>
    @permite('clientes', 'editar')
    <a class="botao-toolbar" href="{{ route('clientes.edit',$cliente) }}"><i class="fa-solid fa-pen" aria-hidden="true"></i> Editar cliente</a>
    @endpermite
</div>

<div class="dados">
    <section class="card-secao">
        <h2><i class="fa-solid fa-id-card" aria-hidden="true"></i> Identificação</h2>
        <div class="campos-secao">
            <div class="label-input">
                <label for="codigo_empresa">Código da empresa</label>
                <input type="text" id="codigo_empresa" readonly value="{{ $cliente->codigo_empresa }}">
            </div>
            <div class="label-input">
                <label for="cnpj_cpf">CNPJ / CPF</label>
                <input type="text" id="cnpj_cpf" readonly value="{{ $cliente->cnpj_cpf }}">
            </div>
            <span class="badge-status {{ $cliente->status ? 'ativo' : 'inativo' }}">
                <i class="fa-solid fa-circle" aria-hidden="true"></i>
                {{ $cliente->status ? 'Ativo' : 'Inativo' }}
            </span>
            <div class="label-input campo-duplo">
                <label for="razao_social">Razão social</label>
                <input type="text" id="razao_social" readonly value="{{ $cliente->razao_social }}">
            </div>
            <div class="label-input">
                <label for="nome_fantasia">Nome fantasia</label>
                <input type="text" id="nome_fantasia" readonly value="{{ $cliente->nome_fantasia ?? '-' }}">
            </div>
        </div>
    </section>

    <section class="card-secao">
        <h2><i class="fa-solid fa-layer-group" aria-hidden="true"></i> Classificação</h2>
        <div class="campos-secao">
            <div class="label-input">
                <label for="tipo">Tipo</label>
                <input type="text" id="tipo" readonly value="{{ $cliente->tipo }}">
            </div>
            <div class="label-input">
                <label for="tipo_empresa">Tipo de empresa</label>
                <input type="text" id="tipo_empresa" readonly value="{{ $cliente->tipo_empresa ?? '-' }}">
            </div>
            <div class="label-input">
                <label for="grupo_fiscal">Grupo fiscal</label>
                <input type="text" id="grupo_fiscal" readonly value="{{ $cliente->grupo_fiscal ?? '-' }}">
            </div>
            <div class="label-input">
                <label for="grupo_economico">Grupo econômico</label>
                <input type="text" id="grupo_economico" readonly value="{{ $cliente->grupo_economico ?? '-' }}">
            </div>
        </div>
    </section>

    <section class="card-secao">
        <h2><i class="fa-solid fa-address-book" aria-hidden="true"></i> Contato</h2>
        <div class="campos-secao">
            <div class="label-input">
                <label for="telefone">Telefone</label>
                <input type="text" id="telefone" readonly value="{{ $cliente->telefone ?? '-' }}">
            </div>
            <div class="label-input campo-duplo">
                <label for="email">E-mail</label>
                <input type="text" id="email" readonly value="{{ $cliente->email ?? '-' }}">
            </div>
            <div class="label-input campo-full">
                <label for="endereco_completo">Endereço completo</label>
                <textarea id="endereco_completo" readonly>{{ $cliente->endereco_completo ?? '-' }}</textarea>
            </div>
        </div>
    </section>

    <section class="card-secao">
        <h2>
            <i class="fa-solid fa-cart-shopping" aria-hidden="true"></i> Últimos pedidos
            @permite('pedidos', 'criar')
            <a class="h3-acao" href="{{ route('pedidos.create') }}"><i class="fa-solid fa-plus" aria-hidden="true"></i> Novo pedido</a>
            @endpermite
        </h2>
        @if ($pedidos->isEmpty())
        <p class="texto-vazio">Este cliente ainda não possui pedidos.</p>
        @else
        <table>
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Emissão</th>
                    <th>Estabelecimento</th>
                    <th>Representante</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($pedidos as $pedido)
                <tr>
                    <td class="centro">{{ $pedido->codigo }}</td>
                    <td class="centro">{{ $pedido->data_emissao->format('d/m/Y') }}</td>
                    <td>{{ $pedido->estabelecimento->razao_social }}</td>
                    <td class="centro">{{ $pedido->user->codigo_usuario }}</td>
                    <td class="centro"><span class="status status-{{ $pedido->status }}">{{ \App\Models\Pedido::STATUS[$pedido->status] }}</span></td>
                    <td class="acoes">
                        <a href="{{ route('pedidos.show', $pedido) }}" title="Ver detalhes"><i class="fa-solid fa-eye" aria-hidden="true"></i><span class="sr-only">Ver detalhes</span></a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </section>
</div>
@endsection
