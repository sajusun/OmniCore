<style>
    .textTransform {
        animation: colorChange 40s infinite;
    }

    @keyframes colorChange {
        0% {
            color: #ff0000;
        }

        25% {
            color: #00ff00;
        }

        50% {
            color: #0000ff;
        }

        75% {
            color: #ff00ff;
        }

        100% {
            color: #ff0000;
        }
    }

    /* Global Button Hover & Contrast Fixes using Client's Primary Color (#8fbd56) */
    .btn-primary, button.btn-primary, a.btn-primary {
        background-color: var(--primary-bg-color, #8fbd56) !important;
        border-color: var(--primary-bg-color, #8fbd56) !important;
        color: #ffffff !important;
    }
    .btn-primary:hover, .btn-primary:focus, .btn-primary:active,
    button.btn-primary:hover, button.btn-primary:focus, button.btn-primary:active,
    a.btn-primary:hover, a.btn-primary:focus, a.btn-primary:active {
        background-color: var(--primary-bg-hover, #7cb342) !important;
        border-color: var(--primary-bg-hover, #7cb342) !important;
        color: #ffffff !important;
    }

    .btn-secondary, button.btn-secondary, a.btn-secondary {
        background-color: #6b7280 !important;
        border-color: #6b7280 !important;
        color: #ffffff !important;
    }
    .btn-secondary:hover, .btn-secondary:focus, .btn-secondary:active,
    button.btn-secondary:hover, button.btn-secondary:focus, button.btn-secondary:active,
    a.btn-secondary:hover, a.btn-secondary:focus, a.btn-secondary:active {
        background-color: #4b5563 !important;
        border-color: #4b5563 !important;
        color: #ffffff !important;
    }

    .btn-success, button.btn-success, a.btn-success {
        background-color: #10b981 !important;
        border-color: #10b981 !important;
        color: #ffffff !important;
    }
    .btn-success:hover, .btn-success:focus, .btn-success:active,
    button.btn-success:hover, button.btn-success:focus, button.btn-success:active,
    a.btn-success:hover, a.btn-success:focus, a.btn-success:active {
        background-color: #059669 !important;
        border-color: #059669 !important;
        color: #ffffff !important;
    }

    .btn-danger, button.btn-danger, a.btn-danger {
        background-color: #ef4444 !important;
        border-color: #ef4444 !important;
        color: #ffffff !important;
    }
    .btn-danger:hover, .btn-danger:focus, .btn-danger:active,
    button.btn-danger:hover, button.btn-danger:focus, button.btn-danger:active,
    a.btn-danger:hover, a.btn-danger:focus, a.btn-danger:active {
        background-color: #dc2626 !important;
        border-color: #dc2626 !important;
        color: #ffffff !important;
    }

    .btn-info, button.btn-info, a.btn-info {
        background-color: #06b6d4 !important;
        border-color: #06b6d4 !important;
        color: #ffffff !important;
    }
    .btn-info:hover, .btn-info:focus, .btn-info:active,
    button.btn-info:hover, button.btn-info:focus, button.btn-info:active,
    a.btn-info:hover, a.btn-info:focus, a.btn-info:active {
        background-color: #0891b2 !important;
        border-color: #0891b2 !important;
        color: #ffffff !important;
    }

    .btn-light, button.btn-light, a.btn-light {
        background-color: #f3f4f6 !important;
        border-color: #d1d5db !important;
        color: #374151 !important;
    }
    .btn-light:hover, .btn-light:focus, .btn-light:active,
    button.btn-light:hover, button.btn-light:focus, button.btn-light:active,
    a.btn-light:hover, a.btn-light:focus, a.btn-light:active {
        background-color: #e5e7eb !important;
        border-color: #9ca3af !important;
        color: #111827 !important;
    }

    .btn-outline-secondary, button.btn-outline-secondary, a.btn-outline-secondary {
        border-color: #d1d5db !important;
        color: #374151 !important;
        background-color: transparent !important;
    }
    .btn-outline-secondary:hover, .btn-outline-secondary:focus, .btn-outline-secondary:active,
    button.btn-outline-secondary:hover, button.btn-outline-secondary:focus, button.btn-outline-secondary:active,
    a.btn-outline-secondary:hover, a.btn-outline-secondary:focus, a.btn-outline-secondary:active {
        background-color: #f3f4f6 !important;
        border-color: #9ca3af !important;
        color: #111827 !important;
    }

    /* Ensure icons inside buttons inherit text color */
    .btn i, .btn svg {
        color: inherit !important;
    }
</style>