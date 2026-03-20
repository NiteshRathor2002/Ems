<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

class PasswordResetOtp
{
    private PDO $db;
    private ?bool $tableExists = null;

    public function __construct(array $config)
    {
        $this->db = Database::connection($config['db']);
    }

    public function featureReady(): bool
    {
        return $this->hasTable();
    }

    private function hasTable(): bool
    {
        if ($this->tableExists !== null) {
            return $this->tableExists;
        }

        $stmt = $this->db->prepare("SHOW TABLES LIKE 'password_reset_otps'");
        $stmt->execute();
        $this->tableExists = (bool) $stmt->fetchColumn();
        return $this->tableExists;
    }

    public function createForEmail(string $email, string $otp, int $ttlMinutes = 10): bool
    {
        if (!$this->hasTable()) {
            return false;
        }

        $this->db->prepare('DELETE FROM password_reset_otps WHERE email = :email AND used_at IS NULL')
            ->execute(['email' => $email]);

        $hash = password_hash($otp, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare('INSERT INTO password_reset_otps (email, otp_hash, expires_at) VALUES (:email, :otp_hash, DATE_ADD(NOW(), INTERVAL :ttl MINUTE))');
        return $stmt->execute([
            'email' => $email,
            'otp_hash' => $hash,
            'ttl' => $ttlMinutes,
        ]);
    }

    public function verify(string $email, string $otp): bool
    {
        if (!$this->hasTable()) {
            return false;
        }

        $stmt = $this->db->prepare('SELECT id, otp_hash FROM password_reset_otps WHERE email = :email AND used_at IS NULL AND expires_at > NOW() ORDER BY id DESC LIMIT 1');
        $stmt->execute(['email' => $email]);
        $row = $stmt->fetch();
        if (!$row) {
            return false;
        }

        $ok = password_verify($otp, (string) ($row['otp_hash'] ?? ''));
        if ($ok) {
            $this->db->prepare('UPDATE password_reset_otps SET used_at = NOW() WHERE id = :id')
                ->execute(['id' => (int) $row['id']]);
        }

        return $ok;
    }
}
