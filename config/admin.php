<?php

declare(strict_types=1);

return [
    'default' => [
        'name' => env('ADMIN_NAME', 'Administrador do Sistema'),
        'email' => env('ADMIN_EMAIL', 'admin@acervopastoral.local'),
        'password' => env('ADMIN_PASSWORD'),
    ],
];
