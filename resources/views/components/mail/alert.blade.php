@props([
    'type' => 'info', {{-- info | success | warning | danger --}}
])

@php
$map = [
    'info'    => ['bg' => '#eff6ff', 'border' => '#3b82f6', 'text' => '#1e40af'],
    'success' => ['bg' => '#f0fdf4', 'border' => '#22c55e', 'text' => '#166534'],
    'warning' => ['bg' => '#fffbeb', 'border' => '#f59e0b', 'text' => '#92400e'],
    'danger'  => ['bg' => '#fef2f2', 'border' => '#ef4444', 'text' => '#991b1b'],
];
$s = $map[$type] ?? $map['info'];
@endphp

{{--
    CONTENT-LEVEL COMPONENT: renders a colored left-border alert box.
    Use inside <x-bootstrap-mail.body>.
    The slot can contain raw HTML (<strong>, <a>, etc.).
--}}
<table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%"
       style="margin-bottom: 24px;">
    <tr>
        <td style="background-color: {{ $s['bg'] }};
                   border-left: 4px solid {{ $s['border'] }};
                   border-radius: 0 8px 8px 0;
                   padding: 14px 18px;">
            <p style="margin: 0;
                      color: {{ $s['text'] }};
                      font-family: Arial, Helvetica, sans-serif;
                      font-size: 13px;
                      line-height: 1.6;
                      mso-line-height-rule: exactly;">
                {{ $slot }}
            </p>
        </td>
    </tr>
</table>
