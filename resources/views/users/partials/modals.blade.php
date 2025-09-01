<!-- Modal d'ajout d'utilisateur -->
<div id="add-user-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-1/2 shadow-lg rounded-xl bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold text-gray-800">Ajouter un nouvel utilisateur</h3>
            <button onclick="closeAddUserModal()" class="text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                    </path>
                </svg>
            </button>
        </div>
        <form action="{{ route('dashboard.users.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="nom_complet" class="block text-sm font-medium text-gray-700 mb-1">Nom
                        complet</label>
                    <input type="text" name="nom_complet" id="nom_complet" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-saar-blue focus:border-transparent">
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" id="email" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-saar-blue focus:border-transparent">
                </div>
                <div>
                    <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Rôle</label>
                    <select name="role" id="role" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-saar-blue focus:border-transparent">
                        <option value="">Sélectionner un rôle</option>
                        <option value="admin">Administrateur</option>
                        <option value="gestionnaire">Gestionnaire</option>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div class="text-sm text-blue-800">
                                <strong>Information :</strong> Un mot de passe temporaire sera généré automatiquement et envoyé par email à l'utilisateur.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeAddUserModal()"
                    class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                    Annuler
                </button>
                <button type="submit"
                    class="px-4 py-2 bg-gradient-to-r from-saar-blue to-blue-600 text-white rounded-lg hover:shadow-lg transition-all">
                    Enregistrer
                </button>
            </div>
        </form>
    </div>
</div>


<!-- Modal pour modifier un utilisateur -->
<div id="edit-user-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-1/2 lg:w-1/3 shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-800">Modifier un utilisateur</h3>
            <button onclick="closeEditUserModal()" class="text-gray-400 hover:text-gray-500">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <form id="edit-user-form" method="POST" action="">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="edit-nom_complet" class="block text-sm font-medium text-gray-700">Nom complet</label>
                <input type="text" id="edit-nom_complet" name="nom_complet" required
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-saar-blue focus:border-saar-blue">
            </div>

            <div class="mb-4">
                <label for="edit-email" class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" id="edit-email" name="email" required
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-saar-blue focus:border-saar-blue">
            </div>

            <div class="mb-4">
                <label for="edit-role" class="block text-sm font-medium text-gray-700">Rôle</label>
                <select id="edit-role" name="role" required onchange="toggleEditAssureField()"
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-saar-blue focus:border-saar-blue">
                    <option value="admin">Administrateur</option>
                    <option value="gestionnaire">Gestionnaire</option>
                    <option value="assure">Assuré</option>
                </select>
            </div>

            <div id="edit-numero-assure-container" class="mb-4 hidden">
                <label for="edit-numero_assure" class="block text-sm font-medium text-gray-700">Numéro
                    assuré</label>
                <input type="text" id="edit-numero_assure" name="numero_assure"
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-saar-blue focus:border-saar-blue">
            </div>

            <div class="flex justify-end space-x-3 mt-6">
                <button type="button" onclick="closeEditUserModal()"
                    class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Annuler
                </button>
                <button type="submit"
                    class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-saar-blue hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-saar-blue">
                    Enregistrer
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal d'informations utilisateur -->
<div id="info-user-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-1/2 lg:w-1/3 shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-800">Informations de l'utilisateur</h3>
            <button onclick="closeInfoUserModal()" class="text-gray-400 hover:text-gray-500">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Nom complet</label>
            <div id="info-nom_complet" class="mt-1 text-gray-900 font-semibold"></div>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Email</label>
            <div id="info-email" class="mt-1 text-gray-900"></div>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Rôle</label>
            <div id="info-role" class="mt-1 text-gray-900"></div>
        </div>
        <div id="info-numero-assure-container" class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Numéro assuré</label>
            <div id="info-numero_assure" class="mt-1 text-gray-900"></div>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Statut</label>
            <div id="info-actif" class="mt-1 text-gray-900"></div>
        </div>
        <div class="flex justify-end mt-6">
            <button type="button" onclick="closeInfoUserModal()"
                class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                Fermer
            </button>
        </div>
    </div>
</div>