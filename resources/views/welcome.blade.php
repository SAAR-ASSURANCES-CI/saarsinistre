<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SAAR AssuranceS Côte d'Ivoire - Déclaration de Sinistre</title>
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#0d6efd">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="SAARCISinistres">
    <link rel="apple-touch-icon" sizes="180x180" href="/logo.png">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
      if ('serviceWorker' in navigator) {
        window.addEventListener('load', function() {
          navigator.serviceWorker.register('/sw.js');
        });
      }
    </script>
    <script src="/js/pwa.js?v={{ config('app.asset_version') }}"></script>
</head>

<body class="bg-gradient-to-br from-red-50 via-white to-green-50 min-h-screen overflow-x-hidden">

    <!-- Éléments décoratifs flottants -->
    <div class="fixed inset-0 pointer-events-none overflow-hidden">
        <div class="absolute top-20 left-10 w-32 h-32 bg-saar-orange/10 rounded-full animate-float"></div>
        <div class="absolute top-40 right-20 w-24 h-24 bg-saar-blue/10 rounded-full animate-float"
            style="animation-delay: -2s;"></div>
        <div class="absolute bottom-40 left-20 w-20 h-20 bg-saar-green/10 rounded-full animate-float"
            style="animation-delay: -4s;"></div>
        <div class="absolute bottom-20 right-10 w-28 h-28 bg-red-200/20 rounded-full animate-float"
            style="animation-delay: -1s;"></div>
    </div>

    <div class="relative z-10 container mx-auto px-4 py-8">

        <!-- Bouton Connexion / Tableau de bord en haut à droite -->
        <div class="absolute top-8 right-8 z-20">
            <div class="inline-flex space-x-2">
                @auth
                    @php($role = Auth::user()->role)
                    @if($role === 'assure')
                        <a href="{{ route('assures.dashboard') }}" class="px-5 py-2 bg-saar-green text-white font-semibold rounded-xl shadow hover:bg-green-700 transition-all">Mon tableau de bord</a>
                    @elseif(in_array($role, ['gestionnaire','admin']))
                        <a href="{{ route('gestionnaires.dashboard') }}" class="px-5 py-2 bg-saar-green text-white font-semibold rounded-xl shadow hover:bg-green-700 transition-all">Mon tableau de bord</a>
                    @else
                        <a href="{{ url('/') }}" class="px-5 py-2 bg-saar-green text-white font-semibold rounded-xl shadow hover:bg-green-700 transition-all">Mon tableau de bord</a>
                    @endif
                @else
                    <a href="{{ route('login.assure') }}" class="px-5 py-2 bg-saar-green text-white font-semibold rounded-xl shadow hover:bg-green-700 transition-all">Espace assuré</a>
                @endauth
            </div>
        </div>

        <!-- Header avec logo et branding -->
        <div class="text-center mb-16 animate-fade-in-up">
            <div class="inline-flex items-center justify-center mb-8">
                <div class="relative">
                    <div
                        class="w-32 h-32 bg-white rounded-2xl shadow-2xl flex items-center justify-center transform rotate-3 hover:rotate-0 transition-transform duration-500">
                        <img src="{{ asset('logo.png') }}" alt="SAAR Assurances" class="w-32 h-32 object-contain">
                    </div>
                    <div class="absolute -top-2 -right-2 w-6 h-6 bg-green-400 rounded-full animate-pulse-slow"></div>
                </div>
            </div>

            <h1
                class="text-5xl md:text-6xl font-black text-transparent bg-clip-text bg-gradient-to-r from-saar-orange via-red-500 to-saar-orange mb-4">
                SAAR ASSURANCES
            </h1>
            <p class="text-xl md:text-2xl font-semibold text-saar-blue mb-6">
                CÔTE D'IVOIRE
            </p>
            <div class="w-24 h-1 bg-gradient-to-r from-saar-orange to-saar-green mx-auto mb-8 rounded-full"></div>
            <p class="text-xl text-gray-700 max-w-3xl mx-auto leading-relaxed">
                Déclarez votre sinistre automobile en toute simplicité.
                <span class="font-semibold text-saar-blue">Notre expertise à votre service</span>
                pour un traitement rapide et efficace de votre dossier.
            </p>
        </div>

        <div class="max-w-5xl mx-auto mb-16 animate-slide-in-right" x-data="{
            activeTab: 'declarer',
            recherche: '',
            loading: false,
            resultat: null,
            erreur: null,
            rechercherSinistre() {
                // Reset erreur et résultat
                this.erreur = null;
                this.resultat = null;
                
                // Validation basique
                if (!this.recherche || this.recherche.trim().length === 0) {
                    this.erreur = 'Veuillez saisir un numéro valide';
                    return;
                }
                
                this.loading = true;
                
                // Appel AJAX
                fetch('{{ route('suivi.rechercher') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        numero: this.recherche.trim()
                    })
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(data => {
                            throw new Error(data.message || 'Une erreur est survenue');
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        this.resultat = data.sinistre;
                        this.erreur = null;
                    } else {
                        this.erreur = data.message || 'Aucun sinistre trouvé';
                        this.resultat = null;
                    }
                })
                .catch(error => {
                    this.erreur = error.message || 'Une erreur est survenue, veuillez réessayer';
                    this.resultat = null;
                })
                .finally(() => {
                    this.loading = false;
                });
            }
        }">
            <div class="relative bg-white rounded-3xl shadow-2xl overflow-hidden border border-gray-100">

                <div class="h-2 bg-gradient-to-r from-saar-orange via-red-400 to-saar-green"></div>

                <div class="flex border-b border-gray-200">
                    <button @click="activeTab = 'declarer'; resultat = null; erreur = null;"
                        :class="activeTab === 'declarer' ? 'border-saar-orange text-saar-orange' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="flex-1 py-4 px-6 text-center border-b-2 font-medium text-sm md:text-base transition-colors duration-200">
                        <svg class="w-5 h-5 inline-block mr-2 -mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Déclarer un sinistre
                    </button>
                    <button @click="activeTab = 'suivi'; resultat = null; erreur = null;"
                        :class="activeTab === 'suivi' ? 'border-saar-blue text-saar-blue' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="flex-1 py-4 px-6 text-center border-b-2 font-medium text-sm md:text-base transition-colors duration-200">
                        <svg class="w-5 h-5 inline-block mr-2 -mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        Suivre mon sinistre
                    </button>
                </div>

                <div x-show="activeTab === 'declarer'" class="px-8 md:px-12 py-16 text-center">
                    <div class="mb-12">
                        <div class="relative inline-flex items-center justify-center mb-8">
                            <div
                                class="w-20 h-20 bg-gradient-to-r from-green-400 to-saar-green rounded-2xl shadow-xl flex items-center justify-center animate-pulse-slow">
                                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                            </div>
                            <div class="absolute -top-1 -right-1 w-6 h-6 bg-saar-orange rounded-full animate-ping">
                            </div>
                        </div>

                        <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                            Nouvelle Déclaration de Sinistre
                        </h2>
                        <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                            Processus simplifié en quelques étapes. Téléchargez vos documents et suivez l'avancement en
                            temps réel.
                        </p>
                    </div>

                    <div class="mb-8">
                        <a href="{{ route('declaration.create') }}"
                            class="group relative inline-flex items-center px-10 py-5 bg-gradient-to-r from-saar-orange to-red-500 hover:from-red-500 hover:to-saar-orange text-white font-bold text-lg rounded-2xl transition-all duration-300 transform hover:scale-105 shadow-xl hover:shadow-2xl">

                            <div
                                class="absolute inset-0 bg-gradient-to-r from-white/20 to-transparent rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            </div>

                            <svg class="w-6 h-6 mr-3 group-hover:rotate-12 transition-transform duration-300"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                            <span class="relative">Déclarer mon sinistre</span>

                            <svg class="w-5 h-5 ml-3 group-hover:translate-x-2 transition-transform duration-300"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                            </svg>
                        </a>
                    </div>

                    <div
                        class="inline-flex items-center px-4 py-2 bg-green-50 border border-green-200 rounded-full text-green-700 text-sm font-medium">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd"></path>
                        </svg>
                        Processus 100% sécurisé
                    </div>
                </div>

                <div x-show="activeTab === 'suivi'" class="px-8 md:px-12 py-16">
                    <div class="max-w-2xl mx-auto">
                        <div class="text-center mb-12">
                            <div class="relative inline-flex items-center justify-center mb-8">
                                <div class="w-20 h-20 bg-gradient-to-r from-blue-400 to-saar-blue rounded-2xl shadow-xl flex items-center justify-center">
                                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                    </svg>
                                </div>
                            </div>

                            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                                Suivez l'état de votre sinistre
                            </h2>
                            <p class="text-lg text-gray-600">
                                Entrez votre numéro de sinistre ou votre numéro d'attestation pour connaître l'état d'avancement de votre dossier
                            </p>
                        </div>

                        <form @submit.prevent="rechercherSinistre()" class="mb-8">
                            <div class="flex flex-col sm:flex-row gap-4">
                                <input 
                                    type="text" 
                                    x-model="recherche"
                                    placeholder="Ex: APP-00123-2025 ou numéro d'attestation"
                                    class="flex-1 px-6 py-4 border-2 border-gray-300 rounded-xl focus:border-saar-blue focus:ring-2 focus:ring-saar-blue/20 outline-none transition-all text-lg"
                                    required
                                >
                                <button 
                                    type="submit"
                                    :disabled="loading"
                                    class="px-8 py-4 bg-gradient-to-r from-saar-blue to-blue-600 hover:from-blue-600 hover:to-saar-blue text-white font-bold rounded-xl transition-all duration-300 transform hover:scale-105 shadow-lg disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none">
                                    <span x-show="!loading">Rechercher</span>
                                    <span x-show="loading" class="flex items-center justify-center">
                                        <svg class="animate-spin h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        Recherche...
                                    </span>
                                </button>
                            </div>
                        </form>

                        <div x-show="erreur" x-transition class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-lg">
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-red-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <p class="text-red-700 font-medium" x-text="erreur"></p>
                            </div>
                        </div>

                        <!-- Résultats -->
                        <div x-show="resultat" x-transition class="bg-gradient-to-br from-gray-50 to-blue-50/30 rounded-2xl p-8 border border-gray-200 shadow-lg">
                            <div class="space-y-6">
                                <!-- En-tête du résultat -->
                                <div class="flex items-center justify-between pb-6 border-b border-gray-200">
                                    <div>
                                        <h3 class="text-2xl font-bold text-gray-900" x-text="resultat?.numero_sinistre"></h3>
                                        <p class="text-sm text-gray-500 mt-1">Numéro de sinistre</p>
                                    </div>
                                    <span 
                                        class="px-4 py-2 rounded-xl font-bold text-sm shadow-md"
                                        :class="{
                                            'bg-yellow-100 text-yellow-800': resultat?.statut_couleur === 'yellow',
                                            'bg-blue-100 text-blue-800': resultat?.statut_couleur === 'blue',
                                            'bg-purple-100 text-purple-800': resultat?.statut_couleur === 'purple',
                                            'bg-orange-100 text-orange-800': resultat?.statut_couleur === 'orange',
                                            'bg-indigo-100 text-indigo-800': resultat?.statut_couleur === 'indigo',
                                            'bg-green-100 text-green-800': resultat?.statut_couleur === 'green',
                                            'bg-red-100 text-red-800': resultat?.statut_couleur === 'red',
                                            'bg-gray-100 text-gray-800': resultat?.statut_couleur === 'gray'
                                        }"
                                        x-text="resultat?.statut_libelle">
                                    </span>
                                </div>

                                <!-- Informations détaillées -->
                                <div class="grid md:grid-cols-2 gap-6">
                                    <div class="flex items-start space-x-3">
                                        <svg class="w-6 h-6 text-saar-blue mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        <div>
                                            <p class="text-sm text-gray-500">Date de déclaration</p>
                                            <p class="font-semibold text-gray-900" x-text="resultat?.date_declaration"></p>
                                        </div>
                                    </div>

                                    <div class="flex items-start space-x-3" x-show="resultat?.date_sinistre">
                                        <svg class="w-6 h-6 text-saar-orange mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <div>
                                            <p class="text-sm text-gray-500">Date du sinistre</p>
                                            <p class="font-semibold text-gray-900" x-text="resultat?.date_sinistre"></p>
                                        </div>
                                    </div>

                                    <div class="flex items-start space-x-3" x-show="resultat?.gestionnaire">
                                        <svg class="w-6 h-6 text-saar-green mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                        <div>
                                            <p class="text-sm text-gray-500">Gestionnaire assigné</p>
                                            <p class="font-semibold text-gray-900" x-text="resultat?.gestionnaire"></p>
                                        </div>
                                    </div>

                                    <div class="flex items-start space-x-3" x-show="!resultat?.gestionnaire">
                                        <svg class="w-6 h-6 text-gray-400 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <div>
                                            <p class="text-sm text-gray-500">Gestionnaire</p>
                                            <p class="font-semibold text-gray-500">En attente d'affectation</p>
                                        </div>
                                    </div>
                                    <div class="flex items-start space-x-3">
                                        <svg class="w-6 h-6 text-red-500 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        <div class="flex-1">
                                            <p class="text-sm text-gray-500">Lieu du sinistre</p>
                                            <p class="font-semibold text-gray-900" x-text="resultat?.lieu_sinistre"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section avantages (commune aux deux onglets) -->
                <div class="bg-gradient-to-r from-gray-50 to-red-50/30 px-8 md:px-12 py-10">
                    <div class="grid md:grid-cols-3 gap-8">

                        <div class="group text-center hover:transform hover:scale-105 transition-all duration-300">
                            <div
                                class="relative inline-flex items-center justify-center w-16 h-16 bg-gradient-to-r from-blue-500 to-saar-blue rounded-2xl shadow-lg mb-4 group-hover:shadow-xl">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div class="absolute -top-1 -right-1 w-4 h-4 bg-saar-orange rounded-full animate-ping">
                                </div>
                            </div>
                            <h3 class="font-bold text-gray-900 mb-2 text-lg">Traitement Rapide</h3>
                            <p class="text-gray-600">Suivi en temps réel et notifications automatiques</p>
                        </div>

                        <div class="group text-center hover:transform hover:scale-105 transition-all duration-300">
                            <div
                                class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-r from-saar-green to-green-600 rounded-2xl shadow-lg mb-4 group-hover:shadow-xl">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                    </path>
                                </svg>
                            </div>
                            <h3 class="font-bold text-gray-900 mb-2 text-lg">Communication</h3>
                            <p class="text-gray-600">Mises à jour par email et SMS</p>
                        </div>

                        <div class="group text-center hover:transform hover:scale-105 transition-all duration-300">
                            <div
                                class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-r from-purple-500 to-purple-600 rounded-2xl shadow-lg mb-4 group-hover:shadow-xl">
                                <img src="{{ asset('logo.png') }}" alt="SAAR Assurances" class="w-8 h-8 object-contain">
                            </div>
                            <h3 class="font-bold text-gray-900 mb-2 text-lg">Sécurité Garantie</h3>
                            <p class="text-gray-600">Protection maximale de vos données</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section contact améliorée -->
        <div class="max-w-4xl mx-auto">
            <div class="bg-gradient-to-r from-saar-blue to-blue-700 rounded-3xl shadow-2xl overflow-hidden text-white">
                <div class="px-8 md:px-12 py-12 text-center">
                    <div class="mb-8">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-white/20 rounded-2xl mb-6">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192L5.636 18.364M12 2.25a9.75 9.75 0 109.75 9.75c0-1.052-.18-2.062-.512-3.011z">
                                </path>
                            </svg>
                        </div>
                        <h3 class="text-3xl font-bold mb-4">Besoin d'assistance ?</h3>
                        <p class="text-xl text-blue-100 max-w-2xl mx-auto">
                            Notre équipe d'experts est disponible pour vous accompagner dans toutes vos démarches
                        </p>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-6 justify-center">
                        <a href="tel:+22500000000"
                            class="group inline-flex items-center px-8 py-4 bg-white/20 hover:bg-white/30 border border-white/30 rounded-xl text-white font-semibold transition-all duration-300 hover:transform hover:scale-105">
                            <svg class="w-6 h-6 mr-3 group-hover:rotate-12 transition-transform duration-300"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z">
                                </path>
                            </svg>
                            Appelez-nous
                        </a>

                        <a href="mailto:sinistres@saar-assurance.com"
                            class="group inline-flex items-center px-8 py-4 bg-saar-orange hover:bg-red-500 rounded-xl text-white font-semibold transition-all duration-300 hover:transform hover:scale-105 shadow-lg">
                            <svg class="w-6 h-6 mr-3 group-hover:rotate-12 transition-transform duration-300"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                </path>
                            </svg>
                            Écrivez-nous
                        </a>
                    </div>

                    <!-- Informations de contact -->
                    <div class="mt-8 pt-8 border-t border-white/20">
                        <p class="text-blue-100 text-sm">
                            Du lundi au vendredi : 8h00 - 18h00 | Samedi : 8h00 - 13h00
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center mt-16 pt-8 border-t border-gray-200">
            <p class="text-gray-600">
                © 2025 SAAR Assurances Côte d'Ivoire - Tous droits réservés
            </p>
            <a href="{{ route('login') }}" class="text-gray-400 hover:text-gray-600 text-sm mt-2 inline-block transition-colors duration-200">
                Accès personnel
            </a>
        </div>
    </div>

    <script>
        // Animation d'apparition au scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        // Observer tous les éléments avec animation
        document.querySelectorAll('.animate-slide-in-right').forEach(el => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(30px)';
            el.style.transition = 'all 0.8s ease-out';
            observer.observe(el);
        });
    </script>
</body>

</html>
