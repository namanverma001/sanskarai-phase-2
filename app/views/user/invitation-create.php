<?php
/**
 * Create Invitation Page — Custom Template Selection
 */
?>

<div class="card">
    <div class="card-header">
        <h2 class="card-title"><i class="fas fa-envelope-open-text"></i> Create Invitation Card</h2>
        <a href="/user/invitations" class="btn btn-sm" style="background: #E5E7EB; color: #374151;">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>

    <p style="color: #6B7280; margin-bottom: 25px; line-height: 1.6;">
        Fill in the details below, pick a beautiful theme, and your invitation will be ready to share instantly!
    </p>

    <form method="POST" action="/user/invitations" id="invitationForm">
        <?= \App\Core\Auth::csrfField() ?>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <!-- Occasion Type -->
            <div class="form-group">
                <label for="occasion_type"><i class="fas fa-star" style="color: var(--primary);"></i> Occasion Type <span style="color: red;">*</span></label>
                <select name="occasion_type" id="occasion_type" class="form-control" required>
                    <option value="">Select Occasion</option>
                    
                    <?php if (!empty($userRituals)): ?>
                        <optgroup label="🙏 My Rituals">
                            <?php foreach ($userRituals as $ritual): ?>
                                <option value="<?= htmlspecialchars($ritual['name']) ?>">
                                    🕉️ <?= htmlspecialchars($ritual['name']) ?>
                                    <?= !empty($ritual['category']) ? ' (' . htmlspecialchars($ritual['category']) . ')' : '' ?>
                                </option>
                            <?php endforeach; ?>
                        </optgroup>
                    <?php endif; ?>

                    <optgroup label="🎉 Common Occasions">
                        <option value="Wedding">💒 Wedding</option>
                        <option value="Birthday">🎂 Birthday</option>
                        <option value="Housewarming">🏠 Housewarming (Griha Pravesh)</option>
                        <option value="Engagement">💍 Engagement</option>
                        <option value="Anniversary">🎊 Anniversary</option>
                        <option value="Baby Shower">👶 Baby Shower</option>
                        <option value="Festival">🪔 Festival Celebration</option>
                        <option value="Puja">🙏 Puja / Religious Ceremony</option>
                        <option value="Naming Ceremony">📝 Naming Ceremony</option>
                        <option value="Graduation">🎓 Graduation</option>
                        <option value="Retirement">🏖️ Retirement</option>
                        <option value="Corporate Event">🏢 Corporate Event</option>
                        <option value="Other">✨ Other</option>
                    </optgroup>
                </select>
            </div>

            <!-- Occasion Title -->
            <div class="form-group">
                <label for="occasion_title"><i class="fas fa-heading" style="color: var(--primary);"></i> Occasion Title <span style="color: red;">*</span></label>
                <input type="text" name="occasion_title" id="occasion_title" class="form-control" 
                       placeholder="e.g., Sharma Family Wedding Celebration" required>
            </div>

            <!-- Event Date -->
            <div class="form-group">
                <label for="event_date"><i class="fas fa-calendar-alt" style="color: var(--primary);"></i> Event Date & Time</label>
                <input type="datetime-local" name="event_date" id="event_date" class="form-control">
            </div>

            <!-- Venue -->
            <div class="form-group">
                <label for="venue"><i class="fas fa-map-marker-alt" style="color: var(--primary);"></i> Venue / Location</label>
                <input type="text" name="venue" id="venue" class="form-control" 
                       placeholder="e.g., Grand Ballroom, Hotel Taj Palace, New Delhi">
            </div>

            <!-- Google Maps Link -->
            <div class="form-group">
                <label for="google_maps_link"><i class="fas fa-map" style="color: var(--primary);"></i> Google Maps Link (Optional)</label>
                <input type="url" name="google_maps_link" id="google_maps_link" class="form-control" 
                       placeholder="e.g., https://maps.app.goo.gl/...">
            </div>

            <!-- Host Name -->
            <div class="form-group">
                <label for="host_name"><i class="fas fa-user" style="color: var(--primary);"></i> Host Name <span style="color: red;">*</span></label>
                <input type="text" name="host_name" id="host_name" class="form-control" 
                       value="<?= htmlspecialchars($user['name'] ?? '') ?>"
                       placeholder="Your name or family name" required>
            </div>

            <!-- Expiry Duration -->
            <div class="form-group">
                <label for="expiry_duration"><i class="fas fa-hourglass-half" style="color: var(--primary);"></i> Link Active For <span style="color: red;">*</span></label>
                <select name="expiry_duration" id="expiry_duration" class="form-control" required>
                    <option value="1">1 Day</option>
                    <option value="3">3 Days</option>
                    <option value="7" selected>7 Days</option>
                    <option value="15">15 Days</option>
                    <option value="30">30 Days</option>
                    <option value="90">90 Days</option>
                </select>
            </div>

            <!-- RSVP Toggle -->
            <div class="form-group" style="display:flex; align-items:center; gap:12px; padding-top:28px;">
                <label class="rsvp-toggle" style="cursor:pointer; display:flex; align-items:center; gap:10px; margin:0;">
                    <input type="checkbox" name="rsvp_enabled" value="1" checked id="rsvpToggle"
                           style="width:20px; height:20px; accent-color: var(--primary); cursor:pointer;">
                    <span style="font-weight:500;"><i class="fas fa-clipboard-check" style="color: var(--primary);"></i> Enable RSVP</span>
                </label>
                <span style="color:#9CA3AF; font-size:0.85rem;">Guests can confirm attendance</span>
            </div>
        </div>

        <!-- Personal Message -->
        <div class="form-group">
            <label for="message"><i class="fas fa-heart" style="color: var(--primary);"></i> Personal Message</label>
            <textarea name="message" id="message" class="form-control" rows="3" 
                      placeholder="Write a personal message for your guests... (e.g., We would be honored to have you join us for our special day!)"></textarea>
        </div>

        <!-- Additional Details -->
        <div class="form-group">
            <label for="additional_details"><i class="fas fa-info-circle" style="color: var(--primary);"></i> Additional Details</label>
            <textarea name="additional_details" id="additional_details" class="form-control" rows="3" 
                      placeholder="Any additional details like dress code, parking instructions, etc."></textarea>
        </div>

        <!-- Template Selection -->
        <div class="form-group">
            <label style="margin-bottom:10px; display:block;"><i class="fas fa-palette" style="color: var(--primary);"></i> Choose Template Design <span style="color: red;">*</span></label>
            <p style="font-size:0.82rem;color:#9CA3AF;margin-bottom:14px;margin-top:0;">
                <i class="fas fa-eye" style="margin-right:4px;"></i> Click the <strong style="color:#6B7280;">eye icon</strong> on a card to preview the full design before selecting.
            </p>
            <div class="template-grid">
                <?php foreach ($templates as $tid => $tpl): ?>
                    <label class="template-option <?= $tid === 'royal_gold' ? 'selected' : '' ?>" for="tpl_<?= $tid ?>">
                        <input type="radio" name="template_id" id="tpl_<?= $tid ?>" value="<?= $tid ?>"
                               <?= $tid === 'royal_gold' ? 'checked' : '' ?>>
                        <div class="template-preview" style="background: <?= $tpl['bg'] ?>; border: 2px solid <?= $tpl['accent'] ?>33;">
                            <span class="template-icon"><?= $tpl['icon'] ?></span>
                            <span class="template-name" style="color: <?= $tpl['accent'] ?>;"><?= $tpl['name'] ?></span>
                            <div class="template-accent-bar" style="background: <?= $tpl['accent'] ?>;"></div>
                        </div>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>

        <div style="display: flex; gap: 15px; align-items: center; margin-top: 20px;">
            <button type="submit" class="btn btn-primary" id="submitBtn" style="padding: 14px 32px; font-size: 1rem;">
                <i class="fas fa-paper-plane"></i> Create Invitation
            </button>
        </div>
    </form>
