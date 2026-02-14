<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-users"></i> All Users</h3>
        <div style="display: flex; gap: 10px;">
            <select onchange="window.location.href='/admin/users?role='+this.value" class="form-control" style="width: auto;">
                <option value="">All Roles</option>
                <option value="user" <?= ($filters['role'] ?? '') === 'user' ? 'selected' : '' ?>>Users</option>
                <option value="pandit" <?= ($filters['role'] ?? '') === 'pandit' ? 'selected' : '' ?>>Pandits</option>
                <option value="admin" <?= ($filters['role'] ?? '') === 'admin' ? 'selected' : '' ?>>Admins</option>
            </select>
        </div>
    </div>
    
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Mobile</th>
                <th>Role</th>
                <th>Status</th>
                <th>Joined</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($users)): ?>
            <tr>
                <td colspan="8" style="text-align: center; color: #6B7280; padding: 30px;">No users found</td>
            </tr>
            <?php else: ?>
            <?php foreach ($users as $u): ?>
            <tr>
                <td>#<?= $u['id'] ?></td>
                <td><strong><?= htmlspecialchars($u['name']) ?></strong></td>
                <td><?= htmlspecialchars($u['email']) ?></td>
                <td><?= htmlspecialchars($u['mobile'] ?? 'N/A') ?></td>
                <td><span class="badge badge-info"><?= ucfirst($u['role']) ?></span></td>
                <td>
                    <span class="badge badge-<?= $u['status'] === 'active' ? 'success' : ($u['status'] === 'blocked' ? 'danger' : 'warning') ?>">
                        <?= ucfirst($u['status']) ?>
                    </span>
                </td>
                <td><?= date('M d, Y', strtotime($u['created_at'])) ?></td>
                <td>
                    <?php if ($u['id'] !== \App\Core\Auth::id() && $u['role'] !== 'admin'): ?>
                        <?php if ($u['status'] === 'active'): ?>
                        <form method="POST" action="/admin/users/<?= $u['id'] ?>/block" style="display: inline;">
                            <?= \App\Core\Auth::csrfField() ?>
                            <button type="submit" class="btn btn-sm btn-warning" onclick="return confirm('Block this user?')" title="Block User">
                                <i class="fas fa-ban"></i>
                            </button>
                        </form>
                        <?php else: ?>
                        <form method="POST" action="/admin/users/<?= $u['id'] ?>/activate" style="display: inline;">
                            <?= \App\Core\Auth::csrfField() ?>
                            <button type="submit" class="btn btn-sm btn-success" title="Activate User">
                                <i class="fas fa-check"></i>
                            </button>
                        </form>
                        <?php endif; ?>
                        <form method="POST" action="/admin/users/<?= $u['id'] ?>/delete" style="display: inline;">
                            <?= \App\Core\Auth::csrfField() ?>
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to PERMANENTLY DELETE this user? This cannot be undone!')" title="Delete User">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    <?php elseif ($u['role'] === 'admin' && $u['id'] !== \App\Core\Auth::id()): ?>
                        <span class="text-muted">Protected</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
