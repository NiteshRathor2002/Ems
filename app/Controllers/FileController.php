<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;

class FileController extends Controller
{
    public function profile(string $file): void
    {
        $file = basename($file);
        if ($file === '' || strlen($file) > 255) {
            http_response_code(404);
            echo 'Not found';
            return;
        }

        $upload = $this->config['upload'] ?? [];
        $dir = (string) ($upload['profile_dir'] ?? '');
        if ($dir === '') {
            http_response_code(500);
            echo 'Upload directory not configured';
            return;
        }

        $candidates = [
            rtrim($dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $file,
            rtrim(dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'profiles', DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $file,
        ];

        $path = null;
        foreach ($candidates as $candidate) {
            if (is_file($candidate)) {
                $path = $candidate;
                break;
            }
        }
        if ($path === null) {
            http_response_code(404);
            echo 'Not found';
            return;
        }

        $mime = mime_content_type($path) ?: 'application/octet-stream';
        if (strpos($mime, 'image/') !== 0) {
            http_response_code(415);
            echo 'Unsupported';
            return;
        }

        header('Content-Type: ' . $mime);
        header('Content-Length: ' . (string) filesize($path));
        header('Cache-Control: private, max-age=86400');
        readfile($path);
        exit;
    }
}
