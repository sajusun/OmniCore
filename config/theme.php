<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Active Dashboard Theme
    |--------------------------------------------------------------------------
    |
    | Supported themes:
    | - "modern_indigo"   : Vibrant SaaS Indigo & Violet with smooth shadows
    | - "glassmorphism"   : Translucent frosted glass, backdrop blur & luminous glow
    | - "dark_luxury"     : Deep OLED obsidian & slate with neon cyan & gold accents
    | - "minimalist_clean": Japanese / Notion monochrome with crisp hairline borders
    | - "corporate_blue"  : Enterprise fintech royal blue with navy executive sidebar
    |
    | Configure via APP_THEME in your .env file.
    |
    */
    'active' => env('APP_THEME', 'modern_indigo'),

    /*
    |--------------------------------------------------------------------------
    | Registered Theme Definitions
    |--------------------------------------------------------------------------
    */
    'themes' => [
        'modern_indigo' => [
            'key'         => 'modern_indigo',
            'name'        => 'Modern Indigo',
            'description' => 'Vibrant SaaS Indigo & Violet aesthetic with clean shadows and refined typography.',
            'category'    => 'Modern SaaS',
            'accent'      => '#6366f1',
            'secondary'   => '#8b5cf6',
            'mode'        => 'light',
            'icon'        => 'fe fe-layers',
        ],
        'glassmorphism' => [
            'key'         => 'glassmorphism',
            'name'        => 'Glassmorphism Frosted',
            'description' => 'Luminous translucent frosted glass cards, dynamic backdrop blur, and glowing borders.',
            'category'    => 'Futuristic Glass',
            'accent'      => '#a855f7',
            'secondary'   => '#0ea5e9',
            'mode'        => 'glass',
            'icon'        => 'fe fe-droplet',
        ],
        'dark_luxury' => [
            'key'         => 'dark_luxury',
            'name'        => 'Dark Luxury OLED',
            'description' => 'Deep obsidian slate backgrounds with glowing neon cyan and warm gold accents.',
            'category'    => 'Luxury OLED Dark',
            'accent'      => '#38bdf8',
            'secondary'   => '#f59e0b',
            'mode'        => 'dark',
            'icon'        => 'fe fe-moon',
        ],
        'minimalist_clean' => [
            'key'         => 'minimalist_clean',
            'name'        => 'Minimalist Clean',
            'description' => 'Japanese & Notion inspired high-contrast monochrome with hairline borders and zero noise.',
            'category'    => 'Minimalist',
            'accent'      => '#0f172a',
            'secondary'   => '#475569',
            'mode'        => 'clean',
            'icon'        => 'fe fe-square',
        ],
        'corporate_blue' => [
            'key'         => 'corporate_blue',
            'name'        => 'Corporate Blue & Slate',
            'description' => 'Fintech and enterprise trust theme featuring deep navy sidebar and royal blue accents.',
            'category'    => 'Enterprise Corporate',
            'accent'      => '#1d4ed8',
            'secondary'   => '#0f766e',
            'mode'        => 'corporate',
            'icon'        => 'fe fe-briefcase',
        ],
    ],
];

