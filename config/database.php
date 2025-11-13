<?php
/**
 * Database Configuration and Connection Manager
 * Implements Singleton pattern for PDO connection
 */

class Database
{
    private string $host;
    private string $db_name;
    private string $username;
    private string $password;
    private ?PDO $conn = null;

    /**
     * Constructor - Load database configuration from environment variables
     */
    public function __construct()
    {
        $this->host = getenv('DB_HOST') ?: 'localhost';
        $this->db_name = getenv('DB_NAME') ?: 'musichub';
        $this->username = getenv('DB_USER') ?: 'root';
        $this->password = getenv('DB_PASS') ?: '';
    }

    /**
     * Get singleton PDO instance
     * Ensures only one database connection exists
     *
     * @return PDO Database connection
     */
    public static function getInstance(): PDO
    {
        static $instance = null;

        if ($instance === null) {
            $db = new self();
            $instance = $db->connect();
        }

        return $instance;
    }

    /**
     * Create PDO connection
     *
     * @return PDO Database connection
     * @throws RuntimeException if connection fails
     */
    private function connect(): PDO
    {
        try {
            $pdo = new PDO(
                "mysql:host={$this->host};dbname={$this->db_name};charset=utf8mb4",
                $this->username,
                $this->password
            );

            // Set error mode to exceptions
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Set default fetch mode to associative array
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

            return $pdo;
        } catch (PDOException $e) {
            // Throw exception instead of echoing error
            throw new RuntimeException("Database connection failed: " . $e->getMessage());
        }
    }
}