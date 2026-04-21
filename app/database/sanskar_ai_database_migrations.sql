-- ============================================================================
-- SANSKAR AI - Phase 2 Database Migration Script
-- ============================================================================
-- Project  : Sanskar AI (SAI)
-- Date     : 2026-04-18
-- Engine   : InnoDB
-- Charset  : utf8mb4 / utf8mb4_unicode_ci
-- 
-- IMPORTANT: This script assumes the following tables already exist:
--   - SAI_users
--   - SAI_families
--   - SAI_pandit_assignments
--   - SAI_invitations
--
-- Run this script against the SAI database.
-- ============================================================================

USE SAI;

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 1;


-- ============================================================================
-- 1. ALTER TABLE: SAI_invitations (Add template & RSVP columns)
-- ============================================================================
-- Source: add_invitation_rsvp_table.php
-- Adds template_id, theme_color, rsvp_enabled columns and makes
-- generated_html nullable.
-- ----------------------------------------------------------------------------

ALTER TABLE SAI_invitations
    ADD COLUMN IF NOT EXISTS template_id VARCHAR(50) DEFAULT 'royal_gold' AFTER additional_details;

ALTER TABLE SAI_invitations
    ADD COLUMN IF NOT EXISTS theme_color VARCHAR(20) DEFAULT '#B8860B' AFTER template_id;

ALTER TABLE SAI_invitations
    ADD COLUMN IF NOT EXISTS rsvp_enabled BOOLEAN DEFAULT TRUE AFTER theme_color;

ALTER TABLE SAI_invitations
    MODIFY generated_html MEDIUMTEXT NULL;


