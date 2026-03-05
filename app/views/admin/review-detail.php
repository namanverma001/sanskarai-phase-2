<div class="page-header">
    <div class="page-header-content">
        <a href="/admin/reviews" class="btn btn-secondary btn-sm" style="margin-bottom: 10px;">
            <i class="fas fa-arrow-left"></i> Back to Reviews
        </a>
        <h1 class="page-title">Review Details</h1>
        <p class="text-muted">Review #<?= $review['id'] ?></p>
    </div>
</div>

<div class="review-detail-grid" style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px;">
    <!-- Main Content -->
    <div class="review-detail-main">
        <!-- Review Card -->
        <div class="card">
            <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                <h3 class="card-title"><i class="fas fa-star"></i> Review Content</h3>
                <?php
                $statusColors = [
                    'pending' => 'warning',
                    'approved' => 'success',
                    'rejected' => 'danger'
                ];
                $statusColor = $statusColors[$review['status']] ?? 'secondary';
                ?>
                <span class="badge badge-<?= $statusColor ?>" style="font-size: 0.85rem; padding: 6px 12px;">
                    <?= ucfirst($review['status']) ?>
                </span>
            </div>
            
            <div class="card-body">
                <!-- Overall Rating -->
                <div class="rating-section" style="margin-bottom: 25px;">
                    <h4 style="margin-bottom: 10px; color: #374151;">Overall Rating</h4>
                    <div style="display: flex; align-items: center; gap: 15px;">
                        <div class="stars" style="display: inline-flex; gap: 5px;">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                            <i class="<?= $i <= $review['rating_overall'] ? 'fas' : 'far' ?> fa-star" 
                               style="color: <?= $i <= $review['rating_overall'] ? '#fbbf24' : '#d1d5db' ?>; font-size: 28px;"></i>
                            <?php endfor; ?>
                        </div>
                        <span style="font-size: 1.5rem; font-weight: 700;"><?= $review['rating_overall'] ?>/5</span>
                    </div>
                </div>

                <!-- Detailed Ratings -->
                <div class="detailed-ratings" style="margin-bottom: 25px;">
                    <h4 style="margin-bottom: 15px; color: #374151;">Detailed Ratings</h4>
                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px;">
                        <?php if ($review['target_type'] === 'pandit'): ?>
                            <?php 
                            $categories = [
                                'punctuality' => ['label' => 'Punctuality', 'icon' => 'fas fa-clock'],
                                'knowledge' => ['label' => 'Knowledge', 'icon' => 'fas fa-book'],
                                'behavior' => ['label' => 'Behavior', 'icon' => 'fas fa-smile'],
                                'clarity' => ['label' => 'Clarity', 'icon' => 'fas fa-comment'],
                            ];
                            ?>
                        <?php else: ?>
                            <?php 
                            $categories = [
                                'item_quality' => ['label' => 'Item Quality', 'icon' => 'fas fa-gem'],
                                'delivery_time' => ['label' => 'Delivery Time', 'icon' => 'fas fa-truck'],
                                'packaging' => ['label' => 'Packaging', 'icon' => 'fas fa-box'],
                                'value_for_money' => ['label' => 'Value for Money', 'icon' => 'fas fa-dollar-sign'],
                            ];
                            ?>
                        <?php endif; ?>
                        
                        <?php foreach ($categories as $key => $cat): ?>
                        <div class="rating-item" style="background: #f9fafb; padding: 12px 15px; border-radius: 8px;">
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <span><i class="<?= $cat['icon'] ?>" style="margin-right: 8px; color: #6b7280;"></i> <?= $cat['label'] ?></span>
                                <span style="font-weight: 600;">
                                    <?php if ($review[$key]): ?>
                                        <?= $review[$key] ?>/5
                                        <span style="color: #fbbf24;">★</span>
                                    <?php else: ?>
                                        <span class="text-muted">N/A</span>
                                    <?php endif; ?>
                                </span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Review Text -->
                <div class="review-text-section" style="margin-bottom: 25px;">
                    <h4 style="margin-bottom: 15px; color: #374151;">Review Text</h4>
                    <?php if (!empty($review['review_text'])): ?>
                    <div style="background: #f9fafb; padding: 20px; border-radius: 8px; line-height: 1.7;">
                        <?= nl2br(htmlspecialchars($review['review_text'])) ?>
                    </div>
                    <?php else: ?>
                    <p class="text-muted">No text review provided.</p>
                    <?php endif; ?>
                </div>

                <!-- AI Moderation Info -->
                <?php if ($review['ai_flag'] || !empty($review['ai_moderation_reason'])): ?>
                <div class="ai-moderation-section" style="background: #fef3c7; padding: 15px 20px; border-radius: 8px; border-left: 4px solid #f59e0b; margin-bottom: 25px;">
                    <h4 style="margin-bottom: 10px; color: #92400e;">
                        <i class="fas fa-robot"></i> AI Moderation Flagged
                    </h4>
                    <p style="margin: 0; color: #92400e;">
                        <?= htmlspecialchars($review['ai_moderation_reason'] ?? 'This review was flagged by AI moderation for manual review.') ?>
                    </p>
                </div>
                <?php endif; ?>

                <!-- Rejection Reason -->
                <?php if ($review['status'] === 'rejected' && !empty($review['rejection_reason'])): ?>
                <div class="rejection-section" style="background: #fee2e2; padding: 15px 20px; border-radius: 8px; border-left: 4px solid #ef4444;">
                    <h4 style="margin-bottom: 10px; color: #991b1b;">
                        <i class="fas fa-exclamation-circle"></i> Rejection Reason
                    </h4>
                    <p style="margin: 0; color: #991b1b;">
                        <?= htmlspecialchars($review['rejection_reason']) ?>
                    </p>
                </div>
                <?php endif; ?>
            </div>

            <!-- Actions -->
            <?php if ($review['status'] === 'pending'): ?>
            <div class="card-footer" style="display: flex; gap: 10px; padding: 20px;">
                <form action="/admin/reviews/<?= $review['id'] ?>/approve" method="POST">
                    <?= \App\Core\Auth::csrfField() ?>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check"></i> Approve Review
                    </button>
                </form>
                <button type="button" class="btn btn-danger" onclick="showRejectModal()">
                    <i class="fas fa-times"></i> Reject Review
                </button>
                <form action="/admin/reviews/<?= $review['id'] ?>/delete" method="POST" onsubmit="return confirm('Delete this review permanently?');">
                    <?= \App\Core\Auth::csrfField() ?>
                    <button type="submit" class="btn btn-secondary">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </form>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Info Sidebar -->
    <div class="review-detail-sidebar">
        <!-- Reviewer Info -->
        <div class="card" style="margin-bottom: 20px;">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-user"></i> Reviewer</h3>
            </div>
            <div class="card-body">
                <?php if ($reviewer): ?>
                <div style="margin-bottom: 15px;">
                    <strong><?= htmlspecialchars($reviewer['name']) ?></strong>
                    <div class="text-muted" style="font-size: 0.9rem;"><?= htmlspecialchars($reviewer['email']) ?></div>
                    <?php if (!empty($reviewer['mobile'])): ?>
                    <div class="text-muted" style="font-size: 0.9rem;"><?= htmlspecialchars($reviewer['mobile']) ?></div>
                    <?php endif; ?>
                </div>
                <a href="/admin/users?search=<?= urlencode($reviewer['email']) ?>" class="btn btn-sm btn-secondary">
                    View User Profile
                </a>
                <?php else: ?>
                <p class="text-muted">Reviewer not found.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Target Info -->
        <div class="card" style="margin-bottom: 20px;">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-<?= $review['target_type'] === 'pandit' ? 'user-tie' : 'store' ?>"></i>
                    <?= ucfirst($review['target_type']) ?>
                </h3>
            </div>
            <div class="card-body">
                <?php if ($target): ?>
                <div style="margin-bottom: 15px;">
                    <strong><?= htmlspecialchars($target['name']) ?></strong>
                    <?php if ($target['type'] === 'pandit' && !empty($target['profile'])): ?>
                    <div class="text-muted" style="font-size: 0.9rem;">
                        <?= htmlspecialchars($target['profile']['specialization'] ?? '') ?>
                    </div>
                    <div style="margin-top: 10px;">
                        <span style="font-weight: 600;">
                            <?= number_format($target['profile']['average_rating'] ?? 0, 1) ?> 
                            <i class="fas fa-star" style="color: #fbbf24;"></i>
                        </span>
                        <span class="text-muted" style="margin-left: 5px;">
                            (<?= $target['profile']['total_rituals_performed'] ?? 0 ?> rituals)
                        </span>
                    </div>
                    <?php elseif ($target['type'] === 'vendor'): ?>
                    <div class="text-muted" style="font-size: 0.9rem;">
                        <?= htmlspecialchars($target['city'] ?? '') ?>
                    </div>
                    <div style="margin-top: 10px;">
                        <span style="font-weight: 600;">
                            <?= number_format($target['average_rating'] ?? 0, 1) ?> 
                            <i class="fas fa-star" style="color: #fbbf24;"></i>
                        </span>
                        <span class="text-muted" style="margin-left: 5px;">
                            (<?= $target['total_reviews'] ?? 0 ?> reviews)
                        </span>
                    </div>
                    <?php endif; ?>
                </div>
                <?php else: ?>
                <p class="text-muted"><?= ucfirst($review['target_type']) ?> not found.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Metadata -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-info-circle"></i> Metadata</h3>
            </div>
            <div class="card-body" style="font-size: 0.9rem;">
                <div style="margin-bottom: 10px;">
                    <span class="text-muted">Submitted:</span><br>
                    <?= date('d M Y, h:i A', strtotime($review['created_at'])) ?>
                </div>
                <?php if ($review['moderated_at']): ?>
                <div style="margin-bottom: 10px;">
                    <span class="text-muted">Moderated:</span><br>
                    <?= date('d M Y, h:i A', strtotime($review['moderated_at'])) ?>
                </div>
                <?php endif; ?>
                <?php if ($review['assignment_id']): ?>
                <div style="margin-bottom: 10px;">
                    <span class="text-muted">Assignment ID:</span><br>
                    #<?= $review['assignment_id'] ?>
                </div>
                <?php endif; ?>
                <?php if ($review['order_id']): ?>
                <div style="margin-bottom: 10px;">
                    <span class="text-muted">Order ID:</span><br>
                    #<?= $review['order_id'] ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="modal" style="display: none;">
    <div class="modal-backdrop" onclick="hideRejectModal()"></div>
    <div class="modal-content">
        <div class="modal-header">
            <h3>Reject Review</h3>
            <button type="button" class="close-btn" onclick="hideRejectModal()">&times;</button>
        </div>
        <form action="/admin/reviews/<?= $review['id'] ?>/reject" method="POST">
            <?= \App\Core\Auth::csrfField() ?>
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Rejection Reason (Optional)</label>
                    <textarea name="reason" class="form-control" rows="3" placeholder="Explain why this review is being rejected..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="hideRejectModal()">Cancel</button>
                <button type="submit" class="btn btn-danger">Reject Review</button>
            </div>
        </form>
    </div>
