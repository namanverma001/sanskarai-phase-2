<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-lightbulb"></i> Cultural Insights</h3>
    </div>
    
    <div style="margin-bottom: 20px; display: flex; gap: 10px; flex-wrap: wrap;">
        <a href="/user/insights" class="btn btn-sm <?= empty($_GET['category']) ? 'btn-primary' : '' ?>" 
           style="<?= !empty($_GET['category']) ? 'background:#E5E7EB;color:#374151;' : '' ?>">All</a>
        <?php foreach ($categories ?? [] as $cat): ?>
        <a href="/user/insights?category=<?= urlencode($cat) ?>" 
           class="btn btn-sm <?= ($_GET['category'] ?? '') === $cat ? 'btn-primary' : '' ?>"
           style="<?= ($_GET['category'] ?? '') !== $cat ? 'background:#E5E7EB;color:#374151;' : '' ?>">
            <?= htmlspecialchars($cat) ?>
        </a>
        <?php endforeach; ?>
    </div>
    
    <?php if (empty($insights)): ?>
        <p style="text-align: center; color: #6B7280; padding: 30px;">No cultural insights available.</p>
    <?php else: ?>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">
            <?php foreach ($insights as $insight): ?>
            <div style="border: 1px solid #E5E7EB; border-radius: 12px; overflow: hidden; transition: all 0.3s;">
                <div style="padding: 20px;">
                    <span class="badge badge-info" style="margin-bottom: 10px;"><?= htmlspecialchars($insight['category']) ?></span>
                    <h4 style="margin-bottom: 10px;">
                        <a href="/user/insights/<?= $insight['slug'] ?>" style="color: var(--dark); text-decoration: none;">
                            <?= htmlspecialchars($insight['title']) ?>
                        </a>
                    </h4>
                    <p style="color: #6B7280; font-size: 0.9rem; margin-bottom: 15px;">
                        <?= htmlspecialchars(substr($insight['summary'] ?? $insight['content'], 0, 100)) ?>...
                    </p>
                    <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.85rem; color: #6B7280;">
                        <span><i class="fas fa-eye"></i> <?= $insight['view_count'] ?? 0 ?> views</span>
                        <a href="/user/insights/<?= $insight['slug'] ?>" style="color: var(--primary);">
                            Read More <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
