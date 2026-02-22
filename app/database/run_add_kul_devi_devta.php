<?php
/**
 * Run this script to add kul_devi_devta column to SAI_users
 * Usage: php app/database/run_add_kul_devi_devta.php
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/app.php';

use App\Config\Database;

try {
    $pdo = Database::getConnection();
    $pdo->exec("USE SAI");

    $cols = $pdo->query("SHOW COLUMNS FROM SAI_users LIKE 'kul_devi_devta'")->fetchAll();
    if (empty($cols)) {
        $pdo->exec("ALTER TABLE SAI_users ADD COLUMN kul_devi_devta VARCHAR(150) NULL AFTER community_name");
        echo "SUCCESS: kul_devi_devta column added to SAI_users!\n";
    } else {
        echo "Column 'kul_devi_devta' already exists in SAI_users.\n";
    }
} catch (PDOException $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
