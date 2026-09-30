<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'land' => [
        'noc_api' => [
            'url' => env('NOC_DETAILS_URL'),
            'apikey' => env('NOC_API_KEY')
        ],
        'noc_ind_lease_api' => [
            'url' => env('NOC_INDUSTRIAL_LEASE'),
            'apikey' => env('NOC_API_KEY')
        ],
        'veriy_dag' => [
            'btr_api' => [
                'url' => env('BTR_VERIFY_DAG_URL'),
                'apikey' => env('BTR_VERIFY_DAG_KEY')
            ],
            'dharitri_api' => [
                'url' => env('DHARITRI_VERIFY_DAG_URL'),
                'apikey' => env('DHARITRI_VERIFY_DAG_KEY')
            ]
        ]
    ],
    'gemini' => [
        'key' => env('GEMINI_API_KEY'),
    ],

    'appointment' => [
        'url' => env('APPOINTMENT_SERVICE_URL'),
        'timeout' => env('APPOINTMENT_SERVICE_TIMEOUT', 10),
        'token' => env('APPOINTMENT_SERVICE_TOKEN', env('APPOINTMENT_SERVICE_SECRET', env('APPOINTMENT_SERVICE_KEY'))),
    ],

    'fee_calculation' => [
        'url' => env('FEE_SERVICE_URL', env('FEE_CALCULATION_SERVICE_URL', 'http://127.0.0.1:8080')),
        'timeout' => (int) env('FEE_SERVICE_TIMEOUT', 10),
        'token' => env('FEE_SERVICE_TOKEN', env('FEE_SERVICE_PSK', env('X_SERVICE_TOKEN'))),
    ],

    'fee_service' => [
        'url' => env('FEE_SERVICE_URL', env('FEE_CALCULATION_SERVICE_URL', 'http://127.0.0.1:8080')),
        'timeout' => (int) env('FEE_SERVICE_TIMEOUT', 10),
        'token' => env('FEE_SERVICE_TOKEN', env('FEE_SERVICE_PSK', env('X_SERVICE_TOKEN'))),
    ],

];
