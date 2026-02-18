<div id="step-2" class="step-content p-8 hidden">
    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-r from-orange-500 to-red-600 rounded-2xl mb-4">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"></path>
            </svg>
        </div>
        <h3 class="text-2xl font-bold text-gray-900 mb-2">Informations du Véhicule</h3>
        <p class="text-gray-600">Détails du véhicule impliqué dans le sinistre</p>
    </div>

    <div class="grid md:grid-cols-2 gap-6">
        <div class="form-group">
            <label class="block text-sm font-semibold text-gray-700 mb-3">
                Marque
            </label>
            <input type="text" name="marque"
                class="w-full px-4 py-4 border-2 border-gray-200 rounded-xl focus:border-saar-blue focus:ring-4 focus:ring-blue-100 transition-all duration-300 text-gray-900"
                placeholder="Ex: Toyota, Peugeot">
        </div>

        <div class="form-group">
            <label class="block text-sm font-semibold text-gray-700 mb-3">
                Modèle
            </label>
            <input type="text" name="modele"
                class="w-full px-4 py-4 border-2 border-gray-200 rounded-xl focus:border-saar-blue focus:ring-4 focus:ring-blue-100 transition-all duration-300 text-gray-900"
                placeholder="Ex: Corolla, 308">
        </div>

        <div class="form-group md:col-span-2">
            <label class="block text-sm font-semibold text-gray-700 mb-3">
                Immatriculation
            </label>
            <input type="text" name="immatriculation"
    class="w-full px-4 py-4 border-2 border-gray-200 rounded-xl focus:border-saar-blue focus:ring-4 focus:ring-blue-100 transition-all duration-300 text-gray-900 uppercase"
    placeholder="1234 AB 01">
            <p class="mt-1 text-xs text-gray-500">Format: 1234 AB 01</p>
        </div>
    </div>
</div>