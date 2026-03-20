<?php
/**
 * Sanskar AI - Budget Controller
 * ================================
 * Handles ritual budget planning, editing, tracking, PDF export, and deletion.
 */

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Services\BudgetService;

class BudgetController extends Controller
{
    private BudgetService $budgetService;

    public function __construct()
    {
        parent::__construct();
        $this->budgetService = new BudgetService();
    }

    /**
     * GET /user/budgets
     * List all budgets for the authenticated user.
     */
    public function index(): void
    {
        $userId  = Auth::id();
        $budgets = $this->budgetService->getUserBudgets($userId);

        $this->viewWithLayout('user/budget-list', 'layouts/user', [
            'title'   => 'My Budgets',
            'budgets' => $budgets,
        ]);
    }

    /**
     * GET /user/budgets/create
     * Show the budget creation form.
     */
    public function create(): void
    {
        $this->viewWithLayout('user/budget-create', 'layouts/user', [
            'title' => 'Plan New Budget',
        ]);
    }

    /**
     * POST /user/budgets
     * Generate and persist an AI budget.
     */
    public function store(): void
    {
        if (!$this->verifyCsrf()) {
            $this->back(['error' => 'Invalid CSRF token.']);
            return;
        }

        $userId = Auth::id();
        $input  = $this->only(['ritual_type', 'location', 'guest_count', 'tier']);

        $result = $this->budgetService->generateAndSave($userId, $input);

        if (!$result['success']) {
            $this->back(['error' => $result['message']]);
            return;
        }

        $budgetId = $result['data']['id'] ?? null;
        $this->redirect("/user/budgets/{$budgetId}", ['success' => $result['message']]);
    }

    /**
     * GET /user/budgets/{id}
     * View budget detail with expense tracker and vendor suggestions.
     */
    public function show(string $id): void
    {
        $userId = Auth::id();
        $budget = $this->budgetService->getBudgetWithItems((int) $id, $userId);

        if (!$budget) {
            $this->redirect('/user/budgets', ['error' => 'Budget not found.']);
            return;
        }

        $vendors = $this->budgetService->suggestVendors((int) $id, $userId);

        $this->viewWithLayout('user/budget-detail', 'layouts/user', [
            'title'   => $budget['ritual_type'] . ' Budget',
            'budget'  => $budget,
            'vendors' => $vendors,
        ]);
    }

    /**
     * POST /user/budgets/items/{id}  (AJAX)
     * Update the estimated amount for a budget item.
     */
    public function updateItem(string $id): void
    {
        if (!$this->verifyCsrf()) {
            $this->error('Invalid CSRF token.', 400);
            return;
        }

        $userId = Auth::id();
        $amount = (float) $this->input('estimated_amount', 0);

        $result = $this->budgetService->updateItemEstimate((int) $id, $userId, $amount);

        $code = ($result['code'] ?? null) === 403 ? 403 : ($result['success'] ? 200 : 400);
        $this->json([
            'success' => $result['success'],
            'message' => $result['message'],
            'data'    => $result['data'],
        ], $code);
    }

    /**
     * POST /user/budgets/{id}/items  (AJAX)
     * Add a custom expense item to a budget.
     */
    public function addItem(string $id): void
    {
        if (!$this->verifyCsrf()) {
            $this->error('Invalid CSRF token.', 400);
            return;
        }

        $userId = Auth::id();
        $name   = trim((string) $this->input('item_name', ''));
        $amount = (float) $this->input('estimated_amount', 0);

        $result = $this->budgetService->addCustomItem((int) $id, $userId, $name, $amount);

        $code = ($result['code'] ?? null) === 403 ? 403 : ($result['success'] ? 200 : 400);
        $this->json([
            'success' => $result['success'],
            'message' => $result['message'],
            'data'    => $result['data'],
        ], $code);
    }

    /**
     * POST /user/budgets/items/{id}/delete  (AJAX)
     * Delete a custom budget item.
     */
    public function deleteItem(string $id): void
    {
        if (!$this->verifyCsrf()) {
            $this->error('Invalid CSRF token.', 400);
            return;
        }

        $userId = Auth::id();
        $result = $this->budgetService->deleteItem((int) $id, $userId);

        $code = ($result['code'] ?? null) === 403 ? 403 : ($result['success'] ? 200 : 400);
        $this->json([
            'success' => $result['success'],
            'message' => $result['message'],
            'data'    => $result['data'],
        ], $code);
    }

    /**
     * POST /user/budgets/items/{id}/actual  (AJAX)
     * Record the actual amount spent for a budget item.
     */
    public function trackActual(string $id): void
    {
        if (!$this->verifyCsrf()) {
            $this->error('Invalid CSRF token.', 400);
            return;
        }

        $userId = Auth::id();
        $amount = (float) $this->input('actual_amount', 0);

        $result = $this->budgetService->recordActual((int) $id, $userId, $amount);

        $code = ($result['code'] ?? null) === 403 ? 403 : ($result['success'] ? 200 : 400);
        $this->json([
            'success' => $result['success'],
            'message' => $result['message'],
            'data'    => $result['data'],
        ], $code);
    }

    /**
     * GET /user/budgets/{id}/pdf
     * Render the budget as a printable PDF view.
     */
    public function download(string $id): void
    {
        $userId = Auth::id();
        $budget = $this->budgetService->getBudgetWithItems((int) $id, $userId);

        if (!$budget) {
            $this->redirect('/user/budgets', ['error' => 'Budget not found or access denied.']);
            return;
        }

        // Render standalone PDF view (no layout)
        $this->view('user/budget-pdf', [
            'budget' => $budget,
        ]);
    }

    /**
     * POST /user/budgets/{id}/delete
     * Delete a budget and all its items.
     */
    public function delete(string $id): void
    {
        if (!$this->verifyCsrf()) {
            $this->back(['error' => 'Invalid CSRF token.']);
            return;
        }

        $userId = Auth::id();
        $result = $this->budgetService->deleteBudget((int) $id, $userId);

        if (!$result['success']) {
            $code = $result['code'] ?? null;
            if ($code === 403) {
                $this->redirect('/user/budgets', ['error' => 'Unauthorized action.']);
            } else {
                $this->back(['error' => $result['message']]);
            }
            return;
        }

        $this->redirect('/user/budgets', ['success' => $result['message']]);
    }
}
