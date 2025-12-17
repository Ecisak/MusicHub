<?php
/**
 * Main Entry Point - Front Controller
 * Handles routing for the MusicHub application
 */

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

// Configuration for error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Starts a session for CSRF protection, error handling, and user data
session_start();

// Check for successful logout status in query parameters
if (isset($_GET['status']) && $_GET['status'] === 'loggedout') {
    $_SESSION['flash'] = [
        'message' => "Úspěšně odhlášen.",
        'type' => 'success'
    ];
}

// Load Composer dependencies and application controllers/models
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../controllers/RegistrationController.php';
require_once __DIR__ . '/../controllers/LoginController.php';
require_once __DIR__ . '/../controllers/ValidationController.php';
require_once __DIR__ . '/../controllers/LogoutController.php';
require_once __DIR__ . '/../controllers/AddMusicController.php';
require_once __DIR__ . '/../controllers/ReviewController.php';
require_once __DIR__ . '/../controllers/ExploreController.php';
require_once __DIR__ . '/../models/music.php';
require_once __DIR__ . '/../controllers/AdminController.php';
require_once __DIR__ . '/../controllers/ProfileController.php';
require_once __DIR__ . '/../controllers/ApiController.php';


// Get the requested page from GET parameter, default to 'home'
$page = $_GET['page'] ?? 'home';

// Initialize Twig environment
$loader = new FilesystemLoader(__DIR__ . '/../templates');
$pdo = Database::getInstance();
$music = new Music($pdo);
$twig = new Environment($loader, [
    'cache' => false
]);

// Add global variables to Twig for use in all templates
$twig->addGlobal('current_page', $page);
$twig->addGlobal('isLoggedIn', isset($_SESSION['user_id']));
$twig->addGlobal('username', $_SESSION['username'] ?? '');
$twig->addGlobal('pending_songs_count', $music->getPendingCount());
$twig->addGlobal('user_role', $_SESSION['role'] ?? '');
$twig->addGlobal('current_user_id', $_SESSION['user_id'] ?? 0);

// Flash message handling
$flash = [
    'message' => '',
    'type' => 'info' // Default type if no message exists
];

// Check for the new 'flash' system
if (isset($_SESSION['flash'])) {
    $flash = $_SESSION['flash'];
}
// Check for the old/deprecated 'flash-message' system
elseif (isset($_SESSION['flash-message'])) {
    $flash['message'] = $_SESSION['flash-message'];
}

// Clear both flash keys after retrieval
unset($_SESSION['flash']);
unset($_SESSION['flash-message']);

