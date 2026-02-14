<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-inbox"></i> Booking Requests</h3>
    </div>
    
    <div style="margin-bottom: 20px;">
        <a href="/pandit/booking-requests" class="btn btn-sm <?= empty($_GET['status']) ? 'btn-primary' : '' ?>" style="<?= !empty($_GET['status']) ? 'background:#E5E7EB;color:#374151;' : '' ?>">All</a>
        <a href="/pandit/booking-requests?status=pending" class="btn btn-sm <?= ($_GET['status'] ?? '') === 'pending' ? 'btn-primary' : '' ?>" style="<?= ($_GET['status'] ?? '') !== 'pending' ? 'background:#E5E7EB;color:#374151;' : '' ?>">Pending</a>
        <a href="/pandit/booking-requests?status=confirmed" class="btn btn-sm <?= ($_GET['status'] ?? '') === 'confirmed' ? 'btn-primary' : '' ?>" style="<?= ($_GET['status'] ?? '') !== 'confirmed' ? 'background:#E5E7EB;color:#374151;' : '' ?>">Confirmed</a>
        <a href="/pandit/booking-requests?status=completed" class="btn btn-sm <?= ($_GET['status'] ?? '') === 'completed' ? 'btn-primary' : '' ?>" style="<?= ($_GET['status'] ?? '') !== 'completed' ? 'background:#E5E7EB;color:#374151;' : '' ?>">Completed</a>
    </div>
    
    <?php if (empty($bookings)): ?>
        <div style="text-align: center; padding: 50px; color: #6B7280;">
            <i class="fas fa-inbox" style="font-size: 3rem; margin-bottom: 20px; color: #E5E7EB;"></i>
            <p>No booking requests found</p>
        </div>
    <?php else: ?>
        <div style="display: flex; flex-direction: column; gap: 15px;">
            <?php foreach ($bookings as $booking): ?>
            <div style="border: 1px solid #E5E7EB; border-radius: 12px; padding: 20px; transition: all 0.3s; <?= $booking['status'] === 'pending' ? 'border-left: 4px solid #F59E0B;' : ($booking['status'] === 'confirmed' ? 'border-left: 4px solid #10B981;' : '') ?>">
                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 15px;">
                    <div>
                        <h4 style="margin-bottom: 5px;">
                            <?= htmlspecialchars($booking['user_name']) ?>
                        </h4>
                        <span style="font-size: 0.85rem; color: #6B7280;">
                            <i class="fas fa-phone"></i> <?= htmlspecialchars($booking['user_mobile'] ?? 'N/A') ?>
                        </span>
                    </div>
                    <span class="badge badge-<?= $booking['status'] === 'completed' ? 'success' : ($booking['status'] === 'confirmed' ? 'info' : ($booking['status'] === 'cancelled' ? 'danger' : 'warning')) ?>">
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
                
                <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #E5E7EB; display: flex; gap: 10px;">
                    <?php if ($booking['status'] === 'pending'): ?>
                    <form method="POST" action="/pandit/assignments/<?= $booking['id'] ?>/confirm" style="display: inline;">
                        <?= \App\Core\Auth::csrfField() ?>
                        <button class="btn btn-sm btn-success">
                            <i class="fas fa-check"></i> Accept Booking
                        </button>
                    </form>
                    <?php elseif ($booking['status'] === 'confirmed'): ?>
                    <form method="POST" action="/pandit/assignments/<?= $booking['id'] ?>/complete" style="display: inline;">
                        <?= \App\Core\Auth::csrfField() ?>
                        <button class="btn btn-sm btn-primary">
                            <i class="fas fa-check-double"></i> Mark Complete
                        </button>
                    </form>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
