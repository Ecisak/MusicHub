<?php
/**
 * Model class for interacting with the 'genres' database table.
 */
class Genre {
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
     * Retrieves all genres from the database.
     * @return array An array of all genre records.
     */
    public function getAll(): array {
        $stmt = $this->db->prepare("SELECT * FROM genres");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Retrieves a single genre by its ID.
     * @param int $id The ID of the genre.
     * @return array|false The genre record or false if not found.
     */
    public function get(int $id): array|false {
        $stmt = $this->db->prepare("SELECT * FROM genres WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}