<?php

return [
    'proxies' => [
        env('TRUSTED_PROXIES', ''),
    ],
    'headers' => [
        \Illuminate\Http\Request::HEADER_X_FORWARDED_ALL,
    ]
];
