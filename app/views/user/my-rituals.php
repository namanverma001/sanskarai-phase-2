<style>
    .my-rituals-header {
        background: linear-gradient(135deg, #10B981 0%, #059669 100%);
        border-radius: 20px;
        padding: 40px;
        color: white;
        margin-bottom: 30px;
        position: relative;
        overflow: hidden;
    }

    .my-rituals-header::before {
        content: '📿';
        position: absolute;
        right: 30px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 5rem;
        opacity: 0.2;
    }

    .my-rituals-header h1 {
        font-size: 2rem;
        margin-bottom: 10px;
    }

    .action-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
    }

    .rituals-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 25px;
    }

    .ritual-card {
        background: white;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        transition: all 0.3s;
    }

    .ritual-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
    }

    .ritual-card-header {
        background: linear-gradient(135deg, #D1FAE5 0%, #A7F3D0 100%);
        padding: 20px;
        border-bottom: 1px solid #6EE7B7;
        position: relative;
    }

    .ritual-card-header.ai-generated::after {
        content: 'AI Generated';
        position: absolute;
        top: 10px;
        right: 10px;
        background: #8B5CF6;
        color: white;
        padding: 3px 10px;
        border-radius: 15px;
        font-size: 0.7rem;
        font-weight: 500;
    }

    .ritual-card-header h4 {
        font-size: 1.2rem;
        color: var(--dark);
        margin-bottom: 5px;
        padding-right: 80px;
    }

    .ritual-card-header .sanskrit {
        font-size: 0.85rem;
        color: #065F46;
        font-style: italic;
    }

    .ritual-card-body {
        padding: 20px;
    }

    .ritual-meta {
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
        margin-bottom: 15px;
    }

    .meta-item {
        display: flex;
        align-items: center;
        gap: 6px;
        color: #6B7280;
        font-size: 0.85rem;
    }

    .meta-item i {
        color: var(--primary);
    }

    .ritual-description {
        color: #6B7280;
        font-size: 0.9rem;
        line-height: 1.6;
        margin-bottom: 20px;
    }

    .ritual-actions {
        display: flex;
        gap: 10px;
    }

    .ritual-actions .btn {
        flex: 1;
        text-align: center;
        padding: 12px;
        border-radius: 8px;
        font-weight: 500;
        text-decoration: none;
    }

    .btn-start {
        background: linear-gradient(135deg, var(--primary) 0%, #FF8C42 100%);
        color: white;
    }

    .btn-start:hover {
        box-shadow: 0 5px 20px rgba(255, 107, 53, 0.3);
    }

    .btn-view-details {
        background: #F3F4F6;
        color: var(--dark);
    }

    .btn-view-details:hover {
        background: #E5E7EB;
    }

    /* History Section */
    .history-section {
        margin-top: 40px;
    }

    .history-section h3 {
        margin-bottom: 20px;
        color: var(--dark);
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .history-section h3 i {
        color: var(--accent);
    }

    .history-list {
        background: white;
        border-radius: 16px;
        overflow: hidden;
    }

    .history-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 20px;
        border-bottom: 1px solid #E5E7EB;
        transition: all 0.3s;
    }

    .history-item:hover {
        background: #F9FAFB;
    }

    .history-item:last-child {
        border-bottom: none;
    }

    .history-info h5 {
        font-weight: 600;
        color: var(--dark);
        margin-bottom: 3px;
    }

    .history-info p {
        font-size: 0.85rem;
        color: #6B7280;
    }

    .history-status {
        padding: 5px 12px;
        border-radius: 15px;
        font-size: 0.75rem;
        font-weight: 500;
    }

    .status-completed {
        background: #D1FAE5;
        color: #065F46;
    }

    .status-in_progress {
        background: #FEF3C7;
        color: #92400E;
    }

    .status-not_started {
        background: #F3F4F6;
        color: #6B7280;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #6B7280;
    }

    .empty-state i {
        font-size: 4rem;
        opacity: 0.3;
        margin-bottom: 20px;
    }

    .empty-state h3 {
        margin-bottom: 10px;
        color: var(--dark);
    }
    .btn-delete {
        background: #FEE2E2;
        color: #DC2626;
        flex: 0 0 50px; /* Fixed width for delete button */
        display: flex;
        align-items: center;
        justify-content: center;
        border: none;
        cursor: pointer;
        font-size: 1.1rem;
        transition: all 0.2s;
    }

    .btn-delete:hover {
        background: #FECACA;
        color: #B91C1C;
    }
</style>

<div class="my-rituals-header">
    <h1><i class="fas fa-book-reader"></i> My Rituals</h1>
    <p>Your personal collection of rituals - customized just for you</p>
</div>

<div class="action-bar">
    <h3 style="color: var(--dark);"><i class="fas fa-folder-open"></i> Your Collection (
        <?= count($rituals ?? []) ?>)
    </h3>
    <a
        href="/user/rituals"
        class="btn btn-primary"
    >
        <i class="fas fa-plus"></i> Add New Ritual
    </a>
</div>

<?php if (empty($rituals)): ?>
    <div class="card">
        <div class="empty-state">
            <i class="fas fa-book"></i>
            <h3>No Rituals Yet</h3>
            <p>Start by exploring rituals and adding them to your collection.</p>
            <a
                href="/user/rituals"
                class="btn btn-primary"
                style="margin-top: 20px;"
            >
                <i class="fas fa-search"></i> Explore Rituals
            </a>
        </div>
    </div>
<?php else: ?>
    <div class="rituals-grid">
        <?php foreach ($rituals as $ritual): ?>
            <div class="ritual-card">
                <div class="ritual-card-header <?= $ritual['is_ai_generated'] ? 'ai-generated' : '' ?>">
                    <h4>
                        <?= htmlspecialchars($ritual['name']) ?>
                    </h4>
                    <?php if ($ritual['name_sanskrit']): ?>
                        <span class="sanskrit">
                            <?= htmlspecialchars($ritual['name_sanskrit']) ?>
                        </span>
                    <?php endif; ?>
                </div>
                <div class="ritual-card-body">
                    <div class="ritual-meta">
                        <?php if ($ritual['category']): ?>
                            <span class="meta-item">
                                <i class="fas fa-tag"></i>
                                <?= htmlspecialchars($ritual['category']) ?>
                            </span>
                        <?php endif; ?>
                        <span class="meta-item">
                            <i class="fas fa-clock"></i>
                            <?= $ritual['duration_minutes'] ?> min
                        </span>
                        <span class="meta-item">
                            <i class="fas fa-signal"></i>
                            <?= ucfirst($ritual['difficulty']) ?>
                        </span>
                        <?php if ($ritual['community_name']): ?>
                            <span class="meta-item">
                                <i class="fas fa-users"></i>
                                <?= htmlspecialchars($ritual['community_name']) ?>
                            </span>
                        <?php endif; ?>
                    </div>
                    <p class="ritual-description">
                        <?= htmlspecialchars(substr($ritual['description'] ?? 'Your personalized ritual.', 0, 120)) ?>...
                    </p>
                    <div class="ritual-actions">
                        <a
                            href="/user/my-rituals/<?= $ritual['id'] ?>"
                            class="btn btn-view-details"
                            title="View Details"
                        >
                            <i class="fas fa-eye"></i>
                        </a>
                        <a
                            href="/user/my-rituals/<?= $ritual['id'] ?>/start"
                            class="btn btn-start"
                        >
                            <i class="fas fa-play"></i> Start
                        </a>
                        <a
                            href="/user/my-rituals/<?= $ritual['id'] ?>/pdf"
                            class="btn btn-view-details" 
                            target="_blank"
                            title="Download PDF"
                            style="background: #E0E7FF; color: #4338CA;"
                        >
                            <i class="fas fa-file-pdf"></i>
                        </a>
                        <form action="/user/my-rituals/<?= $ritual['id'] ?>/delete" method="POST" onsubmit="return confirm('Are you sure you want to remove this ritual from your collection?');" style="display: contents;">
                            <?= \App\Core\Auth::csrfField() ?>
                            <button type="submit" class="btn btn-delete" title="Delete Ritual">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php if (!empty($history)): ?>
    <div class="history-section">
        <h3><i class="fas fa-history"></i> Recent Activity</h3>
        <div class="history-list">
            <?php foreach ($history as $item): ?>
                <div class="history-item">
                    <div class="history-info">
                        <h5>
                            <?= htmlspecialchars($item['ritual_name']) ?>
                        </h5>
                        <p>
                            <?php if ($item['status'] === 'completed'): ?>
                                Completed on
                                <?= date('M d, Y', strtotime($item['completed_at'])) ?>
                            <?php elseif ($item['status'] === 'in_progress'): ?>
                                Started on
                                <?= date('M d, Y', strtotime($item['started_at'])) ?>
                            <?php else: ?>
                                Added on
                                <?= date('M d, Y', strtotime($item['created_at'])) ?>
                            <?php endif; ?>
                        </p>
                    </div>
                    <span class="history-status status-<?= $item['status'] ?>">
                        <?= ucfirst(str_replace('_', ' ', $item['status'])) ?>
                    </span>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>