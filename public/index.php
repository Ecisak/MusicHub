<?php
/**
 * Main Entry Point - Front Controller
 * Handles routing for the MusicHub application
 */

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Starts a session for CSRF protection and error handling
session_start();

// Load required controllers
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../controllers/RegistrationController.php';
require_once __DIR__ . '/../controllers/LoginController.php';
require_once __DIR__ . '/../controllers/ValidationController.php';
require_once __DIR__ . '/../controllers/LogoutController.php';
require_once __DIR__ . '/../controllers/AddMusicController.php';
require_once __DIR__ . '/../controllers/ReviewController.php';
require_once __DIR__ . '/../controllers/ExploreController.php';

// Get the requested page from GET parameter, default to 'home'
$page = $_GET['page'] ?? 'home';
$loader = new FilesystemLoader(__DIR__ . '/../templates');
$twig = new Environment($loader, [
    'cache' => false
]);
$twig->addGlobal('isLoggedIn', isset($_SESSION['user_id']));
$twig->addGlobal('username', $_SESSION['username'] ?? '');
$flashMessage = '';
if (isset($_SESSION['flash-message'])) {
    $flashMessage = $_SESSION['flash-message'];
}
unset($_SESSION['flash-message']);

$twig->addGlobal('flashMessage', $flashMessage);
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
            // POST-request - process login
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
        if ($isLoggedIn = isset($_SESSION['user_id'])) {
            $controller = new AddMusicController();
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller->addMusic();
            } else {
                $controller->showForm($twig);
            }

        }else {
            if (headers_sent($file, $line)) {
                die('Headers already sent in '.$file.' on line '.$line);
            }
            $_SESSION['flash-message'] = "k přidání hudby se musíte přihlásit";
            header('Location: /MusicHub/public/index.php?page=login');
            exit;
        }
        break;
    case 'review':
        if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'reviewer'){
            $controller = new ReviewController();
            $controller->showValidationTask($twig);

        } else {
            $_SESSION['flash-message'] = 'na tuto stranku nemate pristup';
            header('Location: /MusicHub/public/index.php?page=home');
            exit;
        }
        break;

    case 'process_validation':

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['role']) || $_SESSION['role'] !== 'reviewer') {
            header('Location: /MusicHub/public/index.php?page=home');
            exit;
        }

        if (isset($_POST['id'], $_POST['action'])) {
            $songId = (int)$_POST['id'];
            $action = $_POST['action'];

            $newStatus = '';
            if ($action === "approve") {
                $newStatus = 'accepted';
                $_SESSION['flash_message'] = 'Písnička byla schválena k recenzi.';
            } elseif ($action === 'reject') {
                $newStatus = 'rejected';
                $_SESSION['flash_message'] = 'Písnička byla zamítnuta.';
            }

            if ($newStatus) {
                $musicModel = new Music(Database::getInstance());
                $musicModel->updateValidationStatus($songId, $newStatus); // Tvoje nová metoda
            }
        }

        header('Location: /MusicHub/public/index.php?page=review');
        exit;

    case 'explore':
        $controller = new ExploreController();
        $controller->showExplore($twig);
        break;


    case 'home':
    default:
        // Display home page

        echo $twig->render('homepage.html.twig');
        break;
}