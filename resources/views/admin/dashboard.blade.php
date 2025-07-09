<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - SAAR Assurance</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'saar-red': '#FF0000',
                        'saar-blue': '#1E40AF',
                        'saar-green': '#059669',
                    },
                    animation: {
                        'slide-in': 'slideIn 0.5s ease-out',
                        'fade-in': 'fadeIn 0.3s ease-out',
                        'bounce-in': 'bounceIn 0.6s ease-out',
                        'pulse-slow': 'pulse 2s infinite',
                        'slide-down': 'slideDown 0.3s ease-out',
                    },
                    keyframes: {
                        slideIn: {
                            '0%': {
                                opacity: '0',
                                transform: 'translateX(20px)'
                            },
                            '100%': {
                                opacity: '1',
                                transform: 'translateX(0)'
                            }
                        },
                        fadeIn: {
                            '0%': {
                                opacity: '0'
                            },
                            '100%': {
                                opacity: '1'
                            }
                        },
                        bounceIn: {
                            '0%': {
                                opacity: '0',
                                transform: 'scale(0.9)'
                            },
                            '50%': {
                                transform: 'scale(1.05)'
                            },
                            '100%': {
                                opacity: '1',
                                transform: 'scale(1)'
                            }
                        },
                        slideDown: {
                            '0%': {
                                opacity: '0',
                                transform: 'translateY(-10px)'
                            },
                            '100%': {
                                opacity: '1',
                                transform: 'translateY(0)'
                            }
                        }
                    }
                }
            }
        }
    </script>

    <style>
        .menu-item.active {
            background: rgba(255, 255, 255, 0.15);
            border-bottom-color: white !important;
        }
    </style>
</head>

