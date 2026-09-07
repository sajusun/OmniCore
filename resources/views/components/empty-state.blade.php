@props([
    'icon' => 'bi bi-inbox',
    'title' => 'No Records Found',
    'description' => 'There are no items matching the criteria or added yet.',
    'colspan' => null,
])

@if($colspan)
    <tr>
        <td colspan="{{ $colspan }}" class="text-center py-5">
            <div class="py-4">
                <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle bg-light text-muted" style="width: 56px; height: 56px;">
                    <i class="{{ $icon }} fs-3"></i>
                </div>
                <h6 class="fw-bold text-dark mb-1">{{ $title }}</h6>
                <p class="text-muted small mb-0">{{ $description }}</p>
                @if(isset($action))
                    <div class="mt-3">
                        {{ $action }}
                    </div>
                @endif
            </div>
        </td>
    </tr>
@else
    <div {{ $attributes->merge(['class' => 'text-center py-5']) }}>
        <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle bg-light text-muted" style="width: 56px; height: 56px;">
            <i class="{{ $icon }} fs-3"></i>
        </div>
        <h6 class="fw-bold text-dark mb-1">{{ $title }}</h6>
        <p class="text-muted small mb-0">{{ $description }}</p>
        @if(isset($action))
            <div class="mt-3">
                {{ $action }}
            </div>
        @endif
    </div>
@endif
