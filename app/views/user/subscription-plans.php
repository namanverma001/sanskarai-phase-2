<?php
/**
 * AI Pandit Subscription Plans
 * Select and purchase subscription plans
 */
$csrfToken = \App\Core\Auth::csrfToken();
?>

<div class="container py-4">
    <!-- Header -->
    <div class="text-center mb-5">
        <h1 class="display-5 fw-bold text-gradient mb-3">AI Pandit Subscription</h1>
        <p class="lead text-muted">Unlock unlimited spiritual guidance with our AI-powered Pandit</p>
    </div>

    <!-- Active Subscription Banner -->
    <?php if ($activeSubscription): ?>
    <div class="alert alert-success d-flex align-items-center mb-4" role="alert">
        <i class="bi bi-check-circle-fill me-2 fs-4"></i>
        <div>
            <strong>Active Subscription:</strong> <?= htmlspecialchars($activeSubscription['plan_name']) ?>
            <span class="ms-2 badge bg-success"><?= $daysRemaining ?> days remaining</span>
            <a href="/user/ai-pandit" class="btn btn-sm btn-outline-success ms-3">Go to AI Pandit</a>
        </div>
    </div>
    <?php endif; ?>

    <!-- Pricing Cards -->
    <div class="row g-4 justify-content-center">
        <?php foreach ($plans as $index => $plan):
            $features = json_decode($plan['features'], true) ?? [];
            $isPopular = $plan['slug'] === 'half-yearly';
            $isBestValue = $plan['slug'] === 'yearly';
        ?>
        <div class="col-md-6 col-lg-3">
            <div class="card h-100 shadow-sm pricing-card <?= $isPopular ? 'border-primary popular' : '' ?> <?= $isBestValue ? 'border-success' : '' ?>">
                <?php if ($isPopular): ?>
                <div class="popular-badge">Most Popular</div>
                <?php elseif ($isBestValue): ?>
                <div class="best-value-badge">Best Value</div>
                <?php endif; ?>

                <div class="card-header text-center py-4 <?= $isPopular ? 'bg-primary text-white' : ($isBestValue ? 'bg-success text-white' : 'bg-light') ?>">
                    <h4 class="mb-0"><?= htmlspecialchars($plan['name']) ?></h4>
                    <small><?= $plan['duration_days'] ?> days</small>
                </div>

                <div class="card-body d-flex flex-column">
                    <div class="text-center mb-4">
                        <span class="display-4 fw-bold">₹<?= number_format($plan['price'], 0) ?></span>
                        <?php if ($plan['duration_days'] > 1): ?>
                        <small class="text-muted d-block">
                            ₹<?= number_format($plan['price'] / $plan['duration_days'], 2) ?>/day
                        </small>
                        <?php endif; ?>
                    </div>

                    <p class="text-muted text-center mb-4"><?= htmlspecialchars($plan['description']) ?></p>

                    <ul class="list-unstyled mb-4 flex-grow-1">
                        <?php foreach ($features as $feature): ?>
                        <li class="mb-2">
                            <i class="bi bi-check-circle-fill text-success me-2"></i>
                            <?= htmlspecialchars($feature) ?>
                        </li>
                        <?php endforeach; ?>
                    </ul>

                    <button
                        class="btn btn-lg w-100 subscribe-btn <?= $isPopular ? 'btn-primary' : ($isBestValue ? 'btn-success' : 'btn-outline-primary') ?>"
                        data-plan-id="<?= $plan['id'] ?>"
                        data-plan-name="<?= htmlspecialchars($plan['name']) ?>"
                        data-plan-price="<?= $plan['price'] ?>"
                        <?= $activeSubscription ? 'disabled' : '' ?>
                    >
                        <?= $activeSubscription ? 'Already Subscribed' : 'Subscribe Now' ?>
                    </button>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Features Section -->
    <div class="row mt-5 pt-4">
        <div class="col-12 text-center mb-4">
            <h3>What You Get with AI Pandit</h3>
        </div>
        <div class="col-md-4 text-center mb-4">
            <div class="feature-icon bg-primary bg-gradient text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                <i class="bi bi-chat-dots fs-4"></i>
            </div>
            <h5>Unlimited Conversations</h5>
            <p class="text-muted">Chat as much as you want with our AI Pandit for spiritual guidance</p>
        </div>
        <div class="col-md-4 text-center mb-4">
            <div class="feature-icon bg-success bg-gradient text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                <i class="bi bi-book fs-4"></i>
            </div>
            <h5>Ritual Guidance</h5>
            <p class="text-muted">Get personalized guidance for all Hindu rituals and ceremonies</p>
        </div>
        <div class="col-md-4 text-center mb-4">
            <div class="feature-icon bg-warning bg-gradient text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                <i class="bi bi-clock-history fs-4"></i>
            </div>
            <h5>24/7 Availability</h5>
            <p class="text-muted">Access AI Pandit anytime, anywhere - day or night</p>
        </div>
    </div>

    <!-- Payment Security -->
    <div class="row mt-4">
        <div class="col-12 text-center">
            <div class="d-flex align-items-center justify-content-center gap-3 text-muted">
                <i class="bi bi-shield-check fs-4"></i>
                <span>Secure payment powered by Razorpay</span>
                <img src="https://razorpay.com/favicon.png" alt="Razorpay" style="height: 24px;">
            </div>
        </div>
    </div>