<body class="bg-gradient-to-br from-red-50 via-white to-green-50 min-h-screen">

    <!-- Header -->
    <nav class="bg-white shadow-lg border-b-2 border-gray-100">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between h-16">
                <!-- Logo -->
                <div class="flex items-center space-x-4">
                    <div
                        class="w-10 h-10 bg-gradient-to-r from-saar-red to-red-500 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                            </path>
                        </svg>
                    </div>
                    <div>
                        <a href="/">
                            <h1
                                class="text-lg font-bold text-transparent bg-clip-text bg-gradient-to-r from-saar-red to-saar-red">
                                SAAR ASSURANCE
                            </h1>
                        </a>
                        <p class="text-xs text-gray-600">Dashboard de Gestion</p>
                    </div>
                </div>

                <!-- User Menu -->
                <div class="flex items-center space-x-4">
                    <div class="relative">
                        <button onclick="toggleNotifications()"
                            class="relative p-2 text-gray-600 hover:text-saar-blue transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 17h5l-3.405-3.405A2.032 2.032 0 0116 12V9a4.002 4.002 0 00-8 0v3c0 .601-.166 1.177-.595 1.595L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                                </path>
                            </svg>
                            <span id="notification-count"
                                class="absolute -top-1 -right-1 h-4 w-4 bg-red-500 text-white rounded-full text-xs flex items-center justify-center hidden">0</span>
                        </button>

                        <!-- Notifications Dropdown -->
                        <div id="notifications-dropdown"
                            class="hidden absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-lg border border-gray-200 z-50">
                            <div class="p-4 border-b border-gray-200">
                                <h3 class="text-lg font-semibold text-gray-900">Notifications</h3>
                            </div>
                            <div id="notifications-list" class="max-h-64 overflow-y-auto">
                                <!-- Notifications chargées dynamiquement -->
                            </div>
                        </div>
                    </div>

                    <div class="relative">
                        <button onclick="toggleUserMenu()"
                            class="flex items-center space-x-2 p-2 text-gray-600 hover:text-saar-blue transition-colors">
                            <div
                                class="w-8 h-8 bg-gradient-to-r from-saar-blue to-blue-600 rounded-full flex items-center justify-center">
                                @if (Auth::check() && Auth::user()->nom_complet)
                                    <span
                                        class="text-white text-sm font-semibold">{{ substr(Auth::user()->nom_complet, 0, 2) }}</span>
                                @else
                                    <span class="text-white text-sm font-semibold">??</span>
                                @endif
                            </div>
                            <span class="text-sm font-medium">
                                @if (Auth::check() && Auth::user()->nom_complet)
                                    {{ Auth::user()->nom_complet }}
                                @else
                                    Utilisateur
                                @endif
                            </span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <!-- User Dropdown -->
                        <div id="user-menu"
                            class="hidden absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-gray-200 z-50">
                            <div class="py-2">
                                <a href="#"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profil</a>
                                <a href="#"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Paramètres</a>
                                <hr class="my-1">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit"
                                        class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                        Déconnexion
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Navbar Horizontale -->
    <nav class="bg-gradient-to-r from-saar-red to-red-600 shadow-md">
        <div class="container mx-auto px-4">
            <div class="flex space-x-0">
                <!-- Tableau de bord -->
                <a href="{{ route('dashboard') }}"
                    class="menu-item active flex items-center space-x-2 px-6 py-4 text-white/80 hover:text-white hover:bg-white/10 transition-all duration-200 border-b-3 border-transparent hover:border-white/50">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 5a2 2 0 012-2h4a2 2 0 012 2v6H8V5z"></path>
                    </svg>
                    <span class="font-medium">Tableau de bord</span>
                </a>

                <!-- Utilisateurs -->
                <a href="{{ route('dashboard.users') }}"
                    class="menu-item flex items-center space-x-2 px-6 py-4 text-white hover:bg-white/10 transition-all duration-200 border-b-3 border-transparent hover:border-white/50">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z">
                        </path>
                    </svg>
                    <span class="font-medium">Utilisateurs</span>
                </a>

                <!-- Média -->
                <a href="#"
                    class="menu-item flex items-center space-x-2 px-6 py-4 text-white/80 hover:text-white hover:bg-white/10 transition-all duration-200 border-b-3 border-transparent hover:border-white/50">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2h4a1 1 0 011 1v14a2 2 0 01-2 2H4a2 2 0 01-2-2V5a1 1 0 011-1h4z">
                        </path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m-4 4h4">
                        </path>
                    </svg>
                    <span class="font-medium">Média</span>
                </a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mx-auto px-4 py-8">

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div
                class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 hover:shadow-xl transition-all duration-300">
                <div class="flex items-center">
                    <div class="p-3 rounded-xl bg-blue-100">
                        <svg class="w-6 h-6 text-saar-blue" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Sinistres</p>
                        <p id="stat-total" class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
                    </div>
                </div>
            </div>

            <div
                class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 hover:shadow-xl transition-all duration-300">
                <div class="flex items-center">
                    <div class="p-3 rounded-xl bg-yellow-100">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">En Attente</p>
                        <p id="stat-en-attente" class="text-2xl font-bold text-gray-900">{{ $stats['en_attente'] }}
                        </p>
                    </div>
                </div>
            </div>

            <div
                class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 hover:shadow-xl transition-all duration-300">
                <div class="flex items-center">
                    <div class="p-3 rounded-xl bg-green-100">
                        <svg class="w-6 h-6 text-saar-green" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                            </path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Traités</p>
                        <p id="stat-traites" class="text-2xl font-bold text-gray-900">{{ $stats['traites'] }}</p>
                    </div>
                </div>
            </div>

            <div
                class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 hover:shadow-xl transition-all duration-300">
                <div class="flex items-center">
                    <div class="p-3 rounded-xl bg-red-100">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L4.268 16.5c-.77.833.192 2.5 1.732 2.5z">
                            </path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">En Retard</p>
                        <p id="stat-en-retard" class="text-2xl font-bold text-gray-900">{{ $stats['en_retard'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search and Filters -->
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-8 border border-gray-100">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0">
                <!-- Search Bar -->
                <div class="flex-1 max-w-md">
                    <div class="relative">
                        <input type="text" id="search-input"
                            placeholder="Rechercher par numéro, nom ou téléphone..."
                            class="w-full pl-10 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:border-saar-blue focus:ring-4 focus:ring-blue-100 transition-all duration-300">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="flex flex-wrap gap-4">
                    <select id="status-filter"
                        class="px-4 py-2 border-2 border-gray-200 rounded-lg focus:border-saar-blue focus:ring-2 focus:ring-blue-100 transition-all">
                        <option value="">Tous les statuts</option>
                        <option value="en_attente">En attente</option>
                        <option value="en_cours">En cours</option>
                        <option value="expertise_requise">Expertise requise</option>
                        <option value="en_attente_documents">En attente documents</option>
                        <option value="pret_reglement">Prêt règlement</option>
                        <option value="regle">Réglé</option>
                        <option value="refuse">Refusé</option>
                        <option value="clos">Clos</option>
                    </select>

                    <select id="gestionnaire-filter"
                        class="px-4 py-2 border-2 border-gray-200 rounded-lg focus:border-saar-blue focus:ring-2 focus:ring-blue-100 transition-all">
                        <option value="">Tous les gestionnaires</option>
                        @foreach ($gestionnaires as $gestionnaire)
                            <option value="{{ $gestionnaire->id }}">{{ $gestionnaire->nom_complet }}</option>
                        @endforeach
                        <option value="null">Non assigné</option>
                    </select>

                    <button onclick="resetFilters()"
                        class="px-4 py-2 text-gray-600 hover:text-saar-blue transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                            </path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Sinistres Table -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-bold text-gray-900">Liste des Sinistres</h2>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Sinistre
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Assuré
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Date
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Statut
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Gestionnaire
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody id="sinistres-tbody" class="bg-white divide-y divide-gray-200">
                        <!-- Données des sinistres seront chargées ici -->
                    </tbody>
                </table>
            </div>

            <!-- Loading State -->
            <div id="loading-state" class="hidden p-8 text-center">
                <div class="inline-flex items-center">
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-saar-blue" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                    <span class="text-gray-600">Chargement...</span>
                </div>
            </div>

            <!-- Pagination -->
            <div id="pagination-container"
                class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                <!-- Pagination dynamique -->
            </div>
        </div>
    </div>

    <!-- Modals -->

    <!-- Modal Affectation Gestionnaire -->
    <div id="assign-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-2xl bg-white animate-bounce-in">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-gray-900">Affecter un Gestionnaire</h3>
                    <button onclick="closeModal('assign-modal')" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Sinistre</label>
                    <p id="assign-sinistre-info" class="text-sm text-gray-600 bg-gray-50 p-2 rounded-lg"></p>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Gestionnaire</label>
                    <select id="assign-gestionnaire"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:border-saar-blue focus:ring-2 focus:ring-blue-100">
                        <option value="">Sélectionner un gestionnaire</option>
                        @foreach ($gestionnaires as $gestionnaire)
                            <option value="{{ $gestionnaire->id }}">{{ $gestionnaire->nom_complet }}</option>
                        @endforeach
                        <option value="self">M'affecter ce sinistre</option>
                    </select>
                </div>

                <div class="flex justify-end space-x-3">
                    <button onclick="closeModal('assign-modal')"
                        class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                        Annuler
                    </button>
                    <button onclick="confirmAssignment()"
                        class="px-4 py-2 bg-saar-blue text-white rounded-lg hover:bg-blue-700">
                        Affecter
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Changement de Statut -->
    <div id="status-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-2xl bg-white animate-bounce-in">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-gray-900">Changer le Statut</h3>
                    <button onclick="closeModal('status-modal')" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Sinistre</label>
                    <p id="status-sinistre-info" class="text-sm text-gray-600 bg-gray-50 p-2 rounded-lg"></p>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nouveau Statut</label>
                    <select id="new-status"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:border-saar-blue focus:ring-2 focus:ring-blue-100">
                        <option value="en_attente">En attente</option>
                        <option value="en_cours">En cours</option>
                        <option value="expertise_requise">Expertise requise</option>
                        <option value="en_attente_documents">En attente documents</option>
                        <option value="pret_reglement">Prêt règlement</option>
                        <option value="regle">Réglé</option>
                        <option value="refuse">Refusé</option>
                        <option value="clos">Clos</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Commentaire (optionnel)</label>
                    <textarea id="status-comment" rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:border-saar-blue focus:ring-2 focus:ring-blue-100"
                        placeholder="Raison du changement de statut..."></textarea>
                </div>

                <div class="flex justify-end space-x-3">
                    <button onclick="closeModal('status-modal')"
                        class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                        Annuler
                    </button>
                    <button onclick="confirmStatusChange()"
                        class="px-4 py-2 bg-saar-green text-white rounded-lg hover:bg-green-700">
                        Modifier
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Détails Sinistre -->
    <div id="details-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div
            class="relative top-10 mx-auto p-5 border w-4xl max-w-4xl shadow-lg rounded-2xl bg-white animate-bounce-in">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-gray-900">Détails du Sinistre</h3>
                    <button onclick="closeModal('details-modal')" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div id="sinistre-details-content" class="space-y-6">
                    <!-- Le contenu sera chargé dynamiquement -->
                </div>

                <div class="flex justify-end mt-6">
                    <button onclick="closeModal('details-modal')"
                        class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                        Fermer
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentPage = 1;
        let currentPerPage = 10;
        let currentSinistreId = null;
        let allGestionnaires = @json($gestionnaires);

        // Configuration API
        const API_BASE = '{{ url('/') }}';
        const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Initialisation
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Initialisation du dashboard...');
            loadSinistres();
            loadNotifications();
            setupEventListeners();

            // update stats after 5 minutes
            setInterval(refreshStats, 300000);
        });

        function setupEventListeners() {
            //search
            document.getElementById('search-input').addEventListener('input', debounce(handleSearch, 300));

            //filters
            document.getElementById('status-filter').addEventListener('change', handleFilter);
            document.getElementById('gestionnaire-filter').addEventListener('change', handleFilter);

            document.addEventListener('click', function(event) {
                if (!event.target.closest('[onclick*="toggleUserMenu"]')) {
                    document.getElementById('user-menu').classList.add('hidden');
                }
                if (!event.target.closest('[onclick*="toggleNotifications"]')) {
                    document.getElementById('notifications-dropdown').classList.add('hidden');
                }
            });
        }

        //API function
        async function apiRequest(url, options = {}) {
            const defaultOptions = {
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                    'Accept': 'application/json'
                }
            };

            try {
                const response = await fetch(url, {
                    ...defaultOptions,
                    ...options,
                    headers: {
                        ...defaultOptions.headers,
                        ...options.headers
                    }
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                return await response.json();
            } catch (error) {
                console.error('API Request failed:', error);
                showErrorMessage('Erreur de communication avec le serveur');
                throw error;
            }
        }

        async function loadSinistres() {
            showLoading(true);
            console.log('Chargement des sinistres...');

            try {
                const params = new URLSearchParams({
                    page: currentPage,
                    per_page: currentPerPage,
                    search: document.getElementById('search-input').value || '',
                    statut: document.getElementById('status-filter').value || '',
                    gestionnaire_id: document.getElementById('gestionnaire-filter').value || ''
                });

                const data = await apiRequest(`${API_BASE}/dashboard/sinistres?${params}`);
                console.log('Données reçues:', data);

                displaySinistres(data.data);
                updatePagination(data);

            } catch (error) {
                console.error('Erreur lors du chargement des sinistres:', error);
                displayEmptyState();
            } finally {
                showLoading(false);
            }
        }

        async function refreshStats() {
            try {
                const data = await apiRequest(`${API_BASE}/dashboard/stats`);
                updateStatsDisplay(data.stats);
            } catch (error) {
                console.error('Erreur lors du rafraîchissement des stats:', error);
            }
        }

        async function loadNotifications() {
            try {
                const data = await apiRequest(`${API_BASE}/dashboard/notifications`);
                displayNotifications(data.notifications, data.total_unread);
            } catch (error) {
                console.error('Erreur lors du chargement des notifications:', error);
            }
        }

        function updateStatsDisplay(stats) {
            document.getElementById('stat-total').textContent = stats.total;
            document.getElementById('stat-en-attente').textContent = stats.en_attente;
            document.getElementById('stat-traites').textContent = stats.traites;
            document.getElementById('stat-en-retard').textContent = stats.en_retard;
        }

        function displayNotifications(notifications, totalUnread) {
            const countElement = document.getElementById('notification-count');
            const listElement = document.getElementById('notifications-list');

            //update count
            if (totalUnread > 0) {
                countElement.textContent = totalUnread;
                countElement.classList.remove('hidden');
            } else {
                countElement.classList.add('hidden');
            }

            //display notification
            if (notifications.length === 0) {
                listElement.innerHTML = '<div class="p-4 text-center text-gray-500">Aucune notification</div>';
                return;
            }

            listElement.innerHTML = notifications.map(notification => `
                <div class="p-4 border-b border-gray-100 hover:bg-gray-50">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            ${getNotificationIcon(notification.type)}
                        </div>
                        <div class="ml-3 flex-1">
                            <p class="text-sm font-medium text-gray-900">${notification.title}</p>
                            <p class="text-sm text-gray-500">${notification.message}</p>
                            <p class="text-xs text-gray-400 mt-1">${formatRelativeTime(notification.created_at)}</p>
                        </div>
                    </div>
                </div>
            `).join('');
        }

        function getNotificationIcon(type) {
            const icons = {
                'warning': '<div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center"><svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L4.268 16.5c-.77.833.192 2.5 1.732 2.5z"></path></svg></div>',
                'info': '<div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center"><svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></div>',
                'urgent': '<div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center"><svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L4.268 16.5c-.77.833.192 2.5 1.732 2.5z"></path></svg></div>'
            };
            return icons[type] || icons['info'];
        }

        function displaySinistres(sinistres) {
            const tbody = document.getElementById('sinistres-tbody');

            if (sinistres.length === 0) {
                displayEmptyState();
                return;
            }

            tbody.innerHTML = sinistres.map(sinistre => createSinistreRow(sinistre)).join('');
        }

        function createSinistreRow(sinistre) {
            const statusBadge = getStatusBadge(sinistre.statut);
            const urgencyIndicator = sinistre.en_retard ?
                '<span class="inline-block w-2 h-2 bg-red-500 rounded-full animate-pulse mr-2" title="En retard"></span>' :
                (sinistre.jours_en_cours > 10 ?
                    '<span class="inline-block w-2 h-2 bg-yellow-500 rounded-full mr-2" title="Urgent"></span>' : '');

            const dateHeure = sinistre.heure_sinistre ?
                `${formatDate(sinistre.date_sinistre)} à ${formatTime(sinistre.heure_sinistre)}` :
                formatDate(sinistre.date_sinistre);

            return `
        <tr class="hover:bg-gray-50 transition-colors">
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                    ${urgencyIndicator}
                    <div>
                        <div class="text-sm font-medium text-gray-900">${sinistre.numero_sinistre}</div>
                        <div class="text-sm text-gray-500">${sinistre.numero_police}</div>
                    </div>
                </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <div>
                    <div class="text-sm font-medium text-gray-900">${sinistre.nom_assure}</div>
                    <div class="text-sm text-gray-500">${sinistre.telephone_assure}</div>
                </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900">${dateHeure}</div>
                <div class="text-sm text-gray-500">${sinistre.jours_en_cours} jour(s)</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                ${statusBadge}
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900">
                    ${sinistre.gestionnaire ? sinistre.gestionnaire.nom_complet : '<span class="text-gray-400 italic">Non assigné</span>'}
                </div>
                ${sinistre.date_affectation ? `<div class="text-xs text-gray-400">Affecté le ${formatDate(sinistre.date_affectation)}</div>` : ''}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <div class="flex space-x-2">
                    <button onclick="showDetails(${sinistre.id})"
                            class="text-saar-blue hover:text-blue-800 transition-colors"
                            title="Voir détails">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                    </button>

                    <button onclick="showAssignModal(${sinistre.id})"
                            class="text-purple-600 hover:text-purple-800 transition-colors"
                            title="Affecter gestionnaire">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                    </button>

                    <button onclick="showStatusModal(${sinistre.id})"
                            class="text-saar-green hover:text-green-800 transition-colors"
                            title="Changer statut">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </button>
                </div>
            </td>
        </tr>
    `;
        }

        function displayEmptyState() {
            const tbody = document.getElementById('sinistres-tbody');
            tbody.innerHTML = `
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                        <div class="flex flex-col items-center">
                            <svg class="w-12 h-12 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <p class="text-lg font-medium text-gray-900 mb-2">Aucun sinistre trouvé</p>
                            <p class="text-gray-500">Essayez de modifier vos critères de recherche</p>
                        </div>
                    </td>
                </tr>
            `;
        }

        function updatePagination(data) {
            const container = document.getElementById('pagination-container');
            const {
                current_page,
                last_page,
                per_page,
                total,
                from,
                to
            } = data;

            if (total === 0) {
                container.innerHTML = '';
                return;
            }

            const startItem = from || 0;
            const endItem = to || 0;

            container.innerHTML = `
                <div class="flex-1 flex justify-between sm:hidden">
                    <button onclick="changePage(${current_page - 1})"
                            ${current_page <= 1 ? 'disabled' : ''}
                            class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                        Précédent
                    </button>
                    <button onclick="changePage(${current_page + 1})"
                            ${current_page >= last_page ? 'disabled' : ''}
                            class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                        Suivant
                    </button>
                </div>
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700">
                            Affichage de <span class="font-medium">${startItem}</span> à <span class="font-medium">${endItem}</span> sur
                            <span class="font-medium">${total}</span> résultats
                        </p>
                    </div>
                    <div>
                        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                            <button onclick="changePage(${current_page - 1})"
                                    ${current_page <= 1 ? 'disabled' : ''}
                                    class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                            </button>
                            ${generatePageNumbers(current_page, last_page)}
                            <button onclick="changePage(${current_page + 1})"
                                    ${current_page >= last_page ? 'disabled' : ''}
                                    class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </nav>
                    </div>
                </div>
            `;
        }

        function generatePageNumbers(current, last) {
            let pages = '';
            const maxVisible = 5;
            let start = Math.max(1, current - Math.floor(maxVisible / 2));
            let end = Math.min(last, start + maxVisible - 1);

            if (end - start + 1 < maxVisible) {
                start = Math.max(1, end - maxVisible + 1);
            }

            for (let i = start; i <= end; i++) {
                const isActive = i === current;
                pages += `
                    <button onclick="changePage(${i})"
                            class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium ${
                                isActive
                                    ? 'bg-saar-blue text-white border-saar-blue'
                                    : 'bg-white text-gray-700 hover:bg-gray-50'
                            }">
                        ${i}
                    </button>
                `;
            }
            return pages;
        }

        function changePage(page) {
            if (page < 1) return;
            currentPage = page;
            loadSinistres();
        }

        // Fonctions de recherche et filtrage
        function handleSearch(event) {
            currentPage = 1;
            loadSinistres();
        }

        function handleFilter() {
            currentPage = 1;
            loadSinistres();
        }

        function resetFilters() {
            document.getElementById('search-input').value = '';
            document.getElementById('status-filter').value = '';
            document.getElementById('gestionnaire-filter').value = '';
            currentPage = 1;
            loadSinistres();
        }

        // modals fucntion
        async function showDetails(sinistreId) {
            try {
                const data = await apiRequest(`${API_BASE}/dashboard/sinistres/${sinistreId}/details`);
                const sinistre = data.sinistre;
                const stats = data.stats;

                const detailsContent = document.getElementById('sinistre-details-content');
                detailsContent.innerHTML = `
            <div class="grid md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h4 class="font-semibold text-gray-900 mb-3">Informations Générales</h4>
                        <div class="space-y-2 text-sm">
                            <p><span class="font-medium">Numéro:</span> ${sinistre.numero_sinistre}</p>
                            <p><span class="font-medium">Police:</span> ${sinistre.numero_police}</p>
                            <p><span class="font-medium">Date:</span> ${formatDate(sinistre.date_sinistre)}</p>
                            ${sinistre.heure_sinistre ? `<p><span class="font-medium">Heure:</span> ${formatTime(sinistre.heure_sinistre)}</p>` : ''}
                            <p><span class="font-medium">Lieu:</span> ${sinistre.lieu_sinistre}</p>
                            <p><span class="font-medium">Statut:</span> ${getStatusBadge(sinistre.statut)}</p>
                        </div>
                    </div>

                    <div class="bg-blue-50 p-4 rounded-lg">
                        <h4 class="font-semibold text-gray-900 mb-3">Assuré</h4>
                        <div class="space-y-2 text-sm">
                            <p><span class="font-medium">Nom:</span> ${sinistre.nom_assure}</p>
                            <p><span class="font-medium">Email:</span> ${sinistre.email_assure}</p>
                            <p><span class="font-medium">Téléphone:</span> ${sinistre.telephone_assure}</p>
                        </div>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="bg-green-50 p-4 rounded-lg">
                        <h4 class="font-semibold text-gray-900 mb-3">Gestion</h4>
                        <div class="space-y-2 text-sm">
                            <p><span class="font-medium">Gestionnaire:</span> ${sinistre.gestionnaire ? sinistre.gestionnaire.nom_complet : 'Non assigné'}</p>
                            ${sinistre.date_affectation ? `<p><span class="font-medium">Date affectation:</span> ${formatDate(sinistre.date_affectation)}</p>` : ''}
                            <p><span class="font-medium">Jours en cours:</span> ${sinistre.jours_en_cours}</p>
                            <p><span class="font-medium">En retard:</span> ${sinistre.en_retard ? '⚠️ Oui' : '✅ Non'}</p>
                            <p><span class="font-medium">Montant estimé:</span> ${formatCurrency(sinistre.montant_estime)}</p>
                            ${sinistre.montant_regle ? `<p><span class="font-medium">Montant réglé:</span> ${formatCurrency(sinistre.montant_regle)}</p>` : ''}
                            ${sinistre.date_reglement ? `<p><span class="font-medium">Date règlement:</span> ${formatDate(sinistre.date_reglement)}</p>` : ''}
                        </div>
                    </div>

                    ${sinistre.circonstances ? `
                                            <div class="bg-yellow-50 p-4 rounded-lg">
                                                <h4 class="font-semibold text-gray-900 mb-3">Circonstances</h4>
                                                <p class="text-sm text-gray-700">${sinistre.circonstances}</p>
                                            </div>
                                        ` : ''}
                </div>
            </div>

            <div class="mt-6 bg-purple-50 p-4 rounded-lg">
                <h4 class="font-semibold text-gray-900 mb-3">Actions Rapides</h4>
                <div class="flex flex-wrap gap-3">
                    <button onclick="showAssignModal(${sinistre.id}); closeModal('details-modal');"
                            class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                        Affecter Gestionnaire
                    </button>
                    <button onclick="showStatusModal(${sinistre.id}); closeModal('details-modal');"
                            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                        Changer Statut
                    </button>
                </div>
            </div>
        `;

                document.getElementById('details-modal').classList.remove('hidden');
            } catch (error) {
                showErrorMessage('Erreur lors du chargement des détails');
            }
        }

        async function showAssignModal(sinistreId) {
            try {
                const data = await apiRequest(`${API_BASE}/dashboard/sinistres/${sinistreId}/details`);
                const sinistre = data.sinistre;

                currentSinistreId = sinistreId;
                document.getElementById('assign-sinistre-info').textContent =
                    `${sinistre.numero_sinistre} - ${sinistre.nom_assure}`;
                document.getElementById('assign-gestionnaire').value = sinistre.gestionnaire_id || '';
                document.getElementById('assign-modal').classList.remove('hidden');
            } catch (error) {
                showErrorMessage('Erreur lors du chargement des informations du sinistre');
            }
        }

        async function showStatusModal(sinistreId) {
            try {
                const data = await apiRequest(`${API_BASE}/dashboard/sinistres/${sinistreId}/details`);
                const sinistre = data.sinistre;

                currentSinistreId = sinistreId;
                document.getElementById('status-sinistre-info').textContent =
                    `${sinistre.numero_sinistre} - ${sinistre.nom_assure}`;
                document.getElementById('new-status').value = sinistre.statut;
                document.getElementById('status-comment').value = '';
                document.getElementById('status-modal').classList.remove('hidden');
            } catch (error) {
                showErrorMessage('Erreur lors du chargement des informations du sinistre');
            }
        }

        async function confirmAssignment() {
            const gestionnaireId = document.getElementById('assign-gestionnaire').value;
            if (!gestionnaireId || !currentSinistreId) {
                showErrorMessage('Veuillez sélectionner un gestionnaire');
                return;
            }

            try {
                await apiRequest(`${API_BASE}/dashboard/sinistres/${currentSinistreId}/assign`, {
                    method: 'POST',
                    body: JSON.stringify({
                        gestionnaire_id: gestionnaireId
                    })
                });

                showSuccessMessage('Gestionnaire affecté avec succès');
                closeModal('assign-modal');
                loadSinistres();
                refreshStats();
            } catch (error) {
                showErrorMessage('Erreur lors de l\'affectation du gestionnaire');
            }
        }

        async function confirmStatusChange() {
            const newStatus = document.getElementById('new-status').value;
            const comment = document.getElementById('status-comment').value;

            if (!newStatus || !currentSinistreId) {
                showErrorMessage('Veuillez sélectionner un statut');
                return;
            }

            try {
                await apiRequest(`${API_BASE}/dashboard/sinistres/${currentSinistreId}/status`, {
                    method: 'POST',
                    body: JSON.stringify({
                        statut: newStatus,
                        commentaire: comment
                    })
                });

                showSuccessMessage('Statut modifié avec succès');
                closeModal('status-modal');
                loadSinistres();
                refreshStats();
            } catch (error) {
                showErrorMessage('Erreur lors du changement de statut');
            }
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
            currentSinistreId = null;
        }

        //Utility function
        function getStatusBadge(status) {
            const statusConfig = {
                'en_attente': {
                    label: 'En attente',
                    color: 'bg-yellow-100 text-yellow-800'
                },
                'en_cours': {
                    label: 'En cours',
                    color: 'bg-blue-100 text-blue-800'
                },
                'expertise_requise': {
                    label: 'Expertise requise',
                    color: 'bg-purple-100 text-purple-800'
                },
                'en_attente_documents': {
                    label: 'Attente documents',
                    color: 'bg-orange-100 text-orange-800'
                },
                'pret_reglement': {
                    label: 'Prêt règlement',
                    color: 'bg-indigo-100 text-indigo-800'
                },
                'regle': {
                    label: 'Réglé',
                    color: 'bg-green-100 text-green-800'
                },
                'refuse': {
                    label: 'Refusé',
                    color: 'bg-red-100 text-red-800'
                },
                'clos': {
                    label: 'Clos',
                    color: 'bg-gray-100 text-gray-800'
                }
            };

            const config = statusConfig[status] || {
                label: status,
                color: 'bg-gray-100 text-gray-800'
            };
            return `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${config.color}">${config.label}</span>`;
        }

        function formatDate(dateString) {
            if (!dateString) return '';
            const date = new Date(dateString);
            return date.toLocaleDateString('fr-FR');
        }

        function formatCurrency(amount) {
            if (!amount) return '0 FCFA';
            return new Intl.NumberFormat('fr-FR', {
                style: 'currency',
                currency: 'XOF',
                minimumFractionDigits: 0
            }).format(amount);
        }

        function formatTime(timeString) {
            if (!timeString) return '';
            if (timeString.match(/^\d{2}:\d{2}$/)) return timeString;
            const date = new Date(timeString);
            return date.toLocaleTimeString('fr-FR', {
                hour: '2-digit',
                minute: '2-digit'
            });
        }

        function formatRelativeTime(dateString) {
            const date = new Date(dateString);
            const now = new Date();
            const diffInSeconds = Math.floor((now - date) / 1000);

            if (diffInSeconds < 60) return 'À l\'instant';
            if (diffInSeconds < 3600) return `Il y a ${Math.floor(diffInSeconds / 60)} min`;
            if (diffInSeconds < 86400) return `Il y a ${Math.floor(diffInSeconds / 3600)} h`;
            return `Il y a ${Math.floor(diffInSeconds / 86400)} j`;
        }

        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }

        function showLoading(show) {
            const loadingState = document.getElementById('loading-state');
            const tbody = document.getElementById('sinistres-tbody');

            if (show) {
                loadingState.classList.remove('hidden');
                tbody.style.opacity = '0.5';
            } else {
                loadingState.classList.add('hidden');
                tbody.style.opacity = '1';
            }
        }

        function showSuccessMessage(message) {
            showNotification(message, 'success');
        }

        function showErrorMessage(message) {
            showNotification(message, 'error');
        }

        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            const bgColor = type === 'success' ? 'bg-green-500' : type === 'error' ? 'bg-red-500' : 'bg-blue-500';

            notification.className =
                `fixed top-4 right-4 ${bgColor} text-white px-6 py-4 rounded-lg shadow-lg z-50 animate-slide-in`;
            notification.innerHTML = `
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        ${type === 'success'
                            ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>'
                            : type === 'error'
                            ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>'
                            : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>'
                        }
                    </svg>
                    <span>${message}</span>
                    <button onclick="this.parentElement.parentElement.remove()" class="ml-4">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            `;

            document.body.appendChild(notification);

            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 5000);
        }

        function toggleUserMenu() {
            const menu = document.getElementById('user-menu');
            menu.classList.toggle('hidden');
        }

        function toggleNotifications() {
            const dropdown = document.getElementById('notifications-dropdown');
            dropdown.classList.toggle('hidden');

            if (!dropdown.classList.contains('hidden')) {
                //mark notifications as read
                setTimeout(() => {
                    markNotificationsAsRead();
                }, 1000);
            }
        }

        function setActiveMenu(element) {
            // Remove active class from all menu items
            document.querySelectorAll('.menu-item').forEach(item => {
                item.classList.remove('active');
                item.classList.remove('border-white');
                item.classList.add('text-white/80', 'border-transparent');
            });

            // Add active class to clicked item
            element.classList.add('active');
            element.classList.remove('text-white/80', 'border-transparent');
            element.classList.add('text-white', 'border-white');
        }

        async function markNotificationsAsRead() {
            try {
                await apiRequest(`${API_BASE}/dashboard/notifications/mark-read`, {
                    method: 'POST'
                });

                //update count
                document.getElementById('notification-count').classList.add('hidden');
            } catch (error) {
                console.error('Erreur lors du marquage des notifications:', error);
            }
        }
    </script>
</body>

</html>
