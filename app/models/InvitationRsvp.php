<?php
/**
 * Sanskar AI - Invitation RSVP Model
 * =====================================
 * Handles RSVP responses for invitations
 */

namespace App\Models;

use App\Core\Model;

class InvitationRsvp extends Model
{
    protected string $table = 'SAI_invitation_rsvps';
    protected bool $timestamps = false;

    protected array $fillable = [
        'invitation_id',
        'guest_name',
        'attending_status',
        'guest_count',
        'message',
    ];

    /**
     * Get all RSVPs for an invitation
     */
    public function getByInvitation(int $invitationId): array
    {
        return $this->where(['invitation_id' => $invitationId], 'created_at', 'DESC');
    }

    /**
     * Get RSVP summary counts for an invitation
     */
    public function getSummary(int $invitationId): array
    {
        $sql = "SELECT 
                    COUNT(*) as total_responses,
                    SUM(CASE WHEN attending_status = 'yes' THEN 1 ELSE 0 END) as attending,
                    SUM(CASE WHEN attending_status = 'no' THEN 1 ELSE 0 END) as not_attending,
                    SUM(CASE WHEN attending_status = 'maybe' THEN 1 ELSE 0 END) as maybe,
                    SUM(CASE WHEN attending_status = 'yes' THEN guest_count ELSE 0 END) as total_guests
                FROM {$this->table}
                WHERE invitation_id = :invitation_id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['invitation_id' => $invitationId]);
        $result = $stmt->fetch();

        return $result ?: [
            'total_responses' => 0,
            'attending' => 0,
            'not_attending' => 0,
            'maybe' => 0,
            'total_guests' => 0,
        ];
    }

    /**
     * Check if a guest name already submitted RSVP for this invitation
     */
    public function hasAlreadyResponded(int $invitationId, string $guestName): bool
    {
        $sql = "SELECT COUNT(*) as cnt FROM {$this->table}
                WHERE invitation_id = :invitation_id AND LOWER(guest_name) = LOWER(:guest_name)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['invitation_id' => $invitationId, 'guest_name' => $guestName]);
        $row = $stmt->fetch();
        return (int) ($row['cnt'] ?? 0) > 0;
    }

    /**
     * Delete all RSVP responses for an invitation (called before hard-deleting the invitation)
     */
    public function deleteByInvitation(int $invitationId): int
    {
        $sql = "DELETE FROM {$this->table} WHERE invitation_id = :invitation_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['invitation_id' => $invitationId]);
        return $stmt->rowCount();
    }
}
