<?php include ROOT . '/views/partials/header.php'; ?>

<div class="page-header">
    <div>
        <h4><i class="bi bi-person-gear me-2 text-success"></i>Edit User</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/dashboard">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/users/index">Users</a></li>
            <li class="breadcrumb-item active"><?= htmlspecialchars($user['full_name']) ?></li>
        </ol></nav>
    </div>
</div>

<div class="row justify-content-center">
<div class="col-lg-7">
<div class="section-card">
    <div class="section-card-header">
        <h6><i class="bi bi-person me-2"></i><?= htmlspecialchars($user['full_name']) ?></h6>
    </div>
    <div class="section-card-body">
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

        <form method="POST" action="<?= BASE_URL ?>/users/update/<?= $user['id'] ?>">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label">Full Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="full_name"
                           value="<?= htmlspecialchars($formData['full_name'] ?? $user['full_name']) ?>"
                           required>
                </div>
                <div class="col-12">
                    <label class="form-label">Email Address <span class="text-danger">*</span></label>
                    <input type="email" class="form-control" name="email"
                           value="<?= htmlspecialchars($formData['email'] ?? $user['email']) ?>"
                           required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Role <span class="text-danger">*</span></label>
                    <select class="form-select" name="role" required>
                        <?php foreach (['admin' => 'Admin', 'policy_officer' => 'Policy Officer', 'viewer' => 'Viewer'] as $val => $label): ?>
                        <option value="<?= $val ?>" <?= ($formData['role'] ?? $user['role']) === $val ? 'selected' : '' ?>>
                            <?= $label ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">New Password <span class="text-muted fw-normal">(leave blank to keep)</span></label>
                    <input type="password" class="form-control" name="password"
                           placeholder="Min. 8 characters" minlength="8">
                </div>
                <div class="col-12">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1"
                               <?= ($formData['is_active'] ?? $user['is_active']) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="is_active">Account Active</label>
                    </div>
                </div>
            </div>

            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-floppy me-1"></i> Save Changes
                </button>
                <a href="<?= BASE_URL ?>/users/index" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
</div>
</div>

<?php include ROOT . '/views/partials/footer.php'; ?>
