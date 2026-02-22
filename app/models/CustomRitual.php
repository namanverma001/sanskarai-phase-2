<?php
/**
 * Sanskar AI - Custom Ritual Model
 * ==================================
 * Custom ritual management
 */

namespace App\Models;

use App\Core\Model;

class CustomRitual extends Model
{
    protected string $table = 'SAI_custom_rituals';
    
    protected array $fillable = [
        'user_id',
        'assigned_pandit_id',
        'base_ritual_id',
        'name',
        'description',
        'purpose',
        'scheduled_date',
        'scheduled_time',
        'venue',
        'special_requirements',
        'budget',
        'status',
        'validated_by',
        'validation_notes',
        'validated_at',
    ];
    
    /**
     * Get user's custom rituals with pandit info
     */
    public function getByUser(int $userId): array
    {
        $sql = "
            SELECT cr.*, ap.name as assigned_pandit_name
            FROM SAI_custom_rituals cr
            LEFT JOIN SAI_users ap ON cr.assigned_pandit_id = ap.id
            WHERE cr.user_id = :user_id
            ORDER BY cr.created_at DESC
        ";
        return $this->raw($sql, ['user_id' => $userId]);
    }
    
    /**
     * Get with details
     */
    public function getWithDetails(int $id): ?array
    {
        $sql = "
            SELECT cr.*, r.name as base_ritual_name, r.category as base_ritual_category,
                   u.name as user_name, v.name as validator_name,
                   ap.name as assigned_pandit_name, pp.specialization as assigned_pandit_specialization
            FROM SAI_custom_rituals cr
            LEFT JOIN SAI_rituals r ON cr.base_ritual_id = r.id
            LEFT JOIN SAI_users u ON cr.user_id = u.id
            LEFT JOIN SAI_users v ON cr.validated_by = v.id
            LEFT JOIN SAI_users ap ON cr.assigned_pandit_id = ap.id
            LEFT JOIN SAI_pandit_profiles pp ON ap.id = pp.user_id
            WHERE cr.id = :id
        ";
        
        $ritual = $this->rawOne($sql, ['id' => $id]);
        
        if (!$ritual) {
            return null;
        }
        
        // Get steps
        $sql = "SELECT * FROM SAI_custom_ritual_steps WHERE custom_ritual_id = :id ORDER BY step_number ASC";
        $ritual['steps'] = $this->raw($sql, ['id' => $id]);
        
        return $ritual;
    }
    
    /**
     * Get pending for validation (optionally filtered by assigned pandit)
     */
    public function getPendingValidation(?int $panditId = null): array
    {
        $params = [];
        $sql = "
            SELECT cr.*, u.name as user_name, r.name as base_ritual_name
            FROM SAI_custom_rituals cr
            INNER JOIN SAI_users u ON cr.user_id = u.id
            LEFT JOIN SAI_rituals r ON cr.base_ritual_id = r.id
            WHERE cr.status = 'submitted'
        ";
        
        if ($panditId !== null) {
            $sql .= " AND cr.assigned_pandit_id = :pandit_id";
            $params['pandit_id'] = $panditId;
        }
        
        $sql .= " ORDER BY cr.created_at ASC";
        
        return $this->raw($sql, $params);
    }
    
    /**
     * Submit for validation
     */
    public function submit(int $id): bool
    {
        return $this->update($id, ['status' => 'submitted']);
    }
    
    /**
     * Approve ritual
     */
    public function approve(int $id, int $validatedBy, ?string $notes = null): bool
    {
        return $this->update($id, [
            'status' => 'approved',
            'validated_by' => $validatedBy,
            'validation_notes' => $notes,
            'validated_at' => date('Y-m-d H:i:s'),
        ]);
    }
    
    /**
     * Reject ritual
     */
    public function reject(int $id, int $validatedBy, string $notes): bool
    {
        return $this->update($id, [
            'status' => 'rejected',
            'validated_by' => $validatedBy,
            'validation_notes' => $notes,
            'validated_at' => date('Y-m-d H:i:s'),
        ]);
    }
    
    /**
     * Get validation history for a pandit (approved/rejected rituals)
     */
    public function getValidationHistory(int $panditId): array
    {
        $sql = "
            SELECT cr.*, u.name as user_name, r.name as base_ritual_name
            FROM SAI_custom_rituals cr
            INNER JOIN SAI_users u ON cr.user_id = u.id
            LEFT JOIN SAI_rituals r ON cr.base_ritual_id = r.id
            WHERE cr.validated_by = :pandit_id
            AND cr.status IN ('approved', 'rejected')
            ORDER BY cr.validated_at DESC
        ";
        
        return $this->raw($sql, ['pandit_id' => $panditId]);
    }
    
    /**
     * Add custom step
     */
    public function addStep(int $customRitualId, array $data): int
    {
        $data['custom_ritual_id'] = $customRitualId;
        $data['created_at'] = date('Y-m-d H:i:s');
        
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        
        $sql = "INSERT INTO SAI_custom_ritual_steps ($columns) VALUES ($placeholders)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($data);
        
        return (int) $this->db->lastInsertId();
    }
    
    /**
     * Update step
     */
    public function updateStep(int $stepId, array $data): bool
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        
        $setClauses = [];
        foreach (array_keys($data) as $column) {
            $setClauses[] = "$column = :$column";
        }
        
        $data['id'] = $stepId;
        
        $sql = "UPDATE SAI_custom_ritual_steps SET " . implode(', ', $setClauses) . " WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute($data);
    }
    
    /**
     * Delete step
     */
    public function deleteStep(int $stepId): bool
    {
        $sql = "DELETE FROM SAI_custom_ritual_steps WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $stepId]);
    }
}
