<?php
require_once __DIR__ . '/../models/music.php';
require_once __DIR__ . '/../models/reviews.php';

class ReviewController {
    public Music $music;
    public Reviews $reviews;
    public function __construct() {
        $pdo = Database::getInstance();
        $this -> music = new Music($pdo);
        $this -> reviews = new Reviews($pdo);
    }
    public function showValidationTask($twig): void {
        $validatorId = $_SESSION['user_id'];
        $assignedSong = $this->music->findAssignedValidationTask($validatorId);

        if ($assignedSong) {
            $songDetails = $assignedSong;
        } else {
            $newlyAssignedSongId = $this->music->validate($validatorId);
            if ($newlyAssignedSongId) {
                $songDetails = $this->music->findById($newlyAssignedSongId);
            } else {
                echo $twig->render('no_songs_to_validate.html.twig');
                return;
            }
        }
        echo $twig->render('review_validate.html.twig', ['song' => $songDetails]);

    }

    public function submitReview(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id'])) {
            header('Location: /MusicHub/public/index.php?page=home');
            exit;
        }
        if (($_SESSION['role'] ?? '') !== 'reviewer') {
            $_SESSION['flash-message'] = "Nemáte oprávnění psát recenze.";
            header('Location: /MusicHub/public/index.php?page=home');
            exit;
        }

        $rawComment = $_POST["comment"] ?? '';

        $config = \HTMLPurifier_Config::createDefault();
        $config->set('HTML.Allowed', 'p,b,strong,i,em,ul,ol,li,br,h1,h2,h3,h4,blockquote');

        $purifier = new \HTMLPurifier($config);
        $decodedComment = html_entity_decode($rawComment);
        $cleanComment = $purifier->purify($decodedComment);

        $reviewData = [
            'id_reviewer' => $_SESSION['user_id'],
            'id_song' => $_POST['song_id'],
            'rating_quality' => $_POST['rating_quality'],
            'rating_originality' => $_POST['rating_originality'],
            'rating_lyrics' => $_POST['rating_lyrics'],
            'comment' => $cleanComment,
            'created_at' => date("Y-m-d H:i:s"
            )
        ];

        try {
            $reviewModel = new reviews(Database::getInstance());
            $reviewModel->createReview($reviewData);

            $_SESSION['flash-message'] = "Recenze byla úspěšně uložena!";
        } catch (PDOException $e) {
            error_log("Chyba při ukládání recenze: " . $e->getMessage());
            $_SESSION['flash-message'] = "Chyba při ukládání recenze.";
        }
        header('Location: /MusicHub/public/index.php?page=song&id=' . $_POST['song_id']);
        exit;
    }
}