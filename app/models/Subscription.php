<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class Subscription extends Model
{
    protected string $table = 'SAI_user_subscriptions';

    /**
     * Get all active subscription plans
     */
    public function getActivePlans()
    {
        $stmt = $this->db->prepare("
            SELECT * FROM SAI_subscription_plans
            WHERE is_active = 1
            ORDER BY display_order ASC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get a plan by ID
     */
    public function getPlanById($planId)
    {
        $stmt = $this->db->prepare("
            SELECT * FROM SAI_subscription_plans WHERE id = :id
        ");
        $stmt->execute(['id' => $planId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get a plan by slug
     */
    public function getPlanBySlug($slug)
    {
        $stmt = $this->db->prepare("
            SELECT * FROM SAI_subscription_plans WHERE slug = :slug AND is_active = 1
        ");
        $stmt->execute(['slug' => $slug]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get user's active subscription
     */
    public function getUserActiveSubscription($userId)
    {
        $stmt = $this->db->prepare("
            SELECT s.*, p.name as plan_name, p.slug as plan_slug, p.duration_days, p.features
            FROM SAI_user_subscriptions s
            JOIN SAI_subscription_plans p ON s.plan_id = p.id
            WHERE s.user_id = :user_id
            AND s.status = 'active'
            AND s.expires_at > NOW()
            ORDER BY s.expires_at DESC
            LIMIT 1
        ");
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Check if user has active subscription
     */
    public function hasActiveSubscription($userId)
    {
        return $this->getUserActiveSubscription($userId) !== false;
    }

    /**
     * Create a new subscription
     */
    public function createSubscription($data)
    {
        $stmt = $this->db->prepare("
            INSERT INTO SAI_user_subscriptions
            (user_id, plan_id, razorpay_subscription_id, status, starts_at, expires_at, auto_renew)
            VALUES
            (:user_id, :plan_id, :razorpay_subscription_id, :status, :starts_at, :expires_at, :auto_renew)
        ");

        $stmt->execute([
            'user_id' => $data['user_id'],
            'plan_id' => $data['plan_id'],
            'razorpay_subscription_id' => $data['razorpay_subscription_id'] ?? null,
            'status' => $data['status'] ?? 'pending',
            'starts_at' => $data['starts_at'] ?? date('Y-m-d H:i:s'),
            'expires_at' => $data['expires_at'],
            'auto_renew' => $data['auto_renew'] ?? 0
        ]);

        return $this->db->lastInsertId();
    }

    /**
     * Update subscription status
     */
    public function updateSubscriptionStatus($subscriptionId, $status)
    {
        $stmt = $this->db->prepare("
            UPDATE SAI_user_subscriptions
            SET status = :status, updated_at = NOW()
            WHERE id = :id
        ");
        return $stmt->execute([
            'id' => $subscriptionId,
            'status' => $status
        ]);
    }

    /**
     * Activate subscription
     */
    public function activateSubscription($subscriptionId, $expiresAt)
    {
        $stmt = $this->db->prepare("
            UPDATE SAI_user_subscriptions
            SET status = 'active', starts_at = NOW(), expires_at = :expires_at, updated_at = NOW()
            WHERE id = :id
        ");
        return $stmt->execute([
            'id' => $subscriptionId,
            'expires_at' => $expiresAt
        ]);
    }

    /**
     * Get subscription by ID
     */
    public function getSubscriptionById($subscriptionId)
    {
        $stmt = $this->db->prepare("
            SELECT s.*, p.name as plan_name, p.slug as plan_slug, p.duration_days, p.price, p.features
            FROM SAI_user_subscriptions s
            JOIN SAI_subscription_plans p ON s.plan_id = p.id
            WHERE s.id = :id
        ");
        $stmt->execute(['id' => $subscriptionId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get user's subscription history
     */
    public function getUserSubscriptionHistory($userId)
    {
        $stmt = $this->db->prepare("
            SELECT s.*, p.name as plan_name, p.slug as plan_slug, p.duration_days, p.price
            FROM SAI_user_subscriptions s
            JOIN SAI_subscription_plans p ON s.plan_id = p.id
            WHERE s.user_id = :user_id
            ORDER BY s.created_at DESC
        ");
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Create payment transaction
     */
    public function createTransaction($data)
    {
        $stmt = $this->db->prepare("
            INSERT INTO SAI_payment_transactions
            (user_id, subscription_id, plan_id, razorpay_order_id, amount, currency, status, metadata)
            VALUES
            (:user_id, :subscription_id, :plan_id, :razorpay_order_id, :amount, :currency, :status, :metadata)
        ");

        $stmt->execute([
            'user_id' => $data['user_id'],
            'subscription_id' => $data['subscription_id'] ?? null,
            'plan_id' => $data['plan_id'],
            'razorpay_order_id' => $data['razorpay_order_id'],
            'amount' => $data['amount'],
            'currency' => $data['currency'] ?? 'INR',
            'status' => $data['status'] ?? 'created',
            'metadata' => $data['metadata'] ?? null
        ]);

        return $this->db->lastInsertId();
    }

    /**
     * Update transaction with payment details
     */
    public function updateTransactionPayment($transactionId, $data)
    {
        $stmt = $this->db->prepare("
            UPDATE SAI_payment_transactions
            SET razorpay_payment_id = :razorpay_payment_id,
                razorpay_signature = :razorpay_signature,
                status = :status,
                payment_method = :payment_method,
                updated_at = NOW()
            WHERE id = :id
        ");

        return $stmt->execute([
            'id' => $transactionId,
            'razorpay_payment_id' => $data['razorpay_payment_id'],
            'razorpay_signature' => $data['razorpay_signature'] ?? null,
            'status' => $data['status'],
            'payment_method' => $data['payment_method'] ?? null
        ]);
    }

    /**
     * Update transaction by Razorpay order ID
     */
    public function updateTransactionByOrderId($orderId, $data)
    {
        $stmt = $this->db->prepare("
            UPDATE SAI_payment_transactions
            SET razorpay_payment_id = :razorpay_payment_id,
                razorpay_signature = :razorpay_signature,
                status = :status,
                payment_method = :payment_method,
                subscription_id = :subscription_id,
                updated_at = NOW()
            WHERE razorpay_order_id = :razorpay_order_id
        ");

        return $stmt->execute([
            'razorpay_order_id' => $orderId,
            'razorpay_payment_id' => $data['razorpay_payment_id'],
            'razorpay_signature' => $data['razorpay_signature'] ?? null,
            'status' => $data['status'],
            'payment_method' => $data['payment_method'] ?? null,
            'subscription_id' => $data['subscription_id'] ?? null
        ]);
    }

    /**
     * Get transaction by order ID
     */
    public function getTransactionByOrderId($orderId)
    {
        $stmt = $this->db->prepare("
            SELECT t.*, p.name as plan_name, p.duration_days
            FROM SAI_payment_transactions t
            JOIN SAI_subscription_plans p ON t.plan_id = p.id
            WHERE t.razorpay_order_id = :order_id
        ");
        $stmt->execute(['order_id' => $orderId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get user's payment history
     */
    public function getUserPaymentHistory($userId)
    {
        $stmt = $this->db->prepare("
            SELECT t.*, p.name as plan_name, p.duration_days
            FROM SAI_payment_transactions t
            JOIN SAI_subscription_plans p ON t.plan_id = p.id
            WHERE t.user_id = :user_id
            ORDER BY t.created_at DESC
        ");
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Mark transaction as failed
     */
    public function markTransactionFailed($transactionId, $errorCode = null, $errorDescription = null)
    {
        $stmt = $this->db->prepare("
            UPDATE SAI_payment_transactions
            SET status = 'failed',
                error_code = :error_code,
                error_description = :error_description,
                updated_at = NOW()
            WHERE id = :id
        ");

        return $stmt->execute([
            'id' => $transactionId,
            'error_code' => $errorCode,
            'error_description' => $errorDescription
        ]);
    }

    /**
     * Get days remaining in subscription
     */
    public function getDaysRemaining($userId)
    {
        $subscription = $this->getUserActiveSubscription($userId);
        if (!$subscription) {
            return 0;
        }

        $expiresAt = new \DateTime($subscription['expires_at']);
        $now = new \DateTime();
        $diff = $now->diff($expiresAt);

        if ($expiresAt < $now) {
            return 0;
        }

        return $diff->days + ($diff->h > 0 || $diff->i > 0 ? 1 : 0);
    }

    /**
     * Expire old subscriptions
     */
    public function expireOldSubscriptions()
    {
        $stmt = $this->db->prepare("
            UPDATE SAI_user_subscriptions
            SET status = 'expired', updated_at = NOW()
            WHERE status = 'active' AND expires_at < NOW()
        ");
        return $stmt->execute();
    }
}
