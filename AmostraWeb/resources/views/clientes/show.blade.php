@extends('layouts.app')

@section('title','Clientes - '.config('amostraweb.name'))

@section('texto','Clientes')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/layout/detalhes.css') }}">
<link rel="stylesheet" href="{{ asset('css/layout/table.css') }}">
@endpush

@section('content')
<div class="toolbar">
    <h1><a href="{{ route('clientes.index') }}">Clientes </a>> {{ $cliente->codigo_empresa }}</h1>
    <a class="botao-toolbar" href="{{ route('clientes.edit',$cliente) }}"><i class="fa-solid fa-pen"></i> Editar Cliente</a>
</div>

<div class="dados">
    <section class="card-secao">
        <h3><i class="fa-solid fa-id-card"></i> Identificação</h3>
        <div class="campos-secao">
            <div class="label-input">
                <label for="codigo_empresa">Código da empresa</label>
                <input type="text" id="codigo_empresa" disabled value="{{ $cliente->codigo_empresa }}">
            </div>
            <div class="label-input">
                <label for="cnpj_cpf">CNPJ / CPF</label>
                <input type="text" id="cnpj_cpf" disabled value="{{ $cliente->cnpj_cpf }}">
            </div>
            <span class="badge-status {{ $cliente->status ? 'ativo' : 'inativo' }}">
                <i class="fa-solid fa-circle"></i>
                {{ $cliente->status ? 'Ativo' : 'Inativo' }}
            </span>
            <div class="label-input campo-duplo">
                <label for="razao_social">Razão social</label>
                <input type="text" id="razao_social" disabled value="{{ $cliente->razao_social }}">
            </div>
            <div class="label-input">
                <label for="nome_fantasia">Nome fantasia</label>
                <input type="text" id="nome_fantasia" disabled value="{{ $cliente->nome_fantasia ?? '-' }}">
            </div>
        </div>
    </section>

    <section class="card-secao">
        <h3><i class="fa-solid fa-layer-group"></i> Classificação</h3>
        <div class="campos-secao">
            <div class="label-input">
                <label for="tipo">Tipo</label>
                <input type="text" id="tipo" disabled value="{{ $cliente->tipo }}">
            </div>
            <div class="label-input">
                <label for="tipo_empresa">Tipo de empresa</label>
                <input type="text" id="tipo_empresa" disabled value="{{ $cliente->tipo_empresa ?? '-' }}">
            </div>
            <div class="label-input">
                <label for="grupo_fiscal">Grupo fiscal</label>
                <input type="text" id="grupo_fiscal" disabled value="{{ $cliente->grupo_fiscal ?? '-' }}">
            </div>
            <div class="label-input">
                <label for="grupo_economico">Grupo econômico</label>
                <input type="text" id="grupo_economico" disabled value="{{ $cliente->grupo_economico ?? '-' }}">
            </div>
        </div>
    </section>

    <section class="card-secao">
        <h3><i class="fa-solid fa-address-book"></i> Contato</h3>
        <div class="campos-secao">
            <div class="label-input">
                <label for="telefone">Telefone</label>
                <input type="text" id="telefone" disabled value="{{ $cliente->telefone ?? '-' }}">
            </div>
            <div class="label-input campo-duplo">
                <label for="email">E-mail</label>
                <input type="text" id="email" disabled value="{{ $cliente->email ?? '-' }}">
            </div>
            <div class="label-input campo-full">
                <label for="endereco_completo">Endereço completo</label>
                <textarea id="endereco_completo" disabled>{{ $cliente->endereco_completo ?? '-' }}</textarea>
            </div>
        </div>
    </section>

    <section class="card-secao">
        <h3>
            <i class="fa-solid fa-cart-shopping"></i> Últimos pedidos
            <a class="h3-acao" href="{{ route('pedidos.create') }}"><i class="fa-solid fa-plus"></i> Novo pedido</a>
        </h3>
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
                        <a href="{{ route('pedidos.show', $pedido) }}" title="Ver detalhes"><i class="fa-solid fa-eye"></i></a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </section>
</div>
@endsection
