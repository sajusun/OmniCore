@props([
    'variant' => 'body', {{-- body | small | muted | heading --}}
])

@php
$styles = [
    'body'    => 'margin: 0 0 20px; color: #212529; font-family: Arial, Helvetica, sans-serif; font-size: 15px; line-height: 1.7; mso-line-height-rule: exactly;',
    'small'   => 'margin: 0 0 16px; color: #495057; font-family: Arial, Helvetica, sans-serif; font-size: 13px; line-height: 1.6; mso-line-height-rule: exactly;',
    'muted'   => 'margin: 0 0 12px; color: #6c757d; font-family: Arial, Helvetica, sans-serif; font-size: 12px; line-height: 1.6; mso-line-height-rule: exactly;',
    'heading' => 'margin: 0 0 16px; color: #212529; font-family: Arial, Helvetica, sans-serif; font-size: 18px; font-weight: 700; line-height: 1.4; mso-line-height-rule: exactly;',
];
$style = $styles[$variant] ?? $styles['body'];
@endphp

<p style="{{ $style }}">{{ $slot }}</p>
