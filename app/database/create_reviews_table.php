<?php
/**
 * Sanskar AI - Reviews Table Migration
 * =====================================
 * Creates the reviews table for Pandit and Vendor ratings
 * 
 * Usage: php app/database/create_reviews_table.php
 */

require_once __DIR__ . '/../config/database.php';

use App\Config\Database;

echo "==============================================\n";
echo "  Sanskar AI - Reviews Table Migration\n";
echo "==============================================\n\n";

try {
    $pdo = Database::getConnection();
    
    // Disable foreign key checks during migration
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");

    // ============================================================
    // TABLE: SAI_reviews
    // ============================================================
    echo "Creating SAI_reviews table...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS SAI_reviews (
            id INT AUTO_INCREMENT PRIMARY KEY,
            reviewer_id INT NOT NULL,
            target_type ENUM('pandit', 'vendor') NOT NULL,
            target_id INT NOT NULL,
            assignment_id INT NULL,
            order_id INT NULL,
            
            -- Overall rating (required for both)
            rating_overall TINYINT UNSIGNED NOT NULL CHECK (rating_overall BETWEEN 1 AND 5),
            
            -- Pandit-specific ratings (NULL for vendor reviews)
            punctuality TINYINT UNSIGNED NULL CHECK (punctuality IS NULL OR punctuality BETWEEN 1 AND 5),
            knowledge TINYINT UNSIGNED NULL CHECK (knowledge IS NULL OR knowledge BETWEEN 1 AND 5),
            behavior TINYINT UNSIGNED NULL CHECK (behavior IS NULL OR behavior BETWEEN 1 AND 5),
            clarity TINYINT UNSIGNED NULL CHECK (clarity IS NULL OR clarity BETWEEN 1 AND 5),
            
            -- Vendor-specific ratings (NULL for pandit reviews)
            item_quality TINYINT UNSIGNED NULL CHECK (item_quality IS NULL OR item_quality BETWEEN 1 AND 5),
            delivery_time TINYINT UNSIGNED NULL CHECK (delivery_time IS NULL OR delivery_time BETWEEN 1 AND 5),
            packaging TINYINT UNSIGNED NULL CHECK (packaging IS NULL OR packaging BETWEEN 1 AND 5),
            value_for_money TINYINT UNSIGNED NULL CHECK (value_for_money IS NULL OR value_for_money BETWEEN 1 AND 5),
            
            -- Review text
            review_text TEXT NULL,
            
            -- Moderation
            ai_flag TINYINT(1) DEFAULT 0 COMMENT 'AI flagged for review',
            ai_moderation_reason VARCHAR(255) NULL,
            status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
            rejection_reason VARCHAR(255) NULL,
            moderated_by INT NULL,
            moderated_at DATETIME NULL,
            
            -- Timestamps
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            
            -- Foreign keys
            FOREIGN KEY (reviewer_id) REFERENCES SAI_users(id) ON DELETE CASCADE,
            FOREIGN KEY (assignment_id) REFERENCES SAI_pandit_assignments(id) ON DELETE SET NULL,
            FOREIGN KEY (order_id) REFERENCES SAI_orders(id) ON DELETE SET NULL,
            FOREIGN KEY (moderated_by) REFERENCES SAI_users(id) ON DELETE SET NULL,
            
            -- Indexes
            INDEX idx_reviewer (reviewer_id),
            INDEX idx_target (target_type, target_id),
            INDEX idx_assignment (assignment_id),
            INDEX idx_order (order_id),
            INDEX idx_status (status),
            INDEX idx_rating (rating_overall),
            INDEX idx_created (created_at),
            
            -- Prevent duplicate reviews
            UNIQUE KEY unique_assignment_review (assignment_id),
            UNIQUE KEY unique_order_review (order_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "✓ SAI_reviews table created\n\n";

    // ============================================================
    // Add trust badges column to pandit_profiles if not exists
    // ============================================================
    echo "Adding trust_badges column to SAI_pandit_profiles...\n";
    
    // Check if column exists
    $stmt = $pdo->query("SHOW COLUMNS FROM SAI_pandit_profiles LIKE 'trust_badges'");
    if ($stmt->rowCount() === 0) {
        $pdo->exec("
            ALTER TABLE SAI_pandit_profiles 
            ADD COLUMN trust_badges JSON NULL COMMENT 'Array of earned trust badges',
            ADD COLUMN positive_review_percentage DECIMAL(5,2) DEFAULT 0.00,
            ADD COLUMN is_documents_verified TINYINT(1) DEFAULT 0
        ");
        echo "✓ trust_badges column added\n";
    } else {
        echo "✓ trust_badges column already exists\n";
    }

    // ============================================================
    // Add trust badges column to vendors if not exists
    // ============================================================
    echo "Adding trust_badges column to SAI_vendors...\n";
    
    $stmt = $pdo->query("SHOW COLUMNS FROM SAI_vendors LIKE 'trust_badges'");
    if ($stmt->rowCount() === 0) {
        $pdo->exec("
            ALTER TABLE SAI_vendors 
            ADD COLUMN trust_badges JSON NULL COMMENT 'Array of earned trust badges',
            ADD COLUMN positive_review_percentage DECIMAL(5,2) DEFAULT 0.00
        ");
        echo "✓ trust_badges column added to vendors\n";
    } else {
        echo "✓ trust_badges column already exists in vendors\n";
    }

    // ============================================================
    // Create review notifications table
    // ============================================================
    echo "\nCreating SAI_review_notifications table...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS SAI_review_notifications (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            target_type ENUM('pandit', 'vendor') NOT NULL,
            target_id INT NOT NULL,
            assignment_id INT NULL,
            order_id INT NULL,
            notification_text VARCHAR(255) NOT NULL,
            is_read TINYINT(1) DEFAULT 0,
            is_reviewed TINYINT(1) DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            expires_at DATETIME NULL,
            
            FOREIGN KEY (user_id) REFERENCES SAI_users(id) ON DELETE CASCADE,
            INDEX idx_user (user_id),
            INDEX idx_read (is_read),
            INDEX idx_reviewed (is_reviewed)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "✓ SAI_review_notifications table created\n";

    // Re-enable foreign key checks
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");

    echo "\n==============================================\n";
    echo "  Migration completed successfully!\n";
    echo "==============================================\n";

} catch (PDOException $e) {
    echo "\n✗ Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
