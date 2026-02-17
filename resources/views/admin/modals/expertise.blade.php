<div id="expertise-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 z-50">
    <div class="relative mx-auto my-6 w-full max-w-5xl">
        <div class="bg-white rounded-2xl shadow-xl max-h-[90vh] flex flex-col">
            <!-- Header -->
            <div class="flex items-center justify-between px-6 py-4 border-b">
                <h3 class="text-xl font-bold text-gray-900">Fiche d'Expertise</h3>
                <button onclick="Modals.closeModal('expertise-modal')" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Content (scrollable) -->
            <div class="px-6 py-4 overflow-y-auto space-y-6">
                <!-- Informations générales -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 border border-gray-200 rounded-xl p-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase">Date (automatique)</label>
                        <input id="expertise-date" type="text" readonly
                            class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100 text-gray-600 cursor-not-allowed text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase">Client (automatique)</label>
                        <input id="expertise-client" type="text" readonly
                            class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100 text-gray-600 cursor-not-allowed text-sm">
                    </div>
                </div>

                <!-- Collaborateur -->
                <div class="border border-gray-200 rounded-xl p-4 space-y-3">
                    <h4 class="text-sm font-semibold text-gray-800">Collaborateur SAAR</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 uppercase">Nom (automatique)</label>
                            <input id="expertise-collaborateur-nom" type="text" readonly
                                class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100 text-gray-600 cursor-not-allowed text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 uppercase">Téléphones (automatique)</label>
                            <input id="expertise-collaborateur-telephone" type="text" readonly
                                class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100 text-gray-600 cursor-not-allowed text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 uppercase">Email (automatique)</label>
                            <input id="expertise-collaborateur-email" type="text" readonly
                                class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100 text-gray-600 cursor-not-allowed text-sm">
                        </div>
                    </div>
                </div>

                <!-- Lieu & contact -->
                <div class="border border-gray-200 rounded-xl p-4 space-y-3">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 uppercase">Commune (lieu d'expertise) ✏️</label>
                            <input id="expertise-lieu" name="lieu_expertise" type="text"
                                placeholder="Saisir le lieu..."
                                class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg focus:border-saar-blue focus:ring-2 focus:ring-blue-100 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 uppercase">Contact client (automatique)</label>
                            <input id="expertise-contact-client" name="contact_client" type="text" readonly
                                class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100 text-gray-600 cursor-not-allowed text-sm">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 uppercase">Véhicule expertisé (automatique)</label>
                        <input id="expertise-vehicule" name="vehicule_expertise" type="text"
    readonly
    class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100 text-gray-600 cursor-not-allowed text-sm">
                    </div>
                </div>

                <div class="border border-gray-200 rounded-xl p-4 space-y-3">
                    <div class="flex items-center justify-between">
                        <h4 class="text-sm font-semibold text-gray-800">Opérations d'expertise</h4>
                        <button type="button" onclick="Modals.addExpertiseOperationRow()"
                            class="inline-flex items-center px-3 py-1.5 border border-saar-blue text-saar-blue text-xs font-medium rounded-lg hover:bg-saar-blue hover:text-white transition-colors">
                            <span class="mr-1 text-base">+</span> Ajouter une ligne
                        </button>
                    </div>

                    <div class="overflow-x-auto border border-gray-200 rounded-lg">
                        <table class="min-w-full text-xs">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 py-2 text-left font-semibold text-gray-700">Libellé</th>
                                    <th class="px-2 py-2 text-center font-semibold text-gray-700 w-16">ECH</th>
                                    <th class="px-2 py-2 text-center font-semibold text-gray-700 w-16">REP</th>
                                    <th class="px-2 py-2 text-center font-semibold text-gray-700 w-16">CTL</th>
                                    <th class="px-2 py-2 text-center font-semibold text-gray-700 w-16">P</th>
                                    <th class="px-2 py-2 text-center font-semibold text-gray-700 w-10"></th>
                                </tr>
                            </thead>
                            <tbody id="expertise-operations-body" class="divide-y divide-gray-100">
                            </tbody>
                        </table>
                    </div>

                    <p class="text-xs text-gray-500 mt-2">
                        Au moins une opération doit être renseignée. Pour chaque ligne, cochez au moins une colonne (ECH, REP, CTL ou P).
                    </p>
                </div>

                <!-- Signatures (zones statiques) -->
                <div class="border border-gray-200 rounded-xl p-4 grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="flex flex-col justify-end">
                        <p class="text-sm text-gray-700 border-t border-gray-300 pt-4 text-center">
                            Propriétaire ou Représentant(e)
                        </p>
                    </div>
                    <div class="flex flex-col justify-end">
                        <p class="text-sm text-gray-700 border-t border-gray-300 pt-4 text-center">
                            Prestataire
                        </p>
                    </div>
                </div>
            </div>

            <!-- Footer actions -->
            <div class="px-6 py-4 border-t bg-gray-50 flex items-center justify-between">
                <div class="flex items-center space-x-2 text-xs text-gray-500">
                    <span id="expertise-status-message"></span>
                </div>
                <div class="flex items-center space-x-3">
                    <button type="button" onclick="Modals.closeModal('expertise-modal')"
                        class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100 text-sm">
                        Annuler
                    </button>
                    <button type="button" onclick="Modals.previewExpertise()"
                        class="inline-flex items-center px-4 py-2 border border-saar-blue text-saar-blue rounded-lg text-sm font-medium hover:bg-saar-blue hover:text-white transition-colors">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        Prévisualiser
                    </button>
                    <button type="button" onclick="Modals.downloadExpertise()"
                        class="inline-flex items-center px-4 py-2 bg-saar-green text-white rounded-lg hover:bg-green-700 text-sm font-medium">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        Télécharger
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

