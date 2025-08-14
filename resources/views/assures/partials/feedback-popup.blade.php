@if ($sinistresNecessitantFeedback->isNotEmpty())
    <div id="feedback-popup" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50"
        style="display: none;">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <!-- En-tête avec bouton fermer -->
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Votre avis nous intéresse !</h3>
                    <button onclick="closeFeedbackPopup()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Message principal -->
                <div class="mb-6">
                    <p class="text-gray-600 mb-2">
                        Nous avons traité {{ $sinistresNecessitantFeedback->count() }} de vos sinistres.
                    </p>
                    <p class="text-gray-600">
                        Votre avis nous aide à améliorer nos services. Prenez quelques instants pour nous donner votre
                        feedback !
                    </p>
                </div>

                <!-- Liste des sinistres nécessitant un feedback -->
                <div class="mb-6">
                    <h4 class="text-sm font-medium text-gray-700 mb-3">Sinistres concernés :</h4>
                    <div class="space-y-2 max-h-32 overflow-y-auto">
                        @foreach ($sinistresNecessitantFeedback as $sinistre)
                            <div class="flex items-center justify-between p-2 bg-gray-50 rounded">
                                <span class="text-sm text-gray-700">
                                    {{ $sinistre->numero_sinistre }} - {{ $sinistre->statut_libelle }}
                                </span>
                                <a href="{{ route('assures.feedback.form', $sinistre) }}"
                                    class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                    Donner mon avis
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Boutons d'action -->
                <div class="flex justify-center space-x-3">
                    <button onclick="closeFeedbackPopup()"
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors">
                        Plus tard
                    </button>
                    <a href="{{ route('assures.feedback.form', $sinistresNecessitantFeedback->first()) }}"
                        class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                        Commencer maintenant
                    </a>
                </div>

                <!-- Note de bas de page -->
                <div class="mt-4 text-xs text-gray-500">
                    Vous pouvez également accéder aux formulaires de feedback depuis la liste de vos sinistres.
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                document.getElementById('feedback-popup').style.display = 'block';
            }, 1000);
        });


        function closeFeedbackPopup() {
            document.getElementById('feedback-popup').style.display = 'none';
        }


        document.getElementById('feedback-popup').addEventListener('click', function(e) {
            if (e.target === this) {
                closeFeedbackPopup();
            }
        });


        document.querySelector('#feedback-popup > div').addEventListener('click', function(e) {
            e.stopPropagation();
        });
    </script>
@endif
