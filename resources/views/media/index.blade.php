<!DOCTYPE html>
<html lang="fr">
<head>
    @include('admin.partials.head', ['title' => 'Gestion des médias - SAAR Assurances'])
</head>
<body class="bg-gradient-to-br from-red-50 via-white to-green-50 min-h-screen">
    @include('admin.partials.header')
    @include('admin.partials.navbar')
    
    <div class="container mx-auto px-4 py-8">
        <!-- En-tête avec titre et bouton toggle -->
        <div class="flex flex-col sm:flex-row justify-between items-center mb-6">
            <h1 class="text-2xl font-bold mb-4 sm:mb-0">Gestion des médias des sinistres</h1>
            
            <!-- Bouton toggle -->
            <div class="flex bg-gray-100 rounded-lg p-1">
                <button class="view-toggle px-4 py-2 rounded-lg transition flex items-center {{ $viewMode == 'gallery' ? 'bg-white shadow' : '' }}" data-view="gallery">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-1">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12V6.75A2.25 2.25 0 014.5 4.5h3.379c.414 0 .81.17 1.102.474l1.366 1.421c.292.304.688.474 1.102.474H19.5a2.25 2.25 0 012.25 2.25v9A2.25 2.25 0 0119.5 20.25h-15A2.25 2.25 0 012.25 18V12z" />
                    </svg>
                    Galerie
                </button>
                <button class="view-toggle px-4 py-2 rounded-lg transition flex items-center {{ $viewMode == 'list' ? 'bg-white shadow' : '' }}" data-view="list">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-1">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm-.375 5.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                    </svg>
                    Liste
                </button>
            </div>
        </div>

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
            <!-- CONTAINER DYNAMIQUE : c'est ici que sera chargée la vue active -->
            <div id="view-container">
                @include('media.partials.' . $viewMode . '-view', ['sinistres' => $sinistres])
            </div>
        @endif
        <div id="sinistre-documents" class="hidden">
            <div class="flex items-center justify-between mb-4">
                <button id="back-to-folders" class="text-blue-600 hover:underline">← Retour</button>
                <!-- Toggle liste / grandes icônes -->
                <div class="flex bg-gray-100 rounded-lg p-1">
                    <button class="docs-view-toggle px-3 py-1 rounded-lg transition flex items-center bg-white shadow" data-docs-view="list">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-1">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm-.375 5.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                        </svg>
                        Liste
                    </button>
                    <button class="docs-view-toggle px-3 py-1 rounded-lg transition flex items-center" data-docs-view="icons">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-1">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zm0 9.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zm9.75-9.75A2.25 2.25 0 0115.75 3.75H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zm0 9.75A2.25 2.25 0 0115.75 13.5H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
                        </svg>
                        Icônes
                    </button>
                </div>
            </div>
            <h2 class="text-lg font-semibold mb-4" id="sinistre-title"></h2>
            <div id="documents-list"></div>
        </div>

        <div id="preview-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
            <div class="bg-white rounded-lg shadow-lg max-w-2xl w-full p-4 relative">
                <button id="close-modal" class="absolute top-2 right-2 text-gray-500 hover:text-red-600 text-2xl">&times;</button>
                <div id="preview-content" class="flex flex-col items-center justify-center min-h-[300px]"></div>
                <button id="print-btn" class="mt-4 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Imprimer</button>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        let sinistresData = @json($sinistres->items());
        let currentView = '{{ $viewMode }}';
        
        function attachGalleryEvents() {
            document.querySelectorAll('.folder').forEach(folder => {
                folder.addEventListener('dblclick', function() {
                    const id = this.getAttribute('data-sinistre-id');
                    const sinistre = sinistresData.find(s => s.id == id);
                    if (sinistre) showDocuments(sinistre);
                });
                folder.addEventListener('click', function() {
                    this.classList.add('ring', 'ring-blue-400');
                    setTimeout(() => this.classList.remove('ring', 'ring-blue-400'), 200);
                });
            });
        }

        function attachListEvents() {
    document.querySelectorAll('.view-docs').forEach(btn => {
        btn.addEventListener('click', function() {
            const sinistreId = this.dataset.sinistreId;
            const sinistre = sinistresData.find(s => s.id == sinistreId);
            if (sinistre) showDocuments(sinistre);
        });
        });

        document.querySelectorAll('.download-all').forEach(btn => {
            btn.addEventListener('click', function() {
            const sinistreId = this.dataset.sinistreId;
            window.location.href = `/gestionnaires/dashboard/media/${sinistreId}/download`;
         });
        });
    }

    let currentDocsView = 'list';
