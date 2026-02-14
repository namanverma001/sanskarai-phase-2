<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-check-double"></i> Custom Rituals for Validation</h3>
    </div>
    
    <?php if (empty($rituals)): ?>
        <p style="text-align: center; color: #6B7280; padding: 30px;">No custom rituals pending validation</p>
    <?php else: ?>
        <?php foreach ($rituals as $r): ?>
        <div style="border: 1px solid #E5E7EB; border-radius: 12px; padding: 20px; margin-bottom: 15px;">
            <div style="display: flex; justify-content: space-between; align-items: start;">
                <div>
                    <h4><?= htmlspecialchars($r['name']) ?></h4>
                    <p style="color: #6B7280; margin: 5px 0;">
                        By <?= htmlspecialchars($r['user_name']) ?>
                        <?php if ($r['base_ritual_name']): ?>
                        | Based on: <?= htmlspecialchars($r['base_ritual_name']) ?>
                        <?php endif; ?>
                    </p>
                    <?php if ($r['scheduled_date']): ?>
                    <p><i class="fas fa-calendar"></i> <?= date('M d, Y', strtotime($r['scheduled_date'])) ?></p>
                    <?php endif; ?>
                </div>
                <span class="badge badge-warning">Pending</span>
            </div>
            
            <?php if ($r['description']): ?>
            <p style="margin: 15px 0; padding: 10px; background: #F9FAFB; border-radius: 8px;">
                <?= nl2br(htmlspecialchars($r['description'])) ?>
            </p>
            <?php endif; ?>
            
            <form method="POST" action="/pandit/custom-rituals/<?= $r['id'] ?>/validate" style="margin-top: 15px;">
                <?= \App\Core\Auth::csrfField() ?>
                <div class="form-group">
                    <textarea name="notes" class="form-control" rows="2" placeholder="Validation notes (required for rejection)"></textarea>
                </div>
                <div style="display: flex; gap: 10px;">
                    <button type="submit" name="action" value="approve" class="btn btn-success">
                        <i class="fas fa-check"></i> Approve
                    </button>
                    <button type="submit" name="action" value="reject" class="btn btn-danger">
                        <i class="fas fa-times"></i> Reject
                    </button>
                </div>
            </form>
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
                <td>
                    <strong><?= htmlspecialchars($h['name']) ?></strong>
                    <?php if ($h['base_ritual_name']): ?>
                    <br><small style="color: #6B7280;">Based on: <?= htmlspecialchars($h['base_ritual_name']) ?></small>
                    <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($h['user_name']) ?></td>
                <td>
                    <span class="badge badge-<?= $h['status'] === 'approved' ? 'success' : 'danger' ?>">
                        <?= ucfirst($h['status']) ?>
                    </span>
                </td>
                <td><?= $h['validated_at'] ? date('M d, Y', strtotime($h['validated_at'])) : '-' ?></td>
                <td>
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
