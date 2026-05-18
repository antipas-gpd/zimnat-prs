<!-- Shared Policy Form Fields -->
<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label" for="policy_number">Policy Number <span class="text-danger">*</span></label>
        <input type="text" class="form-control" id="policy_number" name="policy_number"
               value="<?= htmlspecialchars($formData['policy_number'] ?? '') ?>"
               placeholder="e.g. ZIM-2024-00123" required>
    </div>
    <div class="col-md-6">
        <label class="form-label" for="client_name">Client Name <span class="text-danger">*</span></label>
        <input type="text" class="form-control" id="client_name" name="client_name"
               value="<?= htmlspecialchars($formData['client_name'] ?? '') ?>"
               placeholder="Full client name" required>
    </div>
    <div class="col-md-6">
        <label class="form-label" for="insurance_type">Insurance Type <span class="text-danger">*</span></label>
        <input type="text" class="form-control" id="insurance_type" name="insurance_type"
               value="<?= htmlspecialchars($formData['insurance_type'] ?? '') ?>"
               placeholder="e.g. Life, Motor, Property" required>
    </div>
    <div class="col-md-6">
        <label class="form-label" for="premium_amount">Premium Amount (USD) <span class="text-danger">*</span></label>
        <div class="input-group">
            <span class="input-group-text">$</span>
            <input type="number" class="form-control" id="premium_amount" name="premium_amount"
                   value="<?= htmlspecialchars($formData['premium_amount'] ?? '') ?>"
                   min="0" step="0.01" placeholder="0.00" required>
        </div>
    </div>
    <div class="col-md-4">
        <label class="form-label" for="start_date">Start Date <span class="text-danger">*</span></label>
        <input type="date" class="form-control" id="start_date" name="start_date"
               value="<?= htmlspecialchars($formData['start_date'] ?? '') ?>" required>
    </div>
    <div class="col-md-4">
        <label class="form-label" for="renewal_date">Renewal Date <span class="text-danger">*</span></label>
        <input type="date" class="form-control" id="renewal_date" name="renewal_date"
               value="<?= htmlspecialchars($formData['renewal_date'] ?? '') ?>" required>
        <div class="form-text">Status is auto-computed from this date.</div>
    </div>
    <div class="col-md-4">
        <label class="form-label" for="status">Status</label>
        <select class="form-select" id="status" name="status">
            <?php foreach (['Active', 'Expired', 'Pending Renewal'] as $s): ?>
            <option value="<?= $s ?>" <?= ($formData['status'] ?? 'Active') === $s ? 'selected' : '' ?>>
                <?= $s ?>
            </option>
            <?php endforeach; ?>
        </select>
        <div class="form-text">Auto-overridden if renewal date is in the past or within 30 days.</div>
    </div>
</div>
