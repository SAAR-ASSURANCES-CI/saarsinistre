<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assuré</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">N° Sinistre</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">Plaque</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden lg:table-cell">Dernier document</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden lg:table-cell">Nbre docs</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($sinistres as $sinistre)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        {{ $sinistre->nom_assure ?? 'Non renseigné' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $sinistre->numero_sinistre }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 hidden md:table-cell">
                        {{ $sinistre->vehicule->immatriculation ?? 'N/A' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 hidden lg:table-cell">
                        @if($sinistre->documents->count() > 0)
                            {{ $sinistre->documents->sortByDesc('created_at')->first()->created_at->format('d/m/Y') }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center hidden lg:table-cell">
                        {{ $sinistre->documents->count() }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <button class="text-blue-600 hover:text-blue-900 mr-3 view-docs" data-sinistre-id="{{ $sinistre->id }}">
                            Voir
                        </button>
                        <button class="text-green-600 hover:text-green-900 download-all" data-sinistre-id="{{ $sinistre->id }}">
                            <span class="hidden sm:inline">Télécharger</span>
                            <span class="sm:hidden">⬇</span>
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="mt-6">
    {{ $sinistres->links() }}
</div>