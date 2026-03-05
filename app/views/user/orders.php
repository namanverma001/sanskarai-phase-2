<!-- Orders History Page -->
<style>
    .orders-header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 25px; border-radius: 12px; margin-bottom: 20px; color: white; }
    .orders-header h2 { margin: 0; font-size: 1.5rem; }
    .orders-header p { margin: 5px 0 0; opacity: 0.9; }
    
    .order-card { background: white; border-radius: 12px; padding: 20px; box-shadow: 0 2px 12px rgba(0,0,0,0.08); margin-bottom: 15px; transition: all 0.2s ease; }
    .order-card:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(0,0,0,0.12); }
    
    .order-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; padding-bottom: 12px; border-bottom: 1px solid #edf2f7; }
    .order-id { font-weight: 700; font-size: 1.1rem; color: #2d3748; }
    .order-date { font-size: 0.85rem; color: #718096; }
    
    .order-status { padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; }
    .order-status.pending { background: #fef3c7; color: #d97706; }
    .order-status.confirmed { background: #d1fae5; color: #059669; }
    .order-status.completed { background: #e0e7ff; color: #4f46e5; }
    .order-status.cancelled { background: #fee2e2; color: #dc2626; }
    
    .order-body { display: flex; justify-content: space-between; align-items: center; }
    .order-shop { display: flex; align-items: center; gap: 12px; }
    .order-shop .icon { width: 45px; height: 45px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: white; }
    .order-shop .details .name { font-weight: 600; color: #2d3748; }
    .order-shop .details .location { font-size: 0.85rem; color: #718096; }
    
    .order-info { text-align: right; }
    .order-info .items { font-size: 0.9rem; color: #4a5568; }
    .order-info .total { font-weight: 700; font-size: 1.2rem; color: #667eea; }
    
    .empty-state { text-align: center; padding: 60px 20px; background: white; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,0.08); }
    .empty-state i { font-size: 4rem; color: #cbd5e0; margin-bottom: 15px; }
    .empty-state h4 { color: #718096; margin-bottom: 15px; }
    .order-actions { margin-top: 15px; padding-top: 12px; border-top: 1px solid #edf2f7; display: flex; gap: 10px; }
</style>

<div class="orders-header d-flex justify-content-between align-items-center">
    <div>
        <h2><i class="fas fa-receipt mr-2"></i>My Orders</h2>
        <p>Your order history</p>
    </div>
    <a href="/user/shopping-list" class="btn btn-light">
        <i class="fas fa-shopping-bag"></i> Shopping List
    </a>
</div>

<?php if (empty($orders)): ?>
    <div class="empty-state">
        <i class="fas fa-box-open"></i>
        <h4>No orders yet</h4>
        <p style="color: #a0aec0;">Your orders will appear here after checkout</p>
        <a href="/user/shopping-list" class="btn btn-primary mt-3">
            <i class="fas fa-shopping-cart"></i> Go to Shopping List
        </a>
    </div>
<?php else: ?>
    <?php foreach ($orders as $order): ?>
    <div class="order-card">
        <a href="/user/orders/<?= $order['id'] ?>" style="text-decoration: none; color: inherit;">
            <div class="order-header">
                <div>
                    <span class="order-id">Order #<?= $order['id'] ?></span>
                    <span class="order-date ml-3"><i class="fas fa-clock"></i> <?= date('d M Y, h:i A', strtotime($order['created_at'])) ?></span>
                </div>
                <span class="order-status <?= $order['status'] ?>"><?= ucfirst($order['status']) ?></span>
            </div>
            <div class="order-body">
                <div class="order-shop">
                    <div class="icon"><i class="fas fa-store"></i></div>
                    <div class="details">
                        <div class="name"><?= htmlspecialchars($order['shop_name']) ?></div>
                        <div class="location"><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($order['shop_location'] ?? 'N/A') ?></div>
                    </div>
                </div>
                <div class="order-info">
                    <div class="items"><?= $order['total_items'] ?> item<?= $order['total_items'] > 1 ? 's' : '' ?></div>
                    <div class="total">₹<?= number_format($order['estimated_total']) ?></div>
                </div>
            </div>
        </a>
        <?php if ($order['status'] === 'completed'): ?>
        <div class="order-actions">
            <?php if (empty($order['has_review'])): ?>
            <a href="/user/reviews/vendor/<?= $order['id'] ?>" class="btn btn-sm btn-primary" onclick="event.stopPropagation();">
                <i class="fas fa-star"></i> Leave Review
            </a>
            <?php else: ?>
            <span class="badge badge-success" style="padding: 6px 12px;"><i class="fas fa-check"></i> Reviewed</span>
            <?php endif; ?>
            <a href="/user/orders/<?= $order['id'] ?>" class="btn btn-sm btn-secondary" onclick="event.stopPropagation();">
                <i class="fas fa-eye"></i> View Details
            </a>
        </div>
        <?php endif; ?>
    </div>
    <?php endforeach; ?>
<?php endif; ?>