let currentSinistre = null;

function renderDocuments(sinistre, view) {
    const docsList = document.getElementById('documents-list');

    if (!sinistre.documents || !sinistre.documents.length) {
        docsList.innerHTML = '<p class="text-gray-500">Aucun fichier pour ce sinistre.</p>';
        return;
    }

    let html = '';

    if (view === 'list') {
        html = '<ul class="divide-y">';
        sinistre.documents.forEach(doc => {
            let url = doc.url || (doc.chemin_fichier && doc.chemin_fichier.startsWith('http')
                ? doc.chemin_fichier
                : '/storage/' + (doc.chemin_fichier ?? ''));
            html += `
                <li class="flex items-center justify-between py-2">
                    <div class="flex items-center space-x-2">
                        <span>📄</span>
                        <button class="text-blue-600 hover:underline preview-btn" data-url="${url}" data-nom="${doc.libelle_document || doc.nom_fichier}">
                            ${doc.libelle_document || doc.nom_fichier}
                        </button>
                    </div>
                </li>`;
        });
        html += '</ul>';
    } else {
        html = '<div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-6">';
sinistre.documents.forEach(doc => {
    let url = doc.url || (doc.chemin_fichier && doc.chemin_fichier.startsWith('http')
        ? doc.chemin_fichier
        : '/storage/' + (doc.chemin_fichier ?? ''));
    const isImage = url.match(/\.(jpg|jpeg|png|gif|bmp|webp)$/i);
    const icon = isImage
        ? `<img src="${url}" class="w-32 h-32 object-cover rounded shadow mb-2" />`
        : `<span class="text-7xl mb-2">📄</span>`;
    html += `
        <div class="flex flex-col items-center cursor-pointer p-4 rounded hover:bg-gray-100 transition preview-btn" data-url="${url}" data-nom="${doc.libelle_document || doc.nom_fichier}">
            ${icon}
            <span class="text-xs text-center text-gray-700 mt-1">${doc.libelle_document || doc.nom_fichier}</span>
        </div>`;
});
html += '</div>';
    }

    docsList.innerHTML = html;

    docsList.querySelectorAll('.preview-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            openPreviewModal(this.dataset.url, this.dataset.nom);
        });
    });
}

function showDocuments(sinistre) {
    currentSinistre = sinistre;
    const viewContainer = document.getElementById('view-container');
    const docsZone = document.getElementById('sinistre-documents');

    if (viewContainer) viewContainer.classList.add('hidden');
    docsZone.classList.remove('hidden');

    document.getElementById('sinistre-title').textContent = 'Sinistre ' + sinistre.numero_sinistre;
    renderDocuments(sinistre, currentDocsView);
}

document.querySelectorAll('.docs-view-toggle').forEach(btn => {
    btn.addEventListener('click', function() {
        currentDocsView = this.dataset.docsView;
        document.querySelectorAll('.docs-view-toggle').forEach(b => b.classList.remove('bg-white', 'shadow'));
        this.classList.add('bg-white', 'shadow');
        if (currentSinistre) renderDocuments(currentSinistre, currentDocsView);
    });
});

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

        if (closeModal) {
            closeModal.addEventListener('click', () => {
                modal.classList.add('hidden');
                previewContent.innerHTML = '';
            });
        }

        if (printBtn) {
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
        }

        const backBtn = document.getElementById('back-to-folders');
