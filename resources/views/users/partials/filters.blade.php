<div class="bg-white rounded-xl shadow-lg mb-6 p-4">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="relative">
            <input type="text" id="search-users" placeholder="Rechercher un utilisateur..."
                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-saar-blue focus:border-transparent">
            <svg class="absolute left-3 top-2.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
        </div>
        <select id="filter-role"
            class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-saar-blue focus:border-transparent">
            <option value="">Tous les rôles</option>
            <option value="admin">Administrateur</option>
            <option value="gestionnaire">Gestionnaire</option>
            <option value="assure">Assuré</option>
        </select>
        <select id="filter-status"
            class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-saar-blue focus:border-transparent">
            <option value="">Tous les statuts</option>
            <option value="1">Actif</option>
            <option value="0">Inactif</option>
        </select>
        <button onclick="resetFilters()"
            class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg transition-colors">
            Réinitialiser
        </button>
    </div>
</div>
