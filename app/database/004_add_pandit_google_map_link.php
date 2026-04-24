<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/app.php';

\App\Config\Database::loadEnv();
$pdo = \App\Config\Database::getConnection();

echo "Adding google_map_link to SAI_pandit_profiles table...\n";

try {
    $sql = "ALTER TABLE SAI_pandit_profiles ADD COLUMN map_url VARCHAR(500) NULL AFTER longitude;";
    $pdo->exec($sql);
    echo "Successfully added map_url column.\n";
} catch (\PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
        echo "Column map_url already exists.\n";
    } else {
        echo "Error: " . $e->getMessage() . "\n";
    }
}
