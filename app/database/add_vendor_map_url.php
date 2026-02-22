<?php
/**
 * Migration: Add map_url column to SAI_vendors table
 * ===================================================
 * Adds a map URL field for custom Google Maps directions link
 *
 * Usage (from project root):
 *   php app/database/add_vendor_map_url.php
 */

require_once __DIR__ . '/../config/database.php';

use App\Config\Database;

echo "==============================================\n";
echo "  Adding map_url column to SAI_vendors\n";
echo "==============================================\n\n";

try {
    $db = Database::getConnection();

    // Check if column already exists
    $stmt = $db->query("SHOW COLUMNS FROM SAI_vendors LIKE 'map_url'");
    if ($stmt->rowCount() > 0) {
        echo "INFO: map_url column already exists.\n";
        exit(0);
    }

    // Add the map_url column after longitude
    $sql = "ALTER TABLE `SAI_vendors` 
            ADD COLUMN `map_url` VARCHAR(500) NULL 
            COMMENT 'Google Maps or custom directions URL' 
            AFTER `longitude`";

    $db->exec($sql);
    echo "SUCCESS: map_url column added to SAI_vendors table.\n";

} catch (PDOException $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
