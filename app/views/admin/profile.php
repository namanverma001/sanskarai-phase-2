<div class="admin-grid">
    <!-- Profile Details -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-id-card"></i> My Profile</h3>
        </div>
        
        <div style="text-align: center; padding: 20px 0;">
            <div style="width: 100px; height: 100px; background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 2.5rem; margin: 0 auto 15px;">
                <?= strtoupper(substr($user['name'], 0, 1)) ?>
            </div>
            <h2 style="font-size: 1.5rem; color: var(--dark); margin-bottom: 5px;"><?= htmlspecialchars($user['name']) ?></h2>
            <span class="badge badge-success" style="font-size: 0.9rem; padding: 5px 12px;"><?= ucfirst($user['role']) ?></span>
            
            <div style="margin-top: 25px; text-align: left; padding: 0 15px;">
                <div style="margin-bottom: 15px; border-bottom: 1px solid #F3F4F6; padding-bottom: 10px;">
                    <label style="color: #6B7280; font-size: 0.85rem; display: block; margin-bottom: 4px;">Email Address</label>
                    <div style="font-weight: 500; color: var(--dark);">
                        <i class="fas fa-envelope" style="color: var(--primary); width: 20px;"></i> 
                        <?= htmlspecialchars($user['email']) ?>
                    </div>
                </div>
                
                <div style="margin-bottom: 15px; border-bottom: 1px solid #F3F4F6; padding-bottom: 10px;">
                    <label style="color: #6B7280; font-size: 0.85rem; display: block; margin-bottom: 4px;">Mobile Number</label>
                    <div style="font-weight: 500; color: var(--dark);">
                        <i class="fas fa-phone" style="color: var(--primary); width: 20px;"></i>
                        <?= htmlspecialchars($user['mobile'] ?? 'Not provided') ?>
                    </div>
                </div>
                
                <div>
                    <label style="color: #6B7280; font-size: 0.85rem; display: block; margin-bottom: 4px;">Member Since</label>
                    <div style="font-weight: 500; color: var(--dark);">
                        <i class="fas fa-calendar-alt" style="color: var(--primary); width: 20px;"></i>
                        <?= date('d M Y', strtotime($user['created_at'])) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Change Password -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-key"></i> Change Password</h3>
        </div>
        
        <form method="POST" action="/admin/profile/password">
            <?= \App\Core\Auth::csrfField() ?>
            
            <?php $errors = $_SESSION['flash']['errors'] ?? []; unset($_SESSION['flash']['errors']); ?>
            
            <div class="form-group">
                <label for="old_password">Current Password <span style="color: var(--danger);">*</span></label>
                <div style="position: relative;">
                    <input type="password" id="old_password" name="old_password" class="form-control" placeholder="Enter current password" required>
                    <button type="button" onclick="togglePassword('old_password')" class="toggle-password-btn">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                <?php if (!empty($errors['old_password'])): ?>
                    <small style="color: var(--danger); margin-top: 4px; display: block;"><?= htmlspecialchars($errors['old_password'] ?? $errors['old_password'][0] ?? '') ?></small>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="new_password">New Password <span style="color: var(--danger);">*</span></label>
                <div style="position: relative;">
                    <input type="password" id="new_password" name="new_password" class="form-control" placeholder="Enter new password (min 6 chars)" required minlength="6">
                    <button type="button" onclick="togglePassword('new_password')" class="toggle-password-btn">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                <?php if (!empty($errors['new_password'])): ?>
                    <small style="color: var(--danger); margin-top: 4px; display: block;"><?= htmlspecialchars($errors['new_password'] ?? $errors['new_password'][0] ?? '') ?></small>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Confirm New Password <span style="color: var(--danger);">*</span></label>
                <div style="position: relative;">
                    <input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="Re-enter new password" required minlength="6">
                    <button type="button" onclick="togglePassword('confirm_password')" class="toggle-password-btn">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                <?php if (!empty($errors['confirm_password'])): ?>
                    <small style="color: var(--danger); margin-top: 4px; display: block;"><?= htmlspecialchars($errors['confirm_password'] ?? $errors['confirm_password'][0] ?? '') ?></small>
                <?php endif; ?>
            </div>
            
            <div style="margin-top: 25px; padding-top: 20px; border-top: 1px solid #F3F4F6;">
                <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 12px;">
                    <i class="fas fa-save"></i> Update Password
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    .admin-grid { display: grid; grid-template-columns: 1fr 2fr; gap: 25px; }
    
    .toggle-password-btn {
        position: absolute; 
        right: 12px; 
        top: 50%; 
        transform: translateY(-50%); 
        background: none; 
        border: none; 
        color: #6B7280; 
        cursor: pointer; 
        padding: 5px;
    }
    
    @media (max-width: 900px) {
        .admin-grid { grid-template-columns: 1fr; }
    }
</style>

<script>
    function togglePassword(inputId) {
        const input = document.getElementById(inputId);
        const icon = input.nextElementSibling.querySelector('i');
        
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
</script>
