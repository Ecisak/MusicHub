<?php
class Reviews {
    private PDO $db;
    public function __construct(PDO $db) {
        $this->db = $db;
    }

    function getAllBySongId(int $songId) {
        $stmt = $this->db->prepare("SELECT * FROM `reviews` JOIN users ON reviews.id_reviewer = users.id_user
    WHERE reviews.id_song = :song_id ORDER BY reviews.created_at DESC");
        $stmt->execute(['song_id' => $songId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
