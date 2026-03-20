<?php
/**
 * Budget Detail — Ritual Budget Planner
 * Displays category-wise items with inline editing, actual tracking,
 * custom expense addition, vendor suggestions, and PDF download.
 *
 * Variables available:
 *   $budget  — array: id, ritual_type, location, tier, guest_count,
 *                     total_estimated, total_actual, items[]
 *   $vendors — array: ['success' => bool, 'data' => array keyed by category]
 */

// Group items by category
$itemsByCategory = [];
foreach ($budget['items'] ?? [] as $item) {
    $itemsByCategory[$item['category']][] = $item;
}

// Tier display config
$tierColors = [
    'basic'    => ['bg' => '#D1FAE5', 'color' => '#065F46', 'icon' => 'fa-seedling',    'label' => 'Basic'],
    'standard' => ['bg' => '#DBEAFE', 'color' => '#1E40AF', 'icon' => 'fa-star-half-alt','label' => 'Standard'],
    'premium'  => ['bg' => '#FEF3C7', 'color' => '#92400E', 'icon' => 'fa-crown',        'label' => 'Premium'],
];
$tier = strtolower($budget['tier'] ?? 'standard');
$tc   = $tierColors[$tier] ?? $tierColors['standard'];

// Vendor data
$vendorData = ($vendors['success'] ?? false) ? ($vendors['data'] ?? []) : [];
?>

<!-- ─── Back link ─────────────────────────────────────────────────────────── -->
<div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:20px; flex-wrap:wrap; gap:12px;">
    <a href="/user/budgets" class="btn btn-sm" style="background:#E5E7EB; color:#374151;">
        <i class="fas fa-arrow-left"></i> Back to My Budgets
    </a>
    <div style="display:flex; gap:10px; flex-wrap:wrap;">
        <a href="/user/budgets/<?= (int)$budget['id'] ?>/pdf" target="_blank"
           class="btn btn-sm" style="background:linear-gradient(135deg,#EF4444,#DC2626); color:#fff;">
            <i class="fas fa-file-pdf"></i> Download PDF
        </a>
        <form method="POST" action="/user/budgets/<?= (int)$budget['id'] ?>/delete"
              onsubmit="return confirm('Delete this budget and all its items? This cannot be undone.')"
              style="margin:0;">
            <?= \App\Core\Auth::csrfField() ?>
            <button type="submit" class="btn btn-sm btn-danger">
                <i class="fas fa-trash"></i> Delete Budget
            </button>
        </form>
    </div>
</div>

<!-- ─── Budget Header ─────────────────────────────────────────────────────── -->
<div class="budget-hero">
    <div class="budget-hero-left">
        <div class="budget-hero-icon">
            <i class="fas fa-om"></i>
        </div>
        <div>
            <h1 class="budget-hero-title"><?= htmlspecialchars($budget['ritual_type']) ?></h1>
            <div class="budget-hero-meta">
                <span><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($budget['location']) ?></span>
                <span><i class="fas fa-users"></i> <?= number_format((int)$budget['guest_count']) ?> Guests</span>
                <span class="tier-badge-hero" style="background:<?= $tc['bg'] ?>; color:<?= $tc['color'] ?>;">
                    <i class="fas <?= $tc['icon'] ?>"></i> <?= $tc['label'] ?>
                </span>
            </div>
        </div>
    </div>
    <div class="budget-hero-totals">
        <div class="hero-total-card">
            <div class="hero-total-label">Total Estimated</div>
            <div class="hero-total-value" id="grandTotalEstimated">
                ₹<?= number_format((float)$budget['total_estimated'], 2) ?>
            </div>
        </div>
        <div class="hero-total-card">
            <div class="hero-total-label">Total Actual</div>
            <div class="hero-total-value" id="grandTotalActual" style="color:#10B981;">
                ₹<?= number_format((float)$budget['total_actual'], 2) ?>
            </div>
        </div>
        <div class="hero-total-card">
            <div class="hero-total-label">Variance</div>
            <?php
            $grandVariance = (float)$budget['total_actual'] - (float)$budget['total_estimated'];
            $varClass = $grandVariance > 0 ? 'variance-over' : ($grandVariance < 0 ? 'variance-under' : 'variance-zero');
            $varSign  = $grandVariance > 0 ? '+' : '';
            ?>
            <div class="hero-total-value <?= $varClass ?>" id="grandTotalVariance">
                <?= $varSign ?>₹<?= number_format(abs($grandVariance), 2) ?>
            </div>
        </div>
    </div>
</div>

<!-- ─── Toast notification ────────────────────────────────────────────────── -->
<div id="budgetToast" class="budget-toast" role="alert" aria-live="polite"></div>

<!-- ─── Category Tables ───────────────────────────────────────────────────── -->
<?php if (empty($itemsByCategory)): ?>
    <div class="card" style="text-align:center; padding:50px 20px; color:#9CA3AF;">
        <div style="font-size:3rem; margin-bottom:16px; opacity:0.3;"><i class="fas fa-receipt"></i></div>
        <p>No budget items found.</p>
    </div>
