<button
    type="submit"
    {{ $attributes->merge(['class' => 'btn btn-primary']) }}
>
    {{ $slot ?? 'Submit' }}
</button>
