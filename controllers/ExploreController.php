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
        $sort = $_GET["sort"] ?? 'date';
        $dir = $_GET["direction"] ?? 'desc';
        $exploreSongs = $this->music->getAllAccepted($sort, $dir);

        echo $twig->render('explore.html.twig', [
            'exploreSongs' => $exploreSongs,
            'currentSort' => $sort,
            'currentDirection' => $dir
        ]);
    }

    public function showSongDetail(int $songId, $twig) {
        $song = $this->music->findById($songId);
        $genre = $this->genre->get($song['id_genre']);
        $reviews = $this->reviews->getAllBySongId($songId);

        $hasReviewed = false;
        if (isset($_SESSION['user_id'])) {
            $hasReviewed = $this->reviews->hasUserReviewed($_SESSION['user_id'], $songId);
        }
        if ($song) {
            echo $twig->render('song.html.twig',[
                'song' => $song,
                'genre' => $genre,
                'reviews' => $reviews,
                'hasReviewed' => $hasReviewed
            ]);
        } else {
            header("Location: /MusicHub/public/index.php?page=explore");
            exit;
        }
    }
}