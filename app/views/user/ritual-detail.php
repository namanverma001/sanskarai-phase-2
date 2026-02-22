<a href="/user/rituals" class="btn btn-sm" style="background: #E5E7EB; color: #374151; margin-bottom: 20px;">
    <i class="fas fa-arrow-left"></i> Back to Rituals
</a>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 25px;">
    <div>
        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 20px;">
                <div>
                    <h2 style="font-size: 1.5rem; font-weight: 600; color: var(--dark);"><?= htmlspecialchars($ritual['name']) ?></h2>
                    <?php if ($ritual['name_sanskrit']): ?>
                    <p style="color: #6B7280; font-style: italic;"><?= htmlspecialchars($ritual['name_sanskrit']) ?></p>
                    <?php endif; ?>
                </div>
                <div>
                    <span class="badge badge-info"><?= htmlspecialchars($ritual['category']) ?></span>
                    <span class="badge badge-<?= $ritual['difficulty'] === 'easy' ? 'success' : ($ritual['difficulty'] === 'hard' ? 'danger' : 'warning') ?>">
                        <?= ucfirst($ritual['difficulty']) ?>
                    </span>
                </div>
            </div>
            
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; margin-bottom: 25px; padding: 20px; background: #F9FAFB; border-radius: 12px;">
                <div style="text-align: center;">
                    <i class="fas fa-clock" style="font-size: 1.5rem; color: var(--primary);"></i>
                    <p style="font-weight: 600; margin-top: 5px;"><?= $ritual['duration_minutes'] ?> min</p>
                    <p style="color: #6B7280; font-size: 0.8rem;">Duration</p>
                </div>
                <?php if ($ritual['deity']): ?>
                <div style="text-align: center;">
                    <i class="fas fa-pray" style="font-size: 1.5rem; color: var(--primary);"></i>
                    <p style="font-weight: 600; margin-top: 5px;"><?= htmlspecialchars($ritual['deity']) ?></p>
                    <p style="color: #6B7280; font-size: 0.8rem;">Deity</p>
                </div>
                <?php endif; ?>
                <?php if ($ritual['best_time']): ?>
                <div style="text-align: center;">
                    <i class="fas fa-sun" style="font-size: 1.5rem; color: var(--primary);"></i>
                    <p style="font-weight: 600; margin-top: 5px;"><?= htmlspecialchars($ritual['best_time']) ?></p>
                    <p style="color: #6B7280; font-size: 0.8rem;">Best Time</p>
                </div>
                <?php endif; ?>
            </div>
            
            <?php if ($ritual['description']): ?>
            <div style="margin-bottom: 25px;">
                <h4 style="margin-bottom: 10px;"><i class="fas fa-info-circle"></i> Description</h4>
                <p style="color: #4B5563; line-height: 1.7;"><?= nl2br(htmlspecialchars($ritual['description'])) ?></p>
            </div>
            <?php endif; ?>
            
            <?php if ($ritual['significance']): ?>
            <div style="margin-bottom: 25px;">
                <h4 style="margin-bottom: 10px;"><i class="fas fa-star"></i> Significance</h4>
                <p style="color: #4B5563; line-height: 1.7;"><?= nl2br(htmlspecialchars($ritual['significance'])) ?></p>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($ritual['steps'])): ?>
            <div style="margin-bottom: 25px;">
                <h4 style="margin-bottom: 15px;"><i class="fas fa-list-ol"></i> Ritual Steps</h4>
                <?php foreach ($ritual['steps'] as $step): ?>
                <div style="display: flex; gap: 15px; margin-bottom: 15px; padding: 15px; background: #F9FAFB; border-radius: 10px;">
                    <div style="width: 40px; height: 40px; background: var(--primary); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 600; flex-shrink: 0;">
                        <?= $step['step_number'] ?>
                    </div>
                    <div>
                        <h5 style="margin-bottom: 5px;"><?= htmlspecialchars($step['title']) ?></h5>
                        <p style="color: #6B7280; font-size: 0.9rem;"><?= nl2br(htmlspecialchars($step['description'] ?? '')) ?></p>
                        <?php if ($step['mantra']): ?>
                        <p style="margin-top: 10px; padding: 10px; background: #FEF3C7; border-radius: 6px; font-style: italic;">
                            <strong>Mantra:</strong> <?= htmlspecialchars($step['mantra']) ?>
                        </p>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <div>
        <?php if (!empty($ritual['items'])): ?>
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-shopping-basket"></i> Required Items</h3>
            </div>
            <ul style="list-style: none; padding: 0;">
                <?php foreach ($ritual['items'] as $item): ?>
                <li style="padding: 10px 0; border-bottom: 1px solid #E5E7EB; display: flex; justify-content: space-between;">
                    <span>
                        <?= htmlspecialchars($item['item_name']) ?>
                        <?php if ($item['is_mandatory']): ?><span class="badge badge-danger" style="font-size: 0.65rem;">Required</span><?php endif; ?>
                    </span>
                    <span style="color: #6B7280;"><?= $item['quantity'] ?> <?= $item['unit'] ?></span>
                </li>
                <?php endforeach; ?>
            </ul>
            <a href="/user/shopping-list/generate/<?= $ritual['id'] ?>" class="btn btn-success" style="width: 100%; margin-top: 15px;">
                <i class="fas fa-cart-plus"></i> Add to Shopping List
            </a>
        </div>
        <?php endif; ?>
        
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-pray"></i> Book a Pandit</h3>
            </div>
            <p style="color: #6B7280; margin-bottom: 15px;">Need help performing this ritual? Book an experienced pandit.</p>
            <form method="POST" action="/user/book-pandit">
                <?= \App\Core\Auth::csrfField() ?>
                <input type="hidden" name="ritual_id" value="<?= $ritual['id'] ?>">
                
                <div class="form-group">
                    <label>Select Pandit</label>
                    <select name="pandit_id" class="form-control" required>
                        <option value="">Choose a pandit...</option>
                        <?php foreach ($pandits ?? [] as $pandit): ?>
                        <option value="<?= $pandit['id'] ?>">
                            <?= htmlspecialchars($pandit['name']) ?> 
                            (<?= $pandit['specialization'] ?? 'General' ?> - ★<?= number_format($pandit['average_rating'] ?? 0, 1) ?>)
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Preferred Date</label>
                    <input type="date" name="scheduled_date" class="form-control" required 
                           min="<?= date('Y-m-d') ?>">
                </div>
                
                <div class="form-group">
                    <label>Preferred Time</label>
                    <input type="time" name="scheduled_time" class="form-control">
                </div>
                
                <div class="form-group">
                    <label>Venue</label>
                    <input type="text" name="venue" class="form-control" placeholder="Your address">
                </div>
                
                <button type="submit" class="btn btn-primary" style="width: 100%;">
                    <i class="fas fa-calendar-plus"></i> Request Booking
                </button>
            </form>
        </div>
    </div>
</div>
