@extends('layouts.app')

@section('title','Editar '.$user->name.' - '.config('amostraweb.name'))

@section('texto','Usuários')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/layout/detalhes.css') }}">
@endpush

@section('content')
<div class="toolbar">
    <h1>
        <a href="{{ route('usuarios.index') }}">Usuários</a> › <a href="{{ route('usuarios.show', $user) }}">{{ $user->name }}</a> › Editar
    </h1>
    <div class="acoes-toolbar">
        <a class="botao-toolbar botao-secundario" href="{{ route('usuarios.show', $user) }}">
            <i class="fa-solid fa-xmark" aria-hidden="true"></i> Cancelar
        </a>
        <button type="submit" form="form-usuario" class="botao-toolbar" id="botao-salvar">
            <i class="fa-solid fa-check" aria-hidden="true"></i> Salvar alterações
        </button>
    </div>
</div>


<div>
    <div class="dados">
        <form id="form-usuario" action="{{ route('usuarios.update', $user) }}" method="POST">
            @csrf
            @method('PUT')

            <section class="card-secao" aria-labelledby="titulo-identificacao">
                <h2 id="titulo-identificacao"><i class="fa-solid fa-id-card" aria-hidden="true"></i> Identificação</h2>
                <div class="campos-secao">
                    <div class="label-input">
                        <label for="name">Nome</label>
                        <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" maxlength="255" required autocomplete="off">
                        @error('name')
                        <span class="erro-campo">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="label-input">
                        <label for="codigo_usuario">Código de usuário</label>
                        <input type="text" id="codigo_usuario" readonly value="{{ $user->codigo_usuario }}">
                    </div>
                    <span class="badge-status {{ $user->status ? 'ativo' : 'inativo' }}">
                        <i class="fa-solid fa-circle" aria-hidden="true"></i>
                        {{ $user->status ? 'Ativo' : 'Inativo' }}
                    </span>
                    <div class="label-input">
                        <label for="email">E-mail</label>
                        <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" maxlength="255" required autocomplete="off">
                        @error('email')
                        <span class="erro-campo">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="label-input">
                        <label for="cargo">Cargo</label>
                        <select id="cargo" name="cargo" required aria-describedby="dica-cargo">
                            @foreach (\App\Models\User::CARGOS as $valor => $nome)
                            <option value="{{ $valor }}" @selected(old('cargo', $user->cargo) === $valor)>{{ $nome }}</option>
                            @endforeach
                        </select>
                        <p class="dica-campo" id="dica-cargo">Define o que o usuário enxerga: vendedor (próprios pedidos), gestor (do estabelecimento) ou administrador (tudo).</p>
                        @error('cargo')
                        <span class="erro-campo">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="label-input">
                        <label for="estabelecimento_id">Estabelecimento</label>
                        <select id="estabelecimento_id" name="estabelecimento_id" required>
                            @foreach ($estabelecimentos as $estab)
                            <option value="{{ $estab->id }}" @selected(old('estabelecimento_id', $user->estabelecimento_id) == $estab->id)>{{ $estab->razao_social }}</option>
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

            <section class="card-secao" aria-labelledby="titulo-senha">
                <h2 id="titulo-senha"><i class="fa-solid fa-key" aria-hidden="true"></i> Definir nova senha</h2>
                <div class="campos-secao">
                    <div class="label-input">
                        <label for="password">Nova senha</label>
                        <input type="password" id="password" name="password" required minlength="6" autocomplete="new-password" aria-describedby="dica-senha">
                        <p class="dica-campo" id="dica-senha">Mínimo de 6 caracteres. Informe a nova senha ao usuário.</p>
                        @error('password')
                        <span class="erro-campo">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="label-input">
                        <label for="password_confirmation">Confirme a nova senha</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" required autocomplete="new-password">
                    </div>
                </div>
                <div class="acoes-card">
                    <button type="submit" class="botao-card"><i class="fa-solid fa-key" aria-hidden="true"></i> Definir senha</button>
                </div>
            </section>
        </form>
    </div>

    @unless ($user->is(auth()->user()))
    @include('partials.acoes-registro', [
        'rotaStatus' => route('usuarios.status', $user),
        'ativo' => $user->status,
        'rotaExcluir' => auth()->user()->temPermissao('usuarios', 'excluir') ? route('usuarios.destroy', $user) : null,
        'descricao' => 'o usuário '.$user->codigo_usuario,
    ])
    @endunless
</div>

@endsection
