{{--
    Shared Role form (used by create & edit)
    @param \Spatie\Permission\Models\Role|null $role
    @param \Illuminate\Support\Collection      $permissions
--}}
@php
    $role = $role ?? null;
    $assigned = old('permissions', $role ? $role->permissions->pluck('name')->toArray() : []);

    // Group by module prefix: "user.create" => "User"
    $grouped = $permissions->sortBy('name')->groupBy(function ($p) {
        $parts = preg_split('/[.\-]/', $p->name);
        return count($parts) > 1 ? ucfirst($parts[0]) : 'General';
    });
@endphp

<form action="{{ $role ? route('admin.roles.update', $role->id) : route('admin.roles.store') }}" method="POST">
    @csrf
    @if($role) @method('PUT') @endif

    {{-- Role Name --}}
    <x-form.text name="name" label="Role Name" placeholder="e.g. Manager, Editor, Moderator"
        :value="old('name', $role->name ?? '')" :readonly="(bool) $role" />

    {{-- Permissions Toolbar --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3 pb-2 border-bottom">
        <div class="d-flex align-items-center gap-2">
            <span class="fw-semibold">Permissions</span>
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle" id="perm-selected-count">
                0 / {{ $permissions->count() }}
            </span>
        </div>
        <div class="d-flex align-items-center gap-3">
            <div class="position-relative">
                <i class="fas fa-search position-absolute top-50 translate-middle-y text-muted" style="left: .65rem; font-size: .75rem;"></i>
                <input type="text" id="perm-search" class="form-control form-control-sm" placeholder="Filter permissions..." style="padding-left: 1.9rem; width: 200px;">
            </div>
            <div class="form-check form-switch mb-0">
                <input class="form-check-input" type="checkbox" id="selectAll">
                <label class="form-check-label small text-muted" for="selectAll">Select All</label>
            </div>
        </div>
    </div>

    {{-- Permission Groups --}}
    @if($permissions->count())
        <div class="row g-3">
            @foreach($grouped as $group => $perms)
                <div class="col-12 col-lg-6 col-xxl-4 perm-group">
                    <div class="perm-group-card h-100">
                        <div class="perm-group-head d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-2">
                                <span class="fw-semibold text-uppercase" style="font-size: .75rem; letter-spacing: .05em;">{{ $group }}</span>
                                <span class="text-muted" style="font-size: .75rem;">({{ $perms->count() }})</span>
                            </div>
                            <div class="form-check mb-0">
                                <input class="form-check-input group-toggle" type="checkbox" title="Select all in {{ $group }}">
                            </div>
                        </div>
                        <div class="p-2">
                            @foreach($perms as $permission)
                                @php $checked = in_array($permission->name, $assigned); @endphp
                                <label for="perm-{{ $permission->id }}" class="perm-item d-flex align-items-center gap-2 {{ $checked ? 'is-checked' : '' }}"
                                    data-search="{{ strtolower($permission->name . ' ' . $permission->display_name) }}">
                                    <input class="form-check-input permission-checkbox m-0 flex-shrink-0" type="checkbox"
                                        name="permissions[]" value="{{ $permission->name }}" id="perm-{{ $permission->id }}" @checked($checked)>
                                    <span class="d-flex flex-column lh-sm overflow-hidden">
                                        <span class="text-truncate" style="font-size: .8125rem;">{{ $permission->display_name ?: $permission->name }}</span>
                                        @if($permission->display_name)
                                            <code class="text-muted text-truncate" style="font-size: .7rem;">{{ $permission->name }}</code>
                                        @endif
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-5 border border-dashed rounded-2">
            <i class="fas fa-key fa-2x text-muted mb-2"></i>
            <p class="small text-muted mb-0">No permissions found. Create permissions first.</p>
        </div>
    @endif

    {{-- Footer Actions --}}
    <div class="d-flex justify-content-end align-items-center gap-2 pt-3 mt-4 border-top">
        <a href="{{ route('admin.roles.index') }}" class="btn btn-light border px-4">Cancel</a>
        <button type="submit" class="btn btn-primary px-4 d-inline-flex align-items-center gap-2">
            <i class="fas fa-check"></i>
            <span>{{ $role ? 'Update Role' : 'Create Role' }}</span>
        </button>
    </div>
</form>

@push('styles')
<style>
    .perm-group-card {
        border: 1px solid var(--theme-card-border, #e2e8f0);
        border-radius: 8px;
        overflow: hidden;
    }
    .perm-group-head {
        padding: .55rem .85rem;
        background-color: var(--theme-table-header-bg, #f8fafc);
        border-bottom: 1px solid var(--theme-card-border, #e2e8f0);
    }
    .perm-item {
        padding: .4rem .6rem;
        border-radius: 6px;
        cursor: pointer;
        transition: background-color .15s ease;
    }
    .perm-item:hover { background-color: rgba(99, 102, 241, .06); }
    .perm-item.is-checked { background-color: rgba(99, 102, 241, .1); }
</style>
@endpush

@push('scripts')
<script>
(function () {
    const selectAll = document.getElementById('selectAll');
    const boxes = [...document.querySelectorAll('.permission-checkbox')];
    const counter = document.getElementById('perm-selected-count');
    const search = document.getElementById('perm-search');

    function sync() {
        boxes.forEach(cb => cb.closest('.perm-item').classList.toggle('is-checked', cb.checked));
        document.querySelectorAll('.perm-group').forEach(group => {
            const items = [...group.querySelectorAll('.permission-checkbox')];
            group.querySelector('.group-toggle').checked = items.length && items.every(c => c.checked);
        });
        const checked = boxes.filter(c => c.checked).length;
        if (counter) counter.textContent = `${checked} / ${boxes.length}`;
        if (selectAll) selectAll.checked = boxes.length && checked === boxes.length;
    }

    selectAll?.addEventListener('change', function () {
        boxes.forEach(cb => cb.checked = this.checked);
        sync();
    });

    document.querySelectorAll('.group-toggle').forEach(toggle => {
        toggle.addEventListener('change', function () {
            this.closest('.perm-group').querySelectorAll('.permission-checkbox').forEach(cb => cb.checked = this.checked);
            sync();
        });
    });

    boxes.forEach(cb => cb.addEventListener('change', sync));

    search?.addEventListener('input', function () {
        const q = this.value.toLowerCase().trim();
        document.querySelectorAll('.perm-group').forEach(group => {
            let visible = 0;
            group.querySelectorAll('.perm-item').forEach(item => {
                const match = !q || item.dataset.search.includes(q);
                item.classList.toggle('d-none', !match);
                item.classList.toggle('d-flex', match);
                if (match) visible++;
            });
            group.classList.toggle('d-none', visible === 0);
        });
    });

    sync();
})();
</script>
@endpush
