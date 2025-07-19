<div id="assign-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-2xl bg-white animate-bounce-in">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-900">Affecter un Gestionnaire</h3>
                <button onclick="Modals.closeModal('assign-modal')" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
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
                <button onclick="Modals.closeModal('assign-modal')"
                    class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                    Annuler
                </button>
                <button onclick="Modals.confirmAssignment()"
                    class="px-4 py-2 bg-saar-blue text-white rounded-lg hover:bg-blue-700">
                    Affecter
                </button>
            </div>
        </div>
    </div>
</div>
