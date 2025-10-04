let currentStep = 1;
const totalSteps = 4;
const uploadedFiles = {};
const uploadProgress = new Map();

document.addEventListener('DOMContentLoaded', function() {
    initializeForm();
    setupEventListeners();
    updateProgressBar();
    
    // Charger le gestionnaire d'upload 
    loadUploadManager();


    const inputs = document.querySelectorAll('input, textarea');
    inputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.parentElement.classList.add('animate-pulse');
        });
        input.addEventListener('blur', function() {
            this.parentElement.classList.remove('animate-pulse');
        });
    });
});

function initializeForm() {
    for (let i = 2; i <= totalSteps; i++) {
        document.getElementById(`step-${i}`).classList.add('hidden');
    }
    const today = new Date().toISOString().split('T')[0];
    document.querySelector('input[name="date_sinistre"]').setAttribute('max', today);
}

function setupEventListeners() {
    document.getElementById('next-btn').addEventListener('click', nextStep);
    document.getElementById('prev-btn').addEventListener('click', prevStep);
    document.getElementById('declaration-form').addEventListener('submit', handleSubmit);
    document.getElementById('constat').addEventListener('change', function() {
        const details = document.getElementById('constat-details');
        if (this.checked) {
            details.classList.remove('hidden');
            details.classList.add('animate-fade-in');
        } else {
            details.classList.add('hidden');
            details.classList.remove('animate-fade-in');
        }
    });
    document.getElementById('implique_tiers').addEventListener('change', function() {
        const nombreTiersGroup = document.getElementById('nombre-tiers-group');
        const tiersFormsContainer = document.getElementById('tiers-forms-container');
        const detailsGroup = document.getElementById('details-tiers-group');
        
        if (this.checked) {
            nombreTiersGroup.classList.remove('hidden');
            detailsGroup.classList.remove('hidden');
        } else {
            nombreTiersGroup.classList.add('hidden');
            tiersFormsContainer.classList.add('hidden');
            detailsGroup.classList.add('hidden');
          
            document.getElementById('nombre_tiers').value = '';
           
            tiersFormsContainer.innerHTML = '';
        }
    });

    document.getElementById('nombre_tiers').addEventListener('change', function() {
        const nombreTiers = this.value;
        const tiersFormsContainer = document.getElementById('tiers-forms-container');
        
        if (nombreTiers) {
            generateTiersForms(nombreTiers);
            tiersFormsContainer.classList.remove('hidden');
        } else {
            tiersFormsContainer.classList.add('hidden');
            tiersFormsContainer.innerHTML = '';
        }
    });
    setupFileUploads();
}

/**
 * Désactiver tous les champs d'upload
 */
function disableUploadFields() {
    const uploadZones = document.querySelectorAll('.upload-zone');
    uploadZones.forEach(zone => {
        const input = zone.querySelector('input[type="file"]');
        const uploadArea = zone.querySelector('.border-dashed');
        
        // Désactiver l'input file
        if (input) {
            input.disabled = true;
        }
        
        // Désactiver la zone de drop
        if (uploadArea) {
            uploadArea.style.pointerEvents = 'none';
            uploadArea.classList.add('opacity-50', 'cursor-not-allowed');
        }
    });
}

/**
 * Réactiver tous les champs d'upload
 */
function enableUploadFields() {
    const uploadZones = document.querySelectorAll('.upload-zone');
    uploadZones.forEach(zone => {
        const input = zone.querySelector('input[type="file"]');
        const uploadArea = zone.querySelector('.border-dashed');
        
        // Réactiver l'input file
        if (input) {
            input.disabled = false;
        }
        
        // Réactiver la zone de drop
        if (uploadArea) {
            uploadArea.style.pointerEvents = 'auto';
            uploadArea.classList.remove('opacity-50', 'cursor-not-allowed');
        }
    });
}

