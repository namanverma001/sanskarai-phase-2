<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-calendar"></i> My Bookings</h3>
    </div>
    
    <?php if (empty($bookings)): ?>
        <div style="text-align: center; padding: 50px; color: #6B7280;">
            <i class="fas fa-calendar-times" style="font-size: 3rem; margin-bottom: 20px; color: #E5E7EB;"></i>
            <p>No bookings yet. Explore rituals and book a pandit!</p>
            <a href="/user/rituals" class="btn btn-primary" style="margin-top: 20px;">
                <i class="fas fa-book"></i> Explore Rituals
            </a>
        </div>
    <?php else: ?>
        <table class="table">
            <thead>
                <tr><th>Ritual</th><th>Pandit</th><th>Date & Time</th><th>Venue</th><th>Status</th><th>Actions</th></tr>
            </thead>
            <tbody>
                <?php foreach ($bookings as $booking): ?>
                <tr>
                    <td>
                        <strong><?= htmlspecialchars($booking['ritual_name'] ?? $booking['custom_ritual_name'] ?? 'Custom') ?></strong>
                    </td>
                    <td>
                        <?= htmlspecialchars($booking['pandit_name']) ?><br>
                        <small style="color: #6B7280;"><i class="fas fa-phone"></i> <?= htmlspecialchars($booking['pandit_mobile'] ?? '') ?></small>
                    </td>
                    <td>
                        <?php if ($booking['scheduled_date']): ?>
                            <?= date('M d, Y', strtotime($booking['scheduled_date'])) ?>
                            <?php if ($booking['scheduled_time']): ?>
                            <br><small><?= date('h:i A', strtotime($booking['scheduled_time'])) ?></small>
                            <?php endif; ?>
                        <?php else: ?>
                            <span style="color: #6B7280;">To be decided</span>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($booking['venue'] ?? 'TBD') ?></td>
                    <td>
                        <span class="badge badge-<?= $booking['status'] === 'completed' ? 'success' : ($booking['status'] === 'confirmed' ? 'info' : ($booking['status'] === 'cancelled' ? 'danger' : 'warning')) ?>">
                            <?= ucfirst($booking['status']) ?>
                        </span>
                    </td>
                    <td>
                        <?php if ($booking['status'] === 'pending'): ?>
                        <form method="POST" action="/user/bookings/<?= $booking['id'] ?>/cancel" style="display: inline;" onsubmit="return confirm('Cancel this booking?')">
                            <?= \App\Core\Auth::csrfField() ?>
                            <button type="submit" class="btn btn-sm btn-danger">
                                <i class="fas fa-times"></i> Cancel
                            </button>
                        </form>
                        <?php elseif ($booking['status'] === 'completed'): ?>
                            <?php if (empty($booking['has_review'])): ?>
                            <a href="/user/reviews/pandit/<?= $booking['id'] ?>" class="btn btn-sm btn-primary">
                                <i class="fas fa-star"></i> Leave Review
                            </a>
                            <?php else: ?>
                            <span class="badge badge-success"><i class="fas fa-check"></i> Reviewed</span>
                            <?php endif; ?>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
