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
        <input type="password" id="password" name="password" class="form-control" 
               placeholder="Enter your password" required>
    </div>
    
    <button type="submit" class="btn btn-primary">
        <i class="fas fa-sign-in-alt"></i> Sign In
    </button>
</form>

<div class="auth-links">
    <p>Don't have an account? <a href="/signup">Create Account</a></p>
    <p style="margin-top: 12px;"><a href="/forgot-password"><i class="fas fa-key"></i> Forgot Password?</a></p>
</div>
