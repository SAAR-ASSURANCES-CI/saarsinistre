class SinistresManager {
    constructor() {
        this.currentPage = 1;
        this.currentPerPage = 10;
        this.currentSinistreId = null;
    }

    async loadSinistres() {
        Utils.showLoading(true);
        try {
            const params = {
                page: this.currentPage,
                per_page: this.currentPerPage,
                search: document.getElementById("search-input").value || "",
                statut: document.getElementById("status-filter").value || "",
                gestionnaire_id:
                    document.getElementById("gestionnaire-filter").value || "",
            };

            const data = await API.getSinistres(params);
            this.displaySinistres(data.data);
            this.updatePagination(data);
        } catch (error) {
            this.displayEmptyState();
        } finally {
            Utils.showLoading(false);
        }
    }

    displaySinistres(sinistres) {
        const tbody = document.getElementById("sinistres-tbody");
        if (sinistres.length === 0) return this.displayEmptyState();

        tbody.innerHTML = sinistres
            .map((sinistre) => this.createSinistreRow(sinistre))
            .join("");
    }

    createSinistreRow(sinistre) {
        const statusBadge = Utils.getStatusBadge(sinistre.statut);
        const urgencyIndicator = sinistre.en_retard
            ? '<span class="inline-block w-2 h-2 bg-red-500 rounded-full animate-pulse mr-2" title="En retard"></span>'
            : sinistre.jours_en_cours > 10
            ? '<span class="inline-block w-2 h-2 bg-yellow-500 rounded-full mr-2" title="Urgent"></span>'
            : "";

        const dateHeure = sinistre.heure_sinistre
            ? `${Utils.formatDate(sinistre.date_sinistre)} à ${Utils.formatTime(
                  sinistre.heure_sinistre
              )}`
            : Utils.formatDate(sinistre.date_sinistre);

        return `
            <tr class="hover:bg-gray-50 transition-colors">
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                        ${urgencyIndicator}
                        <div>
                            <div class="text-sm font-medium text-gray-900">${
                                sinistre.numero_sinistre
                            }</div>
                            <div class="text-sm text-gray-500">${
                                sinistre.numero_police
                            }</div>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div>
                        <div class="text-sm font-medium text-gray-900">${
                            sinistre.nom_assure
                        }</div>
                        <div class="text-sm text-gray-500">${
                            sinistre.telephone_assure
                        }</div>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">${dateHeure}</div>
                    <div class="text-sm text-gray-500">${
                        sinistre.jours_en_cours
                    } jour(s)</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    ${statusBadge}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">
                        ${
                            sinistre.gestionnaire
                                ? sinistre.gestionnaire.nom_complet
                                : '<span class="text-gray-400 italic">Non assigné</span>'
                        }
                    </div>
                    ${
                        sinistre.date_affectation
                            ? `<div class="text-xs text-gray-400">Affecté le ${Utils.formatDate(
                                  sinistre.date_affectation
                              )}</div>`
                            : ""
                    }
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <div class="flex space-x-2">
                        <button onclick="Modals.showDetails(${sinistre.id})"
                                class="text-saar-blue hover:text-blue-800 transition-colors"
                                title="Voir détails">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </button>

                        <button onclick="Modals.showAssignModal(${sinistre.id})"
                                class="text-purple-600 hover:text-purple-800 transition-colors"
                                title="Affecter gestionnaire">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                            </svg>
                        </button>

                        <button onclick="Modals.showStatusModal(${sinistre.id})"
                                class="text-saar-green hover:text-green-800 transition-colors"
                                title="Changer statut">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </button>

                        <a href="/gestionnaires/dashboard/sinistres/${sinistre.id}/fiche" target="_blank"
                           class="text-red-600 hover:text-red-800 transition-colors"
                           title="Télécharger la fiche sinistre">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v12m0 0l-3-3m3 3l3-3M5 20h14a2 2 0 002-2V7a2 2 0 00-2-2h-5l-2-2H5a2 2 0 00-2 2v13a2 2 0 002 2z"></path>
                            </svg>
                        </a>
                        ${window.userRole === 'expert' ? `
                        <button onclick="Modals.showExpertiseModal(${sinistre.id})"
                                class="text-orange-600 hover:text-orange-800 transition-colors"
                                title="Expertise">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </button>
                        ` : ''}
                    </div>
                </td>
                <td class="px-6 py-4 text-center">
                    <a href="/gestionnaires/sinistres/${sinistre.id}/chat" class="inline-block text-saar-blue hover:text-saar-red transition-transform duration-200 rounded-full p-2 shadow-sm hover:scale-110 focus:outline-none focus:ring-2 focus:ring-saar-red animate-bounce-in" title="Ouvrir la discussion">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m-2 8a9 9 0 100-18 9 9 0 000 18z" />
                        </svg>
                    </a>
                </td>
            </tr>
        `;
    }

    displayEmptyState() {
        const tbody = document.getElementById("sinistres-tbody");
        tbody.innerHTML = `
            <tr>
                <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                    <div class="flex flex-col items-center">
                        <svg class="w-12 h-12 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <p class="text-lg font-medium text-gray-900 mb-2">Aucun sinistre trouvé</p>
                        <p class="text-gray-500">Essayez de modifier vos critères de recherche</p>
                    </div>
                </td>
            </tr>
        `;
    }

    updatePagination(data) {
        const container = document.getElementById("pagination-container");
        const { current_page, last_page, per_page, total, from, to } = data;

        if (total === 0) {
            container.innerHTML = "";
            return;
        }

        const startItem = from || 0;
        const endItem = to || 0;

        container.innerHTML = `
            <div class="flex-1 flex justify-between sm:hidden">
                <button onclick="Sinistres.changePage(${current_page - 1})"
                        ${current_page <= 1 ? "disabled" : ""}
                        class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                    Précédent
                </button>
                <button onclick="Sinistres.changePage(${current_page + 1})"
                        ${current_page >= last_page ? "disabled" : ""}
                        class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                    Suivant
                </button>
            </div>
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm text-gray-700">
                        Affichage de <span class="font-medium">${startItem}</span> à <span class="font-medium">${endItem}</span> sur
                        <span class="font-medium">${total}</span> résultats
                    </p>
                </div>
                <div>
                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                        <button onclick="Sinistres.changePage(${
                            current_page - 1
                        })"
                                ${current_page <= 1 ? "disabled" : ""}
                                class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        ${this.generatePageNumbers(current_page, last_page)}
                        <button onclick="Sinistres.changePage(${
                            current_page + 1
                        })"
                                ${current_page >= last_page ? "disabled" : ""}
                                class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </nav>
                </div>
            </div>
        `;
    }

    generatePageNumbers(current, last) {
        let pages = "";
        const maxVisible = 5;
        let start = Math.max(1, current - Math.floor(maxVisible / 2));
        let end = Math.min(last, start + maxVisible - 1);

        if (end - start + 1 < maxVisible) {
            start = Math.max(1, end - maxVisible + 1);
        }

        for (let i = start; i <= end; i++) {
            const isActive = i === current;
            pages += `
                <button onclick="Sinistres.changePage(${i})"
                        class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium ${
                            isActive
                                ? "bg-saar-blue text-white border-saar-blue"
                                : "bg-white text-gray-700 hover:bg-gray-50"
                        }">
                    ${i}
                </button>
            `;
        }
        return pages;
    }

    changePage(page) {
        if (page < 1) return;
        this.currentPage = page;
        this.loadSinistres();
    }

    handleSearch() {
        this.currentPage = 1;
        this.loadSinistres();
    }

    handleFilter() {
        this.currentPage = 1;
        this.loadSinistres();
    }

    resetFilters() {
        document.getElementById("search-input").value = "";
        document.getElementById("status-filter").value = "";
        document.getElementById("gestionnaire-filter").value = "";
        this.currentPage = 1;
        this.loadSinistres();
    }

    async refreshStats() {
        try {
            const data = await API.getStats();
            this.updateStatsDisplay(data.stats);
        } catch (error) {
            console.error("Erreur lors du rafraîchissement des stats:", error);
        }
    }

    updateStatsDisplay(stats) {
        document.getElementById("stat-total").textContent = stats.total;
        document.getElementById("stat-en-attente").textContent =
            stats.en_attente;
        document.getElementById("stat-traites").textContent = stats.traites;
        document.getElementById("stat-en-cours").textContent = stats.en_cours;
        const exp = document.getElementById("stat-expertise-requise");
        if (exp) exp.textContent = stats.expertise_requise;

        const enRetard = document.getElementById("stat-en-retard");
        if (enRetard) enRetard.textContent = stats.en_retard;

        const enAttenteDocs = document.getElementById("stat-en-attente-documents");
        if (enAttenteDocs) enAttenteDocs.textContent = stats.en_attente_documents;

        const refuses = document.getElementById("stat-refuse");
        if (refuses) refuses.textContent = stats.refuse;
    }
}

const Sinistres = new SinistresManager();
