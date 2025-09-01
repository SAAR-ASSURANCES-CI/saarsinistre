<div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">
    <div class="px-6 py-4 border-b border-gray-200">
        <h2 class="text-xl font-bold text-gray-900">Liste des Sinistres</h2>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sinistre
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assuré
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Gestionnaire</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Discussion</th>
                </tr>
            </thead>
            <tbody id="sinistres-tbody" class="bg-white divide-y divide-gray-200">
                {{--
                <td class="px-6 py-4 text-center">
                    <a href="{{ url('/gestionnaires/sinistres/' . $sinistre->id . '/chat') }}" class="inline-block text-saar-blue hover:text-saar-red transition-transform duration-200 rounded-full p-2 shadow-sm hover:scale-110 focus:outline-none focus:ring-2 focus:ring-saar-red animate-bounce-in" title="Ouvrir la discussion">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m-2 8a9 9 0 100-18 9 9 0 000 18z" />
                        </svg>
                    </a>
                </td>
                --}}
            </tbody>
        </table>
    </div>

    <!-- Loading State -->
    <div id="loading-state" class="hidden p-8 text-center">
        <div class="inline-flex items-center">
            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-saar-blue" xmlns="http://www.w3.org/2000/svg"
                fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                </circle>
                <path class="opacity-75" fill="currentColor"
                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                </path>
            </svg>
            <span class="text-gray-600">Chargement...</span>
        </div>
    </div>

    <!-- Pagination -->
    <div id="pagination-container"
        class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
        <!-- Pagination dynamique -->
    </div>
</div>
