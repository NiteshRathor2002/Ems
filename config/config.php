<?php
return [
    'app_url' => 'http://localhost/Ems/public',
    'base_path' => '/Ems/public',
    'db' => [
        'host' => '127.0.0.1',
        'port' => '3306',
        'dbname' => 'ems_core_php',
        'username' => 'root',
        'password' => '',
        'charset' => 'utf8mb4',
    ],
    'session' => [
        'name' => 'ems_session',
    ],
    'mail' => [
        'host' => 'smtp.mailosaur.net',
        'port' => 465,
        'username' => 'd1cxicsf@mailosaur.net',
        'password' => 'RrBgatiEPl2aqqh7ygqo3dUVbyAiGhSo',
        'encryption' => 'SSL',
        'from_address' => 'no-reply@ems.local',
        'from_name' => 'EMS',
    ],
    'upload' => [
        'profile_dir' => __DIR__ . '/../storage/uploads/profiles',
        'profile_web_path' => '/files/profile/',
        'max_size' => 2 * 1024 * 1024,
        'allowed_mime' => [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
        ],
    ],
];
