<?php

return [
    'vat_percentage' => env('VAT_PERCENTAGE', 5.0),
    'tourism_fee_by_stars' => [
        1 => 7,
        2 => 10,
        3 => 10,
        4 => 15,
        5 => 20,
    ],
    'default_tourism_fee' => 10,
];