</div>

<!-- Payment Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Processing Payment</h5>
            </div>
            <div class="modal-body text-center py-5">
                <div class="spinner-border text-primary mb-3" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mb-0">Initializing secure payment...</p>
            </div>
        </div>
    </div>
</div>

<style>
.pricing-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    position: relative;
    overflow: hidden;
}

.pricing-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 1rem 3rem rgba(0,0,0,.175) !important;
}

.pricing-card.popular {
    border-width: 2px;
}

.popular-badge, .best-value-badge {
    position: absolute;
    top: 15px;
    right: -35px;
    background: #ffc107;
    color: #000;
    padding: 5px 40px;
    font-size: 12px;
    font-weight: bold;
    transform: rotate(45deg);
    z-index: 10;
}

.best-value-badge {
    background: #198754;
    color: white;
}

.text-gradient {
    background: linear-gradient(135deg, #FF6B35, #FF8C42);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.subscribe-btn:disabled {
    opacity: 0.7;
}
</style>

<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = '<?= $csrfToken ?>';
    const paymentModal = new bootstrap.Modal(document.getElementById('paymentModal'));

    document.querySelectorAll('.subscribe-btn').forEach(btn => {
        btn.addEventListener('click', async function() {
            if (this.disabled) return;

            const planId = this.dataset.planId;
            const planName = this.dataset.planName;
            const planPrice = this.dataset.planPrice;

            // Show loading modal
            paymentModal.show();

            try {
                // Create order
                const response = await fetch('/user/subscription/purchase', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `plan_id=${planId}&_token=${csrfToken}`
                });

                const data = await response.json();

                if (!data.success) {
                    throw new Error(data.error || 'Failed to create order');
                }

                // Hide loading modal
                paymentModal.hide();

                // Initialize Razorpay
                const options = {
                    ...data.checkout_options,
                    handler: async function(response) {
                        // Show loading again
                        paymentModal.show();
                        document.querySelector('#paymentModal .modal-body p').textContent = 'Verifying payment...';

                        // Verify payment
                        const verifyResponse = await fetch('/user/subscription/verify', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            body: `razorpay_order_id=${response.razorpay_order_id}&razorpay_payment_id=${response.razorpay_payment_id}&razorpay_signature=${response.razorpay_signature}&_token=${csrfToken}`
                        });

                        const verifyData = await verifyResponse.json();

                        if (verifyData.success) {
                            window.location.href = '/user/subscription/success';
                        } else {
                            paymentModal.hide();
                            alert(verifyData.error || 'Payment verification failed');
                        }
                    },
                    modal: {
                        ondismiss: function() {
                            console.log('Payment cancelled');
                        }
                    }
                };

                const rzp = new Razorpay(options);
                rzp.on('payment.failed', function(response) {
                    alert('Payment failed: ' + response.error.description);
                });
                rzp.open();

            } catch (error) {
                paymentModal.hide();
                alert(error.message || 'Something went wrong');
            }
        });
    });
});
</script>
