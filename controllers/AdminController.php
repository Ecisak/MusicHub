<?php
/**
 * Controller for handling administrative tasks:
 * - Review approval/rejection
 * - User management (role change, ban/unban)
 * - Music deletion
 */

require_once __DIR__ . '/../models/reviews.php';
require_once __DIR__ . '/../models/user.php';
require_once __DIR__ . '/../models/music.php';

class AdminController {
    private reviews $reviewModel;
    private music $musicModel; // This model is only used in deleteMusic, but kept for clarity
    private user $userModel;


    public function __construct() {
        // Initialize models with a shared database instance
        $this->reviewModel = new reviews(Database::getInstance());
        $this->userModel = new user(Database::getInstance());
        // $this->musicModel is initialized only when needed (e.g., in deleteMusic)
    }

    /**
     * Shows the list of reviews pending administrator approval.
     * @param Twig\Environment $twig The Twig environment.
     * @return void
     */
    public function showPendingReviews($twig): void {
        $role = $_SESSION['role'] ?? '';
        // Role check: Only Admin and Superadmin can view this page
        if ($role !== 'admin' && $role !== 'superadmin') {
            header('Location: /MusicHub/public/index.php?page=home');
            exit;
        }
        $pendingReviews = $this->reviewModel->getAllPending();
        echo $twig->render('admin_reviews.html.twig', [
            'reviews' => $pendingReviews,
        ]);
    }

    /**
     * Processes the approval or rejection of a review.
     * @return void
     */
    public function processReview(): void {
        $role = $_SESSION['role'] ?? '';
        // Role check
        if ($role  !== 'admin' && $role !== 'superadmin') {
            die('Access denied');
        }
        if (isset($_POST['review_id'], $_POST['action'])) {
            $status = ($_POST['action'] === 'approve') ? 'approved' : 'rejected';
            $this->reviewModel->updateStatus((int)$_POST['review_id'], $status);
            $_SESSION['flash'] = [
                'message' => "Recenze byla zpracována.",
                'type' => 'success'
            ];
        }
        header('Location: /MusicHub/public/index.php?page=admin_reviews');
        exit;
    }

    /**
     * Shows the list of all users for administration.
     * @param Twig\Environment $twig The Twig environment.
     * @return void
     */
    public function showUsers($twig): void {
        $role = $_SESSION['role'] ?? '';
        // Role check: Admin and Superadmin access
        if ($role !== 'admin' && $role !== 'superadmin') {
            $_SESSION['flash'] = [
                'message' => 'na tuto stránku nemáte přístup',
                'type' => 'danger'
            ];
            header('Location: /MusicHub/public/index.php?page=home');
            exit;
        }

        $users = $this->userModel->getAllUsers();
        // The list of roles available for assignment (subject to permissions in updateUserRole)
        $availableRoles = ['guest', 'author', 'reviewer', 'admin', 'superadmin'];
        echo $twig->render('admin_users.html.twig', [
            'users' => $users,
            'availableRoles' => $availableRoles,
            'user_role' => $role // Pass current user's role for template logic
        ]);
    }

    /**
     * Internal function to determine if the current user can manage the target user.
     * @param array $targetUser The user array being managed.
     * @return bool True if management is allowed, false otherwise.
     */
    private function canManageUser(array $targetUser): bool {
        $myRole = $_SESSION['role'] ?? '';
        $targetRole = $targetUser['role'];

        // Cannot manage Superadmin
        if ($targetRole === 'superadmin') {
            return false;
        }

        // Cannot manage Admin unless current user is Superadmin
        if ($targetRole === 'admin' && $myRole !== 'superadmin') {
            return false;
        }

        // Cannot manage self
        if ($targetUser['id_user'] == ($_SESSION['user_id'] ?? null)) {
            return false;
        }

        return true;
    }

    /**
     * Updates the role of a specified user.
     * @return void
     */
    public function updateUserRole(): void {
        $myRole = $_SESSION['role'] ?? '';
        if ($myRole !== 'admin' && $myRole !== 'superadmin') {
            die('Access denied');
        }

        if (isset($_POST['user_id'], $_POST['new_role'])) {
            $userId = (int)$_POST['user_id'];
            $newRole = $_POST['new_role'];

            $targetUser = $this->userModel->getById($userId);

            // Cannot manage protected roles
            if (!$this->canManageUser($targetUser)) {
                $_SESSION['flash'] = [
                    'message' => 'Nemáte oprávnění měnit role tohoto uživatele.',
                    'type' => 'danger'
                ];
                header('Location: /MusicHub/public/index.php?page=admin_users');
                exit;
            }

            // Cannot grant higher roles than self (e.g., Admin cannot grant Superadmin)
            if (($newRole === 'superadmin' || $newRole === 'admin') && $myRole !== 'superadmin') {
                $_SESSION['flash'] = [
                    'message' => 'Nemáte oprávnění udělovat vyšší role než je ta Vaše.',
                    'type' => 'danger'
                ];
                header('Location: /MusicHub/public/index.php?page=admin_users');
                exit;
            }

            $this->userModel->updateRole($userId, $newRole);

            $_SESSION['flash'] = [
                'message' => 'Role uživatele byla změněna na: ' . $newRole,
                'type' => 'success'
            ];
            header('Location: /MusicHub/public/index.php?page=admin_users');
            exit;
        }
    }

    /**
     * Bans or unbans a user.
     * @return void
     */
    public function banUser(): void {
        $myRole = $_SESSION['role'] ?? '';
        if ($myRole !== 'admin' && $myRole !== 'superadmin') {
            die('Access denied');
        }

        if (isset($_POST['user_id'], $_POST['new_role'])) {
            $userId = (int)$_POST['user_id'];
            $newRole = $_POST['new_role'];

            $targetUser = $this->userModel->getById($userId);

            // Cannot ban protected roles
            if (!$this->canManageUser($targetUser)) {
                $_SESSION['flash'] = [
                    'message' => "Nemáte oprávnění zabanovat tohoto uživatele",
                    'type' => 'danger'
                ];
                header('Location: /MusicHub/public/index.php?page=admin_users');
                exit;
            }

            $this->userModel->updateRole($userId, $newRole);

            if ($newRole === 'banned') {
                $_SESSION['flash'] = [
                    'message' => 'Uživatel dostal ban.',
                    'type' => 'success'
                ];
            } else {
                $_SESSION['flash'] = [
                    'message' => 'Uživatel byl odblokován.',
                    'type' => 'success'
                ];
            }

            header('Location: /MusicHub/public/index.php?page=admin_users');
            exit;
        }
    }

    /**
     * Deletes a song and associated data (restricted to Admin).
     * @return void
     */
    public function deleteMusic(): void {
        if (($_SESSION['role'] ?? '') !== 'admin') {
            die('Access denied');
        }
        if (isset($_POST['song_id'])) {
            $musicModel = new Music(Database::getInstance());
            if ($musicModel->delete($_POST['song_id'])) {
                $_SESSION['flash'] = [
                    'message' => "Skladba byla smazána.",
                    'type' => 'success'
                ];
            } else {
                $_SESSION['flash'] = [
                    'message' => "Chyba při mazání skladby.",
                    'type' => 'danger'
                ];
            }
            header('Location: /MusicHub/public/index.php?page=explore');
            exit;
        }
    }
}