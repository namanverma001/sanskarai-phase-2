<?php
/**
 * Subscription Success Page
 * Displayed after successful payment
 */
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-lg border-0">
                <div class="card-body text-center p-5">
                    <!-- Success Icon -->
                    <div class="success-animation mb-4">
                        <div class="success-icon">
                            <i class="bi bi-check-lg"></i>
                        </div>
                    </div>

                    <h2 class="text-success mb-3">Payment Successful!</h2>
                    <p class="lead text-muted mb-4">
                        Your AI Pandit subscription is now active
                    </p>

                    <?php if ($subscription): ?>
                    <div class="bg-light rounded p-4 mb-4">
                        <div class="row text-start">
                            <div class="col-6 mb-3">
                                <small class="text-muted">Plan</small>
                                <div class="fw-bold"><?= htmlspecialchars($subscription['plan_name']) ?></div>
                            </div>
                            <div class="col-6 mb-3">
                                <small class="text-muted">Duration</small>
                                <div class="fw-bold"><?= $subscription['duration_days'] ?> Days</div>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Valid Till</small>
                                <div class="fw-bold"><?= date('d M Y', strtotime($subscription['expires_at'])) ?></div>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Days Remaining</small>
                                <div class="fw-bold text-success"><?= $daysRemaining ?> Days</div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="alert alert-info d-flex align-items-center mb-4" role="alert">
                        <i class="bi bi-envelope-check me-2"></i>
                        <span>A confirmation email has been sent to your registered email address.</span>
                    </div>

                    <div class="d-grid gap-2">
                        <a href="/user/ai-pandit" class="btn btn-primary btn-lg">
                            <i class="bi bi-chat-dots me-2"></i>
                            Start Chatting with AI Pandit
                        </a>
                        <a href="/user/my-subscription" class="btn btn-outline-secondary">
                            View My Subscription
                        </a>
                    </div>
                </div>
            </div>

            <!-- What's Next -->
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-body p-4">
                    <h5 class="mb-3"><i class="bi bi-lightbulb text-warning me-2"></i>What You Can Do Now</h5>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="bi bi-check text-success me-2"></i>
                            Ask about Hindu rituals, ceremonies, and traditions
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check text-success me-2"></i>
                            Get personalized guidance for your family's religious practices
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check text-success me-2"></i>
                            Learn about auspicious dates (muhurat) for important events
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check text-success me-2"></i>
                            Understand mantras, shlokas, and their meanings
                        </li>
                        <li class="mb-0">
                            <i class="bi bi-check text-success me-2"></i>
                            Get answers to your spiritual questions 24/7
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.success-animation {
    display: inline-block;
}

.success-icon {
    width: 100px;
    height: 100px;
    background: linear-gradient(135deg, #28a745, #20c997);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    animation: scaleIn 0.5s ease-out;
}

.success-icon i {
    font-size: 50px;
    color: white;
}

@keyframes scaleIn {
    0% {
        transform: scale(0);
        opacity: 0;
    }
    50% {
        transform: scale(1.1);
    }
    100% {
        transform: scale(1);
        opacity: 1;
    }
}
</style>
