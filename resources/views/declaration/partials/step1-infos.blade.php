<div id="step-1" class="step-content p-8 animate-slide-in">
    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-r from-blue-500 to-saar-blue rounded-2xl mb-4">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
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