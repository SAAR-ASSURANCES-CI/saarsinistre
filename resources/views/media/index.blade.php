<!DOCTYPE html>
<html lang="fr">
<head>
    @include('admin.partials.head', ['title' => 'Gestion des médias - SAAR Assurance'])
</head>
<body class="bg-gradient-to-br from-red-50 via-white to-green-50 min-h-screen">
    @include('admin.partials.header')
    @include('admin.partials.navbar')
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold mb-6">Gestion des médias des sinistres</h1>

        @if(session('success'))
            <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if($sinistres->isEmpty())
            <p>Aucun fichier n'a encore été ajouté.</p>
        @else
            <div class="space-y-8">
                @foreach($sinistres as $sinistre)
                    <div class="bg-white rounded shadow p-4">
                        <h2 class="text-lg font-semibold mb-2">Sinistre {{ $sinistre->numero_sinistre }}</h2>
                        <div class="mb-2">
                            <form action="{{ route('dashboard.media.store') }}" method="POST" enctype="multipart/form-data" class="flex items-center space-x-2">
                                @csrf
                                <input type="hidden" name="sinistre_id" value="{{ $sinistre->id }}">
                                <input type="file" name="file" required class="border rounded px-2 py-1">
                                <input type="text" name="libelle_document" placeholder="Libellé (optionnel)" class="border rounded px-2 py-1">
                                <button type="submit" class="bg-saar-blue text-white px-3 py-1 rounded">Ajouter</button>
                            </form>
                        </div>
                        @if($sinistre->documents->isEmpty())
                            <p class="text-gray-500">Aucun fichier pour ce sinistre.</p>
                        @else
                            <ul class="divide-y mt-2">
                                @foreach($sinistre->documents as $doc)
                                    <li class="flex items-center justify-between py-2">
                                        <div class="flex items-center space-x-2">
                                            <span>{!! $doc->icone_type !!}</span>
                                            <a href="{{ Storage::url($doc->chemin_fichier) }}" target="_blank" class="text-blue-600 hover:underline">{{ $doc->libelle_document ?? $doc->nom_fichier }}</a>
                                            <span class="text-xs text-gray-500">({{ $doc->taille_formatee }})</span>
                                        </div>
                                        <form action="{{ route('dashboard.media.destroy', $doc) }}" method="POST" onsubmit="return confirm('Supprimer ce fichier ?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:underline">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</body>
</html> 