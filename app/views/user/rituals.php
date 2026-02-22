<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-book"></i> Explore Rituals</h3>
        <form method="GET" style="display: flex; gap: 10px;">
            <input type="text" name="search" class="form-control" placeholder="Search..." 
                   style="width: 200px;" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
            <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-search"></i></button>
        </form>
    </div>
    
    <div style="margin-bottom: 20px; display: flex; gap: 10px; flex-wrap: wrap;">
        <a href="/user/rituals" class="btn btn-sm <?= empty($currentCategory) ? 'btn-primary' : '' ?>" 
           style="<?= !empty($currentCategory) ? 'background:#E5E7EB;color:#374151;' : '' ?>">All</a>
        <?php foreach ($categories ?? [] as $cat): ?>
        <a href="/user/rituals?category=<?= urlencode($cat) ?>" 
           class="btn btn-sm <?= $currentCategory === $cat ? 'btn-primary' : '' ?>"
           style="<?= $currentCategory !== $cat ? 'background:#E5E7EB;color:#374151;' : '' ?>">
            <?= htmlspecialchars($cat) ?>
        </a>
        <?php endforeach; ?>
    </div>
    
    <?php if (empty($rituals)): ?>
        <p style="text-align: center; color: #6B7280; padding: 30px;">No rituals found</p>
    <?php else: ?>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">
            <?php foreach ($rituals as $ritual): ?>
            <div class="ritual-card">
                <div style="display: flex; justify-content: space-between; align-items: start;">
                    <div class="ritual-title"><?= htmlspecialchars($ritual['name']) ?></div>
                    <?php if ($ritual['is_featured']): ?>
                    <span class="badge badge-warning"><i class="fas fa-star"></i></span>
                    <?php endif; ?>
                </div>
                <div class="ritual-meta">
                    <span class="badge badge-info"><?= htmlspecialchars($ritual['category']) ?></span>
                    <span class="badge badge-<?= $ritual['difficulty'] === 'easy' ? 'success' : ($ritual['difficulty'] === 'hard' ? 'danger' : 'warning') ?>" style="margin-left: 5px;">
                        <?= ucfirst($ritual['difficulty']) ?>
                    </span>
                </div>
                <p style="color: #6B7280; font-size: 0.85rem; margin: 10px 0;">
                    <i class="fas fa-clock"></i> <?= $ritual['duration_minutes'] ?> min
                    <?php if ($ritual['deity']): ?>
                    | <i class="fas fa-pray"></i> <?= htmlspecialchars($ritual['deity']) ?>
                    <?php endif; ?>
                </p>
                <p style="color: #6B7280; font-size: 0.9rem; margin-bottom: 15px;">
                    <?= htmlspecialchars(substr($ritual['description'] ?? 'No description available.', 0, 80)) ?>...
                </p>
                <a href="/user/rituals/<?= $ritual['id'] ?>" class="btn btn-sm btn-primary">
                    View Details <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
