<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messagerie Sinistre - SAAR Assurance</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(5px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes pulseSlow {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        .animate-fade-in {
            animation: fadeIn 0.3s ease-out forwards;
        }
        .animate-pulse-slow {
            animation: pulseSlow 2s infinite;
        }
        .animate-bounce-in {
            animation: fadeIn 0.5s, pulseSlow 2s 0.5s infinite;
        }
        .message-enter {
            opacity: 0;
            transform: translateY(10px);
        }
        .message-enter-active {
            opacity: 1;
            transform: translateY(0);
            transition: all 0.3s ease-out;
        }
        @media (max-width: 640px) {
            .chat-container {
                padding: 0.5rem;
            }
            .chat-box {
                height: 60vh;
            }
            .message-content {
                max-width: 80%;
            }
        }
    </style>
</head>
<body class="bg-gradient-to-br from-red-50 via-white to-green-50 min-h-screen">
    <!-- Sticky Header -->
    <header class="sticky top-0 z-40 bg-white/90 backdrop-blur border-b border-gray-200 shadow-sm flex items-center justify-between px-4 py-3 md:px-8">
        <div class="flex items-center space-x-3">
            <a href="#" class="text-red-700 hover:text-red-900 mr-2 transition-colors duration-200" title="Retour">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
            </a>
            <span class="text-red-700 font-bold text-lg md:text-2xl">Messagerie Sinistre</span>
        </div>
        <div class="flex items-center space-x-2 md:space-x-4">
            <!-- Notification Bell avec badge et menu -->
            <div class="relative" id="notif-dropdown-wrapper">
                <button id="notif-bell" aria-label="Notifications" class="relative p-2 rounded-full hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-red-400 transition-all duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    <span id="notif-badge" class="absolute top-1 right-1 bg-red-600 text-white text-xs rounded-full px-1.5 hidden">0</span>
                </button>
                <div id="notif-menu" class="hidden absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-lg border border-gray-200 z-50 transition-all duration-200 transform origin-top-right">
                    <div class="p-4 border-b border-gray-100 font-bold text-gray-700">Messages non lus</div>
                    <ul id="notif-list" class="max-h-64 overflow-y-auto divide-y divide-gray-100"></ul>
                    <div class="p-2 text-xs text-gray-400 text-center">Cliquez sur un message pour ouvrir la discussion.</div>
                </div>
            </div>
        </div>
    </header>

    <div class="container mx-auto px-4 py-8 chat-container">
        <div class="max-w-4xl mx-auto bg-white rounded-2xl shadow-lg border border-gray-100 p-4 md:p-6 transition-all duration-200 hover:shadow-xl">
            <div class="flex items-center gap-3 mb-4">
                <svg class="w-6 h-6 text-red-700" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2v-8a2 2 0 012-2h2m2-4h4a2 2 0 012 2v4H7V6a2 2 0 012-2z" /></svg>
                <div>
                    <h2 class="text-lg md:text-xl font-bold text-red-700">
                        Discussion avec {{ Auth::id() === $sinistre->assure_id ? ($sinistre->gestionnaire->nom_complet ?? 'Gestionnaire non assigné') : $sinistre->assure->nom_complet }}
                    </h2>
                    <div class="text-xs text-gray-500">
                        <span class="font-semibold">Numéro du sinistre :</span> {{ $sinistre->numero_sinistre }}
                    </div>
                </div>
            </div>

            <div id="chat-box" class="chat-box h-96 md:h-80 overflow-y-auto flex flex-col gap-3 mb-4 bg-gray-50 rounded-lg p-3 border border-gray-200">
                @foreach($messages as $message)
                    @php
                        $isMine = $message->sender_id === Auth::id();
                        $bg = $isMine ? 'bg-red-100' : 'bg-blue-100';
                        $border = $isMine ? 'border-red-500' : 'border-blue-500';
                        $initiale = strtoupper(mb_substr($message->sender->nom_complet, 0, 1));
                        $avatarBg = $isMine ? 'bg-red-600' : 'bg-blue-600';
                    @endphp
                    <div class="flex items-end {{ $isMine ? 'justify-end' : 'justify-start' }} message-enter">
                        @if(!$isMine)
                            <div class="flex-shrink-0 w-8 h-8 rounded-full {{ $avatarBg }} flex items-center justify-center text-white font-bold shadow mr-2 transition-transform hover:scale-110">
                                {{ $initiale }}
                            </div>
                        @endif
                        <div class="max-w-xs md:max-w-md px-4 py-2 rounded-2xl shadow {{ $bg }} border-l-4 {{ $border }} {{ $isMine ? 'text-right' : 'text-left' }} transition-all duration-200 hover:shadow-md">
                            <div class="text-xs text-gray-500 mb-1 font-semibold">
                                {{ $message->sender->nom_complet }}
                            </div>
                            <div class="whitespace-pre-line text-sm">{{ $message->contenu }}</div>
                            <div class="text-[11px] text-gray-400 mt-1">{{ $message->created_at->format('d/m/Y H:i') }}</div>
                        </div>
                        @if($isMine)
                            <div class="flex-shrink-0 w-8 h-8 rounded-full {{ $avatarBg }} flex items-center justify-center text-white font-bold shadow ml-2 transition-transform hover:scale-110">
                                {{ $initiale }}
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            <form id="chat-form" class="flex gap-2 mt-2">
                <input type="text" id="chat-input" name="contenu" 
                       class="flex-1 px-4 py-2 border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-red-500 text-sm shadow-sm bg-white transition-all duration-200 hover:border-gray-400" 
                       placeholder="Écrire un message..." 
                       autocomplete="off" 
                       required 
                       maxlength="2000">
                <button type="submit" 
                        class="min-w-[80px] md:min-w-[100px] px-4 md:px-6 py-2 bg-red-600 text-white rounded-full font-semibold shadow-lg hover:bg-red-700 transition-all focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 animate-bounce-in text-sm md:text-base">
                    Envoyer
                </button>
            </form>
        </div>
    </div>

    <footer class="w-full py-4 bg-white/80 border-t border-gray-200 text-center text-xs text-gray-500 mt-8 transition-colors duration-200">
        © Saar Assurances Côte d'Ivoire. Tous droits réservés.
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://unpkg.com/laravel-echo/dist/echo.iife.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/pusher-js@8.2.0/dist/web/pusher.min.js"></script>
    <script>
        window.Echo = new Echo({
            broadcaster: 'reverb',
            key: '{{ config('broadcasting.connections.reverb.key') }}',
            wsHost: '{{ config('broadcasting.connections.reverb.host') }}',
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

        function scrollToBottom() {
            chatBox.scrollTop = chatBox.scrollHeight;
        }
        scrollToBottom();

        chatForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const contenu = chatInput.value.trim();
            if (!contenu) return;
            
            const submitBtn = chatForm.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-75');
            
            axios.post(`/sinistres/${sinistreId}/chat`, { contenu })
                .then(res => {
                    appendMessage(res.data, true);
                    chatInput.value = '';
                    scrollToBottom();
                })
                .finally(() => {
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('opacity-75');
                });
        });

        
        function appendMessage(message, isMine = false) {
            const initiale = message.sender.nom_complet ? message.sender.nom_complet.charAt(0).toUpperCase() : '?';
            const bg = isMine ? 'bg-red-100' : 'bg-blue-100';
            const border = isMine ? 'border-red-500' : 'border-blue-500';
            const align = isMine ? 'justify-end' : 'justify-start';
            const avatarBg = isMine ? 'bg-red-600' : 'bg-blue-600';
            
            const html = `
                <div class="flex items-end ${align} message-enter message-enter-active">
                    ${!isMine ? `<div class="flex-shrink-0 w-8 h-8 rounded-full ${avatarBg} flex items-center justify-center text-white font-bold shadow mr-2 transition-transform hover:scale-110">${initiale}</div>` : ''}
                    <div class="max-w-xs md:max-w-md px-4 py-2 rounded-2xl shadow ${bg} border-l-4 ${border} ${isMine ? 'text-right' : 'text-left'} transition-all duration-200 hover:shadow-md">
                        <div class="text-xs text-gray-500 mb-1 font-semibold">${message.sender.nom_complet}</div>
                        <div class="whitespace-pre-line text-sm">${message.contenu}</div>
                        <div class="text-[11px] text-gray-400 mt-1">${new Date(message.created_at).toLocaleString('fr-FR')}</div>
                    </div>
                    ${isMine ? `<div class="flex-shrink-0 w-8 h-8 rounded-full ${avatarBg} flex items-center justify-center text-white font-bold shadow ml-2 transition-transform hover:scale-110">${initiale}</div>` : ''}
                </div>
            `;
            
            const div = document.createElement('div');
            div.innerHTML = html;
            chatBox.appendChild(div.firstElementChild);
            scrollToBottom();
        }

        const notifBell = document.getElementById('notif-bell');
        const notifMenu = document.getElementById('notif-menu');
        notifBell.addEventListener('click', function(e) {
            notifMenu.classList.toggle('hidden');
            notifMenu.classList.toggle('scale-95');
            notifMenu.classList.toggle('opacity-0');
            if (!notifMenu.classList.contains('hidden')) {
                fetchUnreadMessages();
            }
        });
        document.addEventListener('click', function(e) {
            if (!notifBell.contains(e.target) && !notifMenu.contains(e.target)) {
                notifMenu.classList.add('hidden', 'scale-95', 'opacity-0');
            }
        });

        
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
                        li.className = 'p-3 hover:bg-red-50 cursor-pointer flex flex-col transition-colors duration-200';
                        li.innerHTML = `<span class="font-semibold text-red-700">${msg.sender_nom}</span><span class="text-xs text-gray-500">${msg.created_at}</span><span class="text-sm mt-1">${msg.contenu.substring(0, 60)}${msg.contenu.length > 60 ? '…' : ''}</span>`;
                        li.onclick = function() {
                            window.location.href = `/sinistres/${msg.sinistre_id}/chat`;
                        };
                        notifList.appendChild(li);
                    });
                }
            });
        }

        
        function updateNotifBadge(count) {
            const badge = document.getElementById('notif-badge');
            if (count > 0) {
                badge.textContent = count;
                badge.classList.remove('hidden');
                badge.classList.add('animate-pulse');
            } else {
                badge.classList.add('hidden');
                badge.classList.remove('animate-pulse');
            }
        }

        
        function refreshNotifCount() {
            axios.get('/notifications/unread-messages/count').then(res => {
                updateNotifBadge(res.data.count);
            });
        }
        refreshNotifCount();

        
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

       
        chatInput.focus();
    </script>
</body>
</html>