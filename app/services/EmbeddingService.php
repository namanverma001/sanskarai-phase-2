<?php
/**
 * Sanskar AI - Embedding Service
 * ================================
 * Handles OpenAI embedding generation and semantic search.
 * Auto-generates embeddings when rituals are created/updated.
 */

namespace App\Services;

use App\Config\Database;
use PDO;

class EmbeddingService
{
    private string $apiKey;
    private PDO $pdo;
    private string $model = 'text-embedding-ada-002';

    public function __construct()
    {
        Database::loadEnv();
        $this->apiKey = getenv('AI_API_KEY') ?: '';
        $this->pdo = Database::getConnection();
    }

    /**
     * Generate an embedding for a ritual and store it in SAI_ritual_embeddings.
     * Called automatically when admin creates or updates a ritual.
     *
     * @param int         $ritualId      The ritual's ID
     * @param string      $name          Ritual name
     * @param string|null $communityName Community name
     * @param string|null $religion      Religion
     * @return bool True on success, false on failure
     */
    public function generateAndStore(int $ritualId, string $name, ?string $communityName = null, ?string $religion = null): bool
    {
        if (empty($this->apiKey)) {
            error_log("EmbeddingService: AI_API_KEY not set, skipping embedding for ritual #$ritualId");
            return false;
        }

        $combinedText = $this->buildCombinedText($name, $communityName, $religion);

        if (empty(trim($combinedText))) {
            return false;
        }

        // Check if embedding already exists and text hasn't changed
        $existing = $this->getExistingEmbedding($ritualId);
        if ($existing && $existing['combined_text'] === $combinedText) {
            return true; // Already up-to-date
        }

        // Generate embedding via OpenAI
        $embedding = $this->callOpenAIEmbedding($combinedText);
        if (empty($embedding)) {
            error_log("EmbeddingService: Failed to generate embedding for ritual #$ritualId");
            return false;
        }

        // Upsert into SAI_ritual_embeddings
        return $this->upsertEmbedding($ritualId, $name, $communityName, $religion, $combinedText, $embedding);
    }

    /**
     * Perform semantic search against stored embeddings.
     * Returns rituals ranked by cosine similarity to the query.
     *
     * @param string $query  User's search query
     * @param int    $limit  Max results to return (1-50)
     * @return array Array of matching rituals with similarity scores
     */
    public function semanticSearch(string $query, int $limit = 10): array
    {
        if (empty($this->apiKey)) {
            return [];
        }

        $limit = max(1, min(50, $limit));

        // 1. Embed the user's query
        $queryEmbedding = $this->callOpenAIEmbedding($query);
        if (empty($queryEmbedding)) {
            return [];
        }

        // 2. Load all stored embeddings (with ritual details)
        $rows = $this->pdo->query("
            SELECT e.ritual_id, e.ritual_name, e.community_name, e.religion, e.embedding,
                   r.category, r.description, r.difficulty, r.duration_minutes
            FROM SAI_ritual_embeddings e
            INNER JOIN SAI_rituals r ON r.id = e.ritual_id AND r.is_active = 1
        ")->fetchAll(PDO::FETCH_ASSOC);

        if (empty($rows)) {
            return [];
        }

        // 3. Compute cosine similarity for each ritual
        $results = [];
        foreach ($rows as $row) {
            $storedEmbedding = json_decode($row['embedding'], true);
            if (empty($storedEmbedding)) continue;

            $similarity = $this->cosineSimilarity($queryEmbedding, $storedEmbedding);

            $results[] = [
                'ritual_id'        => (int) $row['ritual_id'],
                'ritual_name'      => $row['ritual_name'],
                'community_name'   => $row['community_name'],
                'religion'         => $row['religion'],
                'category'         => $row['category'],
                'description'      => $row['description'],
                'difficulty'       => $row['difficulty'],
                'duration_minutes' => (int) $row['duration_minutes'],
                'similarity_score' => round($similarity, 6),
            ];
        }

        // 4. Sort by similarity (descending) and take top N
        usort($results, fn($a, $b) => $b['similarity_score'] <=> $a['similarity_score']);

        return array_slice($results, 0, $limit);
    }

    /**
     * Delete embedding for a ritual (when ritual is deleted)
     */
    public function deleteEmbedding(int $ritualId): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM SAI_ritual_embeddings WHERE ritual_id = ?");
        return $stmt->execute([$ritualId]);
    }

    // ═══════════════════════════════════════════════════════════
    //  PRIVATE HELPERS
    // ═══════════════════════════════════════════════════════════

    /**
     * Build the combined text string for embedding
     */
    private function buildCombinedText(?string $name, ?string $community, ?string $religion): string
    {
        $parts = [];
        if (!empty($name))      $parts[] = "Ritual: $name";
        if (!empty($community)) $parts[] = "Community: $community";
        if (!empty($religion))  $parts[] = "Religion: $religion";
        return implode(' | ', $parts);
    }

    /**
     * Get existing embedding record for a ritual
     */
    private function getExistingEmbedding(int $ritualId): ?array
    {
        $stmt = $this->pdo->prepare("SELECT combined_text FROM SAI_ritual_embeddings WHERE ritual_id = ?");
        $stmt->execute([$ritualId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /**
     * Upsert embedding into SAI_ritual_embeddings
     */
    private function upsertEmbedding(int $ritualId, string $name, ?string $community, ?string $religion, string $combinedText, array $embedding): bool
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO SAI_ritual_embeddings (ritual_id, ritual_name, community_name, religion, combined_text, embedding, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, NOW())
            ON DUPLICATE KEY UPDATE
                ritual_name = VALUES(ritual_name),
                community_name = VALUES(community_name),
                religion = VALUES(religion),
                combined_text = VALUES(combined_text),
                embedding = VALUES(embedding),
                updated_at = NOW()
        ");

        return $stmt->execute([
            $ritualId,
            $name,
            $community,
            $religion,
            $combinedText,
            json_encode($embedding),
        ]);
    }

    /**
     * Call OpenAI Embeddings API (text-embedding-ada-002)
     * Returns a 1536-dimension float array, or empty on failure.
     */
    private function callOpenAIEmbedding(string $text): array
    {
        $payload = json_encode([
            'input' => $text,
            'model' => $this->model,
        ]);

        $ch = curl_init('https://api.openai.com/v1/embeddings');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $payload,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->apiKey,
            ],
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => 0,
        ]);

        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            error_log("EmbeddingService cURL error: " . curl_error($ch));
            curl_close($ch);
            return [];
        }

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            $err = json_decode($response, true);
            $msg = $err['error']['message'] ?? "HTTP $httpCode";
            error_log("EmbeddingService OpenAI error: $msg");
            return [];
        }

        $result = json_decode($response, true);
        return $result['data'][0]['embedding'] ?? [];
    }

    /**
     * Cosine similarity between two vectors
     */
    private function cosineSimilarity(array $a, array $b): float
    {
        $dotProduct = 0.0;
        $normA = 0.0;
        $normB = 0.0;

        $len = min(count($a), count($b));
        for ($i = 0; $i < $len; $i++) {
            $dotProduct += $a[$i] * $b[$i];
            $normA      += $a[$i] * $a[$i];
            $normB      += $b[$i] * $b[$i];
        }

        $denominator = sqrt($normA) * sqrt($normB);
        if ($denominator == 0) return 0.0;

        return $dotProduct / $denominator;
    }
}
