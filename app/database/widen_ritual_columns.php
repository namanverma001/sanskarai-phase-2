<?php
/**
 * Widen SAI_rituals columns to handle longer AI-generated content
 */

// Load configuration
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/app.php';

use App\Config\Database;

echo "==============================================\n";
echo "  Sanskar AI - Widen Ritual Columns\n";
echo "==============================================\n\n";

try {
    $pdo = Database::getConnection();
    $pdo->exec("USE SAI");

    $alterations = [
        "ALTER TABLE SAI_rituals MODIFY best_time VARCHAR(500) NULL",
        "ALTER TABLE SAI_rituals MODIFY best_tithi VARCHAR(500) NULL",
        "ALTER TABLE SAI_rituals MODIFY deity VARCHAR(500) NULL",
        "ALTER TABLE SAI_rituals MODIFY occasion_type VARCHAR(500) NULL",
        "ALTER TABLE SAI_rituals MODIFY name VARCHAR(300) NOT NULL",
        "ALTER TABLE SAI_rituals MODIFY name_sanskrit VARCHAR(300) NULL",
        "ALTER TABLE SAI_rituals MODIFY category VARCHAR(300) NOT NULL",
        "ALTER TABLE SAI_rituals MODIFY sub_category VARCHAR(300) NULL",
    ];

    foreach ($alterations as $sql) {
        $pdo->exec($sql);
        echo "OK " . $sql . "\n";
    }

    // Also widen user_rituals columns
    $userRitualAlterations = [
        "ALTER TABLE SAI_user_rituals MODIFY best_time VARCHAR(500) NULL",
        "ALTER TABLE SAI_user_rituals MODIFY best_tithi VARCHAR(500) NULL",
        "ALTER TABLE SAI_user_rituals MODIFY deity VARCHAR(500) NULL",
        "ALTER TABLE SAI_user_rituals MODIFY occasion_type VARCHAR(500) NULL",
        "ALTER TABLE SAI_user_rituals MODIFY name VARCHAR(300) NOT NULL",
        "ALTER TABLE SAI_user_rituals MODIFY category VARCHAR(300) NULL",
    ];

    foreach ($userRitualAlterations as $sql) {
        try {
            $pdo->exec($sql);
            echo "OK " . $sql . "\n";
        } catch (Exception $e) {
            echo "SKIP (column may not exist): " . $sql . "\n";
        }
    }

    echo "\nAll columns widened successfully!\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
