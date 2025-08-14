<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donner mon avis - Sinistre {{ $sinistre->numero_sinistre }} - SAAR Assurance</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-red-50 via-white to-green-50 min-h-screen">
    <!-- Sticky Header -->
    <header class="sticky top-0 z-40 bg-white/90 backdrop-blur border-b border-gray-200 shadow-sm flex items-center justify-between px-4 py-3 md:px-8">
        <div class="flex items-center space-x-3">
            <span class="text-red-700 font-bold text-lg md:text-xl">SAAR ASSURANCES</span>
        </div>
        <div class="flex items-center space-x-2 md:space-x-4">
            <a href="{{ route('assures.dashboard') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                Retour au tableau de bord
            </a>
        </div>
    </header>

    <div class="min-h-screen bg-gray-50 py-8">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- En-tête -->
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">
                    Votre avis nous intéresse !
                </h1>
                <p class="text-lg text-gray-600">
                    Sinistre {{ $sinistre->numero_sinistre }} - {{ $sinistre->statut_libelle }}
                </p>
            </div>

            <!-- Formulaire de feedback -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                @if($feedback && $feedback->note_service)
                    <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-green-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-green-800 font-medium">
                                Vous avez déjà donné votre avis pour ce sinistre. Vous pouvez le modifier ci-dessous.
                            </span>
                        </div>
                    </div>
                @endif

                <form action="{{ route('assures.feedback.store', $sinistre) }}" method="POST">
                    @csrf
                    
                    <!-- Note de service -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-3">
                            Comment évaluez-vous la qualité de nos services ? *
                        </label>
                        <div class="flex justify-between items-center">
                            @for($i = 1; $i <= 5; $i++)
                                <label class="flex flex-col items-center cursor-pointer">
                                    <input type="radio" name="note_service" value="{{ $i }}" 
                                           class="sr-only" 
                                           {{ $feedback && $feedback->note_service == $i ? 'checked' : '' }}
                                           required>
                                    <div class="w-12 h-12 rounded-full border-2 border-gray-300 flex items-center justify-center text-lg font-bold text-gray-500 hover:border-blue-500 hover:text-blue-500 transition-colors duration-200 feedback-option">
                                        {{ $i }}
                                    </div>
                                    <span class="text-xs text-gray-500 mt-1">{{ $i === 1 ? 'Très mauvais' : ($i === 5 ? 'Excellent' : '') }}</span>
                                </label>
                            @endfor
                        </div>
                        @error('note_service')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Humeur avec emoticons -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-3">
                            Comment vous sentez-vous après le traitement de votre sinistre ? *
                        </label>
                        <div class="flex justify-between items-center">
                            @php
                                $emoticons = [
                                    '😊' => 'Très satisfait',
                                    '🙂' => 'Satisfait', 
                                    '😐' => 'Neutre',
                                    '😕' => 'Mécontent',
                                    '😠' => 'Très mécontent'
                                ];
                            @endphp
                            
                            @foreach($emoticons as $emoticon => $label)
                                <label class="flex flex-col items-center cursor-pointer">
                                    <input type="radio" name="humeur_emoticon" value="{{ $emoticon }}" 
                                           class="sr-only" 
                                           {{ $feedback && $feedback->humeur_emoticon == $emoticon ? 'checked' : '' }}
                                           required>
                                    <div class="text-4xl hover:scale-110 transition-transform duration-200 feedback-option">
                                        {{ $emoticon }}
                                    </div>
                                    <span class="text-xs text-gray-500 mt-1 text-center">{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('humeur_emoticon')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Commentaire -->
                    <div class="mb-6">
                        <label for="commentaire" class="block text-sm font-medium text-gray-700 mb-2">
                            Avez-vous des commentaires ou suggestions ? (optionnel)
                        </label>
                        <textarea id="commentaire" name="commentaire" rows="4" 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="Partagez votre expérience, vos suggestions d'amélioration...">{{ $feedback ? $feedback->commentaire : old('commentaire') }}</textarea>
                        @error('commentaire')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Boutons -->
                    <div class="flex justify-between items-center">
                        <a href="{{ route('assures.dashboard') }}" 
                           class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                            Retour au tableau de bord
                        </a>
                        <button type="submit" 
                                class="px-6 py-2 bg-blue-600 text-white font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Envoyer mon avis
                        </button>
                    </div>
                </form>
            </div>

            <!-- Informations sur le sinistre -->
            <div class="mt-6 bg-blue-50 rounded-lg p-4">
                <h3 class="text-sm font-medium text-blue-800 mb-2">Informations sur votre sinistre</h3>
                <div class="text-sm text-blue-700">
                    <p><strong>Numéro :</strong> {{ $sinistre->numero_sinistre }}</p>
                    <p><strong>Date :</strong> {{ $sinistre->date_sinistre ? $sinistre->date_sinistre->format('d/m/Y') : 'Non précisée' }}</p>
                    <p><strong>Statut :</strong> {{ $sinistre->statut_libelle }}</p>
                    @if($sinistre->date_reglement)
                        <p><strong>Date de règlement :</strong> {{ $sinistre->date_reglement->format('d/m/Y') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <style>
    .feedback-option input:checked + div {
        @apply border-blue-500 text-blue-600;
    }

    .feedback-option input:checked + div {
        @apply scale-110;
    }
    </style>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Gestion des options de feedback
        const feedbackOptions = document.querySelectorAll('.feedback-option');
        
        feedbackOptions.forEach(option => {
            const radio = option.previousElementSibling;
            const div = option;
            
            radio.addEventListener('change', function() {
                // Retirer la sélection de tous les autres
                feedbackOptions.forEach(opt => {
                    opt.classList.remove('border-blue-500', 'text-blue-600', 'scale-110');
                });
                
                // Ajouter la sélection à l'option choisie
                if (this.checked) {
                    div.classList.add('border-blue-500', 'text-blue-600', 'scale-110');
                }
            });
            
            // Appliquer le style initial si déjà sélectionné
            if (radio.checked) {
                div.classList.add('border-blue-500', 'text-blue-600', 'scale-110');
            }
        });
    });
    </script>
</body>
</html>
