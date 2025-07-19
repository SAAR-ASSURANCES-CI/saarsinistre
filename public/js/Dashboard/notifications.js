class NotificationsManager {
    constructor() {
        this.notificationCount = 0;
    }

    async loadNotifications() {
        try {
            const data = await API.getNotifications();
            this.displayNotifications(data.notifications, data.total_unread);
        } catch (error) {
            console.error(
                "Erreur lors du chargement des notifications:",
                error
            );
        }
    }

    displayNotifications(notifications, totalUnread) {
        const countElement = document.getElementById("notification-count");
        const listElement = document.getElementById("notifications-list");

        // Update count
        this.notificationCount = totalUnread;
        if (totalUnread > 0) {
            countElement.textContent = totalUnread;
            countElement.classList.remove("hidden");
        } else {
            countElement.classList.add("hidden");
        }

        // Display notifications
        if (notifications.length === 0) {
            listElement.innerHTML =
                '<div class="p-4 text-center text-gray-500">Aucune notification</div>';
            return;
        }

        listElement.innerHTML = notifications
            .map(
                (notification) => `
            <div class="p-4 border-b border-gray-100 hover:bg-gray-50">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        ${this.getNotificationIcon(notification.type)}
                    </div>
                    <div class="ml-3 flex-1">
                        <p class="text-sm font-medium text-gray-900">${
                            notification.title
                        }</p>
                        <p class="text-sm text-gray-500">${
                            notification.message
                        }</p>
                        <p class="text-xs text-gray-400 mt-1">${Utils.formatRelativeTime(
                            notification.created_at
                        )}</p>
                    </div>
                </div>
            </div>
        `
            )
            .join("");
    }

    getNotificationIcon(type) {
        const icons = {
            warning:
                '<div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center"><svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L4.268 16.5c-.77.833.192 2.5 1.732 2.5z"></path></svg></div>',
            info: '<div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center"><svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></div>',
            urgent: '<div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center"><svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L4.268 16.5c-.77.833.192 2.5 1.732 2.5z"></path></svg></div>',
        };
        return icons[type] || icons["info"];
    }

    toggleNotifications() {
        const dropdown = document.getElementById("notifications-dropdown");
        dropdown.classList.toggle("hidden");

        if (!dropdown.classList.contains("hidden")) {
            this.markNotificationsAsRead();
        }
    }

    async markNotificationsAsRead() {
        try {
            await API.markNotificationsAsRead();
            document
                .getElementById("notification-count")
                .classList.add("hidden");
            this.notificationCount = 0;
        } catch (error) {
            console.error("Erreur lors du marquage des notifications:", error);
        }
    }
}

const Notifications = new NotificationsManager();
