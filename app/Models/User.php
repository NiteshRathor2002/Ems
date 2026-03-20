<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

class User
{
    private PDO $db;
    private ?bool $departmentColumnExists = null;
    private ?bool $salaryColumnExists = null;

    public function __construct(array $config)
    {
        $this->db = Database::connection($config['db']);
    }

    public function departmentFeatureReady(): bool
    {
        return $this->hasDepartmentColumn();
    }

    public function salaryFeatureReady(): bool
    {
        return $this->hasSalaryColumn();
    }

    private function hasDepartmentColumn(): bool
    {
        if ($this->departmentColumnExists !== null) {
            return $this->departmentColumnExists;
        }

        $stmt = $this->db->prepare("SHOW COLUMNS FROM users LIKE 'department'");
        $stmt->execute();
        $this->departmentColumnExists = (bool) $stmt->fetch();
        return $this->departmentColumnExists;
    }

    private function selectDepartmentExpr(): string
    {
        return $this->hasDepartmentColumn() ? 'department' : 'NULL AS department';
    }

    private function hasSalaryColumn(): bool
    {
        if ($this->salaryColumnExists !== null) {
            return $this->salaryColumnExists;
        }

        $stmt = $this->db->prepare("SHOW COLUMNS FROM users LIKE 'salary'");
        $stmt->execute();
        $this->salaryColumnExists = (bool) $stmt->fetch();
        return $this->salaryColumnExists;
    }

    private function selectSalaryExpr(): string
    {
        return $this->hasSalaryColumn() ? 'salary' : 'NULL AS salary';
    }

    public function create(array $data): int
    {
        if ($this->hasDepartmentColumn()) {
            $data = array_merge(['department' => null], $data);
            $sql = 'INSERT INTO users (
            full_name, email, password_hash, age, department,
            perm_line1, perm_line2, perm_city, perm_state,
            curr_line1, curr_line2, curr_city, curr_state,
            profile_picture
        ) VALUES (
            :full_name, :email, :password_hash, :age, :department,
            :perm_line1, :perm_line2, :perm_city, :perm_state,
            :curr_line1, :curr_line2, :curr_city, :curr_state,
            :profile_picture
        )';
        } else {
            unset($data['department']);
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
        }

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

