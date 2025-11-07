class Utils {
    static formatDate(dateString) {
        if (!dateString) return "";
        const date = new Date(dateString);
        return date.toLocaleDateString("fr-FR");
    }

    static formatTime(timeString) {
        if (!timeString) return "";
        
        // Vérifier si c'est un format HH:MM ou HH:MM:SS
        if (timeString.match(/^\d{2}:\d{2}(:\d{2})?$/)) {
            // Extraire uniquement HH:MM
            return timeString.substring(0, 5);
        }
        
        // Fallback pour les autres formats
        const date = new Date(timeString);
        if (isNaN(date.getTime())) {
            return timeString; 
        }
        return date.toLocaleTimeString("fr-FR", {
            hour: "2-digit",
            minute: "2-digit",
        });
    }

    static formatCurrency(amount) {
        if (!amount) return "0 FCFA";
        return new Intl.NumberFormat("fr-FR", {
            style: "currency",
            currency: "XOF",
            minimumFractionDigits: 0,
        }).format(amount);
    }

    static formatRelativeTime(dateString) {
        const date = new Date(dateString);
        const now = new Date();
        const diffInSeconds = Math.floor((now - date) / 1000);

        if (diffInSeconds < 60) return "À l'instant";
        if (diffInSeconds < 3600)
            return `Il y a ${Math.floor(diffInSeconds / 60)} min`;
        if (diffInSeconds < 86400)
            return `Il y a ${Math.floor(diffInSeconds / 3600)} h`;
        return `Il y a ${Math.floor(diffInSeconds / 86400)} j`;
    }

    static debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    static showLoading(show) {
        const loadingState = document.getElementById("loading-state");
        const tbody = document.getElementById("sinistres-tbody");

        if (show) {
            loadingState.classList.remove("hidden");
            tbody.style.opacity = "0.5";
        } else {
            loadingState.classList.add("hidden");
            tbody.style.opacity = "1";
        }
    }

    static showErrorMessage(message) {
        this.showNotification(message, "error");
    }

    static showSuccessMessage(message) {
        this.showNotification(message, "success");
    }

    static showNotification(message, type = "info") {
        const notification = document.createElement("div");
        const bgColor =
            type === "success"
                ? "bg-green-500"
                : type === "error"
                ? "bg-red-500"
                : "bg-blue-500";

        notification.className = `fixed top-4 right-4 ${bgColor} text-white px-6 py-4 rounded-lg shadow-lg z-50 animate-slide-in`;
        notification.innerHTML = `
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    ${
                        type === "success"
                            ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>'
                            : type === "error"
                            ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>'
                            : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>'
                    }
                </svg>
                <span>${message}</span>
                <button onclick="this.parentElement.parentElement.remove()" class="ml-4">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        `;

        document.body.appendChild(notification);
        setTimeout(() => notification.remove(), 5000);
    }

    static getStatusBadge(status) {
        const statusConfig = {
            en_attente: {
                label: "En attente",
                color: "bg-yellow-100 text-yellow-800",
            },
            en_cours: { label: "En cours", color: "bg-blue-100 text-blue-800" },
            expertise_requise: {
                label: "Expertise requise",
                color: "bg-purple-100 text-purple-800",
            },
            en_attente_documents: {
                label: "Attente documents",
                color: "bg-orange-100 text-orange-800",
            },
            pret_reglement: {
                label: "Prêt règlement",
                color: "bg-indigo-100 text-indigo-800",
            },
            regle: { label: "Réglé", color: "bg-green-100 text-green-800" },
            refuse: { label: "Refusé", color: "bg-red-100 text-red-800" },
            clos: { label: "Clos", color: "bg-gray-100 text-gray-800" },
        };

        const config = statusConfig[status] || {
            label: status,
            color: "bg-gray-100 text-gray-800",
        };
        return `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${config.color}">${config.label}</span>`;
    }
}
