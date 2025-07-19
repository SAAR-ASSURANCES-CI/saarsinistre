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
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-saar-blue focus:border-transparent"
                            onchange="toggleAssureField()">
                            <option value="">Sélectionner un rôle</option>
                            <option value="admin">Administrateur</option>
                            <option value="gestionnaire">Gestionnaire</option>
                            <option value="assure">Assuré</option>
                        </select>
                    </div>
                    <div id="numero-assure-container" class="hidden">
                        <label for="numero_assure" class="block text-sm font-medium text-gray-700 mb-1">Numéro
                            d'assuré</label>
                        <input type="text" name="numero_assure" id="numero_assure"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-saar-blue focus:border-transparent">
                    </div>
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Mot de
                            passe</label>
                        <input type="password" name="password" id="password" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-saar-blue focus:border-transparent">
                    </div>
                    <div>
                        <label for="password_confirmation"
                            class="block text-sm font-medium text-gray-700 mb-1">Confirmer le mot de passe</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-saar-blue focus:border-transparent">
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
