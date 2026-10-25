@extends('layouts.app')

@section('title','Editar Usuário - '.config('amostraweb.name'))

@section('texto','Usuários')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/layout/detalhes.css') }}">
@endpush

@section('content')
<div class="toolbar">
    <h1>
        <a href="{{ route('usuarios.index') }}">Usuários</a> >
        <a href="{{ route('usuarios.show',$user) }}">{{ $user->codigo_usuario }}</a> >
        Editar
    </h1>
    <div class="acoes-toolbar">
        <a class="botao-toolbar botao-secundario" href="{{ route('usuarios.show',$user) }}">
            <i class="fa-solid fa-xmark"></i> Cancelar
        </a>
        {{-- o "form" deste botão muda conforme a aba aberta --}}
        <button type="submit" form="form-usuario" class="botao-toolbar" id="botao-salvar">
            <i class="fa-solid fa-check"></i> Salvar Alterações
        </button>
    </div>
</div>

<nav class="abas">
    <button type="button" class="aba ativa" data-aba="dados" data-form="form-usuario">
        <i class="fa-solid fa-id-card"></i> Dados
    </button>
    <button type="button" class="aba" data-aba="permissoes" data-form="form-permissoes">
        <i class="fa-solid fa-unlock"></i> Permissões
        <span class="contador-aba" data-contador-total>{{ count($selecionadas) }}</span>
    </button>
</nav>

{{-- ===================== ABA DADOS ===================== --}}
<div class="painel-aba" id="aba-dados">
<div class="dados">
    <form id="form-usuario" action="{{ route('usuarios.update', $user) }}" method="POST">
        @csrf
        @method('PUT')

        <section class="card-secao">
            <h3><i class="fa-solid fa-id-card"></i> Identificação</h3>
            <div class="campos-secao">
                <div class="label-input">
                    <label for="name">Nome</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" maxlength="255" required>
                    @error('name')
                    <span class="erro-campo">{{ $message }}</span>
                    @enderror
                </div>
                <div class="label-input">
                    <label for="codigo_usuario">Código de usuário</label>
                    <input type="text" id="codigo_usuario" disabled value="{{ $user->codigo_usuario }}">
                </div>
                <span class="badge-status {{ $user->status ? 'ativo' : 'inativo' }}">
                    <i class="fa-solid fa-circle"></i>
                    {{ $user->status ? 'Ativo' : 'Inativo' }}
                </span>
                <div class="label-input">
                    <label for="email">E-mail</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" maxlength="255" required>
                    @error('email')
                    <span class="erro-campo">{{ $message }}</span>
                    @enderror
                </div>
                <div class="label-input">
                    <label for="cargo">Cargo</label>
                    <select id="cargo" name="cargo" required>
                        @foreach (\App\Models\User::CARGOS as $valor => $nome)
                        <option value="{{ $valor }}" @selected(old('cargo', $user->cargo) === $valor)>{{ $nome }}</option>
                        @endforeach
                    </select>
                    @error('cargo')
                    <span class="erro-campo">{{ $message }}</span>
                    @enderror
                </div>
                <div class="label-input">
                    <label for="estabelecimento_id">Estabelecimento</label>
                    <select id="estabelecimento_id" name="estabelecimento_id" required>
                        @foreach ($estabelecimentos as $estab)
                        <option value="{{ $estab->id }}" @selected(old('estabelecimento_id', $user->estabelecimento_id) == $estab->id)>#{{ $estab->id }} - {{ $estab->razao_social }}</option>
                        @endforeach
                    </select>
                    @error('estabelecimento_id')
                    <span class="erro-campo">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </section>
    </form>

    <form action="{{ route('usuarios.senha', $user) }}" method="POST">
        @csrf
        @method('PATCH')

        <section class="card-secao">
            <h3><i class="fa-solid fa-key"></i> Alterar senha</h3>
            <div class="campos-secao">
                <div class="label-input">
                    <label for="password">Nova senha</label>
                    <input type="password" id="password" name="password" placeholder="Mínimo de 6 caracteres" required>
                    @error('password')
                    <span class="erro-campo">{{ $message }}</span>
                    @enderror
                </div>
                <div class="label-input">
                    <label for="password_confirmation">Confirme a nova senha</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required>
                </div>
            </div>
            <div class="acoes-card">
                <button type="submit" class="botao-card"><i class="fa-solid fa-key"></i> Alterar senha</button>
            </div>
        </section>
    </form>
</div>

@unless ($user->is(auth()->user()))
@include('partials.acoes-registro', [
    'rotaStatus' => route('usuarios.status', $user),
    'ativo' => $user->status,
    'rotaExcluir' => route('usuarios.destroy', $user),
    'descricao' => 'o usuário '.$user->codigo_usuario,
])
@endunless
</div>

{{-- ===================== ABA PERMISSÕES ===================== --}}
<div class="painel-aba" id="aba-permissoes" hidden>
    <form id="form-permissoes" action="{{ route('usuarios.permissoes.update', $user) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- grupos do usuário: só informam quais permissões vêm travadas ("via grupo") --}}
        @if ($user->isAdmin())
        {{-- administrador: todas as permissões liberadas e travadas --}}
        <span hidden data-grupo data-nome="Administrador" data-permissoes="{{ $permissoesPorModulo->flatten()->pluck('id')->toJson() }}"></span>
        @else
        @foreach ($grupos as $grupo)
        <span hidden data-grupo data-nome="{{ $grupo->nome }}" data-permissoes="{{ $grupo->permissoes->pluck('id')->toJson() }}"></span>
        @endforeach
        @endif

        <h3 class="titulo-bloco">Permissões</h3>
        @if ($user->isAdmin())
        <p class="subtitulo-bloco">Administradores têm acesso a tudo: todas as permissões ficam liberadas e não podem ser desmarcadas.</p>
        @else
        <p class="subtitulo-bloco">As marcadas com "via grupo" vêm dos grupos e só mudam editando o grupo ou tirando o usuário dele.</p>
        @endif
        @include('partials.permissoes-modulos', [
            'permissoesPorModulo' => $permissoesPorModulo,
            'selecionadas' => old('permissoes', $selecionadas),
        ])
    </form>
</div>
@endsection

@push('scripts')
<script>
    // ---------- Abas ----------
    const abas = document.querySelectorAll('.aba');
    const botaoSalvar = document.getElementById('botao-salvar');

    function abrirAba(nome) {
        abas.forEach(aba => {
            const ativa = aba.dataset.aba === nome;
            aba.classList.toggle('ativa', ativa);
            document.getElementById('aba-' + aba.dataset.aba).hidden = !ativa;
            if (ativa) botaoSalvar.setAttribute('form', aba.dataset.form);
        });
        history.replaceState(null, '', nome === 'dados' ? location.pathname : '#' + nome);
    }

    abas.forEach(aba => aba.addEventListener('click', () => abrirAba(aba.dataset.aba)));

    // abre a aba Permissões quando vier de um link /editar#permissoes ou após salvar
    if (location.hash === '#permissoes' || {{ $errors->has('permissoes*') ? 'true' : 'false' }}) {
        abrirAba('permissoes');
    }
</script>
@endpush
