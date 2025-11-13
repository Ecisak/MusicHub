<?php
/**
 * Validation Controller
 * Handles AJAX validation requests for form fields
 */

require_once __DIR__ . '/../models/user.php';
require_once __DIR__ . '/../config/database.php';

class ValidationController
{
    /**
     * Main validation endpoint - routes to specific validators
     */
    public function validate(): void
    {
        // Enable error reporting for debugging
        error_reporting(E_ALL);
        ini_set('display_errors', 1);

        // Set JSON response header
        header('Content-Type: application/json');

        try {
            // Get validation parameters
            $field = $_POST['field'] ?? '';
            $value = trim($_POST['value'] ?? '');

            $response = ['valid' => true, 'message' => ''];

            // Route to appropriate validator based on field type
            switch ($field) {
                case 'email':
                    $response = $this->validateEmail($value);
                    break;
                case 'username':
                    $response = $this->validateUsername($value);
                    break;
                case 'password':
                    $response = $this->validatePassword($value);
                    break;
                case 'password_confirm':
                    $password = $_POST['password'] ?? '';
                    $response = $this->validatePasswordConfirm($value, $password);
                    break;
            }

            echo json_encode($response);
        } catch (Exception $e) {
            echo json_encode(['valid' => false, 'message' => 'Chyba: ' . $e->getMessage()]);
        }
        exit;
    }

    /**
     * Validate email address
     */
    private function validateEmail(string $email): array
    {
        // Check if email is provided
        if (empty($email)) {
            return ['valid' => false, 'message' => 'Email je povinný'];
        }

        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['valid' => false, 'message' => 'Neplatný formát emailu'];
        }

        // Check if email is already registered
        $user = new User(Database::getInstance());
        if ($user->exists($email)) {
            return ['valid' => false, 'message' => 'Email je již zaregistrován'];
        }

        return ['valid' => true, 'message' => ''];
    }

    /**
     * Validate username
     */
    private function validateUsername(string $username): array
    {
        // Check if username is provided
        if (empty($username)) {
            return ['valid' => false, 'message' => 'Uživatelské jméno je povinné'];
        }

        // Check minimum length
        if (strlen($username) < 3) {
            return ['valid' => false, 'message' => 'Uživatelské jméno musí mít alespoň 3 znaky'];
        }

        // Check maximum length
        if (strlen($username) > 20) {
            return ['valid' => false, 'message' => 'Uživatelské jméno může mít maximálně 20 znaků'];
        }

        // Check allowed characters (alphanumeric and underscore)
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
            return ['valid' => false, 'message' => 'Uživatelské jméno může obsahovat pouze písmena, číslice a podtržítko'];
        }

        // Check if username is already taken
        $user = new User(Database::getInstance());
        if ($user->usernameExists($username)) {
            return ['valid' => false, 'message' => "Uživatelské jméno je již obsazeno!"];
        }

        return ['valid' => true, 'message' => ''];
    }

    /**
     * Validate password strength
     */
    private function validatePassword(string $password): array
    {
        // Check if password is provided
        if (empty($password)) {
            return ['valid' => false, 'message' => 'Heslo je povinné'];
        }

        // Check minimum length
        if (strlen($password) < 8) {
            return ['valid' => false, 'message' => 'Heslo musí mít alespoň 8 znaků'];
        }

        // Check for at least one digit
        if (!preg_match('/\d/', $password)) {
            return ['valid' => false, 'message' => 'Heslo musí obsahovat alespoň 1 číslo'];
        }

        // Check for at least one uppercase letter
        if (!preg_match('/[A-Z]/', $password)) {
            return ['valid' => false, 'message' => 'Heslo musí obsahovat Velké písmeno'];
        }

        // Check for at least one lowercase letter
        if (!preg_match('/[a-z]/', $password)) {
            return ['valid' => false, 'message' => 'Heslo musí obsahovat malé písmeno'];
        }

        return ['valid' => true, 'message' => ''];
    }

    /**
     * Validate password confirmation matches
     */
    private function validatePasswordConfirm(string $confirm, string $password): array
    {
        // Check if confirmation is provided
        if (empty($confirm)) {
            return ['valid' => false, 'message' => 'Potvrzení hesla je povinné'];
        }

        // Check if passwords match
        if ($confirm !== $password) {
            return ['valid' => false, 'message' => 'Hesla se neshodují'];
        }

        return ['valid' => true, 'message' => ''];
    }
}