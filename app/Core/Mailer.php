<?php

declare(strict_types=1);

namespace App\Core;

class Mailer
{
    private ?string $lastError = null;

    public function __construct(private array $config)
    {
    }

    public function lastError(): ?string
    {
        return $this->lastError;
    }

    public function send(string $to, string $subject, string $body): bool
    {
        $this->lastError = null;
        $mail = $this->config['mail'] ?? [];
        $host = (string) ($mail['host'] ?? '');
        $port = (int) ($mail['port'] ?? 2525);
        $username = (string) ($mail['username'] ?? '');
        $password = (string) ($mail['password'] ?? '');
        $encryption = strtolower((string) ($mail['encryption'] ?? 'tls'));
        $fromAddress = (string) ($mail['from_address'] ?? 'no-reply@ems.local');
        $fromName = (string) ($mail['from_name'] ?? 'EMS');

        if ($host === '' || $username === '' || $password === '') {
            $this->setError('Missing SMTP configuration (host/username/password).');
            return false;
        }

        $socketHost = $encryption === 'ssl' ? 'ssl://' . $host : $host;
        $fp = @fsockopen($socketHost, $port, $errno, $errstr, 10);
        if (!$fp) {
            $this->setError('Connection failed: ' . $errno . ' ' . $errstr);
            return false;
        }
        stream_set_timeout($fp, 10);

        if (!$this->expect($fp, 220)) {
            $this->setError('SMTP server did not respond with 220.');
            fclose($fp);
            return false;
        }

        $resp = $this->command($fp, 'EHLO localhost');
        if (!$this->checkCode($resp, 250)) {
            $resp = $this->command($fp, 'HELO localhost');
            if (!$this->checkCode($resp, 250)) {
                $this->setError('EHLO/HELO rejected by server.');
                fclose($fp);
                return false;
            }
        }

        if ($encryption === 'tls') {
            $resp = $this->command($fp, 'STARTTLS');
            if (!$this->checkCode($resp, 220)) {
                $this->setError('STARTTLS not accepted by server.');
                fclose($fp);
                return false;
            }
            if (!stream_socket_enable_crypto($fp, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
                $this->setError('TLS handshake failed.');
                fclose($fp);
                return false;
            }
            $resp = $this->command($fp, 'EHLO localhost');
            if (!$this->checkCode($resp, 250)) {
                $this->setError('EHLO after STARTTLS failed.');
                fclose($fp);
                return false;
            }
        }

        $resp = $this->command($fp, 'AUTH LOGIN');
        if (!$this->checkCode($resp, 334)) {
            $this->setError('AUTH LOGIN not accepted.');
            fclose($fp);
            return false;
        }
        $resp = $this->command($fp, base64_encode($username));
        if (!$this->checkCode($resp, 334)) {
            $this->setError('SMTP username rejected.');
            fclose($fp);
            return false;
        }
        $resp = $this->command($fp, base64_encode($password));
        if (!$this->checkCode($resp, 235)) {
            $this->setError('SMTP password rejected.');
            fclose($fp);
            return false;
        }

        $resp = $this->command($fp, 'MAIL FROM:<' . $fromAddress . '>');
        if (!$this->checkCode($resp, 250)) {
            $this->setError('MAIL FROM rejected.');
            fclose($fp);
            return false;
        }
        $resp = $this->command($fp, 'RCPT TO:<' . $to . '>');
        if (!$this->checkCode($resp, 250)) {
            $this->setError('RCPT TO rejected.');
            fclose($fp);
            return false;
        }
        $resp = $this->command($fp, 'DATA');
        if (!$this->checkCode($resp, 354)) {
            $this->setError('DATA command rejected.');
            fclose($fp);
            return false;
        }

        $subject = str_replace(["\r", "\n"], '', $subject);
        $fromName = str_replace(["\r", "\n"], '', $fromName);

        $headers = [
            'From: "' . $fromName . '" <' . $fromAddress . '>',
            'To: <' . $to . '>',
            'Subject: ' . $subject,
            'Date: ' . gmdate('D, d M Y H:i:s') . ' +0000',
            'MIME-Version: 1.0',
            'Content-Type: text/plain; charset=UTF-8',
            'Content-Transfer-Encoding: 8bit',
        ];

        $body = preg_replace("/\r?\n/", "\r\n", $body);
        $body = preg_replace('/^\./m', '..', $body);
        $message = implode("\r\n", $headers) . "\r\n\r\n" . $body . "\r\n.\r\n";

        fwrite($fp, $message);
        $resp = $this->getLines($fp);
        if (!$this->checkCode($resp, 250)) {
            $this->setError('Message body rejected.');
            fclose($fp);
            return false;
        }

        $this->command($fp, 'QUIT');
        fclose($fp);
        return true;
    }

    private function command($fp, string $command): string
    {
        fwrite($fp, $command . "\r\n");
        return $this->getLines($fp);
    }

    private function getLines($fp): string
    {
        $data = '';
        while (!feof($fp)) {
            $line = fgets($fp, 512);
            if ($line === false) {
                break;
            }
            $data .= $line;
            if (preg_match('/^\d{3} /', $line)) {
                break;
            }
        }
        return $data;
    }

    private function expect($fp, int $code): bool
    {
        $data = $this->getLines($fp);
        return $this->checkCode($data, $code);
    }

    private function checkCode(string $response, int $code): bool
    {
        $lines = preg_split("/\r?\n/", trim($response));
        foreach ($lines as $line) {
            if (preg_match('/^' . $code . '\s/', $line)) {
                return true;
            }
        }
        return false;
    }

    private function setError(string $message): void
    {
        $this->lastError = $message;
        $this->log($message);
    }

    private function log(string $message): void
    {
        $dir = __DIR__ . '/../../storage/logs';
        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }
        $line = '[' . date('Y-m-d H:i:s') . '] ' . $message . PHP_EOL;
        @file_put_contents($dir . '/mail.log', $line, FILE_APPEND);
    }
}
