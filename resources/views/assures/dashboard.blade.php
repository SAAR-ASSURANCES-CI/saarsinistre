<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Assuré - SAAR Assurances</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- PWA Meta Tags -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#dc2626">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="SAAR Sinistre">
    <link rel="apple-touch-icon" sizes="180x180" href="/logo.png">
    
    <!-- Service Worker Registration -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('/sw.js');
            });
        }
    </script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        .avatar-initials {
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            color: #fff;
            background-color: #dc2626;
        }
        .animate-bounce-in {
            animation: bounce-in 0.5s ease-in-out;
        }
        @keyframes bounce-in {
            0% { transform: scale(0.9); opacity: 0; }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); opacity: 1; }
        }
        .stat-card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }
        .sinistre-row {
            transition: background-color 0.2s ease;
        }
        .sinistre-row:hover {
            background-color: #f9fafb;
        }
        .status-badge {
            font-size: 0.7rem;
            padding: 0.25rem 0.5rem;
        }
        #sinistre-modal {
            transition: opacity 0.2s ease, transform 0.2s ease;
            opacity: 0;
            transform: scale(0.95);
        }
        #sinistre-modal:not(.hidden) {
            opacity: 1;
            transform: scale(1);
        }
        @media (max-width: 640px) {
            .status-badge {
                font-size: 0.6rem;
                padding: 0.2rem 0.4rem;
            }
        }
    </style>
