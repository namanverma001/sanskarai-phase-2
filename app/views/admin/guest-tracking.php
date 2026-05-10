<!-- DataTables CSS -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.css">
<style>
    /* Prevent long text from breaking table layout */
    .truncate-text {
        max-width: 250px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        display: inline-block;
        vertical-align: middle;
    }
    .wrap-text {
        max-width: 450px;
        white-space: normal;
        word-wrap: break-word;
        display: block;
    }
    .dataTables_wrapper {
        padding: 15px;
    }
    .filter-form {
        background: white;
        padding: 15px 20px;
        border-radius: 12px;
        margin-bottom: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        border: 1px solid #E5E7EB;
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        align-items: flex-end;
    }
    .filter-group {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }
    .filter-group label {
        font-size: 0.85rem;
        font-weight: 600;
        color: #4b5563;
    }
    .filter-group input {
        padding: 8px 12px;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        outline: none;
        font-size: 0.9rem;
    }
    .filter-group input:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
    }
    .filter-btn {
        padding: 8px 16px;
        background: var(--primary, #4f46e5);
        color: white;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 500;
        transition: background 0.2s;
        height: 38px;
    }
    .filter-btn:hover {
        background: var(--primary-dark, #4338ca);
    }
    .clear-btn {
        padding: 8px 16px;
        background: #f3f4f6;
        color: #4b5563;
        text-decoration: none;
        border-radius: 6px;
        font-weight: 500;
        transition: background 0.2s;
        height: 38px;
        display: inline-flex;
        align-items: center;
    }
    .clear-btn:hover {
        background: #e5e7eb;
    }
</style>

<form method="GET" action="/admin/guest-tracking" class="filter-form">
    <div class="filter-group">
        <label for="start_date">Start Date</label>
        <input type="date" id="start_date" name="start_date" value="<?= htmlspecialchars($startDate ?? '') ?>">
    </div>
    <div class="filter-group">
        <label for="end_date">End Date</label>
        <input type="date" id="end_date" name="end_date" value="<?= htmlspecialchars($endDate ?? '') ?>">
    </div>
    <div class="filter-group" style="flex-direction: row;">
        <button type="submit" class="filter-btn"><i class="fas fa-filter"></i> Filter</button>
        <?php if (!empty($startDate) || !empty($endDate)): ?>
            <a href="/admin/guest-tracking" class="clear-btn"><i class="fas fa-times"></i> Clear</a>
        <?php endif; ?>
    </div>
</form>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon blue"><i class="fas fa-eye"></i></div>
        <div class="stat-value"><?= $stats['total_views'] ?? 0 ?></div>
        <div class="stat-label">Total Guest Views</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green"><i class="fas fa-search"></i></div>
        <div class="stat-value"><?= $stats['total_searches'] ?? 0 ?></div>
        <div class="stat-label">Total Guest Searches</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon purple"><i class="fas fa-users"></i></div>
        <div class="stat-value"><?= $stats['unique_visitors'] ?? 0 ?></div>
        <div class="stat-label">Unique Guests</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon orange"><i class="fas fa-robot"></i></div>
        <div class="stat-value"><?= $stats['total_ai_pandit'] ?? 0 ?></div>
        <div class="stat-label">Total AI Pandit Queries</div>
    </div>
</div>

<div class="card" style="margin-top: 20px;">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-search"></i> Recent Guest Searches</h3>
    </div>
    <div class="table-responsive">
        <table class="table data-table mobile-card-view">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Session ID</th>
                    <th>IP Address</th>
                    <th>Search Criteria</th>
                    <th>Time</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recentSearches as $search): ?>
                <tr onclick="if(window.innerWidth <= 768) this.classList.toggle('expanded')" style="cursor: pointer;">
                    <td data-label="ID">#<?= $search['id'] ?></td>
                    <td data-label="Session">
                        <code class="truncate-text" style="max-width: 120px; background: #f3f4f6; padding: 2px 6px; border-radius: 4px; color: #4b5563;" title="<?= htmlspecialchars($search['session_id']) ?>">
                            <?= htmlspecialchars($search['session_id']) ?>
                        </code>
                    </td>
                    <td data-label="IP">
                        <?php 
                        $ip = $search['ip_address'] ?? 'Unknown';
                        if ($ip === '::1' || $ip === '127.0.0.1') $ip = 'Localhost';
                        echo htmlspecialchars($ip);
                        ?>
                    </td>
                    <td data-label="Criteria">
                        <div class="wrap-text">
                            <?php 
                            $details = json_decode($search['action_details'], true);
                            if (is_array($details)) {
                                $criteriaStr = [];
                                foreach ($details as $k => $v) {
                                    if (!empty($v)) {
                                        $criteriaStr[] = ucfirst(str_replace('_', ' ', $k)) . ': ' . htmlspecialchars($v);
                                    }
                                }
                                $displayStr = !empty($criteriaStr) ? implode(', ', $criteriaStr) : 'Empty Search';
                            } else {
                                $displayStr = htmlspecialchars($search['action_details']);
                            }
                            echo $displayStr;
                            ?>
                        </div>
                    </td>
                    <td data-label="Time" data-order="<?= strtotime($search['created_at']) ?>"><?= date('M d, H:i', strtotime($search['created_at'])) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="card" style="margin-top: 20px;">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-eye"></i> Recent Guest Page Views</h3>
    </div>
    <div class="table-responsive">
        <table class="table data-table mobile-card-view">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Session ID</th>
                    <th>IP Address</th>
                    <th>Page / Ritual Viewed</th>
                    <th>Time</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recentViews as $view): ?>
                <tr onclick="if(window.innerWidth <= 768) this.classList.toggle('expanded')" style="cursor: pointer;">
                    <td data-label="ID">#<?= $view['id'] ?></td>
                    <td data-label="Session">
                        <code class="truncate-text" style="max-width: 120px; background: #f3f4f6; padding: 2px 6px; border-radius: 4px; color: #4b5563;" title="<?= htmlspecialchars($view['session_id']) ?>">
                            <?= htmlspecialchars($view['session_id']) ?>
                        </code>
                    </td>
                    <td data-label="IP">
                        <?php 
                        $ip = $view['ip_address'] ?? 'Unknown';
                        if ($ip === '::1' || $ip === '127.0.0.1') $ip = 'Localhost';
                        echo htmlspecialchars($ip);
                        ?>
                    </td>
                    <td data-label="Page">
                        <div class="wrap-text">
                            <?= htmlspecialchars($view['action_details']) ?>
                        </div>
                    </td>
                    <td data-label="Time" data-order="<?= strtotime($view['created_at']) ?>"><?= date('M d, H:i', strtotime($view['created_at'])) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="card" style="margin-top: 20px;">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-robot"></i> Recent AI Pandit Interactions</h3>
    </div>
    <div class="table-responsive">
        <table class="table data-table mobile-card-view">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Session ID</th>
                    <th>IP Address</th>
                    <th>Query Sent</th>
                    <th>Time</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recentAiPandit as $interaction): ?>
                <tr onclick="if(window.innerWidth <= 768) this.classList.toggle('expanded')" style="cursor: pointer;">
                    <td data-label="ID">#<?= $interaction['id'] ?></td>
                    <td data-label="Session">
                        <code class="truncate-text" style="max-width: 120px; background: #f3f4f6; padding: 2px 6px; border-radius: 4px; color: #4b5563;" title="<?= htmlspecialchars($interaction['session_id']) ?>">
                            <?= htmlspecialchars($interaction['session_id']) ?>
                        </code>
                    </td>
                    <td data-label="IP">
                        <?php 
                        $ip = $interaction['ip_address'] ?? 'Unknown';
                        if ($ip === '::1' || $ip === '127.0.0.1') $ip = 'Localhost';
                        echo htmlspecialchars($ip);
                        ?>
                    </td>
                    <td data-label="Query">
                        <div class="wrap-text">
                            <?= htmlspecialchars($interaction['action_details']) ?>
                        </div>
                    </td>
                    <td data-label="Time" data-order="<?= strtotime($interaction['created_at']) ?>"><?= date('M d, H:i', strtotime($interaction['created_at'])) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- DataTables JS Integration -->
