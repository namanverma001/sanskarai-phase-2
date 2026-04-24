<?php
/**
 * Sanskar AI - User Ritual Model
 * ================================
 * Manages user's personal ritual collection (My Rituals)
 */

namespace App\Models;

use App\Core\Model;

class UserRitual extends Model
{
    protected string $table = 'SAI_user_rituals';

    protected array $fillable = [
        'user_id',
        'global_ritual_id',
        'name',
        'name_sanskrit',
        'community_name',
        'religion',
        'category',
        'description',
        'significance',
        'duration_minutes',
        'difficulty',
        'deity',
        'best_time',
        'is_ai_generated',
        'ai_generation_prompt',
        'notes',
    ];

    /**
     * Get all rituals for a user
     */
    public function getByUser(int $userId): array
    {
        return $this->where(['user_id' => $userId], 'created_at', 'DESC');
    }

    /**
     * Search user's personal rituals by criteria (name, community, religion)
     */
    public function searchByUser(int $userId, array $criteria): array
    {
        $params = ['user_id' => $userId];
        $conditions = ['ur.user_id = :user_id'];

        if (!empty($criteria['ritual_name'])) {
            $conditions[] = "(ur.name LIKE :ritual_name OR ur.name_sanskrit LIKE :ritual_name_s)";
            $params['ritual_name'] = '%' . $criteria['ritual_name'] . '%';
            $params['ritual_name_s'] = '%' . $criteria['ritual_name'] . '%';
        }

        if (!empty($criteria['community_name'])) {
            $conditions[] = "(ur.community_name LIKE :community_name)";
            $params['community_name'] = '%' . $criteria['community_name'] . '%';
        }

        if (!empty($criteria['religion'])) {
            $conditions[] = "(ur.religion = :religion)";
            $params['religion'] = $criteria['religion'];
        }

        $where = implode(' AND ', $conditions);

        $sql = "
            SELECT ur.*, 'my_ritual' as source_type
            FROM SAI_user_rituals ur
            WHERE {$where}
            ORDER BY ur.created_at DESC
            LIMIT 20
        ";

        return $this->raw($sql, $params);
    }

    /**
     * Get user ritual by global ID
     */
    public function findByUserAndGlobal(int $userId, int $globalRitualId): ?array
    {
        $sql = "SELECT * FROM SAI_user_rituals WHERE user_id = :uid AND global_ritual_id = :gid LIMIT 1";
        return $this->rawOne($sql, ['uid' => $userId, 'gid' => $globalRitualId]);
    }

    /**
     * Get user ritual with steps and items
     */
    public function getWithDetails(int $id): ?array
    {
        $ritual = $this->find($id);

        if (!$ritual) {
            return null;
        }

        // Get steps
        $sql = "SELECT * FROM SAI_user_ritual_steps WHERE user_ritual_id = :id ORDER BY step_number ASC";
        $ritual['steps'] = $this->raw($sql, ['id' => $id]);

        // Get items
        $sql = "SELECT * FROM SAI_user_ritual_items WHERE user_ritual_id = :id ORDER BY is_mandatory DESC, item_name ASC";
        $ritual['items'] = $this->raw($sql, ['id' => $id]);

        return $ritual;
    }

