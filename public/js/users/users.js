function toggleNotifications() {
    const dropdown = document.getElementById("notifications-dropdown");
    dropdown.classList.toggle("hidden");
}

function toggleUserMenu() {
    const menu = document.getElementById("user-menu");
    menu.classList.toggle("hidden");
}

// Fonction principale de filtrage AJAX
function filterUsers() {
    const searchTerm = document.getElementById("search-users").value;
    const roleFilter = document.getElementById("filter-role").value;
    const statusFilter = document.getElementById("filter-status").value;

    updateUrlWithoutReload(searchTerm, roleFilter, statusFilter);
    fetchUsers(searchTerm, roleFilter, statusFilter);
}

function updateUrlWithoutReload(search, role, status) {
    const params = new URLSearchParams();
    if (search) params.set("search", search);
    if (role) params.set("role", role);
    if (status) params.set("status", status);

    const newUrl = window.location.pathname + "?" + params.toString();
    window.history.pushState({}, "", newUrl);
}

function fetchUsers(search, role, status) {
    const params = new URLSearchParams();
    if (search) params.append("search", search);
    if (role) params.append("role", role);
    if (status) params.append("status", status);

    fetch(`/dashboard/users?${params.toString()}`, {
        headers: {
            "X-Requested-With": "XMLHttpRequest",
            Accept: "application/json",
        },
    })
        .then((response) => response.json())
        .then((data) => {
            updateUserTables(data);
        })
        .catch((error) => console.error("Error:", error));
}

function updateUserTables(data) {
    // Mettre à jour la table des gestionnaires
    const gestionnairesContainer = document.getElementById(
        "gestionnaires-container"
    );
    if (gestionnairesContainer) {
        gestionnairesContainer.innerHTML = data.gestionnaires;
    }

    // Mettre à jour la table des assurés
    const assuresContainer = document.getElementById("assures-container");
    if (assuresContainer) {
        assuresContainer.innerHTML = data.assures;
    }

    // Mettre à jour la pagination
    const gestionnairesPagination = document.getElementById(
        "gestionnaires-pagination"
    );
    const assuresPagination = document.getElementById("assures-pagination");
    if (gestionnairesPagination && assuresPagination) {
        gestionnairesPagination.innerHTML = data.gestionnaires_pagination;
        assuresPagination.innerHTML = data.assures_pagination;
    }
}

function resetFilters() {
    document.getElementById("search-users").value = "";
    document.getElementById("filter-role").value = "";
    document.getElementById("filter-status").value = "";

    // Réinitialiser l'URL et charger tous les utilisateurs
    window.history.pushState({}, "", window.location.pathname);
    fetchUsers("", "", "");
}

// Initialisation après le chargement du DOM
document.addEventListener("DOMContentLoaded", function () {
    // Gestion des tabs
    const tabs = document.querySelectorAll("[data-tabs-target]");

    tabs.forEach((tab) => {
        tab.addEventListener("click", function () {
            const target = document.querySelector(this.dataset.tabsTarget);
            const targetId = target.id;

            document
                .querySelectorAll(".tab-content > div")
                .forEach((content) => {
                    content.classList.add("hidden");
                });

            target.classList.remove("hidden");

            if (targetId === "gestionnaires") {
                document
                    .getElementById("gestionnaires-pagination")
                    .classList.remove("hidden");
                document
                    .getElementById("assures-pagination")
                    .classList.add("hidden");
            } else if (targetId === "assures") {
                document
                    .getElementById("assures-pagination")
                    .classList.remove("hidden");
                document
                    .getElementById("gestionnaires-pagination")
                    .classList.add("hidden");
            }

            tabs.forEach((t) => {
                t.classList.remove("border-saar-blue", "text-saar-blue");
                t.classList.add("border-transparent", "text-gray-500");
            });

            this.classList.remove("border-transparent", "text-gray-500");
            this.classList.add("border-saar-blue", "text-saar-blue");
        });
    });

    if (tabs.length > 0) {
        tabs[0].click();
    }

    // Configuration des écouteurs d'événements pour les filtres
    let searchTimeout;
    document
        .getElementById("search-users")
        .addEventListener("input", function () {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(filterUsers, 300);
        });

    document
        .getElementById("filter-role")
        .addEventListener("change", filterUsers);
    document
        .getElementById("filter-status")
        .addEventListener("change", filterUsers);

    // Gestion des clics en dehors des menus déroulants
    document.addEventListener("click", function (event) {
        const notificationsDropdown = document.getElementById(
            "notifications-dropdown"
        );
        const userMenu = document.getElementById("user-menu");

        if (!event.target.closest(".relative")) {
            notificationsDropdown.classList.add("hidden");
            userMenu.classList.add("hidden");
        }
    });
});

// Gestion de la modal
function openAddUserModal() {
    document.getElementById("add-user-modal").classList.remove("hidden");
}

function closeAddUserModal() {
    document.getElementById("add-user-modal").classList.add("hidden");
}

window.onclick = function (event) {
    const modal = document.getElementById("add-user-modal");
    if (event.target === modal) {
        closeAddUserModal();
    }
};

// Gestion du champ numéro assuré
function toggleAssureField() {
    const role = document.getElementById("role").value;
    const container = document.getElementById("numero-assure-container");

    if (role === "assure") {
        container.classList.remove("hidden");
        document.getElementById("numero_assure").required = true;
    } else {
        container.classList.add("hidden");
        document.getElementById("numero_assure").required = false;
    }
}

// Fonctions pour la modal d'édition
function openEditUserModal(userId) {
    // Récupérer les données de l'utilisateur via AJAX
    fetch(`/dashboard/users/${userId}/edit`)
        .then((response) => response.json())
        .then((user) => {
            // Remplir le formulaire
            document.getElementById("edit-nom_complet").value =
                user.nom_complet;
            document.getElementById("edit-email").value = user.email;
            document.getElementById("edit-role").value = user.role;
            document.getElementById("edit-actif").checked = user.actif;

            // Gérer le champ numéro assuré
            if (user.role === "assure") {
                document
                    .getElementById("edit-numero-assure-container")
                    .classList.remove("hidden");
                document.getElementById("edit-numero_assure").value =
                    user.numero_assure || "";
            } else {
                document
                    .getElementById("edit-numero-assure-container")
                    .classList.add("hidden");
            }

            // Mettre à jour l'action du formulaire
            document.getElementById(
                "edit-user-form"
            ).action = `/dashboard/users/${userId}`;

            // Afficher la modal
            document
                .getElementById("edit-user-modal")
                .classList.remove("hidden");
        })
        .catch((error) => console.error("Error:", error));
}

function closeEditUserModal() {
    document.getElementById("edit-user-modal").classList.add("hidden");
}

function toggleEditAssureField() {
    const role = document.getElementById("edit-role").value;
    const container = document.getElementById("edit-numero-assure-container");

    if (role === "assure") {
        container.classList.remove("hidden");
        document.getElementById("edit-numero_assure").required = true;
    } else {
        container.classList.add("hidden");
        document.getElementById("edit-numero_assure").required = false;
    }
}

function editUser(userId) {
    openEditUserModal(userId);
}

window.onclick = function(event) {
    const addModal = document.getElementById('add-user-modal');
    const editModal = document.getElementById('edit-user-modal');

    if (event.target === addModal) {
        closeAddUserModal();
    }
    if (event.target === editModal) {
        closeEditUserModal();
    }
};
