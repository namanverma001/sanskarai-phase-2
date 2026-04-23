<?php
/**
 * Sanskar AI - Add Location Fields to Pandit Profiles
 * ===================================================
 * Run: php app/database/003_add_pandit_location_fields.php
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/app.php';

use App\Config\Database;

echo "==============================================\n";
echo "  Sanskar AI - Database Update\n";
echo "==============================================\n\n";

try {
    Database::loadEnv();
    $pdo = Database::getConnection();
    
    echo "Adding location fields to SAI_pandit_profiles...\n";
    
    $queries = [
        "ALTER TABLE SAI_pandit_profiles ADD COLUMN latitude DECIMAL(10, 8) NULL AFTER average_rating",
        "ALTER TABLE SAI_pandit_profiles ADD COLUMN longitude DECIMAL(11, 8) NULL AFTER latitude",
        "ALTER TABLE SAI_pandit_profiles ADD COLUMN city VARCHAR(100) NULL AFTER longitude",
        "ALTER TABLE SAI_pandit_profiles ADD COLUMN pincode VARCHAR(20) NULL AFTER city",
        "ALTER TABLE SAI_pandit_profiles ADD COLUMN service_area_km INT DEFAULT 50 AFTER pincode"
    ];
    
    foreach ($queries as $sql) {
        try {
            $pdo->exec($sql);
            echo "✓ Executed: " . substr($sql, 0, 80) . "...\n";
        } catch (PDOException $e) {
            // Ignore if column already exists
            if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
                echo "⚠ Column already exists: " . substr($sql, 0, 80) . "...\n";
            } else {
                throw $e;
            }
        }
    }
    
    echo "\nDatabase updated successfully!\n";
    
} catch (PDOException $e) {
    echo "\n✗ Database Update FAILED!\n";
    echo "Error: " . $e->getMessage() . "\n\n";
    exit(1);
}
