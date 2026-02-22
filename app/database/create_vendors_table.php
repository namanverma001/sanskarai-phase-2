<?php
/**
 * Migration: Create SAI_vendors table
 * =====================================
 * Stores vendor information with location for proximity search
 *
 * Usage (from project root):
 *   php app/database/create_vendors_table.php
 */

require_once __DIR__ . '/../config/database.php';

use App\Config\Database;

echo "==============================================\n";
echo "  Creating SAI_vendors table\n";
echo "==============================================\n\n";

try {
    $db = Database::getConnection();

    $sql = "
    CREATE TABLE IF NOT EXISTS `SAI_vendors` (
        `id`                INT UNSIGNED NOT NULL AUTO_INCREMENT,
        `name`              VARCHAR(150) NOT NULL,
        `category`          ENUM('photographer', 'catering', 'decorator', 'florist', 'music', 'lighting', 'tent_house', 'makeup_artist', 'mehendi_artist', 'videographer', 'invitation_cards', 'travel', 'other') NOT NULL DEFAULT 'other',
        `description`       TEXT NULL,
        `contact_person`    VARCHAR(100) NULL,
        `email`             VARCHAR(150) NULL,
        `phone`             VARCHAR(20) NOT NULL,
        `alternate_phone`   VARCHAR(20) NULL,
        `whatsapp`          VARCHAR(20) NULL,
        `website`           VARCHAR(255) NULL,
        
        -- Address Fields
        `address_line1`     VARCHAR(255) NOT NULL,
        `address_line2`     VARCHAR(255) NULL,
        `city`              VARCHAR(100) NOT NULL,
        `state`             VARCHAR(100) NOT NULL,
        `pincode`           VARCHAR(10) NOT NULL,
        `country`           VARCHAR(100) DEFAULT 'India',
        
        -- Location for proximity search (latitude/longitude)
        `latitude`          DECIMAL(10, 8) NOT NULL,
        `longitude`         DECIMAL(11, 8) NOT NULL,
        
        -- Service Details
        `service_area_km`   INT DEFAULT 50 COMMENT 'Service radius in kilometers',
        `min_price`         DECIMAL(12, 2) NULL COMMENT 'Minimum service price',
        `max_price`         DECIMAL(12, 2) NULL COMMENT 'Maximum service price',
        `services_offered`  TEXT NULL COMMENT 'JSON array of services',
        
        -- Media
        `logo_url`          VARCHAR(255) NULL,
        `gallery_images`    TEXT NULL COMMENT 'JSON array of image URLs',
        
        -- Ratings & Reviews
        `average_rating`    DECIMAL(3, 2) DEFAULT 0.00,
        `total_reviews`     INT DEFAULT 0,
        
        -- Status
        `is_active`         TINYINT(1) DEFAULT 1,
        `is_featured`       TINYINT(1) DEFAULT 0,
        `is_verified`       TINYINT(1) DEFAULT 0,
        
        -- Audit
        `added_by`          INT UNSIGNED NULL COMMENT 'Admin who added',
        `created_at`        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at`        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        
        PRIMARY KEY (`id`),
        INDEX `idx_category` (`category`),
        INDEX `idx_city` (`city`),
        INDEX `idx_state` (`state`),
        INDEX `idx_pincode` (`pincode`),
        INDEX `idx_is_active` (`is_active`),
        INDEX `idx_is_featured` (`is_featured`),
        INDEX `idx_location` (`latitude`, `longitude`),
        INDEX `idx_rating` (`average_rating` DESC)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";

    $db->exec($sql);
    echo "SUCCESS: SAI_vendors table created successfully.\n";

    // Create spatial index hint for distance-based queries
    echo "\nNote: For optimal location-based searches, the table uses latitude/longitude\n";
    echo "      with Haversine formula for distance calculations.\n";

} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'already exists') !== false) {
        echo "INFO: SAI_vendors table already exists.\n";
    } else {
        echo "ERROR: " . $e->getMessage() . "\n";
        exit(1);
    }
}
