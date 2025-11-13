<?php
/**
 * User Model
 * Handles database operations for users
 */

class User
{
    private PDO $db;

    /**
     * Constructor - Initialize database connection
     */
    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Check if user with given email exists
     */
    public function exists(string $email): bool
    {
        $stmt = $this->db->prepare("SELECT id_user FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        return $stmt->fetch() !== false;
    }

    /**
     * Check if username is already taken (case-insensitive)
     */
    public function usernameExists(string $username): bool
    {
        $stmt = $this->db->prepare("SELECT id_user FROM users WHERE username = :username");
        $stmt->execute(['username' => $username]);
        return $stmt->fetch() !== false;
    }

    /**
     * Create a new user in the database
     */
    public function create(string $username, string $email, string $password): void
    {
        // Hash password using bcrypt
        $hash = password_hash($password, PASSWORD_BCRYPT);

        // Insert new user record
        $stmt = $this->db->prepare(
            "INSERT INTO users (username, email, password_hash, created_at) VALUES (:u, :e, :p, NOW())"
        );
        $stmt->execute([
            'u' => $username,
            'e' => $email,
            'p' => $hash
        ]);
    }

    /**
     * Find user by email address
     *
     * @return array|null User data or null if not found
     */
    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user ?: null;
    }
}