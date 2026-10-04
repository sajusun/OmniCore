<x-admin-layout>
    <x-slot name="title">Send Bulk Notification</x-slot>

    <div class="container-fluid py-4">
        <x-page-header
            title="Send Bulk Notification"
            subtitle="Broadcast notification message to all registered users across the system."
            :breadcrumbs="['Dashboard' => route('admin.dashboard'), 'Notifications' => route('admin.notifications.index'), 'Send Bulk' => null]">
            <x-slot:actions>
                <a href="{{ route('admin.notifications.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1.5 px-3 py-2" style="font-size: 0.8125rem; border-radius: 8px;">
                    <i class="fa fa-arrow-left"></i>
                    <span>Back to Notifications</span>
                </a>
            </x-slot:actions>
        </x-page-header>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                <i class="fa fa-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row justify-content-center">
            <div class="col-12 col-xl-8">
                <form action="{{ route('admin.notifications.store') }}" method="POST">
                    @csrf
                    <x-card title="Notification Content" class="mb-4">
                        <x-slot:icon>
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                            </svg>
                        </x-slot:icon>

                        <div class="mb-3">
                            <label for="subject" class="form-label fw-medium small">Subject <span class="text-danger">*</span></label>
                            <input type="text" name="subject" id="subject" class="form-control" required placeholder="e.g. Scheduled System Maintenance Notice">
                        </div>

                        <div class="mb-4">
                            <label for="message" class="form-label fw-medium small">Message Content <span class="text-danger">*</span></label>
                            <textarea name="message" id="message" rows="5" class="form-control" required placeholder="Write your announcement details here..."></textarea>
                        </div>

                        <div class="d-flex align-items-center justify-content-end gap-2">
                            <a href="{{ route('admin.notifications.index') }}" class="btn btn-light px-4 py-2 fw-semibold" style="font-size: 0.8125rem; border-radius: 6px;">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary d-inline-flex align-items-center gap-2 px-4 py-2 fw-semibold shadow-sm" style="font-size: 0.8125rem; border-radius: 6px;">
                                <i class="fa fa-paper-plane"></i>
                                <span>Broadcast Notification</span>
                            </button>
                        </div>
                    </x-card>
                </form>
            </div>
        </div>
    </div>
</x-admin-layout>
