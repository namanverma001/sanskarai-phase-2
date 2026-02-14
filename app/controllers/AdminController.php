<?php
/**
 * Sanskar AI - Admin Controller
 * ===============================
 * Dashboard, user management, pandit approval, ritual CRUD
 */

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\User;
use App\Models\PanditProfile;
use App\Models\Ritual;
use App\Models\AIRequest;
use App\Models\Assignment;
use App\Config\App;
use App\Config\Database;

class AdminController extends Controller
{
    private User $userModel;
    private PanditProfile $panditProfileModel;
    private Ritual $ritualModel;
    private AIRequest $aiRequestModel;
    private Assignment $assignmentModel;
    
    public function __construct()
    {
        parent::__construct();
        $this->userModel = new User();
        $this->panditProfileModel = new PanditProfile();
        $this->ritualModel = new Ritual();
        $this->aiRequestModel = new AIRequest();
        $this->assignmentModel = new Assignment();
    }
    
    /**
     * Admin Dashboard
     */
    public function dashboard(): void
    {
        $stats = [
            'users' => $this->userModel->getStats(),
            'rituals' => $this->ritualModel->getStats(),
            'ai' => $this->aiRequestModel->getStats(),
            'assignments' => $this->assignmentModel->getStats(),
        ];
        
        $pendingPandits = $this->userModel->getPendingPandits();
        $recentUsers = Database::query("SELECT * FROM SAI_users ORDER BY created_at DESC LIMIT 5");
        $recentAIRequests = $this->aiRequestModel->getRecent(5);
        
        $this->viewWithLayout('admin/dashboard', 'layouts/admin', [
            'title' => 'Admin Dashboard - Sanskar AI',
            'stats' => $stats,
            'pendingPandits' => $pendingPandits,
            'recentUsers' => $recentUsers,
            'recentAIRequests' => $recentAIRequests,
        ]);
    }
    
    /**
     * List all users
     */
    public function users(): void
    {
        $page = (int) $this->input('page', 1);
        $role = $this->input('role');
        $status = $this->input('status');
        $search = $this->input('search');
        
        $conditions = [];
        if ($role) $conditions['role'] = $role;
        if ($status) $conditions['status'] = $status;
        
        if ($search) {
            $users = $this->userModel->search($search, $role);
            $pagination = null;
        } else {
            $result = $this->userModel->paginate($page, 20, $conditions, 'created_at', 'DESC');
            $users = $result['data'];
            $pagination = $result;
        }
        
        $this->viewWithLayout('admin/users', 'layouts/admin', [
            'title' => 'User Management - Sanskar AI',
            'users' => $users,
            'pagination' => $pagination,
            'filters' => compact('role', 'status', 'search'),
        ]);
    }
    
    /**
     * Block user
     */
    public function blockUser(string $id): void
    {
        if (!$this->verifyCsrf()) {
            $this->back(['error' => 'Invalid security token.']);
            return;
        }
        
        $userId = (int) $id;
        
        // Cannot block yourself
        if ($userId === Auth::id()) {
            $this->back(['error' => 'You cannot block yourself.']);
            return;
        }
        
        $this->userModel->block($userId);
        $this->back(['success' => 'User has been blocked.']);
    }
    
    /**
     * Activate user
     */
    public function activateUser(string $id): void
    {
        if (!$this->verifyCsrf()) {
            $this->back(['error' => 'Invalid security token.']);
            return;
        }
        
        $this->userModel->activate((int) $id);
        $this->back(['success' => 'User has been activated.']);
    }
    
    /**
     * List pending pandits
     */
    public function pendingPandits(): void
    {
        $pendingPandits = $this->userModel->getPendingPandits();
        
        $this->viewWithLayout('admin/pending-pandits', 'layouts/admin', [
            'title' => 'Pending Pandit Approvals - Sanskar AI',
            'pandits' => $pendingPandits,
        ]);
    }
    
    /**
     * Approve pandit
     */
    public function approvePandit(string $id): void
    {
        if (!$this->verifyCsrf()) {
            $this->back(['error' => 'Invalid security token.']);
            return;
        }
        
        $profile = $this->panditProfileModel->getByUserId((int) $id);
        
        if (!$profile) {
            $this->back(['error' => 'Pandit profile not found.']);
            return;
        }
        
        $this->panditProfileModel->approve($profile['id'], Auth::id());
        $this->back(['success' => 'Pandit has been approved successfully.']);
    }
    
