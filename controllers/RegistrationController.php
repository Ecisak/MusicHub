
<?php
/**
 * Registration Controller
 * Handles user registration logic
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/user.php';

class RegistrationController
{
    /**
     * Display the registration form
     */
    public function showForm($twig): void
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        echo $twig->render('register.html.twig', [
            'csrf_token' => $_SESSION['csrf_token'] ?? '',
            'errors' => $_SESSION['errors'] ?? [],
        ]);
        unset($_SESSION['errors']);
        // Generate CSRF token if not already set

    }

    /**
     * Process user registration
     */
    public function register(): void
    {
        // Only process POST requests
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: /MusicHub/public/index.php?page=register");
            exit;

        }

        $errors = [];
        $user = new User(Database::getInstance());

        // Sanitize input data
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $password_confirm = $_POST['password_confirm'] ?? '';

        // Validate CSRF token
        if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf'] ?? '')) {
            $errors[] = "CSRF token error";
        }

        // Validate password requirements
        if ($password !== $password_confirm) {
            $errors[] = "Passwords do not match";
        }
        if (strlen($password) < 8) {
            $errors[] = "Password must be at least 8 characters";
        }
        if (!preg_match('/\d/', $password)) {
            $errors[] = "Password must contain a number";
        }
        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = "Password must contain an uppercase letter";
        }
        if (empty($username)) {
            $errors[] = "Username is required";
        }
        if (empty($email)) {
            $errors[] = "Email is required";
        }
        if (empty($password)) {
            $errors[] = "Password is required";
        }
        if (empty($password_confirm)) {
            $errors[] = "Password confirmation is required";
        }

        // Check if user already exists
        if ($user->exists($email)) {
            $errors[] = "User with this email already exists";
        }

        // If there are validation errors, redirect back to form
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            header("Location: /MusicHub/public/index.php?page=register");
            exit;
        }

        // Create new user and redirect to log in
        $user->create($username, $email, $password);
        header("Location: /MusicHub/public/index.php?page=login");
        $_SESSION['flash'] = [
            'message' => "Registrace byla úspěšná. Můžete se přihlásit.",
            'type' => 'success'
        ];
        exit;
    }
}