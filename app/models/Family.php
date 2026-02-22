<?php
/**
 * Sanskar AI - Family Model
 * ==========================
 * Family and member management
 */

namespace App\Models;

use App\Core\Model;

class Family extends Model
{
    protected string $table = 'SAI_families';
    
    protected array $fillable = [
        'user_id',
        'family_name',
        'gotra',
        'nakshatra',
        'kul_devta',
        'family_deity',
        'address',
        'city',
        'state',
        'pincode',
        'country',
    ];
    
    /**
     * Get families by user
     */
    public function getByUserId(int $userId): array
    {
        return $this->where(['user_id' => $userId], 'created_at', 'DESC');
    }
    
    /**
     * Get family with members
     */
    public function getWithMembers(int $familyId): ?array
    {
        $family = $this->find($familyId);
        
        if (!$family) {
            return null;
        }
        
        $sql = "SELECT * FROM SAI_family_members WHERE family_id = :family_id ORDER BY is_primary DESC, name ASC";
        $family['members'] = $this->raw($sql, ['family_id' => $familyId]);
        
        return $family;
    }
    
    /**
     * Get user's primary family
     */
    public function getPrimaryFamily(int $userId): ?array
    {
        $families = $this->getByUserId($userId);
        return $families[0] ?? null;
    }
    
    /**
     * Add family member
     */
    public function addMember(int $familyId, array $data): int
    {
        $data['family_id'] = $familyId;
        $data['created_at'] = date('Y-m-d H:i:s');
        
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        
        $sql = "INSERT INTO SAI_family_members ($columns) VALUES ($placeholders)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($data);
        
        return (int) $this->db->lastInsertId();
    }
    
    /**
     * Update family member
     */
    public function updateMember(int $memberId, array $data): bool
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        
        $setClauses = [];
        foreach (array_keys($data) as $column) {
            $setClauses[] = "$column = :$column";
        }
        
        $data['id'] = $memberId;
        
        $sql = "UPDATE SAI_family_members SET " . implode(', ', $setClauses) . " WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute($data);
    }
    
    /**
     * Delete family member
     */
    public function deleteMember(int $memberId): bool
    {
        $sql = "DELETE FROM SAI_family_members WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $memberId]);
    }
    
    /**
     * Get member by ID
     */
    public function getMember(int $memberId): ?array
    {
        $sql = "SELECT * FROM SAI_family_members WHERE id = :id";
        return $this->rawOne($sql, ['id' => $memberId]);
    }
    
    /**
     * Get all gotras
     */
    public function getGotras(): array
    {
        $sql = "SELECT DISTINCT gotra FROM SAI_families WHERE gotra IS NOT NULL AND gotra != '' ORDER BY gotra ASC";
        $results = $this->raw($sql);
        return array_column($results, 'gotra');
    }
    
    /**
     * Check if family belongs to user
     */
    public function belongsToUser(int $familyId, int $userId): bool
    {
        $family = $this->find($familyId);
        return $family && $family['user_id'] == $userId;
    }
}
