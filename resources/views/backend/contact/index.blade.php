<x-admin-layout>
    <x-slot name="title">Contact Us Messages</x-slot>

    <div class="container-fluid py-4">
        <x-page-header title="Contact Us Messages"
            subtitle="Total: {{ $total }} messages ({{ $unread }} unread)"
            :breadcrumbs="['Dashboard' => route('admin.dashboard'), 'Contact' => null, 'Messages' => null]">
            <x-slot:actions>
                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-3 py-2">
                    <i class="fas fa-envelope me-1"></i> {{ $unread }} Unread
                </span>
            </x-slot:actions>
        </x-page-header>

        <x-datatable id="contact-datatable" url="{{ route('contact.me') }}" :order="[[5, 'desc']]" :columns="[
                ['data' => 'DT_RowIndex', 'name' => 'DT_RowIndex', 'title' => '#', 'orderable' => false, 'searchable' => false],
                ['data' => 'subject', 'name' => 'subject', 'title' => 'Subject'],
                ['data' => 'email', 'name' => 'email', 'title' => 'Email'],
                ['data' => 'phone', 'name' => 'phone', 'title' => 'Phone'],
                ['data' => 'is_read', 'name' => 'is_read', 'title' => 'Status', 'searchable' => false],
                ['data' => 'created_at', 'name' => 'created_at', 'title' => 'Sent At', 'searchable' => false],
                ['data' => 'action', 'name' => 'action', 'title' => 'Action', 'orderable' => false, 'searchable' => false],
            ]" />
    </div>

    <x-modal.confirm-delete name="confirm-contact-delete" action="#" title="Delete Message"
        message="Are you sure you want to delete this message? This action cannot be undone." />

    @push('scripts')
    <script>
        function deleteContact(id) {
            const form = document.getElementById('confirm-delete-form-confirm-contact-delete');
            form.action = "{{ route('contact.me.delete', ':id') }}".replace(':id', id);
            new bootstrap.Modal(document.getElementById('modal_confirm-contact-delete')).show();
        }
    </script>
    @endpush
</x-admin-layout>