<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-moon"></i> Muhurat Requests</h3>
    </div>

    <?php if (empty($requests)): ?>
        <div style="text-align: center; padding: 50px; color: #6B7280;">
            <div style="width: 80px; height: 80px; background: rgba(139, 92, 246, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; font-size: 2rem; color: #8B5CF6;">
                <i class="fas fa-moon"></i>
            </div>
            <h4 style="margin-bottom: 10px; color: #374151;">No Muhurat Requests</h4>
            <p>When users request a muhurat, their requests will appear here.</p>
        </div>
    <?php else: ?>
        <div style="display: flex; flex-direction: column; gap: 15px;">
            <?php foreach ($requests as $req): ?>
            <div style="border: 1px solid #E5E7EB; border-radius: 12px; padding: 20px; transition: all 0.3s;
                <?= $req['status'] === 'pending' ? 'border-left: 4px solid #F59E0B;' :
                    ($req['status'] === 'replied' && (int)($req['replied_by'] ?? 0) === \App\Core\Auth::id() ? 'border-left: 4px solid #6366F1;' :
                    ($req['status'] === 'accepted' ? 'border-left: 4px solid #10B981;' :
                    'border-left: 4px solid #9CA3AF;')) ?>">

                <!-- Header -->
                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 12px; flex-wrap: wrap; gap: 10px;">
                    <div>
                        <h4 style="margin: 0 0 5px 0;">
                            <i class="fas fa-om" style="color: var(--primary);"></i>
                            <?= htmlspecialchars($req['ritual_type']) ?>
                        </h4>
                        <span style="font-size: 0.85rem; color: #6B7280;">
                            <i class="fas fa-user"></i> <?= htmlspecialchars($req['user_name']) ?>
                            <?php if (!empty($req['user_mobile'])): ?>
                                · <i class="fas fa-phone"></i> <?= htmlspecialchars($req['user_mobile']) ?>
                            <?php endif; ?>
                        </span>
                    </div>
                    <span class="badge badge-<?= $req['status'] === 'accepted' ? 'success' : ($req['status'] === 'replied' ? 'info' : ($req['status'] === 'declined' ? 'danger' : 'warning')) ?>">
                        <?php if ($req['status'] === 'replied' && (int)($req['replied_by'] ?? 0) === \App\Core\Auth::id()): ?>
                            You Replied
                        <?php else: ?>
                            <?= ucfirst($req['status']) ?>
                        <?php endif; ?>
                    </span>
                </div>

                <!-- Details Grid -->
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 12px; font-size: 0.9rem; color: #6B7280; margin-bottom: 12px;">
                    <?php if (!empty($req['city'])): ?>
                    <div><i class="fas fa-map-marker-alt" style="color: var(--primary);"></i> <?= htmlspecialchars($req['city']) ?>, <?= htmlspecialchars($req['country'] ?? 'India') ?></div>
                    <?php endif; ?>
                    <?php if (!empty($req['preferred_month'])): ?>
                    <div><i class="fas fa-calendar" style="color: var(--primary);"></i> <?= htmlspecialchars($req['preferred_month']) ?></div>
                    <?php endif; ?>
                    <?php if (!empty($req['gotra']) || !empty($req['family_gotra'])): ?>
                    <div><i class="fas fa-seedling" style="color: var(--primary);"></i> Gotra: <?= htmlspecialchars($req['gotra'] ?: $req['family_gotra'] ?? '') ?></div>
                    <?php endif; ?>
                    <?php if (!empty($req['nakshatra']) || !empty($req['family_nakshatra'])): ?>
                    <div><i class="fas fa-star" style="color: var(--primary);"></i> <?= htmlspecialchars($req['nakshatra'] ?: $req['family_nakshatra'] ?? '') ?></div>
                    <?php endif; ?>
                    <div>
                        <i class="fas fa-sun" style="color: var(--primary);"></i>
                        <?= $req['time_preference'] === 'morning' ? 'Morning' : ($req['time_preference'] === 'evening' ? 'Evening' : 'Any Time') ?>
                    </div>
                </div>

                <?php if (!empty($req['additional_notes'])): ?>
                <div style="background: #F9FAFB; padding: 12px; border-radius: 8px; margin-bottom: 12px;">
                    <small style="color: #6B7280;"><i class="fas fa-sticky-note"></i> <strong>Notes:</strong> <?= htmlspecialchars($req['additional_notes']) ?></small>
                </div>
                <?php endif; ?>

                <?php if (!empty($req['family_name'])): ?>
                <div style="margin-bottom: 12px;">
                    <small style="color: #6B7280;"><i class="fas fa-users" style="color: var(--primary);"></i> Family: <strong><?= htmlspecialchars($req['family_name']) ?></strong></small>
                </div>
                <?php endif; ?>

                <!-- Reply Button (only for pending requests) -->
                <?php if ($req['status'] === 'pending'): ?>
                <div style="margin-top: 12px; padding-top: 12px; border-top: 1px solid #E5E7EB;">
                    <button type="button" class="btn btn-sm btn-primary" onclick="showReplyModal(<?= $req['id'] ?>, '<?= htmlspecialchars(addslashes($req['ritual_type']), ENT_QUOTES) ?>')">
                        <i class="fas fa-reply"></i> Reply with Muhurat
                    </button>
                </div>
                <?php endif; ?>

                <!-- Show own reply -->
                <?php if ($req['status'] !== 'pending' && (int)($req['replied_by'] ?? 0) === \App\Core\Auth::id()): ?>
                <div style="background: rgba(99,102,241,0.06); padding: 15px; border-radius: 10px; margin-top: 12px;">
                    <h5 style="margin: 0 0 10px 0; color: #4338CA;"><i class="fas fa-check-circle"></i> Your Reply</h5>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 10px;">
                        <div style="background: white; padding: 10px; border-radius: 8px; text-align: center;">
                            <small style="color: #6B7280;">Date</small><br>
                            <strong><?= date('M d, Y', strtotime($req['reply_date'])) ?></strong>
                        </div>
                        <div style="background: white; padding: 10px; border-radius: 8px; text-align: center;">
                            <small style="color: #6B7280;">Time</small><br>
                            <strong style="color: #4338CA;"><?= date('h:i A', strtotime($req['reply_time'])) ?></strong>
                        </div>
                    </div>
                    <?php if (!empty($req['reply_explanation'])): ?>
                    <p style="margin: 0; color: #374151; font-size: 0.9rem;"><?= nl2br(htmlspecialchars($req['reply_explanation'])) ?></p>
                    <?php endif; ?>

                </div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Reply Modal -->