function setupFileUploads() {
    const uploadZones = document.querySelectorAll('.upload-zone');
    uploadZones.forEach(zone => {
        const input = zone.querySelector('input[type="file"]');
        const uploadArea = zone.querySelector('.border-dashed');
        uploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadArea.classList.add('border-saar-blue', 'bg-blue-50');
        });
        uploadArea.addEventListener('dragleave', (e) => {
            e.preventDefault();
            uploadArea.classList.remove('border-saar-blue', 'bg-blue-50');
        });
        uploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadArea.classList.remove('border-saar-blue', 'bg-blue-50');
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                input.files = files;
                handleFileUpload(zone, files);
            }
        });
        uploadArea.addEventListener('click', () => {
            input.click();
        });
        input.addEventListener('change', (e) => {
            handleFileUpload(zone, e.target.files);
        });
    });
}

async function handleFileUpload(zone, files) {
    const field = zone.dataset.field;
    const uploadContent = zone.querySelector('.upload-content');
    const uploadSuccess = zone.querySelector('.upload-success');
    
    if (files.length > 0) {
        // Vérifier la taille des fichiers
        for (let file of files) {
            if (file.size > 5 * 1024 * 1024) {
                alert('Le fichier est trop volumineux. Taille maximum: 5MB');
                enableUploadFields();
                return;
            }
        }

        disableUploadFields();
        
        showUploadProgress(zone, files.length);
        
        try {
            const uploadPromises = Array.from(files).map(async (file, index) => {
                return await uploadManager.uploadFile(file, field, (progress, fileId) => {
                    updateFileProgress(zone, fileId, progress, index);
                });
            });

            const uploadResults = await Promise.all(uploadPromises);
            
            uploadedFiles[field] = uploadResults;
            
            showUploadSuccess(zone, files.length);
            
            // Réactiver tous les champs d'upload après succès
            enableUploadFields();
            
        } catch (error) {
            console.error('Erreur upload:', error);
            showUploadError(zone, error.message);
            
            // Réactiver tous les champs d'upload après erreur
            enableUploadFields();
        }
    }
}

/**
 * Afficher la barre de progression d'upload
 */
function showUploadProgress(zone, fileCount) {
    const uploadContent = zone.querySelector('.upload-content');
    const uploadSuccess = zone.querySelector('.upload-success');
    
    uploadContent.classList.add('hidden');
    uploadSuccess.classList.add('hidden');
    
    let progressContainer = zone.querySelector('.upload-progress');
    if (!progressContainer) {
        progressContainer = document.createElement('div');
        progressContainer.className = 'upload-progress p-4 bg-blue-50 border border-blue-200 rounded-lg';
        progressContainer.innerHTML = `
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-blue-700">Upload en cours...</span>
                <span class="text-xs text-blue-600" id="progress-text">0%</span>
            </div>
            <div class="w-full bg-blue-200 rounded-full h-2">
                <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" id="progress-bar" style="width: 0%"></div>
            </div>
            <div class="mt-2 text-xs text-blue-600" id="file-info">0/${fileCount} fichier(s)</div>
        `;
        zone.appendChild(progressContainer);
    }
    
    progressContainer.classList.remove('hidden');
    zone.querySelector('.border-dashed').classList.add('border-blue-300', 'bg-blue-50');
}

/**
 * Mettre à jour la progression d'un fichier
 */
function updateFileProgress(zone, fileId, progress, fileIndex) {
    const progressBar = zone.querySelector('#progress-bar');
    const progressText = zone.querySelector('#progress-text');
    const fileInfo = zone.querySelector('#file-info');
    
    if (progressBar) {
        progressBar.style.width = `${progress}%`;
    }
    if (progressText) {
        progressText.textContent = `${Math.round(progress)}%`;
    }
    if (fileInfo) {
        const totalFiles = zone.querySelectorAll('input[type="file"]')[0].files.length;
        fileInfo.textContent = `${fileIndex + 1}/${totalFiles} fichier(s)`;
    }
}

