<?php
/**
 * Auth Middleware – session guard and RBAC helper
 */

class AuthMiddleware
{
    /**
     * Require a logged-in user; redirect to login otherwise.
     */
    public static function requireAuth(): void
    {
        if (empty($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/auth/login');
            exit;
        }
    }

    /**
     * Require one of the given roles; abort with 403 otherwise.
     */
    public static function requireRole(string ...$roles): void
    {
        self::requireAuth();

        if (!in_array($_SESSION['user_role'] ?? '', $roles, true)) {
            http_response_code(403);
            include ROOT . '/views/partials/403.php';
            exit;
        }
    }

    /**
     * Redirect already-authenticated users away from the login page.
     */
    public static function guestOnly(): void
    {
        if (!empty($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }
    }

    /**
     * Verify CSRF token submitted with POST requests.
     */
    public static function verifyCsrf(): void
    {
        $token = $_POST['csrf_token'] ?? '';
        if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
            http_response_code(403);
            die('Invalid CSRF token. Please go back and try again.');
        }
    }

    /**
     * Check whether the current user has one of the given roles.
     */
    public static function hasRole(string ...$roles): bool
    {
        return in_array($_SESSION['user_role'] ?? '', $roles, true);
    }

    public static function currentUserId(): int
    {
        return (int) ($_SESSION['user_id'] ?? 0);
    }

    public static function currentUserRole(): string
    {
        return $_SESSION['user_role'] ?? '';
    }
}
