<?php include ROOT . '/views/partials/header.php'; ?>

<div class="page-header">
    <div>
        <h4><i class="bi bi-file-earmark-text me-2 text-success"></i>All Policies</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/dashboard">Dashboard</a></li>
            <li class="breadcrumb-item active">Policies</li>
        </ol></nav>
    </div>
    <?php if (AuthMiddleware::hasRole('admin', 'policy_officer')): ?>
    <a href="<?= BASE_URL ?>/policies/create" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg me-1"></i> Add Policy
    </a>
    <?php endif; ?>
</div>

<!-- Filters -->
<div class="section-card mb-3">
    <div class="section-card-body py-2">
        <form method="GET" action="<?= BASE_URL ?>/policies/index" class="row g-2 align-items-end">
            <div class="col-12 col-md-5">
                <input type="text" class="form-control form-control-sm" name="search"
                       placeholder="Search by policy #, client, or type..."
                       value="<?= htmlspecialchars($search) ?>">
            </div>
            <div class="col-auto">
                <select class="form-select form-select-sm" name="status">
                    <option value="">All Statuses</option>
                    <?php foreach (['Active','Expired','Pending Renewal'] as $s): ?>
                    <option value="<?= $s ?>" <?= $statusFilter === $s ? 'selected' : '' ?>><?= $s ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-auto">
                <button class="btn btn-sm btn-primary"><i class="bi bi-search me-1"></i>Search</button>
                <a href="<?= BASE_URL ?>/policies/index" class="btn btn-sm btn-outline-secondary ms-1">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Table -->
<div class="section-card">
    <div class="section-card-header">
        <h6><i class="bi bi-table me-2"></i>Policy Records</h6>
        <span class="text-muted" style="font-size:.78rem;"><?= count($policies) ?> record<?= count($policies) !== 1 ? 's' : '' ?> found</span>
    </div>

    <?php if (empty($policies)): ?>
    <div class="section-card-body text-center text-muted py-5">
        <i class="bi bi-inbox fs-2 d-block mb-2"></i>
        No policies found. <?php if (AuthMiddleware::hasRole('admin','policy_officer')): ?>
            <a href="<?= BASE_URL ?>/policies/create">Add one now.</a>
        <?php endif; ?>
    </div>
    <?php else: ?>
    <div class="table-responsive">
        <table class="data-table table mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Policy Number</th>
                    <th>Client Name</th>
                    <th>Type</th>
                    <th>Premium (USD)</th>
                    <th>Start Date</th>
                    <th>Renewal Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($policies as $i => $p):
                $statusKey = str_replace(' ', '-', $p['status']);
            ?>
            <tr>
                <td class="text-muted"><?= $i + 1 ?></td>
                <td><code><?= htmlspecialchars($p['policy_number']) ?></code></td>
                <td><strong><?= htmlspecialchars($p['client_name']) ?></strong></td>
                <td><?= htmlspecialchars($p['insurance_type']) ?></td>
                <td><?= number_format((float)$p['premium_amount'], 2) ?></td>
                <td><?= date('d M Y', strtotime($p['start_date'])) ?></td>
                <td><?= date('d M Y', strtotime($p['renewal_date'])) ?></td>
                <td><span class="status-badge status-<?= $statusKey ?>"><?= htmlspecialchars($p['status']) ?></span></td>
                <td>
                    <div class="d-flex gap-1">
                        <a href="<?= BASE_URL ?>/policies/show/<?= $p['id'] ?>"
                           class="btn btn-sm btn-outline-secondary py-0 px-2" title="View">
                            <i class="bi bi-eye"></i>
                        </a>
                        <?php if (AuthMiddleware::hasRole('admin', 'policy_officer')): ?>
                        <a href="<?= BASE_URL ?>/policies/edit/<?= $p['id'] ?>"
                           class="btn btn-sm btn-outline-primary py-0 px-2" title="Edit">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form method="POST" action="<?= BASE_URL ?>/policies/destroy/<?= $p['id'] ?>" class="d-inline">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                            <button type="submit" class="btn btn-sm btn-outline-danger py-0 px-2" title="Delete"
                                    data-confirm="Delete policy '<?= htmlspecialchars($p['policy_number']) ?>'? This cannot be undone.">
                                <i class="bi bi-trash"></i>
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
    <?php endif; ?>
</div>

<?php include ROOT . '/views/partials/footer.php'; ?>
