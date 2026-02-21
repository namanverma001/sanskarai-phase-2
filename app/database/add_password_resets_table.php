<?php
/**
 * Migration: Create SAI_password_resets table
 *
 * Usage (from project root):
 *   php app/database/add_password_resets_table.php
 */

require_once __DIR__ . '/../config/database.php';

use App\Config\Database;

try {
    $db = Database::getConnection();

    // No CHARSET/COLLATE and no FK constraint to avoid remote-DB conflicts
    $sql = "
    CREATE TABLE IF NOT EXISTS `SAI_password_resets` (
        `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
        `user_id`    INT UNSIGNED NOT NULL,
        `token_hash` VARCHAR(255) NOT NULL,
        `expires_at` DATETIME     NOT NULL,
        `created_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        INDEX `idx_user_id` (`user_id`)
    ) ENGINE=InnoDB;
    ";

    $db->exec($sql);
    echo "SUCCESS: SAI_password_resets table created (or already exists).\n";

} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'already exists') !== false) {
        echo "INFO: SAI_password_resets table already exists.\n";
    } else {
        echo "ERROR: " . $e->getMessage() . "\n";
        exit(1);
    }
}
