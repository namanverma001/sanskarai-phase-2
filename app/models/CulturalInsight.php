<?php
/**
 * Sanskar AI - Cultural Insight Model
 * =====================================
 * Cultural insights and knowledge base
 */

namespace App\Models;

use App\Core\Model;

class CulturalInsight extends Model
{
    protected string $table = 'SAI_cultural_insights';
    
    protected array $fillable = [
        'title',
        'slug',
        'category',
        'sub_category',
        'content',
        'summary',
        'featured_image',
        'region',
        'language',
        'source',
        'source_url',
        'tags',
        'related_rituals',
        'is_published',
        'is_featured',
        'created_by',
        'reviewed_by',
        'published_at',
    ];
    
    /**
     * Get published insights
     */
    public function getPublished(): array
    {
        return $this->where(['is_published' => 1], 'created_at', 'DESC');
    }
    
    /**
     * Get featured insights
     */
    public function getFeatured(int $limit = 5): array
    {
        $sql = "
            SELECT * FROM SAI_cultural_insights 
            WHERE is_published = 1 AND is_featured = 1 
            ORDER BY view_count DESC, created_at DESC
            LIMIT :limit
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    /**
     * Get by category
     */
    public function getByCategory(string $category): array
    {
        return $this->where(['category' => $category, 'is_published' => 1], 'title', 'ASC');
    }
    
    /**
     * Get by slug
     */
    public function getBySlug(string $slug): ?array
    {
        return $this->findBy('slug', $slug);
    }
    
    /**
     * Search insights
     */
    public function search(string $query): array
    {
        $sql = "
            SELECT * FROM SAI_cultural_insights
            WHERE is_published = 1 AND (
                MATCH(title, content, summary) AGAINST(:query IN NATURAL LANGUAGE MODE)
                OR title LIKE :like_query
                OR tags LIKE :like_query
            )
            ORDER BY view_count DESC
            LIMIT 20
        ";
        
        return $this->raw($sql, [
            'query' => $query,
            'like_query' => "%$query%",
        ]);
    }
    
    /**
     * Get categories
     */
    public function getCategories(): array
    {
        $sql = "SELECT DISTINCT category FROM SAI_cultural_insights WHERE is_published = 1 ORDER BY category ASC";
        $results = $this->raw($sql);
        return array_column($results, 'category');
    }
    
    /**
     * Increment view count
     */
    public function incrementView(int $id): bool
    {
        $sql = "UPDATE SAI_cultural_insights SET view_count = view_count + 1 WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }
    
    /**
     * Generate slug
     */
    public function generateSlug(string $title): string
    {
        $slug = strtolower(trim($title));
        $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
        $slug = preg_replace('/-+/', '-', $slug);
        $slug = trim($slug, '-');
        
        // Check for uniqueness
        $original = $slug;
        $count = 1;
        while ($this->exists(['slug' => $slug])) {
            $slug = $original . '-' . $count;
            $count++;
        }
        
        return $slug;
    }
}
