<?php
/**
 * Sanskar AI - PanditChat Model
 * ==============================
 * Model for AI Pandit chat sessions and messages
 */

namespace App\Models;

use App\Core\Model;

class PanditChat extends Model
{
    protected string $table = 'SAI_pandit_chat_sessions';

    protected array $fillable = [
        'user_id',
        'title',
        'user_details',
        'status',
    ];

    /**
     * Create a new chat session
     */
    public function createSession(int $userId, string $title = 'New Conversation'): int
    {
        $sql = "INSERT INTO {$this->table} (user_id, title, created_at, updated_at) 
                VALUES (:user_id, :title, NOW(), NOW())";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'user_id' => $userId,
            'title' => $title,
        ]);

        return (int) $this->db->lastInsertId();
    }

    /**
     * Get all sessions for a user (for history panel)
     */
    public function getUserSessions(int $userId, int $limit = 50): array
    {
        $sql = "SELECT s.*, 
                    (SELECT COUNT(*) FROM SAI_pandit_chat_messages WHERE session_id = s.id) as message_count
                FROM {$this->table} s
                WHERE s.user_id = :user_id
                ORDER BY s.updated_at DESC
                LIMIT :limit";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $userId, \PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Get a single session (with ownership check)
     */
    public function getSession(int $sessionId, int $userId): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id AND user_id = :user_id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $sessionId, 'user_id' => $userId]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Update session title (auto-generated from first message)
     */
    public function updateSessionTitle(int $sessionId, string $title): bool
    {
        $sql = "UPDATE {$this->table} SET title = :title, updated_at = NOW() WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['title' => $title, 'id' => $sessionId]);
    }

    /**
     * Update user religious details collected during conversation
     */
    public function updateUserDetails(int $sessionId, array $details): bool
    {
        $sql = "UPDATE {$this->table} SET user_details = :details, updated_at = NOW() WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'details' => json_encode($details, JSON_UNESCAPED_UNICODE),
            'id' => $sessionId,
        ]);
    }

    /**
     * Touch session updated_at (to keep it sorted properly)
     */
    public function touchSession(int $sessionId): bool
    {
        $sql = "UPDATE {$this->table} SET updated_at = NOW() WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $sessionId]);
    }

    /**
     * Add a message to a session
     */
    public function addMessage(int $sessionId, string $role, string $content, int $tokensUsed = 0): int
    {
        $sql = "INSERT INTO SAI_pandit_chat_messages (session_id, role, content, tokens_used, created_at) 
                VALUES (:session_id, :role, :content, :tokens_used, NOW())";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'session_id' => $sessionId,
            'role' => $role,
            'content' => $content,
            'tokens_used' => $tokensUsed,
        ]);

        // Touch the session to update ordering
        $this->touchSession($sessionId);

        return (int) $this->db->lastInsertId();
    }

    /**
     * Get all messages for a session (ordered chronologically)
     */
    public function getMessages(int $sessionId): array
    {
        $sql = "SELECT * FROM SAI_pandit_chat_messages 
                WHERE session_id = :session_id 
                ORDER BY created_at ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['session_id' => $sessionId]);

        return $stmt->fetchAll();
    }

    /**
     * Get message count for a session
     */
    public function getMessageCount(int $sessionId): int
    {
        $sql = "SELECT COUNT(*) as count FROM SAI_pandit_chat_messages WHERE session_id = :session_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['session_id' => $sessionId]);
        return (int) $stmt->fetch()['count'];
    }

    /**
     * Delete a session and all its messages (cascade handles messages)
     */
    public function deleteSession(int $sessionId, int $userId): bool
    {
        $sql = "DELETE FROM {$this->table} WHERE id = :id AND user_id = :user_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $sessionId, 'user_id' => $userId]);
    }

    /**
     * Get the latest active session for a user (if any)
     */
    public function getLatestSession(int $userId): ?array
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE user_id = :user_id AND status = 'active' 
                ORDER BY updated_at DESC LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        $result = $stmt->fetch();
        return $result ?: null;
    }
}
