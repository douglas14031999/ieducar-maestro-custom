<?php

return [

    'active' => env('PMD_ACTIVE', true),

    'token' => env('PMD_TOKEN'),

    'allow_optional_address' => true,

    'show_how_to_do_video' => true,

    'video_intro_url' => null,

    'ibge_codes' => env('PMD_IBGE_CODES', ''),

    'city' => env('PMD_CITY', 'Içara'),

    'state' => env('PMD_STATE', 'SC'),

    'map' => [
        'lat' => env('PMD_MAP_LATITUDE', -28.7),
        'lng' => env('PMD_MAP_LONGITUDE', -49.3),
        'zoom' => env('PMD_MAP_ZOOM', 13),
    ],

    'logo' => env('PMD_LOGO', '/intranet/imagens/brasao-republica.png'),

    'slogan' => env('PMD_SLOGAN', 'Prefeitura Municipal de '),

    'standalone' => !env('PMD_LEGACY', true),

    'legacy' => env('PMD_LEGACY', true),

    'link_to_restrict_area' => null,

    'features' => [

        'allow_preregistration_data_update' => true,

        'allow_external_system_data_update' => true,

        'allow_transfer_registration' => false,

        'transfer_description' => 'Transferência Pré-matrícula Digital',

        'allow_vacancy_certificate' => false,

    ],

    'minha_vaga_na_creche' => [
        'url' => env('PMD_MINHA_VAGA_NA_CRECHE_URL'),
        'token' => env('PMD_MINHA_VAGA_NA_CRECHE_TOKEN'),
    ],

    'user' => env('PMD_USER', 1),

];
