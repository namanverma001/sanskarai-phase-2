<?php
/**
 * Migration: Add country column to SAI_families table
 * Run this script to add the country field
 */

require_once __DIR__ . '/../config/database.php';

use App\Config\Database;

try {
    $db = Database::getConnection();
    
    // Add country column to SAI_families table
    $sql = "ALTER TABLE SAI_families ADD COLUMN country VARCHAR(100) NULL AFTER state";
    
    $db->exec($sql);
    
    echo "✅ Successfully added 'country' column to SAI_families table.\n";
    
} catch (PDOException $e) {
    // Check if column already exists
    if (strpos($e->getMessage(), 'Duplicate column') !== false || strpos($e->getMessage(), 'already exists') !== false) {
        echo "ℹ️ Column 'country' already exists in SAI_families table.\n";
    } else {
        echo "❌ Error: " . $e->getMessage() . "\n";
        exit(1);
    }
}
