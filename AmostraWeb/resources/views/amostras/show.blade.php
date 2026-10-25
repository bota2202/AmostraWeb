@extends('layouts.app')

@section('title','Amostras - '.config('amostraweb.name'))

@section('texto','Amostras')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/layout/detalhes.css') }}">
<link rel="stylesheet" href="{{ asset('css/layout/table.css') }}">
<link rel="stylesheet" href="{{ asset('css/amostra/show.css') }}">
@endpush

@section('content')
<div class="toolbar">
    <h1><a href="{{ route('amostras.index') }}">Amostras </a>> {{ $amostra->codigo }}</h1>
    <a class="botao-toolbar" href="{{ route('amostras.edit',$amostra) }}"><i class="fa-solid fa-pen"></i> Editar Amostra</a>
</div>

<div class="dados">
    <section class="card-secao">
        <h3><i class="fa-solid fa-flask"></i> Amostra</h3>
        <div class="campos-secao">
            <div class="label-input">
                <label for="codigo">Código</label>
                <input type="text" id="codigo" disabled value="{{ $amostra->codigo }}">
            </div>
            <div class="label-input">
                <label for="quantidade">Quantidade</label>
                <input type="text" id="quantidade" disabled value="{{ $amostra->quantidade }}">
            </div>
            <span class="badge-status {{ $amostra->status }}">
                <i class="fa-solid fa-circle"></i>
                {{ \App\Models\Amostra::STATUS[$amostra->status] }}
            </span>
            <div class="label-input">
                <label for="prazo_avaliacao">Prazo de avaliação</label>
                <input type="text" id="prazo_avaliacao" disabled value="{{ $amostra->prazo_avaliacao->format('d/m/Y') }}{{ $amostra->prazoVencido() ? ' (vencido)' : '' }}">
            </div>
            <div class="label-input">
                <label for="cadastro">Cadastrada em</label>
                <input type="text" id="cadastro" disabled value="{{ $amostra->created_at?->format('d/m/Y H:i') }}">
            </div>
        </div>
    </section>

    <section class="card-secao">
        <h3>
            <i class="fa-solid fa-tags"></i> Produto
            <a class="h3-acao" href="{{ route('produtos.show', $amostra->produto) }}"><i class="fa-solid fa-eye"></i> Ver produto</a>
        </h3>
        <div class="campos-secao">
            <div class="label-input">
                <label for="produto_codigo">Código</label>
                <input type="text" id="produto_codigo" disabled value="{{ $amostra->produto->codigo_produto }}">
            </div>
            <div class="label-input campo-duplo">
                <label for="produto_descricao">Descrição</label>
                <input type="text" id="produto_descricao" disabled value="{{ $amostra->produto->descricao }}">
            </div>
        </div>
    </section>

    <section class="card-secao">
        <h3>
            <i class="fa-solid fa-cart-shopping"></i> Pedido
            @if ($amostra->pedido)
            <a class="h3-acao" href="{{ route('pedidos.show', $amostra->pedido) }}"><i class="fa-solid fa-eye"></i> Ver pedido</a>
            @endif
        </h3>
        @if ($amostra->pedido)
        <div class="campos-secao">
            <div class="label-input">
                <label for="pedido_codigo">Código</label>
                <input type="text" id="pedido_codigo" disabled value="{{ $amostra->pedido->codigo }}">
            </div>
            <div class="label-input campo-duplo">
                <label for="pedido_cliente">Cliente</label>
                <input type="text" id="pedido_cliente" disabled value="{{ $amostra->pedido->cliente->codigo_empresa }} - {{ $amostra->pedido->cliente->razao_social }}">
            </div>
        </div>
        @else
        <p class="texto-vazio">Amostra sem pedido vinculado.</p>
        @endif
    </section>

    {{-- ===================== SOLICITAÇÕES DE PRAZO ===================== --}}
    <section class="card-secao">
        <h3><i class="fa-solid fa-calendar-plus"></i> Solicitações de prazo</h3>

        @if ($amostra->solicitacoesPrazo->isEmpty())
        <p class="texto-vazio">Nenhuma solicitação de prazo para esta amostra.</p>
        @else
        <table>
            <thead>
                <tr>
                    <th>Solicitada em</th>
                    <th>Solicitante</th>
                    <th>Prazo atual</th>
                    <th>Novo prazo</th>
                    <th>Justificativa</th>
                    <th>Situação</th>
                    <th>Respondida por</th>
                    <th>Motivo</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($amostra->solicitacoesPrazo as $solicitacao)
                <tr>
                    <td class="centro">{{ $solicitacao->data_solicitacao->format('d/m/Y H:i') }}</td>
                    <td class="centro">{{ $solicitacao->solicitante->codigo_usuario }}</td>
                    <td class="centro">{{ $solicitacao->prazo_atual->format('d/m/Y') }}</td>
                    <td class="centro">{{ $solicitacao->novo_prazo->format('d/m/Y') }}</td>
                    <td>{{ $solicitacao->justificativa }}</td>
                    <td class="centro"><span class="status status-{{ $solicitacao->situacao }}">{{ \App\Models\Solicitacao_Prazo::SITUACOES[$solicitacao->situacao] }}</span></td>
                    <td class="centro">
                        {{ $solicitacao->aprovador?->codigo_usuario ?? '-' }}
                        @if ($solicitacao->data_aprovacao)
                        <br><small>{{ $solicitacao->data_aprovacao->format('d/m/Y H:i') }}</small>
                        @endif
                    </td>
                    <td>{{ $solicitacao->motivo_aprovacao_reprovacao ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif

        @php $pendente = $amostra->solicitacoesPrazo->firstWhere('situacao', 'pendente'); @endphp

        @if ($pendente)
        {{-- Responder a solicitação pendente --}}
        <form action="{{ route('solicitacoes-prazo.update', $pendente) }}" method="POST" class="form-solicitacao">
            @csrf
            @method('PUT')
            <h4>Responder solicitação pendente (novo prazo: {{ $pendente->novo_prazo->format('d/m/Y') }})</h4>
            <div class="label-input">
                <label for="motivo_aprovacao_reprovacao">Motivo (obrigatório para reprovar)</label>
                <textarea id="motivo_aprovacao_reprovacao" name="motivo_aprovacao_reprovacao" maxlength="1000">{{ old('motivo_aprovacao_reprovacao') }}</textarea>
                @error('motivo_aprovacao_reprovacao', 'decisao')
                <span class="erro-campo">{{ $message }}</span>
                @enderror
                @error('situacao', 'decisao')
                <span class="erro-campo">{{ $message }}</span>
                @enderror
            </div>
            <div class="acoes-form-solicitacao">
                <button type="submit" form="form-cancelar-solicitacao" class="botao-card cinza"><i class="fa-solid fa-ban"></i> Cancelar solicitação</button>
                <button type="submit" name="situacao" value="reprovada" class="botao-card vermelho"><i class="fa-solid fa-xmark"></i> Reprovar</button>
                <button type="submit" name="situacao" value="aprovada" class="botao-card verde"><i class="fa-solid fa-check"></i> Aprovar</button>
            </div>
        </form>
        <form id="form-cancelar-solicitacao" action="{{ route('solicitacoes-prazo.destroy', $pendente) }}" method="POST" onsubmit="return confirm('Deseja cancelar esta solicitação de prazo?')">
            @csrf
            @method('DELETE')
        </form>
        @elseif ($amostra->emAberto())
        {{-- Nova solicitação --}}
        <form action="{{ route('solicitacoes-prazo.store', $amostra) }}" method="POST" class="form-solicitacao">
            @csrf
            <h4>Solicitar novo prazo</h4>
            <div class="campos-solicitacao">
                <div class="label-input">
                    <label for="novo_prazo">Novo prazo</label>
                    <input type="date" id="novo_prazo" name="novo_prazo" required min="{{ today()->addDay()->format('Y-m-d') }}" value="{{ old('novo_prazo') }}">
                    @error('novo_prazo', 'solicitacao')
                    <span class="erro-campo">{{ $message }}</span>
                    @enderror
                </div>
                <div class="label-input">
                    <label for="justificativa">Justificativa</label>
                    <textarea id="justificativa" name="justificativa" required minlength="10" maxlength="1000" placeholder="Explique por que o prazo precisa ser estendido">{{ old('justificativa') }}</textarea>
                    @error('justificativa', 'solicitacao')
                    <span class="erro-campo">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="acoes-form-solicitacao">
                <button type="submit" class="botao-card"><i class="fa-solid fa-paper-plane"></i> Enviar solicitação</button>
            </div>
        </form>
        @endif
    </section>
</div>
@endsection
