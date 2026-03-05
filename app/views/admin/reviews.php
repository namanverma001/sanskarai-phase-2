<div class="page-header">
    <div class="page-header-content">
        <h1 class="page-title">Review Management</h1>
        <p class="text-muted">Moderate and manage all user reviews</p>
    </div>
</div>

<!-- Stats Cards -->
<div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px; margin-bottom: 25px;">
    <div class="stat-card" style="background: white; padding: 20px; border-radius: 10px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <div class="stat-value" style="font-size: 2rem; font-weight: 700; color: #3b82f6;"><?= number_format($stats['total'] ?? 0) ?></div>
        <div class="stat-label" style="color: #6b7280; font-size: 0.9rem;">Total Reviews</div>
    </div>
    <div class="stat-card" style="background: white; padding: 20px; border-radius: 10px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <div class="stat-value" style="font-size: 2rem; font-weight: 700; color: #f59e0b;"><?= number_format($stats['pending'] ?? 0) ?></div>
        <div class="stat-label" style="color: #6b7280; font-size: 0.9rem;">Pending</div>
    </div>
    <div class="stat-card" style="background: white; padding: 20px; border-radius: 10px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <div class="stat-value" style="font-size: 2rem; font-weight: 700; color: #10b981;"><?= number_format($stats['approved'] ?? 0) ?></div>
        <div class="stat-label" style="color: #6b7280; font-size: 0.9rem;">Approved</div>
    </div>
    <div class="stat-card" style="background: white; padding: 20px; border-radius: 10px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <div class="stat-value" style="font-size: 2rem; font-weight: 700; color: #ef4444;"><?= number_format($stats['ai_flagged'] ?? 0) ?></div>
        <div class="stat-label" style="color: #6b7280; font-size: 0.9rem;">AI Flagged</div>
    </div>
    <div class="stat-card" style="background: white; padding: 20px; border-radius: 10px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <div class="stat-value" style="font-size: 2rem; font-weight: 700; color: #6366f1;">
            <?= number_format($stats['avg_rating'] ?? 0, 1) ?> <i class="fas fa-star" style="font-size: 1.2rem; color: #fbbf24;"></i>
        </div>
        <div class="stat-label" style="color: #6b7280; font-size: 0.9rem;">Avg Rating</div>
    </div>
</div>

<!-- Filters -->
<div class="card" style="margin-bottom: 20px;">
    <div class="card-body">
        <form method="GET" action="/admin/reviews" class="filters-form" style="display: flex; flex-wrap: wrap; gap: 15px; align-items: end;">
            <div class="form-group" style="margin: 0; min-width: 150px;">
                <label class="form-label" style="font-size: 0.85rem; color: #6b7280;">Status</label>
                <select name="status" class="form-control">
                    <option value="">All Statuses</option>
                    <option value="pending" <?= ($filters['status'] ?? '') === 'pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="approved" <?= ($filters['status'] ?? '') === 'approved' ? 'selected' : '' ?>>Approved</option>
                    <option value="rejected" <?= ($filters['status'] ?? '') === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                </select>
            </div>
            
            <div class="form-group" style="margin: 0; min-width: 150px;">
                <label class="form-label" style="font-size: 0.85rem; color: #6b7280;">Type</label>
                <select name="target_type" class="form-control">
                    <option value="">All Types</option>
                    <option value="pandit" <?= ($filters['target_type'] ?? '') === 'pandit' ? 'selected' : '' ?>>Pandit</option>
                    <option value="vendor" <?= ($filters['target_type'] ?? '') === 'vendor' ? 'selected' : '' ?>>Vendor</option>
                </select>
            </div>
            
            <div class="form-group" style="margin: 0;">
                <label class="form-label" style="font-size: 0.85rem; color: #6b7280;">&nbsp;</label>
                <label class="checkbox-label" style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                    <input type="checkbox" name="ai_flagged" value="1" <?= !empty($filters['ai_flagged']) ? 'checked' : '' ?>>
                    <span>AI Flagged Only</span>
                </label>
            </div>
            
            <div class="form-group" style="margin: 0;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-filter"></i> Filter
                </button>
                <a href="/admin/reviews" class="btn btn-secondary" style="margin-left: 5px;">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Reviews Table -->
