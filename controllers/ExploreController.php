<?php
/**
 * Controller for handling music exploration and song detail display.
 */

require_once __DIR__ . '/../models/music.php';
require_once __DIR__ . '/../models/genre.php';
require_once __DIR__ . '/../models/reviews.php';

class ExploreController {
    public Music $music;
    public Genre $genre;
    public Reviews $reviews;

    /**
     * Initializes the models with a shared database instance.
     */
    public function __construct() {
        $pdo = Database::getInstance();
        $this->music = new Music($pdo);
        $this->genre = new Genre($pdo);
        $this->reviews = new Reviews($pdo);
    }

    /**
     * Shows the main exploration page with a list of accepted songs.
     * Songs can be sorted based on GET parameters.
     * @param Twig\Environment $twig The Twig environment instance.
     * @return void
     */
    public function showExplore($twig): void {
        // Get sorting parameters, default to 'date' descending
        $sort = $_GET["sort"] ?? 'date';
        $dir = $_GET["dir"] ?? 'desc';

        // Retrieve all accepted songs, sorted
        $exploreSongs = $this->music->getAllAccepted($sort, $dir);

        echo $twig->render('explore.html.twig', [
            'exploreSongs' => $exploreSongs,
            'currentSort' => $sort,
            'currentDirection' => $dir
        ]);
    }

    /**
     * Shows the detail page for a specific song.
     * @param int $songId The ID of the song to display.
     * @param Twig\Environment $twig The Twig environment instance.
     * @return void
     */
    public function showSongDetail(int $songId, $twig): void {
        $song = $this->music->findById($songId);

        // Redirect if song is not found
        if (!$song) {
            header("Location: /MusicHub/public/index.php?page=explore");
            exit;
        }

        $genre = $this->genre->get($song['id_genre']);
        // Only fetch approved reviews for display
        $reviews = $this->reviews->getAllApprovedBySongId($songId);

        $hasReviewed = false;
        // Check if the currently logged-in user has already reviewed this song
        if (isset($_SESSION['user_id'])) {
            $hasReviewed = $this->reviews->hasUserReviewed($_SESSION['user_id'], $songId);
        }

        echo $twig->render('song.html.twig',[
            'song' => $song,
            'genre' => $genre,
            'reviews' => $reviews,
            'hasReviewed' => $hasReviewed
        ]);
    }
}