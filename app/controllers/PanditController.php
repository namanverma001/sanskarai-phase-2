<?php
/**
 * Sanskar AI - Pandit Controller
 * ================================
 * Dashboard, assignments, Q&A, ritual management
 */

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\PanditProfile;
use App\Models\Assignment;
use App\Models\Ritual;
use App\Models\CustomRitual;
use App\Models\MohuratRequest;
use App\Config\Database;

class PanditController extends Controller
{
    private PanditProfile $profileModel;
    private Assignment $assignmentModel;
    private Ritual $ritualModel;
    private CustomRitual $customRitualModel;

    public function __construct()
    {
        parent::__construct();
        $this->profileModel = new PanditProfile();
        $this->assignmentModel = new Assignment();
        $this->ritualModel = new Ritual();
        $this->customRitualModel = new CustomRitual();
    }

    public function dashboard(): void
    {
        $userId = Auth::id();
        $profile = $this->profileModel->getFullProfile($userId);

        // If pandit is NOT approved, show appropriate page based on status
        if (!$profile || $profile['approval_status'] !== 'approved') {
            $status = $profile['approval_status'] ?? 'pending';

            if ($status === 'rejected') {
                // Profile was rejected by admin
                $this->viewWithLayout('pandit/pending-approval', 'layouts/pandit', [
                    'title' => 'Profile Rejected - Sanskar AI',
                    'profile' => $profile,
                    'showCongrats' => false,
                    'showRejected' => true,
                ]);
            } else {
                // Profile is pending review
                $this->viewWithLayout('pandit/pending-approval', 'layouts/pandit', [
                    'title' => 'Profile Under Review - Sanskar AI',
                    'profile' => $profile,
                    'showCongrats' => false,
                    'showRejected' => false,
                ]);
            }
            return;
        }

        // ── First-login-after-approval congratulations logic ──
        // Dismiss congrats only when pandit explicitly clicks "Continue to Dashboard"
        if (isset($_GET['welcomed']) && $_GET['welcomed'] === '1') {
            $_SESSION['congrats_dismissed'] = true;
        }

        // Show congrats if: pandit is approved, hasn't dismissed it, and this is first login after approval
        if (empty($_SESSION['congrats_dismissed'])) {
            $user = Auth::fullUser();
            $approvedAt = $profile['approved_at'] ?? null;
            $lastLogin = $user['last_login_at'] ?? null;

            // First login after approval: last_login is null OR last_login was before/equal to approved_at
            // (last_login gets updated at login time, so within a few seconds of approved_at means first login)
            if ($approvedAt && (!$lastLogin || strtotime($lastLogin) <= strtotime($approvedAt) + 60)) {
                $this->viewWithLayout('pandit/pending-approval', 'layouts/pandit', [
                    'title' => 'Account Approved - Sanskar AI',
                    'profile' => $profile,
                    'showCongrats' => true,
                ]);
                return;
            }
        }

        $stats = [
            'pending' => count($this->assignmentModel->getForPandit($userId, 'pending')),
            'confirmed' => count($this->assignmentModel->getForPandit($userId, 'confirmed')),
            'completed' => count($this->assignmentModel->getForPandit($userId, 'completed')),
        ];

        // Get both pending and confirmed assignments for upcoming section
        $pendingAssignments = $this->assignmentModel->getForPandit($userId, 'pending');
        $confirmedAssignments = $this->assignmentModel->getForPandit($userId, 'confirmed');
        $upcomingAssignments = array_merge($pendingAssignments, $confirmedAssignments);
        // Sort by scheduled date
        usort($upcomingAssignments, function ($a, $b) {
            return strtotime($a['scheduled_date'] ?? '9999-12-31') - strtotime($b['scheduled_date'] ?? '9999-12-31');
        });

        $this->viewWithLayout('pandit/dashboard', 'layouts/pandit', [
            'title' => 'Pandit Dashboard',
            'profile' => $profile,
            'stats' => $stats,
            'upcomingAssignments' => array_slice($upcomingAssignments, 0, 5),
        ]);
    }

