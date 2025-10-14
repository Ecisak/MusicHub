<?php

class Database {
    private string $host;
    private string $db_name;
    private string $username;
    private string $password;
    private ?PDO $conn = null;

    // Konstruktor načítá nastavení z environment proměnných
    public function __construct() {
        $this->host = getenv('DB_HOST') ?: 'localhost';
        $this->db_name = getenv('DB_NAME') ?: 'musichub';
        $this->username = getenv('DB_USER') ?: 'root';
        $this->password = getenv('DB_PASS') ?: '';
    }

    // Singleton – vrací vždy stejné PDO spojení
    public static function getInstance(): PDO {
        static $instance = null;
        if ($instance === null) {
            $db = new self();
            $instance = $db->connect();
        }
        return $instance;
    }

    // Privátní metoda pro vytvoření spojení
    private function connect(): PDO {
        try {
            $pdo = new PDO(
                "mysql:host={$this->host};dbname={$this->db_name};charset=utf8mb4",
                $this->username,
                $this->password
            );
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            return $pdo;
        } catch (PDOException $e) {
            // Hodíme výjimku místo echo
            throw new RuntimeException("Database connection failed: " . $e->getMessage());
        }
    }
}