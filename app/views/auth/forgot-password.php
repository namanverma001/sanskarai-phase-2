<div class="auth-header">
    <div class="auth-icon"><i class="fas fa-key"></i></div>
    <h1>Forgot Password?</h1>
    <p>No worries, we'll send you reset instructions</p>
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

<form method="POST" action="/forgot-password">
    <?= \App\Core\Auth::csrfField() ?>
    
    <div class="form-group">
        <label for="email"><i class="fas fa-envelope"></i> Email Address</label>
        <input type="email" id="email" name="email" class="form-control" 
               placeholder="Enter your registered email" required autofocus>
    </div>
    
    <button type="submit" class="btn btn-primary">
        <i class="fas fa-paper-plane"></i> Send Reset Link
    </button>
</form>

<div class="auth-links">
    <p><a href="/login"><i class="fas fa-arrow-left"></i> Back to Sign In</a></p>
</div>
