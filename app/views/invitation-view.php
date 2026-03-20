<?php
/**
 * Public Invitation View Page — Custom Template with RSVP
 * =========================================================
 * Standalone page (no layout) — shown to guests when they open the shared link.
 * Beautiful responsive design with RSVP form.
 */

$accent = is_array($templateConfig) ? ($templateConfig['accent'] ?? '#B8860B') : '#B8860B';
$bg = is_array($templateConfig) ? ($templateConfig['bg'] ?? 'linear-gradient(135deg,#1a1a2e,#16213e,#0f3460)') : 'linear-gradient(135deg,#1a1a2e,#16213e,#0f3460)';
$isLight = (is_array($templateConfig) && is_array($invitation) && ($invitation['template_id'] ?? '') === 'classic_white');
$textColor = $isLight ? '#1E293B' : '#ffffff';
$subtextColor = $isLight ? '#64748B' : 'rgba(255,255,255,0.65)';
$cardBg = $isLight ? 'rgba(255,255,255,0.95)' : 'rgba(255,255,255,0.06)';
$cardBorder = $isLight ? 'rgba(0,0,0,0.08)' : 'rgba(255,255,255,0.1)';
$inputBg = $isLight ? 'rgba(0,0,0,0.04)' : 'rgba(255,255,255,0.08)';
$inputBorder = $isLight ? 'rgba(0,0,0,0.15)' : 'rgba(255,255,255,0.2)';
$inputText = $isLight ? '#1E293B' : '#ffffff';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $expired ? 'Invitation Expired' : htmlspecialchars($invitation['occasion_title'] ?? 'You are Invited!') ?> - Sanskar AI</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700;800&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/svg+xml"
        href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 64 64'><circle cx='32' cy='32' r='32' fill='%23FF6B35'/><text x='32' y='46' text-anchor='middle' font-size='40' font-family='serif' fill='white'>ॐ</text></svg>">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            background: <?= $bg ?>;
            color: <?= $textColor ?>;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        /* --- Expired State --- */
        .expired-container {
            text-align: center;
            padding: 80px 30px;
            animation: fadeIn 0.8s ease;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        .expired-icon { font-size: 5rem; margin-bottom: 20px; opacity: 0.6; }
        .expired-container h1 { font-size: 2rem; margin-bottom: 12px; }
        .expired-container p { color: <?= $subtextColor ?>; font-size: 1.1rem; max-width: 400px; line-height: 1.7; }
        .expired-container a {
            display: inline-block; margin-top: 25px; padding: 12px 28px;
            background: linear-gradient(135deg, <?= $accent ?>, <?= $accent ?>cc);
            color: white; text-decoration: none; border-radius: 50px; font-weight: 600;
            transition: all 0.3s;
        }
        .expired-container a:hover { transform: translateY(-3px); box-shadow: 0 10px 30px <?= $accent ?>66; }

        /* --- Main Invitation --- */
        .inv-wrapper {
            width: 100%; max-width: 680px; padding: 30px 16px 60px;
            animation: fadeIn 0.8s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(25px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Decorative top border */
        .inv-card {
            background: <?= $cardBg ?>;
            backdrop-filter: blur(20px);
            border: 1px solid <?= $cardBorder ?>;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 25px 60px rgba(0,0,0,0.25);
            position: relative;
        }

        .inv-card::before {
            content: '';
            position: absolute; top: 0; left: 0; right: 0; height: 5px;
            background: linear-gradient(90deg, <?= $accent ?>00, <?= $accent ?>, <?= $accent ?>00);
        }

        /* Header section */
        .inv-header {
            text-align: center;
            padding: 50px 30px 20px;
            position: relative;
        }

        .inv-header .ornament {
            font-size: 2.2rem;
            display: block;
            margin-bottom: 8px;
            opacity: 0.7;
        }

        .inv-header .subtitle {
            font-family: 'Poppins', sans-serif;
            font-size: 0.85rem;
            letter-spacing: 4px;
            text-transform: uppercase;
            color: <?= $accent ?>;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .inv-header h1 {
            font-family: 'Playfair Display', serif;
            font-size: 2.2rem;
            font-weight: 700;
            line-height: 1.25;
            margin-bottom: 6px;
        }

        .inv-header .occasion-type {
            color: <?= $subtextColor ?>;
            font-size: 0.95rem;
        }

        /* Divider */
        .inv-divider {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 14px;
            padding: 10px 30px;
        }
        .inv-divider .line {
            flex: 1;
            height: 1px;
            background: linear-gradient(90deg, transparent, <?= $accent ?>55, transparent);
        }
        .inv-divider .diamond {
            color: <?= $accent ?>;
            font-size: 0.7rem;
        }

        /* Info grid */
        .inv-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0;
            padding: 10px 0;
        }

        .inv-info-item {
            text-align: center;
            padding: 20px 20px;
            position: relative;
        }

        .inv-info-item:nth-child(odd):not(:last-child)::after {
            content: '';
            position: absolute; right: 0; top: 20%; height: 60%;
            width: 1px;
            background: <?= $cardBorder ?>;
        }

        .inv-info-item .info-icon {
            font-size: 1.4rem;
            margin-bottom: 6px;
            display: block;
        }

        .inv-info-item .info-label {
            color: <?= $accent ?>;
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-weight: 600;
            margin-bottom: 4px;
        }

        .inv-info-item .info-value {
            font-family: 'Playfair Display', serif;
            font-size: 1rem;
            font-weight: 600;
            line-height: 1.4;
            word-break: break-word;
        }

        /* Message */
        .inv-message {
            padding: 25px 35px;
            text-align: center;
        }

        .inv-message blockquote {
            font-family: 'Playfair Display', serif;
            font-size: 1.1rem;
            font-style: italic;
            line-height: 1.7;
            color: <?= $subtextColor ?>;
            position: relative;
            padding: 0 20px;
        }

        .inv-message blockquote::before,
        .inv-message blockquote::after {
            content: '"';
            font-size: 2.5rem;
            color: <?= $accent ?>44;
            font-family: 'Playfair Display', serif;
            line-height: 1;
            position: absolute;
        }
        .inv-message blockquote::before { top: -10px; left: -5px; }
        .inv-message blockquote::after { content: '"'; bottom: -25px; right: -5px; }

        /* Additional details */
        .inv-details {
            padding: 0 35px 25px;
            text-align: center;
            color: <?= $subtextColor ?>;
            font-size: 0.9rem;
            line-height: 1.7;
        }

        /* Host */
        .inv-host {
            text-align: center;
            padding: 20px 30px 10px;
        }
        .inv-host .hosted-label {
            font-size: 0.8rem;
            color: <?= $subtextColor ?>;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 4px;
        }
        .inv-host .host-name {
            font-family: 'Playfair Display', serif;
            font-size: 1.3rem;
            font-weight: 700;
            color: <?= $accent ?>;
        }

        /* Google Maps button */
        .inv-map-btn {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 10px 22px; border-radius: 50px;
            background: <?= $accent ?>22;
            color: <?= $accent ?>; font-size: 0.85rem; font-weight: 600;
            text-decoration: none;
            transition: all 0.3s;
            margin-top: 10px;
        }
        .inv-map-btn:hover {
            background: <?= $accent ?>44;
            transform: translateY(-2px);
        }

        /* ---- RSVP Section ---- */
        .rsvp-section {
            padding: 30px 30px 35px;
            border-top: 1px solid <?= $cardBorder ?>;
            text-align: center;
            position: relative;
        }

        .rsvp-section h3 {
            font-family: 'Playfair Display', serif;
            font-size: 1.4rem;
            margin-bottom: 5px;
        }

        .rsvp-section .rsvp-subtitle {
            color: <?= $subtextColor ?>;
            font-size: 0.85rem;
            margin-bottom: 22px;
        }

        .rsvp-form { max-width: 400px; margin: 0 auto; }

        .rsvp-input {
            width: 100%;
            padding: 14px 18px;
            border: 1.5px solid <?= $inputBorder ?>;
            border-radius: 12px;
            background: <?= $inputBg ?>;
            color: <?= $inputText ?>;
            font-size: 1rem;
            font-family: 'Poppins', sans-serif;
            outline: none;
            transition: all 0.3s;
            margin-bottom: 14px;
            text-align: center;
        }
        .rsvp-input::placeholder { color: <?= $subtextColor ?>; }
        .rsvp-input:focus { border-color: <?= $accent ?>; box-shadow: 0 0 0 3px <?= $accent ?>22; }

        .rsvp-status-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 10px;
            margin-bottom: 14px;
        }

        .rsvp-status-option {
            position: relative;
        }

        .rsvp-status-option input { display: none; }

        .rsvp-status-label {
            display: block;
            padding: 12px 8px;
            border-radius: 12px;
            border: 1.5px solid <?= $inputBorder ?>;
            background: <?= $inputBg ?>;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 0.82rem;
            font-weight: 500;
        }
        .rsvp-status-label .emoji { font-size: 1.3rem; display: block; margin-bottom: 3px; }

        .rsvp-status-option input:checked + .rsvp-status-label {
            border-color: <?= $accent ?>;
            background: <?= $accent ?>22;
            box-shadow: 0 0 0 2px <?= $accent ?>33;
        }

        .rsvp-guest-row {
            display: flex;
            gap: 10px;
            margin-bottom: 14px;
        }
        .rsvp-guest-row .rsvp-input { flex: 1; margin-bottom: 0; }
        .rsvp-guest-row .guest-count-input { width: 100px; flex: none; }

        .rsvp-submit-btn {
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 12px;
            background: linear-gradient(135deg, <?= $accent ?>, <?= $accent ?>cc);
            color: white;
            font-size: 1rem;
            font-weight: 600;
            font-family: 'Poppins', sans-serif;
            cursor: pointer;
            transition: all 0.3s;
            letter-spacing: 0.5px;
        }
        .rsvp-submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px <?= $accent ?>55;
        }
        .rsvp-submit-btn:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }

        .rsvp-success, .rsvp-error {
            padding: 16px 20px;
            border-radius: 12px;
            margin-top: 16px;
            font-size: 0.95rem;
            display: none;
        }
        .rsvp-success { background: #05966922; color: #059669; border: 1px solid #05966944; }
        .rsvp-error { background: #EF444422; color: #EF4444; border: 1px solid #EF444444; }

        /* Footer */
        .inv-footer {
            text-align: center;
            padding: 20px 30px 30px;
            color: <?= $subtextColor ?>;
            font-size: 0.75rem;
        }
        .inv-footer a { color: <?= $accent ?>88; text-decoration: none; }

        /* Responsive */
        @media (max-width: 500px) {
            .inv-header h1 { font-size: 1.6rem; }
            .inv-info { grid-template-columns: 1fr; }
            .inv-info-item:nth-child(odd)::after { display: none; }
            .inv-info-item { padding: 14px 20px; }
            .rsvp-status-grid { grid-template-columns: 1fr; }
            .inv-message { padding: 20px 25px; }
            .inv-header { padding: 40px 20px 15px; }
        }
    </style>
</head>
<body>

<?php if ($expired): ?>
    <!-- Expired State -->
    <div class="expired-container">
        <div class="expired-icon">⏳</div>
        <h1>Invitation Expired</h1>
        <p>This invitation link is no longer active. The host may have set a time limit or removed it.</p>
        <a href="/">Visit Sanskar AI</a>
    </div>

<?php else: ?>
    <!-- Invitation Card -->
    <div class="inv-wrapper">
        <div class="inv-card">

            <!-- Header -->
            <div class="inv-header">
                <span class="ornament">✦</span>
                <div class="subtitle">You're Invited</div>
                <h1><?= htmlspecialchars($invitation['occasion_title']) ?></h1>
                <div class="occasion-type"><?= htmlspecialchars($invitation['occasion_type']) ?></div>
            </div>

            <!-- Divider -->
            <div class="inv-divider">
                <div class="line"></div>
                <div class="diamond">◆</div>
                <div class="line"></div>
            </div>

            <!-- Info Grid -->
            <div class="inv-info">
                <?php if (!empty($invitation['event_date'])): ?>
                <div class="inv-info-item">
                    <span class="info-icon">📅</span>
                    <div class="info-label">Date & Time</div>
                    <div class="info-value">
                        <?= date('F j, Y', strtotime($invitation['event_date'])) ?>
                        <br>
                        <span style="font-size:0.85rem; opacity:0.8;">
                            <?= date('g:i A', strtotime($invitation['event_date'])) ?>
                        </span>
                    </div>
                </div>
                <?php endif; ?>

                <?php if (!empty($invitation['venue'])): ?>
                <div class="inv-info-item">
                    <span class="info-icon">📍</span>
                    <div class="info-label">Venue</div>
                    <div class="info-value">
                        <?= htmlspecialchars($invitation['venue']) ?>
                        <?php if (!empty($invitation['google_maps_link'])): ?>
                            <br>
                            <a href="<?= htmlspecialchars($invitation['google_maps_link']) ?>" target="_blank" class="inv-map-btn">
                                📍 View on Maps
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Personal Message -->
            <?php if (!empty($invitation['message'])): ?>
                <div class="inv-divider">
                    <div class="line"></div>
                    <div class="diamond">◆</div>
                    <div class="line"></div>
                </div>
                <div class="inv-message">
                    <blockquote>
                        <?= nl2br(htmlspecialchars($invitation['message'])) ?>
                    </blockquote>
                </div>
            <?php endif; ?>

            <!-- Additional Details -->
            <?php if (!empty($invitation['additional_details'])): ?>
                <div class="inv-details">
                    <?= nl2br(htmlspecialchars($invitation['additional_details'])) ?>
                </div>
            <?php endif; ?>

            <!-- Host -->
            <div class="inv-divider">
                <div class="line"></div>
                <div class="diamond">◆</div>
                <div class="line"></div>
            </div>
            <div class="inv-host">
                <div class="hosted-label">Hosted By</div>
                <div class="host-name"><?= htmlspecialchars($invitation['host_name']) ?></div>
            </div>

            <!-- RSVP Section -->
            <?php if (!empty($invitation['rsvp_enabled'])): ?>
                <div class="rsvp-section" id="rsvpSection">
                    <h3>Will You Attend?</h3>
                    <p class="rsvp-subtitle">Let us know if you can make it — we'd love to see you!</p>

                    <form class="rsvp-form" id="rsvpForm" onsubmit="submitRsvp(event)">
                        <input type="text" class="rsvp-input" name="guest_name" id="rsvpName"
                               placeholder="Your Full Name" required>

                        <div class="rsvp-status-grid">
                            <label class="rsvp-status-option">
                                <input type="radio" name="attending_status" value="yes" checked>
                                <div class="rsvp-status-label">
                                    <span class="emoji">🎉</span>
                                    I'll be there!
                                </div>
                            </label>
                            <label class="rsvp-status-option">
                                <input type="radio" name="attending_status" value="maybe">
                                <div class="rsvp-status-label">
                                    <span class="emoji">🤔</span>
                                    Maybe
                                </div>
                            </label>
                            <label class="rsvp-status-option">
                                <input type="radio" name="attending_status" value="no">
                                <div class="rsvp-status-label">
                                    <span class="emoji">😢</span>
                                    Can't make it
                                </div>
                            </label>
                        </div>

                        <div class="rsvp-guest-row">
                            <input type="text" class="rsvp-input" name="message" placeholder="Leave a message (optional)">
                            <input type="number" class="rsvp-input guest-count-input" name="guest_count" 
                                   value="1" min="1" max="50" placeholder="Guests">
                        </div>

                        <button type="submit" class="rsvp-submit-btn" id="rsvpBtn">
                            ✨ Confirm RSVP
                        </button>
                    </form>

                    <div class="rsvp-success" id="rsvpSuccess"></div>
                    <div class="rsvp-error" id="rsvpError"></div>
                </div>
            <?php endif; ?>

            <!-- Footer -->
            <div class="inv-footer">
                Powered by <a href="/">Sanskar AI</a>
            </div>
        </div>
    </div>

    <?php if (!empty($invitation['rsvp_enabled'])): ?>
    <script>
        const RSVP_URL = '/invitation/<?= htmlspecialchars($invitation['share_token']) ?>/rsvp';

        async function submitRsvp(e) {
            e.preventDefault();
            const form = document.getElementById('rsvpForm');
            const btn = document.getElementById('rsvpBtn');
            const successEl = document.getElementById('rsvpSuccess');
            const errorEl = document.getElementById('rsvpError');

            successEl.style.display = 'none';
            errorEl.style.display = 'none';

            const formData = new FormData(form);
            btn.disabled = true;
            btn.textContent = 'Submitting...';

            try {
                const res = await fetch(RSVP_URL, {
                    method: 'POST',
                    body: formData,
                });
                const data = await res.json();

                if (data.success) {
                    successEl.textContent = data.message;
                    successEl.style.display = 'block';
                    form.style.display = 'none';
                } else {
                    errorEl.textContent = data.error || 'Something went wrong.';
                    errorEl.style.display = 'block';
                    btn.disabled = false;
                    btn.textContent = '✨ Confirm RSVP';
                }
            } catch (err) {
                errorEl.textContent = 'Network error. Please try again.';
                errorEl.style.display = 'block';
                btn.disabled = false;
                btn.textContent = '✨ Confirm RSVP';
            }
        }
    </script>
    <?php endif; ?>

<?php endif; ?>
</body>
</html>
