<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-users"></i> My Families</h3>
        <a href="/user/families/create" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add Family
        </a>
    </div>
    
    <?php if (empty($families)): ?>
        <div style="text-align: center; padding: 50px; color: #6B7280;">
            <i class="fas fa-users" style="font-size: 3rem; margin-bottom: 20px; color: #E5E7EB;"></i>
            <p>No family setup yet. Create your first family to get personalized ritual suggestions.</p>
            <a href="/user/families/create" class="btn btn-primary" style="margin-top: 20px;">
                <i class="fas fa-plus"></i> Create Family
            </a>
        </div>
    <?php else: ?>
        <?php foreach ($families as $family): ?>
        <div style="border: 1px solid #E5E7EB; border-radius: 12px; padding: 20px; margin-bottom: 15px;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h4><?= htmlspecialchars($family['family_name']) ?></h4>
                    <p style="color: #6B7280; font-size: 0.9rem; margin-top: 5px;">
                        <?php if ($family['gotra']): ?>Gotra: <?= htmlspecialchars($family['gotra']) ?> | <?php endif; ?>
                        <?= htmlspecialchars($family['city'] ?? 'Location not set') ?>
                    </p>
                </div>
                <div style="display: flex; gap: 8px;">
                    <a href="/user/families/<?= $family['id'] ?>/edit" class="btn btn-sm btn-primary">
                        <i class="fas fa-edit"></i> Manage
                    </a>
                    <form method="POST" action="/user/families/<?= $family['id'] ?>/delete" style="display: inline;" onsubmit="return confirm('Delete this family and all its members?')">
                        <?= \App\Core\Auth::csrfField() ?>
                        <button type="submit" class="btn btn-sm btn-danger">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
