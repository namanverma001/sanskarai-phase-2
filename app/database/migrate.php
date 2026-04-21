<?php
/**
 * Sanskar AI - Database Migration Script
 * ========================================
 * Creates all 42 tables with proper constraints and relationships
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

    // ============================================================
    // ADDITIONAL MIGRATION: Add community_name and religion to SAI_users
    // (from add_user_profile_fields.php)
    // ============================================================
    echo "\n      Adding profile fields to SAI_users...\n";
    try {
        $columns = $pdo->query("SHOW COLUMNS FROM SAI_users LIKE 'community_name'")->fetchAll();
        if (empty($columns)) {
            $pdo->exec("ALTER TABLE SAI_users ADD COLUMN community_name VARCHAR(150) NULL AFTER mobile");
            echo "      ✓ 'community_name' added to SAI_users\n";
        } else {
            echo "      - 'community_name' already exists in SAI_users\n";
        }

        $columns = $pdo->query("SHOW COLUMNS FROM SAI_users LIKE 'religion'")->fetchAll();
        if (empty($columns)) {
            $pdo->exec("ALTER TABLE SAI_users ADD COLUMN religion VARCHAR(100) NULL AFTER community_name");
            echo "      ✓ 'religion' added to SAI_users\n";
        } else {
            echo "      - 'religion' already exists in SAI_users\n";
        }
    } catch (\Exception $e) {
        echo "      ! Could not add user profile fields: " . $e->getMessage() . "\n";
    }

    // ============================================================
    // ADDITIONAL MIGRATION: Add kul_devi_devta to SAI_users
    // (from run_add_kul_devi_devta.php)
    // ============================================================
    echo "\n      Adding kul_devi_devta to SAI_users...\n";
    try {
        $columns = $pdo->query("SHOW COLUMNS FROM SAI_users LIKE 'kul_devi_devta'")->fetchAll();
        if (empty($columns)) {
            $pdo->exec("ALTER TABLE SAI_users ADD COLUMN kul_devi_devta VARCHAR(150) NULL AFTER community_name");
            echo "      ✓ 'kul_devi_devta' added to SAI_users\n";
        } else {
            echo "      - 'kul_devi_devta' already exists in SAI_users\n";
        }
    } catch (\Exception $e) {
        echo "      ! Could not add kul_devi_devta: " . $e->getMessage() . "\n";
    }

    // ============================================================
    // ADDITIONAL MIGRATION: Add country to SAI_families
    // (from add_country_to_families.php)
    // ============================================================
    echo "\n      Adding country column to SAI_families...\n";
    try {
        $columns = $pdo->query("SHOW COLUMNS FROM SAI_families LIKE 'country'")->fetchAll();
        if (empty($columns)) {
            $pdo->exec("ALTER TABLE SAI_families ADD COLUMN country VARCHAR(100) NULL AFTER state");
            echo "      ✓ 'country' added to SAI_families\n";
        } else {
            echo "      - 'country' already exists in SAI_families\n";
        }
    } catch (\Exception $e) {
        echo "      ! Could not add country to families: " . $e->getMessage() . "\n";
    }

    // ============================================================
    // ADDITIONAL MIGRATION: Create SAI_password_resets table
    // (from add_password_resets_table.php)
    // ============================================================
    echo "\n      Creating SAI_password_resets...\n";
    try {
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS SAI_password_resets (
                id INT UNSIGNED NOT NULL AUTO_INCREMENT,
                user_id INT UNSIGNED NOT NULL,
                token_hash VARCHAR(255) NOT NULL,
                expires_at DATETIME NOT NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                INDEX idx_user_id (user_id)
            ) ENGINE=InnoDB
        ");
        echo "      ✓ SAI_password_resets created\n";
    } catch (\Exception $e) {
        echo "      ! Could not create SAI_password_resets: " . $e->getMessage() . "\n";
    }

    // ============================================================
    // ADDITIONAL MIGRATION: Create SAI_vendors table
    // (from create_vendors_table.php)
    // ============================================================
    echo "\n      Creating SAI_vendors...\n";
    try {
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS SAI_vendors (
                id INT UNSIGNED NOT NULL AUTO_INCREMENT,
                name VARCHAR(150) NOT NULL,
                category ENUM('photographer', 'catering', 'decorator', 'florist', 'music', 'lighting', 'tent_house', 'makeup_artist', 'mehendi_artist', 'videographer', 'invitation_cards', 'travel', 'other') NOT NULL DEFAULT 'other',
                description TEXT NULL,
                contact_person VARCHAR(100) NULL,
                email VARCHAR(150) NULL,
                phone VARCHAR(20) NOT NULL,
                alternate_phone VARCHAR(20) NULL,
                whatsapp VARCHAR(20) NULL,
                website VARCHAR(255) NULL,
                address_line1 VARCHAR(255) NOT NULL,
                address_line2 VARCHAR(255) NULL,
                city VARCHAR(100) NOT NULL,
                state VARCHAR(100) NOT NULL,
                pincode VARCHAR(10) NOT NULL,
                country VARCHAR(100) DEFAULT 'India',
                latitude DECIMAL(10, 8) NOT NULL,
                longitude DECIMAL(11, 8) NOT NULL,
                service_area_km INT DEFAULT 50 COMMENT 'Service radius in kilometers',
                min_price DECIMAL(12, 2) NULL COMMENT 'Minimum service price',
                max_price DECIMAL(12, 2) NULL COMMENT 'Maximum service price',
                services_offered TEXT NULL COMMENT 'JSON array of services',
                logo_url VARCHAR(255) NULL,
                gallery_images TEXT NULL COMMENT 'JSON array of image URLs',
                average_rating DECIMAL(3, 2) DEFAULT 0.00,
                total_reviews INT DEFAULT 0,
                is_active TINYINT(1) DEFAULT 1,
                is_featured TINYINT(1) DEFAULT 0,
                is_verified TINYINT(1) DEFAULT 0,
                added_by INT UNSIGNED NULL COMMENT 'Admin who added',
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                INDEX idx_category (category),
                INDEX idx_city (city),
                INDEX idx_state (state),
                INDEX idx_pincode (pincode),
                INDEX idx_is_active (is_active),
                INDEX idx_is_featured (is_featured),
                INDEX idx_location (latitude, longitude),
                INDEX idx_rating (average_rating DESC)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        echo "      ✓ SAI_vendors created\n";
    } catch (\Exception $e) {
        echo "      ! Could not create SAI_vendors: " . $e->getMessage() . "\n";
    }

    // ============================================================
    // ADDITIONAL MIGRATION: Add map_url to SAI_vendors
    // (from add_vendor_map_url.php)
    // ============================================================
    echo "\n      Adding map_url to SAI_vendors...\n";
    try {
        $stmt = $pdo->query("SHOW COLUMNS FROM SAI_vendors LIKE 'map_url'");
        if ($stmt->rowCount() === 0) {
            $pdo->exec("
                ALTER TABLE SAI_vendors 
                ADD COLUMN map_url VARCHAR(500) NULL 
                COMMENT 'Google Maps or custom directions URL' 
                AFTER longitude
            ");
            echo "      ✓ 'map_url' added to SAI_vendors\n";
        } else {
            echo "      - 'map_url' already exists in SAI_vendors\n";
        }
    } catch (\Exception $e) {
        echo "      ! Could not add map_url: " . $e->getMessage() . "\n";
    }

    // ============================================================
    // ADDITIONAL MIGRATION: Create SAI_ritual_embeddings table
    // (from migrate_embeddings.php)
    // ============================================================
    echo "\n      Creating SAI_ritual_embeddings...\n";
    try {
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS SAI_ritual_embeddings (
                id INT AUTO_INCREMENT PRIMARY KEY,
                ritual_id INT NOT NULL,
                ritual_name VARCHAR(150) NULL,
                community_name VARCHAR(150) NULL,
                religion VARCHAR(100) NULL,
                combined_text TEXT NULL,
                embedding JSON NULL,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                UNIQUE KEY unique_ritual (ritual_id),
                FOREIGN KEY (ritual_id) REFERENCES SAI_rituals(id) ON DELETE CASCADE,
                INDEX idx_ritual_id (ritual_id),
                INDEX idx_community_name (community_name),
                INDEX idx_religion (religion)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        echo "      ✓ SAI_ritual_embeddings created\n";
    } catch (\Exception $e) {
        echo "      ! Could not create SAI_ritual_embeddings: " . $e->getMessage() . "\n";
    }

    // ============================================================
    // ADDITIONAL MIGRATION: Widen ritual columns for longer AI content
    // (from widen_ritual_columns.php)
    // ============================================================
    echo "\n      Widening ritual columns...\n";
    $widenQueries = [
        "ALTER TABLE SAI_rituals MODIFY best_time VARCHAR(500) NULL",
        "ALTER TABLE SAI_rituals MODIFY best_tithi VARCHAR(500) NULL",
        "ALTER TABLE SAI_rituals MODIFY deity VARCHAR(500) NULL",
        "ALTER TABLE SAI_rituals MODIFY occasion_type VARCHAR(500) NULL",
        "ALTER TABLE SAI_rituals MODIFY name VARCHAR(300) NOT NULL",
        "ALTER TABLE SAI_rituals MODIFY name_sanskrit VARCHAR(300) NULL",
        "ALTER TABLE SAI_rituals MODIFY category VARCHAR(300) NOT NULL",
        "ALTER TABLE SAI_rituals MODIFY sub_category VARCHAR(300) NULL",
    ];
    foreach ($widenQueries as $sql) {
        try {
            $pdo->exec($sql);
            echo "      ✓ OK: $sql\n";
        } catch (\Exception $e) {
            echo "      ! SKIP: $sql\n";
        }
    }

    // Also widen user_rituals columns
    $userRitualWidenQueries = [
        "ALTER TABLE SAI_user_rituals MODIFY best_time VARCHAR(500) NULL",
        "ALTER TABLE SAI_user_rituals MODIFY best_tithi VARCHAR(500) NULL",
        "ALTER TABLE SAI_user_rituals MODIFY deity VARCHAR(500) NULL",
        "ALTER TABLE SAI_user_rituals MODIFY occasion_type VARCHAR(500) NULL",
        "ALTER TABLE SAI_user_rituals MODIFY name VARCHAR(300) NOT NULL",
        "ALTER TABLE SAI_user_rituals MODIFY category VARCHAR(300) NULL",
    ];
    foreach ($userRitualWidenQueries as $sql) {
        try {
            $pdo->exec($sql);
            echo "      ✓ OK: $sql\n";
        } catch (\Exception $e) {
            echo "      ! SKIP (column may not exist): $sql\n";
        }
    }

    // ============================================================
    // ADDITIONAL MIGRATION: Create SAI_ai_ritual_feedback table
    // (from add_ai_feedback_table.php)
    // ============================================================
    echo "\n      Creating SAI_ai_ritual_feedback...\n";
    try {
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS SAI_ai_ritual_feedback (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                ritual_name VARCHAR(200) NOT NULL,
                community_name VARCHAR(150) NULL,
                religion VARCHAR(100) NULL,
                generation_session_id VARCHAR(64) NOT NULL,
                round_number INT NOT NULL DEFAULT 1,
                ai_response JSON NULL,
                user_feedback TEXT NULL,
                feedback_type ENUM('accepted', 'rejected', 'refined') NOT NULL DEFAULT 'refined',
                search_criteria JSON NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

                FOREIGN KEY (user_id) REFERENCES SAI_users(id) ON DELETE CASCADE,

                INDEX idx_user_id (user_id),
                INDEX idx_ritual_name (ritual_name),
                INDEX idx_session_id (generation_session_id),
                INDEX idx_feedback_type (feedback_type),
                INDEX idx_community (community_name),
                INDEX idx_created_at (created_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        echo "      ✓ SAI_ai_ritual_feedback created\n";
    } catch (\Exception $e) {
        echo "      ! Could not create SAI_ai_ritual_feedback: " . $e->getMessage() . "\n";
    }

    // ============================================================
    // ADDITIONAL MIGRATION: Add updated_at to SAI_ai_ritual_feedback
    // (from fix_feedback_table.php)
    // ============================================================
    echo "\n      Adding updated_at to SAI_ai_ritual_feedback...\n";
    try {
        $stmt = $pdo->query("SHOW COLUMNS FROM SAI_ai_ritual_feedback LIKE 'updated_at'");
        if ($stmt->rowCount() === 0) {
            $pdo->exec("ALTER TABLE SAI_ai_ritual_feedback ADD COLUMN updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER created_at");
            echo "      ✓ 'updated_at' added to SAI_ai_ritual_feedback\n";
        } else {
            echo "      - 'updated_at' already exists in SAI_ai_ritual_feedback\n";
        }
    } catch (\Exception $e) {
        echo "      ! Could not add updated_at: " . $e->getMessage() . "\n";
    }

    // ============================================================
    // ADDITIONAL MIGRATION: Create SAI_invitations table
    // (from add_invitations_table.php)
    // ============================================================
    echo "\n      Creating SAI_invitations...\n";
    try {
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS SAI_invitations (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                share_token VARCHAR(64) NOT NULL UNIQUE,
                occasion_type VARCHAR(100) NOT NULL,
                occasion_title VARCHAR(200) NOT NULL,
                event_date DATETIME NULL,
                venue VARCHAR(300) NULL,
                host_name VARCHAR(150) NOT NULL,
                message TEXT NULL,
                additional_details TEXT NULL,
                generated_html MEDIUMTEXT NOT NULL,
                ai_request_id INT NULL,
                expires_at DATETIME NOT NULL,
                is_active BOOLEAN DEFAULT TRUE,
                view_count INT DEFAULT 0,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

                FOREIGN KEY (user_id) REFERENCES SAI_users(id) ON DELETE CASCADE,
                FOREIGN KEY (ai_request_id) REFERENCES SAI_ai_requests(id) ON DELETE SET NULL,

                INDEX idx_user_id (user_id),
                INDEX idx_share_token (share_token),
                INDEX idx_expires_at (expires_at),
                INDEX idx_is_active (is_active)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        echo "      ✓ SAI_invitations created\n";
    } catch (\Exception $e) {
        echo "      ! Could not create SAI_invitations: " . $e->getMessage() . "\n";
    }

    // ============================================================
    // ADDITIONAL MIGRATION: Create SAI_reviews table
    // (from create_reviews_table.php)
    // ============================================================
    echo "\n      Creating SAI_reviews...\n";
    try {
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS SAI_reviews (
                id INT AUTO_INCREMENT PRIMARY KEY,
                reviewer_id INT NOT NULL,
                target_type ENUM('pandit', 'vendor') NOT NULL,
                target_id INT NOT NULL,
                assignment_id INT NULL,
                order_id INT NULL,

                -- Overall rating (required for both)
                rating_overall TINYINT UNSIGNED NOT NULL CHECK (rating_overall BETWEEN 1 AND 5),

                -- Pandit-specific ratings (NULL for vendor reviews)
                punctuality TINYINT UNSIGNED NULL CHECK (punctuality IS NULL OR punctuality BETWEEN 1 AND 5),
                knowledge TINYINT UNSIGNED NULL CHECK (knowledge IS NULL OR knowledge BETWEEN 1 AND 5),
                behavior TINYINT UNSIGNED NULL CHECK (behavior IS NULL OR behavior BETWEEN 1 AND 5),
                clarity TINYINT UNSIGNED NULL CHECK (clarity IS NULL OR clarity BETWEEN 1 AND 5),

                -- Vendor-specific ratings (NULL for pandit reviews)
                item_quality TINYINT UNSIGNED NULL CHECK (item_quality IS NULL OR item_quality BETWEEN 1 AND 5),
                delivery_time TINYINT UNSIGNED NULL CHECK (delivery_time IS NULL OR delivery_time BETWEEN 1 AND 5),
                packaging TINYINT UNSIGNED NULL CHECK (packaging IS NULL OR packaging BETWEEN 1 AND 5),
                value_for_money TINYINT UNSIGNED NULL CHECK (value_for_money IS NULL OR value_for_money BETWEEN 1 AND 5),

                -- Review text
                review_text TEXT NULL,

                -- Moderation
                ai_flag TINYINT(1) DEFAULT 0 COMMENT 'AI flagged for review',
                ai_moderation_reason VARCHAR(255) NULL,
                status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
                rejection_reason VARCHAR(255) NULL,
                moderated_by INT NULL,
                moderated_at DATETIME NULL,

                -- Timestamps
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

                -- Foreign keys
                FOREIGN KEY (reviewer_id) REFERENCES SAI_users(id) ON DELETE CASCADE,
                FOREIGN KEY (assignment_id) REFERENCES SAI_pandit_assignments(id) ON DELETE SET NULL,
                FOREIGN KEY (order_id) REFERENCES SAI_orders(id) ON DELETE SET NULL,
                FOREIGN KEY (moderated_by) REFERENCES SAI_users(id) ON DELETE SET NULL,

                -- Indexes
                INDEX idx_reviewer (reviewer_id),
                INDEX idx_target (target_type, target_id),
                INDEX idx_assignment (assignment_id),
                INDEX idx_order (order_id),
                INDEX idx_status (status),
                INDEX idx_rating (rating_overall),
                INDEX idx_created (created_at),

                -- Prevent duplicate reviews
                UNIQUE KEY unique_assignment_review (assignment_id),
                UNIQUE KEY unique_order_review (order_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        echo "      ✓ SAI_reviews created\n";
    } catch (\Exception $e) {
        echo "      ! Could not create SAI_reviews: " . $e->getMessage() . "\n";
    }

    // ============================================================
    // ADDITIONAL MIGRATION: Add trust_badges to SAI_pandit_profiles
    // (from create_reviews_table.php)
    // ============================================================
    echo "\n      Adding trust_badges to SAI_pandit_profiles...\n";
    try {
        $stmt = $pdo->query("SHOW COLUMNS FROM SAI_pandit_profiles LIKE 'trust_badges'");
        if ($stmt->rowCount() === 0) {
            $pdo->exec("
                ALTER TABLE SAI_pandit_profiles 
                ADD COLUMN trust_badges JSON NULL COMMENT 'Array of earned trust badges',
                ADD COLUMN positive_review_percentage DECIMAL(5,2) DEFAULT 0.00,
                ADD COLUMN is_documents_verified TINYINT(1) DEFAULT 0
            ");
            echo "      ✓ trust_badges columns added to SAI_pandit_profiles\n";
        } else {
            echo "      - trust_badges already exists in SAI_pandit_profiles\n";
        }
    } catch (\Exception $e) {
        echo "      ! Could not add trust_badges to pandit_profiles: " . $e->getMessage() . "\n";
    }

    // ============================================================
    // ADDITIONAL MIGRATION: Add trust_badges to SAI_vendors
    // (from create_reviews_table.php)
    // ============================================================
    echo "\n      Adding trust_badges to SAI_vendors...\n";
    try {
        $stmt = $pdo->query("SHOW COLUMNS FROM SAI_vendors LIKE 'trust_badges'");
        if ($stmt->rowCount() === 0) {
            $pdo->exec("
                ALTER TABLE SAI_vendors 
                ADD COLUMN trust_badges JSON NULL COMMENT 'Array of earned trust badges',
                ADD COLUMN positive_review_percentage DECIMAL(5,2) DEFAULT 0.00
            ");
            echo "      ✓ trust_badges columns added to SAI_vendors\n";
        } else {
            echo "      - trust_badges already exists in SAI_vendors\n";
        }
    } catch (\Exception $e) {
        echo "      ! Could not add trust_badges to vendors: " . $e->getMessage() . "\n";
    }

    // ============================================================
    // ADDITIONAL MIGRATION: Create SAI_review_notifications table
    // (from create_reviews_table.php)
    // ============================================================
    echo "\n      Creating SAI_review_notifications...\n";
    try {
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS SAI_review_notifications (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                target_type ENUM('pandit', 'vendor') NOT NULL,
                target_id INT NOT NULL,
                assignment_id INT NULL,
                order_id INT NULL,
                notification_text VARCHAR(255) NOT NULL,
                is_read TINYINT(1) DEFAULT 0,
                is_reviewed TINYINT(1) DEFAULT 0,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                expires_at DATETIME NULL,

                FOREIGN KEY (user_id) REFERENCES SAI_users(id) ON DELETE CASCADE,
                INDEX idx_user (user_id),
                INDEX idx_read (is_read),
                INDEX idx_reviewed (is_reviewed)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        echo "      ✓ SAI_review_notifications created\n";
    } catch (\Exception $e) {
        echo "      ! Could not create SAI_review_notifications: " . $e->getMessage() . "\n";
    }

    // ============================================================
    // ADDITIONAL MIGRATION: Add template & RSVP columns to SAI_invitations
    // and create SAI_invitation_rsvps table
    // (from add_invitation_rsvp_table.php)
    // ============================================================
    echo "\n      Updating SAI_invitations with template/RSVP columns...\n";
    try {
        // Add template_id column
        $stmt = $pdo->query("SHOW COLUMNS FROM SAI_invitations LIKE 'template_id'");
        if ($stmt->rowCount() === 0) {
            $pdo->exec("
                ALTER TABLE SAI_invitations
                ADD COLUMN template_id VARCHAR(50) DEFAULT 'royal_gold' AFTER additional_details
            ");
            echo "      ✓ 'template_id' added to SAI_invitations\n";
        } else {
            echo "      - 'template_id' already exists in SAI_invitations\n";
        }

        // Add theme_color column
        $stmt = $pdo->query("SHOW COLUMNS FROM SAI_invitations LIKE 'theme_color'");
        if ($stmt->rowCount() === 0) {
            $pdo->exec("
                ALTER TABLE SAI_invitations
                ADD COLUMN theme_color VARCHAR(20) DEFAULT '#B8860B' AFTER template_id
            ");
            echo "      ✓ 'theme_color' added to SAI_invitations\n";
        } else {
            echo "      - 'theme_color' already exists in SAI_invitations\n";
        }

        // Add rsvp_enabled column
        $stmt = $pdo->query("SHOW COLUMNS FROM SAI_invitations LIKE 'rsvp_enabled'");
        if ($stmt->rowCount() === 0) {
            $pdo->exec("
                ALTER TABLE SAI_invitations
                ADD COLUMN rsvp_enabled BOOLEAN DEFAULT TRUE AFTER theme_color
            ");
            echo "      ✓ 'rsvp_enabled' added to SAI_invitations\n";
        } else {
            echo "      - 'rsvp_enabled' already exists in SAI_invitations\n";
        }

        // Make generated_html nullable
        $pdo->exec("ALTER TABLE SAI_invitations MODIFY generated_html MEDIUMTEXT NULL");
        echo "      ✓ 'generated_html' is now nullable\n";
    } catch (\Exception $e) {
        echo "      ! Could not update SAI_invitations: " . $e->getMessage() . "\n";
    }

    // Create SAI_invitation_rsvps table
    echo "\n      Creating SAI_invitation_rsvps...\n";
    try {
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
    } catch (\Exception $e) {
        echo "      ! Could not create SAI_invitation_rsvps: " . $e->getMessage() . "\n";
    }

    // ============================================================
    // ADDITIONAL MIGRATION: Create SAI_mohurat_requests table
    // (from add_mohurat_requests_table.php)
    // ============================================================
    echo "\n      Creating SAI_mohurat_requests...\n";
    try {
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS SAI_mohurat_requests (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                pandit_id INT NULL,
                family_id INT NULL,
                ritual_type VARCHAR(200) NOT NULL,
                country VARCHAR(100) DEFAULT 'India',
                city VARCHAR(100) NULL,
                preferred_month VARCHAR(50) NULL,
                gotra VARCHAR(50) NULL,
                nakshatra VARCHAR(50) NULL,
                time_preference ENUM('morning', 'evening', 'any') DEFAULT 'any',
                additional_notes TEXT NULL,
                status ENUM('pending', 'replied', 'accepted', 'declined', 'expired') DEFAULT 'pending',
                reply_date DATE NULL,
                reply_time TIME NULL,
                reply_explanation TEXT NULL,
                consultation_fee DECIMAL(10,2) NULL,
                replied_by INT NULL,
                replied_at DATETIME NULL,
                accepted_at DATETIME NULL,
                assignment_id INT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

                FOREIGN KEY (user_id) REFERENCES SAI_users(id) ON DELETE CASCADE,
                FOREIGN KEY (pandit_id) REFERENCES SAI_users(id) ON DELETE SET NULL,
                FOREIGN KEY (family_id) REFERENCES SAI_families(id) ON DELETE SET NULL,
                FOREIGN KEY (replied_by) REFERENCES SAI_users(id) ON DELETE SET NULL,
                FOREIGN KEY (assignment_id) REFERENCES SAI_pandit_assignments(id) ON DELETE SET NULL,

                INDEX idx_user_id (user_id),
                INDEX idx_pandit_id (pandit_id),
                INDEX idx_status (status),
                INDEX idx_created_at (created_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        echo "      ✓ SAI_mohurat_requests created\n";
    } catch (\Exception $e) {
        echo "      ! Could not create SAI_mohurat_requests: " . $e->getMessage() . "\n";
    }

    // ============================================================
    // ADDITIONAL MIGRATION: Create AI Pandit Chat Tables
    // (from create_pandit_chat_tables.php)
    // ============================================================
    echo "\n      Creating SAI_pandit_chat_sessions...\n";
    try {
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS SAI_pandit_chat_sessions (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                title VARCHAR(255) DEFAULT 'New Conversation',
                user_details JSON DEFAULT NULL COMMENT 'Stores collected DOB, birth time, place, gotra etc.',
                status ENUM('active', 'archived') DEFAULT 'active',
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_user_id (user_id),
                INDEX idx_status (status),
                INDEX idx_created (created_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        echo "      ✓ SAI_pandit_chat_sessions created\n";
    } catch (\Exception $e) {
        echo "      ! Could not create SAI_pandit_chat_sessions: " . $e->getMessage() . "\n";
    }

    echo "\n      Creating SAI_pandit_chat_messages...\n";
    try {
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS SAI_pandit_chat_messages (
                id INT AUTO_INCREMENT PRIMARY KEY,
                session_id INT NOT NULL,
                role ENUM('user', 'assistant') NOT NULL,
                content TEXT NOT NULL,
                tokens_used INT DEFAULT 0,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_session_id (session_id),
                INDEX idx_role (role),
                CONSTRAINT fk_chat_msg_session FOREIGN KEY (session_id) 
                    REFERENCES SAI_pandit_chat_sessions(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        echo "      ✓ SAI_pandit_chat_messages created\n";
    } catch (\Exception $e) {
        echo "      ! Could not create SAI_pandit_chat_messages: " . $e->getMessage() . "\n";
    }

    // ============================================================
    // ADDITIONAL MIGRATION: Create Ritual Budget Tables
    // (from create_ritual_budget_tables.php)
    // ============================================================
    echo "\n      Creating SAI_ritual_budgets...\n";
    try {
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
    } catch (\Exception $e) {
        echo "      ! Could not create SAI_ritual_budgets: " . $e->getMessage() . "\n";
    }

    echo "\n      Creating SAI_ritual_budget_items...\n";
    try {
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
    } catch (\Exception $e) {
        echo "      ! Could not create SAI_ritual_budget_items: " . $e->getMessage() . "\n";
    }

    // ============================================================
    // ADDITIONAL MIGRATION: Create SAI_ritual_feedbacks table
    // (from create_ritual_feedbacks_table.php)
    // ============================================================
    echo "\n      Creating SAI_ritual_feedbacks...\n";
    try {
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS SAI_ritual_feedbacks (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NULL,
                community_name VARCHAR(255) NULL,
                religion VARCHAR(100) NULL,
                ritual_name VARCHAR(255) NOT NULL,
                feedback_type ENUM('like', 'dislike') NOT NULL,
                feedback_text TEXT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_user_id (user_id),
                INDEX idx_ritual_name (ritual_name),
                INDEX idx_feedback_type (feedback_type),
                CONSTRAINT fk_ritual_feedbacks_user
                    FOREIGN KEY (user_id) REFERENCES SAI_users(id)
                    ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        echo "      ✓ SAI_ritual_feedbacks created\n";
    } catch (\Exception $e) {
        echo "      ! Could not create SAI_ritual_feedbacks: " . $e->getMessage() . "\n";
    }

    // ============================================================
    // ADDITIONAL MIGRATION: Create Subscription Tables
    // (from create_subscription_tables.php)
    // ============================================================
    echo "\n      Creating SAI_subscription_plans...\n";
    try {
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS SAI_subscription_plans (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) NOT NULL,
                slug VARCHAR(50) NOT NULL UNIQUE,
                description TEXT,
                duration_days INT NOT NULL,
                price DECIMAL(10,2) NOT NULL,
                currency VARCHAR(3) DEFAULT 'INR',
                features JSON,
                is_active TINYINT(1) DEFAULT 1,
                display_order INT DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_slug (slug),
                INDEX idx_active (is_active)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        echo "      ✓ SAI_subscription_plans created\n";
    } catch (\Exception $e) {
        echo "      ! Could not create SAI_subscription_plans: " . $e->getMessage() . "\n";
    }

    echo "\n      Creating SAI_user_subscriptions...\n";
    try {
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS SAI_user_subscriptions (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                plan_id INT NOT NULL,
                razorpay_subscription_id VARCHAR(255),
                status ENUM('pending', 'active', 'expired', 'cancelled', 'failed') DEFAULT 'pending',
                starts_at TIMESTAMP NULL,
                expires_at TIMESTAMP NULL,
                auto_renew TINYINT(1) DEFAULT 0,
                cancelled_at TIMESTAMP NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES SAI_users(id) ON DELETE CASCADE,
                FOREIGN KEY (plan_id) REFERENCES SAI_subscription_plans(id) ON DELETE RESTRICT,
                INDEX idx_user_id (user_id),
                INDEX idx_status (status),
                INDEX idx_expires_at (expires_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        echo "      ✓ SAI_user_subscriptions created\n";
    } catch (\Exception $e) {
        echo "      ! Could not create SAI_user_subscriptions: " . $e->getMessage() . "\n";
    }

    echo "\n      Creating SAI_payment_transactions...\n";
    try {
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS SAI_payment_transactions (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                subscription_id INT,
                plan_id INT NOT NULL,
                razorpay_order_id VARCHAR(255),
                razorpay_payment_id VARCHAR(255),
                razorpay_signature VARCHAR(255),
                amount DECIMAL(10,2) NOT NULL,
                currency VARCHAR(3) DEFAULT 'INR',
                status ENUM('created', 'pending', 'completed', 'failed', 'refunded') DEFAULT 'created',
                payment_method VARCHAR(50),
                error_code VARCHAR(100),
                error_description TEXT,
                metadata JSON,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES SAI_users(id) ON DELETE CASCADE,
                FOREIGN KEY (subscription_id) REFERENCES SAI_user_subscriptions(id) ON DELETE SET NULL,
                FOREIGN KEY (plan_id) REFERENCES SAI_subscription_plans(id) ON DELETE RESTRICT,
                INDEX idx_user_id (user_id),
                INDEX idx_razorpay_order_id (razorpay_order_id),
                INDEX idx_razorpay_payment_id (razorpay_payment_id),
                INDEX idx_status (status)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        echo "      ✓ SAI_payment_transactions created\n";
    } catch (\Exception $e) {
        echo "      ! Could not create SAI_payment_transactions: " . $e->getMessage() . "\n";
    }

    // Seed Default Subscription Plans
    echo "\n      Seeding default subscription plans...\n";
    try {
        $plans = [
            [
                'name' => '1 Day Trial',
                'slug' => 'daily',
                'description' => 'Try AI Pandit for 24 hours',
                'duration_days' => 1,
                'price' => 1.00,
                'features' => json_encode([
                    'Unlimited AI Pandit chats',
                    'Access to all ritual guidance',
                    'Personalized recommendations',
                    '24/7 availability'
                ]),
                'display_order' => 1
            ],
            [
                'name' => '1 Month',
                'slug' => 'monthly',
                'description' => 'Full access for 1 month',
                'duration_days' => 30,
                'price' => 28.00,
                'features' => json_encode([
                    'Unlimited AI Pandit chats',
                    'Access to all ritual guidance',
                    'Personalized recommendations',
                    '24/7 availability',
                    'Chat history saved'
                ]),
                'display_order' => 2
            ],
            [
                'name' => '6 Months',
                'slug' => 'half-yearly',
                'description' => 'Best value - 6 months access',
                'duration_days' => 180,
                'price' => 400.00,
                'features' => json_encode([
                    'Unlimited AI Pandit chats',
                    'Access to all ritual guidance',
                    'Personalized recommendations',
                    '24/7 availability',
                    'Chat history saved',
                    'Priority support',
                    'Save 28% compared to monthly'
                ]),
                'display_order' => 3
            ],
            [
                'name' => '1 Year',
                'slug' => 'yearly',
                'description' => 'Maximum savings - Annual plan',
                'duration_days' => 365,
                'price' => 750.00,
                'features' => json_encode([
                    'Unlimited AI Pandit chats',
                    'Access to all ritual guidance',
                    'Personalized recommendations',
                    '24/7 availability',
                    'Chat history saved',
                    'Priority support',
                    'Exclusive features',
                    'Save 44% compared to monthly'
                ]),
                'display_order' => 4
            ]
        ];

        $stmt = $pdo->prepare("
            INSERT INTO SAI_subscription_plans (name, slug, description, duration_days, price, features, display_order)
            VALUES (:name, :slug, :description, :duration_days, :price, :features, :display_order)
            ON DUPLICATE KEY UPDATE
                name = VALUES(name),
                description = VALUES(description),
                duration_days = VALUES(duration_days),
                price = VALUES(price),
                features = VALUES(features),
                display_order = VALUES(display_order)
        ");

        foreach ($plans as $plan) {
            $stmt->execute($plan);
        }
        echo "      ✓ Default subscription plans seeded\n";
    } catch (\Exception $e) {
        echo "      ! Could not seed subscription plans: " . $e->getMessage() . "\n";
    }

    // ============================================================
    // ADDITIONAL MIGRATION: Create SAI_user_feedbacks table
    // (from create_user_feedbacks_table.php)
    // ============================================================
    echo "\n      Creating SAI_user_feedbacks...\n";
    try {
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS SAI_user_feedbacks (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                name VARCHAR(255) NOT NULL,
                email VARCHAR(255) NOT NULL,
                phone VARCHAR(20) NOT NULL,
                community_name VARCHAR(255),
                features_feedback JSON,
                likes_about TEXT,
                improvements_for TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES SAI_users(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        echo "      ✓ SAI_user_feedbacks created\n";
    } catch (\Exception $e) {
        echo "      ! Could not create SAI_user_feedbacks: " . $e->getMessage() . "\n";
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
        'SAI_password_resets',
        'SAI_vendors',
        'SAI_ritual_embeddings',
        'SAI_ai_ritual_feedback',
        'SAI_invitations',
        'SAI_reviews',
        'SAI_review_notifications',
        'SAI_invitation_rsvps',
        'SAI_mohurat_requests',
        'SAI_pandit_chat_sessions',
        'SAI_pandit_chat_messages',
        'SAI_ritual_budgets',
        'SAI_ritual_budget_items',
        'SAI_ritual_feedbacks',
        'SAI_subscription_plans',
        'SAI_user_subscriptions',
        'SAI_payment_transactions',
        'SAI_user_feedbacks',
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
        echo "  ✓ All 42 tables created with constraints\n";
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