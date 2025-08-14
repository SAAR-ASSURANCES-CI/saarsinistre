<!DOCTYPE html>
<html lang="fr">

<head>
    @include('admin.partials.head', ['title' => 'Détails du Feedback - SAAR Assurance'])
</head>

<body class="bg-gradient-to-br from-red-50 via-white to-green-50 min-h-screen">

    <!-- Header -->
    @include('admin.partials.header')

    <!-- Navbar Horizontale -->
    @include('admin.partials.navbar')

    <div class="container mx-auto px-4 py-8">
        <!-- En-tête avec bouton retour -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Détails du Feedback</h1>
                    <p class="text-gray-600">Feedback pour le sinistre {{ $feedback->sinistre->numero_sinistre }}</p>
                </div>
                <a href="{{ route('dashboard.feedback.index') }}" 
                   class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Retour à la liste
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Informations principales -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow p-6 mb-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Informations du Feedback</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Note de service -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Note de service</label>
                            @if($feedback->note_service)
                                <div class="flex items-center">
                                    <span class="text-2xl font-bold text-gray-900 mr-3">
                                        {{ $feedback->note_service }}/5
                                    </span>
                                    <div class="flex">
                                        @for($i = 1; $i <= 5; $i++)
                                            <svg class="w-6 h-6 {{ $i <= $feedback->note_service ? 'text-yellow-400' : 'text-gray-300' }}" 
                                                 fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                            </svg>
                                        @endfor
                                    </div>
                                </div>
                                <p class="text-sm text-gray-600 mt-1">{{ $feedback->note_service_libelle }}</p>
                            @else
                                <span class="text-sm text-gray-500">Non noté</span>
                            @endif
                        </div>

                        <!-- Humeur -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Humeur de l'assuré</label>
                            @if($feedback->humeur_emoticon)
                                <div class="flex items-center">
                                    <span class="text-4xl mr-3">{{ $feedback->humeur_emoticon }}</span>
                                    <div>
                                        <p class="text-lg font-medium text-gray-900">{{ $feedback->humeur_libelle }}</p>
                                        <p class="text-sm text-gray-600">Niveau de satisfaction</p>
                                    </div>
                                </div>
                            @else
                                <span class="text-sm text-gray-500">Non renseigné</span>
                            @endif
                        </div>
                    </div>

                    <!-- Commentaire -->
                    @if($feedback->commentaire)
                        <div class="mt-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Commentaire de l'assuré</label>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <p class="text-gray-800">{{ $feedback->commentaire }}</p>
                            </div>
                        </div>
                    @endif

                    <!-- Statut du feedback -->
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="text-sm font-medium text-gray-700">Statut :</span>
                                @if($feedback->note_service && $feedback->humeur_emoticon)
                                    <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                        Complété
                                    </span>
                                @else
                                    <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                        </svg>
                                        En attente
                                    </span>
                                @endif
                            </div>
                            
                            @if($feedback->envoye_automatiquement)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                    </svg>
                                    Envoyé automatiquement
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Informations du sinistre -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Informations du Sinistre</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <p class="text-sm text-gray-600">Numéro de sinistre</p>
                            <p class="text-lg font-medium text-gray-900">{{ $feedback->sinistre->numero_sinistre }}</p>
                        </div>
                        
                        <div>
                            <p class="text-sm text-gray-600">Statut</p>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $feedback->sinistre->statut_couleur }}-100 text-{{ $feedback->sinistre->statut_couleur }}-800">
                                {{ $feedback->sinistre->statut_libelle }}
                            </span>
                        </div>
                        
                        <div>
                            <p class="text-sm text-gray-600">Date du sinistre</p>
                            <p class="text-lg font-medium text-gray-900">
                                {{ $feedback->sinistre->date_sinistre ? $feedback->sinistre->date_sinistre->format('d/m/Y') : 'Non précisée' }}
                            </p>
                        </div>
                        
                        <div>
                            <p class="text-sm text-gray-600">Date de règlement</p>
                            <p class="text-lg font-medium text-gray-900">
                                {{ $feedback->sinistre->date_reglement ? $feedback->sinistre->date_reglement->format('d/m/Y') : 'Non réglé' }}
                            </p>
                        </div>
                    </div>

                    @if($feedback->sinistre->lieu_sinistre)
                        <div class="mt-4">
                            <p class="text-sm text-gray-600">Lieu du sinistre</p>
                            <p class="text-lg font-medium text-gray-900">{{ $feedback->sinistre->lieu_sinistre }}</p>
                        </div>
                    @endif

                    @if($feedback->sinistre->circonstances)
                        <div class="mt-4">
                            <p class="text-sm text-gray-600">Circonstances</p>
                            <p class="text-gray-900">{{ $feedback->sinistre->circonstances }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Sidebar avec informations de l'assuré et dates -->
            <div class="lg:col-span-1">
                <!-- Informations de l'assuré -->
                <div class="bg-white rounded-lg shadow p-6 mb-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Assuré</h2>
                    
                    <div class="space-y-3">
                        <div>
                            <p class="text-sm text-gray-600">Nom complet</p>
                            <p class="font-medium text-gray-900">
                                {{ $feedback->assure->nom_complet ?? $feedback->assure->name }}
                            </p>
                        </div>
                        
                        <div>
                            <p class="text-sm text-gray-600">Email</p>
                            <p class="font-medium text-gray-900">{{ $feedback->assure->email }}</p>
                        </div>
                        
                        @if($feedback->assure->telephone_assure)
                            <div>
                                <p class="text-sm text-gray-600">Téléphone</p>
                                <p class="font-medium text-gray-900">{{ $feedback->assure->telephone_assure }}</p>
                            </div>
                        @endif
                        
                        @if($feedback->sinistre->numero_police)
                            <div>
                                <p class="text-sm text-gray-600">Numéro de police</p>
                                <p class="font-medium text-gray-900">{{ $feedback->sinistre->numero_police }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Dates importantes -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Dates</h2>
                    
                    <div class="space-y-3">
                        <div>
                            <p class="text-sm text-gray-600">Création du feedback</p>
                            <p class="font-medium text-gray-900">
                                {{ $feedback->created_at->format('d/m/Y H:i') }}
                            </p>
                        </div>
                        
                        @if($feedback->date_feedback)
                            <div>
                                <p class="text-sm text-gray-600">Date de réponse</p>
                                <p class="font-medium text-gray-900">
                                    {{ $feedback->date_feedback->format('d/m/Y H:i') }}
                                </p>
                            </div>
                        @endif
                        
                        <div>
                            <p class="text-sm text-gray-600">Dernière modification</p>
                            <p class="font-medium text-gray-900">
                                {{ $feedback->updated_at->format('d/m/Y H:i') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="w-full py-3 bg-white/80 border-t border-gray-200 text-center text-xs text-gray-500 mt-8">
        © Saar Assurances Côte d'Ivoire. Tous droits réservés.
    </footer>

</body>
</html>
