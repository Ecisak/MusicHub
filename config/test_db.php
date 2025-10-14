<?php
require_once 'database.php';

try {
    // Získáme PDO spojení přes singleton
    $conn = Database::getInstance();

    // Ověříme, že spojení funguje
    if ($conn instanceof PDO) {
        echo "Spojení s databází bylo úspěšné! ✅\n";
    }

    // Zkusíme jednoduchý dotaz
    $stmt = $conn->query("SHOW TABLES;");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

    echo "Tabulky v databázi:\n";
    print_r($tables);

} catch (Exception $e) {
    echo "Chyba při testu databáze: " . $e->getMessage();
}
