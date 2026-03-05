<div class="page-header">
    <h1 class="page-title">My Reviews</h1>
    <p class="text-muted">Your submitted reviews for Pandits and Vendors</p>
</div>

<div class="content-grid">
    <?php if (empty($reviews)): ?>
    <div class="card">
        <div class="empty-state" style="text-align: center; padding: 60px 20px;">
            <i class="fas fa-star" style="font-size: 48px; color: #d1d5db; margin-bottom: 15px;"></i>
            <h3>No Reviews Yet</h3>
            <p class="text-muted">You haven't submitted any reviews yet. Complete a ritual or order to leave a review.</p>
            <div style="margin-top: 20px;">
                <a href="/user/bookings" class="btn btn-primary">View Bookings</a>
                <a href="/user/orders" class="btn btn-secondary" style="margin-left: 10px;">View Orders</a>
            </div>
        </div>
    </div>
    <?php else: ?>
    
    <div class="reviews-list">
        <?php foreach ($reviews as $review): ?>
        <div class="card review-card" style="margin-bottom: 20px;">
            <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <span class="badge badge-<?= $review['target_type'] === 'pandit' ? 'primary' : 'success' ?>">
                        <?= ucfirst($review['target_type']) ?>
                    </span>
                    <strong style="margin-left: 10px;"><?= htmlspecialchars($review['target_name'] ?? 'Unknown') ?></strong>
                </div>
                <div>
                    <?php
                    $statusColors = [
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger'
                    ];
                    $statusColor = $statusColors[$review['status']] ?? 'secondary';
                    ?>
                    <span class="badge badge-<?= $statusColor ?>">
                        <?= ucfirst($review['status']) ?>
                    </span>
                </div>
            </div>
            
            <div class="card-body">
                <!-- Rating Display -->
                <div class="rating-display" style="margin-bottom: 15px;">
                    <div class="stars" style="display: inline-flex; gap: 3px;">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                        <i class="<?= $i <= $review['rating_overall'] ? 'fas' : 'far' ?> fa-star" 
                           style="color: <?= $i <= $review['rating_overall'] ? '#fbbf24' : '#d1d5db' ?>; font-size: 18px;"></i>
                        <?php endfor; ?>
                    </div>
                    <span style="margin-left: 10px; font-weight: 600;"><?= $review['rating_overall'] ?>/5</span>
                </div>

                <!-- Detailed Ratings -->
                <?php if ($review['target_type'] === 'pandit'): ?>
                <div class="detailed-ratings" style="display: flex; flex-wrap: wrap; gap: 15px; margin-bottom: 15px; font-size: 0.9rem; color: #6b7280;">
                    <?php if ($review['punctuality']): ?>
                    <span><i class="fas fa-clock"></i> Punctuality: <?= $review['punctuality'] ?>/5</span>
                    <?php endif; ?>
                    <?php if ($review['knowledge']): ?>
                    <span><i class="fas fa-book"></i> Knowledge: <?= $review['knowledge'] ?>/5</span>
                    <?php endif; ?>
                    <?php if ($review['behavior']): ?>
                    <span><i class="fas fa-smile"></i> Behavior: <?= $review['behavior'] ?>/5</span>
                    <?php endif; ?>
                    <?php if ($review['clarity']): ?>
                    <span><i class="fas fa-comment"></i> Clarity: <?= $review['clarity'] ?>/5</span>
                    <?php endif; ?>
                </div>
                <?php else: ?>
                <div class="detailed-ratings" style="display: flex; flex-wrap: wrap; gap: 15px; margin-bottom: 15px; font-size: 0.9rem; color: #6b7280;">
                    <?php if ($review['item_quality']): ?>
                    <span><i class="fas fa-gem"></i> Quality: <?= $review['item_quality'] ?>/5</span>
                    <?php endif; ?>
                    <?php if ($review['delivery_time']): ?>
                    <span><i class="fas fa-truck"></i> Delivery: <?= $review['delivery_time'] ?>/5</span>
                    <?php endif; ?>
                    <?php if ($review['packaging']): ?>
                    <span><i class="fas fa-box"></i> Packaging: <?= $review['packaging'] ?>/5</span>
                    <?php endif; ?>
                    <?php if ($review['value_for_money']): ?>
                    <span><i class="fas fa-dollar-sign"></i> Value: <?= $review['value_for_money'] ?>/5</span>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <!-- Review Text -->
                <?php if (!empty($review['review_text'])): ?>
                <div class="review-text" style="background: #f9fafb; padding: 15px; border-radius: 8px; margin-bottom: 15px;">
                    <p style="margin: 0; line-height: 1.6;"><?= nl2br(htmlspecialchars($review['review_text'])) ?></p>
                </div>
                <?php endif; ?>

                <!-- Meta Info -->
                <div class="review-meta" style="font-size: 0.85rem; color: #9ca3af;">
                    <span><i class="fas fa-calendar"></i> Submitted on <?= date('d M Y, h:i A', strtotime($review['created_at'])) ?></span>
                    <?php if ($review['ai_flag']): ?>
                    <span style="margin-left: 15px; color: #f59e0b;">
                        <i class="fas fa-robot"></i> Under AI Review
                    </span>
                    <?php endif; ?>
                    <?php if ($review['status'] === 'rejected' && !empty($review['rejection_reason'])): ?>
                    <div style="margin-top: 10px; color: #dc2626;">
                        <i class="fas fa-exclamation-circle"></i> Rejection reason: <?= htmlspecialchars($review['rejection_reason']) ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<style>
.badge-primary { background: #3b82f6; color: white; }
.badge-success { background: #10b981; color: white; }
.badge-warning { background: #f59e0b; color: white; }
.badge-danger { background: #ef4444; color: white; }
.badge-secondary { background: #6b7280; color: white; }

.badge {
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
}

.review-card {
    transition: box-shadow 0.2s;
}

.review-card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
</style>
