function toggleNotifications() {
    const dropdown = document.getElementById("notifications-dropdown");
    dropdown.classList.toggle("hidden");
}

function toggleUserMenu() {
    const menu = document.getElementById("user-menu");
    menu.classList.toggle("hidden");
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
});

function openAddUserModal() {
    document.getElementById("add-user-modal").classList.remove("hidden");
}

function closeAddUserModal() {
    document.getElementById("add-user-modal").classList.add("hidden");
}

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

document.addEventListener("DOMContentLoaded", function () {
    const urlParams = new URLSearchParams(window.location.search);

    if (urlParams.has("search")) {
        document.getElementById("search-users").value = urlParams.get("search");
    }
    if (urlParams.has("role")) {
        document.getElementById("filter-role").value = urlParams.get("role");
    }
    if (urlParams.has("status")) {
        document.getElementById("filter-status").value =
            urlParams.get("status");
    }
});

window.onclick = function (event) {
    const modal = document.getElementById("add-user-modal");
    if (event.target === modal) {
        closeAddUserModal();
    }
};

function updateQueryStringParameter(uri, key, value) {
    const re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
    const separator = uri.indexOf("?") !== -1 ? "&" : "?";
    if (uri.match(re)) {
        return uri.replace(re, "$1" + key + "=" + value + "$2");
    }
    return uri + separator + key + "=" + value;
}

function filterUsers() {
    const searchTerm = document
        .getElementById("search-users")
        .value.toLowerCase();
    const roleFilter = document.getElementById("filter-role").value;
    const statusFilter = document.getElementById("filter-status").value;

    let url = window.location.pathname + "?";
    if (searchTerm) url += "search=" + encodeURIComponent(searchTerm) + "&";
    if (roleFilter) url += "role=" + encodeURIComponent(roleFilter) + "&";
    if (statusFilter) url += "status=" + encodeURIComponent(statusFilter);

    window.location.href = url;
}

function resetFilters() {
    document.getElementById("search-users").value = "";
    document.getElementById("filter-role").value = "";
    document.getElementById("filter-status").value = "";

    const rows = document.querySelectorAll(".user-row");
    rows.forEach((row) => {
        row.style.display = "";
    });
}

document.getElementById("search-users").addEventListener("input", filterUsers);
document.getElementById("filter-role").addEventListener("change", filterUsers);
document
    .getElementById("filter-status")
    .addEventListener("change", filterUsers);
