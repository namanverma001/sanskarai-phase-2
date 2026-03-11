<?php
/**
 * User Invitations - List Page
 */
?>

<div class="card">
    <div class="card-header">
        <h2 class="card-title"><i class="fas fa-envelope-open-text"></i> My Invitations</h2>
        <a href="/user/invitations/create" class="btn btn-primary">
            <i class="fas fa-plus"></i> Create New Invitation
        </a>
    </div>

    <?php if (empty($invitations)): ?>
        <div style="text-align: center; padding: 60px 20px;">
            <div style="font-size: 4rem; margin-bottom: 20px; opacity: 0.3;">
                <i class="fas fa-envelope-open-text"></i>
            </div>
            <h3 style="color: #6B7280; margin-bottom: 10px;">No Invitations Yet</h3>
            <p style="color: #9CA3AF; margin-bottom: 25px;">
                Create beautiful AI-generated invitation cards for your occasions and share them with your guests!
            </p>
            <a href="/user/invitations/create" class="btn btn-primary">
                <i class="fas fa-magic"></i> Create Your First Invitation
            </a>
        </div>
    <?php else: ?>
        <div class="invitation-grid">
            <?php foreach ($invitations as $inv): ?>
                <div class="invitation-card-item <?= $inv['is_expired'] ? 'expired' : '' ?>">
                    <div class="inv-card-header">
                        <span class="inv-occasion-badge"><?= htmlspecialchars($inv['occasion_type']) ?></span>
                        <?php if ($inv['is_expired'] || !$inv['is_active']): ?>
                            <span class="badge badge-danger">Expired</span>
                        <?php else: ?>
                            <span class="badge badge-success">Active</span>
                        <?php endif; ?>
                    </div>
                    <h3 class="inv-title"><?= htmlspecialchars($inv['occasion_title']) ?></h3>
                    <div class="inv-meta">
                        <?php if ($inv['event_date']): ?>
                            <span><i class="fas fa-calendar"></i> <?= date('M j, Y g:i A', strtotime($inv['event_date'])) ?></span>
                        <?php endif; ?>
                        <?php if ($inv['venue']): ?>
                            <span><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($inv['venue']) ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="inv-stats">
                        <span><i class="fas fa-eye"></i> <?= $inv['view_count'] ?> views</span>
                        <span><i class="fas fa-clock"></i> Expires: <?= date('M j, Y', strtotime($inv['expires_at'])) ?></span>
                    </div>
                    <div class="inv-actions">
                        <a href="/user/invitations/<?= $inv['id'] ?>" class="btn btn-primary btn-sm">
                            <i class="fas fa-eye"></i> View & Share
                        </a>
                        <form method="POST" action="/user/invitations/<?= $inv['id'] ?>/delete" 
                              onsubmit="return confirm('Are you sure you want to delete this invitation?')" style="margin:0;">
                            <?= \App\Core\Auth::csrfField() ?>
                            <button type="submit" class="btn btn-danger btn-sm">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<style>
    .invitation-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
        gap: 20px;
    }

    .invitation-card-item {
        background: white;
        border: 1px solid #E5E7EB;
        border-radius: 16px;
        padding: 24px;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .invitation-card-item::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
    }

    .invitation-card-item.expired::before {
        background: linear-gradient(135deg, #9CA3AF 0%, #6B7280 100%);
    }

    .invitation-card-item:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    }

    .invitation-card-item.expired {
        opacity: 0.7;
    }

    .inv-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 12px;
    }

    .inv-occasion-badge {
        background: linear-gradient(135deg, #FF6B35 0%, #F59E0B 100%);
        color: white;
        padding: 4px 14px;
        border-radius: 20px;
        font-size: 0.78rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .inv-title {
        font-size: 1.15rem;
        font-weight: 600;
        color: var(--dark);
        margin-bottom: 10px;
    }

    .inv-meta {
        display: flex;
        flex-direction: column;
        gap: 6px;
        margin-bottom: 12px;
        color: #6B7280;
        font-size: 0.88rem;
    }

    .inv-meta i {
        width: 18px;
        color: var(--primary);
        margin-right: 6px;
    }

    .inv-stats {
        display: flex;
        gap: 16px;
        font-size: 0.82rem;
        color: #9CA3AF;
        margin-bottom: 16px;
        padding-top: 12px;
        border-top: 1px solid #F3F4F6;
    }

    .inv-stats i {
        margin-right: 4px;
    }

    .inv-actions {
        display: flex;
        gap: 10px;
        align-items: center;
    }

    @media (max-width: 768px) {
        .invitation-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
