<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

class LeaveRequest
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

        $stmt = $this->db->prepare("SHOW TABLES LIKE 'leave_requests'");
        $stmt->execute();
        $this->tableExists = (bool) $stmt->fetchColumn();
        return $this->tableExists;
    }

    public function create(int $userId, string $startDate, string $endDate, string $reason): int
    {
        if (!$this->hasTable()) {
            return 0;
        }

        $stmt = $this->db->prepare('INSERT INTO leave_requests (user_id, start_date, end_date, reason, status) VALUES (:user_id, :start_date, :end_date, :reason, :status)');
        $stmt->execute([
            'user_id' => $userId,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'reason' => $reason,
            'status' => 'pending',
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function overlapExists(int $userId, string $startDate, string $endDate): bool
    {
        if (!$this->hasTable()) {
            return false;
        }

        $stmt = $this->db->prepare("SELECT 1 FROM leave_requests WHERE user_id = :user_id AND status IN ('pending','approved') AND start_date <= :end_date AND end_date >= :start_date LIMIT 1");
        $stmt->execute([
            'user_id' => $userId,
            'start_date' => $startDate,
            'end_date' => $endDate,
        ]);
        return (bool) $stmt->fetchColumn();
    }

    public function forUser(int $userId, int $limit = 200): array
    {
        if (!$this->hasTable()) {
            return [];
        }

        $limit = max(1, min(500, $limit));
        $stmt = $this->db->prepare('SELECT * FROM leave_requests WHERE user_id = :user_id ORDER BY id DESC LIMIT ' . $limit);
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll();
    }

    public function listForAdmin(?string $status = null, int $limit = 200): array
    {
        if (!$this->hasTable()) {
            return [];
        }

        $limit = max(1, min(500, $limit));
        $status = $status !== null ? trim($status) : null;

        $allowed = ['pending', 'approved', 'rejected'];
        $params = [];
        $where = '';
        if ($status !== null && $status !== '' && in_array($status, $allowed, true)) {
            $where = 'WHERE lr.status = :status';
            $params['status'] = $status;
        }

        $sql = 'SELECT lr.*, u.full_name AS employee_name, u.email AS employee_email
            FROM leave_requests lr
            INNER JOIN users u ON u.id = lr.user_id
            ' . $where . '
            ORDER BY lr.id DESC
            LIMIT ' . $limit;

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array
    {
        if (!$this->hasTable()) {
            return null;
        }

        $stmt = $this->db->prepare('SELECT * FROM leave_requests WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function decide(int $id, int $adminId, string $status): bool
    {
        if (!$this->hasTable()) {
            return false;
        }

        if (!in_array($status, ['approved', 'rejected'], true)) {
            return false;
        }

        $stmt = $this->db->prepare("UPDATE leave_requests SET status = :status, decided_by = :admin_id, decided_at = NOW() WHERE id = :id AND status = 'pending'");
        $stmt->execute([
            'id' => $id,
            'status' => $status,
            'admin_id' => $adminId,
        ]);

        return $stmt->rowCount() > 0;
    }
}
