<div class="auth-header">
    <div class="auth-icon"><i class="fas fa-sign-in-alt"></i></div>
    <h1>Welcome Back</h1>
    <p>Sign in to continue your spiritual journey</p>
</div>

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

<form method="POST" action="/login">
    <?= \App\Core\Auth::csrfField() ?>
    
    <div class="form-group">
        <label for="identifier"><i class="fas fa-user"></i> Email or Mobile</label>
        <input type="text" id="identifier" name="identifier" class="form-control" 
               placeholder="Enter email or mobile number" required autofocus>
    </div>
    
    <div class="form-group">
        <label for="password"><i class="fas fa-lock"></i> Password</label>
        <div class="password-input-wrapper">
            <input type="password" id="password" name="password" class="form-control" 
                   placeholder="Enter your password" required>
            <button type="button" class="password-toggle" onclick="togglePassword('password', this)" aria-label="Show password">
                <i class="fas fa-eye"></i>
            </button>
        </div>
    </div>
    
    <button type="submit" class="btn btn-primary">
        <i class="fas fa-sign-in-alt"></i> Sign In
    </button>
</form>

<div class="auth-links">
    <p>Don't have an account? <a href="/signup">Create Account</a></p>
    <p style="margin-top: 12px;"><a href="/forgot-password"><i class="fas fa-key"></i> Forgot Password?</a></p>
</div>

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
