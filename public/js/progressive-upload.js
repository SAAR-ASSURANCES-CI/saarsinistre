/**
 * Système d'upload progressif pour la déclaration mobile
 */

// Variables globales pour l'upload progressif
let progressiveUploadEnabled = false;
let uploadQueue = [];
let uploadedFilesData = [];
let sessionId = '';
let uploadInProgress = false;

// Détecter si on est sur mobile
function isMobileDevice() {
    return /Android|iPhone|iPad|iPod|BlackBerry|Windows Phone/i.test(navigator.userAgent);
}

// Initialiser l'upload progressif si on est sur mobile
document.addEventListener('DOMContentLoaded', function() {
    if (isMobileDevice()) {
        progressiveUploadEnabled = true;
        sessionId = generateSessionId();
        initializeProgressiveUpload();
        console.log('Upload progressif activé pour mobile, Session ID:', sessionId);
    }
});

function generateSessionId() {
    return 'session_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
}

function initializeProgressiveUpload() {
    const uploadZones = document.querySelectorAll('.upload-zone');
    uploadZones.forEach(zone => {
        const input = zone.querySelector('input[type="file"]');
        if (input) {
            input.removeEventListener('change', handleFileUpload);
            input.addEventListener('change', handleProgressiveFileUpload);
        }
    });

    createGlobalProgressBar();
}

function createGlobalProgressBar() {
    const progressContainer = document.createElement('div');
    progressContainer.id = 'global-upload-progress';
    progressContainer.className = 'fixed top-4 right-4 z-50 bg-white rounded-lg shadow-lg p-4 max-w-sm hidden';
    progressContainer.innerHTML = `
        <div class="flex items-center justify-between mb-2">
            <span class="text-sm font-medium text-gray-700">Upload en cours</span>
            <span id="upload-count" class="text-xs text-gray-500">0/0</span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-2">
            <div id="global-progress-bar" class="bg-saar-green h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
        </div>
        <div id="current-file-name" class="text-xs text-gray-600 mt-1 truncate"></div>
    `;
    document.body.appendChild(progressContainer);
}

async function handleProgressiveFileUpload(event) {
    const files = Array.from(event.target.files);
    const zone = event.target.closest('.upload-zone');
    const fieldType = zone.dataset.field;

    if (files.length === 0) return;

    files.forEach(file => {
        uploadQueue.push({
            file: file,
            type: fieldType,
            zone: zone,
            input: event.target
        });
    });

    if (!uploadInProgress) {
        await processUploadQueue();
    }
}

async function processUploadQueue() {
    if (uploadQueue.length === 0) {
        hideGlobalProgress();
        return;
    }

    uploadInProgress = true;
    showGlobalProgress();

    const totalFiles = uploadQueue.length;
    let completedFiles = 0;

    while (uploadQueue.length > 0) {
        const uploadItem = uploadQueue.shift();
        
        try {
            updateGlobalProgress(completedFiles, totalFiles, uploadItem.file.name);
            
            const result = await uploadSingleFile(uploadItem);
            
            if (result.success) {
                markFileAsUploaded(uploadItem.zone, result.file_info);
                
                uploadedFilesData.push(result.file_info);
                
                completedFiles++;
                updateGlobalProgress(completedFiles, totalFiles, '');
            } else {
                showUploadError(uploadItem.zone, result.message);
            }
        } catch (error) {
            console.error('Erreur upload:', error);
            showUploadError(uploadItem.zone, 'Erreur lors de l\'upload');
        }
    }

    uploadInProgress = false;
    setTimeout(() => hideGlobalProgress(), 1000);
}

async function uploadSingleFile(uploadItem) {
    const formData = new FormData();
    formData.append('file', uploadItem.file);
    formData.append('type', uploadItem.type);
    formData.append('session_id', sessionId);

    const response = await fetch('/declaration/upload-file', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        }
    });

    if (!response.ok) {
        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
    }

    return await response.json();
}

function markFileAsUploaded(zone, fileInfo) {
    const statusDiv = zone.querySelector('.file-status') || createFileStatusDiv(zone);
    
    statusDiv.innerHTML = `
        <div class="flex items-center justify-between p-2 bg-green-50 border border-green-200 rounded">
            <div class="flex items-center">
                <svg class="w-4 h-4 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <span class="text-sm text-green-700">${fileInfo.original_name}</span>
            </div>
            <button type="button" onclick="removeUploadedFile('${fileInfo.stored_path}', this)" 
                    class="text-red-500 hover:text-red-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    `;
}

function createFileStatusDiv(zone) {
    const statusDiv = document.createElement('div');
    statusDiv.className = 'file-status mt-2';
    zone.appendChild(statusDiv);
    return statusDiv;
}

async function removeUploadedFile(filePath, button) {
    try {
        const response = await fetch('/declaration/delete-file', {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            },
            body: JSON.stringify({
                file_path: filePath,
                session_id: sessionId
            })
        });

        if (response.ok) {
            uploadedFilesData = uploadedFilesData.filter(file => file.stored_path !== filePath);
            
            button.closest('.file-status').innerHTML = '';
        } else {
            console.error('Erreur suppression fichier');
        }
    } catch (error) {
        console.error('Erreur suppression fichier:', error);
    }
}


