<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-moon"></i> My Muhurat Requests</h3>
        <a href="/user/mohurat-requests/create" class="btn btn-sm btn-primary">
            <i class="fas fa-plus"></i> New Request
        </a>
    </div>

    <?php if (empty($requests)): ?>
        <div style="text-align: center; padding: 50px; color: #6B7280;">
            <div style="width: 80px; height: 80px; background: linear-gradient(135deg, rgba(99,102,241,0.1), rgba(168,85,247,0.1)); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; font-size: 2rem; color: #6366F1;">
                <i class="fas fa-moon"></i>
            </div>
            <h4 style="margin-bottom: 10px; color: #374151;">No Muhurat Requests Yet</h4>
            <p>Request an auspicious muhurat from our expert pandits for your upcoming ritual or ceremony.</p>
            <a href="/user/mohurat-requests/create" class="btn btn-primary" style="margin-top: 15px;">
                <i class="fas fa-plus"></i> Request Muhurat
            </a>
        </div>
    <?php else: ?>
        <div style="display: flex; flex-direction: column; gap: 15px;">
            <?php foreach ($requests as $req): ?>
            <div style="border: 1px solid #E5E7EB; border-radius: 12px; padding: 20px; transition: all 0.3s;
                <?= $req['status'] === 'pending' ? 'border-left: 4px solid #F59E0B;' :
                    ($req['status'] === 'replied' ? 'border-left: 4px solid #6366F1;' :
                    ($req['status'] === 'accepted' ? 'border-left: 4px solid #10B981;' :
                    'border-left: 4px solid #9CA3AF;')) ?>">

                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 12px; flex-wrap: wrap; gap: 10px;">
                    <div>
                        <h4 style="margin: 0 0 5px 0; color: #1E1E2E;">
                            <i class="fas fa-om" style="color: var(--primary);"></i>
                            <?= htmlspecialchars($req['ritual_type']) ?>
                        </h4>
                        <span style="font-size: 0.85rem; color: #6B7280;">
                            <i class="fas fa-clock"></i> <?= date('M d, Y h:i A', strtotime($req['created_at'])) ?>
                        </span>
                        <?php if (!empty($req['pandit_name'])): ?>
                        <span style="font-size: 0.85rem; color: #6B7280; display: block; margin-top: 3px;">
                            <i class="fas fa-pray" style="color: var(--primary);"></i> Sent to: <strong><?= htmlspecialchars($req['pandit_name']) ?></strong>
                            <?php if (!empty($req['pandit_specialization'])): ?> · <?= htmlspecialchars($req['pandit_specialization']) ?><?php endif; ?>
                        </span>
                        <?php endif; ?>
                    </div>
                    <span class="badge badge-<?= $req['status'] === 'accepted' ? 'success' : ($req['status'] === 'replied' ? 'info' : ($req['status'] === 'declined' ? 'danger' : 'warning')) ?>">
                        <?= ucfirst($req['status']) ?>
                    </span>
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 12px; font-size: 0.9rem; color: #6B7280; margin-bottom: 12px;">
                    <?php if (!empty($req['city'])): ?>
                    <div><i class="fas fa-map-marker-alt" style="color: var(--primary);"></i> <?= htmlspecialchars($req['city']) ?>, <?= htmlspecialchars($req['country'] ?? 'India') ?></div>
                    <?php endif; ?>
                    <?php if (!empty($req['preferred_month'])): ?>
                    <div><i class="fas fa-calendar" style="color: var(--primary);"></i> <?= htmlspecialchars($req['preferred_month']) ?></div>
                    <?php endif; ?>
                    <?php if (!empty($req['gotra'])): ?>
                    <div><i class="fas fa-seedling" style="color: var(--primary);"></i> Gotra: <?= htmlspecialchars($req['gotra']) ?></div>
                    <?php endif; ?>
                    <?php if (!empty($req['nakshatra'])): ?>
                    <div><i class="fas fa-star" style="color: var(--primary);"></i> <?= htmlspecialchars($req['nakshatra']) ?></div>
                    <?php endif; ?>
                    <div>
                        <i class="fas fa-sun" style="color: var(--primary);"></i>
                        <?= $req['time_preference'] === 'morning' ? 'Morning' : ($req['time_preference'] === 'evening' ? 'Evening' : 'Any Time') ?>
                    </div>
                </div>

                <?php if ($req['status'] === 'replied'): ?>
                <!-- Pandit's Reply -->
                <div style="background: linear-gradient(135deg, rgba(99,102,241,0.06), rgba(168,85,247,0.06)); padding: 18px; border-radius: 10px; margin-top: 12px;">
                    <h5 style="margin: 0 0 12px 0; color: #4338CA; display: flex; align-items: center; gap: 8px;">
                        <i class="fas fa-check-circle"></i> Pandit's Muhurat Reply
                    </h5>
                    <?php if (!empty($req['pandit_name'])): ?>
                    <p style="margin: 0 0 10px 0; font-size: 0.9rem; color: #6B7280;">
                        <i class="fas fa-user"></i> <strong><?= htmlspecialchars($req['pandit_name']) ?></strong>
                        <?php if (!empty($req['pandit_specialization'])): ?>
                            · <?= htmlspecialchars($req['pandit_specialization']) ?>
                        <?php endif; ?>
                        <?php if (!empty($req['pandit_rating']) && $req['pandit_rating'] > 0): ?>
                            · <i class="fas fa-star" style="color: #F59E0B;"></i> <?= number_format($req['pandit_rating'], 1) ?>
                        <?php endif; ?>
                    </p>
                    <?php endif; ?>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 12px;">
                        <div style="background: white; padding: 12px; border-radius: 8px; text-align: center;">
                            <div style="font-size: 0.75rem; color: #6B7280; text-transform: uppercase; letter-spacing: 0.5px;">Date</div>
                            <div style="font-size: 1.1rem; font-weight: 600; color: #1E1E2E;"><?= date('M d, Y', strtotime($req['reply_date'])) ?></div>
                        </div>
                        <div style="background: white; padding: 12px; border-radius: 8px; text-align: center;">
                            <div style="font-size: 0.75rem; color: #6B7280; text-transform: uppercase; letter-spacing: 0.5px;">Muhurat Time</div>
                            <div style="font-size: 1.1rem; font-weight: 600; color: #4338CA;"><?= date('h:i A', strtotime($req['reply_time'])) ?></div>
                        </div>
                    </div>

                    <?php if (!empty($req['reply_explanation'])): ?>
                    <div style="background: white; padding: 12px; border-radius: 8px; margin-bottom: 12px;">
                        <div style="font-size: 0.75rem; color: #6B7280; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 5px;">Explanation</div>
                        <p style="margin: 0; color: #374151; line-height: 1.6;"><?= nl2br(htmlspecialchars($req['reply_explanation'])) ?></p>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($req['consultation_fee']) && $req['consultation_fee'] > 0): ?>
                    <div style="background: white; padding: 10px 12px; border-radius: 8px; display: inline-flex; align-items: center; gap: 8px;">
                        <i class="fas fa-rupee-sign" style="color: #10B981;"></i>
                        <span style="font-weight: 600;">₹<?= number_format($req['consultation_fee'], 2) ?></span>
                        <span style="color: #6B7280; font-size: 0.85rem;">Consultation Fee</span>
                    </div>
                    <?php endif; ?>

                    <!-- Accept / Decline Buttons -->
                    <div style="display: flex; gap: 10px; margin-top: 15px; flex-wrap: wrap;">
                        <form method="POST" action="/user/mohurat-requests/<?= $req['id'] ?>/accept" style="display: inline;">
                            <?= \App\Core\Auth::csrfField() ?>
                            <button type="submit" class="btn btn-success btn-sm">
                                <i class="fas fa-check"></i> Accept & Book
                            </button>
                        </form>
                        <form method="POST" action="/user/mohurat-requests/<?= $req['id'] ?>/decline" style="display: inline;"
                            onsubmit="return confirm('Are you sure you want to decline this muhurat?')">
                            <?= \App\Core\Auth::csrfField() ?>
                            <button type="submit" class="btn btn-sm" style="background: #FEE2E2; color: #991B1B;">
                                <i class="fas fa-times"></i> Decline
                            </button>
                        </form>
                    </div>
                </div>
                <?php endif; ?>

                <?php if ($req['status'] === 'accepted'): ?>
                <div style="background: #D1FAE5; padding: 12px 15px; border-radius: 8px; margin-top: 12px;">
                    <i class="fas fa-check-circle" style="color: #065F46;"></i>
                    <strong style="color: #065F46;">Accepted!</strong>
                    <span style="color: #065F46;"> Muhurat on <?= date('M d, Y', strtotime($req['reply_date'])) ?> at <?= date('h:i A', strtotime($req['reply_time'])) ?>.
                    <a href="/user/bookings" style="color: #065F46; font-weight: 600;">View Booking →</a></span>
                </div>
                <?php endif; ?>

                <?php if ($req['status'] === 'declined'): ?>
                <div style="background: #FEE2E2; padding: 12px 15px; border-radius: 8px; margin-top: 12px;">
                    <i class="fas fa-times-circle" style="color: #991B1B;"></i>
                    <span style="color: #991B1B;">You declined this muhurat suggestion.</span>
                </div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
