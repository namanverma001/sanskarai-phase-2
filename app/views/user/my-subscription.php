<?php
/**
 * My Subscription Page
 * View subscription details and history
 */
?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-credit-card-2-front me-2"></i>My Subscription</h2>
        <a href="/user/subscription/plans" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i>
            <?= $activeSubscription ? 'Renew/Upgrade' : 'Subscribe Now' ?>
        </a>
    </div>

    <!-- Active Subscription Card -->
    <?php if ($activeSubscription): ?>
    <div class="card border-success mb-4 shadow-sm">
        <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
            <span><i class="bi bi-check-circle-fill me-2"></i>Active Subscription</span>
            <span class="badge bg-light text-success"><?= $daysRemaining ?> days remaining</span>
        </div>
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h4 class="mb-3"><?= htmlspecialchars($activeSubscription['plan_name']) ?></h4>
                    <div class="row g-3">
                        <div class="col-6 col-md-3">
                            <small class="text-muted d-block">Started On</small>
                            <strong><?= date('d M Y', strtotime($activeSubscription['starts_at'])) ?></strong>
                        </div>
                        <div class="col-6 col-md-3">
                            <small class="text-muted d-block">Expires On</small>
                            <strong><?= date('d M Y', strtotime($activeSubscription['expires_at'])) ?></strong>
                        </div>
                        <div class="col-6 col-md-3">
                            <small class="text-muted d-block">Duration</small>
                            <strong><?= $activeSubscription['duration_days'] ?> Days</strong>
                        </div>
                        <div class="col-6 col-md-3">
                            <small class="text-muted d-block">Status</small>
                            <span class="badge bg-success">Active</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    <a href="/user/ai-pandit" class="btn btn-success btn-lg">
                        <i class="bi bi-chat-dots me-2"></i>Go to AI Pandit
                    </a>
                </div>
            </div>

            <!-- Progress Bar -->
            <?php
            $totalDays = $activeSubscription['duration_days'];
            $usedDays = $totalDays - $daysRemaining;
            $progressPercent = min(100, ($usedDays / $totalDays) * 100);
            ?>
            <div class="mt-4">
                <div class="d-flex justify-content-between mb-1 small">
                    <span>Subscription Progress</span>
                    <span><?= $usedDays ?> of <?= $totalDays ?> days used</span>
                </div>
                <div class="progress" style="height: 8px;">
                    <div class="progress-bar bg-success" role="progressbar" style="width: <?= $progressPercent ?>%"></div>
                </div>
            </div>
        </div>
    </div>
    <?php else: ?>
    <div class="card border-warning mb-4">
        <div class="card-body text-center py-5">
            <i class="bi bi-exclamation-circle text-warning fs-1 mb-3"></i>
            <h4>No Active Subscription</h4>
            <p class="text-muted mb-4">Subscribe to AI Pandit to get unlimited spiritual guidance</p>
            <a href="/user/subscription/plans" class="btn btn-primary btn-lg">
                <i class="bi bi-cart-plus me-2"></i>View Plans & Subscribe
            </a>
        </div>
    </div>
    <?php endif; ?>

    <!-- Subscription History -->
    <div class="card mb-4 shadow-sm">
        <div class="card-header">
            <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Subscription History</h5>
        </div>
        <div class="card-body p-0">
            <?php if (empty($subscriptionHistory)): ?>
            <div class="text-center py-4 text-muted">
                <i class="bi bi-inbox fs-1 mb-2"></i>
                <p>No subscription history found</p>
            </div>
            <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Plan</th>
                            <th>Duration</th>
                            <th>Started</th>
                            <th>Expired</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($subscriptionHistory as $sub): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($sub['plan_name']) ?></strong></td>
                            <td><?= $sub['duration_days'] ?> days</td>
                            <td><?= $sub['starts_at'] ? date('d M Y', strtotime($sub['starts_at'])) : '-' ?></td>
                            <td><?= $sub['expires_at'] ? date('d M Y', strtotime($sub['expires_at'])) : '-' ?></td>
                            <td>
                                <?php
                                $statusClass = match($sub['status']) {
                                    'active' => 'success',
                                    'expired' => 'secondary',
                                    'cancelled' => 'danger',
                                    'pending' => 'warning',
                                    default => 'secondary'
                                };
                                ?>
                                <span class="badge bg-<?= $statusClass ?>"><?= ucfirst($sub['status']) ?></span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Payment History -->
    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="mb-0"><i class="bi bi-receipt me-2"></i>Payment History</h5>
        </div>
        <div class="card-body p-0">
            <?php if (empty($paymentHistory)): ?>
            <div class="text-center py-4 text-muted">
                <i class="bi bi-wallet2 fs-1 mb-2"></i>
                <p>No payment history found</p>
            </div>
            <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Transaction ID</th>
                            <th>Plan</th>
                            <th>Amount</th>
                            <th>Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($paymentHistory as $payment): ?>
                        <tr>
                            <td>
                                <small class="text-muted"><?= htmlspecialchars($payment['razorpay_order_id'] ?? $payment['id']) ?></small>
                            </td>
                            <td><?= htmlspecialchars($payment['plan_name']) ?></td>
                            <td><strong>₹<?= number_format($payment['amount'], 2) ?></strong></td>
                            <td><?= date('d M Y, h:i A', strtotime($payment['created_at'])) ?></td>
                            <td>
                                <?php
                                $statusClass = match($payment['status']) {
                                    'completed' => 'success',
                                    'pending' => 'warning',
                                    'failed' => 'danger',
                                    'refunded' => 'info',
                                    default => 'secondary'
                                };
                                $statusIcon = match($payment['status']) {
                                    'completed' => 'check-circle-fill',
                                    'pending' => 'clock',
                                    'failed' => 'x-circle-fill',
                                    'refunded' => 'arrow-counterclockwise',
                                    default => 'circle'
                                };
                                ?>
                                <span class="badge bg-<?= $statusClass ?>">
                                    <i class="bi bi-<?= $statusIcon ?> me-1"></i>
                                    <?= ucfirst($payment['status']) ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
