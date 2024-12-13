export default function notifications() {
    return {
        notifications: [],
        add(notification) {
            const id = Date.now();
            this.notifications.push({
                id,
                title: notification.title,
                message: notification.message,
                visible: true
            });

            setTimeout(() => {
                this.remove(id);
            }, 5000);
        },
        remove(id) {
            const index = this.notifications.findIndex(n => n.id === id);
            if (index > -1) {
                this.notifications[index].visible = false;
                setTimeout(() => {
                    this.notifications = this.notifications.filter(n => n.id !== id);
                }, 300);
            }
        }
    };
} 