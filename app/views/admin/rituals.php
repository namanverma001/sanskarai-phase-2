<div class="card">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
        <h3 class="card-title"><i class="fas fa-pray"></i> Ritual Management</h3>
        <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
            <form method="GET" action="/admin/rituals" style="display: flex; gap: 5px; align-items: center;">
                <div class="input-group" style="width: auto; min-width: 200px; flex-wrap: nowrap;">
                    <input type="text" name="search" class="form-control" placeholder="Search..." value="<?= htmlspecialchars($search ?? '') ?>">
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-primary d-flex align-items-center justify-content-center"><i class="fas fa-search"></i></button>
                    </div>
                </div>
                <?php if (!empty($search)): ?>
                <a href="/admin/rituals" class="btn btn-secondary" title="Clear"><i class="fas fa-times"></i></a>
                <?php endif; ?>
            </form>
            <a href="/admin/rituals/generate" class="btn btn-success">
                <i class="fas fa-magic"></i> AI Generate
            </a>
            <a href="/admin/rituals/create" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add
            </a>
        </div>
    </div>
    
    <table class="table mobile-card-view">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Category</th>
                <th>Difficulty</th>
                <th>Duration</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($rituals)): ?>
            <tr>
                <td colspan="7" style="text-align: center; color: #6B7280; padding: 30px;">No rituals found</td>
            </tr>
            <?php else: ?>
            <?php foreach ($rituals as $ritual): ?>
            <tr onclick="if(window.innerWidth <= 768) this.classList.toggle('expanded')" style="cursor: pointer;">
                <td data-label="ID">#<?= $ritual['id'] ?></td>
                <td data-label="Name">
                    <strong><?= htmlspecialchars($ritual['name']) ?></strong>
                    <?php if (!empty($ritual['name_sanskrit'])): ?>
                    <br><small style="color: #6B7280;"><?= htmlspecialchars($ritual['name_sanskrit']) ?></small>
                    <?php endif; ?>
                </td>
                <td data-label="Category"><?= htmlspecialchars($ritual['category']) ?></td>
                <td data-label="Difficulty">
                    <span class="badge badge-<?= $ritual['difficulty'] === 'easy' ? 'success' : ($ritual['difficulty'] === 'hard' ? 'danger' : 'warning') ?>">
                        <?= ucfirst($ritual['difficulty']) ?>
                    </span>
                </td>
                <td data-label="Duration"><?= $ritual['duration_minutes'] ?> min</td>
                <td data-label="Status">
                    <span class="badge badge-<?= $ritual['is_active'] ? 'success' : 'danger' ?>">
                        <?= $ritual['is_active'] ? 'Active' : 'Inactive' ?>
                    </span>
                </td>
                <td data-label="Actions" onclick="event.stopPropagation()">
                    <a href="/admin/rituals/<?= $ritual['id'] ?>/edit" class="btn btn-sm btn-primary">
                        <i class="fas fa-edit"></i>
                    </a>
                    <form method="POST" action="/admin/rituals/<?= $ritual['id'] ?>/delete" style="display: inline;">
                        <?= \App\Core\Auth::csrfField() ?>
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this ritual?')">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
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
