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
        'generated_html',
        'ai_request_id',
        'expires_at',
        'is_active',
        'view_count',
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
}
