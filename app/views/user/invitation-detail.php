<?php
/**
 * Invitation Detail Page - View & Share
 */
$appUrl = rtrim(getenv('APP_URL') ?: 'http://localhost:8000', '/');
?>

<div style="display: flex; gap: 10px; margin-bottom: 20px;">
    <a href="/user/invitations" class="btn btn-sm" style="background: #E5E7EB; color: #374151;">
        <i class="fas fa-arrow-left"></i> Back to Invitations
    </a>
</div>

<!-- Share URL Card -->
<div class="card" style="background: linear-gradient(135deg, #1A1A2E 0%, #16213E 100%); color: white; border: none;">
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 15px;">
        <div>
            <h3 style="margin-bottom: 8px; color: #FF9933;">
                <i class="fas fa-link"></i> Share This Invitation
            </h3>
            <p style="color: rgba(255,255,255,0.7); font-size: 0.9rem; margin: 0;">
                Copy the link below and share it with your guests. They'll be asked for their name to personalize the card.
            </p>
        </div>
        <?php if (!$invitation['is_expired'] && $invitation['is_active']): ?>
            <span class="badge badge-success" style="font-size: 0.85rem; padding: 6px 16px;">
                <i class="fas fa-check-circle"></i> Active
            </span>
        <?php else: ?>
            <span class="badge badge-danger" style="font-size: 0.85rem; padding: 6px 16px;">
                <i class="fas fa-times-circle"></i> Expired
            </span>
        <?php endif; ?>
    </div>

    <?php if (!$invitation['is_expired'] && $invitation['is_active']): ?>
        <div style="margin-top: 20px; display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
            <input type="text" id="shareUrl" readonly
                   value="<?= htmlspecialchars($shareUrl) ?>"
                   style="flex: 1; min-width: 200px; padding: 12px 16px; border-radius: 10px; border: 1px solid rgba(255,255,255,0.2); background: rgba(255,255,255,0.1); color: white; font-size: 0.95rem; font-family: monospace;">
            <button onclick="copyShareUrl()" id="copyBtn" 
                    class="btn btn-primary" style="white-space: nowrap;">
                <i class="fas fa-copy"></i> Copy Link
            </button>
            <a href="https://wa.me/?text=<?= urlencode('You are invited! 🎉 ' . $shareUrl) ?>" 
               target="_blank" class="btn btn-success" style="white-space: nowrap;">
                <i class="fab fa-whatsapp"></i> WhatsApp
            </a>
        </div>
    <?php endif; ?>
</div>

<!-- Invitation Details Card -->
<div class="card">
    <div class="card-header">
        <h2 class="card-title"><i class="fas fa-info-circle"></i> Invitation Details</h2>
        <form method="POST" action="/user/invitations/<?= $invitation['id'] ?>/delete" 
              onsubmit="return confirm('Delete this invitation permanently?')" style="margin: 0;">
            <?= \App\Core\Auth::csrfField() ?>
            <button type="submit" class="btn btn-danger btn-sm">
                <i class="fas fa-trash"></i> Delete
            </button>
        </form>
    </div>

    <div class="detail-grid">
        <div class="detail-item">
            <span class="detail-label">Occasion Type</span>
            <span class="detail-value"><?= htmlspecialchars($invitation['occasion_type']) ?></span>
        </div>
        <div class="detail-item">
            <span class="detail-label">Title</span>
            <span class="detail-value"><?= htmlspecialchars($invitation['occasion_title']) ?></span>
        </div>
        <div class="detail-item">
            <span class="detail-label">Host</span>
            <span class="detail-value"><?= htmlspecialchars($invitation['host_name']) ?></span>
        </div>
        <div class="detail-item">
            <span class="detail-label">Event Date</span>
            <span class="detail-value">
                <?= $invitation['event_date'] ? date('F j, Y \a\t g:i A', strtotime($invitation['event_date'])) : 'Not set' ?>
            </span>
        </div>
        <div class="detail-item">
            <span class="detail-label">Venue</span>
            <span class="detail-value"><?= htmlspecialchars($invitation['venue'] ?: 'Not set') ?></span>
        </div>
        <div class="detail-item">
            <span class="detail-label">Views</span>
            <span class="detail-value"><i class="fas fa-eye" style="color: var(--primary);"></i> <?= $invitation['view_count'] ?></span>
        </div>
        <div class="detail-item">
            <span class="detail-label">Created</span>
            <span class="detail-value"><?= date('M j, Y g:i A', strtotime($invitation['created_at'])) ?></span>
        </div>
        <div class="detail-item">
            <span class="detail-label">Expires</span>
            <span class="detail-value"><?= date('M j, Y g:i A', strtotime($invitation['expires_at'])) ?></span>
        </div>
    </div>

    <?php if (!empty($invitation['message'])): ?>
        <div style="margin-top: 20px; padding: 16px; background: #FFF7ED; border-radius: 10px; border-left: 4px solid var(--primary);">
            <strong style="color: var(--dark);"><i class="fas fa-heart" style="color: var(--primary);"></i> Personal Message:</strong>
            <p style="margin: 8px 0 0; color: #6B7280;"><?= nl2br(htmlspecialchars($invitation['message'])) ?></p>
        </div>
    <?php endif; ?>