    /**
     * Add ritual from global database to user's collection
     */
    public function addFromGlobal(int $userId, int $globalRitualId): int
    {
        $ritualModel = new Ritual();
        $globalRitual = $ritualModel->getWithDetails($globalRitualId);

        if (!$globalRitual) {
            throw new \Exception('Global ritual not found');
        }

        // Create user ritual
        $userRitualId = $this->create([
            'user_id' => $userId,
            'global_ritual_id' => $globalRitualId,
            'name' => $globalRitual['name'],
            'name_sanskrit' => $globalRitual['name_sanskrit'] ?? null,
            'community_name' => $globalRitual['community_name'] ?? null,
            'religion' => $globalRitual['religion'] ?? null,
            'category' => $globalRitual['category'],
            'description' => $globalRitual['description'],
            'significance' => $globalRitual['significance'],
            'duration_minutes' => $globalRitual['duration_minutes'],
            'difficulty' => $globalRitual['difficulty'],
            'deity' => $globalRitual['deity'],
            'best_time' => $globalRitual['best_time'],
            'is_ai_generated' => 0,
        ]);

        // Copy steps
        if (!empty($globalRitual['steps'])) {
            foreach ($globalRitual['steps'] as $step) {
                $this->addStep($userRitualId, [
                    'step_number' => $step['step_number'],
                    'title' => $step['title'],
                    'title_sanskrit' => $step['title_sanskrit'] ?? null,
                    'description' => $step['description'],
                    'mantra' => $step['mantra'],
                    'mantra_meaning' => $step['mantra_meaning'] ?? null,
                    'duration_minutes' => $step['duration_minutes'],
                    'is_optional' => $step['is_optional'],
                    'special_instructions' => $step['special_instructions'] ?? null,
                ]);
            }
        }

        // Copy items
        if (!empty($globalRitual['items'])) {
            foreach ($globalRitual['items'] as $item) {
                $this->addItem($userRitualId, [
                    'item_name' => $item['item_name'],
                    'item_name_local' => $item['item_name_local'] ?? null,
                    'quantity' => $item['quantity'],
                    'unit' => $item['unit'],
                    'is_mandatory' => $item['is_mandatory'],
                    'description' => $item['description'] ?? null,
                ]);
            }
        }

        return $userRitualId;
    }

    /**
     * Create ritual from AI generated data
     */
    public function createFromAI(int $userId, array $aiData, string $prompt): int
    {
        // Normalize difficulty to expected values (easy, medium, hard)
        $difficulty = strtolower($aiData['difficulty'] ?? 'medium');
        $difficultyMap = [
            'easy' => 'easy', 'simple' => 'easy', 'low' => 'easy', 'beginner' => 'easy',
            'medium' => 'medium', 'moderate' => 'medium', 'intermediate' => 'medium', 'normal' => 'medium',
            'hard' => 'hard', 'difficult' => 'hard', 'high' => 'hard', 'advanced' => 'hard', 'expert' => 'hard',
        ];
        $normalizedDifficulty = $difficultyMap[$difficulty] ?? 'medium';
        
        $userRitualId = $this->create([
            'user_id' => $userId,
            'global_ritual_id' => null,
            'name' => $aiData['name'],
            'name_sanskrit' => $aiData['name_sanskrit'] ?? null,
            'community_name' => $aiData['community_name'] ?? null,
            'religion' => $aiData['religion'] ?? null,
            'category' => $aiData['category'] ?? 'General',
            'description' => $aiData['description'] ?? '',
            'significance' => $aiData['significance'] ?? null,
            'duration_minutes' => (int) ($aiData['duration_minutes'] ?? 60),
            'difficulty' => $normalizedDifficulty,
            'deity' => $aiData['deity'] ?? null,
            'best_time' => $aiData['best_time'] ?? null,
            'is_ai_generated' => 1,
            'ai_generation_prompt' => $prompt,
        ]);

        // Add steps
        if (!empty($aiData['steps'])) {
            foreach ($aiData['steps'] as $index => $step) {
                $this->addStep($userRitualId, [
                    'step_number' => $step['step_number'] ?? ($index + 1),
                    'title' => $step['title'],
                    'title_sanskrit' => $step['title_sanskrit'] ?? null,
                    'description' => $step['description'] ?? null,
                    'mantra' => $step['mantra'] ?? null,
                    'mantra_meaning' => $step['mantra_meaning'] ?? null,
                    'duration_minutes' => $step['duration_minutes'] ?? 5,
                    'is_optional' => (int) ($step['is_optional'] ?? 0),
                    'special_instructions' => $step['special_instructions'] ?? null,
                    'items_needed' => $step['items_needed'] ?? null,
                ]);
            }
        }

        // Add items
        if (!empty($aiData['items'])) {
            foreach ($aiData['items'] as $item) {
                $this->addItem($userRitualId, [
                    'item_name' => $item['item_name'],
                    'item_name_local' => $item['item_name_local'] ?? null,
                    'quantity' => $item['quantity'] ?? 1,
                    'unit' => $item['unit'] ?? 'piece',
                    'is_mandatory' => (int) ($item['is_mandatory'] ?? 1),
                    'description' => $item['description'] ?? null,
                    'alternatives' => $item['alternatives'] ?? null,
                ]);
            }
        }

        return $userRitualId;
    }

