<?php
session_start(); // session pro CSRF a chyby

require_once __DIR__ . '/../controllers/RegistrationController.php';
// require_once __DIR__ . '/../controllers/LoginController.php'; // až budeš mít login
require_once __DIR__ . '/../controllers/ValidationController.php';
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
        // $controller = new LoginController();
        // podobně jako u registrace
        break;

    default:
        echo "<h1>Vítejte v MusicHub</h1>";
        break;
}

//TODO OPRAVIT VALIDATOR