</head>
<body class="bg-gradient-to-br from-red-50 via-white to-green-50 min-h-screen flex flex-col">
    <!-- Sticky Header -->
    <header class="sticky top-0 z-40 bg-white/90 backdrop-blur border-b border-gray-200 shadow-sm flex items-center justify-between px-4 py-3 md:px-8">
        <div class="flex items-center space-x-3">
            <div class="w-12 h-12 rounded-lg flex items-center justify-center">
                <img src="{{ asset('logo.png') }}" alt="SAAR Assurances" class="w-12 h-12 object-contain">
            </div>
            <span class="text-red-700 font-bold text-lg md:text-xl">SAAR ASSURANCES</span>
        </div>
        <div class="flex items-center space-x-2 md:space-x-4">
            <a href="{{ url('/') }}" class="hidden sm:inline-flex items-center space-x-2 px-3 py-2 text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l9-9 9 9M4 10v10a1 1 0 001 1h14a1 1 0 001-1V10" />
                </svg>
                <span class="text-sm font-medium">Retour à l'accueil</span>
            </a>
            <!-- Notification Bell -->
            <button aria-label="Notifications" class="relative p-2 rounded-full hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-red-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
                <span class="absolute top-0 right-0 bg-red-600 text-white text-xs rounded-full h-4 w-4 flex items-center justify-center">2</span>
            </button>
            
            <!-- User Profile (mobile) -->
            <div class="md:hidden flex items-center">
                <div class="avatar-initials h-8 w-8 rounded-full flex items-center justify-center text-sm">
                    {{ strtoupper(mb_substr(Auth::user()->nom_complet, 0, 1)) }}
                </div>
            </div>
            
            <!-- Logout Icon (mobile/tablet) -->
            <form method="POST" action="{{ route('logout.assure') }}" class="block md:hidden">
                @csrf
                <button type="submit" aria-label="Se déconnecter" class="p-2 rounded-full hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-red-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                </button>
            </form>
        </div>
    </header>

    <div class="container mx-auto px-4 py-6 md:py-8 max-w-7xl flex-grow">
        <!-- User Info and Desktop Logout -->
        <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6 gap-4">
            <div class="flex items-center space-x-4">
                @php
                    $heure = now()->format('H');
                    $salutation = ($heure >= 5 && $heure < 15) ? 'Bonjour,' : 'Bonsoir,';
                @endphp
                <div class="hidden md:block">
                    <div class="avatar-initials h-10 w-10 rounded-full flex items-center justify-center text-base">
                        KK
                    </div>
                </div>
                <div>
                    <h1 class="text-lg md:text-xl font-bold text-gray-800">{{ $salutation }}</h1>
                    <h2 class="text-xl md:text-2xl font-bold text-red-700">{{ Auth::user()->nom_complet }}</h2>
                </div>
            </div>
            
            <!-- Desktop profile and logout -->
            <div class="flex items-center space-x-4">
                <form method="POST" action="{{ route('logout.assure') }}" class="hidden md:block">
                    @csrf
                    <button type="submit" class="flex items-center space-x-2 px-4 py-2 bg-red-600 text-white rounded-lg font-medium hover:bg-red-700 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        <span>Déconnexion</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- Statistiques -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3 sm:gap-4 mb-6">
            <!-- Total Sinistres -->
            <div class="stat-card bg-white rounded-xl shadow p-4 flex items-center space-x-3 border border-gray-100">
                <div class="p-2 rounded-lg bg-blue-100 text-blue-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-xs md:text-sm font-medium text-gray-600">Total Sinistres</p>
                    <p class="text-lg md:text-xl font-bold text-gray-900">{{ $sinistres->total() }}</p>
                </div>
            </div>
            
            <!-- En Attente -->
            <div class="stat-card bg-white rounded-xl shadow p-4 flex items-center space-x-3 border border-gray-100">
                <div class="p-2 rounded-lg bg-yellow-100 text-yellow-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-xs md:text-sm font-medium text-gray-600">En Attente</p>
                    <p class="text-lg md:text-xl font-bold text-gray-900">{{ $sinistres->where('statut', 'en_attente')->count() }}</p>
                </div>
            </div>
            
            <!-- En Cours -->
            <div class="stat-card bg-white rounded-xl shadow p-4 flex items-center space-x-3 border border-gray-100">
                <div class="p-2 rounded-lg bg-blue-100 text-blue-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs md:text-sm font-medium text-gray-600">En Cours</p>
                    <p class="text-lg md:text-xl font-bold text-gray-900">{{ $sinistres->where('statut', 'en_cours')->count() }}</p>
                </div>
            </div>
            
            <!-- Traités -->
            <div class="stat-card bg-white rounded-xl shadow p-4 flex items-center space-x-3 border border-gray-100">
                <div class="p-2 rounded-lg bg-green-100 text-green-600">
                    <svg class="w-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-xs md:text-sm font-medium text-gray-600">Traités</p>
                    <p class="text-lg md:text-xl font-bold text-gray-900">{{ $sinistres->whereIn('statut', ['regle', 'clos'])->count() }}</p>
                </div>
            </div>
            
            <!-- Expertise Requise -->
            <div class="stat-card bg-white rounded-xl shadow p-4 flex items-center space-x-3 border border-gray-100">
                <div class="p-2 rounded-lg bg-red-100 text-red-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 12v4m-6 4h12a2 2 0 002-2V10a2 2 0 00-2-2h-3V6a2 2 0 00-2-2h-2a2 2 0 00-2 2v2H6a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs md:text-sm font-medium text-gray-600">Expertise</p>
                    <p class="text-lg md:text-xl font-bold text-gray-900">{{ $sinistres->where('statut', 'expertise_requise')->count() }}</p>
                </div>
            </div>
        </div>

        <!-- Filtres et recherche -->
        <div class="bg-white rounded-xl shadow p-3 sm:p-4 mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border border-gray-100">
            <div class="w-full sm:w-auto flex-grow">
                <input id="sinistre-filter" type="text" placeholder="Rechercher..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-400 text-sm">
            </div>
            <div class="grid grid-cols-2 sm:flex gap-2 sm:gap-3 w-full sm:w-auto">
                <select id="statut-filter" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-400 text-sm">
                    <option value="">Tous statuts</option>
                    <option value="en_attente">En attente</option>
                    <option value="en_cours">En cours</option>
                    <option value="regle">Réglé</option>
                    <option value="clos">Clos</option>
                    <option value="expertise_requise">Expertise</option>
                </select>
                <select id="gestionnaire-filter" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-400 text-sm">
                    <option value="">Tous gestionnaires</option>
                    @foreach($sinistres->pluck('gestionnaire.nom_complet')->unique()->filter() as $gestionnaire)
                        <option value="{{ $gestionnaire }}">{{ $gestionnaire }}</option>
                    @endforeach
                </select>
                <a href="{{ route('declaration.create') }}" class="col-span-2 sm:col-span-1 px-4 py-2 bg-red-600 text-white rounded-lg font-medium hover:bg-red-700 transition-colors text-center text-sm sm:text-base">
                    <span class="hidden sm:inline">Déclarer un sinistre</span>
                    <span class="sm:hidden">Nouveau</span>
                </a>
            </div>
        </div>

        <!-- Tableau des sinistres -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
            <div class="px-4 sm:px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h2 class="text-lg sm:text-xl font-bold text-gray-900">Mes Sinistres</h2>
                <span class="text-xs text-gray-500">{{ $sinistres->total() }} résultat(s)</span>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 sm:px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">N° Sinistre</th>
                            <th class="px-3 sm:px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Date</th>
                            <th class="px-3 sm:px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Statut</th>
                            <th class="px-3 sm:px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Gestionnaire</th>
                            <th class="px-3 sm:px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Actions</th>
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
                                    'gestionnaire_email' => $sinistre->gestionnaire->email ?? null,
                                    'lieu_sinistre' => $sinistre->lieu_sinistre,
                                    'circonstances' => $sinistre->circonstances,
                                    'dommages_releves' => $sinistre->dommages_releves,
                                    'statut' => $sinistre->statut,
                                    'implique_tiers' => $sinistre->implique_tiers,
                                    'nombre_tiers' => $sinistre->nombre_tiers,
                                    'details_tiers' => $sinistre->details_tiers,
                                    'tiers' => $sinistre->tiers->map(function($tiers) {
                                        return [
                                            'numero_tiers' => $tiers->numero_tiers,
                                            'nom_complet' => $tiers->nom_complet,
                                            'telephone' => $tiers->telephone,
                                            'email' => $tiers->email,
                                            'designation_vehicule' => $tiers->designation_vehicule,
                                            'immatriculation' => $tiers->immatriculation,
                                            'compagnie_assurance' => $tiers->compagnie_assurance,
                                            'numero_police_assurance' => $tiers->numero_police_assurance,
                                            'adresse' => $tiers->adresse,
                                            'details_supplementaires' => $tiers->details_supplementaires
                                        ];
                                    })->toArray()
                                ];
                                
                                // Couleur du badge selon le statut
                                $badgeColor = 'bg-gray-100 text-gray-800';
                                if ($sinistre->statut === 'en_attente') $badgeColor = 'bg-yellow-100 text-yellow-800';
                                elseif ($sinistre->statut === 'en_cours') $badgeColor = 'bg-blue-100 text-blue-800';
                                elseif ($sinistre->statut === 'regle' || $sinistre->statut === 'clos') $badgeColor = 'bg-green-100 text-green-800';
                                elseif ($sinistre->statut === 'expertise_requise') $badgeColor = 'bg-red-100 text-red-800';
                            @endphp
                            <tr class="sinistre-row" data-statut="{{ $sinistre->statut }}" data-gestionnaire="{{ $sinistre->gestionnaire->nom_complet ?? '' }}">
                                <td class="px-3 sm:px-4 py-3 whitespace-nowrap font-medium text-sm">{{ $sinistre->numero_sinistre }}</td>
                                <td class="px-3 sm:px-4 py-3 whitespace-nowrap text-sm">{{ $sinistre->date_sinistre ? $sinistre->date_sinistre->format('d/m/Y') : '-' }}</td>
                                <td class="px-3 sm:px-4 py-3 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badgeColor }} status-badge">
                                        {{ $sinistre->statut_libelle }}
                                    </span>
                                </td>
                                <td class="px-3 sm:px-4 py-3 whitespace-nowrap text-sm">
                                    @if($sinistre->gestionnaire)
                                        <div class="flex flex-col">
                                            <span class="font-medium">{{ $sinistre->gestionnaire->nom_complet }}</span>
                                            <span class="text-gray-500 text-xs">{{ $sinistre->gestionnaire->email }}</span>
                                        </div>
                                    @else
                                        <span>Non assigné</span>
                                    @endif
                                </td>
                                <td class="px-3 sm:px-4 py-3 whitespace-nowrap text-center text-sm">
                                    <div class="flex justify-center space-x-2">
                                        <button type="button" class="details-btn text-blue-600 hover:text-blue-800 p-1 rounded-full hover:bg-blue-50" data-sinistre='@json($sinistreData)' title="Détails">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zm6 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </button>
                                        <a href="{{ route('assures.chat.index', $sinistre->id) }}" class="text-saar-blue hover:text-saar-red p-1 rounded-full hover:bg-gray-50" title="Discussion">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                            </svg>
                                        </a>
                                        @if($sinistre->necessiteFeedback())
                                            <a href="{{ route('assures.feedback.form', $sinistre) }}" 
                                               class="text-orange-600 hover:text-orange-800 p-1 rounded-full hover:bg-orange-50" 
                                               title="Feedback requis">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-500 text-sm">
                                    <div class="flex flex-col items-center justify-center space-y-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <p>Aucun sinistre déclaré pour le moment.</p>
                                        <a href="{{ route('declaration.create') }}" class="text-red-600 hover:text-red-800 font-medium">Déclarer un sinistre</a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($sinistres->hasPages())
            <div class="px-4 sm:px-6 py-3 border-t border-gray-200 bg-gray-50">
                {{ $sinistres->links('pagination::tailwind') }}
            </div>
            @endif
        </div>
    </div>

    <!-- Modal Détails Sinistre -->
    <div id="sinistre-modal" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50 hidden opacity-0 scale-95 transition-all duration-200 p-4">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-md max-h-[90vh] overflow-y-auto relative transform transition-all duration-200">
            <button onclick="closeModal()" class="absolute top-4 right-4 text-gray-400 hover:text-red-600 p-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            <div class="p-6">
                <h3 class="text-lg font-bold mb-4 text-red-700 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Détails du Sinistre
                </h3>
                <div id="modal-content" class="space-y-4 text-sm">
                    <!-- Contenu dynamique -->
                </div>
            </div>
        </div>
    </div>

    <!-- Footer discret -->
    <footer class="w-full py-3 bg-white/80 border-t border-gray-200 text-center text-xs text-gray-400 mt-auto">
        <div class="container mx-auto px-4">
            © {{ date('Y') }} Saar Assurances Côte d'Ivoire. Tous droits réservés.
        </div>
    </footer>

    <script>
        
        function filterSinistres() {
            const search = document.getElementById('sinistre-filter').value.toLowerCase();
            const statut = document.getElementById('statut-filter').value;
            const gestionnaire = document.getElementById('gestionnaire-filter').value;
            
            document.querySelectorAll('.sinistre-row').forEach(function(row) {
                const text = row.textContent.toLowerCase();
                const rowStatut = row.getAttribute('data-statut') || '';
                const rowGestionnaire = (row.getAttribute('data-gestionnaire') || '').trim();
                
                let visible = text.includes(search);
                if (statut && rowStatut !== statut) visible = false;
                if (gestionnaire && rowGestionnaire !== gestionnaire) visible = false;
                
                row.style.display = visible ? '' : 'none';
            });
        }
        
        document.getElementById('sinistre-filter').addEventListener('input', filterSinistres);
        document.getElementById('statut-filter').addEventListener('change', filterSinistres);
        document.getElementById('gestionnaire-filter').addEventListener('change', filterSinistres);

       
        function closeModal() {
            const modal = document.getElementById('sinistre-modal');
            modal.classList.add('opacity-0', 'scale-95');
            setTimeout(() => {
                modal.classList.add('hidden');
                document.body.style.overflow = 'auto';
            }, 200);
        }
        
        function openModal(contentHtml) {
            const modal = document.getElementById('sinistre-modal');
            document.getElementById('modal-content').innerHTML = contentHtml;
            modal.classList.remove('hidden');
            
            setTimeout(() => {
                modal.classList.remove('opacity-0', 'scale-95');
            }, 10);
            
            document.body.style.overflow = 'hidden';
        }
        
        document.querySelectorAll('.details-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                const sinistre = JSON.parse(this.getAttribute('data-sinistre'));
                
                let html = `
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-gray-500 font-medium">Numéro</p>
                                <p class="font-semibold">${sinistre.numero_sinistre}</p>
                            </div>
                            <div>
                                <p class="text-gray-500 font-medium">Date</p>
                                <p class="font-semibold">${sinistre.date_sinistre}</p>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-gray-500 font-medium">Statut</p>
                                <p class="font-semibold">${sinistre.statut_libelle}</p>
                            </div>
                            <div>
                                <p class="text-gray-500 font-medium">Gestionnaire</p>
                                <p class="font-semibold">${sinistre.gestionnaire}${sinistre.gestionnaire_email ? ' — ' + sinistre.gestionnaire_email : ''}</p>
                            </div>
                        </div>
                        
                        <div>
                            <p class="text-gray-500 font-medium">Lieu du sinistre</p>
                            <p class="font-semibold">${sinistre.lieu_sinistre || '-'}</p>
                        </div>
                        
                        <div>
                            <p class="text-gray-500 font-medium">Circonstances</p>
                            <p class="font-semibold whitespace-pre-line">${sinistre.circonstances || '-'}</p>
                        </div>
                        
                        <div>
                            <p class="text-gray-500 font-medium">Dommages relevés</p>
                            <p class="font-semibold whitespace-pre-line">${sinistre.dommages_releves || '-'}</p>
                        </div>
                        
                        ${sinistre.implique_tiers ? `
                            <div class="border-t pt-4">
                                <h4 class="text-gray-700 font-semibold mb-3 flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                    Tiers impliqués ${sinistre.nombre_tiers ? '(' + sinistre.nombre_tiers + ')' : ''}
                                </h4>
                                
                                ${sinistre.tiers && sinistre.tiers.length > 0 ? 
                                    sinistre.tiers.map(tiers => `
                                        <div class="bg-orange-50 p-3 rounded-lg mb-3 border border-orange-200">
                                            <div class="font-medium text-orange-900 mb-2">Tiers ${tiers.numero_tiers}</div>
                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-sm">
                                                ${tiers.nom_complet ? `
                                                    <div>
                                                        <span class="text-gray-600">Nom:</span>
                                                        <span class="ml-1 font-medium">${tiers.nom_complet}</span>
                                                    </div>
                                                ` : ''}
                                                ${tiers.telephone ? `
                                                    <div>
                                                        <span class="text-gray-600">Téléphone:</span>
                                                        <span class="ml-1">${tiers.telephone}</span>
                                                    </div>
                                                ` : ''}
                                                ${tiers.designation_vehicule ? `
                                                    <div>
                                                        <span class="text-gray-600">Véhicule:</span>
                                                        <span class="ml-1">${tiers.designation_vehicule}</span>
                                                    </div>
                                                ` : ''}
                                                ${tiers.immatriculation ? `
                                                    <div>
                                                        <span class="text-gray-600">Immatriculation:</span>
                                                        <span class="ml-1">${tiers.immatriculation}</span>
                                                    </div>
                                                ` : ''}
                                                ${tiers.compagnie_assurance ? `
                                                    <div class="sm:col-span-2">
                                                        <span class="text-gray-600">Assurance:</span>
                                                        <span class="ml-1">${tiers.compagnie_assurance}</span>
                                                        ${tiers.numero_police_assurance ? ' (N° ' + tiers.numero_police_assurance + ')' : ''}
                                                    </div>
                                                ` : ''}
                                            </div>
                                            ${tiers.adresse ? `
                                                <div class="mt-2 text-sm">
                                                    <span class="text-gray-600">Adresse:</span>
                                                    <p class="text-gray-700 mt-1">${tiers.adresse}</p>
                                                </div>
                                            ` : ''}
                                            ${tiers.details_supplementaires ? `
                                                <div class="mt-2 text-sm">
                                                    <span class="text-gray-600">Détails:</span>
                                                    <p class="text-gray-700 mt-1">${tiers.details_supplementaires}</p>
                                                </div>
                                            ` : ''}
                                        </div>
                                    `).join('') : 
                                    `<div class="bg-orange-50 p-3 rounded-lg border border-orange-200">
                                        <p class="text-sm text-orange-700">Tiers impliqué confirmé</p>
                                        ${sinistre.details_tiers ? '<p class="text-sm text-gray-700 mt-1">' + sinistre.details_tiers + '</p>' : ''}
                                    </div>`
                                }
                            </div>
                        ` : ''}
                    </div>
                `;
                
                openModal(html);
            });
        });
        
      
        document.addEventListener('keydown', function(e) {
            if(e.key === 'Escape') closeModal();
        });
        
        document.getElementById('sinistre-modal').addEventListener('click', function(e) {
            if(e.target === this) closeModal();
        });
    </script>

    <!-- Popup de feedback -->
    @include('assures.partials.feedback-popup')
    
    <!-- PWA Script -->
    <script src="{{ asset('js/pwa.js') }}"></script>
</body>
</html>