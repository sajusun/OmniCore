@if (config('app.enable_in_app_notifications', true))
<style>
    .notif-btn-read {
        transition: all 0.2s ease !important;
    }
    .notif-btn-read:hover {
        background: #2e7d32 !important;
        color: #ffffff !important;
        border-color: #2e7d32 !important;
        transform: scale(1.08);
    }
    .notif-btn-delete {
        transition: all 0.2s ease !important;
    }
    .notif-btn-delete:hover {
        background: #c62828 !important;
        color: #ffffff !important;
        border-color: #c62828 !important;
        transform: scale(1.08);
    }
    .notification-item-row:hover {
        background-color: #eef4fc !important;
    }
</style>

<div class="dropdown d-md-flex notifications">
    {{-- Notice data-bs-auto-close="outside" keeps the dropdown open on inner clicks --}}
    <a class="nav-link icon"
       data-bs-toggle="dropdown"
       data-bs-auto-close="outside"
       id="notificationDropdownBtn"
       style="cursor: pointer;">
        <svg xmlns="http://www.w3.org/2000/svg" enable-background="new 0 0 24 24" viewBox="0 0 24 24" width="22" height="22">
            <path fill="currentColor" d="M18,14.1V10c0-3.1-2.4-5.7-5.5-6V2.5C12.5,2.2,12.3,2,12,2s-0.5,0.2-0.5,0.5V4C8.4,4.3,6,6.9,6,10v4.1c-1.1,0.2-2,1.2-2,2.4v2C4,18.8,4.2,19,4.5,19h3.7c0.5,1.7,2,3,3.8,3c1.8,0,3.4-1.3,3.8-3h3.7c0.3,0,0.5-0.2,0.5-0.5v-2C20,15.3,19.1,14.3,18,14.1z M7,10c0-2.8,2.2-5,5-5s5,2.2,5,5v4H7V10z M13,20.8c-1.6,0.5-3.3-0.3-3.8-1.8h5.6C14.5,19.9,13.8,20.5,13,20.8z M19,18H5v-1.5C5,15.7,5.7,15,6.5,15h11c0.8,0,1.5,0.7,1.5,1.5V18z" />
        </svg>
        <span class="pulse-container">
            @if (auth()->check() && auth()->user()->unreadAppNotifications()->count() > 0)
                <span class="pulse"></span>
            @endif
        </span>
    </a>

    {{-- Dropdown Container with explicit click stop --}}
    <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow p-0 border-0 shadow-lg"
         id="notification-dropdown-menu"
         onclick="event.stopPropagation()"
         style="min-width: 390px; width: 430px; max-width: 95vw; border-radius: 14px; overflow: hidden; box-shadow: 0 15px 35px rgba(0,0,0,0.15);">
        
        {{-- Header --}}
        <div class="p-3 border-bottom bg-white d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <h6 class="mb-0 fs-15 fw-bold text-dark">Notifications</h6>
                <span class="badge bg-primary text-white rounded-pill fs-11 px-2 py-1" id="notification-unread-badge">0 Unread</span>
            </div>
            <div id="notification-clear-btn">
                <button type="button" onclick="markAllAsRead(event)" class="btn btn-sm btn-link text-primary p-0 text-decoration-none fs-12 fw-bold">
                    ✓ Mark all read
                </button>
            </div>
        </div>

        {{-- Notifications Scrollable Body --}}
        <div class="notifications-menu overflow-auto" id="notification" style="max-height: 390px;">
            <div class="text-center p-4 text-muted fs-13">Loading notifications...</div>
        </div>

        {{-- Footer / Cursor Pagination Load More --}}
        <div id="notification-load-more-wrap" class="p-2 border-top bg-light text-center" style="display: none;" onclick="event.stopPropagation()">
            <button type="button" id="btn-load-more-notif" onclick="loadMoreNotifications(event)" class="btn btn-sm btn-primary w-100 fs-12 py-2 fw-semibold shadow-sm">
                Load more notifications
            </button>
        </div>
    </div>
</div>
@endif

