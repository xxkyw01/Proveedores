<?php
return [
    //'base_uri'   => env('SAPB1SL_BASEURI', 'https://127.0.0.1:50000/b1s/v1'),
    'base_uri'   => env('SAPB1SL_BASEURI', 'https://192.168.2.214:50000/b1s/v1'),
    'company'    => env('SAPB1SL_COMPANYDB', ''),
    'username'   => env('SAPB1SL_USERNAME', ''),
    'password'   => env('SAPB1SL_PASSWORD', ''),
    'verify_ssl' => filter_var(env('SAPB1SL_VERIFY_SSL', false), FILTER_VALIDATE_BOOL),
];
