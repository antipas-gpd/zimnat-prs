<?php
/**
 * Policy Model
 */

class PolicyModel extends BaseModel
{
    // ── Finders ──────────────────────────────────────────────

    public function findAll(string $search = '', string $statusFilter = ''): array
    {
        $sql    = 'SELECT p.*, u.full_name AS created_by_name
                     FROM policies p
                     JOIN users u ON u.id = p.created_by
                    WHERE 1=1';
        $params = [];

        if ($search !== '') {
            $sql     .= ' AND (p.policy_number LIKE ? OR p.client_name LIKE ? OR p.insurance_type LIKE ?)';
            $like     = '%' . $search . '%';
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
        }

        if ($statusFilter !== '') {
            $sql     .= ' AND p.status = ?';
            $params[] = $statusFilter;
        }

        $sql .= ' ORDER BY p.renewal_date ASC';
        return $this->query($sql, $params)->fetchAll();
    }

    public function findById(int $id): array|false
    {
        return $this->query(
            'SELECT p.*, u.full_name AS created_by_name
               FROM policies p
               JOIN users u ON u.id = p.created_by
              WHERE p.id = ?',
            [$id]
        )->fetch();
    }

    public function findByPolicyNumber(string $number, int $excludeId = 0): array|false
    {
        return $this->query(
            'SELECT id FROM policies WHERE policy_number = ? AND id != ?',
            [$number, $excludeId]
        )->fetch();
    }

    public function getNearingRenewal(int $days = 30): array
    {
        return $this->query(
            "SELECT * FROM policies
              WHERE renewal_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL ? DAY)
                AND status != 'Expired'
              ORDER BY renewal_date ASC",
            [$days]
        )->fetchAll();
    }

    // ── Dashboard stats ───────────────────────────────────────

    public function getCounts(): array
    {
        $row = $this->query(
            "SELECT
                COUNT(*) AS total,
                SUM(status = 'Active') AS active,
                SUM(status = 'Expired') AS expired,
                SUM(status = 'Pending Renewal') AS pending_renewal
             FROM policies"
        )->fetch();
        return $row ?: ['total' => 0, 'active' => 0, 'expired' => 0, 'pending_renewal' => 0];
    }

    public function getNearingRenewalCount(int $days = 30): int
    {
        $row = $this->query(
            "SELECT COUNT(*) AS cnt
               FROM policies
              WHERE renewal_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL ? DAY)
                AND status != 'Expired'",
            [$days]
        )->fetch();
        return (int) ($row['cnt'] ?? 0);
    }

    // ── Mutations ─────────────────────────────────────────────

    public function create(array $data, int $userId): int
    {
        $this->query(
            'INSERT INTO policies
                (policy_number, client_name, insurance_type, premium_amount,
                 start_date, renewal_date, status, created_by)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)',
            [
                $this->sanitize($data['policy_number']),
                $this->sanitize($data['client_name']),
                $this->sanitize($data['insurance_type']),
                (float) $data['premium_amount'],
                $data['start_date'],
                $data['renewal_date'],
                $this->computeStatus($data['renewal_date'], $data['status']),
                $userId,
            ]
        );
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data, int $userId): bool
    {
        return (bool) $this->query(
            'UPDATE policies
                SET policy_number  = ?,
                    client_name    = ?,
                    insurance_type = ?,
                    premium_amount = ?,
                    start_date     = ?,
                    renewal_date   = ?,
                    status         = ?,
                    updated_by     = ?
              WHERE id = ?',
            [
                $this->sanitize($data['policy_number']),
                $this->sanitize($data['client_name']),
                $this->sanitize($data['insurance_type']),
                (float) $data['premium_amount'],
                $data['start_date'],
                $data['renewal_date'],
                $this->computeStatus($data['renewal_date'], $data['status']),
                $userId,
                $id,
            ]
        )->rowCount();
    }

    public function delete(int $id): bool
    {
        return (bool) $this->query(
            'DELETE FROM policies WHERE id = ?',
            [$id]
        )->rowCount();
    }

    // ── Helpers ───────────────────────────────────────────────

    /**
     * Auto-compute status based on renewal date unless admin forces a value.
     */
    public function computeStatus(string $renewalDate, string $requested = 'Active'): string
    {
        $today   = new DateTimeImmutable('today');
        $renewal = new DateTimeImmutable($renewalDate);
        $diff    = (int) $today->diff($renewal)->days;
        $past    = $renewal < $today;

        if ($past) return 'Expired';
        if ($diff <= RENEWAL_WARN_DAYS) return 'Pending Renewal';
        return $requested === 'Expired' ? 'Active' : $requested;
    }

    /** Refresh statuses of all policies (e.g. run as a cron). */
    public function refreshStatuses(): void
    {
        $this->query(
            "UPDATE policies
                SET status = CASE
                    WHEN renewal_date < CURDATE() THEN 'Expired'
                    WHEN DATEDIFF(renewal_date, CURDATE()) <= ? THEN 'Pending Renewal'
                    ELSE 'Active'
                END",
            [RENEWAL_WARN_DAYS]
        );
    }
}
