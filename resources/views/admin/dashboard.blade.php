<!DOCTYPE html>
<html lang="fr">

<head>
    @include('admin.partials.head', ['title' => 'Tableau de bord - SAAR Assurance'])
</head>

<body class="bg-gradient-to-br from-red-50 via-white to-green-50 min-h-screen">

    <!-- Header -->
    @include('admin.partials.header')

    <!-- Navbar Horizontale -->
    @include('admin.partials.navbar')

    <div class="container mx-auto px-4 py-8">
        @include('admin.partials.stats-cards')
        @include('admin.partials.search-filters')
        @include('admin.partials.sinistres-table')
    </div>

    @include('admin.modals.assign')
    @include('admin.modals.status')
    @include('admin.modals.details')

    <script src="{{ asset('js/Dashboard/utils.js') }}"></script>
    <script src="{{ asset('js/Dashboard/api.js') }}"></script>
    <script src="{{ asset('js/Dashboard/notifications.js') }}"></script>
    <script src="{{ asset('js/Dashboard/modals.js') }}"></script>
    <script src="{{ asset('js/Dashboard/sinistres.js') }}"></script>
    <script src="{{ asset('js/Dashboard/dashboard.js') }}"></script>
</body>

</html>
