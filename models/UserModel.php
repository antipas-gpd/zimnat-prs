<?php
/**
 * User Model
 */

class UserModel extends BaseModel
{
    // ── Finders ──────────────────────────────────────────────

    public function findById(int $id): array|false
    {
        return $this->query(
            'SELECT id, full_name, email, role, is_active, created_at, updated_at
               FROM users WHERE id = ?',
            [$id]
        )->fetch();
    }

    public function findByEmail(string $email): array|false
    {
        return $this->query(
            'SELECT * FROM users WHERE email = ? LIMIT 1',
            [$email]
        )->fetch();
    }

    public function findAll(): array
    {
        return $this->query(
            'SELECT id, full_name, email, role, is_active, created_at
               FROM users ORDER BY created_at DESC'
        )->fetchAll();
    }

    // ── Mutations ─────────────────────────────────────────────

    public function create(array $data): int
    {
        $this->query(
            'INSERT INTO users (full_name, email, password, role, is_active)
             VALUES (?, ?, ?, ?, 1)',
            [
                $this->sanitize($data['full_name']),
                strtolower(trim($data['email'])),
                password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 12]),
                $data['role'],
            ]
        );
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $fields = ['full_name = ?', 'role = ?', 'is_active = ?'];
        $values = [
            $this->sanitize($data['full_name']),
            $data['role'],
            (int) $data['is_active'],
        ];

        if (!empty($data['password'])) {
            $fields[] = 'password = ?';
            $values[]  = password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 12]);
        }

        $values[] = $id;

        return (bool) $this->query(
            'UPDATE users SET ' . implode(', ', $fields) . ' WHERE id = ?',
            $values
        )->rowCount();
    }

    public function setActive(int $id, bool $active): bool
    {
        return (bool) $this->query(
            'UPDATE users SET is_active = ? WHERE id = ?',
            [(int) $active, $id]
        )->rowCount();
    }

    // ── Auth ──────────────────────────────────────────────────

    public function verifyPassword(string $plain, string $hash): bool
    {
        return password_verify($plain, $hash);
    }

    public function emailExists(string $email, int $excludeId = 0): bool
    {
        $row = $this->query(
            'SELECT COUNT(*) as cnt FROM users WHERE email = ? AND id != ?',
            [$email, $excludeId]
        )->fetch();
        return $row['cnt'] > 0;
    }
}
