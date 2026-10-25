@extends('layouts.app')

@section('title','Amostra '.$amostra->codigo.' - '.config('amostraweb.name'))

@section('texto','Amostras')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/layout/detalhes.css') }}">
<link rel="stylesheet" href="{{ asset('css/layout/table.css') }}">
<link rel="stylesheet" href="{{ asset('css/amostra/show.css') }}">
@endpush

@php
    $user = auth()->user();
    $podeEditar = $amostra->emAberto() && $user->temPermissao('amostras', 'editar');
    $podeCancelar = $amostra->emAberto() && $user->temPermissao('amostras', 'cancelar');
@endphp

@section('content')
<div class="toolbar">
    <h1>
        <a href="{{ route('amostras.index') }}">Amostras</a> ›
        @if ($amostra->pedido)<a href="{{ route('pedidos.show', $amostra->pedido) }}">Pedido {{ $amostra->pedido->codigo }}</a> ›@endif
        {{ $amostra->codigo }}
    </h1>
    <div class="acoes-toolbar">
        @permite('logs')
        <a class="botao-toolbar botao-secundario" href="{{ route('logs.index', ['registro' => 'Amostra-'.$amostra->id]) }}"><i class="fa-solid fa-clock-rotate-left" aria-hidden="true"></i> Histórico</a>
        @endpermite
        @if ($podeEditar)
        <a class="botao-toolbar botao-secundario" href="{{ route('amostras.edit', $amostra) }}"><i class="fa-solid fa-pen" aria-hidden="true"></i> Editar amostra</a>
        @endif
    </div>
</div>

