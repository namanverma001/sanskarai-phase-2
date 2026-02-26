<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon orange"><i class="fas fa-clock"></i></div>
        <div class="stat-value"><?= $stats['pending'] ?? 0 ?></div>
        <div class="stat-label">Pending</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon purple"><i class="fas fa-check"></i></div>
        <div class="stat-value"><?= $stats['confirmed'] ?? 0 ?></div>
        <div class="stat-label">Confirmed</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green"><i class="fas fa-check-double"></i></div>
        <div class="stat-value"><?= $stats['completed'] ?? 0 ?></div>
        <div class="stat-label">Completed</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon pink"><i class="fas fa-star"></i></div>
        <div class="stat-value"><?= number_format($profile['average_rating'] ?? 0, 1) ?></div>
        <div class="stat-label">Rating</div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-calendar-check"></i> Upcoming Assignments</h3>
        <a href="/pandit/assignments" class="btn btn-sm btn-primary">View All</a>
    </div>
    <?php if (empty($upcomingAssignments)): ?>
        <p style="text-align: center; color: #6B7280; padding: 30px;">No upcoming assignments</p>
    <?php else: ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Ritual</th>
                    <th>User</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($upcomingAssignments as $a): ?>
                    <tr>
                        <td><?= htmlspecialchars($a['ritual_name'] ?? $a['custom_ritual_name'] ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars($a['user_name']) ?></td>
                        <td><?= $a['scheduled_date'] ? date('M d, Y', strtotime($a['scheduled_date'])) : 'TBD' ?></td>
                        <td><span
                                class="badge badge-<?= $a['status'] === 'confirmed' ? 'success' : 'warning' ?>"><?= ucfirst($a['status']) ?></span>
                        </td>
                        <td>
                            <?php if ($a['status'] === 'pending'): ?>
                                <form method="POST" action="/pandit/assignments/<?= $a['id'] ?>/confirm" style="display: inline;">
                                    <?= \App\Core\Auth::csrfField() ?>
                                    <button class="btn btn-sm btn-success">Confirm</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>