</div>

<style>
    .form-control:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(255, 107, 53, 0.1);
    }
    select.form-control {
        appearance: none; -webkit-appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%236B7280' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
        background-repeat: no-repeat; background-position: right 16px center; padding-right: 40px;
    }
    .template-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 16px; }
    .template-option { cursor: pointer; position: relative; }
    .template-option input[type="radio"] { display: none; }
    .template-preview { border-radius: 14px; padding: 22px 14px; text-align: center; transition: all 0.3s ease; position: relative; overflow: hidden; }
    .template-option:hover .template-preview { transform: translateY(-3px); box-shadow: 0 8px 25px rgba(0,0,0,0.15); }
    .template-option.selected .template-preview,
    .template-option input:checked + .template-preview { box-shadow: 0 0 0 3px var(--primary), 0 8px 25px rgba(255,107,53,0.2); transform: translateY(-3px); }
    .template-icon { font-size: 2rem; display: block; margin-bottom: 8px; }
    .template-name  { font-size: 0.85rem; font-weight: 600; display: block; }
    .template-accent-bar { height: 3px; border-radius: 2px; margin-top: 12px; opacity: 0.8; }

    /* Eye preview button */
    .tpl-eye-btn {
        position: absolute; top: 8px; right: 8px;
        width: 30px; height: 30px; border-radius: 50%;
        background: rgba(0,0,0,0.55);
        border: 1px solid rgba(255,255,255,0.25);
        color: #fff; font-size: 0.78rem;
        cursor: pointer; display: flex; align-items: center; justify-content: center;
        z-index: 10; transition: all 0.2s ease; backdrop-filter: blur(4px);
    }
    .tpl-eye-btn:hover { background: rgba(0,0,0,0.80); transform: scale(1.18); box-shadow: 0 4px 14px rgba(0,0,0,0.45); }

    /* Preview modal */
    #tplPreviewModal {
        display: none; position: fixed; inset: 0; z-index: 9999;
        background: rgba(0,0,0,0.82); backdrop-filter: blur(8px);
        align-items: flex-start; justify-content: center;
        padding: 24px 16px 48px; overflow-y: auto;
    }
    #tplPreviewModal.open { display: flex; }
    .tpm-close-btn {
        position: fixed; top: 16px; right: 20px;
        width: 42px; height: 42px; border-radius: 50%;
        background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.3);
        color: #fff; font-size: 1.1rem; cursor: pointer;
        display: flex; align-items: center; justify-content: center;
        z-index: 10001; transition: all 0.2s; backdrop-filter: blur(8px);
    }
    .tpm-close-btn:hover { background: rgba(255,255,255,0.3); transform: scale(1.1); }
    .tpm-shell {
        width: 100%; max-width: 600px; margin: auto;
        border-radius: 24px; overflow: hidden;
        animation: tpmSlide 0.3s cubic-bezier(.22,.68,0,1.2);
    }
    @keyframes tpmSlide { from { transform: translateY(50px) scale(0.95); opacity: 0; } to { transform: translateY(0) scale(1); opacity: 1; } }

    /* Invitation preview card styles (mirrors invitation-view.php) */
    .inv-prev-wrap   { padding: 28px 18px 38px; font-family: 'Poppins', sans-serif; }
    .inv-prev-label  { text-align: center; margin-bottom: 12px; }
    .inv-prev-card   { border-radius: 20px; overflow: hidden; position: relative; box-shadow: 0 20px 50px rgba(0,0,0,0.35); }
    .inv-prev-topbar { height: 5px; }
    .inv-prev-header { text-align: center; padding: 34px 24px 14px; }
    .inv-prev-ornament  { font-size: 1.5rem; opacity: 0.7; display: block; margin-bottom: 4px; }
    .inv-prev-subtitle  { font-size: 0.7rem; letter-spacing: 3.5px; text-transform: uppercase; font-weight: 600; margin-bottom: 5px; }
    .inv-prev-title     { font-family: 'Playfair Display', serif; font-size: 1.65rem; font-weight: 700; line-height: 1.25; margin-bottom: 4px; }
    .inv-prev-divider   { display: flex; align-items: center; justify-content: center; gap: 10px; padding: 7px 24px; }
    .inv-prev-divider .dl { flex: 1; height: 1px; }
    .inv-prev-divider .dd { font-size: 0.6rem; }
    .inv-prev-info      { display: grid; grid-template-columns: 1fr 1fr; padding: 6px 0; }
    .inv-prev-info-item { text-align: center; padding: 12px 14px; }
    .inv-prev-icon      { font-size: 1.2rem; display: block; margin-bottom: 4px; }
    .inv-prev-ilabel    { font-size: 0.63rem; text-transform: uppercase; letter-spacing: 2px; font-weight: 600; margin-bottom: 2px; }
    .inv-prev-ivalue    { font-family: 'Playfair Display', serif; font-size: 0.88rem; font-weight: 600; line-height: 1.35; }
    .inv-prev-msg       { padding: 16px 26px; text-align: center; }
    .inv-prev-msg blockquote { font-family: 'Playfair Display', serif; font-style: italic; font-size: 0.93rem; line-height: 1.7; padding: 0 14px; }
    .inv-prev-host      { text-align: center; padding: 12px 24px 8px; }
    .inv-prev-hosted-label { font-size: 0.7rem; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 3px; }
    .inv-prev-host-name    { font-family: 'Playfair Display', serif; font-size: 1.12rem; font-weight: 700; }
    .inv-prev-footer    { text-align: center; padding: 12px 24px 22px; font-size: 0.71rem; opacity: 0.5; }

    @media (max-width: 768px) {
        div[style*="grid-template-columns: 1fr 1fr"] { display: flex !important; flex-direction: column; }
        .template-grid { grid-template-columns: repeat(2, 1fr); }
    }
