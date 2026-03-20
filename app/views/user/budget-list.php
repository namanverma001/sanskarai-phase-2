<?php
/**
 * User Budget List - Ritual Budget Planner
 * Displays all budgets belonging to the authenticated user.
 */
?>

<div class="card">
    <div class="card-header">
        <h2 class="card-title"><i class="fas fa-wallet"></i> My Ritual Budgets</h2>
        <a href="/user/budgets/create" class="btn btn-primary">
            <i class="fas fa-plus"></i> Plan New Budget
        </a>
    </div>

    <?php if (empty($budgets)): ?>
        <div style="text-align: center; padding: 60px 20px;">
            <div style="font-size: 4rem; margin-bottom: 20px; opacity: 0.3;">
                <i class="fas fa-wallet"></i>
            </div>
            <h3 style="color: #6B7280; margin-bottom: 10px;">No Budgets Yet</h3>
            <p style="color: #9CA3AF; margin-bottom: 25px;">
                Plan your ritual expenses with AI-generated category-wise cost estimates.
            </p>
            <a href="/user/budgets/create" class="btn btn-primary">
                <i class="fas fa-magic"></i> Plan Your First Budget
            </a>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table budget-table">
                <thead>
                    <tr>
                        <th>Ritual Type</th>
                        <th>Tier</th>
                        <th>Guests</th>
                        <th>Total Estimated</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($budgets as $budget): ?>
                        <tr>
                            <td data-label="Ritual">
                                <div class="budget-ritual-name">
                                    <i class="fas fa-om" style="color: var(--primary); margin-right: 8px;"></i>
                                    <?= htmlspecialchars($budget['ritual_type']) ?>
                                </div>
                                <?php if (!empty($budget['location'])): ?>
                                    <div class="budget-location">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <?= htmlspecialchars($budget['location']) ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td data-label="Tier">
                                <?php
                                $tierColors = [
                                    'basic'    => ['bg' => '#D1FAE5', 'color' => '#065F46', 'icon' => 'fa-seedling'],
                                    'standard' => ['bg' => '#DBEAFE', 'color' => '#1E40AF', 'icon' => 'fa-star-half-alt'],
                                    'premium'  => ['bg' => '#FEF3C7', 'color' => '#92400E', 'icon' => 'fa-crown'],
                                ];
                                $tier = strtolower($budget['tier'] ?? 'standard');
                                $tc = $tierColors[$tier] ?? $tierColors['standard'];
                                ?>
                                <span class="tier-badge" style="background: <?= $tc['bg'] ?>; color: <?= $tc['color'] ?>;">
                                    <i class="fas <?= $tc['icon'] ?>"></i>
                                    <?= ucfirst($tier) ?>
                                </span>
                            </td>
                            <td data-label="Guests">
                                <span style="color: #374151;">
                                    <i class="fas fa-users" style="color: #9CA3AF; margin-right: 4px;"></i>
                                    <?= number_format((int)$budget['guest_count']) ?>
                                </span>
                            </td>
                            <td data-label="Total Estimated">
                                <span class="budget-amount">
                                    ₹<?= number_format((float)$budget['total_estimated'], 2) ?>
                                </span>
                            </td>
                            <td data-label="Created">
                                <span style="color: #6B7280; font-size: 0.88rem;">
                                    <i class="fas fa-calendar-alt" style="margin-right: 4px;"></i>
                                    <?= date('M j, Y', strtotime($budget['created_at'])) ?>
                                </span>
                            </td>
                            <td data-label="Actions">
                                <div class="budget-actions">
                                    <a href="/user/budgets/<?= (int)$budget['id'] ?>" class="btn btn-primary btn-sm">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    <form method="POST"
                                          action="/user/budgets/<?= (int)$budget['id'] ?>/delete"
                                          onsubmit="return confirm('Delete this budget and all its items? This cannot be undone.')"
                                          style="margin: 0;">
                                        <?= \App\Core\Auth::csrfField() ?>
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<style>
    .budget-table th {
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #6B7280;
    }

    .budget-ritual-name {
        font-weight: 600;
        color: var(--dark);
        font-size: 0.95rem;
    }

    .budget-location {
        font-size: 0.8rem;
        color: #9CA3AF;
        margin-top: 3px;
    }

    .budget-location i {
        margin-right: 4px;
        color: #D1D5DB;
    }

    .tier-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.78rem;
        font-weight: 600;
        text-transform: capitalize;
        letter-spacing: 0.3px;
    }

    .budget-amount {
        font-weight: 700;
        font-size: 1rem;
        color: var(--primary);
    }

    .budget-actions {
        display: flex;
        gap: 8px;
        align-items: center;
    }

    @media (max-width: 768px) {
        .budget-table thead {
            display: none;
        }

        .budget-table tr {
            display: block;
            background: white;
            border: 1px solid #E5E7EB;
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 12px;
        }

        .budget-table td {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 6px 0;
            border-bottom: none;
            font-size: 0.9rem;
        }

        .budget-table td::before {
            content: attr(data-label);
            font-weight: 600;
            color: #6B7280;
            font-size: 0.78rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            flex-shrink: 0;
            margin-right: 12px;
        }

        .budget-actions {
            justify-content: flex-end;
        }
    }
</style>
