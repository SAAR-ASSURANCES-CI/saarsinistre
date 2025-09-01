class DashboardAPI {
    constructor() {
        this.API_BASE = window.location.origin;
        this.CSRF_TOKEN = document
            .querySelector('meta[name="csrf-token"]')
            .getAttribute("content");
    }

    async request(url, options = {}) {
        const defaultOptions = {
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": this.CSRF_TOKEN,
                Accept: "application/json",
            },
        };

        try {
            const response = await fetch(url, {
                ...defaultOptions,
                ...options,
                headers: {
                    ...defaultOptions.headers,
                    ...options.headers,
                },
            });

            if (!response.ok)
                throw new Error(`HTTP error! status: ${response.status}`);
            return await response.json();
        } catch (error) {
            Utils.showErrorMessage("Erreur de communication avec le serveur");
            throw error;
        }
    }

    async getSinistres(params = {}) {
        const query = new URLSearchParams(params).toString();
        return this.request(`${this.API_BASE}/gestionnaires/dashboard/sinistres?${query}`);
    }

    async getSinistreDetails(id) {
        return this.request(
            `${this.API_BASE}/gestionnaires/dashboard/sinistres/${id}/details`
        );
    }

    async assignGestionnaire(sinistreId, gestionnaireId) {
        return this.request(
            `${this.API_BASE}/gestionnaires/dashboard/sinistres/${sinistreId}/assign`,
            {
                method: "POST",
                body: JSON.stringify({ gestionnaire_id: gestionnaireId }),
            }
        );
    }

    async updateStatus(sinistreId, status, comment = "") {
        return this.request(
            `${this.API_BASE}/gestionnaires/dashboard/sinistres/${sinistreId}/status`,
            {
                method: "POST",
                body: JSON.stringify({ statut: status, commentaire: comment }),
            }
        );
    }

    async getNotifications() {
        return this.request(`${this.API_BASE}/gestionnaires/dashboard/notifications`);
    }

    async markNotificationsAsRead() {
        return this.request(
            `${this.API_BASE}/gestionnaires/dashboard/notifications/mark-read`,
            {
                method: "POST",
            }
        );
    }

    async getStats() {
        return this.request(`${this.API_BASE}/gestionnaires/dashboard/stats`);
    }
}

const API = new DashboardAPI();