    public function profile(): void
    {
        $profile = $this->profileModel->getFullProfile(Auth::id());
        $this->viewWithLayout('pandit/profile', 'layouts/pandit', [
            'title' => 'My Profile',
            'profile' => $profile,
        ]);
    }

    public function updateProfile(): void
    {
        if (!$this->verifyCsrf()) {
            $this->back(['error' => 'Invalid token.']);
            return;
        }
        $profile = $this->profileModel->getByUserId(Auth::id());
        if (!$profile) {
            $this->back(['error' => 'Profile not found.']);
            return;
        }
        $data = $this->only(['specialization', 'experience_years', 'bio', 'languages', 'hourly_rate', 'latitude', 'longitude', 'city', 'pincode', 'service_area_km', 'map_url']);
        // Format decimal and integer data
        if ($data['latitude'] === '') $data['latitude'] = null;
        if ($data['longitude'] === '') $data['longitude'] = null;
        if ($data['service_area_km'] === '') $data['service_area_km'] = null;
        if ($data['hourly_rate'] === '') $data['hourly_rate'] = null;
        if ($data['experience_years'] === '') $data['experience_years'] = null;
        // Extract lat/lng from map_url if they are not provided, or if map_url has changed
        if (!empty($data['map_url']) && (
            empty($data['latitude']) || 
            empty($data['longitude']) || 
            $data['map_url'] !== ($profile['map_url'] ?? '')
        )) {
            $coords = $this->extractCoordinatesFromMapUrl($data['map_url']);
            if ($coords) {
                $data['latitude'] = $coords['latitude'];
                $data['longitude'] = $coords['longitude'];
            }
        }
        
        $this->profileModel->update($profile['id'], $data);
        $this->back(['success' => 'Profile updated.']);
    }
    
    private function extractCoordinatesFromMapUrl(string $url): ?array
    {
        // Follow redirects to get the final URL
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_NOBODY, true); // We only need headers
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_exec($ch);
        $finalUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
        curl_close($ch);
        
        $urlToParse = $finalUrl ?: $url;

        // Try to match @lat,lng
        if (preg_match('/@(-?\d+\.\d+),(-?\d+\.\d+)/', $urlToParse, $matches)) {
            return [
                'latitude' => $matches[1],
                'longitude' => $matches[2]
            ];
        }

        // Try to match query parameter q=lat,lng
        if (preg_match('/q=(-?\d+\.\d+),(-?\d+\.\d+)/', $urlToParse, $matches)) {
            return [
                'latitude' => $matches[1],
                'longitude' => $matches[2]
            ];
        }

        // Match 3dLAT!4dLNG format
        if (preg_match('/3d(-?\d+\.\d+)!4d(-?\d+\.\d+)/', $urlToParse, $matches)) {
            return [
                'latitude' => $matches[1],
                'longitude' => $matches[2]
            ];
        }