<?php else: ?>
    <?php foreach ($itemsByCategory as $category => $items): ?>
        <?php
        $catEstimated = array_sum(array_column($items, 'estimated_amount'));
        $catActual    = array_sum(array_filter(array_column($items, 'actual_amount'), fn($v) => $v !== null));
        $catVariance  = $catActual - $catEstimated;
        $catVarClass  = $catVariance > 0 ? 'variance-over' : ($catVariance < 0 ? 'variance-under' : 'variance-zero');
        $catVarSign   = $catVariance > 0 ? '+' : '';
        $catSlug      = preg_replace('/[^a-z0-9]+/', '-', strtolower($category));
        ?>
        <div class="budget-category-card" id="cat-<?= $catSlug ?>">
            <div class="category-header">
                <div class="category-title">
                    <span class="category-dot"></span>
                    <?= htmlspecialchars($category) ?>
                    <span class="category-count"><?= count($items) ?> item<?= count($items) !== 1 ? 's' : '' ?></span>
                </div>
                <div class="category-summary">
                    <span class="cat-summary-chip">
                        Est: <strong id="cat-est-<?= $catSlug ?>">₹<?= number_format($catEstimated, 2) ?></strong>
                    </span>
                    <span class="cat-summary-chip">
                        Act: <strong id="cat-act-<?= $catSlug ?>">₹<?= number_format($catActual, 2) ?></strong>
                    </span>
                    <span class="cat-summary-chip <?= $catVarClass ?>" id="cat-var-<?= $catSlug ?>">
                        <?= $catVarSign ?>₹<?= number_format(abs($catVariance), 2) ?>
                    </span>
                </div>
            </div>

            <div class="table-responsive">
                <table class="budget-items-table" id="table-<?= $catSlug ?>">
                    <thead>
                        <tr>
                            <th style="width:40%;">Item</th>
                            <th style="width:18%; text-align:right;">Estimated (₹)</th>
                            <th style="width:18%; text-align:right;">Actual (₹)</th>
                            <th style="width:16%; text-align:right;">Variance</th>
                            <th style="width:8%; text-align:center;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item): ?>
                            <?php
                            $itemVariance = ($item['actual_amount'] !== null)
                                ? ((float)$item['actual_amount'] - (float)$item['estimated_amount'])
                                : null;
                            $ivClass = $itemVariance === null ? '' : ($itemVariance > 0 ? 'variance-over' : ($itemVariance < 0 ? 'variance-under' : 'variance-zero'));
                            $ivSign  = ($itemVariance !== null && $itemVariance > 0) ? '+' : '';
                            ?>
                            <tr id="item-row-<?= (int)$item['id'] ?>" class="item-row<?= $item['is_custom'] ? ' custom-item-row' : '' ?>">
                                <td>
                                    <span class="item-name-text">
                                        <?= htmlspecialchars($item['item_name']) ?>
                                    </span>
                                    <?php if ($item['is_custom']): ?>
                                        <span class="custom-badge">Custom</span>
                                    <?php endif; ?>
                                    <?php if (!empty($item['notes'])): ?>
                                        <span class="item-notes" title="<?= htmlspecialchars($item['notes']) ?>">
                                            <i class="fas fa-info-circle"></i>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td style="text-align:right;">
                                    <div class="inline-edit-wrap">
                                        <span class="amount-display" id="est-display-<?= (int)$item['id'] ?>"
                                              onclick="startEdit(<?= (int)$item['id'] ?>, 'estimated')">
                                            ₹<?= number_format((float)$item['estimated_amount'], 2) ?>
                                            <i class="fas fa-pencil-alt edit-pencil"></i>
                                        </span>
                                        <div class="inline-edit-form" id="est-form-<?= (int)$item['id'] ?>" style="display:none;">
                                            <input type="number" step="0.01" min="0"
                                                   class="inline-input"
                                                   id="est-input-<?= (int)$item['id'] ?>"
                                                   value="<?= number_format((float)$item['estimated_amount'], 2, '.', '') ?>"
                                                   onkeydown="handleEditKey(event, <?= (int)$item['id'] ?>, 'estimated')"
                                                   onblur="cancelEdit(<?= (int)$item['id'] ?>, 'estimated')">
                                            <button class="inline-save-btn" type="button"
                                                    onmousedown="saveEdit(<?= (int)$item['id'] ?>, 'estimated')">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <button class="inline-cancel-btn" type="button"
                                                    onmousedown="cancelEdit(<?= (int)$item['id'] ?>, 'estimated')">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                </td>
                                <td style="text-align:right;">
                                    <div class="inline-edit-wrap">
                                        <span class="amount-display" id="act-display-<?= (int)$item['id'] ?>"
                                              onclick="startEdit(<?= (int)$item['id'] ?>, 'actual')">
                                            <?php if ($item['actual_amount'] !== null): ?>
                                                ₹<?= number_format((float)$item['actual_amount'], 2) ?>
                                            <?php else: ?>
                                                <span class="no-actual">— Add</span>
                                            <?php endif; ?>
                                            <i class="fas fa-pencil-alt edit-pencil"></i>
                                        </span>
                                        <div class="inline-edit-form" id="act-form-<?= (int)$item['id'] ?>" style="display:none;">
                                            <input type="number" step="0.01" min="0"
                                                   class="inline-input"
                                                   id="act-input-<?= (int)$item['id'] ?>"
                                                   value="<?= $item['actual_amount'] !== null ? number_format((float)$item['actual_amount'], 2, '.', '') : '' ?>"
                                                   placeholder="0.00"
                                                   onkeydown="handleEditKey(event, <?= (int)$item['id'] ?>, 'actual')"
                                                   onblur="cancelEdit(<?= (int)$item['id'] ?>, 'actual')">
                                            <button class="inline-save-btn" type="button"
                                                    onmousedown="saveEdit(<?= (int)$item['id'] ?>, 'actual')">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <button class="inline-cancel-btn" type="button"
                                                    onmousedown="cancelEdit(<?= (int)$item['id'] ?>, 'actual')">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                </td>
                                <td style="text-align:right;">
                                    <span class="variance-cell <?= $ivClass ?>" id="var-<?= (int)$item['id'] ?>">
                                        <?php if ($itemVariance !== null): ?>
                                            <?= $ivSign ?>₹<?= number_format(abs($itemVariance), 2) ?>
                                        <?php else: ?>
                                            <span style="color:#D1D5DB;">—</span>
                                        <?php endif; ?>
                                    </span>
                                </td>
                                <td style="text-align:center;">
                                    <?php if ($item['is_custom']): ?>
                                        <button class="btn-icon-danger" type="button"
                                                onclick="deleteCustomItem(<?= (int)$item['id'] ?>)"
                                                title="Delete custom item">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    <?php else: ?>
                                        <span style="color:#E5E7EB; font-size:0.75rem;">AI</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr class="category-totals-row">
                            <td><strong>Category Total</strong></td>
                            <td style="text-align:right;">
                                <strong id="cat-est-foot-<?= $catSlug ?>">₹<?= number_format($catEstimated, 2) ?></strong>
                            </td>
                            <td style="text-align:right;">
                                <strong id="cat-act-foot-<?= $catSlug ?>">₹<?= number_format($catActual, 2) ?></strong>
                            </td>
                            <td style="text-align:right;">
                                <strong class="<?= $catVarClass ?>" id="cat-var-foot-<?= $catSlug ?>">
                                    <?= $catVarSign ?>₹<?= number_format(abs($catVariance), 2) ?>
                                </strong>
                            </td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Vendor Suggestions for this category (collapsible) -->
            <?php
            $catVendors = $vendorData[$category] ?? [];
            ?>
            <?php if (!empty($catVendors)): ?>
                <div class="vendor-section">
                    <button class="vendor-toggle" type="button"
                            onclick="toggleVendors('<?= $catSlug ?>')"
                            id="vendor-toggle-<?= $catSlug ?>">
                        <i class="fas fa-store"></i>
                        Vendor Suggestions for <?= htmlspecialchars($category) ?>
                        <span class="vendor-count-badge"><?= count($catVendors) ?></span>
                        <i class="fas fa-chevron-down vendor-chevron" id="vendor-chevron-<?= $catSlug ?>"></i>
                    </button>
                    <div class="vendor-list" id="vendor-list-<?= $catSlug ?>" style="display:none;">
                        <div class="vendor-grid">
                            <?php foreach ($catVendors as $vendor): ?>
                                <div class="vendor-card">
                                    <div class="vendor-card-header">
                                        <div class="vendor-avatar">
                                            <?= strtoupper(substr($vendor['name'] ?? 'V', 0, 1)) ?>
                                        </div>
                                        <div>
                                            <div class="vendor-name"><?= htmlspecialchars($vendor['name'] ?? '') ?></div>
                                            <div class="vendor-category-tag"><?= htmlspecialchars($vendor['category'] ?? '') ?></div>
                                        </div>
                                    </div>
                                    <div class="vendor-meta">
                                        <span><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($vendor['city'] ?? '') ?></span>
                                        <?php if (!empty($vendor['min_price']) || !empty($vendor['max_price'])): ?>
                                            <span><i class="fas fa-rupee-sign"></i> ₹<?= number_format((float)($vendor['min_price'] ?? 0)) ?> – ₹<?= number_format((float)($vendor['max_price'] ?? 0)) ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<!-- ─── Grand Totals Row ──────────────────────────────────────────────────── -->
