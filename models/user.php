<?php
class User {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function exists(string $email): bool {
        $stmt = $this->db->prepare("SELECT id_user FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        return $stmt->fetch() !== false;
    }
    public function usernameExists(string $username): bool {
        // case-insensitive porovnani
        $stmt = $this->db->prepare("SELECT id_user FROM users WHERE username = :username");
        $stmt->execute(['username' => $username]);
        return $stmt->fetch() !== false;
    }

    public function create(string $username, string $email, string $password): void {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $this->db->prepare(
            "INSERT INTO users (username, email, password_hash, created_at) VALUES (:u, :e, :p, NOW())"
        );
        $stmt->execute([
            'u' => $username,
            'e' => $email,
            'p' => $hash
        ]);
    }
    public function findByEMail(string $email): ?array {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user ?: null;
    }
}