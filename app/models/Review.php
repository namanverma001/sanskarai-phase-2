<?php
/**
 * Sanskar AI - Review Model
 * ==========================
 * Handles reviews for Pandits and Vendors with validation,
 * rating calculation, and badge management.
 */

namespace App\Models;

use App\Core\Model;
use App\Config\Database;
use PDO;

class Review extends Model
{
    protected string $table = 'SAI_reviews';
    
    protected array $fillable = [
        'reviewer_id',
        'target_type',
        'target_id',
        'assignment_id',
        'order_id',
        'rating_overall',
        'punctuality',
        'knowledge',
        'behavior',
        'clarity',
        'item_quality',
        'delivery_time',
        'packaging',
        'value_for_money',
        'review_text',
        'ai_flag',
        'ai_moderation_reason',
        'status',
        'rejection_reason',
        'moderated_by',
        'moderated_at',
    ];

    // Trust badge definitions
    public const BADGE_TOP_RATED = 'top_rated';
    public const BADGE_EXPERIENCED = 'experienced';
    public const BADGE_VERIFIED = 'verified';
    public const BADGE_TRUSTED_PARTNER = 'trusted_partner';

    public const BADGE_LABELS = [
        self::BADGE_TOP_RATED => 'Top Rated',
        self::BADGE_EXPERIENCED => 'Experienced',
        self::BADGE_VERIFIED => 'Verified Pandit',
        self::BADGE_TRUSTED_PARTNER => 'Trusted Partner',
    ];

    public const BADGE_ICONS = [
        self::BADGE_TOP_RATED => '⭐',
        self::BADGE_EXPERIENCED => '🏆',
        self::BADGE_VERIFIED => '✓',
        self::BADGE_TRUSTED_PARTNER => '🤝',
    ];

    // Review status constants
    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';

    // Validation constants
    public const MIN_REVIEW_LENGTH = 10;
    public const MAX_REVIEW_LENGTH = 500;
    public const MIN_RATING = 1;
    public const MAX_RATING = 5;

    // Badge thresholds
    public const TOP_RATED_THRESHOLD = 4.8;
    public const EXPERIENCED_THRESHOLD = 50;
    public const TRUSTED_PARTNER_PERCENTAGE = 95;

    /**
     * Check if user has already reviewed a Pandit assignment
     */
    public function hasReviewedPandit(int $userId, int $assignmentId): bool
    {
        $sql = "SELECT id FROM {$this->table} WHERE reviewer_id = :user_id AND assignment_id = :assignment_id LIMIT 1";
        $result = $this->rawOne($sql, ['user_id' => $userId, 'assignment_id' => $assignmentId]);
        return $result !== null;
    }

    /**
     * Check if user has already reviewed a Vendor order
     */
    public function hasReviewedVendor(int $userId, int $orderId): bool
    {
        $sql = "SELECT id FROM {$this->table} WHERE reviewer_id = :user_id AND order_id = :order_id LIMIT 1";
        $result = $this->rawOne($sql, ['user_id' => $userId, 'order_id' => $orderId]);
        return $result !== null;
    }

    /**
     * Validate if user can review a Pandit
     * @return array ['valid' => bool, 'error' => string|null, 'assignment' => array|null]
     */
    public function canReviewPandit(int $userId, int $assignmentId): array
    {
        // Check assignment exists and belongs to user
        $sql = "
            SELECT a.*, p.name as pandit_name
            FROM SAI_pandit_assignments a
            INNER JOIN SAI_users p ON a.pandit_id = p.id
            WHERE a.id = :assignment_id
        ";
        $assignment = $this->rawOne($sql, ['assignment_id' => $assignmentId]);

        if (!$assignment) {
            return ['valid' => false, 'error' => 'Booking not found.', 'assignment' => null];
        }

        // Check ownership
        if ((int)$assignment['user_id'] !== $userId) {
            return ['valid' => false, 'error' => 'You can only review your own bookings.', 'assignment' => null];
        }

        // Check completion status
        if ($assignment['status'] !== 'completed') {
            return ['valid' => false, 'error' => 'You can only review after the ritual is completed.', 'assignment' => null];
        }

        // Check if already reviewed
        $existing = $this->findBy('assignment_id', $assignmentId);
        if ($existing) {
            return ['valid' => false, 'error' => 'You have already reviewed this booking.', 'assignment' => null];
        }

        // Check if pandit still exists
        $pandit = $this->rawOne("SELECT id FROM SAI_users WHERE id = :id AND role = 'pandit'", ['id' => $assignment['pandit_id']]);
        if (!$pandit) {
            return ['valid' => false, 'error' => 'Pandit no longer exists.', 'assignment' => null];
        }

        return ['valid' => true, 'error' => null, 'assignment' => $assignment];
    }

