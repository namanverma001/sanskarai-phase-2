<?php
require_once __DIR__ . '/app/config/Database.php';

use App\Config\Database;

try {
    $db = Database::getConnection();
    $sql = "ALTER TABLE SAI_guest_tracking MODIFY COLUMN action_type ENUM('view', 'search', 'ai_pandit') NOT NULL";
    $db->exec($sql);
    echo "Table SAI_guest_tracking altered successfully.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