</div>

<style>
.badge-primary { background: #3b82f6; color: white; }
.badge-success { background: #10b981; color: white; }
.badge-warning { background: #f59e0b; color: white; }
.badge-danger { background: #ef4444; color: white; }

.badge {
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
}

.modal {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 1000;
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal-backdrop {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.5);
}

.modal-content {
    position: relative;
    background: white;
    border-radius: 12px;
    width: 100%;
    max-width: 500px;
    box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1);
}

.modal-header {
    padding: 20px;
    border-bottom: 1px solid #e5e7eb;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-header h3 { margin: 0; }

.close-btn {
    background: none;
    border: none;
    font-size: 24px;
    cursor: pointer;
    color: #6b7280;
}

.modal-body { padding: 20px; }

.modal-footer {
    padding: 15px 20px;
    border-top: 1px solid #e5e7eb;
    display: flex;
    justify-content: flex-end;
    gap: 10px;
}

@media (max-width: 768px) {
    .review-detail-grid {
        grid-template-columns: 1fr !important;
    }
}

.review-detail-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 20px;
}

.review-detail-main {
    min-width: 0;
}

.review-detail-sidebar {
    min-width: 0;
}
</style>

<script>
function showRejectModal() {
    document.getElementById('rejectModal').style.display = 'flex';
}

function hideRejectModal() {
    document.getElementById('rejectModal').style.display = 'none';
}
</script>
