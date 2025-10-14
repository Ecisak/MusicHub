<?php

require_once __DIR__ . '/../models/user.php';
require_once __DIR__ . '/../config/database.php';
class ValidationController {

    public function validate() {
    // Přidáme error reporting
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    
    header('Content-Type: application/json');
    
    try {
        $field = $_POST['field'] ?? '';
        $value = trim($_POST['value'] ?? '');

        $response = ['valid' => true, 'message' => ''];

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
    private function validateEmail(string $email): array {
        if (empty($email)) {
            return ['valid' => false, 'message' => 'Email je povinný'];
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['valid' => false, 'message' => 'Neplatný formát emailu'];
        }
        $user = new User(Database::getInstance());
        if ($user->exists($email)) {
            return ['valid' => false, 'message' => 'Email je již zaregistrován'];
        }
        return['valid' => true, 'message' => ''];
    }
    private function validateUsername(string $username): array {
        if (empty($username)) {
            return ['valid' => false, 'message' => 'Uživatelské jméno je povinné'];
        }
        if (strlen($username) < 3) {
            return['valid' => false, 'message' => 'Uživatelské jméno musí mít alespoň 3 znaky'];
        }
        if (strlen($username) > 20) {
            return['valid' => false, 'message' => 'Uživatelské jméno může mít maximálně 20 znaků'];
        }
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
            return ['valid' => false, 'message' => 'Uživatelské jméno může obsahovat pouze písmena, číslice a podtržítko'];
        }

        // kontrola unique
        $user = new User(Database::getInstance());
        if ($user->usernameExists($username)) {
            return ['valid' => false, 'message' => "Uživatelské jméno je již obsazeno!"];
        }
        return ['valid' => true, 'message' => ''];
    }
    private function validatePassword(string $password): array {
        if (empty($password)) {
            return ['valid' => false, 'message' => 'Heslo je povinné'];
        }
        if (strlen($password) < 8) {
            return ['valid' => false, 'message' => 'Heslo musí mít alespoň 8 znaků'];
        }
        if (!preg_match('/\d/', $password)) {
            return ['valid' => false, 'message' => 'Heslo musí obsahovat alespoň 1 číslo'];
        }
        if (!preg_match('/[A-Z]/', $password)) {
            return ['valid' => false, 'message' => 'Heslo musí obsahovat Velké písmeno'];
        }
        if (!preg_match('/[a-z]/', $password)) {
            return ['valid' => false, 'message' => 'heslo musí obsahovat malé písmeno'];
        }
        return ['valid' => true, 'message' => ''];
    }
    private function validatePasswordConfirm(string $confirm, string $password): array {
        if (empty($confirm)) {
            return ['valid' => false, 'message' => 'Potvrzení hesla je povinné'];
        }
        if ($confirm !== $password) {
            return ['valid' => false, 'message' => 'Hesla se neshodují'];
        }
        return ['valid' => true, 'message' => ''];
    }
}
