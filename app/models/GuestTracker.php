<?php
namespace App\Models;

use App\Core\Model;

class GuestTracker extends Model
{
    protected string $table = 'SAI_guest_tracking';
    protected array $fillable = ['session_id', 'ip_address', 'action_type', 'action_details'];
    protected bool $timestamps = false;

    /**
     * Record a page view
     *
     * @param string $url The URL or page name viewed
     * @return int Insert ID
     */
    public function recordView(string $url): int
    {
        return $this->create([
            'session_id' => session_id() ?: 'unknown',
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
            'action_type' => 'view',
            'action_details' => $url
        ]);
    }

    /**
     * Record a search query
     *
     * @param array $criteria The search criteria
     * @return int Insert ID
     */
    public function recordSearch(array $criteria): int
    {
        return $this->create([
            'session_id' => session_id() ?: 'unknown',
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
            'action_type' => 'search',
            'action_details' => json_encode($criteria)
        ]);
    }

    /**
     * Record an AI Pandit interaction
     *
     * @param string $message The message/prompt sent by the guest
     * @return int Insert ID
     */
    public function recordAIPandit(string $message): int
    {
        return $this->create([
            'session_id' => session_id() ?: 'unknown',
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
            'action_type' => 'ai_pandit',
            'action_details' => $message
        ]);
    }

    /**
     * Get statistics for the admin dashboard
     *
     * @param string|null $startDate
     * @param string|null $endDate
     * @return array
     */
    public function getStats(?string $startDate = null, ?string $endDate = null): array
    {
        $conditions = [];
        $whereSql = "";
        $params = [];
        
        if ($startDate) {
            $conditions[] = "created_at >= :start_date";
            $params['start_date'] = $startDate . ' 00:00:00';
        }
        if ($endDate) {
            $conditions[] = "created_at <= :end_date";
            $params['end_date'] = $endDate . ' 23:59:59';
        }
        
        if (!empty($conditions)) {
            $whereSql = " WHERE " . implode(' AND ', $conditions);
        }

        $totalViews = $this->countWithDate('view', $whereSql, $params);
        $totalSearches = $this->countWithDate('search', $whereSql, $params);
        $totalAiPandit = $this->countWithDate('ai_pandit', $whereSql, $params);
        
        $sql = "SELECT COUNT(DISTINCT session_id) as unique_visitors FROM {$this->table}{$whereSql}";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $uniqueVisitors = $stmt->fetch()['unique_visitors'] ?? 0;

        return [
            'total_views' => $totalViews,
            'total_searches' => $totalSearches,
            'total_ai_pandit' => $totalAiPandit,
            'unique_visitors' => $uniqueVisitors
        ];
    }
    
    private function countWithDate(string $type, string $whereSql, array $params): int
    {
        $sql = "SELECT COUNT(*) as count FROM {$this->table}";
        if ($whereSql) {
            $sql .= $whereSql . " AND action_type = :type";
        } else {
            $sql .= " WHERE action_type = :type";
        }
        
        $mergedParams = array_merge($params, ['type' => $type]);
        $stmt = $this->db->prepare($sql);
        $stmt->execute($mergedParams);
        return (int) $stmt->fetch()['count'];
    }

    /**
     * Get recent logs
     *
     * @param string|null $type 'view' or 'search', null for all
     * @param string|null $startDate
     * @param string|null $endDate
     * @return array
     */
    public function getRecentLogs(?string $type = null, int $limit = 50, ?string $startDate = null, ?string $endDate = null): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE 1=1";
        $params = [];

        if ($type) {
            $sql .= " AND action_type = :type";
            $params['type'] = $type;
        }
        
        if ($startDate) {
            $sql .= " AND created_at >= :start_date";
            $params['start_date'] = $startDate . ' 00:00:00';
        }
        if ($endDate) {
            $sql .= " AND created_at <= :end_date";
            $params['end_date'] = $endDate . ' 23:59:59';
        }

        $sql .= " ORDER BY created_at DESC LIMIT " . (int)$limit;

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
