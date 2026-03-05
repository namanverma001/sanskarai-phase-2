<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-inbox"></i> Booking Requests</h3>
    </div>
    
    <div style="margin-bottom: 20px;">
        <a href="/pandit/booking-requests" class="btn btn-sm <?= empty($_GET['status']) ? 'btn-primary' : '' ?>" style="<?= !empty($_GET['status']) ? 'background:#E5E7EB;color:#374151;' : '' ?>">All</a>
        <a href="/pandit/booking-requests?status=pending" class="btn btn-sm <?= ($_GET['status'] ?? '') === 'pending' ? 'btn-primary' : '' ?>" style="<?= ($_GET['status'] ?? '') !== 'pending' ? 'background:#E5E7EB;color:#374151;' : '' ?>">Pending</a>
        <a href="/pandit/booking-requests?status=confirmed" class="btn btn-sm <?= ($_GET['status'] ?? '') === 'confirmed' ? 'btn-primary' : '' ?>" style="<?= ($_GET['status'] ?? '') !== 'confirmed' ? 'background:#E5E7EB;color:#374151;' : '' ?>">Confirmed</a>
        <a href="/pandit/booking-requests?status=completed" class="btn btn-sm <?= ($_GET['status'] ?? '') === 'completed' ? 'btn-primary' : '' ?>" style="<?= ($_GET['status'] ?? '') !== 'completed' ? 'background:#E5E7EB;color:#374151;' : '' ?>">Completed</a>
        <a href="/pandit/booking-requests?status=rejected" class="btn btn-sm <?= ($_GET['status'] ?? '') === 'rejected' ? 'btn-primary' : '' ?>" style="<?= ($_GET['status'] ?? '') !== 'rejected' ? 'background:#E5E7EB;color:#374151;' : '' ?>">Rejected</a>
    </div>
    
    <?php if (empty($bookings)): ?>
        <div style="text-align: center; padding: 50px; color: #6B7280;">
            <i class="fas fa-inbox" style="font-size: 3rem; margin-bottom: 20px; color: #E5E7EB;"></i>
            <p>No booking requests found</p>
        </div>
    <?php else: ?>
        <div style="display: flex; flex-direction: column; gap: 15px;">
            <?php foreach ($bookings as $booking): ?>
            <div style="border: 1px solid #E5E7EB; border-radius: 12px; padding: 20px; transition: all 0.3s; <?= $booking['status'] === 'pending' ? 'border-left: 4px solid #F59E0B;' : ($booking['status'] === 'confirmed' ? 'border-left: 4px solid #10B981;' : ($booking['status'] === 'rejected' ? 'border-left: 4px solid #ef4444;' : '')) ?>">
                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 15px;">
                    <div>
                        <h4 style="margin-bottom: 5px;">
                            <?= htmlspecialchars($booking['user_name']) ?>
                        </h4>
                        <span style="font-size: 0.85rem; color: #6B7280;">
                            <i class="fas fa-phone"></i> <?= htmlspecialchars($booking['user_mobile'] ?? 'N/A') ?>
                        </span>
                    </div>
                    <span class="badge badge-<?= $booking['status'] === 'completed' ? 'success' : ($booking['status'] === 'confirmed' ? 'info' : ($booking['status'] === 'cancelled' || $booking['status'] === 'rejected' ? 'danger' : 'warning')) ?>">
                        <?= ucfirst($booking['status']) ?>
                    </span>
                </div>
                
                <?php if (!empty($booking['booking_purpose'])): ?>
                <div style="background: #F9FAFB; padding: 15px; border-radius: 8px; margin-bottom: 15px;">
                    <strong style="color: #374151; display: block; margin-bottom: 8px;">
                        <i class="fas fa-info-circle" style="color: var(--primary);"></i> Purpose:
                    </strong>
                    <p style="color: #4B5563; margin: 0; line-height: 1.6;">
                        <?= nl2br(htmlspecialchars($booking['booking_purpose'])) ?>
                    </p>
                </div>
                <?php endif; ?>
                
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px; font-size: 0.9rem; color: #6B7280;">
                    <div>
                        <i class="fas fa-calendar" style="color: var(--primary);"></i> 
                        <strong>Date:</strong> 
                        <?= $booking['scheduled_date'] ? date('M d, Y', strtotime($booking['scheduled_date'])) : 'To be decided' ?>
                    </div>
                    <div>
                        <i class="fas fa-clock" style="color: var(--primary);"></i> 
                        <strong>Time:</strong> 
                        <?= $booking['scheduled_time'] ? date('h:i A', strtotime($booking['scheduled_time'])) : 'To be decided' ?>
                    </div>
                    <div>
                        <i class="fas fa-map-marker-alt" style="color: var(--primary);"></i> 
                        <strong>Venue:</strong> 
                        <?= htmlspecialchars($booking['venue'] ?? 'To be decided') ?>
                    </div>
                </div>
                
                <?php if (!empty($booking['user_notes'])): ?>
                <div style="margin-top: 12px; padding-top: 12px; border-top: 1px solid #E5E7EB;">
                    <small style="color: #6B7280;">
                        <i class="fas fa-sticky-note"></i> <strong>Notes:</strong> 
                        <?= htmlspecialchars($booking['user_notes']) ?>
                    </small>
                </div>
                <?php endif; ?>
                
                <?php if ($booking['status'] === 'rejected' && !empty($booking['rejection_reason'])): ?>
                <div style="margin-top: 12px; padding: 12px; background: #fef2f2; border-radius: 8px; border-left: 3px solid #ef4444;">
                    <small style="color: #991b1b;">
                        <i class="fas fa-exclamation-circle"></i> <strong>Rejection Reason:</strong> 
                        <?= htmlspecialchars($booking['rejection_reason']) ?>
                    </small>
                </div>
                <?php endif; ?>
                
                <?php if ($booking['status'] === 'pending' || $booking['status'] === 'confirmed'): ?>
                <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #E5E7EB; display: flex; gap: 10px; flex-wrap: wrap;">
                    <?php if ($booking['status'] === 'pending'): ?>
                    <form method="POST" action="/pandit/assignments/<?= $booking['id'] ?>/confirm" style="display: inline;">
                        <?= \App\Core\Auth::csrfField() ?>
                        <button class="btn btn-sm btn-success">
                            <i class="fas fa-check"></i> Accept Booking
                        </button>
                    </form>
                    <button type="button" class="btn btn-sm btn-danger" onclick="showRejectModal(<?= $booking['id'] ?>)">
                        <i class="fas fa-times"></i> Reject
                    </button>
                    <?php elseif ($booking['status'] === 'confirmed'): ?>
                    <form method="POST" action="/pandit/assignments/<?= $booking['id'] ?>/complete" style="display: inline;">
                        <?= \App\Core\Auth::csrfField() ?>
                        <button class="btn btn-sm btn-primary">
                            <i class="fas fa-check-double"></i> Mark Complete
                        </button>
                    </form>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="pandit-modal" style="display: none;">
    <div class="pandit-modal-backdrop" onclick="hideRejectModal()"></div>
    <div class="pandit-modal-content">
        <div class="pandit-modal-header">
            <h3><i class="fas fa-times-circle" style="color: #ef4444;"></i> Reject Booking</h3>
            <button type="button" class="pandit-close-btn" onclick="hideRejectModal()">&times;</button>
        </div>
        <form id="rejectForm" method="POST" action="">
            <?= \App\Core\Auth::csrfField() ?>
            <div class="pandit-modal-body">
                <p style="color: #6b7280; margin-bottom: 15px;">Are you sure you want to reject this booking request?</p>
                <div class="form-group">
                    <label class="form-label">Reason for Rejection (Optional)</label>
                    <textarea name="rejection_reason" class="form-control" rows="3" placeholder="Let the user know why you cannot accept this booking..."></textarea>
                </div>
            </div>
            <div class="pandit-modal-footer">
                <button type="button" class="btn btn-secondary" onclick="hideRejectModal()">Cancel</button>
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-times"></i> Reject Booking
                </button>
            </div>
        </form>
    </div>
