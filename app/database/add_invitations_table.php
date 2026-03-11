<?php
/**
 * Sanskar AI - Add Invitations Table Migration
 * ==============================================
 * Creates SAI_invitations table for the invitation card feature
 * 
 * Usage: php app/database/add_invitations_table.php
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/app.php';

use App\Config\Database;

echo "==============================================\n";
echo "  Sanskar AI - Add Invitations Table\n";
echo "==============================================\n\n";

try {
    Database::loadEnv();
    $pdo = Database::getConnection();

    echo "Creating SAI_invitations table...\n";

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS SAI_invitations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            share_token VARCHAR(64) NOT NULL UNIQUE,
            occasion_type VARCHAR(100) NOT NULL,
            occasion_title VARCHAR(200) NOT NULL,
            event_date DATETIME NULL,
            venue VARCHAR(300) NULL,
            host_name VARCHAR(150) NOT NULL,
            message TEXT NULL,
            additional_details TEXT NULL,
            generated_html MEDIUMTEXT NOT NULL,
            ai_request_id INT NULL,
            expires_at DATETIME NOT NULL,
            is_active BOOLEAN DEFAULT TRUE,
            view_count INT DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

            FOREIGN KEY (user_id) REFERENCES SAI_users(id) ON DELETE CASCADE,
            FOREIGN KEY (ai_request_id) REFERENCES SAI_ai_requests(id) ON DELETE SET NULL,

            INDEX idx_user_id (user_id),
            INDEX idx_share_token (share_token),
            INDEX idx_expires_at (expires_at),
            INDEX idx_is_active (is_active)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    echo "✓ SAI_invitations table created successfully!\n\n";

} catch (PDOException $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    exit(1);
}
