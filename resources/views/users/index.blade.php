<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SAAR ASSURANCE - Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'saar-red': '#dc2626',
                        'saar-green': '#16a34a',
                        'saar-blue': '#2563eb'
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-gray-50">
    <!-- Header -->
    <nav class="bg-white shadow-lg border-b-2 border-gray-100">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between h-16">
                <!-- Logo -->
                <div class="flex items-center space-x-4">
                    <div
                        class="w-10 h-10 bg-gradient-to-r from-saar-red to-red-500 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                            </path>
                        </svg>
                    </div>
                    <div>
                        <h1
                            class="text-lg font-bold text-transparent bg-clip-text bg-gradient-to-r from-saar-red to-saar-green">
                            SAAR ASSURANCE
                        </h1>
                        <p class="text-xs text-gray-600">Dashboard de Gestion</p>
                    </div>
                </div>

                <!-- User Menu -->
                <div class="flex items-center space-x-4">
                    <div class="relative">
                        <button onclick="toggleNotifications()"
                            class="relative p-2 text-gray-600 hover:text-saar-blue transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 17h5l-3.405-3.405A2.032 2.032 0 0116 12V9a4.002 4.002 0 00-8 0v3c0 .601-.166 1.177-.595 1.595L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                                </path>
                            </svg>
                            <span id="notification-count"
                                class="absolute -top-1 -right-1 h-4 w-4 bg-red-500 text-white rounded-full text-xs flex items-center justify-center hidden">0</span>
                        </button>

                        <!-- Notifications Dropdown -->
                        <div id="notifications-dropdown"
                            class="hidden absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-lg border border-gray-200 z-50">
                            <div class="p-4 border-b border-gray-200">
                                <h3 class="text-lg font-semibold text-gray-900">Notifications</h3>
                            </div>
                            <div id="notifications-list" class="max-h-64 overflow-y-auto">
                                <!-- Notifications chargées dynamiquement -->
                            </div>
                        </div>
                    </div>

                    <div class="relative">
                        <button onclick="toggleUserMenu()"
                            class="flex items-center space-x-2 p-2 text-gray-600 hover:text-saar-blue transition-colors">
                            <div
                                class="w-8 h-8 bg-gradient-to-r from-saar-blue to-blue-600 rounded-full flex items-center justify-center">
                                <span class="text-white text-sm font-semibold">AD</span>
                            </div>
                            <span class="text-sm font-medium">Admin User</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <!-- User Dropdown -->
                        <div id="user-menu"
                            class="hidden absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-gray-200 z-50">
                            <div class="py-2">
                                <a href="#"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profil</a>
                                <a href="#"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Paramètres</a>
                                <hr class="my-1">
                                <button type="button"
                                    class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                    Déconnexion
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Navbar Horizontale -->
    <nav class="bg-gradient-to-r from-saar-red to-red-600 shadow-md">
        <div class="container mx-auto px-4">
            <div class="flex space-x-0">
                <!-- Tableau de bord -->
                <a href="{{ route('dashboard') }}"
                    class="menu-item flex items-center space-x-2 px-6 py-4 text-white/80 hover:text-white hover:bg-white/10 transition-all duration-200 border-b-3 border-transparent hover:border-white/50">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 5a2 2 0 012-2h4a2 2 0 012 2v6H8V5z"></path>
                    </svg>
                    <span class="font-medium">Tableau de bord</span>
                </a>

                <!-- Utilisateurs -->
                <a href="{{ route('dashboard.users') }}"
                    class="menu-item active flex items-center space-x-2 px-6 py-4 text-white hover:bg-white/10 transition-all duration-200 border-b-3 border-transparent hover:border-white/50">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z">
                        </path>
                    </svg>
                    <span class="font-medium">Utilisateurs</span>
                </a>

                <!-- Média -->
                <a href="#"
                    class="menu-item flex items-center space-x-2 px-6 py-4 text-white/80 hover:text-white hover:bg-white/10 transition-all duration-200 border-b-3 border-transparent hover:border-white/50">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2h4a1 1 0 011 1v14a2 2 0 01-2 2H4a2 2 0 01-2-2V5a1 1 0 011-1h4z">
                        </path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m-4 4h4">
                        </path>
                    </svg>
                    <span class="font-medium">Média</span>
                </a>
            </div>
        </div>
    </nav>

    <!-- Contenu principal - Vue Utilisateurs -->
    <main class="container mx-auto px-4 py-8">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Gestion des Utilisateurs</h2>
                <p class="text-gray-600 mt-1">Gérez les utilisateurs et leurs permissions</p>
            </div>
            <button onclick="openAddUserModal()"
                class="bg-gradient-to-r from-saar-blue to-blue-600 text-white px-6 py-3 rounded-lg hover:shadow-lg transition-all duration-200 flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                <span>Nouvel Utilisateur</span>
            </button>
        </div>

        <!-- Filtres et recherche -->
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
                    <option value="user">Utilisateur</option>
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

        <!-- Statistiques rapides -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Total Utilisateurs</p>
                        <p class="text-2xl font-bold text-gray-800" id="total-users">15</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-saar-blue" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z">
                            </path>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Gestionnaires</p>
                        <p class="text-2xl font-bold text-gray-800" id="total-managers">8</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Utilisateurs Actifs</p>
                        <p class="text-2xl font-bold text-gray-800" id="active-users">12</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z">
                            </path>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Comptes Suspendus</p>
                        <p class="text-2xl font-bold text-gray-800" id="suspended-users">3</p>
                    </div>
                    <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728">
                            </path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table des utilisateurs -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Utilisateur
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Email
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Rôle
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Statut
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Sinistres
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Limite
                            </th>
                            <th
                                class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody id="users-table-body" class="bg-white divide-y divide-gray-200">
                        <!-- Les données seront injectées ici via JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <div class="flex items-center justify-between mt-6">
            <div class="flex items-center text-sm text-gray-700">
                <span>Affichage de <span class="font-medium">1</span> à <span class="font-medium">10</span> sur <span
                        class="font-medium">15</span> résultats</span>
            </div>
            <div class="flex items-center space-x-2">
                <button
                    class="px-3 py-2 text-sm bg-white border border-gray-300 rounded-md hover:bg-gray-50 disabled:opacity-50"
                    disabled>
                    Précédent
                </button>
                <button class="px-3 py-2 text-sm bg-saar-blue text-white rounded-md">1</button>
                <button
                    class="px-3 py-2 text-sm bg-white border border-gray-300 rounded-md hover:bg-gray-50">2</button>
                <button class="px-3 py-2 text-sm bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                    Suivant
                </button>
            </div>
        </div>
    </main>

    <script>
        // Toggle notifications dropdown
        function toggleNotifications() {
            const dropdown = document.getElementById('notifications-dropdown');
            dropdown.classList.toggle('hidden');
        }

        // Toggle user menu dropdown
        function toggleUserMenu() {
            const menu = document.getElementById('user-menu');
            menu.classList.toggle('hidden');
        }

        // Set active menu
        function setActiveMenu(element) {
            // Remove active class from all menu items
            document.querySelectorAll('.menu-item').forEach(item => {
                item.classList.remove('active');
                item.classList.remove('border-white');
                item.classList.add('text-white/80', 'border-transparent');
            });

            // Add active class to clicked item
            element.classList.add('active');
            element.classList.remove('text-white/80', 'border-transparent');
            element.classList.add('text-white', 'border-white');

            // Show corresponding view
            const views = document.querySelectorAll('.content-view');
            views.forEach(view => view.classList.add('hidden'));

            const menuText = element.textContent.trim().toLowerCase();
            if (menuText.includes('tableau')) {
                document.getElementById('dashboard-view').classList.remove('hidden');
            } else if (menuText.includes('utilisateurs')) {
                document.getElementById('users-view').classList.remove('hidden');
                loadUsers(); // Charger les données utilisateurs
            } else if (menuText.includes('média')) {
                document.getElementById('media-view').classList.remove('hidden');
            }
        }

        // Données d'exemple pour les utilisateurs
        const sampleUsers = [{
                id: 1,
                nom_complet: "Marie Dupont",
                email: "marie.dupont@saar.com",
                role: "admin",
                actif: true,
                sinistres_en_cours: 5,
                limite_sinistres: 20,
                created_at: "2024-01-15"
            },
            {
                id: 2,
                nom_complet: "Jean Martin",
                email: "jean.martin@saar.com",
                role: "gestionnaire",
                actif: true,
                sinistres_en_cours: 12,
                limite_sinistres: 15,
                created_at: "2024-02-10"
            },
            {
                id: 3,
                nom_complet: "Sophie Laurent",
                email: "sophie.laurent@saar.com",
                role: "gestionnaire",
                actif: true,
                sinistres_en_cours: 8,
                limite_sinistres: 15,
                created_at: "2024-01-20"
            },
            {
                id: 4,
                nom_complet: "Pierre Dubois",
                email: "pierre.dubois@saar.com",
                role: "user",
                actif: false,
                sinistres_en_cours: 0,
                limite_sinistres: 5,
                created_at: "2024-03-05"
            },
            {
                id: 5,
                nom_complet: "Amélie Bernard",
                email: "amelie.bernard@saar.com",
                role: "gestionnaire",
                actif: true,
                sinistres_en_cours: 7,
                limite_sinistres: 15,
                created_at: "2024-02-28"
            }
        ];

        // Charger les utilisateurs
        function loadUsers() {
            const tbody = document.getElementById('users-table-body');
            tbody.innerHTML = '';

            sampleUsers.forEach(user => {
                const row = createUserRow(user);
                tbody.appendChild(row);
            });

            updateUserStats();
        }

        // Créer une ligne utilisateur
        function createUserRow(user) {
            const row = document.createElement('tr');
            row.className = 'hover:bg-gray-50 transition-colors';

            const statusBadge = user.actif ?
                '<span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">Actif</span>' :
                '<span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-800 rounded-full">Inactif</span>';

            const roleBadge = {
                'admin': '<span class="px-2 py-1 text-xs font-medium bg-purple-100 text-purple-800 rounded-full">Administrateur</span>',
                'gestionnaire': '<span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">Gestionnaire</span>',
                'user': '<span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-800 rounded-full">Utilisateur</span>'
            } [user.role];

            const progressPercentage = (user.sinistres_en_cours / user.limite_sinistres) * 100;
            const progressColor = progressPercentage > 80 ? 'bg-red-500' : progressPercentage > 60 ? 'bg-yellow-500' :
                'bg-green-500';

            row.innerHTML = `
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-gradient-to-r from-saar-blue to-blue-600 rounded-full flex items-center justify-center">
                            <span class="text-white text-sm font-semibold">${user.nom_complet.split(' ').map(n => n[0]).join('')}</span>
                        </div>
                        <div class="ml-4">
                            <div class="text-sm font-medium text-gray-900">${user.nom_complet}</div>
                            <div class="text-sm text-gray-500">ID: ${user.id}</div>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">${user.email}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    ${roleBadge}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    ${statusBadge}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center space-x-2">
                        <span class="text-sm font-medium text-gray-900">${user.sinistres_en_cours}</span>
                        <div class="w-16 bg-gray-200 rounded-full h-2">
                            <div class="${progressColor} h-2 rounded-full" style="width: ${Math.min(progressPercentage, 100)}%"></div>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    ${user.limite_sinistres}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <div class="flex items-center space-x-2 justify-end">
                        <button onclick="editUser(${user.id})" class="text-saar-blue hover:text-blue-800 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </button>
                        <button onclick="toggleUserStatus(${user.id})" class="text-gray-600 hover:text-gray-800 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </button>
                        <button onclick="deleteUser(${user.id})" class="text-red-600 hover:text-red-800 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </div>
                </td>
            `;

            return row;
        }

        // Mettre à jour les statistiques
        function updateUserStats() {
            const totalUsers = sampleUsers.length;
            const activeUsers = sampleUsers.filter(u => u.actif).length;
            const managers = sampleUsers.filter(u => u.role === 'gestionnaire' || u.role === 'admin').length;
            const suspendedUsers = sampleUsers.filter(u => !u.actif).length;

            document.getElementById('total-users').textContent = totalUsers;
            document.getElementById('active-users').textContent = activeUsers;
            document.getElementById('total-managers').textContent = managers;
            document.getElementById('suspended-users').textContent = suspendedUsers;
        }

        // Actions des utilisateurs
        function editUser(id) {
            alert(`Éditer l'utilisateur ${id}`);
        }

        function toggleUserStatus(id) {
            alert(`Basculer le statut de l'utilisateur ${id}`);
        }

        function deleteUser(id) {
            if (confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')) {
                alert(`Supprimer l'utilisateur ${id}`);
            }
        }

        function openAddUserModal() {
            alert('Ouvrir le modal d\'ajout d\'utilisateur');
        }

        function resetFilters() {
            document.getElementById('search-users').value = '';
            document.getElementById('filter-role').value = '';
            document.getElementById('filter-status').value = '';
            loadUsers();
        }

        // Close dropdowns when clicking outside
        document.addEventListener('click', function(event) {
            const notificationsDropdown = document.getElementById('notifications-dropdown');
            const userMenu = document.getElementById('user-menu');

            if (!event.target.closest('.relative')) {
                notificationsDropdown.classList.add('hidden');
                userMenu.classList.add('hidden');
            }
        });
    </script>

    <style>
        .menu-item.active {
            background: rgba(255, 255, 255, 0.15);
            border-bottom-color: white !important;
        }
    </style>
</body>

</html>
