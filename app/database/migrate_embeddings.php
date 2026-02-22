<?php
/**
 * Sanskar AI - Embedding Table Migration
 * ========================================
 * Creates SAI_ritual_embeddings table for semantic search
 * 
 * Usage: php app/database/migrate_embeddings.php
 *   or visit: http://localhost:8000/app/database/migrate_embeddings.php
 */

require_once __DIR__ . '/../config/database.php';

use App\Config\Database;

echo "<h2>Sanskar AI - Embedding Table Migration</h2>";

try {
    Database::loadEnv();
    $pdo = Database::getConnection();

    echo "<p>✅ Database connection successful.</p>";

    // Create SAI_ritual_embeddings table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS SAI_ritual_embeddings (
            id INT AUTO_INCREMENT PRIMARY KEY,
            ritual_id INT NOT NULL,
            ritual_name VARCHAR(150) NULL,
            community_name VARCHAR(150) NULL,
            religion VARCHAR(100) NULL,
            combined_text TEXT NULL,
            embedding JSON NULL,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

            UNIQUE KEY unique_ritual (ritual_id),
            FOREIGN KEY (ritual_id) REFERENCES SAI_rituals(id) ON DELETE CASCADE,

            INDEX idx_ritual_id (ritual_id),
            INDEX idx_community_name (community_name),
            INDEX idx_religion (religion)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    echo "<p>✅ <strong>SAI_ritual_embeddings</strong> table created/verified successfully.</p>";

    // Show current row count
    $count = $pdo->query("SELECT COUNT(*) FROM SAI_ritual_embeddings")->fetchColumn();
    echo "<p>📊 Current embeddings count: <strong>$count</strong></p>";

    echo "<hr>";
    echo "<p>✅ Migration complete! You can now run <a href='/generate_embeddings.php'>generate_embeddings.php</a> to populate embeddings.</p>";

} catch (PDOException $e) {
    echo "<p>❌ Database error: " . htmlspecialchars($e->getMessage()) . "</p>";
} catch (Exception $e) {
    echo "<p>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>