<div id="replyModal" class="pandit-modal" style="display: none;">
    <div class="pandit-modal-backdrop" onclick="hideReplyModal()"></div>
    <div class="pandit-modal-content" style="max-width: 520px;">
        <div class="pandit-modal-header">
            <h3><i class="fas fa-moon" style="color: #6366F1;"></i> Reply with Muhurat</h3>
            <button type="button" class="pandit-close-btn" onclick="hideReplyModal()">&times;</button>
        </div>
        <form id="replyForm" method="POST" action="">
            <?= \App\Core\Auth::csrfField() ?>
            <div class="pandit-modal-body">
                <p style="color: #6b7280; margin-bottom: 15px;">
                    Providing muhurat for: <strong id="replyRitualName"></strong>
                </p>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div class="form-group">
                        <label class="form-label"><i class="fas fa-calendar"></i> Auspicious Date <span style="color:#EF4444;">*</span></label>
                        <input type="date" name="reply_date" required class="form-control" min="<?= date('Y-m-d') ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label"><i class="fas fa-clock"></i> Muhurat Time <span style="color:#EF4444;">*</span></label>
                        <input type="time" name="reply_time" required class="form-control">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label"><i class="fas fa-book"></i> Explanation <span style="color:#EF4444;">*</span></label>
                    <textarea name="reply_explanation" rows="4" required class="form-control"
                        placeholder="Explain why this date and time is auspicious. E.g., 'This is Shubh Muhurat as per Panchang — the nakshatra alignment is favorable...'"></textarea>
                </div>

            </div>
            <div class="pandit-modal-footer">
                <button type="button" class="btn btn-secondary" onclick="hideReplyModal()" style="background: #E5E7EB; color: #374151;">Cancel</button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-paper-plane"></i> Send Muhurat Reply
                </button>
            </div>
        </form>
    </div>
</div>

<style>
.pandit-modal {
    position: fixed;
    top: 0; left: 0; right: 0; bottom: 0;
    z-index: 1050;
    display: flex;
    align-items: center;
    justify-content: center;
}

.pandit-modal-backdrop {
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(0, 0, 0, 0.5);
}

.pandit-modal-content {
    position: relative;
    background: white;
    border-radius: 12px;
    width: 100%;
    max-width: 520px;
    margin: 20px;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    animation: modalSlideIn 0.3s ease;
}

@keyframes modalSlideIn {
    from { transform: translateY(-20px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}

.pandit-modal-header {
    padding: 20px;
    border-bottom: 1px solid #e5e7eb;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.pandit-modal-header h3 {
    margin: 0;
    font-size: 1.1rem;
    display: flex;
    align-items: center;
    gap: 10px;
}

.pandit-close-btn {
    background: none;
    border: none;
    font-size: 24px;
    cursor: pointer;
    color: #6b7280;
    line-height: 1;
}

.pandit-close-btn:hover { color: #374151; }

.pandit-modal-body { padding: 20px; }

.pandit-modal-footer {
    padding: 15px 20px;
    border-top: 1px solid #e5e7eb;
    display: flex;
    justify-content: flex-end;
    gap: 10px;
}

.btn-secondary {
    background: #E5E7EB;
    color: #374151;
}
</style>

<script>
function showReplyModal(requestId, ritualName) {
    const modal = document.getElementById('replyModal');
    const form = document.getElementById('replyForm');
    const ritualLabel = document.getElementById('replyRitualName');

    form.action = '/pandit/mohurat-requests/' + requestId + '/reply';
    ritualLabel.textContent = ritualName;
    modal.style.display = 'flex';
}

function hideReplyModal() {
    document.getElementById('replyModal').style.display = 'none';
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') hideReplyModal();
});
</script>
