<style>
    /* Pending Pandits - Mobile Card Layout */
    .pending-pandits-cards {
        display: none;
    }

    @media (max-width: 768px) {
        .pending-pandits-table {
            display: none !important;
        }

        .pending-pandits-cards {
            display: block;
        }

        .pandit-card-item {
            background: #fff;
            border: 1px solid #E5E7EB;
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 14px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
        }

        .pandit-card-item:last-child {
            margin-bottom: 0;
        }

        .pandit-card-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 12px;
        }

        .pandit-card-avatar {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: linear-gradient(135deg, #6366F1, #8B5CF6);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-weight: 600;
            font-size: 1.1rem;
            flex-shrink: 0;
        }

        .pandit-card-name {
            font-weight: 600;
            font-size: 1rem;
            color: #1E1E2E;
        }

        .pandit-card-email {
            font-size: 0.8rem;
            color: #6B7280;
            word-break: break-all;
        }

        .pandit-card-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-bottom: 14px;
            padding: 12px;
            background: #F9FAFB;
            border-radius: 8px;
        }

        .pandit-card-detail-item {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .pandit-card-detail-label {
            font-size: 0.7rem;
            text-transform: uppercase;
            color: #9CA3AF;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .pandit-card-detail-value {
            font-size: 0.88rem;
            color: #374151;
            font-weight: 500;
        }

        .pandit-card-bio {
            background: #F9FAFB;
            border-radius: 8px;
            padding: 10px 12px;
            margin-bottom: 14px;
            font-size: 0.85rem;
            color: #4B5563;
            line-height: 1.5;
        }

        .pandit-card-bio strong {
            color: #374151;
        }

        .pandit-card-actions {
            display: flex;
            gap: 10px;
        }

        .pandit-card-actions form {
            flex: 1;
        }

        .pandit-card-actions .btn {
            width: 100%;
            justify-content: center;
            padding: 10px 14px;
            font-size: 0.9rem;
        }
    }
</style>

<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-user-check"></i> Pending Pandit Approvals</h3>
    </div>

    <?php if (empty($pandits)): ?>
        <div style="text-align: center; padding: 50px; color: #6B7280;">
            <i class="fas fa-check-circle" style="font-size: 3rem; margin-bottom: 20px; color: #10B981;"></i>
            <p>No pending pandit applications!</p>
        </div>
    <?php else: ?>
        <!-- Desktop Table View -->
        <div class="pending-pandits-table">
            <table class="table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Specialization</th>
                        <th>Experience</th>
                        <th>Applied</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pandits as $pandit): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($pandit['name']) ?></strong></td>
                            <td><?= htmlspecialchars($pandit['email']) ?></td>
                            <td><?= htmlspecialchars($pandit['specialization'] ?? 'N/A') ?></td>
                            <td><?= $pandit['experience_years'] ?? 0 ?> years</td>
                            <td><?= date('M d, Y', strtotime($pandit['profile_created_at'] ?? $pandit['created_at'])) ?></td>
                            <td>
                                <form method="POST" action="/admin/pandits/<?= $pandit['id'] ?>/approve"
                                    style="display: inline;">
                                    <?= \App\Core\Auth::csrfField() ?>
                                    <button type="submit" class="btn btn-sm btn-success">
                                        <i class="fas fa-check"></i> Approve
                                    </button>
                                </form>
                                <form method="POST" action="/admin/pandits/<?= $pandit['id'] ?>/reject"
                                    style="display: inline;">
                                    <?= \App\Core\Auth::csrfField() ?>
                                    <input type="hidden" name="reason" value="Application does not meet requirements.">
                                    <button type="submit" class="btn btn-sm btn-danger"
                                        onclick="return confirm('Reject this application?')">
                                        <i class="fas fa-times"></i> Reject
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php if (!empty($pandit['bio'])): ?>
                            <tr>
                                <td colspan="6" style="background: #F9FAFB; padding: 15px;">
                                    <strong>Bio:</strong> <?= htmlspecialchars($pandit['bio']) ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Mobile Card View -->
        <div class="pending-pandits-cards">
            <?php foreach ($pandits as $pandit): ?>
                <div class="pandit-card-item">
                    <div class="pandit-card-header">
                        <div class="pandit-card-avatar">
                            <?= strtoupper(substr($pandit['name'], 0, 1)) ?>
                        </div>
                        <div>
                            <div class="pandit-card-name"><?= htmlspecialchars($pandit['name']) ?></div>
                            <div class="pandit-card-email"><?= htmlspecialchars($pandit['email']) ?></div>
                        </div>
                    </div>

                    <div class="pandit-card-details">
                        <div class="pandit-card-detail-item">
                            <span class="pandit-card-detail-label">Specialization</span>
                            <span
                                class="pandit-card-detail-value"><?= htmlspecialchars($pandit['specialization'] ?? 'N/A') ?></span>
                        </div>
                        <div class="pandit-card-detail-item">
                            <span class="pandit-card-detail-label">Experience</span>
                            <span class="pandit-card-detail-value"><?= $pandit['experience_years'] ?? 0 ?> years</span>
                        </div>
                        <div class="pandit-card-detail-item">
                            <span class="pandit-card-detail-label">Applied</span>
                            <span
                                class="pandit-card-detail-value"><?= date('M d, Y', strtotime($pandit['profile_created_at'] ?? $pandit['created_at'])) ?></span>
                        </div>
                    </div>

                    <?php if (!empty($pandit['bio'])): ?>
                        <div class="pandit-card-bio">
                            <strong>Bio:</strong> <?= htmlspecialchars($pandit['bio']) ?>
                        </div>
                    <?php endif; ?>

                    <div class="pandit-card-actions">
                        <form method="POST" action="/admin/pandits/<?= $pandit['id'] ?>/approve">
                            <?= \App\Core\Auth::csrfField() ?>
                            <button type="submit" class="btn btn-sm btn-success">
                                <i class="fas fa-check"></i> Approve
                            </button>
                        </form>
                        <form method="POST" action="/admin/pandits/<?= $pandit['id'] ?>/reject">
                            <?= \App\Core\Auth::csrfField() ?>
                            <input type="hidden" name="reason" value="Application does not meet requirements.">
                            <button type="submit" class="btn btn-sm btn-danger"
                                onclick="return confirm('Reject this application?')">
                                <i class="fas fa-times"></i> Reject
                            </button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>