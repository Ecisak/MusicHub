<?php


session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/user.php';

class RegistrationController {

    public function showForm() {
        // CSRF token
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        require __DIR__ . '/../views/register.php';
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->showForm();
            return;
        }
        $errors = [];
        $user = new User(Database::getInstance());
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $password_confirm = $_POST['password_confirm'] ?? '';

        // CSRF
        if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf'] ?? '')) {
            $errors[] = "CSRF token error";
        }

        // validation
        if ($password !== $password_confirm) $errors[] = "passwords do not match";
        if (strlen($password) < 8) $errors[] = "password must be at least 8 characters";
        if (!preg_match('/\d/', $password)) $errors[] ="password must contain a number";
        if (!preg_match('/[A-Z]/', $password)) $errors[] ="password must contain an uppercase letter";
        if ($user->exists($email)) $errors[] = "user with this email already exists";
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            header("Location: /MusicHub/public/index.php?page=register");
            exit;
        }
        $user->create($username, $email, $password);
        header("Location: /MusicHub/public/index.php?page=login");
        exit;
    }

}
