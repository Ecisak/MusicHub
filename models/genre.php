<?php
class Genre {
    private PDO $db;
    public function __construct(PDO $db)
    {
        $this->db = $db;
    }
    function getAll() {
        $stmt = $this->db->prepare("SELECT * FROM genres");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function get($id) {
        $stmt = $this->db->prepare("SELECT * FROM genres WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}