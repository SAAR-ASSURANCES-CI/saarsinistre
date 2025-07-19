<!DOCTYPE html>
<html lang="fr">

<head>
    @include('admin.partials.head', ['title' => 'Gestions des Utilisateurs - SAAR Assurance'])
    <style>
        a[disabled] {
            pointer-events: none;
            opacity: 0.6;
            cursor: not-allowed;
        }

        .pagination-btn:disabled {
            background-color: #e5e7eb !important;
            color: #9ca3af !important;
            border-color: #e5e7eb !important;
        }
    </style>
</head>

<body class="bg-gray-50">
    <!-- Header -->
    @include('admin.partials.header')

    <!-- Navbar Horizontale -->
    @include('admin.partials.navbar')

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
        @include('users.partials.filters')

        <!-- Statistiques rapides -->
        @include('users.partials.stats')

        <!-- Onglets -->
        @include('users.partials.tabs')

        <!-- Contenu des onglets -->
        @include('users.partials.tabscontent')


        <!-- Pagination -->
        @include('users.partials.paginate')
    </main>

    <!-- Modal componennt -->
    @include('users.partials.modals')

    <script src="{{ asset('js/users/users.js') }}"></script>
</body>

</html>
