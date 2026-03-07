<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

class Qualification
{
    private PDO $db;

    public function __construct(array $config)
    {
        $this->db = Database::connection($config['db']);
    }

    public function saveMany(int $userId, array $items): void
    {
        $this->db->prepare('DELETE FROM user_qualifications WHERE user_id = :user_id')->execute(['user_id' => $userId]);
        if (!$items) {
            return;
        }

        $stmt = $this->db->prepare('INSERT INTO user_qualifications (user_id, qualification) VALUES (:user_id, :qualification)');
        foreach ($items as $item) {
            $stmt->execute([
                'user_id' => $userId,
                'qualification' => trim($item),
            ]);
        }
    }

    public function forUser(int $userId): array
    {
        $stmt = $this->db->prepare('SELECT qualification FROM user_qualifications WHERE user_id = :user_id ORDER BY id ASC');
        $stmt->execute(['user_id' => $userId]);
        return array_map(static fn (array $row): string => $row['qualification'], $stmt->fetchAll());
    }
}
