<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-magic"></i> <?= htmlspecialchars($ritual['name']) ?></h3>
        <a href="/user/custom-rituals" class="btn btn-sm" style="background: #E5E7EB; color: #374151;">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>
    
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px; margin-bottom: 25px;">
        <div>
            <?php if ($ritual['purpose']): ?>
            <div style="margin-bottom: 20px;">
                <label style="font-weight: 600; color: #374151; display: block; margin-bottom: 8px;">Purpose</label>
                <p style="color: #6B7280; line-height: 1.6;"><?= htmlspecialchars($ritual['purpose']) ?></p>
            </div>
            <?php endif; ?>
            
            <?php if ($ritual['description']): ?>
            <div style="margin-bottom: 20px;">
                <label style="font-weight: 600; color: #374151; display: block; margin-bottom: 8px;">Description</label>
                <p style="color: #6B7280; line-height: 1.6;"><?= htmlspecialchars($ritual['description']) ?></p>
            </div>
            <?php endif; ?>
            
            <?php if ($ritual['base_ritual_name']): ?>
            <div style="margin-bottom: 20px;">
                <label style="font-weight: 600; color: #374151; display: block; margin-bottom: 8px;">Based On</label>
                <p style="color: #6B7280;"><?= htmlspecialchars($ritual['base_ritual_name']) ?></p>
            </div>
            <?php endif; ?>
        </div>
        
        <div>
            <div style="background: #F9FAFB; padding: 20px; border-radius: 12px;">
                <div style="margin-bottom: 15px;">
                    <label style="font-weight: 600; color: #374151; display: block; margin-bottom: 5px;">Status</label>
                    <span class="badge badge-<?= $ritual['status'] === 'approved' ? 'success' : ($ritual['status'] === 'rejected' ? 'danger' : 'warning') ?>" style="font-size: 0.9rem;">
                        <?= ucfirst($ritual['status']) ?>
                    </span>
                </div>
                
                <?php if ($ritual['scheduled_date']): ?>
                <div style="margin-bottom: 15px;">
                    <label style="font-weight: 600; color: #374151; display: block; margin-bottom: 5px;">
                        <i class="fas fa-calendar"></i> Scheduled Date
                    </label>
                    <p style="color: #6B7280;"><?= date('F j, Y', strtotime($ritual['scheduled_date'])) ?></p>
                </div>
                <?php endif; ?>
                
                <?php if ($ritual['scheduled_time']): ?>
                <div style="margin-bottom: 15px;">
                    <label style="font-weight: 600; color: #374151; display: block; margin-bottom: 5px;">
                        <i class="fas fa-clock"></i> Scheduled Time
                    </label>
                    <p style="color: #6B7280;"><?= date('g:i A', strtotime($ritual['scheduled_time'])) ?></p>
                </div>
                <?php endif; ?>
                
                <?php if ($ritual['venue']): ?>
                <div style="margin-bottom: 15px;">
                    <label style="font-weight: 600; color: #374151; display: block; margin-bottom: 5px;">
                        <i class="fas fa-map-marker-alt"></i> Venue
                    </label>
                    <p style="color: #6B7280;"><?= htmlspecialchars($ritual['venue']) ?></p>
                </div>
                <?php endif; ?>
                
                <?php if ($ritual['assigned_pandit_name']): ?>
                <div style="margin-bottom: 15px;">
                    <label style="font-weight: 600; color: #374151; display: block; margin-bottom: 5px;">
                        <i class="fas fa-user-tie"></i> Assigned Pandit
                    </label>
                    <p style="color: #6B7280;">
                        <?= htmlspecialchars($ritual['assigned_pandit_name']) ?>
                        <?php if ($ritual['assigned_pandit_specialization']): ?>
                        <br><small style="color: #9CA3AF;"><?= htmlspecialchars($ritual['assigned_pandit_specialization']) ?></small>
                        <?php endif; ?>
                    </p>
                </div>
                <?php endif; ?>
                
                <?php if ($ritual['status'] === 'draft'): ?>
                <form method="POST" action="/user/custom-rituals/<?= $ritual['id'] ?>/submit">
                    <?= \App\Core\Auth::csrfField() ?>
                    <button class="btn btn-success" style="width: 100%;">
                        <i class="fas fa-paper-plane"></i> Submit for Validation
                    </button>
                </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <?php if ($ritual['validation_notes']): ?>
    <div style="background: <?= $ritual['status'] === 'approved' ? '#D1FAE5' : '#FEE2E2' ?>; padding: 15px 20px; border-radius: 10px; margin-bottom: 25px;">
        <strong style="color: <?= $ritual['status'] === 'approved' ? '#065F46' : '#991B1B' ?>;">
            <i class="fas fa-<?= $ritual['status'] === 'approved' ? 'check-circle' : 'exclamation-circle' ?>"></i>
            Validator Notes:
        </strong>
        <p style="margin-top: 8px; color: <?= $ritual['status'] === 'approved' ? '#065F46' : '#991B1B' ?>;">
            <?= htmlspecialchars($ritual['validation_notes']) ?>
        </p>
        <?php if ($ritual['validator_name']): ?>
        <p style="margin-top: 10px; font-size: 0.85rem; color: #6B7280;">
            — <?= htmlspecialchars($ritual['validator_name']) ?>, 
            <?= $ritual['validated_at'] ? date('M j, Y', strtotime($ritual['validated_at'])) : '' ?>
        </p>
        <?php endif; ?>
    </div>
    <?php endif; ?>
    
    <?php if (!empty($ritual['steps'])): ?>
    <div style="border-top: 1px solid #E5E7EB; padding-top: 25px;">
        <h4 style="margin-bottom: 20px; color: #374151;">
            <i class="fas fa-list-ol"></i> Ritual Steps
        </h4>
        
        <?php foreach ($ritual['steps'] as $step): ?>
        <div style="border: 1px solid #E5E7EB; border-radius: 12px; padding: 20px; margin-bottom: 15px;">
            <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 10px;">
                <div style="width: 35px; height: 35px; background: linear-gradient(135deg, #FF6B35, #FF8C42); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 600;">
                    <?= $step['step_number'] ?>
                </div>
                <h5 style="margin: 0; color: #374151;"><?= htmlspecialchars($step['title'] ?? 'Step ' . $step['step_number']) ?></h5>
            </div>
            
            <?php if ($step['description']): ?>
            <p style="color: #6B7280; margin-left: 50px;"><?= htmlspecialchars($step['description']) ?></p>
            <?php endif; ?>
            
            <?php if (isset($step['duration_minutes']) && $step['duration_minutes'] > 0): ?>
            <p style="color: #9CA3AF; margin-left: 50px; font-size: 0.85rem;">
                <i class="fas fa-clock"></i> <?= $step['duration_minutes'] ?> minutes
            </p>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div style="text-align: center; padding: 40px; color: #6B7280; border-top: 1px solid #E5E7EB; margin-top: 20px;">
        <i class="fas fa-info-circle" style="font-size: 2rem; margin-bottom: 15px; color: #E5E7EB;"></i>
        <p>No steps defined yet. Steps will be added by the Pandit after validation.</p>
    </div>
    <?php endif; ?>
</div>
