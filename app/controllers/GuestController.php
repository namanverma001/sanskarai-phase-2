<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\Ritual;
use App\Models\User;
use App\Models\GuestTracker;
use App\Services\AIService;

class GuestController extends Controller
{
    private Ritual $ritualModel;
    private AIService $aiService;
    private GuestTracker $guestTracker;
    private const MAX_AI_GENERATIONS = 3;
    private const MAX_AI_CHAT_MESSAGES = 5;

    public function __construct()
    {
        parent::__construct();
        $this->ritualModel = new Ritual();
        $this->aiService = new AIService();
        $this->guestTracker = new GuestTracker();
    }

    private function initGuestSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['guest_ai_gen_count'])) $_SESSION['guest_ai_gen_count'] = 0;
        if (!isset($_SESSION['guest_ai_chat_count'])) $_SESSION['guest_ai_chat_count'] = 0;
    }

    public function exploreRituals(): void
    {
        if (Auth::check() && Auth::isUser()) { $this->redirect('/user/rituals'); return; }

        $categories = $this->ritualModel->getCategories();
        $popularRituals = $this->ritualModel->getPopular(3);
        $totalRitualCount = $this->ritualModel->count(['is_active' => 1]);
        $userModel = new User();
        $topCommunities = $userModel->getTopCommunities(6);
        $topRitualNames = $this->ritualModel->getTopRitualNames(15);

        $this->initGuestSession();
        $this->guestTracker->recordView('/explore');
        $aiGenRemaining = max(0, self::MAX_AI_GENERATIONS - ($_SESSION['guest_ai_gen_count'] ?? 0));

        $this->viewWithLayout('user/explore-rituals', 'layouts/guest', [
            'title' => 'Explore Rituals - Sanskar AI',
            'isGuest' => true,
            'categories' => $categories,
            'popularRituals' => $popularRituals,
            'totalRitualCount' => $totalRitualCount,
            'topCommunities' => $topCommunities,
            'topRitualNames' => $topRitualNames,
            'userCommunity' => '',
            'myRituals' => [],
            'aiGenRemaining' => $aiGenRemaining,
            'maxAiGenerations' => self::MAX_AI_GENERATIONS,
        ]);
    }

    public function searchRituals(): void
    {
        ob_start(); error_reporting(0);
        try {
            $criteria = [
                'community_name' => $this->input('community_name'),
                'religion' => $this->input('religion'),
                'ritual_name' => $this->input('ritual_name'),
                'category' => $this->input('category'),
            ];
            $this->initGuestSession();
            $this->guestTracker->recordSearch($criteria);
            
            $globalRituals = $this->ritualModel->advancedSearch($criteria);
            foreach ($globalRituals as &$r) $r['source_type'] = 'global';
            unset($r);
            ob_end_clean();
            $this->json(['success' => true, 'count' => count($globalRituals), 'rituals' => $globalRituals, 'criteria' => $criteria, 'my_rituals_count' => 0, 'global_count' => count($globalRituals)]);
        } catch (\Exception $e) { ob_end_clean(); $this->json(['success' => false, 'error' => $e->getMessage()], 500); }
    }

    public function loadMoreRituals(): void
    {
        ob_start(); error_reporting(0);
        try {
            if (!$this->verifyCsrf()) { ob_end_clean(); $this->json(['success' => false, 'error' => 'Invalid token.'], 400); return; }
            $offset = (int) $this->input('offset', 0);
            $limit = min((int) $this->input('limit', 6), 20);
            $rituals = $this->ritualModel->getActiveRituals($limit, $offset);
            $totalCount = $this->ritualModel->count(['is_active' => 1]);
            ob_end_clean();
            $this->json(['success' => true, 'rituals' => $rituals, 'total' => $totalCount, 'offset' => $offset, 'limit' => $limit]);
        } catch (\Exception $e) { ob_end_clean(); $this->json(['success' => false, 'error' => $e->getMessage()], 500); }
    }

    public function viewRitual(string $id): void
    {
        if (Auth::check() && Auth::isUser()) { $this->redirect('/user/rituals/' . $id); return; }
        $ritual = $this->ritualModel->getWithDetails((int) $id);
        if (!$ritual) { $this->redirect('/explore', ['error' => 'Ritual not found.']); return; }
        $this->ritualModel->incrementView((int) $id);
        
        $this->initGuestSession();
        $this->guestTracker->recordView('/explore/rituals/' . $id . ' (' . $ritual['name'] . ')');
        
        $this->viewWithLayout('user/ritual-detail', 'layouts/guest', [
            'title' => $ritual['name'] . ' - Sanskar AI',
            'ritual' => $ritual,
            'isGuest' => true,
        ]);
    }

    public function generateRitual(): void
    {
        ob_start(); error_reporting(0);
        try {
            if (!$this->verifyCsrf()) { ob_end_clean(); $this->json(['success' => false, 'error' => 'Invalid token.'], 400); return; }
            $this->initGuestSession();
            $genCount = $_SESSION['guest_ai_gen_count'] ?? 0;
            if ($genCount >= self::MAX_AI_GENERATIONS) {
                ob_end_clean();
                $this->json(['success' => false, 'error' => 'You\'ve used all your free AI generations! Create a free account for unlimited access.', 'limit_reached' => true], 429);
                return;
            }
            $criteria = [
                'community_name' => $this->input('community_name', ''),
                'religion' => $this->input('religion', 'Hinduism'),
                'ritual_name' => $this->input('ritual_name', ''),
                'occasion' => $this->input('occasion', ''),
                'additional_info' => $this->input('additional_info', ''),
            ];
            $result = $this->aiService->generateRitual(null, $criteria);
            if (!$result['success']) { ob_end_clean(); $this->json(['success' => false, 'error' => $result['error']], 500); return; }
            $_SESSION['guest_ai_gen_count'] = $genCount + 1;
            $remaining = max(0, self::MAX_AI_GENERATIONS - $genCount - 1);
            ob_end_clean();
            $this->json(['success' => true, 'ritual' => $result['ritual'], 'remaining' => $remaining, 'cta' => 'Create your free profile to save this ritual!']);
        } catch (\Exception $e) { ob_end_clean(); $this->json(['success' => false, 'error' => $e->getMessage()], 500); }
    }

    public function regenerateRitual(): void
    {
        ob_start(); error_reporting(0);
        try {
            if (!$this->verifyCsrf()) { ob_end_clean(); $this->json(['success' => false, 'error' => 'Invalid token.'], 400); return; }
            $this->initGuestSession();
            $genCount = $_SESSION['guest_ai_gen_count'] ?? 0;
            if ($genCount >= self::MAX_AI_GENERATIONS) {
                ob_end_clean();
                $this->json(['success' => false, 'error' => 'Free AI generations used up! Sign up for unlimited.', 'limit_reached' => true], 429);
                return;
            }
            $criteria = [
                'community_name' => $this->input('community_name', ''),
                'religion' => $this->input('religion', 'Hinduism'),
                'ritual_name' => $this->input('ritual_name', ''),
                'occasion' => $this->input('occasion', ''),
                'additional_info' => $this->input('additional_info', ''),
            ];
            $previousResponse = json_decode($this->input('previous_response', ''), true) ?? [];
            $userFeedback = $this->input('user_feedback', '');
            $sessionId = $this->input('session_id', '') ?: bin2hex(random_bytes(16));
            $roundNumber = (int) $this->input('round_number', 1);
            if (empty($userFeedback)) { ob_end_clean(); $this->json(['success' => false, 'error' => 'Please provide feedback'], 400); return; }
            $result = $this->aiService->regenerateRitualWithFeedback(null, $criteria, $previousResponse, $userFeedback, []);
            if (!$result['success']) { ob_end_clean(); $this->json(['success' => false, 'error' => $result['error']], 500); return; }
            $_SESSION['guest_ai_gen_count'] = $genCount + 1;
            $remaining = max(0, self::MAX_AI_GENERATIONS - $genCount - 1);
            ob_end_clean();
            $this->json(['success' => true, 'ritual' => $result['ritual'], 'session_id' => $sessionId, 'round_number' => $roundNumber + 1, 'remaining' => $remaining]);
        } catch (\Exception $e) { ob_end_clean(); $this->json(['success' => false, 'error' => $e->getMessage()], 500); }
    }

    public function aiPandit(): void
    {
        if (Auth::check() && Auth::isUser()) { $this->redirect('/user/ai-pandit'); return; }
        $this->initGuestSession();
        $chatRemaining = max(0, self::MAX_AI_CHAT_MESSAGES - ($_SESSION['guest_ai_chat_count'] ?? 0));
        $this->viewWithLayout('guest/guest-ai-pandit', 'layouts/guest', [
            'title' => 'Try AI Pandit - Sanskar AI',
            'isGuest' => true,
            'chatRemaining' => $chatRemaining,
            'maxChatMessages' => self::MAX_AI_CHAT_MESSAGES,
        ]);
    }

    public function aiPanditSend(): void
    {
        ob_start(); error_reporting(0);
        try {
            if (!$this->verifyCsrf()) { ob_end_clean(); $this->json(['success' => false, 'error' => 'Invalid token.'], 400); return; }
            $this->initGuestSession();
            $chatCount = $_SESSION['guest_ai_chat_count'] ?? 0;
            if ($chatCount >= self::MAX_AI_CHAT_MESSAGES) {
                ob_end_clean();
                $this->json(['success' => false, 'error' => 'Free messages used up! Create an account to continue.', 'limit_reached' => true], 429);
                return;
            }
            $message = trim($this->input('message', ''));
            if (empty($message)) { ob_end_clean(); $this->json(['success' => false, 'error' => 'Message cannot be empty.'], 400); return; }
            $clientHistory = $this->input('history', '');
            $messageHistory = [];
            if (!empty($clientHistory)) { $decoded = json_decode($clientHistory, true); if (is_array($decoded)) $messageHistory = $decoded; }
            $messageHistory[] = ['role' => 'user', 'content' => $message];
            
            $this->guestTracker->recordAIPandit($message);
            
            $result = $this->aiService->chatAssistant(null, [
                'ritual_name' => 'General Spiritual Guidance',
                'ritual_description' => 'Guest exploring Hindu rituals and traditions',
                'question' => $message,
                'all_steps' => [],
            ]);
            if (!$result['success']) { ob_end_clean(); $this->json(['success' => false, 'error' => $result['error']], 500); return; }
            $_SESSION['guest_ai_chat_count'] = $chatCount + 1;
            $remaining = max(0, self::MAX_AI_CHAT_MESSAGES - $chatCount - 1);
            ob_end_clean();
            $this->json(['success' => true, 'answer' => $result['answer'], 'remaining' => $remaining, 'cta' => 'Sign up to save chats & get unlimited access!']);
        } catch (\Exception $e) { ob_end_clean(); $this->json(['success' => false, 'error' => 'Something went wrong.'], 500); }
    }
}