<div class="grand-totals-bar">
    <div class="grand-total-item">
        <span class="grand-total-label"><i class="fas fa-calculator"></i> Grand Total Estimated</span>
        <span class="grand-total-value" id="footerTotalEstimated">
            ₹<?= number_format((float)$budget['total_estimated'], 2) ?>
        </span>
    </div>
    <div class="grand-total-divider"></div>
    <div class="grand-total-item">
        <span class="grand-total-label"><i class="fas fa-receipt"></i> Grand Total Actual</span>
        <span class="grand-total-value" style="color:#10B981;" id="footerTotalActual">
            ₹<?= number_format((float)$budget['total_actual'], 2) ?>
        </span>
    </div>
    <div class="grand-total-divider"></div>
    <div class="grand-total-item">
        <span class="grand-total-label"><i class="fas fa-balance-scale"></i> Net Variance</span>
        <span class="grand-total-value <?= $varClass ?>" id="footerTotalVariance">
            <?= $varSign ?>₹<?= number_format(abs($grandVariance), 2) ?>
        </span>
    </div>
</div>

<!-- ─── Add Custom Expense ────────────────────────────────────────────────── -->
<div class="card custom-expense-card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-plus-circle"></i> Add Custom Expense</h3>
    </div>
    <form id="addCustomForm" onsubmit="addCustomItem(event)">
        <div class="custom-form-row">
            <div class="form-group" style="flex:2; margin-bottom:0;">
                <label for="customItemName">Expense Name <span style="color:#EF4444;">*</span></label>
                <input type="text" id="customItemName" class="form-control"
                       placeholder="e.g., Photography, Mehendi Artist, Tent…"
                       maxlength="255" required>
            </div>
            <div class="form-group" style="flex:1; margin-bottom:0;">
                <label for="customItemAmount">Estimated Amount (₹) <span style="color:#EF4444;">*</span></label>
                <input type="number" id="customItemAmount" class="form-control"
                       placeholder="0.00" step="0.01" min="0" required>
            </div>
            <div style="display:flex; align-items:flex-end; padding-bottom:1px;">
                <button type="submit" class="btn btn-primary" id="addCustomBtn">
                    <i class="fas fa-plus"></i> Add Expense
                </button>
            </div>
        </div>
        <div id="customFormError" class="form-error" style="display:none;"></div>
    </form>
</div>