function showUploadError(zone, message) {
    const statusDiv = zone.querySelector('.file-status') || createFileStatusDiv(zone);
    statusDiv.innerHTML = `
        <div class="p-2 bg-red-50 border border-red-200 rounded">
            <div class="flex items-center">
                <svg class="w-4 h-4 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="text-sm text-red-700">${message}</span>
            </div>
        </div>
    `;
}


function showGlobalProgress() {
    const progressContainer = document.getElementById('global-upload-progress');
    if (progressContainer) {
        progressContainer.classList.remove('hidden');
    }
}


function hideGlobalProgress() {
    const progressContainer = document.getElementById('global-upload-progress');
    if (progressContainer) {
        progressContainer.classList.add('hidden');
    }
}


function updateGlobalProgress(completed, total, currentFileName) {
    const progressBar = document.getElementById('global-progress-bar');
    const countSpan = document.getElementById('upload-count');
    const fileNameSpan = document.getElementById('current-file-name');

    if (progressBar) {
        const percentage = total > 0 ? (completed / total) * 100 : 0;
        progressBar.style.width = `${percentage}%`;
    }

    if (countSpan) {
        countSpan.textContent = `${completed}/${total}`;
    }

    if (fileNameSpan && currentFileName) {
        fileNameSpan.textContent = currentFileName;
    }
}


function modifySubmitFunction() {

    if (typeof window.handleSubmit === 'function') {
        window.originalHandleSubmit = window.handleSubmit;
    }


    window.handleSubmit = function(e) {
        e.preventDefault();
        
        const submitBtn = document.getElementById('submit-btn');
        const submitText = submitBtn.querySelector('.submit-text');
        const loadingText = submitBtn.querySelector('.loading-text');
        const acceptConditions = document.getElementById('accept-conditions');
        
        if (!acceptConditions || !acceptConditions.checked) {
            alert('Veuillez accepter les conditions générales pour continuer.');
            return;
        }

        submitBtn.disabled = true;
        submitText.classList.add('hidden');
        loadingText.classList.remove('hidden');
        submitBtn.classList.add('animate-pulse');

        const formData = new FormData(document.getElementById('declaration-form'));
        

        if (progressiveUploadEnabled && uploadedFilesData.length > 0) {
            formData.append('uploaded_files', JSON.stringify(uploadedFilesData));
            formData.append('session_id', sessionId);
        } else {

            Object.keys(uploadedFiles).forEach(key => {
                if (key === 'photos_vehicule') {
                    Array.from(uploadedFiles[key]).forEach((file, index) => {
                        formData.append(`photos_vehicule[${index}]`, file);
                    });
                } else if (uploadedFiles[key] && uploadedFiles[key].length > 0) {
                    formData.append(key, uploadedFiles[key][0]);
                }
            });
        }


        const constatCheckbox = document.getElementById('constat');
        formData.set('constat_autorite', constatCheckbox.checked ? '1' : '0');
        const impliqueTiersCheckbox = document.getElementById('implique_tiers');
        formData.set('implique_tiers', impliqueTiersCheckbox.checked ? '1' : '0');
        const detailsTiersTextarea = document.getElementById('details_tiers');
        if (impliqueTiersCheckbox.checked) {
            formData.set('details_tiers', detailsTiersTextarea.value);
        } else {
            formData.delete('details_tiers');
        }

        const submitUrl = '/declaration/store';
        fetch(submitUrl, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                credentials: 'same-origin'
            })
            .then(async response => {
                const contentType = response.headers.get('content-type');
                if (contentType && contentType.includes('text/html')) {
                    const htmlText = await response.text();
                    console.error('Received HTML instead of JSON:', htmlText.substring(0, 500));
                    throw new Error('Le serveur a retourné du HTML au lieu de JSON. Vérifiez la route et le contrôleur.');
                }
                if (!response.ok) {
                    const errorText = await response.text();
                    console.error('Error response:', errorText);
                    throw new Error(`Erreur ${response.status}: ${response.statusText}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    showSuccessPage(data);
                } else {
                    throw new Error(data.message || 'Erreur lors de la soumission');
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                showErrorMessage(error.message);
                submitBtn.disabled = false;
                submitText.classList.remove('hidden');
                loadingText.classList.add('hidden');
                submitBtn.classList.remove('animate-pulse');
            });
    };
}


document.addEventListener('DOMContentLoaded', function() {
    if (progressiveUploadEnabled) {
        setTimeout(modifySubmitFunction, 100);
    }
});

window.addEventListener('beforeunload', function() {
    if (progressiveUploadEnabled && sessionId) {

        navigator.sendBeacon('/declaration/cleanup-session', JSON.stringify({
            session_id: sessionId,
            _token: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
        }));
    }
});
