<nav class="bg-white shadow-lg border-b-2 border-gray-100">
    <div class="container mx-auto px-4">
        <div class="flex items-center justify-between h-16">
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
                    <a href="/">
                        <h1
                            class="text-lg font-bold text-transparent bg-clip-text bg-gradient-to-r from-saar-red to-saar-red">
                            SAAR ASSURANCE</h1>
                    </a>
                    <p class="text-xs text-gray-600">Dashboard de Gestion</p>
                </div>
            </div>

            <div class="flex items-center space-x-4">
                <div class="relative">
                    <button onclick="Notifications.toggleNotifications()"
                        class="relative p-2 text-gray-600 hover:text-saar-blue transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 17h5l-3.405-3.405A2.032 2.032 0 0116 12V9a4.002 4.002 0 00-8 0v3c0 .601-.166 1.177-.595 1.595L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                            </path>
                        </svg>
                        <span id="notification-count"
                            class="absolute -top-1 -right-1 h-4 w-4 bg-red-500 text-white rounded-full text-xs flex items-center justify-center hidden">0</span>
                    </button>
                    <div id="notifications-dropdown"
                        class="hidden absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-lg border border-gray-200 z-50">
                        <div class="p-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Notifications</h3>
                        </div>
                        <div id="notifications-list" class="max-h-64 overflow-y-auto"></div>
                    </div>
                </div>

                <div class="relative">
                    <button onclick="toggleUserMenu()"
                        class="flex items-center space-x-2 p-2 text-gray-600 hover:text-saar-blue transition-colors">
                        <div
                            class="w-8 h-8 bg-gradient-to-r from-saar-blue to-blue-600 rounded-full flex items-center justify-center">
                            @if (Auth::check() && Auth::user()->nom_complet)
                                <span
                                    class="text-white text-sm font-semibold">{{ substr(Auth::user()->nom_complet, 0, 2) }}</span>
                            @else
                                <span class="text-white text-sm font-semibold">??</span>
                            @endif
                        </div>
                        <span class="text-sm font-medium">
                            @if (Auth::check() && Auth::user()->nom_complet)
                                {{ Auth::user()->nom_complet }}
                            @else
                                Utilisateur
                            @endif
                        </span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                            </path>
                        </svg>
                    </button>
                    <div id="user-menu"
                        class="hidden absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-gray-200 z-50">
                        <div class="py-2">
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profil</a>
                            <a href="#"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Paramètres</a>
                            <hr class="my-1">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">Déconnexion</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>

<script>
    function toggleUserMenu() {
        document.getElementById('user-menu').classList.toggle('hidden');
    }
</script>
