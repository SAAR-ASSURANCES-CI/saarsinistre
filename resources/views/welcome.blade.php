<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SAAR Assurance Côte d'Ivoire - Déclaration de Sinistre</title>
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#0d6efd">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'saar-orange': '#FF0000',
                        'saar-blue': '#1E40AF',
                        'saar-green': '#059669',
                        'ivory': '#FFF8DC'
                    },
                    animation: {
                        'float': 'float 6s ease-in-out infinite',
                        'pulse-slow': 'pulse 3s ease-in-out infinite',
                        'fade-in-up': 'fadeInUp 0.8s ease-out',
                        'slide-in-right': 'slideInRight 0.8s ease-out',
                    },
                    keyframes: {
                        float: {
                            '0%, 100%': {
                                transform: 'translateY(0px)'
                            },
                            '50%': {
                                transform: 'translateY(-20px)'
                            }
                        },
                        fadeInUp: {
                            '0%': {
                                opacity: '0',
                                transform: 'translateY(30px)'
                            },
                            '100%': {
                                opacity: '1',
                                transform: 'translateY(0)'
                            }
                        },
                        slideInRight: {
                            '0%': {
                                opacity: '0',
                                transform: 'translateX(50px)'
                            },
                            '100%': {
                                opacity: '1',
                                transform: 'translateX(0)'
                            }
                        }
                    }
                }
            }
        }
    </script>
    <script>
      if ('serviceWorker' in navigator) {
        window.addEventListener('load', function() {
          navigator.serviceWorker.register('/sw.js');
        });
      }
    </script>
    <script src="/js/pwa.js"></script>
</head>

