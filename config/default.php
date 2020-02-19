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
    * Mail Service Config
    * ----------------------------------------------------------------------
    * See https://learn.userfrosting.com/mail/the-mailer-service
    */
    'mail'    => [
        'mailer'          => 'smtp', // Set to one of 'smtp', 'mail', 'qmail', 'sendmail'
        'host'            => getenv('SMTP_HOST') ?: null,
        'port'            => getenv('SMTP_PORT') ?: null,
        'auth'            => true,
        'secure'          => 'tls', // Enable TLS encryption. Set to `tls`, `ssl` or `false` (to disabled)
        'username'        => getenv('SMTP_USER') ?: null,
        'password'        => getenv('SMTP_PASSWORD') ?: null,
        'smtp_debug'      => 4,
        'message_options' => [
            'CharSet'   => 'UTF-8',
            'isHtml'    => true,
            'Timeout'   => 15,
        ],
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
        ],
        'registration' => [
            'user_defaults' => [
                'locale'      => 'es_ES',
                'institucion' => 'UNLu',
            ]
        ],
        'title' => 'CIDETIC',
    ],

    'actividad' => [
        'tipo' => [
            "acta" => [
                "agregar" => "agregar_acta",
                "asignar" => "asignar_acta_vinculacion",
                "eliminar" => "eliminar_acta",
                "reemplazar" => "reemplazar_acta",
            ],
            "peticion" => [
                "asignar_acta" => "asignar_acta_peticion",
                "baja" => "baja_solicitud",
                "editar" => "editar_peticion",
                "solicitud" => "solicitud_servicio",
            ],
            "servicio" => [
                "agregar" => "agregar_servicio",
                "editar" => "editar_servicio",
                "eliminar" => "eliminar_servicio",
            ],
            'vinculacion' => [
                "editar" => "editar_vinculacion",
                "solicitud" => "solicitud_vinculacion",
            ],
        ]
    ]
];