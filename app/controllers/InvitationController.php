<?php
/**
 * Sanskar AI - Invitation Controller
 * =====================================
 * Handles invitation card creation, management, and public viewing
 */

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\Invitation;
use App\Services\AIService;

class InvitationController extends Controller
{
    private Invitation $invitationModel;
    private AIService $aiService;

    public function __construct()
    {
        parent::__construct();
        $this->invitationModel = new Invitation();
        $this->aiService = new AIService();
    }

    /**
     * List all invitations for the current user
     */
    public function index()
    {
        $user = Auth::user();
        
        // Deactivate expired invitations
        $this->invitationModel->deactivateExpired();
        
        $invitations = $this->invitationModel->getByUser($user['id']);
        
        // Mark expired status for display
        foreach ($invitations as &$inv) {
            $inv['is_expired'] = $this->invitationModel->isExpired($inv);
        }

        $this->viewWithLayout('user/invitations', 'layouts/user', [
            'title' => 'My Invitations',
            'invitations' => $invitations,
        ]);
    }

    /**
     * Show create invitation form
     */
    public function create()
    {
        $user = Auth::user();

        $this->viewWithLayout('user/invitation-create', 'layouts/user', [
            'title' => 'Create Invitation',
            'user' => $user,
        ]);
    }

    /**
     * Store a new invitation (process form + AI generation)
     */
    public function store()
    {
        $this->verifyCsrf();

        $user = Auth::user();

        // Validate input
        $errors = $this->validate($_POST, [
            'occasion_type' => 'required',
            'occasion_title' => 'required',
            'host_name' => 'required',
            'expiry_duration' => 'required',
        ]);

        if (!empty($errors)) {
            $this->back(['error' => 'Please fill in all required fields.']);
            return;
        }

        $occasionType = trim($_POST['occasion_type']);
        $occasionTitle = trim($_POST['occasion_title']);
        $eventDate = !empty($_POST['event_date']) ? $_POST['event_date'] : null;
        $venue = trim($_POST['venue'] ?? '');
        $googleMapsLink = trim($_POST['google_maps_link'] ?? '');
        $hostName = trim($_POST['host_name']);
        $message = trim($_POST['message'] ?? '');
        $additionalDetails = trim($_POST['additional_details'] ?? '');
        $expiryDuration = (int) $_POST['expiry_duration']; // in days

        // Calculate expiry date
        $expiresAt = date('Y-m-d H:i:s', strtotime("+{$expiryDuration} days"));

        // Generate invitation card via AI
        $details = [
            'occasion_type' => $occasionType,
            'occasion_title' => $occasionTitle,
            'event_date' => $eventDate ? date('F j, Y \a\t g:i A', strtotime($eventDate)) : 'To be announced',
            'venue' => $venue ?: 'To be announced',
            'google_maps_link' => $googleMapsLink,
            'host_name' => $hostName,
            'message' => $message,
            'additional_details' => $additionalDetails,
        ];

        $result = $this->aiService->generateInvitationCard($user['id'], $details);

        if (!$result['success']) {
            $this->back(['error' => 'Failed to generate invitation: ' . ($result['error'] ?? 'Unknown error')]);
            return;
        }

        // Generate unique share token
        $shareToken = $this->invitationModel->generateShareToken();

        // Save to database
        $invitationId = $this->invitationModel->create([
            'user_id' => $user['id'],
            'share_token' => $shareToken,
            'occasion_type' => $occasionType,
            'occasion_title' => $occasionTitle,
            'event_date' => $eventDate,
            'venue' => $venue,
            'google_maps_link' => $googleMapsLink,
            'host_name' => $hostName,
            'message' => $message,
            'additional_details' => $additionalDetails,
            'generated_html' => $result['html'],
            'ai_request_id' => $result['request_id'] ?? null,
            'expires_at' => $expiresAt,
            'is_active' => 1,
        ]);

        $this->redirect("/user/invitations/{$invitationId}", [
            'success' => 'Invitation card created successfully! Share the link with your guests.',
        ]);
    }

    /**
     * Show invitation detail page
     */
    public function show($id)
    {
        $user = Auth::user();
        $invitation = $this->invitationModel->find((int) $id);

        if (!$invitation || $invitation['user_id'] !== $user['id']) {
            $this->redirect('/user/invitations', ['error' => 'Invitation not found.']);
            return;
        }

        $invitation['is_expired'] = $this->invitationModel->isExpired($invitation);

        // Build share URL
        $appUrl = rtrim(getenv('APP_URL') ?: 'http://localhost:8000', '/');
        $shareUrl = $appUrl . '/invitation/' . $invitation['share_token'];

        $this->viewWithLayout('user/invitation-detail', 'layouts/user', [
            'title' => 'Invitation Details',
            'invitation' => $invitation,
            'shareUrl' => $shareUrl,
        ]);
    }

    /**
     * Delete (deactivate) an invitation
     */
    public function delete($id)
    {
        $this->verifyCsrf();

        $user = Auth::user();
        $invitation = $this->invitationModel->find((int) $id);

        if (!$invitation || $invitation['user_id'] !== $user['id']) {
            $this->redirect('/user/invitations', ['error' => 'Invitation not found.']);
            return;
        }

        $this->invitationModel->delete((int) $id);

        $this->redirect('/user/invitations', [
            'success' => 'Invitation deleted successfully.',
        ]);
    }

    /**
     * Public invitation view (no auth required)
     * Shows a name prompt, then renders the personalized invitation
     */
    public function viewPublic($token)
    {
        $invitation = $this->invitationModel->findByToken($token);

        if (!$invitation || !$invitation['is_active']) {
            $this->view('invitation-view', [
                'expired' => true,
                'invitation' => null,
            ]);
            return;
        }

        // Check if expired
        if ($this->invitationModel->isExpired($invitation)) {
            // Deactivate it
            $this->invitationModel->update($invitation['id'], ['is_active' => 0]);

            $this->view('invitation-view', [
                'expired' => true,
                'invitation' => null,
            ]);
            return;
        }

        // Increment view count
        $this->invitationModel->incrementViewCount($invitation['id']);

        $this->view('invitation-view', [
            'expired' => false,
            'invitation' => $invitation,
        ]);
    }
}
