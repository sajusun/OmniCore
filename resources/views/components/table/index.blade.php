@props(['thead' => null])

<div class="table-responsive shadow-sm border">
    <table {{ $attributes->merge(['class' => 'table table-hover table-striped mb-0 align-middle']) }}>
        @if(isset($thead) && !empty(trim((string) $thead)))
            <thead class="table-dark">
                <tr>
                    {{ $thead }}
                </tr>
            </thead>
            <tbody>
                {{ $slot }}
            </tbody>
        @else
            {{ $slot }}
        @endif
    </table>
</div>
