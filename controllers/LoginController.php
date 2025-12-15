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
     * @param Twig\Environment $twig The Twig environment instance.
     * @return void
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
        // Clear errors after displaying them
        unset($_SESSION['errors']);
    }

    /**
     * Process user login attempt.
     * @return void
     */
    public function login(): void
    {
        // Check for POST request method
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: /MusicHub/public/index.php?page=login");
            exit;
        }

        $errors = [];
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        // CSRF Token validation
        if (!isset($_POST['csrf']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf'])) {
            $errors['general'] = "Chyba CSRF tokenu.";
        }

        // Attempt to authenticate the user
        if (empty($errors)) {
            $user = $this->userModel->findByEmail($email);

            if ($user && password_verify($password, $user['password_hash'])) {

                // Check for 'banned' status
                if ($user['role'] === 'banned') {
                    $errors['general'] = 'Váš účet byl zablokován administrátorem.';
                }
                else {
                    // LOGIN SUCCESS
                    // Set session variables
                    $_SESSION['user_id'] = $user['id_user'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['role'] = $user['role'];

                    // Set success flash message
                    $_SESSION['flash'] = [
                        'message' => "Vítejte, " . $user['username'] . "! Úspěšné přihlášení.",
                        'type' => 'success'
                    ];

                    // Redirect to home page
                    header("Location: /MusicHub/public/index.php?page=home");
                    exit;
                }

            } else {
                // Invalid credentials
                $errors['general'] = "Neplatný email nebo heslo.";
            }
        }

        // --- ERROR HANDLING BLOCK ---

        // Store errors for the form display
        $_SESSION['errors'] = $errors;

        // Set flash message for the general error
        $_SESSION['flash'] = [
            'message' => $errors['general'] ?? "Chyba přihlášení. Zkontrolujte údaje.", // Use specific error or a default one
            'type' => 'danger'
        ];

        // Redirect back to the login form
        header("Location: /MusicHub/public/index.php?page=login");
        exit;
    }
}