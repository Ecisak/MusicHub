<?php
/**
 * Main Entry Point - Front Controller
 * Handles routing for the MusicHub application
 */
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Start session for CSRF protection and error handling
session_start();

// Load required controllers
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../controllers/RegistrationController.php';
require_once __DIR__ . '/../controllers/LoginController.php';
require_once __DIR__ . '/../controllers/ValidationController.php';
require_once __DIR__ . '/../controllers/LogoutController.php';
require_once __DIR__ . '/../controllers/AddMusicController.php';

// Get the requested page from GET parameter, default to 'home'
$page = $_GET['page'] ?? 'home';
$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../templates');
$twig = new \Twig\Environment($loader, [
    'cache' => false
]);
$twig->addGlobal('isLoggedIn', isset($_SESSION['user_id']));
$twig->addGlobal('username', $_SESSION['username'] ?? '');

// Route requests to appropriate controllers
switch ($page) {
    case 'register':
        $controller = new RegistrationController();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // POST request - process registration
            $controller->register();
        } else {
            // GET request - display registration form
            $controller->showForm($twig);
        }
        break;

    case 'validate':
        // AJAX validation endpoint
        $controller = new ValidationController();
        $controller->validate();
        break;

    case 'login':
        $controller = new LoginController();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // POST request - process login
            $controller->login();
        } else {
            // GET request - display login form
            $controller->showForm($twig);
        }
        break;

    case 'logout':
        $controller = new LogoutController();
        $controller->logout();
        break;

    case 'add':
        $controller = new AddMusicController();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->addMusic();
        } else {
            $controller->showForm($twig);
        }
        break;
    case 'home':
    default:
        // Display home page

        echo $twig->render('homepage.html.twig');
        break;
}