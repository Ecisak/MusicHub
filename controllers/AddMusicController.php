<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/genre.php';
class AddMusicController {
    public function showForm($twig):void {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        $genre = new genre(Database::getInstance());
        $genres = $genre->getAll();
        echo $twig->render('add_music.html.twig', [
            'csrf_token' => $_SESSION['csrf_token'] ?? '',
            'errors' => $_SESSION['errors'] ??'',
            'genres' => $genres ??[]
        ]);
        unset($_SESSION['errors']);



    }
}