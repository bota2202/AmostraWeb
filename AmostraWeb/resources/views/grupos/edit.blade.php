@extends('layouts.app')

@section('title','Editar grupo '.$grupo->nome.' - '.config('amostraweb.name'))

@section('texto','Permissões')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/layout/detalhes.css') }}">
<link rel="stylesheet" href="{{ asset('css/layout/modulos.css') }}">
@endpush

@section('content')
<div class="toolbar">
    <h1>
        <a href="{{ route('grupos.index') }}">Grupos</a> › <a href="{{ route('grupos.show', $grupo) }}">{{ $grupo->nome }}</a> › Editar
    </h1>
    <div class="acoes-toolbar">
        <a class="botao-toolbar botao-secundario" href="{{ route('grupos.show', $grupo) }}">
            <i class="fa-solid fa-xmark" aria-hidden="true"></i> Cancelar
        </a>
        {{-- salva as três abas de uma vez --}}
        <button type="submit" form="form-grupo" class="botao-toolbar">
            <i class="fa-solid fa-check" aria-hidden="true"></i> Salvar grupo
        </button>
    </div>
</div>

<div class="abas" role="tablist" aria-label="Seções do grupo" data-abas>
    <button type="button" role="tab" class="aba" id="aba-dados" aria-controls="painel-dados" data-aba="dados">
        <i class="fa-solid fa-id-card" aria-hidden="true"></i> Dados
    </button>
    <button type="button" role="tab" class="aba" id="aba-membros" aria-controls="painel-membros" data-aba="membros">
        <i class="fa-solid fa-user" aria-hidden="true"></i> Membros
        <span class="contador-aba"><span data-contador-membros>{{ count($membros) }}</span><span class="sr-only"> membros</span></span>
    </button>
    <button type="button" role="tab" class="aba" id="aba-permissoes" aria-controls="painel-permissoes" data-aba="permissoes">
        <i class="fa-solid fa-unlock" aria-hidden="true"></i> Permissões
        <span class="contador-aba"><span data-contador-total>{{ count($selecionadas) }}</span><span class="sr-only"> liberadas</span></span>
    </button>
</div>

<form id="form-grupo" action="{{ route('grupos.update', $grupo) }}" method="POST">
    @csrf
    @method('PUT')

    {{-- ===================== ABA DADOS ===================== --}}
    <div role="tabpanel" id="painel-dados" aria-labelledby="aba-dados">
        <div class="dados">
            <section class="card-secao" aria-labelledby="titulo-grupo">
                <h2 id="titulo-grupo"><i class="fa-solid fa-users" aria-hidden="true"></i> Grupo</h2>
                <div class="campos-secao">
                    <div class="label-input">
                        <label for="nome">Nome</label>
                        <input type="text" id="nome" name="nome" maxlength="100" required value="{{ old('nome', $grupo->nome) }}">
                        @error('nome')
                        <span class="erro-campo">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="label-input campo-duplo">
                        <label for="descricao">Descrição</label>
                        <input type="text" id="descricao" name="descricao" maxlength="255" value="{{ old('descricao', $grupo->descricao) }}">
                        @error('descricao')
                        <span class="erro-campo">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </section>
        </div>
    </div>

    {{-- ===================== ABA MEMBROS ===================== --}}
    <div role="tabpanel" id="painel-membros" aria-labelledby="aba-membros" hidden>
        <div class="busca-membros">
            <label for="busca-membros" class="sr-only">Buscar usuário</label>
            <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
            <input type="search" id="busca-membros" placeholder="Buscar por nome ou código de usuário" data-busca-membros>
        </div>
        <fieldset class="lista-membros">
            <legend class="sr-only">Membros do grupo</legend>
            @foreach ($usuarios as $usuario)
            <label class="membro" data-texto="{{ mb_strtolower($usuario->name.' '.$usuario->codigo_usuario) }}">
                <input type="checkbox" name="usuarios[]" value="{{ $usuario->id }}" data-membro
                    @checked(in_array($usuario->id, old('usuarios', $membros)))>
                <span class="avatar-mini" aria-hidden="true">{{ $usuario->iniciais }}</span>
                <span class="membro-info">
                    <strong>{{ $usuario->name }}</strong>
                    <span>{{ $usuario->codigo_usuario }} · {{ $usuario->cargo_nome }}{{ $usuario->status ? '' : ' · inativo' }}</span>
                </span>
            </label>
            @endforeach
        </fieldset>
        <p class="texto-vazio" data-sem-resultado hidden>Nenhum usuário encontrado.</p>
    </div>

    {{-- ===================== ABA PERMISSÕES ===================== --}}
    <div role="tabpanel" id="painel-permissoes" aria-labelledby="aba-permissoes" hidden>
        @include('partials.permissoes-modulos', [
            'permissoesPorModulo' => $permissoesPorModulo,
            'selecionadas' => old('permissoes', $selecionadas),
        ])
    </div>
</form>

@include('partials.acoes-registro', [
    'rotaExcluir' => auth()->user()->temPermissao('permissoes', 'excluir') ? route('grupos.destroy', $grupo) : null,
    'descricao' => 'o grupo '.$grupo->nome.' (os membros perdem as permissões que vinham dele)',
])
@endsection

@push('scripts')
<script>
    // Busca e contador de membros
    const busca = document.querySelector('[data-busca-membros]');
    const semResultado = document.querySelector('[data-sem-resultado]');
    busca.addEventListener('input', () => {
        const termo = busca.value.trim().toLowerCase();
        let visiveis = 0;
        document.querySelectorAll('.membro').forEach(m => {
            m.hidden = !m.dataset.texto.includes(termo);
            if (!m.hidden) visiveis++;
        });
        semResultado.hidden = visiveis > 0;
    });

    const contadorMembros = document.querySelector('[data-contador-membros]');
    document.querySelectorAll('[data-membro]').forEach(m => m.addEventListener('change', () => {
        contadorMembros.textContent = document.querySelectorAll('[data-membro]:checked').length;
    }));
</script>
@endpush
