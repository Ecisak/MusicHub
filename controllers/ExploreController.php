<?php
require_once __DIR__ . '/../models/music.php';
require_once __DIR__ . '/../models/genre.php';
require_once __DIR__ . '/../models/reviews.php';
class ExploreController{
    public Music $music;
    public Genre $genre;
    public Reviews $reviews;
    public function __construct(){
        $pdo = Database::getInstance();
        $this->music = new Music($pdo);
        $this->genre = new Genre($pdo);
        $this->reviews = new Reviews($pdo);
    }
    public function showExplore($twig) {
        $exploreSongs = $this->music->getAllAccepted();
        echo $twig->render('explore.html.twig', ['exploreSongs' => $exploreSongs]);
    }

    public function showSongDetail(int $songId, $twig) {
        $song = $this->music->findById($songId);
        $genre = $this->genre->get($song['id_genre']);
        $reviews = $this->reviews->getAllBySongId($songId);
        if ($song) {
            echo $twig->render('song.html.twig',[
                'song' => $song,
                'genre' => $genre,
                'reviews' => $reviews
            ]);
        } else {
            header("Location: /MusicHub/public/index.php?page=explore");
            exit;
        }
    }
}