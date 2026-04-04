<?php
/**
 * Migration: Create Subscription Tables for AI Pandit Feature
 *
 * Tables:
 * - SAI_subscription_plans: Available subscription plans
 * - SAI_user_subscriptions: User subscription records
 * - SAI_payment_transactions: Payment history with Razorpay
 *
 * Run: php app/database/create_subscription_tables.php
 */

require_once __DIR__ . '/../../index.php';

use App\Config\Database;

try {
    $db = Database::getConnection();

    echo "Creating subscription tables...\n";

    // 1. Subscription Plans Table
    $db->exec("
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
    echo "- SAI_subscription_plans table created\n";

    // 2. User Subscriptions Table
    $db->exec("
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
    echo "- SAI_user_subscriptions table created\n";

    // 3. Payment Transactions Table
    $db->exec("
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
    echo "- SAI_payment_transactions table created\n";

    // 4. Seed Default Subscription Plans
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

    $stmt = $db->prepare("
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
    echo "- Default subscription plans seeded\n";

    echo "\n✅ All subscription tables created successfully!\n";

} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
