document.addEventListener("DOMContentLoaded", function () {
    // Initialisation des modules
    Sinistres.loadSinistres();
    Notifications.loadNotifications();

    // Configuration des écouteurs d'événements
    document.getElementById("search-input").addEventListener(
        "input",
        Utils.debounce(() => Sinistres.handleSearch(), 300)
    );

    document
        .getElementById("status-filter")
        .addEventListener("change", () => Sinistres.handleFilter());

    document
        .getElementById("gestionnaire-filter")
        .addEventListener("change", () => Sinistres.handleFilter());

    // Mise à jour périodique des stats
    setInterval(() => Sinistres.refreshStats(), 300000);

    // Gestion du clic en dehors des menus dropdown
    document.addEventListener("click", function (event) {
        if (!event.target.closest('[onclick*="toggleUserMenu"]')) {
            document.getElementById("user-menu").classList.add("hidden");
        }
        if (!event.target.closest('[onclick*="toggleNotifications"]')) {
            document
                .getElementById("notifications-dropdown")
                .classList.add("hidden");
        }
    });
});

// Fonction globale pour le menu utilisateur (peut être migrée vers une classe si nécessaire)
function toggleUserMenu() {
    document.getElementById("user-menu").classList.toggle("hidden");
}
