<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-user"></i> My Profile</h3>
    </div>
    
    <form method="POST" action="/pandit/profile">
        <?= \App\Core\Auth::csrfField() ?>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label>Name</label>
                <input type="text" class="form-control" value="<?= htmlspecialchars($profile['name'] ?? '') ?>" disabled>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" class="form-control" value="<?= htmlspecialchars($profile['email'] ?? '') ?>" disabled>
            </div>
            <div class="form-group">
                <label>Specialization</label>
                <input type="text" name="specialization" class="form-control" value="<?= htmlspecialchars($profile['specialization'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Experience (Years)</label>
                <input type="number" name="experience_years" class="form-control" value="<?= $profile['experience_years'] ?? 0 ?>" min="0">
            </div>
            <div class="form-group">
                <label>Languages</label>
                <input type="text" name="languages" class="form-control" value="<?= htmlspecialchars($profile['languages'] ?? 'Hindi') ?>">
            </div>
            <div class="form-group">
                <label>Hourly Rate (₹)</label>
                <input type="number" name="hourly_rate" class="form-control" value="<?= $profile['hourly_rate'] ?? '' ?>" min="0">
            </div>
        </div>
        
        <div class="form-group">
            <label>Bio</label>
            <textarea name="bio" class="form-control" rows="4"><?= htmlspecialchars($profile['bio'] ?? '') ?></textarea>
        </div>
        
        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 20px; padding-top: 20px; border-top: 1px solid #E5E7EB;">
            <div>
                <span class="badge badge-<?= ($profile['approval_status'] ?? '') === 'approved' ? 'success' : 'warning' ?>">
                    <?= ucfirst($profile['approval_status'] ?? 'pending') ?>
                </span>
                <span style="margin-left: 15px; color: #6B7280;">
                    <i class="fas fa-star" style="color: #F59E0B;"></i> <?= number_format($profile['average_rating'] ?? 0, 1) ?>
                    | <?= $profile['total_rituals_performed'] ?? 0 ?> rituals performed
                </span>
            </div>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Update Profile
            </button>
        </div>
    </form>
</div>

<div class="card" style="margin-top: 20px;">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-lock"></i> Change Password</h3>
    </div>
    
    <form method="POST" action="/pandit/profile/password">
        <?= \App\Core\Auth::csrfField() ?>
        
        <div class="form-group">
            <label>Current Password</label>
            <div style="position: relative;">
                <input type="password" name="current_password" class="form-control" id="current_password" required style="padding-right: 40px;">
                <span class="toggle-password" data-target="current_password" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #6B7280; z-index: 10;">
                    <i class="fas fa-eye"></i>
                </span>
            </div>
        </div>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label>New Password</label>
                <div style="position: relative;">
                    <input type="password" name="new_password" class="form-control" id="new_password" required minlength="8" style="padding-right: 40px;">
                    <span class="toggle-password" data-target="new_password" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #6B7280; z-index: 10;">
                        <i class="fas fa-eye"></i>
                    </span>
                </div>
            </div>
            <div class="form-group">
                <label>Confirm New Password</label>
                <div style="position: relative;">
                    <input type="password" name="confirm_password" class="form-control" id="confirm_password" required minlength="8" style="padding-right: 40px;">
                    <span class="toggle-password" data-target="confirm_password" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #6B7280; z-index: 10;">
                        <i class="fas fa-eye"></i>
                    </span>
                </div>
            </div>
        </div>
        
        <div style="margin-top: 20px; text-align: right;">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-key"></i> Update Password
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const toggles = document.querySelectorAll('.toggle-password');
    
    toggles.forEach(toggle => {
        toggle.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const input = document.getElementById(targetId);
            const icon = this.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    });
});
</script>
