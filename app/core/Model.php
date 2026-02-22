<?php
/**
 * Sanskar AI - Base Model
 * ========================
 * Base model with PDO integration and common query methods
 */

namespace App\Core;

use App\Config\Database;
use PDO;

abstract class Model
{
    /**
     * Table name (override in child classes)
     */
    protected string $table = '';
    
    /**
     * Primary key column
     */
    protected string $primaryKey = 'id';
    
    /**
     * Fillable columns (for mass assignment)
     */
    protected array $fillable = [];
    
    /**
     * Hidden columns (excluded from toArray)
     */
    protected array $hidden = ['password_hash'];
    
    /**
     * Timestamps columns
     */
    protected bool $timestamps = true;
    
    /**
     * PDO connection
     */
    protected PDO $db;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->db = Database::getConnection();
    }
    
    /**
     * Get table name
     */
    public function getTable(): string
    {
        return $this->table;
    }
    
    /**
     * Find record by primary key
     */
    public function find(int $id): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        
        $result = $stmt->fetch();
        return $result ?: null;
    }
    
    /**
     * Find record by column value
     */
    public function findBy(string $column, $value): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$column} = :value LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['value' => $value]);
        
        $result = $stmt->fetch();
        return $result ?: null;
    }
    
    /**
     * Get all records
     */
    public function all(?string $orderBy = null, string $direction = 'ASC'): array
    {
        $sql = "SELECT * FROM {$this->table}";
        
        if ($orderBy) {
            $direction = strtoupper($direction) === 'DESC' ? 'DESC' : 'ASC';
            $sql .= " ORDER BY {$orderBy} {$direction}";
        }
        
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
    
    /**
     * Get records with conditions
     */
    public function where(array $conditions, ?string $orderBy = null, string $direction = 'ASC'): array
    {
        $whereClauses = [];
        $params = [];
        
        foreach ($conditions as $column => $value) {
            $whereClauses[] = "{$column} = :{$column}";
            $params[$column] = $value;
        }
        
        $sql = "SELECT * FROM {$this->table} WHERE " . implode(' AND ', $whereClauses);
        
        if ($orderBy) {
            $direction = strtoupper($direction) === 'DESC' ? 'DESC' : 'ASC';
            $sql .= " ORDER BY {$orderBy} {$direction}";
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll();
    }
    
    /**
     * Get first record with conditions
     */
    public function whereFirst(array $conditions): ?array
    {
        $results = $this->where($conditions);
        return $results[0] ?? null;
    }
    
    /**
     * Create new record
     */
    public function create(array $data): int
    {
        // Filter to fillable columns
        $data = $this->filterFillable($data);
        
        // Add timestamps
        if ($this->timestamps) {
            $data['created_at'] = date('Y-m-d H:i:s');
            $data['updated_at'] = date('Y-m-d H:i:s');
        }
        
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        
        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($data);
        
        return (int) $this->db->lastInsertId();
    }
    
    /**
     * Update record
     */
    public function update(int $id, array $data): bool
    {
        // Filter to fillable columns
        $data = $this->filterFillable($data);
        
        // Add updated timestamp
        if ($this->timestamps) {
            $data['updated_at'] = date('Y-m-d H:i:s');
        }
        
        $setClauses = [];
        foreach (array_keys($data) as $column) {
            $setClauses[] = "{$column} = :{$column}";
        }
        
        $data['id'] = $id;
        
        $sql = "UPDATE {$this->table} SET " . implode(', ', $setClauses) . " WHERE {$this->primaryKey} = :id";
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute($data);
    }
    
    /**
     * Delete record
     */
    public function delete(int $id): bool
    {
        $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id";
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute(['id' => $id]);
    }
    
    /**
     * Count records
     */
    public function count(array $conditions = []): int
    {
        $sql = "SELECT COUNT(*) as count FROM {$this->table}";
        $params = [];
        
        if (!empty($conditions)) {
            $whereClauses = [];
            foreach ($conditions as $column => $value) {
                $whereClauses[] = "{$column} = :{$column}";
                $params[$column] = $value;
            }
            $sql .= " WHERE " . implode(' AND ', $whereClauses);
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        return (int) $stmt->fetch()['count'];
    }
    
    /**
     * Check if record exists
     */
    public function exists(array $conditions): bool
    {
        return $this->count($conditions) > 0;
    }
    
    /**
     * Paginate records
     */
    public function paginate(int $page = 1, int $perPage = 15, array $conditions = [], ?string $orderBy = null, string $direction = 'DESC'): array
    {
        $offset = ($page - 1) * $perPage;
        $total = $this->count($conditions);
        $totalPages = ceil($total / $perPage);
        
        $sql = "SELECT * FROM {$this->table}";
        $params = [];
        
        if (!empty($conditions)) {
            $whereClauses = [];
            foreach ($conditions as $column => $value) {
                $whereClauses[] = "{$column} = :{$column}";
                $params[$column] = $value;
            }
            $sql .= " WHERE " . implode(' AND ', $whereClauses);
        }
        
        if ($orderBy) {
            $direction = strtoupper($direction) === 'DESC' ? 'DESC' : 'ASC';
            $sql .= " ORDER BY {$orderBy} {$direction}";
        }
        
        $sql .= " LIMIT {$perPage} OFFSET {$offset}";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        return [
            'data' => $stmt->fetchAll(),
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'total_pages' => $totalPages,
            'has_more' => $page < $totalPages,
        ];
    }
    
    /**
     * Execute raw query
     */
    public function raw(string $sql, array $params = []): array
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    /**
     * Execute raw query and return single result
     */
    public function rawOne(string $sql, array $params = []): ?array
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        return $result ?: null;
    }
    
    /**
     * Filter data to fillable columns
     */
    protected function filterFillable(array $data): array
    {
        if (empty($this->fillable)) {
            return $data;
        }
        
        return array_intersect_key($data, array_flip($this->fillable));
    }
    
    /**
     * Remove hidden columns from data
     */
    protected function removeHidden(array $data): array
    {
        return array_diff_key($data, array_flip($this->hidden));
    }
    
    /**
     * Begin transaction
     */
    public function beginTransaction(): bool
    {
        return $this->db->beginTransaction();
    }
    
    /**
     * Commit transaction
     */
    public function commit(): bool
    {
        return $this->db->commit();
    }
    
    /**
     * Rollback transaction
     */
    public function rollback(): bool
    {
        return $this->db->rollBack();
    }
}
