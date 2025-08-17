class ModalsManager {
    constructor() {
        this.currentSinistreId = null;
    }

    async showDetails(sinistreId) {
        try {
            const data = await API.getSinistreDetails(sinistreId);
            const sinistre = data.sinistre;
            const detailsContent = document.getElementById("sinistre-details-content");
    
            let documentsHTML = '';
            if (sinistre.documents && sinistre.documents.length > 0) {
                documentsHTML = `
                    <div class="mt-8">
                        <h4 class="font-semibold text-gray-900 mb-4 text-lg">Documents associés</h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                            ${sinistre.documents.map(doc => `
                                <div class="border rounded-lg p-4 hover:shadow-md transition-shadow">
                                    <div class="flex items-center mb-2">
                                        <span class="text-2xl mr-2">${this.getDocumentIcon(doc.type_mime)}</span>
                                        <div class="overflow-hidden">
                                            <p class="font-medium truncate">${doc.libelle_document || 'Document sans nom'}</p>
                                            <p class="text-xs text-gray-500">${doc.type_document || 'Type inconnu'}</p>
                                        </div>
                                    </div>
                                    <div class="flex justify-between items-center text-sm">
                                        <span class="text-gray-600">${this.formatFileSize(doc.taille_fichier)}</span>
                                        ${this.getDocumentActionButton(doc)}
                                    </div>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                `;
            } else {
                documentsHTML = `
                    <div class="mt-8 bg-gray-50 p-4 rounded-lg">
                        <p class="text-gray-500 text-center">Aucun document associé à ce sinistre</p>
                    </div>
                `;
            }
    
            let sinistreDetailsHTML = '';
            if (sinistre.dommages_releves || sinistre.commissariat || sinistre.officier_nom || 
                sinistre.conducteur_nom || sinistre.constat_autorite || sinistre.implique_tiers) {
                
                let tiersDetailsHTML = '';
                if (sinistre.implique_tiers) {
                    let tiersCardsHTML = '';
                    
                    // Affichage des tiers détaillés s'ils existent
                    if (sinistre.tiers && sinistre.tiers.length > 0) {
                        tiersCardsHTML = sinistre.tiers.map(tiers => `
                            <div class="bg-white border border-gray-200 rounded-lg p-4 mb-3">
                                <h6 class="font-medium text-gray-900 mb-3 flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    Tiers ${tiers.numero_tiers}
                                </h6>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                                    ${tiers.nom_conducteur || tiers.prenom_conducteur ? `
                                        <div>
                                            <span class="font-medium text-gray-600">Conducteur:</span>
                                            <span class="ml-1">${[tiers.prenom_conducteur, tiers.nom_conducteur].filter(Boolean).join(' ') || 'Non spécifié'}</span>
                                        </div>
                                    ` : ''}
                                    ${tiers.telephone ? `
                                        <div>
                                            <span class="font-medium text-gray-600">Téléphone:</span>
                                            <span class="ml-1">${tiers.telephone}</span>
                                        </div>
                                    ` : ''}
                                    ${tiers.email ? `
                                        <div>
                                            <span class="font-medium text-gray-600">Email:</span>
                                            <span class="ml-1">${tiers.email}</span>
                                        </div>
                                    ` : ''}
                                    ${tiers.marque_vehicule || tiers.modele_vehicule ? `
                                        <div>
                                            <span class="font-medium text-gray-600">Véhicule:</span>
                                            <span class="ml-1">${[tiers.marque_vehicule, tiers.modele_vehicule].filter(Boolean).join(' ') || 'Non spécifié'}</span>
                                        </div>
                                    ` : ''}
                                    ${tiers.immatriculation ? `
                                        <div>
                                            <span class="font-medium text-gray-600">Immatriculation:</span>
                                            <span class="ml-1">${tiers.immatriculation}</span>
                                        </div>
                                    ` : ''}
                                    ${tiers.compagnie_assurance ? `
                                        <div>
                                            <span class="font-medium text-gray-600">Assurance:</span>
                                            <span class="ml-1">${tiers.compagnie_assurance}</span>
                                        </div>
                                    ` : ''}
                                    ${tiers.numero_police_assurance ? `
                                        <div>
                                            <span class="font-medium text-gray-600">N° Police:</span>
                                            <span class="ml-1">${tiers.numero_police_assurance}</span>
                                        </div>
                                    ` : ''}
                                </div>
                                ${tiers.adresse ? `
                                    <div class="mt-2">
                                        <span class="font-medium text-gray-600">Adresse:</span>
                                        <p class="text-sm text-gray-700 mt-1">${tiers.adresse}</p>
                                    </div>
                                ` : ''}
                                ${tiers.details_supplementaires ? `
                                    <div class="mt-2">
                                        <span class="font-medium text-gray-600">Détails supplémentaires:</span>
                                        <p class="text-sm text-gray-700 mt-1">${tiers.details_supplementaires}</p>
                                    </div>
                                ` : ''}
                                ${tiers.documents && tiers.documents.length > 0 ? `
                                    <div class="mt-3">
                                        <span class="font-medium text-gray-600">Documents:</span>
                                        <div class="flex flex-wrap gap-2 mt-1">
                                            ${tiers.documents.map(doc => `
                                                <span class="inline-flex items-center px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">
                                                    ${this.getDocumentIcon(doc.type_mime)} ${doc.type_document || 'Document'}
                                                </span>
                                            `).join('')}
                                        </div>
                                    </div>
                                ` : ''}
                            </div>
                        `).join('');
                    }
                    
                    tiersDetailsHTML = `
                        <div class="mt-4">
                            <h5 class="font-medium text-gray-900 mb-3 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                Tiers impliqués ${sinistre.nombre_tiers ? `(${sinistre.nombre_tiers})` : ''}
                            </h5>
                            
                            ${tiersCardsHTML || `
                                <div class="bg-orange-50 p-3 rounded-lg">
                                    <p class="text-sm text-orange-700">Tiers impliqué confirmé</p>
                                    ${sinistre.details_tiers ? `<p class="text-sm text-gray-700 mt-1">${sinistre.details_tiers}</p>` : ''}
                                </div>
                            `}
                        </div>
                    `;
                }
    
                sinistreDetailsHTML = `
                    <div class="mt-4 bg-red-50 p-4 rounded-lg">
                        <h4 class="font-semibold text-gray-900 mb-3">Détails du Sinistre</h4>
                        <div class="space-y-2 text-sm">
                            ${sinistre.conducteur_nom ? `<p><span class="font-medium">Conducteur:</span> ${sinistre.conducteur_nom}</p>` : ''}
                            ${sinistre.constat_autorite ? `<p><span class="font-medium">Constat autorité:</span> ${sinistre.constat_autorite ? 'Oui' : 'Non'}</p>` : ''}
                            ${sinistre.officier_nom ? `<p><span class="font-medium">Officier:</span> ${sinistre.officier_nom}</p>` : ''}
                            ${sinistre.commissariat ? `<p><span class="font-medium">Commissariat:</span> ${sinistre.commissariat}</p>` : ''}
                            ${sinistre.dommages_releves ? `<p><span class="font-medium">Dommages relevés:</span> ${sinistre.dommages_releves}</p>` : ''}
                            <p><span class="font-medium">Tiers impliqué:</span> ${sinistre.implique_tiers ? 'Oui' : 'Non'}</p>
                            ${tiersDetailsHTML}
                        </div>
                    </div>
                `;
            }
    
            detailsContent.innerHTML = `
                <div class="grid md:grid-cols-2 gap-6">
                    <!-- Colonne gauche - Informations générales -->
                    <div class="space-y-4">
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="font-semibold text-gray-900 mb-3">Informations Générales</h4>
                            <div class="space-y-2 text-sm">
                                <p><span class="font-medium">Numéro:</span> ${sinistre.numero_sinistre}</p>
                                <p><span class="font-medium">Police:</span> ${sinistre.numero_police}</p>
                                <p><span class="font-medium">Date:</span> ${Utils.formatDate(sinistre.date_sinistre)}</p>
                                ${sinistre.heure_sinistre ? `<p><span class="font-medium">Heure:</span> ${Utils.formatTime(sinistre.heure_sinistre)}</p>` : ''}
                                <p><span class="font-medium">Lieu:</span> ${sinistre.lieu_sinistre}</p>
                                <p><span class="font-medium">Statut:</span> ${Utils.getStatusBadge(sinistre.statut)}</p>
                            </div>
                        </div>
    
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <h4 class="font-semibold text-gray-900 mb-3">Assuré</h4>
                            <div class="space-y-2 text-sm">
                                <p><span class="font-medium">Nom:</span> ${sinistre.nom_assure}</p>
                                <p><span class="font-medium">Email:</span> ${sinistre.email_assure}</p>
                                <p><span class="font-medium">Téléphone:</span> ${sinistre.telephone_assure}</p>
                            </div>
                        </div>
                    </div>
    
                    <!-- Colonne droite - Gestion -->
                    <div class="space-y-4">
                        <div class="bg-green-50 p-4 rounded-lg">
                            <h4 class="font-semibold text-gray-900 mb-3">Gestion</h4>
                            <div class="space-y-2 text-sm">
                                <p><span class="font-medium">Gestionnaire:</span> ${sinistre.gestionnaire ? sinistre.gestionnaire.nom_complet : "Non assigné"}</p>
                                ${sinistre.date_affectation ? `<p><span class="font-medium">Date affectation:</span> ${Utils.formatDate(sinistre.date_affectation)}</p>` : ''}
                                <p><span class="font-medium">Jours en cours:</span> ${sinistre.jours_en_cours}</p>
                                <p><span class="font-medium">En retard:</span> ${sinistre.en_retard ? "⚠️ Oui" : "✅ Non"}</p>
                                <p><span class="font-medium">Montant estimé:</span> ${Utils.formatCurrency(sinistre.montant_estime)}</p>
                                ${sinistre.montant_regle ? `<p><span class="font-medium">Montant réglé:</span> ${Utils.formatCurrency(sinistre.montant_regle)}</p>` : ''}
                                ${sinistre.date_reglement ? `<p><span class="font-medium">Date règlement:</span> ${Utils.formatDate(sinistre.date_reglement)}</p>` : ''}
                            </div>
                        </div>
    
                        ${sinistre.circonstances ? `
                            <div class="bg-yellow-50 p-4 rounded-lg">
                                <h4 class="font-semibold text-gray-900 mb-3">Circonstances</h4>
                                <p class="text-sm text-gray-700">${sinistre.circonstances}</p>
                            </div>
                        ` : ''}
                    </div>
                </div>
    
                ${sinistreDetailsHTML}
    
                ${documentsHTML}
    
                <div class="mt-6 bg-purple-50 p-4 rounded-lg">
                    <h4 class="font-semibold text-gray-900 mb-3">Actions Rapides</h4>
                    <div class="flex flex-wrap gap-3">
                        <button onclick="Modals.showAssignModal(${sinistre.id}); Modals.closeModal('details-modal');"
                                class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                            Affecter Gestionnaire
                        </button>
                        <button onclick="Modals.showStatusModal(${sinistre.id}); Modals.closeModal('details-modal');"
                                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                            Changer Statut
                        </button>
                    </div>
                </div>
            `;
    
            this.openModal("details-modal");
        } catch (error) {
            console.error("Erreur lors du chargement des détails:", error);
            Utils.showErrorMessage("Erreur lors du chargement des détails");
        }
    }

    getDocumentIcon(mimeType) {
        if (!mimeType) return '📎';
        if (mimeType.includes('image/')) return '🖼️';
        if (mimeType === 'application/pdf') return '📄';
        return '📎';
    }

    formatFileSize(bytes) {
        if (!bytes) return '0 KB';
        if (bytes < 1024) return bytes + ' bytes';
        if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
        return (bytes / 1048576).toFixed(1) + ' MB';
    }

    getDocumentActionButton(doc) {
        if (doc.type_mime && doc.type_mime.includes('image/')) {
            return `
                <button onclick="Modals.showImagePreview('${doc.chemin_fichier}', '${doc.libelle_document || 'Image'}')"
                    class="text-saar-blue hover:text-blue-700 cursor-pointer">
                    Prévisualiser
                </button>
            `;
        }
        return `
            <a href="/storage/${doc.chemin_fichier}" target="_blank" download
               class="text-saar-blue hover:text-blue-700">
                Télécharger
            </a>
        `;
    }

    showImagePreview(imagePath, title) {
        const previewModal = document.createElement('div');
        previewModal.className = 'fixed inset-0 bg-black bg-opacity-75 z-50 flex items-center justify-center p-4';
        previewModal.innerHTML = `
            <div class="bg-white rounded-lg max-w-4xl w-full max-h-[90vh] flex flex-col">
                <div class="flex justify-between items-center p-4 border-b">
                    <h3 class="text-lg font-bold truncate max-w-[80%]">${title}</h3>
                    <button onclick="this.parentElement.parentElement.parentElement.remove()" 
                            class="text-gray-500 hover:text-gray-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div class="flex-1 overflow-auto p-4 flex items-center justify-center">
                    <img src="/storage/${imagePath}" alt="${title}" class="max-w-full max-h-[70vh] object-contain">
                </div>
                <div class="p-4 border-t flex justify-end gap-3">
                    <button onclick="this.parentElement.parentElement.parentElement.remove()"
                            class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                        Fermer
                    </button>
                    <a href="/storage/${imagePath}" download 
                       class="px-4 py-2 bg-saar-blue text-white rounded-lg hover:bg-blue-700">
                        Télécharger
                    </a>
                </div>
            </div>
        `;
        document.body.appendChild(previewModal);
    }
    

    async showAssignModal(sinistreId) {
        try {
            const data = await API.getSinistreDetails(sinistreId);
            const sinistre = data.sinistre;

            this.currentSinistreId = sinistreId;
            document.getElementById(
                "assign-sinistre-info"
            ).textContent = `${sinistre.numero_sinistre} - ${sinistre.nom_assure}`;
            document.getElementById("assign-gestionnaire").value =
                sinistre.gestionnaire_id || "";
            this.openModal("assign-modal");
        } catch (error) {
            Utils.showErrorMessage(
                "Erreur lors du chargement des informations du sinistre"
            );
        }
    }

    async showStatusModal(sinistreId) {
        try {
            const data = await API.getSinistreDetails(sinistreId);
            const sinistre = data.sinistre;

            this.currentSinistreId = sinistreId;
            document.getElementById(
                "status-sinistre-info"
            ).textContent = `${sinistre.numero_sinistre} - ${sinistre.nom_assure}`;
            document.getElementById("new-status").value = sinistre.statut;
            document.getElementById("status-comment").value = "";
            this.openModal("status-modal");
        } catch (error) {
            Utils.showErrorMessage(
                "Erreur lors du chargement des informations du sinistre"
            );
        }
    }

    async confirmAssignment() {
        const gestionnaireId = document.getElementById(
            "assign-gestionnaire"
        ).value;
        if (!gestionnaireId || !this.currentSinistreId) {
            Utils.showErrorMessage("Veuillez sélectionner un gestionnaire");
            return;
        }

        try {
            await API.assignGestionnaire(
                this.currentSinistreId,
                gestionnaireId
            );
            Utils.showSuccessMessage("Gestionnaire affecté avec succès");
            this.closeModal("assign-modal");
            Sinistres.loadSinistres();
            Sinistres.refreshStats();
        } catch (error) {
            Utils.showErrorMessage(
                "Erreur lors de l'affectation du gestionnaire"
            );
        }
    }

    async confirmStatusChange() {
        const newStatus = document.getElementById("new-status").value;
        const comment = document.getElementById("status-comment").value;

        if (!newStatus || !this.currentSinistreId) {
            Utils.showErrorMessage("Veuillez sélectionner un statut");
            return;
        }

        try {
            await API.updateStatus(this.currentSinistreId, newStatus, comment);
            Utils.showSuccessMessage("Statut modifié avec succès");
            this.closeModal("status-modal");
            Sinistres.loadSinistres();
            Sinistres.refreshStats();
        } catch (error) {
            Utils.showErrorMessage("Erreur lors du changement de statut");
        }
    }

    openModal(modalId) {
        document.getElementById(modalId).classList.remove("hidden");
    }

    closeModal(modalId) {
        document.getElementById(modalId).classList.add("hidden");
        this.currentSinistreId = null;
    }
}

const Modals = new ModalsManager();
