<?php
class Reviews {
    private PDO $db;
    public function __construct(PDO $db) {
        $this->db = $db;
    }

    function getAllBySongId(int $songId) {
        $stmt = $this->db->prepare("SELECT * FROM `reviews` JOIN users ON reviews.id_reviewer = users.id_user
    WHERE reviews.id_song = :song_id 
    AND reviews.status = 'approved'
    ORDER BY reviews.created_at DESC");
        $stmt->execute(['song_id' => $songId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createReview(array $data) {
        $stmt = $this->db->prepare("
        INSERT INTO `reviews` (id_reviewer, id_song, rating_quality, rating_originality, rating_lyrics, comment, created_at)
        VALUES (:id_reviewer, :id_song, :rating_quality, :rating_originality, :rating_lyrics, :comment, :created_at)
        ");
        $stmt->execute($data);
    }
    public function hasUserReviewed(int $userId, int $songId): bool {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM reviews WHERE id_reviewer = :uid AND id_song = :sid");
        $stmt->execute(['uid' => $userId, 'sid' => $songId]);
        return $stmt->fetchColumn() > 0;
    }

    public function getAllPending() {
        $stmt = $this->db->prepare("
            SELECT 
                reviews.*, 
                users.username as reviewer_name,
                songs.title as song_title
            FROM reviews
            JOIN users ON reviews.id_reviewer = users.id_user
            JOIN songs ON reviews.id_song = songs.id_song
            WHERE reviews.status = 'pending'
            ORDER BY reviews.created_at ASC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateStatus(int $reviewId, string $status) {
        $stmt = $this->db->prepare("UPDATE reviews SET status = :status WHERE id_review = :id");
        $stmt->execute(['status' => $status, 'id' => $reviewId]);
    }
}





/*
 *
ALTER TABLE reviews
ADD UNIQUE KEY `unique_user_song` (`id_reviewer`, `id_song`);

ALTER TABLE reviews
ADD COLUMN status ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending';
 */