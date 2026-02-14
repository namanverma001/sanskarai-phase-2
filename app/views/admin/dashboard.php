<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon blue"><i class="fas fa-users"></i></div>
        <div class="stat-value"><?= $stats['users']['total_users'] ?? 0 ?></div>
        <div class="stat-label">Total Users</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon purple"><i class="fas fa-pray"></i></div>
        <div class="stat-value"><?= $stats['users']['total_pandits'] ?? 0 ?></div>
        <div class="stat-label">Total Pandits</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green"><i class="fas fa-book"></i></div>
        <div class="stat-value"><?= $stats['rituals']['total'] ?? 0 ?></div>
        <div class="stat-label">Rituals</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon orange"><i class="fas fa-robot"></i></div>
        <div class="stat-value"><?= $stats['ai']['total'] ?? 0 ?></div>
        <div class="stat-label">AI Requests</div>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 25px;">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-clock"></i> Pending Pandit Approvals</h3>
            <a href="/admin/pandits/pending" class="btn btn-sm btn-primary">View All</a>
        </div>
        <?php if (empty($pendingPandits)): ?>
            <p style="color: #6B7280; text-align: center; padding: 20px;">No pending approvals</p>
        <?php else: ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Specialization</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach (array_slice($pendingPandits, 0, 5) as $pandit): ?>
                    <tr>
                        <td><?= htmlspecialchars($pandit['name']) ?></td>
                        <td><?= htmlspecialchars($pandit['specialization'] ?? 'N/A') ?></td>
                        <td>
                            <form method="POST" action="/admin/pandits/<?= $pandit['id'] ?>/approve" style="display: inline;">
                                <?= \App\Core\Auth::csrfField() ?>
                                <button type="submit" class="btn btn-sm btn-success">Approve</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
    
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-user-plus"></i> Recent Users</h3>
            <a href="/admin/users" class="btn btn-sm btn-primary">View All</a>
        </div>
        <?php if (empty($recentUsers)): ?>
            <p style="color: #6B7280; text-align: center; padding: 20px;">No users yet</p>
        <?php else: ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Role</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentUsers as $u): ?>
                    <tr>
                        <td>
                            <strong><?= htmlspecialchars($u['name']) ?></strong><br>
                            <small style="color: #6B7280;"><?= htmlspecialchars($u['email']) ?></small>
                        </td>
                        <td><span class="badge badge-info"><?= ucfirst($u['role']) ?></span></td>
                        <td>
                            <span class="badge badge-<?= $u['status'] === 'active' ? 'success' : 'danger' ?>">
                                <?= ucfirst($u['status']) ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<div class="card" style="margin-top: 25px;">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-chart-line"></i> Assignment Statistics</h3>
    </div>
    <div class="stats-grid">
        <div style="text-align: center; padding: 20px;">
            <div style="font-size: 2rem; font-weight: 700; color: #F59E0B;"><?= $stats['assignments']['pending'] ?? 0 ?></div>
            <div style="color: #6B7280;">Pending</div>
        </div>
        <div style="text-align: center; padding: 20px;">
            <div style="font-size: 2rem; font-weight: 700; color: #6366F1;"><?= $stats['assignments']['confirmed'] ?? 0 ?></div>
            <div style="color: #6B7280;">Confirmed</div>
        </div>
        <div style="text-align: center; padding: 20px;">
            <div style="font-size: 2rem; font-weight: 700; color: #10B981;"><?= $stats['assignments']['completed'] ?? 0 ?></div>
            <div style="color: #6B7280;">Completed</div>
        </div>
        <div style="text-align: center; padding: 20px;">
            <div style="font-size: 2rem; font-weight: 700; color: #EF4444;"><?= $stats['assignments']['cancelled'] ?? 0 ?></div>
            <div style="color: #6B7280;">Cancelled</div>
        </div>
    </div>
</div>
