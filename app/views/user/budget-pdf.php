<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Budget — <?= htmlspecialchars($budget['ritual_type']) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Karma:wght@400;500;600;700&family=Cinzel:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Karma', serif;
            color: #1F2937;
            background: white;
            padding: 0;
        }

        .page {
            max-width: 800px;
            margin: 0 auto;
            padding: 40px 48px;
        }

        /* ── Header ─────────────────────────────────────────────────────── */
        .header {
            border-bottom: 3px solid #059669;
            padding-bottom: 24px;
            margin-bottom: 32px;
        }

        .header-brand {
            font-family: 'Cinzel', serif;
            font-size: 0.75rem;
            color: #059669;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-bottom: 12px;
        }

        .header-title {
            font-family: 'Cinzel', serif;
            font-size: 2rem;
            color: #064E3B;
            font-weight: 800;
            margin-bottom: 16px;
            line-height: 1.2;
        }

        .header-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            font-size: 0.88rem;
            color: #4B5563;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .meta-label {
            font-weight: 600;
            color: #374151;
        }

        .tier-badge {
            display: inline-block;
            padding: 3px 12px;
            border-radius: 20px;
            font-size: 0.78rem;
            font-weight: 700;
        }

        .tier-basic    { background: #D1FAE5; color: #065F46; }
        .tier-standard { background: #DBEAFE; color: #1E40AF; }
        .tier-premium  { background: #FEF3C7; color: #92400E; }

        .generated-date {
            margin-top: 12px;
            font-size: 0.78rem;
            color: #9CA3AF;
        }

        /* ── Category Section ────────────────────────────────────────────── */
        .category-section {
            margin-bottom: 28px;
            break-inside: avoid;
        }

        .category-heading {
            font-family: 'Cinzel', serif;
            font-size: 0.95rem;
            font-weight: 600;
            color: #064E3B;
            background: #F0FDF4;
            border-left: 4px solid #059669;
            padding: 8px 14px;
            margin-bottom: 0;
            letter-spacing: 0.3px;
        }

        /* ── Items Table ─────────────────────────────────────────────────── */
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.875rem;
        }

        thead th {
            background: #F9FAFB;
            color: #6B7280;
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
            padding: 9px 12px;
            border-bottom: 1px solid #E5E7EB;
            border-top: 1px solid #E5E7EB;
        }

        thead th:not(:first-child) {
            text-align: right;
        }

        tbody td {
            padding: 10px 12px;
            border-bottom: 1px solid #F3F4F6;
            color: #374151;
            vertical-align: middle;
        }

        tbody td:not(:first-child) {
            text-align: right;
        }

        tbody tr:last-child td {
            border-bottom: none;
        }

        .item-name {
            font-weight: 500;
        }

        .custom-tag {
            display: inline-block;
            background: #FEF3C7;
            color: #92400E;
            font-size: 0.62rem;
            font-weight: 700;
            padding: 1px 6px;
            border-radius: 4px;
            margin-left: 6px;
            vertical-align: middle;
        }

        .no-actual {
            color: #D1D5DB;
        }

        .variance-over  { color: #EF4444; }
        .variance-under { color: #10B981; }
        .variance-zero  { color: #9CA3AF; }

        /* Category subtotal row */
        tfoot td {
            padding: 9px 12px;
            font-weight: 700;
            font-size: 0.85rem;
            background: #F9FAFB;
            border-top: 2px solid #E5E7EB;
            color: #1F2937;
        }

        tfoot td:not(:first-child) {
            text-align: right;
        }

        /* ── Grand Totals ────────────────────────────────────────────────── */
        .totals-section {
            margin-top: 32px;
            border-top: 3px solid #059669;
            padding-top: 20px;
            break-inside: avoid;
        }

        .totals-title {
            font-family: 'Cinzel', serif;
            font-size: 1rem;
            color: #064E3B;
            font-weight: 700;
            margin-bottom: 14px;
        }

        .totals-grid {
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
        }

        .total-card {
            flex: 1;
            min-width: 160px;
            border: 1px solid #E5E7EB;
            border-radius: 10px;
            padding: 14px 18px;
            text-align: center;
        }

        .total-card-label {
            font-size: 0.72rem;
            color: #9CA3AF;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
        }

        .total-card-value {
            font-size: 1.3rem;
            font-weight: 700;
            color: #064E3B;
        }

        .total-card-value.actual {
            color: #10B981;
        }

        /* ── Empty State ─────────────────────────────────────────────────── */
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #9CA3AF;
            border: 1px dashed #E5E7EB;
            border-radius: 10px;
            margin-bottom: 28px;
        }

        .empty-state p {
            font-size: 0.9rem;
        }

        /* ── Footer ──────────────────────────────────────────────────────── */
        .footer {
            margin-top: 48px;
            padding-top: 16px;
            border-top: 1px solid #E5E7EB;
            text-align: center;
            font-size: 0.75rem;
            color: #9CA3AF;
        }

        /* ── Print styles ────────────────────────────────────────────────── */
        @media print {
            body { padding: 0; }
            .page { padding: 20px 28px; }
            .category-section { break-inside: avoid; }
            .totals-section { break-inside: avoid; }
        }
    </style>
</head>
<body>

<?php
// Group items by category
$itemsByCategory = [];
foreach ($budget['items'] ?? [] as $item) {
    $itemsByCategory[$item['category']][] = $item;
}

$tier      = strtolower($budget['tier'] ?? 'standard');
$tierLabel = ucfirst($tier);
$tierClass = 'tier-' . $tier;

$totalEstimated = (float)($budget['total_estimated'] ?? 0);
$totalActual    = (float)($budget['total_actual'] ?? 0);
?>

<div class="page">

    <!-- ── Header ──────────────────────────────────────────────────────── -->
    <div class="header">
        <div class="header-brand">Sanskar AI — Ritual Budget Planner</div>
        <h1 class="header-title"><?= htmlspecialchars($budget['ritual_type']) ?> Budget</h1>
        <div class="header-meta">
            <div class="meta-item">
                <span class="meta-label">Location:</span>
                <span><?= htmlspecialchars($budget['location']) ?></span>
            </div>
            <div class="meta-item">
                <span class="meta-label">Guests:</span>
                <span><?= number_format((int)$budget['guest_count']) ?></span>
            </div>
            <div class="meta-item">
                <span class="meta-label">Tier:</span>
                <span class="tier-badge <?= $tierClass ?>"><?= $tierLabel ?></span>
            </div>
        </div>
        <div class="generated-date">
            Generated on <?= date('d F Y, g:i A') ?>
        </div>
    </div>

    <!-- ── Items by Category ───────────────────────────────────────────── -->
    <?php if (empty($itemsByCategory)): ?>
        <div class="empty-state">
            <p>No budget items have been added yet.</p>
        </div>
    <?php else: ?>
        <?php foreach ($itemsByCategory as $category => $items): ?>
            <?php
            $catEstimated = array_sum(array_column($items, 'estimated_amount'));
            $catActual    = array_sum(array_filter(array_column($items, 'actual_amount'), fn($v) => $v !== null));
            $catVariance  = $catActual - $catEstimated;
            $catVarClass  = $catVariance > 0 ? 'variance-over' : ($catVariance < 0 ? 'variance-under' : 'variance-zero');
            $catVarSign   = $catVariance > 0 ? '+' : '';
            ?>
            <div class="category-section">
                <div class="category-heading"><?= htmlspecialchars($category) ?></div>
                <table>
                    <thead>
                        <tr>
                            <th style="width:45%;">Item</th>
                            <th style="width:18%;">Estimated</th>
                            <th style="width:18%;">Actual</th>
                            <th style="width:19%;">Variance</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item): ?>
                            <?php
                            $hasActual    = $item['actual_amount'] !== null;
                            $itemVariance = $hasActual
                                ? ((float)$item['actual_amount'] - (float)$item['estimated_amount'])
                                : null;
                            $ivClass = $itemVariance === null ? '' : ($itemVariance > 0 ? 'variance-over' : ($itemVariance < 0 ? 'variance-under' : 'variance-zero'));
                            $ivSign  = ($itemVariance !== null && $itemVariance > 0) ? '+' : '';
                            ?>
                            <tr>
                                <td>
                                    <span class="item-name"><?= htmlspecialchars($item['item_name']) ?></span>
                                    <?php if ($item['is_custom']): ?>
                                        <span class="custom-tag">Custom</span>
                                    <?php endif; ?>
                                </td>
                                <td>₹<?= number_format((float)$item['estimated_amount'], 2) ?></td>
                                <td>
                                    <?php if ($hasActual): ?>
                                        ₹<?= number_format((float)$item['actual_amount'], 2) ?>
                                    <?php else: ?>
                                        <span class="no-actual">—</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($itemVariance !== null): ?>
                                        <span class="<?= $ivClass ?>">
                                            <?= $ivSign ?>₹<?= number_format(abs($itemVariance), 2) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="no-actual">—</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td>Category Total</td>
                            <td>₹<?= number_format($catEstimated, 2) ?></td>
                            <td>₹<?= number_format($catActual, 2) ?></td>
                            <td class="<?= $catVarClass ?>">
                                <?= $catVarSign ?>₹<?= number_format(abs($catVariance), 2) ?>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- ── Grand Totals ────────────────────────────────────────────────── -->
    <div class="totals-section">
        <div class="totals-title">Budget Summary</div>
        <div class="totals-grid">
            <div class="total-card">
                <div class="total-card-label">Total Estimated</div>
                <div class="total-card-value">₹<?= number_format($totalEstimated, 2) ?></div>
            </div>
            <div class="total-card">
                <div class="total-card-label">Total Actual</div>
                <div class="total-card-value actual">₹<?= number_format($totalActual, 2) ?></div>
            </div>
            <?php
            $grandVariance = $totalActual - $totalEstimated;
            $gvClass = $grandVariance > 0 ? 'variance-over' : ($grandVariance < 0 ? 'variance-under' : 'variance-zero');
            $gvSign  = $grandVariance > 0 ? '+' : '';
            ?>
            <div class="total-card">
                <div class="total-card-label">Net Variance</div>
                <div class="total-card-value <?= $gvClass ?>">
                    <?= $gvSign ?>₹<?= number_format(abs($grandVariance), 2) ?>
                </div>
            </div>
        </div>
    </div>

    <!-- ── Footer ──────────────────────────────────────────────────────── -->
    <div class="footer">
        Generated by Sanskar AI — Your Spiritual Companion &nbsp;|&nbsp;
        Budget ID #<?= (int)$budget['id'] ?>
    </div>

</div>

<script>
    window.onload = function () {
        window.print();
    };
</script>

</body>
</html>
