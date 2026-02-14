<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-pray"></i> Ritual Management</h3>
        <div style="display: flex; gap: 10px;">
            <a href="/admin/rituals/generate" class="btn btn-success">
                <i class="fas fa-magic"></i> AI Generate
            </a>
            <a href="/admin/rituals/create" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add Manually
            </a>
        </div>
    </div>
    
    <table class="table">
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
            <tr>
                <td>#<?= $ritual['id'] ?></td>
                <td>
                    <strong><?= htmlspecialchars($ritual['name']) ?></strong>
                    <?php if (!empty($ritual['name_sanskrit'])): ?>
                    <br><small style="color: #6B7280;"><?= htmlspecialchars($ritual['name_sanskrit']) ?></small>
                    <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($ritual['category']) ?></td>
                <td>
                    <span class="badge badge-<?= $ritual['difficulty'] === 'easy' ? 'success' : ($ritual['difficulty'] === 'hard' ? 'danger' : 'warning') ?>">
                        <?= ucfirst($ritual['difficulty']) ?>
                    </span>
                </td>
                <td><?= $ritual['duration_minutes'] ?> min</td>
                <td>
                    <span class="badge badge-<?= $ritual['is_active'] ? 'success' : 'danger' ?>">
                        <?= $ritual['is_active'] ? 'Active' : 'Inactive' ?>
                    </span>
                </td>
                <td>
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
