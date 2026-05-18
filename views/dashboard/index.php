<?php include ROOT . '/views/partials/header.php'; ?>

<!-- Page Header -->
<div class="page-header">
    <div>
        <h4><i class="bi bi-speedometer2 me-2 text-success"></i>Dashboard</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0"><li class="breadcrumb-item active">Overview</li></ol></nav>
    </div>
    <span class="text-muted" style="font-size:.8rem;">
        <i class="bi bi-calendar3 me-1"></i><?= date('D, d M Y') ?>
    </span>
</div>

<!-- Stat Cards -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="icon-box bg-blue-soft"><i class="bi bi-files"></i></div>
            <div>
                <div class="stat-value"><?= number_format((int)$counts['total']) ?></div>
                <div class="stat-label">Total Policies</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="icon-box bg-green-soft"><i class="bi bi-shield-check"></i></div>
            <div>
                <div class="stat-value"><?= number_format((int)$counts['active']) ?></div>
                <div class="stat-label">Active Policies</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="icon-box bg-red-soft"><i class="bi bi-shield-x"></i></div>
            <div>
                <div class="stat-value"><?= number_format((int)$counts['expired']) ?></div>
                <div class="stat-label">Expired Policies</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="icon-box bg-yellow-soft"><i class="bi bi-clock-history"></i></div>
            <div>
                <div class="stat-value"><?= number_format((int)$nearingCount) ?></div>
                <div class="stat-label">Nearing Renewal</div>
            </div>
        </div>
    </div>
</div>

<!-- Nearing Renewal Table -->
<div class="section-card">
    <div class="section-card-header">
        <h6><i class="bi bi-exclamation-triangle-fill text-warning me-2"></i>Policies Nearing Renewal (within <?= RENEWAL_WARN_DAYS ?> days)</h6>
        <a href="<?= BASE_URL ?>/policies/index?status=Pending+Renewal" class="btn btn-sm btn-outline-primary">View All</a>
    </div>

    <?php if (empty($nearingList)): ?>
    <div class="section-card-body text-center text-muted py-4">
        <i class="bi bi-check-circle fs-3 d-block mb-2 text-success"></i>
        No policies are nearing their renewal date. All clear!
    </div>
    <?php else: ?>
    <div class="table-responsive">
        <table class="data-table table mb-0">
            <thead>
                <tr>
                    <th>Policy #</th>
                    <th>Client</th>
                    <th>Type</th>
                    <th>Renewal Date</th>
                    <th>Days Left</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($nearingList as $p):
                $renewal   = new DateTime($p['renewal_date']);
                $today     = new DateTime('today');
                $daysLeft  = (int) $today->diff($renewal)->days;
                $statusKey = str_replace(' ', '-', $p['status']);
            ?>
            <tr>
                <td><span class="fw-600 font-monospace"><?= htmlspecialchars($p['policy_number']) ?></span></td>
                <td><?= htmlspecialchars($p['client_name']) ?></td>
                <td><?= htmlspecialchars($p['insurance_type']) ?></td>
                <td><?= date('d M Y', strtotime($p['renewal_date'])) ?></td>
                <td>
                    <span class="badge <?= $daysLeft <= 7 ? 'bg-danger' : 'bg-warning text-dark' ?>">
                        <?= $daysLeft ?> day<?= $daysLeft !== 1 ? 's' : '' ?>
                    </span>
                </td>
                <td><span class="status-badge status-<?= $statusKey ?>"><?= htmlspecialchars($p['status']) ?></span></td>
                <td>
                    <a href="<?= BASE_URL ?>/policies/show/<?= $p['id'] ?>" class="btn btn-xs btn-outline-secondary btn-sm py-0 px-2">
                        <i class="bi bi-eye"></i>
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<?php include ROOT . '/views/partials/footer.php'; ?>