/**
 * Afficher le succès d'upload
 */
function showUploadSuccess(zone, fileCount) {
    const progressContainer = zone.querySelector('.upload-progress');
    const uploadSuccess = zone.querySelector('.upload-success');
    
    if (progressContainer) {
        progressContainer.classList.add('hidden');
    }
    
    // Afficher le succès
    uploadSuccess.classList.remove('hidden');
    uploadSuccess.classList.add('animate-bounce-in');
    
    if (zone.dataset.field === 'photos_vehicule') {
        const photoCount = uploadSuccess.querySelector('.photo-count');
        if (photoCount) {
            photoCount.textContent = fileCount;
        }
    }
    
    zone.querySelector('.border-dashed').classList.remove('border-blue-300', 'bg-blue-50');
    zone.querySelector('.border-dashed').classList.add('border-green-300', 'bg-green-50');
}

/**
 * Afficher une erreur d'upload
 */
function showUploadError(zone, errorMessage) {
    const progressContainer = zone.querySelector('.upload-progress');
    const uploadContent = zone.querySelector('.upload-content');
    
    // Masquer la progression
    if (progressContainer) {
        progressContainer.classList.add('hidden');
    }
    
    // Afficher l'erreur
    let errorContainer = zone.querySelector('.upload-error');
    if (!errorContainer) {
        errorContainer = document.createElement('div');
        errorContainer.className = 'upload-error p-3 bg-red-50 border border-red-200 rounded-lg mb-2';
        zone.appendChild(errorContainer);
    }
    
    errorContainer.innerHTML = `
        <div class="flex items-center">
            <svg class="w-4 h-4 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L4.268 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
            </svg>
            <span class="text-sm text-red-700">${errorMessage}</span>
        </div>
    `;
    
    errorContainer.classList.remove('hidden');
    zone.querySelector('.border-dashed').classList.remove('border-blue-300', 'bg-blue-50');
    zone.querySelector('.border-dashed').classList.add('border-red-300', 'bg-red-50');
    
    // Réafficher le contenu d'upload
    uploadContent.classList.remove('hidden');
}

/**
 * Charger le gestionnaire d'upload
 */
function loadUploadManager() {
    // Le script upload-manager.js doit être chargé avant
    if (typeof window.uploadManager === 'undefined') {
        console.error('UploadManager non chargé. Vérifiez que upload-manager.js est inclus.');
    }
}

function nextStep() {
    if (validateCurrentStep()) {
        if (currentStep < totalSteps) {
            document.getElementById(`step-${currentStep}`).classList.add('hidden');
            currentStep++;
            const nextStepElement = document.getElementById(`step-${currentStep}`);
            nextStepElement.classList.remove('hidden');
            nextStepElement.classList.add('animate-slide-in');
            updateProgressBar();
            updateNavigation();
            if (currentStep === 4) {
                updateRecap();
            }
        }
    }
}

function prevStep() {
    if (currentStep > 1) {
        document.getElementById(`step-${currentStep}`).classList.add('hidden');
        currentStep--;
        const prevStepElement = document.getElementById(`step-${currentStep}`);
        prevStepElement.classList.remove('hidden');
        prevStepElement.classList.add('animate-slide-in');
        updateProgressBar();
        updateNavigation();
    }
}

