<?php
/**
 * Sanskar AI - User Controller
 * ==============================
 * Family management, rituals, custom rituals, shopping list
 * Enhanced with AI ritual generation and My Rituals features
 */

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\Family;
use App\Models\Ritual;
use App\Models\CustomRitual;
use App\Models\Assignment;
use App\Models\ShoppingList;
use App\Models\CulturalInsight;
use App\Models\User;
use App\Models\UserRitual;
use App\Models\Order;
use App\Services\AIService;
use App\Config\Database;

class UserController extends Controller
{
    private Family $familyModel;
    private Ritual $ritualModel;
    private CustomRitual $customRitualModel;
    private Assignment $assignmentModel;
    private ShoppingList $shoppingListModel;
    private CulturalInsight $insightModel;
    private UserRitual $userRitualModel;
    private Order $orderModel;
    private AIService $aiService;

    public function __construct()
    {
        parent::__construct();
        $this->familyModel = new Family();
        $this->ritualModel = new Ritual();
        $this->customRitualModel = new CustomRitual();
        $this->assignmentModel = new Assignment();
        $this->shoppingListModel = new ShoppingList();
        $this->insightModel = new CulturalInsight();
        $this->userRitualModel = new UserRitual();
        $this->orderModel = new Order();
        $this->aiService = new AIService();
    }

    public function dashboard(): void
    {
        $userId = Auth::id();
        $family = $this->familyModel->getPrimaryFamily($userId);
        $assignments = $this->assignmentModel->getForUser($userId);
        $shoppingSummary = $this->shoppingListModel->getSummary($userId);
        $featuredRituals = $this->ritualModel->getFeatured();
        $featuredInsights = $this->insightModel->getFeatured(3);
        $myRituals = $this->userRitualModel->getByUser($userId);

        $this->viewWithLayout('user/dashboard', 'layouts/user', [
            'title' => 'Dashboard - Sanskar AI',
            'family' => $family,
            'assignments' => array_slice($assignments, 0, 5),
            'shoppingSummary' => $shoppingSummary,
            'featuredRituals' => $featuredRituals,
            'featuredInsights' => $featuredInsights,
            'myRituals' => array_slice($myRituals, 0, 3),
        ]);
    }

    // Family Management
    public function families(): void
    {
        $families = $this->familyModel->getByUserId(Auth::id());
        $this->viewWithLayout('user/families', 'layouts/user', [
            'title' => 'My Families',
            'families' => $families,
        ]);
    }

    public function createFamily(): void
    {
        $this->viewWithLayout('user/family-form', 'layouts/user', [
            'title' => 'Create Family',
            'family' => null,
        ]);
    }

    public function storeFamily(): void
    {
        if (!$this->verifyCsrf()) {
            $this->back(['error' => 'Invalid token.']);
            return;
        }
        $data = $this->only(['family_name', 'gotra', 'nakshatra', 'kul_devta', 'city', 'state']);
        $data['user_id'] = Auth::id();
        $this->familyModel->create($data);
        $this->redirect('/user/families', ['success' => 'Family created.']);
    }

    public function editFamily(string $id): void
    {
        $family = $this->familyModel->getWithMembers((int) $id);
        if (!$family || !$this->familyModel->belongsToUser((int) $id, Auth::id())) {
            $this->redirect('/user/families', ['error' => 'Not found.']);
            return;
        }
        $this->viewWithLayout('user/family-form', 'layouts/user', [
            'title' => 'Edit Family',
            'family' => $family,
        ]);
    }

    public function updateFamily(string $id): void
    {
        if (!$this->verifyCsrf()) {
            $this->back(['error' => 'Invalid token.']);
            return;
        }
        $data = $this->only(['family_name', 'gotra', 'nakshatra', 'kul_devta', 'city', 'state']);
        $this->familyModel->update((int) $id, $data);
        $this->back(['success' => 'Family updated.']);
    }

    public function addFamilyMember(string $familyId): void
    {
        if (!$this->verifyCsrf()) {
            $this->back(['error' => 'Invalid token.']);
            return;
        }
        $data = $this->only(['name', 'date_of_birth', 'gender', 'relation']);
        $this->familyModel->addMember((int) $familyId, $data);
        $this->back(['success' => 'Member added.']);
    }

    public function deleteFamilyMember(string $id): void
    {
        if (!$this->verifyCsrf()) {
            $this->back(['error' => 'Invalid token.']);
            return;
        }
        $this->familyModel->deleteMember((int) $id);
        $this->back(['success' => 'Member removed.']);
    }

    /**
     * Delete family
     */
    public function deleteFamily(string $id): void
    {
        if (!$this->verifyCsrf()) {
            $this->back(['error' => 'Invalid token.']);
            return;
        }
        
        if (!$this->familyModel->belongsToUser((int) $id, Auth::id())) {
            $this->redirect('/user/families', ['error' => 'Unauthorized action.']);
            return;
        }
        
        $this->familyModel->delete((int) $id);
        $this->redirect('/user/families', ['success' => 'Family deleted successfully.']);
    }