    /**
     * Reject pandit
     */
    public function rejectPandit(string $id): void
    {
        if (!$this->verifyCsrf()) {
            $this->back(['error' => 'Invalid security token.']);
            return;
        }
        
        $reason = $this->input('reason', 'Application rejected by admin.');
        $profile = $this->panditProfileModel->getByUserId((int) $id);
        
        if (!$profile) {
            $this->back(['error' => 'Pandit profile not found.']);
            return;
        }
        
        $this->panditProfileModel->reject($profile['id'], $reason);
        $this->back(['success' => 'Pandit application has been rejected.']);
    }
    
    /**
     * List all rituals
     */
    public function rituals(): void
    {
        $rituals = $this->ritualModel->all('name', 'ASC');
        
        $this->viewWithLayout('admin/rituals', 'layouts/admin', [
            'title' => 'Ritual Management - Sanskar AI',
            'rituals' => $rituals,
        ]);
    }
    
    /**
     * Show create ritual form
     */
    public function createRitual(): void
    {
        $categories = $this->ritualModel->getCategories();
        
        $this->viewWithLayout('admin/ritual-form', 'layouts/admin', [
            'title' => 'Create Ritual - Sanskar AI',
            'ritual' => null,
            'categories' => $categories,
            'isEdit' => false,
        ]);
    }
    
    /**
     * Store new ritual
     */
    public function storeRitual(): void
    {
        if (!$this->verifyCsrf()) {
            $this->back(['error' => 'Invalid security token.']);
            return;
        }
        
        $data = $this->only([
            'name', 'name_sanskrit', 'category', 'sub_category', 'description',
            'significance', 'duration_minutes', 'difficulty', 'occasion_type',
            'best_time', 'deity', 'is_active', 'is_featured'
        ]);
        
        $errors = $this->validate($data, [
            'name' => 'required|min:2|max:150',
            'category' => 'required|max:100',
            'duration_minutes' => 'required|numeric',
            'difficulty' => 'required|in:easy,medium,hard',
        ]);
        
        if (!empty($errors)) {
            $this->back(['error' => 'Please correct the errors.', 'errors' => $errors, 'old' => $data]);
            return;
        }
        
        $data['is_active'] = isset($data['is_active']) ? 1 : 0;
        $data['is_featured'] = isset($data['is_featured']) ? 1 : 0;
        $data['created_by'] = Auth::id();
        
        $ritualId = $this->ritualModel->create($data);
        
        $this->redirect('/admin/rituals/' . $ritualId . '/edit', [
            'success' => 'Ritual created successfully. You can now add steps and items.',
        ]);
    }
    
    /**
     * Show edit ritual form
     */
    public function editRitual(string $id): void
    {
        $ritual = $this->ritualModel->getWithDetails((int) $id);
        
        if (!$ritual) {
            $this->redirect('/admin/rituals', ['error' => 'Ritual not found.']);
            return;
        }
        
        $categories = $this->ritualModel->getCategories();
        
        $this->viewWithLayout('admin/ritual-form', 'layouts/admin', [
            'title' => 'Edit Ritual - Sanskar AI',
            'ritual' => $ritual,
            'categories' => $categories,
            'isEdit' => true,
        ]);
    }
    
    /**
     * Update ritual
     */
    public function updateRitual(string $id): void
    {
        if (!$this->verifyCsrf()) {
            $this->back(['error' => 'Invalid security token.']);
            return;
        }
        
        $ritualId = (int) $id;
        
        $data = $this->only([
            'name', 'name_sanskrit', 'category', 'sub_category', 'description',
            'significance', 'duration_minutes', 'difficulty', 'occasion_type',
            'best_time', 'deity', 'is_active', 'is_featured'
        ]);
        
        $data['is_active'] = isset($data['is_active']) ? 1 : 0;
        $data['is_featured'] = isset($data['is_featured']) ? 1 : 0;
        
        $this->ritualModel->update($ritualId, $data);
        
        $this->back(['success' => 'Ritual updated successfully.']);
    }
    
    /**
     * Delete ritual
     */
    public function deleteRitual(string $id): void
    {
        if (!$this->verifyCsrf()) {
            $this->back(['error' => 'Invalid security token.']);
            return;
        }
        
        $this->ritualModel->delete((int) $id);
        
        $this->redirect('/admin/rituals', ['success' => 'Ritual deleted successfully.']);
    }
    
