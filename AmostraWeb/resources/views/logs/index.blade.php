@extends('layouts.app')

@section('title','Logs - '.config('amostraweb.name'))

@section('texto','Logs')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/layout/table.css') }}">
@endpush

@section('content')
<div class="toolbar">
    <h1>Logs</h1>
</div>

<table>
    <thead>
        <tr>
            <th>Data / hora</th>
            <th>Usuário</th>
            <th>Módulo</th>
            <th>Registro</th>
            <th>Ação</th>
            <th>Campo alterado</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($logs as $log)
        <tr>
            <td class="centro">{{ $log->data_hora->format('d/m/Y H:i:s') }}</td>
            <td class="centro">{{ $log->user?->codigo_usuario ?? '-' }}</td>
            <td class="centro">{{ config('amostraweb.modulos')[$log->modulo] ?? $log->modulo }}</td>
            <td class="centro">{{ $log->tipo_registro }} #{{ $log->registro_id }}</td>
            <td class="centro capitalizado">{{ $log->acao }}</td>
            <td class="centro">{{ $log->campo_alterado ?? '-' }}</td>
            <td class="acoes">
                <a href="{{ route('logs.show', $log) }}" title="Ver detalhes"><i class="fa-solid fa-eye"></i></a>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="7" class="vazio">Nenhum log registrado</td>
        </tr>
        @endforelse
    </tbody>
</table>
{{ $logs->links() }}
@endsection