    public function emailExists(string $email, ?int $excludeId = null): bool
    {
        if ($excludeId !== null) {
            $stmt = $this->db->prepare('SELECT 1 FROM users WHERE email = :email AND id <> :id LIMIT 1');
            $stmt->execute(['email' => $email, 'id' => $excludeId]);
            return (bool) $stmt->fetchColumn();
        }

        $stmt = $this->db->prepare('SELECT 1 FROM users WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => $email]);
        return (bool) $stmt->fetchColumn();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function updatePassword(int $id, string $passwordHash): void
    {
        $stmt = $this->db->prepare('UPDATE users SET password_hash = :password_hash WHERE id = :id');
        $stmt->execute(['id' => $id, 'password_hash' => $passwordHash]);
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
        $stmt = $this->db->query('SELECT id, full_name, email, age, ' . $this->selectDepartmentExpr() . ', ' . $this->selectSalaryExpr() . ', perm_city, perm_state, curr_city, curr_state, profile_picture, created_at FROM users WHERE is_admin = 0 ORDER BY id DESC');
        return $stmt->fetchAll();
    }

    public function findEmployeeById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE id = :id AND is_admin = 0 LIMIT 1');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function createEmployee(array $data): int
    {
        $data['is_admin'] = 0;
        $hasDepartment = $this->hasDepartmentColumn();
        $hasSalary = $this->hasSalaryColumn();

        if ($hasDepartment) {
            $data = array_merge(['department' => null], $data);
        } else {
            unset($data['department']);
        }
        if ($hasSalary) {
            $data = array_merge(['salary' => null], $data);
        } else {
            unset($data['salary']);
        }

        if ($hasDepartment && $hasSalary) {
            $sql = 'INSERT INTO users (
            full_name, email, password_hash, age, department, salary,
            perm_line1, perm_line2, perm_city, perm_state,
            curr_line1, curr_line2, curr_city, curr_state,
            profile_picture, is_admin
        ) VALUES (
            :full_name, :email, :password_hash, :age, :department, :salary,
            :perm_line1, :perm_line2, :perm_city, :perm_state,
            :curr_line1, :curr_line2, :curr_city, :curr_state,
            :profile_picture, :is_admin
        )';
        } elseif ($hasDepartment) {
            $sql = 'INSERT INTO users (
            full_name, email, password_hash, age, department,
            perm_line1, perm_line2, perm_city, perm_state,
            curr_line1, curr_line2, curr_city, curr_state,
            profile_picture, is_admin
        ) VALUES (
            :full_name, :email, :password_hash, :age, :department,
            :perm_line1, :perm_line2, :perm_city, :perm_state,
            :curr_line1, :curr_line2, :curr_city, :curr_state,
            :profile_picture, :is_admin
        )';
        } elseif ($hasSalary) {
            $sql = 'INSERT INTO users (
            full_name, email, password_hash, age, salary,
            perm_line1, perm_line2, perm_city, perm_state,
            curr_line1, curr_line2, curr_city, curr_state,
            profile_picture, is_admin
        ) VALUES (
            :full_name, :email, :password_hash, :age, :salary,
            :perm_line1, :perm_line2, :perm_city, :perm_state,
            :curr_line1, :curr_line2, :curr_city, :curr_state,
            :profile_picture, :is_admin
        )';
        } else {
            $sql = 'INSERT INTO users (
            full_name, email, password_hash, age,
            perm_line1, perm_line2, perm_city, perm_state,
            curr_line1, curr_line2, curr_city, curr_state,
            profile_picture, is_admin
        ) VALUES (
            :full_name, :email, :password_hash, :age,
            :perm_line1, :perm_line2, :perm_city, :perm_state,
            :curr_line1, :curr_line2, :curr_city, :curr_state,
            :profile_picture, :is_admin
        )';
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($data);