    // ============================================================
    // EXPLORE RITUALS - Enhanced with AI Generation + Community Filtering
    // ============================================================

    public function rituals(): void
    {
        $categories = $this->ritualModel->getCategories();
        
        // Get current user's community for filtering
        $userId = Auth::id();
        $userModel = new User();
        $user = $userModel->find($userId);
        $userCommunity = trim($user['community_name'] ?? '');
        
        // Check if user wants to see all rituals (bypass community filter)
        $showAll = $this->input('all') === '1';
        
        if (!empty($userCommunity) && !$showAll) {
            // Filter by user's community (fuzzy Levenshtein match)
            $popularRituals = $this->ritualModel->getActiveForCommunity($userCommunity, 6, 0);
            $totalRitualCount = $this->ritualModel->countForCommunity($userCommunity);
        } else {
            // No community set or user wants all — show all rituals
            $popularRituals = $this->ritualModel->getPopular(6);
            $totalRitualCount = $this->ritualModel->count(['is_active' => 1]);
        }

        $this->viewWithLayout('user/explore-rituals', 'layouts/user', [
            'title' => 'Explore Rituals',
            'categories' => $categories,
            'popularRituals' => $popularRituals,
            'totalRitualCount' => $totalRitualCount,
            'userCommunity' => $userCommunity,
        ]);
    }

