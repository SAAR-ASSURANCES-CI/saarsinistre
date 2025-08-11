<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 hover:shadow-xl transition-all duration-300">
        <div class="flex items-center">
            <div class="p-3 rounded-xl bg-blue-100">
                <svg class="w-6 h-6 text-saar-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                    </path>
                </svg>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Total Sinistres</p>
                <p id="stat-total" class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 hover:shadow-xl transition-all duration-300">
        <div class="flex items-center">
            <div class="p-3 rounded-xl bg-yellow-100">
                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">En Attente</p>
                <p id="stat-en-attente" class="text-2xl font-bold text-gray-900">{{ $stats['en_attente'] }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 hover:shadow-xl transition-all duration-300">
        <div class="flex items-center">
            <div class="p-3 rounded-xl bg-blue-100">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">En Cours</p>
                <p id="stat-en-cours" class="text-2xl font-bold text-gray-900">{{ $stats['en_cours'] }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 hover:shadow-xl transition-all duration-300">
        <div class="flex items-center">
            <div class="p-3 rounded-xl bg-green-100">
                <svg class="w-6 h-6 text-saar-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Traités</p>
                <p id="stat-traites" class="text-2xl font-bold text-gray-900">{{ $stats['traites'] }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 hover:shadow-xl transition-all duration-300">
        <div class="flex items-center">
            <div class="p-3 rounded-xl bg-red-100">
                <!-- Icône de briefcase -->
                <svg class="w-6 h-6 text-saar-red" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 12v4m-6 4h12a2 2 0 002-2V10a2 2 0 00-2-2h-3V6a2 2 0 00-2-2h-2a2 2 0 00-2 2v2H6a2 2 0 00-2 2v8a2 2 0 002 2z" />
                </svg>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Expertise Requise</p>
                <p id="stat-expertise-requise" class="text-2xl font-bold text-gray-900">{{ $stats['expertise_requise'] }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 hover:shadow-xl transition-all duration-300">
        <div class="flex items-center">
            <div class="p-3 rounded-xl bg-orange-100">
                <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">En attente de documents</p>
                <p id="stat-en-attente-documents" class="text-2xl font-bold text-gray-900">{{ $stats['en_attente_documents'] }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 hover:shadow-xl transition-all duration-300">
        <div class="flex items-center">
            <div class="p-3 rounded-xl bg-red-100">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636M18.364 5.636L5.636 18.364"></path>
                </svg>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Refusés</p>
                <p id="stat-refuse" class="text-2xl font-bold text-gray-900">{{ $stats['refuse'] }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 hover:shadow-xl transition-all duration-300">
        <div class="flex items-center">
            <div class="p-3 rounded-xl bg-rose-100">
                <svg class="w-6 h-6 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">En retard</p>
                <p id="stat-en-retard" class="text-2xl font-bold text-gray-900">{{ $stats['en_retard'] }}</p>
            </div>
        </div>
    </div>

</div>
