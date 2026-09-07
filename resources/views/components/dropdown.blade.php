@props(['align' => 'right', 'width' => 'auto', 'contentClasses' => ''])

@php
$alignmentClasses = match ($align) {
    'left' => 'start-0',
    'top' => 'bottom-100 mb-2',
    default => 'end-0',
};

$widthClass = match ($width) {
    '48' => 'style="min-width: 12rem;"',
    'auto' => '',
    default => 'style="width: ' . $width . ';"',
};
@endphp

<div class="dropdown d-inline-block position-relative" x-data="{ open: false }" @click.outside="open = false" @close.stop="open = false">
    <div @click="open = ! open" class="cursor-pointer">
        {{ $trigger }}
    </div>

    <div x-show="open"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 transform scale-95"
         x-transition:enter-end="opacity-100 transform scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="opacity-100 transform scale-100"
         x-transition:leave-end="opacity-0 transform scale-95"
         class="dropdown-menu show position-absolute z-index-dropdown mt-2 p-0 {{ $alignmentClasses }}"
         {!! $widthClass !!}
         style="display: none;"
         @click="open = false">
        <div class="shadow border py-1 bg-white {{ $contentClasses }}">
            {{ $content }}
        </div>
    </div>
</div>
