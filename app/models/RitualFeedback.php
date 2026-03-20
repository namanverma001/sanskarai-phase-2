<?php
/**
 * Sanskar AI - Ritual Feedback Model
 * =====================================
 * Manages Like/Dislike feedback for AI-generated rituals
 */

namespace App\Models;

use App\Core\Model;

class RitualFeedback extends Model
{
    protected string $table = 'SAI_ritual_feedbacks';

    protected array $fillable = [
        'user_id',
        'community_name',
        'religion',
        'ritual_name',
        'feedback_type',
        'feedback_text',
    ];

    protected bool $timestamps = false;

    /**
     * Store a like/dislike feedback entry
     */
    public function storeFeedback(array $data): int
    {
        return $this->create($data);
    }
}
