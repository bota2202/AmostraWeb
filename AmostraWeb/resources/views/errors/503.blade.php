@extends('errors.layout')

@section('codigo', '503')
@section('titulo', 'Sistema em manutenção')
@section('mensagem')
O {{ config('amostraweb.name') }} está em manutenção. Tente novamente em alguns minutos.
@endsection