-- ============================================================================
-- 2. CREATE TABLE: SAI_invitation_rsvps
-- ============================================================================
-- Source: add_invitation_rsvp_table.php
-- Stores RSVP responses from guests for invitations.
-- ----------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS SAI_invitation_rsvps (
    id                INT AUTO_INCREMENT PRIMARY KEY,
    invitation_id     INT NOT NULL,
    guest_name        VARCHAR(150) NOT NULL,
    attending_status  ENUM('yes', 'no', 'maybe') NOT NULL DEFAULT 'yes',
    guest_count       INT DEFAULT 1,
    message           TEXT NULL,
    created_at        DATETIME DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (invitation_id) REFERENCES SAI_invitations(id) ON DELETE CASCADE,

    INDEX idx_invitation_id (invitation_id),
    INDEX idx_attending_status (attending_status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================================
-- 3. CREATE TABLE: SAI_mohurat_requests
-- ============================================================================
-- Source: add_mohurat_requests_table.php
-- Stores muhurat (auspicious timing) requests from users to pandits.
-- ----------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS SAI_mohurat_requests (
    id                INT AUTO_INCREMENT PRIMARY KEY,
    user_id           INT NOT NULL,
    pandit_id         INT NULL,
    family_id         INT NULL,
    ritual_type       VARCHAR(200) NOT NULL,
    country           VARCHAR(100) DEFAULT 'India',
    city              VARCHAR(100) NULL,
    preferred_month   VARCHAR(50) NULL,
    gotra             VARCHAR(50) NULL,
    nakshatra         VARCHAR(50) NULL,
    time_preference   ENUM('morning', 'evening', 'any') DEFAULT 'any',
    additional_notes  TEXT NULL,
    status            ENUM('pending', 'replied', 'accepted', 'declined', 'expired') DEFAULT 'pending',
    reply_date        DATE NULL,
    reply_time        TIME NULL,
    reply_explanation  TEXT NULL,
    consultation_fee  DECIMAL(10,2) NULL,
    replied_by        INT NULL,
    replied_at        DATETIME NULL,
    accepted_at       DATETIME NULL,
    assignment_id     INT NULL,
    created_at        DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at        DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id)       REFERENCES SAI_users(id)              ON DELETE CASCADE,
    FOREIGN KEY (pandit_id)     REFERENCES SAI_users(id)              ON DELETE SET NULL,
    FOREIGN KEY (family_id)     REFERENCES SAI_families(id)           ON DELETE SET NULL,
    FOREIGN KEY (replied_by)    REFERENCES SAI_users(id)              ON DELETE SET NULL,
    FOREIGN KEY (assignment_id) REFERENCES SAI_pandit_assignments(id) ON DELETE SET NULL,

    INDEX idx_user_id    (user_id),
    INDEX idx_pandit_id  (pandit_id),
    INDEX idx_status     (status),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================================
-- 4. CREATE TABLE: SAI_pandit_chat_sessions
-- ============================================================================
-- Source: create_pandit_chat_tables.php
-- Stores AI Pandit chat sessions per user.
-- ----------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS SAI_pandit_chat_sessions (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    user_id       INT NOT NULL,
    title         VARCHAR(255) DEFAULT 'New Conversation',
    user_details  JSON DEFAULT NULL COMMENT 'Stores collected DOB, birth time, place, gotra etc.',
    status        ENUM('active', 'archived') DEFAULT 'active',
    created_at    DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at    DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_user_id (user_id),
    INDEX idx_status  (status),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================================
-- 5. CREATE TABLE: SAI_pandit_chat_messages
-- ============================================================================
-- Source: create_pandit_chat_tables.php
-- Stores individual messages within AI Pandit chat sessions.
-- ----------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS SAI_pandit_chat_messages (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    session_id  INT NOT NULL,
    role        ENUM('user', 'assistant') NOT NULL,
    content     TEXT NOT NULL,
    tokens_used INT DEFAULT 0,
    created_at  DATETIME DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_session_id (session_id),
    INDEX idx_role       (role),

    CONSTRAINT fk_chat_msg_session
        FOREIGN KEY (session_id) REFERENCES SAI_pandit_chat_sessions(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================================
-- 6. CREATE TABLE: SAI_ritual_budgets
-- ============================================================================
-- Source: create_ritual_budget_tables.php
-- Stores AI-generated or user-created ritual budget plans.
-- ----------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS SAI_ritual_budgets (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    user_id         INT NOT NULL,
    ritual_type     VARCHAR(255) NOT NULL,
    location        VARCHAR(255) NOT NULL,
    guest_count     SMALLINT UNSIGNED NOT NULL,
    tier            ENUM('basic', 'standard', 'premium') NOT NULL DEFAULT 'standard',
    total_estimated DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    total_actual    DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    ai_request_id   INT NULL,
    created_at      DATETIME NOT NULL,
    updated_at      DATETIME NOT NULL,

    INDEX idx_user_id (user_id),

    FOREIGN KEY (user_id) REFERENCES SAI_users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================================
-- 7. CREATE TABLE: SAI_ritual_budget_items
-- ============================================================================
-- Source: create_ritual_budget_tables.php
-- Stores individual line items within a ritual budget.
-- ----------------------------------------------------------------------------

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================================
-- 8. CREATE TABLE: SAI_ritual_feedbacks
-- ============================================================================
-- Source: create_ritual_feedbacks_table.php
-- Stores like/dislike feedback for AI-generated rituals.
-- ----------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS SAI_ritual_feedbacks (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    user_id         INT NULL,
    community_name  VARCHAR(255) NULL,
    religion        VARCHAR(100) NULL,
    ritual_name     VARCHAR(255) NOT NULL,
    feedback_type   ENUM('like', 'dislike') NOT NULL,
    feedback_text   TEXT NULL,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_user_id       (user_id),
    INDEX idx_ritual_name   (ritual_name),
    INDEX idx_feedback_type (feedback_type),

    CONSTRAINT fk_ritual_feedbacks_user
        FOREIGN KEY (user_id) REFERENCES SAI_users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================================
-- 9. CREATE TABLE: SAI_subscription_plans
-- ============================================================================
-- Source: create_subscription_tables.php
-- Available subscription plans for the AI Pandit feature.
-- ----------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS SAI_subscription_plans (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    name          VARCHAR(100) NOT NULL,
    slug          VARCHAR(50) NOT NULL UNIQUE,
    description   TEXT,
    duration_days INT NOT NULL,
    price         DECIMAL(10,2) NOT NULL,
    currency      VARCHAR(3) DEFAULT 'INR',
    features      JSON,
    is_active     TINYINT(1) DEFAULT 1,
    display_order INT DEFAULT 0,
    created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_slug   (slug),
    INDEX idx_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================================
-- 10. CREATE TABLE: SAI_user_subscriptions
-- ============================================================================
-- Source: create_subscription_tables.php
-- Tracks user subscription records (linked to Razorpay).
-- ----------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS SAI_user_subscriptions (
    id                        INT AUTO_INCREMENT PRIMARY KEY,
    user_id                   INT NOT NULL,
    plan_id                   INT NOT NULL,
    razorpay_subscription_id  VARCHAR(255),
    status                    ENUM('pending', 'active', 'expired', 'cancelled', 'failed') DEFAULT 'pending',
    starts_at                 TIMESTAMP NULL,
    expires_at                TIMESTAMP NULL,
    auto_renew                TINYINT(1) DEFAULT 0,
    cancelled_at              TIMESTAMP NULL,
    created_at                TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at                TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES SAI_users(id)              ON DELETE CASCADE,
    FOREIGN KEY (plan_id) REFERENCES SAI_subscription_plans(id) ON DELETE RESTRICT,

    INDEX idx_user_id    (user_id),
    INDEX idx_status     (status),
    INDEX idx_expires_at (expires_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================================
-- 11. CREATE TABLE: SAI_payment_transactions
-- ============================================================================
-- Source: create_subscription_tables.php
-- Payment history with Razorpay integration.
-- ----------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS SAI_payment_transactions (
    id                    INT AUTO_INCREMENT PRIMARY KEY,
    user_id               INT NOT NULL,
    subscription_id       INT,
    plan_id               INT NOT NULL,
    razorpay_order_id     VARCHAR(255),
    razorpay_payment_id   VARCHAR(255),
    razorpay_signature    VARCHAR(255),
    amount                DECIMAL(10,2) NOT NULL,
    currency              VARCHAR(3) DEFAULT 'INR',
    status                ENUM('created', 'pending', 'completed', 'failed', 'refunded') DEFAULT 'created',
    payment_method        VARCHAR(50),
    error_code            VARCHAR(100),
    error_description     TEXT,
    metadata              JSON,
    created_at            TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at            TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id)         REFERENCES SAI_users(id)              ON DELETE CASCADE,
    FOREIGN KEY (subscription_id) REFERENCES SAI_user_subscriptions(id) ON DELETE SET NULL,
    FOREIGN KEY (plan_id)         REFERENCES SAI_subscription_plans(id) ON DELETE RESTRICT,

    INDEX idx_user_id              (user_id),
    INDEX idx_razorpay_order_id    (razorpay_order_id),
    INDEX idx_razorpay_payment_id  (razorpay_payment_id),
    INDEX idx_status               (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================================
-- 12. CREATE TABLE: SAI_user_feedbacks
-- ============================================================================
-- Source: create_user_feedbacks_table.php
-- Stores mandatory user feedback responses.
-- ----------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS SAI_user_feedbacks (
    id                INT AUTO_INCREMENT PRIMARY KEY,
    user_id           INT NOT NULL,
    name              VARCHAR(255) NOT NULL,
    email             VARCHAR(255) NOT NULL,
    phone             VARCHAR(20) NOT NULL,
    community_name    VARCHAR(255),
    features_feedback JSON,
    likes_about       TEXT,
    improvements_for  TEXT,
    created_at        DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at        DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES SAI_users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================================
-- 13. SEED DATA: Default Subscription Plans
-- ============================================================================
-- Source: create_subscription_tables.php
-- Inserts the 4 default subscription plans. Uses ON DUPLICATE KEY UPDATE
-- so this is safe to re-run.
-- ----------------------------------------------------------------------------

INSERT INTO SAI_subscription_plans (name, slug, description, duration_days, price, features, display_order)
VALUES
    (
        '1 Day Trial',
        'daily',
        'Try AI Pandit for 24 hours',
        1,
        1.00,
        '["Unlimited AI Pandit chats","Access to all ritual guidance","Personalized recommendations","24/7 availability"]',
        1
    ),
    (
        '1 Month',
        'monthly',
        'Full access for 1 month',
        30,
        28.00,
        '["Unlimited AI Pandit chats","Access to all ritual guidance","Personalized recommendations","24/7 availability","Chat history saved"]',
        2
    ),
    (
        '6 Months',
        'half-yearly',
        'Best value - 6 months access',
        180,
        400.00,
        '["Unlimited AI Pandit chats","Access to all ritual guidance","Personalized recommendations","24/7 availability","Chat history saved","Priority support","Save 28% compared to monthly"]',
        3
    ),
    (
        '1 Year',
        'yearly',
        'Maximum savings - Annual plan',
        365,
        750.00,
        '["Unlimited AI Pandit chats","Access to all ritual guidance","Personalized recommendations","24/7 availability","Chat history saved","Priority support","Exclusive features","Save 44% compared to monthly"]',
        4
    )
ON DUPLICATE KEY UPDATE
    name          = VALUES(name),
    description   = VALUES(description),
    duration_days = VALUES(duration_days),
    price         = VALUES(price),
    features      = VALUES(features),
    display_order = VALUES(display_order);


-- ============================================================================
-- END OF MIGRATION SCRIPT
-- ============================================================================
-- Summary:
--   Tables Created  : 11 (1 ALTER + 10 CREATE)
--   Seed Data       : 4 subscription plans
--   Dependencies    : SAI_users, SAI_families, SAI_pandit_assignments, SAI_invitations
-- ============================================================================
