<?php
/**
 * Sanskar AI - Vendor Model
 * ==========================
 * Vendor model with location-based search methods
 */

namespace App\Models;

use App\Core\Model;
use PDO;

class Vendor extends Model
{
    protected string $table = 'SAI_vendors';
    
    protected array $fillable = [
        'name',
        'category',
        'description',
        'contact_person',
        'email',
        'phone',
        'alternate_phone',
        'whatsapp',
        'website',
        'address_line1',
        'address_line2',
        'city',
        'state',
        'pincode',
        'country',
        'latitude',
        'longitude',
        'map_url',
        'service_area_km',
        'min_price',
        'max_price',
        'services_offered',
        'logo_url',
        'gallery_images',
        'average_rating',
        'total_reviews',
        'is_active',
        'is_featured',
        'is_verified',
        'added_by',
    ];
    
    /**
     * Vendor categories
     */
    public const CATEGORIES = [
        'photographer' => 'Photographer',
        'catering' => 'Catering Service',
        'decorator' => 'Decorator',
        'florist' => 'Florist',
        'music' => 'Music & DJ',
        'lighting' => 'Lighting',
        'tent_house' => 'Tent House / Pandal',
        'makeup_artist' => 'Makeup Artist',
        'mehendi_artist' => 'Mehendi Artist',
        'videographer' => 'Videographer',
        'invitation_cards' => 'Invitation Cards',
        'travel' => 'Travel & Transport',
        'other' => 'Other',
    ];
    
    /**
     * Get category display name
     */
    public static function getCategoryName(string $category): string
    {
        return self::CATEGORIES[$category] ?? ucfirst($category);
    }
    
    /**
     * Get all active vendors
     */
    public function getActiveVendors(?string $category = null, ?string $search = null, int $limit = 50, int $offset = 0): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE is_active = 1";
        $params = [];
        
        if ($category) {
            $sql .= " AND category = :category";
            $params['category'] = $category;
        }
        
        if ($search) {
            $sql .= " AND (name LIKE :search OR description LIKE :search2 OR city LIKE :search3)";
            $params['search'] = "%{$search}%";
            $params['search2'] = "%{$search}%";
            $params['search3'] = "%{$search}%";
        }
        
        $sql .= " ORDER BY is_featured DESC, average_rating DESC, name ASC LIMIT :limit OFFSET :offset";
        
        $stmt = $this->db->prepare($sql);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue(":{$key}", $value);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Find vendors near a location using Haversine formula
     * 
     * @param float $latitude User's latitude
     * @param float $longitude User's longitude
     * @param float $radiusKm Search radius in kilometers
     * @param string|null $category Filter by category
     * @param string|null $search Search term
     * @param int $limit Max results
     * @return array Vendors with distance
     */
    public function findNearbyVendors(
        float $latitude,
        float $longitude,
        float $radiusKm = 50,
        ?string $category = null,
        ?string $search = null,
        int $limit = 50
    ): array {
        // Haversine formula for calculating distance
        $sql = "
            SELECT *,
                (
                    6371 * acos(
                        cos(radians(:lat1)) * cos(radians(latitude)) * 
                        cos(radians(longitude) - radians(:lng1)) + 
                        sin(radians(:lat2)) * sin(radians(latitude))
                    )
                ) AS distance_km
            FROM {$this->table}
            WHERE is_active = 1
        ";
        
        $params = [
            'lat1' => $latitude,
            'lng1' => $longitude,
            'lat2' => $latitude,
        ];
        
        if ($category) {
            $sql .= " AND category = :category";
            $params['category'] = $category;
        }
        
        if ($search) {
            $sql .= " AND (name LIKE :search OR description LIKE :search2 OR city LIKE :search3)";
            $params['search'] = "%{$search}%";
            $params['search2'] = "%{$search}%";
            $params['search3'] = "%{$search}%";
        }
        
        $sql .= "
            HAVING distance_km <= :radius
            ORDER BY distance_km ASC
            LIMIT :limit
        ";
        
        $stmt = $this->db->prepare($sql);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue(":{$key}", $value);
        }
        $stmt->bindValue(':radius', $radiusKm, PDO::PARAM_STR);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Get vendors by category
     */
    public function getByCategory(string $category, bool $activeOnly = true): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE category = :category";
        
        if ($activeOnly) {
            $sql .= " AND is_active = 1";
        }
        
        $sql .= " ORDER BY is_featured DESC, average_rating DESC, name ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['category' => $category]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get vendors by city
     */
    public function getByCity(string $city, ?string $category = null, bool $activeOnly = true): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE city LIKE :city";
        $params = ['city' => "%{$city}%"];
        
        if ($category) {
            $sql .= " AND category = :category";
            $params['category'] = $category;
        }
        
        if ($activeOnly) {
            $sql .= " AND is_active = 1";
        }
        
        $sql .= " ORDER BY is_featured DESC, average_rating DESC, name ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    /**
     * Get featured vendors
     */
    public function getFeatured(int $limit = 10): array
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE is_active = 1 AND is_featured = 1 
                ORDER BY average_rating DESC 
                LIMIT :limit";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Get all vendors for admin (including inactive)
     */
    public function getAllForAdmin(?string $category = null, ?string $search = null): array
    {
        $sql = "SELECT v.*, u.name as added_by_name 
                FROM {$this->table} v 
                LEFT JOIN SAI_users u ON v.added_by = u.id 
                WHERE 1=1";
        $params = [];
        
        if ($category) {
            $sql .= " AND v.category = :category";
            $params['category'] = $category;
        }
        
        if ($search) {
            $sql .= " AND (v.name LIKE :search OR v.city LIKE :search2 OR v.phone LIKE :search3)";
            $params['search'] = "%{$search}%";
            $params['search2'] = "%{$search}%";
            $params['search3'] = "%{$search}%";
        }
        
        $sql .= " ORDER BY v.created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    /**
     * Get unique cities
     */
    public function getCities(): array
    {
        $sql = "SELECT DISTINCT city FROM {$this->table} WHERE is_active = 1 ORDER BY city ASC";
        $stmt = $this->db->query($sql);
        return array_column($stmt->fetchAll(), 'city');
    }
    
    /**
     * Get vendor count by category
     */
    public function getCountByCategory(): array
    {
        $sql = "SELECT category, COUNT(*) as count 
                FROM {$this->table} 
                WHERE is_active = 1 
                GROUP BY category 
                ORDER BY count DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
    
    /**
     * Toggle vendor active status
     */
    public function toggleStatus(int $id): bool
    {
        $sql = "UPDATE {$this->table} SET is_active = NOT is_active WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }
    
    /**
     * Toggle featured status
     */
    public function toggleFeatured(int $id): bool
    {
        $sql = "UPDATE {$this->table} SET is_featured = NOT is_featured WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }
}
