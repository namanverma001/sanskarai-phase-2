<?php
/**
 * Sanskar AI - Migration: Create Ritual Budget Tables
 * =====================================================
 * Creates SAI_ritual_budgets and SAI_ritual_budget_items tables
 *
 * Usage: php app/database/create_ritual_budget_tables.php
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/app.php';

use App\Config\Database;

echo "==============================================\n";
echo "  Migration: Create Ritual Budget Tables\n";
echo "==============================================\n\n";

try {
    $pdo = Database::getConnection(true);
    $pdo->exec("USE SAI");

    // ============================================================
    // 1. Create SAI_ritual_budgets
    // ============================================================
    echo "[1/3] Creating SAI_ritual_budgets table...\n";

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS SAI_ritual_budgets (
            id              INT AUTO_INCREMENT PRIMARY KEY,
            user_id         INT NOT NULL,
            ritual_type     VARCHAR(255) NOT NULL,
            location        VARCHAR(255) NOT NULL,
            guest_count     SMALLINT UNSIGNED NOT NULL,
            tier            ENUM('basic','standard','premium') NOT NULL DEFAULT 'standard',
            total_estimated DECIMAL(12,2) NOT NULL DEFAULT 0.00,
            total_actual    DECIMAL(12,2) NOT NULL DEFAULT 0.00,
            ai_request_id   INT NULL,
            created_at      DATETIME NOT NULL,
            updated_at      DATETIME NOT NULL,

            INDEX idx_user_id (user_id),
            FOREIGN KEY (user_id) REFERENCES SAI_users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    echo "      ✓ SAI_ritual_budgets created\n";

    // ============================================================
    // 2. Create SAI_ritual_budget_items
    // ============================================================
    echo "\n[2/3] Creating SAI_ritual_budget_items table...\n";

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS SAI_ritual_budget_items (
            id               INT AUTO_INCREMENT PRIMARY KEY,
            budget_id        INT NOT NULL,
            category         VARCHAR(100) NOT NULL,
            item_name        VARCHAR(255) NOT NULL,
            estimated_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
            actual_amount    DECIMAL(10,2) NULL,
            is_custom        TINYINT(1) NOT NULL DEFAULT 0,
            notes            TEXT NULL,
            created_at       DATETIME NOT NULL,
            updated_at       DATETIME NOT NULL,

            INDEX idx_budget_id (budget_id),
            FOREIGN KEY (budget_id) REFERENCES SAI_ritual_budgets(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    echo "      ✓ SAI_ritual_budget_items created\n";

    // ============================================================
    // 3. Verify
    // ============================================================
    echo "\n[3/3] Verifying...\n";

    $stmt = $pdo->query("SHOW TABLES LIKE 'SAI_ritual_budgets'");
    if ($stmt->rowCount() > 0) {
        echo "      ✓ SAI_ritual_budgets exists\n";
    } else {
        echo "      ✗ SAI_ritual_budgets MISSING!\n";
    }

    $stmt = $pdo->query("SHOW TABLES LIKE 'SAI_ritual_budget_items'");
    if ($stmt->rowCount() > 0) {
        echo "      ✓ SAI_ritual_budget_items exists\n";
    } else {
        echo "      ✗ SAI_ritual_budget_items MISSING!\n";
    }

    echo "\n==============================================\n";
    echo "  ✓ Migration completed successfully!\n";
    echo "==============================================\n\n";

} catch (PDOException $e) {
    echo "\n✗ Migration FAILED!\n";
    echo "Error: " . $e->getMessage() . "\n\n";
    exit(1);
}
