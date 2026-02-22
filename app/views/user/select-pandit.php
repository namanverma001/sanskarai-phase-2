<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-pray"></i> Find a Pandit</h3>
    </div>
    
    <p style="color: #6B7280; margin-bottom: 25px;">
        Choose from our verified pandits to perform rituals at your home or venue.
    </p>
    
    <?php if (empty($pandits)): ?>
        <p style="text-align: center; color: #6B7280; padding: 30px;">No pandits available at the moment.</p>
    <?php else: ?>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">
            <?php foreach ($pandits as $pandit): ?>
            <div style="border: 1px solid #E5E7EB; border-radius: 12px; padding: 20px; transition: all 0.3s;">
                <div style="display: flex; gap: 15px; align-items: start;">
                    <div style="width: 60px; height: 60px; background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.5rem;">
                        <i class="fas fa-user"></i>
                    </div>
                    <div style="flex: 1;">
                        <h4 style="margin-bottom: 5px;"><?= htmlspecialchars($pandit['name']) ?></h4>
                        <p style="color: #6B7280; font-size: 0.85rem;">
                            <?= htmlspecialchars($pandit['specialization'] ?? 'General Puja') ?>
                        </p>
                        <div style="margin-top: 10px; display: flex; gap: 15px; font-size: 0.85rem;">
                            <span><i class="fas fa-star" style="color: #F59E0B;"></i> <?= number_format($pandit['average_rating'] ?? 0, 1) ?></span>
                            <span><i class="fas fa-briefcase"></i> <?= $pandit['experience_years'] ?? 0 ?> yrs</span>
                            <span><i class="fas fa-check-circle" style="color: #10B981;"></i> <?= $pandit['total_rituals_performed'] ?? 0 ?> rituals</span>
                        </div>
                    </div>
                </div>
                
                <?php if (!empty($pandit['bio'])): ?>
                <p style="margin-top: 15px; color: #6B7280; font-size: 0.9rem;">
                    <?= htmlspecialchars(substr($pandit['bio'], 0, 100)) ?>...
                </p>
                <?php endif; ?>
                
                <?php if (!empty($pandit['languages'])): ?>
                <p style="margin-top: 10px; font-size: 0.85rem;">
                    <i class="fas fa-language"></i> <?= htmlspecialchars($pandit['languages']) ?>
                </p>
                <?php endif; ?>
                
                <div style="margin-top: 15px; display: flex; gap: 10px;">
                    <a href="/user/book-pandit/<?= $pandit['id'] ?>" class="btn btn-primary btn-sm" style="flex: 1; justify-content: center;">
                        <i class="fas fa-calendar-plus"></i> Book Now
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
