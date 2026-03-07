<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

class Experience
{
    private PDO $db;

    public function __construct(array $config)
    {
        $this->db = Database::connection($config['db']);
    }

    public function saveMany(int $userId, array $items): void
    {
        $this->db->prepare('DELETE FROM user_experiences WHERE user_id = :user_id')->execute(['user_id' => $userId]);
        if (!$items) {
            return;
        }

        $stmt = $this->db->prepare('INSERT INTO user_experiences (user_id, experience) VALUES (:user_id, :experience)');
        foreach ($items as $item) {
            $stmt->execute([
                'user_id' => $userId,
                'experience' => trim($item),
            ]);
        }
    }

    public function forUser(int $userId): array
    {
        $stmt = $this->db->prepare('SELECT experience FROM user_experiences WHERE user_id = :user_id ORDER BY id ASC');
        $stmt->execute(['user_id' => $userId]);
        return array_map(static fn (array $row): string => $row['experience'], $stmt->fetchAll());
    }
}
