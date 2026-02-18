<?php
/**
 * Sanskar AI - Ritual Model
 * ==========================
 * Ritual and related data management
 */

namespace App\Models;

use App\Core\Model;

class Ritual extends Model
{
    protected string $table = 'SAI_rituals';

    protected array $fillable = [
        'name',
        'name_sanskrit',
        'category',
        'sub_category',
        'description',
        'significance',
        'duration_minutes',
        'difficulty',
        'occasion_type',
        'best_time',
        'best_tithi',
        'deity',
        'is_active',
        'is_featured',
        'created_by',
    ];

    /**
     * Get active rituals
     */
    public function getActive(): array
    {
        return $this->where(['is_active' => 1], 'name', 'ASC');
    }

    /**
     * Get featured rituals
     */
    public function getFeatured(): array
    {
        return $this->where(['is_featured' => 1, 'is_active' => 1], 'view_count', 'DESC');
    }

    /**
     * Get rituals by category
     */
    public function getByCategory(string $category): array
    {
        return $this->where(['category' => $category, 'is_active' => 1], 'name', 'ASC');
    }

    /**
     * Get rituals by difficulty
     */
    public function getByDifficulty(string $difficulty): array
    {
        return $this->where(['difficulty' => $difficulty, 'is_active' => 1], 'name', 'ASC');
    }

    /**
     * Get ritual with steps and items
     */
    public function getWithDetails(int $id): ?array
    {
        $ritual = $this->find($id);

        if (!$ritual) {
            return null;
        }

        // Get steps
        $sql = "SELECT * FROM SAI_ritual_steps WHERE ritual_id = :ritual_id ORDER BY step_number ASC";
        $ritual['steps'] = $this->raw($sql, ['ritual_id' => $id]);

        // Get items
        $sql = "SELECT * FROM SAI_ritual_items WHERE ritual_id = :ritual_id ORDER BY is_mandatory DESC, item_name ASC";
        $ritual['items'] = $this->raw($sql, ['ritual_id' => $id]);

        return $ritual;
    }

    /**
     * Get all categories
     */
    public function getCategories(): array
    {
        $sql = "SELECT DISTINCT category FROM SAI_rituals WHERE is_active = 1 ORDER BY category ASC";
        $results = $this->raw($sql);
        return array_column($results, 'category');
    }

