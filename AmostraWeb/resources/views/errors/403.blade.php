@extends('errors.layout')

@section('codigo', '403')
@section('titulo', 'Acesso não permitido')
@section('mensagem')
{{ $exception->getMessage() ?: 'Você não tem permissão para acessar esta página.' }}<br>Se precisar desse acesso, fale com o administrador do sistema.
@endsection
