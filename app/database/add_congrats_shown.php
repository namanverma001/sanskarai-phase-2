<?php
/**
 * Migration: Add congrats_shown column to SAI_pandit_profiles
 * ============================================================
 * Tracks whether the pandit has seen the approval congratulations
 */

require_once __DIR__ . '/../../index.php';

use App\Config\Database;

echo "Adding congrats_shown column to SAI_pandit_profiles...\n";

try {
    $pdo = Database::getConnection();
    
    // Check if column already exists
    $checkSql = "SHOW COLUMNS FROM SAI_pandit_profiles LIKE 'congrats_shown'";
    $result = $pdo->query($checkSql)->fetch();
    
    if (!$result) {
        // Add the column
        $sql = "ALTER TABLE SAI_pandit_profiles ADD COLUMN congrats_shown TINYINT(1) DEFAULT 0 AFTER approved_at";
        $pdo->exec($sql);
        echo "✓ Added congrats_shown column\n";
        
        // Set congrats_shown = 1 for all already approved pandits (so they don't see the message)
        // unless they were approved in the last 7 days
        $updateSql = "
            UPDATE SAI_pandit_profiles 
            SET congrats_shown = 1 
            WHERE approval_status = 'approved' 
            AND (approved_at IS NULL OR approved_at < DATE_SUB(NOW(), INTERVAL 7 DAY))
        ";
        $affected = $pdo->exec($updateSql);
        echo "✓ Updated {$affected} existing approved records (older than 7 days)\n";
        
    } else {
        echo "✓ Column congrats_shown already exists\n";
    }
    
    echo "\n✅ Migration completed successfully!\n";
    
} catch (PDOException $e) {
    echo "❌ Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
