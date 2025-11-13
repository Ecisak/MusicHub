<?php
/**
 * Logout Controller
 * Handles user logout and session destruction
 */


class LogoutController
{
    /**
     * Log out the user and destroy session
     */
    public function logout(): void
    {
        // Clear all session variables
        $_SESSION = [];

        // Delete session cookie if cookies are enabled
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

        // Destroy the session
        session_destroy();

        // Redirect to login page
        header("Location: /MusicHub/public/index.php?page=login");
        exit;
    }
}