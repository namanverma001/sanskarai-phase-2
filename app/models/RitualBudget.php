<?php
/**
 * Sanskar AI - Ritual Budget Model
 * ==================================
 * Manages ritual budget plans for users
 */

namespace App\Models;

use App\Core\Model;

class RitualBudget extends Model
{
    protected string $table = 'SAI_ritual_budgets';

    protected array $fillable = [
        'user_id',
        'ritual_type',
        'location',
        'guest_count',
        'tier',
        'total_estimated',
        'total_actual',
        'ai_request_id',
    ];

    /**
     * Get all budgets for a user, ordered by newest first
     */
    public function getByUser(int $userId): array
    {
        $sql = "SELECT * FROM {$this->table}
                WHERE user_id = :user_id
                ORDER BY created_at DESC";
        return $this->raw($sql, ['user_id' => $userId]);
    }

    /**
     * Get budget with nested items array
     */
    public function getWithItems(int $id): ?array
    {
        $budget = $this->find($id);
        if (!$budget) {
            return null;
        }

        $sql = "SELECT * FROM SAI_ritual_budget_items
                WHERE budget_id = :budget_id
                ORDER BY category ASC, id ASC";
        $budget['items'] = $this->raw($sql, ['budget_id' => $id]);

        return $budget;
    }

    /**
     * Check if a budget belongs to a user
     */
    public function belongsToUser(int $id, int $userId): bool
    {
        $budget = $this->find($id);
        return $budget !== null && (int) $budget['user_id'] === $userId;
    }

    /**
     * Recalculate and update total_estimated and total_actual from items
     */
    public function recalculateTotals(int $id): void
    {
        $sql = "SELECT
                    COALESCE(SUM(estimated_amount), 0) AS total_estimated,
                    COALESCE(SUM(actual_amount), 0)    AS total_actual
                FROM SAI_ritual_budget_items
                WHERE budget_id = :budget_id";

        $totals = $this->rawOne($sql, ['budget_id' => $id]);

        $updateSql = "UPDATE {$this->table}
                      SET total_estimated = :total_estimated,
                          total_actual    = :total_actual,
                          updated_at      = NOW()
                      WHERE id = :id";

        $stmt = $this->db->prepare($updateSql);
        $stmt->execute([
            'total_estimated' => $totals['total_estimated'],
            'total_actual'    => $totals['total_actual'],
            'id'              => $id,
        ]);
    }
}
