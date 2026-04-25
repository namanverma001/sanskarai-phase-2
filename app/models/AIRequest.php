<?php
/**
 * Sanskar AI - AI Request Model
 * ===============================
 * AI request/response storage
 */

namespace App\Models;

use App\Core\Model;

class AIRequest extends Model
{
    protected string $table = 'SAI_ai_requests';

    // This table does not have an `updated_at` column; disable automatic timestamps
    protected bool $timestamps = false;

    protected array $fillable = [
        'user_id',
        'request_type',
        'request_category',
        'prompt',
        'context_data',
        'response',
        'response_data',
        'model_used',
        'status',
        'tokens_used',
        'processing_time_ms',
        'error_message',
        'is_flagged',
        'flag_reason',
        'completed_at',
    ];

    /**
     * Create new AI request
     */
    public function createRequest(?int $userId, string $type, string $prompt, array $context = []): int
    {
        return $this->create([
            'user_id' => $userId,
            'request_type' => $type,
            'prompt' => $prompt,
            'context_data' => !empty($context) ? json_encode($context) : null,
            'status' => 'pending',
        ]);
    }

    /**
     * Update request with response
     */
    public function updateWithResponse(int $id, string $response, array $data = []): bool
    {
        $updateData = [
            'response' => $response,
            'status' => 'completed',
            'completed_at' => date('Y-m-d H:i:s'),
        ];

        if (!empty($data)) {
            $updateData['response_data'] = json_encode($data);
        }

        if (isset($data['tokens_used'])) {
            $updateData['tokens_used'] = $data['tokens_used'];
        }

        if (isset($data['processing_time_ms'])) {
            $updateData['processing_time_ms'] = $data['processing_time_ms'];
        }

        return $this->update($id, $updateData);
    }

    /**
     * Mark request as failed
     */
    public function markFailed(int $id, string $error): bool
    {
        return $this->update($id, [
            'status' => 'failed',
            'error_message' => $error,
            'completed_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Flag request for moderation
     */
    public function flag(int $id, string $reason): bool
    {
        return $this->update($id, [
            'is_flagged' => 1,
            'flag_reason' => $reason,
            'status' => 'moderated',
        ]);
    }

    /**
     * Unflag request (remove moderation flag)
     */
    public function unflag(int $id): bool
    {
        return $this->update($id, [
            'is_flagged' => 0,
            'flag_reason' => null,
            'status' => 'completed',
        ]);
    }

    /**
     * Get requests by user
     */
    public function getByUser(int $userId, int $limit = 20): array
    {
        $sql = "
            SELECT * FROM SAI_ai_requests 
            WHERE user_id = :user_id 
            ORDER BY created_at DESC 
            LIMIT :limit
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $userId, \PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Get flagged requests
     */
    public function getFlagged(): array
    {
        return $this->where(['is_flagged' => 1], 'created_at', 'DESC');
    }

    /**
     * Get recent requests for moderation
     */
    public function getRecent(int $limit = 50): array
    {
        $sql = "
            SELECT r.*, u.name as user_name, u.email as user_email
            FROM SAI_ai_requests r
            INNER JOIN SAI_users u ON r.user_id = u.id
            ORDER BY r.created_at DESC
            LIMIT :limit
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Search AI requests
     */
    public function search(string $query): array
    {
        $sql = "
            SELECT r.*, u.name as user_name, u.email as user_email
            FROM SAI_ai_requests r
            INNER JOIN SAI_users u ON r.user_id = u.id
            WHERE 
                u.name LIKE :name
                OR r.request_type LIKE :type
                OR r.status LIKE :status
                OR r.prompt LIKE :prompt
            ORDER BY r.created_at DESC
            LIMIT 50
        ";

        $term = "%$query%";
        $params = [
            'name' => $term,
            'type' => $term,
            'status' => $term,
            'prompt' => $term
        ];

        return $this->raw($sql, $params);
    }

    /**
     * Get AI statistics
     */
    public function getStats(): array
    {
        $sql = "
            SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
                SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as failed,
                SUM(CASE WHEN is_flagged = 1 THEN 1 ELSE 0 END) as flagged,
                SUM(tokens_used) as total_tokens
            FROM SAI_ai_requests
        ";

        return $this->rawOne($sql);
    }

    /**
     * Log AI event
     */
    public function log(string $level, string $event, string $message, array $metadata = [], ?int $requestId = null): int
    {
        $sql = "
            INSERT INTO SAI_ai_logs (ai_request_id, user_id, log_level, event_type, message, metadata, ip_address, user_agent, created_at)
            VALUES (:request_id, :user_id, :level, :event, :message, :metadata, :ip, :ua, NOW())
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'request_id' => $requestId,
            'user_id' => $_SESSION['sai_user']['id'] ?? null,
            'level' => $level,
            'event' => $event,
            'message' => $message,
            'metadata' => !empty($metadata) ? json_encode($metadata) : null,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? null,
            'ua' => $_SERVER['HTTP_USER_AGENT'] ?? null,
        ]);

        return (int) $this->db->lastInsertId();
    }

    /**
     * Get logs for request
     */
    public function getLogs(int $requestId): array
    {
        $sql = "SELECT * FROM SAI_ai_logs WHERE ai_request_id = :request_id ORDER BY created_at ASC";
        return $this->raw($sql, ['request_id' => $requestId]);
    }
}
