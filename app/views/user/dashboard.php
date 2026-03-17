<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon orange"><i class="fas fa-calendar-check"></i></div>
        <div class="stat-value"><?= count($assignments ?? []) ?></div>
        <div class="stat-label">My Bookings</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green"><i class="fas fa-shopping-cart"></i></div>
        <div class="stat-value"><?= $shoppingSummary['pending'] ?? 0 ?></div>
        <div class="stat-label">Items to Buy</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon yellow"><i class="fas fa-users"></i></div>
        <div class="stat-value"><?= !empty($family) ? 1 : 0 ?></div>
        <div class="stat-label">Family Setup</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon blue"><i class="fas fa-book"></i></div>
        <div class="stat-value"><?= count($featuredRituals ?? []) ?></div>
        <div class="stat-label">Featured Rituals</div>
    </div>
</div>

<?php if (!empty($upcomingFestivals)): ?>
<div class="card" style="margin-bottom: 25px;">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-calendar-star" style="color: #F59E0B;"></i>
            Upcoming Rituals For You
            <span style="font-size: 0.75rem; font-weight: 400; color: #6B7280; margin-left: 8px;">
                Based on <?= htmlspecialchars($communityLabel) ?> traditions
            </span>
        </h3>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 16px;">
        <?php foreach ($upcomingFestivals as $festival): ?>
        <?php
            $festDate = strtotime($festival['date']);
            $daysLeft = (int) ceil(($festDate - time()) / 86400);
            $prepStart = $daysLeft - $festival['preparation_days'];
            $isUrgent = $daysLeft <= 7;
            $isPrepTime = $prepStart <= 0;
        ?>
        <div style="background: linear-gradient(135deg, <?= $isUrgent ? 'rgba(239,68,68,0.06), rgba(249,115,22,0.06)' : 'rgba(99,102,241,0.05), rgba(168,85,247,0.05)' ?>);
             border: 1px solid <?= $isUrgent ? '#FCA5A5' : '#E5E7EB' ?>;
             border-radius: 14px; padding: 20px; position: relative; transition: all 0.3s; overflow: hidden;">

            <!-- Days badge -->
            <div style="position: absolute; top: 14px; right: 14px;
                 background: <?= $isUrgent ? 'linear-gradient(135deg, #EF4444, #F97316)' : 'linear-gradient(135deg, #6366F1, #8B5CF6)' ?>;
                 color: white; padding: 4px 10px; border-radius: 20px; font-size: 0.72rem; font-weight: 600;">
                <?php if ($daysLeft === 0): ?>Today!
                <?php elseif ($daysLeft === 1): ?>Tomorrow
                <?php else: ?><?= $daysLeft ?> days
                <?php endif; ?>
            </div>

            <!-- Icon + Name -->
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px;">
                <div style="width: 44px; height: 44px;
                     background: <?= $isUrgent ? 'linear-gradient(135deg, #EF4444, #F97316)' : 'linear-gradient(135deg, #6366F1, #A855F7)' ?>;
                     border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.2rem; flex-shrink: 0;">
                    <i class="fas <?= $festival['icon'] ?>"></i>
                </div>
                <div>
                    <h4 style="margin: 0; font-size: 1rem; color: #1E1E2E;"><?= htmlspecialchars($festival['name']) ?></h4>
                    <span style="font-size: 0.78rem; color: #6B7280;">
                        <i class="fas fa-calendar-alt"></i> <?= date('D, M d, Y', $festDate) ?>
                    </span>
                </div>
            </div>

            <!-- Significance -->
            <p style="margin: 0 0 12px; font-size: 0.85rem; color: #4B5563; line-height: 1.5;">
                <?= htmlspecialchars($festival['significance']) ?>
            </p>

            <!-- Prep timeline -->
            <?php if ($isPrepTime): ?>
            <div style="background: rgba(249,115,22,0.1); color: #C2410C; padding: 6px 10px; border-radius: 8px; font-size: 0.78rem; margin-bottom: 12px; display: inline-flex; align-items: center; gap: 5px;">
                <i class="fas fa-exclamation-circle"></i> Start preparing now!
            </div>
            <?php elseif ($festival['preparation_days'] > 0): ?>
            <div style="background: rgba(99,102,241,0.08); color: #4338CA; padding: 6px 10px; border-radius: 8px; font-size: 0.78rem; margin-bottom: 12px; display: inline-flex; align-items: center; gap: 5px;">
                <i class="fas fa-clock"></i> Start preparing <?= $festival['preparation_days'] ?> day<?= $festival['preparation_days'] > 1 ? 's' : '' ?> before
            </div>
            <?php endif; ?>

            <!-- Plan Ritual button -->
            <form method="POST" action="/user/plan-festival-ritual" style="margin: 0;">
                <?= \App\Core\Auth::csrfField() ?>
                <input type="hidden" name="festival_name" value="<?= htmlspecialchars($festival['name']) ?>">
                <button type="submit" class="btn btn-sm btn-primary" style="width: 100%; justify-content: center;"
                    onclick="this.disabled=true; this.innerHTML='<i class=\'fas fa-spinner fa-spin\'></i> Generating...'; this.form.submit();">
                    <i class="fas fa-magic"></i> Plan Ritual
                </button>
            </form>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<div class="content-grid">
    <div>
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-star"></i> Featured Rituals</h3>
                <a href="/user/rituals" class="btn btn-sm btn-primary">View All</a>
            </div>
            <?php if (empty($featuredRituals)): ?>
                <p style="text-align: center; color: #6B7280; padding: 20px;">No featured rituals</p>
            <?php else: ?>
                <?php foreach (array_slice($featuredRituals, 0, 3) as $ritual): ?>
                <div class="ritual-card">
                    <div class="ritual-title"><?= htmlspecialchars($ritual['name']) ?></div>
                    <div class="ritual-meta">
                        <span class="badge badge-info"><?= htmlspecialchars($ritual['category']) ?></span>
                        <span style="margin-left: 10px;"><i class="fas fa-clock"></i> <?= $ritual['duration_minutes'] ?> min</span>
                    </div>
                    <p style="color: #6B7280; font-size: 0.9rem; margin: 10px 0;">
                        <?= htmlspecialchars(substr($ritual['description'] ?? '', 0, 100)) ?>...
                    </p>
                    <a href="/user/rituals/<?= $ritual['id'] ?>" class="btn btn-sm btn-primary">Learn More</a>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <?php if (!empty($assignments)): ?>
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-calendar"></i> Upcoming Bookings</h3>
                <a href="/user/bookings" class="btn btn-sm btn-primary">View All</a>
            </div>
            <table class="table">
                <thead><tr><th>Ritual</th><th>Pandit</th><th>Date</th><th>Status</th></tr></thead>
                <tbody>
                    <?php foreach (array_slice($assignments, 0, 3) as $a): ?>
                    <tr>
                        <td><?= htmlspecialchars($a['ritual_name'] ?? 'Custom') ?></td>
                        <td><?= htmlspecialchars($a['pandit_name']) ?></td>
                        <td><?= $a['scheduled_date'] ? date('M d', strtotime($a['scheduled_date'])) : 'TBD' ?></td>
                        <td><span class="badge badge-<?= $a['status'] === 'confirmed' ? 'success' : 'warning' ?>"><?= ucfirst($a['status']) ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
    
    <div>
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-users"></i> My Family</h3>
            </div>
            <?php if (empty($family)): ?>
                <p style="text-align: center; color: #6B7280; padding: 20px;">No family setup yet</p>
                <a href="/user/families/create" class="btn btn-primary" style="width: 100%;">
                    <i class="fas fa-plus"></i> Setup Family
                </a>
            <?php else: ?>
                <p><strong><?= htmlspecialchars($family['family_name']) ?></strong></p>
                <p style="color: #6B7280; font-size: 0.9rem; margin: 10px 0;">
                    Gotra: <?= htmlspecialchars($family['gotra'] ?? 'Not set') ?><br>
                    Kul Devta: <?= htmlspecialchars($family['kul_devta'] ?? 'Not set') ?><br>
                    Location: <?= htmlspecialchars($family['city'] ?? 'Not set') ?>
                </p>
                <a href="/user/families/<?= $family['id'] ?>/edit" class="btn btn-sm btn-primary">
                    <i class="fas fa-edit"></i> Manage
                </a>
            <?php endif; ?>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-lightbulb"></i> Cultural Insights</h3>
            </div>
            <?php if (!empty($featuredInsights)): ?>
                <?php foreach ($featuredInsights as $insight): ?>
                <div style="margin-bottom: 15px; padding-bottom: 15px; border-bottom: 1px solid #E5E7EB;">
                    <a href="/user/insights/<?= $insight['slug'] ?>" style="color: var(--dark); text-decoration: none; font-weight: 500;">
                        <?= htmlspecialchars($insight['title']) ?>
                    </a>
                    <p style="color: #6B7280; font-size: 0.8rem;"><?= htmlspecialchars($insight['category']) ?></p>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
            <a href="/user/insights" class="btn btn-sm" style="background: #E5E7EB; color: #374151;">View All</a>
        </div>
        
        <div class="card" style="background: linear-gradient(135deg, #FF6B35 0%, #F59E0B 100%); color: white;">
            <h3 style="margin-bottom: 15px;"><i class="fas fa-robot"></i> AI Suggestions</h3>
            <p style="font-size: 0.9rem; margin-bottom: 15px;">Get personalized ritual recommendations based on your needs.</p>
            <a href="/user/ai-suggestions" class="btn" style="background: white; color: #FF6B35;">
                Try Now <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
</div>
