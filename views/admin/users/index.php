<?php include ROOT . '/views/partials/header.php'; ?>

<div class="page-header">
    <div>
        <h4><i class="bi bi-people me-2 text-success"></i>Manage Users</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/dashboard">Dashboard</a></li>
            <li class="breadcrumb-item active">Users</li>
        </ol></nav>
    </div>
    <a href="<?= BASE_URL ?>/users/create" class="btn btn-primary btn-sm">
        <i class="bi bi-person-plus me-1"></i> Add User
    </a>
</div>

<div class="section-card">
    <div class="section-card-header">
        <h6><i class="bi bi-person-lines-fill me-2"></i>System Users</h6>
        <span class="text-muted" style="font-size:.78rem;"><?= count($users) ?> user<?= count($users) !== 1 ? 's' : '' ?></span>
    </div>

    <div class="table-responsive">
        <table class="data-table table mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($users as $i => $u): ?>
            <tr>
                <td class="text-muted"><?= $i + 1 ?></td>
                <td><strong><?= htmlspecialchars($u['full_name']) ?></strong>
                    <?php if ($u['id'] === AuthMiddleware::currentUserId()): ?>
                    <span class="badge bg-success ms-1" style="font-size:.6rem;">You</span>
                    <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($u['email']) ?></td>
                <td>
                    <span class="role-pill role-<?= $u['role'] ?>">
                        <?= str_replace('_', ' ', $u['role']) ?>
                    </span>
                </td>
                <td>
                    <?php if ($u['is_active']): ?>
                    <span class="badge" style="background:#d1fae5;color:#065f46;font-size:.7rem;">Active</span>
                    <?php else: ?>
                    <span class="badge" style="background:#fee2e2;color:#991b1b;font-size:.7rem;">Inactive</span>
                    <?php endif; ?>
                </td>
                <td class="text-muted"><?= date('d M Y', strtotime($u['created_at'])) ?></td>
                <td>
                    <div class="d-flex gap-1">
                        <a href="<?= BASE_URL ?>/users/edit/<?= $u['id'] ?>"
                           class="btn btn-sm btn-outline-primary py-0 px-2" title="Edit">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <?php if ($u['id'] !== AuthMiddleware::currentUserId()): ?>
                        <form method="POST" action="<?= BASE_URL ?>/users/toggleActive/<?= $u['id'] ?>">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                            <button type="submit"
                                    class="btn btn-sm py-0 px-2 <?= $u['is_active'] ? 'btn-outline-warning' : 'btn-outline-success' ?>"
                                    title="<?= $u['is_active'] ? 'Deactivate' : 'Activate' ?>"
                                    data-confirm="<?= $u['is_active'] ? 'Deactivate' : 'Activate' ?> this user?">
                                <i class="bi <?= $u['is_active'] ? 'bi-person-dash' : 'bi-person-check' ?>"></i>
                            </button>
                        </form>
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include ROOT . '/views/partials/footer.php'; ?>
