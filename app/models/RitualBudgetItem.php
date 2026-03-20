<?php
/**
 * Sanskar AI - Ritual Budget Item Model
 * =======================================
 * Manages individual line items within a ritual budget plan
 */

namespace App\Models;

use App\Core\Model;

class RitualBudgetItem extends Model
{
    protected string $table = 'SAI_ritual_budget_items';

    protected array $fillable = [
        'budget_id',
        'category',
        'item_name',
        'estimated_amount',
        'actual_amount',
        'is_custom',
        'notes',
    ];

    /**
     * Get all items for a given budget, ordered by category then insertion order
     */
    public function getByBudget(int $budgetId): array
    {
        $sql = "SELECT * FROM {$this->table}
                WHERE budget_id = :budget_id
                ORDER BY category ASC, id ASC";
        return $this->raw($sql, ['budget_id' => $budgetId]);
    }

    /**
     * Return the budget_id that owns the given item, or null if not found
     */
    public function getBudgetId(int $itemId): ?int
    {
        $item = $this->find($itemId);
        return $item !== null ? (int) $item['budget_id'] : null;
    }
}
