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
use App\Models\Vendor;
use App\Models\Review;
use App\Models\PanditChat;
use App\Models\MohuratRequest;
use App\Models\RitualFeedback;
use App\Models\Subscription;
use App\Services\AIService;
use App\Services\CommunityFestivalService;
use App\Services\MailService;
use App\Services\RazorpayService;
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
    private Vendor $vendorModel;
    private Review $reviewModel;
    private MailService $mailService;
    private RitualFeedback $ritualFeedbackModel;
    private PanditChat $panditChatModel;
    private Subscription $subscriptionModel;
    private RazorpayService $razorpayService;

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
        $this->vendorModel = new Vendor();
        $this->reviewModel = new Review();
        $this->vendorModel = new Vendor();
        $this->mailService = new MailService();
        $this->ritualFeedbackModel = new RitualFeedback();
        $this->panditChatModel = new PanditChat();
        $this->subscriptionModel = new Subscription();
        $this->razorpayService = new RazorpayService();
    }

    public function dashboard(): void
    {
        $userId = Auth::id();
        $user = (new User())->find($userId);
        $family = $this->familyModel->getPrimaryFamily($userId);
        $assignments = $this->assignmentModel->getForUser($userId);
        $shoppingSummary = $this->shoppingListModel->getSummary($userId);
        $featuredRituals = $this->ritualModel->getFeatured();
        $featuredInsights = $this->insightModel->getFeatured(3);
        $myRituals = $this->userRitualModel->getByUser($userId);

        // Community-based upcoming festivals
        $communityName = trim($user['community_name'] ?? '');
        $upcomingFestivals = CommunityFestivalService::getUpcomingForCommunity($communityName, 5);
        $communityLabel = CommunityFestivalService::getCommunityLabel($communityName);

        $this->viewWithLayout('user/dashboard', 'layouts/user', [
            'title' => 'Dashboard - Sanskar AI',
            'family' => $family,
            'assignments' => array_slice($assignments, 0, 5),
            'shoppingSummary' => $shoppingSummary,
            'featuredRituals' => $featuredRituals,
            'featuredInsights' => $featuredInsights,
            'myRituals' => array_slice($myRituals, 0, 3),
            'upcomingFestivals' => $upcomingFestivals,
            'communityLabel' => $communityLabel,
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
        $data = $this->only(['family_name', 'gotra', 'nakshatra', 'kul_devta', 'city', 'state', 'country']);
        $data['user_id'] = Auth::id();

        // Prevent duplicate families for the same user
        $existing = $this->familyModel->where([
            'user_id' => $data['user_id'],
            'family_name' => $data['family_name'],
        ]);
        if (!empty($existing)) {
            $this->redirect('/user/families', ['error' => 'A family with this name already exists.']);
            return;
        }

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
        $data = $this->only(['family_name', 'gotra', 'nakshatra', 'kul_devta', 'city', 'state', 'country']);
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

        // Get top communities for dropdown
        $userModel = new User();
        $topCommunities = $userModel->getTopCommunities(6);
        
        // Get top ritual names for dropdown
        $topRitualNames = $this->ritualModel->getTopRitualNames(15);

        $this->viewWithLayout('user/explore-rituals', 'layouts/user', [
            'title' => 'Explore Rituals',
            'categories' => $categories,
            'popularRituals' => $popularRituals,
            'totalRitualCount' => $totalRitualCount,
            'userCommunity' => $userCommunity,
            'topCommunities' => $topCommunities,
            'topRitualNames' => $topRitualNames,
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
     * Search rituals in global database AND user's My Rituals
     * Auto-injects user's community if not manually specified
     */
    public function searchRituals(): void
    {
        ob_start();
        error_reporting(0);
        ini_set('display_errors', '0');

        try {
            $userId = Auth::id();
            $communityInput = $this->input('community_name');
            
            // If user didn't specify community, use their profile community
            if (empty($communityInput)) {
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

            // Search in global database
            $globalRituals = $this->ritualModel->advancedSearch($criteria);
            // Tag global rituals with source
            foreach ($globalRituals as &$r) {
                $r['source_type'] = 'global';
            }
            unset($r);

            // Also search in user's My Rituals
            $myRituals = $this->userRitualModel->searchByUser($userId, $criteria);
            // Already tagged with source_type = 'my_ritual' in the model

            // Merge results (My Rituals first, then global)
            $allRituals = array_merge($myRituals, $globalRituals);

            ob_end_clean();
            $this->json([
                'success' => true,
                'count' => count($allRituals),
                'rituals' => $allRituals,
                'criteria' => $criteria,
                'my_rituals_count' => count($myRituals),
                'global_count' => count($globalRituals),
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
     * Regenerate ritual with user feedback (feedback loop)
     */
    public function regenerateRitual(): void
    {
        ob_start();
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

            $previousResponseRaw = $this->input('previous_response', '');
            $previousResponse = json_decode($previousResponseRaw, true) ?? [];
            $userFeedback = $this->input('user_feedback', '');
            $sessionId = $this->input('session_id', '');
            $roundNumber = (int) $this->input('round_number', 1);

            if (empty($userFeedback)) {
                ob_end_clean();
                error_reporting($oldErrorReporting);
                $this->json(['success' => false, 'error' => 'Please provide feedback'], 400);
                return;
            }

            // Generate session ID if not provided
            if (empty($sessionId)) {
                $sessionId = bin2hex(random_bytes(16));
            }

            $userId = Auth::id();

            // Past learning feedback is disabled because the feedback table was removed
            $pastFeedback = [];

            // Regenerate with feedback
            $result = $this->aiService->regenerateRitualWithFeedback(
                $userId,
                $criteria,
                $previousResponse,
                $userFeedback,
                $pastFeedback
            );

            if (!$result['success']) {
                ob_end_clean();
                error_reporting($oldErrorReporting);
                $this->json(['success' => false, 'error' => $result['error']], 500);
                return;
            }

            ob_end_clean();
            error_reporting($oldErrorReporting);

            $this->json([
                'success' => true,
                'ritual' => $result['ritual'],
                'session_id' => $sessionId,
                'round_number' => $roundNumber + 1,
                'message' => 'Ritual regenerated with your feedback!',
            ]);

        } catch (\Exception $e) {
            ob_end_clean();
            error_reporting($oldErrorReporting);
            $this->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Accept AI generated ritual (final step of feedback loop)
     */
    public function acceptAIRitual(): void
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
            $sessionId = $this->input('session_id', '');
            $prompt = $this->input('prompt', '');
            $userId = Auth::id();

            if (empty($ritualDataRaw)) {
                ob_end_clean();
                $this->json(['success' => false, 'error' => 'No ritual data received'], 400);
                return;
            }

            $ritualData = json_decode($ritualDataRaw, true);
            if (!$ritualData || empty($ritualData['name'])) {
                ob_end_clean();
                $this->json(['success' => false, 'error' => 'Invalid ritual data'], 400);
                return;
            }

            // Add to user's My Rituals collection
            $userRitualId = $this->userRitualModel->createFromAI($userId, $ritualData, $prompt);

            ob_end_clean();
            $this->json([
                'success' => true,
                'user_ritual_id' => $userRitualId,
                'message' => 'Ritual accepted and added to your collection!',
            ]);
        } catch (\Exception $e) {
            ob_end_clean();
            error_log("acceptAIRitual error: " . $e->getMessage());
            $this->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Submit Like/Dislike feedback for AI-generated rituals
     * Supports both AJAX (with CSRF) and sendBeacon (without CSRF) requests
     */
    public function submitRitualFeedback(): void
    {
        ob_start();
        error_reporting(0);
        ini_set('display_errors', '0');

        try {
            // Detect if this is a beacon request (Content-Type: application/x-www-form-urlencoded or text/plain)
            $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
            $isBeacon = str_contains($contentType, 'application/x-www-form-urlencoded')
                     || str_contains($contentType, 'text/plain');

            // Only verify CSRF for non-beacon requests
            if (!$isBeacon && !$this->verifyCsrf()) {
                ob_end_clean();
                $this->json(['success' => false, 'error' => 'Invalid token.'], 400);
                return;
            }

            // For beacon requests, parse form data from raw input if needed
            if ($isBeacon && empty($_POST)) {
                $rawInput = file_get_contents('php://input');
                parse_str($rawInput, $_POST);
            }

            $feedbackType = $this->input('feedback_type', '');
            $feedbackText = $this->input('feedback_text', '');
            $ritualName = $this->input('ritual_name', '');
            $communityName = $this->input('community_name', '');
            $religion = $this->input('religion', '');

            if (!in_array($feedbackType, ['like', 'dislike'])) {
                ob_end_clean();
                $this->json(['success' => false, 'error' => 'Invalid feedback type.'], 400);
                return;
            }

            if (empty($ritualName)) {
                ob_end_clean();
                $this->json(['success' => false, 'error' => 'Ritual name is required.'], 400);
                return;
            }

            $userId = null;
            try {
                $userId = Auth::id();
            } catch (\Exception $e) {
                // User may not be logged in for beacon requests
            }

            $this->ritualFeedbackModel->storeFeedback([
                'user_id' => $userId,
                'community_name' => $communityName ?: null,
                'religion' => $religion ?: null,
                'ritual_name' => $ritualName,
                'feedback_type' => $feedbackType,
                'feedback_text' => $feedbackText ?: null,
            ]);

            ob_end_clean();
            $this->json(['success' => true, 'message' => 'Feedback submitted. Thank you!']);
        } catch (\Exception $e) {
            ob_end_clean();
            error_log('submitRitualFeedback error: ' . $e->getMessage());
            $this->json(['success' => false, 'error' => $e->getMessage()], 500);
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

            // Check if already added
            $existing = $this->userRitualModel->findByUserAndGlobal($userId, $globalRitualId);
            if ($existing) {
                ob_end_clean();
                $this->json([
                    'success' => false,
                    'already_added' => true,
                    'user_ritual_id' => $existing['id'],
                    'error' => 'This ritual is already in your collection!',
                ]);
                return;
            }

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

    /**
     * Add item to My Ritual
     */
    public function addMyRitualItem(string $id): void
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

        $data = [
            'item_name' => $this->input('item_name'),
            'item_name_local' => $this->input('item_name_local'),
            'quantity' => $this->input('quantity', 1),
            'unit' => $this->input('unit', 'piece'),
            'is_mandatory' => (bool) $this->input('is_mandatory', true),
            'description' => $this->input('description'),
            'alternatives' => $this->input('alternatives'),
        ];

        if (empty($data['item_name'])) {
            $this->json(['error' => 'Item name is required'], 400);
            return;
        }

        try {
            $itemId = $this->userRitualModel->addItem((int) $id, $data);
            $this->json(['success' => true, 'item_id' => $itemId, 'message' => 'Item added!']);
        } catch (\Exception $e) {
            $this->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Update item in My Ritual
     */
    public function updateMyRitualItem(string $id): void
    {
        if (!$this->verifyCsrf()) {
            $this->json(['error' => 'Invalid token.'], 400);
            return;
        }

        $data = [
            'item_name' => $this->input('item_name'),
            'item_name_local' => $this->input('item_name_local'),
            'quantity' => $this->input('quantity', 1),
            'unit' => $this->input('unit', 'piece'),
            'is_mandatory' => (bool) $this->input('is_mandatory', false),
            'description' => $this->input('description'),
            'alternatives' => $this->input('alternatives'),
        ];

        try {
            $this->userRitualModel->updateItem((int) $id, $data);
            $this->json(['success' => true, 'message' => 'Item updated!']);
        } catch (\Exception $e) {
            $this->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Delete item from My Ritual
     */
    public function deleteMyRitualItem(string $id): void
    {
        if (!$this->verifyCsrf()) {
            $this->json(['error' => 'Invalid token.'], 400);
            return;
        }

        try {
            $this->userRitualModel->deleteItem((int) $id);
            $this->json(['success' => true, 'message' => 'Item deleted!']);
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
        $ritualId = $this->input('ritual_id');
        $bookingPurpose = $this->input('booking_purpose');
        $scheduledDate = $this->input('scheduled_date');
        
        // Validate required fields - need pandit, date, and either ritual_id or booking_purpose
        if (empty($panditId) || empty($scheduledDate)) {
            $this->back(['error' => 'Please select a pandit and preferred date.']);
            return;
        }
        
        if (empty($ritualId) && empty($bookingPurpose)) {
            $this->back(['error' => 'Please specify a ritual or booking purpose.']);
            return;
        }
        
        $data = [
            'pandit_id' => $panditId,
            'user_id' => Auth::id(),
            'ritual_id' => !empty($ritualId) ? (int)$ritualId : null,
            'booking_purpose' => $bookingPurpose ?: null,
            'scheduled_date' => $scheduledDate ?: null,
            'scheduled_time' => $this->input('scheduled_time') ?: null,
            'venue' => $this->input('venue') ?: null,
            'user_notes' => $this->input('additional_notes') ?: null,
            'status' => 'pending',
        ];
        
        $this->assignmentModel->create($data);

        // Send booking notification email to the pandit
        try {
            $pandit = (new User())->getPanditProfile((int) $panditId);
            $booker = (new User())->find(Auth::id());

            if ($pandit && !empty($pandit['email']) && $booker) {
                $ritualName = null;
                if (!empty($ritualId)) {
                    $ritual = $this->ritualModel->find((int) $ritualId);
                    $ritualName = $ritual['name'] ?? null;
                }
                $this->mailService->sendPanditBookingNotification(
                    $pandit['email'],
                    $pandit['name'],
                    array_merge($data, ['ritual_name' => $ritualName]),
                    $booker
                );
            }
        } catch (\Throwable $e) {
            error_log('Booking notification email failed: ' . $e->getMessage());
        }

        $this->redirect('/user/bookings', ['success' => 'Booking request sent successfully! The pandit will review your request.']);
    }

    public function bookings(): void
    {
        $bookings = $this->assignmentModel->getForUser(Auth::id());
        
        // Check if each completed booking has a review
        foreach ($bookings as &$booking) {
            if ($booking['status'] === 'completed') {
                $booking['has_review'] = $this->reviewModel->hasReviewedPandit(
                    Auth::id(),
                    (int) $booking['id']
                );
            }
        }
        unset($booking);
        
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

        // Check if each completed order has a review
        foreach ($orders as &$order) {
            if ($order['status'] === 'completed') {
                $order['has_review'] = $this->reviewModel->hasReviewedVendor(
                    $userId,
                    (int) $order['id']
                );
            }
        }
        unset($order);

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
        $data = $this->only(['community_name', 'religion', 'kul_devi_devta']);

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

    /**
     * Update user password
     */
    public function updatePassword(): void
    {
        if (!$this->verifyCsrf()) {
            $this->back(['error' => 'Invalid token.']);
            return;
        }

        $currentPassword = $this->input('current_password');
        $newPassword = $this->input('new_password');
        $confirmPassword = $this->input('confirm_password');

        if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
            $this->back(['error' => 'All fields are required.']);
            return;
        }

        if ($newPassword !== $confirmPassword) {
            $this->back(['error' => 'New password and confirm password do not match.']);
            return;
        }

        if (strlen($newPassword) < 6) {
            $this->back(['error' => 'Password must be at least 6 characters long.']);
            return;
        }

        $userId = Auth::id();
        $userModel = new User();
        $user = $userModel->find($userId);

        if (!password_verify($currentPassword, $user['password_hash'])) {
            $this->back(['error' => 'Incorrect current password.']);
            return;
        }

        try {
            $userModel->update($userId, ['password_hash' => password_hash($newPassword, PASSWORD_DEFAULT)]);
            $this->redirect('/user/profile', ['success' => 'Password changed successfully.']);
        } catch (\Exception $e) {
            $this->back(['error' => 'Failed to update password: ' . $e->getMessage()]);
        }
    }

    // ============================================================
    // VENDOR BROWSING
    // ============================================================

    /**
     * Browse vendors page
     */
    public function vendors(): void
    {
        $category = $this->input('category');
        $search = $this->input('search');
        $city = $this->input('city');
        
        // Get featured vendors for showcase
        $featuredVendors = $this->vendorModel->getFeatured(6);
        
        // Get all active vendors (or filtered)
        $vendors = $this->vendorModel->getActiveVendors($category, $search);
        
        // Get available cities for filter
        $cities = $this->vendorModel->getCities();
        
        // Get vendor count by category
        $categoryCounts = $this->vendorModel->getCountByCategory();
        
        $this->viewWithLayout('user/vendors', 'layouts/user', [
            'title' => 'Browse Vendors - Sanskar AI',
            'vendors' => $vendors,
            'featuredVendors' => $featuredVendors,
            'categories' => Vendor::CATEGORIES,
            'cities' => $cities,
            'categoryCounts' => $categoryCounts,
            'selectedCategory' => $category,
            'selectedCity' => $city,
            'search' => $search,
        ]);
    }

    /**
     * Find nearby vendors (AJAX endpoint)
     */
    public function findNearbyVendors(): void
    {
        header('Content-Type: application/json');
        
        $latitude = (float)$this->input('latitude');
        $longitude = (float)$this->input('longitude');
        $radiusKm = (float)($this->input('radius') ?: 15);
        $category = $this->input('category');
        $search = $this->input('search');
        
        if (!$latitude || !$longitude) {
            echo json_encode([
                'success' => false,
                'message' => 'Location coordinates are required.',
            ]);
            return;
        }
        
        try {
            $vendors = $this->vendorModel->findNearbyVendors(
                $latitude,
                $longitude,
                $radiusKm,
                $category,
                $search
            );
            
            echo json_encode([
                'success' => true,
                'vendors' => $vendors,
                'count' => count($vendors),
                'radius' => $radiusKm,
            ]);
        } catch (\Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to find vendors. ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * View single vendor details
     */
    public function viewVendor(int $id): void
    {
        $vendor = $this->vendorModel->find($id);
        
        if (!$vendor || !$vendor['is_active']) {
            $this->redirect('/user/vendors', ['error' => 'Vendor not found.']);
            return;
        }
        
        // Get similar vendors (same category, same city)
        $similarVendors = $this->vendorModel->getByCity($vendor['city'], $vendor['category']);
        // Remove current vendor from similar list
        $similarVendors = array_filter($similarVendors, fn($v) => $v['id'] != $id);
        $similarVendors = array_slice($similarVendors, 0, 4);
        
        // Get vendor reviews
        $reviews = $this->reviewModel->getVendorReviews($id, 5);
        
        $this->viewWithLayout('user/vendor-detail', 'layouts/user', [
            'title' => $vendor['name'] . ' - Sanskar AI',
            'vendor' => $vendor,
            'similarVendors' => $similarVendors,
            'categories' => Vendor::CATEGORIES,
            'reviews' => $reviews,
        ]);
    }

    // =========================================================================
    // REVIEW MANAGEMENT
    // =========================================================================

    /**
     * Show review form for a Pandit (after completed assignment)
     */
    public function reviewPanditForm(int $assignmentId): void
    {
        $userId = Auth::id();
        
        // Check eligibility
        $eligibility = $this->reviewModel->canReviewPandit($userId, $assignmentId);
        
        if (!$eligibility['valid']) {
            $this->redirect('/user/bookings', ['error' => $eligibility['error']]);
            return;
        }
        
        $assignment = $eligibility['assignment'];
        
        $this->viewWithLayout('user/review-pandit-form', 'layouts/user', [
            'title' => 'Review Pandit - Sanskar AI',
            'assignment' => $assignment,
        ]);
    }

    /**
     * Submit Pandit review
     */
    public function submitPanditReview(): void
    {
        if (!$this->verifyCsrf()) {
            $this->json(['success' => false, 'error' => 'Invalid security token.'], 403);
            return;
        }

        $userId = Auth::id();
        $assignmentId = (int)$this->input('assignment_id');
        
        if (!$assignmentId) {
            $this->json(['success' => false, 'error' => 'Invalid booking.'], 400);
            return;
        }

        // Collect review data
        $data = [
            'rating_overall' => $this->input('rating_overall'),
            'punctuality' => $this->input('punctuality'),
            'knowledge' => $this->input('knowledge'),
            'behavior' => $this->input('behavior'),
            'clarity' => $this->input('clarity'),
            'review_text' => $this->input('review_text'),
        ];

        // Create review
        $result = $this->reviewModel->createPanditReview($userId, $assignmentId, $data);

        if (!$result['success']) {
            if (isset($result['errors'])) {
                $this->json(['success' => false, 'errors' => $result['errors']], 400);
            } else {
                $this->json(['success' => false, 'error' => $result['error']], 400);
            }
            return;
        }

        // Run AI moderation
        if (!empty($data['review_text'])) {
            $moderation = $this->aiService->moderateReview(
                $data['review_text'],
                (int)$data['rating_overall'],
                'pandit'
            );

            if ($moderation['flagged']) {
                $this->reviewModel->updateModerationStatus(
                    $result['review_id'],
                    true,
                    $moderation['reason']
                );
            } else {
                // Auto-approve if not flagged
                $this->reviewModel->updateModerationStatus($result['review_id'], false);
            }
        } else {
            // Auto-approve reviews without text
            $this->reviewModel->updateModerationStatus($result['review_id'], false);
        }

        $this->json([
            'success' => true,
            'message' => $result['message'],
            'redirect' => '/user/bookings',
        ]);
    }

    /**
     * Show review form for a Vendor (after delivered order)
     */
    public function reviewVendorForm(int $orderId): void
    {
        $userId = Auth::id();
        
        // Check eligibility
        $eligibility = $this->reviewModel->canReviewVendor($userId, $orderId);
        
        if (!$eligibility['valid']) {
            $this->redirect('/user/orders', ['error' => $eligibility['error']]);
            return;
        }
        
        $order = $eligibility['order'];
        
        $this->viewWithLayout('user/review-vendor-form', 'layouts/user', [
            'title' => 'Review Vendor - Sanskar AI',
            'order' => $order,
        ]);
    }

    /**
     * Submit Vendor review
     */
    public function submitVendorReview(): void
    {
        if (!$this->verifyCsrf()) {
            $this->json(['success' => false, 'error' => 'Invalid security token.'], 403);
            return;
        }

        $userId = Auth::id();
        $orderId = (int)$this->input('order_id');
        
        if (!$orderId) {
            $this->json(['success' => false, 'error' => 'Invalid order.'], 400);
            return;
        }

        // Collect review data
        $data = [
            'rating_overall' => $this->input('rating_overall'),
            'item_quality' => $this->input('item_quality'),
            'delivery_time' => $this->input('delivery_time'),
            'packaging' => $this->input('packaging'),
            'value_for_money' => $this->input('value_for_money'),
            'review_text' => $this->input('review_text'),
        ];

        // Create review
        $result = $this->reviewModel->createVendorReview($userId, $orderId, $data);

        if (!$result['success']) {
            if (isset($result['errors'])) {
                $this->json(['success' => false, 'errors' => $result['errors']], 400);
            } else {
                $this->json(['success' => false, 'error' => $result['error']], 400);
            }
            return;
        }

        // Run AI moderation
        if (!empty($data['review_text'])) {
            $moderation = $this->aiService->moderateReview(
                $data['review_text'],
                (int)$data['rating_overall'],
                'vendor'
            );

            if ($moderation['flagged']) {
                $this->reviewModel->updateModerationStatus(
                    $result['review_id'],
                    true,
                    $moderation['reason']
                );
            } else {
                // Auto-approve if not flagged
                $this->reviewModel->updateModerationStatus($result['review_id'], false);
            }
        } else {
            // Auto-approve reviews without text
            $this->reviewModel->updateModerationStatus($result['review_id'], false);
        }

        $this->json([
            'success' => true,
            'message' => $result['message'],
            'redirect' => '/user/orders',
        ]);
    }

    /**
     * Get user's submitted reviews
     */
    public function myReviews(): void
    {
        $userId = Auth::id();
        $reviews = $this->reviewModel->getUserReviews($userId);
        
        $this->viewWithLayout('user/my-reviews', 'layouts/user', [
            'title' => 'My Reviews - Sanskar AI',
            'reviews' => $reviews,
        ]);
    }

    /**
     * Get pending review notifications for current user
     */
    public function reviewNotifications(): void
    {
        $userId = Auth::id();
        $notifications = $this->reviewModel->getPendingNotifications($userId);
        
        $this->json([
            'success' => true,
            'notifications' => $notifications,
            'count' => count($notifications),
        ]);
    }

    // ============================================================
    // MOHURAT REQUESTS
    // ============================================================

    /**
     * Show mohurat request form
     */
    public function requestMohuratForm(): void
    {
        $userId = Auth::id();
        $families = $this->familyModel->getByUserId($userId);
        $pandits = (new User())->getApprovedPandits();

        $this->viewWithLayout('user/mohurat-request-form', 'layouts/user', [
            'title' => 'Request Muhurat',
            'families' => $families,
            'pandits' => $pandits,
        ]);
    }

    /**
     * Submit mohurat request
     */
    public function submitMohuratRequest(): void
    {
        if (!$this->verifyCsrf()) {
            $this->back(['error' => 'Invalid token.']);
            return;
        }

        $data = $this->only([
            'ritual_type', 'country', 'city', 'preferred_month',
            'gotra', 'nakshatra', 'time_preference', 'additional_notes', 'family_id', 'pandit_id'
        ]);

        if (empty($data['ritual_type'])) {
            $this->back(['error' => 'Ritual type is required.']);
            return;
        }

        if (empty($data['pandit_id'])) {
            $this->back(['error' => 'Please select a pandit.']);
            return;
        }

        $data['user_id'] = Auth::id();
        $data['status'] = 'pending';

        // If family selected, auto-fill gotra/nakshatra if not provided
        if (!empty($data['family_id'])) {
            $family = $this->familyModel->find((int) $data['family_id']);
            if ($family && $family['user_id'] == Auth::id()) {
                if (empty($data['gotra'])) $data['gotra'] = $family['gotra'] ?? null;
                if (empty($data['nakshatra'])) $data['nakshatra'] = $family['nakshatra'] ?? null;
            }
        }

        $mohuratModel = new MohuratRequest();
        $mohuratModel->create($data);

        $this->redirect('/user/mohurat-requests', ['success' => 'Muhurat request submitted! Pandits will respond with auspicious timings.']);
    }

    /**
     * List user's mohurat requests
     */
    public function mohuratRequests(): void
    {
        $mohuratModel = new MohuratRequest();
        $requests = $mohuratModel->getForUser(Auth::id());

        $this->viewWithLayout('user/mohurat-requests', 'layouts/user', [
            'title' => 'My Muhurat Requests',
            'requests' => $requests,
        ]);
    }

    /**
     * Accept a mohurat reply - creates a booking
     */
    public function acceptMohuratReply(string $id): void
    {
        if (!$this->verifyCsrf()) {
            $this->back(['error' => 'Invalid token.']);
            return;
        }

        $mohuratModel = new MohuratRequest();
        $request = $mohuratModel->find((int) $id);

        if (!$request || (int)$request['user_id'] !== Auth::id() || $request['status'] !== 'replied') {
            $this->back(['error' => 'Invalid request.']);
            return;
        }

        // Create a pandit assignment (booking)
        $assignmentData = [
            'pandit_id' => $request['replied_by'],
            'user_id' => Auth::id(),
            'scheduled_date' => $request['reply_date'],
            'scheduled_time' => $request['reply_time'],
            'booking_purpose' => 'Muhurat: ' . $request['ritual_type'],
            'user_notes' => $request['additional_notes'],
            'amount' => $request['consultation_fee'],
            'status' => 'confirmed',
        ];

        $assignmentId = $this->assignmentModel->create($assignmentData);

        // Update mohurat request
        $mohuratModel->accept((int) $id, $assignmentId);

        $this->redirect('/user/mohurat-requests', ['success' => 'Muhurat accepted! Booking has been created. Check your bookings for details.']);
    }

    /**
     * Decline a mohurat reply
     */
    public function declineMohuratReply(string $id): void
    {
        if (!$this->verifyCsrf()) {
            $this->back(['error' => 'Invalid token.']);
            return;
        }

        $mohuratModel = new MohuratRequest();
        $request = $mohuratModel->find((int) $id);

        if (!$request || (int)$request['user_id'] !== Auth::id() || $request['status'] !== 'replied') {
            $this->back(['error' => 'Invalid request.']);
            return;
        }

        $mohuratModel->decline((int) $id);
        $this->redirect('/user/mohurat-requests', ['success' => 'Muhurat reply declined.']);
    }

    // ============================================================
    // PLAN FESTIVAL RITUAL (AI-generated from community festivals)
    // ============================================================

    /**
     * Generate an AI ritual for a festival and save to My Rituals
     */
    public function planFestivalRitual(): void
    {
        if (!$this->verifyCsrf()) {
            $this->back(['error' => 'Invalid token.']);
            return;
        }

        $festivalName = trim($this->input('festival_name', ''));
        if (empty($festivalName)) {
            $this->back(['error' => 'Festival name is required.']);
            return;
        }

        $userId = Auth::id();
        $user = (new User())->find($userId);
        $communityName = trim($user['community_name'] ?? 'Hindu');

        // Build criteria for AI generation
        $criteria = [
            'ritual_name' => $festivalName,
            'community_name' => $communityName,
            'religion' => 'Hinduism',
            'occasion' => $festivalName,
            'additional_info' => "This is a {$communityName} tradition. Generate a detailed, authentic ritual guide for {$festivalName} as practiced in the {$communityName} community.",
        ];

        // Generate via AI
        $result = $this->aiService->generateRitual($userId, $criteria);

        if (!$result['success']) {
            $this->redirect('/user/dashboard', ['error' => 'AI could not generate the ritual. Please try again later.']);
            return;
        }

        // Save to My Rituals
        $prompt = "Festival: {$festivalName} | Community: {$communityName}";
        $userRitualId = $this->userRitualModel->createFromAI($userId, $result['ritual'], $prompt);

        $this->redirect("/user/my-rituals/{$userRitualId}", [
            'success' => "'{$festivalName}' ritual has been generated and saved to your collection!"
        ]);
    }

    // ============================================================
    // AI PANDIT - Conversational Hindu Pandit Chatbot
    // ============================================================

    /**
     * Show AI Pandit chat page
     */
    public function aiPandit(): void
    {
        $userId = Auth::id();

        // Check subscription status
        $hasSubscription = $this->subscriptionModel->hasActiveSubscription($userId);
        $activeSubscription = $this->subscriptionModel->getUserActiveSubscription($userId);
        $daysRemaining = $this->subscriptionModel->getDaysRemaining($userId);

        // If no active subscription, redirect to plans page
        if (!$hasSubscription) {
            $this->redirect('/user/subscription/plans', [
                'warning' => 'Please subscribe to access AI Pandit. Choose a plan that suits you!'
            ]);
            return;
        }

        $sessions = $this->panditChatModel->getUserSessions($userId, 20);

        // Get user data for initials
        $userModel = new User();
        $user = $userModel->find($userId);

        $this->viewWithLayout('user/ai-pandit', 'layouts/user', [
            'title' => 'AI Pandit',
            'sessions' => $sessions,
            'subscription' => $activeSubscription,
            'daysRemaining' => $daysRemaining,
            'user' => $user,
        ]);
    }

    /**
     * Send message to AI Pandit and get response (AJAX)
     */
    public function aiPanditSend(): void
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

            // Check subscription status
            if (!$this->subscriptionModel->hasActiveSubscription($userId)) {
                ob_end_clean();
                $this->json([
                    'success' => false,
                    'error' => 'Your subscription has expired. Please renew to continue using AI Pandit.',
                    'subscription_required' => true
                ], 403);
                return;
            }

            $message = trim($this->input('message', ''));
            $sessionId = $this->input('session_id') ? (int) $this->input('session_id') : null;

            if (empty($message)) {
                ob_end_clean();
                $this->json(['success' => false, 'error' => 'Message cannot be empty.'], 400);
                return;
            }

            // Create new session if needed
            if (!$sessionId) {
                $sessionTitle = mb_substr($message, 0, 80);
                $sessionId = $this->panditChatModel->createSession($userId, $sessionTitle);
            } else {
                // Verify ownership
                $session = $this->panditChatModel->getSession($sessionId, $userId);
                if (!$session) {
                    ob_end_clean();
                    $this->json(['success' => false, 'error' => 'Session not found.'], 404);
                    return;
                }
            }

            // Save user message
            $this->panditChatModel->addMessage($sessionId, 'user', $message);

            // Get full message history for context
            $messages = $this->panditChatModel->getMessages($sessionId);
            $messageHistory = array_map(function ($msg) {
                return ['role' => $msg['role'], 'content' => $msg['content']];
            }, $messages);

            // Gather user details for personalization
            $session = $this->panditChatModel->getSession($sessionId, $userId);
            $userDetails = !empty($session['user_details']) ? json_decode($session['user_details'], true) : [];

            // Also pull from user profile
            $userModel = new User();
            $user = $userModel->find($userId);
            if ($user) {
                if (empty($userDetails['name']) && !empty($user['name'])) {
                    $userDetails['name'] = $user['name'];
                }
                if (empty($userDetails['community']) && !empty($user['community_name'])) {
                    $userDetails['community'] = $user['community_name'];
                }
                if (empty($userDetails['religion']) && !empty($user['religion'])) {
                    $userDetails['religion'] = $user['religion'];
                }
            }

            // Call AI Pandit
            $result = $this->aiService->panditChat($userId, $messageHistory, $userDetails);

            if (!$result['success']) {
                ob_end_clean();
                $this->json(['success' => false, 'error' => $result['error']], 500);
                return;
            }

            // Save AI response
            $this->panditChatModel->addMessage($sessionId, 'assistant', $result['answer'], $result['tokens'] ?? 0);

            // Update session title from first user message if it's a new conversation
            $messageCount = $this->panditChatModel->getMessageCount($sessionId);
            if ($messageCount <= 2) {
                $title = mb_substr($message, 0, 80);
                $this->panditChatModel->updateSessionTitle($sessionId, $title);
            }

            ob_end_clean();
            $this->json([
                'success' => true,
                'session_id' => $sessionId,
                'answer' => $result['answer'],
                'tokens' => $result['tokens'] ?? 0,
            ]);

        } catch (\Exception $e) {
            ob_end_clean();
            error_log('aiPanditSend error: ' . $e->getMessage());
            $this->json(['success' => false, 'error' => 'Something went wrong. Please try again.'], 500);
        }
    }

    /**
     * Get chat history (list of sessions) via AJAX
     */
    public function aiPanditHistory(): void
    {
        ob_start();
        try {
            $userId = Auth::id();
            $sessions = $this->panditChatModel->getUserSessions($userId, 50);

            ob_end_clean();
            $this->json(['success' => true, 'sessions' => $sessions]);
        } catch (\Exception $e) {
            ob_end_clean();
            $this->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Load a specific chat session with all messages (AJAX)
     */
    public function aiPanditSession(string $id): void
    {
        ob_start();
        try {
            $userId = Auth::id();
            $session = $this->panditChatModel->getSession((int) $id, $userId);

            if (!$session) {
                ob_end_clean();
                $this->json(['success' => false, 'error' => 'Session not found.'], 404);
                return;
            }

            $messages = $this->panditChatModel->getMessages((int) $id);

            ob_end_clean();
            $this->json([
                'success' => true,
                'session' => $session,
                'messages' => $messages,
            ]);
        } catch (\Exception $e) {
            ob_end_clean();
            $this->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Delete a chat session (AJAX)
     */
    public function aiPanditDeleteSession(string $id): void
    {
        ob_start();
        try {
            if (!$this->verifyCsrf()) {
                ob_end_clean();
                $this->json(['success' => false, 'error' => 'Invalid token.'], 400);
                return;
            }

            $userId = Auth::id();
            $deleted = $this->panditChatModel->deleteSession((int) $id, $userId);

            ob_end_clean();
            if ($deleted) {
                $this->json(['success' => true, 'message' => 'Chat deleted successfully.']);
            } else {
                $this->json(['success' => false, 'error' => 'Session not found or already deleted.'], 404);
            }
        } catch (\Exception $e) {
            ob_end_clean();
            $this->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    // ============================================================
    // SUBSCRIPTION MANAGEMENT - AI Pandit Subscription Plans
    // ============================================================

    /**
     * Show subscription plans page
     */
    public function subscriptionPlans(): void
    {
        $userId = Auth::id();
        $plans = $this->subscriptionModel->getActivePlans();
        $activeSubscription = $this->subscriptionModel->getUserActiveSubscription($userId);
        $daysRemaining = $this->subscriptionModel->getDaysRemaining($userId);

        $this->viewWithLayout('user/subscription-plans', 'layouts/user', [
            'title' => 'AI Pandit Subscription Plans',
            'plans' => $plans,
            'activeSubscription' => $activeSubscription,
            'daysRemaining' => $daysRemaining,
        ]);
    }

    /**
     * Initiate subscription purchase - Create Razorpay order
     */
    public function subscriptionPurchase(): void
    {
        try {
            if (!$this->verifyCsrf()) {
                $this->json(['success' => false, 'error' => 'Invalid token.'], 400);
                return;
            }

            $userId = Auth::id();
            $planId = (int) $this->input('plan_id');

            if (!$planId) {
                $this->json(['success' => false, 'error' => 'Invalid plan selected.'], 400);
                return;
            }

            $plan = $this->subscriptionModel->getPlanById($planId);
            if (!$plan) {
                $this->json(['success' => false, 'error' => 'Plan not found.'], 404);
                return;
            }

            // Get user details
            $userModel = new User();
            $user = $userModel->find($userId);

            // Create Razorpay order
            $order = $this->razorpayService->createOrder(
                $plan['price'],
                'INR',
                [
                    'plan_id' => $plan['id'],
                    'plan_name' => $plan['name'],
                    'user_id' => $userId
                ]
            );

            if (!$order) {
                $errorMsg = $this->razorpayService->getLastError() ?: 'Failed to create payment order';
                error_log('Razorpay order creation failed: ' . $errorMsg);
                $this->json(['success' => false, 'error' => $errorMsg], 500);
                return;
            }

            // Create transaction record
            $transactionId = $this->subscriptionModel->createTransaction([
                'user_id' => $userId,
                'plan_id' => $plan['id'],
                'razorpay_order_id' => $order['id'],
                'amount' => $plan['price'],
                'currency' => 'INR',
                'status' => 'created',
                'metadata' => json_encode(['plan' => $plan])
            ]);

            // Get checkout options
            $checkoutOptions = $this->razorpayService->getCheckoutOptions($order, $user, $plan);

            $this->json([
                'success' => true,
                'order_id' => $order['id'],
                'transaction_id' => $transactionId,
                'checkout_options' => $checkoutOptions
            ]);

        } catch (\Exception $e) {
            error_log('subscriptionPurchase error: ' . $e->getMessage());
            $this->json(['success' => false, 'error' => 'Something went wrong. Please try again.'], 500);
        }
    }

    /**
     * Verify payment and activate subscription
     */
    public function subscriptionVerify(): void
    {
        try {
            if (!$this->verifyCsrf()) {
                $this->json(['success' => false, 'error' => 'Invalid token.'], 400);
                return;
            }

            $userId = Auth::id();
            $orderId = $this->input('razorpay_order_id');
            $paymentId = $this->input('razorpay_payment_id');
            $signature = $this->input('razorpay_signature');

            if (!$orderId || !$paymentId || !$signature) {
                $this->json(['success' => false, 'error' => 'Missing payment details.'], 400);
                return;
            }

            // Verify signature
            if (!$this->razorpayService->verifyPaymentSignature($orderId, $paymentId, $signature)) {
                $this->json(['success' => false, 'error' => 'Payment verification failed.'], 400);
                return;
            }

            // Get transaction
            $transaction = $this->subscriptionModel->getTransactionByOrderId($orderId);
            if (!$transaction || $transaction['user_id'] != $userId) {
                $this->json(['success' => false, 'error' => 'Transaction not found.'], 404);
                return;
            }

            // Get plan details
            $plan = $this->subscriptionModel->getPlanById($transaction['plan_id']);

            // Calculate expiry date
            $expiresAt = date('Y-m-d H:i:s', strtotime("+{$plan['duration_days']} days"));

            // Create subscription
            $subscriptionId = $this->subscriptionModel->createSubscription([
                'user_id' => $userId,
                'plan_id' => $plan['id'],
                'status' => 'active',
                'starts_at' => date('Y-m-d H:i:s'),
                'expires_at' => $expiresAt
            ]);

            // Update transaction
            $this->subscriptionModel->updateTransactionByOrderId($orderId, [
                'razorpay_payment_id' => $paymentId,
                'razorpay_signature' => $signature,
                'status' => 'completed',
                'payment_method' => 'razorpay',
                'subscription_id' => $subscriptionId
            ]);

            // Send confirmation email
            $userModel = new User();
            $user = $userModel->find($userId);
            $this->sendSubscriptionEmail($user, $plan, $transaction, $expiresAt);

            $this->json([
                'success' => true,
                'message' => 'Payment successful! Your subscription is now active.',
                'subscription_id' => $subscriptionId,
                'expires_at' => $expiresAt
            ]);

        } catch (\Exception $e) {
            error_log('subscriptionVerify error: ' . $e->getMessage());
            $this->json(['success' => false, 'error' => 'Something went wrong. Please try again.'], 500);
        }
    }

    /**
     * Show subscription success page
     */
    public function subscriptionSuccess(): void
    {
        $userId = Auth::id();
        $activeSubscription = $this->subscriptionModel->getUserActiveSubscription($userId);
        $daysRemaining = $this->subscriptionModel->getDaysRemaining($userId);

        $this->viewWithLayout('user/subscription-success', 'layouts/user', [
            'title' => 'Subscription Activated!',
            'subscription' => $activeSubscription,
            'daysRemaining' => $daysRemaining,
        ]);
    }

    /**
     * Show my subscription page
     */
    public function mySubscription(): void
    {
        $userId = Auth::id();
        $activeSubscription = $this->subscriptionModel->getUserActiveSubscription($userId);
        $daysRemaining = $this->subscriptionModel->getDaysRemaining($userId);
        $subscriptionHistory = $this->subscriptionModel->getUserSubscriptionHistory($userId);
        $paymentHistory = $this->subscriptionModel->getUserPaymentHistory($userId);

        $this->viewWithLayout('user/my-subscription', 'layouts/user', [
            'title' => 'My Subscription',
            'activeSubscription' => $activeSubscription,
            'daysRemaining' => $daysRemaining,
            'subscriptionHistory' => $subscriptionHistory,
            'paymentHistory' => $paymentHistory,
        ]);
    }

    /**
     * Send subscription confirmation email
     */
    private function sendSubscriptionEmail($user, $plan, $transaction, $expiresAt): bool
    {
        $appName = $_ENV['APP_NAME'] ?? 'Sanskar AI';
        $formattedDate = date('d M Y, h:i A', strtotime($expiresAt));
        $formattedAmount = number_format($transaction['amount'], 2);

        $subject = "Payment Confirmed - {$plan['name']} Subscription Activated";

        $body = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #FF6B35, #FF8C42); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
                .content { background: #fff; padding: 30px; border: 1px solid #ddd; border-top: none; }
                .footer { background: #f9f9f9; padding: 20px; text-align: center; border: 1px solid #ddd; border-top: none; border-radius: 0 0 10px 10px; }
                .detail-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #eee; }
                .detail-label { color: #666; }
                .detail-value { font-weight: bold; }
                .success-badge { background: #28a745; color: white; padding: 5px 15px; border-radius: 20px; display: inline-block; }
                .cta-button { background: #FF6B35; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; display: inline-block; margin: 20px 0; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>Payment Successful!</h1>
                    <p>Your AI Pandit subscription is now active</p>
                </div>
                <div class='content'>
                    <p>Namaste <strong>{$user['name']}</strong>,</p>
                    <p>Thank you for subscribing to AI Pandit! Your payment has been processed successfully.</p>

                    <h3>Payment Details</h3>
                    <table width='100%' style='border-collapse: collapse;'>
                        <tr style='border-bottom: 1px solid #eee;'>
                            <td style='padding: 10px 0; color: #666;'>Transaction ID</td>
                            <td style='padding: 10px 0; text-align: right; font-weight: bold;'>{$transaction['razorpay_order_id']}</td>
                        </tr>
                        <tr style='border-bottom: 1px solid #eee;'>
                            <td style='padding: 10px 0; color: #666;'>Plan</td>
                            <td style='padding: 10px 0; text-align: right; font-weight: bold;'>{$plan['name']}</td>
                        </tr>
                        <tr style='border-bottom: 1px solid #eee;'>
                            <td style='padding: 10px 0; color: #666;'>Duration</td>
                            <td style='padding: 10px 0; text-align: right; font-weight: bold;'>{$plan['duration_days']} Days</td>
                        </tr>
                        <tr style='border-bottom: 1px solid #eee;'>
                            <td style='padding: 10px 0; color: #666;'>Amount Paid</td>
                            <td style='padding: 10px 0; text-align: right; font-weight: bold; color: #28a745;'>Rs. {$formattedAmount}</td>
                        </tr>
                        <tr style='border-bottom: 1px solid #eee;'>
                            <td style='padding: 10px 0; color: #666;'>Subscription Valid Till</td>
                            <td style='padding: 10px 0; text-align: right; font-weight: bold;'>{$formattedDate}</td>
                        </tr>
                        <tr>
                            <td style='padding: 10px 0; color: #666;'>Status</td>
                            <td style='padding: 10px 0; text-align: right;'><span class='success-badge'>ACTIVE</span></td>
                        </tr>
                    </table>

                    <h3>What's Included</h3>
                    <ul>
                        <li>Unlimited AI Pandit conversations</li>
                        <li>Personalized ritual guidance</li>
                        <li>24/7 availability</li>
                        <li>Chat history saved</li>
                    </ul>

                    <p style='text-align: center;'>
                        <a href='" . ($_ENV['APP_URL'] ?? '') . "/user/ai-pandit' class='cta-button'>Start Chatting with AI Pandit</a>
                    </p>
                </div>
                <div class='footer'>
                    <p>Thank you for choosing {$appName}!</p>
                    <p style='font-size: 12px; color: #999;'>This is an automated email. Please do not reply to this email.</p>
                </div>
            </div>
        </body>
        </html>
        ";

        try {
            return $this->mailService->sendHtml($user['email'], $subject, $body);
        } catch (\Exception $e) {
            error_log('Failed to send subscription email: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Check if user has active subscription (API)
     */
    public function checkSubscription(): void
    {
        $userId = Auth::id();
        $hasSubscription = $this->subscriptionModel->hasActiveSubscription($userId);
        $activeSubscription = $this->subscriptionModel->getUserActiveSubscription($userId);
        $daysRemaining = $this->subscriptionModel->getDaysRemaining($userId);

        $this->json([
            'success' => true,
            'has_subscription' => $hasSubscription,
            'subscription' => $activeSubscription,
            'days_remaining' => $daysRemaining
        ]);
    }
}
