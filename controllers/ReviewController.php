<?php
/**
 * Review Controller
 * Handles song validation tasks (approve/reject) and review submission/management.
 */

require_once __DIR__ . '/../models/music.php';
require_once __DIR__ . '/../models/reviews.php';

class ReviewController {
    public Music $music;
    public Reviews $reviews;

    /**
     * Constructor - Initializes the Music and Reviews models.
     */
    public function __construct() {
        $pdo = Database::getInstance();
        $this->music = new Music($pdo);
        $this->reviews = new Reviews($pdo);
    }

    /**
     * Shows the validation task for a reviewer.
     * Assigns a new song if none is currently assigned.
     * @param Twig\Environment $twig The Twig environment instance.
     * @return void
     */
    public function showValidationTask($twig): void {
        $validatorId = $_SESSION['user_id'];
        // 1. Check if the reviewer has an assigned task pending
        $assignedSong = $this->music->findAssignedValidationTask($validatorId);

        if ($assignedSong) {
            $songDetails = $assignedSong;
        } else {
            // 2. If no task, assign a new pending song
            $newlyAssignedSongId = $this->music->assignValidationTask($validatorId);
            if ($newlyAssignedSongId) {
                $songDetails = $this->music->findById($newlyAssignedSongId);
            } else {
                // 3. No songs left to validate
                echo $twig->render('no_songs_to_validate.html.twig');
                return;
            }
        }
        // Render the validation page with song details
        echo $twig->render('review_validate.html.twig', ['song' => $songDetails]);
    }

    /**
     * Submits a new review for a song.
     * @return void
     */
    public function submitReview(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id'])) {
            header('Location: /MusicHub/public/index.php?page=home');
            exit;
        }
        // Access restricted to 'reviewer' role
        if (($_SESSION['role'] ?? '') !== 'reviewer') {
            $_SESSION['flash'] = [
                'message' => "Nemáte oprávnění psát recenze.",
                'type' => 'danger'
            ];
            header('Location: /MusicHub/public/index.php?page=home');
            exit;
        }

        $rawComment = $_POST["comment"] ?? '';

        // Initialize HTML Purifier for XSS sanitization
        $config = \HTMLPurifier_Config::createDefault();
        $config->set('HTML.Allowed', 'p,b,strong,i,em,ul,ol,li,br,h1,h2,h3,h4,blockquote'); // Allowed tags
        $purifier = new \HTMLPurifier($config);

        // Decode HTML entities before purification
        $decodedComment = html_entity_decode($rawComment);
        $cleanComment = $purifier->purify($decodedComment);

        $reviewData = [
            'id_reviewer' => $_SESSION['user_id'],
            'id_song' => $_POST['song_id'],
            'rating_quality' => $_POST['rating_quality'],
            'rating_originality' => $_POST['rating_originality'],
            'rating_lyrics' => $_POST['rating_lyrics'],
            'comment' => $cleanComment,
            'created_at' => date("Y-m-d H:i:s")
        ];

        try {
            // Note: Model is initialized inside the try block for dependency injection consistency
            $reviewModel = new reviews(Database::getInstance());
            $reviewModel->createReview($reviewData);

            $_SESSION['flash'] = [
                'message' => "Recenze byla úspěšně uložena a čeká na schválení administrátorem.",
                'type' => 'success'
            ];
        } catch (PDOException $e) {
            error_log("Chyba při ukládání recenze: " . $e->getMessage());
            $_SESSION['flash'] = [
                'message' => "Chyba při ukládání recenze.",
                'type' => 'danger'
            ];
        }

        // Redirect back to the song detail page
        header('Location: /MusicHub/public/index.php?page=song&id=' . $_POST['song_id']);
        exit;
    }

    /**
     * Deletes a review (only pending reviews by the current user can be deleted).
     * @return void
     */
    public function delete(): void {
        // Corrected condition check for POST and review_id presence
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['review_id'])) {
            $reviewId = (int)$_POST['review_id'];
            $userId = $_SESSION['user_id'];

            // Delete review only if it's not approved and belongs to the user
            if ($this->reviews->deleteReview($reviewId, $userId)) {
                $_SESSION['flash'] = [
                    'message' => "Recenze byla smazána.",
                    'type' => 'success'
                ];
            } else {
                $_SESSION['flash'] = [
                    'message' => "Nelze smazat recenzi (možná již byla schválena nebo nejste autorem).",
                    'type' => 'danger'
                ];
            }
        }
        header('Location: /MusicHub/public/index.php?page=profile');
        exit;
    }

    /**
     * Shows the form to edit a pending review.
     * @param Twig\Environment $twig The Twig environment instance.
     * @return void
     */
    public function showEditForm($twig): void {
        $reviewId = (int)($_GET['id'] ?? 0);
        $review = $this->reviews->getById($reviewId);

        // Security check: review must exist, belong to the user, and must not be approved
        if (!$review || $review['id_reviewer'] !== ($_SESSION['user_id'] ?? null) || $review['status'] === 'approved') {
            $_SESSION['flash'] = [
                'message' => "Tuto recenzi nelze upravit.",
                'type' => 'danger'
            ];
            header('Location: /MusicHub/public/index.php?page=profile');
            exit;
        }
        echo $twig->render('edit_review.html.twig', ['review' => $review]);
    }

    /**
     * Processes the submission of an edited review.
     * @return void
     */
    public function processEdit(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /MusicHub/public/index.php?page=profile');
            exit;
        }

        $rawComment = $_POST["comment"] ?? '';

        // Sanitization using HTML Purifier
        $config = \HTMLPurifier_Config::createDefault();
        $config->set('HTML.Allowed', 'p,b,strong,i,em,ul,ol,li,br,h1,h2,h3,h4,blockquote');
        $purifier = new \HTMLPurifier($config);

        $decodedComment = html_entity_decode($rawComment);
        $cleanComment = $purifier->purify($decodedComment);

        $reviewData = [
            'id_review' => $_POST['review_id'],
            'id_reviewer' => $_SESSION['user_id'], // Ensure the logged-in user is the one submitting the edit
            'id_song' => $_POST['song_id'],
            'rating_quality' => $_POST['rating_quality'],
            'rating_originality' => $_POST['rating_originality'],
            'rating_lyrics' => $_POST['rating_lyrics'],
            'comment' => $cleanComment
        ];

        try {
            $reviewModel = new reviews(Database::getInstance());
            // This method should handle setting the status back to 'pending' if it was rejected/needs re-approval
            $reviewModel->updateReview($reviewData);

            $_SESSION['flash'] = [
                'message' => "Recenze byla úspěšně aktualizována a čeká na nové schválení.",
                'type' => 'success'
            ];
        } catch (PDOException $e) {
            error_log("Chyba při aktualizaci recenze: " . $e->getMessage());
            $_SESSION['flash'] = [
                'message' => "Chyba při aktualizaci recenze.",
                'type' => 'danger'
            ];
        }
        header('Location: /MusicHub/public/index.php?page=profile');
        exit;
    }
}