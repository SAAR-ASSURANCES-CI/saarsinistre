<!DOCTYPE html>
<html lang="fr">
<head>
    @include('admin.partials.head', ['title' => 'Gestion des médias - SAAR Assurances'])
</head>
<body class="bg-gradient-to-br from-red-50 via-white to-green-50 min-h-screen">
    @include('admin.partials.header')
    @include('admin.partials.navbar')
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold mb-6">Gestion des médias des sinistres</h1>
        <div class="mb-6 flex justify-center">
            <input type="text" id="search-sinistre" placeholder="Rechercher un numéro de sinistre..." class="border-2 border-saar-blue rounded-full px-5 py-2 w-full max-w-md shadow focus:outline-none focus:ring-2 focus:ring-saar-blue transition" />
        </div>
        <div id="no-result-message" class="hidden text-center text-gray-500 italic mb-6">Aucun dossier sinistre trouvé.</div>

        @if(session('success'))
            <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if($sinistres->isEmpty())
            <p>Aucun fichier n'a encore été ajouté.</p>
        @else
            <!-- Grille de dossiers -->
            <div id="sinistres-grid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-6 mb-10">
                @foreach($sinistres as $sinistre)
                    <div class="folder flex flex-col items-center cursor-pointer p-3 rounded hover:bg-gray-100 transition" data-sinistre-id="{{ $sinistre->id }}">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 h-12 text-yellow-400 mb-2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12V6.75A2.25 2.25 0 014.5 4.5h3.379c.414 0 .81.17 1.102.474l1.366 1.421c.292.304.688.474 1.102.474H19.5a2.25 2.25 0 012.25 2.25v9A2.25 2.25 0 0119.5 20.25h-15A2.25 2.25 0 012.25 18V12z" />
                        </svg>
                        <span class="text-center text-sm font-medium">Sinistre {{ $sinistre->numero_sinistre }}</span>
                    </div>
                @endforeach
            </div>
            <!-- Zone d'affichage des fichiers du sinistre sélectionné -->
            <div id="sinistre-documents" class="hidden">
                <button id="back-to-folders" class="mb-4 text-blue-600 hover:underline">← Retour aux dossiers</button>
                <h2 class="text-lg font-semibold mb-2" id="sinistre-title"></h2>
                <div id="documents-list"></div>
            </div>
            <div id="preview-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
                <div class="bg-white rounded-lg shadow-lg max-w-2xl w-full p-4 relative">
                    <button id="close-modal" class="absolute top-2 right-2 text-gray-500 hover:text-red-600 text-2xl">&times;</button>
                    <div id="preview-content" class="flex flex-col items-center justify-center min-h-[300px]"></div>
                    <button id="print-btn" class="mt-4 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Imprimer</button>
                </div>
            </div>
            <script>
                const sinistres = @json($sinistres);
                const grid = document.getElementById('sinistres-grid');
                const docsZone = document.getElementById('sinistre-documents');
                const docsList = document.getElementById('documents-list');
                const sinistreTitle = document.getElementById('sinistre-title');
                const backBtn = document.getElementById('back-to-folders');

                grid.querySelectorAll('.folder').forEach(folder => {
                    folder.addEventListener('dblclick', function() {
                        const id = this.getAttribute('data-sinistre-id');
                        const sinistre = sinistres.find(s => s.id == id);
                        showDocuments(sinistre);
                    });
                    folder.addEventListener('click', function() {
                        this.classList.add('ring', 'ring-blue-400');
                        setTimeout(() => this.classList.remove('ring', 'ring-blue-400'), 200);
                    });
                });
                backBtn.addEventListener('click', function() {
                    docsZone.classList.add('hidden');
                    grid.classList.remove('hidden');
                });
                function showDocuments(sinistre) {
                    grid.classList.add('hidden');
                    docsZone.classList.remove('hidden');
                    sinistreTitle.textContent = 'Sinistre ' + sinistre.numero_sinistre;
                    if (!sinistre.documents.length) {
                        docsList.innerHTML = '<p class="text-gray-500">Aucun fichier pour ce sinistre.</p>';
                    } else {
                        docsList.innerHTML = '<ul class="divide-y">' +
                            sinistre.documents.map((doc, idx) => {
                                
                                let icone = doc.icone_type ?? `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-gray-400"><path stroke-linecap="round" stroke-linejoin="round" d="M17.25 6.75V19.5a2.25 2.25 0 01-2.25 2.25H6.75A2.25 2.25 0 014.5 19.5V4.5A2.25 2.25 0 016.75 2.25h6.75L17.25 6.75z" /></svg>`;
                                
                                let nom = doc.libelle_document ? doc.libelle_document : (doc.nom_fichier ? doc.nom_fichier : 'Document');
                                
                                let taille = doc.taille_formatee ? `(${doc.taille_formatee})` : '';
                                
                                let url = doc.chemin_fichier && doc.chemin_fichier.startsWith('http') ? doc.chemin_fichier : ('/storage/' + (doc.chemin_fichier ?? ''));
                                // Extension
                                let ext = '';
                                if (doc.nom_fichier && doc.nom_fichier.includes('.')) {
                                    ext = doc.nom_fichier.split('.').pop().toLowerCase();
                                }
                                let extAffiche = ext ? `<span class="text-xs text-gray-500 mr-1">[.${ext}]</span>` : '';
                                return `
                                    <li class="flex items-center justify-between py-2">
                                        <div class="flex items-center space-x-2">
                                            <span>${icone}</span>
                                            ${extAffiche}
                                            <button class="text-blue-600 hover:underline preview-btn" data-url="${url}" data-nom="${nom}">${nom}</button>
                                            <span class="text-xs text-gray-500">${taille}</span>
                                        </div>
                                    </li>
                                `;
                            }).join('') + '</ul>';
                       
                        docsList.querySelectorAll('.preview-btn').forEach(btn => {
                            btn.addEventListener('click', function() {
                                openPreviewModal(this.getAttribute('data-url'), this.getAttribute('data-nom'));
                            });
                        });
                    }
                }
                
                const modal = document.getElementById('preview-modal');
                const previewContent = document.getElementById('preview-content');
                const closeModal = document.getElementById('close-modal');
                const printBtn = document.getElementById('print-btn');
                let lastPreviewType = null;
                let lastPreviewUrl = null;
                function openPreviewModal(url, nom) {
                    previewContent.innerHTML = '<span class="text-gray-400">Chargement...</span>';
                    modal.classList.remove('hidden');
                    lastPreviewUrl = url;
                    
                    if (url.match(/\.(jpg|jpeg|png|gif|bmp|webp)$/i)) {
                        previewContent.innerHTML = `<img src="${url}" alt="${nom}" class="max-h-96 max-w-full rounded shadow" />`;
                        lastPreviewType = 'image';
                    } else if (url.match(/\.(pdf)$/i)) {
                        previewContent.innerHTML = `<iframe src="${url}" class="w-full h-96" frameborder="0"></iframe>`;
                        lastPreviewType = 'pdf';
                    } else {
                        previewContent.innerHTML = `<a href="${url}" target="_blank" class="text-blue-600 underline">Télécharger ou ouvrir le fichier</a>`;
                        lastPreviewType = 'autre';
                    }
                }
                closeModal.addEventListener('click', () => {
                    modal.classList.add('hidden');
                    previewContent.innerHTML = '';
                });
                // Impression
                printBtn.addEventListener('click', () => {
                    if (lastPreviewType === 'image') {
                        const win = window.open('');
                        win.document.write(`<img id='toPrint' src='${lastPreviewUrl}' style='max-width:100%' />`);
                        win.document.close();
                        const img = win.document.getElementById('toPrint');
                        img.onload = function() {
                            win.focus();
                            win.print();
                            win.close();
                        };
                    } else if (lastPreviewType === 'pdf') {
                        const win = window.open(lastPreviewUrl);
                        win.print();
                    } else {
                        alert('Ce type de fichier ne peut pas être imprimé directement.');
                    }
                });
                
                const searchInput = document.getElementById('search-sinistre');
                const noResultMsg = document.getElementById('no-result-message');
                searchInput.addEventListener('input', function() {
                    const value = this.value.trim().toLowerCase();
                    let visibleCount = 0;
                    grid.querySelectorAll('.folder').forEach(folder => {
                        const numero = folder.querySelector('span').textContent.toLowerCase();
                        if (numero.includes(value)) {
                            folder.classList.remove('hidden');
                            visibleCount++;
                        } else {
                            folder.classList.add('hidden');
                        }
                    });
                    if (visibleCount === 0) {
                        noResultMsg.classList.remove('hidden');
                    } else {
                        noResultMsg.classList.add('hidden');
                    }
                });
            </script>
        @endif
    </div>
</body>
</html> 