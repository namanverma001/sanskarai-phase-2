<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-magic"></i> My Custom Rituals</h3>
        <a href="/user/custom-rituals/create" class="btn btn-primary">
            <i class="fas fa-plus"></i> Create Custom Ritual
        </a>
    </div>
    
    <?php if (empty($rituals)): ?>
        <div style="text-align: center; padding: 50px; color: #6B7280;">
            <i class="fas fa-magic" style="font-size: 3rem; margin-bottom: 20px; color: #E5E7EB;"></i>
            <p>No custom rituals yet. Create one for your special occasion!</p>
        </div>
    <?php else: ?>
        <?php foreach ($rituals as $r): ?>
        <div style="border: 1px solid #E5E7EB; border-radius: 12px; padding: 20px; margin-bottom: 15px;">
            <div style="display: flex; justify-content: space-between; align-items: start;">
                <div>
                    <h4><a href="/user/custom-rituals/<?= $r['id'] ?>" style="color: inherit; text-decoration: none;"><?= htmlspecialchars($r['name']) ?></a></h4>
                    <?php if ($r['scheduled_date']): ?>
                    <p style="color: #6B7280; font-size: 0.9rem;">
                        <i class="fas fa-calendar"></i> <?= date('M d, Y', strtotime($r['scheduled_date'])) ?>
                    </p>
                    <?php endif; ?>
                    <?php if ($r['assigned_pandit_name']): ?>
                    <p style="color: #6B7280; font-size: 0.9rem;">
                        <i class="fas fa-user-tie"></i> <?= htmlspecialchars($r['assigned_pandit_name']) ?>
                    </p>
                    <?php endif; ?>
                </div>
                <span class="badge badge-<?= $r['status'] === 'approved' ? 'success' : ($r['status'] === 'rejected' ? 'danger' : 'warning') ?>">
                    <?= ucfirst($r['status']) ?>
                </span>
            </div>
            
            <?php if ($r['description']): ?>
            <p style="color: #6B7280; margin: 15px 0; font-size: 0.9rem;">
                <?= htmlspecialchars(substr($r['description'], 0, 150)) ?>...
            </p>
            <?php endif; ?>
            
            <?php if ($r['status'] === 'draft'): ?>
            <div style="display: flex; gap: 8px; margin-top: 10px;">
                <form method="POST" action="/user/custom-rituals/<?= $r['id'] ?>/submit" style="display: inline;">
                    <?= \App\Core\Auth::csrfField() ?>
                    <button class="btn btn-sm btn-success">
                        <i class="fas fa-paper-plane"></i> Submit for Validation
                    </button>
                </form>
                <form method="POST" action="/user/custom-rituals/<?= $r['id'] ?>/delete" style="display: inline;" onsubmit="return confirm('Delete this custom ritual?')">
                    <?= \App\Core\Auth::csrfField() ?>
                    <button type="submit" class="btn btn-sm btn-danger">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </form>
            </div>
            <?php elseif ($r['status'] === 'rejected'): ?>
            <form method="POST" action="/user/custom-rituals/<?= $r['id'] ?>/delete" style="margin-top: 10px;" onsubmit="return confirm('Delete this rejected custom ritual?')">
                <?= \App\Core\Auth::csrfField() ?>
                <button type="submit" class="btn btn-sm btn-danger">
                    <i class="fas fa-trash"></i> Delete
                </button>
            </form>
            <?php endif; ?>
            
            <?php if ($r['validation_notes']): ?>
            <div style="margin-top: 15px; padding: 10px; background: #F9FAFB; border-radius: 8px; font-size: 0.9rem;">
                <strong>Validator Notes:</strong> <?= htmlspecialchars($r['validation_notes']) ?>
            </div>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
