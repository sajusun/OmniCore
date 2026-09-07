<x-admin-layout>
    @slot('title')
        AI Assistant & Knowledge Base
    @endslot

    <div class="container-fluid py-4">
        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1"><i class="bi bi-robot me-2 text-primary"></i> AI Assistant & Automated Helpdesk Knowledge Base</h4>
                <p class="text-muted small mb-0">Manage training FAQ datasets, keyword triggers, automated ticket responses, and conversational AI analytics.</p>
            </div>
        </div>

        {{-- Status Notification Modal --}}
        <x-modal.status />

        {{-- Stats Row --}}
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <x-stat-card 
                    title="Total AI Conversations" 
                    value="{{ number_format($totalConversations) }}" 
                    color="primary" 
                    icon="<i class='bi bi-chat-dots fs-3'></i>" 
                />
            </div>
            <div class="col-md-3">
                <x-stat-card 
                    title="Messages Handled" 
                    value="{{ number_format($totalMessages) }}" 
                    color="indigo" 
                    icon="<i class='bi bi-send-check fs-3'></i>" 
                />
            </div>
            <div class="col-md-3">
                <x-stat-card 
                    title="Knowledge FAQs" 
                    value="{{ number_format($totalFaqs) }}" 
                    color="emerald" 
                    icon="<i class='bi bi-book fs-3'></i>" 
                />
            </div>
            <div class="col-md-3">
                <x-stat-card 
                    title="Tokens Processed" 
                    value="{{ number_format($totalTokens) }}" 
                    color="purple" 
                    icon="<i class='bi bi-cpu fs-3'></i>" 
                />
            </div>
        </div>

        {{-- Add Knowledge Base Form --}}
        <x-card title="Add New Knowledge Base Entry / FAQ" class="mb-4">
            <form action="{{ route('admin.ai.knowledge-base.store') }}" method="POST">
                @csrf
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Category</label>
                        <input type="text" name="category" required placeholder="e.g. Orders, Refunds, Shipping" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Keywords (Comma separated)</label>
                        <input type="text" name="keywords" placeholder="e.g. return, refund, damaged item" class="form-control">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Question / Trigger Prompt</label>
                    <input type="text" name="question" required placeholder="e.g. How do I request a refund?" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Standard AI Answer / Resolution</label>
                    <textarea name="answer" rows="3" required placeholder="Provide the verified official response..." class="form-control"></textarea>
                </div>

                <div class="d-flex justify-content-between align-items-center pt-2">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="kbActive" checked>
                        <label class="form-check-label fw-semibold" for="kbActive">Active & Searchable</label>
                    </div>
                    <button type="submit" class="btn btn-primary px-4 shadow-sm">
                        <i class="bi bi-plus-circle me-1"></i> Save Knowledge Entry
                    </button>
                </div>
            </form>
        </x-card>

        {{-- Knowledge Base Table --}}
        <x-card title="Knowledge Base Directory">
            <x-table>
                <thead>
                    <tr>
                        <x-table.th>Category</x-table.th>
                        <x-table.th>Question / Trigger</x-table.th>
                        <x-table.th>Keywords</x-table.th>
                        <x-table.th>Hit Count</x-table.th>
                        <x-table.th>Status</x-table.th>
                        <x-table.th class="text-end">Action</x-table.th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($faqs as $faq)
                        <tr>
                            <x-table.td>
                                <span class="badge bg-light text-dark border">{{ $faq->category }}</span>
                            </x-table.td>
                            <x-table.td>
                                <div class="fw-bold text-dark">{{ $faq->question }}</div>
                                <div class="text-muted small text-truncate" style="max-width: 320px;">{{ $faq->answer }}</div>
                            </x-table.td>
                            <x-table.td>
                                @if(!empty($faq->keywords))
                                    @foreach((array)$faq->keywords as $kw)
                                        <span class="badge bg-light text-muted border small">{{ $kw }}</span>
                                    @endforeach
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </x-table.td>
                            <x-table.td class="text-muted small">
                                <span class="fw-bold text-primary">{{ number_format($faq->hit_count ?? 0) }}</span> hits
                            </x-table.td>
                            <x-table.td>
                                @if($faq->is_active)
                                    <x-badge color="success">Active</x-badge>
                                @else
                                    <x-badge color="secondary">Inactive</x-badge>
                                @endif
                            </x-table.td>
                            <x-table.td class="text-end">
                                <form action="{{ route('admin.ai.knowledge-base.destroy', $faq->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this FAQ entry?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </x-table.td>
                        </tr>
                    @empty
                        <x-empty-state colspan="6" title="No Knowledge Base Entries" description="Add your first question-answer knowledge pair above." />
                    @endforelse
                </tbody>
            </x-table>

            <div class="mt-3">
                {{ $faqs->links() }}
            </div>
        </x-card>
    </div>
</x-admin-layout>
