<?php
/**
 * Document Model
 */

class DocumentModel extends BaseModel
{
    public function findByPolicy(int $policyId): array
    {
        return $this->query(
            'SELECT d.*, u.full_name AS uploaded_by_name
               FROM documents d
               JOIN users u ON u.id = d.uploaded_by
              WHERE d.policy_id = ?
              ORDER BY d.created_at DESC',
            [$policyId]
        )->fetchAll();
    }

    public function findById(int $id): array|false
    {
        return $this->query(
            'SELECT * FROM documents WHERE id = ?',
            [$id]
        )->fetch();
    }

    public function create(array $data): int
    {
        $this->query(
            'INSERT INTO documents
                (policy_id, original_name, stored_name, file_path, mime_type, file_size, uploaded_by)
             VALUES (?, ?, ?, ?, ?, ?, ?)',
            [
                $data['policy_id'],
                $this->sanitize($data['original_name']),
                $data['stored_name'],
                $data['file_path'],
                $data['mime_type'],
                $data['file_size'],
                $data['uploaded_by'],
            ]
        );
        return (int) $this->db->lastInsertId();
    }

    public function delete(int $id): array|false
    {
        $doc = $this->findById($id);
        if (!$doc) return false;

        $this->query('DELETE FROM documents WHERE id = ?', [$id]);
        return $doc;
    }

    public function countByPolicy(int $policyId): int
    {
        $row = $this->query(
            'SELECT COUNT(*) AS cnt FROM documents WHERE policy_id = ?',
            [$policyId]
        )->fetch();
        return (int) ($row['cnt'] ?? 0);
    }
}
