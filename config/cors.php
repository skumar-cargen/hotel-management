<?php

return [

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => ['*'],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['Content-Type', 'Accept', 'Authorization', 'X-Domain', 'X-Requested-With', 'X-CSRF-TOKEN', 'Origin'],

    'exposed_headers' => [],

    'max_age' => 86400,

    'supports_credentials' => false,

];
