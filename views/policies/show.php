<?php
$statusKey = str_replace(' ', '-', $policy['status']);
include ROOT . '/views/partials/header.php';
?>

<div class="page-header">
    <div>
        <h4><i class="bi bi-file-earmark-text me-2 text-success"></i>Policy Detail</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/dashboard">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/policies/index">Policies</a></li>
            <li class="breadcrumb-item active"><?= htmlspecialchars($policy['policy_number']) ?></li>
        </ol></nav>
    </div>
    <?php if (AuthMiddleware::hasRole('admin', 'policy_officer')): ?>
    <div class="d-flex gap-2">
        <a href="<?= BASE_URL ?>/policies/edit/<?= $policy['id'] ?>" class="btn btn-sm btn-primary">
            <i class="bi bi-pencil me-1"></i> Edit
        </a>
        <form method="POST" action="<?= BASE_URL ?>/policies/destroy/<?= $policy['id'] ?>">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
            <button type="submit" class="btn btn-sm btn-outline-danger"
                    data-confirm="Permanently delete this policy and all its documents?">
                <i class="bi bi-trash me-1"></i> Delete
            </button>
        </form>
    </div>
    <?php endif; ?>
</div>

<div class="row g-3">
    <!-- Policy Info -->
    <div class="col-lg-7">
        <div class="section-card h-100">
            <div class="section-card-header">
                <h6><i class="bi bi-info-circle me-2"></i>Policy Information</h6>
                <span class="status-badge status-<?= $statusKey ?>"><?= htmlspecialchars($policy['status']) ?></span>
            </div>
            <div class="section-card-body">
                <dl class="row mb-0" style="font-size:.875rem;">
                    <dt class="col-sm-5 text-muted fw-normal">Policy Number</dt>
                    <dd class="col-sm-7"><code><?= htmlspecialchars($policy['policy_number']) ?></code></dd>

                    <dt class="col-sm-5 text-muted fw-normal">Client Name</dt>
                    <dd class="col-sm-7 fw-semibold"><?= htmlspecialchars($policy['client_name']) ?></dd>

                    <dt class="col-sm-5 text-muted fw-normal">Insurance Type</dt>
                    <dd class="col-sm-7"><?= htmlspecialchars($policy['insurance_type']) ?></dd>

                    <dt class="col-sm-5 text-muted fw-normal">Premium Amount</dt>
                    <dd class="col-sm-7 fw-semibold text-success">
                        USD <?= number_format((float)$policy['premium_amount'], 2) ?>
                    </dd>

                    <dt class="col-sm-5 text-muted fw-normal">Start Date</dt>
                    <dd class="col-sm-7"><?= date('d F Y', strtotime($policy['start_date'])) ?></dd>

                    <dt class="col-sm-5 text-muted fw-normal">Renewal Date</dt>
                    <dd class="col-sm-7">
                        <?= date('d F Y', strtotime($policy['renewal_date'])) ?>
                        <?php
                        $today   = new DateTime('today');
                        $renewal = new DateTime($policy['renewal_date']);
                        $diff    = (int) $today->diff($renewal)->days;
                        $isPast  = $renewal < $today;
                        if ($isPast): ?>
                            <span class="badge bg-danger ms-1">Expired <?= $diff ?> day<?= $diff !== 1 ? 's' : '' ?> ago</span>
                        <?php elseif ($diff <= RENEWAL_WARN_DAYS): ?>
                            <span class="badge bg-warning text-dark ms-1"><?= $diff ?> day<?= $diff !== 1 ? 's' : '' ?> left</span>
                        <?php endif; ?>
                    </dd>

                    <dt class="col-sm-5 text-muted fw-normal">Added By</dt>
                    <dd class="col-sm-7"><?= htmlspecialchars($policy['created_by_name']) ?></dd>

                    <dt class="col-sm-5 text-muted fw-normal">Created At</dt>
                    <dd class="col-sm-7 text-muted"><?= date('d M Y H:i', strtotime($policy['created_at'])) ?></dd>
                </dl>
            </div>
        </div>
    </div>

    <!-- Documents -->
    <div class="col-lg-5">
        <div class="section-card h-100">
            <div class="section-card-header">
                <h6><i class="bi bi-paperclip me-2"></i>Documents <span class="badge bg-secondary"><?= count($documents) ?></span></h6>
            </div>
            <div class="section-card-body p-0">

                <?php if (AuthMiddleware::hasRole('admin', 'policy_officer')): ?>
                <div class="p-3 border-bottom" style="background:#fafafa;">
                    <form method="POST" action="<?= BASE_URL ?>/documents/store/<?= $policy['id'] ?>" enctype="multipart/form-data">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                        <label class="form-label mb-1" style="font-size:.8rem;">Upload Document (PDF, JPG, PNG · max 5 MB)</label>
                        <div class="input-group input-group-sm">
                            <input type="file" class="form-control" name="document"
                                   accept=".pdf,.jpg,.jpeg,.png" required>
                            <button class="btn btn-primary" type="submit">
                                <i class="bi bi-upload"></i>
                            </button>
                        </div>
                    </form>
                </div>
                <?php endif; ?>

                <?php if (empty($documents)): ?>
                <div class="text-center text-muted py-4">
                    <i class="bi bi-file-earmark d-block fs-3 mb-2"></i>
                    No documents uploaded yet.
                </div>
                <?php else: ?>
                <ul class="list-group list-group-flush">
                    <?php foreach ($documents as $doc):
                        $icon = match(true) {
                            str_contains($doc['mime_type'], 'pdf')   => 'bi-file-pdf text-danger',
                            str_contains($doc['mime_type'], 'image') => 'bi-file-image text-info',
                            default                                   => 'bi-file text-secondary',
                        };
                        $sizeKb = round($doc['file_size'] / 1024, 1);
                    ?>
                    <li class="list-group-item d-flex align-items-center gap-2 py-2 px-3">
                        <i class="bi <?= $icon ?> fs-5"></i>
                        <div class="flex-grow-1 overflow-hidden">
                            <div style="font-size:.82rem;font-weight:500;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                <?= htmlspecialchars($doc['original_name']) ?>
                            </div>
                            <div style="font-size:.72rem;color:#9ca3af;">
                                <?= $sizeKb ?> KB · <?= date('d M Y', strtotime($doc['created_at'])) ?>
                                · <?= htmlspecialchars($doc['uploaded_by_name']) ?>
                            </div>
                        </div>
                        <div class="d-flex gap-1 flex-shrink-0">
                            <a href="<?= BASE_URL ?>/documents/download/<?= $doc['id'] ?>"
                               class="btn btn-xs btn-sm btn-outline-secondary py-0 px-2" title="Download">
                                <i class="bi bi-download"></i>
                            </a>
                            <?php if (AuthMiddleware::hasRole('admin', 'policy_officer')): ?>
                            <form method="POST" action="<?= BASE_URL ?>/documents/destroy/<?= $doc['id'] ?>">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                                <button class="btn btn-xs btn-sm btn-outline-danger py-0 px-2" title="Delete"
                                        data-confirm="Delete this document? This cannot be undone.">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                            <?php endif; ?>
                        </div>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include ROOT . '/views/partials/footer.php'; ?>
