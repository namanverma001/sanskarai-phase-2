<?php
/**
 * Sanskar AI - Add Mohurat Requests Table
 * ========================================
 * Creates the SAI_mohurat_requests table for the Muhurat Request system
 * 
 * Usage: php app/database/add_mohurat_requests_table.php
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/app.php';

use App\Config\Database;

echo "==============================================\n";
echo "  Sanskar AI - Add Mohurat Requests Table\n";
echo "==============================================\n\n";

try {
    $pdo = Database::getConnection(true);
    $pdo->exec("USE SAI");

    echo "Creating SAI_mohurat_requests...\n";

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS SAI_mohurat_requests (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            pandit_id INT NULL,
            family_id INT NULL,
            ritual_type VARCHAR(200) NOT NULL,
            country VARCHAR(100) DEFAULT 'India',
            city VARCHAR(100) NULL,
            preferred_month VARCHAR(50) NULL,
            gotra VARCHAR(50) NULL,
            nakshatra VARCHAR(50) NULL,
            time_preference ENUM('morning', 'evening', 'any') DEFAULT 'any',
            additional_notes TEXT NULL,
            status ENUM('pending', 'replied', 'accepted', 'declined', 'expired') DEFAULT 'pending',
            reply_date DATE NULL,
            reply_time TIME NULL,
            reply_explanation TEXT NULL,
            consultation_fee DECIMAL(10,2) NULL,
            replied_by INT NULL,
            replied_at DATETIME NULL,
            accepted_at DATETIME NULL,
            assignment_id INT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

            FOREIGN KEY (user_id) REFERENCES SAI_users(id) ON DELETE CASCADE,
            FOREIGN KEY (pandit_id) REFERENCES SAI_users(id) ON DELETE SET NULL,
            FOREIGN KEY (family_id) REFERENCES SAI_families(id) ON DELETE SET NULL,
            FOREIGN KEY (replied_by) REFERENCES SAI_users(id) ON DELETE SET NULL,
            FOREIGN KEY (assignment_id) REFERENCES SAI_pandit_assignments(id) ON DELETE SET NULL,

            INDEX idx_user_id (user_id),
            INDEX idx_pandit_id (pandit_id),
            INDEX idx_status (status),
            INDEX idx_created_at (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    echo "✓ SAI_mohurat_requests created successfully!\n\n";

    // Verify
    $stmt = $pdo->query("SHOW TABLES LIKE 'SAI_mohurat_requests'");
    if ($stmt->fetch()) {
        echo "✓ Verification passed - table exists\n";
    } else {
        echo "✗ Verification failed - table not found\n";
    }

    echo "\n==============================================\n";
    echo "  Migration completed!\n";
    echo "==============================================\n";

} catch (PDOException $e) {
    echo "\n✗ Migration FAILED!\n";
    echo "Error: " . $e->getMessage() . "\n\n";
    exit(1);
}
