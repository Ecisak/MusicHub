<?php
/**
 * Logout Controller
 * Handles user logout and session destruction
 */


class LogoutController
{
    /**
     * Log out the user and destroy session.
     * @return void
     */
    public function logout(): void
    {
        // 1. Preserve user ID (optional, but harmless)
        $userId = $_SESSION['user_id'] ?? null;

        // 2. Clear all session variables
        $_SESSION = [];

        // 3. Clear the session cookie (the most critical step for proper logout)
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }

        // 4. Destroy the session data on the server
        session_destroy();

        // 5. Redirect to login page with a status parameter
        header("Location: /MusicHub/public/index.php?page=login&status=loggedout");
        exit;
    }
}