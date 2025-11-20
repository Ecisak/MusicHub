<?php
require_once __DIR__ . '/../models/music.php';

class ReviewController {
    public Music $music;
    public function __construct() {
        $pdo = Database::getInstance();
        $this -> music = new Music($pdo);
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
}