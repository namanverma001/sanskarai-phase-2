<?php
$invalid = $invalid ?? false;
$token   = $token ?? '';
$email   = $email ?? '';
?>

<div class="auth-header">
    <div class="auth-icon"><i class="fas fa-lock-open"></i></div>
    <?php if ($invalid): ?>
        <h1>Link Expired</h1>
        <p>This password reset link is invalid or has expired.</p>
    <?php else: ?>
        <h1>Set New Password</h1>
        <p>Enter your new password below.</p>
    <?php endif; ?>
</div>

<?php if (!$invalid && $email): ?>
<div style="
    background: rgba(255,107,53,0.1);
    border: 1px solid rgba(255,107,53,0.3);
    border-radius: 10px;
    padding: 12px 16px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 0.9rem;
">
    <i class="fas fa-envelope" style="color: #FF6B35;"></i>
    <span>Resetting password for: <strong><?= htmlspecialchars($email) ?></strong></span>
</div>
<?php endif; ?>

<?php if (isset($_SESSION['flash']['success'])): ?>
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i> <?= htmlspecialchars($_SESSION['flash']['success']) ?>
    </div>
    <?php unset($_SESSION['flash']['success']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['flash']['error'])): ?>
    <div class="alert alert-error">
        <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($_SESSION['flash']['error']) ?>
    </div>
    <?php unset($_SESSION['flash']['error']); ?>
<?php endif; ?>

<?php if ($invalid): ?>
    <!-- Token is invalid/expired — show link to try again -->
    <div class="auth-links" style="margin-top: 20px;">
        <p><a href="/forgot-password"><i class="fas fa-redo"></i> Request a new reset link</a></p>
        <p><a href="/login"><i class="fas fa-arrow-left"></i> Back to Sign In</a></p>
    </div>
<?php else: ?>
    <!-- Reset password form -->
    <form method="POST" action="/reset-password">
        <?= \App\Core\Auth::csrfField() ?>

        <!-- Pass the token back as a hidden field -->
        <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

        <div class="form-group">
            <label for="password"><i class="fas fa-lock"></i> New Password</label>
            <div class="password-input-wrapper">
                <input type="password" id="password" name="password" class="form-control"
                       placeholder="At least 8 characters" required minlength="8" autofocus>
                <button type="button" class="password-toggle" onclick="togglePassword('password', this)" aria-label="Show password">
                    <i class="fas fa-eye"></i>
                </button>
            </div>
        </div>

        <div class="form-group">
            <label for="password_confirmation"><i class="fas fa-lock"></i> Confirm New Password</label>
            <div class="password-input-wrapper">
                <input type="password" id="password_confirmation" name="password_confirmation" class="form-control"
                       placeholder="Repeat your new password" required minlength="8">
                <button type="button" class="password-toggle" onclick="togglePassword('password_confirmation', this)" aria-label="Show password">
                    <i class="fas fa-eye"></i>
                </button>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> Reset Password
        </button>
    </form>

    <div class="auth-links">
        <p><a href="/login"><i class="fas fa-arrow-left"></i> Back to Sign In</a></p>
    </div>
<?php endif; ?>

<style>
.password-input-wrapper {
    position: relative;
    display: flex;
    align-items: center;
}
.password-input-wrapper .form-control {
    padding-right: 45px;
}
.password-toggle {
    position: absolute;
    right: 12px;
    background: none;
    border: none;
    color: #9CA3AF;
    cursor: pointer;
    font-size: 1rem;
    padding: 5px;
    transition: color 0.2s;
}
.password-toggle:hover {
    color: #FF6B35;
}
</style>

<script>
function togglePassword(fieldId, btn) {
    const input = document.getElementById(fieldId);
    const icon = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'fas fa-eye-slash';
    } else {
        input.type = 'password';
        icon.className = 'fas fa-eye';
    }
}
</script>
