<?php
/**
 * Sanskar AI - Database Migration Script
 * ========================================
 * Creates all 24 tables with proper constraints and relationships
 * 
 * Usage: php app/database/migrate.php
 */

// Load configuration
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/app.php';

use App\Config\Database;
use App\Config\App;

echo "==============================================\n";
echo "  Sanskar AI - Database Migration\n";
echo "==============================================\n\n";

try {
    // Create database if not exists
    echo "[1/3] Creating database SAI...\n";

    if (Database::createDatabase()) {
        echo "      ✓ Database SAI created/verified successfully\n\n";
    } else {
        echo "      ✗ Failed to create database\n";
        exit(1);
    }

    // Get PDO connection and select database
    $pdo = Database::getConnection(true);
    $pdo->exec("USE SAI");

    echo "[2/3] Creating tables...\n\n";

    // Disable foreign key checks during migration
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");

    // ============================================================
    // TABLE 1: SAI_users
    // ============================================================
    echo "      Creating SAI_users...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS SAI_users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            email VARCHAR(150) NOT NULL UNIQUE,
            mobile VARCHAR(15) UNIQUE,
            password_hash VARCHAR(255) NOT NULL,
            role ENUM('admin', 'pandit', 'user') NOT NULL DEFAULT 'user',
            status ENUM('active', 'blocked', 'pending') NOT NULL DEFAULT 'active',
            email_verified_at DATETIME NULL,
            remember_token VARCHAR(100) NULL,
            last_login_at DATETIME NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            
            INDEX idx_email (email),
            INDEX idx_mobile (mobile),
            INDEX idx_role (role),
            INDEX idx_status (status)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "      ✓ SAI_users created\n";

    // ============================================================
    // TABLE 2: SAI_pandit_profiles
    // ============================================================
    echo "      Creating SAI_pandit_profiles...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS SAI_pandit_profiles (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            specialization VARCHAR(200) NOT NULL,
            experience_years INT DEFAULT 0,
            bio TEXT NULL,
            profile_photo VARCHAR(255) NULL,
            languages VARCHAR(200) DEFAULT 'Hindi',
            availability_days VARCHAR(100) DEFAULT 'Mon,Tue,Wed,Thu,Fri,Sat,Sun',
            hourly_rate DECIMAL(10,2) NULL,
            approval_status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
            approved_by INT NULL,
            approved_at DATETIME NULL,
            rejection_reason TEXT NULL,
            total_rituals_performed INT DEFAULT 0,
            average_rating DECIMAL(3,2) DEFAULT 0.00,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            
            FOREIGN KEY (user_id) REFERENCES SAI_users(id) ON DELETE CASCADE,
            FOREIGN KEY (approved_by) REFERENCES SAI_users(id) ON DELETE SET NULL,
            
            INDEX idx_user_id (user_id),
            INDEX idx_approval_status (approval_status),
            INDEX idx_specialization (specialization)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "      ✓ SAI_pandit_profiles created\n";

    // ============================================================
    // TABLE 3: SAI_families
    // ============================================================
    echo "      Creating SAI_families...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS SAI_families (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            family_name VARCHAR(100) NOT NULL,
            gotra VARCHAR(50) NULL,
            nakshatra VARCHAR(50) NULL,
            kul_devta VARCHAR(100) NULL,
            family_deity VARCHAR(100) NULL,
            address TEXT NULL,
            city VARCHAR(100) NULL,
            state VARCHAR(100) NULL,
            pincode VARCHAR(10) NULL,
            country VARCHAR(50) DEFAULT 'India',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            
            FOREIGN KEY (user_id) REFERENCES SAI_users(id) ON DELETE CASCADE,
            
            INDEX idx_user_id (user_id),
            INDEX idx_gotra (gotra)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "      ✓ SAI_families created\n";

    // ============================================================
    // TABLE 4: SAI_family_members
    // ============================================================
    echo "      Creating SAI_family_members...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS SAI_family_members (
            id INT AUTO_INCREMENT PRIMARY KEY,
            family_id INT NOT NULL,
            name VARCHAR(100) NOT NULL,
            date_of_birth DATE NULL,
            birth_time TIME NULL,
            birth_place VARCHAR(150) NULL,
            gender ENUM('male', 'female', 'other') NOT NULL,
            relation VARCHAR(50) NOT NULL,
            nakshatra VARCHAR(50) NULL,
            rashi VARCHAR(50) NULL,
            is_primary BOOLEAN DEFAULT FALSE,
            occupation VARCHAR(100) NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            
            FOREIGN KEY (family_id) REFERENCES SAI_families(id) ON DELETE CASCADE,
            
            INDEX idx_family_id (family_id),
            INDEX idx_relation (relation)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "      ✓ SAI_family_members created\n";

    // ============================================================
    // TABLE 5: SAI_rituals
    // ============================================================
    echo "      Creating SAI_rituals...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS SAI_rituals (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(150) NOT NULL,
            name_sanskrit VARCHAR(150) NULL,
            community_name VARCHAR(150) NULL,
            religion VARCHAR(100) NULL,
            category VARCHAR(100) NOT NULL,
            sub_category VARCHAR(100) NULL,
            description TEXT NULL,
            significance TEXT NULL,
            duration_minutes INT DEFAULT 60,
            difficulty ENUM('easy', 'medium', 'hard') DEFAULT 'medium',
            occasion_type VARCHAR(100) NULL,
            best_time VARCHAR(100) NULL,
            best_tithi VARCHAR(100) NULL,
            deity VARCHAR(100) NULL,
            is_active BOOLEAN DEFAULT TRUE,
            is_featured BOOLEAN DEFAULT FALSE,
            view_count INT DEFAULT 0,
            created_by INT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            
            FOREIGN KEY (created_by) REFERENCES SAI_users(id) ON DELETE SET NULL,
            
            INDEX idx_category (category),
            INDEX idx_difficulty (difficulty),
            INDEX idx_is_active (is_active),
            INDEX idx_occasion_type (occasion_type),
            INDEX idx_community_name (community_name),
            INDEX idx_religion (religion)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "      ✓ SAI_rituals created\n";

    // ------------------------------------------------------------
    // UPDATE: Add religion and community_name columns if missing
    // ------------------------------------------------------------
    try {
        $columns = $pdo->query("SHOW COLUMNS FROM SAI_rituals LIKE 'religion'")->fetchAll();
        if (empty($columns)) {
            echo "      - Adding 'religion' column to SAI_rituals...\n";
            $pdo->exec("ALTER TABLE SAI_rituals ADD COLUMN religion VARCHAR(100) NULL AFTER name_sanskrit");
            $pdo->exec("CREATE INDEX idx_religion ON SAI_rituals(religion)");
        }
        
        $columns = $pdo->query("SHOW COLUMNS FROM SAI_rituals LIKE 'community_name'")->fetchAll();
        if (empty($columns)) {
            echo "      - Adding 'community_name' column to SAI_rituals...\n";
            $pdo->exec("ALTER TABLE SAI_rituals ADD COLUMN community_name VARCHAR(150) NULL AFTER name_sanskrit");
            $pdo->exec("CREATE INDEX idx_community_name ON SAI_rituals(community_name)");
        }
    } catch (PDOException $e) {
        echo "      ! Warning: Could not update SAI_rituals columns: " . $e->getMessage() . "\n";
    }

    // ============================================================
    // TABLE 6: SAI_ritual_steps
    // ============================================================
    echo "      Creating SAI_ritual_steps...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS SAI_ritual_steps (
            id INT AUTO_INCREMENT PRIMARY KEY,
            ritual_id INT NOT NULL,
            step_number INT NOT NULL,
            title VARCHAR(150) NOT NULL,
            title_sanskrit VARCHAR(150) NULL,
            description TEXT NULL,
            mantra TEXT NULL,
            mantra_meaning TEXT NULL,
            duration_minutes INT DEFAULT 5,
            is_optional BOOLEAN DEFAULT FALSE,
            special_instructions TEXT NULL,
            audio_url VARCHAR(255) NULL,
            video_url VARCHAR(255) NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            
            FOREIGN KEY (ritual_id) REFERENCES SAI_rituals(id) ON DELETE CASCADE,
            
            INDEX idx_ritual_id (ritual_id),
            INDEX idx_step_number (step_number),
            UNIQUE KEY unique_ritual_step (ritual_id, step_number)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "      ✓ SAI_ritual_steps created\n";

    // ============================================================
    // TABLE 7: SAI_ritual_items
    // ============================================================
    echo "      Creating SAI_ritual_items...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS SAI_ritual_items (
            id INT AUTO_INCREMENT PRIMARY KEY,
            ritual_id INT NOT NULL,
            item_name VARCHAR(100) NOT NULL,
            item_name_local VARCHAR(100) NULL,
            quantity DECIMAL(10,2) DEFAULT 1.00,
            unit VARCHAR(20) DEFAULT 'piece',
            is_mandatory BOOLEAN DEFAULT TRUE,
            approximate_cost DECIMAL(10,2) NULL,
            category VARCHAR(50) DEFAULT 'general',
            description TEXT NULL,
            where_to_buy VARCHAR(200) NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            
            FOREIGN KEY (ritual_id) REFERENCES SAI_rituals(id) ON DELETE CASCADE,
            
            INDEX idx_ritual_id (ritual_id),
            INDEX idx_is_mandatory (is_mandatory),
            INDEX idx_category (category)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "      ✓ SAI_ritual_items created\n";

    // ============================================================
    // TABLE 8: SAI_custom_rituals
    // ============================================================
    echo "      Creating SAI_custom_rituals...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS SAI_custom_rituals (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            base_ritual_id INT NULL,
            name VARCHAR(150) NOT NULL,
            description TEXT NULL,
            purpose TEXT NULL,
            scheduled_date DATE NULL,
            scheduled_time TIME NULL,
            venue VARCHAR(200) NULL,
            special_requirements TEXT NULL,
            budget DECIMAL(12,2) NULL,
            status ENUM('draft', 'submitted', 'under_review', 'approved', 'rejected', 'completed') DEFAULT 'draft',
            validated_by INT NULL,
            validation_notes TEXT NULL,
            validated_at DATETIME NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            
            FOREIGN KEY (user_id) REFERENCES SAI_users(id) ON DELETE CASCADE,
            FOREIGN KEY (base_ritual_id) REFERENCES SAI_rituals(id) ON DELETE SET NULL,
            FOREIGN KEY (validated_by) REFERENCES SAI_users(id) ON DELETE SET NULL,
            
            INDEX idx_user_id (user_id),
            INDEX idx_base_ritual_id (base_ritual_id),
            INDEX idx_status (status),
            INDEX idx_scheduled_date (scheduled_date)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "      ✓ SAI_custom_rituals created\n";

    // ============================================================
    // TABLE 9: SAI_custom_ritual_steps
    // ============================================================
    echo "      Creating SAI_custom_ritual_steps...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS SAI_custom_ritual_steps (
            id INT AUTO_INCREMENT PRIMARY KEY,
            custom_ritual_id INT NOT NULL,
            step_number INT NOT NULL,
            title VARCHAR(150) NOT NULL,
            description TEXT NULL,
            mantra TEXT NULL,
            duration_minutes INT DEFAULT 5,
            is_from_base BOOLEAN DEFAULT FALSE,
            base_step_id INT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            
            FOREIGN KEY (custom_ritual_id) REFERENCES SAI_custom_rituals(id) ON DELETE CASCADE,
            FOREIGN KEY (base_step_id) REFERENCES SAI_ritual_steps(id) ON DELETE SET NULL,
            
            INDEX idx_custom_ritual_id (custom_ritual_id),
            UNIQUE KEY unique_custom_ritual_step (custom_ritual_id, step_number)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "      ✓ SAI_custom_ritual_steps created\n";

    // ============================================================
    // TABLE 10: SAI_pandit_assignments
    // ============================================================
    echo "      Creating SAI_pandit_assignments...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS SAI_pandit_assignments (
            id INT AUTO_INCREMENT PRIMARY KEY,
            ritual_id INT NULL,
            custom_ritual_id INT NULL,
            pandit_id INT NOT NULL,
            user_id INT NOT NULL,
            assigned_by INT NULL,
            scheduled_date DATE NULL,
            scheduled_time TIME NULL,
            end_time TIME NULL,
            venue VARCHAR(200) NULL,
            venue_address TEXT NULL,
            status ENUM('pending', 'confirmed', 'in_progress', 'completed', 'cancelled') DEFAULT 'pending',
            pandit_notes TEXT NULL,
            user_notes TEXT NULL,
            cancellation_reason TEXT NULL,
            cancelled_by INT NULL,
            amount DECIMAL(12,2) NULL,
            payment_status ENUM('pending', 'partial', 'completed', 'refunded') DEFAULT 'pending',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            
            FOREIGN KEY (ritual_id) REFERENCES SAI_rituals(id) ON DELETE SET NULL,
            FOREIGN KEY (custom_ritual_id) REFERENCES SAI_custom_rituals(id) ON DELETE SET NULL,
            FOREIGN KEY (pandit_id) REFERENCES SAI_users(id) ON DELETE CASCADE,
            FOREIGN KEY (user_id) REFERENCES SAI_users(id) ON DELETE CASCADE,
            FOREIGN KEY (assigned_by) REFERENCES SAI_users(id) ON DELETE SET NULL,
            FOREIGN KEY (cancelled_by) REFERENCES SAI_users(id) ON DELETE SET NULL,
            
            INDEX idx_pandit_id (pandit_id),
            INDEX idx_user_id (user_id),
            INDEX idx_status (status),
            INDEX idx_scheduled_date (scheduled_date)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "      ✓ SAI_pandit_assignments created\n";

    // ============================================================
    // TABLE 11: SAI_pandit_qna
    // ============================================================
    echo "      Creating SAI_pandit_qna...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS SAI_pandit_qna (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            pandit_id INT NULL,
            ritual_id INT NULL,
            assignment_id INT NULL,
            question TEXT NOT NULL,
            question_category VARCHAR(50) DEFAULT 'general',
            answer TEXT NULL,
            status ENUM('pending', 'answered', 'closed') DEFAULT 'pending',
            is_public BOOLEAN DEFAULT FALSE,
            helpful_count INT DEFAULT 0,
            answered_at DATETIME NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            
            FOREIGN KEY (user_id) REFERENCES SAI_users(id) ON DELETE CASCADE,
            FOREIGN KEY (pandit_id) REFERENCES SAI_users(id) ON DELETE SET NULL,
            FOREIGN KEY (ritual_id) REFERENCES SAI_rituals(id) ON DELETE SET NULL,
            FOREIGN KEY (assignment_id) REFERENCES SAI_pandit_assignments(id) ON DELETE SET NULL,
            
            INDEX idx_user_id (user_id),
            INDEX idx_pandit_id (pandit_id),
            INDEX idx_status (status),
            INDEX idx_is_public (is_public)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "      ✓ SAI_pandit_qna created\n";

    // ============================================================
    // TABLE 12: SAI_ritual_reviews
    // ============================================================
    echo "      Creating SAI_ritual_reviews...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS SAI_ritual_reviews (
            id INT AUTO_INCREMENT PRIMARY KEY,
            ritual_id INT NULL,
            custom_ritual_id INT NULL,
            assignment_id INT NULL,
            user_id INT NOT NULL,
            pandit_id INT NULL,
            rating TINYINT NOT NULL CHECK (rating >= 1 AND rating <= 5),
            review_title VARCHAR(150) NULL,
            review_text TEXT NULL,
            experience_rating TINYINT NULL,
            punctuality_rating TINYINT NULL,
            knowledge_rating TINYINT NULL,
            is_verified BOOLEAN DEFAULT FALSE,
            is_featured BOOLEAN DEFAULT FALSE,
            admin_notes TEXT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            
            FOREIGN KEY (ritual_id) REFERENCES SAI_rituals(id) ON DELETE SET NULL,
            FOREIGN KEY (custom_ritual_id) REFERENCES SAI_custom_rituals(id) ON DELETE SET NULL,
            FOREIGN KEY (assignment_id) REFERENCES SAI_pandit_assignments(id) ON DELETE SET NULL,
            FOREIGN KEY (user_id) REFERENCES SAI_users(id) ON DELETE CASCADE,
            FOREIGN KEY (pandit_id) REFERENCES SAI_users(id) ON DELETE SET NULL,
            
            INDEX idx_ritual_id (ritual_id),
            INDEX idx_pandit_id (pandit_id),
            INDEX idx_rating (rating),
            INDEX idx_is_verified (is_verified)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "      ✓ SAI_ritual_reviews created\n";

    // ============================================================
    // TABLE 13: SAI_ai_requests
    // ============================================================
    echo "      Creating SAI_ai_requests...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS SAI_ai_requests (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            request_type VARCHAR(50) NOT NULL,
            request_category VARCHAR(50) DEFAULT 'general',
            prompt TEXT NOT NULL,
            context_data JSON NULL,
            response TEXT NULL,
            response_data JSON NULL,
            model_used VARCHAR(50) DEFAULT 'mock',
            status ENUM('pending', 'processing', 'completed', 'failed', 'moderated') DEFAULT 'pending',
            tokens_used INT DEFAULT 0,
            processing_time_ms INT NULL,
            error_message TEXT NULL,
            is_flagged BOOLEAN DEFAULT FALSE,
            flag_reason VARCHAR(200) NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            completed_at DATETIME NULL,
            
            FOREIGN KEY (user_id) REFERENCES SAI_users(id) ON DELETE CASCADE,
            
            INDEX idx_user_id (user_id),
            INDEX idx_request_type (request_type),
            INDEX idx_status (status),
            INDEX idx_is_flagged (is_flagged),
            INDEX idx_created_at (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "      ✓ SAI_ai_requests created\n";

    // ============================================================
    // TABLE 14: SAI_ai_logs
    // ============================================================
    echo "      Creating SAI_ai_logs...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS SAI_ai_logs (
            id INT AUTO_INCREMENT PRIMARY KEY,
            ai_request_id INT NULL,
            user_id INT NULL,
            log_level ENUM('debug', 'info', 'warning', 'error', 'critical') DEFAULT 'info',
            event_type VARCHAR(50) NOT NULL,
            message TEXT NOT NULL,
            metadata JSON NULL,
            ip_address VARCHAR(45) NULL,
            user_agent VARCHAR(255) NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            
            FOREIGN KEY (ai_request_id) REFERENCES SAI_ai_requests(id) ON DELETE SET NULL,
            FOREIGN KEY (user_id) REFERENCES SAI_users(id) ON DELETE SET NULL,
            
            INDEX idx_ai_request_id (ai_request_id),
            INDEX idx_log_level (log_level),
            INDEX idx_event_type (event_type),
            INDEX idx_created_at (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "      ✓ SAI_ai_logs created\n";

    // ============================================================
    // TABLE 15: SAI_shopping_list
    // ============================================================
    echo "      Creating SAI_shopping_list...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS SAI_shopping_list (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            ritual_id INT NULL,
            custom_ritual_id INT NULL,
            assignment_id INT NULL,
            item_name VARCHAR(100) NOT NULL,
            item_name_local VARCHAR(100) NULL,
            quantity DECIMAL(10,2) DEFAULT 1.00,
            unit VARCHAR(20) DEFAULT 'piece',
            category VARCHAR(50) DEFAULT 'general',
            estimated_cost DECIMAL(10,2) NULL,
            actual_cost DECIMAL(10,2) NULL,
            is_purchased BOOLEAN DEFAULT FALSE,
            purchased_at DATETIME NULL,
            store_name VARCHAR(150) NULL,
            notes TEXT NULL,
            priority ENUM('low', 'medium', 'high') DEFAULT 'medium',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            
            FOREIGN KEY (user_id) REFERENCES SAI_users(id) ON DELETE CASCADE,
            FOREIGN KEY (ritual_id) REFERENCES SAI_rituals(id) ON DELETE SET NULL,
            FOREIGN KEY (custom_ritual_id) REFERENCES SAI_custom_rituals(id) ON DELETE SET NULL,
            FOREIGN KEY (assignment_id) REFERENCES SAI_pandit_assignments(id) ON DELETE SET NULL,
            
            INDEX idx_user_id (user_id),
            INDEX idx_is_purchased (is_purchased),
            INDEX idx_priority (priority)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "      ✓ SAI_shopping_list created\n";

    // ============================================================
    // TABLE 16: SAI_cultural_insights
    // ============================================================
    echo "      Creating SAI_cultural_insights...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS SAI_cultural_insights (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(200) NOT NULL,
            slug VARCHAR(220) UNIQUE,
            category VARCHAR(100) NOT NULL,
            sub_category VARCHAR(100) NULL,
            content TEXT NOT NULL,
            summary TEXT NULL,
            featured_image VARCHAR(255) NULL,
            region VARCHAR(100) NULL,
            language VARCHAR(50) DEFAULT 'Hindi',
            source VARCHAR(255) NULL,
            source_url VARCHAR(255) NULL,
            tags VARCHAR(255) NULL,
            related_rituals JSON NULL,
            is_published BOOLEAN DEFAULT TRUE,
            is_featured BOOLEAN DEFAULT FALSE,
            view_count INT DEFAULT 0,
            created_by INT NULL,
            reviewed_by INT NULL,
            published_at DATETIME NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            
            FOREIGN KEY (created_by) REFERENCES SAI_users(id) ON DELETE SET NULL,
            FOREIGN KEY (reviewed_by) REFERENCES SAI_users(id) ON DELETE SET NULL,
            
            INDEX idx_category (category),
            INDEX idx_region (region),
            INDEX idx_is_published (is_published),
            INDEX idx_is_featured (is_featured),
            FULLTEXT INDEX ft_content (title, content, summary)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "      ✓ SAI_cultural_insights created\n";

    // ============================================================
    // TABLE 17: SAI_admin_dashboard_stats
    // ============================================================
    echo "      Creating SAI_admin_dashboard_stats...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS SAI_admin_dashboard_stats (
            id INT AUTO_INCREMENT PRIMARY KEY,
            stat_date DATE NOT NULL UNIQUE,
            total_users INT DEFAULT 0,
            new_users INT DEFAULT 0,
            total_pandits INT DEFAULT 0,
            approved_pandits INT DEFAULT 0,
            pending_pandits INT DEFAULT 0,
            total_rituals INT DEFAULT 0,
            active_rituals INT DEFAULT 0,
            total_custom_rituals INT DEFAULT 0,
            completed_custom_rituals INT DEFAULT 0,
            total_assignments INT DEFAULT 0,
            completed_assignments INT DEFAULT 0,
            cancelled_assignments INT DEFAULT 0,
            total_ai_requests INT DEFAULT 0,
            successful_ai_requests INT DEFAULT 0,
            failed_ai_requests INT DEFAULT 0,
            total_reviews INT DEFAULT 0,
            average_rating DECIMAL(3,2) DEFAULT 0.00,
            total_questions INT DEFAULT 0,
            answered_questions INT DEFAULT 0,
            active_users_today INT DEFAULT 0,
            revenue_today DECIMAL(12,2) DEFAULT 0.00,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            
            INDEX idx_stat_date (stat_date)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "      ✓ SAI_admin_dashboard_stats created\n";

    // ============================================================
    // TABLE 18: SAI_orders
    // ============================================================
    echo "      Creating SAI_orders...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS SAI_orders (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            shop_name VARCHAR(200) NOT NULL,
            shop_location VARCHAR(500) NULL,
            shop_type VARCHAR(100) NULL,
            user_latitude DECIMAL(10,8) NULL,
            user_longitude DECIMAL(11,8) NULL,
            user_address VARCHAR(500) NULL,
            total_items INT DEFAULT 0,
            estimated_total DECIMAL(12,2) DEFAULT 0.00,
            status ENUM('pending', 'confirmed', 'completed', 'cancelled') DEFAULT 'pending',
            notes TEXT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            
            FOREIGN KEY (user_id) REFERENCES SAI_users(id) ON DELETE CASCADE,
            
            INDEX idx_user_id (user_id),
            INDEX idx_status (status),
            INDEX idx_created_at (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "      ✓ SAI_orders created\n";

    // ============================================================
    // TABLE 19: SAI_order_items
    // ============================================================
    echo "      Creating SAI_order_items...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS SAI_order_items (
            id INT AUTO_INCREMENT PRIMARY KEY,
            order_id INT NOT NULL,
            shopping_list_id INT NULL,
            item_name VARCHAR(100) NOT NULL,
            item_name_local VARCHAR(100) NULL,
            quantity DECIMAL(10,2) DEFAULT 1.00,
            unit VARCHAR(20) DEFAULT 'piece',
            estimated_cost DECIMAL(10,2) NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            
            FOREIGN KEY (order_id) REFERENCES SAI_orders(id) ON DELETE CASCADE,
            FOREIGN KEY (shopping_list_id) REFERENCES SAI_shopping_list(id) ON DELETE SET NULL,
            
            INDEX idx_order_id (order_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "      ✓ SAI_order_items created\n";

    // ============================================================
    // TABLE 20: SAI_user_rituals
    // ============================================================
    echo "      Creating SAI_user_rituals...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS SAI_user_rituals (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            global_ritual_id INT NULL,
            name VARCHAR(200) NOT NULL,
            name_sanskrit VARCHAR(200) NULL,
            community_name VARCHAR(150) NULL,
            religion VARCHAR(100) NULL,
            category VARCHAR(100) NULL,
            description TEXT NULL,
            significance TEXT NULL,
            duration_minutes INT DEFAULT 60,
            difficulty ENUM('easy', 'medium', 'hard') DEFAULT 'medium',
            deity VARCHAR(100) NULL,
            best_time VARCHAR(100) NULL,
            is_ai_generated BOOLEAN DEFAULT FALSE,
            ai_generation_prompt TEXT NULL,
            notes TEXT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            
            FOREIGN KEY (user_id) REFERENCES SAI_users(id) ON DELETE CASCADE,
            FOREIGN KEY (global_ritual_id) REFERENCES SAI_rituals(id) ON DELETE SET NULL,
            
            INDEX idx_user_id (user_id),
            INDEX idx_global_ritual_id (global_ritual_id),
            INDEX idx_community (community_name),
            INDEX idx_religion (religion)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "      ✓ SAI_user_rituals created\n";

    // ============================================================
    // TABLE 21: SAI_user_ritual_steps
    // ============================================================
    echo "      Creating SAI_user_ritual_steps...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS SAI_user_ritual_steps (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_ritual_id INT NOT NULL,
            step_number INT NOT NULL,
            title VARCHAR(200) NOT NULL,
            title_sanskrit VARCHAR(200) NULL,
            description TEXT NULL,
            mantra TEXT NULL,
            mantra_meaning TEXT NULL,
            duration_minutes INT DEFAULT 5,
            is_optional BOOLEAN DEFAULT FALSE,
            special_instructions TEXT NULL,
            items_needed TEXT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            
            FOREIGN KEY (user_ritual_id) REFERENCES SAI_user_rituals(id) ON DELETE CASCADE,
            
            INDEX idx_user_ritual_id (user_ritual_id),
            UNIQUE KEY unique_user_ritual_step (user_ritual_id, step_number)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "      ✓ SAI_user_ritual_steps created\n";

    // ============================================================
    // TABLE 22: SAI_user_ritual_items
    // ============================================================
    echo "      Creating SAI_user_ritual_items...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS SAI_user_ritual_items (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_ritual_id INT NOT NULL,
            item_name VARCHAR(150) NOT NULL,
            item_name_local VARCHAR(150) NULL,
            quantity DECIMAL(10,2) DEFAULT 1.00,
            unit VARCHAR(30) DEFAULT 'piece',
            is_mandatory BOOLEAN DEFAULT TRUE,
            description TEXT NULL,
            alternatives TEXT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            
            FOREIGN KEY (user_ritual_id) REFERENCES SAI_user_rituals(id) ON DELETE CASCADE,
            
            INDEX idx_user_ritual_id (user_ritual_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "      ✓ SAI_user_ritual_items created\n";

    // ============================================================
    // TABLE 23: SAI_ritual_progress
    // ============================================================
    echo "      Creating SAI_ritual_progress...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS SAI_ritual_progress (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            user_ritual_id INT NOT NULL,
            session_id VARCHAR(64) NOT NULL,
            current_step INT DEFAULT 1,
            status ENUM('not_started', 'in_progress', 'paused', 'completed') DEFAULT 'not_started',
            started_at DATETIME NULL,
            completed_at DATETIME NULL,
            notes TEXT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            
            FOREIGN KEY (user_id) REFERENCES SAI_users(id) ON DELETE CASCADE,
            FOREIGN KEY (user_ritual_id) REFERENCES SAI_user_rituals(id) ON DELETE CASCADE,
            
            INDEX idx_user_id (user_id),
            INDEX idx_user_ritual_id (user_ritual_id),
            INDEX idx_session_id (session_id),
            INDEX idx_status (status)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "      ✓ SAI_ritual_progress created\n";

    // ============================================================
    // TABLE 24: SAI_step_completion
    // ============================================================
    echo "      Creating SAI_step_completion...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS SAI_step_completion (
            id INT AUTO_INCREMENT PRIMARY KEY,
            progress_id INT NOT NULL,
            step_id INT NOT NULL,
            step_number INT NOT NULL,
            is_completed BOOLEAN DEFAULT FALSE,
            completed_at DATETIME NULL,
            notes TEXT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            
            FOREIGN KEY (progress_id) REFERENCES SAI_ritual_progress(id) ON DELETE CASCADE,
            FOREIGN KEY (step_id) REFERENCES SAI_user_ritual_steps(id) ON DELETE CASCADE,
            
            INDEX idx_progress_id (progress_id),
            UNIQUE KEY unique_progress_step (progress_id, step_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "      ✓ SAI_step_completion created\n";

    // Re-enable foreign key checks
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");

    // ============================================================
    // ADDITIONAL MIGRATION: Add assigned_pandit_id to SAI_custom_rituals
    // ============================================================
    echo "\n      Adding assigned_pandit_id column to SAI_custom_rituals...\n";
    $result = $pdo->query("SHOW COLUMNS FROM SAI_custom_rituals LIKE 'assigned_pandit_id'");
    if ($result->rowCount() > 0) {
        echo "      ✓ Column 'assigned_pandit_id' already exists\n";
    } else {
        try {
            $pdo->exec("
                ALTER TABLE SAI_custom_rituals 
                ADD COLUMN assigned_pandit_id INT NULL AFTER user_id,
                ADD INDEX idx_assigned_pandit (assigned_pandit_id),
                ADD CONSTRAINT fk_custom_ritual_pandit FOREIGN KEY (assigned_pandit_id) REFERENCES SAI_users(id) ON DELETE SET NULL
            ");
            echo "      ✓ Column 'assigned_pandit_id' added successfully\n";
        } catch (\Exception $e) {
            echo "      ! Could not add assigned_pandit_id: " . $e->getMessage() . "\n";
        }
    }

    // ============================================================
    // ADDITIONAL MIGRATION: Add booking_purpose to SAI_pandit_assignments
    // ============================================================
    echo "\n      Adding booking_purpose column to SAI_pandit_assignments...\n";
    $result = $pdo->query("SHOW COLUMNS FROM SAI_pandit_assignments LIKE 'booking_purpose'");
    if ($result->rowCount() > 0) {
        echo "      ✓ Column 'booking_purpose' already exists\n";
    } else {
        try {
            $pdo->exec("
                ALTER TABLE SAI_pandit_assignments 
                ADD COLUMN booking_purpose TEXT NULL AFTER user_notes
            ");
            echo "      ✓ Column 'booking_purpose' added successfully\n";
        } catch (\Exception $e) {
            echo "      ! Could not add booking_purpose: " . $e->getMessage() . "\n";
        }
    }

    echo "\n[3/3] Verifying tables...\n";

    // Verify all tables exist
    $tables = [
        'SAI_users',
        'SAI_pandit_profiles',
        'SAI_families',
        'SAI_family_members',
        'SAI_rituals',
        'SAI_ritual_steps',
        'SAI_ritual_items',
        'SAI_custom_rituals',
        'SAI_custom_ritual_steps',
        'SAI_pandit_assignments',
        'SAI_pandit_qna',
        'SAI_ritual_reviews',
        'SAI_ai_requests',
        'SAI_ai_logs',
        'SAI_shopping_list',
        'SAI_cultural_insights',
        'SAI_admin_dashboard_stats',
        'SAI_orders',
        'SAI_order_items',
        'SAI_user_rituals',
        'SAI_user_ritual_steps',
        'SAI_user_ritual_items',
        'SAI_ritual_progress',
        'SAI_step_completion',
    ];

    $pdo->exec("USE SAI");
    $stmt = $pdo->query("SHOW TABLES FROM SAI");
    $existingTables = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // Normalize to lowercase for comparison
    $existingTablesLower = array_map('strtolower', $existingTables);

    $allExist = true;
    foreach ($tables as $table) {
        if (in_array(strtolower($table), $existingTablesLower)) {
            echo "      ✓ $table exists\n";
        } else {
            echo "      ✗ $table MISSING!\n";
            $allExist = false;
        }
    }

    echo "\n==============================================\n";
    if ($allExist) {
        echo "  ✓ Migration completed successfully!\n";
        echo "  ✓ All 24 tables created with constraints\n";
    } else {
        echo "  ✗ Migration completed with errors!\n";
    }
    echo "==============================================\n\n";

    echo "Next steps:\n";
    echo "  1. Run: php app/database/seed_admin.php\n";
    echo "  2. Run: php -S localhost:8000 -t public\n";
    echo "  3. Open: http://localhost:8000\n\n";

} catch (PDOException $e) {
    echo "\n✗ Migration FAILED!\n";
    echo "Error: " . $e->getMessage() . "\n\n";
    exit(1);
} catch (Exception $e) {
    echo "\n✗ Migration FAILED!\n";
    echo "Error: " . $e->getMessage() . "\n\n";
    exit(1);
}