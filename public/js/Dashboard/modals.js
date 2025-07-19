class ModalsManager {
    constructor() {
        this.currentSinistreId = null;
    }

    async showDetails(sinistreId) {
        try {
            const data = await API.getSinistreDetails(sinistreId);
            const sinistre = data.sinistre;
            const detailsContent = document.getElementById(
                "sinistre-details-content"
            );

            detailsContent.innerHTML = `
                <div class="grid md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="font-semibold text-gray-900 mb-3">Informations Générales</h4>
                            <div class="space-y-2 text-sm">
                                <p><span class="font-medium">Numéro:</span> ${sinistre.numero_sinistre
                }</p>
                                <p><span class="font-medium">Police:</span> ${sinistre.numero_police
                }</p>
                                <p><span class="font-medium">Date:</span> ${Utils.formatDate(
                    sinistre.date_sinistre
                )}</p>
                                ${sinistre.heure_sinistre
                    ? `<p><span class="font-medium">Heure:</span> ${Utils.formatTime(
                        sinistre.heure_sinistre
                    )}</p>`
                    : ""
                }
                                <p><span class="font-medium">Lieu:</span> ${sinistre.lieu_sinistre
                }</p>
                                <p><span class="font-medium">Statut:</span> ${Utils.getStatusBadge(
                    sinistre.statut
                )}</p>
                            </div>
                        </div>

                        <div class="bg-blue-50 p-4 rounded-lg">
                            <h4 class="font-semibold text-gray-900 mb-3">Assuré</h4>
                            <div class="space-y-2 text-sm">
                                <p><span class="font-medium">Nom:</span> ${sinistre.nom_assure
                }</p>
                                <p><span class="font-medium">Email:</span> ${sinistre.email_assure
                }</p>
                                <p><span class="font-medium">Téléphone:</span> ${sinistre.telephone_assure
                }</p>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="bg-green-50 p-4 rounded-lg">
                            <h4 class="font-semibold text-gray-900 mb-3">Gestion</h4>
                            <div class="space-y-2 text-sm">
                                <p><span class="font-medium">Gestionnaire:</span> ${sinistre.gestionnaire
                    ? sinistre.gestionnaire.nom_complet
                    : "Non assigné"
                }</p>
                                ${sinistre.date_affectation
                    ? `<p><span class="font-medium">Date affectation:</span> ${Utils.formatDate(
                        sinistre.date_affectation
                    )}</p>`
                    : ""
                }
                                <p><span class="font-medium">Jours en cours:</span> ${sinistre.jours_en_cours
                }</p>
                                <p><span class="font-medium">En retard:</span> ${sinistre.en_retard ? "⚠️ Oui" : "✅ Non"
                }</p>
                                <p><span class="font-medium">Montant estimé:</span> ${Utils.formatCurrency(
                    sinistre.montant_estime
                )}</p>
                                ${sinistre.montant_regle
                    ? `<p><span class="font-medium">Montant réglé:</span> ${Utils.formatCurrency(
                        sinistre.montant_regle
                    )}</p>`
                    : ""
                }
                                ${sinistre.date_reglement
                    ? `<p><span class="font-medium">Date règlement:</span> ${Utils.formatDate(
                        sinistre.date_reglement
                    )}</p>`
                    : ""
                }
                            </div>
                        </div>

                        ${sinistre.circonstances
                    ? `
                            <div class="bg-yellow-50 p-4 rounded-lg">
                                <h4 class="font-semibold text-gray-900 mb-3">Circonstances</h4>
                                <p class="text-sm text-gray-700">${sinistre.circonstances}</p>
                            </div>
                        `
                    : ""
                }
                    </div>
                </div>

                <div class="mt-6 bg-purple-50 p-4 rounded-lg">
                    <h4 class="font-semibold text-gray-900 mb-3">Actions Rapides</h4>
                    <div class="flex flex-wrap gap-3">
                        <button onclick="Modals.showAssignModal(${sinistre.id
                }); Modals.closeModal('details-modal');"
                                class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                            Affecter Gestionnaire
                        </button>
                        <button onclick="Modals.showStatusModal(${sinistre.id
                }); Modals.closeModal('details-modal');"
                                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                            Changer Statut
                        </button>
                    </div>
                </div>
            `;

            this.openModal("details-modal");
        } catch (error) {
            Utils.showErrorMessage("Erreur lors du chargement des détails");
        }
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
