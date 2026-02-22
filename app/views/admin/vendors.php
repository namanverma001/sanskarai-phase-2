<style>
    .vendor-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
        width: 100%;
    }
    .vendor-header .card-title {
        margin: 0;
        white-space: nowrap;
    }
    .vendor-controls {
        display: flex;
        gap: 10px;
        align-items: center;
        flex-wrap: wrap;
        margin-left: auto;
    }
    .vendor-filter-form {
        display: flex;
        gap: 10px;
        align-items: center;
        flex-wrap: wrap;
    }
    .vendor-filter-form .form-field {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }
    .vendor-filter-form .form-field label {
        font-weight: 500;
        color: #374151;
        font-size: 0.9rem;
    }
    .vendor-filter-form .form-field select {
        padding: 12px 16px;
        border: 2px solid #E5E7EB;
        border-radius: 10px;
        font-size: 1rem;
        transition: all 0.3s;
        min-width: 200px;
    }
    .vendor-filter-form .form-field select:focus {
        border-color: var(--primary);
        outline: none;
        box-shadow: 0 0 0 3px rgba(255, 107, 53, 0.1);
    }
    .vendor-search-group {
        display: flex;
        align-items: stretch;
    }
    .vendor-search-group .form-control {
        border-radius: 8px 0 0 8px;
        min-width: 180px;
        width: auto;
    }
    .vendor-search-group .btn {
        border-radius: 0 8px 8px 0;
        padding: 8px 14px;
    }
    
    .vendor-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }
    .vendor-table thead th {
        background: #F8FAFC;
        padding: 14px 12px;
        font-weight: 600;
        color: #475569;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 2px solid #E2E8F0;
        white-space: nowrap;
    }
    .vendor-table tbody td {
        padding: 16px 12px;
        vertical-align: middle;
        border-bottom: 1px solid #F1F5F9;
    }
    .vendor-table tbody tr:hover {
        background: #F8FAFC;
    }
    .vendor-table tbody tr:last-child td {
        border-bottom: none;
    }
    
    .vendor-name {
        font-weight: 600;
        color: #1E293B;
        margin-bottom: 2px;
    }
    .vendor-contact-person {
        color: #64748B;
        font-size: 0.85rem;
    }
    
    .vendor-contact-info {
        font-size: 0.9rem;
        line-height: 1.6;
    }
    .vendor-contact-info i {
        width: 16px;
        color: #94A3B8;
        margin-right: 4px;
    }
    
    .vendor-location {
        font-size: 0.9rem;
        line-height: 1.5;
    }
    .vendor-location .city {
        font-weight: 500;
        color: #334155;
    }
    .vendor-location .pincode {
        color: #94A3B8;
        font-size: 0.85rem;
    }
    
    .status-badges {
        display: flex;
        gap: 5px;
        flex-wrap: wrap;
    }
    
    .action-buttons {
        display: flex;
        gap: 6px;
        align-items: center;
    }
    .action-buttons .btn {
        padding: 6px 10px;
        font-size: 0.85rem;
    }
    .action-buttons form {
        margin: 0;
    }
    
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #64748B;
    }
    .empty-state i {
        font-size: 4rem;
        color: #E2E8F0;
        margin-bottom: 15px;
    }
    .empty-state h4 {
        color: #475569;
        margin-bottom: 8px;
    }
    
    @media (max-width: 1400px) {
        .vendor-filter-form select {
            min-width: 200px;
        }
    }
    
    @media (max-width: 1200px) {
        .vendor-table thead { display: none; }
        .vendor-table, .vendor-table tbody, .vendor-table tr, .vendor-table td { 
            display: block; 
            width: 100%; 
        }
        .vendor-table tr { 
            margin-bottom: 16px; 
            background: white; 
            border-radius: 12px; 
            box-shadow: 0 2px 8px rgba(0,0,0,0.06); 
            padding: 16px; 
            border: 1px solid #E2E8F0; 
            position: relative;
            cursor: pointer;
        }
        .vendor-table tr::after {
            content: '\f078';
            font-family: 'Font Awesome 5 Free';
            font-weight: 900;
            position: absolute;
            top: 18px;
            right: 16px;
            color: #94A3B8;
            font-size: 0.9rem;
            transition: transform 0.3s;
        }
        .vendor-table tr.expanded::after {
            transform: rotate(180deg);
        }
        .vendor-table td { 
            display: none;
            padding: 10px 0; 
            border-bottom: 1px solid #F1F5F9;
        }
        .vendor-table td[data-label="ID"],
        .vendor-table td[data-label="Vendor"],
        .vendor-table td[data-label="Status"] {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .vendor-table tr.expanded td {
            display: flex;
            justify-content: space-between;
            align-items: center;
            animation: fadeIn 0.3s ease;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-5px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .vendor-table td::before { 
            content: attr(data-label); 
            font-weight: 600; 
            color: #64748B; 
            font-size: 0.85rem;
            flex-shrink: 0;
            margin-right: 10px;
        }
        .vendor-table td[data-label="ID"] { padding-right: 30px; }
        .vendor-table td[data-label="Vendor"] { 
            font-size: 1rem; 
            border-bottom: none; 
            padding-right: 30px;
        }
        .vendor-table td[data-label="Actions"] {
            border-bottom: none;
            padding-top: 15px;
        }
        .vendor-table td[data-label="Actions"]::before { display: none; }
        .vendor-table td[data-label="Actions"] .action-buttons {
            width: 100%;
            justify-content: flex-end;
        }
    }
</style>

<div class="card">
    <div class="card-header">
        <div class="vendor-header">
            <h3 class="card-title"><i class="fas fa-store"></i> Vendor Management</h3>
            <div class="vendor-controls">
                <form method="GET" action="/admin/vendors" class="vendor-filter-form">
                    <div class="form-field">
                        <select name="category" onchange="this.form.submit()">
                            <option value="">All Categories</option>
                            <?php foreach ($categories as $key => $name): ?>
                            <option value="<?= $key ?>" <?= ($selectedCategory ?? '') === $key ? 'selected' : '' ?>>
                                <?= htmlspecialchars($name) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="vendor-search-group">
                        <input type="text" name="search" class="form-control" placeholder="Search vendors..." value="<?= htmlspecialchars($search ?? '') ?>">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
                    </div>
                    <?php if (!empty($search) || !empty($selectedCategory)): ?>
                    <a href="/admin/vendors" class="btn btn-secondary" title="Clear filters"><i class="fas fa-times"></i></a>
                    <?php endif; ?>
                </form>
                <a href="/admin/vendors/create" class="btn btn-success">
                    <i class="fas fa-plus"></i> Add Vendor
                </a>
            </div>
        </div>
    </div>
    
    <div style="overflow-x: auto;">
        <table class="vendor-table">
            <thead>
                <tr>
                    <th style="width: 60px;">ID</th>
                    <th style="min-width: 180px;">Vendor</th>
                    <th style="width: 140px;">Category</th>
                    <th style="min-width: 160px;">Contact</th>
                    <th style="min-width: 140px;">Location</th>
                    <th style="width: 120px;">Status</th>
                    <th style="width: 180px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($vendors)): ?>
                <tr style="cursor: default;">
                    <td colspan="7" style="display: table-cell; text-align: center;">
                        <div class="empty-state">
                            <i class="fas fa-store"></i>
                            <h4>No vendors found</h4>
                            <p>Add your first vendor to get started</p>
                            <a href="/admin/vendors/create" class="btn btn-primary" style="margin-top: 15px;">
                                <i class="fas fa-plus"></i> Add Vendor
                            </a>
                        </div>
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($vendors as $vendor): ?>
                <tr onclick="if(window.innerWidth <= 1200) this.classList.toggle('expanded')">
                    <td data-label="ID">#<?= $vendor['id'] ?></td>
                    <td data-label="Vendor">
                        <div>
                            <div class="vendor-name"><?= htmlspecialchars($vendor['name']) ?></div>
                            <?php if (!empty($vendor['contact_person'])): ?>
                            <div class="vendor-contact-person"><i class="fas fa-user"></i> <?= htmlspecialchars($vendor['contact_person']) ?></div>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td data-label="Category">
                        <span class="badge badge-info">
                            <?= htmlspecialchars($categories[$vendor['category']] ?? ucfirst($vendor['category'])) ?>
                        </span>
                    </td>
                    <td data-label="Contact">
                        <div class="vendor-contact-info">
                            <div><i class="fas fa-phone"></i> <?= htmlspecialchars($vendor['phone']) ?></div>
                            <?php if (!empty($vendor['email'])): ?>
                            <div><i class="fas fa-envelope"></i> <?= htmlspecialchars($vendor['email']) ?></div>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td data-label="Location">
                        <div class="vendor-location">
                            <div class="city"><?= htmlspecialchars($vendor['city']) ?>, <?= htmlspecialchars($vendor['state']) ?></div>
                            <div class="pincode"><?= htmlspecialchars($vendor['pincode']) ?></div>
                        </div>
                    </td>
                    <td data-label="Status">
                        <div class="status-badges">
                            <?php if ($vendor['is_active']): ?>
                            <span class="badge badge-success">Active</span>
                            <?php else: ?>
                            <span class="badge badge-danger">Inactive</span>
                            <?php endif; ?>
                            <?php if ($vendor['is_featured']): ?>
                            <span class="badge badge-warning" title="Featured"><i class="fas fa-star"></i></span>
                            <?php endif; ?>
                            <?php if ($vendor['is_verified']): ?>
                            <span class="badge badge-primary" title="Verified"><i class="fas fa-check-circle"></i></span>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td data-label="Actions" onclick="event.stopPropagation()">
                        <div class="action-buttons">
                            <a href="/admin/vendors/<?= $vendor['id'] ?>/edit" class="btn btn-sm btn-primary" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form method="POST" action="/admin/vendors/<?= $vendor['id'] ?>/toggle-status">
                                <?= \App\Core\Auth::csrfField() ?>
                                <button type="submit" class="btn btn-sm btn-<?= $vendor['is_active'] ? 'warning' : 'success' ?>" title="<?= $vendor['is_active'] ? 'Deactivate' : 'Activate' ?>">
                                    <i class="fas fa-<?= $vendor['is_active'] ? 'eye-slash' : 'eye' ?>"></i>
                                </button>
                            </form>
                            <form method="POST" action="/admin/vendors/<?= $vendor['id'] ?>/toggle-featured">
                                <?= \App\Core\Auth::csrfField() ?>
                                <button type="submit" class="btn btn-sm btn-<?= $vendor['is_featured'] ? 'secondary' : 'warning' ?>" title="<?= $vendor['is_featured'] ? 'Unfeature' : 'Feature' ?>">
                                    <i class="fas fa-star"></i>
                                </button>
                            </form>
                            <form method="POST" action="/admin/vendors/<?= $vendor['id'] ?>/delete">
                                <?= \App\Core\Auth::csrfField() ?>
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this vendor?')" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
