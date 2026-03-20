<?php
/**
 * Sanskar AI - Budget Service
 * ============================
 * Encapsulates all business logic for the Ritual Budget Planner feature.
 * Handles AI generation, persistence, item mutations, and totals recalculation.
 */

namespace App\Services;

use App\Models\RitualBudget;
use App\Models\RitualBudgetItem;

class BudgetService
{
    private RitualBudget $budgetModel;
    private RitualBudgetItem $itemModel;

    public function __construct()
    {
        $this->budgetModel = new RitualBudget();
        $this->itemModel   = new RitualBudgetItem();
    }

    // =========================================================================
    // PUBLIC API
    // =========================================================================

    /**
     * Validate input, call AI, persist budget + items, recalculate totals.
     *
     * @param int   $userId
     * @param array $input  Keys: ritual_type, location, guest_count, tier
     * @return array ['success' => bool, 'message' => string, 'data' => mixed]
     */
    public function generateAndSave(int $userId, array $input): array
    {
        // --- Validation ---
        $ritualType = trim($input['ritual_type'] ?? '');
        if ($ritualType === '') {
            return $this->error('Ritual type is required.');
        }

        $guestCount = isset($input['guest_count']) ? (int) $input['guest_count'] : 0;
        if ($guestCount < 1 || $guestCount > 10000) {
            return $this->error('Guest count must be between 1 and 10,000.');
        }

        $tier = strtolower(trim($input['tier'] ?? ''));
        if (!in_array($tier, ['basic', 'standard', 'premium'], true)) {
            return $this->error('Tier must be one of: basic, standard, premium.');
        }

        $location = trim($input['location'] ?? '');

        // --- AI Generation ---
        $aiService = new AIService();
        $aiResult  = $aiService->generateBudget($userId, [
            'ritual_type' => $ritualType,
            'location'    => $location,
            'guest_count' => $guestCount,
            'tier'        => $tier,
        ]);

        if (!($aiResult['success'] ?? false)) {
            return $this->error($aiResult['error'] ?? 'AI budget generation failed.');
        }

        $categories = $aiResult['budget'] ?? [];
        if (empty($categories)) {
            return $this->error('AI returned an empty budget. Please try again.');
        }

        // --- Persist ---
        $budgetId = $this->budgetModel->create([
            'user_id'         => $userId,
            'ritual_type'     => $ritualType,
            'location'        => $location,
            'guest_count'     => $guestCount,
            'tier'            => $tier,
            'total_estimated' => 0,
            'total_actual'    => 0,
            'ai_request_id'   => $aiResult['request_id'] ?? null,
        ]);

        foreach ($categories as $cat) {
            $categoryName = $cat['category'] ?? 'General';
            foreach ($cat['items'] ?? [] as $item) {
                $this->itemModel->create([
                    'budget_id'        => $budgetId,
                    'category'         => $categoryName,
                    'item_name'        => $item['item_name'] ?? 'Item',
                    'estimated_amount' => max(0, (float) ($item['estimated_amount'] ?? 0)),
                    'actual_amount'    => null,
                    'is_custom'        => 0,
                    'notes'            => $item['notes'] ?? null,
                ]);
            }
        }

        $this->recalculateTotals($budgetId);

        $budget = $this->budgetModel->getWithItems($budgetId);

        return $this->success('Budget generated successfully.', $budget);
    }

    /**
     * Update the estimated amount for a budget item.
     *
     * @return array ['success' => bool, 'message' => string, 'data' => mixed]
     */
    public function updateItemEstimate(int $itemId, int $userId, float $amount): array
    {
        if ($amount < 0) {
            return $this->error('Estimated amount cannot be negative.');
        }

        if (!$this->ownsItem($itemId, $userId)) {
            return $this->forbidden();
        }

        $this->itemModel->update($itemId, ['estimated_amount' => $amount]);

        $budgetId = $this->itemModel->getBudgetId($itemId);
        $this->recalculateTotals($budgetId);

        $budget = $this->budgetModel->getWithItems($budgetId);

        return $this->success('Estimate updated.', $budget);
    }

    /**
     * Add a custom expense item to a budget.
     *
     * @return array ['success' => bool, 'message' => string, 'data' => mixed]
     */
    public function addCustomItem(int $budgetId, int $userId, string $name, float $amount): array
    {
        if (!$this->ownsBudget($budgetId, $userId)) {
            return $this->forbidden();
        }

        $name = trim($name);
        if ($name === '') {
            return $this->error('Item name cannot be empty.');
        }

        if ($amount < 0) {
            return $this->error('Amount cannot be negative.');
        }

        $this->itemModel->create([
            'budget_id'        => $budgetId,
            'category'         => 'Custom',
            'item_name'        => $name,
            'estimated_amount' => $amount,
            'actual_amount'    => null,
            'is_custom'        => 1,
            'notes'            => null,
        ]);

        $this->recalculateTotals($budgetId);

        $budget = $this->budgetModel->getWithItems($budgetId);

        return $this->success('Custom item added.', $budget);
    }