    /**
     * Get top ritual names (most popular/viewed)
     */
    public function getTopRitualNames(int $limit = 10): array
    {
        $sql = "
            SELECT DISTINCT name as ritual_name, view_count
            FROM SAI_rituals 
            WHERE is_active = 1 
            ORDER BY view_count DESC, name ASC 
            LIMIT :limit
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Increment view count
     */
    public function incrementView(int $id): bool
    {
        $sql = "UPDATE SAI_rituals SET view_count = view_count + 1 WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Search rituals
     */
    public function search(string $query): array
    {
        // Search in all rituals, not just active ones (for admin)
        // Or should it be active only? The admin might want to search inactive ones too.
        // The original query had 'is_active = 1'. 
        // But for Admin management, we should probably search ALL rituals.
        // However, I will stick to the original logic for now, or remove is_active constraint if this is for admin?
        // Let's assume this method is used by Frontend too? 
        // AdminController calls `all()` which returns everything.
        // So `search` should probably return everything for admin.
        // But this method might be used by User side too?
        // Let's create a new method `adminSearch` or just modify this to be more flexible?
        // User side uses `advancedSearch` or `getActiveRituals`.
        // This `search` method seems generic. 
        // I will make it search ALL rituals for now, or check usage.
        // Actually, for Admin, we definitely want to find Inactive rituals too.
        
        $sql = "
            SELECT * FROM SAI_rituals
            WHERE (
                name LIKE :name 
                OR name_sanskrit LIKE :sanskrit 
                OR category LIKE :category 
                OR description LIKE :desc
                OR deity LIKE :deity
            )
            ORDER BY name ASC
            LIMIT 50
        ";

        $term = "%$query%";
        $params = [
            'name' => $term,
            'sanskrit' => $term,
            'category' => $term,
            'desc' => $term,
            'deity' => $term
        ];

        return $this->raw($sql, $params);
    }

    /**
     * Get popular rituals
     */
    public function getPopular(int $limit = 10): array
    {
        $sql = "
            SELECT * FROM SAI_rituals 
            WHERE is_active = 1 
            ORDER BY view_count DESC, name ASC 
            LIMIT :limit
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Get active rituals with pagination
     */
    public function getActiveRituals(int $limit = 6, int $offset = 0): array
    {
        $sql = "
            SELECT * FROM SAI_rituals 
            WHERE is_active = 1 
            ORDER BY view_count DESC, name ASC 
            LIMIT :limit OFFSET :offset
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Get ritual statistics
     */
    public function getStats(): array
    {
        return [
            'total' => $this->count(),
            'active' => $this->count(['is_active' => 1]),
            'featured' => $this->count(['is_featured' => 1]),
        ];
    }

    /**
     * Find community names in DB that match the user's community
     * Uses OpenAI to intelligently identify spelling variations of the same community.
     * e.g. shelke ↔ shelake ✓, verma ↔ varama ✓, shelke ↔ shukla ✗
     * Results are cached in session to avoid repeated API calls.
     */
    public function findMatchingCommunities(string $userCommunity): array
    {
        $userCommunity = trim($userCommunity);
        if (empty($userCommunity)) {
            return [];
        }

        // Check session cache first
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $cacheKey = 'community_match_' . md5(strtolower($userCommunity));
        if (isset($_SESSION[$cacheKey]) && $_SESSION[$cacheKey]['expires'] > time()) {
            return $_SESSION[$cacheKey]['matches'];
        }

        // Get all distinct community names from the database
        $sql = "SELECT DISTINCT community_name FROM SAI_rituals WHERE community_name IS NOT NULL AND community_name != ''";
        $allCommunities = $this->raw($sql);
        $communityList = array_column($allCommunities, 'community_name');

        if (empty($communityList)) {
            return [];
        }

        // Quick exact match check (case-insensitive) — no API call needed
        $exactMatches = [];
        foreach ($communityList as $name) {
            if (strtolower(trim($name)) === strtolower($userCommunity)) {
                $exactMatches[] = $name;
            }
        }

        // If only one community in DB or exact match is the only one, skip API
        if (count($communityList) === 1 && !empty($exactMatches)) {
            return $exactMatches;
        }

        // Use OpenAI to find matching community name variations
        $matched = $this->askAIForCommunityMatch($userCommunity, $communityList);

        // If AI call fails, fall back to exact match
        if ($matched === null) {
            $matched = $exactMatches;
        }

        // Cache result for 30 minutes
        $_SESSION[$cacheKey] = [
            'matches' => $matched,
            'expires' => time() + 1800,
        ];

        return $matched;
    }

    /**
     * Ask OpenAI to identify which community names are variations of the user's community
     * Returns array of matching names, or null if API call fails
     */
    private function askAIForCommunityMatch(string $userCommunity, array $communityList): ?array
    {
        $apiKey = getenv('AI_API_KEY') ?: '';
        if (empty($apiKey)) {
            return null; // No API key, fall back
        }

        $listStr = implode(', ', array_map(fn($n) => '"' . $n . '"', $communityList));

        $prompt = "I have a user whose community/caste name is: \"$userCommunity\"

Here is a list of community names from our database: [$listStr]

Which of these database community names are spelling variations, transliterations, or alternate names for the SAME community/caste as \"$userCommunity\"?

Rules:
- Only match names that truly refer to the same community/caste
- Different communities (e.g., Shelke vs Shukla, Verma vs Sharma) must NOT match
- Spelling variations of the same name (e.g., Shelke/Shelake, Verma/Varama) SHOULD match
- Respond with ONLY a JSON array of matching names from the database list
- If no matches found, respond with an empty array []

Respond with valid JSON only, no explanation.";

        $data = [
            'model' => getenv('AI_MODEL') ?: 'gpt-4o-mini',
            'messages' => [
                ['role' => 'system', 'content' => 'You are an expert in Indian community/caste names and their various spellings and transliterations. Respond with JSON only.'],
                ['role' => 'user', 'content' => $prompt],
            ],
            'temperature' => 0.1,
            'max_tokens' => 200,
            'response_format' => ['type' => 'json_object'],
        ];

        try {
            $ch = curl_init('https://api.openai.com/v1/chat/completions');
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json',
                    'Authorization: Bearer ' . $apiKey,
                ],
                CURLOPT_POSTFIELDS => json_encode($data),
                CURLOPT_TIMEOUT => 15,
                CURLOPT_CONNECTTIMEOUT => 5,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => 0,
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode !== 200 || !$response) {
                return null;
            }

            $result = json_decode($response, true);
            $content = $result['choices'][0]['message']['content'] ?? '';
            $parsed = json_decode($content, true);

            // Handle both {"matches": [...]} and plain [...] responses
            if (is_array($parsed)) {
                if (isset($parsed['matches'])) {
                    return $parsed['matches'];
                }
                // If it's a flat array of strings
                if (!empty($parsed) && is_string(reset($parsed))) {
                    return $parsed;
                }
                // If first key is numeric, it's a flat array  
                if (array_keys($parsed) === range(0, count($parsed) - 1)) {
                    return $parsed;
                }
                // Try to get the first array value
                foreach ($parsed as $val) {
                    if (is_array($val)) {
                        return $val;
                    }
                }
                return [];
            }

            return null;
        } catch (\Exception $e) {
            return null; // Fall back silently
        }
    }


    /**
     * Get active rituals filtered by community with OpenAI fuzzy matching
     * Shows universal rituals (NULL community) + community-matching rituals
     */
    public function getActiveForCommunity(string $communityName, int $limit = 6, int $offset = 0): array
    {
        $matchedCommunities = $this->findMatchingCommunities($communityName);

        if (empty($matchedCommunities)) {
            // No matching communities found — show only universal rituals
            $sql = "
                SELECT * FROM SAI_rituals 
                WHERE is_active = 1 AND (community_name IS NULL OR community_name = '')
                ORDER BY view_count DESC, name ASC 
                LIMIT :limit OFFSET :offset
            ";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        }

        // Build IN clause with matched community names
        $placeholders = [];
        $params = [];
        foreach ($matchedCommunities as $i => $name) {
            $key = ":comm_$i";
            $placeholders[] = $key;
            $params[$key] = $name;
        }
        $inClause = implode(', ', $placeholders);

        $sql = "
            SELECT * FROM SAI_rituals 
            WHERE is_active = 1 
            AND (community_name IS NULL OR community_name = '' OR community_name IN ($inClause))
            ORDER BY view_count DESC, name ASC 
            LIMIT :limit OFFSET :offset
        ";

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val, \PDO::PARAM_STR);
        }
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Count active rituals for a community (OpenAI fuzzy match)
     */
    public function countForCommunity(string $communityName): int
    {
        $matchedCommunities = $this->findMatchingCommunities($communityName);

        if (empty($matchedCommunities)) {
            $sql = "SELECT COUNT(*) as total FROM SAI_rituals WHERE is_active = 1 AND (community_name IS NULL OR community_name = '')";
            return (int) $this->raw($sql)[0]['total'];
        }

        $placeholders = [];
        $params = [];
        foreach ($matchedCommunities as $i => $name) {
            $key = ":comm_$i";
            $placeholders[] = $key;
            $params[$key] = $name;
        }
        $inClause = implode(', ', $placeholders);

        $sql = "SELECT COUNT(*) as total FROM SAI_rituals WHERE is_active = 1 AND (community_name IS NULL OR community_name = '' OR community_name IN ($inClause))";

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val, \PDO::PARAM_STR);
        }
        $stmt->execute();

        return (int) $stmt->fetch()['total'];
    }