if (backBtn) {
    backBtn.addEventListener('click', function() {
        document.getElementById('sinistre-documents').classList.add('hidden');
        document.getElementById('view-container').classList.remove('hidden');
    });
}

        const searchInput = document.getElementById('search-sinistre');
        const noResultMsg = document.getElementById('no-result-message');
        const paginationLinks = document.getElementById('pagination-links');

        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const value = this.value.trim();
                
                if (value === '') {
                    if (paginationLinks) paginationLinks.classList.remove('hidden');
                    location.reload();
                    return;
                }
                
                if (paginationLinks) paginationLinks.classList.add('hidden');
                
                fetch(`/gestionnaires/dashboard/media/search?q=${encodeURIComponent(value)}`)
                    .then(res => res.json())

                    .then(data => {
    sinistresData = data;

    if (data.length === 0) {
        noResultMsg.classList.remove('hidden');
        document.getElementById('view-container').innerHTML = '';
        return;
    }

    noResultMsg.classList.add('hidden');

    if (currentView === 'gallery') {
        const grid = document.getElementById('sinistres-grid');
        if (grid) {
            grid.innerHTML = '';
            data.forEach(sinistre => {
                grid.innerHTML += `
                    <div class="folder flex flex-col items-center cursor-pointer p-3 rounded hover:bg-gray-100 transition" data-sinistre-id="${sinistre.id}">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 h-12 text-yellow-400 mb-2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12V6.75A2.25 2.25 0 014.5 4.5h3.379c.414 0 .81.17 1.102.474l1.366 1.421c.292.304.688.474 1.102.474H19.5a2.25 2.25 0 012.25 2.25v9A2.25 2.25 0 0119.5 20.25h-15A2.25 2.25 0 012.25 18V12z" />
                        </svg>
                        <span class="text-center text-sm font-medium">Sinistre ${sinistre.numero_sinistre}</span>
                        <span class="text-xs text-gray-400 mt-1">${sinistre.documents.length} doc(s)</span>
                    </div>`;
            });
            attachGalleryEvents();
        }
    } else {
        let rows = '';
        data.forEach(sinistre => {
            const dernierDoc = sinistre.documents.length > 0
                ? sinistre.documents.sort((a, b) => new Date(b.created_at) - new Date(a.created_at))[0].created_at.substring(0, 10).split('-').reverse().join('/')
                : '-';
            const plaque = sinistre.vehicule ? sinistre.vehicule.immatriculation : 'N/A';
            rows += `
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${sinistre.nom_assure ?? 'Non renseigné'}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${sinistre.numero_sinistre}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${plaque}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${dernierDoc}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center">${sinistre.documents.length}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <button class="text-blue-600 hover:text-blue-900 mr-3 view-docs" data-sinistre-id="${sinistre.id}">Voir</button>
                        <button class="text-green-600 hover:text-green-900 download-all" data-sinistre-id="${sinistre.id}">Télécharger</button>
                    </td>
                </tr>`;
        });
        document.querySelector('#view-container table tbody').innerHTML = rows;
        attachListEvents();
    }
});
            });
        }

        document.querySelectorAll('.view-toggle').forEach(btn => {
            btn.addEventListener('click', function() {
                const view = this.dataset.view;
                
                document.querySelectorAll('.view-toggle').forEach(b => {
                    b.classList.remove('bg-white', 'shadow');
                });
                this.classList.add('bg-white', 'shadow');
                
                fetch(`{{ route('gestionnaires.dashboard.media.index') }}?view=${view}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    document.getElementById('view-container').innerHTML = data.html;
                    currentView = data.view;
                    
                    localStorage.setItem('media_view_mode', currentView);
                    document.cookie = `media_view_mode=${currentView};path=/;max-age=31536000`;
                    
                    if (currentView === 'gallery') {
                        attachGalleryEvents();
                    } else {
                        attachListEvents();
                    }
                    
                });
            });
        });

        (function() {
    if (currentView === 'gallery') {
        attachGalleryEvents();
    } else {
        attachListEvents();
    }
})();
    </script>
</body>
</html>