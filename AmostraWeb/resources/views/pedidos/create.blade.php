@extends('layouts.app')

@section('title','Novo Pedido - '.config('amostraweb.name'))

@section('texto','Pedidos')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/layout/form.css') }}">
<link rel="stylesheet" href="{{ asset('css/pedido/create.css') }}">
@endpush

@php
    $ehVendedor = auth()->user()->isVendedor();
    // linhas de amostra: as enviadas (quando a validação falha) ou uma linha vazia
    $itens = old('amostras', [['produto_id' => '', 'quantidade' => 1, 'prazo_avaliacao' => '']]);
@endphp

@section('content')
<div class="toolbar">
    <h1><a href="{{ route('pedidos.index') }}">Pedidos</a> > Novo</h1>
</div>

<form action="{{ route('pedidos.store') }}" method="post" class="form-cadastro">
    @csrf

    {{-- ============ DADOS GERAIS ============ --}}
    <div class="form-grupo grupo-duplo">
        <label for="cliente_id">Cliente</label>
        <select name="cliente_id" id="cliente_id" required>
            <option value="">Selecione</option>
            @foreach ($clientes as $cliente)
            <option value="{{ $cliente->id }}" @selected(old('cliente_id')==$cliente->id)>{{ $cliente->codigo_empresa }} - {{ $cliente->razao_social }}</option>
            @endforeach
        </select>
        @error('cliente_id')
        <p class="erro-campo">{{ $message }}</p>
        @enderror
    </div>

    <div class="form-grupo">
        <label for="data_emissao">Data de envio</label>
        <input type="date" name="data_emissao" required id="data_emissao" value="{{ old('data_emissao', today()->format('Y-m-d')) }}">
        @error('data_emissao')
        <p class="erro-campo">{{ $message }}</p>
        @enderror
    </div>

    <div class="form-grupo grupo-full">
        <label for="user_id">Vendedor responsável</label>
        @if ($ehVendedor)
        <input type="text" id="user_id" readonly value="{{ auth()->user()->codigo_usuario }} - {{ auth()->user()->name }}">
        @else
        <select name="user_id" id="user_id" required>
            <option value="">Selecione</option>
            @foreach ($vendedores as $vendedor)
            <option value="{{ $vendedor->id }}" @selected(old('user_id')==$vendedor->id)>{{ $vendedor->codigo_usuario }} - {{ $vendedor->name }} ({{ $vendedor->estabelecimento?->razao_social }})</option>
            @endforeach
        </select>
        @endif
        <p class="dica-campo">O estabelecimento do pedido é o do vendedor responsável.</p>
        @error('user_id')
        <p class="erro-campo">{{ $message }}</p>
        @enderror
    </div>

    <div class="form-grupo grupo-full">
        <label for="observacao">Observação</label>
        <textarea name="observacao" id="observacao" maxlength="255" placeholder="Informações adicionais sobre o pedido">{{ old('observacao') }}</textarea>
        @error('observacao')
        <p class="erro-campo">{{ $message }}</p>
        @enderror
    </div>

    {{-- ============ PRODUTOS (cada linha vira uma amostra) ============ --}}
    <section class="itens-pedido grupo-full">
        <div class="itens-cabecalho">
            <h2><i class="fa-solid fa-flask"></i> Produtos do pedido</h2>
            <p>Cada produto gera uma amostra com código próprio e status <strong>Em análise</strong>.</p>
        </div>

        @error('amostras')
        <p class="erro-campo">{{ $message }}</p>
        @enderror

        <div class="itens-titulos" aria-hidden="true">
            <span>Produto</span>
            <span>Quantidade</span>
            <span>Prazo de avaliação</span>
            <span></span>
        </div>

        <div id="lista-itens">
            @foreach ($itens as $i => $item)
            <div class="item-amostra" data-item>
                <div class="form-grupo">
                    <label class="rotulo-celular" for="amostras-{{ $i }}-produto">Produto</label>
                    <select name="amostras[{{ $i }}][produto_id]" id="amostras-{{ $i }}-produto" required>
                        <option value="">Selecione o produto</option>
                        @foreach ($produtos as $produto)
                        <option value="{{ $produto->id }}" @selected(($item['produto_id'] ?? '') == $produto->id)>{{ $produto->codigo_produto }} - {{ $produto->descricao }}</option>
                        @endforeach
                    </select>
                    @error("amostras.$i.produto_id")
                    <p class="erro-campo">{{ $message }}</p>
                    @enderror
                </div>
                <div class="form-grupo">
                    <label class="rotulo-celular" for="amostras-{{ $i }}-quantidade">Quantidade</label>
                    <input type="number" name="amostras[{{ $i }}][quantidade]" id="amostras-{{ $i }}-quantidade" min="1" step="1" required value="{{ $item['quantidade'] ?? 1 }}">
                    @error("amostras.$i.quantidade")
                    <p class="erro-campo">{{ $message }}</p>
                    @enderror
                </div>
                <div class="form-grupo">
                    <label class="rotulo-celular" for="amostras-{{ $i }}-prazo">Prazo de avaliação</label>
                    <input type="date" name="amostras[{{ $i }}][prazo_avaliacao]" id="amostras-{{ $i }}-prazo" min="{{ today()->format('Y-m-d') }}" required value="{{ $item['prazo_avaliacao'] ?? '' }}">
                    @error("amostras.$i.prazo_avaliacao")
                    <p class="erro-campo">{{ $message }}</p>
                    @enderror
                </div>
                <button type="button" class="remover-item" data-remover title="Remover produto" aria-label="Remover produto">
                    <i class="fa-solid fa-trash" aria-hidden="true"></i>
                </button>
            </div>
            @endforeach
        </div>

        <button type="button" class="adicionar-item" id="adicionar-item">
            <i class="fa-solid fa-plus" aria-hidden="true"></i> Adicionar produto
        </button>
    </section>

    <div class="form-acoes">
        <a href="{{ route('pedidos.index') }}" class="botao-cancelar">Cancelar</a>
        <button type="submit" class="botao-salvar">Salvar pedido</button>
    </div>
