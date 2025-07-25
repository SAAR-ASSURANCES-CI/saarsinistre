<div id="details-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative mx-auto p-5 w-full h-full max-w-full">
        <div class="bg-white rounded-2xl shadow-xl h-[95vh] flex flex-col">
            <div class="flex items-center justify-between p-6 border-b">
                <h3 class="text-xl font-bold text-gray-900">Détails du Sinistre</h3>
                <button onclick="Modals.closeModal('details-modal')" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <div id="sinistre-details-content" class="flex-1 overflow-y-auto p-6 space-y-6">
                <!-- Contenu dynamique -->
            </div>

            <div class="p-4 border-t bg-gray-50">
                <button onclick="Modals.closeModal('details-modal')"
                    class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                    Fermer
                </button>
            </div>
        </div>
    </div>
</div>
