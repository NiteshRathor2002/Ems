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
