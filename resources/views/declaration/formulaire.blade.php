<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulaire de Déclaration - SAAR Assurance</title>
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
                        }
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-gradient-to-br from-red-50 via-white to-green-50 min-h-screen">

    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="container mx-auto px-4 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div
                        class="w-12 h-12 bg-gradient-to-r from-saar-red to-red-500 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                            </path>
                        </svg>
                    </div>
                    <div>
                        <h1
                            class="text-xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-saar-red to-saar-green">
                            SAAR ASSURANCE
                        </h1>
                        <p class="text-sm text-gray-600">Déclaration de Sinistre</p>
                    </div>
                </div>
                <a href="/" class="text-gray-600 hover:text-saar-blue transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                        </path>
                    </svg>
                </a>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 py-8">

        <!-- Barre de progression -->
        <div class="max-w-4xl mx-auto mb-8">
            <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-bold text-gray-900">Processus de Déclaration</h2>
                    <span class="text-sm text-gray-500 bg-gray-100 px-3 py-1 rounded-full">
                        Étape <span id="current-step-text">1</span> sur 4
                    </span>
                </div>

                <div class="w-full bg-gray-200 rounded-full h-3 mb-4">
                    <div id="progress-bar"
                        class="bg-gradient-to-r from-saar-red to-red-500 h-3 rounded-full transition-all duration-500 ease-out"
                        style="width: 25%"></div>
                </div>

                <div class="flex justify-between text-xs">
                    <span id="step-1-label" class="text-saar-red font-semibold">✓ Informations</span>
                    <span id="step-2-label" class="text-gray-500">Sinistre</span>
                    <span id="step-3-label" class="text-gray-500">Documents</span>
                    <span id="step-4-label" class="text-gray-500">Confirmation</span>
                </div>
            </div>
        </div>

        <!-- Formulaire -->
        <div class="max-w-4xl mx-auto">
            <form id="declaration-form" class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">

                <!-- Étape 1: Informations personnelles -->
                <div id="step-1" class="step-content p-8 animate-slide-in">
                    <div class="text-center mb-8">
                        <div
                            class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-r from-blue-500 to-saar-blue rounded-2xl mb-4">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">Vos Informations</h3>
                        <p class="text-gray-600">Commençons par vos informations personnelles</p>
                    </div>

                    <div class="grid md:grid-cols-2 gap-6">
                        <div class="form-group">
                            <label class="block text-sm font-semibold text-gray-700 mb-3">
                                Nom complet *
                            </label>
                            <input type="text" name="nom_assure" required
                                class="w-full px-4 py-4 border-2 border-gray-200 rounded-xl focus:border-saar-blue focus:ring-4 focus:ring-blue-100 transition-all duration-300 text-gray-900"
                                placeholder="Votre nom complet">
                        </div>

                        <div class="form-group">
                            <label class="block text-sm font-semibold text-gray-700 mb-3">
                                Adresse email
                            </label>
                            <input type="email" name="email_assure"
                                class="w-full px-4 py-4 border-2 border-gray-200 rounded-xl focus:border-saar-blue focus:ring-4 focus:ring-blue-100 transition-all duration-300 text-gray-900"
                                placeholder="votre@email.com">
                        </div>

                        <div class="form-group">
                            <label class="block text-sm font-semibold text-gray-700 mb-3">
                                Numéro de téléphone *
                            </label>
                            <input type="tel" name="telephone_assure" required
                                class="w-full px-4 py-4 border-2 border-gray-200 rounded-xl focus:border-saar-blue focus:ring-4 focus:ring-blue-100 transition-all duration-300 text-gray-900"
                                placeholder="+225 XX XX XX XX XX">
                        </div>

                        <div class="form-group">
                            <label class="block text-sm font-semibold text-gray-700 mb-3">
                                Numéro attestation d'assurance *
                            </label>
                            <input type="text" name="numero_police" required
                                class="w-full px-4 py-4 border-2 border-gray-200 rounded-xl focus:border-saar-blue focus:ring-4 focus:ring-blue-100 transition-all duration-300 text-gray-900"
                                placeholder="Numéro de votre police">
                        </div>
                    </div>
                </div>

                <!-- Étape 2: Détails du sinistre -->
                <div id="step-2" class="step-content hidden p-8">
                    <div class="text-center mb-8">
                        <div
                            class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-r from-red-500 to-saar-red rounded-2xl mb-4">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">Détails du Sinistre</h3>
                        <p class="text-gray-600">Décrivez les circonstances de l'accident</p>
                    </div>

                    <div class="space-y-6">
                        <div class="grid md:grid-cols-2 gap-6">
                            <div class="form-group">
                                <label class="block text-sm font-semibold text-gray-700 mb-3">
                                    Date du sinistre *
                                </label>
                                <input type="date" name="date_sinistre" required
                                    class="w-full px-4 py-4 border-2 border-gray-200 rounded-xl focus:border-saar-blue focus:ring-4 focus:ring-blue-100 transition-all duration-300">
                            </div>

                            <div class="form-group">
                                <label class="block text-sm font-semibold text-gray-700 mb-3">
                                    Lieu du sinistre *
                                </label>
                                <input type="text" name="lieu_sinistre" required
                                    class="w-full px-4 py-4 border-2 border-gray-200 rounded-xl focus:border-saar-blue focus:ring-4 focus:ring-blue-100 transition-all duration-300"
                                    placeholder="Ville, quartier, rue...">
                            </div>
                        </div>

                        <div class="grid md:grid-cols-2 gap-6">
                            <div class="form-group">
                                <label class="block text-sm font-semibold text-gray-700 mb-3">
                                    Heure du sinistre *
                                </label>
                                <input type="time" name="heure_sinistre" required
                                    class="w-full px-4 py-4 border-2 border-gray-200 rounded-xl focus:border-saar-blue focus:ring-4 focus:ring-blue-100 transition-all duration-300">
                            </div>

                            <div class="form-group">
                                <label class="block text-sm font-semibold text-gray-700 mb-3">
                                    Nom du conducteur au moment du sinistre *
                                </label>
                                <input type="text" name="conducteur_nom" required
                                    class="w-full px-4 py-4 border-2 border-gray-200 rounded-xl focus:border-saar-blue focus:ring-4 focus:ring-blue-100 transition-all duration-300"
                                    placeholder="Nom du conducteur">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="block text-sm font-semibold text-gray-700 mb-3">
                                Circonstances du sinistre *
                            </label>
                            <textarea name="circonstances" required rows="4"
                                class="w-full px-4 py-4 border-2 border-gray-200 rounded-xl focus:border-saar-blue focus:ring-4 focus:ring-blue-100 transition-all duration-300 resize-none"
                                placeholder="Décrivez en détail les circonstances de l'accident..."></textarea>
                        </div>

                        <!-- Section constat -->
                        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-6 rounded-xl border border-blue-200">
                            <div class="flex items-center mb-4">
                                <input type="checkbox" name="constat_autorite" id="constat"
                                    class="h-5 w-5 text-saar-blue focus:ring-blue-500 border-gray-300 rounded">
                                <label for="constat" class="ml-3 text-sm font-semibold text-gray-700">
                                    Un constat a été établi par les autorités (Police, Gendarmerie)
                                </label>
                            </div>

                            <div id="constat-details" class="hidden grid md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Nom de l'officier
                                    </label>
                                    <input type="text" name="officier_nom"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:border-saar-blue focus:ring-2 focus:ring-blue-100 transition-all">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Commissariat/Brigade
                                    </label>
                                    <input type="text" name="commissariat"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:border-saar-blue focus:ring-2 focus:ring-blue-100 transition-all">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="block text-sm font-semibold text-gray-700 mb-3">
                                Dommages relevés sur le véhicule
                            </label>
                            <textarea name="dommages_releves" rows="3"
                                class="w-full px-4 py-4 border-2 border-gray-200 rounded-xl focus:border-saar-blue focus:ring-4 focus:ring-blue-100 transition-all duration-300 resize-none"
                                placeholder="Décrivez les dommages visibles sur votre véhicule..."></textarea>
                        </div>
                    </div>
                </div>

                <!-- Étape 3: Documents -->
                <div id="step-3" class="step-content hidden p-8">
                    <div class="text-center mb-8">
                        <div
                            class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-r from-green-500 to-saar-green rounded-2xl mb-4">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">Documents Requis</h3>
                        <p class="text-gray-600">Téléchargez les documents nécessaires pour votre dossier</p>
                    </div>

                    <div class="space-y-8">
                        <!-- Documents obligatoires -->
                        <div>
                            <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                <span
                                    class="w-6 h-6 bg-red-500 text-white rounded-full flex items-center justify-center text-xs mr-2">!</span>
                                Documents Obligatoires
                            </h4>

                            <div class="grid md:grid-cols-2 gap-6">
                                <!-- Carte grise recto -->
                                <div class="upload-zone" data-field="carte_grise_recto">
                                    <label class="block text-sm font-medium text-gray-700 mb-3">
                                        Carte grise (Recto) *
                                    </label>
                                    <div
                                        class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-saar-blue hover:bg-blue-50 transition-all duration-300 cursor-pointer">
                                        <input type="file" accept=".pdf,.jpg,.jpeg,.png" required class="hidden">
                                        <div class="upload-content">
                                            <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" stroke="currentColor"
                                                fill="none" viewBox="0 0 48 48">
                                                <path
                                                    d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                                    stroke-width="2" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                            </svg>
                                            <span class="text-sm text-saar-blue font-semibold">Cliquer pour
                                                télécharger</span>
                                            <p class="text-xs text-gray-500 mt-1">PDF, JPG, PNG (max 5MB)</p>
                                        </div>
                                        <div class="upload-success hidden">
                                            <svg class="mx-auto h-12 w-12 text-green-500 mb-2" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            <p class="text-sm text-green-600 font-semibold">Fichier téléchargé ✓</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Carte grise verso -->
                                <div class="upload-zone" data-field="carte_grise_verso">
                                    <label class="block text-sm font-medium text-gray-700 mb-3">
                                        Carte grise (Verso) *
                                    </label>
                                    <div
                                        class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-saar-blue hover:bg-blue-50 transition-all duration-300 cursor-pointer">
                                        <input type="file" accept=".pdf,.jpg,.jpeg,.png" required class="hidden">
                                        <div class="upload-content">
                                            <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" stroke="currentColor"
                                                fill="none" viewBox="0 0 48 48">
                                                <path
                                                    d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                                    stroke-width="2" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                            </svg>
                                            <span class="text-sm text-saar-blue font-semibold">Cliquer pour
                                                télécharger</span>
                                            <p class="text-xs text-gray-500 mt-1">PDF, JPG, PNG (max 5MB)</p>
                                        </div>
                                        <div class="upload-success hidden">
                                            <svg class="mx-auto h-12 w-12 text-green-500 mb-2" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            <p class="text-sm text-green-600 font-semibold">Fichier téléchargé ✓</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Visite technique recto -->
                                <div class="upload-zone" data-field="visite_technique_recto">
                                    <label class="block text-sm font-medium text-gray-700 mb-3">Visite technique
                                        (Recto) *</label>
                                    <div
                                        class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-saar-blue transition-all cursor-pointer">
                                        <input type="file" accept=".pdf,.jpg,.jpeg,.png" required class="hidden">
                                        <span class="text-sm text-saar-blue font-semibold">Télécharger</span>
                                        <div class="upload-success hidden text-xs text-green-600 mt-1">✓ Téléchargé
                                        </div>
                                    </div>
                                </div>

                                <!-- Visite technique verso -->
                                <div class="upload-zone" data-field="visite_technique_verso">
                                    <label class="block text-sm font-medium text-gray-700 mb-3">Visite technique
                                        (Verso) *</label>
                                    <div
                                        class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-saar-blue transition-all cursor-pointer">
                                        <input type="file" accept=".pdf,.jpg,.jpeg,.png" required class="hidden">
                                        <span class="text-sm text-saar-blue font-semibold">Télécharger</span>
                                        <div class="upload-success hidden text-xs text-green-600 mt-1">✓ Téléchargé
                                        </div>
                                    </div>
                                </div>

                                <!-- Attestation d'assurance -->
                                <div class="upload-zone" data-field="attestation_assurance">
                                    <label class="block text-sm font-medium text-gray-700 mb-3">Attestation d'assurance
                                        *</label>
                                    <div
                                        class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-saar-blue transition-all cursor-pointer">
                                        <input type="file" accept=".pdf,.jpg,.jpeg,.png" required class="hidden">
                                        <span class="text-sm text-saar-blue font-semibold">Télécharger</span>
                                        <div class="upload-success hidden text-xs text-green-600 mt-1">✓ Téléchargé
                                        </div>
                                    </div>
                                </div>

                                <!-- Permis de conduire -->
                                <div class="upload-zone" data-field="permis_conduire">
                                    <label class="block text-sm font-medium text-gray-700 mb-3">Permis de conduire
                                        *</label>
                                    <div
                                        class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-saar-blue transition-all cursor-pointer">
                                        <input type="file" accept=".pdf,.jpg,.jpeg,.png" required class="hidden">
                                        <span class="text-sm text-saar-blue font-semibold">Télécharger</span>
                                        <div class="upload-success hidden text-xs text-green-600 mt-1">✓ Téléchargé
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Photos du véhicule -->
                        <div>
                            <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                <span
                                    class="w-6 h-6 bg-blue-500 text-white rounded-full flex items-center justify-center text-xs mr-2">📷</span>
                                Photos du Véhicule
                            </h4>

                            <div class="upload-zone" data-field="photos_vehicule">
                                <div
                                    class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center hover:border-saar-blue hover:bg-blue-50 transition-all duration-300 cursor-pointer">
                                    <input type="file" accept=".jpg,.jpeg,.png" multiple class="hidden">
                                    <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" stroke="currentColor"
                                        fill="none" viewBox="0 0 48 48">
                                        <path
                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h36a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V8a2 2 0 012-2h2m2 0h.01m2 0h.01m2 0h.01m2 0h.01M10 12l8 8 8-8"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="upload-content">
                                        <span class="text-lg text-saar-blue font-semibold">Télécharger les
                                            photos</span>
                                        <p class="text-sm text-gray-500 mt-2">JPG, PNG - Plusieurs fichiers acceptés
                                        </p>
                                        <p class="text-xs text-gray-400 mt-1">Recommandé: photos des dommages sous
                                            différents angles</p>
                                    </div>
                                    <div class="upload-success hidden">
                                        <p class="text-sm text-green-600 font-semibold"><span
                                                class="photo-count">0</span> photo(s) téléchargée(s) ✓</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Étape 4: Confirmation -->
                <div id="step-4" class="step-content hidden p-8">
                    <div class="text-center mb-8">
                        <div
                            class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-r from-purple-500 to-purple-600 rounded-2xl mb-4">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">Vérification et Envoi</h3>
                        <p class="text-gray-600">Vérifiez vos informations avant l'envoi final</p>
                    </div>

                    <div class="bg-gradient-to-r from-gray-50 to-blue-50 rounded-xl p-6 mb-8 border border-gray-200">
                        <h4 class="font-bold text-gray-900 mb-6 text-lg">📋 Récapitulatif de votre déclaration</h4>

                        <div class="grid md:grid-cols-2 gap-8 text-sm">
                            <div class="space-y-3">
                                <p><strong>Nom:</strong> <span id="recap-nom" class="text-gray-700"></span></p>
                                <p><strong>Email:</strong> <span id="recap-email" class="text-gray-700"></span></p>
                                <p><strong>Téléphone:</strong> <span id="recap-telephone"
                                        class="text-gray-700"></span></p>
                                <p><strong>N° Police:</strong> <span id="recap-police" class="text-gray-700"></span>
                                </p>
                            </div>
                            <div class="space-y-3">
                                <p><strong>Date sinistre:</strong> <span id="recap-date" class="text-gray-700"></span>
                                </p>
                                <p><strong>Lieu:</strong> <span id="recap-lieu" class="text-gray-700"></span></p>
                                <p><strong>Conducteur:</strong> <span id="recap-conducteur"
                                        class="text-gray-700"></span></p>
                            </div>
                        </div>
                    </div>

                    <div
                        class="bg-gradient-to-r from-blue-50 to-indigo-50 border-l-4 border-saar-blue p-6 mb-8 rounded-r-xl">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-saar-blue" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h4 class="text-lg font-semibold text-saar-blue mb-2">Information importante</h4>
                                <p class="text-sm text-blue-700 leading-relaxed">
                                    En soumettant ce formulaire, vous confirmez que toutes les informations fournies
                                    sont exactes et complètes.
                                    Vous recevrez un email de confirmation avec votre numéro de sinistre dans les
                                    minutes qui suivent.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white border-2 border-gray-200 rounded-xl p-6">
                        <div class="flex items-start">
                            <input type="checkbox" id="accept-conditions" required
                                class="h-5 w-5 text-saar-blue focus:ring-blue-500 border-gray-300 rounded mt-1">
                            <label for="accept-conditions" class="ml-3 text-sm text-gray-700 leading-relaxed">
                                <strong>J'accepte les conditions générales</strong> et je certifie sur l'honneur que
                                toutes les informations fournies dans cette déclaration sont exactes et complètes. Je
                                comprends que toute fausse déclaration peut entraîner la nullité de mon contrat
                                d'assurance. *
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Navigation -->
                <div
                    class="bg-gradient-to-r from-gray-50 to-gray-100 px-8 py-6 flex justify-between items-center border-t">
                    <button type="button" id="prev-btn"
                        class="hidden px-6 py-3 border-2 border-gray-300 rounded-xl text-gray-700 font-semibold hover:bg-gray-50 hover:border-gray-400 transition-all duration-300 flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 19l-7-7 7-7"></path>
                        </svg>
                        Précédent
                    </button>

                    <div class="flex space-x-4 ml-auto">
                        <button type="button" id="next-btn"
                            class="px-8 py-3 bg-gradient-to-r from-saar-blue to-blue-600 text-white font-bold rounded-xl hover:from-blue-600 hover:to-blue-700 transition-all duration-300 transform hover:scale-105 shadow-lg flex items-center">
                            Suivant
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5l7 7-7 7"></path>
                            </svg>
                        </button>

                        <button type="submit" id="submit-btn"
                            class="hidden px-8 py-4 bg-gradient-to-r from-saar-green to-green-600 text-white font-bold rounded-xl hover:from-green-600 hover:to-green-700 transition-all duration-300 transform hover:scale-105 shadow-xl disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                            </svg>
                            <span class="submit-text">Envoyer ma déclaration</span>
                            <span class="loading-text hidden">Envoi en cours...</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        let currentStep = 1;
        const totalSteps = 4;
        const uploadedFiles = {};

        document.addEventListener('DOMContentLoaded', function() {
            initializeForm();
            setupEventListeners();
            updateProgressBar();
        });

        function initializeForm() {
            for (let i = 2; i <= totalSteps; i++) {
                document.getElementById(`step-${i}`).classList.add('hidden');
            }

            const today = new Date().toISOString().split('T')[0];
            document.querySelector('input[name="date_sinistre"]').setAttribute('max', today);
        }

        function setupEventListeners() {
            document.getElementById('next-btn').addEventListener('click', nextStep);
            document.getElementById('prev-btn').addEventListener('click', prevStep);

            document.getElementById('declaration-form').addEventListener('submit', handleSubmit);

            document.getElementById('constat').addEventListener('change', function() {
                const details = document.getElementById('constat-details');
                if (this.checked) {
                    details.classList.remove('hidden');
                    details.classList.add('animate-fade-in');
                } else {
                    details.classList.add('hidden');
                    details.classList.remove('animate-fade-in');
                }
            });

            setupFileUploads();
        }

        function setupFileUploads() {
            const uploadZones = document.querySelectorAll('.upload-zone');

            uploadZones.forEach(zone => {
                const input = zone.querySelector('input[type="file"]');
                const uploadArea = zone.querySelector('.border-dashed');

                uploadArea.addEventListener('dragover', (e) => {
                    e.preventDefault();
                    uploadArea.classList.add('border-saar-blue', 'bg-blue-50');
                });

                uploadArea.addEventListener('dragleave', (e) => {
                    e.preventDefault();
                    uploadArea.classList.remove('border-saar-blue', 'bg-blue-50');
                });

                uploadArea.addEventListener('drop', (e) => {
                    e.preventDefault();
                    uploadArea.classList.remove('border-saar-blue', 'bg-blue-50');

                    const files = e.dataTransfer.files;
                    if (files.length > 0) {
                        input.files = files;
                        handleFileUpload(zone, files);
                    }
                });

                uploadArea.addEventListener('click', () => {
                    input.click();
                });

                input.addEventListener('change', (e) => {
                    handleFileUpload(zone, e.target.files);
                });
            });
        }

        function handleFileUpload(zone, files) {
            const field = zone.dataset.field;
            const uploadContent = zone.querySelector('.upload-content');
            const uploadSuccess = zone.querySelector('.upload-success');

            if (files.length > 0) {
                for (let file of files) {
                    if (file.size > 5 * 1024 * 1024) {
                        alert('Le fichier est trop volumineux. Taille maximum: 5MB');
                        return;
                    }
                }

                uploadedFiles[field] = files;

                uploadContent.classList.add('hidden');
                uploadSuccess.classList.remove('hidden');
                uploadSuccess.classList.add('animate-bounce-in');

                if (field === 'photos_vehicule') {
                    const photoCount = uploadSuccess.querySelector('.photo-count');
                    if (photoCount) {
                        photoCount.textContent = files.length;
                    }
                }

                zone.querySelector('.border-dashed').classList.add('border-green-300', 'bg-green-50');
            }
        }

        function nextStep() {
            if (validateCurrentStep()) {
                if (currentStep < totalSteps) {
                    document.getElementById(`step-${currentStep}`).classList.add('hidden');

                    currentStep++;
                    const nextStepElement = document.getElementById(`step-${currentStep}`);
                    nextStepElement.classList.remove('hidden');
                    nextStepElement.classList.add('animate-slide-in');

                    updateProgressBar();
                    updateNavigation();

                    if (currentStep === 4) {
                        updateRecap();
                    }
                }
            }
        }

        function prevStep() {
            if (currentStep > 1) {
                document.getElementById(`step-${currentStep}`).classList.add('hidden');

                currentStep--;
                const prevStepElement = document.getElementById(`step-${currentStep}`);
                prevStepElement.classList.remove('hidden');
                prevStepElement.classList.add('animate-slide-in');

                updateProgressBar();
                updateNavigation();
            }
        }

        function validateCurrentStep() {
            const currentStepElement = document.getElementById(`step-${currentStep}`);
            const requiredFields = currentStepElement.querySelectorAll('[required]');

            let isValid = true;

            requiredFields.forEach(field => {
                if (field.type === 'file') {
                    const zone = field.closest('.upload-zone');
                    if (zone) {
                        const fieldName = zone.dataset.field;
                        if (!uploadedFiles[fieldName] || uploadedFiles[fieldName].length === 0) {
                            zone.classList.add('border-red-500', 'bg-red-50');
                            isValid = false;
                        } else {
                            zone.classList.remove('border-red-500', 'bg-red-50');
                        }
                    }
                } else {
                    if (!field.value.trim()) {
                        field.classList.add('border-red-500', 'bg-red-50');
                        field.addEventListener('input', function() {
                            this.classList.remove('border-red-500', 'bg-red-50');
                        }, {
                            once: true
                        });
                        isValid = false;
                    } else {
                        field.classList.remove('border-red-500', 'bg-red-50');
                    }
                }
            });

            if (!isValid) {
                currentStepElement.classList.add('animate-bounce-in');
                setTimeout(() => {
                    currentStepElement.classList.remove('animate-bounce-in');
                }, 600);

                if (currentStep === 3) {
                    alert('Veuillez télécharger tous les documents obligatoires.');
                } else {
                    alert('Veuillez remplir tous les champs obligatoires.');
                }
            }

            return isValid;
        }

        function updateProgressBar() {
            const progressBar = document.getElementById('progress-bar');
            const currentStepText = document.getElementById('current-step-text');
            const percentage = (currentStep / totalSteps) * 100;

            progressBar.style.width = `${percentage}%`;
            currentStepText.textContent = currentStep;

            for (let i = 1; i <= totalSteps; i++) {
                const label = document.getElementById(`step-${i}-label`);
                if (i < currentStep) {
                    label.className = 'text-saar-green font-semibold';
                    if (!label.textContent.includes('✓')) {
                        label.textContent = '✓ ' + label.textContent;
                    }
                } else if (i === currentStep) {
                    label.className = 'text-saar-red font-semibold';
                    label.textContent = label.textContent.replace('✓ ', '');
                } else {
                    label.className = 'text-gray-500';
                    label.textContent = label.textContent.replace('✓ ', '');
                }
            }
        }

        function updateNavigation() {
            const prevBtn = document.getElementById('prev-btn');
            const nextBtn = document.getElementById('next-btn');
            const submitBtn = document.getElementById('submit-btn');

            if (currentStep > 1) {
                prevBtn.classList.remove('hidden');
            } else {
                prevBtn.classList.add('hidden');
            }

            if (currentStep < totalSteps) {
                nextBtn.classList.remove('hidden');
                submitBtn.classList.add('hidden');
            } else {
                nextBtn.classList.add('hidden');
                submitBtn.classList.remove('hidden');
            }
        }

        function updateRecap() {

            const form = document.getElementById('declaration-form');
            const formData = new FormData(form);

            document.getElementById('recap-nom').textContent = formData.get('nom_assure') || '';
            document.getElementById('recap-email').textContent = formData.get('email_assure') || '';
            document.getElementById('recap-telephone').textContent = formData.get('telephone_assure') || '';
            document.getElementById('recap-police').textContent = formData.get('numero_police') || '';
            document.getElementById('recap-date').textContent = formData.get('date_sinistre') || '';
            document.getElementById('recap-lieu').textContent = formData.get('lieu_sinistre') || '';
            document.getElementById('recap-conducteur').textContent = formData.get('conducteur_nom') || '';
        }

        function handleSubmit(e) {
            e.preventDefault();

            const submitBtn = document.getElementById('submit-btn');
            const submitText = submitBtn.querySelector('.submit-text');
            const loadingText = submitBtn.querySelector('.loading-text');
            const acceptConditions = document.getElementById('accept-conditions');

            if (!acceptConditions.checked) {
                alert('Veuillez accepter les conditions générales pour continuer.');
                return;
            }

            submitBtn.disabled = true;
            submitText.classList.add('hidden');
            loadingText.classList.remove('hidden');
            submitBtn.classList.add('animate-pulse');

            const formData = new FormData(document.getElementById('declaration-form'));

            Object.keys(uploadedFiles).forEach(key => {
                if (key === 'photos_vehicule') {
                    Array.from(uploadedFiles[key]).forEach((file, index) => {
                        formData.append(`photos_vehicule[${index}]`, file);
                    });
                } else if (uploadedFiles[key] && uploadedFiles[key].length > 0) {

                    formData.append(key, uploadedFiles[key][0]);
                }
            });

            const constatCheckbox = document.getElementById('constat');
            formData.set('constat_autorite', constatCheckbox.checked ? '1' : '0');

            console.log('FormData contents:');
            for (let [key, value] of formData.entries()) {
                console.log(key + ':', value);
            }

            const submitUrl = '/declaration/store';

            fetch(submitUrl, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    }
                })
                .then(async response => {
                    const contentType = response.headers.get('content-type');

                    if (contentType && contentType.includes('text/html')) {
                        const htmlText = await response.text();
                        console.error('Received HTML instead of JSON:', htmlText.substring(0, 500));
                        throw new Error(
                            'Le serveur a retourné du HTML au lieu de JSON. Vérifiez la route et le contrôleur.'
                        );
                    }

                    if (!response.ok) {
                        const errorText = await response.text();
                        console.error('Error response:', errorText);
                        throw new Error(`Erreur ${response.status}: ${response.statusText}`);
                    }

                    return response.json();
                })
                .then(data => {
                    console.log('Success response:');

                    if (data.success) {
                        showSuccessPage(data);
                    } else {
                        throw new Error(data.message || 'Erreur lors de la soumission');
                    }
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                    showErrorMessage(error.message);

                    submitBtn.disabled = false;
                    submitText.classList.remove('hidden');
                    loadingText.classList.add('hidden');
                    submitBtn.classList.remove('animate-pulse');
                });
        }

        function showSuccessPage(data) {
            document.body.innerHTML = `
                <div class="min-h-screen bg-gradient-to-br from-green-50 to-blue-50 flex items-center justify-center p-4">
                    <div class="max-w-lg w-full bg-white rounded-2xl shadow-2xl p-8 text-center animate-bounce-in">
                        <div class="w-20 h-20 bg-gradient-to-r from-saar-green to-green-600 rounded-full flex items-center justify-center mx-auto mb-6">
                            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-900 mb-4">Déclaration Envoyée !</h2>
                        <p class="text-gray-600 mb-2">Votre sinistre a été déclaré avec succès.</p>
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                            <p class="text-sm text-blue-800 font-semibold mb-1">Numéro de référence:</p>
                            <p class="text-lg font-bold text-blue-900">${data.numero_sinistre || 'SIN-' + Date.now()}</p>
                        </div>
                        <p class="text-sm text-gray-600 mb-6">Vous recevrez un email de confirmation avec tous les détails de votre dossier.</p>
                        <div class="flex flex-col sm:flex-row gap-3 justify-center">
                            <a href="${data.redirect_url || '/dashboard'}" class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-saar-blue to-blue-600 text-white font-semibold rounded-xl hover:from-blue-600 hover:to-blue-700 transition-all duration-300 transform hover:scale-105">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Voir ma déclaration
                            </a>
                            <a href="/" class="inline-flex items-center justify-center px-6 py-3 border-2 border-gray-300 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 hover:border-gray-400 transition-all duration-300">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                </svg>
                                Retour à l'accueil
                            </a>
                        </div>
                    </div>
                </div>
            `;
        }

        function showErrorMessage(message) {
            const existingError = document.querySelector('.error-notification');
            if (existingError) {
                existingError.remove();
            }

            const errorDiv = document.createElement('div');
            errorDiv.className =
                'error-notification fixed top-4 right-4 bg-red-500 text-white px-6 py-4 rounded-lg shadow-lg z-50 animate-slide-in max-w-md';
            errorDiv.innerHTML = `
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L4.268 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                    <span class="flex-1">${message}</span>
                    <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-white hover:text-gray-200 flex-shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            `;

            document.body.appendChild(errorDiv);

            setTimeout(() => {
                if (errorDiv.parentNode) {
                    errorDiv.parentNode.removeChild(errorDiv);
                }
            }, 8000);
        }

        document.addEventListener('DOMContentLoaded', function() {
            const inputs = document.querySelectorAll('input, textarea');
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.classList.add('animate-pulse');
                });
                input.addEventListener('blur', function() {
                    this.parentElement.classList.remove('animate-pulse');
                });
            });
        });
    </script>
</body>

</html>
