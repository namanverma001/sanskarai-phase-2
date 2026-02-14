<?php
/**
 * Add Profile Fields to Users Table
 * ==========================================
 * Adds community_name and religion columns
 */

// Load configuration
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/app.php';

use App\Config\Database;

echo "==============================================\n";
echo "  Sanskar AI - Add User Profile Fields\n";
echo "==============================================\n\n";

try {
    // Get PDO connection
    $pdo = Database::getConnection();
    $pdo->exec("USE SAI");

    echo "Checking SAI_users table...\n";

    // Add community_name if not exists
    $columns = $pdo->query("SHOW COLUMNS FROM SAI_users LIKE 'community_name'")->fetchAll();
    if (empty($columns)) {
        echo "Adding 'community_name' column...\n";
        $pdo->exec("ALTER TABLE SAI_users ADD COLUMN community_name VARCHAR(150) NULL AFTER mobile");
        echo "✓ 'community_name' added.\n";
    } else {
        echo "- 'community_name' already exists.\n";
    }

    // Add religion if not exists
    $columns = $pdo->query("SHOW COLUMNS FROM SAI_users LIKE 'religion'")->fetchAll();
    if (empty($columns)) {
        echo "Adding 'religion' column...\n";
        $pdo->exec("ALTER TABLE SAI_users ADD COLUMN religion VARCHAR(100) NULL AFTER community_name");
        echo "✓ 'religion' added.\n";
    } else {
        echo "- 'religion' already exists.\n";
    }

    echo "\nMigration completed successfully!\n";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
