    <div class="tab-content">
        <!-- Onglet Gestionnaires -->
        <div class="hidden p-4 rounded-lg bg-gray-50" id="gestionnaires" role="tabpanel"
            aria-labelledby="gestionnaires-tab">
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Utilisateur
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Email
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Rôle
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Statut
                                </th>
                                <th
                                    class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($gestionnaires as $user)
                                <tr class="user-row hover:bg-gray-50 transition-colors" data-role="{{ $user->role }}"
                                    data-status="{{ $user->actif ? '1' : '0' }}">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div
                                                class="w-10 h-10 bg-gradient-to-r from-saar-blue to-blue-600 rounded-full flex items-center justify-center">
                                                <span class="text-white text-sm font-semibold">
                                                    {{ substr($user->nom_complet, 0, 1) }}
                                                    @php
                                                        $lastSpace = strrpos($user->nom_complet, ' ');
                                                        echo $lastSpace !== false
                                                            ? substr($user->nom_complet, $lastSpace + 1, 1)
                                                            : '';
                                                    @endphp
                                                </span>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $user->nom_complet }}</div>
                                                <div class="text-sm text-gray-500">ID: {{ $user->id }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $user->email }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if ($user->role === 'admin')
                                            <span
                                                class="px-2 py-1 text-xs font-medium bg-purple-100 text-purple-800 rounded-full">Administrateur</span>
                                        @else
                                            <span
                                                class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">Gestionnaire</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if ($user->actif)
                                            <span
                                                class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">Actif</span>
                                        @else
                                            <span
                                                class="px-2 py-1 text-xs font-medium bg-red-100 text-red-800 rounded-full">Inactif</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center space-x-2 justify-end">
                                            <button onclick="editUser({{ $user->id }})"
                                                class="text-saar-blue hover:text-blue-800 transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                    </path>
                                                </svg>
                                            </button>
                                            <form action="{{ route('dashboard.users.toggle-status', $user->id) }}"
                                                method="POST" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit"
                                                    class="text-gray-600 hover:text-gray-800 transition-colors">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z">
                                                        </path>
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                                        </path>
                                                    </svg>
                                                </button>
                                            </form>
                                            <form action="{{ route('dashboard.users.destroy', $user->id) }}"
                                                method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')"
                                                    class="text-red-600 hover:text-red-800 transition-colors">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                        </path>
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Onglet Assurés -->
        <div class="hidden p-4 rounded-lg bg-gray-50" id="assures" role="tabpanel" aria-labelledby="assures-tab">
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Utilisateur
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Email
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Numéro Assuré
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Statut
                                </th>
                                <th
                                    class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($assures as $user)
                                <tr class="user-row hover:bg-gray-50 transition-colors" data-role="assure"
                                    data-status="{{ $user->actif ? '1' : '0' }}">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div
                                                class="w-10 h-10 bg-gradient-to-r from-saar-blue to-blue-600 rounded-full flex items-center justify-center">
                                                <span class="text-white text-sm font-semibold">
                                                    {{ substr($user->nom_complet, 0, 1) }}
                                                    @php
                                                        $lastSpace = strrpos($user->nom_complet, ' ');
                                                        echo $lastSpace !== false
                                                            ? substr($user->nom_complet, $lastSpace + 1, 1)
                                                            : '';
                                                    @endphp
                                                </span>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $user->nom_complet }}</div>
                                                <div class="text-sm text-gray-500">ID: {{ $user->id }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $user->email ?? 'N/A' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $user->numero_assure ?? 'N/A' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if ($user->actif)
                                            <span
                                                class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">Actif</span>
                                        @else
                                            <span
                                                class="px-2 py-1 text-xs font-medium bg-red-100 text-red-800 rounded-full">Inactif</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center space-x-2 justify-end">
                                            <button onclick="editUser({{ $user->id }})"
                                                class="text-saar-blue hover:text-blue-800 transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                    </path>
                                                </svg>
                                            </button>
                                            <form action="{{ route('dashboard.users.toggle-status', $user->id) }}"
                                                method="POST" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit"
                                                    class="text-gray-600 hover:text-gray-800 transition-colors">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z">
                                                        </path>
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                                        </path>
                                                    </svg>
                                                </button>
                                            </form>
                                            <form action="{{ route('dashboard.users.destroy', $user->id) }}"
                                                method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')"
                                                    class="text-red-600 hover:text-red-800 transition-colors">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                        </path>
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
