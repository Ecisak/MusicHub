
<?php
/**
 * Model class for interacting with the 'songs' database table.
 * Handles CRUD operations, validation assignments, and leaderboard retrieval.
 */
class Music {
    private PDO $db;

    /**
     * Constructor.
     * @param PDO $db The PDO database connection instance.
     */
    public function __construct(PDO $db) {
        $this->db = $db;
    }

    /**
     * Retrieves all songs from the database.
     * @return array An array of all song records.
     */
    public function getAll(): array {
        $stmt = $this->db->prepare("SELECT * FROM `songs`");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Retrieves all songs with status 'pending'.
     * @return array An array of pending song records.
     */
    public function getAllPending(): array {
        $stmt = $this->db->prepare("SELECT * FROM `songs` WHERE status = 'pending'");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Retrieves all songs with status 'accepted', including average ratings.
     * @param string $orderBy Column name for sorting.
     * @param string $direction Sort direction ('ASC' or 'DESC').
     * @return array An array of accepted song records with ratings.
     */
    public function getAllAccepted(string $orderBy = 'date', string $direction = 'DESC'): array {
        $allowedColumns = [
            'date' => 'songs.uploaded_at',
            'rating' => 'average_rating',
            'lyrics' => 'average_lyrics',
            'quality' => 'average_quality',
            'originality' => 'average_originality'
        ];

        // Sanitize and default sort column
        $sortColumn = $allowedColumns[$orderBy] ?? $allowedColumns['date'];

        // Sanitize and default sort direction
        $direction = strtoupper($direction);
        if ($direction !== 'ASC' && $direction !== 'DESC') {
            $direction = 'DESC';
        }

        $sql = "
        SELECT
            songs.*,
            users.username as author_name,
            genre.genre as genre_name,
            -- Calculate overall average rating
            (SELECT ROUND(AVG((rating_quality + rating_originality + rating_lyrics) / 3), 1)
             FROM reviews WHERE reviews.id_song = songs.id_song AND reviews.status = 'approved') as average_rating,

            -- Calculate individual average ratings
            (SELECT ROUND(AVG(rating_quality), 1)
             FROM reviews WHERE reviews.id_song = songs.id_song AND reviews.status = 'approved') as average_quality,

            (SELECT ROUND(AVG(rating_originality), 1)
             FROM reviews WHERE reviews.id_song = songs.id_song AND reviews.status = 'approved') as average_originality,

            (SELECT ROUND(AVG(rating_lyrics), 1)
             FROM reviews WHERE reviews.id_song = songs.id_song AND reviews.status = 'approved') as average_lyrics

        FROM songs
        LEFT JOIN users ON songs.id_user = users.id_user
        LEFT JOIN genres genre ON songs.id_genre = genre.id
        WHERE songs.status = 'accepted'

        ORDER BY $sortColumn $direction
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Finds a song currently assigned to a specific validator.
     * @param int $validatorId The ID of the validator.
     * @return array|false The assigned song record or false if none is assigned.
     */
    public function findAssignedValidationTask(int $validatorId): array|false {
        $stmt = $this->db->prepare("SELECT * FROM songs WHERE assigned_validator_id = :validator_id AND status = 'pending'");
        $stmt->execute(['validator_id' => $validatorId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Finds the next available song for validation that has not been assigned or the assignment has expired.
     * Uses FOR UPDATE to lock the row during the transaction.
     * @param int $validatorId The ID of the potential validator (to exclude their own songs).
     * @return int|false The ID of the song or false if none is found.
     */
    public function findForValidation(int $validatorId): int|false {
        $stmt = $this->db->prepare("
        SELECT id_song FROM songs
        WHERE
            status = 'pending'
            AND id_user != :validator_id
            AND (
            assigned_validator_id IS NULL
            OR assigned_at < NOW() - INTERVAL 1 DAY
            )
        ORDER BY uploaded_at ASC
        LIMIT 1
        FOR UPDATE
        ");
        $stmt->execute(['validator_id' => $validatorId]);
        $result = $stmt->fetchColumn();
        return $result;
    }

    /**
     * Assigns a specific song to a validator.
     * @param int $validatorId The ID of the validator.
     * @param int $songId The ID of the song.
     * @return bool True on success, false on failure.
     */
    public function assignForValidation(int $validatorId, int $songId): bool {
        $stmt = $this->db->prepare("UPDATE songs SET assigned_validator_id = :validator_id, assigned_at = NOW() WHERE id_song = :song_id");
        return $stmt->execute(['validator_id' => $validatorId, 'song_id' => $songId]);
    }

    /**
     * Executes the transaction to find and assign a song for validation.
     * @param int $validatorId The ID of the validator.
     * @return int|null The assigned song ID or null if no song was assigned due to lack of songs or transaction error.
     */
    public function assignValidationTask(int $validatorId): ?int {
        $this->db->beginTransaction();
        try {
            $songId = $this->findForValidation($validatorId);
            if ($songId) {
                $this->assignForValidation($validatorId, $songId);
            }
            $this->db->commit();
            return $songId;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log('[ERROR] Error when assigning validation task: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Finds a song by its ID, including average ratings.
     * @param int $id The ID of the song.
     * @return array|false The song record with ratings or false if not found.
     */
    public function findById(int $id): array|false {
        $sql = "
    SELECT
        songs.*,
        users.username as author_name,
        genre.genre as genre_name,

        -- Calculate average ratings (same subqueries as getAllAccepted)
        (SELECT ROUND(AVG((rating_quality + rating_originality + rating_lyrics) / 3), 1)
         FROM reviews WHERE reviews.id_song = songs.id_song AND reviews.status = 'approved') as average_rating,

        (SELECT ROUND(AVG(rating_quality), 1)
         FROM reviews WHERE reviews.id_song = songs.id_song AND reviews.status = 'approved') as average_quality,

        (SELECT ROUND(AVG(rating_originality), 1)
         FROM reviews WHERE reviews.id_song = songs.id_song AND reviews.status = 'approved') as average_originality,

        (SELECT ROUND(AVG(rating_lyrics), 1)
         FROM reviews WHERE reviews.id_song = songs.id_song AND reviews.status = 'approved') as average_lyrics

    FROM songs
    LEFT JOIN users ON songs.id_user = users.id_user
    LEFT JOIN genres genre ON songs.id_genre = genre.id
    WHERE id_song = :id
    ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Creates a new song record in the database.
     * @param array $data The song data array.
     * @return bool True on success.
     */
    public function create(array $data): bool {
        $stmt = $this->db->prepare("INSERT INTO `songs` (`author`, `cover_image`, `filename`, `id_genre`,`id_user`, `release_year`, `status`, `title`, `uploaded_at`)
VALUES (:author, :coverImage, :filename, :id_genre, :id_user, :release_year, :status, :title, :uploaded_at)");
        return $stmt->execute($data);
    }

    /**
     * Deletes a song, its associated reviews, and the physical files (cover/music).
     * @param int $songId The ID of the song to delete.
     * @return bool True on successful deletion, false otherwise.
     */
    public function delete(int $songId): bool {
        $stmt = $this->db->prepare("SELECT cover_image, filename FROM songs WHERE id_song = :id");
        $stmt->execute(['id' => $songId]);
        $song = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($song) {
            // Define paths relative to the current file's directory
            $coverPath = __DIR__ . '/../public/uploads/covers/' . $song['cover_image'];
            $musicPath = __DIR__ . '/../public/uploads/music/' . $song['filename'];

            // Delete physical files
            if (file_exists($coverPath)) {
                unlink($coverPath);
            }
            if (file_exists($musicPath)) {
                unlink($musicPath);
            }

            // Delete associated reviews
            $delReviews = $this->db->prepare("DELETE FROM reviews WHERE id_song = :id");
            $delReviews->execute(['id' => $songId]);

            // Delete the song record
            $delSong = $this->db->prepare("DELETE FROM songs WHERE id_song = :id");
            return $delSong->execute(['id' => $songId]);
        }
        return false;
    }

    /**
     * Updates the validation status of a song.
     * Clears assigned validator fields upon status change.
     * @param int $songId The ID of the song.
     * @param string $status The new status ('accepted' or 'rejected').
     * @return bool True on success.
     */
    public function updateValidationStatus(int $songId, string $status): bool {
        $stmt = $this->db->prepare("UPDATE songs SET status = :status, assigned_validator_id = NULL, assigned_at = NULL WHERE id_song = :song_id");
        return $stmt->execute(['status' => $status, 'song_id' => $songId]);
    }

    /**
     * Gets the count of songs with status 'pending'.
     * @return int The count of pending songs.
     */
    public function getPendingCount(): int {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM `songs` WHERE status = 'pending'");
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }

    /**
     * Retrieves all songs uploaded by a specific user.
     * @param int $userId The ID of the user.
     * @return array An array of song records.
     */
    public function getByUserId(int $userId): array {
        $stmt = $this->db->prepare("SELECT * FROM songs WHERE id_user = :id ORDER BY uploaded_at DESC");
        $stmt->execute(['id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Retrieves the top N accepted songs based on average rating.
     * @param int $limit The maximum number of songs to return.
     * @return array An array of top song records.
     */
    public function getTopSongs(int $limit = 10): array {
        $sql = "
        SELECT
            songs.id_song,
            songs.title,
            songs.cover_image,
            songs.author,
            songs.filename, -- Added filename for audio player on leaderboard
            users.username as author_name,
            (SELECT ROUND(AVG((rating_quality + rating_originality + rating_lyrics) / 3), 1)
             FROM reviews WHERE reviews.id_song = songs.id_song AND reviews.status = 'approved') as average_rating
        FROM songs
        LEFT JOIN users on songs.id_user = users.id_user
        WHERE songs.status = 'accepted'
        HAVING average_rating IS NOT NULL -- Only include songs that have been rated
        
        ORDER BY average_rating DESC, songs.uploaded_at DESC
        LIMIT :limit
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue('limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

  