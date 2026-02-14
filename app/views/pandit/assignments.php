<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-calendar-check"></i> My Assignments</h3>
    </div>
    
    <div style="margin-bottom: 20px;">
        <a href="/pandit/assignments" class="btn btn-sm <?= empty($_GET['status']) ? 'btn-primary' : '' ?>" style="<?= !empty($_GET['status']) ? 'background:#E5E7EB;color:#374151;' : '' ?>">All</a>
        <a href="/pandit/assignments?status=pending" class="btn btn-sm <?= ($_GET['status'] ?? '') === 'pending' ? 'btn-primary' : '' ?>" style="<?= ($_GET['status'] ?? '') !== 'pending' ? 'background:#E5E7EB;color:#374151;' : '' ?>">Pending</a>
        <a href="/pandit/assignments?status=confirmed" class="btn btn-sm <?= ($_GET['status'] ?? '') === 'confirmed' ? 'btn-primary' : '' ?>" style="<?= ($_GET['status'] ?? '') !== 'confirmed' ? 'background:#E5E7EB;color:#374151;' : '' ?>">Confirmed</a>
        <a href="/pandit/assignments?status=completed" class="btn btn-sm <?= ($_GET['status'] ?? '') === 'completed' ? 'btn-primary' : '' ?>" style="<?= ($_GET['status'] ?? '') !== 'completed' ? 'background:#E5E7EB;color:#374151;' : '' ?>">Completed</a>
    </div>
    
    <?php if (empty($assignments)): ?>
        <p style="text-align: center; color: #6B7280; padding: 30px;">No assignments found</p>
    <?php else: ?>
        <table class="table">
            <thead>
                <tr><th>Ritual</th><th>User</th><th>Date</th><th>Venue</th><th>Status</th><th>Actions</th></tr>
            </thead>
            <tbody>
                <?php foreach ($assignments as $a): ?>
                <tr>
                    <td><?= htmlspecialchars($a['ritual_name'] ?? $a['custom_ritual_name'] ?? 'N/A') ?></td>
                    <td>
                        <strong><?= htmlspecialchars($a['user_name']) ?></strong><br>
                        <small><?= htmlspecialchars($a['user_mobile'] ?? '') ?></small>
                    </td>
                    <td><?= $a['scheduled_date'] ? date('M d, Y', strtotime($a['scheduled_date'])) : 'TBD' ?></td>
                    <td><?= htmlspecialchars($a['venue'] ?? 'TBD') ?></td>
                    <td><span class="badge badge-<?= $a['status'] === 'completed' ? 'success' : ($a['status'] === 'confirmed' ? 'info' : 'warning') ?>"><?= ucfirst($a['status']) ?></span></td>
                    <td>
                        <?php if ($a['status'] === 'pending'): ?>
                        <form method="POST" action="/pandit/assignments/<?= $a['id'] ?>/confirm" style="display: inline;">
                            <?= \App\Core\Auth::csrfField() ?>
                            <button class="btn btn-sm btn-success">Confirm</button>
                        </form>
                        <?php elseif ($a['status'] === 'confirmed'): ?>
                        <div style="display: flex; gap: 8px;">
                            <a href="/pandit/assignments/<?= $a['id'] ?>/ritual" 
                               class="btn btn-sm" 
                               style="background: #8B5CF6; color: white; border-radius: 8px; padding: 6px 12px; display: flex; align-items: center; gap: 5px;"
                               title="Manage Ritual Steps">
                                <i class="fas fa-edit"></i> Manage
                            </a>
                            <form method="POST" action="/pandit/assignments/<?= $a['id'] ?>/complete" style="display: inline;">
                                <?= \App\Core\Auth::csrfField() ?>
                                <button class="btn btn-sm btn-primary" 
                                        style="border-radius: 8px; padding: 6px 12px; display: flex; align-items: center; gap: 5px;"
                                        title="Mark as Completed">
                                    <i class="fas fa-check-circle"></i> Complete
                                </button>
                            </form>
                        </div>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
