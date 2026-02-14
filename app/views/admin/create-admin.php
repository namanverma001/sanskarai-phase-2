<?php $old = $_SESSION['flash']['old'] ?? []; $errors = $_SESSION['flash']['errors'] ?? []; unset($_SESSION['flash']['old'], $_SESSION['flash']['errors']); ?>

<!-- Security Notice -->
<div class="card" style="border-left: 4px solid #F59E0B; margin-bottom: 25px;">
    <div style="display: flex; align-items: center; gap: 15px;">
        <div style="width: 50px; height: 50px; background: rgba(245,158,11,0.1); border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
            <i class="fas fa-shield-alt" style="font-size: 1.4rem; color: #F59E0B;"></i>
        </div>
        <div>
            <h3 style="font-size: 1.1rem; font-weight: 600; color: #92400E; margin-bottom: 4px;">Security Notice</h3>
            <p style="color: #6B7280; font-size: 0.9rem; margin: 0;">Creating an admin account grants full system access. You must provide the secret admin creation key to authorize this action. Keep this key confidential.</p>
        </div>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 25px;">
    <!-- Create Admin Form -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-user-shield"></i> New Admin Account</h3>
        </div>
        
        <form method="POST" action="/admin/create-admin" id="createAdminForm">
            <?= \App\Core\Auth::csrfField() ?>
            
            <div class="form-group">
                <label for="name"><i class="fas fa-user" style="color: var(--primary); margin-right: 6px;"></i>Full Name <span style="color: var(--danger);">*</span></label>
                <input type="text" id="name" name="name" class="form-control" placeholder="Enter full name" value="<?= htmlspecialchars($old['name'] ?? '') ?>" required>
                <?php if (!empty($errors['name'])): ?>
                    <small style="color: var(--danger); margin-top: 4px; display: block;"><?= htmlspecialchars($errors['name'][0]) ?></small>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="email"><i class="fas fa-envelope" style="color: var(--primary); margin-right: 6px;"></i>Email Address <span style="color: var(--danger);">*</span></label>
                <input type="email" id="email" name="email" class="form-control" placeholder="admin@example.com" value="<?= htmlspecialchars($old['email'] ?? '') ?>" required>
                <?php if (!empty($errors['email'])): ?>
                    <small style="color: var(--danger); margin-top: 4px; display: block;"><?= htmlspecialchars($errors['email'][0]) ?></small>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="mobile"><i class="fas fa-phone" style="color: var(--primary); margin-right: 6px;"></i>Mobile Number <span style="color: var(--danger);">*</span></label>
                <input type="text" id="mobile" name="mobile" class="form-control" placeholder="9876543210" value="<?= htmlspecialchars($old['mobile'] ?? '') ?>" required>
                <?php if (!empty($errors['mobile'])): ?>
                    <small style="color: var(--danger); margin-top: 4px; display: block;"><?= htmlspecialchars($errors['mobile'][0]) ?></small>
                <?php endif; ?>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div class="form-group">
                    <label for="password"><i class="fas fa-lock" style="color: var(--primary); margin-right: 6px;"></i>Password <span style="color: var(--danger);">*</span></label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="Min 6 characters" required minlength="6">
                    <?php if (!empty($errors['password'])): ?>
                        <small style="color: var(--danger); margin-top: 4px; display: block;"><?= htmlspecialchars($errors['password'][0]) ?></small>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label for="password_confirmation"><i class="fas fa-lock" style="color: var(--primary); margin-right: 6px;"></i>Confirm Password <span style="color: var(--danger);">*</span></label>
                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" placeholder="Repeat password" required minlength="6">
                </div>
            </div>
            
            <div class="form-group" style="margin-top: 10px;">
                <label for="secret_key"><i class="fas fa-key" style="color: #F59E0B; margin-right: 6px;"></i>Admin Secret Key <span style="color: var(--danger);">*</span></label>
                <div style="position: relative;">
                    <input type="password" id="secret_key" name="secret_key" class="form-control" placeholder="Enter the secret admin creation key" required style="padding-right: 45px;">
                    <button type="button" onclick="toggleSecretKey()" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; color: #6B7280; cursor: pointer; padding: 5px;">
                        <i class="fas fa-eye" id="secretKeyIcon"></i>
                    </button>
                </div>
                <?php if (!empty($errors['secret_key'])): ?>
                    <small style="color: var(--danger); margin-top: 4px; display: block;"><?= htmlspecialchars($errors['secret_key'][0]) ?></small>
                <?php endif; ?>
                <small style="color: #6B7280; margin-top: 4px; display: block;"><i class="fas fa-info-circle"></i> This key is defined in the server's environment configuration.</small>
            </div>
            
            <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; margin-top: 10px; padding: 14px;">
                <i class="fas fa-user-shield"></i> Create Admin Account
            </button>
        </form>
    </div>
    
    <!-- Existing Admins -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-users-cog"></i> Existing Admins</h3>
            <span class="badge badge-info"><?= count($admins ?? []) ?> Admin<?= count($admins ?? []) !== 1 ? 's' : '' ?></span>
        </div>
        
        <?php if (empty($admins)): ?>
            <p style="color: #6B7280; text-align: center; padding: 30px;">No admin accounts found.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Admin</th>
                            <th>Mobile</th>
                            <th>Status</th>
                            <th>Created</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($admins as $admin): ?>
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <div style="width: 40px; height: 40px; background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; flex-shrink: 0;">
                                        <?= strtoupper(substr($admin['name'], 0, 1)) ?>
                                    </div>
                                    <div>
                                        <strong><?= htmlspecialchars($admin['name']) ?></strong><br>
                                        <small style="color: #6B7280;"><?= htmlspecialchars($admin['email']) ?></small>
                                    </div>
                                </div>
                            </td>
                            <td style="color: #6B7280;"><?= htmlspecialchars($admin['mobile'] ?? 'N/A') ?></td>
                            <td>
                                <span class="badge badge-<?= $admin['status'] === 'active' ? 'success' : 'danger' ?>">
                                    <?= ucfirst($admin['status']) ?>
                                </span>
                            </td>
                            <td style="color: #6B7280; font-size: 0.85rem;">
                                <?= date('d M Y', strtotime($admin['created_at'])) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
    @media (max-width: 768px) {
        div[style*="grid-template-columns: 1fr 1fr"] {
            grid-template-columns: 1fr !important;
        }
    }
</style>

<script>
    function toggleSecretKey() {
        const input = document.getElementById('secret_key');
        const icon = document.getElementById('secretKeyIcon');
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