<script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.js"></script>
<script>
    $(document).ready(function() {
        $('.data-table').DataTable({
            "order": [[ 4, "desc" ]],
            "pageLength": 10,
            "lengthMenu": [5, 10, 25, 50, 100],
            "responsive": true,
            "language": {
                "search": "Filter records:",
                "lengthMenu": "Show _MENU_ entries"
            }
        });
    });
</script>

<style>
@media (max-width: 768px) {
    .mobile-card-view thead { display: none; }
    .mobile-card-view, .mobile-card-view tbody, .mobile-card-view tr, .mobile-card-view td { display: block; width: 100%; }
    .mobile-card-view tr { 
        margin-bottom: 20px; 
        background: white; 
        border-radius: 12px; 
        box-shadow: 0 2px 8px rgba(0,0,0,0.05); 
        padding: 15px; 
        border: 1px solid #E5E7EB; 
        position: relative;
    }
    
    /* Toggle Indicator */
    .mobile-card-view tr::after {
        content: '\f078'; /* fa-chevron-down */
        font-family: 'Font Awesome 5 Free';
        font-weight: 900;
        position: absolute;
        top: 20px;
        right: 20px;
        color: #6B7280;
        transition: transform 0.3s;
    }
    .mobile-card-view tr.expanded::after {
        transform: rotate(180deg);
    }

    /* Cell layout */
    .mobile-card-view td { 
        display: none; /* Hide all by default */
        justify-content: space-between; 
        align-items: center; 
        text-align: right; 
        padding: 10px 0; 
        border-bottom: 1px solid #F3F4F6; 
    }
    
    /* Always visible fields */
    .mobile-card-view td[data-label="ID"],
    .mobile-card-view td[data-label="Criteria"],
    .mobile-card-view td[data-label="Page"] {
        display: flex;
    }

    /* Show all when expanded */
    .mobile-card-view tr.expanded td {
        display: flex;
        animation: fadeIn 0.3s ease;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    .mobile-card-view td:last-child { border-bottom: none; }
    .mobile-card-view td::before { content: attr(data-label); font-weight: 600; color: #6B7280; font-size: 0.85rem; }
}
</style>