// Pass the flash message array to Twig
$twig->addGlobal('flash', $flash);

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

        } else {
            // Redirect unauthenticated users to login page
            if (headers_sent($file, $line)) {
                die('Headers already sent in '.$file.' on line '.$line);
            }

            header('Location: /MusicHub/public/index.php?page=login');
            $_SESSION['flash'] = [
                'message' => "K přidání hudby se musíte přihlásit.",
                'type' => 'info'
            ];
            exit;
        }
        break;
    case 'review':
        // Access restricted to reviewers
        if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'reviewer'){
            $controller = new ReviewController();
            $controller->showValidationTask($twig);

        } else {
            // Redirect unauthorized users
            header('Location: /MusicHub/public/index.php?page=home');
            $_SESSION['flash'] = [
                'message' => "Na tuto stránku nemáte přístup.",
                'type' => 'danger'
            ];
            exit;
        }
        break;

    case 'process_validation':
        // Process song approval/rejection (POST request only, restricted to reviewer)
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
                $_SESSION['flash'] = [
                    'message' => "Písnička byla schválena k recenzi.",
                    'type' => 'success'
                ];
            } elseif ($action === 'reject') {
                $newStatus = 'rejected';
                $_SESSION['flash'] = [
                    'message' => "Písnička byla zamítnuta.",
                    'type' => 'success'
                ];
            }

            if ($newStatus) {
                $musicModel = new Music(Database::getInstance());
                $musicModel->updateValidationStatus($songId, $newStatus);
            }
        }

        header('Location: /MusicHub/public/index.php?page=review');
        exit;

    case 'explore':
        $controller = new ExploreController();
        $controller->showExplore($twig);
        break;

    case 'song':
        // Show song detail page based on ID
        if (isset($_GET['id'])) {
            $songId = (int)$_GET['id'];
            $controller = new ExploreController();
            $controller->showSongDetail($songId, $twig);
        } else {
            header('Location: /MusicHub/public/index.php?page=explore');
            exit;
        }
        break;

    case 'process_review':
        // Submit a new review
        $controller = new ReviewController();
        $controller->submitReview();
        break;


    case 'admin_reviews':
        // Show pending reviews for admin approval
        $controller = new AdminController();
        $controller->showPendingReviews($twig);
        break;

    case 'process_admin_review':
        // Approve or reject reviews by admin
        $controller = new AdminController();
        $controller->processReview();
        break;

    case 'profile':
        // Show user profile, requires login
        if (!isset($_SESSION['user_id'])) {
            header('Location: /MusicHub/public/index.php?page=login');
            $_SESSION['flash'] = [
                'message' => "Pro zobrazení profilu se musíte přihlásit.",
                'type' => 'info'
            ];
            exit;
        }
        $controller = new ProfileController();
        $controller->showProfile($twig);
        break;

    case 'edit_review':
        // Show form to edit an existing review, requires login
        if (!isset($_SESSION['user_id'])) {
            header('Location: /MusicHub/public/index.php?page=login');
            $_SESSION['flash'] = [
                'message' => "pro editaci recenze se musíte přihlásit nebo být recenzent.",
                'type' => 'success'
            ];
            exit;
        }
        $controller = new ReviewController();
        $controller->showEditForm($twig);
        break;

    case 'process_edit_review':
        // Process the submission of the edited review
        if (!isset($_SESSION['user_id'])) {
            header('Location: /MusicHub/public/index.php?page=login');
            $_SESSION['flash'] = [
                'message' => "pro editaci recenze se musíte přihlásit nebo být recenzent",
                'type' => 'danger'
            ];
            exit;
        }
        $controller = new ReviewController();
        $controller->processEdit();
        break;

    case 'delete_review':
        // Delete a review
        $controller = new ReviewController();
        $controller->delete();
        break;

    case 'admin_users':
        $role = $_SESSION['role'];
        // Access restricted to admin/superadmin
        if (!isset($_SESSION['user_id'])) {
            header('Location: /MusicHub/public/index.php?page=login');
            $_SESSION['flash'] = [
                'message' => "k přístupu na tuto stránku se musíte přihlásit.",
                'type' => 'info'
            ];
            exit;
        }

        if (($role !== 'admin' && $role !== 'superadmin' )) {
            header('Location: /MusicHub/public/index.php?page=home');
            $_SESSION['flash'] = [
                'message' => "Na tuto stránku nemáte přístup.",
                'type' => 'danger'
            ];
            exit;

        }
        $controller = new AdminController();
        $controller->showUsers($twig);
        break;


    case 'update_role':
        $role = $_SESSION['role'];
        // Update user role, restricted to admin/superadmin
        if ($role !== 'admin' && $role !== 'superadmin') {
            header('Location: /MusicHub/public/index.php?page=home');
            $_SESSION['flash'] = [
                'message' => "Na tuto akci nemáte oprávnění.",
                'type' => 'danger'
            ];
            exit;
        }
        $controller = new AdminController();
        $controller->updateUserRole();
        break;

    case  'ban_user':
        // Ban/Unban a user
        $controller = new AdminController();
        $controller->banUser();
        break;


    case 'admin_delete_music':
        // Admin delete music functionality
        $controller = new AdminController();
        $controller->deleteMusic();
        break;

    case 'api_leaderboard':
        // REST API endpoint for leaderboard data
        $controller = new ApiController();
        $controller->getLeaderboard();
        break;

    case 'api_leaderboard_sse':
        // Server-Sent Events endpoint for live leaderboard updates
        $controller = new ApiController();
        $controller->StreamLeaderboard();
        break;

    case 'leaderboard':
        // Display leaderboard page (uses JS for SSE data fetch)
        echo $twig->render('leaderboard.html.twig');
        break;

    case 'about':
        // Display 'About Us' page
        echo $twig->render('about.html.twig');
        break;

    case 'home':
    default:
        // Default route: Display home page
        echo $twig->render('homepage.html.twig');
        break;
}