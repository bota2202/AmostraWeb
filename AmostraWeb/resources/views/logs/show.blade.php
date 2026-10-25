@extends('layouts.app')

@section('title','Logs - '.config('amostraweb.name'))

@section('texto','Logs')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/layout/detalhes.css') }}">
@endpush

@section('content')
<div class="toolbar">
    <h1><a href="{{ route('logs.index') }}">Logs </a>> #{{ $log->id }}</h1>
</div>

<div class="dados">
    <section class="card-secao">
        <h3><i class="fa-solid fa-clock-rotate-left"></i> Registro</h3>
        <div class="campos-secao">
            <div class="label-input">
                <label for="data_hora">Data / hora</label>
                <input type="text" id="data_hora" disabled value="{{ $log->data_hora->format('d/m/Y H:i:s') }}">
            </div>
            <div class="label-input">
                <label for="usuario">Usuário</label>
                <input type="text" id="usuario" disabled value="{{ $log->user ? $log->user->codigo_usuario.' - '.$log->user->name : '-' }}">
            </div>
            <div class="label-input">
                <label for="acao">Ação</label>
                <input type="text" id="acao" disabled value="{{ $log->acao }}">
            </div>
            <div class="label-input">
                <label for="modulo">Módulo</label>
                <input type="text" id="modulo" disabled value="{{ config('amostraweb.modulos')[$log->modulo] ?? $log->modulo }}">
            </div>
            <div class="label-input">
                <label for="registro">Registro</label>
                <input type="text" id="registro" disabled value="{{ $log->tipo_registro }} #{{ $log->registro_id }}">
            </div>
            <div class="label-input">
                <label for="campo_alterado">Campo alterado</label>
                <input type="text" id="campo_alterado" disabled value="{{ $log->campo_alterado ?? '-' }}">
            </div>
        </div>
    </section>

    <section class="card-secao">
        <h3><i class="fa-solid fa-code-compare"></i> Alteração</h3>
        <div class="campos-secao">
            <div class="label-input campo-duplo">
                <label for="valor_anterior">Valor anterior</label>
                <textarea id="valor_anterior" disabled>{{ $log->valor_anterior ?? '-' }}</textarea>
            </div>
            <div class="label-input">
                <label for="valor_novo">Valor novo</label>
                <textarea id="valor_novo" disabled>{{ $log->valor_novo ?? '-' }}</textarea>
            </div>
            <div class="label-input campo-full">
                <label for="observacao">Observação</label>
                <textarea id="observacao" disabled>{{ $log->observacao ?? '-' }}</textarea>
            </div>
        </div>
    </section>
</div>
@endsection
