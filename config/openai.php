<?php

return [
    'api_key' => env('OPENAI_API_KEY'),
    'base_url' => 'https://api.openai.com/v1',
    'endpoints' => [
        'completions' => [
            'url' => 'completions',
            'method' => 'post',
        ],
    ],
];
