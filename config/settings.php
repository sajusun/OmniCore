<?php

return [
    'logo' => env('APP_LOGO', 'default/logo.svg'),
    'sms' => env('SMS', 'off'),
    'mail' => env('MAIL', 'on'),

    //on||off
    'reverb' => env('REVERB', 'off'),

    //yes||no
    'recaptcha' => env('RECAPTCHA_ENABLE', 'no'), 

    'pagination' => env('PAGINATION', '12'),

    'google-map' => env('GOOGLE_MAPS_API_KEY', 'AIzaSyDl7ias7CMBPanjqPisVXwhXXVth21Cl5Y')
];