    /**
     * Assign pandit to ritual
     */
    public function assignPandit(): void
    {
        $rituals = $this->ritualModel->getActive();
        $pandits = $this->userModel->getApprovedPandits();
        
        $this->viewWithLayout('admin/assign-pandit', 'layouts/admin', [
            'title' => 'Assign Pandit - Sanskar AI',
            'rituals' => $rituals,
            'pandits' => $pandits,
        ]);
    }
    
    /**
     * View AI logs
     */
    public function aiLogs(): void
    {
        $requests = $this->aiRequestModel->getRecent(50);
        $flagged = $this->aiRequestModel->getFlagged();
        $stats = $this->aiRequestModel->getStats();
        
        $this->viewWithLayout('admin/ai-logs', 'layouts/admin', [
            'title' => 'AI Logs - Sanskar AI',
            'requests' => $requests,
            'flagged' => $flagged,
            'stats' => $stats,
        ]);
    }
    
    /**
     * Flag AI request
     */
    public function flagAIRequest(string $id): void
    {
        if (!$this->verifyCsrf()) {
            $this->back(['error' => 'Invalid security token.']);
            return;
        }
        
        $reason = $this->input('reason', 'Flagged by admin for review.');
        $this->aiRequestModel->flag((int) $id, $reason);
        
        $this->back(['success' => 'AI request has been flagged.']);
    }
    
    /**
     * Unflag AI request
     */
    public function unflagAIRequest(string $id): void
    {
        if (!$this->verifyCsrf()) {
            $this->back(['error' => 'Invalid security token.']);
            return;
        }
        
        $this->aiRequestModel->unflag((int) $id);
        
        $this->back(['success' => 'AI request has been unflagged.']);
    }
    
    /**
     * Delete user
     */
    public function deleteUser(string $id): void
    {
        if (!$this->verifyCsrf()) {
            $this->back(['error' => 'Invalid security token.']);
            return;
        }
        
        $userId = (int) $id;
        
        // Cannot delete yourself
        if ($userId === Auth::id()) {
            $this->back(['error' => 'You cannot delete yourself.']);
            return;
        }
        
        // Check if user exists
        $user = $this->userModel->find($userId);
        if (!$user) {
            $this->back(['error' => 'User not found.']);
            return;
        }
        
        // Cannot delete other admins
        if ($user['role'] === 'admin') {
            $this->back(['error' => 'You cannot delete other administrators.']);
            return;
        }
        
        $this->userModel->delete($userId);
        $this->back(['success' => 'User has been deleted permanently.']);
    }
    
    /**
     * Add ritual step
     */
    public function addRitualStep(string $id): void
    {
        if (!$this->verifyCsrf()) {
            $this->back(['error' => 'Invalid security token.']);
            return;
        }
        
        $ritualId = (int) $id;
        
        // Define default values for all fields to ensure all placeholders have values
        $defaults = [
            'step_number' => 1,
            'title' => '',
            'title_sanskrit' => null,
            'description' => null,
            'mantra' => null,
            'mantra_meaning' => null,
            'duration_minutes' => 5,
            'is_optional' => 0,
            'special_instructions' => null
        ];
        
        $data = array_merge($defaults, $this->only(array_keys($defaults)));
        
        $data['ritual_id'] = $ritualId;
        $data['is_optional'] = isset($_POST['is_optional']) ? 1 : 0;
        $data['duration_minutes'] = (int) ($data['duration_minutes'] ?: 5);
        $data['step_number'] = (int) ($data['step_number'] ?: 1);
        
        Database::execute(
            "INSERT INTO SAI_ritual_steps (ritual_id, step_number, title, title_sanskrit, description, mantra, mantra_meaning, duration_minutes, is_optional, special_instructions, created_at) 
             VALUES (:ritual_id, :step_number, :title, :title_sanskrit, :description, :mantra, :mantra_meaning, :duration_minutes, :is_optional, :special_instructions, NOW())",
            $data
        );
        
        $this->back(['success' => 'Ritual step added successfully.']);
    }
    
    /**
     * Delete ritual step
     */
    public function deleteRitualStep(string $id): void
    {
        if (!$this->verifyCsrf()) {
            $this->back(['error' => 'Invalid security token.']);
            return;
        }
        
        Database::execute("DELETE FROM SAI_ritual_steps WHERE id = :id", ['id' => (int) $id]);
        
        $this->back(['success' => 'Ritual step deleted successfully.']);
    }
    