function validateCurrentStep() {
    const currentStepElement = document.getElementById(`step-${currentStep}`);
    const requiredFields = currentStepElement.querySelectorAll('[required]');
    let isValid = true;
    let firstErrorField = null;

    // Nettoyer les erreurs précédentes
    clearAllFieldErrors();

    requiredFields.forEach(field => {
        if (field.type === 'file') {
            const zone = field.closest('.upload-zone');
            if (zone) {
                const fieldName = zone.dataset.field;
                const fieldLabel = getFieldLabel(field);
                
                if (!uploadedFiles[fieldName] || uploadedFiles[fieldName].length === 0) {
                    showFieldError(fieldName, `${fieldLabel} est obligatoire`);
                    if (!firstErrorField) firstErrorField = zone;
                    isValid = false;
                }
            }
        } else {
            if (!field.value.trim()) {
                const fieldName = field.getAttribute('name');
                const fieldLabel = getFieldLabel(field);
                showFieldError(fieldName, `${fieldLabel} est obligatoire`);
                if (!firstErrorField) firstErrorField = field;
                isValid = false;
            }
        }
    });

    if (!isValid) {
        currentStepElement.classList.add('animate-bounce-in');
        setTimeout(() => {
            currentStepElement.classList.remove('animate-bounce-in');
        }, 600);
        
        // Faire défiler vers la première erreur
        if (firstErrorField) {
            firstErrorField.scrollIntoView({ 
                behavior: 'smooth', 
                block: 'center' 
            });
        }
    }
    return isValid;
}

function getFieldLabel(field) {
    // Essayer de trouver le label associé
    const label = field.closest('.space-y-4, .grid, .mb-4, .mb-6')?.querySelector('label');
    if (label) {
        return label.textContent.trim().replace('*', '').replace(':', '');
    }
    
    // Labels par défaut basés sur le nom du champ
    const fieldLabels = {
        'nom_assure': 'Le nom de l\'assuré',
        'telephone_assure': 'Le numéro de téléphone',
        'numero_police': 'Le numéro de police',
        'date_sinistre': 'La date du sinistre',
        'lieu_sinistre': 'Le lieu du sinistre',
        'circonstances': 'Les circonstances',
        'conducteur_nom': 'Le nom du conducteur',
        'carte_grise_recto': 'La carte grise (recto)',
        'carte_grise_verso': 'La carte grise (verso)',
        'visite_technique_recto': 'La visite technique (recto)',
        'visite_technique_verso': 'La visite technique (verso)',
        'attestation_assurance': 'L\'attestation d\'assurance',
        'permis_conduire': 'Le permis de conduire'
    };
    
    return fieldLabels[field.name] || 'Ce champ';
}

function updateProgressBar() {
    const progressBar = document.getElementById('progress-bar');
    const currentStepText = document.getElementById('current-step-text');
    const percentage = (currentStep / totalSteps) * 100;
    progressBar.style.width = `${percentage}%`;
    currentStepText.textContent = currentStep;
    for (let i = 1; i <= totalSteps; i++) {
        const label = document.getElementById(`step-${i}-label`);
        if (i < currentStep) {
            label.className = 'text-saar-green font-semibold';
            if (!label.textContent.includes('✓')) {
                label.textContent = '✓ ' + label.textContent;
            }
        } else if (i === currentStep) {
            label.className = 'text-saar-red font-semibold';
            label.textContent = label.textContent.replace('✓ ', '');
        } else {
            label.className = 'text-gray-500';
            label.textContent = label.textContent.replace('✓ ', '');
        }
    }
}

function updateNavigation() {
    const prevBtn = document.getElementById('prev-btn');
    const nextBtn = document.getElementById('next-btn');
    const submitBtn = document.getElementById('submit-btn');
    const accept = document.getElementById('accept-conditions');
    if (currentStep > 1) {
        prevBtn.classList.remove('hidden');
    } else {
        prevBtn.classList.add('hidden');
    }
    if (currentStep < totalSteps) {
        nextBtn.classList.remove('hidden');
        submitBtn.classList.add('hidden');
    } else {
        nextBtn.classList.add('hidden');
        submitBtn.classList.remove('hidden');
        if (accept) {
            submitBtn.disabled = !accept.checked;
        }
    }
}

