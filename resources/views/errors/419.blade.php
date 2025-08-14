@extends('errors.layout')

@section('title', 'Session expirée')
@section('badge', '419')
@section('code', '419')
@section('heading', 'Votre session a expiré')
@section('message', "Veuillez actualiser la page ou vous reconnecter pour continuer.")

@section('illustration')
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200" class="h-24 w-24 text-amber-600">
    <g fill="none" stroke="currentColor" stroke-width="3">
        <circle cx="100" cy="100" r="60" opacity=".25"/>
        <path d="M100 70v35l20 10" stroke-linecap="round" stroke-linejoin="round"/>
        <path d="M60 140c25-10 55-10 80 0" opacity=".5"/>
    </g>
    <text x="100" y="170" text-anchor="middle" font-size="18" class="fill-current" opacity=".7">419</text>
  </svg>
@endsection

@section('actions')
<form method="GET" action="{{ url()->current() }}">
    <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-amber-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm ring-1 ring-amber-600/30 hover:bg-amber-500">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 4v5h.582M20 20v-5h-.581M5.5 9A7.5 7.5 0 0119 12M4.5 12A7.5 7.5 0 0118 15"/></svg>
        Actualiser
    </button>
</form>
@endsection


