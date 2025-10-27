<?php

session_start();

class LogoutController {
    public function logout() {
        $_SESSION = [];

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]);
        }
        session_destroy();
        header("Location: /MusicHub/public/index.php?page=login");
        exit;
    }
}