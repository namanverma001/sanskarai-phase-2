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
            SET approval_status = 'approved', approved_by = :approved_by, approved_at = NOW(), updated_at = NOW()
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
}
