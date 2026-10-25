@extends('layouts.app')

@section('title','Editar Grupo - '.config('amostraweb.name'))

@section('texto','Permissões')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/layout/detalhes.css') }}">
@endpush

@section('content')
<div class="toolbar">
    <h1>
        <a href="{{ route('grupos.index') }}">Grupos</a> >
        <a href="{{ route('grupos.show',$grupo) }}">{{ $grupo->nome }}</a> >
        Editar
    </h1>
    <div class="acoes-toolbar">
        <a class="botao-toolbar botao-secundario" href="{{ route('grupos.show',$grupo) }}">
            <i class="fa-solid fa-xmark"></i> Cancelar
        </a>
        {{-- salva as três abas de uma vez --}}
        <button type="submit" form="form-grupo" class="botao-toolbar">
            <i class="fa-solid fa-check"></i> Salvar Grupo
        </button>
    </div>
</div>

<nav class="abas">
    <button type="button" class="aba ativa" data-aba="dados">
        <i class="fa-solid fa-id-card"></i> Dados
    </button>
    <button type="button" class="aba" data-aba="membros">
        <i class="fa-solid fa-user"></i> Membros
        <span class="contador-aba" data-contador-membros>{{ count($membros) }}</span>
    </button>
    <button type="button" class="aba" data-aba="permissoes">
        <i class="fa-solid fa-unlock"></i> Permissões
        <span class="contador-aba" data-contador-total>{{ count($selecionadas) }}</span>
    </button>
</nav>

<form id="form-grupo" action="{{ route('grupos.update', $grupo) }}" method="POST">
    @csrf
    @method('PUT')

    {{-- ===================== ABA DADOS ===================== --}}
    <div class="painel-aba" id="aba-dados">
        <div class="dados">
            <section class="card-secao">
                <h3><i class="fa-solid fa-users"></i> Grupo</h3>
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
    <div class="painel-aba" id="aba-membros" hidden>
        <div class="busca-membros">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="search" placeholder="Buscar por nome ou código de usuário" data-busca-membros>
        </div>
        <div class="lista-membros">
            @foreach ($usuarios as $usuario)
            <label class="membro" data-texto="{{ mb_strtolower($usuario->name.' '.$usuario->codigo_usuario) }}">
                <input type="checkbox" name="usuarios[]" value="{{ $usuario->id }}" data-membro
                    @checked(in_array($usuario->id, old('usuarios', $membros)))>
                <span class="avatar-mini">{{ $usuario->iniciais }}</span>
                <span class="membro-info">
                    <strong>{{ $usuario->name }}</strong>
                    <span>{{ $usuario->codigo_usuario }} · {{ \App\Models\User::CARGOS[$usuario->cargo] ?? $usuario->cargo }}{{ $usuario->status ? '' : ' · inativo' }}</span>
                </span>
            </label>
            @endforeach
        </div>
    </div>

    {{-- ===================== ABA PERMISSÕES ===================== --}}
    <div class="painel-aba" id="aba-permissoes" hidden>
        @include('partials.permissoes-modulos', [
            'permissoesPorModulo' => $permissoesPorModulo,
            'selecionadas' => old('permissoes', $selecionadas),
        ])
    </div>
</form>

@include('partials.acoes-registro', [
    'rotaExcluir' => route('grupos.destroy', $grupo),
    'descricao' => 'o grupo '.$grupo->nome.' (os membros perdem as permissões que vinham dele)',
])
@endsection

@push('scripts')
<script>
    // ---------- Abas ----------
    const abas = document.querySelectorAll('.aba');

    function abrirAba(nome) {
        abas.forEach(aba => {
            const ativa = aba.dataset.aba === nome;
            aba.classList.toggle('ativa', ativa);
            document.getElementById('aba-' + aba.dataset.aba).hidden = !ativa;
        });
        history.replaceState(null, '', nome === 'dados' ? location.pathname : '#' + nome);
    }

    abas.forEach(aba => aba.addEventListener('click', () => abrirAba(aba.dataset.aba)));

    const abaInicial = location.hash.replace('#', '');
    if (['membros', 'permissoes'].includes(abaInicial)) abrirAba(abaInicial);

    // ---------- Busca e contador de membros ----------
    const busca = document.querySelector('[data-busca-membros]');
    busca.addEventListener('input', () => {
        const termo = busca.value.trim().toLowerCase();
        document.querySelectorAll('.membro').forEach(m => m.hidden = !m.dataset.texto.includes(termo));
    });

    const contadorMembros = document.querySelector('[data-contador-membros]');
    document.querySelectorAll('[data-membro]').forEach(m => m.addEventListener('change', () => {
        contadorMembros.textContent = document.querySelectorAll('[data-membro]:checked').length;
    }));
</script>
@endpush
