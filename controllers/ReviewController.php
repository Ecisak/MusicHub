<?php
require_once __DIR__ . '/../models/music.php';

class ReviewController {
    public Music $music;
    public function __construct() {
        $pdo = Database::getInstance();
        $this -> music = new Music($pdo);
    }
    public function showValidationTask($twig): void
    {
        $validatorId = $_SESSION['user_id'];
        $assignedSongId = $this->music->validate($validatorId);

        if ($assignedSongId) {
            $songDetails = $this->music->findById($assignedSongId);
            echo $twig->render('review_validate.html.twig', [
                'song' => $songDetails
            ]);
        } else {
            echo $twig->render('no_songs_to_validate.html.twig', [
                'message' => 'Děkujeme, momentálně jsou zvalidovány všechny písničky'
            ]);
        }

    }
}