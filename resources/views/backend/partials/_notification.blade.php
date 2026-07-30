@if (config('app.enable_in_app_notifications'))
<div class="dropdown d-md-flex notifications">
    <a class="nav-link icon" data-bs-toggle="dropdown">
        <svg xmlns="http://www.w3.org/2000/svg" enable-background="new 0 0 24 24" viewBox="0 0 24 24">
            <path d="M18,14.1V10c0-3.1-2.4-5.7-5.5-6V2.5C12.5,2.2,12.3,2,12,2s-0.5,0.2-0.5,0.5V4C8.4,4.3,6,6.9,6,10v4.1c-1.1,0.2-2,1.2-2,2.4v2C4,18.8,4.2,19,4.5,19h3.7c0.5,1.7,2,3,3.8,3c1.8,0,3.4-1.3,3.8-3h3.7c0.3,0,0.5-0.2,0.5-0.5v-2C20,15.3,19.1,14.3,18,14.1z M7,10c0-2.8,2.2-5,5-5s5,2.2,5,5v4H7V10z M13,20.8c-1.6,0.5-3.3-0.3-3.8-1.8h5.6C14.5,19.9,13.8,20.5,13,20.8z M19,18H5v-1.5C5,15.7,5.7,15,6.5,15h11c0.8,0,1.5,0.7,1.5,1.5V18z" />
        </svg>
        <span class="pulse-container">
            @if (auth()->check() && auth()->user()->unreadAppNotifications()->count() > 0)
                <span class="pulse"></span>
            @endif
        </span>
    </a>
    <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
        <div class="drop-heading border-bottom">
            <div class="d-flex">
                <h6 class="mt-1 mb-0 fs-15 text-dark">Notifications</h6>
                <div class="ms-auto" id="notification-clear-btn">
                    @if (auth()->check() && auth()->user()->unreadAppNotifications()->count() > 0)
                    <span class="xm-title badge bg-secondary text-white fw-normal fs-12 badge-pill">
                        <a onclick="markAllAsRead()" class="showall-text text-white" style="cursor:pointer">Clear</a>
                    </span>
                    @endif
                </div>
            </div>
        </div>
        <div class="notifications-menu ps3 overflow-scroll" id="notification">
        </div>
        <div class="dropdown-divider m-0"></div>
    </div>
</div>
@endif

<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
<script>
    function notification() {
        fetch("{{ route('notification.index') }}", {
            method: "GET",
            headers: {
                "Accept": "application/json",
                "X-Requested-With": "XMLHttpRequest"
            }
        })
        .then(response => response.json())
        .then(response => {
            let html = '';
            let notifications = [];
            if (response && response.data) {
                notifications = Array.isArray(response.data) ? response.data : (response.data.data || []);
            }

            let hasUnread = false;

            if (notifications.length > 0) {
                notifications.forEach(item => {
                    if (!item.read_at) {
                        hasUnread = true;
                    }
                    let createdAt = typeof moment !== 'undefined' ? moment(item.created_at).fromNow() : item.created_at;
                    let title = item.title ? `<strong class="d-block text-dark">${item.title}</strong>` : '';
                    let body = item.body || (item.data ? item.data.body : '');
                    let unreadStyle = item.read_at ? '' : 'background-color: #f0f4f9;';

                    html += `
                        <a class="dropdown-item" onclick="readnotify('${item.id}', '${item.link || ''}')" style="cursor:pointer; ${unreadStyle}">
                            <div class="notification-each d-flex align-items-center">
                                <div class="me-3 notifyimg bg-primary brround text-white d-flex align-items-center justify-content-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-bell" viewBox="0 0 16 16">
                                        <path d="M8 16a2 2 0 0 0 2-2H6a2 2 0 0 0 2 2M8 1.918l-.797.161A4 4 0 0 0 4 6c0 .628-.134 2.197-.459 3.742-.16.767-.376 1.566-.663 2.258h10.244c-.287-.692-.502-1.49-.663-2.258C12.134 8.197 12 6.628 12 6a4 4 0 0 0-3.203-3.92zM14.22 12c.223.447.481.801.78 1H1c.299-.199.557-.553.78-1C2.68 10.2 3 6.88 3 6c0-2.42 1.72-4.44 4.005-4.901a1 1 0 1 1 1.99 0A5 5 0 0 1 13 6c0 .88.32 4.2 1.22 6"/>
                                    </svg>
                                </div>
                                <div class="overflow-hidden">
                                    <span class="notification-label mb-1 fs-13 text-wrap d-block">${title}${body}</span>
                                    <span class="notification-subtext text-muted d-block fs-11">${createdAt}</span>
                                </div>
                            </div>
                        </a>
                    `;
                });
            } else {
                html = '<div class="text-center p-3 text-muted">No notifications found</div>';
            }

            const notifContainer = document.getElementById('notification');
            if (notifContainer) {
                notifContainer.innerHTML = html;
            }

            const pulseContainers = document.querySelectorAll('.pulse-container');
            pulseContainers.forEach(el => {
                el.innerHTML = hasUnread ? '<span class="pulse"></span>' : '';
            });

            const clearBtn = document.getElementById('notification-clear-btn');
            if (clearBtn) {
                clearBtn.innerHTML = hasUnread ? `
                    <span class="xm-title badge bg-secondary text-white fw-normal fs-12 badge-pill">
                        <a onclick="markAllAsRead()" class="showall-text text-white" style="cursor:pointer">Clear</a>
                    </span>
                ` : '';
            }
        })
        .catch(error => {
            console.error("Error fetching notifications:", error);
        });
    }

    function readnotify(id, link) {
        fetch("{{ route('notification.read.single', ['id' => ':id']) }}".replace(':id', id), {
            method: "POST",
            headers: {
                "Accept": "application/json",
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                "X-Requested-With": "XMLHttpRequest"
            }
        })
        .then(response => response.json())
        .then(response => {
            if (response.status === 'success') {
                if (typeof toastr !== 'undefined') {
                    toastr.success(response.message);
                }
                if (link && link !== 'null' && link !== '' && link !== 'undefined') {
                    window.location.href = link;
                    return;
                }
            }
            notification();
        })
        .catch(error => {
            console.error('Error reading notification:', error);
        });
    }

    function markAllAsRead() {
        fetch("{{ route('notification.read.all') }}", {
            method: "POST",
            headers: {
                "Accept": "application/json",
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                "X-Requested-With": "XMLHttpRequest"
            }
        })
        .then(response => response.json())
        .then(response => {
            if (response.status === 'success') {
                if (typeof toastr !== 'undefined') {
                    toastr.success(response.message);
                }
            } else {
                if (typeof toastr !== 'undefined') {
                    toastr.error(response.message);
                }
            }
            notification();
        })
        .catch(error => {
            console.error('Error marking all as read:', error);
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', notification);
    } else {
        notification();
    }

    @auth
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof Echo !== 'undefined') {
            Echo.private('user.{{ auth()->user()->id }}').listen('.notification.created', (e) => {

                if (typeof toastr !== 'undefined') {
                    // console.log(data->)
                    toastr.success((e.title || '') + ' ' + (e.body || ''));
                }
                notification();
            });

            Echo.private('test-notify.{{ auth()->user()->id }}').listen('TestNotificationEvent', (e) => {
                if (typeof toastr !== 'undefined') {
                    toastr.success((e.title || '') + ' ' + (e.body || ''));
                }
                notification();
            });
        }
    });
    @endauth
</script>