    /**
     * Delete a budget item and recalculate totals.
     *
     * @return array ['success' => bool, 'message' => string, 'data' => mixed]
     */
    public function deleteItem(int $itemId, int $userId): array
    {
        if (!$this->ownsItem($itemId, $userId)) {
            return $this->forbidden();
        }

        $budgetId = $this->itemModel->getBudgetId($itemId);

        $this->itemModel->delete($itemId);
        $this->recalculateTotals($budgetId);

        $budget = $this->budgetModel->getWithItems($budgetId);

        return $this->success('Item deleted.', $budget);
    }

    /**
     * Record the actual amount spent for a budget item.
     *
     * @return array ['success' => bool, 'message' => string, 'data' => mixed]
     */
    public function recordActual(int $itemId, int $userId, float $amount): array
    {
        if ($amount < 0) {
            return $this->error('Actual amount cannot be negative.');
        }

        if (!$this->ownsItem($itemId, $userId)) {
            return $this->forbidden();
        }

        $this->itemModel->update($itemId, ['actual_amount' => $amount]);

        $budgetId = $this->itemModel->getBudgetId($itemId);
        $this->recalculateTotals($budgetId);

        $budget = $this->budgetModel->getWithItems($budgetId);

        return $this->success('Actual amount recorded.', $budget);
    }

    /**
     * Delete a budget (CASCADE removes all items).
     *
     * @return array ['success' => bool, 'message' => string, 'data' => mixed]
     */
    public function deleteBudget(int $budgetId, int $userId): array
    {
        if (!$this->ownsBudget($budgetId, $userId)) {
            return $this->forbidden();
        }

        $this->budgetModel->delete($budgetId);

        return $this->success('Budget deleted.');
    }

    /**
     * Get a budget with its items, enforcing ownership.
     *
     * @return ?array  Full budget array with nested items, or null if not found / not owned.
     */
    public function getBudgetWithItems(int $budgetId, int $userId): ?array
    {
        if (!$this->ownsBudget($budgetId, $userId)) {
            return null;
        }

        return $this->budgetModel->getWithItems($budgetId);
    }

    /**
     * Get all budgets for a user.
     *
     * @return array
     */
    public function getUserBudgets(int $userId): array
    {
        return $this->budgetModel->getByUser($userId);
    }

    /**
     * Suggest vendors from SAI_vendors matching budget categories and location.
     *
     * @return array ['success' => bool, 'message' => string, 'data' => array keyed by category]
     */
    public function suggestVendors(int $budgetId, int $userId): array
    {
        if (!$this->ownsBudget($budgetId, $userId)) {
            return $this->forbidden();
        }

        $budget = $this->budgetModel->getWithItems($budgetId);
        if (!$budget) {
            return $this->error('Budget not found.');
        }

        $location = $budget['location'] ?? '';
        $suggestions = [];

        // Collect unique categories from items
        $categories = array_unique(array_column($budget['items'] ?? [], 'category'));

        foreach ($categories as $category) {
            $sql = "SELECT id, name, category, city, min_price, max_price
                    FROM SAI_vendors
                    WHERE city LIKE :location
                      AND category LIKE :category
                      AND is_active = 1
                    LIMIT 5";

            $vendors = $this->budgetModel->raw($sql, [
                'location' => '%' . $location . '%',
                'category' => '%' . $category . '%',
            ]);

            $suggestions[$category] = $vendors;
        }

        return $this->success('Vendor suggestions loaded.', $suggestions);
    }

    // =========================================================================
    // PRIVATE HELPERS
    // =========================================================================

    /**
     * Trigger totals recalculation on the budget model.
     */
    private function recalculateTotals(int $budgetId): void
    {
        $this->budgetModel->recalculateTotals($budgetId);
    }

    /**
     * Check whether the given user owns the budget that contains $itemId.
     */
    private function ownsItem(int $itemId, int $userId): bool
    {
        $budgetId = $this->itemModel->getBudgetId($itemId);
        if ($budgetId === null) {
            return false;
        }
        return $this->budgetModel->belongsToUser($budgetId, $userId);
    }

    /**
     * Check whether the given user owns the budget.
     */
    private function ownsBudget(int $budgetId, int $userId): bool
    {
        return $this->budgetModel->belongsToUser($budgetId, $userId);
    }

    // =========================================================================
    // RESPONSE HELPERS
    // =========================================================================

    private function success(string $message, mixed $data = null): array
    {
        return ['success' => true, 'message' => $message, 'data' => $data];
    }

    private function error(string $message, mixed $data = null): array
    {
        return ['success' => false, 'message' => $message, 'data' => $data];
    }

    private function forbidden(): array
    {
        return ['success' => false, 'message' => 'Unauthorized. You do not own this resource.', 'data' => null, 'code' => 403];
    }
}
