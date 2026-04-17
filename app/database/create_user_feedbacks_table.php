<?php
/**
 * Sanskar AI - Migration: Create User Feedbacks Table
 * ========================================================================
 * Creates SAI_user_feedbacks table to store mandatory user feedback responses.
 *
 * Usage: php app/database/create_user_feedbacks_table.php
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/app.php';

use App\Config\Database;

echo "==============================================\n";
echo "  Migration: User Feedbacks Table\n";
echo "==============================================\n\n";

try {
    $pdo = Database::getConnection();

    echo "Creating SAI_user_feedbacks table...\n";
    
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS SAI_user_feedbacks (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL,
            phone VARCHAR(20) NOT NULL,
            community_name VARCHAR(255),
            features_feedback JSON,
            likes_about TEXT,
            improvements_for TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES SAI_users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
    
    echo "      ✓ SAI_user_feedbacks created successfully.\n";

    echo "\n==============================================\n";
    echo "  ✓ Migration completed successfully!\n";
    echo "==============================================\n\n";

} catch (PDOException $e) {
    echo "\n✗ Migration FAILED!\n";
    echo "Error: " . $e->getMessage() . "\n\n";
    exit(1);
}