</form>
@endsection

@push('scripts')
<script>
    // Linhas de produto: adicionar / remover (sempre fica pelo menos uma)
    const lista = document.getElementById('lista-itens');

    function renumerar() {
        lista.querySelectorAll('[data-item]').forEach((linha, i) => {
            linha.querySelectorAll('[name]').forEach(campo => {
                campo.name = campo.name.replace(/amostras\[\d+\]/, `amostras[${i}]`);
                campo.id = campo.id.replace(/amostras-\d+-/, `amostras-${i}-`);
            });
            linha.querySelectorAll('label[for]').forEach(rotulo => {
                rotulo.htmlFor = rotulo.htmlFor.replace(/amostras-\d+-/, `amostras-${i}-`);
            });
        });
        const linhas = lista.querySelectorAll('[data-item]');
        linhas.forEach(l => l.querySelector('[data-remover]').disabled = linhas.length === 1);
    }

    document.getElementById('adicionar-item').addEventListener('click', () => {
        const modelo = lista.querySelector('[data-item]');
        const nova = modelo.cloneNode(true);
        nova.querySelectorAll('.erro-campo').forEach(e => e.remove());
        nova.querySelector('select').value = '';
        nova.querySelector('input[type=number]').value = 1;
        // novo item já vem com o mesmo prazo do anterior, para agilizar
        nova.querySelector('input[type=date]').value = lista.querySelector('[data-item]:last-child input[type=date]').value;
        lista.appendChild(nova);
        renumerar();
        nova.querySelector('select').focus();
    });

    lista.addEventListener('click', e => {
        const botao = e.target.closest('[data-remover]');
        if (!botao || botao.disabled) return;
        botao.closest('[data-item]').remove();
        renumerar();
    });

    renumerar();
</script>
@endpush
