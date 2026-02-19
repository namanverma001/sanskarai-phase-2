<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-check-double"></i> Custom Rituals for Validation</h3>
    </div>
    
    <style>
        @media (max-width: 768px) {
            /* General Accordion Styles */
            .mobile-card-toggle {
                display: block;
                width: 30px;
                height: 30px;
                background: #F3F4F6;
                border-radius: 50%;
                text-align: center;
                line-height: 30px;
                color: #4B5563;
                cursor: pointer;
                transition: transform 0.3s;
                margin-left: auto; /* Push to right */
            }
            .expanded .mobile-card-toggle {
                transform: rotate(180deg);
                background: #E5E7EB;
            }

            /* Pending Section Styles */
            .pending-ritual-card .card-details {
                display: none;
            }
            .pending-ritual-card.expanded .card-details {
                display: block;
                border-top: 1px solid #F3F4F6;
                margin-top: 15px;
                padding-top: 15px;
            }
            .pending-ritual-card .card-header-mobile {
                display: flex;
                align-items: flex-start;
                gap: 10px;
                cursor: pointer;
            }
            
            /* History Table Styles (Same as Assignments) */
            .table thead { display: none; }
            .table, .table tbody, .table tr, .table td { display: block; width: 100%; }
            .table tr {
                margin-bottom: 15px;
                background: white;
                border-radius: 12px;
                box-shadow: 0 4px 10px rgba(0,0,0,0.05);
                padding: 15px;
                border: 1px solid #E5E7EB;
                position: relative;
            }
            .table td {
                text-align: left;
                padding: 10px 0;
                position: relative;
                border-bottom: 1px solid #F3F4F6;
                display: flex;
                flex-direction: column;
                gap: 5px;
            }
            
            /* Hide details by default */
            .table tr:not(.expanded) td[data-label="Requested By"],
            .table tr:not(.expanded) td[data-label="Validated On"],
            .table tr:not(.expanded) td[data-label="Notes"] { 
                display: none; 
            }
            
            /* Always show Ritual Name and Status */
            .table td[data-label="Ritual Name"] {
                border-bottom: none;
                padding-bottom: 0;
            }
            .table td[data-label="Status"] {
                border-bottom: none;
                padding-top: 5px;
            }

            /* Labels */
            .table td::before {
                content: attr(data-label);
                font-size: 0.85rem;
                font-weight: 600;
                color: #9CA3AF;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                display: none;
            }
            .table tr.expanded td::before { display: block; }
            
            .table tr.expanded td { display: flex; border-bottom: 1px solid #F3F4F6; }
            .table tr.expanded td:last-child { border-bottom: none; }
            
            /* Table Toggle */
            .table .mobile-card-toggle {
                position: absolute;
                top: 15px;
                right: 15px;
                margin: 0;
            }
        }
        @media (min-width: 769px) {
            .mobile-card-toggle { display: none; }
        }
    </style>

    <?php if (empty($rituals)): ?>
        <p style="text-align: center; color: #6B7280; padding: 30px;">No custom rituals pending validation</p>
    <?php else: ?>
        <?php foreach ($rituals as $r): ?>
        <div class="pending-ritual-card" style="border: 1px solid #E5E7EB; border-radius: 12px; padding: 20px; margin-bottom: 15px;">
            <!-- Mobile Header Wrapper for toggle -->
            <div class="card-header-mobile" onclick="if(window.innerWidth <= 768) this.closest('.pending-ritual-card').classList.toggle('expanded')">
                <div style="flex: 1;">
                    <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 10px;">
                        <div>
                            <h4 style="margin: 0;"><?= htmlspecialchars($r['name']) ?></h4>
                            <p style="color: #6B7280; margin: 5px 0; font-size: 0.9rem;">
                                By <?= htmlspecialchars($r['user_name']) ?>
                            </p>
                        </div>
                        <span class="badge badge-warning">Pending</span>
                    </div>
                     <!-- Summary Info (Always Visible) -->
                    <div>
                         <?php if ($r['base_ritual_name']): ?>
                         <small style="display: block; color: #4B5563; margin-bottom: 3px;">
                             <strong>Base:</strong> <?= htmlspecialchars($r['base_ritual_name']) ?>
                         </small>
                         <?php endif; ?>
                         <?php if ($r['scheduled_date']): ?>
                         <small style="display: block; color: #4B5563;">
                             <i class="fas fa-calendar" style="width: 15px;"></i> <?= date('M d, Y', strtotime($r['scheduled_date'])) ?>
                         </small>
                         <?php endif; ?>
                    </div>
                </div>
                <div class="mobile-card-toggle">
                    <i class="fas fa-chevron-down"></i>
                </div>
            </div>
            
            <!-- Details Section (Collapsible on Mobile) -->
            <div class="card-details">
                <?php if ($r['description']): ?>
                <div style="margin: 15px 0; padding: 12px; background: #F9FAFB; border-radius: 8px;">
                    <strong style="font-size: 0.9rem; color: #374151; display: block; margin-bottom: 5px;">Description:</strong>
                    <p style="margin: 0; color: #4B5563; line-height: 1.5; font-size: 0.95rem;">
                        <?= nl2br(htmlspecialchars($r['description'])) ?>
                    </p>
                </div>
                <?php endif; ?>
                
                <form method="POST" action="/pandit/custom-rituals/<?= $r['id'] ?>/validate" style="margin-top: 15px;">
                    <?= \App\Core\Auth::csrfField() ?>
                    <div class="form-group">
                        <textarea name="notes" class="form-control" rows="2" placeholder="Validation notes (required for rejection)"></textarea>
                    </div>
                    <div style="display: flex; gap: 10px;">
                        <button type="submit" name="action" value="approve" class="btn btn-success" style="flex: 1;">
                            <i class="fas fa-check"></i> Approve
                        </button>
                        <button type="submit" name="action" value="reject" class="btn btn-danger" style="flex: 1;">
                            <i class="fas fa-times"></i> Reject
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Validation History Section -->
<?php if (!empty($history)): ?>
<div class="card" style="margin-top: 25px;">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-history"></i> Validation History</h3>
    </div>
    
    <table class="table">
        <thead>
            <tr>
                <th>Ritual Name</th>
                <th>Requested By</th>
                <th>Status</th>
                <th>Validated On</th>
                <th>Notes</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($history as $h): ?>
            <tr>
                <td data-label="Ritual Name">
                    <div class="mobile-card-toggle" onclick="event.stopPropagation(); this.closest('tr').classList.toggle('expanded')">
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <strong><?= htmlspecialchars($h['name']) ?></strong>
                    <?php if ($h['base_ritual_name']): ?>
                    <br><small style="color: #6B7280;">Based on: <?= htmlspecialchars($h['base_ritual_name']) ?></small>
                    <?php endif; ?>
                </td>
                <td data-label="Requested By"><?= htmlspecialchars($h['user_name']) ?></td>
                <td data-label="Status">
                    <span class="badge badge-<?= $h['status'] === 'approved' ? 'success' : 'danger' ?>">
                        <?= ucfirst($h['status']) ?>
                    </span>
                </td>
                <td data-label="Validated On"><?= $h['validated_at'] ? date('M d, Y', strtotime($h['validated_at'])) : '-' ?></td>
                <td data-label="Notes">
                    <?php if ($h['validation_notes']): ?>
                    <span style="color: #6B7280; font-size: 0.9rem;">
                        <?= htmlspecialchars(substr($h['validation_notes'], 0, 50)) ?><?= strlen($h['validation_notes']) > 50 ? '...' : '' ?>
                    </span>
                    <?php else: ?>
                    <span style="color: #9CA3AF;">-</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>