function updateRecap() {
    const form = document.getElementById('declaration-form');
    const formData = new FormData(form);
    document.getElementById('recap-nom').textContent = formData.get('nom_assure') || '';
    document.getElementById('recap-email').textContent = formData.get('email_assure') || '';
    document.getElementById('recap-telephone').textContent = formData.get('telephone_assure') || '';
    document.getElementById('recap-police').textContent = formData.get('numero_police') || '';
    document.getElementById('recap-date').textContent = formData.get('date_sinistre') || '';
    document.getElementById('recap-lieu').textContent = formData.get('lieu_sinistre') || '';
    document.getElementById('recap-conducteur').textContent = formData.get('conducteur_nom') || '';
}

function handleSubmit(e) {
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
    
    // Ajouter les fichiers uploadés de manière asynchrone
    const uploadedFilesData = [];
    Object.keys(uploadedFiles).forEach(key => {
        if (uploadedFiles[key] && uploadedFiles[key].length > 0) {
            if (key === 'photos_vehicule') {
                uploadedFiles[key].forEach((fileData, index) => {
                    uploadedFilesData.push({
                        field: `photos_vehicule[${index}]`,
                        stored_path: fileData.path,  // ← Corrigé : stored_path au lieu de path
                        type: 'photo_vehicule',      // ← Corrigé : type au lieu de field
                        original_name: fileData.originalName,
                        size: fileData.size,
                        mime_type: fileData.mimeType
                    });
                });
            } else {
                const fileData = uploadedFiles[key][0];
                uploadedFilesData.push({
                    field: key,
                    stored_path: fileData.path,      // ← Corrigé : stored_path au lieu de path
                    type: key,                       // ← Corrigé : type au lieu de field
                    original_name: fileData.originalName,
                    size: fileData.size,
                    mime_type: fileData.mimeType
                });
            }
        }
    });
    
    formData.append('uploaded_files', JSON.stringify(uploadedFilesData));
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
                throw new Error(
                    'Le serveur a retourné du HTML au lieu de JSON. Vérifiez la route et le contrôleur.'
                );
            }
            
            if (!response.ok) {
                if (response.status === 422) {
                    try {
                        const errorData = await response.json();
                        handleValidationErrors(errorData);
                        return;
                    } catch (parseError) {
                        console.error('Erreur parsing JSON 422:', parseError);
                        throw new Error('Erreur de validation (422) - Impossible de parser la réponse');
                    }
                }
                
                try {
                    const errorText = await response.text();
                    console.error('Error response:', errorText);
                    throw new Error(`Erreur ${response.status}: ${response.statusText}`);
                } catch (textError) {
                    throw new Error(`Erreur ${response.status}: ${response.statusText}`);
                }
            }
            
            try {
                return await response.json();
            } catch (jsonError) {
                console.error('Erreur parsing JSON:', jsonError);
                throw new Error('Erreur lors du parsing de la réponse JSON');
            }
        })
        .then(data => {
            if (data && data.success) {
                showSuccessPage(data);
            } else {
                throw new Error(data?.message || 'Erreur lors de la soumission');
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
}

document.addEventListener('change', function(e) {
    if (e.target && e.target.id === 'accept-conditions') {
        const submitBtn = document.getElementById('submit-btn');
        if (submitBtn) {
            submitBtn.disabled = !e.target.checked;
        }
    }
});

function showSuccessPage(data) {
    document.body.innerHTML = `
        <div class="min-h-screen bg-gradient-to-br from-green-50 to-blue-50 flex items-center justify-center p-4">
            <div class="max-w-lg w-full bg-white rounded-2xl shadow-2xl p-8 text-center animate-bounce-in">
                <div class="w-20 h-20 bg-gradient-to-r from-saar-green to-green-600 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-900 mb-4">Déclaration Envoyée !</h2>
                <p class="text-gray-600 mb-2">Votre sinistre a été déclaré avec succès.</p>
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <p class="text-sm text-blue-800 font-semibold mb-1">Numéro de référence:</p>
                    <p class="text-lg font-bold text-blue-900">${data.numero_sinistre || 'SIN-' + Date.now()}</p>
                </div>
                <p class="text-sm text-gray-600 mb-6">Vous recevrez un email de confirmation avec tous les détails de votre dossier.</p>
                <div class="flex flex-col sm:flex-row gap-3 justify-center">
                    <a href="${data.redirect_url || '/dashboard'}" class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-saar-blue to-blue-600 text-white font-semibold rounded-xl hover:from-blue-600 hover:to-blue-700 transition-all duration-300 transform hover:scale-105">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Voir ma déclaration
                    </a>
                    <a href="/" class="inline-flex items-center justify-center px-6 py-3 border-2 border-gray-300 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 hover:border-gray-400 transition-all duration-300">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        Retour à l'accueil
                    </a>
                </div>
            </div>
        </div>
    `;
}

function handleValidationErrors(errorData) {
    const submitBtn = document.getElementById('submit-btn');
    const submitText = submitBtn.querySelector('.submit-text');
    const loadingText = submitBtn.querySelector('.loading-text');
    submitBtn.disabled = false;
    submitText.classList.remove('hidden');
    loadingText.classList.add('hidden');
    submitBtn.classList.remove('animate-pulse');

    clearAllFieldErrors();

    if (errorData.errors) {
        Object.keys(errorData.errors).forEach(fieldName => {
            const errorMessages = errorData.errors[fieldName];
            if (Array.isArray(errorMessages) && errorMessages.length > 0) {
                showFieldError(fieldName, errorMessages[0]);
            }
        });
    }

    if (errorData.message) {
        showErrorMessage(errorData.message);
    }

    scrollToFirstError();
}

function showFieldError(fieldName, errorMessage) {
    let field = document.querySelector(`[name="${fieldName}"]`);
    let targetElement = field;
    
    if (!field) {
        const uploadZone = document.querySelector(`[data-field="${fieldName}"]`);
        if (uploadZone) {
            field = uploadZone;
            targetElement = uploadZone.querySelector('.border-dashed') || uploadZone;
        }
    }

    if (field) {
        // Appliquer les styles d'erreur sur l'élément cible
        if (targetElement) {
            targetElement.classList.add('border-red-500', 'bg-red-50');
        }
        
        // Trouver ou créer l'élément d'erreur
        let errorElement = field.querySelector('.field-error');
        if (!errorElement) {
            errorElement = document.createElement('div');
            errorElement.className = 'field-error text-red-600 text-sm mt-2 flex items-center';
            field.appendChild(errorElement);
        }
        
        errorElement.innerHTML = `
            <svg class="w-4 h-4 mr-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L4.268 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
            </svg>
            <span>${errorMessage}</span>
        `;
        errorElement.classList.remove('hidden');

        // Fonction de nettoyage
        const cleanupError = () => {
            if (targetElement) {
                targetElement.classList.remove('border-red-500', 'bg-red-50');
            }
            if (errorElement && errorElement.parentNode) {
                errorElement.classList.add('hidden');
            }
        };

        // Ajouter les listeners de nettoyage
        if (field.tagName === 'INPUT' || field.tagName === 'TEXTAREA' || field.tagName === 'SELECT') {
            field.addEventListener('input', cleanupError, { once: true });
            field.addEventListener('change', cleanupError, { once: true });
        } else if (field.classList.contains('upload-zone')) {
            const fileInput = field.querySelector('input[type="file"]');
            if (fileInput) {
                fileInput.addEventListener('change', cleanupError, { once: true });
            }
            // Nettoyer l'erreur quand un fichier est uploadé avec succès
            const observer = new MutationObserver((mutations) => {
                mutations.forEach((mutation) => {
                    if (mutation.type === 'childList') {
                        const successElement = field.querySelector('.upload-success');
                        if (successElement && !successElement.classList.contains('hidden')) {
                            cleanupError();
                            observer.disconnect();
                        }
                    }
                });
            });
            observer.observe(field, { childList: true, subtree: true });
        }
    }
}

function clearAllFieldErrors() {
    // Nettoyer les styles d'erreur
    document.querySelectorAll('.border-red-500, .bg-red-50').forEach(element => {
        element.classList.remove('border-red-500', 'bg-red-50');
    });
    
    // Nettoyer les messages d'erreur
    document.querySelectorAll('.field-error').forEach(errorElement => {
        if (errorElement.parentNode) {
            errorElement.classList.add('hidden');
        }
    });
}

function scrollToFirstError() {
    const firstError = document.querySelector('.field-error');
    if (firstError) {
        firstError.scrollIntoView({ 
            behavior: 'smooth', 
            block: 'center' 
        });
        
        const fieldContainer = firstError.closest('.space-y-4, .grid, .mb-4, .mb-6');
        if (fieldContainer) {
            fieldContainer.classList.add('animate-pulse');
            setTimeout(() => {
                fieldContainer.classList.remove('animate-pulse');
            }, 2000);
        }
    }
}

function showErrorMessage(message) {
    const existingError = document.querySelector('.error-notification');
    if (existingError) {
        existingError.remove();
    }
    const errorDiv = document.createElement('div');
    errorDiv.className =
        'error-notification fixed top-4 right-4 bg-red-500 text-white px-6 py-4 rounded-lg shadow-lg z-50 animate-slide-in max-w-md';
    errorDiv.innerHTML = `
        <div class="flex items-center">
            <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L4.268 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
            </svg>
            <span class="flex-1">${message}</span>
            <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-white hover:text-gray-200 flex-shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    `;
    document.body.appendChild(errorDiv);
    setTimeout(() => {
        if (errorDiv.parentNode) {
            errorDiv.parentNode.removeChild(errorDiv);
        }
    }, 8000);
}

function generateTiersForms(nombreTiers) {
    const container = document.getElementById('tiers-forms-container');
    container.innerHTML = '';
    
    const count = nombreTiers === '10+' ? 10 : parseInt(nombreTiers);
    
    for (let i = 1; i <= count; i++) {
        const tiersForm = createTiersForm(i);
        container.appendChild(tiersForm);
    }
}

function createTiersForm(numero) {
    const formDiv = document.createElement('div');
    formDiv.className = 'bg-gradient-to-r from-blue-50 to-indigo-50 p-6 rounded-xl border border-blue-200';
    
    formDiv.innerHTML = `
        <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            Tiers ${numero}
        </h4>
        
        <div class="grid md:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Nom du conducteur
                </label>
                <input type="text" name="tiers[${numero}][nom_conducteur]" 
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:border-saar-blue focus:ring-2 focus:ring-blue-100 transition-all">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Prénom du conducteur
                </label>
                <input type="text" name="tiers[${numero}][prenom_conducteur]" 
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:border-saar-blue focus:ring-2 focus:ring-blue-100 transition-all">
            </div>
        </div>
        
        <div class="grid md:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Téléphone
                </label>
                <input type="tel" name="tiers[${numero}][telephone]" 
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:border-saar-blue focus:ring-2 focus:ring-blue-100 transition-all">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Email
                </label>
                <input type="email" name="tiers[${numero}][email]" 
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:border-saar-blue focus:ring-2 focus:ring-blue-100 transition-all">
            </div>
        </div>
        
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Adresse
            </label>
            <textarea name="tiers[${numero}][adresse]" rows="2" 
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:border-saar-blue focus:ring-2 focus:ring-blue-100 transition-all resize-none"
                placeholder="Adresse complète du tiers..."></textarea>
        </div>
        
        <div class="grid md:grid-cols-3 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Marque du véhicule
                </label>
                <input type="text" name="tiers[${numero}][marque_vehicule]" 
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:border-saar-blue focus:ring-2 focus:ring-blue-100 transition-all">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Modèle du véhicule
                </label>
                <input type="text" name="tiers[${numero}][modele_vehicule]" 
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:border-saar-blue focus:ring-2 focus:ring-blue-100 transition-all">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Immatriculation
                </label>
                <input type="text" name="tiers[${numero}][immatriculation]" 
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:border-saar-blue focus:ring-2 focus:ring-blue-100 transition-all">
            </div>
        </div>
        
        <div class="grid md:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Compagnie d'assurance
                </label>
                <input type="text" name="tiers[${numero}][compagnie_assurance]" 
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:border-saar-blue focus:ring-2 focus:ring-blue-100 transition-all">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    N° Police d'assurance
                </label>
                <input type="text" name="tiers[${numero}][numero_police_assurance]" 
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:border-saar-blue focus:ring-2 focus:ring-blue-100 transition-all">
            </div>
        </div>
        
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Détails supplémentaires
            </label>
            <textarea name="tiers[${numero}][details_supplementaires]" rows="3" 
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:border-saar-blue focus:ring-2 focus:ring-blue-100 transition-all resize-none"
                placeholder="Informations complémentaires sur ce tiers..."></textarea>
        </div>
        
        <div class="grid md:grid-cols-2 gap-4">
            <div class="upload-zone" data-field="tiers_photos_${numero}">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Photos du véhicule du tiers (optionnel)
                </label>
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center cursor-pointer hover:border-saar-blue transition-colors">
                    <div class="upload-content">
                        <svg class="w-8 h-8 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                        </svg>
                        <p class="text-sm text-gray-600">Cliquez ou glissez les photos ici</p>
                        <p class="text-xs text-gray-500">JPG, PNG, PDF (max. 5MB)</p>
                    </div>
                    <div class="upload-success hidden">
                        <svg class="w-8 h-8 mx-auto text-green-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <p class="text-sm text-green-600 font-medium">Photos téléchargées</p>
                    </div>
                </div>
                <input type="file" name="tiers_photos_${numero}[]" multiple accept="image/*,.pdf" class="hidden">
            </div>
            
            <div class="upload-zone" data-field="tiers_attestation_${numero}">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Attestation d'assurance (optionnel)
                </label>
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center cursor-pointer hover:border-saar-blue transition-colors">
                    <div class="upload-content">
                        <svg class="w-8 h-8 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <p class="text-sm text-gray-600">Cliquez ou glissez le document ici</p>
                        <p class="text-xs text-gray-500">JPG, PNG, PDF (max. 5MB)</p>
                    </div>
                    <div class="upload-success hidden">
                        <svg class="w-8 h-8 mx-auto text-green-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <p class="text-sm text-green-600 font-medium">Attestation téléchargée</p>
                    </div>
                </div>
                <input type="file" name="tiers_attestation_${numero}" accept="image/*,.pdf" class="hidden">
            </div>
        </div>
    `;
    
    setupFileUploadsForTiers(formDiv);
    
    return formDiv;
}

function setupFileUploadsForTiers(formDiv) {
    const uploadZones = formDiv.querySelectorAll('.upload-zone');
    uploadZones.forEach(zone => {
        const input = zone.querySelector('input[type="file"]');
        const uploadArea = zone.querySelector('.border-dashed');
        
        uploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadArea.classList.add('border-saar-blue', 'bg-blue-50');
        });
        
        uploadArea.addEventListener('dragleave', (e) => {
            e.preventDefault();
            uploadArea.classList.remove('border-saar-blue', 'bg-blue-50');
        });
        
        uploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadArea.classList.remove('border-saar-blue', 'bg-blue-50');
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                input.files = files;
                handleFileUpload(zone, files);
            }
        });
        
        uploadArea.addEventListener('click', () => {
            input.click();
        });
        
        input.addEventListener('change', (e) => {
            handleFileUpload(zone, e.target.files);
        });
    });
} 