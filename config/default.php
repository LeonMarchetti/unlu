<?php

return [
    /*
    * ----------------------------------------------------------------------
    * Debug Configuration
    * ----------------------------------------------------------------------
    * Turn any of those on to help debug your app
    */
    'debug' => [
        'deprecation'   => true,
        'queries'       => false,
        'smtp'          => true,
        'twig'          => true,
    ],
    /*
    * ----------------------------------------------------------------------
    * Site Settings
    * ----------------------------------------------------------------------
    * "Site" settings that are automatically passed to Twig
    */
    'site' => [
        'locales' => [
            // Should be ordered according to https://en.wikipedia.org/wiki/List_of_languages_by_total_number_of_speakers,
            // with the exception of English, which as the default language comes first.

            // Para ignorar los idiomas hay que asignarlos a null, pero no borrarlos.
            'available' => [
                'en_US' => 'English',
                'zh_CN' => null,
                'es_ES' => 'Español',
                'ar'    => null,
                'pt_Br' => null,
                'pt_PT' => null,
                'ru_RU' => null,
                'de_DE' => null,
                'fr_FR' => null,
                'tr'    => null,
                'it_IT' => null,
                'th_TH' => null,
                'fa'    => null,
                'el'    => null,
                'sr_RS' => null,
            ],
            'default' => 'es_ES'
        ]
    ]
];