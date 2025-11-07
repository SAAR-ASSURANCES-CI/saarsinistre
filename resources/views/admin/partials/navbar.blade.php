<nav class="bg-gradient-to-r from-saar-red to-red-600 shadow-md">
    <div class="container mx-auto px-4">
        <div class="flex space-x-0">
            <!-- Tableau de bord -->
            <a href="{{ route('gestionnaires.dashboard') }}"
                class="menu-item flex items-center space-x-2 px-6 py-4 {{ request()->routeIs('gestionnaires.dashboard') ? 'text-white bg-white/20 border-white' : 'text-white/80 hover:text-white hover:bg-white/10' }} transition-all duration-200 border-b-3 border-transparent hover:border-white/50">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 5a2 2 0 012-2h4a2 2 0 012 2v6H8V5z"></path>
                </svg>
                <span class="font-medium">Tableau de bord</span>
            </a>

            <!-- Média -->
            <a href="{{ route('gestionnaires.dashboard.media.index') }}"
                class="menu-item flex items-center space-x-2 px-6 py-4 {{ request()->routeIs('gestionnaires.dashboard.media.*') ? 'text-white bg-white/20 border-white' : 'text-white/80 hover:text-white hover:bg-white/10' }} transition-all duration-200 border-b-3 border-transparent hover:border-white/50">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2h4a1 1 0 011 1v14a2 2 0 01-2 2H4a2 2 0 01-2-2V5a1 1 0 011-1h4z">
                    </path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m-4 4h4">
                    </path>
                </svg>
                <span class="font-medium">Média</span>
            </a>

            <!-- Utilisateurs (visible uniquement pour les administrateurs) -->
            @if(auth()->user()->isAdmin())
            <a href="{{ route('gestionnaires.dashboard.users.index') }}"
                class="menu-item flex items-center space-x-2 px-6 py-4 {{ request()->routeIs('gestionnaires.dashboard.users.*') ? 'text-white bg-white/20 border-white' : 'text-white/80 hover:text-white hover:bg-white/10' }} transition-all duration-200 border-b-3 border-transparent hover:border-white/50">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z">
                    </path>
                </svg>
                <span class="font-medium">Utilisateurs</span>
            </a>
            @endif

            <!-- Feedback -->
            <a href="{{ route('gestionnaires.dashboard.feedback.index') }}"
                class="menu-item flex items-center space-x-2 px-6 py-4 {{ request()->routeIs('gestionnaires.dashboard.feedback.*') ? 'text-white bg-white/20 border-white' : 'text-white/80 hover:text-white hover:bg-white/10' }} transition-all duration-200 border-b-3 border-transparent hover:border-white/50">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z">
                    </path>
                </svg>
                <span class="font-medium">Feedback</span>
            </a>
        </div>
    </div>
</nav>