<!-- ─── Vendor Suggestions (all categories, if no per-category vendors shown) -->
<?php if (!empty($vendorData)): ?>
    <div class="card" style="margin-top:0;">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-store-alt"></i> All Vendor Suggestions</h3>
            <span style="font-size:0.82rem; color:#9CA3AF;">Based on your location: <?= htmlspecialchars($budget['location']) ?></span>
        </div>
        <?php foreach ($vendorData as $cat => $vendors): ?>
            <?php if (!empty($vendors)): ?>
                <?php $allCatSlug = 'all-' . preg_replace('/[^a-z0-9]+/', '-', strtolower($cat)); ?>
                <div class="vendor-section" style="margin:0 0 8px;">
                    <button class="vendor-toggle" type="button"
                            onclick="toggleVendors('<?= $allCatSlug ?>')"
                            id="vendor-toggle-<?= $allCatSlug ?>">
                        <i class="fas fa-tag"></i>
                        <?= htmlspecialchars($cat) ?>
                        <span class="vendor-count-badge"><?= count($vendors) ?></span>
                        <i class="fas fa-chevron-down vendor-chevron" id="vendor-chevron-<?= $allCatSlug ?>"></i>
                    </button>
                    <div class="vendor-list" id="vendor-list-<?= $allCatSlug ?>" style="display:none;">
                        <div class="vendor-grid">
                            <?php foreach ($vendors as $vendor): ?>
                                <div class="vendor-card">
                                    <div class="vendor-card-header">
                                        <div class="vendor-avatar">
                                            <?= strtoupper(substr($vendor['name'] ?? 'V', 0, 1)) ?>
                                        </div>
                                        <div>
                                            <div class="vendor-name"><?= htmlspecialchars($vendor['name'] ?? '') ?></div>
                                            <div class="vendor-category-tag"><?= htmlspecialchars($vendor['category'] ?? '') ?></div>
                                        </div>
                                    </div>
                                    <div class="vendor-meta">
                                        <span><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($vendor['city'] ?? '') ?></span>
                                        <?php if (!empty($vendor['min_price']) || !empty($vendor['max_price'])): ?>
                                            <span><i class="fas fa-rupee-sign"></i> ₹<?= number_format((float)($vendor['min_price'] ?? 0)) ?> – ₹<?= number_format((float)($vendor['max_price'] ?? 0)) ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<!-- ─── Styles ────────────────────────────────────────────────────────────── -->