    /**
     * Update ritual step
     */
    public function updateRitualStep(string $id): void
    {
        if (!$this->verifyCsrf()) {
            $this->back(['error' => 'Invalid security token.']);
            return;
        }
        
        $stepId = (int) $id;
        
        $defaults = [
            'step_number' => 1,
            'title' => '',
            'title_sanskrit' => null,
            'description' => null,
            'mantra' => null,
            'mantra_meaning' => null,
            'duration_minutes' => 5,
            'is_optional' => 0,
            'special_instructions' => null
        ];
        
        $data = array_merge($defaults, $this->only(array_keys($defaults)));
        $data['id'] = $stepId;
        $data['is_optional'] = isset($_POST['is_optional']) ? 1 : 0;
        $data['duration_minutes'] = (int) ($data['duration_minutes'] ?: 5);
        $data['step_number'] = (int) ($data['step_number'] ?: 1);
        
        Database::execute(
            "UPDATE SAI_ritual_steps SET 
                step_number = :step_number, title = :title, title_sanskrit = :title_sanskrit, 
                description = :description, mantra = :mantra, mantra_meaning = :mantra_meaning, 
                duration_minutes = :duration_minutes, is_optional = :is_optional, 
                special_instructions = :special_instructions
             WHERE id = :id",
            $data
        );
        
        $this->back(['success' => 'Ritual step updated successfully.']);
    }
    
    /**
     * Add ritual item
     */
    public function addRitualItem(string $id): void
    {
        if (!$this->verifyCsrf()) {
            $this->back(['error' => 'Invalid security token.']);
            return;
        }
        
        $ritualId = (int) $id;
        
        // Define default values for all fields to ensure all placeholders have values
        $defaults = [
            'item_name' => '',
            'item_name_local' => null,
            'quantity' => 1,
            'unit' => null,
            'is_mandatory' => 1,
            'approximate_cost' => null,
            'category' => null,
            'description' => null
        ];
        
        $data = array_merge($defaults, $this->only(array_keys($defaults)));
        
        $data['ritual_id'] = $ritualId;
        $data['is_mandatory'] = isset($_POST['is_mandatory']) ? 1 : 0;
        $data['quantity'] = (float) ($data['quantity'] ?: 1);
        
        Database::execute(
            "INSERT INTO SAI_ritual_items (ritual_id, item_name, item_name_local, quantity, unit, is_mandatory, approximate_cost, category, description, created_at) 
             VALUES (:ritual_id, :item_name, :item_name_local, :quantity, :unit, :is_mandatory, :approximate_cost, :category, :description, NOW())",
            $data
        );
        
        $this->back(['success' => 'Ritual item added successfully.']);
    }
    
    /**
     * Delete ritual item
     */
    public function deleteRitualItem(string $id): void
    {
        if (!$this->verifyCsrf()) {
            $this->back(['error' => 'Invalid security token.']);
            return;
        }
        
        Database::execute("DELETE FROM SAI_ritual_items WHERE id = :id", ['id' => (int) $id]);
        
        $this->back(['success' => 'Ritual item deleted successfully.']);
    }
    
    /**
     * Update ritual item
     */
    public function updateRitualItem(string $id): void
    {
        if (!$this->verifyCsrf()) {
            $this->back(['error' => 'Invalid security token.']);
            return;
        }
        
        $itemId = (int) $id;
        
        $defaults = [
            'item_name' => '',
            'item_name_local' => null,
            'quantity' => 1,
            'unit' => null,
            'is_mandatory' => 1,
            'approximate_cost' => null,
            'category' => null,
            'description' => null
        ];
        
        $data = array_merge($defaults, $this->only(array_keys($defaults)));
        $data['id'] = $itemId;
        $data['is_mandatory'] = isset($_POST['is_mandatory']) ? 1 : 0;
        $data['quantity'] = (float) ($data['quantity'] ?: 1);
        
        Database::execute(
            "UPDATE SAI_ritual_items SET 
                item_name = :item_name, item_name_local = :item_name_local, 
                quantity = :quantity, unit = :unit, is_mandatory = :is_mandatory, 
                approximate_cost = :approximate_cost, category = :category, description = :description
             WHERE id = :id",
            $data
        );
        
        $this->back(['success' => 'Ritual item updated successfully.']);
    }
    
