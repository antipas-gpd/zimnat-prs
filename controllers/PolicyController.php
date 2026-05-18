<?php
/**
 * Policy Controller
 */

class PolicyController
{
    private PolicyModel   $policyModel;
    private DocumentModel $docModel;

    public function __construct()
    {
        $this->policyModel = new PolicyModel();
        $this->docModel    = new DocumentModel();
    }

    // GET /policies/index
    public function index(): void
    {
        AuthMiddleware::requireAuth();

        $search       = trim($_GET['search'] ?? '');
        $statusFilter = $_GET['status'] ?? '';
        $policies     = $this->policyModel->findAll($search, $statusFilter);

        $pageTitle = 'Policies';
        include ROOT . '/views/policies/index.php';
    }

    // GET /policies/create
    public function create(): void
    {
        AuthMiddleware::requireRole('admin', 'policy_officer');

        $errors    = $_SESSION['flash_errors'] ?? [];
        $formData  = $_SESSION['flash_form']   ?? [];
        unset($_SESSION['flash_errors'], $_SESSION['flash_form']);

        $pageTitle = 'Add Policy';
        include ROOT . '/views/policies/create.php';
    }

    // POST /policies/store
    public function store(): void
    {
        AuthMiddleware::requireRole('admin', 'policy_officer');
        AuthMiddleware::verifyCsrf();

        $data   = $this->extractPostData();
        $errors = $this->validate($data);

        // Duplicate policy number check
        if ($this->policyModel->findByPolicyNumber($data['policy_number'])) {
            $errors[] = 'Policy number already exists in the system.';
        }

        if (!empty($errors)) {
            $_SESSION['flash_errors'] = $errors;
            $_SESSION['flash_form']   = $data;
            header('Location: ' . BASE_URL . '/policies/create');
            exit;
        }

        $this->policyModel->create($data, AuthMiddleware::currentUserId());
        $_SESSION['flash_success'] = 'Policy created successfully.';
        header('Location: ' . BASE_URL . '/policies/index');
        exit;
    }

    // GET /policies/show/{id}
    public function show(?string $id): void
    {
        AuthMiddleware::requireAuth();

        $policy    = $this->fetchPolicyOr404((int) $id);
        $documents = $this->docModel->findByPolicy($policy['id']);

        $pageTitle = 'Policy Detail';
        include ROOT . '/views/policies/show.php';
    }

    // GET /policies/edit/{id}
    public function edit(?string $id): void
    {
        AuthMiddleware::requireRole('admin', 'policy_officer');

        $policy    = $this->fetchPolicyOr404((int) $id);
        $errors    = $_SESSION['flash_errors'] ?? [];
        $formData  = $_SESSION['flash_form']   ?? $policy;
        unset($_SESSION['flash_errors'], $_SESSION['flash_form']);

        $pageTitle = 'Edit Policy';
        include ROOT . '/views/policies/edit.php';
    }

    // POST /policies/update/{id}
    public function update(?string $id): void
    {
        AuthMiddleware::requireRole('admin', 'policy_officer');
        AuthMiddleware::verifyCsrf();

        $policy = $this->fetchPolicyOr404((int) $id);
        $data   = $this->extractPostData();
        $errors = $this->validate($data);

        if ($this->policyModel->findByPolicyNumber($data['policy_number'], $policy['id'])) {
            $errors[] = 'Policy number already in use by another policy.';
        }

        if (!empty($errors)) {
            $_SESSION['flash_errors'] = $errors;
            $_SESSION['flash_form']   = $data;
            header('Location: ' . BASE_URL . '/policies/edit/' . $policy['id']);
            exit;
        }

        $this->policyModel->update($policy['id'], $data, AuthMiddleware::currentUserId());
        $_SESSION['flash_success'] = 'Policy updated successfully.';
        header('Location: ' . BASE_URL . '/policies/show/' . $policy['id']);
        exit;
    }

    // POST /policies/destroy/{id}
    public function destroy(?string $id): void
    {
        AuthMiddleware::requireRole('admin', 'policy_officer');
        AuthMiddleware::verifyCsrf();

        $policy = $this->fetchPolicyOr404((int) $id);
        $this->policyModel->delete($policy['id']);

        $_SESSION['flash_success'] = 'Policy deleted successfully.';
        header('Location: ' . BASE_URL . '/policies/index');
        exit;
    }

    // ── Helpers ───────────────────────────────────────────────

    private function fetchPolicyOr404(int $id): array
    {
        $policy = $this->policyModel->findById($id);
        if (!$policy) {
            http_response_code(404);
            include ROOT . '/views/partials/404.php';
            exit;
        }
        return $policy;
    }

    private function extractPostData(): array
    {
        return [
            'policy_number'  => trim($_POST['policy_number']  ?? ''),
            'client_name'    => trim($_POST['client_name']     ?? ''),
            'insurance_type' => trim($_POST['insurance_type']  ?? ''),
            'premium_amount' => trim($_POST['premium_amount']  ?? ''),
            'start_date'     => trim($_POST['start_date']      ?? ''),
            'renewal_date'   => trim($_POST['renewal_date']    ?? ''),
            'status'         => trim($_POST['status']          ?? 'Active'),
        ];
    }

    private function validate(array $data): array
    {
        $errors = [];

        if (empty($data['policy_number']))  $errors[] = 'Policy number is required.';
        if (empty($data['client_name']))    $errors[] = 'Client name is required.';
        if (empty($data['insurance_type'])) $errors[] = 'Insurance type is required.';

        if (!is_numeric($data['premium_amount']) || (float) $data['premium_amount'] < 0) {
            $errors[] = 'Premium amount must be a valid positive number.';
        }

        if (empty($data['start_date'])) {
            $errors[] = 'Start date is required.';
        } elseif (!$this->isValidDate($data['start_date'])) {
            $errors[] = 'Start date is not a valid date.';
        }

        if (empty($data['renewal_date'])) {
            $errors[] = 'Renewal date is required.';
        } elseif (!$this->isValidDate($data['renewal_date'])) {
            $errors[] = 'Renewal date is not a valid date.';
        }

        if (empty($errors) && $data['renewal_date'] < $data['start_date']) {
            $errors[] = 'Renewal date must be on or after start date.';
        }

        $validStatuses = ['Active', 'Expired', 'Pending Renewal'];
        if (!in_array($data['status'], $validStatuses, true)) {
            $errors[] = 'Invalid status selected.';
        }

        return $errors;
    }

    private function isValidDate(string $date): bool
    {
        $d = DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
    }
}
