function toggleNotifications() {
    const dropdown = document.getElementById("notifications-dropdown");
    dropdown.classList.toggle("hidden");
}

function toggleUserMenu() {
    const menu = document.getElementById("user-menu");
    menu.classList.toggle("hidden");
}


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
    
    const gestionnairesContainer = document.getElementById(
        "gestionnaires-container"
    );
    if (gestionnairesContainer) {
        gestionnairesContainer.innerHTML = data.gestionnaires;
    }

    
    const assuresContainer = document.getElementById("assures-container");
    if (assuresContainer) {
        assuresContainer.innerHTML = data.assures;
    }

   
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

    
    window.history.pushState({}, "", window.location.pathname);
    fetchUsers("", "", "");
}


document.addEventListener("DOMContentLoaded", function () {
    
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

    const editUserForm = document.getElementById("edit-user-form");
    if (editUserForm) {
        editUserForm.addEventListener("submit", function (e) {
            e.preventDefault();
            const userId = this.action.split("/").pop();
            const formData = new FormData(this);
            
            if (!formData.has('actif')) {
                formData.append('actif', 0);
            }
            formData.append('_method', 'PUT');
            fetch(`/dashboard/users/${userId}`, {
                method: "POST",
                headers: {
                    "X-Requested-With": "XMLHttpRequest",
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                },
                body: formData,
            })
                .then((response) => {
                    if (!response.ok) throw new Error('Erreur lors de la modification');
                    return response.json();
                })
                .then((data) => {
                    closeEditUserModal();
                    const roleFilter = document.getElementById("filter-role");
                    if (roleFilter) {
                        roleFilter.value = "";
                    }
                    filterUsers();
                    if (window.toastr) toastr.success(data.success || 'Utilisateur modifié');
                })
                .catch(async (error) => {
                    let errorMsg = error.message;
                    if (error.response && error.response.status === 422) {
                        const data = await error.response.json();
                        errorMsg = Object.values(data.errors).join('\n');
                    }
                    if (window.toastr) toastr.error(errorMsg);
                    else alert(errorMsg);
                });
        });
    }
});


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


function openEditUserModal(userId) {
    
    fetch(`/dashboard/users/${userId}/edit`)
        .then((response) => response.json())
        .then((user) => {
            
            document.getElementById("edit-nom_complet").value =
                user.nom_complet;
            document.getElementById("edit-email").value = user.email;
            setTimeout(function() {
                document.getElementById("edit-role").value = user.role;
                const event = new Event('change', { bubbles: true });
                document.getElementById("edit-role").dispatchEvent(event);
            }, 0);

           
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

            
            document.getElementById(
                "edit-user-form"
            ).action = `/dashboard/users/${userId}`;

           
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

function openInfoUserModal(userId) {
    fetch(`/dashboard/users/${userId}/edit`)
        .then((response) => response.json())
        .then((user) => {
            document.getElementById("info-nom_complet").textContent = user.nom_complet;
            document.getElementById("info-email").textContent = user.email;
            document.getElementById("info-role").textContent = user.role === 'admin' ? 'Administrateur' : (user.role === 'gestionnaire' ? 'Gestionnaire' : 'Assuré');
            document.getElementById("info-actif").textContent = user.actif ? 'Actif' : 'Inactif';

            
            const numeroAssureContainer = document.getElementById("info-numero-assure-container");
            if (user.role === 'assure') {
                numeroAssureContainer.classList.remove("hidden");
                document.getElementById("info-numero_assure").textContent = user.numero_assure || 'N/A';
            } else {
                numeroAssureContainer.classList.add("hidden");
            }

            document.getElementById("info-user-modal").classList.remove("hidden");
        })
        .catch((error) => console.error("Erreur lors du chargement des infos utilisateur:", error));
}

function closeInfoUserModal() {
    document.getElementById("info-user-modal").classList.add("hidden");
}
