<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Messagerie Sinistre - SAAR Assurances</title>

    <!-- PWA Meta Tags -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#dc2626">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="SAAR Sinistre">
    <link rel="apple-touch-icon" href="/icons/icon-192x192.svg">

    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(5px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes pulseSlow {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }
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
    <header
        class="sticky top-0 z-40 bg-white/90 backdrop-blur border-b border-gray-200 shadow-sm flex items-center justify-between px-4 py-3 md:px-8">
        <div class="flex items-center space-x-3">
            <a href="{{ url()->previous() }}"
                class="text-red-700 hover:text-red-900 mr-2 transition-colors duration-200" title="Retour">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <span class="text-red-700 font-bold text-lg md:text-2xl">Messagerie Sinistre</span>
        </div>
        {{-- <div class="flex items-center space-x-2 md:space-x-4">
            <span class="text-sm text-gray-600">Messagerie simple</span>
        </div> --}}
    </header>

    <div class="container mx-auto px-4 py-8 chat-container">
        <div
            class="max-w-4xl mx-auto bg-white rounded-2xl shadow-lg border border-gray-100 p-4 md:p-6 transition-all duration-200 hover:shadow-xl">
            <div class="flex items-center gap-3 mb-4">
                <svg class="w-6 h-6 text-red-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 8h2a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2v-8a2 2 0 012-2h2m2-4h4a2 2 0 012 2v4H7V6a2 2 0 012-2z" />
                </svg>
                <div>
                    <h2 class="text-lg md:text-xl font-bold text-red-700">
                        Discussion avec
                        {{ Auth::id() === $sinistre->assure_id ? $sinistre->gestionnaire->nom_complet ?? 'Gestionnaire non assigné' : $sinistre->assure->nom_complet }}
                    </h2>
                    <div class="text-xs text-gray-500">
                        <span class="font-semibold">Numéro du sinistre :</span> {{ $sinistre->numero_sinistre }}
                    </div>
                </div>
            </div>

            <!-- Formulaire d'envoi de message -->
            <form id="message-form" class="mb-6 bg-gray-50 rounded-lg p-4 border border-gray-200"
                enctype="multipart/form-data">
                <div class="mb-4">
                    <label for="message-content" class="block text-sm font-medium text-gray-700 mb-2">
                        Votre message
                    </label>
                    <textarea id="message-content" name="contenu" rows="4"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent resize-none"
                        placeholder="Écrivez votre message ici..." maxlength="2000"></textarea>
                    <div class="text-xs text-gray-500 mt-1">
                        <span id="char-count">0</span>/2000 caractères
                    </div>
                </div>

                <div class="mb-4">
                    <label for="file-input" class="block text-sm font-medium text-gray-700 mb-2">
                        Joindre un fichier (optionnel)
                    </label>
                    <div class="flex items-center gap-3">
                        <input id="file-input" name="fichiers[]" type="file" class="hidden" multiple>
                        <button type="button" id="file-button"
                            class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg bg-white hover:bg-gray-50 text-sm text-gray-700 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"
                                    clip-rule="evenodd" />
                            </svg>
                            Choisir des fichiers
                        </button>
                        <span id="file-info" class="text-sm text-gray-500"></span>
                    </div>
                    <div id="selected-files" class="mt-2 flex flex-wrap gap-2"></div>
                </div>

                <div class="flex justify-end">
                    <button type="submit"
                        class="px-6 py-2 bg-red-600 text-white rounded-lg font-semibold hover:bg-red-700 transition-colors focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                        Envoyer le message
                    </button>
                </div>
            </form>

            <!-- Liste des conversations -->
            <div class="bg-white rounded-lg border border-gray-200">
                <div class="px-4 py-3 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">Historique des échanges</h3>
                </div>
                <div id="conversations-list" class="divide-y divide-gray-200">
                    <!-- Les conversations seront chargées ici -->
                </div>

                <!-- Contrôles de pagination -->
                <div id="pagination-controls" class="px-4 py-3 border-t border-gray-200 bg-gray-50">
                    <div class="flex items-center justify-between">
                        <div id="pagination-info" class="text-sm text-gray-600">
                            <!-- Informations de pagination -->
                        </div>
                        <div class="flex items-center gap-2">
                            <button id="load-more-btn"
                                class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors text-sm">
                                Charger plus de messages
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer
        class="w-full py-4 bg-white/80 border-t border-gray-200 text-center text-xs text-gray-500 mt-8 transition-colors duration-200">
        © Saar Assurances Côte d'Ivoire. Tous droits réservés.
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    @vite(['resources/js/app.js'])
    <script>
        const userId = {{ Auth::id() }};
        const sinistreId = {{ $sinistre->id }};
        const userRole = '{{ Auth::user()->role }}';
        const messageForm = document.getElementById('message-form');
        const messageContent = document.getElementById('message-content');
        const conversationsList = document.getElementById('conversations-list');
        const charCount = document.getElementById('char-count');
        const fileInput = document.getElementById('file-input');
        const fileButton = document.getElementById('file-button');
        const fileInfo = document.getElementById('file-info');
        const selectedFiles = document.getElementById('selected-files');
        const loadMoreBtn = document.getElementById('load-more-btn');
        const paginationInfo = document.getElementById('pagination-info');
        // URLs pour le chat - utiliser les routes selon le type d'utilisateur
        const chatUrls = {
            @if(auth()->user()->role === 'assure')
            fetch: `{{ route('assures.chat.fetch', $sinistre->id) }}`,
            store: `{{ route('assures.chat.store', $sinistre->id) }}`
            @else
            fetch: `{{ route('gestionnaires.chat.fetch', $sinistre->id) }}`,
            store: `{{ route('gestionnaires.chat.store', $sinistre->id) }}`
            @endif
        };

        let isLoading = false;
        let messagesLoaded = false;
        let currentPage = 1;
        let hasMorePages = true;

        // Configuration CSRF pour axios
        axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute(
            'content');



        function setLoading(state) {
            isLoading = state;
            const submitBtn = messageForm.querySelector('button[type="submit"]');
            submitBtn.disabled = state;
            submitBtn.textContent = state ? 'Envoi en cours...' : 'Envoyer le message';
        }

        // Fonction pour charger les messages avec pagination
        function loadConversations(page = 1, append = false) {
            if (isLoading) return;

            setLoading(true);

            axios.get(`${chatUrls.fetch}?page=${page}`)
                .then(response => {
                    const data = response.data;
                    const messages = data.data || data; // Gérer les deux formats (paginé ou non)

                    console.log('Messages chargés:', messages.length);

                    // Si c'est la première page, vider la liste
                    if (!append) {
                        conversationsList.innerHTML = '';
                    }

                    // Ajouter les messages
                    messages.forEach(msg => {
                        appendConversation(msg);
                    });

                    // Mettre à jour les informations de pagination
                    if (data.current_page !== undefined) {
                        currentPage = data.current_page;
                        hasMorePages = data.next_page_url !== null;
                        updatePaginationInfo(data);
                    } else {
                        hasMorePages = false;
                        updatePaginationInfo({
                            total: messages.length,
                            current_page: 1,
                            last_page: 1
                        });
                    }

                    messagesLoaded = true;
                })
                .catch(error => {
                    console.error('Erreur de chargement des messages:', error);
                    if (!append) {
                        conversationsList.innerHTML =
                            '<div class="p-4 text-center text-gray-500">Erreur lors du chargement des messages</div>';
                    }
                })
                .finally(() => {
                    setLoading(false);
                });
        }

        function updatePaginationInfo(data) {
            const total = data.total || 0;
            const current = data.current_page || 1;
            const last = data.last_page || 1;

            paginationInfo.textContent = `Page ${current} sur ${last} - ${total} message(s) au total`;

            // Afficher/masquer le bouton "Charger plus"
            if (hasMorePages) {
                loadMoreBtn.style.display = 'block';
                loadMoreBtn.textContent = 'Charger plus de messages';
            } else {
                loadMoreBtn.style.display = 'none';
            }
        }

        function appendConversation(message) {
            const existingMessage = document.querySelector(`[data-message-id="${message.id}"]`);
            if (existingMessage) {
                return;
            }

            const isMine = message.sender_id === userId;
            const initiale = message.sender.nom_complet ? message.sender.nom_complet.charAt(0).toUpperCase() : '?';
            const avatarBg = isMine ? 'bg-red-600' : 'bg-blue-600';
            const borderColor = isMine ? 'border-l-red-500' : 'border-l-blue-500';

            const conversationEl = document.createElement('div');
            conversationEl.className = `p-4 hover:bg-gray-50 transition-colors cursor-pointer border-l-4 ${borderColor}`;
            conversationEl.dataset.messageId = message.id;

            // Si ce n'est pas mon message, ajouter la fonctionnalité de réponse
            if (!isMine) {
                conversationEl.addEventListener('click', () => replyToMessage(message));
            }

            let attachmentsHtml = '';
            if (message.attachments && message.attachments.length) {
                const items = message.attachments.map(att => {
                    const isImage = att.type_mime && att.type_mime.startsWith('image/');
                    const preview = isImage ?
                        `<a href="${att.url}" target="_blank" class="block"><img src="${att.url}" alt="${att.nom_fichier}" class="max-h-40 rounded-lg border border-gray-200"/></a>` :
                        `<a href="${att.url}" target="_blank" class="inline-flex items-center gap-2 px-2 py-1 bg-white border rounded hover:bg-gray-50">
                               <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path d="M8 2a2 2 0 00-2 2v7a4 4 0 108 0V7h-2v4a2 2 0 11-4 0V4a1 1 0 112 0v7a3 3 0 106 0V4a6 6 0 10-12 0v9a6 6 0 0012 0V7h-2v6a4 4 0 11-8 0V4a2 2 0 114 0v7a1 1 0 11-2 0V4a3 3 0 106 0v7a5 5 0 11-10 0V4a6 6 0 0012 0v7a7 7 0 11-14 0V4a8 8 0 0016 0v7h-2V4a6 6 0 10-12 0v7a5 5 0 1010 0V4a3 3 0 10-6 0v7a2 2 0 104 0V4a1 1 0 10-2 0v7a3 3 0 106 0V4a4 4 0 10-8 0v7a5 5 0 1010 0V4a6 6 0 10-12 0v7a7 7 0 1014 0V4a8 8 0 10-16 0v7h2z"/></svg>
                               <span>${att.nom_fichier}</span>
                           </a>`;
                    return `<div class="mt-2">${preview}</div>`;
                }).join('');
                attachmentsHtml = `<div class="mt-2 flex flex-col gap-2">${items}</div>`;
            }

            conversationEl.innerHTML = `
                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0 w-10 h-10 rounded-full ${avatarBg} flex items-center justify-center text-white font-bold shadow">
                        ${initiale}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between mb-1">
                            <h4 class="text-sm font-semibold text-gray-900">${message.sender.nom_complet}</h4>
                            <span class="text-xs text-gray-500">${formatDate(message.created_at)}</span>
                        </div>
                        ${message.contenu ? `<div class="text-sm text-gray-700 whitespace-pre-line mb-2">${message.contenu}</div>` : ''}
                        ${attachmentsHtml}
                        ${!isMine ? '<div class="mt-2 text-xs text-blue-600">Cliquer pour répondre</div>' : ''}
                    </div>
                </div>
            `;

            conversationsList.appendChild(conversationEl);


        }

        function replyToMessage(originalMessage) {
            // Pré-remplir le textarea avec une réponse
            const replyText =
                `\n\n--- En réponse à ${originalMessage.sender.nom_complet} ---\n${originalMessage.contenu || '[Message avec fichier]'}\n\n`;
            messageContent.value = replyText;
            messageContent.focus();
            messageContent.setSelectionRange(0, 0); // Placer le curseur au début
            updateCharCount();
        }

        function updateCharCount() {
            const count = messageContent.value.length;
            charCount.textContent = count;
            charCount.className = count > 1800 ? 'text-red-500' : 'text-gray-500';
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

        function getFileIcon(filename) {
            const ext = filename.split('.').pop().toLowerCase();
            const icons = {
                'doc': '📄',
                'docx': '📄',
                'xls': '📊',
                'xlsx': '📊',
                'ppt': '📽️',
                'pptx': '📽️',
                'txt': '📝',
                'zip': '🗜️',
                'rar': '🗜️',
                '7z': '🗜️',
                'mp3': '🎵',
                'wav': '🎵',
                'flac': '🎵',
                'mp4': '🎬',
                'avi': '🎬',
                'mov': '🎬',
                'jpg': '🖼️',
                'jpeg': '🖼️',
                'png': '🖼️',
                'gif': '🖼️',
                'pdf': '📕'
            };
            return icons[ext] || '📎';
        }

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 B';
            const k = 1024;
            const sizes = ['B', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
        }

        function handleMessageSubmit(e) {
            e.preventDefault();
            const contenu = messageContent.value.trim();
            const hasFiles = fileInput.files && fileInput.files.length > 0;
            if (!contenu && !hasFiles) return;

            setLoading(true);

            const formData = new FormData();
            if (contenu) formData.append('contenu', contenu);
            if (hasFiles) {
                Array.from(fileInput.files).forEach(f => formData.append('fichiers[]', f));
            }

            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (!csrfToken) {
                console.error('Token CSRF manquant');
                alert('Erreur de sécurité. Veuillez actualiser la page.');
                setLoading(false);
                return;
            }

            axios.post(chatUrls.store, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data',
                        'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => {
                    appendConversation(response.data);
                    messageContent.value = '';
                    fileInput.value = '';
                    selectedFiles.innerHTML = '';
                    fileInfo.textContent = '';
                    updateCharCount();
                })
                .catch(error => {
                    console.error('Erreur d\'envoi:', error);

                    if (error.response) {
                        const status = error.response.status;

                        switch (status) {
                            case 401:
                                alert('Session expirée. Vous allez être redirigé vers la page de connexion.');
                                window.location.href = '/login/assure';
                                break;
                            case 419:
                                alert('Session expirée. Veuillez actualiser la page.');
                                window.location.reload();
                                break;
                            case 403:
                                alert('Vous n\'êtes pas autorisé à effectuer cette action.');
                                break;
                            case 422:
                                const errors = error.response.data.errors || {};
                                let errorMessage = 'Données invalides:\n';
                                Object.keys(errors).forEach(key => {
                                    errorMessage += `- ${errors[key].join('\n- ')}\n`;
                                });
                                alert(errorMessage);
                                break;
                            default:
                                alert('Une erreur est survenue lors de l\'envoi du message.');
                        }
                    } else {
                        alert('Erreur de connexion au serveur.');
                    }
                })
                .finally(() => {
                    setLoading(false);
                });
        }

        function initChat() {
            console.log('Initialisation du système de messages...');

            // Gestion du formulaire
            messageForm.addEventListener('submit', handleMessageSubmit);

            // Gestion du compteur de caractères
            messageContent.addEventListener('input', updateCharCount);

            // Gestion des fichiers
            fileButton.addEventListener('click', () => fileInput.click());

            fileInput.addEventListener('change', () => {
                if (fileInput.files.length === 0) {
                    selectedFiles.innerHTML = '';
                    fileInfo.textContent = '';
                } else {
                    const files = Array.from(fileInput.files);
                    fileInfo.textContent = `${files.length} fichier(s) sélectionné(s)`;

                    selectedFiles.innerHTML = files.map(file => `
                        <div class="inline-flex items-center gap-1 px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs">
                            <span>${getFileIcon(file.name)}</span>
                            <span>${file.name}</span>
                            <span class="text-blue-600">(${formatFileSize(file.size)})</span>
                        </div>
                    `).join('');
                }
            });

            // Gestion du bouton "Charger plus"
            loadMoreBtn.addEventListener('click', () => {
                if (hasMorePages && !isLoading) {
                    loadConversations(currentPage + 1, true);
                }
            });

            loadConversations();

            messageContent.focus();
        }

        // Initialiser le chat directement
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initChat);
        } else {
            initChat();
        }
    </script>

    <!-- Service Worker Registration -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('/sw.js');
            });
        }
    </script>

    <!-- PWA Script -->
    <script src="{{ asset('js/pwa.js') }}"></script>
</body>

</html>
