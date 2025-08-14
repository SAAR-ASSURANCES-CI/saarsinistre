@extends('errors.layout')

@section('title', 'Erreur serveur')
@section('badge', '500')
@section('code', '500')
@section('heading', 'Un problème est survenu côté serveur')
@section('message', "Nous avons rencontré une erreur inattendue. Nos équipes ont été notifiées si nécessaire.")

@section('illustration')
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200" class="h-24 w-24 text-rose-600">
    <g fill="none" stroke="currentColor" stroke-width="3">
        <circle cx="70" cy="100" r="25" opacity=".25"/>
        <circle cx="130" cy="100" r="25" opacity=".25"/>
        <path d="M95 100h10" stroke-linecap="round"/>
        <path d="M60 130c25-10 55-10 80 0" opacity=".5"/>
        <path d="M80 92l-6-6M86 86l-6 6M120 92l-6-6M126 86l-6 6" stroke-linecap="round"/>
    </g>
    <text x="100" y="170" text-anchor="middle" font-size="18" class="fill-current" opacity=".7">500</text>
  </svg>
@endsection


