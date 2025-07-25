<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Assuré - SAAR Assurance</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-red-50 via-white to-green-50 min-h-screen">
    <!-- Sticky Header -->
    <header class="sticky top-0 z-40 bg-white/90 backdrop-blur border-b border-gray-200 shadow-sm flex items-center justify-between px-4 py-3 md:px-8">
        <div class="flex items-center space-x-3">
            <span class="text-red-700 font-bold text-lg md:text-2xl">Dashboard Assuré</span>
        </div>
        <div class="flex items-center space-x-2 md:space-x-4">
            <!-- Notification Bell -->
            <button aria-label="Notifications" class="relative p-2 rounded-full hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-red-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
                <!-- Badge (optionnel) -->
                <!-- <span class="absolute top-1 right-1 bg-red-600 text-white text-xs rounded-full px-1.5">2</span> -->
            </button>
            <!-- Logout Icon (mobile/tablet) -->
            <form method="POST" action="{{ route('logout.assure') }}" class="block md:hidden">
                @csrf
                <button type="submit" aria-label="Se déconnecter" class="p-2 rounded-full hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-red-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                </button>
            </form>
        </div>
    </header>
    <div class="container mx-auto px-4 py-8">
        <!-- User Info and Desktop Logout -->
        <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6 gap-4 md:gap-0">
            <div class="flex items-center space-x-4">
                @php
                    $heure = now()->format('H');
                    $salutation = ($heure >= 5 && $heure < 15) ? 'Bonjour,' : 'Bonsoir,';
                @endphp
                <h1 class="text-xl md:text-2xl font-bold text-red-700">{{ $salutation }} {{ Auth::user()->nom_complet }}</h1>
            </div>
            <!-- Desktop logout button -->
            <form method="POST" action="{{ route('logout.assure') }}" class="hidden md:block">
                @csrf
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Se déconnecter</button>
            </form>
        </div>

        <!-- Statistiques -->
        @php
            $stats = [
                'total' => $sinistres->total(),
                'en_attente' => $sinistres->where('statut', 'en_attente')->count(),
                'en_cours' => $sinistres->where('statut', 'en_cours')->count(),
                'traites' => $sinistres->whereIn('statut', ['regle', 'clos'])->count(),
                'expertise_requise' => $sinistres->where('statut', 'expertise_requise')->count(),
            ];
        @endphp
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 sm:gap-6 mb-8">
            <div class="bg-white rounded-2xl shadow p-6 flex items-center space-x-4 border border-gray-100">
                <div class="p-3 rounded-xl bg-blue-100">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Sinistres</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
                </div>
            </div>
            <div class="bg-white rounded-2xl shadow p-6 flex items-center space-x-4 border border-gray-100">
                <div class="p-3 rounded-xl bg-yellow-100">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">En Attente</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['en_attente'] }}</p>
                </div>
            </div>
            <div class="bg-white rounded-2xl shadow p-6 flex items-center space-x-4 border border-gray-100">
                <div class="p-3 rounded-xl bg-blue-100">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">En Cours</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['en_cours'] }}</p>
                </div>
            </div>
            <div class="bg-white rounded-2xl shadow p-6 flex items-center space-x-4 border border-gray-100">
                <div class="p-3 rounded-xl bg-green-100">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Traités</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['traites'] }}</p>
                </div>
            </div>
            <div class="bg-white rounded-2xl shadow p-6 flex items-center space-x-4 border border-gray-100">
                <div class="p-3 rounded-xl bg-red-100">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 12v4m-6 4h12a2 2 0 002-2V10a2 2 0 00-2-2h-3V6a2 2 0 00-2-2h-2a2 2 0 00-2 2v2H6a2 2 0 00-2 2v8a2 2 0 002 2z" /></svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Expertise Requise</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['expertise_requise'] }}</p>
                </div>
            </div>
        </div>

        <!-- Filtres et recherche -->
        <div class="bg-white rounded-2xl shadow p-3 sm:p-4 mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-4 border border-gray-100">
            <input id="sinistre-filter" type="text" placeholder="Rechercher par numéro, lieu, gestionnaire..." class="w-full sm:w-80 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-400 text-sm" />
            <select id="statut-filter" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-400 text-sm">
                <option value="">Tous les statuts</option>
                <option value="en_attente">En attente</option>
                <option value="en_cours">En cours</option>
                <option value="regle">Réglé</option>
                <option value="clos">Clos</option>
                <option value="expertise_requise">Expertise requise</option>
            </select>
            <select id="gestionnaire-filter" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-400 text-sm">
                <option value="">Tous les gestionnaires</option>
                @foreach($sinistres->pluck('gestionnaire.nom_complet')->unique()->filter() as $gestionnaire)
                    <option value="{{ $gestionnaire }}">{{ $gestionnaire }}</option>
                @endforeach
            </select>
            <a href="{{ route('declaration.create') }}" class="w-full sm:w-auto px-6 py-2 bg-red-700 text-white rounded-lg font-semibold shadow hover:bg-red-800 transition text-center">Déclarer un nouveau sinistre</a>
        </div>

        <div class="bg-white rounded-2xl shadow-lg overflow-x-auto border border-gray-100">
            <!-- Label scroll sur mobile -->
            <div class="block sm:hidden px-4 pt-4 pb-1 text-xs text-gray-400 font-medium">Scroll →</div>
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-bold text-gray-900">Mes Sinistres</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm sm:text-base" id="sinistres-table">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Numéro</th>
                            <th class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                            <th class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gestionnaire</th>
                            <th class="px-3 sm:px-6 py-2 sm:py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="sinistres-tbody">
                        @forelse($sinistres as $sinistre)
                            @php
                                $sinistreData = [
                                    'numero_sinistre' => $sinistre->numero_sinistre,
                                    'date_sinistre' => $sinistre->date_sinistre ? $sinistre->date_sinistre->format('d/m/Y') : '-',
                                    'statut_libelle' => $sinistre->statut_libelle,
                                    'gestionnaire' => $sinistre->gestionnaire->nom_complet ?? 'Non assigné',
                                    'lieu_sinistre' => $sinistre->lieu_sinistre,
                                    'montant_estime' => $sinistre->montant_estime,
                                    'montant_regle' => $sinistre->montant_regle,
                                    'circonstances' => $sinistre->circonstances,
                                    'statut' => $sinistre->statut
                                ];
                            @endphp
                            <tr class="sinistre-row">
                                <td class="px-3 sm:px-6 py-3 whitespace-nowrap font-semibold">{{ $sinistre->numero_sinistre }}</td>
                                <td class="px-3 sm:px-6 py-3 whitespace-nowrap">{{ $sinistre->date_sinistre ? $sinistre->date_sinistre->format('d/m/Y') : '-' }}</td>
                                <td class="px-3 sm:px-6 py-3 whitespace-nowrap">
                                    <span class="inline-block px-2 sm:px-3 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-800">
                                        {{ $sinistre->statut_libelle }}
                                    </span>
                                </td>
                                <td class="px-3 sm:px-6 py-3 whitespace-nowrap">{{ $sinistre->gestionnaire->nom_complet ?? 'Non assigné' }}</td>
                                <td class="px-3 sm:px-6 py-3 whitespace-nowrap text-center">
                                    <button type="button" class="details-btn text-blue-600 hover:text-blue-900" data-sinistre='@json($sinistreData)' title="Voir les détails">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zm6 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    </button>
                                    <a href="{{ route('chat.index', $sinistre->id) }}" class="inline-block ml-2 text-red-600 hover:text-red-800" title="Messagerie">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m-2 8a9 9 0 100-18 9 9 0 000 18z" />
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-500">Aucun sinistre déclaré pour le moment.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                {{ $sinistres->links('pagination::tailwind') }}
            </div>
        </div>

        <!-- Modal Détails Sinistre -->
        <div id="sinistre-modal" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50 hidden">
            <div class="bg-white rounded-xl shadow-lg w-full max-w-lg p-6 relative">
                <button onclick="closeModal()" class="absolute top-3 right-3 text-gray-400 hover:text-red-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
                <h3 class="text-xl font-bold mb-4 text-red-700">Détails du Sinistre</h3>
                <div id="modal-content">
                    <!-- Contenu dynamique -->
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="w-full py-4 bg-white/80 border-t border-gray-200 text-center text-xs text-gray-500 mt-8">
        © Saar Assurances Côte d'Ivoire. Tous droits réservés.
    </footer>

    <script>
       
        function filterSinistres() {
            const search = document.getElementById('sinistre-filter').value.toLowerCase();
            const statut = document.getElementById('statut-filter').value;
            const gestionnaire = document.getElementById('gestionnaire-filter').value;
            document.querySelectorAll('.sinistre-row').forEach(function(row) {
                const text = row.textContent.toLowerCase();
                const rowStatut = row.querySelector('td:nth-child(3) span').textContent.trim().toLowerCase();
                const rowGestionnaire = row.querySelector('td:nth-child(4)').textContent.trim();
                let visible = text.includes(search);
                if (statut && rowStatut !== document.querySelector(`#statut-filter option[value="${statut}"]`).textContent.trim().toLowerCase()) visible = false;
                if (gestionnaire && rowGestionnaire !== gestionnaire) visible = false;
                row.style.display = visible ? '' : 'none';
            });
        }
        document.getElementById('sinistre-filter').addEventListener('input', filterSinistres);
        document.getElementById('statut-filter').addEventListener('change', filterSinistres);
        document.getElementById('gestionnaire-filter').addEventListener('change', filterSinistres);

        // Modal détails
        function closeModal() {
            document.getElementById('sinistre-modal').classList.add('hidden');
        }
        function openModal(contentHtml) {
            document.getElementById('modal-content').innerHTML = contentHtml;
            document.getElementById('sinistre-modal').classList.remove('hidden');
        }
        document.querySelectorAll('.details-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                const sinistre = JSON.parse(this.getAttribute('data-sinistre'));
                let html = `
                    <div class="space-y-2">
                        <div><span class="font-semibold">Numéro :</span> ${sinistre.numero_sinistre}</div>
                        <div><span class="font-semibold">Date :</span> ${sinistre.date_sinistre}</div>
                        <div><span class="font-semibold">Statut :</span> ${sinistre.statut_libelle}</div>
                        <div><span class="font-semibold">Gestionnaire :</span> ${sinistre.gestionnaire}</div>
                        <div><span class="font-semibold">Lieu :</span> ${sinistre.lieu_sinistre ?? '-'}</div>
                        <div><span class="font-semibold">Montant estimé :</span> ${sinistre.montant_estime ?? '-'} FCFA</div>
                        <div><span class="font-semibold">Montant réglé :</span> ${sinistre.montant_regle ?? '-'} FCFA</div>
                        <div><span class="font-semibold">Circonstances :</span> ${sinistre.circonstances ?? '-'}</div>
                    </div>
                `;
                openModal(html);
            });
        });
        
        document.addEventListener('keydown', function(e) {
            if(e.key === 'Escape') closeModal();
        });
    </script>
</body>
</html>