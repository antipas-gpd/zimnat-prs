<?php include ROOT . '/views/partials/header.php'; ?>

<div class="page-header">
    <div>
        <h4><i class="bi bi-plus-circle me-2 text-success"></i>Add New Policy</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/dashboard">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/policies/index">Policies</a></li>
            <li class="breadcrumb-item active">Add Policy</li>
        </ol></nav>
    </div>
</div>

<div class="row justify-content-center">
<div class="col-lg-9">
<div class="section-card">
    <div class="section-card-header">
        <h6><i class="bi bi-file-earmark-plus me-2"></i>Policy Details</h6>
    </div>
    <div class="section-card-body">
        <form method="POST" action="<?= BASE_URL ?>/policies/store">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

            <?php if (!empty($errors)): ?>
            <div class="alert alert-danger mb-3" style="border-radius:8px;font-size:.85rem;">
                <strong>Please fix:</strong>
                <ul class="mb-0 mt-1 ps-3">
                    <?php foreach ($errors as $e): ?>
                    <li><?= htmlspecialchars($e) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>

            <?php include ROOT . '/views/policies/_form.php'; ?>

            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-floppy me-1"></i> Save Policy
                </button>
                <a href="<?= BASE_URL ?>/policies/index" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
</div>
</div>

<?php include ROOT . '/views/partials/footer.php'; ?>
