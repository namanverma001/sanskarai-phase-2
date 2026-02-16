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
