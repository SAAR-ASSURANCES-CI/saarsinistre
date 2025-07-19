<div class="bg-white rounded-2xl shadow-lg p-6 mb-8 border border-gray-100">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0">
        <!-- Search Bar -->
        <div class="flex-1 max-w-md">
            <div class="relative">
                <input type="text" id="search-input" placeholder="Rechercher par numéro, nom ou téléphone..."
                    class="w-full pl-10 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:border-saar-blue focus:ring-4 focus:ring-blue-100 transition-all duration-300">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="flex flex-wrap gap-4">
            <select id="status-filter"
                class="px-4 py-2 border-2 border-gray-200 rounded-lg focus:border-saar-blue focus:ring-2 focus:ring-blue-100 transition-all">
                <option value="">Tous les statuts</option>
                <option value="en_attente">En attente</option>
                <option value="en_cours">En cours</option>
                <option value="expertise_requise">Expertise requise</option>
                <option value="en_attente_documents">En attente documents</option>
                <option value="pret_reglement">Prêt règlement</option>
                <option value="regle">Réglé</option>
                <option value="refuse">Refusé</option>
                <option value="clos">Clos</option>
            </select>

            <select id="gestionnaire-filter"
                class="px-4 py-2 border-2 border-gray-200 rounded-lg focus:border-saar-blue focus:ring-2 focus:ring-blue-100 transition-all">
                <option value="">Tous les gestionnaires</option>
                @foreach ($gestionnaires as $gestionnaire)
                    <option value="{{ $gestionnaire->id }}">{{ $gestionnaire->nom_complet }}</option>
                @endforeach
                <option value="null">Non assigné</option>
            </select>

            <button onclick="Sinistres.resetFilters()"
                class="px-4 py-2 text-gray-600 hover:text-saar-blue transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                    </path>
                </svg>
            </button>
        </div>
    </div>
</div>