    /**
     * Add step to user ritual
     */
    public function addStep(int $userRitualId, array $data): int
    {
        // Check if we need to shift steps
        $stepNumber = $data['step_number'];
        $checkSql = "SELECT COUNT(*) as count FROM SAI_user_ritual_steps WHERE user_ritual_id = :id AND step_number = :step";
        $exists = $this->rawOne($checkSql, ['id' => $userRitualId, 'step' => $stepNumber]);
        
        if ($exists['count'] > 0) {
            // Shift steps down (must be done in reverse order to avoid unique constraint violations)
            $shiftSql = "UPDATE SAI_user_ritual_steps SET step_number = step_number + 1 
                         WHERE user_ritual_id = :id AND step_number >= :step
                         ORDER BY step_number DESC";
            $shiftStmt = $this->db->prepare($shiftSql);
            $shiftStmt->execute(['id' => $userRitualId, 'step' => $stepNumber]);
        }

        $sql = "INSERT INTO SAI_user_ritual_steps 
                (user_ritual_id, step_number, title, title_sanskrit, description, mantra, mantra_meaning, duration_minutes, is_optional, special_instructions, items_needed, created_at)
                VALUES (:user_ritual_id, :step_number, :title, :title_sanskrit, :description, :mantra, :mantra_meaning, :duration_minutes, :is_optional, :special_instructions, :items_needed, NOW())";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'user_ritual_id' => $userRitualId,
            'step_number' => $stepNumber,
            'title' => $data['title'],
            'title_sanskrit' => $data['title_sanskrit'] ?? null,
            'description' => $data['description'] ?? null,
            'mantra' => $data['mantra'] ?? null,
            'mantra_meaning' => $data['mantra_meaning'] ?? null,
            'duration_minutes' => $data['duration_minutes'] ?? 5,
            'is_optional' => (int) ($data['is_optional'] ?? 0),
            'special_instructions' => $data['special_instructions'] ?? null,
            'items_needed' => $data['items_needed'] ?? null,
        ]);

        return (int) $this->db->lastInsertId();
    }

    /**
     * Update step
     */
    public function updateStep(int $stepId, array $data): bool
    {
        $sets = [];
        $params = ['id' => $stepId];

        foreach ($data as $key => $value) {
            if (in_array($key, ['title', 'title_sanskrit', 'description', 'mantra', 'mantra_meaning', 'duration_minutes', 'is_optional', 'special_instructions', 'items_needed'])) {
                $sets[] = "$key = :$key";
                $params[$key] = $value;
            }
        }

        if (empty($sets)) {
            return false;
        }

        $sql = "UPDATE SAI_user_ritual_steps SET " . implode(', ', $sets) . ", updated_at = NOW() WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Delete step
     */
    public function deleteStep(int $stepId): bool
    {
        // Get step info first
        $sql = "SELECT user_ritual_id, step_number FROM SAI_user_ritual_steps WHERE id = :id";
        $step = $this->rawOne($sql, ['id' => $stepId]);

        if (!$step) {
            return false;
        }

        // Delete the step
        $sql = "DELETE FROM SAI_user_ritual_steps WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $stepId]);

        // Renumber remaining steps
        $sql = "UPDATE SAI_user_ritual_steps SET step_number = step_number - 1 
                WHERE user_ritual_id = :user_ritual_id AND step_number > :step_number";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'user_ritual_id' => $step['user_ritual_id'],
            'step_number' => $step['step_number'],
        ]);

        return true;
    }

    /**
     * Add item to user ritual
     */
    public function addItem(int $userRitualId, array $data): int
    {
        $sql = "INSERT INTO SAI_user_ritual_items 
                (user_ritual_id, item_name, item_name_local, quantity, unit, is_mandatory, description, alternatives, created_at)
                VALUES (:user_ritual_id, :item_name, :item_name_local, :quantity, :unit, :is_mandatory, :description, :alternatives, NOW())";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'user_ritual_id' => $userRitualId,
            'item_name' => $data['item_name'],
            'item_name_local' => $data['item_name_local'] ?? null,
            'quantity' => $data['quantity'] ?? 1,
            'unit' => $data['unit'] ?? 'piece',
            'is_mandatory' => $data['is_mandatory'] ?? true,
            'description' => $data['description'] ?? null,
            'alternatives' => $data['alternatives'] ?? null,
        ]);

        return (int) $this->db->lastInsertId();
    }

    /**
     * Update item
     */
    public function updateItem(int $itemId, array $data): bool
    {
        $sets = [];
        $params = ['id' => $itemId];

        foreach ($data as $key => $value) {
            if (in_array($key, ['item_name', 'item_name_local', 'quantity', 'unit', 'is_mandatory', 'description', 'alternatives'])) {
                $sets[] = "$key = :$key";
                $params[$key] = $value;
            }
        }

        if (empty($sets)) {
            return false;
        }

        $sql = "UPDATE SAI_user_ritual_items SET " . implode(', ', $sets) . ", updated_at = NOW() WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Delete item
     */
    public function deleteItem(int $itemId): bool
    {
        $sql = "DELETE FROM SAI_user_ritual_items WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $itemId]);
    }

    /**
     * Check if ritual belongs to user
     */
    public function belongsToUser(int $ritualId, int $userId): bool
    {
        $ritual = $this->find($ritualId);
        return $ritual && (int) $ritual['user_id'] === $userId;
    }

    /**
     * Delete a ritual and all dependent rows safely (for schemas without cascade constraints).
     */
    public function deleteWithRelations(int $ritualId): bool
    {
        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare("DELETE FROM SAI_user_ritual_steps WHERE user_ritual_id = :id");
            $stmt->execute(['id' => $ritualId]);

            $stmt = $this->db->prepare("DELETE FROM SAI_user_ritual_items WHERE user_ritual_id = :id");
            $stmt->execute(['id' => $ritualId]);

            $stmt = $this->db->prepare("DELETE FROM SAI_step_completion WHERE progress_id IN (SELECT id FROM SAI_ritual_progress WHERE user_ritual_id = :id)");
            $stmt->execute(['id' => $ritualId]);

            $stmt = $this->db->prepare("DELETE FROM SAI_ritual_progress WHERE user_ritual_id = :id");
            $stmt->execute(['id' => $ritualId]);

            $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id");
            $stmt->execute(['id' => $ritualId]);

            $deleted = $stmt->rowCount() > 0;
            if (!$deleted) {
                $this->db->rollBack();
                return false;
            }

            $this->db->commit();
            return true;
        } catch (\Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e;
        }
    }

    /**
     * Start ritual session
     */
    public function startSession(int $userId, int $userRitualId): string
    {
        // Reuse latest in-progress session so users can resume after pause.
        $existingSql = "SELECT session_id
                        FROM SAI_ritual_progress
                        WHERE user_id = :user_id
                          AND user_ritual_id = :user_ritual_id
                          AND status = 'in_progress'
                        ORDER BY id DESC
                        LIMIT 1";
        $existing = $this->rawOne($existingSql, [
            'user_id' => $userId,
            'user_ritual_id' => $userRitualId,
        ]);

        if (!empty($existing['session_id'])) {
            return (string) $existing['session_id'];
        }

        $sessionId = bin2hex(random_bytes(32));

        $sql = "INSERT INTO SAI_ritual_progress 
                (user_id, user_ritual_id, session_id, current_step, status, started_at, created_at)
                VALUES (:user_id, :user_ritual_id, :session_id, 1, 'in_progress', NOW(), NOW())";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'user_id' => $userId,
            'user_ritual_id' => $userRitualId,
            'session_id' => $sessionId,
        ]);

        $progressId = (int) $this->db->lastInsertId();

        // Initialize step completion records
        $sql = "SELECT id, step_number FROM SAI_user_ritual_steps WHERE user_ritual_id = :user_ritual_id ORDER BY step_number";
        $steps = $this->raw($sql, ['user_ritual_id' => $userRitualId]);

        foreach ($steps as $step) {
            $sql = "INSERT INTO SAI_step_completion (progress_id, step_id, step_number, is_completed)
                    VALUES (:progress_id, :step_id, :step_number, 0)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'progress_id' => $progressId,
                'step_id' => $step['id'],
                'step_number' => $step['step_number'],
            ]);
        }

        return $sessionId;
    }

    /**
     * Get session progress
     */
    public function getSessionProgress(string $sessionId): ?array
    {
        $sql = "SELECT p.*, ur.name as ritual_name, ur.description as ritual_description
                FROM SAI_ritual_progress p
                JOIN SAI_user_rituals ur ON p.user_ritual_id = ur.id
                WHERE p.session_id = :session_id";
        $progress = $this->rawOne($sql, ['session_id' => $sessionId]);

        if (!$progress) {
            return null;
        }

        // Get step completions
        $sql = "SELECT sc.*, urs.title, urs.description, urs.mantra, urs.special_instructions, urs.items_needed
                FROM SAI_step_completion sc
                JOIN SAI_user_ritual_steps urs ON sc.step_id = urs.id
                WHERE sc.progress_id = :progress_id
                ORDER BY sc.step_number";
        $progress['step_completions'] = $this->raw($sql, ['progress_id' => $progress['id']]);

        return $progress;
    }

    /**
     * Mark step as completed
     */
    public function completeStep(string $sessionId, int $stepNumber): bool
    {
        $sql = "SELECT id FROM SAI_ritual_progress WHERE session_id = :session_id";
        $progress = $this->rawOne($sql, ['session_id' => $sessionId]);

        if (!$progress) {
            return false;
        }

        $sql = "UPDATE SAI_step_completion 
                SET is_completed = 1, completed_at = NOW() 
                WHERE progress_id = :progress_id AND step_number = :step_number";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'progress_id' => $progress['id'],
            'step_number' => $stepNumber,
        ]);

        // Update current step
        $sql = "UPDATE SAI_ritual_progress SET current_step = :step, updated_at = NOW() WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'step' => $stepNumber + 1,
            'id' => $progress['id'],
        ]);

        return true;
    }

    /**
     * Complete ritual session
     */
    public function completeSession(string $sessionId): bool
    {
        $sql = "UPDATE SAI_ritual_progress 
                SET status = 'completed', completed_at = NOW(), updated_at = NOW() 
                WHERE session_id = :session_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['session_id' => $sessionId]);
    }

    /**
     * Get user's ritual history
     */
    public function getHistory(int $userId, int $limit = 20): array
    {
        $sql = "SELECT p.*, ur.name as ritual_name
                FROM SAI_ritual_progress p
                JOIN SAI_user_rituals ur ON p.user_ritual_id = ur.id
                WHERE p.user_id = :user_id
                ORDER BY p.created_at DESC
                LIMIT :limit";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $userId, \PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}
