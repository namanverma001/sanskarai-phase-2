<!-- Order Detail Page -->
<style>
    .order-detail-header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 25px; border-radius: 12px; margin-bottom: 20px; color: white; }
    .order-detail-header h2 { margin: 0; font-size: 1.5rem; }
    .order-detail-header .order-status { display: inline-block; background: rgba(255,255,255,0.2); padding: 5px 15px; border-radius: 20px; font-size: 0.85rem; margin-top: 8px; }
    
    .detail-grid { display: grid; grid-template-columns: 1fr 350px; gap: 20px; }
    @media (max-width: 900px) { .detail-grid { grid-template-columns: 1fr; } }
    
    .detail-card { background: white; border-radius: 12px; padding: 20px; box-shadow: 0 2px 12px rgba(0,0,0,0.08); margin-bottom: 20px; }
    .detail-card h4 { margin: 0 0 15px; color: #2d3748; display: flex; align-items: center; gap: 10px; }
    .detail-card h4 i { color: #667eea; }
    
    .shop-info { display: flex; align-items: center; gap: 15px; padding: 15px; background: linear-gradient(135deg, #f7fafc 0%, #edf2f7 100%); border-radius: 10px; }
    .shop-info .icon { width: 50px; height: 50px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.3rem; }
    .shop-info .details .name { font-weight: 700; font-size: 1.1rem; color: #2d3748; }
    .shop-info .details .type { font-size: 0.85rem; color: #667eea; background: #ebf4ff; padding: 2px 8px; border-radius: 12px; display: inline-block; margin-top: 3px; }
    .shop-info .details .location { font-size: 0.9rem; color: #718096; margin-top: 5px; }
    
    .item-list .item { display: flex; align-items: center; padding: 12px 0; border-bottom: 1px solid #edf2f7; }
    .item-list .item:last-child { border-bottom: none; }
    .item-list .item .letter { width: 40px; height: 40px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; margin-right: 12px; }
    .item-list .item .details { flex: 1; }
    .item-list .item .name { font-weight: 600; color: #2d3748; }
    .item-list .item .qty { font-size: 0.85rem; color: #718096; }
    .item-list .item .price { font-weight: 600; color: #667eea; }
    
    .summary-row { display: flex; justify-content: space-between; padding: 10px 0; }
    .summary-row.total { font-weight: 700; font-size: 1.3rem; color: #2d3748; border-top: 2px solid #667eea; padding-top: 15px; margin-top: 10px; }
    
    .map-container { height: 200px; border-radius: 10px; overflow: hidden; background: #edf2f7; }
    .map-container iframe { width: 100%; height: 100%; border: none; }
    
    .action-buttons { display: flex; gap: 10px; margin-top: 15px; }
    .action-buttons .btn { flex: 1; }
</style>

<div class="order-detail-header">
    <h2><i class="fas fa-receipt mr-2"></i>Order #<?= $order['id'] ?></h2>
    <div class="order-status"><i class="fas fa-check-circle"></i> <?= ucfirst($order['status']) ?></div>
    <p style="margin: 10px 0 0; opacity: 0.9;"><i class="fas fa-clock"></i> <?= date('d M Y, h:i A', strtotime($order['created_at'])) ?></p>
</div>

<div class="detail-grid">
    <!-- Main Column -->
    <div>
        <!-- Shop Info -->
        <div class="detail-card">
            <h4><i class="fas fa-store"></i> Shop Details</h4>
            <div class="shop-info">
                <div class="icon"><i class="fas fa-store"></i></div>
                <div class="details">
                    <div class="name"><?= htmlspecialchars($order['shop_name']) ?></div>
                    <span class="type"><?= htmlspecialchars($order['shop_type'] ?? 'Shop') ?></span>
                    <div class="location"><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($order['shop_location'] ?? 'N/A') ?></div>
                </div>
            </div>
            
            <div class="action-buttons">
                <a href="https://www.google.com/maps/search/?api=1&query=<?= urlencode($order['shop_name'] . ' ' . ($order['shop_location'] ?? '')) ?>" target="_blank" class="btn btn-primary">
                    <i class="fas fa-directions"></i> Get Directions
                </a>
            </div>
        </div>
        
        <!-- Items -->
        <div class="detail-card">
            <h4><i class="fas fa-shopping-basket"></i> Items (<?= count($order['items']) ?>)</h4>
            <div class="item-list">
                <?php foreach ($order['items'] as $item): ?>
                <div class="item">
                    <div class="letter"><?= strtoupper(substr($item['item_name'], 0, 1)) ?></div>
                    <div class="details">
                        <div class="name"><?= htmlspecialchars($item['item_name']) ?></div>
                        <div class="qty"><?= $item['quantity'] ?> <?= $item['unit'] ?></div>
                    </div>
                    <div class="price">₹<?= number_format(($item['estimated_cost'] ?? 0) * $item['quantity']) ?></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <?php if (!empty($order['notes'])): ?>
        <div class="detail-card">
            <h4><i class="fas fa-sticky-note"></i> Notes</h4>
            <p style="color: #4a5568; margin: 0;"><?= nl2br(htmlspecialchars($order['notes'])) ?></p>
        </div>
        <?php endif; ?>
    </div>
    
    <!-- Sidebar -->
    <div>
        <!-- Order Summary -->
        <div class="detail-card">
            <h4><i class="fas fa-calculator"></i> Order Summary</h4>
            <div class="summary-row">
                <span>Items</span>
                <span><?= $order['total_items'] ?></span>
            </div>
            <div class="summary-row">
                <span>Status</span>
                <span style="color: #48bb78; font-weight: 600;"><?= ucfirst($order['status']) ?></span>
            </div>
            <div class="summary-row total">
                <span>Total</span>
                <span style="color: #667eea;">₹<?= number_format($order['estimated_total']) ?></span>
            </div>
        </div>
        
        <?php if ($order['user_address']): ?>
        <div class="detail-card">
            <h4><i class="fas fa-map-marker-alt"></i> Your Location</h4>
            <p style="color: #4a5568; margin: 0; font-size: 0.9rem;"><?= htmlspecialchars($order['user_address']) ?></p>
        </div>
        <?php endif; ?>
        
        <a href="/user/orders" class="btn btn-outline-secondary btn-block">
            <i class="fas fa-arrow-left"></i> Back to Orders
        </a>
        
        <a href="/user/shopping-list" class="btn btn-primary btn-block mt-2">
            <i class="fas fa-shopping-cart"></i> New Shopping List
        </a>
    </div>
</div>
