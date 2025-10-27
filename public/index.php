<?php
session_start(); // session pro CSRF a chyby

require_once __DIR__ . '/../controllers/RegistrationController.php';
require_once __DIR__ . '/../controllers/LoginController.php';
require_once __DIR__ . '/../controllers/ValidationController.php';
require_once __DIR__ . '/../controllers/LogoutController.php';
// jednoduchý router podle GET parametru "page"
$page = $_GET['page'] ?? 'home';

switch ($page) {
    case 'register':
        $controller = new RegistrationController();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->register();  // POST → zpracovat registraci
        } else {
            $controller->showForm();  // GET → zobrazit formulář
        }
        break;
    case 'validate':
        $controller = new ValidationController();
        $controller->validate();
        break;
    case 'login':
        $controller = new LoginController();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller-> login();
        } else {
            $controller->showForm();
        }
        break;
    case 'logout':
        require_once __DIR__ . '/../controllers/LogoutController.php';
        $controller = new LogoutController();
        $controller->logout();
        break;

    default:
        echo "<h1>Vítejte v MusicHub</h1>";
        break;
}

