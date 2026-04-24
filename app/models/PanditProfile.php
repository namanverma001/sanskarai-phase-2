<?php
/**
 * Sanskar AI - Pandit Profile Model
 * ===================================
 * Pandit profile management
 */

namespace App\Models;

use App\Core\Model;
use App\Config\App;

class PanditProfile extends Model
{
    protected string $table = 'SAI_pandit_profiles';
    
    protected array $fillable = [
        'user_id',
        'specialization',
        'experience_years',
        'bio',
        'profile_photo',
        'languages',
        'availability_days',
        'hourly_rate',
        'approval_status',
        'approved_by',
        'approved_at',
        'rejection_reason',
        'latitude',
        'longitude',
        'city',
        'pincode',
        'service_area_km',
        'map_url',
    ];
    
    /**
     * Get profile by user ID
     */
    public function getByUserId(int $userId): ?array
    {
        return $this->findBy('user_id', $userId);
    }
    
    /**
     * Create profile for pandit
     */
    public function createProfile(int $userId, array $data): int
    {
        $data['user_id'] = $userId;
        $data['approval_status'] = App::APPROVAL_PENDING;
        
        return $this->create($data);
    }
    
    /**
     * Approve pandit
     */
    public function approve(int $profileId, int $approvedBy): bool
    {
        $sql = "
            UPDATE SAI_pandit_profiles 
            SET approval_status = 'approved', approved_by = :approved_by, approved_at = NOW(), 
                updated_at = NOW()
            WHERE id = :id
        ";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $profileId, 'approved_by' => $approvedBy]);
    }
    
    /**
     * Reject pandit
     */
    public function reject(int $profileId, ?string $reason = null): bool
    {
        $sql = "
            UPDATE SAI_pandit_profiles 
            SET approval_status = 'rejected', rejection_reason = :reason, updated_at = NOW()
            WHERE id = :id
        ";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $profileId, 'reason' => $reason]);
    }
    
    /**
     * Get pending profiles
     */
    public function getPending(): array
    {
        return $this->where(['approval_status' => App::APPROVAL_PENDING], 'created_at', 'ASC');
    }
    
    /**
     * Update rating
     */
    public function updateRating(int $userId): bool
    {
        $sql = "
            UPDATE SAI_pandit_profiles p
            SET average_rating = (
                SELECT COALESCE(AVG(rating), 0) 
                FROM SAI_ritual_reviews 
                WHERE pandit_id = :user_id
            ),
            total_rituals_performed = (
                SELECT COUNT(*) 
                FROM SAI_pandit_assignments 
                WHERE pandit_id = :user_id2 AND status = 'completed'
            )
            WHERE p.user_id = :user_id3
        ";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'user_id' => $userId,
            'user_id2' => $userId,
            'user_id3' => $userId,
        ]);
    }
    
    /**
     * Get full profile with user data
     */
    public function getFullProfile(int $userId): ?array
    {
        $sql = "
            SELECT p.*, u.name, u.email, u.mobile, u.status as user_status
            FROM SAI_pandit_profiles p
            INNER JOIN SAI_users u ON p.user_id = u.id
            WHERE p.user_id = :user_id
        ";
        
        return $this->rawOne($sql, ['user_id' => $userId]);
    }

    /**
     * Get trust badges for a pandit
     */
    public function getBadges(int $profileId): array
    {
        $profile = $this->find($profileId);
        if (!$profile || empty($profile['trust_badges'])) {
            return [];
        }
        
        $badges = json_decode($profile['trust_badges'], true);
        return is_array($badges) ? $badges : [];
    }

    /**
     * Update trust badges for a pandit
     */
    public function updateBadges(int $profileId, array $badges): bool
    {
        $sql = "UPDATE {$this->table} SET trust_badges = :badges, updated_at = NOW() WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'id' => $profileId,
            'badges' => json_encode(array_values(array_unique($badges)))
        ]);
    }

    /**
     * Add a badge to pandit profile
     */
    public function addBadge(int $profileId, string $badge): bool
    {
        $badges = $this->getBadges($profileId);
        if (!in_array($badge, $badges)) {
            $badges[] = $badge;
            return $this->updateBadges($profileId, $badges);
        }
        return true;
    }

    /**
     * Remove a badge from pandit profile
     */
    public function removeBadge(int $profileId, string $badge): bool
    {
        $badges = $this->getBadges($profileId);
        $badges = array_filter($badges, fn($b) => $b !== $badge);
        return $this->updateBadges($profileId, $badges);
    }

    /**
     * Check if pandit has a specific badge
     */
    public function hasBadge(int $profileId, string $badge): bool
    {
        return in_array($badge, $this->getBadges($profileId));
    }

    /**
     * Get pandits with specific badge
     */
    public function getByBadge(string $badge): array
    {
        $sql = "
            SELECT p.*, u.name, u.email
            FROM {$this->table} p
            INNER JOIN SAI_users u ON p.user_id = u.id
            WHERE p.approval_status = 'approved'
            AND JSON_CONTAINS(p.trust_badges, :badge)
        ";
        
        return $this->raw($sql, ['badge' => json_encode($badge)]);
    }

    /**
     * Search pandits by location with Haversine formula and apply filters
     */
    public function searchByLocation(?float $lat, ?float $lng, int $maxDistanceKm, array $filters = []): array
    {
        $params = [];
        
        $sql = "
            SELECT p.*, u.name, u.email, u.mobile, u.status as user_status
        ";
        
        if ($lat !== null && $lng !== null) {
            // Haversine formula
            $sql .= ", (
                6371 * acos(
                    cos(radians(:lat1)) * cos(radians(p.latitude)) *
                    cos(radians(p.longitude) - radians(:lng1)) +
                    sin(radians(:lat2)) * sin(radians(p.latitude))
                )
            ) AS distance";
            $params['lat1'] = $lat;
            $params['lng1'] = $lng;
            $params['lat2'] = $lat;
        } else {
            $sql .= ", NULL as distance";
        }
        
        $sql .= "
            FROM {$this->table} p
            INNER JOIN SAI_users u ON p.user_id = u.id
            WHERE p.approval_status = 'approved' AND u.status = 'active'
        ";
        
        // Location filters (if no lat/lng provided, maybe city/pincode is used)
        if (!empty($filters['city'])) {
            $sql .= " AND p.city LIKE :city";
            $params['city'] = '%' . $filters['city'] . '%';
        }
        if (!empty($filters['pincode'])) {
            $sql .= " AND p.pincode = :pincode";
            $params['pincode'] = $filters['pincode'];
        }
        
        // Other filters
        if (!empty($filters['specialization'])) {
            $sql .= " AND p.specialization LIKE :spec";
            $params['spec'] = '%' . $filters['specialization'] . '%';
        }
        if (!empty($filters['min_rating'])) {
            $sql .= " AND p.average_rating >= :min_rating";
            $params['min_rating'] = $filters['min_rating'];
        }
        if (!empty($filters['max_charges'])) {
            $sql .= " AND p.hourly_rate <= :max_charges";
            $params['max_charges'] = $filters['max_charges'];
        }

        // Apply distance filter using HAVING
        if ($lat !== null && $lng !== null) {
            $sql .= " HAVING distance <= :max_distance AND distance <= COALESCE(p.service_area_km, 50)";
            $params['max_distance'] = $maxDistanceKm;
        }
        
        // Order
        if ($lat !== null && $lng !== null) {
            $sql .= " ORDER BY distance ASC";
        } else {
            $sql .= " ORDER BY p.average_rating DESC, p.total_rituals_performed DESC";
        }
        
        return $this->raw($sql, $params);
    }
}
