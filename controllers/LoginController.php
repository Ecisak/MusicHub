<?php


session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/user.php';


class LoginController {

    private $userModel;
    public function showForm() {
        require __DIR__ . '/../views/login.php';
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
    }

    public function __construct() {
        $pdo = Database::getInstance();
        $this->userModel = new User($pdo);
    }

    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->showForm();
            return;
        }
        $errors = [];
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        //csrf check
        if (!isset($_POST['csrf']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf'])) {
            $errors[] = "CSRF token error";
        }

        // validation
        if (empty($errors)) {
            $user = $this->userModel->findByEmail($email);

            if ($user && password_verify($password, $user['password_hash'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                header("Location: /MusicHub/public/index.php?page=home");
                exit;
            }
            else {
                $errors[] = "Invalid email or password";
            }

        }
        $_SESSION['errors'] = $errors;
        $this->showForm();


    }



}