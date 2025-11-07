<!DOCTYPE html>
<html lang="fr">

<head>
    @include('admin.partials.head', ['title' => 'Tableau de bord - SAAR Assurances'])
</head>

<body class="bg-gradient-to-br from-red-50 via-white to-green-50 min-h-screen">

    <!-- Header -->
    @include('admin.partials.header')

    <!-- Navbar Horizontale -->
    @include('admin.partials.navbar')

    <div class="container mx-auto px-4 py-8">
        <div class="grid grid-cols-1 gap-6">
            @include('admin.partials.stats-cards')
            @include('admin.partials.search-filters')
            @include('admin.partials.sinistres-table')
        </div>
    </div>

    <footer class="w-full py-3 bg-white/80 border-t border-gray-200 text-center text-xs text-gray-500 mt-8">
        © Saar Assurances Côte d'Ivoire. Tous droits réservés.
    </footer>

    @include('admin.modals.assign')
    @include('admin.modals.status')
    @include('admin.modals.details')

    <script src="{{ asset('js/Dashboard/utils.js') }}?v={{ config('app.asset_version') }}"></script>
    <script src="{{ asset('js/Dashboard/api.js') }}?v={{ config('app.asset_version') }}"></script>
    <script src="{{ asset('js/Dashboard/notifications.js') }}?v={{ config('app.asset_version') }}"></script>
    <script src="{{ asset('js/Dashboard/modals.js') }}?v={{ config('app.asset_version') }}"></script>
    <script src="{{ asset('js/Dashboard/sinistres.js') }}?v={{ config('app.asset_version') }}"></script>
    <script src="{{ asset('js/Dashboard/dashboard.js') }}?v={{ config('app.asset_version') }}"></script>
</body>

</html>