</div>

<style>
.pandit-modal {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 1050;
    display: flex;
    align-items: center;
    justify-content: center;
}

.pandit-modal-backdrop {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
}

.pandit-modal-content {
    position: relative;
    background: white;
    border-radius: 12px;
    width: 100%;
    max-width: 450px;
    margin: 20px;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    animation: modalSlideIn 0.3s ease;
}

@keyframes modalSlideIn {
    from { transform: translateY(-20px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}

.pandit-modal-header {
    padding: 20px;
    border-bottom: 1px solid #e5e7eb;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.pandit-modal-header h3 {
    margin: 0;
    font-size: 1.1rem;
    display: flex;
    align-items: center;
    gap: 10px;
}

.pandit-close-btn {
    background: none;
    border: none;
    font-size: 24px;
    cursor: pointer;
    color: #6b7280;
    line-height: 1;
}

.pandit-close-btn:hover {
    color: #374151;
}

.pandit-modal-body {
    padding: 20px;
}

.pandit-modal-footer {
    padding: 15px 20px;
    border-top: 1px solid #e5e7eb;
    display: flex;
    justify-content: flex-end;
    gap: 10px;
}
</style>

<script>
function showRejectModal(bookingId) {
    const modal = document.getElementById('rejectModal');
    const form = document.getElementById('rejectForm');
    form.action = '/pandit/assignments/' + bookingId + '/reject';
    modal.style.display = 'flex';
}

function hideRejectModal() {
    document.getElementById('rejectModal').style.display = 'none';
}

// Close modal on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        hideRejectModal();
    }
});
</script>
