@props([
'type', // Acceptable values: 'view', 'edit', 'delete'
'href' => null, // Optional URL string
'onclick' => null, // Optional JavaScript function string
'title' => null // Optional HTML tooltip title override
])

@php
// Mapping config parameters for premium tailwind-like theme layers
$configs = [
'view' => [
'class' => 'text-info bg-info-subtle border-info-subtle',
'hover_bg' => 'var(--bs-info)',
'icon' => 'fa-solid fa-eye',
'default_title' => 'View Details'
],
'edit' => [
'class' => 'text-primary bg-primary-subtle border-primary-subtle',
'hover_bg' => 'var(--bs-primary)',
'icon' => 'fa-solid fa-pen-to-square',
'default_title' => 'Edit Item'
],
'delete' => [
'class' => 'text-danger bg-danger-subtle border-danger-subtle',
'hover_bg' => 'var(--bs-danger)',
'icon' => 'fa-solid fa-trash-can',
'default_title' => 'Delete Item'
]
][$type] ?? $configs['view'];

// Determine absolute tag interface execution element type
$isLink = !empty($href);
$tag = $isLink ? 'a' : 'button';
@endphp

<{{ $tag }} {{ $isLink ? "href={$href}" : "type=button" }} @if(!empty($onclick)) onclick="{{ $onclick }}" @endif
    title="{{ $title ?? $configs['default_title'] }}"
    class="btn btn-sm d-inline-flex align-items-center justify-content-center p-0 border rounded-0 {{ $configs['class'] }}"
    style="width: 32px; height: 32px; transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1); vertical-align: middle;"
    onmouseover="this.style.backgroundColor='{{ $configs['hover_bg'] }}'; this.style.setProperty('color', '#ffffff', 'important');"
    onmouseout="this.style.backgroundColor=''; this.style.setProperty('color', '', '');" {{ $attributes }}>
    <i class="{{ $configs['icon'] }}" style="font-size: 0.85rem;"></i>
</{{ $tag }}>




{{-- 1st using system
->addColumn('action', function ($row) {
    return '
        <div class="d-flex align-items-center gap-1">
            '.view('components.table-action', ['type' => 'view', 'href' => route('admin.users.show', $row->id)])->render().'
            '.view('components.table-action', ['type' => 'edit', 'href' => route('admin.users.edit', $row->id)])->render().'
            '.view('components.table-action', ['type' => 'delete', 'onclick' => "deleteRole({$row->id})"])->render().'
        </div>
    ';
})
2nd using system
<div class="d-flex align-items-center gap-1">
    <x-table-action type="view" :href="route('admin.roles.show', $row->id)" />
    <x-table-action type="edit" :href="route('admin.roles.edit', $row->id)" />
    <x-table-action type="delete" onclick="confirmDeleteUser({{ $row->id }}, '{{ $row->name }}')" />
</div> --}}