<?php
require_once __DIR__ . '/../models/reviews.php';

class AdminController {
    private reviews $reviewModel;

    public function __construct() {
        $this->reviewModel = new reviews(Database::getInstance());
    }

    public function showPendingReviews($twig) {
        if (($_SESSION['role'] ?? '') !== 'admin') {
            header('Location: /MusicHub/public/index.php?page=home');
            exit;
        }
        $pendingReviews = $this->reviewModel->getAllPending();
        echo $twig->render('admin_reviews.html.twig', [
            'reviews' => $pendingReviews,
        ]);
    }
    public function processReview() {
        if (($_SESSION['role'] ?? '') !== 'admin') {
            die('Access denied');
        }
        if (isset($_POST['review_id'], $_POST['action'])) {
            $status = ($_POST['action'] === 'approve') ? 'approved' : 'rejected';
            $this->reviewModel->updateStatus((int)$_POST['review_id'], $status);
            $_SESSION['flash-message'] = "Recenze byla zpracována.";
        }
        header('Location: /MusicHub/public/index.php?page=admin_reviews');
        exit;
    }
}