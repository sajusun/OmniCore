<x-admin-layout>
    <x-slot name="title">Contact Message Details</x-slot>

    <div class="container-fluid py-4">
        <x-page-header title="Message Details" subtitle="{{ $contactUs->subject }}"
            :breadcrumbs="['Dashboard' => route('admin.dashboard'), 'Contact Messages' => route('contact.me'), 'Details' => null]">
            <x-slot:actions>
                <a href="{{ route('contact.me') }}" class="btn btn-light border d-inline-flex align-items-center gap-2 px-3">
                    <i class="fas fa-arrow-left"></i><span>Back</span>
                </a>
                <button type="button" class="btn btn-primary d-inline-flex align-items-center gap-2 px-3" data-bs-toggle="modal" data-bs-target="#replyModal">
                    <i class="fas fa-reply"></i><span>Reply via Email</span>
                </button>
            </x-slot:actions>
        </x-page-header>

        <div class="row g-4">
            {{-- Message --}}
            <div class="col-lg-8">
                <x-card title="{{ $contactUs->subject }}" subtitle="Received {{ $contactUs->created_at?->diffForHumans() }}" class="h-100">
                    <x-slot:icon><i class="fas fa-envelope-open-text"></i></x-slot:icon>
                    <div class="lh-lg" style="white-space: pre-line; color: var(--theme-body-color, #334155);">{{ $contactUs->message }}</div>
                </x-card>
            </div>

            {{-- Sender Info --}}
            <div class="col-lg-4">
                <x-card title="Sender Information" class="h-100">
                    <x-slot:icon><i class="fas fa-user"></i></x-slot:icon>
                    <dl class="mb-0 contact-meta">
                        <dt>Name</dt>
                        <dd>{{ $contactUs->name ?: 'N/A' }}</dd>

                        <dt>Email</dt>
                        <dd><a href="mailto:{{ $contactUs->email }}" class="text-decoration-none">{{ $contactUs->email }}</a></dd>

                        <dt>Phone</dt>
                        <dd>{{ $contactUs->phone ?: 'N/A' }}</dd>

                        <dt>Sent At</dt>
                        <dd>{{ $contactUs->created_at?->format('d M, Y h:i A') ?? 'N/A' }}</dd>

                        <dt>Read At</dt>
                        <dd class="mb-0">{{ $contactUs->read_at?->format('d M, Y h:i A') ?? 'Not read yet' }}</dd>
                    </dl>
                </x-card>
            </div>
        </div>
    </div>

    {{-- Reply Email Modal --}}
    <div class="modal fade" id="replyModal" tabindex="-1" aria-labelledby="replyModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <form action="{{ route('contact-us.reply', $contactUs->id) }}" method="POST" class="modal-content border-0 shadow">
                @csrf
                <div class="modal-header px-4">
                    <h5 class="modal-title fw-bold d-flex align-items-center gap-2" id="replyModalLabel">
                        <i class="fas fa-reply text-primary"></i> Reply to {{ $contactUs->email }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-medium">To</label>
                        <input type="email" class="form-control" value="{{ $contactUs->email }}" disabled>
                    </div>
                    <div class="mb-3">
                        <label for="reply-subject" class="form-label fw-medium">Subject</label>
                        <input type="text" id="reply-subject" name="subject" class="form-control" value="Re: {{ $contactUs->subject }}" required>
                    </div>
                    <div class="mb-0">
                        <label for="reply-message" class="form-label fw-medium">Message</label>
                        <textarea id="reply-message" name="message" rows="7" class="form-control" required>Hello {{ $contactUs->name }},

</textarea>
                    </div>
                </div>
                <div class="modal-footer px-4">
                    <button type="button" class="btn btn-light border px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4 d-inline-flex align-items-center gap-2">
                        <i class="fas fa-paper-plane"></i><span>Send Email</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('styles')
    <style>
        .contact-meta dt {
            font-size: .75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .04em;
            color: var(--theme-muted-color, #64748b);
            margin-bottom: .15rem;
        }
        .contact-meta dd {
            font-size: .875rem;
            margin-bottom: 1rem;
            word-break: break-word;
        }
    </style>
    @endpush
</x-admin-layout>