</style>

<!-- Template Preview Modal -->
<div id="tplPreviewModal" role="dialog" aria-modal="true" aria-label="Template Preview">
    <button class="tpm-close-btn" id="tpmCloseBtn" title="Close preview"><i class="fas fa-times"></i></button>
    <div class="tpm-shell" id="tpmShell"></div>
</div>

<script>
    const TEMPLATES_DATA = <?= json_encode($templates) ?>;

    // Template radio selection feedback
    document.querySelectorAll('.template-option input[type="radio"]').forEach(radio => {
        radio.addEventListener('change', () => {
            document.querySelectorAll('.template-option').forEach(opt => opt.classList.remove('selected'));
            radio.closest('.template-option').classList.add('selected');
        });
    });

    // Form submit spinner
    document.getElementById('invitationForm').addEventListener('submit', function(e) {
        const btn = document.getElementById('submitBtn');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating...';
        btn.style.opacity = '0.7';
    });

    // --- Preview modal ---
    const modal    = document.getElementById('tplPreviewModal');
    const shell    = document.getElementById('tpmShell');
    const closeBtn = document.getElementById('tpmCloseBtn');

    function buildPreview(tid) {
        const t = TEMPLATES_DATA[tid];
        if (!t) return '';
        const light      = tid === 'classic_white';
        const textColor  = light ? '#1E293B'              : '#ffffff';
        const subColor   = light ? '#64748B'              : 'rgba(255,255,255,0.65)';
        const cardBg     = light ? 'rgba(255,255,255,0.95)'  : 'rgba(255,255,255,0.06)';
        const cardBorder = light ? 'rgba(0,0,0,0.08)'     : 'rgba(255,255,255,0.1)';
        const af         = t.accent + '55';
        const div  = `<div class="inv-prev-divider"><div class="dl" style="background:linear-gradient(90deg,transparent,${af},transparent);"></div><div class="dd" style="color:${t.accent};">◆</div><div class="dl" style="background:linear-gradient(90deg,transparent,${af},transparent);"></div></div>`;

        return `
<div class="inv-prev-wrap" style="background:${t.bg};color:${textColor};">
  <div class="inv-prev-label">
    <span style="font-size:0.78rem;color:${subColor};letter-spacing:2px;text-transform:uppercase;">${t.icon} ${t.name} — Sample Preview</span>
  </div>
  <div class="inv-prev-card" style="background:${cardBg};border:1px solid ${cardBorder};backdrop-filter:blur(20px);">
    <div class="inv-prev-topbar" style="background:linear-gradient(90deg,${t.accent}00,${t.accent},${t.accent}00);"></div>
    <div class="inv-prev-header">
      <span class="inv-prev-ornament">✦</span>
      <div class="inv-prev-subtitle" style="color:${t.accent};">You're Invited</div>
      <div class="inv-prev-title">Your Special Celebration</div>
      <div style="font-size:0.88rem;color:${subColor};">Wedding · Family Occasion</div>
    </div>
    ${div}
    <div class="inv-prev-info">
      <div class="inv-prev-info-item" style="border-right:1px solid ${cardBorder};">
        <span class="inv-prev-icon">📅</span>
        <div class="inv-prev-ilabel" style="color:${t.accent};">Date &amp; Time</div>
        <div class="inv-prev-ivalue">March 25, 2026<br><span style="font-size:0.78rem;opacity:0.8;">7:00 PM</span></div>
      </div>
      <div class="inv-prev-info-item">
        <span class="inv-prev-icon">📍</span>
        <div class="inv-prev-ilabel" style="color:${t.accent};">Venue</div>
        <div class="inv-prev-ivalue">Grand Ballroom<br><span style="font-size:0.78rem;opacity:0.8;">Hotel Taj Palace</span></div>
      </div>
    </div>
    ${div}
    <div class="inv-prev-msg"><blockquote style="color:${subColor};">We would be deeply honoured to have you join us on our most special day. Your presence will fill our hearts with joy and blessings.</blockquote></div>
    ${div}
    <div class="inv-prev-host">
      <div class="inv-prev-hosted-label" style="color:${subColor};">Hosted By</div>
      <div class="inv-prev-host-name" style="color:${t.accent};">The Sharma Family</div>
    </div>
    <div style="text-align:center;padding:10px 24px 4px;">
      <span style="display:inline-block;padding:7px 20px;border-radius:50px;font-size:0.75rem;font-weight:600;background:${t.accent}22;color:${t.accent};border:1px solid ${t.accent}44;">${t.icon} ${t.name} Theme</span>
    </div>
    <div class="inv-prev-footer" style="color:${subColor};">Powered by <span style="color:${t.accent};">Sanskar AI</span></div>
  </div>
</div>`;
    }

    function openPreview(tid) {
        shell.innerHTML = buildPreview(tid);
        modal.classList.add('open');
        document.body.style.overflow = 'hidden';
    }
    function closePreview() {
        modal.classList.remove('open');
        document.body.style.overflow = '';
        shell.innerHTML = '';
    }

    closeBtn.addEventListener('click', closePreview);
    modal.addEventListener('click', e => { if (e.target === modal) closePreview(); });
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closePreview(); });

    // Inject eye buttons into each template card
    document.querySelectorAll('.template-option').forEach(option => {
        const input = option.querySelector('input[type="radio"]');
        if (!input) return;
        const tid     = input.value;
        const preview = option.querySelector('.template-preview');
        if (!preview) return;

        const eye = document.createElement('button');
        eye.type      = 'button';
        eye.className = 'tpl-eye-btn';
        eye.title     = 'Preview ' + (TEMPLATES_DATA[tid] ? TEMPLATES_DATA[tid].name : '') + ' template';
        eye.innerHTML = '<i class="fas fa-eye"></i>';
        eye.addEventListener('click', e => {
            e.preventDefault();
            e.stopPropagation();
            openPreview(tid);
        });
        preview.appendChild(eye);
    });
</script>

