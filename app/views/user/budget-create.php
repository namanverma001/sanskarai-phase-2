<?php
/**
 * Create Ritual Budget — AI-Powered Budget Planner
 */
?>

<!-- Loading Overlay -->
<div id="budgetLoadingOverlay" style="display:none; position:fixed; inset:0; z-index:9999; background:rgba(15,15,30,0.82); backdrop-filter:blur(8px); align-items:center; justify-content:center; flex-direction:column; gap:20px;">
    <div style="width:72px; height:72px; border:4px solid rgba(255,255,255,0.15); border-top-color:var(--primary); border-radius:50%; animation:budgetSpin 0.9s linear infinite;"></div>
    <div style="text-align:center;">
        <p style="color:#fff; font-size:1.15rem; font-weight:600; margin:0 0 6px;">Generating Your Budget...</p>
        <p style="color:rgba(255,255,255,0.6); font-size:0.88rem; margin:0;">AI is estimating costs for your ritual. This may take a few seconds.</p>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2 class="card-title"><i class="fas fa-magic"></i> Plan a Ritual Budget</h2>
        <a href="/user/budgets" class="btn btn-sm" style="background:#E5E7EB; color:#374151;">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>

    <!-- Intro Banner -->
    <div style="background:linear-gradient(135deg,rgba(255,107,53,0.08),rgba(168,85,247,0.08)); border-radius:14px; padding:20px 24px; margin-bottom:28px; display:flex; align-items:center; gap:16px;">
        <div style="width:52px; height:52px; background:linear-gradient(135deg,var(--primary),#A855F7); border-radius:14px; display:flex; align-items:center; justify-content:center; color:#fff; font-size:1.5rem; flex-shrink:0;">
            <i class="fas fa-rupee-sign"></i>
        </div>
        <div>
            <h4 style="margin:0 0 4px; color:#1E1E2E;">AI-Powered Cost Estimation</h4>
            <p style="margin:0; color:#6B7280; font-size:0.9rem; line-height:1.5;">
                Provide your ritual details and our AI will generate a detailed, category-wise budget estimate in seconds — covering Pandit fees, decoration, food, venue, and more.
            </p>
        </div>
    </div>

    <form method="POST" action="/user/budgets" id="budgetCreateForm">
        <?= \App\Core\Auth::csrfField() ?>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">

            <!-- Ritual Type -->
            <div class="form-group">
                <label for="ritual_type">
                    <i class="fas fa-om" style="color:var(--primary);"></i>
                    Ritual / Ceremony Type <span style="color:#EF4444;">*</span>
                </label>
                <input type="text" name="ritual_type" id="ritual_type" class="form-control budget-input"
                       placeholder="e.g., Griha Pravesh, Vivah, Satyanarayan Katha, Mundan…"
                       value="<?= htmlspecialchars($_POST['ritual_type'] ?? '') ?>"
                       required maxlength="255">
                <small style="color:#9CA3AF; display:block; margin-top:5px;">The name of the ritual or ceremony you are planning</small>
            </div>

            <!-- Location -->
            <div class="form-group">
                <label for="location">
                    <i class="fas fa-map-marker-alt" style="color:var(--primary);"></i>
                    Location / City <span style="color:#EF4444;">*</span>
                </label>
                <input type="text" name="location" id="location" class="form-control budget-input"
                       placeholder="e.g., Mumbai, Delhi, Varanasi…"
                       value="<?= htmlspecialchars($_POST['location'] ?? '') ?>"
                       required maxlength="255">
                <small style="color:#9CA3AF; display:block; margin-top:5px;">Used to tailor cost estimates to your region</small>
            </div>

            <!-- Number of Guests -->
            <div class="form-group">
                <label for="guest_count">
                    <i class="fas fa-users" style="color:var(--primary);"></i>
                    Number of Guests <span style="color:#EF4444;">*</span>
                </label>
                <input type="number" name="guest_count" id="guest_count" class="form-control budget-input"
                       placeholder="e.g., 100"
                       value="<?= htmlspecialchars($_POST['guest_count'] ?? '') ?>"
                       min="1" max="10000" required>
                <small style="color:#9CA3AF; display:block; margin-top:5px;">Between 1 and 10,000 guests</small>
            </div>

        </div>

        <!-- Tier Selection -->
        <div class="form-group" style="margin-top:8px;">
            <label style="display:block; margin-bottom:14px;">
                <i class="fas fa-layer-group" style="color:var(--primary);"></i>
                Budget Tier <span style="color:#EF4444;">*</span>
            </label>
            <p style="color:#9CA3AF; font-size:0.85rem; margin:-8px 0 16px;">Choose the scale and quality level for your ritual</p>

            <div class="tier-cards-grid">

                <!-- Basic -->
                <label class="tier-card" for="tier_basic">
                    <input type="radio" name="tier" id="tier_basic" value="basic"
                           <?= (($_POST['tier'] ?? 'standard') === 'basic') ? 'checked' : '' ?>>
                    <div class="tier-card-inner">
                        <div class="tier-card-icon" style="background:linear-gradient(135deg,#D1FAE5,#A7F3D0); color:#065F46;">
                            <i class="fas fa-seedling"></i>
                        </div>
                        <div class="tier-card-title">Basic</div>
                        <div class="tier-card-desc">Simple, meaningful ceremony with essential items. Ideal for intimate gatherings on a modest budget.</div>
                        <div class="tier-card-features">
                            <span><i class="fas fa-check"></i> Essential puja items</span>
                            <span><i class="fas fa-check"></i> Simple decoration</span>
                            <span><i class="fas fa-check"></i> Home or local venue</span>
                        </div>
                        <div class="tier-card-check"><i class="fas fa-check-circle"></i></div>
                    </div>
                </label>

                <!-- Standard -->
                <label class="tier-card" for="tier_standard">
                    <input type="radio" name="tier" id="tier_standard" value="standard"
                           <?= (($_POST['tier'] ?? 'standard') === 'standard') ? 'checked' : '' ?>>
                    <div class="tier-card-inner tier-card-recommended">
                        <div class="tier-recommended-badge">Most Popular</div>
                        <div class="tier-card-icon" style="background:linear-gradient(135deg,#DBEAFE,#BFDBFE); color:#1E40AF;">
                            <i class="fas fa-star-half-alt"></i>
                        </div>
                        <div class="tier-card-title">Standard</div>
                        <div class="tier-card-desc">Well-rounded ceremony with quality arrangements. Perfect for family celebrations with a comfortable budget.</div>
                        <div class="tier-card-features">
                            <span><i class="fas fa-check"></i> Full puja setup</span>
                            <span><i class="fas fa-check"></i> Floral decoration</span>
                            <span><i class="fas fa-check"></i> Catering included</span>
                        </div>
                        <div class="tier-card-check"><i class="fas fa-check-circle"></i></div>
                    </div>
                </label>

                <!-- Premium -->
                <label class="tier-card" for="tier_premium">
                    <input type="radio" name="tier" id="tier_premium" value="premium"
                           <?= (($_POST['tier'] ?? 'standard') === 'premium') ? 'checked' : '' ?>>
                    <div class="tier-card-inner">
                        <div class="tier-card-icon" style="background:linear-gradient(135deg,#FEF3C7,#FDE68A); color:#92400E;">
                            <i class="fas fa-crown"></i>
                        </div>
                        <div class="tier-card-title">Premium</div>
                        <div class="tier-card-desc">Grand, luxurious ceremony with premium vendors and elaborate arrangements. For a truly memorable occasion.</div>
                        <div class="tier-card-features">
                            <span><i class="fas fa-check"></i> Premium decorations</span>
                            <span><i class="fas fa-check"></i> Professional vendors</span>
                            <span><i class="fas fa-check"></i> Banquet hall & catering</span>
                        </div>
                        <div class="tier-card-check"><i class="fas fa-check-circle"></i></div>
                    </div>
                </label>

            </div>
        </div>

        <!-- Submit -->
        <div style="display:flex; gap:15px; align-items:center; margin-top:28px;">
            <button type="submit" class="btn btn-primary" id="budgetSubmitBtn" style="padding:14px 36px; font-size:1rem;">
                <i class="fas fa-magic"></i> Generate Budget with AI
            </button>
            <a href="/user/budgets" class="btn" style="background:#E5E7EB; color:#374151; padding:14px 24px;">
                <i class="fas fa-times"></i> Cancel
            </a>
        </div>

    </form>
