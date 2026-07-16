<!-- Notifications -->
<div class="position-relative me-2" x-data="notificationComponent()" x-init="init()">
    <button @click="notifOpen = !notifOpen"
        class="btn btn-link text-muted p-0 position-relative mt-1">
        <svg style="width:24px;height:24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
            </path>
        </svg>
        <span x-show="unreadCount > 0" x-cloak
            class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
            style="width:10px;height:10px;padding:0;"></span>
    </button>

    <!-- Notification Menu -->
    <div x-show="notifOpen" @click.away="notifOpen = false"
        class="dropdown-menu dropdown-menu-end shadow border p-0 show"
        style="display: none; width: 320px; position: absolute; right: 0; top: 100%; z-index: 1050;" x-cloak>

        <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom">
            <p class="fw-semibold small mb-0">Notifications</p>
            <span class="badge bg-primary bg-opacity-10 text-primary" x-text="unreadCount + ' New'"></span>
        </div>

        <div style="max-height: 256px; overflow-y: auto;">
            <template x-for="notif in notifications" :key="notif.id">
                <a href="#" @click.prevent="readNotify(notif.id)"
                    class="d-flex px-3 py-2 border-bottom text-decoration-none hover-bg-light">
                    <div class="flex-shrink-0">
                        <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center"
                            style="width:32px;height:32px;">
                            <svg style="width:16px;height:16px;" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ps-3 w-100">
                        <div class="text-dark small fw-medium mb-1" x-text="notif.body"></div>
                        <div class="text-muted" style="font-size:0.75rem;" x-text="formatDate(notif.created_at)"></div>
                    </div>
                </a>
            </template>
            <div x-show="notifications.length === 0" class="px-3 py-3 text-center text-muted small">
                No notifications found.
            </div>
        </div>

        <a href="#" @click.prevent="markAllAsRead()"
            class="d-block px-3 py-2 text-center small text-primary fw-medium border-top text-decoration-none">
            Mark all as read
        </a>
    </div>
</div>

<!-- Scripts for Notifications -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('notificationComponent', () => ({
            notifOpen: false,
            notifications: [],
            get unreadCount() {
                return this.notifications.length;
            },
            init() {
                this.fetchNotifications();

                // Wait for Echo to be initialized from app.js
                setTimeout(() => {
                    this.listenForChannels();
                }, 1000);
            },
            fetchNotifications() {
                axios.get("{{ route('notification.index') }}")
                    .then(response => {
                        this.notifications = response.data.data.data || [];
                    })
                    .catch(error => console.error("Error fetching notifications:", error));
            },
            readNotify(id) {
                axios.post("{{ route('notification.read.single', ['id' => ':id']) }}".replace(':id', id))
                    .then(response => {
                        if (response.data.status === 'success') {
                            toastr.success(response.data.message);
                            this.fetchNotifications();
                        } else {
                            toastr.error(response.data.message);
                        }
                    })
                    .catch(error => console.error("Error reading notification:", error));
            },
            markAllAsRead() {
                axios.post("{{ route('notification.read.all') }}")
                    .then(response => {
                        if (response.data.status === 'success') {
                            toastr.success(response.data.message);
                            this.fetchNotifications();
                        } else {
                            toastr.error(response.data.message);
                        }
                    })
                    .catch(error => console.error("Error marking all as read:", error));
            },
            formatDate(date) {
                return moment(date).fromNow();
            },
            listenForChannels() {
                if (window.Echo) {
                    window.Echo.private('user.{{ auth()->user()->id }}')
                        .listen('.notification.created', (e) => {
                            toastr.success(e.title + ' ' + e.body);
                            this.fetchNotifications();
                        });

                    window.Echo.channel('test-channel')
                        .listen('.test-message', (e) => {
                            console.log('Received test message:', e);
                        });
                } else {
                    console.warn("Laravel Echo not found. Realtime notifications disabled.");
                }
            }
        }));
    });
</script>