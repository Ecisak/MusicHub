<?php
/**
 * Model class for interacting with the 'reviews' database table.
 * Handles review creation, retrieval, status updates, and deletion.
 */
class Reviews
{
    private PDO $db;

    /**
     * Constructor.
     * @param PDO $db The PDO database connection instance.
     */
    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Retrieves all approved reviews for a specific song ID, including reviewer username.
     * @param int $songId The ID of the song.
     * @return array An array of approved review records.
     */
    public function getAllApprovedBySongId(int $songId): array
    {
        $stmt = $this->db->prepare("SELECT 
            reviews.*, 
            users.username as username
        FROM `reviews` 
        JOIN users ON reviews.id_reviewer = users.id_user
        WHERE reviews.id_song = :song_id 
        AND reviews.status = 'approved'
        ORDER BY reviews.created_at DESC");
        $stmt->execute(['song_id' => $songId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Creates a new review record.
     * @param array $data The review data.
     * @return bool True on success.
     */
    public function createReview(array $data): bool
    {
        $stmt = $this->db->prepare("
        INSERT INTO `reviews` (id_reviewer, id_song, rating_quality, rating_originality, rating_lyrics, comment, created_at)
        VALUES (:id_reviewer, :id_song, :rating_quality, :rating_originality, :rating_lyrics, :comment, :created_at)
        ");
        return $stmt->execute($data);
    }

    /**
     * Checks if a user has already submitted a review for a specific song.
     * @param int $userId The ID of the reviewer.
     * @param int $songId The ID of the song.
     * @return bool True if a review exists, false otherwise.
     */
    public function hasUserReviewed(int $userId, int $songId): bool
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM reviews WHERE id_reviewer = :uid AND id_song = :sid");
        $stmt->execute(['uid' => $userId, 'sid' => $songId]);
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Retrieves all reviews with status 'pending' for admin approval.
     * @return array An array of pending review records with song and reviewer names.
     */
    public function getAllPending(): array
    {
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

    /**
     * Updates the status of a specific review.
     * @param int $reviewId The ID of the review.
     * @param string $status The new status ('approved' or 'rejected').
     * @return bool True on success.
     */
    public function updateStatus(int $reviewId, string $status): bool
    {
        $stmt = $this->db->prepare("UPDATE reviews SET status = :status WHERE id_review = :id");
        return $stmt->execute(['status' => $status, 'id' => $reviewId]);
    }

    /**
     * Retrieves all reviews written by a specific user, along with associated song details.
     * @param int $userId The ID of the reviewer.
     * @return array An array of review records with song details.
     */
    public function getByUserId(int $userId): array
    {
        $stmt = $this->db->prepare(
            "SELECT 
                    reviews.*, 
                    songs.title as song_title,
                    songs.cover_image,
                    songs.author,
                    songs.filename
            FROM reviews
            JOIN songs ON reviews.id_song = songs.id_song
            WHERE reviews.id_reviewer = :id
            ORDER BY reviews.created_at DESC
            ");
        $stmt->execute(['id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Deletes a review. Only possible if the review belongs to the user and is not yet 'approved'.
     * @param int $reviewId The ID of the review.
     * @param int $userId The ID of the reviewer (for security check).
     * @return bool True if a row was deleted, false otherwise.
     */
    public function deleteReview(int $reviewId, int $userId): bool
    {
        $stmt = $this->db->prepare("
        DELETE FROM reviews 
        WHERE id_review = :rid 
          AND id_reviewer = :uid 
          AND status != 'approved'
    ");
        $stmt->execute(['rid' => $reviewId, 'uid' => $userId]);
        return $stmt->rowCount() > 0;
    }

    /**
     * Retrieves a single review by its ID.
     * @param int $reviewId The ID of the review.
     * @return array|false The review record or false if not found.
     */
    public function getById(int $reviewId): array|false {
        $stmt = $this->db->prepare(
            "SELECT * FROM reviews WHERE id_review = :id"
        );
        $stmt->execute(['id' => $reviewId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Updates an existing review's details and sets its status back to 'pending'.
     * @param array $data The updated review data (must include id_review and id_reviewer).
     * @return bool True on success.
     */
    public function updateReview(array $data): bool {
        $stmt = $this->db->prepare("
        UPDATE reviews
        SET rating_quality = :rating_quality,
            rating_originality = :rating_originality,
            rating_lyrics = :rating_lyrics,
            comment = :comment,
            status = 'pending'
        WHERE id_review = :rid AND id_reviewer = :uid
        ");
        return $stmt->execute([
            'rating_quality' => $data['rating_quality'],
            'rating_originality' => $data['rating_originality'],
            'rating_lyrics' => $data['rating_lyrics'],
            'comment' => $data['comment'],
            'uid' => $data['id_reviewer'],
            'rid' => $data['id_review'],
        ]);
    }
}