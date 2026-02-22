<?php
/**
 * Sanskar AI - Order Model
 * =========================
 * Order management for checkout flow
 */

namespace App\Models;

use App\Core\Model;

class Order extends Model
{
    protected string $table = 'SAI_orders';

    protected array $fillable = [
        'user_id',
        'shop_name',
        'shop_location',
        'shop_type',
        'user_latitude',
        'user_longitude',
        'user_address',
        'total_items',
        'estimated_total',
        'status',
        'notes',
    ];

    /**
     * Create order with items
     */
    public function createWithItems(int $userId, array $orderData, array $items): int
    {
        $orderData['user_id'] = $userId;
        $orderData['total_items'] = count($items);
        
        // Calculate total
        $total = 0;
        foreach ($items as $item) {
            $total += ($item['estimated_cost'] ?? 0) * ($item['quantity'] ?? 1);
        }
        $orderData['estimated_total'] = $total;
        
        // Create order
        $orderId = $this->create($orderData);
        
        // Create order items
        foreach ($items as $item) {
            $sql = "INSERT INTO SAI_order_items 
                    (order_id, shopping_list_id, item_name, item_name_local, quantity, unit, estimated_cost, created_at)
                    VALUES (:order_id, :shopping_list_id, :item_name, :item_name_local, :quantity, :unit, :estimated_cost, NOW())";
            
            $this->db->prepare($sql)->execute([
                'order_id' => $orderId,
                'shopping_list_id' => $item['id'] ?? null,
                'item_name' => $item['item_name'],
                'item_name_local' => $item['item_name_local'] ?? null,
                'quantity' => $item['quantity'] ?? 1,
                'unit' => $item['unit'] ?? 'piece',
                'estimated_cost' => $item['estimated_cost'] ?? 0,
            ]);
        }
        
        return $orderId;
    }

    /**
     * Get orders by user
     */
    public function getByUser(int $userId): array
    {
        $sql = "SELECT * FROM SAI_orders 
                WHERE user_id = :user_id 
                ORDER BY created_at DESC";
        return $this->raw($sql, ['user_id' => $userId]);
    }

    /**
     * Get order with items
     */
    public function getWithItems(int $orderId): ?array
    {
        $order = $this->find($orderId);
        if (!$order) return null;
        
        $sql = "SELECT * FROM SAI_order_items WHERE order_id = :order_id";
        $order['items'] = $this->raw($sql, ['order_id' => $orderId]);
        
        return $order;
    }

    /**
     * Update order status
     */
    public function updateStatus(int $orderId, string $status): bool
    {
        return $this->update($orderId, ['status' => $status]);
    }

    /**
     * Check if order belongs to user
     */
    public function belongsToUser(int $orderId, int $userId): bool
    {
        $order = $this->find($orderId);
        return $order && $order['user_id'] == $userId;
    }
}
