<?php
/**
 * Sanskar AI - Mohurat Request Model
 * ====================================
 * Mohurat (auspicious time) request management
 */

namespace App\Models;

use App\Core\Model;

class MohuratRequest extends Model
{
    protected string $table = 'SAI_mohurat_requests';

    protected array $fillable = [
        'user_id',
        'pandit_id',
        'family_id',
        'ritual_type',
        'country',
        'city',
        'preferred_month',
        'gotra',
        'nakshatra',
        'time_preference',
        'additional_notes',
        'status',
        'reply_date',
        'reply_time',
        'reply_explanation',
        'consultation_fee',
        'replied_by',
        'replied_at',
        'accepted_at',
        'assignment_id',
    ];

    /**
     * Get mohurat requests for a user
     */
    public function getForUser(int $userId): array
    {
        $sql = "
            SELECT mr.*,
                   p.name as pandit_name,
                   pp.specialization as pandit_specialization,
                   pp.average_rating as pandit_rating,
                   f.family_name
            FROM {$this->table} mr
            LEFT JOIN SAI_users p ON mr.pandit_id = p.id
            LEFT JOIN SAI_pandit_profiles pp ON mr.pandit_id = pp.user_id
            LEFT JOIN SAI_families f ON mr.family_id = f.id
            WHERE mr.user_id = :user_id
            ORDER BY mr.created_at DESC
        ";

        return $this->raw($sql, ['user_id' => $userId]);
    }

    /**
     * Get mohurat requests visible to a pandit
     * Shows all pending requests + requests the pandit has replied to
     */
    public function getForPandit(int $panditId): array
    {
        $sql = "
            SELECT mr.*,
                   u.name as user_name,
                   u.mobile as user_mobile,
                   f.family_name, f.gotra as family_gotra, f.nakshatra as family_nakshatra
            FROM {$this->table} mr
            INNER JOIN SAI_users u ON mr.user_id = u.id
            LEFT JOIN SAI_families f ON mr.family_id = f.id
            WHERE mr.pandit_id = :pandit_id
            ORDER BY 
                CASE mr.status 
                    WHEN 'pending' THEN 1
                    WHEN 'replied' THEN 2
                    WHEN 'accepted' THEN 3
                    ELSE 4 
                END,
                mr.created_at DESC
        ";

        return $this->raw($sql, ['pandit_id' => $panditId]);
    }

    /**
     * Count pending requests (for pandit dashboard)
     */
    public function countPending(): int
    {
        return $this->count(['status' => 'pending']);
    }

    /**
     * Reply to a mohurat request
     */
    public function reply(int $id, int $panditId, array $data): bool
    {
        $sql = "
            UPDATE {$this->table}
            SET status = 'replied',
                replied_by = :pandit_id,
                reply_date = :reply_date,
                reply_time = :reply_time,
                reply_explanation = :reply_explanation,
                consultation_fee = :consultation_fee,
                replied_at = NOW(),
                updated_at = NOW()
            WHERE id = :id AND status = 'pending'
        ";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'pandit_id' => $panditId,
            'reply_date' => $data['reply_date'],
            'reply_time' => $data['reply_time'],
            'reply_explanation' => $data['reply_explanation'],
            'consultation_fee' => $data['consultation_fee'] ?? null,
        ]);
    }

    /**
     * Accept a mohurat reply (user side)
     */
    public function accept(int $id, int $assignmentId): bool
    {
        $sql = "
            UPDATE {$this->table}
            SET status = 'accepted',
                accepted_at = NOW(),
                assignment_id = :assignment_id,
                updated_at = NOW()
            WHERE id = :id AND status = 'replied'
        ";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'assignment_id' => $assignmentId,
        ]);
    }

    /**
     * Decline a mohurat reply (user side)
     */
    public function decline(int $id): bool
    {
        $sql = "
            UPDATE {$this->table}
            SET status = 'declined',
                updated_at = NOW()
            WHERE id = :id AND status = 'replied'
        ";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }
}
