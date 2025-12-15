<?php
/**
 * Profile Controller
 * Handles displaying the user's profile page, including their uploaded music and reviews.
 */

REQUIRE_ONCE '../models/music.php';
REQUIRE_ONCE '../models/reviews.php';

Class ProfileController {
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
     * Renders the user profile page with associated data.
     * @param Twig\Environment $twig The Twig environment instance.
     * @return void
     */
    public function showProfile($twig): void {
        // Ensure user is logged in (checked by the front controller, but good practice here too)
        if (!isset($_SESSION['user_id'])) {
            header('Location: /MusicHub/public/index.php?page=login');
            exit;
        }

        $userId = $_SESSION['user_id'];
        $userRole = $_SESSION['role'];

        // Retrieve user's uploaded songs
        $mySongs = $this->music->getByUserId($userId);

        // Retrieve user's written reviews
        // This is only relevant if the user is a reviewer
        $myReviews = $this->reviews->getByUserId($userId);

        echo $twig->render('profile.html.twig', [
            'mySongs' => $mySongs,
            'myReviews' => $myReviews,
            'userRole' => $userRole
        ]);
    }


}