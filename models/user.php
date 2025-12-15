<?php
/**
 * User Model
 * Handles database operations for users: authentication, registration, and administrative tasks.
 */

class User
{
    private PDO $db;

    /**
     * Constructor - Initialize database connection.
     * @param PDO $db The PDO database connection instance.
     */
    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Check if a user with the given email exists.
     * @param string $email The email address to check.
     * @return bool True if the email exists, false otherwise.
     */
    public function exists(string $email): bool
    {
        $stmt = $this->db->prepare("SELECT id_user FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        return $stmt->fetch() !== false;
    }

    /**
     * Check if a username is already taken (case-insensitive check is recommended but implementation here is case-sensitive).
     * @param string $username The username to check.
     * @return bool True if the username exists, false otherwise.
     */
    public function usernameExists(string $username): bool
    {
        $stmt = $this->db->prepare("SELECT id_user FROM users WHERE username = :username");
        $stmt->execute(['username' => $username]);
        return $stmt->fetch() !== false;
    }

    /**
     * Create a new user in the database with role 'author' (default role).
     * @param string $username The user's desired username.
     * @param string $email The user's email address.
     * @param string $password The user's plain text password.
     * @return void
     */
    public function create(string $username, string $email, string $password): void
    {
        // Hash password using bcrypt
        $hash = password_hash($password, PASSWORD_BCRYPT);

        // Insert new user record
        $stmt = $this->db->prepare(
            "INSERT INTO users (username, email, password_hash, created_at, role) VALUES (:u, :e, :p, NOW(), 'author')"
        ); // Added 'author' as the default role
        $stmt->execute([
            'u' => $username,
            'e' => $email,
            'p' => $hash
        ]);
    }

    /**
     * Find user by email address.
     * @param string $email The email address.
     * @return array|null User data or null if not found.
     */
    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user ?: null;
    }

    /**
     * Retrieves all users from the database.
     * @return array An array of all user records.
     */
    public function getAllUsers(): array {
        $stmt = $this->db->prepare("SELECT * FROM users");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Retrieves a single user by their ID.
     * @param int $id The ID of the user.
     * @return array|false The user record or false if not found.
     */
    public function getById(int $id): array|false {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id_user = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Updates the role of a specified user.
     * @param int $userId The ID of the user to update.
     * @param string $newRole The new role to assign.
     * @return bool True on success, false on failure.
     */
    public function updateRole(int $userId, string $newRole): bool {
        $stmt = $this->db->prepare("UPDATE users SET role = :role WHERE id_user = :id");
        return $stmt->execute(['role' => $newRole, 'id' => $userId]);
    }
}