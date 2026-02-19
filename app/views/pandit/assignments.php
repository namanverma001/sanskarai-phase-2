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
    
    <style>
        @media (max-width: 768px) {
            .table thead { display: none; }
            .table, .table tbody, .table tr, .table td { display: block; width: 100%; }
            .table tr {
                margin-bottom: 15px;
                background: white;
                border-radius: 12px;
                box-shadow: 0 4px 10px rgba(0,0,0,0.05);
                padding: 15px;
                border: 1px solid #E5E7EB;
                position: relative; /* For absolute positioning of toggle */
            }
            .table td {
                text-align: left;
                padding: 10px 0;
                position: relative;
                border-bottom: 1px solid #F3F4F6;
                display: flex; /* Default display for visible items */
                flex-direction: column;
                gap: 5px;
            }
            
            /* Hide description/details columns by default on mobile */
            .table tr:not(.expanded) td[data-label="Ritual"],
            .table tr:not(.expanded) td[data-label="Date"],
            .table tr:not(.expanded) td[data-label="Venue"],
            .table tr:not(.expanded) td[data-label="Actions"],
            .table tr:not(.expanded) td[data-label="User"] small { 
                display: none; 
            }

            /* Always show User and Status */
            .table td[data-label="User"] {
                border-bottom: none;
                padding-bottom: 0;
            }
            .table td[data-label="Status"] {
                border-bottom: none;
                padding-top: 5px;
            }

            .table td::before {
                content: attr(data-label);
                font-size: 0.85rem;
                font-weight: 600;
                color: #9CA3AF;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                margin-bottom: 2px;
                display: none; /* Hide labels in collapsed summary view if clearer */
            }
            
            /* Show labels when expanded */
            .table tr.expanded td::before {
                display: block;
            }

            /* Adjustments for Expanded State */
            .table tr.expanded td {
                display: flex;
                border-bottom: 1px solid #F3F4F6;
            }
            .table tr.expanded td:last-child {
                border-bottom: none;
            }

            /* Toggle Button */
            .mobile-card-toggle {
                display: block;
                position: absolute;
                top: 15px;
                right: 15px;
                width: 30px;
                height: 30px;
                background: #F3F4F6;
                border-radius: 50%;
                text-align: center;
                line-height: 30px;
                color: #4B5563;
                cursor: pointer;
                z-index: 10;
                transition: transform 0.3s;
            }
            .table tr.expanded .mobile-card-toggle {
                transform: rotate(180deg);
                background: #E5E7EB;
            }
        }
        @media (min-width: 769px) {
            .mobile-card-toggle { display: none; }
        }
    </style>

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
                    <td data-label="Ritual"><?= htmlspecialchars($a['ritual_name'] ?? $a['custom_ritual_name'] ?? 'N/A') ?></td>
                    <td data-label="User">
                        <div class="mobile-card-toggle" onclick="event.stopPropagation(); this.closest('tr').classList.toggle('expanded')">
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <strong><?= htmlspecialchars($a['user_name']) ?></strong><br>
                        <small class="text-muted"><?= htmlspecialchars($a['user_mobile'] ?? '') ?></small>
                    </td>
                    <td data-label="Date"><?= $a['scheduled_date'] ? date('M d, Y', strtotime($a['scheduled_date'])) : 'TBD' ?></td>
                    <td data-label="Venue"><?= htmlspecialchars($a['venue'] ?? 'TBD') ?></td>
                    <td data-label="Status"><span class="badge badge-<?= $a['status'] === 'completed' ? 'success' : ($a['status'] === 'confirmed' ? 'info' : 'warning') ?>"><?= ucfirst($a['status']) ?></span></td>
                    <td data-label="Actions">
                        <?php if ($a['status'] === 'pending'): ?>
                        <form method="POST" action="/pandit/assignments/<?= $a['id'] ?>/confirm" style="display: inline;">
                            <?= \App\Core\Auth::csrfField() ?>
                            <button class="btn btn-sm btn-success" style="width: auto;">Confirm</button>
                        </form>
                        <?php elseif ($a['status'] === 'confirmed'): ?>
                        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                            <a href="/pandit/assignments/<?= $a['id'] ?>/ritual" 
                               class="btn btn-sm" 
                               style="background: #8B5CF6; color: white; border-radius: 8px; padding: 8px 16px; display: flex; align-items: center; gap: 5px;"
                               title="Manage Ritual Steps">
                                <i class="fas fa-edit"></i> Manage
                            </a>
                            <form method="POST" action="/pandit/assignments/<?= $a['id'] ?>/complete" style="display: inline;">
                                <?= \App\Core\Auth::csrfField() ?>
                                <button class="btn btn-sm btn-primary" 
                                        style="border-radius: 8px; padding: 8px 16px; display: flex; align-items: center; gap: 5px;"
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
