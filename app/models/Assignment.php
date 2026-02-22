<?php
/**
 * Sanskar AI - Assignment Model
 * ===============================
 * Pandit assignment management
 */

namespace App\Models;

use App\Core\Model;

class Assignment extends Model
{
    protected string $table = 'SAI_pandit_assignments';
    
    protected array $fillable = [
        'ritual_id',
        'custom_ritual_id',
        'pandit_id',
        'user_id',
        'assigned_by',
        'scheduled_date',
        'scheduled_time',
        'end_time',
        'venue',
        'venue_address',
        'status',
        'pandit_notes',
        'user_notes',
        'booking_purpose',
        'cancellation_reason',
        'cancelled_by',
        'amount',
        'payment_status',
    ];
    
    /**
     * Get assignments for pandit
     */
    public function getForPandit(int $panditId, ?string $status = null): array
    {
        $sql = "
            SELECT a.*, 
                   r.name as ritual_name, r.category as ritual_category,
                   cr.name as custom_ritual_name,
                   u.name as user_name, u.mobile as user_mobile
            FROM SAI_pandit_assignments a
            LEFT JOIN SAI_rituals r ON a.ritual_id = r.id
            LEFT JOIN SAI_custom_rituals cr ON a.custom_ritual_id = cr.id
            INNER JOIN SAI_users u ON a.user_id = u.id
            WHERE a.pandit_id = :pandit_id
        ";
        
        $params = ['pandit_id' => $panditId];
        
        if ($status) {
            $sql .= " AND a.status = :status";
            $params['status'] = $status;
        }
        
        $sql .= " ORDER BY a.scheduled_date ASC, a.scheduled_time ASC";
        
        return $this->raw($sql, $params);
    }
    
    /**
     * Get assignments for user
     */
    public function getForUser(int $userId, ?string $status = null): array
    {
        $sql = "
            SELECT a.*, 
                   r.name as ritual_name, r.category as ritual_category,
                   cr.name as custom_ritual_name,
                   p.name as pandit_name, p.mobile as pandit_mobile,
                   pp.specialization, pp.average_rating
            FROM SAI_pandit_assignments a
            LEFT JOIN SAI_rituals r ON a.ritual_id = r.id
            LEFT JOIN SAI_custom_rituals cr ON a.custom_ritual_id = cr.id
            INNER JOIN SAI_users p ON a.pandit_id = p.id
            LEFT JOIN SAI_pandit_profiles pp ON p.id = pp.user_id
            WHERE a.user_id = :user_id
        ";
        
        $params = ['user_id' => $userId];
        
        if ($status) {
            $sql .= " AND a.status = :status";
            $params['status'] = $status;
        }
        
        $sql .= " ORDER BY a.scheduled_date DESC, a.scheduled_time DESC";
        
        return $this->raw($sql, $params);
    }
    
    /**
     * Confirm assignment
     */
    public function confirm(int $id): bool
    {
        return $this->update($id, ['status' => 'confirmed']);
    }
    
    /**
     * Start assignment
     */
    public function start(int $id): bool
    {
        return $this->update($id, ['status' => 'in_progress']);
    }
    
    /**
     * Complete assignment
     */
    public function complete(int $id): bool
    {
        return $this->update($id, ['status' => 'completed']);
    }
    
    /**
     * Cancel assignment
     */
    public function cancel(int $id, int $cancelledBy, string $reason): bool
    {
        return $this->update($id, [
            'status' => 'cancelled',
            'cancelled_by' => $cancelledBy,
            'cancellation_reason' => $reason,
        ]);
    }
    
    /**
     * Get upcoming assignments
     */
    public function getUpcoming(int $days = 7): array
    {
        $sql = "
            SELECT a.*, 
                   r.name as ritual_name,
                   u.name as user_name,
                   p.name as pandit_name
            FROM SAI_pandit_assignments a
            LEFT JOIN SAI_rituals r ON a.ritual_id = r.id
            INNER JOIN SAI_users u ON a.user_id = u.id
            INNER JOIN SAI_users p ON a.pandit_id = p.id
            WHERE a.scheduled_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL :days DAY)
            AND a.status IN ('pending', 'confirmed')
            ORDER BY a.scheduled_date ASC, a.scheduled_time ASC
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':days', $days, \PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    /**
     * Get assignment statistics
     */
    public function getStats(): array
    {
        $sql = "
            SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END) as confirmed,
                SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
                SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled
            FROM SAI_pandit_assignments
        ";
        
        return $this->rawOne($sql);
    }
}