<body class="bg-gradient-to-br from-red-50 via-white to-green-50 min-h-screen overflow-x-hidden">

    <!-- Éléments décoratifs flottants -->
    <div class="fixed inset-0 pointer-events-none overflow-hidden">
        <div class="absolute top-20 left-10 w-32 h-32 bg-saar-orange/10 rounded-full animate-float"></div>
        <div class="absolute top-40 right-20 w-24 h-24 bg-saar-blue/10 rounded-full animate-float"
            style="animation-delay: -2s;"></div>
        <div class="absolute bottom-40 left-20 w-20 h-20 bg-saar-green/10 rounded-full animate-float"
            style="animation-delay: -4s;"></div>
        <div class="absolute bottom-20 right-10 w-28 h-28 bg-red-200/20 rounded-full animate-float"
            style="animation-delay: -1s;"></div>
    </div>

    <div class="relative z-10 container mx-auto px-4 py-8">

        <!-- Bouton Connexion en haut à droite -->
        <div class="absolute top-8 right-8 z-20">
            <div class="inline-flex space-x-2">
                <a href="{{ route('login.assure') }}" class="px-5 py-2 bg-saar-green text-white font-semibold rounded-xl shadow hover:bg-green-700 transition-all">Espace assuré</a>
            </div>
        </div>

        <!-- Header avec logo et branding -->
        <div class="text-center mb-16 animate-fade-in-up">
            <div class="inline-flex items-center justify-center mb-8">
                <div class="relative">
                    <div
                        class="w-24 h-24 bg-gradient-to-r from-saar-orange to-red-400 rounded-2xl shadow-2xl flex items-center justify-center transform rotate-3 hover:rotate-0 transition-transform duration-500">
                        <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                            </path>
                        </svg>
                    </div>
                    <div class="absolute -top-2 -right-2 w-6 h-6 bg-green-400 rounded-full animate-pulse-slow"></div>
                </div>
            </div>

            <h1
                class="text-5xl md:text-6xl font-black text-transparent bg-clip-text bg-gradient-to-r from-saar-orange via-red-500 to-saar-orange mb-4">
                SAAR ASSURANCES
            </h1>
            <p class="text-xl md:text-2xl font-semibold text-saar-blue mb-6">
                CÔTE D'IVOIRE
            </p>
            <div class="w-24 h-1 bg-gradient-to-r from-saar-orange to-saar-green mx-auto mb-8 rounded-full"></div>
            <p class="text-xl text-gray-700 max-w-3xl mx-auto leading-relaxed">
                Déclarez votre sinistre automobile en toute simplicité.
                <span class="font-semibold text-saar-blue">Notre expertise à votre service</span>
                pour un traitement rapide et efficace de votre dossier.
            </p>
        </div>

        <!-- Carte principale améliorée -->
        <div class="max-w-5xl mx-auto mb-16 animate-slide-in-right">
            <div class="relative bg-white rounded-3xl shadow-2xl overflow-hidden border border-gray-100">

                <!-- Gradient décoratif en haut -->
                <div class="h-2 bg-gradient-to-r from-saar-orange via-red-400 to-saar-green"></div>

                <div class="px-8 md:px-12 py-16 text-center">
                    <div class="mb-12">
                        <div class="relative inline-flex items-center justify-center mb-8">
                            <div
                                class="w-20 h-20 bg-gradient-to-r from-green-400 to-saar-green rounded-2xl shadow-xl flex items-center justify-center animate-pulse-slow">
                                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                            </div>
                            <div class="absolute -top-1 -right-1 w-6 h-6 bg-saar-orange rounded-full animate-ping">
                            </div>
                        </div>

                        <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                            Nouvelle Déclaration de Sinistre
                        </h2>
                        <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                            Processus simplifié en quelques étapes. Téléchargez vos documents et suivez l'avancement en
                            temps réel.
                        </p>
                    </div>

                    <!-- Bouton principal amélioré -->
                    <div class="mb-8">
                        <a href="{{ route('declaration.create') }}"
                            class="group relative inline-flex items-center px-10 py-5 bg-gradient-to-r from-saar-orange to-red-500 hover:from-red-500 hover:to-saar-orange text-white font-bold text-lg rounded-2xl transition-all duration-300 transform hover:scale-105 shadow-xl hover:shadow-2xl">

                            <!-- Effet brillant -->
                            <div
                                class="absolute inset-0 bg-gradient-to-r from-white/20 to-transparent rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            </div>

                            <svg class="w-6 h-6 mr-3 group-hover:rotate-12 transition-transform duration-300"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                            <span class="relative">Déclarer mon sinistre</span>

                            <!-- Flèche animée -->
                            <svg class="w-5 h-5 ml-3 group-hover:translate-x-2 transition-transform duration-300"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                            </svg>
                        </a>
                    </div>

                    <!-- Badge de confiance -->
                    <div
                        class="inline-flex items-center px-4 py-2 bg-green-50 border border-green-200 rounded-full text-green-700 text-sm font-medium">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd"></path>
                        </svg>
                        Processus 100% sécurisé
                    </div>
                </div>

                <!-- Section avantages améliorée -->
                <div class="bg-gradient-to-r from-gray-50 to-red-50/30 px-8 md:px-12 py-10">
                    <div class="grid md:grid-cols-3 gap-8">

                        <div class="group text-center hover:transform hover:scale-105 transition-all duration-300">
                            <div
                                class="relative inline-flex items-center justify-center w-16 h-16 bg-gradient-to-r from-blue-500 to-saar-blue rounded-2xl shadow-lg mb-4 group-hover:shadow-xl">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div class="absolute -top-1 -right-1 w-4 h-4 bg-saar-orange rounded-full animate-ping">
                                </div>
                            </div>
                            <h3 class="font-bold text-gray-900 mb-2 text-lg">Traitement Rapide</h3>
                            <p class="text-gray-600">Suivi en temps réel et notifications automatiques</p>
                        </div>

                        <div class="group text-center hover:transform hover:scale-105 transition-all duration-300">
                            <div
                                class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-r from-saar-green to-green-600 rounded-2xl shadow-lg mb-4 group-hover:shadow-xl">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                    </path>
                                </svg>
                            </div>
                            <h3 class="font-bold text-gray-900 mb-2 text-lg">Communication</h3>
                            <p class="text-gray-600">Mises à jour par email et SMS</p>
                        </div>

                        <div class="group text-center hover:transform hover:scale-105 transition-all duration-300">
                            <div
                                class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-r from-purple-500 to-purple-600 rounded-2xl shadow-lg mb-4 group-hover:shadow-xl">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <h3 class="font-bold text-gray-900 mb-2 text-lg">Sécurité Garantie</h3>
                            <p class="text-gray-600">Protection maximale de vos données</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section contact améliorée -->
        <div class="max-w-4xl mx-auto">
            <div class="bg-gradient-to-r from-saar-blue to-blue-700 rounded-3xl shadow-2xl overflow-hidden text-white">
                <div class="px-8 md:px-12 py-12 text-center">
                    <div class="mb-8">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-white/20 rounded-2xl mb-6">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192L5.636 18.364M12 2.25a9.75 9.75 0 109.75 9.75c0-1.052-.18-2.062-.512-3.011z">
                                </path>
                            </svg>
                        </div>
                        <h3 class="text-3xl font-bold mb-4">Besoin d'assistance ?</h3>
                        <p class="text-xl text-blue-100 max-w-2xl mx-auto">
                            Notre équipe d'experts est disponible pour vous accompagner dans toutes vos démarches
                        </p>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-6 justify-center">
                        <a href="tel:+22500000000"
                            class="group inline-flex items-center px-8 py-4 bg-white/20 hover:bg-white/30 border border-white/30 rounded-xl text-white font-semibold transition-all duration-300 hover:transform hover:scale-105">
                            <svg class="w-6 h-6 mr-3 group-hover:rotate-12 transition-transform duration-300"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z">
                                </path>
                            </svg>
                            Appelez-nous
                        </a>

                        <a href="mailto:sinistres@saar-assurance.com"
                            class="group inline-flex items-center px-8 py-4 bg-saar-orange hover:bg-red-500 rounded-xl text-white font-semibold transition-all duration-300 hover:transform hover:scale-105 shadow-lg">
                            <svg class="w-6 h-6 mr-3 group-hover:rotate-12 transition-transform duration-300"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                </path>
                            </svg>
                            Écrivez-nous
                        </a>
                    </div>

                    <!-- Informations de contact -->
                    <div class="mt-8 pt-8 border-t border-white/20">
                        <p class="text-blue-100 text-sm">
                            Du lundi au vendredi : 8h00 - 18h00 | Samedi : 8h00 - 13h00
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center mt-16 pt-8 border-t border-gray-200">
            <p class="text-gray-600">
                © 2025 SAAR Assurance Côte d'Ivoire - Tous droits réservés
            </p>
        </div>
    </div>

    <script>
        // Animation d'apparition au scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        // Observer tous les éléments avec animation
        document.querySelectorAll('.animate-slide-in-right').forEach(el => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(30px)';
            el.style.transition = 'all 0.8s ease-out';
            observer.observe(el);
        });
    </script>
</body>

</html>
