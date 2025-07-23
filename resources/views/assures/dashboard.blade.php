<h1>Bonjour {{ Auth::user()->nom_complet }}</h1>
<form method="POST" action="{{ route('logout.assure') }}" style="margin-top: 1rem;">
    @csrf
    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Se déconnecter</button>
</form>