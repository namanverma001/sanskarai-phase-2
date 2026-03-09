<?php
/**
 * Sanskar AI - AI Ritual Feedback Model
 * =======================================
 * Manages AI ritual generation feedback for self-learning
 */

namespace App\Models;

use App\Core\Model;

class AIRitualFeedback extends Model
{
    protected string $table = 'SAI_ai_ritual_feedback';
    
    protected array $fillable = [
        'user_id',
        'ritual_name',
        'community_name',
        'religion',
        'generation_session_id',
        'round_number',
        'ai_response',
        'user_feedback',
        'feedback_type',
        'search_criteria',
    ];

    /**
     * Store a feedback entry
     */
    public function storeFeedback(array $data): int
    {
        // JSON encode the ai_response and search_criteria if they are arrays
        if (isset($data['ai_response']) && is_array($data['ai_response'])) {
            $data['ai_response'] = json_encode($data['ai_response'], JSON_UNESCAPED_UNICODE);
        }
        if (isset($data['search_criteria']) && is_array($data['search_criteria'])) {
            $data['search_criteria'] = json_encode($data['search_criteria'], JSON_UNESCAPED_UNICODE);
        }
        
        return $this->create($data);
    }

    /**
     * Get all feedback rounds for a session
     */
    public function getBySession(string $sessionId): array
    {
        $sql = "
            SELECT f.*, u.name as user_name
            FROM {$this->table} f
            INNER JOIN SAI_users u ON f.user_id = u.id
            WHERE f.generation_session_id = :session_id
            ORDER BY f.round_number ASC
        ";
        return $this->raw($sql, ['session_id' => $sessionId]);
    }

    /**
     * Get past feedback for the same ritual name (for AI self-learning)
     * Returns feedback from ALL users for the same ritual to help AI learn
     */
    public function getLearningFeedback(string $ritualName, ?string $communityName = null, int $limit = 20): array
    {
        $params = ['ritual_name' => $ritualName];
        
        $sql = "
            SELECT f.user_feedback, f.feedback_type, f.round_number, f.community_name, f.religion
            FROM {$this->table} f
            WHERE f.ritual_name LIKE :ritual_name
            AND f.feedback_type IN ('rejected', 'refined')
            AND f.user_feedback IS NOT NULL
            AND f.user_feedback != ''
        ";
        
        if ($communityName) {
            $sql .= " AND (f.community_name = :community_name OR f.community_name IS NULL)";
            $params['community_name'] = $communityName;
        }
        
        $sql .= " ORDER BY f.created_at DESC LIMIT {$limit}";
        
        return $this->raw($sql, $params);
    }

    /**
     * Get the latest round number for a session
     */
    public function getLatestRound(string $sessionId): int
    {
        $sql = "SELECT MAX(round_number) as max_round FROM {$this->table} WHERE generation_session_id = :session_id";
        $result = $this->rawOne($sql, ['session_id' => $sessionId]);
        return (int) ($result['max_round'] ?? 0);
    }

    /**
     * Mark a session as accepted (final feedback entry)
     */
    public function markAccepted(string $sessionId, int $userId, array $aiResponse): int
    {
        $roundNumber = $this->getLatestRound($sessionId) + 1;
        
        return $this->storeFeedback([
            'user_id' => $userId,
            'ritual_name' => $aiResponse['name'] ?? 'Unknown',
            'community_name' => $aiResponse['community_name'] ?? null,
            'religion' => $aiResponse['religion'] ?? null,
            'generation_session_id' => $sessionId,
            'round_number' => $roundNumber,
            'ai_response' => $aiResponse,
            'user_feedback' => null,
            'feedback_type' => 'accepted',
        ]);
    }
}
