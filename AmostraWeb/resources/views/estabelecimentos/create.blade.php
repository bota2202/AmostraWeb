@extends('layouts.app')

@section('title',"Estabelecimentos - ".config('amostraweb.name'))

@section('texto','Estabelecimentos')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/estabelecimento/create.css') }}">
@endpush

@section('content')
<div class="toolbar">
    <h1><a href="{{ route('estabelecimentos.index') }}">Estabelecimentos</a>>Novo</h1>
</div>

@endsection