<x-admin-layout>
    <x-slot name="title">Mail & Notification</x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Mail & Notification</h2>
    </x-slot>

    @push('styles')
    <style>
        /* Sleek Modern Tabs */
        .nav-tabs {
            border-bottom: 1px solid #e5e7eb;
        }
        .nav-tabs .nav-link {
            color: #6b7280;
            border: none;
            border-bottom: 2px solid transparent;
            font-weight: 600;
            font-size: 0.9375rem;
            padding: 1rem 1.5rem;
            transition: all 0.15s ease-in-out;
            background: transparent;
        }
        .nav-tabs .nav-link:hover {
            color: #111827;
            border-color: transparent;
        }
        .nav-tabs .nav-link.active {
            color: #8fbd56 !important;
            font-weight: 700 !important;
            background: transparent !important;
            border-bottom: 2px solid #8fbd56 !important;
        }

        /* Input & UI Cleanup */
        .form-control, .btn, .card, .alert, .form-select {
            border-radius: 0 !important;
        }
        .form-control:focus, .form-select:focus {
            border-color: #8fbd56;
            box-shadow: 0 0 0 3px rgba(143, 189, 86, 0.15);
        }

        /* Button Hover Fix - Using Client's Brand Primary Color (#8fbd56) */
        .btn-primary {
            background-color: var(--primary-bg-color, #8fbd56) !important;
            border-color: var(--primary-bg-color, #8fbd56) !important;
            color: #ffffff !important;
        }
        .btn-primary:hover, .btn-primary:focus, .btn-primary:active {
            background-color: var(--primary-bg-hover, #7cb342) !important;
            border-color: var(--primary-bg-hover, #7cb342) !important;
            color: #ffffff !important;
        }
        .btn-light {
            background-color: #f3f4f6 !important;
            border-color: #d1d5db !important;
            color: #374151 !important;
        }
        .btn-light:hover, .btn-light:focus, .btn-light:active {
            background-color: #e5e7eb !important;
            border-color: #9ca3af !important;
            color: #111827 !important;
        }

        /* User Table Selection Container */
        .user-scroll-container {
            position: relative;
            max-height: 420px;
            overflow-y: auto;
            border: 1px solid #e5e7eb;
            background-color: #ffffff;
        }
        .user-select-table {
            width: 100%;
            border-collapse: collapse;
            margin: 0;
        }
        .user-select-table th {
            background-color: #f8fafc;
            color: #475569;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 0.85rem 1rem;
            border-bottom: 1px solid #e2e8f0;
            position: sticky;
            top: 0;
            z-index: 10;
        }
        .user-select-table td {
            padding: 0.85rem 1rem;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
            font-size: 0.875rem;
            background-color: #ffffff;
            transition: background-color 0.1s ease;
        }
        .user-select-table tr:hover td {
            background-color: #f8fafc;
        }
        .user-select-table tr.selected td {
            background-color: #f4fbf0;
        }

        /* Force Checkboxes to Move in Normal Scroll Flow */
        .user-select-table .form-check-input {
            position: static !important;
            float: none !important;
            margin: 0 !important;
            vertical-align: middle !important;
            width: 1.2rem !important;
            height: 1.2rem !important;
            cursor: pointer;
            display: inline-block !important;
        }

        .avatar-circle {
            width: 34px;
            height: 34px;
            border-radius: 50% !important;
            background: linear-gradient(135deg, #8fbd56 0%, #7cb342 100%);
            color: #ffffff;
            font-weight: 600;
            font-size: 0.8rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        /* Custom Radio Buttons */
        .form-check-input:checked {
            background-color: #8fbd56;
            border-color: #8fbd56;
        }
    </style>
    @endpush

    {{-- Breadcrumb & Header --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <nav aria-label="breadcrumb" class="mb-1">
                <ol class="breadcrumb mb-0" style="font-size: 0.875rem;">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active text-dark fw-medium" aria-current="page">Mail & Notification</li>
                </ol>
            </nav>
            <h4 class="fw-bold text-dark mb-0">Mail & In-App Notification Center</h4>
        </div>
    </div>

    {{-- Alert Messages --}}
    @if(session('t-success') || session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4 border-0 border-start border-4 border-success shadow-sm" role="alert">
            <i class="fa fa-check-circle me-2"></i> {{ session('t-success') ?? session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('t-error') || session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-4 border-0 border-start border-4 border-danger shadow-sm" role="alert">
            <i class="fa fa-exclamation-triangle me-2"></i> {{ session('t-error') ?? session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Main Container Card --}}
    <div class="card border-0 shadow-sm" style="border-radius: 0;">
        
        {{-- Navigation Tabs Header --}}
        <div class="card-header bg-white border-bottom p-0">
            <ul class="nav nav-tabs card-header-tabs m-0" id="mainNotificationTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-item nav-link active" id="inapp-main-tab" data-bs-toggle="tab"
                        data-bs-target="#inapp_tab_content" type="button" role="tab" aria-controls="inapp_tab_content" aria-selected="true">
                        <i class="fa fa-bell me-2" style="color: #8fbd56;"></i> In-App Notification
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-item nav-link" id="email-main-tab" data-bs-toggle="tab"
                        data-bs-target="#email_tab_content" type="button" role="tab" aria-controls="email_tab_content" aria-selected="false">
                        <i class="fa fa-envelope me-2" style="color: #8fbd56;"></i> Email Notification
                    </button>
                </li>
            </ul>
        </div>

        <div class="card-body p-4">
            <div class="tab-content" id="mainNotificationTabsContent">

                {{-- ==================================================================== --}}
                {{-- TAB 1: IN-APP NOTIFICATION --}}
                {{-- ==================================================================== --}}
                <div class="tab-pane fade show active" id="inapp_tab_content" role="tabpanel" aria-labelledby="inapp-main-tab">
                    
                    {{-- Validation Alert for In-App --}}
                    <div id="inapp_validation_alert" class="alert alert-warning alert-dismissible fade show d-none mb-4" role="alert">
                        <i class="fa fa-exclamation-circle me-2"></i> <span id="inapp_validation_alert_text">Please select at least one recipient.</span>
                        <button type="button" class="btn-close" onclick="this.parentElement.classList.add('d-none')"></button>
                    </div>

                    <form action="{{ route('admin.notifications.send-inapp') }}" method="POST">
                        @csrf

                        {{-- IN-APP STEP 1: SELECT RECIPIENTS --}}
                        <div id="inapp_step_1">
                            <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                                <div>
                                    <h6 class="fw-bold text-dark mb-1">Target Audience</h6>
                                    <p class="small text-muted mb-0">Choose whether to notify all registered users or pick specific accounts.</p>
                                </div>
                                <span class="badge bg-light text-dark border px-3 py-2 fw-semibold" id="inapp_selected_count_badge">0 Users Selected</span>
                            </div>

                            <div class="d-flex gap-4 mb-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="target_type" id="inapp_target_selected" value="selected" checked>
                                    <label class="form-check-label fw-semibold text-dark cursor-pointer" for="inapp_target_selected">
                                        Select Specific Users
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="target_type" id="inapp_target_all" value="all">
                                    <label class="form-check-label fw-semibold text-dark cursor-pointer" for="inapp_target_all">
                                        All Registered Users ({{ $users->count() }})
                                    </label>
                                </div>
                            </div>

                            {{-- Clean User Table Selection Container --}}
                            <div id="inapp_specific_users_wrapper" class="mb-4">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <div class="input-group input-group-sm w-50">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="fa fa-search text-muted"></i>
                                        </span>
                                        <input type="text" id="inapp_user_search" class="form-control border-start-0" placeholder="Filter users by name or email...">
                                    </div>
                                    <span class="small text-muted">Showing {{ $users->count() }} accounts</span>
                                </div>

                                <div class="user-scroll-container">
                                    <table class="user-select-table">
                                        <thead>
                                            <tr>
                                                <th style="width: 50px; text-align: center;">
                                                    <input class="form-check-input" type="checkbox" id="inapp_select_all_cb">
                                                </th>
                                                <th>User</th>
                                                <th>Email</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($users as $user)
                                                <tr class="inapp-user-item">
                                                    <td style="width: 50px; text-align: center;">
                                                        <input class="form-check-input inapp-user-checkbox" type="checkbox" name="user_ids[]" value="{{ $user->id }}" id="inapp_u_{{ $user->id }}">
                                                    </td>
                                                    <td>
                                                        <label class="d-flex align-items-center cursor-pointer mb-0 w-100" for="inapp_u_{{ $user->id }}">
                                                            @php
                                                                $inappAvatarUrl = !empty($user->avatar) 
                                                                    ? (filter_var($user->avatar, FILTER_VALIDATE_URL) ? $user->avatar : asset($user->avatar))
                                                                    : asset('default/profile.png');
                                                            @endphp
                                                            <img src="{{ $inappAvatarUrl }}" alt="{{ $user->name }}" class="me-2.5" style="width: 34px; height: 34px; object-fit: cover; border: 1px solid #e2e8f0; flex-shrink: 0;" onError="this.onerror=null;this.src='{{ asset('default/profile.png') }}';">
                                                            <span class="fw-semibold text-dark">{{ $user->name }}</span>
                                                        </label>
                                                    </td>
                                                    <td>
                                                        <span class="text-muted">{{ $user->email }}</span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end pt-2">
                                <button type="button" id="btn_inapp_next" class="btn btn-primary px-4 py-2 fw-semibold">
                                    Next: Compose Message <i class="fa fa-arrow-right ms-2"></i>
                                </button>
                            </div>
                        </div>

                        {{-- IN-APP STEP 2: COMPOSE & SEND --}}
                        <div id="inapp_step_2" class="d-none">
                            <div class="bg-light p-3 mb-4 border d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    <i class="fa fa-users me-2" style="color: #8fbd56;"></i>
                                    <span class="fw-semibold text-dark me-2">Selected Recipients:</span>
                                    <span id="inapp_step2_summary_text" class="text-dark fw-bold">All Registered Users</span>
                                </div>
                                <button type="button" id="btn_inapp_back_header" class="btn btn-link p-0 text-decoration-none small fw-semibold" style="color: #8fbd56;">
                                    <i class="fa fa-edit me-1"></i> Change
                                </button>
                            </div>

                            <div class="row g-4">
                                <div class="col-12 col-md-6">
                                    <label for="inapp_title" class="form-label fw-semibold text-dark">Notification Title <span class="text-danger">*</span></label>
                                    <input type="text" name="title" id="inapp_title" class="form-control" placeholder="e.g. Special Announcement" required>
                                </div>

                                <div class="col-12">
                                    <label for="inapp_body" class="form-label fw-semibold text-dark">Notification Message Body <span class="text-danger">*</span></label>
                                    <textarea name="body" id="inapp_body" rows="5" class="form-control" placeholder="Write the in-app notification content here..." required></textarea>
                                </div>

                                <div class="col-12 d-flex justify-content-between align-items-center pt-2">
                                    <button type="button" id="btn_inapp_back" class="btn btn-light border px-4 py-2 fw-semibold">
                                        <i class="fa fa-arrow-left me-1"></i> Back
                                    </button>
                                    <button type="submit" class="btn btn-primary px-4 py-2 fw-semibold">
                                        <i class="fa fa-paper-plane me-1"></i> Send In-App Notification
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>


                {{-- ==================================================================== --}}
                {{-- TAB 2: EMAIL NOTIFICATION --}}
                {{-- ==================================================================== --}}
                <div class="tab-pane fade" id="email_tab_content" role="tabpanel" aria-labelledby="email-main-tab">
                    
                    {{-- Recipient Type Selection Header Radios --}}
                    <div class="mb-4 pb-3 border-bottom">
                        <label class="form-label fw-bold text-dark mb-2">Recipient Mode</label>
                        <div class="d-flex gap-4">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="email_recipient_mode_toggle" id="email_type_registered" value="registered" checked>
                                <label class="form-check-label fw-semibold text-dark cursor-pointer" for="email_type_registered">
                                    Registered Users
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="email_recipient_mode_toggle" id="email_type_custom" value="custom">
                                <label class="form-check-label fw-semibold text-dark cursor-pointer" for="email_type_custom">
                                    Custom Email Address(es)
                                </label>
                            </div>
                        </div>
                    </div>

                    {{-- -------------------------------------------------------------------- --}}
                    {{-- OPTION 1: REGISTERED USERS FLOW (2-STEP) --}}
                    {{-- -------------------------------------------------------------------- --}}
                    <div id="email_registered_flow_wrapper">
                        {{-- Validation Alert for Registered Email --}}
                        <div id="email_validation_alert" class="alert alert-warning alert-dismissible fade show d-none mb-4" role="alert">
                            <i class="fa fa-exclamation-circle me-2"></i> <span id="email_validation_alert_text">Please select at least one recipient.</span>
                            <button type="button" class="btn-close" onclick="this.parentElement.classList.add('d-none')"></button>
                        </div>

                        <form action="{{ route('admin.notifications.send-email') }}" method="POST">
                            @csrf
                            <input type="hidden" name="recipient_type" value="registered">

                            {{-- REGISTERED EMAIL STEP 1: SELECT RECIPIENTS --}}
                            <div id="email_step_1">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <div>
                                        <h6 class="fw-bold text-dark mb-1">Select Registered Recipients</h6>
                                        <p class="small text-muted mb-0">Select users to receive the custom email broadcast.</p>
                                    </div>
                                    <span class="badge bg-light text-dark border px-3 py-2 fw-semibold" id="email_selected_count_badge">0 Users Selected</span>
                                </div>

                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <div class="input-group input-group-sm w-50">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="fa fa-search text-muted"></i>
                                        </span>
                                        <input type="text" id="email_user_search" class="form-control border-start-0" placeholder="Filter users by name or email...">
                                    </div>
                                    <span class="small text-muted">Showing {{ $users->count() }} accounts</span>
                                </div>

                                <div class="user-scroll-container mb-4">
                                    <table class="user-select-table">
                                        <thead>
                                            <tr>
                                                <th style="width: 50px; text-align: center;">
                                                    <input class="form-check-input" type="checkbox" id="email_select_all_cb" name="select_all_registered" value="1">
                                                </th>
                                                <th>User</th>
                                                <th>Email</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($users as $user)
                                                <tr class="email-user-item">
                                                    <td style="width: 50px; text-align: center;">
                                                        <input class="form-check-input email-user-checkbox" type="checkbox" name="user_ids[]" value="{{ $user->id }}" id="email_u_{{ $user->id }}">
                                                    </td>
                                                    <td>
                                                        <label class="d-flex align-items-center cursor-pointer mb-0 w-100" for="email_u_{{ $user->id }}">
                                                            @php
                                                                $emailAvatarUrl = !empty($user->avatar) 
                                                                    ? (filter_var($user->avatar, FILTER_VALIDATE_URL) ? $user->avatar : asset($user->avatar))
                                                                    : asset('default/profile.png');
                                                            @endphp
                                                            <img src="{{ $emailAvatarUrl }}" alt="{{ $user->name }}" class="me-2.5" style="width: 34px; height: 34px; object-fit: cover; border: 1px solid #e2e8f0; flex-shrink: 0;" onError="this.onerror=null;this.src='{{ asset('default/profile.png') }}';">
                                                            <span class="fw-semibold text-dark">{{ $user->name }}</span>
                                                        </label>
                                                    </td>
                                                    <td>
                                                        <span class="text-muted">{{ $user->email }}</span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <div class="d-flex justify-content-end pt-2">
                                    <button type="button" id="btn_email_next" class="btn btn-primary px-4 py-2 fw-semibold">
                                        Next: Compose Email <i class="fa fa-arrow-right ms-2"></i>
                                    </button>
                                </div>
                            </div>

                            {{-- REGISTERED EMAIL STEP 2: COMPOSE & SEND --}}
                            <div id="email_step_2" class="d-none">
                                <div class="bg-light p-3 mb-4 border d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center">
                                        <i class="fa fa-envelope me-2" style="color: #8fbd56;"></i>
                                        <span class="fw-semibold text-dark me-2">Selected Recipients:</span>
                                        <span id="email_step2_summary_text" class="text-dark fw-bold">All Registered Users</span>
                                    </div>
                                    <button type="button" id="btn_email_back_header" class="btn btn-link p-0 text-decoration-none small fw-semibold" style="color: #8fbd56;">
                                        <i class="fa fa-edit me-1"></i> Change
                                    </button>
                                </div>

                                <div class="row g-4">
                                    <div class="col-12">
                                        <label for="email_subject" class="form-label fw-semibold text-dark">Email Subject <span class="text-danger">*</span></label>
                                        <input type="text" name="subject" id="email_subject" class="form-control" placeholder="e.g. Important Account Announcement" required>
                                    </div>

                                    <div class="col-12">
                                        <x-form.quilleditor name="message" label="Email Message Content <span class='text-danger'>*</span>" placeholder="Write your email content here..." />
                                    </div>

                                    <div class="col-12 d-flex justify-content-between align-items-center pt-2">
                                        <button type="button" id="btn_email_back" class="btn btn-light border px-4 py-2 fw-semibold">
                                            <i class="fa fa-arrow-left me-1"></i> Back
                                        </button>
                                        <button type="submit" class="btn btn-primary px-4 py-2 fw-semibold">
                                            <i class="fa fa-envelope me-1"></i> Send Email Notification
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>


                    {{-- -------------------------------------------------------------------- --}}
                    {{-- OPTION 2: DIRECT CUSTOM EMAIL VIEW (1-STEP DIRECT INPUT & COMPOSE) --}}
                    {{-- -------------------------------------------------------------------- --}}
                    <div id="email_custom_flow_wrapper" class="d-none">
                        <form action="{{ route('admin.notifications.send-email') }}" method="POST">
                            @csrf
                            <input type="hidden" name="recipient_type" value="custom">

                            <div class="row g-4">
                                {{-- Custom Email Input --}}
                                <div class="col-12">
                                    <label for="custom_emails" class="form-label fw-semibold text-dark">Custom Email Address(es) <span class="text-danger">*</span></label>
                                    <textarea name="custom_emails" id="custom_emails" rows="3" class="form-control" placeholder="Enter emails separated by comma or new lines (e.g. user1@example.com, user2@example.com)" required></textarea>
                                    <small class="text-muted">Multiple email addresses can be separated by commas or line breaks.</small>
                                </div>

                                {{-- Subject Input --}}
                                <div class="col-12">
                                    <label for="custom_email_subject" class="form-label fw-semibold text-dark">Email Subject <span class="text-danger">*</span></label>
                                    <input type="text" name="subject" id="custom_email_subject" class="form-control" placeholder="e.g. Special Notification" required>
                                </div>

                                {{-- Quill Editor for Custom Email --}}
                                <div class="col-12">
                                    <x-form.quilleditor name="custom_message" label="Email Message Content <span class='text-danger'>*</span>" placeholder="Write your email content here..." />
                                </div>

                                <div class="col-12 d-flex justify-content-end pt-2">
                                    <button type="submit" class="btn btn-primary px-4 py-2 fw-semibold">
                                        <i class="fa fa-envelope me-1"></i> Send Custom Email Notification
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                </div>

            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            function refreshQuillEditors() {
                setTimeout(function () {
                    if (window.quill_editors) {
                        Object.values(window.quill_editors).forEach(q => {
                            if (q && typeof q.update === 'function') {
                                q.update();
                            }
                        });
                    }
                }, 50);
            }

            const emailMainTabBtn = document.getElementById('email-main-tab');
            if (emailMainTabBtn) {
                emailMainTabBtn.addEventListener('shown.bs.tab', refreshQuillEditors);
            }

            // =========================================================================
            // IN-APP TAB LOGIC
            // =========================================================================
            const inappStep1 = document.getElementById('inapp_step_1');
            const inappStep2 = document.getElementById('inapp_step_2');
            const inappTargetAll = document.getElementById('inapp_target_all');
            const inappTargetSelected = document.getElementById('inapp_target_selected');
            const inappSpecificWrapper = document.getElementById('inapp_specific_users_wrapper');
            const inappSelectAllCb = document.getElementById('inapp_select_all_cb');
            const inappUserCheckboxes = document.querySelectorAll('.inapp-user-checkbox');
            const inappUserSearch = document.getElementById('inapp_user_search');
            const btnInappNext = document.getElementById('btn_inapp_next');
            const btnInappBack = document.getElementById('btn_inapp_back');
            const btnInappBackHeader = document.getElementById('btn_inapp_back_header');
            const inappCountBadge = document.getElementById('inapp_selected_count_badge');
            const inappStep2Summary = document.getElementById('inapp_step2_summary_text');
            const inappValidationAlert = document.getElementById('inapp_validation_alert');
            const inappValidationAlertText = document.getElementById('inapp_validation_alert_text');

            function updateInappUI() {
                if (inappTargetAll.checked) {
                    inappSpecificWrapper.classList.add('d-none');
                } else {
                    inappSpecificWrapper.classList.remove('d-none');
                }
                updateInappBadge();
            }

            inappTargetAll.addEventListener('change', updateInappUI);
            inappTargetSelected.addEventListener('change', updateInappUI);

            if (inappSelectAllCb) {
                inappSelectAllCb.addEventListener('change', function () {
                    inappUserCheckboxes.forEach(cb => {
                        cb.checked = this.checked;
                        const tr = cb.closest('tr');
                        if (tr) tr.classList.toggle('selected', this.checked);
                    });
                    updateInappBadge();
                });
            }

            inappUserCheckboxes.forEach(cb => {
                cb.addEventListener('change', function () {
                    const tr = this.closest('tr');
                    if (tr) tr.classList.toggle('selected', this.checked);
                    updateInappBadge();
                });
            });

            if (inappUserSearch) {
                inappUserSearch.addEventListener('input', function () {
                    const q = this.value.toLowerCase();
                    document.querySelectorAll('.inapp-user-item').forEach(row => {
                        row.style.display = row.textContent.toLowerCase().includes(q) ? 'table-row' : 'none';
                    });
                });
            }

            function updateInappBadge() {
                if (inappTargetAll.checked) {
                    inappCountBadge.textContent = 'All Registered Users ({{ $users->count() }})';
                } else {
                    const count = document.querySelectorAll('.inapp-user-checkbox:checked').length;
                    inappCountBadge.textContent = count + ' Users Selected';
                }
            }

            btnInappNext.addEventListener('click', function () {
                inappValidationAlert.classList.add('d-none');

                if (inappTargetSelected.checked) {
                    const checkedCount = document.querySelectorAll('.inapp-user-checkbox:checked').length;
                    if (checkedCount === 0) {
                        inappValidationAlertText.textContent = 'Please select at least one user from the list or choose All Users.';
                        inappValidationAlert.classList.remove('d-none');
                        return;
                    }
                    inappStep2Summary.textContent = checkedCount + ' Selected Registered User(s)';
                } else {
                    inappStep2Summary.textContent = 'All Registered Users ({{ $users->count() }})';
                }

                inappStep1.classList.add('d-none');
                inappStep2.classList.remove('d-none');
            });

            function showInappStep1() {
                inappStep2.classList.add('d-none');
                inappStep1.classList.remove('d-none');
            }

            btnInappBack.addEventListener('click', showInappStep1);
            btnInappBackHeader.addEventListener('click', showInappStep1);


            // =========================================================================
            // EMAIL TAB LOGIC
            // =========================================================================
            const emailTypeRegistered = document.getElementById('email_type_registered');
            const emailTypeCustom = document.getElementById('email_type_custom');
            const emailRegisteredFlowWrapper = document.getElementById('email_registered_flow_wrapper');
            const emailCustomFlowWrapper = document.getElementById('email_custom_flow_wrapper');

            const emailStep1 = document.getElementById('email_step_1');
            const emailStep2 = document.getElementById('email_step_2');
            const emailSelectAllCb = document.getElementById('email_select_all_cb');
            const emailUserCheckboxes = document.querySelectorAll('.email-user-checkbox');
            const emailUserSearch = document.getElementById('email_user_search');
            const btnEmailNext = document.getElementById('btn_email_next');
            const btnEmailBack = document.getElementById('btn_email_back');
            const btnEmailBackHeader = document.getElementById('btn_email_back_header');
            const emailCountBadge = document.getElementById('email_selected_count_badge');
            const emailStep2Summary = document.getElementById('email_step2_summary_text');
            const emailValidationAlert = document.getElementById('email_validation_alert');
            const emailValidationAlertText = document.getElementById('email_validation_alert_text');

            function toggleEmailModeView() {
                if (emailTypeCustom.checked) {
                    emailCustomFlowWrapper.classList.remove('d-none');
                    emailRegisteredFlowWrapper.classList.add('d-none');
                } else {
                    emailRegisteredFlowWrapper.classList.remove('d-none');
                    emailCustomFlowWrapper.classList.add('d-none');
                }
                refreshQuillEditors();
            }

            emailTypeRegistered.addEventListener('change', toggleEmailModeView);
            emailTypeCustom.addEventListener('change', toggleEmailModeView);

            if (emailSelectAllCb) {
                emailSelectAllCb.addEventListener('change', function () {
                    emailUserCheckboxes.forEach(cb => {
                        cb.checked = this.checked;
                        const tr = cb.closest('tr');
                        if (tr) tr.classList.toggle('selected', this.checked);
                    });
                    updateEmailBadge();
                });
            }

            emailUserCheckboxes.forEach(cb => {
                cb.addEventListener('change', function () {
                    const tr = this.closest('tr');
                    if (tr) tr.classList.toggle('selected', this.checked);
                    updateEmailBadge();
                });
            });

            if (emailUserSearch) {
                emailUserSearch.addEventListener('input', function () {
                    const q = this.value.toLowerCase();
                    document.querySelectorAll('.email-user-item').forEach(row => {
                        row.style.display = row.textContent.toLowerCase().includes(q) ? 'table-row' : 'none';
                    });
                });
            }

            function updateEmailBadge() {
                if (emailSelectAllCb.checked) {
                    emailCountBadge.textContent = 'All Registered Users ({{ $users->count() }})';
                } else {
                    const count = document.querySelectorAll('.email-user-checkbox:checked').length;
                    emailCountBadge.textContent = count + ' Users Selected';
                }
            }

            btnEmailNext.addEventListener('click', function () {
                emailValidationAlert.classList.add('d-none');

                const checkedCount = document.querySelectorAll('.email-user-checkbox:checked').length;
                if (!emailSelectAllCb.checked && checkedCount === 0) {
                    emailValidationAlertText.textContent = 'Please select at least one user from the list or check "Select All".';
                    emailValidationAlert.classList.remove('d-none');
                    return;
                }
                emailStep2Summary.textContent = emailSelectAllCb.checked 
                    ? 'All Registered Users ({{ $users->count() }})' 
                    : checkedCount + ' Selected User(s)';

                emailStep1.classList.add('d-none');
                emailStep2.classList.remove('d-none');
                refreshQuillEditors();
            });

            function showEmailStep1() {
                emailStep2.classList.add('d-none');
                emailStep1.classList.remove('d-none');
            }

            btnEmailBack.addEventListener('click', showEmailStep1);
            btnEmailBackHeader.addEventListener('click', showEmailStep1);

        });
    </script>
    @endpush
</x-admin-layout>
