<?php
/**
 * User Controller – Admin only
 */

class UserController
{
    private UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    // GET /users/index
    public function index(): void
    {
        AuthMiddleware::requireRole('admin');

        $users     = $this->userModel->findAll();
        $pageTitle = 'Manage Users';
        include ROOT . '/views/admin/users/index.php';
    }

    // GET /users/create
    public function create(): void
    {
        AuthMiddleware::requireRole('admin');

        $errors   = $_SESSION['flash_errors'] ?? [];
        $formData = $_SESSION['flash_form']   ?? [];
        unset($_SESSION['flash_errors'], $_SESSION['flash_form']);

        $pageTitle = 'Create User';
        include ROOT . '/views/admin/users/create.php';
    }

    // POST /users/store
    public function store(): void
    {
        AuthMiddleware::requireRole('admin');
        AuthMiddleware::verifyCsrf();

        $data   = $this->extractPostData();
        $errors = $this->validate($data, isCreate: true);

        if ($this->userModel->emailExists($data['email'])) {
            $errors[] = 'Email address is already registered.';
        }

        if (!empty($errors)) {
            $_SESSION['flash_errors'] = $errors;
            $_SESSION['flash_form']   = $data;
            header('Location: ' . BASE_URL . '/users/create');
            exit;
        }

        $this->userModel->create($data);
        $_SESSION['flash_success'] = 'User created successfully.';
        header('Location: ' . BASE_URL . '/users/index');
        exit;
    }

    // GET /users/edit/{id}
    public function edit(?string $id): void
    {
        AuthMiddleware::requireRole('admin');

        $user     = $this->fetchUserOr404((int) $id);
        $errors   = $_SESSION['flash_errors'] ?? [];
        $formData = $_SESSION['flash_form']   ?? $user;
        unset($_SESSION['flash_errors'], $_SESSION['flash_form']);

        $pageTitle = 'Edit User';
        include ROOT . '/views/admin/users/edit.php';
    }

    // POST /users/update/{id}
    public function update(?string $id): void
    {
        AuthMiddleware::requireRole('admin');
        AuthMiddleware::verifyCsrf();

        $user   = $this->fetchUserOr404((int) $id);
        $data   = $this->extractPostData();
        $errors = $this->validate($data, isCreate: false);

        if ($this->userModel->emailExists($data['email'], $user['id'])) {
            $errors[] = 'Email address is already in use by another account.';
        }

        if (!empty($errors)) {
            $_SESSION['flash_errors'] = $errors;
            $_SESSION['flash_form']   = $data;
            header('Location: ' . BASE_URL . '/users/edit/' . $user['id']);
            exit;
        }

        $data['is_active'] = (int) ($data['is_active'] ?? 1);
        $this->userModel->update($user['id'], $data);
        $_SESSION['flash_success'] = 'User updated successfully.';
        header('Location: ' . BASE_URL . '/users/index');
        exit;
    }

    // POST /users/toggleActive/{id}
    public function toggleActive(?string $id): void
    {
        AuthMiddleware::requireRole('admin');
        AuthMiddleware::verifyCsrf();

        $user = $this->fetchUserOr404((int) $id);

        // Prevent admin from deactivating themselves
        if ($user['id'] === AuthMiddleware::currentUserId()) {
            $_SESSION['flash_error'] = 'You cannot deactivate your own account.';
            header('Location: ' . BASE_URL . '/users/index');
            exit;
        }

        $newState = !$user['is_active'];
        $this->userModel->setActive($user['id'], $newState);
        $msg = $newState ? 'User activated.' : 'User deactivated.';
        $_SESSION['flash_success'] = $msg;
        header('Location: ' . BASE_URL . '/users/index');
        exit;
    }

    // ── Helpers ───────────────────────────────────────────────

    private function fetchUserOr404(int $id): array
    {
        $user = $this->userModel->findById($id);
        if (!$user) {
            http_response_code(404);
            include ROOT . '/views/partials/404.php';
            exit;
        }
        return $user;
    }

    private function extractPostData(): array
    {
        return [
            'full_name' => trim($_POST['full_name'] ?? ''),
            'email'     => strtolower(trim($_POST['email'] ?? '')),
            'password'  => $_POST['password'] ?? '',
            'role'      => trim($_POST['role'] ?? ''),
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
        ];
    }

    private function validate(array $data, bool $isCreate): array
    {
        $errors = [];

        if (empty($data['full_name'])) $errors[] = 'Full name is required.';

        if (empty($data['email'])) {
            $errors[] = 'Email is required.';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Invalid email address.';
        }

        $validRoles = ['admin', 'policy_officer', 'viewer'];
        if (!in_array($data['role'], $validRoles, true)) {
            $errors[] = 'Please select a valid role.';
        }

        if ($isCreate) {
            if (empty($data['password'])) {
                $errors[] = 'Password is required.';
            } elseif (strlen($data['password']) < 8) {
                $errors[] = 'Password must be at least 8 characters.';
            }
        } elseif (!empty($data['password']) && strlen($data['password']) < 8) {
            $errors[] = 'New password must be at least 8 characters.';
        }

        return $errors;
    }
}
