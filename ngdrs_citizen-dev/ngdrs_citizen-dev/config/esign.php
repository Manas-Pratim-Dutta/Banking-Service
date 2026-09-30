<?php

return [

    'asp_id' => env('ESIGN_ASP_ID','IGRA-900'),

    'url' => env(
        'ESIGN_URL',
        'https://es-staging.cdac.in/esignlevel2/2.1/form/signdoc'
        // 'https://es-staging.cdac.in/esignlevel1/2.1/form/signdoc'
    ),

    'response_url'=>env('APP_URL').'/esign/response',

    'private_key'=>storage_path(
        'app/esign/cert/dlrs_private_key.pem'
    ),

];