</div>

<style>
    /* Input focus */
    .budget-input:focus {
        border-color: var(--primary) !important;
        box-shadow: 0 0 0 3px rgba(255,107,53,0.12);
        outline: none;
    }

    /* Tier cards grid */
    .tier-cards-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
    }

    .tier-card {
        cursor: pointer;
        display: block;
    }

    .tier-card input[type="radio"] {
        display: none;
    }

    .tier-card-inner {
        position: relative;
        border: 2px solid #E5E7EB;
        border-radius: 16px;
        padding: 22px 18px 18px;
        background: #FAFAFA;
        transition: all 0.25s ease;
        height: 100%;
        box-sizing: border-box;
    }

    .tier-card-inner:hover {
        border-color: #D1D5DB;
        background: #fff;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0,0,0,0.07);
    }

    .tier-card input:checked + .tier-card-inner {
        border-color: var(--primary);
        background: #fff;
        box-shadow: 0 0 0 3px rgba(255,107,53,0.12), 0 6px 20px rgba(255,107,53,0.1);
        transform: translateY(-2px);
    }

    .tier-card-recommended {
        border-color: #3B82F6 !important;
        background: #fff !important;
    }

    .tier-card input:checked + .tier-card-recommended {
        border-color: var(--primary) !important;
        box-shadow: 0 0 0 3px rgba(255,107,53,0.12), 0 6px 20px rgba(255,107,53,0.1) !important;
    }

    .tier-recommended-badge {
        position: absolute;
        top: -11px;
        left: 50%;
        transform: translateX(-50%);
        background: linear-gradient(135deg, #3B82F6, #6366F1);
        color: #fff;
        font-size: 0.7rem;
        font-weight: 700;
        letter-spacing: 0.5px;
        padding: 3px 12px;
        border-radius: 20px;
        white-space: nowrap;
    }

    .tier-card input:checked + .tier-card-recommended .tier-recommended-badge {
        background: linear-gradient(135deg, var(--primary), #A855F7);
    }

    .tier-card-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.3rem;
        margin-bottom: 12px;
    }

    .tier-card-title {
        font-size: 1.05rem;
        font-weight: 700;
        color: #1E1E2E;
        margin-bottom: 8px;
    }

    .tier-card-desc {
        font-size: 0.82rem;
        color: #6B7280;
        line-height: 1.55;
        margin-bottom: 14px;
    }

    .tier-card-features {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .tier-card-features span {
        font-size: 0.78rem;
        color: #374151;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .tier-card-features span i {
        color: #10B981;
        font-size: 0.7rem;
        flex-shrink: 0;
    }

    .tier-card-check {
        position: absolute;
        top: 14px;
        right: 14px;
        font-size: 1.2rem;
        color: #D1D5DB;
        transition: color 0.2s ease;
    }

    .tier-card input:checked + .tier-card-inner .tier-card-check {
        color: var(--primary);
    }

    /* Loading overlay spin */
    @keyframes budgetSpin {
        to { transform: rotate(360deg); }
    }

    /* Responsive */
    @media (max-width: 768px) {
        div[style*="grid-template-columns:1fr 1fr"] {
            display: flex !important;
            flex-direction: column;
        }
        .tier-cards-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<script>
    // Tier card visual selection sync
    document.querySelectorAll('.tier-card input[type="radio"]').forEach(function(radio) {
        radio.addEventListener('change', function() {
            // nothing extra needed — CSS :checked handles it
        });
    });

    // Form submit → show loading overlay
    document.getElementById('budgetCreateForm').addEventListener('submit', function() {
        const btn = document.getElementById('budgetSubmitBtn');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generating…';
        btn.style.opacity = '0.75';

        const overlay = document.getElementById('budgetLoadingOverlay');
        overlay.style.display = 'flex';
    });
</script>
