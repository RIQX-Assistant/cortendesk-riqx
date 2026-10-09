<?php

/*
 * RIQX fork settings (branding, languages, dates). See riqx/README.md.
 */
return [
    // Bump with every RIQX image release: busts browser caches of our CSS/JS
    // (VERSION stays the upstream number, hbbs and clients read it).
    'revision' => 6,

    // Console languages: code => name shown in the switch (in its own language).
    'locales' => [
        'fa' => 'فارسی',
        'en' => 'English',
    ],

    // Used until a user picks a language (account setting or the switch).
    'default_locale' => env('RIQX_DEFAULT_LOCALE', 'fa'),

    // Every date the console prints is shown in this zone (stored values stay UTC).
    'display_timezone' => env('RIQX_DISPLAY_TIMEZONE', 'Asia/Tehran'),

    // Footer "Based on RIQX" link.
    'site_url' => env('RIQX_SITE_URL', 'https://riqx.one'),

    // AGPL-3.0 §13: users of the network service are offered the source.
    'source_url' => env('RIQX_SOURCE_URL', 'https://github.com/RIQX-Assistant/cortendesk-riqx'),
    'upstream_url' => 'https://github.com/marcpope/cortendesk',
];
