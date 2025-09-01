<!DOCTYPE html>
<html lang="fr">

<head>
    @include('admin.partials.head', ['title' => 'Gestion des Feedback - SAAR Assurances'])
</head>

<body class="bg-gradient-to-br from-red-50 via-white to-green-50 min-h-screen">

    <!-- Header -->
    @include('admin.partials.header')

    <!-- Navbar Horizontale -->
    @include('admin.partials.navbar')

    <div class="container mx-auto px-4 py-8">
        <!-- En-tête -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Gestion des Feedback</h1>
            <p class="text-gray-600">Consultez les avis des assurés sur vos services</p>
        </div>

        <!-- Statistiques -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-blue-100 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Feedback</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['total'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-green-100 rounded-lg">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Moyenne Note</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['moyenne_note'] }}/5</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-yellow-100 rounded-lg">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Taux Réponse</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['taux_reponse'] }}%</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtres -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Note de service</label>
                    <select name="note_service" class="w-full border border-gray-300 rounded-md px-3 py-2">
                        <option value="">Toutes les notes</option>
                        @php
                            $noteLabels = [
                                1 => 'Très mécontent',
                                2 => 'Mécontent', 
                                3 => 'Neutre',
                                4 => 'Satisfait',
                                5 => 'Très satisfait'
                            ];
                        @endphp
                        @for($i = 1; $i <= 5; $i++)
                            <option value="{{ $i }}" {{ request('note_service') == $i ? 'selected' : '' }}>
                                {{ $i }} - {{ $noteLabels[$i] }}
                            </option>
                        @endfor
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Humeur</label>
                    <select name="humeur_emoticon" class="w-full border border-gray-300 rounded-md px-3 py-2">
                        <option value="">Toutes les humeurs</option>
                        <option value="😊" {{ request('humeur_emoticon') == '😊' ? 'selected' : '' }}>😊 Très satisfait</option>
                        <option value="🙂" {{ request('humeur_emoticon') == '🙂' ? 'selected' : '' }}>🙂 Satisfait</option>
                        <option value="😐" {{ request('humeur_emoticon') == '😐' ? 'selected' : '' }}>😐 Neutre</option>
                        <option value="😕" {{ request('humeur_emoticon') == '😕' ? 'selected' : '' }}>😕 Mécontent</option>
                        <option value="😠" {{ request('humeur_emoticon') == '😠' ? 'selected' : '' }}>😠 Très mécontent</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date début</label>
                    <input type="date" name="date_debut" value="{{ request('date_debut') }}" 
                           class="w-full border border-gray-300 rounded-md px-3 py-2">
                </div>

                <div class="flex items-end space-x-2">
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Date fin</label>
                        <input type="date" name="date_fin" value="{{ request('date_fin') }}" 
                               class="w-full border border-gray-300 rounded-md px-3 py-2">
                    </div>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        Filtrer
                    </button>
                </div>
            </form>
        </div>

        <!-- Actions -->
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-semibold text-gray-900">Liste des Feedback</h2>
            <a href="{{ route('gestionnaires.dashboard.feedback.export') }}" 
               class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Exporter CSV
            </a>
        </div>

        <!-- Tableau des feedbacks -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Assuré
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Sinistre
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Note
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Humeur
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Date
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($feedbacks as $feedback)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $feedback->assure->nom_complet ?? $feedback->assure->name }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ $feedback->assure->email }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $feedback->sinistre->numero_sinistre }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ $feedback->sinistre->statut_libelle }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($feedback->note_service)
                                        <div class="flex items-center">
                                            <span class="text-sm font-medium text-gray-900 mr-2">
                                                {{ $feedback->note_service }}/5
                                            </span>
                                            <div class="flex">
                                                @php
                                                    $starColor = 'text-gray-300';
                                                    if ($feedback->note_service >= 4) {
                                                        $starColor = 'text-green-400'; 
                                                    } elseif ($feedback->note_service == 3) {
                                                        $starColor = 'text-yellow-400';
                                                    } elseif ($feedback->note_service <= 2) {
                                                        $starColor = 'text-red-400';
                                                    }
                                                @endphp
                                                @for($i = 1; $i <= 5; $i++)
                                                    <svg class="w-4 h-4 {{ $i <= $feedback->note_service ? $starColor : 'text-gray-300' }}" 
                                                         fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                    </svg>
                                                @endfor
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-sm text-gray-500">Non noté</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($feedback->humeur_emoticon)
                                        <div class="flex items-center">
                                            <span class="text-2xl mr-2">{{ $feedback->humeur_emoticon }}</span>
                                            <span class="text-sm text-gray-600">{{ $feedback->humeur_libelle }}</span>
                                        </div>
                                    @else
                                        <span class="text-sm text-gray-500">Non renseigné</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    @if($feedback->date_feedback)
                                        {{ $feedback->date_feedback->format('d/m/Y H:i') }}
                                    @else
                                        <span class="text-xs text-gray-400">En attente</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('gestionnaires.dashboard.feedback.show', $feedback) }}" 
                                       class="text-blue-600 hover:text-blue-900">Voir détails</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                    Aucun feedback trouvé
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            @if($feedbacks->hasPages())
                <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                    {{ $feedbacks->links() }}
                </div>
            @endif
        </div>
    </div>

    <footer class="w-full py-3 bg-white/80 border-t border-gray-200 text-center text-xs text-gray-500 mt-8">
        © Saar Assurances Côte d'Ivoire. Tous droits réservés.
    </footer>

</body>
</html>
