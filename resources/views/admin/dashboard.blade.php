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
                        <h1
                            class="text-lg font-bold text-transparent bg-clip-text bg-gradient-to-r from-saar-red to-saar-green">
                            SAAR ASSURANCE
                        </h1>
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
                            <span
                                class="absolute -top-1 -right-1 h-4 w-4 bg-red-500 text-white rounded-full text-xs flex items-center justify-center">3</span>
                        </button>
                    </div>

                    <div class="relative">
                        <button onclick="toggleUserMenu()"
                            class="flex items-center space-x-2 p-2 text-gray-600 hover:text-saar-blue transition-colors">
                            <div
                                class="w-8 h-8 bg-gradient-to-r from-saar-blue to-blue-600 rounded-full flex items-center justify-center">
                                <span class="text-white text-sm font-semibold">JD</span>
                            </div>
                            <span class="text-sm font-medium">John Doe</span>
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
                                <a href="#"
                                    class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50">Déconnexion</a>
                            </div>
                        </div>
                    </div>
                </div>
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
                        <svg class="w-6 h-6 text-saar-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Sinistres</p>
                        <p class="text-2xl font-bold text-gray-900">156</p>
                    </div>
                </div>
            </div>

            <div
                class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 hover:shadow-xl transition-all duration-300">
                <div class="flex items-center">
                    <div class="p-3 rounded-xl bg-yellow-100">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">En Attente</p>
                        <p class="text-2xl font-bold text-gray-900">23</p>
                    </div>
                </div>
            </div>

            <div
                class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 hover:shadow-xl transition-all duration-300">
                <div class="flex items-center">
                    <div class="p-3 rounded-xl bg-green-100">
                        <svg class="w-6 h-6 text-saar-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                            </path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Traités</p>
                        <p class="text-2xl font-bold text-gray-900">98</p>
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
                        <p class="text-2xl font-bold text-gray-900">12</p>
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
                        <option value="1">Jean Dupont</option>
                        <option value="2">Marie Martin</option>
                        <option value="3">Paul Durand</option>
                        <option value="">Non assigné</option>
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
            <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                <div class="flex-1 flex justify-between sm:hidden">
                    <button
                        class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Précédent
                    </button>
                    <button
                        class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Suivant
                    </button>
                </div>
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700">
                            Affichage de <span class="font-medium">1</span> à <span class="font-medium">10</span> sur
                            <span class="font-medium">97</span> résultats
                        </p>
                    </div>
                    <div>
                        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                            <button
                                class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                <span class="sr-only">Précédent</span>
                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                            <button
                                class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                                1
                            </button>
                            <button
                                class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                                2
                            </button>
                            <button
                                class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                                3
                            </button>
                            <button
                                class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                <span class="sr-only">Suivant</span>
                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                        </nav>
                    </div>
                </div>
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
                        <option value="1">Jean Dupont</option>
                        <option value="2">Marie Martin</option>
                        <option value="3">Paul Durand</option>
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
        // Données simulées des sinistres
        const sinistresData = [{
                id: 1,
                numero_sinistre: 'SIN-2025-00001',
                nom_assure: 'Jean Dupont',
                email_assure: 'jean.dupont@email.com',
                telephone_assure: '+225 01 02 03 04 05',
                numero_police: 'POL-2024-789',
                date_sinistre: '2025-06-15',
                lieu_sinistre: 'Abidjan, Plateau',
                statut: 'en_attente',
                gestionnaire_id: null,
                gestionnaire_nom: null,
                jours_en_cours: 2,
                en_retard: false,
                montant_estime: 150000,
                circonstances: 'Collision avec un autre véhicule au carrefour'
            },
            {
                id: 2,
                numero_sinistre: 'SIN-2025-00002',
                nom_assure: 'Marie Koné',
                email_assure: 'marie.kone@email.com',
                telephone_assure: '+225 07 08 09 10 11',
                numero_police: 'POL-2024-456',
                date_sinistre: '2025-06-10',
                lieu_sinistre: 'Bouaké, Centre-ville',
                statut: 'en_cours',
                gestionnaire_id: 1,
                gestionnaire_nom: 'Jean Dupont',
                jours_en_cours: 7,
                en_retard: false,
                montant_estime: 75000,
                circonstances: 'Bris de glace par vandalisme'
            },
            {
                id: 3,
                numero_sinistre: 'SIN-2025-00003',
                nom_assure: 'Kouadio Paul',
                email_assure: 'paul.kouadio@email.com',
                telephone_assure: '+225 05 06 07 08 09',
                numero_police: 'POL-2024-123',
                date_sinistre: '2025-05-25',
                lieu_sinistre: 'Yamoussoukro, Autoroute',
                statut: 'expertise_requise',
                gestionnaire_id: 2,
                gestionnaire_nom: 'Marie Martin',
                jours_en_cours: 23,
                en_retard: true,
                montant_estime: 850000,
                circonstances: 'Accident grave avec plusieurs véhicules'
            },
            {
                id: 4,
                numero_sinistre: 'SIN-2025-00004',
                nom_assure: 'Fatou Traoré',
                email_assure: 'fatou.traore@email.com',
                telephone_assure: '+225 02 03 04 05 06',
                numero_police: 'POL-2024-678',
                date_sinistre: '2025-06-12',
                lieu_sinistre: 'San Pedro, Port',
                statut: 'regle',
                gestionnaire_id: 3,
                gestionnaire_nom: 'Paul Durand',
                jours_en_cours: 5,
                en_retard: false,
                montant_estime: 45000,
                circonstances: 'Rayure sur la carrosserie'
            },
            {
                id: 5,
                numero_sinistre: 'SIN-2025-00005',
                nom_assure: 'Ibrahim Ouattara',
                email_assure: 'ibrahim.ouattara@email.com',
                telephone_assure: '+225 09 10 11 12 13',
                numero_police: 'POL-2024-321',
                date_sinistre: '2025-06-01',
                lieu_sinistre: 'Daloa, Marché',
                statut: 'en_attente_documents',
                gestionnaire_id: 1,
                gestionnaire_nom: 'Jean Dupont',
                jours_en_cours: 16,
                en_retard: true,
                montant_estime: 120000,
                circonstances: 'Vol avec effraction'
            }
        ];

        let filteredData = [...sinistresData];
        let currentSinistreId = null;

        // Initialisation
        document.addEventListener('DOMContentLoaded', function() {
            loadSinistres();
            setupEventListeners();
        });

        function setupEventListeners() {
            // Recherche
            document.getElementById('search-input').addEventListener('input', debounce(handleSearch, 300));

            // Filtres
            document.getElementById('status-filter').addEventListener('change', handleFilter);
            document.getElementById('gestionnaire-filter').addEventListener('change', handleFilter);

            // Clic en dehors des menus pour les fermer
            document.addEventListener('click', function(event) {
                if (!event.target.closest('[onclick*="toggleUserMenu"]')) {
                    document.getElementById('user-menu').classList.add('hidden');
                }
            });
        }

        function loadSinistres() {
            const tbody = document.getElementById('sinistres-tbody');
            tbody.innerHTML = '';

            if (filteredData.length === 0) {
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
                return;
            }

            filteredData.forEach(sinistre => {
                const row = createSinistreRow(sinistre);
                tbody.appendChild(row);
            });
        }

        function createSinistreRow(sinistre) {
            const tr = document.createElement('tr');
            tr.className = 'hover:bg-gray-50 transition-colors';

            const statusBadge = getStatusBadge(sinistre.statut);
            const urgencyIndicator = sinistre.en_retard ?
                '<span class="inline-block w-2 h-2 bg-red-500 rounded-full animate-pulse mr-2"></span>' :
                (sinistre.jours_en_cours > 10 ?
                    '<span class="inline-block w-2 h-2 bg-yellow-500 rounded-full mr-2"></span>' : '');

            tr.innerHTML = `
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
                    <div class="text-sm text-gray-900">${formatDate(sinistre.date_sinistre)}</div>
                    <div class="text-sm text-gray-500">${sinistre.jours_en_cours} jour(s)</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    ${statusBadge}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">
                        ${sinistre.gestionnaire_nom || '<span class="text-gray-400 italic">Non assigné</span>'}
                    </div>
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
            `;

            return tr;
        }

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
            const date = new Date(dateString);
            return date.toLocaleDateString('fr-FR', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
            });
        }

        function formatCurrency(amount) {
            return new Intl.NumberFormat('fr-FR', {
                style: 'currency',
                currency: 'XOF',
                minimumFractionDigits: 0
            }).format(amount);
        }

        // Fonctions de recherche et filtrage
        function handleSearch(event) {
            const searchTerm = event.target.value.toLowerCase().trim();
            applyFilters();
        }

        function handleFilter() {
            applyFilters();
        }

        function applyFilters() {
            const searchTerm = document.getElementById('search-input').value.toLowerCase().trim();
            const statusFilter = document.getElementById('status-filter').value;
            const gestionnaireFilter = document.getElementById('gestionnaire-filter').value;

            filteredData = sinistresData.filter(sinistre => {
                // Recherche textuelle
                const matchesSearch = !searchTerm ||
                    sinistre.numero_sinistre.toLowerCase().includes(searchTerm) ||
                    sinistre.nom_assure.toLowerCase().includes(searchTerm) ||
                    sinistre.telephone_assure.includes(searchTerm) ||
                    sinistre.numero_police.toLowerCase().includes(searchTerm);

                // Filtre par statut
                const matchesStatus = !statusFilter || sinistre.statut === statusFilter;

                // Filtre par gestionnaire
                const matchesGestionnaire = !gestionnaireFilter ||
                    (gestionnaireFilter === '' && !sinistre.gestionnaire_id) ||
                    sinistre.gestionnaire_id == gestionnaireFilter;

                return matchesSearch && matchesStatus && matchesGestionnaire;
            });

            loadSinistres();
        }

        function resetFilters() {
            document.getElementById('search-input').value = '';
            document.getElementById('status-filter').value = '';
            document.getElementById('gestionnaire-filter').value = '';
            filteredData = [...sinistresData];
            loadSinistres();
        }

        // Fonctions des modals
        function showAssignModal(sinistreId) {
            const sinistre = sinistresData.find(s => s.id === sinistreId);
            if (!sinistre) return;

            currentSinistreId = sinistreId;
            document.getElementById('assign-sinistre-info').textContent =
                `${sinistre.numero_sinistre} - ${sinistre.nom_assure}`;
            document.getElementById('assign-gestionnaire').value = sinistre.gestionnaire_id || '';
            document.getElementById('assign-modal').classList.remove('hidden');
        }

        function showStatusModal(sinistreId) {
            const sinistre = sinistresData.find(s => s.id === sinistreId);
            if (!sinistre) return;

            currentSinistreId = sinistreId;
            document.getElementById('status-sinistre-info').textContent =
                `${sinistre.numero_sinistre} - ${sinistre.nom_assure}`;
            document.getElementById('new-status').value = sinistre.statut;
            document.getElementById('status-comment').value = '';
            document.getElementById('status-modal').classList.remove('hidden');
        }

        function showDetails(sinistreId) {
            const sinistre = sinistresData.find(s => s.id === sinistreId);
            if (!sinistre) return;

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
                                <p><span class="font-medium">Gestionnaire:</span> ${sinistre.gestionnaire_nom || 'Non assigné'}</p>
                                <p><span class="font-medium">Jours en cours:</span> ${sinistre.jours_en_cours}</p>
                                <p><span class="font-medium">En retard:</span> ${sinistre.en_retard ? '⚠️ Oui' : '✅ Non'}</p>
                                <p><span class="font-medium">Montant estimé:</span> ${formatCurrency(sinistre.montant_estime)}</p>
                            </div>
                        </div>

                        <div class="bg-yellow-50 p-4 rounded-lg">
                            <h4 class="font-semibold text-gray-900 mb-3">Circonstances</h4>
                            <p class="text-sm text-gray-700">${sinistre.circonstances}</p>
                        </div>
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
                        <button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            Voir Documents
                        </button>
                    </div>
                </div>
            `;

            document.getElementById('details-modal').classList.remove('hidden');
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
            currentSinistreId = null;
        }

        function confirmAssignment() {
            const gestionnaireId = document.getElementById('assign-gestionnaire').value;
            if (!gestionnaireId || !currentSinistreId) return;

            // Simuler l'affectation
            const sinistre = sinistresData.find(s => s.id === currentSinistreId);
            if (sinistre) {
                if (gestionnaireId === 'self') {
                    sinistre.gestionnaire_id = 'current_user_id'; // ID de l'utilisateur connecté
                    sinistre.gestionnaire_nom = 'Moi-même';
                } else {
                    sinistre.gestionnaire_id = parseInt(gestionnaireId);
                    const gestionnaires = {
                        1: 'Jean Dupont',
                        2: 'Marie Martin',
                        3: 'Paul Durand'
                    };
                    sinistre.gestionnaire_nom = gestionnaires[gestionnaireId];
                }

                if (sinistre.statut === 'en_attente') {
                    sinistre.statut = 'en_cours';
                }
            }

            showSuccessMessage('Gestionnaire affecté avec succès');
            closeModal('assign-modal');
            applyFilters();
        }

        function confirmStatusChange() {
            const newStatus = document.getElementById('new-status').value;
            const comment = document.getElementById('status-comment').value;

            if (!newStatus || !currentSinistreId) return;

            // Simuler le changement de statut
            const sinistre = sinistresData.find(s => s.id === currentSinistreId);
            if (sinistre) {
                sinistre.statut = newStatus;
                // Ici vous pourriez aussi enregistrer le commentaire dans l'historique
            }

            showSuccessMessage('Statut modifié avec succès');
            closeModal('status-modal');
            applyFilters();
        }

        // Fonctions utilitaires
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

        function showSuccessMessage(message) {
            const notification = document.createElement('div');
            notification.className =
                'fixed top-4 right-4 bg-green-500 text-white px-6 py-4 rounded-lg shadow-lg z-50 animate-slide-in';
            notification.innerHTML = `
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span>${message}</span>
                </div>
            `;

            document.body.appendChild(notification);

            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 3000);
        }

        function toggleUserMenu() {
            const menu = document.getElementById('user-menu');
            menu.classList.toggle('hidden');
        }
    </script>
</body>

</html>
