<div id="step-3" class="step-content hidden p-8">
    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-r from-red-500 to-saar-red rounded-2xl mb-4">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
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
        <!-- Implique-tiers -->
        <div class="form-group">
            <div class="flex items-center mb-2">
                <input type="checkbox" name="implique_tiers" id="implique_tiers" value="1" class="h-5 w-5 text-saar-blue focus:ring-blue-500 border-gray-300 rounded">
                <label for="implique_tiers" class="ml-3 text-sm font-semibold text-gray-700">
                    Le sinistre implique un tiers ?
                </label>
            </div>
            
            <!-- Nombre de tiers -->
            <div id="nombre-tiers-group" class="hidden mt-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Nombre de tiers impliqués
                </label>
                <select name="nombre_tiers" id="nombre_tiers" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-saar-blue focus:ring-4 focus:ring-blue-100 transition-all duration-300">
                    <option value="">Sélectionnez le nombre de tiers</option>
                    <option value="1">1 tiers</option>
                    <option value="2">2 tiers</option>
                    <option value="3">3 tiers</option>
                    <option value="4">4 tiers</option>
                    <option value="5">5 tiers</option>
                    <option value="6">6 tiers</option>
                    <option value="7">7 tiers</option>
                    <option value="8">8 tiers</option>
                    <option value="9">9 tiers</option>
                    <option value="10+">10 tiers ou plus</option>
                </select>
            </div>

            <!-- Formulaires des tiers -->
            <div id="tiers-forms-container" class="hidden mt-6 space-y-6">
                <!-- Les formulaires seront générés dynamiquement par JavaScript -->
            </div>

            <div id="details-tiers-group" class="hidden mt-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Détails généraux sur le(s) tiers impliqué(s)
                </label>
                <textarea name="details_tiers" id="details_tiers" rows="3" class="w-full px-4 py-4 border-2 border-gray-200 rounded-xl focus:border-saar-blue focus:ring-4 focus:ring-blue-100 transition-all duration-300 resize-none" placeholder="Informations complémentaires sur les tiers..."></textarea>
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