</div>

<!-- Preview Card -->
<div class="card">
    <div class="card-header">
        <h2 class="card-title"><i class="fas fa-eye"></i> Invitation Preview</h2>
        <?php if (!$invitation['is_expired'] && $invitation['is_active']): ?>
            <a href="<?= htmlspecialchars($shareUrl) ?>" target="_blank" class="btn btn-primary btn-sm">
                <i class="fas fa-external-link-alt"></i> Open Full Page
            </a>
        <?php endif; ?>
    </div>
    <div style="border: 2px solid #E5E7EB; border-radius: 12px; overflow: hidden; background: white;">
        <iframe id="previewFrame" 
                style="width: 100%; height: 600px; border: none;"
                sandbox="allow-scripts allow-same-origin"></iframe>
    </div>
</div>

<style>
    .detail-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 16px;
    }

    .detail-item {
        padding: 12px 16px;
        background: #F9FAFB;
        border-radius: 10px;
    }

    .detail-label {
        display: block;
        font-size: 0.78rem;
        color: #9CA3AF;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 4px;
    }

    .detail-value {
        font-weight: 500;
        color: var(--dark);
    }

    @media (max-width: 768px) {
        .detail-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<script>
    // Load preview
    const previewHtml = <?= json_encode($invitation['generated_html']) ?>;
    const iframe = document.getElementById('previewFrame');
    const doc = iframe.contentDocument || iframe.contentWindow.document;
    // Replace placeholder with sample name for preview
    const previewContent = previewHtml.replace(/\{GUEST_NAME\}/g, 'Dear Guest');
    doc.open();
    doc.write(previewContent);
    doc.close();

    // Copy URL
    function copyShareUrl() {
        const urlInput = document.getElementById('shareUrl');
        urlInput.select();
        urlInput.setSelectionRange(0, 99999);
        
        navigator.clipboard.writeText(urlInput.value).then(() => {
            const btn = document.getElementById('copyBtn');
            btn.innerHTML = '<i class="fas fa-check"></i> Copied!';
            btn.style.background = '#10B981';
            setTimeout(() => {
                btn.innerHTML = '<i class="fas fa-copy"></i> Copy Link';
                btn.style.background = '';
            }, 2000);
        }).catch(() => {
            document.execCommand('copy');
            const btn = document.getElementById('copyBtn');
            btn.innerHTML = '<i class="fas fa-check"></i> Copied!';
            setTimeout(() => {
                btn.innerHTML = '<i class="fas fa-copy"></i> Copy Link';
            }, 2000);
        });
    }
</script>