<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
<script>
    window._notificationNextCursor = null;
    window._notificationIsLoading = false;

    function fetchNotifications(append = false) {
        if (window._notificationIsLoading) return;
        window._notificationIsLoading = true;

        const notifContainer = document.getElementById('notification');
        const loadMoreWrap = document.getElementById('notification-load-more-wrap');
        const loadMoreBtn = document.getElementById('btn-load-more-notif');

        if (append && loadMoreBtn) {
            loadMoreBtn.innerText = 'Loading...';
            loadMoreBtn.disabled = true;
        }

        let url = "{{ route('notification.index') }}";
        if (append && window._notificationNextCursor) {
            url += (url.includes('?') ? '&' : '?') + 'cursor=' + encodeURIComponent(window._notificationNextCursor);
        }

        fetch(url, {
            method: "GET",
            headers: {
                "Accept": "application/json",
                "X-Requested-With": "XMLHttpRequest"
            }
        })
        .then(response => response.json())
        .then(response => {
            window._notificationIsLoading = false;
            let notifications = [];
            if (response && response.data) {
                notifications = Array.isArray(response.data) ? response.data : (response.data.data || []);
            }

            window._notificationNextCursor = response.next_cursor || null;

            let html = '';
            if (notifications.length > 0) {
                notifications.forEach(item => {
                    html += renderNotificationItem(item);
                });
            } else if (!append) {
                html = `
                    <div class="text-center py-5 px-3 text-muted">
                        <div class="mb-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="38" height="38" fill="currentColor" class="bi bi-bell-slash text-muted opacity-50" viewBox="0 0 16 16">
                                <path d="M5.164 14H15c-.288-.692-.503-1.49-.663-2.258C13.134 8.197 13 6.628 13 6a5 5 0 0 0-1.528-3.556l-.707.707a4 4 0 0 1 1.235 2.849c0 .628.134 2.197.459 3.742.16.767.376 1.566.663 2.258H6.164zm-1.045-.584l-.797.797A2 2 0 0 0 6 16h4a2 2 0 0 0 1.996-1.802l-.797-.797c-.067.14-.144.275-.23.402a1 1 0 0 1-.969.197H6a1 1 0 0 1-.969-.197 2 2 0 0 1-.912-1.385M1.49 1.49a.5.5 0 0 1 .707 0l12.5 12.5a.5.5 0 0 1-.707.707l-1.636-1.636A4 4 0 0 1 12 13H4c.299-.199.557-.553.78-1 .225-.447.44-1.22.68-2.258.325-1.545.459-3.114.459-3.742 0-.629.135-2.197.46-3.742.062-.294.137-.584.223-.868L1.49 2.197a.5.5 0 0 1 0-.707M4.68 12h5.539l-1.5-1.5H7.75C7.2 10.5 7 10 7 9.5V8.293L4.68 6z"/>
                            </svg>
                        </div>
                        <span class="fs-13">No notifications found</span>
                    </div>
                `;
            }

            if (notifContainer) {
                if (append) {
                    notifContainer.insertAdjacentHTML('beforeend', html);
                } else {
                    notifContainer.innerHTML = html;
                }
            }

            // Manage Load More button
            if (loadMoreWrap && loadMoreBtn) {
                if (response.has_more && window._notificationNextCursor) {
                    loadMoreWrap.style.display = 'block';
                    loadMoreBtn.innerText = 'Load more notifications';
                    loadMoreBtn.disabled = false;
                } else {
                    loadMoreWrap.style.display = 'none';
                }
            }

            // Update badge & pulse
            updateUnreadState(response.unread_count);
        })
        .catch(error => {
            window._notificationIsLoading = false;
            console.error("Error fetching notifications:", error);
            if (loadMoreBtn) {
                loadMoreBtn.innerText = 'Load more notifications';
                loadMoreBtn.disabled = false;
            }
        });
    }

    function renderNotificationItem(item) {
        const isRead = !!item.read_at;
        const createdAt = (typeof moment !== 'undefined' && item.created_at) ? moment(item.created_at).fromNow() : (item.created_at || '');
        const title = item.title ? `<strong class="d-block text-dark fs-13 mb-1 text-truncate">${item.title}</strong>` : '';
        const bodyText = item.body || (item.data ? item.data.body : '');
        const body = bodyText ? `<p class="mb-1 text-muted fs-12 text-wrap" style="line-height: 1.45; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">${bodyText}</p>` : '';

        const bgStyle = isRead ? 'background: #ffffff;' : 'background: #f4f8fe; border-left: 3.5px solid #0d6efd;';

        // Vibrant Green Mark Read Button
        const readActionBtn = !isRead ? `
            <button type="button"
                    class="btn btn-sm rounded-circle p-0 d-flex align-items-center justify-content-center notif-btn-read shadow-sm"
                    style="width: 32px; height: 32px; background: #e8f5e9 !important; border: 1.5px solid #81c784 !important; color: #1b5e20 !important; cursor: pointer;"
                    title="Mark as read"
                    onclick="markSingleRead('${item.id}', event)">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M12.736 3.97a.733.733 0 0 1 1.047 0c.286.289.29.756.01 1.05L7.88 12.01a.733.733 0 0 1-1.065.02L3.217 8.384a.757.757 0 0 1 0-1.06.733.733 0 0 1 1.047 0l3.052 3.093 5.4-6.425z"/>
                </svg>
            </button>
        ` : `
            <span class="d-flex align-items-center justify-content-center rounded-circle"
                  style="width: 32px; height: 32px; background: #f0f0f0; border: 1px solid #dcdcdc; color: #8c8c8c;"
                  title="Already read">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M12.354 4.354a.5.5 0 0 0-.708-.708L5 10.293 1.854 7.146a.5.5 0 1 0-.708.708l3.5 3.5a.5.5 0 0 0 .708 0zm-4.208 7-.896-.897.707-.707.543.543 6.646-6.647a.5.5 0 0 1 .708.708l-7 7a.5.5 0 0 1-.708 0"/>
                    <path d="m5.354 7.146.896.897-.707.707-.897-.896a.5.5 0 1 1 .708-.708"/>
                </svg>
            </span>
        `;

        // Vibrant Red Delete Button
        const deleteActionBtn = `
            <button type="button"
                    class="btn btn-sm rounded-circle p-0 d-flex align-items-center justify-content-center notif-btn-delete shadow-sm"
                    style="width: 32px; height: 32px; background: #ffebee !important; border: 1.5px solid #e57373 !important; color: #c62828 !important; cursor: pointer;"
                    title="Delete notification"
                    onclick="deleteNotification('${item.id}', event)">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z"/>
                    <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z"/>
                </svg>
            </button>
        `;

        // Vibrant Left Icon (Blue when unread, Slate when read)
        const iconStyle = isRead 
            ? 'background: #f1f3f5 !important; border: 1.5px solid #ced4da !important; color: #495057 !important;'
            : 'background: #e7f1ff !important; border: 1.5px solid #9ec5fe !important; color: #0d6efd !important;';

        return `
            <div id="notif-item-${item.id}"
                 class="d-flex align-items-center justify-content-between p-3 border-bottom position-relative notification-item-row"
                 style="cursor: pointer; transition: background 0.15s ease; ${bgStyle}"
                 onclick="onNotificationClick('${item.id}', '${item.link || ''}')">
                
                {{-- Left: Icon & Text --}}
                <div class="d-flex align-items-start gap-2 me-2 overflow-hidden flex-grow-1">
                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 shadow-sm" 
                         style="width: 38px; height: 38px; ${iconStyle}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M8 16a2 2 0 0 0 2-2H6a2 2 0 0 0 2 2m.995-14.901a1 1 0 1 0-1.99 0A5 5 0 0 0 3 6c0 1.098-.5 6-2 7h14c-1.5-1-2-5.902-2-7 0-2.42-1.72-4.44-4.005-4.901"/>
                        </svg>
                    </div>
                    <div class="overflow-hidden flex-grow-1">
                        ${title}
                        ${body}
                        <span class="text-muted fs-11 fw-medium">${createdAt}</span>
                    </div>
                </div>

                {{-- Right: Action Buttons --}}
                <div class="d-flex align-items-center gap-2 flex-shrink-0 ms-2" onclick="event.stopPropagation()">
                    ${readActionBtn}
                    ${deleteActionBtn}
                </div>
            </div>
        `;
    }

    function loadMoreNotifications(event) {
        if (event) {
            event.preventDefault();
            event.stopPropagation();
        }
        fetchNotifications(true);
    }

    function onNotificationClick(id, link) {
        markSingleRead(id, null, () => {
            if (link && link !== 'null' && link !== '' && link !== 'undefined') {
                window.location.href = link;
            }
        });
    }

    function markSingleRead(id, event, callback = null) {
        if (event) {
            event.preventDefault();
            event.stopPropagation();
        }

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
            if (response.code === 200 || response.status === 'success') {
                const itemEl = document.getElementById(`notif-item-${id}`);
                if (itemEl) {
                    itemEl.style.background = '#ffffff';
                    itemEl.style.borderLeft = 'none';

                    // Update action button to "already read" icon
                    const actionsDiv = itemEl.querySelector('.d-flex.align-items-center.gap-2');
                    if (actionsDiv) {
                        const readBtn = actionsDiv.querySelector('.notif-btn-read');
                        if (readBtn) {
                            readBtn.outerHTML = `
                                <span class="d-flex align-items-center justify-content-center rounded-circle"
                                      style="width: 32px; height: 32px; background: #f0f0f0; border: 1px solid #dcdcdc; color: #8c8c8c;"
                                      title="Already read">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                        <path d="M12.354 4.354a.5.5 0 0 0-.708-.708L5 10.293 1.854 7.146a.5.5 0 1 0-.708.708l3.5 3.5a.5.5 0 0 0 .708 0zm-4.208 7-.896-.897.707-.707.543.543 6.646-6.647a.5.5 0 0 1 .708.708l-7 7a.5.5 0 0 1-.708 0"/>
                                        <path d="m5.354 7.146.896.897-.707.707-.897-.896a.5.5 0 1 1 .708-.708"/>
                                    </svg>
                                </span>
                            `;
                        }
                    }

                    // Update left icon to greyish read style
                    const leftIcon = itemEl.querySelector('.rounded-circle.shadow-sm');
                    if (leftIcon) {
                        leftIcon.style.cssText = 'width: 38px; height: 38px; background: #f1f3f5 !important; border: 1.5px solid #ced4da !important; color: #495057 !important;';
                    }
                }
                updateUnreadState(response.unread_count);
            }
            if (typeof callback === 'function') {
                callback();
            }
        })
        .catch(error => {
            console.error('Error marking notification as read:', error);
            if (typeof callback === 'function') {
                callback();
            }
        });
    }

    function deleteNotification(id, event) {
        if (event) {
            event.preventDefault();
            event.stopPropagation();
        }

        const itemEl = document.getElementById(`notif-item-${id}`);
        if (itemEl) {
            itemEl.style.opacity = '0.3';
            itemEl.style.pointerEvents = 'none';
        }

        fetch("{{ route('notification.destroy', ['id' => ':id']) }}".replace(':id', id), {
            method: "DELETE",
            headers: {
                "Accept": "application/json",
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                "X-Requested-With": "XMLHttpRequest"
            }
        })
        .then(response => response.json())
        .then(response => {
            if (response.code === 200 || response.status === 'success') {
                if (itemEl) {
                    itemEl.remove();
                }
                updateUnreadState(response.unread_count);
                if (typeof toastr !== 'undefined') {
                    toastr.success(response.message || 'Notification deleted.');
                }
            } else {
                if (itemEl) {
                    itemEl.style.opacity = '1';
                    itemEl.style.pointerEvents = 'auto';
                }
            }
        })
        .catch(error => {
            console.error('Error deleting notification:', error);
            if (itemEl) {
                itemEl.style.opacity = '1';
                itemEl.style.pointerEvents = 'auto';
            }
        });
    }

    function markAllAsRead(event) {
        if (event) {
            event.preventDefault();
            event.stopPropagation();
        }

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
            if (response.code === 200 || response.status === 'success') {
                fetchNotifications(false);
                if (typeof toastr !== 'undefined') {
                    toastr.success(response.message || 'All notifications marked as read.');
                }
            }
        })
        .catch(error => {
            console.error('Error marking all notifications as read:', error);
        });
    }

    function updateUnreadState(count) {
        const unreadCount = parseInt(count || 0, 10);
        const badge = document.getElementById('notification-unread-badge');
        if (badge) {
            badge.innerText = `${unreadCount} Unread`;
            badge.style.display = unreadCount > 0 ? 'inline-block' : 'none';
        }

        const pulseContainers = document.querySelectorAll('.pulse-container');
        pulseContainers.forEach(el => {
            el.innerHTML = unreadCount > 0 ? '<span class="pulse"></span>' : '';
        });

        const clearBtn = document.getElementById('notification-clear-btn');
        if (clearBtn) {
            clearBtn.style.display = unreadCount > 0 ? 'block' : 'none';
        }
    }

    // Initialize on load
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => fetchNotifications(false));
    } else {
        fetchNotifications(false);
    }

    // Real-Time Echo Listeners
    @auth
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof Echo !== 'undefined') {
            Echo.private('user.{{ auth()->user()->id }}').listen('.notification.created', (e) => {
                if (typeof toastr !== 'undefined') {
                    toastr.success((e.title || '') + (e.body ? ': ' + e.body : ''));
                }
                fetchNotifications(false);
            });
        }
    });
    @endauth
</script>