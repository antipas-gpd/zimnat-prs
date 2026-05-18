<?php
/**
 * Auth Controller – login / logout
 */

class AuthController
{
    private UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    // GET /auth/login
    public function login(): void
    {
        AuthMiddleware::guestOnly();
        $error = $_SESSION['flash_error'] ?? null;
        unset($_SESSION['flash_error']);
        include ROOT . '/views/auth/login.php';
    }

    // POST /auth/login
    public function doLogin(): void
    {
        AuthMiddleware::guestOnly();
        AuthMiddleware::verifyCsrf();

        $email    = strtolower(trim($_POST['email']   ?? ''));
        $password = trim($_POST['password'] ?? '');

        $user = $this->userModel->findByEmail($email);

        if (!$user || !$this->userModel->verifyPassword($password, $user['password'])) {
            $_SESSION['flash_error'] = 'Invalid email or password.';
            header('Location: ' . BASE_URL . '/auth/login');
            exit;
        }

        if (!$user['is_active']) {
            $_SESSION['flash_error'] = 'Your account has been deactivated. Contact the administrator.';
            header('Location: ' . BASE_URL . '/auth/login');
            exit;
        }

        // Regenerate session ID to prevent fixation
        session_regenerate_id(true);

        $_SESSION['user_id']    = $user['id'];
        $_SESSION['user_name']  = $user['full_name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role']  = $user['role'];

        header('Location: ' . BASE_URL . '/dashboard');
        exit;
    }

    // GET /auth/logout
    public function logout(): void
    {
        session_unset();
        session_destroy();
        header('Location: ' . BASE_URL . '/auth/login');
        exit;
    }
}
