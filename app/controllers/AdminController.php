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
use App\Models\Vendor;
use App\Models\Review;
use App\Services\EmbeddingService;
use App\Config\App;
use App\Config\Database;

class AdminController extends Controller
{
    private User $userModel;
    private PanditProfile $panditProfileModel;
    private Ritual $ritualModel;
    private AIRequest $aiRequestModel;
    private Assignment $assignmentModel;
    private Vendor $vendorModel;
    private Review $reviewModel;
    
    public function __construct()
    {
        parent::__construct();
        $this->userModel = new User();
        $this->panditProfileModel = new PanditProfile();
        $this->ritualModel = new Ritual();
        $this->aiRequestModel = new AIRequest();
        $this->assignmentModel = new Assignment();
        $this->vendorModel = new Vendor();
        $this->reviewModel = new Review();
    }

    /**
     * Admin Profile Page
     */
    public function profile(): void
    {
        $sessionUser = Auth::user();
        $user = $this->userModel->find($sessionUser['id']);
        
        if (!$user) {
            Auth::logout();
            $this->redirect('login');
            return;
        }
        
        $this->viewWithLayout('admin/profile', 'layouts/admin', [
            'title' => 'My Profile - Sanskar AI',
            'user' => $user
        ]);
    }

    /**
     * Update Admin Password
     */
    public function updatePassword(): void
    {
        if (!$this->verifyCsrf()) {
            $this->back(['error' => 'Invalid security token.']);
            return;
        }

        $user = Auth::user();
        $oldPassword = $this->input('old_password');
        $newPassword = $this->input('new_password');
        $confirmPassword = $this->input('confirm_password');

        $errors = [];

        if (empty($oldPassword)) $errors['old_password'] = 'Current password is required';
        if (empty($newPassword)) $errors['new_password'] = 'New password is required';
        if (strlen($newPassword) < 6) $errors['new_password'] = 'Password must be at least 6 characters';
        if ($newPassword !== $confirmPassword) $errors['confirm_password'] = 'Passwords do not match';

        if (!empty($errors)) {
            $this->back(['errors' => $errors]);
            return;
        }

        // Verify old password
        // Fetch fresh user data to get the password hash
        $currentUser = $this->userModel->find($user['id']);
        
        if (!$currentUser) {
            $this->back(['error' => 'User not found.']);
            return;
        }

        // FIX: The column name is 'password_hash', not 'password'
        $currentPasswordHash = $currentUser['password_hash'] ?? $currentUser['password'] ?? null;
        
        if (!$currentPasswordHash) {
             // If no password set, allow update without verification (or handle error)
             // For now, logging it
             error_log("No password hash found for user {$user['id']}");
        }

        // DEBUG: Log to file since we can't see terminal
        $logMsg = "--- Password Update Debug ---\n";
        $logMsg .= "Time: " . date('Y-m-d H:i:s') . "\n";
        $logMsg .= "User ID: {$user['id']}\n";
        $logMsg .= "Stored Hash (raw): '" . $currentPasswordHash . "'\n";
        $logMsg .= "Input Password: '" . $oldPassword . "'\n";
        $logMsg .= "password_verify: " . (password_verify($oldPassword, $currentPasswordHash) ? 'TRUE' : 'FALSE') . "\n";
        $logMsg .= "Plain Text Check (===): " . ($oldPassword === $currentPasswordHash ? 'MATCH' : 'NO MATCH') . "\n";
        
        file_put_contents(__DIR__ . '/../debug_pass.log', $logMsg, FILE_APPEND);
        
        $verified = password_verify($oldPassword, $currentPasswordHash);
        
        // Fallback: Check if stored password is plain text (legacy/seeded data)
        // Also trying trim() in case of database padding
        if (!$verified && ($oldPassword === $currentPasswordHash || $oldPassword === trim($currentPasswordHash))) {
            $verified = true;
        }
        
        if (!$verified) {
            $this->back(['errors' => ['old_password' => 'Incorrect current password']]);
            return;
        }

        // Hash new password
        $newPasswordHash = Auth::hashPassword($newPassword);
        
        // Update password using Model's update method
        // Note: User model uses 'password_hash' column
        $success = $this->userModel->update($user['id'], [
            'password_hash' => $newPasswordHash
        ]);

        if ($success) {
            $this->redirect('/admin/profile', ['success' => 'Password updated successfully.']);
        } else {
             $this->back(['error' => 'Failed to update password. Please try again.']);
        }
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
        $recentFeedbacks = Database::query("SELECT f.*, u.role, u.status as user_status FROM SAI_user_feedbacks f JOIN SAI_users u ON f.user_id = u.id ORDER BY f.created_at DESC LIMIT 3");
        
        $this->viewWithLayout('admin/dashboard', 'layouts/admin', [
            'title' => 'Admin Dashboard - Sanskar AI',
            'stats' => $stats,
            'pendingPandits' => $pendingPandits,
            'recentUsers' => $recentUsers,
            'recentAIRequests' => $recentAIRequests,
            'recentFeedbacks' => $recentFeedbacks,
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
        $search = trim($this->input('search') ?? '');
        
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
        $search = trim($this->input('search') ?? '');

        if ($search) {
            $rituals = $this->ritualModel->search($search);
        } else {
            $rituals = $this->ritualModel->all('name', 'ASC');
        }
        
        $this->viewWithLayout('admin/rituals', 'layouts/admin', [
            'title' => 'Ritual Management - Sanskar AI',
            'rituals' => $rituals,
            'search' => $search,
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
            'best_time', 'deity', 'community_name', 'religion', 'is_active', 'is_featured'
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
        
        // Auto-generate embedding for semantic search
        try {
            $embeddingService = new EmbeddingService();
            $embeddingService->generateAndStore($ritualId, $data['name'] ?? '', $data['community_name'] ?? null, $data['religion'] ?? null);
        } catch (\Exception $e) {
            error_log('Embedding generation failed for ritual #' . $ritualId . ': ' . $e->getMessage());
        }
        
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
            'best_time', 'deity', 'community_name', 'religion', 'is_active', 'is_featured'
        ]);
        
        $data['is_active'] = isset($data['is_active']) ? 1 : 0;
        $data['is_featured'] = isset($data['is_featured']) ? 1 : 0;
        
        $this->ritualModel->update($ritualId, $data);
        
        // Refresh embedding for semantic search
        try {
            $embeddingService = new EmbeddingService();
            $embeddingService->generateAndStore($ritualId, $data['name'] ?? '', $data['community_name'] ?? null, $data['religion'] ?? null);
        } catch (\Exception $e) {
            error_log('Embedding refresh failed for ritual #' . $ritualId . ': ' . $e->getMessage());
        }
        
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
        
        $ritualId = (int) $id;
        
        // Delete embedding first
        try {
            $embeddingService = new EmbeddingService();
            $embeddingService->deleteEmbedding($ritualId);
        } catch (\Exception $e) {
            error_log('Embedding delete failed for ritual #' . $ritualId . ': ' . $e->getMessage());
        }
        
        $this->ritualModel->delete($ritualId);
        
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
        $search = trim($this->input('search') ?? '');

        if ($search) {
            $requests = $this->aiRequestModel->search($search);
        } else {
            $requests = $this->aiRequestModel->getRecent(50);
        }
        
        $flagged = $this->aiRequestModel->getFlagged();
        $stats = $this->aiRequestModel->getStats();
        
        $this->viewWithLayout('admin/ai-logs', 'layouts/admin', [
            'title' => 'AI Logs - Sanskar AI',
            'requests' => $requests,
            'flagged' => $flagged,
            'stats' => $stats,
            'search' => $search,
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
            
            // Auto-generate embedding for semantic search
            try {
                $embeddingService = new EmbeddingService();
                $embeddingService->generateAndStore(
                    $globalRitualId,
                    $ritualData['name'] ?? '',
                    $criteria['community_name'] ?? null,
                    $criteria['religion'] ?? null
                );
            } catch (\Exception $e) {
                error_log('Embedding generation failed for AI ritual #' . $globalRitualId . ': ' . $e->getMessage());
            }
            
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

    // ============================================================
    // VENDOR MANAGEMENT
    // ============================================================

    /**
     * List all vendors
     */
    public function vendors(): void
    {
        $category = $this->input('category');
        $search = $this->input('search');
        
        $vendors = $this->vendorModel->getAllForAdmin($category, $search);
        
        $this->viewWithLayout('admin/vendors', 'layouts/admin', [
            'title' => 'Vendor Management - Sanskar AI',
            'vendors' => $vendors,
            'categories' => Vendor::CATEGORIES,
            'selectedCategory' => $category,
            'search' => $search,
        ]);
    }

    /**
     * Show create vendor form
     */
    public function createVendor(): void
    {
        $this->viewWithLayout('admin/vendor-form', 'layouts/admin', [
            'title' => 'Add Vendor - Sanskar AI',
            'vendor' => null,
            'categories' => Vendor::CATEGORIES,
            'isEdit' => false,
        ]);
    }

    /**
     * Store new vendor
     */
    public function storeVendor(): void
    {
        if (!$this->verifyCsrf()) {
            $this->back(['error' => 'Invalid security token. Please try again.']);
            return;
        }
        
        $data = $this->only([
            'name', 'category', 'description', 'contact_person',
            'email', 'phone', 'alternate_phone', 'whatsapp', 'website',
            'address_line1', 'address_line2', 'city', 'state', 'pincode', 'country',
            'latitude', 'longitude', 'map_url', 'service_area_km',
            'min_price', 'max_price', 'services_offered',
        ]);
        
        // Validate required fields
        $errors = $this->validate($data, [
            'name' => 'required|min:2|max:150',
            'category' => 'required',
            'phone' => 'required|min:10|max:20',
            'address_line1' => 'required|max:255',
            'city' => 'required|max:100',
            'state' => 'required|max:100',
            'pincode' => 'required|max:10',
            'latitude' => 'required',
            'longitude' => 'required',
        ]);
        
        if (!empty($errors)) {
            $this->back([
                'error' => 'Please correct the errors below.',
                'errors' => $errors,
                'old' => $data,
            ]);
            return;
        }
        
        try {
            // Process checkboxes
            $data['is_active'] = isset($_POST['is_active']) ? 1 : 0;
            $data['is_featured'] = isset($_POST['is_featured']) ? 1 : 0;
            $data['is_verified'] = isset($_POST['is_verified']) ? 1 : 0;
            
            // Set defaults
            $data['country'] = $data['country'] ?: 'India';
            $data['service_area_km'] = $data['service_area_km'] ?: 50;
            $data['map_url'] = $data['map_url'] ?: null;
            $data['added_by'] = Auth::user()['id'];
            
            // Convert prices to appropriate format
            $data['min_price'] = $data['min_price'] ? (float)$data['min_price'] : null;
            $data['max_price'] = $data['max_price'] ? (float)$data['max_price'] : null;
            
            $this->vendorModel->create($data);
            
            $this->redirect('/admin/vendors', [
                'success' => 'Vendor "' . $data['name'] . '" added successfully!',
            ]);
            
        } catch (\Exception $e) {
            $this->back([
                'error' => 'Failed to add vendor. ' . $e->getMessage(),
                'old' => $data,
            ]);
        }
    }

    /**
     * Show edit vendor form
     */
    public function editVendor(int $id): void
    {
        $vendor = $this->vendorModel->find($id);
        
        if (!$vendor) {
            $this->redirect('/admin/vendors', ['error' => 'Vendor not found.']);
            return;
        }
        
        $this->viewWithLayout('admin/vendor-form', 'layouts/admin', [
            'title' => 'Edit Vendor - Sanskar AI',
            'vendor' => $vendor,
            'categories' => Vendor::CATEGORIES,
            'isEdit' => true,
        ]);
    }

    /**
     * Update vendor
     */
    public function updateVendor(int $id): void
    {
        if (!$this->verifyCsrf()) {
            $this->back(['error' => 'Invalid security token. Please try again.']);
            return;
        }
        
        $vendor = $this->vendorModel->find($id);
        
        if (!$vendor) {
            $this->redirect('/admin/vendors', ['error' => 'Vendor not found.']);
            return;
        }
        
        $data = $this->only([
            'name', 'category', 'description', 'contact_person',
            'email', 'phone', 'alternate_phone', 'whatsapp', 'website',
            'address_line1', 'address_line2', 'city', 'state', 'pincode', 'country',
            'latitude', 'longitude', 'map_url', 'service_area_km',
            'min_price', 'max_price', 'services_offered',
        ]);
        
        // Validate required fields
        $errors = $this->validate($data, [
            'name' => 'required|min:2|max:150',
            'category' => 'required',
            'phone' => 'required|min:10|max:20',
            'address_line1' => 'required|max:255',
            'city' => 'required|max:100',
            'state' => 'required|max:100',
            'pincode' => 'required|max:10',
            'latitude' => 'required',
            'longitude' => 'required',
        ]);
        
        if (!empty($errors)) {
            $this->back([
                'error' => 'Please correct the errors below.',
                'errors' => $errors,
                'old' => $data,
            ]);
            return;
        }
        
        try {
            // Process checkboxes
            $data['is_active'] = isset($_POST['is_active']) ? 1 : 0;
            $data['is_featured'] = isset($_POST['is_featured']) ? 1 : 0;
            $data['is_verified'] = isset($_POST['is_verified']) ? 1 : 0;
            
            // Set defaults
            $data['country'] = $data['country'] ?: 'India';
            $data['service_area_km'] = $data['service_area_km'] ?: 50;
            $data['map_url'] = $data['map_url'] ?: null;
            
            // Convert prices to appropriate format
            $data['min_price'] = $data['min_price'] ? (float)$data['min_price'] : null;
            $data['max_price'] = $data['max_price'] ? (float)$data['max_price'] : null;
            
            $this->vendorModel->update($id, $data);
            
            $this->redirect('/admin/vendors', [
                'success' => 'Vendor "' . $data['name'] . '" updated successfully!',
            ]);
            
        } catch (\Exception $e) {
            $this->back([
                'error' => 'Failed to update vendor. ' . $e->getMessage(),
                'old' => $data,
            ]);
        }
    }

    /**
     * Delete vendor
     */
    public function deleteVendor(int $id): void
    {
        if (!$this->verifyCsrf()) {
            $this->back(['error' => 'Invalid security token.']);
            return;
        }
        
        $vendor = $this->vendorModel->find($id);
        
        if (!$vendor) {
            $this->redirect('/admin/vendors', ['error' => 'Vendor not found.']);
            return;
        }
        
        try {
            $this->vendorModel->delete($id);
            $this->redirect('/admin/vendors', [
                'success' => 'Vendor "' . $vendor['name'] . '" deleted successfully.',
            ]);
        } catch (\Exception $e) {
            $this->back(['error' => 'Failed to delete vendor.']);
        }
    }

    /**
     * Toggle vendor status (active/inactive)
     */
    public function toggleVendorStatus(int $id): void
    {
        if (!$this->verifyCsrf()) {
            $this->back(['error' => 'Invalid security token.']);
            return;
        }
        
        $vendor = $this->vendorModel->find($id);
        
        if (!$vendor) {
            $this->redirect('/admin/vendors', ['error' => 'Vendor not found.']);
            return;
        }
        
        $this->vendorModel->toggleStatus($id);
        $newStatus = $vendor['is_active'] ? 'deactivated' : 'activated';
        
        $this->redirect('/admin/vendors', [
            'success' => 'Vendor "' . $vendor['name'] . '" has been ' . $newStatus . '.',
        ]);
    }

    /**
     * Toggle vendor featured status
     */
    public function toggleVendorFeatured(int $id): void
    {
        if (!$this->verifyCsrf()) {
            $this->back(['error' => 'Invalid security token.']);
            return;
        }
        
        $vendor = $this->vendorModel->find($id);
        
        if (!$vendor) {
            $this->redirect('/admin/vendors', ['error' => 'Vendor not found.']);
            return;
        }
        
        $this->vendorModel->toggleFeatured($id);
        $newStatus = $vendor['is_featured'] ? 'removed from featured' : 'marked as featured';
        
        $this->redirect('/admin/vendors', [
            'success' => 'Vendor "' . $vendor['name'] . '" has been ' . $newStatus . '.',
        ]);
    }

    // =========================================================================
    // REVIEW MANAGEMENT
    // =========================================================================

    /**
     * List all reviews with filters
     */
    public function reviews(): void
    {
        $page = (int)$this->input('page', 1);
        $status = $this->input('status');
        $targetType = $this->input('target_type');
        $aiFlagged = $this->input('ai_flagged');

        $filters = [];
        if ($status) $filters['status'] = $status;
        if ($targetType) $filters['target_type'] = $targetType;
        if ($aiFlagged) $filters['ai_flag'] = 1;

        $perPage = 20;
        $offset = ($page - 1) * $perPage;

        $reviews = $this->reviewModel->getAdminReviews($filters, $perPage, $offset);
        $stats = $this->reviewModel->getStats();

        $this->viewWithLayout('admin/reviews', 'layouts/admin', [
            'title' => 'Review Management - Sanskar AI',
            'reviews' => $reviews,
            'stats' => $stats,
            'filters' => [
                'status' => $status,
                'target_type' => $targetType,
                'ai_flagged' => $aiFlagged,
            ],
            'page' => $page,
        ]);
    }

    /**
     * View single review details
     */
    public function viewReview(int $id): void
    {
        $review = $this->reviewModel->find($id);

        if (!$review) {
            $this->redirect('/admin/reviews', ['error' => 'Review not found.']);
            return;
        }

        // Get reviewer info
        $reviewer = $this->userModel->find($review['reviewer_id']);

        // Get target info
        $target = null;
        if ($review['target_type'] === 'pandit') {
            $target = $this->userModel->find($review['target_id']);
            $target['type'] = 'pandit';
            $target['profile'] = $this->panditProfileModel->getByUserId($review['target_id']);
        } else {
            $target = $this->vendorModel->find($review['target_id']);
            $target['type'] = 'vendor';
        }

        $this->viewWithLayout('admin/review-detail', 'layouts/admin', [
            'title' => 'Review Details - Sanskar AI',
            'review' => $review,
            'reviewer' => $reviewer,
            'target' => $target,
        ]);
    }

    /**
     * Approve a review
     */
    public function approveReview(int $id): void
    {
        if (!$this->verifyCsrf()) {
            $this->back(['error' => 'Invalid security token.']);
            return;
        }

        $review = $this->reviewModel->find($id);

        if (!$review) {
            $this->redirect('/admin/reviews', ['error' => 'Review not found.']);
            return;
        }

        $adminId = Auth::id();
        $success = $this->reviewModel->approveReview($id, $adminId);

        if ($success) {
            $this->redirect('/admin/reviews', ['success' => 'Review approved successfully.']);
        } else {
            $this->back(['error' => 'Failed to approve review.']);
        }
    }

    /**
     * Reject a review
     */
    public function rejectReview(int $id): void
    {
        if (!$this->verifyCsrf()) {
            $this->back(['error' => 'Invalid security token.']);
            return;
        }

        $review = $this->reviewModel->find($id);

        if (!$review) {
            $this->redirect('/admin/reviews', ['error' => 'Review not found.']);
            return;
        }

        $reason = $this->input('reason');
        $adminId = Auth::id();

        $success = $this->reviewModel->rejectReview($id, $adminId, $reason);

        if ($success) {
            $this->redirect('/admin/reviews', ['success' => 'Review rejected.']);
        } else {
            $this->back(['error' => 'Failed to reject review.']);
        }
    }

    /**
     * Delete a review permanently
     */
    public function deleteReview(int $id): void
    {
        if (!$this->verifyCsrf()) {
            $this->back(['error' => 'Invalid security token.']);
            return;
        }

        $review = $this->reviewModel->find($id);

        if (!$review) {
            $this->redirect('/admin/reviews', ['error' => 'Review not found.']);
            return;
        }

        // Store target info for recalculation
        $targetType = $review['target_type'];
        $targetId = $review['target_id'];

        $success = $this->reviewModel->delete($id);

        if ($success) {
            // Recalculate ratings after deletion
            $this->reviewModel->recalculateRatings($targetType, $targetId);
            $this->redirect('/admin/reviews', ['success' => 'Review deleted permanently.']);
        } else {
            $this->back(['error' => 'Failed to delete review.']);
        }
    }

    /**
     * Bulk approve reviews
     */
    public function bulkApproveReviews(): void
    {
        if (!$this->verifyCsrf()) {
            $this->json(['success' => false, 'error' => 'Invalid security token.'], 403);
            return;
        }

        $reviewIds = $this->input('review_ids');
        
        if (!is_array($reviewIds) || empty($reviewIds)) {
            $this->json(['success' => false, 'error' => 'No reviews selected.'], 400);
            return;
        }

        $adminId = Auth::id();
        $approved = 0;

        foreach ($reviewIds as $id) {
            if ($this->reviewModel->approveReview((int)$id, $adminId)) {
                $approved++;
            }
        }

        $this->json([
            'success' => true,
            'message' => "Approved $approved review(s).",
            'approved_count' => $approved,
        ]);
    }

    /**
     * Verify Pandit documents and add badge
     */
    public function verifyPanditDocuments(int $userId): void
    {
        if (!$this->verifyCsrf()) {
            $this->back(['error' => 'Invalid security token.']);
            return;
        }

        $profile = $this->panditProfileModel->getByUserId($userId);

        if (!$profile) {
            $this->redirect('/admin/pandits/pending', ['error' => 'Pandit profile not found.']);
            return;
        }

        // Update documents verified status
        $sql = "UPDATE SAI_pandit_profiles SET is_documents_verified = 1, updated_at = NOW() WHERE user_id = :user_id";
        Database::query($sql, ['user_id' => $userId]);

        // Recalculate badges
        $this->reviewModel->recalculateRatings('pandit', $userId);

        $this->back(['success' => 'Pandit documents verified. Verified badge added.']);
    }

    /**
     * Get review statistics for dashboard
     */
    public function reviewStats(): void
    {
        $stats = $this->reviewModel->getStats();
        
        $this->json([
            'success' => true,
            'stats' => $stats,
        ]);
    }

    /**
     * User Feedbacks List
     */
    public function feedbacks(): void
    {
        $feedbackModel = new \App\Models\UserFeedback();
        $feedbacks = $feedbackModel->getAllFeedbacks();

        $this->viewWithLayout('admin/feedbacks', 'layouts/admin', [
            'title' => 'User Feedback Logs - Sanskar AI',
            'feedbacks' => $feedbacks
        ]);
    }

    /**
     * Delete Feedback
     */
    public function deleteFeedback(string $id): void
    {
        if (!$this->verifyCsrf()) {
            $this->back(['error' => 'Invalid security token.']);
            return;
        }

        $feedbackModel = new \App\Models\UserFeedback();
        $feedbackModel->delete((int) $id);

        $this->back(['success' => 'Feedback deleted successfully.']);
    }

    /**
     * Export Feedbacks as CSV
     */
    public function exportFeedbacks(): void
    {
        $feedbackModel = new \App\Models\UserFeedback();
        $feedbacks = $feedbackModel->getAllFeedbacks();

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="feedbacks_'.date('Ymd').'.csv"');

        $out = fopen('php://output', 'w');
        // Add BOM for Excel UTF-8 compatibility
        fputs($out, $bom =(chr(0xEF) . chr(0xBB) . chr(0xBF)));
        fputcsv($out, ['ID', 'User ID', 'Name', 'Email', 'Phone', 'Community', 'Features Checked', 'Likes', 'Improvements', 'Date']);

        foreach ($feedbacks as $f) {
            $featuresStr = '';
            if (!empty($f['features_feedback'])) {
                $features = is_string($f['features_feedback']) ? json_decode($f['features_feedback'], true) : $f['features_feedback'];
                if (is_array($features)) {
                    $featuresStr = implode(', ', array_keys($features));
                }
            }
            
            fputcsv($out, [
                $f['id'],
                $f['user_id'],
                $f['name'],
                $f['email'],
                $f['phone'],
                $f['community_name'],
                $featuresStr,
                $f['likes_about'],
                $f['improvements_for'],
                $f['created_at']
            ]);
        }

        fclose($out);
        exit;
    }
}
