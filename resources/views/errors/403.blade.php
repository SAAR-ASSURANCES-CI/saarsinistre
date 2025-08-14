@extends('errors.layout')

@section('title', 'Accès refusé')
@section('badge', '403')
@section('code', '403')
@section('heading', 'Accès refusé')
@section('message', "Vous n'avez pas les permissions nécessaires pour accéder à cette ressource.")

@section('illustration')
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200" class="h-24 w-24 text-fuchsia-600">
    <g fill="none" stroke="currentColor" stroke-width="3">
        <rect x="40" y="80" width="120" height="70" rx="8" opacity=".25"/>
        <path d="M80 80v-8a20 20 0 1140 0v8"/>
        <path d="M100 110v20" stroke-linecap="round"/>
        <circle cx="100" cy="106" r="4" fill="currentColor"/>
    </g>
    <text x="100" y="170" text-anchor="middle" font-size="18" class="fill-current" opacity=".7">403</text>
  </svg>
@endsection


