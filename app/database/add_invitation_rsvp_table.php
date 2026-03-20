<?php
/**
 * Sanskar AI - Migration: Add Invitation RSVP Table & Update Invitations
 * ========================================================================
 * Creates SAI_invitation_rsvps table and adds template columns to SAI_invitations
 *
 * Usage: php app/database/add_invitation_rsvp_table.php
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/app.php';

use App\Config\Database;

echo "==============================================\n";
echo "  Migration: Invitation RSVP & Templates\n";
echo "==============================================\n\n";

try {
    $pdo = Database::getConnection();

    // ============================================================
    // 1. Add template columns to SAI_invitations
    // ============================================================
    echo "[1/3] Updating SAI_invitations table...\n";

    // Add template_id column
    $stmt = $pdo->query("SHOW COLUMNS FROM SAI_invitations LIKE 'template_id'");
    if ($stmt->rowCount() === 0) {
        $pdo->exec("
            ALTER TABLE SAI_invitations
            ADD COLUMN template_id VARCHAR(50) DEFAULT 'royal_gold' AFTER additional_details
        ");
        echo "      ✓ 'template_id' added\n";
    } else {
        echo "      - 'template_id' already exists\n";
    }

    // Add theme_color column
    $stmt = $pdo->query("SHOW COLUMNS FROM SAI_invitations LIKE 'theme_color'");
    if ($stmt->rowCount() === 0) {
        $pdo->exec("
            ALTER TABLE SAI_invitations
            ADD COLUMN theme_color VARCHAR(20) DEFAULT '#B8860B' AFTER template_id
        ");
        echo "      ✓ 'theme_color' added\n";
    } else {
        echo "      - 'theme_color' already exists\n";
    }

    // Add rsvp_enabled column
    $stmt = $pdo->query("SHOW COLUMNS FROM SAI_invitations LIKE 'rsvp_enabled'");
    if ($stmt->rowCount() === 0) {
        $pdo->exec("
            ALTER TABLE SAI_invitations
            ADD COLUMN rsvp_enabled BOOLEAN DEFAULT TRUE AFTER theme_color
        ");
        echo "      ✓ 'rsvp_enabled' added\n";
    } else {
        echo "      - 'rsvp_enabled' already exists\n";
    }

    // Make generated_html nullable (no longer required)
    echo "      Making generated_html nullable...\n";
    try {
        $pdo->exec("ALTER TABLE SAI_invitations MODIFY generated_html MEDIUMTEXT NULL");
        echo "      ✓ 'generated_html' is now nullable\n";
    } catch (Exception $e) {
        echo "      ! Could not modify generated_html: " . $e->getMessage() . "\n";
    }

    // ============================================================
    // 2. Create SAI_invitation_rsvps table
    // ============================================================
    echo "\n[2/3] Creating SAI_invitation_rsvps table...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS SAI_invitation_rsvps (
            id INT AUTO_INCREMENT PRIMARY KEY,
            invitation_id INT NOT NULL,
            guest_name VARCHAR(150) NOT NULL,
            attending_status ENUM('yes', 'no', 'maybe') NOT NULL DEFAULT 'yes',
            guest_count INT DEFAULT 1,
            message TEXT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,

            FOREIGN KEY (invitation_id) REFERENCES SAI_invitations(id) ON DELETE CASCADE,

            INDEX idx_invitation_id (invitation_id),
            INDEX idx_attending_status (attending_status)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "      ✓ SAI_invitation_rsvps created\n";

    // ============================================================
    // 3. Verify
    // ============================================================
    echo "\n[3/3] Verifying...\n";
    $stmt = $pdo->query("SHOW TABLES LIKE 'SAI_invitation_rsvps'");
    if ($stmt->rowCount() > 0) {
        echo "      ✓ SAI_invitation_rsvps exists\n";
    } else {
        echo "      ✗ SAI_invitation_rsvps MISSING!\n";
    }

    echo "\n==============================================\n";
    echo "  ✓ Migration completed successfully!\n";
    echo "==============================================\n\n";

} catch (PDOException $e) {
    echo "\n✗ Migration FAILED!\n";
    echo "Error: " . $e->getMessage() . "\n\n";
    exit(1);
}
