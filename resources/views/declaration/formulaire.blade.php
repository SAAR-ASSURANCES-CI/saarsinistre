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
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="SAAR Sinistre">
    <link rel="apple-touch-icon" href="/icons/icon-192x192.svg">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'saar-red': '#FF0000',
                        'saar-blue': '#1E40AF',
                        'saar-green': '#059669',
                    },
                    animation: {
                        'slide-in': 'slideIn 0.5s ease-out',
                        'fade-in': 'fadeIn 0.3s ease-out',
                        'bounce-in': 'bounceIn 0.6s ease-out',
                        'pulse-slow': 'pulse 2s infinite',
                    },
                    keyframes: {
                        slideIn: {
                            '0%': {
                                opacity: '0',
                                transform: 'translateX(20px)'
                            },
                            '100%': {
                                opacity: '1',
                                transform: 'translateX(0)'
                            }
                        },
                        fadeIn: {
                            '0%': {
                                opacity: '0'
                            },
                            '100%': {
                                opacity: '1'
                            }
                        },
                        bounceIn: {
                            '0%': {
                                opacity: '0',
                                transform: 'scale(0.9)'
                            },
                            '50%': {
                                transform: 'scale(1.05)'
                            },
                            '100%': {
                                opacity: '1',
                                transform: 'scale(1)'
                            }
                        }
                    }
                }
            }
        }
    </script>
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
    
    <script src="{{ asset('js/declaration.js') }}"></script>
    
    <!-- Upload progressif pour mobile -->
    <script src="{{ asset('js/progressive-upload.js') }}"></script>
    
    <!-- PWA Script -->
    <script src="{{ asset('js/pwa.js') }}"></script>
</body>

</html>
