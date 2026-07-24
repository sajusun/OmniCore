<x-guest-layout>
    <p class="text-muted small mb-4">
        This is a secure area of the application. Please confirm your password before continuing
    </p>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <x-form.password
            name="password"
            label="{{ __('Password') }}"
            placeholder="••••••••"
            autocomplete="current-password"
        />

        <div class="d-flex justify-content-end mt-3">
            <x-form.submit class="btn btn-primary">Confirm</x-form.submit>
        </div>
    </form>
</x-guest-layout>
