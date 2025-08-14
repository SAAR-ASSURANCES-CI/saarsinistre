@extends('errors.layout')

@section('title', 'Page introuvable')
@section('badge', '404')
@section('code', '404')
@section('heading', 'Oups, page introuvable')
@section('message', "La page que vous cherchez a peut-être été supprimée ou n'a jamais existé.")

@section('illustration')
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200" class="h-24 w-24 text-indigo-600">
    <g fill="none" stroke="currentColor" stroke-width="3">
        <circle cx="100" cy="100" r="70" opacity=".25"/>
        <path d="M60 120l20-40 20 40 20-40 20 40" stroke-linecap="round" stroke-linejoin="round"/>
        <circle cx="85" cy="85" r="3" fill="currentColor"/>
        <circle cx="115" cy="85" r="3" fill="currentColor"/>
        <path d="M80 110c8 8 32 8 40 0" stroke-linecap="round"/>
    </g>
    <text x="100" y="170" text-anchor="middle" font-size="18" class="fill-current" opacity=".7">404</text>
  </svg>
@endsection