    /**
     * Validate if user can review a Vendor
     * @return array ['valid' => bool, 'error' => string|null, 'order' => array|null]
     */
    public function canReviewVendor(int $userId, int $orderId): array
    {
        // Check order exists and belongs to user
        $sql = "
            SELECT o.*, v.name as vendor_name
            FROM SAI_orders o
            INNER JOIN SAI_vendors v ON o.vendor_id = v.id
            WHERE o.id = :order_id
        ";
        $order = $this->rawOne($sql, ['order_id' => $orderId]);

        if (!$order) {
            return ['valid' => false, 'error' => 'Order not found.', 'order' => null];
        }

        // Check ownership
        if ((int)$order['user_id'] !== $userId) {
            return ['valid' => false, 'error' => 'You can only review your own orders.', 'order' => null];
        }

        // Check delivery status
        if ($order['status'] !== 'delivered') {
            return ['valid' => false, 'error' => 'You can only review after the order is delivered.', 'order' => null];
        }

        // Check if already reviewed
        $existing = $this->findBy('order_id', $orderId);
        if ($existing) {
            return ['valid' => false, 'error' => 'You have already reviewed this order.', 'order' => null];
        }

        // Check if vendor still exists and is active
        $vendor = $this->rawOne("SELECT id FROM SAI_vendors WHERE id = :id AND is_active = 1", ['id' => $order['vendor_id']]);
        if (!$vendor) {
            return ['valid' => false, 'error' => 'Vendor no longer available.', 'order' => null];
        }

        return ['valid' => true, 'error' => null, 'order' => $order];
    }

    /**
     * Validate review data
     * @return array ['valid' => bool, 'errors' => array]
     */
    public function validateReviewData(array $data, string $targetType): array
    {
        $errors = [];

        // Overall rating is required
        if (!isset($data['rating_overall']) || !is_numeric($data['rating_overall'])) {
            $errors['rating_overall'] = 'Overall rating is required.';
        } elseif ($data['rating_overall'] < self::MIN_RATING || $data['rating_overall'] > self::MAX_RATING) {
            $errors['rating_overall'] = 'Rating must be between 1 and 5.';
        }

        // Validate type-specific ratings
        if ($targetType === 'pandit') {
            $panditFields = ['punctuality', 'knowledge', 'behavior', 'clarity'];
            foreach ($panditFields as $field) {
                if (isset($data[$field]) && $data[$field] !== '' && $data[$field] !== null) {
                    if (!is_numeric($data[$field]) || $data[$field] < self::MIN_RATING || $data[$field] > self::MAX_RATING) {
                        $errors[$field] = ucfirst($field) . ' rating must be between 1 and 5.';
                    }
                }
            }
        } elseif ($targetType === 'vendor') {
            $vendorFields = ['item_quality', 'delivery_time', 'packaging', 'value_for_money'];
            foreach ($vendorFields as $field) {
                if (isset($data[$field]) && $data[$field] !== '' && $data[$field] !== null) {
                    if (!is_numeric($data[$field]) || $data[$field] < self::MIN_RATING || $data[$field] > self::MAX_RATING) {
                        $errors[$field] = ucfirst(str_replace('_', ' ', $field)) . ' rating must be between 1 and 5.';
                    }
                }
            }
        }

        // Validate review text
        if (isset($data['review_text']) && !empty(trim($data['review_text']))) {
            $text = trim($data['review_text']);
            if (mb_strlen($text) < self::MIN_REVIEW_LENGTH) {
                $errors['review_text'] = 'Review must be at least ' . self::MIN_REVIEW_LENGTH . ' characters.';
            } elseif (mb_strlen($text) > self::MAX_REVIEW_LENGTH) {
                $errors['review_text'] = 'Review must not exceed ' . self::MAX_REVIEW_LENGTH . ' characters.';
            }
        }

        return ['valid' => empty($errors), 'errors' => $errors];
    }

