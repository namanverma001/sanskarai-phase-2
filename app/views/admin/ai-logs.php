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
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
        <h3 class="card-title"><i class="fas fa-history"></i> Recent AI Requests</h3>
        <form method="GET" action="/admin/ai-logs" style="display: flex; gap: 5px; align-items: center;">
            <div class="input-group" style="width: auto; min-width: 200px; flex-wrap: nowrap;">
                <input type="text" name="search" class="form-control" placeholder="Search logs..." value="<?= htmlspecialchars($search ?? '') ?>">
                <div class="input-group-append">
                    <button type="submit" class="btn btn-primary d-flex align-items-center justify-content-center"><i class="fas fa-search"></i></button>
                </div>
            </div>
            <?php if (!empty($search)): ?>
            <a href="/admin/ai-logs" class="btn btn-secondary" title="Clear"><i class="fas fa-times"></i></a>
            <?php endif; ?>
        </form>
    </div>
    
    <table class="table mobile-card-view">
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
                <td colspan="7" style="text-align: center; color: #6B7280; padding: 30px;">No AI requests found</td>
            </tr>
            <?php else: ?>
            <?php foreach ($requests as $req): ?>
            <tr onclick="if(window.innerWidth <= 768) this.classList.toggle('expanded')" style="cursor: pointer;">
                <td data-label="ID">#<?= $req['id'] ?></td>
                <td data-label="User"><?= htmlspecialchars($req['user_name'] ?? 'Unknown') ?></td>
                <td data-label="Type"><?= htmlspecialchars($req['request_type']) ?></td>
                <td data-label="Status">
                    <span class="badge badge-<?= $req['status'] === 'completed' ? 'success' : ($req['is_flagged'] ? 'danger' : 'warning') ?>">
                        <?= $req['is_flagged'] ? 'Flagged' : ucfirst($req['status']) ?>
                    </span>
                </td>
                <td data-label="Tokens"><?= $req['tokens_used'] ?></td>
                <td data-label="Created"><?= date('M d, H:i', strtotime($req['created_at'])) ?></td>
                <td data-label="Actions" onclick="event.stopPropagation()">
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
    .mobile-card-view td[data-label="User"],
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
    .mobile-card-view td[data-label="User"] { font-size: 1.1rem; color: var(--dark); border-bottom: none; margin-bottom: 5px; padding-right: 30px; }
    .mobile-card-view td[data-label="Actions"]::before { display: none; }
}
</style>