        return (int) $this->db->lastInsertId();
    }

    public function updateEmployee(int $id, array $data): void
    {
        $data['id'] = $id;
        $hasDepartment = $this->hasDepartmentColumn();
        $hasSalary = $this->hasSalaryColumn();
        if (!$hasDepartment) {
            unset($data['department']);
        }
        if (!$hasSalary) {
            unset($data['salary']);
        }

        $sql = 'UPDATE users SET
            full_name = :full_name,
            email = :email,
            password_hash = :password_hash,
            age = :age,
            ' . ($hasDepartment ? 'department = :department,' : '') . '
            ' . ($hasSalary ? 'salary = :salary,' : '') . '
            perm_line1 = :perm_line1,
            perm_line2 = :perm_line2,
            perm_city = :perm_city,
            perm_state = :perm_state,
            curr_line1 = :curr_line1,
            curr_line2 = :curr_line2,
            curr_city = :curr_city,
            curr_state = :curr_state,
            profile_picture = :profile_picture
        WHERE id = :id AND is_admin = 0';

        $stmt = $this->db->prepare($sql);
        $stmt->execute($data);
    }

    public function deleteEmployee(int $id): void
    {
        $stmt = $this->db->prepare('DELETE FROM users WHERE id = :id AND is_admin = 0');
        $stmt->execute(['id' => $id]);
    }

    public function countEmployees(): int
    {
        $stmt = $this->db->query('SELECT COUNT(*) AS c FROM users WHERE is_admin = 0');
        $row = $stmt->fetch();
        return (int) ($row['c'] ?? 0);
    }

    public function countEmployeesSince(string $since): int
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) AS c FROM users WHERE is_admin = 0 AND created_at >= :since');
        $stmt->execute(['since' => $since]);
        $row = $stmt->fetch();
        return (int) ($row['c'] ?? 0);
    }

    public function recentEmployees(int $limit = 5): array
    {
        $limit = max(1, min(50, $limit));
        $stmt = $this->db->prepare('SELECT id, full_name, email, age, ' . $this->selectDepartmentExpr() . ', ' . $this->selectSalaryExpr() . ', profile_picture, created_at FROM users WHERE is_admin = 0 ORDER BY id DESC LIMIT ' . $limit);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function searchEmployees(?string $query, int $limit = 200): array
    {
        $limit = max(1, min(500, $limit));
        $query = trim((string) $query);
        if ($query === '') {
            $stmt = $this->db->prepare('SELECT id, full_name, email, age, ' . $this->selectDepartmentExpr() . ', ' . $this->selectSalaryExpr() . ', perm_city, perm_state, curr_city, curr_state, profile_picture, created_at FROM users WHERE is_admin = 0 ORDER BY id DESC LIMIT ' . $limit);
            $stmt->execute();
            return $stmt->fetchAll();
        }

        $like = '%' . $query . '%';
        $where = 'full_name LIKE :q_name OR email LIKE :q_email OR CAST(id AS CHAR) = :q_id';
        $params = [
            'q_name' => $like,
            'q_email' => $like,
            'q_id' => $query,
        ];
        if ($this->hasDepartmentColumn()) {
            $where = 'full_name LIKE :q_name OR email LIKE :q_email OR department LIKE :q_dept OR CAST(id AS CHAR) = :q_id';
            $params['q_dept'] = $like;
        }

        $stmt = $this->db->prepare('SELECT id, full_name, email, age, ' . $this->selectDepartmentExpr() . ', ' . $this->selectSalaryExpr() . ', perm_city, perm_state, curr_city, curr_state, profile_picture, created_at FROM users WHERE is_admin = 0 AND (' . $where . ') ORDER BY id DESC LIMIT ' . $limit);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function countEmployeesMatching(?string $query): int
    {
        $query = trim((string) $query);
        if ($query === '') {
            return $this->countEmployees();
        }

        $like = '%' . $query . '%';
        $where = 'full_name LIKE :q_name OR email LIKE :q_email OR CAST(id AS CHAR) = :q_id';
        $params = [
            'q_name' => $like,
            'q_email' => $like,
            'q_id' => $query,
        ];
        if ($this->hasDepartmentColumn()) {
            $where = 'full_name LIKE :q_name OR email LIKE :q_email OR department LIKE :q_dept OR CAST(id AS CHAR) = :q_id';
            $params['q_dept'] = $like;
        }

        $stmt = $this->db->prepare('SELECT COUNT(*) AS c FROM users WHERE is_admin = 0 AND (' . $where . ')');
        $stmt->execute($params);
        $row = $stmt->fetch();
        return (int) ($row['c'] ?? 0);
    }

    public function searchEmployeesPage(?string $query, int $limit = 20, int $offset = 0): array
    {
        $limit = max(1, min(500, $limit));
        $offset = max(0, $offset);
        $query = trim((string) $query);

        if ($query === '') {
            $stmt = $this->db->prepare('SELECT id, full_name, email, age, ' . $this->selectDepartmentExpr() . ', ' . $this->selectSalaryExpr() . ', perm_city, perm_state, curr_city, curr_state, profile_picture, created_at FROM users WHERE is_admin = 0 ORDER BY id DESC LIMIT ' . $limit . ' OFFSET ' . $offset);
            $stmt->execute();
            return $stmt->fetchAll();
        }

        $like = '%' . $query . '%';
        $where = 'full_name LIKE :q_name OR email LIKE :q_email OR CAST(id AS CHAR) = :q_id';
        $params = [
            'q_name' => $like,
            'q_email' => $like,
            'q_id' => $query,
        ];
        if ($this->hasDepartmentColumn()) {
            $where = 'full_name LIKE :q_name OR email LIKE :q_email OR department LIKE :q_dept OR CAST(id AS CHAR) = :q_id';
            $params['q_dept'] = $like;
        }

        $stmt = $this->db->prepare('SELECT id, full_name, email, age, ' . $this->selectDepartmentExpr() . ', ' . $this->selectSalaryExpr() . ', perm_city, perm_state, curr_city, curr_state, profile_picture, created_at FROM users WHERE is_admin = 0 AND (' . $where . ') ORDER BY id DESC LIMIT ' . $limit . ' OFFSET ' . $offset);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
