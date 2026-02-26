<style>
    .approval-page {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 65vh;
        padding: 30px 20px;
    }

    .approval-container {
        max-width: 600px;
        width: 100%;
        text-align: center;
    }

    .approval-icon-wrap {
        position: relative;
        width: 140px;
        height: 140px;
        margin: 0 auto 30px;
    }

    .approval-icon-circle {
        width: 140px;
        height: 140px;
        border-radius: 50%;
        background: linear-gradient(135deg, #FEF3C7, #FDE68A);
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        animation: pulse-glow 2.5s ease-in-out infinite;
    }

    .approval-icon-circle i {
        font-size: 3.5rem;
        color: #D97706;
    }

    .approval-icon-circle::after {
        content: '';
        position: absolute;
        inset: -8px;
        border-radius: 50%;
        border: 3px dashed #F59E0B;
        animation: spin-slow 12s linear infinite;
    }

    @keyframes pulse-glow {

        0%,
        100% {
            box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.2);
        }

        50% {
            box-shadow: 0 0 30px 10px rgba(245, 158, 11, 0.15);
        }
    }

    @keyframes spin-slow {
        from {
            transform: rotate(0deg);
        }

        to {
            transform: rotate(360deg);
        }
    }

    .approval-title {
        font-size: 1.8rem;
        font-weight: 700;
        color: #1E1E2E;
        margin-bottom: 12px;
        line-height: 1.3;
    }

    .approval-title span {
        background: linear-gradient(135deg, #F59E0B, #D97706);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .approval-subtitle {
        font-size: 1.05rem;
        color: #6B7280;
        line-height: 1.7;
        margin-bottom: 30px;
    }

    .approval-status-card {
        background: linear-gradient(135deg, #FFFBEB, #FEF3C7);
        border: 1px solid #FDE68A;
        border-radius: 16px;
        padding: 24px;
        margin-bottom: 30px;
    }

    .approval-status-header {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        margin-bottom: 16px;
    }

    .approval-status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #FEF3C7;
        border: 1px solid #F59E0B;
        color: #92400E;
        padding: 6px 16px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
    }

    .approval-status-badge i {
        animation: blink 1.5s ease-in-out infinite;
    }

    @keyframes blink {

        0%,
        100% {
            opacity: 1;
        }

        50% {
            opacity: 0.3;
        }
    }

    .approval-steps {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0;
        margin-top: 16px;
    }

    .approval-step {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
        position: relative;
    }

    .approval-step-icon {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        font-weight: 600;
        z-index: 1;
    }

    .approval-step-icon.done {
        background: #10B981;
        color: white;
    }

    .approval-step-icon.active {
        background: #F59E0B;
        color: white;
        animation: pulse-step 2s ease-in-out infinite;
    }

    .approval-step-icon.pending {
        background: #E5E7EB;
        color: #9CA3AF;
    }

    @keyframes pulse-step {

        0%,
        100% {
            box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.4);
        }

        50% {
            box-shadow: 0 0 0 8px rgba(245, 158, 11, 0);
        }
    }

    .approval-step-label {
        font-size: 0.75rem;
        color: #6B7280;
        font-weight: 500;
        max-width: 80px;
        text-align: center;
    }

    .approval-step-connector {
        width: 50px;
        height: 3px;
        background: #E5E7EB;
        margin: 0 4px;
        margin-bottom: 28px;
        border-radius: 2px;
    }

    .approval-step-connector.done {
        background: #10B981;
    }

    .approval-info-box {
        background: white;
        border: 1px solid #E5E7EB;
        border-radius: 16px;
        padding: 24px;
        margin-bottom: 24px;
    }

    .approval-info-title {
        font-size: 0.95rem;
        font-weight: 600;
        color: #374151;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .approval-info-title i {
        color: #8B5CF6;
    }

    .approval-info-list {
        list-style: none;
        padding: 0;
        text-align: left;
    }

    .approval-info-list li {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        padding: 10px 0;
        border-bottom: 1px solid #F3F4F6;
        font-size: 0.9rem;
        color: #4B5563;
    }

    .approval-info-list li:last-child {
        border-bottom: none;
    }

    .approval-info-list li i {
        color: #8B5CF6;
        margin-top: 3px;
        flex-shrink: 0;
    }

    .approval-profile-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 28px;
        background: linear-gradient(135deg, #8B5CF6, #7C3AED);
        color: white;
        border: none;
        border-radius: 12px;
        font-size: 0.95rem;
        font-weight: 600;
        text-decoration: none;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(139, 92, 246, 0.3);
    }

    .approval-profile-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(139, 92, 246, 0.4);
    }

    .approval-footer-note {
        margin-top: 24px;
        font-size: 0.82rem;
        color: #9CA3AF;
    }

    .approval-footer-note i {
        color: #D97706;
    }

    /* ===== CONGRATS STYLES ===== */
    .congrats-page {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 65vh;
        padding: 30px 20px;
    }

    .congrats-container {
        max-width: 600px;
        width: 100%;
        text-align: center;
    }

    .congrats-icon-wrap {
        width: 140px;
        height: 140px;
        margin: 0 auto 30px;
    }

    .congrats-icon-circle {
        width: 140px;
        height: 140px;
        border-radius: 50%;
        background: linear-gradient(135deg, #D1FAE5, #A7F3D0);
        display: flex;
        align-items: center;
        justify-content: center;
        animation: congrats-pop 0.6s ease-out;
        position: relative;
    }

    .congrats-icon-circle i {
        font-size: 3.5rem;
        color: #059669;
    }

    .congrats-icon-circle::after {
        content: '';
        position: absolute;
        inset: -8px;
        border-radius: 50%;
        border: 3px solid #10B981;
        animation: congrats-ring 1.5s ease-out;
        opacity: 0.5;
    }

    @keyframes congrats-pop {
        0% {
            transform: scale(0);
        }

        60% {
            transform: scale(1.15);
        }

        100% {
            transform: scale(1);
        }
    }

    @keyframes congrats-ring {
        0% {
            transform: scale(0.8);
            opacity: 0;
        }

        50% {
            opacity: 0.6;
        }

        100% {
            transform: scale(1);
            opacity: 0.5;
        }
    }

    .congrats-title {
        font-size: 2rem;
        font-weight: 700;
        color: #1E1E2E;
        margin-bottom: 8px;
        animation: fadeInUp 0.5s ease-out 0.3s both;
    }

    .congrats-title span {
        background: linear-gradient(135deg, #10B981, #059669);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .congrats-subtitle {
        font-size: 1.1rem;
        color: #6B7280;
        line-height: 1.7;
        margin-bottom: 30px;
        animation: fadeInUp 0.5s ease-out 0.5s both;
    }

    .congrats-features {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 14px;
        margin-bottom: 30px;
        animation: fadeInUp 0.5s ease-out 0.7s both;
    }

    .congrats-feature {
        background: white;
        border: 1px solid #E5E7EB;
        border-radius: 12px;
        padding: 18px 14px;
        text-align: center;
        transition: all 0.3s ease;
    }

    .congrats-feature:hover {
        border-color: #8B5CF6;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(139, 92, 246, 0.1);
    }

    .congrats-feature i {
        font-size: 1.5rem;
        margin-bottom: 8px;
        display: block;
    }

    .congrats-feature span {
        font-size: 0.85rem;
        font-weight: 500;
        color: #374151;
    }

    .congrats-cta {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 14px 32px;
        background: linear-gradient(135deg, #8B5CF6, #7C3AED);
        color: white;
        border: none;
        border-radius: 12px;
        font-size: 1rem;
        font-weight: 600;
        text-decoration: none;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(139, 92, 246, 0.3);
        animation: fadeInUp 0.5s ease-out 0.9s both;
    }

    .congrats-cta:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(139, 92, 246, 0.4);
    }

    .confetti {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        pointer-events: none;
        z-index: 9999;
        overflow: hidden;
    }

    .confetti-piece {
        position: absolute;
        width: 10px;
        height: 10px;
        top: -10px;
        animation: confetti-fall 3s ease-in-out forwards;
    }

    @keyframes confetti-fall {
        0% {
            transform: translateY(0) rotate(0deg);
            opacity: 1;
        }

        100% {
            transform: translateY(100vh) rotate(720deg);
            opacity: 0;
        }
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* ===== RESPONSIVE ===== */
    @media (max-width: 768px) {

        .approval-page,
        .congrats-page {
            min-height: 60vh;
            padding: 24px 16px;
        }

        .approval-icon-wrap,
        .congrats-icon-wrap {
            width: 110px;
            height: 110px;
            margin-bottom: 24px;
        }

        .approval-icon-circle,
        .congrats-icon-circle {
            width: 110px;
            height: 110px;
        }

        .approval-icon-circle i,
        .congrats-icon-circle i {
            font-size: 2.8rem;
        }

        .approval-title {
            font-size: 1.4rem;
        }

        .congrats-title {
            font-size: 1.5rem;
        }

        .approval-subtitle,
        .congrats-subtitle {
            font-size: 0.92rem;
        }

        .approval-status-card {
            padding: 18px 16px;
        }

        .approval-steps {
            gap: 0;
        }

        .approval-step-icon {
            width: 36px;
            height: 36px;
            font-size: 0.85rem;
        }

        .approval-step-connector {
            width: 30px;
        }

        .approval-step-label {
            font-size: 0.68rem;
            max-width: 65px;
        }

        .approval-info-box {
            padding: 18px 16px;
        }

        .approval-info-list li {
            font-size: 0.82rem;
        }

        .congrats-features {
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .congrats-feature {
            padding: 14px 10px;
        }

        .congrats-feature i {
            font-size: 1.2rem;
        }

        .congrats-feature span {
            font-size: 0.78rem;
        }
    }

    @media (max-width: 480px) {
        .approval-title {
            font-size: 1.25rem;
        }

        .congrats-title {
            font-size: 1.3rem;
        }

        .approval-step-connector {
            width: 20px;
        }

        .approval-step-icon {
            width: 32px;
            height: 32px;
            font-size: 0.8rem;
        }

        .approval-step-label {
            font-size: 0.65rem;
            max-width: 55px;
        }

        .congrats-features {
            grid-template-columns: 1fr;
        }
    }
</style>

<?php if (isset($showCongrats) && $showCongrats): ?>
    <!-- ========== CONGRATULATIONS PAGE ========== -->
    <div class="confetti" id="confettiContainer"></div>

    <div class="congrats-page">
        <div class="congrats-container">
            <div class="congrats-icon-wrap">
                <div class="congrats-icon-circle">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>

            <h1 class="congrats-title">🎉 <span>Congratulations!</span></h1>
            <p class="congrats-subtitle">
                Your pandit profile has been <strong>approved</strong> by the admin.<br>
                You now have full access to all dashboard features. Start managing your assignments and connect with
                devotees!
            </p>

            <div class="congrats-features">
                <div class="congrats-feature">
                    <i class="fas fa-calendar-check" style="color: #8B5CF6;"></i>
                    <span>Manage Assignments</span>
                </div>
                <div class="congrats-feature">
                    <i class="fas fa-question-circle" style="color: #F59E0B;"></i>
                    <span>Answer Questions</span>
                </div>
                <div class="congrats-feature">
                    <i class="fas fa-om" style="color: #EC4899;"></i>
                    <span>Custom Rituals</span>
                </div>
                <div class="congrats-feature">
                    <i class="fas fa-user-edit" style="color: #10B981;"></i>
                    <span>Update Profile</span>
                </div>
            </div>

            <a href="/pandit/dashboard" class="congrats-cta">
                <i class="fas fa-rocket"></i> Go to Dashboard
            </a>
        </div>
    </div>

    <script>
        // Confetti animation
        (function () {
            const container = document.getElementById('confettiContainer');
            const colors = ['#8B5CF6', '#EC4899', '#F59E0B', '#10B981', '#6366F1', '#EF4444', '#FF9933'];
            const shapes = ['circle', 'square'];

            for (let i = 0; i < 60; i++) {
                const piece = document.createElement('div');
                piece.className = 'confetti-piece';
                piece.style.left = Math.random() * 100 + '%';
                piece.style.background = colors[Math.floor(Math.random() * colors.length)];
                piece.style.animationDelay = Math.random() * 2 + 's';
                piece.style.animationDuration = (2 + Math.random() * 2) + 's';

                if (shapes[Math.floor(Math.random() * shapes.length)] === 'circle') {
                    piece.style.borderRadius = '50%';
                }

                piece.style.width = (6 + Math.random() * 8) + 'px';
                piece.style.height = (6 + Math.random() * 8) + 'px';

                container.appendChild(piece);
            }

            // Remove confetti after animation
            setTimeout(() => {
                container.style.display = 'none';
            }, 5000);
        })();
    </script>

<?php else: ?>
    <!-- ========== PENDING APPROVAL PAGE ========== -->
    <div class="approval-page">
        <div class="approval-container">
            <div class="approval-icon-wrap">
                <div class="approval-icon-circle">
                    <i class="fas fa-hourglass-half"></i>
                </div>
            </div>

            <h1 class="approval-title">Profile <span>Under Review</span></h1>
            <p class="approval-subtitle">
                Your pandit profile has been submitted and is currently being reviewed by our admin team.
                You'll get full access to the dashboard once your profile is approved.
            </p>

            <!-- Status Card -->
            <div class="approval-status-card">
                <div class="approval-status-header">
                    <div class="approval-status-badge">
                        <i class="fas fa-circle"></i>
                        <?= ucfirst($profile['approval_status'] ?? 'pending') ?> Review
                    </div>
                </div>

                <!-- Progress Steps -->
                <div class="approval-steps">
                    <div class="approval-step">
                        <div class="approval-step-icon done"><i class="fas fa-check"></i></div>
                        <span class="approval-step-label">Signed Up</span>
                    </div>
                    <div class="approval-step-connector done"></div>
                    <div class="approval-step">
                        <div class="approval-step-icon done"><i class="fas fa-check"></i></div>
                        <span class="approval-step-label">Profile Created</span>
                    </div>
                    <div class="approval-step-connector"></div>
                    <div class="approval-step">
                        <div class="approval-step-icon active"><i class="fas fa-search"></i></div>
                        <span class="approval-step-label">Admin Review</span>
                    </div>
                    <div class="approval-step-connector"></div>
                    <div class="approval-step">
                        <div class="approval-step-icon pending"><i class="fas fa-rocket"></i></div>
                        <span class="approval-step-label">Go Live!</span>
                    </div>
                </div>
            </div>

            <!-- Info Section -->
            <div class="approval-info-box">
                <div class="approval-info-title">
                    <i class="fas fa-info-circle"></i> What happens next?
                </div>
                <ul class="approval-info-list">
                    <li>
                        <i class="fas fa-check-circle"></i>
                        <span>Our admin team will verify your profile details and qualifications.</span>
                    </li>
                    <li>
                        <i class="fas fa-bell"></i>
                        <span>Once approved, you'll automatically get access to all dashboard features.</span>
                    </li>
                    <li>
                        <i class="fas fa-clock"></i>
                        <span>This usually takes 24-48 hours. Thank you for your patience!</span>
                    </li>
                    <li>
                        <i class="fas fa-edit"></i>
                        <span>Meanwhile, you can update your profile to ensure all details are correct.</span>
                    </li>
                </ul>
            </div>

            <a href="/pandit/profile" class="approval-profile-btn">
                <i class="fas fa-user-edit"></i> Update My Profile
            </a>

            <p class="approval-footer-note">
                <i class="fas fa-om"></i> Sanskar AI — Preserving sacred traditions through technology
            </p>
        </div>
    </div>
<?php endif; ?>