<?php

return [
    'api_url' => env('IXC_API_URL', 'https://painel.seudominio.com.br'),
    'token' => env('IXC_API_TOKEN', ''),
    'secret' => env('IXC_API_SECRET', ''),
    'version' => env('IXC_API_VERSION', 'v1'),
    'timeout' => 30,
    'connect_timeout' => 10,
];
