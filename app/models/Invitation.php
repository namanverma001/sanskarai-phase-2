<?php
/**
 * Sanskar AI - Invitation Model
 * ===============================
 * Handles invitation CRUD and token-based lookups
 */

namespace App\Models;

use App\Core\Model;

class Invitation extends Model
{
    protected string $table = 'SAI_invitations';

    protected array $fillable = [
        'user_id',
        'share_token',
        'occasion_type',
        'occasion_title',
        'event_date',
        'venue',
        'google_maps_link',
        'host_name',
        'message',
        'additional_details',
        'template_id',
        'theme_color',
        'rsvp_enabled',
        'generated_html',
        'ai_request_id',
        'expires_at',
        'is_active',
        'view_count',
    ];

    /** Available invitation templates */
    public const TEMPLATES = [
        'royal_gold' => ['name' => 'Royal Gold', 'accent' => '#B8860B', 'bg' => 'linear-gradient(135deg,#1a1a2e,#16213e,#0f3460)', 'icon' => '👑'],
        'floral_pink' => ['name' => 'Floral Pink', 'accent' => '#E91E63', 'bg' => 'linear-gradient(135deg,#2d1b3d,#1a1a2e,#2d1b3d)', 'icon' => '🌸'],
        'classic_white' => ['name' => 'Classic White', 'accent' => '#6366F1', 'bg' => 'linear-gradient(135deg,#f8fafc,#e2e8f0,#f1f5f9)', 'icon' => '🤍'],
        'festive_orange' => ['name' => 'Festive Saffron', 'accent' => '#FF6B35', 'bg' => 'linear-gradient(135deg,#1a1a2e,#2d1b0e,#1a1a2e)', 'icon' => '🪔'],
        'nature_green' => ['name' => 'Nature Green', 'accent' => '#059669', 'bg' => 'linear-gradient(135deg,#0f2922,#1a1a2e,#0f2922)', 'icon' => '🌿'],
    ];

    /**
     * Find invitation by share token
     */
    public function findByToken(string $token): ?array
    {
        return $this->findBy('share_token', $token);
    }

    /**
     * Get all invitations for a user, ordered by newest first
     */
    public function getByUser(int $userId): array
    {
        return $this->where(['user_id' => $userId], 'created_at', 'DESC');
    }

    /**
     * Get active invitations for a user
     */
    public function getActiveByUser(int $userId): array
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE user_id = :user_id AND is_active = 1 
                ORDER BY created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll();
    }

    /**
     * Increment view count for an invitation
     */
    public function incrementViewCount(int $id): bool
    {
        $sql = "UPDATE {$this->table} SET view_count = view_count + 1 WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Check if an invitation has expired
     */
    public function isExpired(array $invitation): bool
    {
        return strtotime($invitation['expires_at']) < time();
    }

    /**
     * Deactivate all expired invitations
     */
    public function deactivateExpired(): int
    {
        $sql = "UPDATE {$this->table} SET is_active = 0 
                WHERE is_active = 1 AND expires_at < NOW()";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->rowCount();
    }

    /**
     * Generate a unique share token
     */
    public function generateShareToken(): string
    {
        do {
            $token = bin2hex(random_bytes(16));
        } while ($this->findByToken($token) !== null);

        return $token;
    }

    /**
     * Get invitation count for a user
     */
    public function countByUser(int $userId): int
    {
        return $this->count(['user_id' => $userId]);
    }

    /**
     * Get RSVPs for an invitation
     */
    public function getRsvps(int $invitationId): array
    {
        $rsvpModel = new InvitationRsvp();
        return $rsvpModel->getByInvitation($invitationId);
    }

    /**
     * Get RSVP summary for an invitation
     */
    public function getRsvpSummary(int $invitationId): array
    {
        $rsvpModel = new InvitationRsvp();
        return $rsvpModel->getSummary($invitationId);
    }

    /**
     * Get template config for an invitation
     */
    public function getTemplateConfig(array $invitation): array
    {
        $templateId = $invitation['template_id'] ?? 'royal_gold';
        return self::TEMPLATES[$templateId] ?? self::TEMPLATES['royal_gold'];
    }
}
