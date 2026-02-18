<div class="card">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
        <h3 class="card-title"><i class="fas fa-users"></i> All Users</h3>
        <form method="GET" action="/admin/users" style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
            <select name="role" onchange="this.form.submit()" class="form-control" style="width: auto;">
                <option value="">All Roles</option>
                <option value="user" <?= ($filters['role'] ?? '') === 'user' ? 'selected' : '' ?>>Users</option>
                <option value="pandit" <?= ($filters['role'] ?? '') === 'pandit' ? 'selected' : '' ?>>Pandits</option>
                <option value="admin" <?= ($filters['role'] ?? '') === 'admin' ? 'selected' : '' ?>>Admins</option>
            </select>
            <div class="input-group" style="width: auto; min-width: 250px; flex-wrap: nowrap;">
                <input type="text" name="search" class="form-control" placeholder="Search by name..." value="<?= htmlspecialchars($filters['search'] ?? '') ?>">
                <div class="input-group-append">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
                </div>
            </div>
            <?php if (!empty($filters['role']) || !empty($filters['search'])): ?>
            <a href="/admin/users" class="btn btn-secondary" title="Clear Filters"><i class="fas fa-times"></i></a>
            <?php endif; ?>
        </form>
    </div>
    
    <table class="table mobile-card-view">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Mobile</th>
                <th>Community</th>
                <!-- Kul Devi/Devta removed -->
                <th>Role</th>
                <th>Status</th>
                <th>Joined</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($users)): ?>
            <tr>
                <td colspan="9" style="text-align: center; color: #6B7280; padding: 30px;">No users found</td>
            </tr>
            <?php else: ?>
            <?php foreach ($users as $u): ?>
            <tr onclick="if(window.innerWidth <= 768) this.classList.toggle('expanded')" style="cursor: pointer;">
                <td data-label="ID">#<?= $u['id'] ?></td>
                <td data-label="Name"><strong><?= htmlspecialchars($u['name']) ?></strong></td>
                <td data-label="Email"><?= htmlspecialchars($u['email']) ?></td>
                <td data-label="Mobile"><?= htmlspecialchars($u['mobile'] ?? 'N/A') ?></td>
                <td data-label="Community"><?= htmlspecialchars($u['community_name'] ?? '-') ?></td>
                <!-- Kul Devi/Devta removed -->
                <td data-label="Role"><span class="badge badge-info"><?= ucfirst($u['role']) ?></span></td>
                <td data-label="Status">
                    <span class="badge badge-<?= $u['status'] === 'active' ? 'success' : ($u['status'] === 'blocked' ? 'danger' : 'warning') ?>">
                        <?= ucfirst($u['status']) ?>
                    </span>
                </td>
                <td data-label="Joined"><?= date('M d, Y', strtotime($u['created_at'])) ?></td>
                <td data-label="Actions" onclick="event.stopPropagation()">
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

<style>
@media (max-width: 768px) {
    .mobile-card-view thead { display: none; }
    .mobile-card-view, .mobile-card-view tbody, .mobile-card-view tr, .mobile-card-view td { display: block; width: 100%; }
    .mobile-card-view tr { 
        margin-bottom: 20px; 
        background: white; 
        border-radius: 12px; 
        box-shadow: 0 2px 8px rgba(0,0,0,0.05); 
        padding: 15px; 
        border: 1px solid #E5E7EB; 
        position: relative;
    }
    
    /* Toggle Indicator */
    .mobile-card-view tr::after {
        content: '\f078'; /* fa-chevron-down */
        font-family: 'Font Awesome 5 Free';
        font-weight: 900;
        position: absolute;
        top: 20px;
        right: 20px;
        color: #6B7280;
        transition: transform 0.3s;
    }
    .mobile-card-view tr.expanded::after {
        transform: rotate(180deg);
    }

    /* Cell layout */
    .mobile-card-view td { 
        display: none; /* Hide all by default */
        justify-content: space-between; 
        align-items: center; 
        text-align: right; 
        padding: 10px 0; 
        border-bottom: 1px solid #F3F4F6; 
    }
    
    /* Always visible fields */
    .mobile-card-view td[data-label="ID"],
    .mobile-card-view td[data-label="Name"],
    .mobile-card-view td[data-label="Role"],
    .mobile-card-view td[data-label="Status"] {
        display: flex;
    }

    /* Show all when expanded */
    .mobile-card-view tr.expanded td {
        display: flex;
        animation: fadeIn 0.3s ease;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    .mobile-card-view td:last-child { border-bottom: none; justify-content: flex-end; gap: 10px; padding-top: 15px; }
    .mobile-card-view td::before { content: attr(data-label); font-weight: 600; color: #6B7280; font-size: 0.85rem; }
    .mobile-card-view td[data-label="ID"] { padding-right: 30px; }
    .mobile-card-view td[data-label="Name"] { font-size: 1.1rem; color: var(--dark); border-bottom: none; margin-bottom: 5px; padding-right: 30px; }
    .mobile-card-view td[data-label="Actions"]::before { display: none; }
}
</style>