<div class="card">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
        <h3 class="card-title"><i class="fas fa-star"></i> Reviews</h3>
        <button type="button" class="btn btn-success btn-sm" id="bulkApproveBtn" style="display: none;">
            <i class="fas fa-check-double"></i> Approve Selected
        </button>
    </div>
    
    <?php if (empty($reviews)): ?>
    <div class="empty-state" style="text-align: center; padding: 60px 20px;">
        <i class="fas fa-inbox" style="font-size: 48px; color: #d1d5db; margin-bottom: 15px;"></i>
        <h3>No Reviews Found</h3>
        <p class="text-muted">No reviews match your current filters.</p>
    </div>
    <?php else: ?>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th style="width: 40px;">
                        <input type="checkbox" id="selectAll" title="Select All">
                    </th>
                    <th>Reviewer</th>
                    <th>Target</th>
                    <th>Rating</th>
                    <th>Review</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th style="width: 120px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reviews as $review): ?>
                <tr data-review-id="<?= $review['id'] ?>">
                    <td>
                        <?php if ($review['status'] === 'pending'): ?>
                        <input type="checkbox" class="review-checkbox" value="<?= $review['id'] ?>">
                        <?php endif; ?>
                    </td>
                    <td>
                        <div style="font-weight: 500;"><?= htmlspecialchars($review['reviewer_name']) ?></div>
                        <small class="text-muted"><?= htmlspecialchars($review['reviewer_email']) ?></small>
                    </td>
                    <td>
                        <span class="badge badge-<?= $review['target_type'] === 'pandit' ? 'primary' : 'success' ?>" style="font-size: 0.7rem;">
                            <?= ucfirst($review['target_type']) ?>
                        </span>
                        <div style="margin-top: 3px;"><?= htmlspecialchars($review['target_name'] ?? 'Unknown') ?></div>
                    </td>
                    <td>
                        <div class="rating-stars">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                            <i class="<?= $i <= $review['rating_overall'] ? 'fas' : 'far' ?> fa-star" 
                               style="color: <?= $i <= $review['rating_overall'] ? '#fbbf24' : '#d1d5db' ?>; font-size: 12px;"></i>
                            <?php endfor; ?>
                        </div>
                        <small><?= $review['rating_overall'] ?>/5</small>
                    </td>
                    <td style="max-width: 250px;">
                        <?php if (!empty($review['review_text'])): ?>
                        <div class="review-text-preview" style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="<?= htmlspecialchars($review['review_text']) ?>">
                            <?= htmlspecialchars(substr($review['review_text'], 0, 100)) ?><?= strlen($review['review_text']) > 100 ? '...' : '' ?>
                        </div>
                        <?php else: ?>
                        <span class="text-muted">No text</span>
                        <?php endif; ?>
                        <?php if ($review['ai_flag']): ?>
                        <span class="badge badge-warning" style="font-size: 0.65rem; margin-top: 3px;">
                            <i class="fas fa-robot"></i> AI Flagged
                        </span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php
                        $statusColors = [
                            'pending' => 'warning',
                            'approved' => 'success',
                            'rejected' => 'danger'
                        ];
                        $statusColor = $statusColors[$review['status']] ?? 'secondary';
                        ?>
                        <span class="badge badge-<?= $statusColor ?>"><?= ucfirst($review['status']) ?></span>
                    </td>
                    <td>
                        <small><?= date('d M Y', strtotime($review['created_at'])) ?></small>
                    </td>
                    <td>
                        <div class="btn-group" style="display: flex; gap: 5px;">
                            <a href="/admin/reviews/<?= $review['id'] ?>" class="btn btn-sm btn-secondary" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                            <?php if ($review['status'] === 'pending'): ?>
                            <form action="/admin/reviews/<?= $review['id'] ?>/approve" method="POST" style="display: inline;">
                                <?= \App\Core\Auth::csrfField() ?>
                                <button type="submit" class="btn btn-sm btn-success" title="Approve">
                                    <i class="fas fa-check"></i>
                                </button>
                            </form>
                            <button type="button" class="btn btn-sm btn-danger reject-btn" data-id="<?= $review['id'] ?>" title="Reject">
                                <i class="fas fa-times"></i>
                            </button>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="modal" style="display: none;">
    <div class="modal-backdrop" onclick="closeRejectModal()"></div>
    <div class="modal-content">
        <div class="modal-header">
            <h3>Reject Review</h3>
            <button type="button" class="close-btn" onclick="closeRejectModal()">&times;</button>
        </div>
        <form id="rejectForm" method="POST">
            <?= \App\Core\Auth::csrfField() ?>
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Rejection Reason (Optional)</label>
                    <textarea name="reason" class="form-control" rows="3" placeholder="Explain why this review is being rejected..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeRejectModal()">Cancel</button>
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
.badge-secondary { background: #6b7280; color: white; }

.badge {
    padding: 3px 8px;
    border-radius: 20px;
    font-size: 0.7rem;
    font-weight: 600;
    text-transform: uppercase;
}

.table th {
    font-weight: 600;
    font-size: 0.85rem;
    color: #6b7280;
    background: #f9fafb;
}

.table td {
    vertical-align: middle;
}

.btn-group .btn {
    padding: 5px 10px;
}

/* Modal Styles */
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

.modal-header h3 {
    margin: 0;
}

.close-btn {
    background: none;
    border: none;
    font-size: 24px;
    cursor: pointer;
    color: #6b7280;
}

.modal-body {
    padding: 20px;
}

.modal-footer {
    padding: 15px 20px;
    border-top: 1px solid #e5e7eb;
    display: flex;
    justify-content: flex-end;
    gap: 10px;
}

.checkbox-label {
    font-weight: normal;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Select all functionality
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.review-checkbox');
    const bulkApproveBtn = document.getElementById('bulkApproveBtn');

    function updateBulkButton() {
        const checked = document.querySelectorAll('.review-checkbox:checked');
        bulkApproveBtn.style.display = checked.length > 0 ? 'inline-flex' : 'none';
    }

    if (selectAll) {
        selectAll.addEventListener('change', function() {
            checkboxes.forEach(cb => cb.checked = this.checked);
            updateBulkButton();
        });
    }

    checkboxes.forEach(cb => {
        cb.addEventListener('change', updateBulkButton);
    });

    // Bulk approve
    bulkApproveBtn.addEventListener('click', async function() {
        const selected = Array.from(document.querySelectorAll('.review-checkbox:checked')).map(cb => cb.value);
        
        if (selected.length === 0) return;
        
        if (!confirm(`Approve ${selected.length} review(s)?`)) return;
        
        this.disabled = true;
        this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';

        try {
            const formData = new FormData();
            formData.append('csrf_token', '<?= \App\Core\Auth::csrfToken() ?>');
            selected.forEach(id => formData.append('review_ids[]', id));

            const response = await fetch('/admin/reviews/bulk-approve', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                alert(result.message);
                location.reload();
            } else {
                alert(result.error || 'Failed to approve reviews.');
            }
        } catch (error) {
            alert('An error occurred.');
        }

        this.disabled = false;
        this.innerHTML = '<i class="fas fa-check-double"></i> Approve Selected';
    });

    // Reject button handlers
    document.querySelectorAll('.reject-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const reviewId = this.dataset.id;
            document.getElementById('rejectForm').action = `/admin/reviews/${reviewId}/reject`;
            document.getElementById('rejectModal').style.display = 'flex';
        });
    });
});

function closeRejectModal() {
    document.getElementById('rejectModal').style.display = 'none';
}
</script>
