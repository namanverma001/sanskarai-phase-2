<?php
require_once __DIR__ . '/../config/database.php';
use App\Config\Database;

try {
    $pdo = Database::getConnection(true);
    $pdo->exec("USE SAI");
    $pdo->exec("ALTER TABLE SAI_ai_ritual_feedback ADD COLUMN updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER created_at");
    echo "updated_at column added!\n";
} catch (Exception $e) {
    echo "Error or already exists: " . $e->getMessage() . "\n";
}
