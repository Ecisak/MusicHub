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

    public function getAllPending() {
        $stmt = $this->db->prepare("SELECT * FROM `songs` WHERE status = 'pending'");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllAccepted() {
        $stmt = $this->db->prepare("SELECT * FROM `songs` WHERE status = 'accepted'");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findAssignedValidationTask($validatorId) {
        $stmt = $this->db->prepare("SELECT * FROM songs WHERE assigned_validator_id = :validator_id AND status = 'pending'");
        $stmt->execute(['validator_id' => $validatorId]);
        return $stmt->fetch();
    }
    public function findForValidation(int $validatorId) {
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
        FOR UPDATE;"
        );
        $stmt->execute(['validator_id' => $validatorId]);
        $result = $stmt->fetchColumn();
        return $result;
    }
    public function assignForValidation(int $validatorId, int $songId) {
        $stmt = $this->db->prepare("UPDATE songs SET assigned_validator_id = :validator_id, assigned_at = NOW() WHERE id_song = :song_id");
        return $stmt->execute(['validator_id' => $validatorId, 'song_id' => $songId]);
    }

    public function validate(int $validatorId) {
        $this->db->beginTransaction();
        try {
            $songId = $this->findForValidation($validatorId);
            if ($songId) {
                $this->assignForValidation($validatorId, $songId);
            }
            $this->db->commit();
            return $songId; // Nezapomeň vrátit ID!
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log('[ERROR] Error when assigning' . $e->getMessage());

            // --- NÁŠ DOČASNÝ LADICÍ BLOK ---
            die("Chyba zachycena v transakci: " . $e->getMessage());
            // --- KONEC LADICÍHO BLOKU ---

            return null;
        }
    }

    public function findById(int $id) {
        $stmt = $this->db->prepare("SELECT * FROM `songs` WHERE id_song = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create(array $data) {
        $stmt = $this-> db-> prepare("INSERT INTO `songs` (`author`, `cover_image`, `filename`, `id_genre`,`id_user`, `release_year`, `status`, `title`, `uploaded_at`) 
VALUES (:author, :coverImage, :filename, :id_genre, :id_user, :release_year, :status, :title, :uploaded_at)");
        $stmt->execute($data);
    }

    public function updateValidationStatus(int $songId, string $status) {
        $stmt = $this->db->prepare("UPDATE songs SET status = :status, assigned_validator_id = NULL, assigned_at = NULL WHERE id_song = :song_id");
        $stmt->execute(['status' => $status, 'song_id' => $songId]);
    }

}