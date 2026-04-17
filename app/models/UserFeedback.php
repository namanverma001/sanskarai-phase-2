<?php
/**
 * Sanskar AI - User Feedback Model
 * =====================================
 * Manages user feedback interactions
 */

namespace App\Models;

use App\Core\Model;

class UserFeedback extends Model
{
    protected string $table = 'SAI_user_feedbacks';

    protected array $fillable = [
        'user_id',
        'name',
        'email',
        'phone',
        'community_name',
        'features_feedback',
        'likes_about',
        'improvements_for'
    ];

    protected bool $timestamps = true;

    /**
     * Store feedback entry
     */
    public function storeFeedback(array $data): int
    {
        // Encode JSON if features_feedback is an array
        if (isset($data['features_feedback']) && is_array($data['features_feedback'])) {
            $data['features_feedback'] = json_encode($data['features_feedback']);
        }
        
        return $this->create($data);
    }
    
    /**
     * Get all feedbacks with user basic info
     */
    public function getAllFeedbacks()
    {
        $sql = "SELECT f.*, u.role, u.status as user_status 
                FROM {$this->table} f 
                JOIN SAI_users u ON f.user_id = u.id 
                ORDER BY f.created_at DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
}
