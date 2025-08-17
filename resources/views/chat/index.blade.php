<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Messagerie Sinistre - SAAR Assurances</title>
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
            <!-- Status de connexion -->
            <div id="connection-status" class="flex items-center space-x-2">
                <div id="status-indicator" class="w-3 h-3 rounded-full bg-gray-400"></div>
                <span id="status-text" class="text-sm text-gray-600">Connexion...</span>
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
                <!-- Messages chargé via JavaScript -->
            </div>

            <form id="chat-form" class="flex flex-col gap-2 mt-2" enctype="multipart/form-data">
                <div class="flex gap-2">
                    <input type="text" id="chat-input" name="contenu" 
                           class="flex-1 px-4 py-2 border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-red-500 text-sm shadow-sm bg-white transition-all duration-200 hover:border-gray-400" 
                           placeholder="Écrire un message..." 
                           autocomplete="off"
                           maxlength="2000">
                    <label for="file-input" class="cursor-pointer inline-flex items-center gap-2 px-3 py-2 border border-gray-300 rounded-full bg-white hover:bg-gray-50 text-sm text-gray-700">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path d="M8 12a4 4 0 118 0v3a5 5 0 01-10 0V7a3 3 0 016 0v6a2 2 0 11-4 0V7h2v6a1 1 0 002 0V7a4 4 0 10-8 0v8a6 6 0 0012 0v-3h-2v3a4 4 0 11-8 0V7a2 2 0 114 0v6a3 3 0 106 0V7a6 6 0 10-12 0v8a8 8 0 0016 0v-3a6 6 0 00-12 0v3a6 6 0 0012 0v-3h-2v3a4 4 0 11-8 0V7h2v6a2 2 0 104 0V7a3 3 0 10-6 0v8a5 5 0 0010 0v-3h-2v3a3 3 0 11-6 0V7h2v5z"/></svg>
                        Joindre
                    </label>
                    <input id="file-input" name="fichiers[]" type="file" class="hidden" multiple>
                    
                    <button type="submit" 
                            class="min-w-[80px] md:min-w-[100px] px-4 md:px-6 py-2 bg-red-600 text-white rounded-full font-semibold shadow-lg hover:bg-red-700 transition-all focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 animate-bounce-in text-sm md:text-base">
                        Envoyer
                    </button>
                </div>
                <div id="selected-files" class="flex flex-wrap gap-2 text-xs text-gray-600"></div>
            </form>
        </div>
    </div>

    <footer class="w-full py-4 bg-white/80 border-t border-gray-200 text-center text-xs text-gray-500 mt-8 transition-colors duration-200">
        © Saar Assurances Côte d'Ivoire. Tous droits réservés.
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    @vite(['resources/js/app.js'])
    <script>
        const userId = {{ Auth::id() }};
        const sinistreId = {{ $sinistre->id }};
        const chatBox = document.getElementById('chat-box');
        const chatForm = document.getElementById('chat-form');
        const chatInput = document.getElementById('chat-input');
        const statusIndicator = document.getElementById('status-indicator');
        const statusText = document.getElementById('status-text');
        
        let isLoading = false;
        let lastMessageId = 0;
        let messagesLoaded = false;

        // Configuration CSRF pour axios
        axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        function updateConnectionStatus(connected) {
            if (connected) {
                statusIndicator.className = 'w-3 h-3 rounded-full bg-green-500 animate-pulse';
                statusText.textContent = 'Connecté';
                statusText.className = 'text-sm text-green-600';
            } else {
                statusIndicator.className = 'w-3 h-3 rounded-full bg-red-500';
                statusText.textContent = 'Déconnecté';
                statusText.className = 'text-sm text-red-600';
            }
        }

        function scrollToBottom() {
            chatBox.scrollTop = chatBox.scrollHeight;
        }

        function setLoading(state) {
            isLoading = state;
            chatBox.classList.toggle('opacity-60', state);
        }

        // Fonction pour charger tous les messages existants
        function loadAllMessages() {
            if (isLoading || messagesLoaded) return;
            
            setLoading(true);
            
            axios.get(`/sinistres/${sinistreId}/chat/fetch`)
                .then(response => {
                    const messages = response.data;
                    console.log('Messages chargés:', messages.length);
                    
                    // Vider le chat box
                    // chatBox.innerHTML = '';
                    
                    // Ajouter tous les messages
                    messages.forEach(msg => {
                        appendMessage(msg, false);
                    });
                    
                    // Mettre à jour le dernier ID de message
                    if (messages.length > 0) {
                        lastMessageId = messages[messages.length - 1].id;
                    }
                    
                    messagesLoaded = true;
                    scrollToBottom();
                })
                .catch(error => {
                    console.error('Erreur de chargement des messages:', error);
                    // Réessayer après un délai
                    setTimeout(loadAllMessages, 2000);
                })
                .finally(() => {
                    setLoading(false);
                });
        }

        function appendMessage(message, animate = true) {
            const existingMessage = document.querySelector(`[data-message-id="${message.id}"]`);
            if (existingMessage) {
                return;
            }

            const isMine = message.sender_id === userId;
            const initiale = message.sender.nom_complet ? message.sender.nom_complet.charAt(0).toUpperCase() : '?';
            const bg = isMine ? 'bg-red-100' : 'bg-blue-100';
            const border = isMine ? 'border-red-500' : 'border-blue-500';
            const align = isMine ? 'justify-end' : 'justify-start';
            const avatarBg = isMine ? 'bg-red-600' : 'bg-blue-600';

            const messageEl = document.createElement('div');
            messageEl.className = `flex items-end ${align} message-enter`;
            messageEl.dataset.messageId = message.id;
            
            if (!isMine) {
                messageEl.innerHTML += `
                    <div class="flex-shrink-0 w-8 h-8 rounded-full ${avatarBg} flex items-center justify-center text-white font-bold shadow mr-2 transition-transform hover:scale-110">
                        ${initiale}
                    </div>
                `;
            }
            
            let attachmentsHtml = '';
            if (message.attachments && message.attachments.length) {
                const items = message.attachments.map(att => {
                    const isImage = att.type_mime && att.type_mime.startsWith('image/');
                    const preview = isImage
                        ? `<a href="${att.url}" target="_blank" class="block"><img src="${att.url}" alt="${att.nom_fichier}" class="max-h-40 rounded-lg border border-gray-200"/></a>`
                        : `<a href="${att.url}" target="_blank" class="inline-flex items-center gap-2 px-2 py-1 bg-white border rounded hover:bg-gray-50">
                               <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path d="M8 2a2 2 0 00-2 2v7a4 4 0 108 0V7h-2v4a2 2 0 11-4 0V4a1 1 0 112 0v7a3 3 0 106 0V4a6 6 0 10-12 0v9a6 6 0 0012 0V7h-2v6a4 4 0 11-8 0V4a2 2 0 114 0v7a1 1 0 11-2 0V4a3 3 0 106 0v7a5 5 0 11-10 0V4a6 6 0 0012 0v7a7 7 0 11-14 0V4a8 8 0 0016 0v7h-2V4a6 6 0 10-12 0v7a5 5 0 1010 0V4a3 3 0 10-6 0v7a2 2 0 104 0V4a1 1 0 10-2 0v7a3 3 0 106 0V4a4 4 0 10-8 0v7a5 5 0 1010 0V4a6 6 0 10-12 0v7a7 7 0 1014 0V4a8 8 0 10-16 0v7h2z"/></svg>
                               <span>${att.nom_fichier}</span>
                           </a>`;
                    return `<div class="mt-2">${preview}</div>`;
                }).join('');
                attachmentsHtml = `<div class="mt-2 flex flex-col gap-2">${items}</div>`;
            }

            messageEl.innerHTML += `
                <div class="max-w-xs md:max-w-md px-4 py-2 rounded-2xl shadow ${bg} border-l-4 ${border} ${isMine ? 'text-right' : 'text-left'} transition-all duration-200 hover:shadow-md">
                    <div class="text-xs text-gray-500 mb-1 font-semibold">${message.sender.nom_complet}</div>
                    ${message.contenu ? `<div class="whitespace-pre-line text-sm message-content">${message.contenu}</div>` : ''}
                    ${attachmentsHtml}
                    <div class="text-[11px] text-gray-400 mt-1">${formatDate(message.created_at)}</div>
                </div>
            `;
            
            if (isMine) {
                messageEl.innerHTML += `
                    <div class="flex-shrink-0 w-8 h-8 rounded-full ${avatarBg} flex items-center justify-center text-white font-bold shadow ml-2 transition-transform hover:scale-110">
                        ${initiale}
                    </div>
                `;
            }
            
            chatBox.appendChild(messageEl);
            
            if (animate) {
                setTimeout(() => {
                    messageEl.classList.add('message-enter-active');
                }, 10);
                scrollToBottom();
            }
            
            // Mettre à jour le dernier ID de message
            if (message.id > lastMessageId) {
                lastMessageId = message.id;
            }
        }

        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleString('fr-FR', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        }

        function handleMessageSubmit(e) {
            e.preventDefault();
            const contenu = chatInput.value.trim();
            const filesInput = document.getElementById('file-input');
            const hasFiles = filesInput.files && filesInput.files.length > 0;
            if (!contenu && !hasFiles) return;
            
            const submitBtn = chatForm.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-75');
            
            setLoading(true);

            const formData = new FormData();
            if (contenu) formData.append('contenu', contenu);
            if (hasFiles) {
                Array.from(filesInput.files).forEach(f => formData.append('fichiers[]', f));
            }

            axios.post(`/sinistres/${sinistreId}/chat`, formData, {
                headers: { 'Content-Type': 'multipart/form-data' }
            })
                .then(response => {
                    // Le message sera ajouté automatiquement via l'événement broadcast
                    chatInput.value = '';
                    filesInput.value = '';
                    document.getElementById('selected-files').innerHTML = '';
                })
                .catch(error => {
                    console.error('Erreur d\'envoi:', error);
                    alert('Une erreur est survenue lors de l\'envoi du message.');
                })
                .finally(() => {
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('opacity-75');
                    setLoading(false);
                });
        }

        function initChat() {
            console.log('Initialisation du chat...');
            
            chatForm.addEventListener('submit', handleMessageSubmit);

            const fileInput = document.getElementById('file-input');
            const selectedFiles = document.getElementById('selected-files');
            const submitBtn = chatForm.querySelector('button[type="submit"]');

            function updateSubmitState() {
                const hasText = !!chatInput.value.trim();
                const hasFiles = fileInput.files && fileInput.files.length > 0;
                submitBtn.disabled = !(hasText || hasFiles);
            }

            chatInput.addEventListener('input', updateSubmitState);

            fileInput.addEventListener('change', () => {
                if (fileInput.files.length === 0) {
                    selectedFiles.innerHTML = '';
                } else {
                    const names = Array.from(fileInput.files).map(f => `• ${f.name}`);
                    selectedFiles.innerHTML = names.join('<br/>');
                }
                updateSubmitState();
            });

            updateSubmitState();
            
            chatInput.addEventListener('input', function() {
                const submitBtn = chatForm.querySelector('button[type="submit"]');
                submitBtn.disabled = !chatInput.value.trim();
            });
            chatInput.dispatchEvent(new Event('input'));
            
            // Charger tous les messages existants en premier
            loadAllMessages();
            
            chatInput.focus();

            // Écouter les messages entrants via Laravel Echo
            window.Echo.private(`sinistre.${sinistreId}`)
                .listen('.message.sent', (data) => {
                    console.log('Nouveau message reçu:', data);
                    // Vérifier si le message n'est pas déjà affiché
                    if (data.id > lastMessageId) {
                        appendMessage(data, true);
                    }
                })
                .error((error) => {
                    console.error('Erreur Echo:', error);
                    updateConnectionStatus(false);
                });

            // Gestion de la connexion/déconnexion
            window.Echo.connector.pusher.connection.bind('connected', () => {
                console.log('WebSocket connecté');
                updateConnectionStatus(true);
            });

            window.Echo.connector.pusher.connection.bind('disconnected', () => {
                console.log('WebSocket déconnecté');
                updateConnectionStatus(false);
            });

            window.Echo.connector.pusher.connection.bind('failed', () => {
                console.log('Connexion WebSocket échouée');
                updateConnectionStatus(false);
            });
        }

        // Attendre que Echo soit disponible
        function waitForEcho() {
            if (typeof window.Echo !== 'undefined') {
                initChat();
            } else {
                console.log('En attente de Laravel Echo...');
                setTimeout(waitForEcho, 100);
            }
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', waitForEcho);
        } else {
            waitForEcho();
        }
    </script>
</body>
</html>