    /**
     * Advanced search by community, religion, ritual name, and other criteria
     * Uses OpenAI for fuzzy community matching
     */
    public function advancedSearch(array $criteria): array
    {
        $conditions = ['is_active = 1'];
        $params = [];
        $extraBinds = []; // For IN clause params that need bindValue

        if (!empty($criteria['community_name'])) {
            $matchedCommunities = $this->findMatchingCommunities($criteria['community_name']);
            
            if (!empty($matchedCommunities)) {
                $placeholders = [];
                foreach ($matchedCommunities as $i => $name) {
                    $key = ":comm_$i";
                    $placeholders[] = $key;
                    $extraBinds[$key] = $name;
                }
                $inClause = implode(', ', $placeholders);
                $conditions[] = "(community_name IS NULL OR community_name = '' OR community_name IN ($inClause))";
            } else {
                $conditions[] = "(community_name IS NULL OR community_name = '')";
            }
        }

        if (!empty($criteria['religion'])) {
            $conditions[] = "religion LIKE :religion";
            $params['religion'] = '%' . $criteria['religion'] . '%';
        }

        if (!empty($criteria['ritual_name'])) {
            $conditions[] = "(name LIKE :ritual_name OR name_sanskrit LIKE :ritual_name_sanskrit)";
            $params['ritual_name'] = '%' . $criteria['ritual_name'] . '%';
            $params['ritual_name_sanskrit'] = '%' . $criteria['ritual_name'] . '%';
        }

        if (!empty($criteria['category'])) {
            $conditions[] = "category LIKE :category";
            $params['category'] = '%' . $criteria['category'] . '%';
        }

        if (!empty($criteria['deity'])) {
            $conditions[] = "deity LIKE :deity";
            $params['deity'] = '%' . $criteria['deity'] . '%';
        }

        $sql = "SELECT * FROM SAI_rituals WHERE " . implode(' AND ', $conditions) .
            " ORDER BY view_count DESC, name ASC LIMIT 50";

        $stmt = $this->db->prepare($sql);
        foreach ($extraBinds as $key => $val) {
            $stmt->bindValue($key, $val, \PDO::PARAM_STR);
        }
        foreach ($params as $key => $val) {
            $stmt->bindValue(":$key", $val, \PDO::PARAM_STR);
        }
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Save AI generated ritual to global database
     */
    public function saveFromAI(array $aiData, ?int $createdBy = null): int
    {
        // Normalize difficulty to expected values (easy, medium, hard)
        $difficulty = strtolower($aiData['difficulty'] ?? 'medium');
        $difficultyMap = [
            'easy' => 'easy', 'simple' => 'easy', 'low' => 'easy', 'beginner' => 'easy',
            'medium' => 'medium', 'moderate' => 'medium', 'intermediate' => 'medium', 'normal' => 'medium',
            'hard' => 'hard', 'difficult' => 'hard', 'high' => 'hard', 'advanced' => 'hard', 'expert' => 'hard',
        ];
        $normalizedDifficulty = $difficultyMap[$difficulty] ?? 'medium';
        
        $ritualId = $this->create([
            'name' => $aiData['name'],
            'name_sanskrit' => $aiData['name_sanskrit'] ?? null,
            'category' => $aiData['category'] ?? 'General',
            'sub_category' => $aiData['sub_category'] ?? null,
            'description' => $aiData['description'] ?? null,
            'significance' => $aiData['significance'] ?? null,
            'duration_minutes' => (int) ($aiData['duration_minutes'] ?? 60),
            'difficulty' => $normalizedDifficulty,
            'occasion_type' => $aiData['occasion_type'] ?? null,
            'best_time' => $aiData['best_time'] ?? null,
            'best_tithi' => $aiData['best_tithi'] ?? null,
            'deity' => $aiData['deity'] ?? null,
            'is_active' => 1,
            'is_featured' => 0,
            'created_by' => $createdBy,
        ]);

        // Add religion and community columns if data provided
        if (!empty($aiData['religion']) || !empty($aiData['community_name'])) {
            $sets = [];
            $params = ['id' => $ritualId];

            if (!empty($aiData['religion'])) {
                $sets[] = "religion = :religion";
                $params['religion'] = $aiData['religion'];
            }

            if (!empty($aiData['community_name'])) {
                $sets[] = "community_name = :community_name";
                $params['community_name'] = $aiData['community_name'];
            }

            if (!empty($sets)) {
                $sql = "UPDATE SAI_rituals SET " . implode(', ', $sets) . " WHERE id = :id";
                $stmt = $this->db->prepare($sql);
                $stmt->execute($params);
            }
        }

        // Add steps
        if (!empty($aiData['steps'])) {
            foreach ($aiData['steps'] as $index => $step) {
                $sql = "INSERT INTO SAI_ritual_steps 
                        (ritual_id, step_number, title, title_sanskrit, description, mantra, mantra_meaning, duration_minutes, is_optional, special_instructions, created_at)
                        VALUES (:ritual_id, :step_number, :title, :title_sanskrit, :description, :mantra, :mantra_meaning, :duration_minutes, :is_optional, :special_instructions, NOW())";

                $stmt = $this->db->prepare($sql);
                $stmt->execute([
                    'ritual_id' => $ritualId,
                    'step_number' => $step['step_number'] ?? ($index + 1),
                    'title' => $step['title'],
                    'title_sanskrit' => $step['title_sanskrit'] ?? null,
                    'description' => $step['description'] ?? null,
                    'mantra' => $step['mantra'] ?? null,
                    'mantra_meaning' => $step['mantra_meaning'] ?? null,
                    'duration_minutes' => $step['duration_minutes'] ?? 5,
                    'is_optional' => (int) ($step['is_optional'] ?? 0),
                    'special_instructions' => $step['special_instructions'] ?? null,
                ]);
            }
        }

        // Add items
        if (!empty($aiData['items'])) {
            foreach ($aiData['items'] as $item) {
                $sql = "INSERT INTO SAI_ritual_items 
                        (ritual_id, item_name, item_name_local, quantity, unit, is_mandatory, description, where_to_buy, created_at)
                        VALUES (:ritual_id, :item_name, :item_name_local, :quantity, :unit, :is_mandatory, :description, :where_to_buy, NOW())";

                $stmt = $this->db->prepare($sql);
                $stmt->execute([
                    'ritual_id' => $ritualId,
                    'item_name' => $item['item_name'],
                    'item_name_local' => $item['item_name_local'] ?? null,
                    'quantity' => $item['quantity'] ?? 1,
                    'unit' => $item['unit'] ?? 'piece',
                    'is_mandatory' => (int) ($item['is_mandatory'] ?? 1),
                    'description' => $item['description'] ?? null,
                    'where_to_buy' => $item['where_to_buy'] ?? null,
                ]);
            }
        }

        return $ritualId;
    }
}