        return null;
    }

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

        if (strlen($newPassword) < 8) {
            $this->back(['error' => 'Password must be at least 8 characters long.']);
            return;
        }

        // Verify current password
        $user = Auth::user(); // Get session user
        // Need full user for password hash which is hidden in session usually? 
        // Auth::user() returns session data. Auth::fullUser() hits DB.
        $fullUser = Auth::fullUser();

        if (!Auth::verifyPassword($currentPassword, $fullUser['password_hash'])) {
            $this->back(['error' => 'Incorrect current password.']);
            return;
        }

        // Update password
        $newHash = Auth::hashPassword($newPassword);

        // Use Database class directly or User model if available
        // $this->userModel is not injected here directly, but we can instantiate it or use DB
        $userModel = new \App\Models\User();
        $userModel->update(Auth::id(), ['password_hash' => $newHash]);

        $this->back(['success' => 'Password updated successfully.']);
    }

    public function assignments(): void
    {
        $status = $this->input('status');
        $assignments = $this->assignmentModel->getForPandit(Auth::id(), $status);
        $this->viewWithLayout('pandit/assignments', 'layouts/pandit', [
            'title' => 'Assignments',
            'assignments' => $assignments,
        ]);
    }

    /**
     * View booking requests (assignments with booking purposes)
     */
    public function bookingRequests(): void
    {
        $status = $this->input('status');
        $assignments = $this->assignmentModel->getForPandit(Auth::id(), $status);
        $this->viewWithLayout('pandit/booking-requests', 'layouts/pandit', [
            'title' => 'Booking Requests',
            'bookings' => $assignments,
        ]);
    }

    public function confirmAssignment(string $id): void
    {
        if (!$this->verifyCsrf()) {
            $this->back(['error' => 'Invalid token.']);
            return;
        }
        $this->assignmentModel->confirm((int) $id);
        $this->back(['success' => 'Assignment confirmed.']);
    }

    public function rejectAssignment(string $id): void
    {
        if (!$this->verifyCsrf()) {
            $this->back(['error' => 'Invalid token.']);
            return;
        }
        
        $assignment = $this->assignmentModel->find((int) $id);
        if (!$assignment || (int)$assignment['pandit_id'] !== Auth::id()) {
            $this->back(['error' => 'Assignment not found.']);
            return;
        }
        
        if ($assignment['status'] !== 'pending') {
            $this->back(['error' => 'Can only reject pending bookings.']);
            return;
        }
        
        $reason = $this->input('rejection_reason');
        $this->assignmentModel->update((int) $id, [
            'status' => 'rejected',
            'rejection_reason' => $reason
        ]);
        
        $this->back(['success' => 'Booking request rejected.']);
    }

    public function completeAssignment(string $id): void
    {
        if (!$this->verifyCsrf()) {
            $this->back(['error' => 'Invalid token.']);
            return;
        }
        $this->assignmentModel->complete((int) $id);
        $this->profileModel->updateRating(Auth::id());
        $this->back(['success' => 'Assignment completed.']);
    }

    public function questions(): void
    {
        $sql = "SELECT q.*, u.name as user_name FROM SAI_pandit_qna q 
                INNER JOIN SAI_users u ON q.user_id = u.id 
                WHERE q.pandit_id = :pid ORDER BY q.status ASC, q.created_at DESC";
        $questions = Database::query($sql, ['pid' => Auth::id()]);
        $this->viewWithLayout('pandit/questions', 'layouts/pandit', [
            'title' => 'Questions',
            'questions' => $questions,
        ]);
    }

    public function answerQuestion(string $id): void
    {
        if (!$this->verifyCsrf()) {
            $this->back(['error' => 'Invalid token.']);
            return;
        }
        $answer = $this->input('answer');
        Database::execute(
            "UPDATE SAI_pandit_qna SET answer=:a, status='answered', answered_at=NOW() WHERE id=:id",
            ['a' => $answer, 'id' => (int) $id]
        );
        $this->back(['success' => 'Answer submitted.']);
    }

    public function customRituals(): void
    {
        $rituals = $this->customRitualModel->getPendingValidation(Auth::id());
        $history = $this->customRitualModel->getValidationHistory(Auth::id());
        $this->viewWithLayout('pandit/custom-rituals', 'layouts/pandit', [
            'title' => 'Custom Rituals',
            'rituals' => $rituals,
            'history' => $history,
        ]);
    }

    public function validateCustomRitual(string $id): void
    {
        if (!$this->verifyCsrf()) {
            $this->back(['error' => 'Invalid token.']);
            return;
        }
        $action = $this->input('action');
        $notes = $this->input('notes', '');
        if ($action === 'approve') {
            $this->customRitualModel->approve((int) $id, Auth::id(), $notes);
            $this->back(['success' => 'Approved.']);
        } else {
            $this->customRitualModel->reject((int) $id, Auth::id(), $notes);
            $this->back(['success' => 'Rejected.']);
        }
    }

    /**
     * View Client's Ritual from Assignment
     */
    public function viewAssignmentRitual(string $id): void
    {
        // 1. Verify Assignment
        $sql = "SELECT * FROM SAI_pandit_assignments WHERE id = :id AND pandit_id = :pid";
        $assignment = Database::queryOne($sql, ['id' => $id, 'pid' => Auth::id()]);

        if (!$assignment) {
            $this->redirect('/pandit/assignments', ['error' => 'Assignment not found or access denied.']);
            return;
        }

        // 2. Find User Ritual
        // Only works for Global Rituals assignments for now
        if (!$assignment['ritual_id']) {
            $this->redirect('/pandit/assignments', ['error' => 'This assignment is not linked to a standard ritual.']);
            return;
        }

        $userRitualModel = new \App\Models\UserRitual();
        $userRitual = $userRitualModel->findByUserAndGlobal($assignment['user_id'], $assignment['ritual_id']);

        // 3. If not found, Auto-Create it!
        if (!$userRitual) {
            $userRitualId = $userRitualModel->addFromGlobal($assignment['user_id'], $assignment['ritual_id']);
            $userRitual = $userRitualModel->getWithDetails($userRitualId);
        } else {
            $userRitual = $userRitualModel->getWithDetails($userRitual['id']);
        }

        // 4. View
        $this->viewWithLayout('pandit/user-ritual-detail', 'layouts/pandit', [
            'title' => 'Manage Client Ritual',
            'ritual' => $userRitual,
            'assignment' => $assignment
        ]);
    }

    /**
     * Add Step to Client Ritual
     */
    public function addClientRitualStep(string $id): void
    {
        if (!$this->verifyCsrf()) {
            $this->json(['error' => 'Invalid token.'], 400);
            return;
        }

        // Verify Assignment
        $sql = "SELECT user_id, ritual_id FROM SAI_pandit_assignments WHERE id = :id AND pandit_id = :pid";
        $assignment = Database::queryOne($sql, ['id' => $id, 'pid' => Auth::id()]);

        if (!$assignment) {
            $this->json(['error' => 'Unauthorized'], 403);
            return;
        }

        $userRitualModel = new \App\Models\UserRitual();
        $userRitual = $userRitualModel->findByUserAndGlobal($assignment['user_id'], $assignment['ritual_id']);

        if (!$userRitual) {
            $this->json(['error' => 'Ritual not initialized'], 404);
            return;
        }

        // Logic similar to User add step
        $maxStep = 0;
        // Need to fetch details to get max step? Or just query DB.
        // Let's use getWithDetails for now or direct query if optimized.
        $details = $userRitualModel->getWithDetails($userRitual['id']);
        foreach ($details['steps'] as $step) {
            if ($step['step_number'] > $maxStep)
                $maxStep = $step['step_number'];
        }

        $stepNumber = (int) $this->input('step_number', 0);
        if ($stepNumber <= 0)
            $stepNumber = $maxStep + 1;

        $data = [
            'step_number' => $stepNumber,
            'title' => $this->input('title', 'Pandit Added Step'),
            'description' => $this->input('description', ''),
            'mantra' => $this->input('mantra'),
            'duration_minutes' => (int) $this->input('duration_minutes', 5),
            'is_optional' => (int) $this->input('is_optional', 0),
        ];

        try {
            $stepId = $userRitualModel->addStep($userRitual['id'], $data);
            $this->json(['success' => true, 'step_id' => $stepId]);
        } catch (\Exception $e) {
            $this->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Update Client Ritual Step
     */
    public function updateClientRitualStep(string $id): void
    {
        // For updates, we just need to verify the step belongs to a ritual owned by a user assigned to this pandit
        // This is complex join. Simplified: Find step -> Find UserRitual -> Find User -> Check active assignment with Pandit

        $userRitualModel = new \App\Models\UserRitual();
        $sql = "SELECT s.user_ritual_id, ur.user_id, ur.global_ritual_id 
                FROM SAI_user_ritual_steps s 
                JOIN SAI_user_rituals ur ON s.user_ritual_id = ur.id 
                WHERE s.id = :id";
        $stepInfo = Database::queryOne($sql, ['id' => $id]);

        if (!$stepInfo) {
            $this->json(['error' => 'Step not found'], 404);
            return;
        }

        // Check active assignment
        $sql = "SELECT id FROM SAI_pandit_assignments 
                WHERE pandit_id = :pid AND user_id = :uid AND ritual_id = :rid 
                AND status IN ('confirmed', 'in_progress')";
        $assignment = Database::queryOne($sql, [
            'pid' => Auth::id(),
            'uid' => $stepInfo['user_id'],
            'rid' => $stepInfo['global_ritual_id']
        ]);

        if (!$assignment) {
            $this->json(['error' => 'Unauthorized: No active assignment for this ritual'], 403);
            return;
        }

        $data = $this->only(['title', 'description', 'mantra', 'mantra_meaning', 'duration_minutes', 'special_instructions']);
        $userRitualModel->updateStep((int) $id, $data);
        $this->json(['success' => true]);
    }

    /**
     * Delete Client Ritual Step
     */
    public function deleteClientRitualStep(string $id): void
    {
        // Same auth logic as update
        $userRitualModel = new \App\Models\UserRitual();
        $sql = "SELECT s.user_ritual_id, ur.user_id, ur.global_ritual_id 
                FROM SAI_user_ritual_steps s 
                JOIN SAI_user_rituals ur ON s.user_ritual_id = ur.id 
                WHERE s.id = :id";
        $stepInfo = Database::queryOne($sql, ['id' => $id]);

        if (!$stepInfo) {
            $this->json(['error' => 'Step not found'], 404);
            return;
        }

        $sql = "SELECT id FROM SAI_pandit_assignments 
                WHERE pandit_id = :pid AND user_id = :uid AND ritual_id = :rid 
                AND status IN ('confirmed', 'in_progress')";
        $assignment = Database::queryOne($sql, [
            'pid' => Auth::id(),
            'uid' => $stepInfo['user_id'],
            'rid' => $stepInfo['global_ritual_id']
        ]);

        if (!$assignment) {
            $this->json(['error' => 'Unauthorized'], 403);
            return;
        }

        $userRitualModel->deleteStep((int) $id);
        $this->json(['success' => true]);
    }

    // ============================================================
    // MOHURAT REQUESTS
    // ============================================================

    /**
     * View mohurat requests
     */
    public function mohuratRequests(): void
    {
        $mohuratModel = new MohuratRequest();
        $requests = $mohuratModel->getForPandit(Auth::id());

        $this->viewWithLayout('pandit/mohurat-requests', 'layouts/pandit', [
            'title' => 'Muhurat Requests',
            'requests' => $requests,
        ]);
    }

    /**
     * Reply to a mohurat request
     */
    public function replyMohuratRequest(string $id): void
    {
        if (!$this->verifyCsrf()) {
            $this->back(['error' => 'Invalid token.']);
            return;
        }

        $mohuratModel = new MohuratRequest();
        $request = $mohuratModel->find((int) $id);

        if (!$request || $request['status'] !== 'pending') {
            $this->back(['error' => 'Request not found or already replied.']);
            return;
        }

        $data = [
            'reply_date' => $this->input('reply_date'),
            'reply_time' => $this->input('reply_time'),
            'reply_explanation' => $this->input('reply_explanation'),
            'consultation_fee' => $this->input('consultation_fee') ?: null,
        ];

        if (empty($data['reply_date']) || empty($data['reply_time']) || empty($data['reply_explanation'])) {
            $this->back(['error' => 'Date, time, and explanation are required.']);
            return;
        }

        $mohuratModel->reply((int) $id, Auth::id(), $data);
        $this->redirect('/pandit/mohurat-requests', ['success' => 'Muhurat reply sent successfully!']);
    }
}