    /**
     * Load more rituals via AJAX for pagination
     */
    public function loadMoreRituals(): void
    {
        ob_start();
        error_reporting(0);
        ini_set('display_errors', '0');

        try {
            if (!$this->verifyCsrf()) {
                ob_end_clean();
                $this->json(['success' => false, 'error' => 'Invalid token.'], 400);
                return;
            }

            $offset = (int) $this->input('offset', 0);
            $limit = (int) $this->input('limit', 6);
            
            // Cap limit to prevent abuse
            $limit = min($limit, 20);

            // Get current user's community for filtering
            $userId = Auth::id();
            $userModel = new User();
            $user = $userModel->find($userId);
            $userCommunity = trim($user['community_name'] ?? '');
            $showAll = $this->input('show_all') === '1';

            if (!empty($userCommunity) && !$showAll) {
                $rituals = $this->ritualModel->getActiveForCommunity($userCommunity, $limit, $offset);
                $totalCount = $this->ritualModel->countForCommunity($userCommunity);
            } else {
                $rituals = $this->ritualModel->getActiveRituals($limit, $offset);
                $totalCount = $this->ritualModel->count(['is_active' => 1]);
            }

            ob_end_clean();
            $this->json([
                'success' => true,
                'rituals' => $rituals,
                'total' => $totalCount,
                'offset' => $offset,
                'limit' => $limit,
            ]);
        } catch (\Exception $e) {
            ob_end_clean();
            $this->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Search rituals in global database
     * Auto-injects user's community if not manually specified
     */
    public function searchRituals(): void
    {
        ob_start();
        error_reporting(0);
        ini_set('display_errors', '0');

        try {
            $communityInput = $this->input('community_name');
            
            // If user didn't specify community, use their profile community
            if (empty($communityInput)) {
                $userId = Auth::id();
                $userModel = new User();
                $user = $userModel->find($userId);
                $communityInput = trim($user['community_name'] ?? '');
            }

            $criteria = [
                'community_name' => $communityInput,
                'religion' => $this->input('religion'),
                'ritual_name' => $this->input('ritual_name'),
                'category' => $this->input('category'),
            ];

            $rituals = $this->ritualModel->advancedSearch($criteria);

            ob_end_clean();
            $this->json([
                'success' => true,
                'count' => count($rituals),
                'rituals' => $rituals,
                'criteria' => $criteria,
            ]);
        } catch (\Exception $e) {
            ob_end_clean();
            $this->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Generate ritual using AI
     * Note: This does NOT save to global rituals - only returns data for user to add to their collection
     */
    public function generateRitual(): void
    {
        // Start output buffering to capture any unexpected output
        ob_start();

        // Suppress PHP error display for this request
        $oldErrorReporting = error_reporting();
        error_reporting(0);
        ini_set('display_errors', '0');

        try {
            if (!$this->verifyCsrf()) {
                ob_end_clean();
                error_reporting($oldErrorReporting);
                $this->json(['success' => false, 'error' => 'Invalid token.'], 400);
                return;
            }

            $criteria = [
                'community_name' => $this->input('community_name', ''),
                'religion' => $this->input('religion', 'Hinduism'),
                'ritual_name' => $this->input('ritual_name', ''),
                'occasion' => $this->input('occasion', ''),
                'additional_info' => $this->input('additional_info', ''),
            ];

            $userId = Auth::id();

            // Generate ritual using AI
            $result = $this->aiService->generateRitual($userId, $criteria);

            if (!$result['success']) {
                ob_end_clean();
                error_reporting($oldErrorReporting);
                $this->json(['success' => false, 'error' => $result['error']], 500);
                return;
            }

            $ritualData = $result['ritual'];

            // DO NOT save to global database - user can only add to their personal collection
            // AI-generated rituals are only for the user who generated them

            // Clear any buffered output
            ob_end_clean();
            error_reporting($oldErrorReporting);

            $this->json([
                'success' => true,
                'ritual' => $ritualData,
                'message' => 'Ritual generated successfully! Add it to your collection.',
            ]);

        } catch (\Exception $e) {
            // Clear any buffered output
            ob_end_clean();
            error_reporting($oldErrorReporting);

            $this->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * View ritual details (global)
     */
    public function viewRitual(string $id): void
    {
        $ritual = $this->ritualModel->getWithDetails((int) $id);
        if (!$ritual) {
            $this->redirect('/user/rituals', ['error' => 'Not found.']);
            return;
        }
        $this->ritualModel->incrementView((int) $id);
        $pandits = (new User())->getApprovedPandits();

        $this->viewWithLayout('user/ritual-detail', 'layouts/user', [
            'title' => $ritual['name'],
            'ritual' => $ritual,
            'pandits' => $pandits,
        ]);
    }

    // ============================================================
    // MY RITUALS - Personal Ritual Collection
    // ============================================================

    /**
     * View My Rituals collection
     */
    public function myRituals(): void
    {
        $userId = Auth::id();
        $rituals = $this->userRitualModel->getByUser($userId);
        $history = $this->userRitualModel->getHistory($userId, 10);

        $this->viewWithLayout('user/my-rituals', 'layouts/user', [
            'title' => 'My Rituals',
            'rituals' => $rituals,
            'history' => $history,
        ]);
    }

    /**
     * Add ritual to My Rituals from global database
     */
    public function addToMyRituals(): void
    {
        ob_start();
        error_reporting(0);
        ini_set('display_errors', '0');

        try {
            if (!$this->verifyCsrf()) {
                ob_end_clean();
                $this->json(['success' => false, 'error' => 'Invalid token.'], 400);
                return;
            }

            $globalRitualId = (int) $this->input('global_ritual_id');
            $userId = Auth::id();

            $userRitualId = $this->userRitualModel->addFromGlobal($userId, $globalRitualId);

            ob_end_clean();
            $this->json([
                'success' => true,
                'user_ritual_id' => $userRitualId,
                'message' => 'Ritual added to your collection!',
            ]);
        } catch (\Exception $e) {
            ob_end_clean();
            $this->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Add AI generated ritual to My Rituals
     */
    public function addGeneratedToMyRituals(): void
    {
        ob_start();
        error_reporting(0);
        ini_set('display_errors', '0');

        try {
            if (!$this->verifyCsrf()) {
                ob_end_clean();
                $this->json(['success' => false, 'error' => 'Invalid token.'], 400);
                return;
            }

            $ritualDataRaw = $this->input('ritual_data');
            
            if (empty($ritualDataRaw)) {
                ob_end_clean();
                $this->json(['success' => false, 'error' => 'No ritual data received'], 400);
                return;
            }
            
            $ritualData = json_decode($ritualDataRaw, true);
            $prompt = $this->input('prompt', '');
            $userId = Auth::id();

            if (!$ritualData) {
                ob_end_clean();
                $this->json(['success' => false, 'error' => 'Invalid ritual data format: ' . json_last_error_msg()], 400);
                return;
            }
            
            // Validate required fields
            if (empty($ritualData['name'])) {
                ob_end_clean();
                $this->json(['success' => false, 'error' => 'Ritual name is required'], 400);
                return;
            }

            $userRitualId = $this->userRitualModel->createFromAI($userId, $ritualData, $prompt);

            ob_end_clean();
            $this->json([
                'success' => true,
                'user_ritual_id' => $userRitualId,
                'message' => 'Ritual added to your collection!',
            ]);
        } catch (\Exception $e) {
            ob_end_clean();
            error_log("addGeneratedToMyRituals error: " . $e->getMessage() . " | File: " . $e->getFile() . " | Line: " . $e->getLine());
            $this->json(['success' => false, 'error' => 'Database error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Delete My Ritual
     */
    public function deleteMyRitual(string $id): void
    {
        if (!$this->verifyCsrf()) {
            $this->redirect('/user/my-rituals', ['error' => 'Invalid token.']);
            return;
        }

        $userId = Auth::id();
        
        if (!$this->userRitualModel->belongsToUser((int) $id, $userId)) {
            $this->redirect('/user/my-rituals', ['error' => 'Unauthorized action.']);
            return;
        }

        try {
            $this->userRitualModel->delete((int) $id);
            $this->redirect('/user/my-rituals', ['success' => 'Ritual removed from your collection.']);
        } catch (\Exception $e) {
            $this->redirect('/user/my-rituals', ['error' => 'Failed to delete ritual: ' . $e->getMessage()]);
        }
    }

    /**
     * Download Ritual PDF
     */
    public function downloadRitualPdf(string $id): void
    {
        $userId = Auth::id();
        $ritual = $this->userRitualModel->getWithDetails((int) $id);

        if (!$ritual || !$this->userRitualModel->belongsToUser((int) $id, $userId)) {
            $this->redirect('/user/my-rituals', ['error' => 'Ritual not found.']);
            return;
        }

        // Render PDF view directly (no layout)
        $this->view('user/ritual-pdf', [
            'ritual' => $ritual
        ]);
    }

    /**
     * View My Ritual details
     */
    public function viewMyRitual(string $id): void
    {
        $userId = Auth::id();
        $ritual = $this->userRitualModel->getWithDetails((int) $id);

        if (!$ritual || !$this->userRitualModel->belongsToUser((int) $id, $userId)) {
            $this->redirect('/user/my-rituals', ['error' => 'Ritual not found.']);
            return;
        }

        $this->viewWithLayout('user/my-ritual-detail', 'layouts/user', [
            'title' => $ritual['name'],
            'ritual' => $ritual,
        ]);
    }

    /**
     * Update My Ritual step
     */
    public function updateMyRitualStep(string $id): void
    {
        if (!$this->verifyCsrf()) {
            $this->json(['error' => 'Invalid token.'], 400);
            return;
        }

        $data = [
            'title' => $this->input('title'),
            'description' => $this->input('description'),
            'mantra' => $this->input('mantra'),
            'mantra_meaning' => $this->input('mantra_meaning'),
            'duration_minutes' => (int) $this->input('duration_minutes', 5),
            'special_instructions' => $this->input('special_instructions'),
            'items_needed' => $this->input('items_needed'),
        ];

        try {
            $this->userRitualModel->updateStep((int) $id, $data);
            $this->json(['success' => true, 'message' => 'Step updated!']);
        } catch (\Exception $e) {
            $this->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Add step to My Ritual
     */
    public function addMyRitualStep(string $id): void
    {
        if (!$this->verifyCsrf()) {
            $this->json(['error' => 'Invalid token.'], 400);
            return;
        }

        $userId = Auth::id();
        if (!$this->userRitualModel->belongsToUser((int) $id, $userId)) {
            $this->json(['error' => 'Unauthorized'], 403);
            return;
        }

        // Get current max step number
        $ritual = $this->userRitualModel->getWithDetails((int) $id);
        $maxStep = 0;
        foreach ($ritual['steps'] as $step) {
            if ($step['step_number'] > $maxStep) {
                $maxStep = $step['step_number'];
            }
        }

        // Determine step number: use input if provided, otherwise append
        $stepNumber = (int) $this->input('step_number', 0);
        if ($stepNumber <= 0) {
            $stepNumber = $maxStep + 1;
        }

        $data = [
            'step_number' => $stepNumber,
            'title' => $this->input('title', 'New Step'),
            'description' => $this->input('description', ''),
            'mantra' => $this->input('mantra'),
            'duration_minutes' => (int) $this->input('duration_minutes', 5),
        ];

        try {
            $stepId = $this->userRitualModel->addStep((int) $id, $data);
            $this->json(['success' => true, 'step_id' => $stepId, 'message' => 'Step added!']);
        } catch (\Exception $e) {
            $this->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Delete step from My Ritual
     */
    public function deleteMyRitualStep(string $id): void
    {
        if (!$this->verifyCsrf()) {
            $this->json(['error' => 'Invalid token.'], 400);
            return;
        }

        try {
            $this->userRitualModel->deleteStep((int) $id);
            $this->json(['success' => true, 'message' => 'Step deleted!']);
        } catch (\Exception $e) {
            $this->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    // ============================================================
    // START RITUAL - With Chatbot Assistance
    // ============================================================

    /**
     * Start ritual session
     */
    public function startRitual(string $id): void
    {
        $userId = Auth::id();
        $ritual = $this->userRitualModel->getWithDetails((int) $id);

        if (!$ritual || !$this->userRitualModel->belongsToUser((int) $id, $userId)) {
            $this->redirect('/user/my-rituals', ['error' => 'Ritual not found.']);
            return;
        }

        // Create or get existing session
        $sessionId = $this->userRitualModel->startSession($userId, (int) $id);

        $this->viewWithLayout('user/start-ritual', 'layouts/user', [
            'title' => 'Performing: ' . $ritual['name'],
            'ritual' => $ritual,
            'sessionId' => $sessionId,
        ]);
    }

    /**
     * Mark step as completed
     */
    public function completeStep(): void
    {
        if (!$this->verifyCsrf()) {
            $this->json(['error' => 'Invalid token.'], 400);
            return;
        }

        $sessionId = $this->input('session_id');
        $stepNumber = (int) $this->input('step_number');

        try {
            $this->userRitualModel->completeStep($sessionId, $stepNumber);
            $this->json(['success' => true, 'message' => 'Step completed!']);
        } catch (\Exception $e) {
            $this->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Complete ritual session
     */
    public function completeRitual(): void
    {
        if (!$this->verifyCsrf()) {
            $this->json(['error' => 'Invalid token.'], 400);
            return;
        }

        $sessionId = $this->input('session_id');

        try {
            $this->userRitualModel->completeSession($sessionId);
            $this->json(['success' => true, 'message' => 'Ritual completed! 🙏']);
        } catch (\Exception $e) {
            $this->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Chat with AI assistant during ritual
     */
    public function ritualChat(): void
    {
        if (!$this->verifyCsrf()) {
            $this->json(['error' => 'Invalid token.'], 400);
            return;
        }

        $userId = Auth::id();
        $ritualId = (int) $this->input('ritual_id');
        $stepNumber = (int) $this->input('step_number', 1);
        $question = $this->input('question', '');

        if (empty($question)) {
            $this->json(['success' => false, 'error' => 'Please ask a question.'], 400);
            return;
        }

        // Get ritual details
        $ritual = $this->userRitualModel->getWithDetails($ritualId);

        if (!$ritual) {
            $this->json(['success' => false, 'error' => 'Ritual not found.'], 404);
            return;
        }

        // Find current step
        $currentStep = null;
        foreach ($ritual['steps'] as $step) {
            if ($step['step_number'] == $stepNumber) {
                $currentStep = $step;
                break;
            }
        }

        $context = [
            'ritual_name' => $ritual['name'],
            'ritual_description' => $ritual['description'],
            'current_step' => $currentStep,
            'step_number' => $stepNumber,
            'question' => $question,
            'all_steps' => $ritual['steps'],
        ];

        $result = $this->aiService->chatAssistant($userId, $context);

        if ($result['success']) {
            $this->json([
                'success' => true,
                'answer' => $result['answer'],
            ]);
        } else {
            $this->json([
                'success' => false,
                'error' => $result['error'] ?? 'Failed to get response.',
            ], 500);
        }
    }

    // ============================================================
    // CUSTOM RITUALS (Legacy)
    // ============================================================

    public function customRituals(): void
    {
        $rituals = $this->customRitualModel->getByUser(Auth::id());
        $this->viewWithLayout('user/custom-rituals', 'layouts/user', [
            'title' => 'My Custom Rituals',
            'rituals' => $rituals,
        ]);
    }

    public function createCustomRitual(): void
    {
        $baseRituals = $this->ritualModel->getActive();
        $pandits = (new User())->getApprovedPandits();
        $this->viewWithLayout('user/custom-ritual-form', 'layouts/user', [
            'title' => 'Create Custom Ritual',
            'ritual' => null,
            'baseRituals' => $baseRituals,
            'pandits' => $pandits,
        ]);
    }

    public function storeCustomRitual(): void
    {
        if (!$this->verifyCsrf()) {
            $this->back(['error' => 'Invalid token.']);
            return;
        }
        $data = $this->only(['name', 'description', 'purpose', 'scheduled_date', 'venue', 'base_ritual_id', 'assigned_pandit_id']);
        
        // Convert empty strings to NULL for optional integer fields
        if (empty($data['base_ritual_id'])) {
            $data['base_ritual_id'] = null;
        }
        if (empty($data['assigned_pandit_id'])) {
            $data['assigned_pandit_id'] = null;
        }
        if (empty($data['scheduled_date'])) {
            $data['scheduled_date'] = null;
        }
        
        $data['user_id'] = Auth::id();
        $id = $this->customRitualModel->create($data);
        $this->redirect('/user/custom-rituals/' . $id, ['success' => 'Custom ritual created successfully.']);
    }

    public function submitCustomRitual(string $id): void
    {
        if (!$this->verifyCsrf()) {
            $this->back(['error' => 'Invalid token.']);
            return;
        }
        $this->customRitualModel->submit((int) $id);
        $this->back(['success' => 'Submitted for validation.']);
    }

    /**
     * View individual custom ritual details
     */
    public function viewCustomRitual(string $id): void
    {
        $ritual = $this->customRitualModel->getWithDetails((int) $id);
        
        if (!$ritual || $ritual['user_id'] != Auth::id()) {
            $this->redirect('/user/custom-rituals', ['error' => 'Custom ritual not found.']);
            return;
        }
        
        $this->viewWithLayout('user/custom-ritual-detail', 'layouts/user', [
            'title' => $ritual['name'],
            'ritual' => $ritual,
        ]);
    }

    /**
     * Delete custom ritual (only if draft or rejected)
     */
    public function deleteCustomRitual(string $id): void
    {
        if (!$this->verifyCsrf()) {
            $this->back(['error' => 'Invalid token.']);
            return;
        }
        
        $ritual = $this->customRitualModel->find((int) $id);
        if (!$ritual || $ritual['user_id'] !== Auth::id()) {
            $this->redirect('/user/custom-rituals', ['error' => 'Unauthorized action.']);
            return;
        }
        
        // Only allow deletion of draft or rejected rituals
        if (!in_array($ritual['status'], ['draft', 'rejected'])) {
            $this->redirect('/user/custom-rituals', ['error' => 'Cannot delete submitted or approved rituals.']);
            return;
        }
        
        $this->customRitualModel->delete((int) $id);
        $this->redirect('/user/custom-rituals', ['success' => 'Custom ritual deleted successfully.']);
    }

    // Pandit Selection
    public function selectPandit(): void
    {
        $pandits = (new User())->getApprovedPandits();
        $this->viewWithLayout('user/select-pandit', 'layouts/user', [
            'title' => 'Select Pandit',
            'pandits' => $pandits,
        ]);
    }

    /**
     * Show booking form for a specific pandit
     */
    public function showBookingForm(string $panditId): void
    {
        $pandit = (new User())->getPanditProfile((int) $panditId);
        
        if (!$pandit) {
            $this->redirect('/user/select-pandit', ['error' => 'Pandit not found.']);
            return;
        }
        
        $this->viewWithLayout('user/book-pandit-form', 'layouts/user', [
            'title' => 'Book ' . $pandit['name'],
            'pandit' => $pandit,
        ]);
    }

    public function bookPandit(): void
    {
        if (!$this->verifyCsrf()) {
            $this->back(['error' => 'Invalid token.']);
            return;
        }
        
        $panditId = $this->input('pandit_id');
        $bookingPurpose = $this->input('booking_purpose');
        
        if (empty($panditId) || empty($bookingPurpose)) {
            $this->back(['error' => 'Please fill in all required fields.']);
            return;
        }
        
        $data = [
            'pandit_id' => $panditId,
            'user_id' => Auth::id(),
            'booking_purpose' => $bookingPurpose,
            'scheduled_date' => $this->input('scheduled_date') ?: null,
            'scheduled_time' => $this->input('scheduled_time') ?: null,
            'venue' => $this->input('venue') ?: null,
            'user_notes' => $this->input('additional_notes') ?: null,
            'status' => 'pending',
        ];
        
        $this->assignmentModel->create($data);
        $this->redirect('/user/bookings', ['success' => 'Booking request sent successfully! The pandit will review your request.']);
    }

    public function bookings(): void
    {
        $bookings = $this->assignmentModel->getForUser(Auth::id());
        $this->viewWithLayout('user/bookings', 'layouts/user', [
            'title' => 'My Bookings',
            'bookings' => $bookings,
        ]);
    }

    /**
     * Cancel a booking (only if pending)
     */
    public function cancelBooking(string $id): void
    {
        if (!$this->verifyCsrf()) {
            $this->back(['error' => 'Invalid token.']);
            return;
        }
        
        $booking = $this->assignmentModel->find((int) $id);
        if (!$booking || $booking['user_id'] !== Auth::id()) {
            $this->redirect('/user/bookings', ['error' => 'Unauthorized action.']);
            return;
        }
        
        // Only allow cancellation of pending bookings
        if ($booking['status'] !== 'pending') {
            $this->redirect('/user/bookings', ['error' => 'Can only cancel pending bookings.']);
            return;
        }
        
        $this->assignmentModel->update((int) $id, ['status' => 'cancelled']);
        $this->redirect('/user/bookings', ['success' => 'Booking cancelled successfully.']);
    }

    // Shopping List
    public function shoppingList(): void
    {
        $items = $this->shoppingListModel->getByUser(Auth::id(), false); // Show all items (pending + purchased)
        $summary = $this->shoppingListModel->getSummary(Auth::id());
        $this->viewWithLayout('user/shopping-list', 'layouts/user', [
            'title' => 'Shopping List',
            'items' => $items,
            'summary' => $summary,
        ]);
    }

    public function addToShoppingList(): void
    {
        if (!$this->verifyCsrf()) {
            $this->back(['error' => 'Invalid token.']);
            return;
        }
        $data = $this->only(['item_name', 'quantity', 'unit', 'estimated_cost']);
        $data['user_id'] = Auth::id();
        $this->shoppingListModel->create($data);
        $this->back(['success' => 'Item added.']);
    }

    public function generateShoppingList(string $id): void
    {
        $userId = Auth::id();
        $id = (int) $id;

        // Clear all existing items first (fresh start)
        $this->shoppingListModel->clearAll($userId);

        // Check if it's a user ritual first (prioritize personal customizations)
        if ($this->userRitualModel->belongsToUser($id, $userId)) {
            $count = $this->shoppingListModel->createFromUserRitual($userId, $id);
        } else {
            // Fallback to global ritual
            $count = $this->shoppingListModel->createFromRitual($userId, $id);
        }

        $this->redirect('/user/shopping-list', ['success' => "$count items added to your shopping list."]);
    }

    public function markItemPurchased(string $id): void
    {
        $this->shoppingListModel->markPurchased((int) $id);
        $this->back(['success' => 'Marked as purchased.']);
    }

    public function markItemUnpurchased(string $id): void
    {
        $this->shoppingListModel->markNotPurchased((int) $id);
        $this->back(['success' => 'Item unmarked.']);
    }

    public function updateItemQuantity(string $id): void
    {
        if (!$this->verifyCsrf()) {
            $this->json(['success' => false, 'error' => 'Invalid token.'], 400);
            return;
        }

        $quantity = (float) $this->input('quantity', 1);
        if ($quantity < 0.1) {
            $quantity = 0.1;
        }

        try {
            $this->shoppingListModel->update((int) $id, ['quantity' => $quantity]);
            $this->json(['success' => true, 'quantity' => $quantity]);
        } catch (\Exception $e) {
            $this->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    // ============================================================
    // CHECKOUT FLOW - Cart, Shop Finder, Order Placement
    // ============================================================

    /**
     * Shopping checkout page
     */
    public function shoppingCheckout(): void
    {
        $userId = Auth::id();
        $items = $this->shoppingListModel->getByUser($userId, true); // Pending only
        $summary = $this->shoppingListModel->getSummary($userId);

        if (empty($items)) {
            $this->redirect('/user/shopping-list', ['error' => 'Your cart is empty.']);
            return;
        }

        $this->viewWithLayout('user/shopping-checkout', 'layouts/user', [
            'title' => 'Checkout',
            'items' => $items,
            'summary' => $summary,
        ]);
    }

    /**
     * Find nearby shops for all cart items
     */
    public function findNearbyShopsForCart(): void
    {
        ob_start();
        error_reporting(0);
        ini_set('display_errors', '0');

        try {
            if (!$this->verifyCsrf()) {
                ob_end_clean();
                $this->json(['success' => false, 'error' => 'Invalid token.'], 400);
                return;
            }

            $location = $this->input('location');
            $latitude = $this->input('latitude');
            $longitude = $this->input('longitude');

            if (empty($location)) {
                ob_end_clean();
                $this->json(['success' => false, 'error' => 'Location is required.'], 400);
                return;
            }

            // Get all pending items
            $userId = Auth::id();
            $items = $this->shoppingListModel->getByUser($userId, true);
            
            if (empty($items)) {
                ob_end_clean();
                $this->json(['success' => false, 'error' => 'No items in cart.'], 400);
                return;
            }

            // Create a combined item list for AI
            $itemNames = array_map(fn($item) => $item['item_name'], $items);
            $itemList = implode(', ', $itemNames);

            // Use AI service to find shops
            $result = $this->aiService->findNearbyShops($location, $itemList);

            ob_end_clean();
            $this->json($result);

        } catch (\Exception $e) {
            ob_end_clean();
            $this->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Place order
     */
    public function placeOrder(): void
    {
        ob_start();
        error_reporting(0);
        ini_set('display_errors', '0');

        try {
            if (!$this->verifyCsrf()) {
                ob_end_clean();
                $this->json(['success' => false, 'error' => 'Invalid token.'], 400);
                return;
            }

            $userId = Auth::id();
            
            // Get form data
            $shopName = $this->input('shop_name');
            $shopLocation = $this->input('shop_location');
            $shopType = $this->input('shop_type');
            $latitude = $this->input('latitude');
            $longitude = $this->input('longitude');
            $userAddress = $this->input('user_address');
            $notes = $this->input('notes');

            if (empty($shopName)) {
                ob_end_clean();
                $this->json(['success' => false, 'error' => 'Please select a shop.'], 400);
                return;
            }

            // Get pending items
            $items = $this->shoppingListModel->getByUser($userId, true);
            
            if (empty($items)) {
                ob_end_clean();
                $this->json(['success' => false, 'error' => 'No items in cart.'], 400);
                return;
            }

            // Create order
            $orderData = [
                'shop_name' => $shopName,
                'shop_location' => $shopLocation,
                'shop_type' => $shopType,
                'user_latitude' => $latitude ?: null,
                'user_longitude' => $longitude ?: null,
                'user_address' => $userAddress,
                'status' => 'confirmed',
                'notes' => $notes,
            ];

            $orderId = $this->orderModel->createWithItems($userId, $orderData, $items);

            // Mark all items as purchased
            foreach ($items as $item) {
                $this->shoppingListModel->markPurchased($item['id'], null, $shopName);
            }

            ob_end_clean();
            $this->json([
                'success' => true,
                'order_id' => $orderId,
                'message' => 'Order placed successfully!',
            ]);

        } catch (\Exception $e) {
            ob_end_clean();
            $this->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * View orders history
     */
    public function orders(): void
    {
        $userId = Auth::id();
        $orders = $this->orderModel->getByUser($userId);

        $this->viewWithLayout('user/orders', 'layouts/user', [
            'title' => 'My Orders',
            'orders' => $orders,
        ]);
    }

    /**
     * View single order
     */
    public function viewOrder(string $id): void
    {
        $userId = Auth::id();
        
        if (!$this->orderModel->belongsToUser((int) $id, $userId)) {
            $this->redirect('/user/orders', ['error' => 'Order not found.']);
            return;
        }

        $order = $this->orderModel->getWithItems((int) $id);

        $this->viewWithLayout('user/order-detail', 'layouts/user', [
            'title' => 'Order #' . $id,
            'order' => $order,
        ]);
    }

    // AI Suggestions
    public function aiSuggestions(): void
    {
        $history = $this->aiService->getHistory(Auth::id());
        $this->viewWithLayout('user/ai-suggestions', 'layouts/user', [
            'title' => 'AI Suggestions',
            'history' => $history,
        ]);
    }

    public function getAISuggestion(): void
    {
        if (!$this->verifyCsrf()) {
            $this->json(['error' => 'Invalid token.'], 400);
            return;
        }

        $type = $this->input('type');
        $context = $this->input('context', []);
        
        // Use existing AI service for suggestion
        $userId = Auth::id();
        $result = $this->aiService->suggestRitual($userId, $context);
        $this->json($result);
    }

    /**
     * Find nearby shops for item
     */
    public function findShops(): void
    {
        ob_start();
        error_reporting(0);
        ini_set('display_errors', '0');

        try {
            if (!$this->verifyCsrf()) {
                ob_end_clean();
                $this->json(['success' => false, 'error' => 'Invalid token.'], 400);
                return;
            }

            $location = $this->input('location');
            $item = $this->input('item');

            if (empty($location) || empty($item)) {
                ob_end_clean();
                $this->json(['success' => false, 'error' => 'Location and item are required.'], 400);
                return;
            }

            $result = $this->aiService->findNearbyShops($location, $item);

            ob_end_clean();
            $this->json($result);

        } catch (\Exception $e) {
            ob_end_clean();
            $this->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    // Cultural Insights
    public function insights(): void
    {
        $category = $this->input('category');
        if ($category) {
            $insights = $this->insightModel->getByCategory($category);
        } else {
            $insights = $this->insightModel->getPublished();
        }
        $categories = $this->insightModel->getCategories();
        $this->viewWithLayout('user/insights', 'layouts/user', [
            'title' => 'Cultural Insights',
            'insights' => $insights,
            'categories' => $categories,
        ]);
    }

    public function viewInsight(string $slug): void
    {
        $insight = $this->insightModel->getBySlug($slug);
        if (!$insight) {
            $this->redirect('/user/insights', ['error' => 'Not found.']);
            return;
        }
        $this->insightModel->incrementView($insight['id']);
        $this->viewWithLayout('user/insight-detail', 'layouts/user', [
            'title' => $insight['title'],
            'insight' => $insight,
        ]);
    }

    // Q&A - View and Ask Questions
    public function questions(): void
    {
        $userId = Auth::id();
        $pandits = (new User())->getApprovedPandits();
        $rituals = $this->ritualModel->getActive();

        // Get user's questions with pandit and ritual info
        $sql = "SELECT q.*, 
                       p.name as pandit_name,
                       r.name as ritual_name
                FROM SAI_pandit_qna q
                INNER JOIN SAI_users p ON q.pandit_id = p.id
                LEFT JOIN SAI_rituals r ON q.ritual_id = r.id
                WHERE q.user_id = :user_id
                ORDER BY q.created_at DESC";
        $questions = Database::query($sql, ['user_id' => $userId]);

        $this->viewWithLayout('user/questions', 'layouts/user', [
            'title' => 'Ask a Pandit',
            'pandits' => $pandits,
            'rituals' => $rituals,
            'questions' => $questions,
        ]);
    }

    // Ask Pandit
    public function askPandit(): void
    {
        if (!$this->verifyCsrf()) {
            $this->back(['error' => 'Invalid token.']);
            return;
        }
        $data = $this->only(['pandit_id', 'ritual_id', 'question']);
        $data['user_id'] = Auth::id();
        $data['status'] = 'pending';
        $data['created_at'] = date('Y-m-d H:i:s');

        // Convert empty ritual_id to NULL (optional field)
        if (empty($data['ritual_id'])) {
            $data['ritual_id'] = null;
        }

        $sql = "INSERT INTO SAI_pandit_qna (user_id, pandit_id, ritual_id, question, status, created_at)
                VALUES (:user_id, :pandit_id, :ritual_id, :question, :status, :created_at)";
        Database::execute($sql, $data);
        $this->back(['success' => 'Question submitted.']);
    }
    // ============================================================
    // USER PROFILE
    // ============================================================

    /**
     * Show user profile
     */
    public function profile(): void
    {
        $userId = Auth::id();
        if (!$userId) {
            $this->redirect('/login');
            return;
        }

        // Fetch full user data from database (session only has limited fields)
        $user = (new User())->find($userId);

        $this->viewWithLayout('user/profile', 'layouts/user', [
            'title' => 'My Profile',
            'user' => $user,
        ]);
    }

    /**
     * Update user profile
     */
    public function updateProfile(): void
    {
        if (!$this->verifyCsrf()) {
            $this->back(['error' => 'Invalid token.']);
            return;
        }

        $userId = Auth::id();
        $data = $this->only(['community_name', 'religion']);

        try {
            // Update user model (User model needs to be instantiated to use update)
            (new User())->update($userId, $data);
            
            // Refresh session user data
            Auth::refresh();

            $this->redirect('/user/profile', ['success' => 'Profile updated successfully.']);
        } catch (\Exception $e) {
            $this->back(['error' => 'Failed to update profile: ' . $e->getMessage()]);
        }
    }
}
