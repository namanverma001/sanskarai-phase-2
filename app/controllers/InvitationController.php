<?php
/**
 * Sanskar AI - Invitation Controller
 * =====================================
 * Handles invitation card creation, management, RSVP, and public viewing
 * Uses custom beautiful templates instead of AI generation
 */

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\Invitation;
use App\Models\InvitationRsvp;
use App\Models\UserRitual;

class InvitationController extends Controller
{
    private Invitation $invitationModel;
    private UserRitual $userRitualModel;
    private InvitationRsvp $rsvpModel;

    public function __construct()
    {
        parent::__construct();
        $this->invitationModel = new Invitation();
        $this->userRitualModel = new UserRitual();
        $this->rsvpModel = new InvitationRsvp();
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
        
        // Mark expired status and attach RSVP summary for display
        foreach ($invitations as &$inv) {
            $inv['is_expired'] = $this->invitationModel->isExpired($inv);
            $inv['rsvp_summary'] = $this->rsvpModel->getSummary($inv['id']);
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
        
        // Fetch user's rituals for the occasion type dropdown
        $userRituals = $this->userRitualModel->getByUser($user['id']);

        $this->viewWithLayout('user/invitation-create', 'layouts/user', [
            'title' => 'Create Invitation',
            'user' => $user,
            'userRituals' => $userRituals,
            'templates' => Invitation::TEMPLATES,
        ]);
    }

    /**
     * Store a new invitation (process form — no AI, instant save)
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
        $templateId = trim($_POST['template_id'] ?? 'royal_gold');
        $rsvpEnabled = isset($_POST['rsvp_enabled']) ? 1 : 0;

        // Validate template
        if (!isset(Invitation::TEMPLATES[$templateId])) {
            $templateId = 'royal_gold';
        }

        $themeColor = Invitation::TEMPLATES[$templateId]['accent'] ?? '#B8860B';

        // Calculate expiry date
        $expiresAt = date('Y-m-d H:i:s', strtotime("+{$expiryDuration} days"));

        // Generate unique share token
        $shareToken = $this->invitationModel->generateShareToken();

        // Save to database — no AI generation needed
        try {
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
                'template_id' => $templateId,
                'theme_color' => $themeColor,
                'rsvp_enabled' => $rsvpEnabled,
                'generated_html' => '',
                'expires_at' => $expiresAt,
                'is_active' => 1,
            ]);
        } catch (\Exception $e) {
            // Fallback: if new columns don't exist, insert without them
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
                'generated_html' => '',
                'expires_at' => $expiresAt,
                'is_active' => 1,
            ]);
        }

        $this->redirect("/user/invitations/{$invitationId}", [
            'success' => 'Invitation card created successfully! Share the link with your guests.',
        ]);
    }

    /**
     * Show invitation detail page (host view with RSVP list)
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

        // Get RSVP data
        $rsvps = $this->rsvpModel->getByInvitation((int) $id);
        $rsvpSummary = $this->rsvpModel->getSummary((int) $id);

        $this->viewWithLayout('user/invitation-detail', 'layouts/user', [
            'title' => 'Invitation Details',
            'invitation' => $invitation,
            'shareUrl' => $shareUrl,
            'rsvps' => $rsvps,
            'rsvpSummary' => $rsvpSummary,
            'templateConfig' => $this->invitationModel->getTemplateConfig($invitation),
        ]);
    }

    /**
     * Delete an invitation and all its associated RSVP responses
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

        try {
            // Begin transaction so RSVPs + invitation are removed atomically
            $this->invitationModel->beginTransaction();

            // 1. Delete all RSVP responses for this invitation
            $rsvpCount = $this->rsvpModel->deleteByInvitation((int) $id);

            // 2. Hard-delete the invitation itself (FK CASCADE handles it on fully-migrated schemas,
            //    but step 1 ensures cleanup on any schema)
            $this->invitationModel->delete((int) $id);

            $this->invitationModel->commit();

            $msg = 'Invitation deleted successfully.';
            if ($rsvpCount > 0) {
                $msg .= " {$rsvpCount} RSVP response(s) were also removed.";
            }

            $this->redirect('/user/invitations', ['success' => $msg]);

        } catch (\Exception $e) {
            $this->invitationModel->rollback();
            $this->redirect('/user/invitations', ['error' => 'Failed to delete invitation. Please try again.']);
        }
    }

    /**
     * Public invitation view (no auth required)
     * Shows the beautiful custom template with RSVP form
     */
    public function viewPublic($token)
    {
        $invitation = $this->invitationModel->findByToken($token);

        if (!$invitation || (int) $invitation['is_active'] !== 1) {
            $this->view('invitation-view', [
                'expired' => true,
                'invitation' => null,
                'templateConfig' => null,
                'rsvpSummary' => null,
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
                'templateConfig' => null,
                'rsvpSummary' => null,
            ]);
            return;
        }

        // Increment view count
        $this->invitationModel->incrementViewCount($invitation['id']);

        // Get template config
        $templateConfig = $this->invitationModel->getTemplateConfig($invitation);

        // Get RSVP summary (gracefully handle if table doesn't exist yet)
        $rsvpSummary = ['total_responses' => 0, 'attending' => 0, 'not_attending' => 0, 'maybe' => 0, 'total_guests' => 0];
        try {
            $rsvpSummary = $this->rsvpModel->getSummary($invitation['id']);
        } catch (\Exception $e) {
            // RSVP table may not exist yet — continue without it
        }

        $this->view('invitation-view', [
            'expired' => false,
            'invitation' => $invitation,
            'templateConfig' => $templateConfig,
            'rsvpSummary' => $rsvpSummary,
        ]);
    }

    /**
     * Submit RSVP (public — no auth required)
     */
    public function submitRsvp($token)
    {
        $invitation = $this->invitationModel->findByToken($token);

        if (!$invitation || !$invitation['is_active']) {
            $this->json(['success' => false, 'error' => 'This invitation is no longer active.'], 400);
            return;
        }

        if ($this->invitationModel->isExpired($invitation)) {
            $this->json(['success' => false, 'error' => 'This invitation has expired.'], 400);
            return;
        }

        if (empty($invitation['rsvp_enabled'])) {
            $this->json(['success' => false, 'error' => 'RSVP is not enabled for this invitation.'], 400);
            return;
        }

        $guestName = trim($_POST['guest_name'] ?? '');
        $attendingStatus = trim($_POST['attending_status'] ?? 'yes');
        $guestCount = (int) ($_POST['guest_count'] ?? 1);
        $message = trim($_POST['message'] ?? '');

        if (empty($guestName)) {
            $this->json(['success' => false, 'error' => 'Please enter your name.'], 400);
            return;
        }

        if (!in_array($attendingStatus, ['yes', 'no', 'maybe'])) {
            $attendingStatus = 'yes';
        }

        if ($guestCount < 1) $guestCount = 1;
        if ($guestCount > 50) $guestCount = 50;

        // Check for duplicate
        if ($this->rsvpModel->hasAlreadyResponded($invitation['id'], $guestName)) {
            $this->json(['success' => false, 'error' => 'You have already submitted your RSVP!'], 400);
            return;
        }

        try {
            $this->rsvpModel->create([
                'invitation_id' => $invitation['id'],
                'guest_name' => $guestName,
                'attending_status' => $attendingStatus,
                'guest_count' => $guestCount,
                'message' => $message ?: null,
            ]);

            $statusMessages = [
                'yes' => 'Thank you! Your attendance has been confirmed. 🎉',
                'no' => 'Thank you for letting us know. We\'ll miss you! 💐',
                'maybe' => 'Thank you! We hope to see you there. 🙏',
            ];

            $this->json([
                'success' => true,
                'message' => $statusMessages[$attendingStatus] ?? 'RSVP submitted!',
            ]);
        } catch (\Exception $e) {
            $this->json(['success' => false, 'error' => 'Failed to submit RSVP. Please try again.'], 500);
        }
    }
}
