<?php
/**
 * Login Controller
 * Handles user authentication
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/user.php';

class LoginController
{
    private User $userModel;

    /**
     * Constructor - Initialize user model
     */
    public function __construct()
    {
        $pdo = Database::getInstance();
        $this->userModel = new User($pdo);
    }

    /**
     * Display the login form
     */
    public function showForm($twig): void
    {
        // Generate CSRF token if not already set
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        echo $twig->render('login.html.twig', [
            'csrf_token' => $_SESSION['csrf_token'] ?? '',
            'errors' => $_SESSION['errors'] ?? []
        ]);
        unset($_SESSION['errors']);
    }

    /**
     * Process user login
     */
    public function login(): void
    {
        // Only process POST requests
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: /MusicHub/public/index.php?page=login");
            exit;
        }

        $errors = [];

        // Sanitize input data
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        // Validate CSRF token
        if (!isset($_POST['csrf']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf'])) {
            $errors[] = "CSRF token error";
        }

        // Authenticate user if no CSRF errors
        if (empty($errors)) {
            $user = $this->userModel->findByEmail($email);
            // Verify user exists and password is correct
            if ($user && password_verify($password, $user['password_hash'])) {
                // Set session variables for logged-in user
                $_SESSION['user_id'] = $user['id_user'];
                $_SESSION['username'] = $user['username'];

                // Redirect to home page
                header("Location: /MusicHub/public/index.php?page=home");
                exit;
            } else {
                $errors[] = "Invalid email or password";
            }
        }

        // Store errors and show form again
        $_SESSION['errors'] = $errors;
        header("Location: /MusicHub/public/index.php?page=login");
        exit;    }
}