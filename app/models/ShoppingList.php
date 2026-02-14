<?php
/**
 * Sanskar AI - Shopping List Model
 * ==================================
 * Shopping list management for rituals
 */

namespace App\Models;

use App\Core\Model;

class ShoppingList extends Model
{
    protected string $table = 'SAI_shopping_list';

    protected array $fillable = [
        'user_id',
        'ritual_id',
        'custom_ritual_id',
        'assignment_id',
        'item_name',
        'item_name_local',
        'quantity',
        'unit',
        'category',
        'estimated_cost',
        'actual_cost',
        'is_purchased',
        'purchased_at',
        'store_name',
        'notes',
        'priority',
    ];

    /**
     * Get user's shopping list
     */
    public function getByUser(int $userId, bool $pendingOnly = false): array
    {
        $sql = "
            SELECT sl.*, r.name as ritual_name, cr.name as custom_ritual_name
            FROM SAI_shopping_list sl
            LEFT JOIN SAI_rituals r ON sl.ritual_id = r.id
            LEFT JOIN SAI_custom_rituals cr ON sl.custom_ritual_id = cr.id
            WHERE sl.user_id = :user_id
        ";

        if ($pendingOnly) {
            $sql .= " AND sl.is_purchased = 0";
        }

        $sql .= " ORDER BY sl.priority DESC, sl.item_name ASC";

        return $this->raw($sql, ['user_id' => $userId]);
    }

    /**
     * Create from ritual items
     */
    public function createFromRitual(int $userId, int $ritualId): int
    {
        $sql = "
            INSERT INTO SAI_shopping_list (user_id, ritual_id, item_name, item_name_local, quantity, unit, category, estimated_cost, priority, created_at)
            SELECT :user_id, :ritual_id, item_name, item_name_local, quantity, unit, category, approximate_cost,
                   CASE WHEN is_mandatory = 1 THEN 'high' ELSE 'medium' END,
                   NOW()
            FROM SAI_ritual_items
            WHERE ritual_id = :ritual_id2
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'user_id' => $userId,
            'ritual_id' => $ritualId,
            'ritual_id2' => $ritualId,
        ]);

        return $stmt->rowCount();
    }

    /**
     * Create from user ritual items
     */
    public function createFromUserRitual(int $userId, int $userRitualId): int
    {
        $sql = "
            INSERT INTO SAI_shopping_list (user_id, ritual_id, item_name, item_name_local, quantity, unit, category, estimated_cost, priority, created_at)
            SELECT :user_id, NULL, item_name, item_name_local, quantity, unit, 'General', 0,
                   CASE WHEN is_mandatory = 1 THEN 'high' ELSE 'medium' END,
                   NOW()
            FROM SAI_user_ritual_items
            WHERE user_ritual_id = :user_ritual_id
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'user_id' => $userId,
            'user_ritual_id' => $userRitualId,
        ]);

        return $stmt->rowCount();
    }

    /**
     * Mark as purchased
     */
    public function markPurchased(int $id, ?float $actualCost = null, ?string $storeName = null): bool
    {
        $data = [
            'is_purchased' => 1,
            'purchased_at' => date('Y-m-d H:i:s'),
        ];

        if ($actualCost !== null) {
            $data['actual_cost'] = $actualCost;
        }

        if ($storeName !== null) {
            $data['store_name'] = $storeName;
        }

        return $this->update($id, $data);
    }

    /**
     * Mark as not purchased
     */
    public function markNotPurchased(int $id): bool
    {
        return $this->update($id, [
            'is_purchased' => 0,
            'purchased_at' => null,
        ]);
    }

    /**
     * Get summary
     */
    public function getSummary(int $userId): array
    {
        $sql = "
            SELECT 
                COUNT(*) as total_items,
                SUM(CASE WHEN is_purchased = 1 THEN 1 ELSE 0 END) as purchased,
                SUM(CASE WHEN is_purchased = 0 THEN 1 ELSE 0 END) as pending,
                COALESCE(SUM(estimated_cost * quantity), 0) as estimated_total,
                COALESCE(SUM(CASE WHEN is_purchased = 1 THEN actual_cost * quantity ELSE 0 END), 0) as actual_total
            FROM SAI_shopping_list
            WHERE user_id = :user_id
        ";

        return $this->rawOne($sql, ['user_id' => $userId]);
    }

    /**
     * Clear completed items
     */
    public function clearPurchased(int $userId): int
    {
        $sql = "DELETE FROM SAI_shopping_list WHERE user_id = :user_id AND is_purchased = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        return $stmt->rowCount();
    }

    /**
     * Clear all items for user (for fresh shopping list generation)
     */
    public function clearAll(int $userId): int
    {
        $sql = "DELETE FROM SAI_shopping_list WHERE user_id = :user_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        return $stmt->rowCount();
    }
}
