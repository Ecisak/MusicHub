<?php
class Music {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }
    public function getAll() {
        $stmt = $this->db->prepare("SELECT * FROM `songs`");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create(array $data) {
        $stmt = $this-> db-> prepare("INSERT INTO `songs` (`author`, `cover_image`, `filename`, `id_genre`,`id_user`, `release_year`, `status`, `title`, `uploaded_at`) 
VALUES (:author, :coverImage, :filename, :id_genre, :id_user, :release_year, :status, :title, :uploaded_at)");
        $stmt->execute($data);
    }

}