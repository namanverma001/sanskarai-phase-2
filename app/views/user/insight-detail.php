<a href="/user/insights" class="btn btn-sm" style="background: #E5E7EB; color: #374151; margin-bottom: 20px;">
    <i class="fas fa-arrow-left"></i> Back to Insights
</a>

<div class="card">
    <div style="margin-bottom: 15px;">
        <span class="badge badge-info"><?= htmlspecialchars($insight['category']) ?></span>
        <span style="color: #6B7280; font-size: 0.85rem; margin-left: 15px;">
            <i class="fas fa-eye"></i> <?= $insight['view_count'] ?? 0 ?> views
        </span>
    </div>
    
    <h1 style="font-size: 2rem; font-weight: 700; color: var(--dark); margin-bottom: 15px;">
        <?= htmlspecialchars($insight['title']) ?>
    </h1>
    
    <?php if ($insight['summary']): ?>
    <p style="font-size: 1.1rem; color: #4B5563; margin-bottom: 25px; line-height: 1.6; font-style: italic;">
        <?= htmlspecialchars($insight['summary']) ?>
    </p>
    <?php endif; ?>
    
    <div style="border-top: 1px solid #E5E7EB; padding-top: 25px; line-height: 1.8; color: #374151;">
        <?= nl2br(htmlspecialchars($insight['content'])) ?>
    </div>
    
    <?php if ($insight['source']): ?>
    <div style="margin-top: 25px; padding: 15px; background: #F9FAFB; border-radius: 10px; font-size: 0.9rem;">
        <strong>Source:</strong> <?= htmlspecialchars($insight['source']) ?>
    </div>
    <?php endif; ?>
    
    <?php if (!empty($insight['tags'])): ?>
    <div style="margin-top: 20px;">
        <strong>Tags:</strong>
        <?php foreach (explode(',', $insight['tags']) as $tag): ?>
        <span class="badge" style="background: #E5E7EB; color: #374151; margin-left: 5px;">
            <?= htmlspecialchars(trim($tag)) ?>
        </span>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>
