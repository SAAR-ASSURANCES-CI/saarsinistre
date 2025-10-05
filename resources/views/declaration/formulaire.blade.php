<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulaire de Déclaration - SAAR Assurances</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- PWA Meta Tags -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#dc2626">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="SAARCISinistres">
    <link rel="apple-touch-icon" sizes="180x180" href="/logo.png">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gradient-to-br from-red-50 via-white to-green-50 min-h-screen">
    @include('declaration.partials.header')
    <div class="container mx-auto px-4 py-8">
        @include('declaration.partials.progress')
        <div class="max-w-4xl mx-auto">
            <form id="declaration-form" class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
                @include('declaration.partials.step1-infos')
                @include('declaration.partials.step2-sinistre')
                @include('declaration.partials.step3-documents')
                @include('declaration.partials.step4-confirmation')
                @include('declaration.partials.navigation')
            </form>
        </div>
    </div>
    
    <!-- Service Worker Registration -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('/sw.js');
            });
        }
    </script>
    
    <!-- Gestionnaire d'upload optimisé -->
    <script src="{{ asset('js/upload-manager.js') }}"></script>
    <script src="{{ asset('js/declaration.js') }}"></script>
    
    <!-- PWA Script -->
    <script src="{{ asset('js/pwa.js') }}"></script>
</body>

</html>