<div class="dados">
    {{-- ===================== DADOS ===================== --}}
    <section class="card-secao" aria-labelledby="titulo-amostra">
        <h2 id="titulo-amostra"><i class="fa-solid fa-flask" aria-hidden="true"></i> Amostra</h2>
        <div class="campos-secao">
            <div class="label-input">
                <label for="codigo">Código</label>
                <input type="text" id="codigo" readonly value="{{ $amostra->codigo }}">
            </div>
            <div class="label-input">
                <label for="quantidade">Quantidade</label>
                <input type="text" id="quantidade" readonly value="{{ $amostra->quantidade }}">
            </div>
            <span class="badge-status {{ $amostra->status }}">
                <i class="fa-solid fa-circle" aria-hidden="true"></i>
                {{ $amostra->status_nome }}
            </span>
            <div class="label-input">
                <label for="prazo_avaliacao">Prazo de avaliação</label>
                <input type="text" id="prazo_avaliacao" readonly value="{{ $amostra->prazo_avaliacao->format('d/m/Y') }}{{ $amostra->prazoVencido() ? ' (vencido)' : '' }}">
            </div>
            <div class="label-input">
                <label for="cliente">Cliente</label>
                <input type="text" id="cliente" readonly value="{{ $amostra->pedido?->cliente?->razao_social ?? "-" }}">
            </div>
            <div class="label-input">
                <label for="vendedor">Vendedor responsável</label>
                <input type="text" id="vendedor" readonly value="{{ $amostra->pedido?->user?->name ?? "-" }}">
            </div>
            <div class="label-input campo-duplo">
                <label for="produto">Produto</label>
                <input type="text" id="produto" readonly value="{{ $amostra->produto->codigo_produto }} - {{ $amostra->produto->descricao }}">
            </div>
            <div class="label-input">
                <label for="cadastro">Cadastrada em</label>
                <input type="text" id="cadastro" readonly value="{{ $amostra->created_at?->format('d/m/Y H:i') }}">
            </div>
        </div>
    </section>

    {{-- ===================== RESULTADO / CANCELAMENTO ===================== --}}
    @if ($podeEditar || $podeCancelar)
    <section class="card-secao" aria-labelledby="titulo-resultado">
        <h2 id="titulo-resultado"><i class="fa-solid fa-clipboard-check" aria-hidden="true"></i> Resultado da avaliação</h2>

        @if ($podeEditar)
        <form method="POST" action="{{ route('amostras.aprovar', $amostra) }}" class="form-solicitacao">
            @csrf
            <p class="texto-ajuda">Registre aqui a resposta do cliente. Depois de aprovada ou reprovada, a amostra é finalizada e não pode mais ser alterada.</p>
            <div class="label-input">
                <label for="observacao">Comentário (opcional)</label>
                <textarea id="observacao" name="observacao" maxlength="1000" placeholder="Ex.: cliente aprovou e vai fazer o pedido de 200 unidades">{{ old('observacao') }}</textarea>
                @error('observacao', 'status')
                <span class="erro-campo">{{ $message }}</span>
                @enderror
            </div>
            <div class="acoes-form-solicitacao">
                <button type="submit" formaction="{{ route('amostras.reprovar', $amostra) }}" class="botao-card vermelho"
                    data-confirmar="Registrar que o cliente reprovou a amostra {{ $amostra->codigo }}? Depois disso ela não poderá mais ser alterada."
                    data-confirmar-titulo="Amostra reprovada" data-confirmar-botao="Registrar reprovação" data-confirmar-tipo="perigo">
                    <i class="fa-solid fa-thumbs-down" aria-hidden="true"></i> Reprovada pelo cliente
                </button>
                <button type="submit" class="botao-card verde"
                    data-confirmar="Registrar que o cliente aprovou a amostra {{ $amostra->codigo }}? Depois disso ela não poderá mais ser alterada."
                    data-confirmar-titulo="Amostra aprovada" data-confirmar-botao="Registrar aprovação" data-confirmar-tipo="sucesso">
                    <i class="fa-solid fa-thumbs-up" aria-hidden="true"></i> Aprovada pelo cliente
                </button>
            </div>
        </form>
        @endif

        @if ($podeCancelar)
        <details class="cancelamento" @if ($errors->cancelamento->any()) open @endif>
            <summary><i class="fa-solid fa-ban" aria-hidden="true"></i> Cancelar amostra</summary>
            <form method="POST" action="{{ route('amostras.cancelar', $amostra) }}" class="form-solicitacao"
                data-confirmar="Cancelar a amostra {{ $amostra->codigo }}? Ela não poderá mais ser alterada."
                data-confirmar-titulo="Cancelar amostra" data-confirmar-botao="Cancelar amostra" data-confirmar-tipo="perigo">
                @csrf
                <div class="label-input">
                    <label for="motivo">Motivo do cancelamento <span class="obrigatorio" aria-hidden="true">*</span></label>
                    <textarea id="motivo" name="motivo" required minlength="5" maxlength="1000">{{ old('motivo') }}</textarea>
                    @error('motivo', 'cancelamento')
                    <span class="erro-campo">{{ $message }}</span>
                    @enderror
                </div>
                <div class="acoes-form-solicitacao">
                    <button type="submit" class="botao-card cinza"><i class="fa-solid fa-ban" aria-hidden="true"></i> Confirmar cancelamento</button>
                </div>
            </form>
        </details>
        @endif
    </section>
    @elseif ($amostra->finalizada())
    <div class="alerta alerta-{{ $amostra->status === 'aprovada' ? 'sucesso' : ($amostra->status === 'reprovada' ? 'erro' : 'info') }} alerta-card" role="note">
        <i class="fa-solid {{ $amostra->status === 'aprovada' ? 'fa-circle-check' : ($amostra->status === 'reprovada' ? 'fa-circle-xmark' : 'fa-ban') }}" aria-hidden="true"></i>
        <span>
            Amostra <strong>{{ mb_strtolower($amostra->status_nome) }}</strong>
            @if ($finalizacao)
                em {{ $finalizacao->data_hora->format('d/m/Y') }} por {{ $finalizacao->user?->name ?? 'Sistema' }}.
                @if ($finalizacao->observacao) <br>{{ $finalizacao->observacao }} @endif
            @endif
            Ela está finalizada e não pode mais ser alterada.
        </span>
    </div>
    @endif

    {{-- ===================== SOLICITAÇÕES DE PRAZO ===================== --}}
    @permite('solicitacoes_prazo')
    <section class="card-secao" id="solicitacoes" aria-labelledby="titulo-solicitacoes">
        <h2 id="titulo-solicitacoes"><i class="fa-solid fa-calendar-plus" aria-hidden="true"></i> Solicitações de prazo</h2>

        @if ($amostra->solicitacoesPrazo->isEmpty())
        <p class="texto-vazio">Nenhuma solicitação de prazo para esta amostra.</p>
        @else
        <div class="rolagem-tabela">
        <table>
            <caption class="sr-only">Solicitações de prazo da amostra {{ $amostra->codigo }}</caption>
            <thead>
                <tr>
                    <th scope="col">Pedida em</th>
                    <th scope="col">Solicitante</th>
                    <th scope="col">Prazo atual</th>
                    <th scope="col">Novo prazo</th>
                    <th scope="col">Justificativa</th>
                    <th scope="col">Situação</th>
                    <th scope="col">Decisão</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($amostra->solicitacoesPrazo as $solicitacao)
                <tr>
                    <td class="centro nao-quebra">{{ $solicitacao->data_solicitacao->format('d/m/Y H:i') }}</td>
                    <td>{{ $solicitacao->solicitante->name }}</td>
                    <td class="centro">{{ $solicitacao->prazo_atual->format('d/m/Y') }}</td>
                    <td class="centro">{{ $solicitacao->novo_prazo->format('d/m/Y') }}</td>
                    <td>{{ $solicitacao->justificativa }}</td>
                    <td class="centro"><span class="status status-{{ $solicitacao->situacao }}">{{ $solicitacao->situacao_nome }}</span></td>
                    <td>
                        @if ($solicitacao->aprovador)
                        {{ $solicitacao->aprovador->name }}
                        <span class="texto-secundario">{{ $solicitacao->data_aprovacao?->format('d/m/Y H:i') }}</span>
                        @if ($solicitacao->motivo_aprovacao_reprovacao)
                        <span class="texto-secundario">“{{ $solicitacao->motivo_aprovacao_reprovacao }}”</span>
                        @endif
                        @else
                        <span class="texto-secundario">Aguardando</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        </div>
        @endif

        @if ($pendente)
            @if ($motivoNaoDecidir === null)
            {{-- Responder a solicitação pendente --}}
            <form action="{{ route('solicitacoes-prazo.update', $pendente) }}" method="POST" class="form-solicitacao">
                @csrf
                @method('PUT')
                <h3>Responder solicitação de {{ $pendente->solicitante->name }}</h3>
                <p class="texto-ajuda">Novo prazo pedido: <strong>{{ $pendente->novo_prazo->format('d/m/Y') }}</strong> (atual: {{ $pendente->prazo_atual->format('d/m/Y') }}).</p>
                <div class="label-input">
                    <label for="motivo_aprovacao_reprovacao">Motivo <span class="texto-secundario-inline">(obrigatório para reprovar)</span></label>
                    <textarea id="motivo_aprovacao_reprovacao" name="motivo_aprovacao_reprovacao" maxlength="1000">{{ old('motivo_aprovacao_reprovacao') }}</textarea>
                    @error('motivo_aprovacao_reprovacao', 'decisao')
                    <span class="erro-campo">{{ $message }}</span>
                    @enderror
                </div>
                <div class="acoes-form-solicitacao">
                    <button type="submit" name="situacao" value="reprovada" class="botao-card vermelho"><i class="fa-solid fa-xmark" aria-hidden="true"></i> Reprovar</button>
                    <button type="submit" name="situacao" value="aprovada" class="botao-card verde"><i class="fa-solid fa-check" aria-hidden="true"></i> Aprovar novo prazo</button>
                </div>
            </form>
            @else
            <p class="aviso-solicitacao">
                <i class="fa-solid fa-hourglass-half" aria-hidden="true"></i>
                Solicitação pendente de {{ $pendente->solicitante->name }} (novo prazo {{ $pendente->novo_prazo->format('d/m/Y') }}).
                @if (! auth()->user()->isVendedor()) {{ $motivoNaoDecidir }} @else Aguardando o gestor ou um administrador. @endif
            </p>
            @endif

            @if ($pendente->podeSerCanceladaPor(auth()->user()))
            <form action="{{ route('solicitacoes-prazo.destroy', $pendente) }}" method="POST" class="acoes-card"
                data-confirmar="Cancelar a solicitação de prazo? O prazo atual da amostra continua valendo."
                data-confirmar-titulo="Cancelar solicitação" data-confirmar-botao="Cancelar solicitação" data-confirmar-tipo="perigo">
                @csrf
                @method('DELETE')
                <button type="submit" class="botao-card cinza"><i class="fa-solid fa-ban" aria-hidden="true"></i> Desistir da solicitação</button>
            </form>
            @endif
        @elseif ($amostra->emAberto())
            @permite('solicitacoes_prazo', 'criar')
            {{-- Nova solicitação --}}
            <form action="{{ route('solicitacoes-prazo.store', $amostra) }}" method="POST" class="form-solicitacao">
                @csrf
                <h3>Solicitar prorrogação do prazo</h3>
                <p class="texto-ajuda">O prazo só muda depois da aprovação do gestor do estabelecimento ou de um administrador.</p>
                <div class="campos-solicitacao">
                    <div class="label-input">
                        <label for="novo_prazo">Novo prazo <span class="obrigatorio" aria-hidden="true">*</span></label>
                        <input type="date" id="novo_prazo" name="novo_prazo" required min="{{ $amostra->prazo_avaliacao->copy()->max(today())->addDay()->format('Y-m-d') }}" value="{{ old('novo_prazo') }}">
                        @error('novo_prazo', 'solicitacao')
                        <span class="erro-campo">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="label-input">
                        <label for="justificativa">Justificativa <span class="obrigatorio" aria-hidden="true">*</span></label>
                        <textarea id="justificativa" name="justificativa" required minlength="10" maxlength="1000" placeholder="Explique por que o prazo precisa ser estendido">{{ old('justificativa') }}</textarea>
                        @error('justificativa', 'solicitacao')
                        <span class="erro-campo">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="acoes-form-solicitacao">
                    <button type="submit" class="botao-card"><i class="fa-solid fa-paper-plane" aria-hidden="true"></i> Enviar solicitação</button>
                </div>
            </form>
            @endpermite
        @endif
    </section>
    @endpermite

</div>

{{-- Exclusão permanente: só amostras canceladas (RN014) --}}
@if ($amostra->status === 'cancelada')
@permite('amostras', 'excluir')
@include('partials.acoes-registro', [
    'rotaExcluir' => route('amostras.destroy', $amostra),
    'descricao' => 'a amostra cancelada '.$amostra->codigo,
])
@endpermite
@endif
@endsection