<style>
/* ── Hero Header ─────────────────────────────────────────────────────────── */
.budget-hero {
    background: linear-gradient(135deg, #1E1E2E 0%, #2D1B69 50%, #1A1A2E 100%);
    border-radius: 20px;
    padding: 32px 36px;
    color: white;
    margin-bottom: 28px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 24px;
    flex-wrap: wrap;
    box-shadow: 0 8px 32px rgba(30, 30, 46, 0.25);
}

.budget-hero-left {
    display: flex;
    align-items: center;
    gap: 20px;
}

.budget-hero-icon {
    width: 64px;
    height: 64px;
    background: linear-gradient(135deg, var(--primary), #A855F7);
    border-radius: 18px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.8rem;
    color: white;
    flex-shrink: 0;
    box-shadow: 0 4px 16px rgba(255, 107, 53, 0.4);
}

.budget-hero-title {
    font-size: 1.7rem;
    font-weight: 700;
    margin: 0 0 10px;
    color: white;
}

.budget-hero-meta {
    display: flex;
    align-items: center;
    gap: 16px;
    flex-wrap: wrap;
    font-size: 0.88rem;
    color: rgba(255, 255, 255, 0.75);
}

.budget-hero-meta span {
    display: flex;
    align-items: center;
    gap: 5px;
}

.tier-badge-hero {
    padding: 4px 14px;
    border-radius: 20px;
    font-size: 0.78rem;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 5px;
}

.budget-hero-totals {
    display: flex;
    gap: 16px;
    flex-wrap: wrap;
}

.hero-total-card {
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.12);
    border-radius: 14px;
    padding: 16px 22px;
    text-align: center;
    min-width: 130px;
    backdrop-filter: blur(4px);
}

.hero-total-label {
    font-size: 0.72rem;
    color: rgba(255, 255, 255, 0.55);
    text-transform: uppercase;
    letter-spacing: 0.6px;
    margin-bottom: 6px;
}

.hero-total-value {
    font-size: 1.25rem;
    font-weight: 700;
    color: white;
}

/* ── Variance colours ────────────────────────────────────────────────────── */
.variance-over  { color: #EF4444 !important; }
.variance-under { color: #10B981 !important; }
.variance-zero  { color: #9CA3AF !important; }

/* ── Toast ───────────────────────────────────────────────────────────────── */
.budget-toast {
    position: fixed;
    bottom: 28px;
    right: 28px;
    z-index: 9999;
    padding: 14px 22px;
    border-radius: 12px;
    font-size: 0.9rem;
    font-weight: 500;
    color: white;
    box-shadow: 0 8px 24px rgba(0,0,0,0.2);
    opacity: 0;
    transform: translateY(12px);
    transition: opacity 0.3s ease, transform 0.3s ease;
    pointer-events: none;
    max-width: 340px;
}

.budget-toast.show {
    opacity: 1;
    transform: translateY(0);
}

.budget-toast.toast-success { background: #10B981; }
.budget-toast.toast-error   { background: #EF4444; }

/* ── Category Card ───────────────────────────────────────────────────────── */
.budget-category-card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.06);
    margin-bottom: 20px;
    overflow: hidden;
    border: 1px solid #F3F4F6;
}

.category-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 18px 24px;
    background: #FAFAFA;
    border-bottom: 1px solid #F3F4F6;
    flex-wrap: wrap;
    gap: 10px;
}

.category-title {
    font-size: 1rem;
    font-weight: 700;
    color: #1E1E2E;
    display: flex;
    align-items: center;
    gap: 10px;
}

.category-dot {
    width: 10px;
    height: 10px;
    background: linear-gradient(135deg, var(--primary), #A855F7);
    border-radius: 50%;
    flex-shrink: 0;
}

.category-count {
    font-size: 0.75rem;
    color: #9CA3AF;
    font-weight: 400;
    background: #F3F4F6;
    padding: 2px 8px;
    border-radius: 10px;
}

.category-summary {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.cat-summary-chip {
    font-size: 0.78rem;
    padding: 4px 10px;
    background: #F3F4F6;
    border-radius: 8px;
    color: #374151;
}

/* ── Items Table ─────────────────────────────────────────────────────────── */
.budget-items-table {
    width: 100%;
    border-collapse: collapse;
}

.budget-items-table th {
    padding: 10px 16px;
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #6B7280;
    font-weight: 600;
    background: #F9FAFB;
    border-bottom: 1px solid #E5E7EB;
}

.budget-items-table td {
    padding: 13px 16px;
    border-bottom: 1px solid #F3F4F6;
    vertical-align: middle;
    font-size: 0.9rem;
    color: #374151;
}

.budget-items-table tbody tr:last-child td {
    border-bottom: none;
}

.budget-items-table tbody tr:hover {
    background: #FAFAFA;
}

.item-row.custom-item-row {
    background: #FFFBF5;
}

.item-row.custom-item-row:hover {
    background: #FEF3C7;
}

.item-name-text {
    font-weight: 500;
}

.custom-badge {
    display: inline-block;
    background: #FEF3C7;
    color: #92400E;
    font-size: 0.65rem;
    font-weight: 700;
    padding: 2px 7px;
    border-radius: 8px;
    margin-left: 6px;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

.item-notes {
    color: #9CA3AF;
    margin-left: 6px;
    cursor: help;
    font-size: 0.8rem;
}

/* ── Inline Edit ─────────────────────────────────────────────────────────── */
.inline-edit-wrap {
    display: inline-flex;
    align-items: center;
    justify-content: flex-end;
    gap: 4px;
}

.amount-display {
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 8px;
    border-radius: 6px;
    transition: background 0.15s;
    font-weight: 500;
}

.amount-display:hover {
    background: #EEF2FF;
    color: #4338CA;
}

.edit-pencil {
    font-size: 0.65rem;
    color: #C7D2FE;
    opacity: 0;
    transition: opacity 0.15s;
}

.amount-display:hover .edit-pencil {
    opacity: 1;
    color: #6366F1;
}

.no-actual {
    color: #D1D5DB;
    font-size: 0.82rem;
    font-style: italic;
}

.inline-edit-form {
    display: flex;
    align-items: center;
    gap: 4px;
}

.inline-input {
    width: 100px;
    padding: 5px 8px;
    border: 2px solid #6366F1;
    border-radius: 6px;
    font-size: 0.88rem;
    text-align: right;
    outline: none;
    color: #1E1E2E;
}

.inline-input:focus {
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
}

.inline-save-btn,
.inline-cancel-btn {
    width: 28px;
    height: 28px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    transition: background 0.15s;
}

.inline-save-btn   { background: #D1FAE5; color: #065F46; }
.inline-cancel-btn { background: #FEE2E2; color: #991B1B; }
.inline-save-btn:hover   { background: #10B981; color: white; }
.inline-cancel-btn:hover { background: #EF4444; color: white; }

/* ── Category Totals Row ─────────────────────────────────────────────────── */
.category-totals-row td {
    background: #F9FAFB;
    border-top: 2px solid #E5E7EB;
    padding: 12px 16px;
    font-size: 0.88rem;
    color: #374151;
}

/* ── Delete button ───────────────────────────────────────────────────────── */
.btn-icon-danger {
    width: 30px;
    height: 30px;
    background: #FEE2E2;
    color: #991B1B;
    border: none;
    border-radius: 7px;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    transition: background 0.15s, color 0.15s;
}

.btn-icon-danger:hover {
    background: #EF4444;
    color: white;
}

/* ── Grand Totals Bar ────────────────────────────────────────────────────── */
.grand-totals-bar {
    background: linear-gradient(135deg, #1E1E2E, #2D1B69);
    border-radius: 16px;
    padding: 24px 32px;
    display: flex;
    align-items: center;
    justify-content: space-around;
    gap: 20px;
    margin-bottom: 28px;
    flex-wrap: wrap;
    box-shadow: 0 6px 24px rgba(30, 30, 46, 0.2);
}

.grand-total-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 6px;
}

.grand-total-label {
    font-size: 0.75rem;
    color: rgba(255, 255, 255, 0.55);
    text-transform: uppercase;
    letter-spacing: 0.6px;
    display: flex;
    align-items: center;
    gap: 5px;
}

.grand-total-value {
    font-size: 1.4rem;
    font-weight: 700;
    color: white;
}

.grand-total-divider {
    width: 1px;
    height: 50px;
    background: rgba(255, 255, 255, 0.12);
}

/* ── Custom Expense Card ─────────────────────────────────────────────────── */
.custom-expense-card {
    margin-bottom: 28px;
}

.custom-form-row {
    display: flex;
    gap: 16px;
    align-items: flex-end;
    flex-wrap: wrap;
}

.form-error {
    color: #EF4444;
    font-size: 0.85rem;
    margin-top: 8px;
    padding: 8px 12px;
    background: #FEF2F2;
    border-radius: 8px;
    border-left: 3px solid #EF4444;
}

/* ── Vendor Section ──────────────────────────────────────────────────────── */
.vendor-section {
    border-top: 1px solid #F3F4F6;
    padding: 0;
}

.vendor-toggle {
    width: 100%;
    background: #F9FAFB;
    border: none;
    padding: 14px 24px;
    text-align: left;
    cursor: pointer;
    font-size: 0.88rem;
    font-weight: 600;
    color: #374151;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: background 0.15s;
}

.vendor-toggle:hover {
    background: #F3F4F6;
}

.vendor-toggle i:first-child {
    color: var(--primary);
}

.vendor-count-badge {
    background: var(--primary);
    color: white;
    font-size: 0.7rem;
    padding: 2px 7px;
    border-radius: 10px;
    font-weight: 700;
}

.vendor-chevron {
    margin-left: auto;
    transition: transform 0.25s ease;
    color: #9CA3AF;
}

.vendor-chevron.open {
    transform: rotate(180deg);
}

.vendor-list {
    padding: 16px 24px 20px;
    background: #FAFAFA;
}

.vendor-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    gap: 12px;
}

.vendor-card {
    background: white;
    border: 1px solid #E5E7EB;
    border-radius: 12px;
    padding: 14px 16px;
    transition: box-shadow 0.2s, border-color 0.2s;
}

.vendor-card:hover {
    box-shadow: 0 4px 14px rgba(0,0,0,0.08);
    border-color: #D1D5DB;
}

.vendor-card-header {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 10px;
}

.vendor-avatar {
    width: 36px;
    height: 36px;
    background: linear-gradient(135deg, var(--primary), #A855F7);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 700;
    font-size: 0.9rem;
    flex-shrink: 0;
}

.vendor-name {
    font-weight: 600;
    font-size: 0.88rem;
    color: #1E1E2E;
    line-height: 1.3;
}

.vendor-category-tag {
    font-size: 0.72rem;
    color: #9CA3AF;
    margin-top: 2px;
}

.vendor-meta {
    display: flex;
    flex-direction: column;
    gap: 4px;
    font-size: 0.78rem;
    color: #6B7280;
}

.vendor-meta span {
    display: flex;
    align-items: center;
    gap: 5px;
}

.vendor-meta i {
    color: #D1D5DB;
    width: 12px;
}

/* ── Saving spinner on cells ─────────────────────────────────────────────── */
.cell-saving {
    opacity: 0.5;
    pointer-events: none;
}

/* ── Responsive ──────────────────────────────────────────────────────────── */
@media (max-width: 900px) {
    .budget-hero {
        flex-direction: column;
        align-items: flex-start;
        padding: 24px 20px;
    }
    .budget-hero-totals {
        width: 100%;
        justify-content: space-between;
    }
    .hero-total-card {
        flex: 1;
        min-width: 90px;
    }
    .grand-totals-bar {
        padding: 20px 16px;
    }
    .grand-total-divider { display: none; }
    .grand-total-value { font-size: 1.1rem; }
}

@media (max-width: 640px) {
    .budget-items-table thead { display: none; }
    .budget-items-table tr {
        display: block;
        border-bottom: 1px solid #E5E7EB;
        padding: 12px 16px;
    }
    .budget-items-table td {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 6px 0;
        border-bottom: none;
        text-align: left !important;
    }
    .budget-items-table td::before {
        content: attr(data-label);
        font-size: 0.72rem;
        font-weight: 600;
        color: #9CA3AF;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        flex-shrink: 0;
        margin-right: 10px;
    }
    .inline-edit-wrap { justify-content: flex-end; }
    .custom-form-row { flex-direction: column; }
    .custom-form-row > div, .custom-form-row > button { width: 100%; }
}
</style>

<!-- ─── JavaScript ─────────────────────────────────────────────────────────── -->
<script>
(function () {
    'use strict';

    /* ── Constants ─────────────────────────────────────────────────────────── */
    const CSRF_TOKEN  = '<?= \App\Core\Auth::csrfToken() ?>';
    const BUDGET_ID   = <?= (int)$budget['id'] ?>;

    /* ── Item data map (estimated / actual per item id) ─────────────────────
       Populated from PHP so JS can recalculate without extra requests.       */
    const itemData = {};
    <?php foreach ($budget['items'] ?? [] as $item): ?>
    itemData[<?= (int)$item['id'] ?>] = {
        estimated: <?= json_encode((float)$item['estimated_amount']) ?>,
        actual:    <?= json_encode($item['actual_amount'] !== null ? (float)$item['actual_amount'] : null) ?>,
        category:  <?= json_encode($item['category']) ?>,
        catSlug:   <?= json_encode(preg_replace('/[^a-z0-9]+/', '-', strtolower($item['category']))) ?>
    };
    <?php endforeach; ?>

    /* ── Toast ──────────────────────────────────────────────────────────────── */
    let toastTimer = null;
    window.showToast = function (msg, type = 'success') {
        const el = document.getElementById('budgetToast');
        el.textContent = msg;
        el.className = 'budget-toast show toast-' + type;
        clearTimeout(toastTimer);
        toastTimer = setTimeout(() => {
            el.classList.remove('show');
        }, 3500);
    };

    /* ── Vendor toggle ──────────────────────────────────────────────────────── */
    window.toggleVendors = function (slug) {
        const list    = document.getElementById('vendor-list-' + slug);
        const chevron = document.getElementById('vendor-chevron-' + slug);
        if (!list) return;
        const isOpen = list.style.display !== 'none';
        list.style.display = isOpen ? 'none' : 'block';
        if (chevron) chevron.classList.toggle('open', !isOpen);
    };

    /* ── Inline edit helpers ────────────────────────────────────────────────── */
    window.startEdit = function (itemId, field) {
        const prefix = field === 'estimated' ? 'est' : 'act';
        document.getElementById(prefix + '-display-' + itemId).style.display = 'none';
        const form = document.getElementById(prefix + '-form-' + itemId);
        form.style.display = 'flex';
        const input = document.getElementById(prefix + '-input-' + itemId);
        input.focus();
        input.select();
    };

    window.cancelEdit = function (itemId, field) {
        const prefix = field === 'estimated' ? 'est' : 'act';
        document.getElementById(prefix + '-form-' + itemId).style.display = 'none';
        document.getElementById(prefix + '-display-' + itemId).style.display = '';
    };

    window.handleEditKey = function (e, itemId, field) {
        if (e.key === 'Enter') {
            e.preventDefault();
            saveEdit(itemId, field);
        } else if (e.key === 'Escape') {
            cancelEdit(itemId, field);
        }
    };

    window.saveEdit = async function (itemId, field) {
        const prefix  = field === 'estimated' ? 'est' : 'act';
        const input   = document.getElementById(prefix + '-input-' + itemId);
        const rawVal  = parseFloat(input.value);

        if (isNaN(rawVal) || rawVal < 0) {
            showToast('Amount must be 0 or greater.', 'error');
            input.focus();
            return;
        }

        // Optimistic: hide form, show display
        cancelEdit(itemId, field);

        const row = document.getElementById('item-row-' + itemId);
        if (row) row.classList.add('cell-saving');

        try {
            let url, body;
            if (field === 'estimated') {
                url  = '/user/budgets/items/' + itemId;
                body = new URLSearchParams({ estimated_amount: rawVal, _token: CSRF_TOKEN });
            } else {
                url  = '/user/budgets/items/' + itemId + '/actual';
                body = new URLSearchParams({ actual_amount: rawVal, _token: CSRF_TOKEN });
            }

            const resp = await fetch(url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: body.toString()
            });
            const data = await resp.json();

            if (data.success) {
                // Update local data store
                if (field === 'estimated') {
                    itemData[itemId].estimated = rawVal;
                } else {
                    itemData[itemId].actual = rawVal;
                }

                // Update display cell
                const displayEl = document.getElementById(prefix + '-display-' + itemId);
                if (field === 'estimated') {
                    displayEl.innerHTML = '₹' + formatAmount(rawVal) + ' <i class="fas fa-pencil-alt edit-pencil"></i>';
                } else {
                    displayEl.innerHTML = '₹' + formatAmount(rawVal) + ' <i class="fas fa-pencil-alt edit-pencil"></i>';
                }

                // Recalculate variance for this row
                updateRowVariance(itemId);

                // Recalculate category and grand totals
                recalculateAll();

                showToast('Saved successfully.', 'success');
            } else {
                showToast(data.message || 'Failed to save.', 'error');
                // Revert input value
                if (field === 'estimated') {
                    document.getElementById('est-input-' + itemId).value = itemData[itemId].estimated.toFixed(2);
                } else {
                    document.getElementById('act-input-' + itemId).value =
                        itemData[itemId].actual !== null ? itemData[itemId].actual.toFixed(2) : '';
                }
            }
        } catch (err) {
            showToast('Network error. Please try again.', 'error');
        } finally {
            if (row) row.classList.remove('cell-saving');
        }
    };

    /* ── Delete custom item ─────────────────────────────────────────────────── */
    window.deleteCustomItem = async function (itemId) {
        if (!confirm('Delete this custom expense?')) return;

        const row = document.getElementById('item-row-' + itemId);
        if (row) row.classList.add('cell-saving');

        try {
            const body = new URLSearchParams({ _token: CSRF_TOKEN });
            const resp = await fetch('/user/budgets/items/' + itemId + '/delete', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: body.toString()
            });
            const data = await resp.json();

            if (data.success) {
                // Remove from local data
                const catSlug = itemData[itemId] ? itemData[itemId].catSlug : null;
                delete itemData[itemId];

                // Remove row from DOM
                if (row) row.remove();

                recalculateAll();
                showToast('Custom expense deleted.', 'success');
            } else {
                showToast(data.message || 'Failed to delete.', 'error');
                if (row) row.classList.remove('cell-saving');
            }
        } catch (err) {
            showToast('Network error. Please try again.', 'error');
            if (row) row.classList.remove('cell-saving');
        }
    };

    /* ── Add custom item ────────────────────────────────────────────────────── */
    window.addCustomItem = async function (e) {
        e.preventDefault();
        const nameInput   = document.getElementById('customItemName');
        const amountInput = document.getElementById('customItemAmount');
        const errorEl     = document.getElementById('customFormError');
        const btn         = document.getElementById('addCustomBtn');

        const name   = nameInput.value.trim();
        const amount = parseFloat(amountInput.value);

        errorEl.style.display = 'none';

        if (!name) {
            errorEl.textContent = 'Expense name is required.';
            errorEl.style.display = 'block';
            nameInput.focus();
            return;
        }
        if (isNaN(amount) || amount < 0) {
            errorEl.textContent = 'Amount must be 0 or greater.';
            errorEl.style.display = 'block';
            amountInput.focus();
            return;
        }

        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding…';

        try {
            const body = new URLSearchParams({
                item_name:        name,
                estimated_amount: amount,
                _token:           CSRF_TOKEN
            });
            const resp = await fetch('/user/budgets/' + BUDGET_ID + '/items', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: body.toString()
            });
            const data = await resp.json();

            if (data.success) {
                const newItem = data.data;
                // Add to local data store
                const catSlug = slugify(newItem.category || 'Custom');
                itemData[newItem.id] = {
                    estimated: parseFloat(newItem.estimated_amount),
                    actual:    null,
                    category:  newItem.category || 'Custom',
                    catSlug:   catSlug
                };

                // Inject new row into DOM (reload page for simplicity if category doesn't exist yet)
                const tableBody = document.querySelector('#table-' + catSlug + ' tbody');
                if (tableBody) {
                    const tr = buildCustomRow(newItem);
                    tableBody.appendChild(tr);
                } else {
                    // Category doesn't exist in DOM — reload to show it properly
                    location.reload();
                    return;
                }

                recalculateAll();
                nameInput.value   = '';
                amountInput.value = '';
                showToast('Custom expense added.', 'success');
            } else {
                errorEl.textContent = data.message || 'Failed to add expense.';
                errorEl.style.display = 'block';
            }
        } catch (err) {
            errorEl.textContent = 'Network error. Please try again.';
            errorEl.style.display = 'block';
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-plus"></i> Add Expense';
        }
    };

    /* ── Build a new custom row ─────────────────────────────────────────────── */
    function buildCustomRow(item) {
        const id = item.id;
        const tr = document.createElement('tr');
        tr.id        = 'item-row-' + id;
        tr.className = 'item-row custom-item-row';
        tr.innerHTML = `
            <td>
                <span class="item-name-text">${escHtml(item.item_name)}</span>
                <span class="custom-badge">Custom</span>
            </td>
            <td style="text-align:right;">
                <div class="inline-edit-wrap">
                    <span class="amount-display" id="est-display-${id}"
                          onclick="startEdit(${id}, 'estimated')">
                        ₹${formatAmount(parseFloat(item.estimated_amount))}
                        <i class="fas fa-pencil-alt edit-pencil"></i>
                    </span>
                    <div class="inline-edit-form" id="est-form-${id}" style="display:none;">
                        <input type="number" step="0.01" min="0" class="inline-input"
                               id="est-input-${id}"
                               value="${parseFloat(item.estimated_amount).toFixed(2)}"
                               onkeydown="handleEditKey(event,${id},'estimated')"
                               onblur="cancelEdit(${id},'estimated')">
                        <button class="inline-save-btn" type="button" onmousedown="saveEdit(${id},'estimated')"><i class="fas fa-check"></i></button>
                        <button class="inline-cancel-btn" type="button" onmousedown="cancelEdit(${id},'estimated')"><i class="fas fa-times"></i></button>
                    </div>
                </div>
            </td>
            <td style="text-align:right;">
                <div class="inline-edit-wrap">
                    <span class="amount-display" id="act-display-${id}"
                          onclick="startEdit(${id}, 'actual')">
                        <span class="no-actual">— Add</span>
                        <i class="fas fa-pencil-alt edit-pencil"></i>
                    </span>
                    <div class="inline-edit-form" id="act-form-${id}" style="display:none;">
                        <input type="number" step="0.01" min="0" class="inline-input"
                               id="act-input-${id}" value="" placeholder="0.00"
                               onkeydown="handleEditKey(event,${id},'actual')"
                               onblur="cancelEdit(${id},'actual')">
                        <button class="inline-save-btn" type="button" onmousedown="saveEdit(${id},'actual')"><i class="fas fa-check"></i></button>
                        <button class="inline-cancel-btn" type="button" onmousedown="cancelEdit(${id},'actual')"><i class="fas fa-times"></i></button>
                    </div>
                </div>
            </td>
            <td style="text-align:right;">
                <span class="variance-cell variance-zero" id="var-${id}">—</span>
            </td>
            <td style="text-align:center;">
                <button class="btn-icon-danger" type="button"
                        onclick="deleteCustomItem(${id})" title="Delete custom item">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </td>
        `;
        return tr;
    }

    /* ── Recalculate variance for a single row ──────────────────────────────── */
    function updateRowVariance(itemId) {
        const d   = itemData[itemId];
        const el  = document.getElementById('var-' + itemId);
        if (!el || !d) return;

        if (d.actual === null) {
            el.innerHTML  = '<span style="color:#D1D5DB;">—</span>';
            el.className  = 'variance-cell';
            return;
        }
        const v    = d.actual - d.estimated;
        const sign = v > 0 ? '+' : '';
        el.textContent = sign + '₹' + formatAmount(Math.abs(v));
        el.className   = 'variance-cell ' + (v > 0 ? 'variance-over' : v < 0 ? 'variance-under' : 'variance-zero');
    }

    /* ── Recalculate all category and grand totals from itemData ────────────── */
    function recalculateAll() {
        // Group by catSlug
        const catTotals = {};
        for (const [id, d] of Object.entries(itemData)) {
            const slug = d.catSlug;
            if (!catTotals[slug]) catTotals[slug] = { est: 0, act: 0 };
            catTotals[slug].est += d.estimated;
            if (d.actual !== null) catTotals[slug].act += d.actual;
        }

        // Update category header chips + footer row
        for (const [slug, totals] of Object.entries(catTotals)) {
            const variance = totals.act - totals.est;
            const sign     = variance > 0 ? '+' : '';
            const varClass = variance > 0 ? 'variance-over' : variance < 0 ? 'variance-under' : 'variance-zero';

            setTextSafe('cat-est-' + slug,      '₹' + formatAmount(totals.est));
            setTextSafe('cat-act-' + slug,      '₹' + formatAmount(totals.act));
            setTextSafe('cat-est-foot-' + slug, '₹' + formatAmount(totals.est));
            setTextSafe('cat-act-foot-' + slug, '₹' + formatAmount(totals.act));

            const varEl     = document.getElementById('cat-var-' + slug);
            const varFootEl = document.getElementById('cat-var-foot-' + slug);
            const varText   = sign + '₹' + formatAmount(Math.abs(variance));

            if (varEl) {
                varEl.textContent = varText;
                varEl.className   = 'cat-summary-chip ' + varClass;
            }
            if (varFootEl) {
                varFootEl.textContent = varText;
                varFootEl.className   = varClass;
            }
        }

        // Grand totals
        let grandEst = 0, grandAct = 0;
        for (const d of Object.values(itemData)) {
            grandEst += d.estimated;
            if (d.actual !== null) grandAct += d.actual;
        }
        const grandVar     = grandAct - grandEst;
        const grandSign    = grandVar > 0 ? '+' : '';
        const grandVarClass = grandVar > 0 ? 'variance-over' : grandVar < 0 ? 'variance-under' : 'variance-zero';
        const grandVarText  = grandSign + '₹' + formatAmount(Math.abs(grandVar));

        // Hero totals
        setTextSafe('grandTotalEstimated', '₹' + formatAmount(grandEst));
        setTextSafe('grandTotalActual',    '₹' + formatAmount(grandAct));
        const heroVar = document.getElementById('grandTotalVariance');
        if (heroVar) {
            heroVar.textContent = grandVarText;
            heroVar.className   = 'hero-total-value ' + grandVarClass;
        }

        // Footer bar totals
        setTextSafe('footerTotalEstimated', '₹' + formatAmount(grandEst));
        setTextSafe('footerTotalActual',    '₹' + formatAmount(grandAct));
        const footVar = document.getElementById('footerTotalVariance');
        if (footVar) {
            footVar.textContent = grandVarText;
            footVar.className   = 'grand-total-value ' + grandVarClass;
        }
    }

    /* ── Utilities ──────────────────────────────────────────────────────────── */
    function formatAmount(n) {
        return n.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function setTextSafe(id, text) {
        const el = document.getElementById(id);
        if (el) el.textContent = text;
    }

    function escHtml(str) {
        const d = document.createElement('div');
        d.appendChild(document.createTextNode(str));
        return d.innerHTML;
    }

    function slugify(str) {
        return str.toLowerCase().replace(/[^a-z0-9]+/g, '-');
    }

})();
</script>
