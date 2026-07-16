<div class="table-responsive rounded shadow-sm border">
    <table {{ $attributes->merge(['class' => 'table table-hover table-striped mb-0 align-middle']) }}>
        <thead class="table-dark">
            <tr>
                {{ $thead }}
            </tr>
        </thead>
        <tbody>
            {{ $slot }}
        </tbody>
    </table>
</div>
