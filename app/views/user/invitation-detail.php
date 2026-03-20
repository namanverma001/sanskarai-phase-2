<?php
/**
 * Invitation Detail Page - View, Share & RSVP Guest List
 */
$appUrl = rtrim(getenv('APP_URL') ?: 'http://localhost:8000', '/');
$templateName = $templateConfig['name'] ?? 'Royal Gold';
$templateIcon = $templateConfig['icon'] ?? '👑';
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
                Copy the link below and share it with your guests.
                <?php if (!empty($invitation['rsvp_enabled'])): ?>
                    Guests can RSVP directly from the invitation!
                <?php endif; ?>
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
            <a href="<?= htmlspecialchars($shareUrl) ?>" target="_blank" 
               class="btn btn-sm" style="background: #6366F1; color: white; white-space: nowrap;">
                <i class="fas fa-external-link-alt"></i> Preview
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
            <span class="detail-label">Template</span>
            <span class="detail-value"><?= $templateIcon ?> <?= htmlspecialchars($templateName) ?></span>
        </div>
        <div class="detail-item">
            <span class="detail-label">Views</span>
            <span class="detail-value"><i class="fas fa-eye" style="color: var(--primary);"></i> <?= $invitation['view_count'] ?></span>
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

<!-- RSVP Guest List -->
<?php if (!empty($invitation['rsvp_enabled'])): ?>
<div class="card">
    <div class="card-header">
        <h2 class="card-title"><i class="fas fa-clipboard-check"></i> RSVP Responses</h2>
    </div>

    <!-- Summary Stats -->
    <div class="rsvp-stats-grid">
        <div class="rsvp-stat-card" style="border-left: 4px solid #10B981;">
            <div class="rsvp-stat-number" style="color: #10B981;"><?= (int)($rsvpSummary['attending'] ?? 0) ?></div>
            <div class="rsvp-stat-label">🎉 Attending</div>
        </div>
        <div class="rsvp-stat-card" style="border-left: 4px solid #F59E0B;">
            <div class="rsvp-stat-number" style="color: #F59E0B;"><?= (int)($rsvpSummary['maybe'] ?? 0) ?></div>
            <div class="rsvp-stat-label">🤔 Maybe</div>
        </div>
        <div class="rsvp-stat-card" style="border-left: 4px solid #EF4444;">
            <div class="rsvp-stat-number" style="color: #EF4444;"><?= (int)($rsvpSummary['not_attending'] ?? 0) ?></div>
            <div class="rsvp-stat-label">😢 Can't Make It</div>
        </div>
        <div class="rsvp-stat-card" style="border-left: 4px solid #6366F1;">
            <div class="rsvp-stat-number" style="color: #6366F1;"><?= (int)($rsvpSummary['total_guests'] ?? 0) ?></div>
            <div class="rsvp-stat-label">👥 Total Guests</div>
        </div>
    </div>

    <!-- Guest List Table -->
    <?php if (!empty($rsvps)): ?>
        <div style="overflow-x: auto; margin-top: 25px;">
            <table class="rsvp-table">
                <thead>
                    <tr>
                        <th style="text-align: left;">Guest Name</th>
                        <th style="text-align: left;">Status</th>
                        <th style="text-align: center;">Guests</th>
                        <th style="text-align: left;">Message</th>
                        <th style="text-align: right;">Responded At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rsvps as $rsvp): ?>
                        <tr>
                            <td style="font-weight: 600; text-align: left; color: var(--dark);">
                                <i class="fas fa-user-circle" style="color: var(--primary); margin-right: 6px;"></i>
                                <?= htmlspecialchars($rsvp['guest_name']) ?>
                            </td>
                            <td style="text-align: left;">
                                <?php
                                    $statusBadges = [
                                        'yes' => '<span class="badge badge-success" style="padding: 6px 12px;">🎉 Attending</span>',
                                        'maybe' => '<span class="badge" style="background:#FEF3C7;color:#92400E;padding: 6px 12px;">🤔 Maybe</span>',
                                        'no' => '<span class="badge badge-danger" style="padding: 6px 12px;">😢 Not attending</span>',
                                    ];
                                    echo $statusBadges[$rsvp['attending_status']] ?? $rsvp['attending_status'];
                                ?>
                            </td>
                            <td style="text-align: center; font-weight: 600;"><?= (int) $rsvp['guest_count'] ?></td>
                            <td style="color: #6B7280; max-width: 250px; text-align: left;">
                                <?= $rsvp['message'] ? htmlspecialchars($rsvp['message']) : '<span style="opacity:0.4;">—</span>' ?>
                            </td>
                            <td style="color: #9CA3AF; font-size: 0.85rem; text-align: right;">
                                <?= date('M j, Y g:i A', strtotime($rsvp['created_at'])) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div style="text-align: center; padding: 40px 20px; color: #9CA3AF;">
            <div style="font-size: 2.5rem; margin-bottom: 10px;">📭</div>
            <p style="font-size: 1rem;">No RSVP responses yet.</p>
            <p style="font-size: 0.85rem;">Share the invitation link with your guests — they can RSVP directly!</p>
        </div>
    <?php endif; ?>
</div>
<?php endif; ?>

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

    .rsvp-stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 14px;
    }

    .rsvp-stat-card {
        background: #F9FAFB;
        padding: 16px;
        border-radius: 10px;
        text-align: center;
    }

    .rsvp-stat-number {
        font-size: 2rem;
        font-weight: 700;
        line-height: 1;
        margin-bottom: 4px;
    }

    .rsvp-stat-label {
        font-size: 0.8rem;
        color: #6B7280;
        font-weight: 500;
    }

    /* New dedicated RSVP Table Styles */
    .rsvp-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 600px;
    }

    .rsvp-table th {
        padding: 12px 16px;
        background: #F9FAFB;
        color: #4B5563;
        font-weight: 700;
        font-size: 0.82rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 2px solid #E5E7EB;
    }

    .rsvp-table td {
        padding: 16px;
        border-bottom: 1px solid #E5E7EB;
        vertical-align: middle;
    }

    .rsvp-table tr:hover td {
        background: #F9FAFB;
    }

    @media (max-width: 768px) {
        .detail-grid { grid-template-columns: 1fr; }
        .rsvp-stats-grid { grid-template-columns: 1fr 1fr; }
    }
</style>

<script>
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
