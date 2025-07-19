<div id="details-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-10 mx-auto p-5 border w-4xl max-w-4xl shadow-lg rounded-2xl bg-white animate-bounce-in">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-900">Détails du Sinistre</h3>
                <button onclick="Modals.closeModal('details-modal')" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>

            <div id="sinistre-details-content" class="space-y-6">
                <!-- Le contenu sera chargé dynamiquement via JavaScript -->
                <div class="text-center py-8">
                    <svg class="animate-spin -ml-1 mr-3 h-8 w-8 text-saar-blue inline-block"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                    <p class="mt-2 text-gray-600">Chargement des détails...</p>
                </div>
            </div>

            <div class="flex justify-end mt-6">
                <button onclick="Modals.closeModal('details-modal')"
                    class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                    Fermer
                </button>
            </div>
        </div>
    </div>
</div>
