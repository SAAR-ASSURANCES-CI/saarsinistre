<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messagerie Sinistre - SAAR Assurance</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-red-50 via-white to-green-50 min-h-screen">
    <!-- Sticky Header -->
    <header class="sticky top-0 z-40 bg-white/90 backdrop-blur border-b border-gray-200 shadow-sm flex items-center justify-between px-4 py-3 md:px-8">
        <div class="flex items-center space-x-3">
            <a href="{{ route('assures.dashboard') }}" class="text-red-700 hover:text-red-900 mr-2" title="Retour">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
            </a>
            <span class="text-red-700 font-bold text-lg md:text-2xl">Messagerie Sinistre</span>
        </div>
        <div class="flex items-center space-x-2 md:space-x-4">
            <!-- Notification Bell avec badge et menu -->
            <div class="relative" id="notif-dropdown-wrapper">
                <button id="notif-bell" aria-label="Notifications" class="relative p-2 rounded-full hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-red-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    <span id="notif-badge" class="absolute top-1 right-1 bg-red-600 text-white text-xs rounded-full px-1.5 hidden">0</span>
                </button>
                <div id="notif-menu" class="hidden absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-lg border border-gray-200 z-50">
                    <div class="p-4 border-b border-gray-100 font-bold text-gray-700">Messages non lus</div>
                    <ul id="notif-list" class="max-h-64 overflow-y-auto divide-y divide-gray-100"></ul>
                    <div class="p-2 text-xs text-gray-400 text-center">Cliquez sur un message pour ouvrir la discussion.</div>
                </div>
            </div>
        </div>
    </header>
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
            <div class="flex items-center gap-3 mb-4">
                <svg class="w-6 h-6 text-red-700" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2v-8a2 2 0 012-2h2m2-4h4a2 2 0 012 2v4H7V6a2 2 0 012-2z" /></svg>
                <h2 class="text-xl font-bold text-red-700">
                    Discussion avec {{ Auth::id() === $sinistre->assure_id ? $sinistre->gestionnaire->nom_complet ?? 'Gestionnaire non assigné' : $sinistre->assure->nom_complet }}
                </h2>
            </div>
            <div class="mb-2 text-xs text-gray-500">
                <span class="font-semibold">Numéro du sinistre :</span> {{ $sinistre->numero_sinistre }}
            </div>
            <div id="chat-box" class="h-80 overflow-y-auto flex flex-col gap-2 mb-4 bg-gray-50 rounded-lg p-3 border border-gray-200">
                @foreach($messages as $message)
                    <div class="flex {{ $message->sender_id === Auth::id() ? 'justify-end' : 'justify-start' }}">
                        <div class="max-w-xs md:max-w-md px-4 py-2 rounded-lg shadow {{ $message->sender_id === Auth::id() ? 'bg-red-100 text-right' : 'bg-green-100 text-left' }}">
                            <div class="text-xs text-gray-500 mb-1">
                                {{ $message->sender->nom_complet }}
                                <span class="ml-2 text-gray-400">{{ $message->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                            <div class="whitespace-pre-line text-sm">{{ $message->contenu }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
            <form id="chat-form" class="flex gap-2">
                <input type="text" id="chat-input" name="contenu" class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-400 text-sm" placeholder="Écrire un message..." autocomplete="off" required maxlength="2000">
                <button type="submit" class="px-4 py-2 bg-red-700 text-white rounded-lg font-semibold hover:bg-red-800 transition">Envoyer</button>
            </form>
        </div>
    </div>
    <footer class="w-full py-4 bg-white/80 border-t border-gray-200 text-center text-xs text-gray-500 mt-8">
        © Saar Assurances Côte d'Ivoire. Tous droits réservés.
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://unpkg.com/laravel-echo/dist/echo.iife.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/pusher-js@8.2.0/dist/web/pusher.min.js"></script>
    <script>
        // Configuration Laravel Echo avec Reverb
        window.Echo = new window.Echo({
            broadcaster: 'reverb',
            key: '{{ config('broadcasting.connections.reverb.key') }}',
            wsHost: '{{ config('broadcasting.connections.reverb.host', request()->getHost()) }}',
            wsPort: {{ config('broadcasting.connections.reverb.port', 443) }},
            wssPort: {{ config('broadcasting.connections.reverb.port', 443) }},
            forceTLS: true,
            enabledTransports: ['ws', 'wss'],
            authEndpoint: '/broadcasting/auth',
            auth: {
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }
        });

        const userId = {{ Auth::id() }};
        const sinistreId = {{ $sinistre->id }};
        const chatBox = document.getElementById('chat-box');
        const chatForm = document.getElementById('chat-form');
        const chatInput = document.getElementById('chat-input');

        // Scroll en bas au chargement
        function scrollToBottom() {
            chatBox.scrollTop = chatBox.scrollHeight;
        }
        scrollToBottom();

        // Envoi AJAX du message
        chatForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const contenu = chatInput.value.trim();
            if (!contenu) return;
            axios.post(`/sinistres/${sinistreId}/chat`, { contenu })
                .then(res => {
                    appendMessage(res.data, true);
                    chatInput.value = '';
                    scrollToBottom();
                });
        });

        // Fonction d'ajout d'un message dans le chat
        function appendMessage(message, isMine = false) {
            const div = document.createElement('div');
            div.className = 'flex ' + (isMine ? 'justify-end' : 'justify-start');
            div.innerHTML = `<div class="max-w-xs md:max-w-md px-4 py-2 rounded-lg shadow ${isMine ? 'bg-red-100 text-right' : 'bg-green-100 text-left'}">
                <div class="text-xs text-gray-500 mb-1">
                    ${message.sender.nom_complet}
                    <span class="ml-2 text-gray-400">${new Date(message.created_at).toLocaleString('fr-FR')}</span>
                </div>
                <div class="whitespace-pre-line text-sm">${message.contenu}</div>
            </div>`;
            chatBox.appendChild(div);
            scrollToBottom();
        }

        // Gestion du menu déroulant notifications
        const notifBell = document.getElementById('notif-bell');
        const notifMenu = document.getElementById('notif-menu');
        notifBell.addEventListener('click', function(e) {
            notifMenu.classList.toggle('hidden');
            if (!notifMenu.classList.contains('hidden')) {
                fetchUnreadMessages();
            }
        });
        document.addEventListener('click', function(e) {
            if (!notifBell.contains(e.target) && !notifMenu.contains(e.target)) {
                notifMenu.classList.add('hidden');
            }
        });

        // Récupérer les messages non lus (AJAX)
        function fetchUnreadMessages() {
            axios.get('/notifications/unread-messages').then(res => {
                const notifList = document.getElementById('notif-list');
                notifList.innerHTML = '';
                const messages = res.data;
                if (messages.length === 0) {
                    notifList.innerHTML = '<li class="p-4 text-gray-400 text-center">Aucun message non lu</li>';
                } else {
                    messages.forEach(msg => {
                        const li = document.createElement('li');
                        li.className = 'p-3 hover:bg-red-50 cursor-pointer flex flex-col';
                        li.innerHTML = `<span class="font-semibold text-red-700">${msg.sender_nom}</span><span class="text-xs text-gray-500">${msg.created_at}</span><span class="text-sm mt-1">${msg.contenu.substring(0, 60)}${msg.contenu.length > 60 ? '…' : ''}</span>`;
                        li.onclick = function() {
                            window.location.href = `/sinistres/${msg.sinistre_id}/chat`;
                        };
                        notifList.appendChild(li);
                    });
                }
            });
        }

        // Badge dynamique
        function updateNotifBadge(count) {
            const badge = document.getElementById('notif-badge');
            if (count > 0) {
                badge.textContent = count;
                badge.classList.remove('hidden');
            } else {
                badge.classList.add('hidden');
            }
        }

        // Rafraîchir le badge au chargement et à chaque nouveau message
        function refreshNotifCount() {
            axios.get('/notifications/unread-messages/count').then(res => {
                updateNotifBadge(res.data.count);
            });
        }
        refreshNotifCount();

        // Mettre à jour le badge en temps réel
        window.Echo.private(`users.${userId}`)
            .listen('.new-message', (e) => {
                refreshNotifCount();
                if (e.sinistre_id == sinistreId) {
                    axios.get(`/sinistres/${sinistreId}/chat/fetch`).then(res => {
                        chatBox.innerHTML = '';
                        res.data.forEach(msg => appendMessage(msg, msg.sender_id === userId));
                    });
                }
            });
    </script>
</body>
</html> 