    /**
     * Create a Pandit review with full validation
     */
    public function createPanditReview(int $userId, int $assignmentId, array $data): array
    {
        // Validate eligibility
        $eligibility = $this->canReviewPandit($userId, $assignmentId);
        if (!$eligibility['valid']) {
            return ['success' => false, 'error' => $eligibility['error']];
        }

        // Validate data
        $validation = $this->validateReviewData($data, 'pandit');
        if (!$validation['valid']) {
            return ['success' => false, 'errors' => $validation['errors']];
        }

        $assignment = $eligibility['assignment'];

        // Prepare review data
        $reviewData = [
            'reviewer_id' => $userId,
            'target_type' => 'pandit',
            'target_id' => $assignment['pandit_id'],
            'assignment_id' => $assignmentId,
            'rating_overall' => (int)$data['rating_overall'],
            'punctuality' => !empty($data['punctuality']) ? (int)$data['punctuality'] : null,
            'knowledge' => !empty($data['knowledge']) ? (int)$data['knowledge'] : null,
            'behavior' => !empty($data['behavior']) ? (int)$data['behavior'] : null,
            'clarity' => !empty($data['clarity']) ? (int)$data['clarity'] : null,
            'review_text' => !empty($data['review_text']) ? trim($data['review_text']) : null,
            'status' => self::STATUS_PENDING,
        ];

        try {
            $this->db->beginTransaction();

            // Insert review
            $reviewId = $this->create($reviewData);

            // Mark notification as reviewed if exists
            $this->markNotificationReviewed($userId, 'pandit', $assignmentId);

            $this->db->commit();

            return [
                'success' => true,
                'review_id' => $reviewId,
                'message' => 'Thank you for your review! It will be visible after moderation.',
            ];
        } catch (\Exception $e) {
            $this->db->rollBack();
            error_log("Review creation failed: " . $e->getMessage());
            return ['success' => false, 'error' => 'Failed to submit review. Please try again.'];
        }
    }

    /**
     * Create a Vendor review with full validation
     */
    public function createVendorReview(int $userId, int $orderId, array $data): array
    {
        // Validate eligibility
        $eligibility = $this->canReviewVendor($userId, $orderId);
        if (!$eligibility['valid']) {
            return ['success' => false, 'error' => $eligibility['error']];
        }

        // Validate data
        $validation = $this->validateReviewData($data, 'vendor');
        if (!$validation['valid']) {
            return ['success' => false, 'errors' => $validation['errors']];
        }

        $order = $eligibility['order'];

        // Prepare review data
        $reviewData = [
            'reviewer_id' => $userId,
            'target_type' => 'vendor',
            'target_id' => $order['vendor_id'],
            'order_id' => $orderId,
            'rating_overall' => (int)$data['rating_overall'],
            'item_quality' => !empty($data['item_quality']) ? (int)$data['item_quality'] : null,
            'delivery_time' => !empty($data['delivery_time']) ? (int)$data['delivery_time'] : null,
            'packaging' => !empty($data['packaging']) ? (int)$data['packaging'] : null,
            'value_for_money' => !empty($data['value_for_money']) ? (int)$data['value_for_money'] : null,
            'review_text' => !empty($data['review_text']) ? trim($data['review_text']) : null,
            'status' => self::STATUS_PENDING,
        ];

        try {
            $this->db->beginTransaction();

            // Insert review
            $reviewId = $this->create($reviewData);

            // Mark notification as reviewed if exists
            $this->markNotificationReviewed($userId, 'vendor', null, $orderId);

            $this->db->commit();

            return [
                'success' => true,
                'review_id' => $reviewId,
                'message' => 'Thank you for your review! It will be visible after moderation.',
            ];
        } catch (\Exception $e) {
            $this->db->rollBack();
            error_log("Review creation failed: " . $e->getMessage());
            return ['success' => false, 'error' => 'Failed to submit review. Please try again.'];
        }
    }

    /**
     * Update review status (for AI moderation)
     */
    public function updateModerationStatus(int $reviewId, bool $flagged, ?string $reason = null): bool
    {
        $data = [
            'ai_flag' => $flagged ? 1 : 0,
            'ai_moderation_reason' => $reason,
            'status' => $flagged ? self::STATUS_PENDING : self::STATUS_APPROVED,
        ];

        $success = $this->update($reviewId, $data);

        // If approved, update ratings
        if ($success && !$flagged) {
            $review = $this->find($reviewId);
            if ($review) {
                $this->recalculateRatings($review['target_type'], $review['target_id']);
            }
        }

        return $success;
    }

