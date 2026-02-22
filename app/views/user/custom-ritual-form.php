<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-magic"></i> Create Custom Ritual</h3>
        <a href="/user/custom-rituals" class="btn btn-sm" style="background: #E5E7EB; color: #374151;">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>
    
    <form method="POST" action="/user/custom-rituals">
        <?= \App\Core\Auth::csrfField() ?>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label>Ritual Name *</label>
                <input type="text" name="name" class="form-control" required 
                       placeholder="e.g., Family Satyanarayan Puja">
            </div>
            
            <div class="form-group">
                <label>Base Ritual (Optional)</label>
                <select name="base_ritual_id" class="form-control">
                    <option value="">Select a base ritual...</option>
                    <?php foreach ($baseRituals ?? [] as $base): ?>
                    <option value="<?= $base['id'] ?>"><?= htmlspecialchars($base['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label>Scheduled Date</label>
                <input type="date" name="scheduled_date" class="form-control" min="<?= date('Y-m-d') ?>">
            </div>
            
            <div class="form-group">
                <label>Venue</label>
                <input type="text" name="venue" class="form-control" placeholder="e.g., Home, Temple">
            </div>
            
            <div class="form-group" style="grid-column: span 2;">
                <label>Select Pandit for Validation *</label>
                <select name="assigned_pandit_id" class="form-control" required>
                    <option value="">Choose a pandit to validate your ritual...</option>
                    <?php foreach ($pandits ?? [] as $pandit): ?>
                    <option value="<?= $pandit['id'] ?>">
                        <?= htmlspecialchars($pandit['name']) ?> 
                        - <?= htmlspecialchars($pandit['specialization'] ?? 'General') ?>
                        <?php if ($pandit['average_rating'] > 0): ?>
                        (★ <?= number_format($pandit['average_rating'], 1) ?>)
                        <?php endif; ?>
                    </option>
                    <?php endforeach; ?>
                </select>
                <small style="color: #6B7280; font-size: 0.85rem;">The selected pandit will review and validate your custom ritual.</small>
            </div>
        </div>
        
        <div class="form-group">
            <label>Purpose *</label>
            <textarea name="purpose" class="form-control" rows="2" required 
                      placeholder="Why are you performing this ritual?"></textarea>
        </div>
        
        <div class="form-group">
            <label>Description</label>
            <textarea name="description" class="form-control" rows="4" 
                      placeholder="Describe the ritual, any special requirements, modifications from base ritual, etc."></textarea>
        </div>
        
        <div style="background: #FEF3C7; padding: 15px; border-radius: 10px; margin-bottom: 20px;">
            <p style="color: #92400E; font-size: 0.9rem;">
                <i class="fas fa-info-circle"></i> After submission, your custom ritual will be reviewed by a pandit for authenticity before you can proceed with booking.
            </p>
        </div>
        
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> Create Custom Ritual
        </button>
    </form>
</div>