    /**
     * Show AI ritual generation form (Admin only)
     */
    public function generateRitualForm(): void
    {
        $categories = $this->ritualModel->getCategories();
        
        $this->viewWithLayout('admin/generate-ritual', 'layouts/admin', [
            'title' => 'AI Ritual Generator - Sanskar AI',
            'categories' => $categories,
        ]);
    }
    
    /**
     * Generate ritual using AI and save to global database (Admin only)
     */
    public function generateRitual(): void
    {
        if (!$this->verifyCsrf()) {
            $this->back(['error' => 'Invalid security token.']);
            return;
        }
        
        $criteria = [
            'community_name' => $this->input('community_name', ''),
            'religion' => $this->input('religion', 'Hinduism'),
            'ritual_name' => $this->input('ritual_name', ''),
            'occasion' => $this->input('occasion', ''),
            'additional_info' => $this->input('additional_info', ''),
        ];
        
        if (empty($criteria['ritual_name'])) {
            $this->back(['error' => 'Ritual name is required.', 'old' => $criteria]);
            return;
        }
        
        try {
            // Initialize AI Service
            $aiService = new \App\Services\AIService();
            
            // Generate ritual using AI
            $result = $aiService->generateRitual(Auth::id(), $criteria);
            
            if (!$result['success']) {
                $this->back(['error' => $result['error'] ?? 'AI generation failed.', 'old' => $criteria]);
                return;
            }
            
            $ritualData = $result['ritual'];
            
            // Save to global database
            $globalRitualId = $this->ritualModel->saveFromAI($ritualData, Auth::id());
            
            // Redirect to edit page so admin can review and modify
            $this->redirect('/admin/rituals/' . $globalRitualId . '/edit', [
                'success' => 'AI-generated ritual saved successfully! Review and edit the details below.',
            ]);
            
        } catch (\Exception $e) {
            $this->back(['error' => 'Error generating ritual: ' . $e->getMessage(), 'old' => $criteria]);
        }
    }
    
    /**
     * Show create admin form
     */
    public function showCreateAdmin(): void
    {
        $admins = $this->userModel->getAdmins();
        
        $this->viewWithLayout('admin/create-admin', 'layouts/admin', [
            'title' => 'Create Admin Account - Sanskar AI',
            'admins' => $admins,
        ]);
    }
    
    /**
     * Store new admin account
     */
    public function storeAdmin(): void
    {
        if (!$this->verifyCsrf()) {
            $this->back(['error' => 'Invalid security token. Please try again.']);
            return;
        }
        
        $data = $this->only(['name', 'email', 'mobile', 'password', 'password_confirmation', 'secret_key']);
        
        // Validate inputs
        $errors = $this->validate($data, [
            'name' => 'required|min:2|max:100',
            'email' => 'required|email|max:150',
            'mobile' => 'required|min:10|max:15',
            'password' => 'required|min:6|confirmed',
            'secret_key' => 'required',
        ]);
        
        // Verify secret key
        $validKey = App::env('ADMIN_CREATION_KEY', '');
        if (empty($validKey) || $data['secret_key'] !== $validKey) {
            $errors['secret_key'][] = 'Invalid admin creation key.';
        }
        
        // Check if email already exists
        if ($this->userModel->findByEmail($data['email'])) {
            $errors['email'][] = 'This email is already registered.';
        }
        
        // Check if mobile already exists
        if ($this->userModel->findByMobile($data['mobile'])) {
            $errors['mobile'][] = 'This mobile number is already registered.';
        }
        
        if (!empty($errors)) {
            $this->back([
                'error' => 'Please correct the errors below.',
                'errors' => $errors,
                'old' => $data,
            ]);
            return;
        }
        
        try {
            $this->userModel->createUser([
                'name' => $data['name'],
                'email' => $data['email'],
                'mobile' => $data['mobile'],
                'password' => $data['password'],
                'role' => App::ROLE_ADMIN,
                'status' => App::STATUS_ACTIVE,
            ]);
            
            $this->redirect('/admin/create-admin', [
                'success' => 'Admin account created successfully for ' . $data['name'] . '!',
            ]);
            
        } catch (\Exception $e) {
            $this->back([
                'error' => 'Failed to create admin account. Please try again.',
                'old' => $data,
            ]);
        }
    }
}
