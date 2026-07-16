<!-- Notifications -->
<div class="relative mr-4" x-data="notificationComponent()" x-init="init()">
    <button @click="notifOpen = !notifOpen"
        class="relative text-gray-500 hover:text-indigo-600 dark:text-gray-400 focus:outline-none transition-colors mt-1">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
            </path>
        </svg>
        <span x-show="unreadCount > 0" x-cloak
            class="absolute top-0 right-0 block h-2.5 w-2.5 rounded-full bg-red-500 ring-2 ring-white dark:ring-gray-800"></span>
    </button>

    <!-- Notification Menu -->
    <div x-show="notifOpen" @click.away="notifOpen = false" x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95"
        class="absolute right-0 mt-2 w-80 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-100 dark:border-gray-700 py-1 z-50"
        style="display: none;">

        <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
            <p class="text-sm font-semibold text-gray-900 dark:text-white">Notifications</p>
            <span
                class="bg-indigo-100 text-indigo-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-indigo-900 dark:text-indigo-300"
                x-text="unreadCount + ' New'"></span>
        </div>

        <div class="max-h-64 overflow-y-auto">
            <template x-for="notif in notifications" :key="notif.id">
                <a href="#" @click.prevent="readNotify(notif.id)"
                    class="flex px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors border-b border-gray-50 dark:border-gray-700/50 cursor-pointer">
                    <div class="flex-shrink-0">
                        <div
                            class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center dark:bg-blue-900/50 dark:text-blue-400">
                            <!-- Notification Icon -->
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="w-full pl-3">
                        <div class="text-gray-900 dark:text-gray-100 text-sm mb-1.5 font-medium" x-text="notif.body">
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400" x-text="formatDate(notif.created_at)">
                        </div>
                    </div>
                </a>
            </template>
            <div x-show="notifications.length === 0" class="px-4 py-4 text-center text-sm text-gray-500">
                No notifications found.
            </div>
        </div>

        <a href="#" @click.prevent="markAllAsRead()"
            class="block px-4 py-2 text-sm text-center text-indigo-600 dark:text-indigo-400 font-medium border-t border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
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
                        // console.log("Fetched notifications:", response.data);
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
                            // this.fetchNotifications();
                        });
                } else {
                    console.warn("Laravel Echo not found. Realtime notifications disabled.");
                }
            }
        }));
    });
</script>