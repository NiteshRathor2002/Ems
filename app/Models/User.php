<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

class User
{
    private PDO $db;

    public function __construct(array $config)
    {
        $this->db = Database::connection($config['db']);
    }

    public function create(array $data): int
    {
        $sql = 'INSERT INTO users (
            full_name, email, password_hash, age,
            perm_line1, perm_line2, perm_city, perm_state,
            curr_line1, curr_line2, curr_city, curr_state,
            profile_picture
        ) VALUES (
            :full_name, :email, :password_hash, :age,
            :perm_line1, :perm_line2, :perm_city, :perm_state,
            :curr_line1, :curr_line2, :curr_city, :curr_state,
            :profile_picture
        )';

        $stmt = $this->db->prepare($sql);
        $stmt->execute($data);

        return (int) $this->db->lastInsertId();
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => $email]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function updateProfile(int $userId, array $data): void
    {
        $data['id'] = $userId;
        $sql = 'UPDATE users SET
            full_name = :full_name,
            age = :age,
            perm_line1 = :perm_line1,
            perm_line2 = :perm_line2,
            perm_city = :perm_city,
            perm_state = :perm_state,
            curr_line1 = :curr_line1,
            curr_line2 = :curr_line2,
            curr_city = :curr_city,
            curr_state = :curr_state,
            profile_picture = :profile_picture
            WHERE id = :id';

        $stmt = $this->db->prepare($sql);
        $stmt->execute($data);
    }

    public function allEmployees(): array
    {
        $stmt = $this->db->query('SELECT id, full_name, email, age, perm_city, perm_state, curr_city, curr_state, profile_picture, created_at FROM users WHERE is_admin = 0 ORDER BY id DESC');
        return $stmt->fetchAll();
    }
}
