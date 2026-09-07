@extends('layouts.app')

@section('title', 'Dashboard - ' . config('amostraweb.name'))

@section('texto')
    Olá {{ explode(' ', auth()->user()->name)[0] }}!
@endsection

@section('content')
    <section></section>
    <section></section>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard/dashboard.css') }}">
@endpush