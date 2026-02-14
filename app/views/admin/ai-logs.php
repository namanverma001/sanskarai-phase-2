<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon blue"><i class="fas fa-robot"></i></div>
        <div class="stat-value"><?= $stats['total'] ?? 0 ?></div>
        <div class="stat-label">Total Requests</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green"><i class="fas fa-check"></i></div>
        <div class="stat-value"><?= $stats['completed'] ?? 0 ?></div>
        <div class="stat-label">Completed</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon orange"><i class="fas fa-flag"></i></div>
        <div class="stat-value"><?= $stats['flagged'] ?? 0 ?></div>
        <div class="stat-label">Flagged</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon purple"><i class="fas fa-coins"></i></div>
        <div class="stat-value"><?= number_format($stats['total_tokens'] ?? 0) ?></div>
        <div class="stat-label">Total Tokens</div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-history"></i> Recent AI Requests</h3>
    </div>
    
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>User</th>
                <th>Type</th>
                <th>Status</th>
                <th>Tokens</th>
                <th>Created</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($requests)): ?>
            <tr>
                <td colspan="7" style="text-align: center; color: #6B7280; padding: 30px;">No AI requests yet</td>
            </tr>
            <?php else: ?>
            <?php foreach ($requests as $req): ?>
            <tr>
                <td>#<?= $req['id'] ?></td>
                <td><?= htmlspecialchars($req['user_name'] ?? 'Unknown') ?></td>
                <td><?= htmlspecialchars($req['request_type']) ?></td>
                <td>
                    <span class="badge badge-<?= $req['status'] === 'completed' ? 'success' : ($req['is_flagged'] ? 'danger' : 'warning') ?>">
                        <?= $req['is_flagged'] ? 'Flagged' : ucfirst($req['status']) ?>
                    </span>
                </td>
                <td><?= $req['tokens_used'] ?></td>
                <td><?= date('M d, H:i', strtotime($req['created_at'])) ?></td>
                <td>
                    <?php if ($req['is_flagged']): ?>
                    <form method="POST" action="/admin/ai-logs/<?= $req['id'] ?>/unflag" style="display: inline;">
                        <?= \App\Core\Auth::csrfField() ?>
                        <button type="submit" class="btn btn-sm btn-success" title="Unflag this request">
                            <i class="fas fa-check"></i> Unflag
                        </button>
                    </form>
                    <?php else: ?>
                    <form method="POST" action="/admin/ai-logs/<?= $req['id'] ?>/flag" style="display: inline;">
                        <?= \App\Core\Auth::csrfField() ?>
                        <input type="hidden" name="reason" value="Flagged for review by admin">
                        <button type="submit" class="btn btn-sm btn-danger" title="Flag this request">
                            <i class="fas fa-flag"></i> Flag
                        </button>
                    </form>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