    /**
     * Admin approve review
     */
    public function approveReview(int $reviewId, int $adminId): bool
    {
        $review = $this->find($reviewId);
        if (!$review) {
            return false;
        }

        $success = $this->update($reviewId, [
            'status' => self::STATUS_APPROVED,
            'moderated_by' => $adminId,
            'moderated_at' => date('Y-m-d H:i:s'),
        ]);

        if ($success) {
            $this->recalculateRatings($review['target_type'], $review['target_id']);
        }

        return $success;
    }

    /**
     * Admin reject review
     */
    public function rejectReview(int $reviewId, int $adminId, ?string $reason = null): bool
    {
        $review = $this->find($reviewId);
        if (!$review) {
            return false;
        }

        return $this->update($reviewId, [
            'status' => self::STATUS_REJECTED,
            'rejection_reason' => $reason,
            'moderated_by' => $adminId,
            'moderated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Recalculate ratings for a Pandit or Vendor
     */
    public function recalculateRatings(string $targetType, int $targetId): void
    {
        // Get all approved reviews
        $sql = "
            SELECT 
                COUNT(*) as total_reviews,
                AVG(rating_overall) as avg_rating,
                SUM(CASE WHEN rating_overall >= 4 THEN 1 ELSE 0 END) as positive_reviews
            FROM SAI_reviews 
            WHERE target_type = :target_type 
            AND target_id = :target_id 
            AND status = 'approved'
        ";
        $stats = $this->rawOne($sql, ['target_type' => $targetType, 'target_id' => $targetId]);

        $totalReviews = (int)($stats['total_reviews'] ?? 0);
        $avgRating = $totalReviews > 0 ? round((float)$stats['avg_rating'], 2) : 0.00;
        $positivePercentage = $totalReviews > 0 
            ? round(($stats['positive_reviews'] / $totalReviews) * 100, 2) 
            : 0.00;

        if ($targetType === 'pandit') {
            $this->updatePanditRating($targetId, $avgRating, $totalReviews, $positivePercentage);
        } else {
            $this->updateVendorRating($targetId, $avgRating, $totalReviews, $positivePercentage);
        }
    }

    /**
     * Update Pandit profile ratings and badges
     */
    private function updatePanditRating(int $panditId, float $avgRating, int $totalReviews, float $positivePercentage): void
    {
        // Get current profile data
        $sql = "
            SELECT pp.*, 
                   (SELECT COUNT(*) FROM SAI_pandit_assignments WHERE pandit_id = :pandit_id AND status = 'completed') as completed_rituals
            FROM SAI_pandit_profiles pp 
            WHERE pp.user_id = :pandit_id2
        ";
        $profile = $this->rawOne($sql, ['pandit_id' => $panditId, 'pandit_id2' => $panditId]);

        if (!$profile) {
            return;
        }

        // Calculate badges
        $badges = [];

        // Top Rated: rating >= 4.8
        if ($avgRating >= self::TOP_RATED_THRESHOLD && $totalReviews >= 5) {
            $badges[] = self::BADGE_TOP_RATED;
        }

        // Experienced: completed rituals >= 50
        if ((int)$profile['completed_rituals'] >= self::EXPERIENCED_THRESHOLD) {
            $badges[] = self::BADGE_EXPERIENCED;
        }

        // Verified: documents verified by admin
        if (!empty($profile['is_documents_verified'])) {
            $badges[] = self::BADGE_VERIFIED;
        }

        // Trusted Partner: positive reviews >= 95%
        if ($positivePercentage >= self::TRUSTED_PARTNER_PERCENTAGE && $totalReviews >= 10) {
            $badges[] = self::BADGE_TRUSTED_PARTNER;
        }

        // Update profile
        $updateSql = "
            UPDATE SAI_pandit_profiles 
            SET average_rating = :avg_rating,
                total_rituals_performed = (SELECT COUNT(*) FROM SAI_pandit_assignments WHERE pandit_id = user_id AND status = 'completed'),
                positive_review_percentage = :positive_pct,
                trust_badges = :badges,
                updated_at = NOW()
            WHERE user_id = :pandit_id
        ";
        
        $stmt = $this->db->prepare($updateSql);
        $stmt->execute([
            'avg_rating' => $avgRating,
            'positive_pct' => $positivePercentage,
            'badges' => json_encode($badges),
            'pandit_id' => $panditId,
        ]);
    }

    /**
     * Update Vendor ratings and badges
     */
    private function updateVendorRating(int $vendorId, float $avgRating, int $totalReviews, float $positivePercentage): void
    {
        // Calculate badges
        $badges = [];

        // Top Rated
        if ($avgRating >= self::TOP_RATED_THRESHOLD && $totalReviews >= 5) {
            $badges[] = self::BADGE_TOP_RATED;
        }

        // Trusted Partner
        if ($positivePercentage >= self::TRUSTED_PARTNER_PERCENTAGE && $totalReviews >= 10) {
            $badges[] = self::BADGE_TRUSTED_PARTNER;
        }

        // Update vendor
        $updateSql = "
            UPDATE SAI_vendors 
            SET average_rating = :avg_rating,
                total_reviews = :total_reviews,
                positive_review_percentage = :positive_pct,
                trust_badges = :badges,
                updated_at = NOW()
            WHERE id = :vendor_id
        ";
        
        $stmt = $this->db->prepare($updateSql);
        $stmt->execute([
            'avg_rating' => $avgRating,
            'total_reviews' => $totalReviews,
            'positive_pct' => $positivePercentage,
            'badges' => json_encode($badges),
            'vendor_id' => $vendorId,
        ]);
    }

    /**
     * Get reviews for a Pandit
     */
    public function getPanditReviews(int $panditId, int $limit = 10, int $offset = 0, bool $approvedOnly = true): array
    {
        $statusCondition = $approvedOnly ? "AND r.status = 'approved'" : "";
        
        $sql = "
            SELECT r.*, u.name as reviewer_name,
                   a.scheduled_date, ri.name as ritual_name
            FROM SAI_reviews r
            INNER JOIN SAI_users u ON r.reviewer_id = u.id
            LEFT JOIN SAI_pandit_assignments a ON r.assignment_id = a.id
            LEFT JOIN SAI_rituals ri ON a.ritual_id = ri.id
            WHERE r.target_type = 'pandit' 
            AND r.target_id = :pandit_id
            {$statusCondition}
            ORDER BY r.created_at DESC
            LIMIT :limit OFFSET :offset
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':pandit_id', $panditId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    /**
     * Get reviews for a Vendor
     */
    public function getVendorReviews(int $vendorId, int $limit = 10, int $offset = 0, bool $approvedOnly = true): array
    {
        $statusCondition = $approvedOnly ? "AND r.status = 'approved'" : "";
        
        $sql = "
            SELECT r.*, u.name as reviewer_name
            FROM SAI_reviews r
            INNER JOIN SAI_users u ON r.reviewer_id = u.id
            WHERE r.target_type = 'vendor' 
            AND r.target_id = :vendor_id
            {$statusCondition}
            ORDER BY r.created_at DESC
            LIMIT :limit OFFSET :offset
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':vendor_id', $vendorId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    /**
     * Get all reviews for admin (with filters)
     */
    public function getAdminReviews(array $filters = [], int $limit = 20, int $offset = 0): array
    {
        $conditions = [];
        $params = [];

        if (!empty($filters['status'])) {
            $conditions[] = "r.status = :status";
            $params['status'] = $filters['status'];
        }

        if (!empty($filters['target_type'])) {
            $conditions[] = "r.target_type = :target_type";
            $params['target_type'] = $filters['target_type'];
        }

        if (!empty($filters['ai_flag'])) {
            $conditions[] = "r.ai_flag = 1";
        }

        $whereClause = !empty($conditions) ? "WHERE " . implode(" AND ", $conditions) : "";

        $sql = "
            SELECT r.*, 
                   u.name as reviewer_name, u.email as reviewer_email,
                   CASE 
                       WHEN r.target_type = 'pandit' THEN (SELECT name FROM SAI_users WHERE id = r.target_id)
                       WHEN r.target_type = 'vendor' THEN (SELECT name FROM SAI_vendors WHERE id = r.target_id)
                   END as target_name
            FROM SAI_reviews r
            INNER JOIN SAI_users u ON r.reviewer_id = u.id
            {$whereClause}
            ORDER BY 
                CASE r.status WHEN 'pending' THEN 0 ELSE 1 END,
                r.created_at DESC
            LIMIT :limit OFFSET :offset
        ";

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Get review statistics for admin dashboard
     */
    public function getStats(): array
    {
        $sql = "
            SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved,
                SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected,
                SUM(CASE WHEN ai_flag = 1 THEN 1 ELSE 0 END) as ai_flagged,
                SUM(CASE WHEN target_type = 'pandit' THEN 1 ELSE 0 END) as pandit_reviews,
                SUM(CASE WHEN target_type = 'vendor' THEN 1 ELSE 0 END) as vendor_reviews,
                AVG(rating_overall) as avg_rating
            FROM SAI_reviews
        ";
        
        return $this->rawOne($sql) ?: [
            'total' => 0, 'pending' => 0, 'approved' => 0, 'rejected' => 0,
            'ai_flagged' => 0, 'pandit_reviews' => 0, 'vendor_reviews' => 0, 'avg_rating' => 0
        ];
    }

    /**
     * Get user's submitted reviews
     */
    public function getUserReviews(int $userId): array
    {
        $sql = "
            SELECT r.*,
                   CASE 
                       WHEN r.target_type = 'pandit' THEN (SELECT name FROM SAI_users WHERE id = r.target_id)
                       WHEN r.target_type = 'vendor' THEN (SELECT name FROM SAI_vendors WHERE id = r.target_id)
                   END as target_name
            FROM SAI_reviews r
            WHERE r.reviewer_id = :user_id
            ORDER BY r.created_at DESC
        ";
        
        return $this->raw($sql, ['user_id' => $userId]);
    }

    /**
     * Create review notification
     */
    public function createReviewNotification(int $userId, string $targetType, int $targetId, ?int $assignmentId, ?int $orderId, string $message): int
    {
        $sql = "
            INSERT INTO SAI_review_notifications 
            (user_id, target_type, target_id, assignment_id, order_id, notification_text, expires_at)
            VALUES (:user_id, :target_type, :target_id, :assignment_id, :order_id, :text, DATE_ADD(NOW(), INTERVAL 30 DAY))
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'user_id' => $userId,
            'target_type' => $targetType,
            'target_id' => $targetId,
            'assignment_id' => $assignmentId,
            'order_id' => $orderId,
            'text' => $message,
        ]);
        
        return (int)$this->db->lastInsertId();
    }

    /**
     * Get pending review notifications for user
     */
    public function getPendingNotifications(int $userId): array
    {
        $sql = "
            SELECT n.*,
                   CASE 
                       WHEN n.target_type = 'pandit' THEN (SELECT name FROM SAI_users WHERE id = n.target_id)
                       WHEN n.target_type = 'vendor' THEN (SELECT name FROM SAI_vendors WHERE id = n.target_id)
                   END as target_name
            FROM SAI_review_notifications n
            WHERE n.user_id = :user_id 
            AND n.is_reviewed = 0
            AND (n.expires_at IS NULL OR n.expires_at > NOW())
            ORDER BY n.created_at DESC
        ";
        
        return $this->raw($sql, ['user_id' => $userId]);
    }

    /**
     * Mark notification as reviewed
     */
    private function markNotificationReviewed(int $userId, string $targetType, ?int $assignmentId = null, ?int $orderId = null): void
    {
        if ($assignmentId) {
            $sql = "UPDATE SAI_review_notifications SET is_reviewed = 1 WHERE user_id = :user_id AND assignment_id = :assignment_id";
            $this->db->prepare($sql)->execute(['user_id' => $userId, 'assignment_id' => $assignmentId]);
        } elseif ($orderId) {
            $sql = "UPDATE SAI_review_notifications SET is_reviewed = 1 WHERE user_id = :user_id AND order_id = :order_id";
            $this->db->prepare($sql)->execute(['user_id' => $userId, 'order_id' => $orderId]);
        }
    }

    /**
     * Check if review exists for assignment
     */
    public function hasReviewedAssignment(int $assignmentId): bool
    {
        return $this->findBy('assignment_id', $assignmentId) !== null;
    }

    /**
     * Check if review exists for order
     */
    public function hasReviewedOrder(int $orderId): bool
    {
        return $this->findBy('order_id', $orderId) !== null;
    }

    /**
     * Get badge display info
     */
    public static function getBadgeInfo(string $badge): array
    {
        return [
            'key' => $badge,
            'label' => self::BADGE_LABELS[$badge] ?? ucfirst($badge),
            'icon' => self::BADGE_ICONS[$badge] ?? '🏅',
        ];
    }

    /**
     * Format badges for display
     */
    public static function formatBadges(?string $badgesJson): array
    {
        if (empty($badgesJson)) {
            return [];
        }

        $badges = json_decode($badgesJson, true);
        if (!is_array($badges)) {
            return [];
        }

        return array_map([self::class, 'getBadgeInfo'], $badges);
    }
}
