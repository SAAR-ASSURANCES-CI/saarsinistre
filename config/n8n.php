<?php
// config/n8n.php

return [
    /*
    |--------------------------------------------------------------------------
    | N8N Webhook Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration pour les webhooks n8n utilisés pour les notifications
    | automatiques lors de la création de sinistres
    |
    */

    'webhook_url' => env('N8N_WEBHOOK_URL', 'https://localhost/webhook'),

    'endpoints' => [
        'nouveau_sinistre' => 'nouveau_sinistre',
        'sinistre_assigne' => 'sinistre_assigne',
        'sinistre_cloture' => 'sinistre_cloture',
    ],

    'timeout' => env('N8N_TIMEOUT', 10),

    'retry_attempts' => env('N8N_RETRY_ATTEMPTS', 3),

    'enabled' => env('N8N_ENABLED', true),
];
