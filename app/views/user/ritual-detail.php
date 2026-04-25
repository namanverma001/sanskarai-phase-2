<style>
    /* Mobile responsive fix for ritual detail page */
    @media (max-width: 768px) {
        .ritual-detail-grid {
            grid-template-columns: 1fr !important;
        }
    }

    /* Save to My Rituals Button */
    .btn-save-ritual {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        padding: 16px 24px;
        background: linear-gradient(135deg, #10B981 0%, #059669 100%);
        color: white;
        border: none;
        border-radius: 12px;
        font-size: 1.05rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
    }

    .btn-save-ritual:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(16, 185, 129, 0.4);
    }

    .btn-save-ritual:active {
        transform: translateY(0);
    }

    .btn-save-ritual.saved {
        background: linear-gradient(135deg, #6B7280 0%, #4B5563 100%);
        cursor: default;
        box-shadow: 0 4px 15px rgba(107, 114, 128, 0.3);
    }

    .btn-save-ritual.saved:hover {
        transform: none;
        box-shadow: 0 4px 15px rgba(107, 114, 128, 0.3);
    }

    .btn-save-ritual .spinner {
        display: none;
        width: 20px;
        height: 20px;
        border: 3px solid rgba(255,255,255,0.3);
        border-top-color: white;
        border-radius: 50%;
        animation: spin-btn 0.8s linear infinite;
    }

    .btn-save-ritual.loading .spinner {
        display: inline-block;
    }

    .btn-save-ritual.loading .btn-icon,
    .btn-save-ritual.loading .btn-label {
        display: none;
    }

    @keyframes spin-btn {
        to { transform: rotate(360deg); }
    }

    /* Toast Notification */
    .ritual-toast {
        position: fixed;
        bottom: 30px;
        right: 30px;
        padding: 16px 24px;
        border-radius: 12px;
        color: white;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 10px;
        z-index: 1001;
        transform: translateX(120%);
        transition: transform 0.4s cubic-bezier(0.68, -0.55, 0.27, 1.55);
        box-shadow: 0 8px 30px rgba(0,0,0,0.15);
    }

    .ritual-toast.show {
        transform: translateX(0);
    }

    .ritual-toast.success {
        background: linear-gradient(135deg, #10B981 0%, #059669 100%);
    }

    .ritual-toast.error {
        background: linear-gradient(135deg, #EF4444 0%, #DC2626 100%);
    }

    .ritual-toast.info {
        background: linear-gradient(135deg, #6366F1 0%, #4F46E5 100%);
    }
</style>

<?php $isGuest = $isGuest ?? false; ?>
<a href="<?= $isGuest ? '/explore' : '/user/rituals' ?>" class="btn btn-sm" style="background: #E5E7EB; color: #374151; margin-bottom: 20px;">
    <i class="fas fa-arrow-left"></i> Back to Rituals
</a>

<div class="ritual-detail-grid" style="display: grid; grid-template-columns: 2fr 1fr; gap: 25px;">
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
        <!-- ★ Save to My Rituals Card -->
        <div class="card" style="background: linear-gradient(135deg, #ECFDF5 0%, #D1FAE5 100%); border: 2px solid #6EE7B7; margin-bottom: 20px;">
            <div style="text-align: center; padding: 10px 0 5px;">
                <i class="fas fa-bookmark" style="font-size: 2rem; color: #059669; margin-bottom: 10px;"></i>
                <?php if ($isGuest): ?>
                <h4 style="color: #065F46; margin-bottom: 8px;">🙏 Create Your Profile</h4>
                <p style="color: #047857; font-size: 0.85rem; margin-bottom: 18px;">
                    Sign up for free to save this ritual, track progress, download PDF, and get personalized recommendations!
                </p>
                <a href="/signup" class="btn-save-ritual" style="text-decoration: none; background: linear-gradient(135deg, #FF6B35 0%, #F59E0B 100%); box-shadow: 0 4px 15px rgba(255,107,53,0.3);">
                    <i class="fas fa-user-plus btn-icon"></i>
                    <span class="btn-label">Sign Up to Save</span>
                </a>
                <?php else: ?>
                <h4 style="color: #065F46; margin-bottom: 8px;">Save to My Rituals</h4>
                <p style="color: #047857; font-size: 0.85rem; margin-bottom: 18px;">
                    Add this ritual to your personal collection to start performing it, track progress, and download PDF.
                </p>
                <button id="btnSaveToMyRituals" class="btn-save-ritual" onclick="saveToMyRituals(<?= $ritual['id'] ?>)">
                    <span class="spinner"></span>
                    <i class="fas fa-plus-circle btn-icon"></i>
                    <span class="btn-label">Save to My Rituals</span>
                </button>
                <?php endif; ?>
            </div>
        </div>

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
            <?php if ($isGuest): ?>
            <a href="/signup" class="btn btn-success" style="width: 100%; margin-top: 15px; text-decoration: none;">
                <i class="fas fa-user-plus"></i> Sign Up for Shopping List
            </a>
            <?php else: ?>
            <a href="/user/shopping-list/generate/<?= $ritual['id'] ?>" class="btn btn-success" style="width: 100%; margin-top: 15px;">
                <i class="fas fa-cart-plus"></i> Add to Shopping List
            </a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        
        <?php if ($isGuest): ?>
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-pray"></i> Book a Pandit</h3>
            </div>
            <p style="color: #6B7280; margin-bottom: 15px;">Need help performing this ritual? Sign up to book an experienced pandit.</p>
            <a href="/signup" class="btn btn-primary" style="width: 100%; text-decoration: none;">
                <i class="fas fa-user-plus"></i> Sign Up to Book a Pandit
            </a>
        </div>
        <?php else: ?>
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
                    <input type="date" name="scheduled_date" class="form-control" required min="<?= date('Y-m-d') ?>">
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
        <?php endif; ?>
    </div>
</div>

<!-- Toast Notification -->
<div id="ritualToast" class="ritual-toast"></div>

<script>
    const csrfToken = '<?= \App\Core\Auth::csrfToken() ?>';

    function showRitualToast(message, type = 'success') {
        const toast = document.getElementById('ritualToast');
        toast.innerHTML = `<i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i> ${message}`;
        toast.className = `ritual-toast ${type} show`;
        setTimeout(() => {
            toast.classList.remove('show');
        }, 3500);
    }

    async function saveToMyRituals(globalRitualId) {
        const btn = document.getElementById('btnSaveToMyRituals');

        // Prevent double clicks
        if (btn.classList.contains('loading') || btn.classList.contains('saved')) {
            return;
        }

        btn.classList.add('loading');

        const formData = new FormData();
        formData.append('csrf_token', csrfToken);
        formData.append('global_ritual_id', globalRitualId);

        try {
            const response = await fetch('/user/my-rituals/add', {
                method: 'POST',
                body: formData
            });
            const data = await response.json();

            btn.classList.remove('loading');

            if (data.success) {
                btn.classList.add('saved');
                btn.innerHTML = '<i class="fas fa-check-circle"></i> <span>Saved to My Rituals!</span>';
                showRitualToast('Ritual saved to your collection! You can find it in My Rituals.', 'success');

                // After a moment, show a "Go to My Rituals" link
                setTimeout(() => {
                    btn.onclick = null;
                    btn.innerHTML = '<i class="fas fa-folder-open"></i> <span>View in My Rituals</span>';
                    btn.style.cursor = 'pointer';
                    btn.classList.remove('saved');
                    btn.style.background = 'linear-gradient(135deg, #6366F1 0%, #4F46E5 100%)';
                    btn.style.boxShadow = '0 4px 15px rgba(99, 102, 241, 0.3)';
                    btn.addEventListener('click', () => {
                        window.location.href = '/user/my-rituals/' + data.user_ritual_id;
                    });
                }, 2000);
            } else if (data.already_added) {
                btn.classList.add('saved');
                btn.innerHTML = '<i class="fas fa-check-circle"></i> <span>Already in My Rituals</span>';
                showRitualToast('This ritual is already in your collection!', 'info');

                // Show link to go to it
                setTimeout(() => {
                    btn.onclick = null;
                    btn.innerHTML = '<i class="fas fa-folder-open"></i> <span>View in My Rituals</span>';
                    btn.style.cursor = 'pointer';
                    btn.classList.remove('saved');
                    btn.style.background = 'linear-gradient(135deg, #6366F1 0%, #4F46E5 100%)';
                    btn.style.boxShadow = '0 4px 15px rgba(99, 102, 241, 0.3)';
                    btn.addEventListener('click', () => {
                        window.location.href = '/user/my-rituals/' + data.user_ritual_id;
                    });
                }, 2000);
            } else {
                showRitualToast(data.error || 'Failed to save ritual', 'error');
            }
        } catch (error) {
            btn.classList.remove('loading');
            showRitualToast('Error: ' + error.message, 'error');
        }
    }
</script>
