{{-- resources/views/declaration/confirmation.blade.php --}}
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation de Déclaration - SAAR Assurance</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gradient-to-br from-green-50 via-white to-blue-50 min-h-screen">

    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="container mx-auto px-4 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div
                        class="w-12 h-12 bg-gradient-to-r from-red-500 to-red-600 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                            </path>
                        </svg>
                    </div>
                    <div>
                        <h1
                            class="text-xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-red-500 to-green-600">
                            SAAR ASSURANCE
                        </h1>
                        <p class="text-sm text-gray-600">Confirmation de Déclaration</p>
                    </div>
                </div>
                <a href="/" class="text-gray-600 hover:text-blue-600 transition-colors">
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
        <div class="max-w-4xl mx-auto">

            <!-- Message de succès -->
            <div class="text-center mb-8">
                <div
                    class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-r from-green-500 to-green-600 rounded-full mb-6 animate-bounce">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7">
                        </path>
                    </svg>
                </div>
                <h2 class="text-3xl font-bold text-gray-900 mb-2">Déclaration Envoyée !</h2>
                <p class="text-lg text-gray-600">Votre sinistre a été déclaré avec succès</p>
            </div>

            <!-- Informations du sinistre -->
            <div class="bg-white rounded-2xl shadow-xl p-8 mb-8">
                <div class="text-center mb-6">
                    <div class="bg-blue-50 border-2 border-blue-200 rounded-xl p-6 inline-block">
                        <p class="text-sm text-blue-600 font-semibold mb-1">Numéro de sinistre</p>
                        <p class="text-2xl font-bold text-blue-900">{{ $sinistre->numero_sinistre }}</p>
                    </div>
                </div>

                <div class="grid md:grid-cols-2 gap-8">
                    <!-- Informations de l'assuré -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Vos informations
                        </h3>
                        <div class="space-y-3 text-sm">
                            <p><strong>Nom :</strong> {{ $sinistre->nom_assure }}</p>
                            <p><strong>Email :</strong> {{ $sinistre->email_assure }}</p>
                            <p><strong>Téléphone :</strong> {{ $sinistre->telephone_assure }}</p>
                            <p><strong>Police :</strong> {{ $sinistre->numero_police }}</p>
                        </div>
                    </div>

                    <!-- Détails du sinistre -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-red-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Détails du sinistre
                        </h3>
                        <div class="space-y-3 text-sm">
                            <p><strong>Date :</strong> {{ $sinistre->date_sinistre->format('d/m/Y') }}</p>
                            <p><strong>Lieu :</strong> {{ $sinistre->lieu_sinistre }}</p>
                            <p><strong>Conducteur :</strong> {{ $sinistre->conducteur_nom }}</p>
                            <p><strong>Statut :</strong>
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    {{ $sinistre->statut_libelle }}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Documents téléchargés -->
                <div class="mt-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                        Documents reçus ({{ $sinistre->documents->count() }})
                    </h3>

                    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach ($sinistre->documents as $document)
                            <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                                <div class="flex items-start justify-between">
                                    <div class="flex items-center space-x-3">
                                        <div class="text-2xl">{{ $document->icone_type }}</div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">
                                                {{ $document->libelle_document }}</p>
                                            <p class="text-xs text-gray-500">{{ $document->taille_formatee }}</p>
                                        </div>
                                    </div>
                                    <div class="text-green-500">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Prochaines étapes -->
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-2xl p-8 mb-8">
                <h3 class="text-xl font-semibold text-gray-900 mb-6">📋 Prochaines étapes</h3>

                <div class="space-y-4">
                    <div class="flex items-start">
                        <div
                            class="flex-shrink-0 w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center text-sm font-bold mr-4">
                            1</div>
                        <div>
                            <p class="font-semibold text-gray-900">Confirmation par email</p>
                            <p class="text-sm text-gray-600">Vous recevrez un email de confirmation avec tous les
                                détails dans quelques minutes.</p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div
                            class="flex-shrink-0 w-8 h-8 bg-yellow-500 text-white rounded-full flex items-center justify-center text-sm font-bold mr-4">
                            2</div>
                        <div>
                            <p class="font-semibold text-gray-900">Attribution d'un gestionnaire</p>
                            <p class="text-sm text-gray-600">Un expert sera assigné à votre dossier sous 24h ouvrées.
                            </p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div
                            class="flex-shrink-0 w-8 h-8 bg-green-600 text-white rounded-full flex items-center justify-center text-sm font-bold mr-4">
                            3</div>
                        <div>
                            <p class="font-semibold text-gray-900">Traitement du dossier</p>
                            <p class="text-sm text-gray-600">Notre équipe étudiera votre dossier et vous contactera si
                                nécessaire.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="text-center space-y-4">
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('declaration.recu', $sinistre->id) }}"
                        class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-green-600 to-green-700 text-white font-semibold rounded-xl hover:from-green-700 hover:to-green-800 transition-all duration-300">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 10v6m0 0l-4-4m4 4l4-4m-6 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Télécharger le reçu PDF
                    </a>

                    <a href="/"
                        class="inline-flex items-center px-8 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-semibold rounded-xl hover:from-blue-700 hover:to-blue-800 transition-all duration-300 transform hover:scale-105 shadow-lg">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                            </path>
                        </svg>
                        Retour à l'accueil
                    </a>
                </div>

                <p class="text-sm text-gray-500">
                    Pour toute question, contactez-nous au
                    <a href="tel:+22520303030" class="text-blue-600 hover:underline font-semibold">+225 20 30 30
                        30</a>
                    ou par email à
                    <a href="mailto:contact@saar-assurance.ci"
                        class="text-blue-600 hover:underline font-semibold">contact@saar-assurance.ci</a>
                </p>
            </div>
        </div>
    </div>
</body>

</html>
