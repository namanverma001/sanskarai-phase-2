<div class="auth-header">
    <div class="auth-icon"><i class="fas fa-user-plus"></i></div>
    <h1>Create Account</h1>
    <p>Join our community and explore sacred traditions</p>
</div>

<?php if (isset($_SESSION['flash']['error'])): ?>
    <div class="alert alert-error">
        <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($_SESSION['flash']['error']) ?>
        <?php if (isset($_SESSION['flash']['errors']) && is_array($_SESSION['flash']['errors'])): ?>
        <ul>
            <?php foreach ($_SESSION['flash']['errors'] as $field => $fieldErrors): ?>
                <?php foreach ($fieldErrors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </ul>
        <?php endif; ?>
    </div>
    <?php unset($_SESSION['flash']['error'], $_SESSION['flash']['errors']); ?>
<?php endif; ?>

<form method="POST" action="/signup">
    <?= \App\Core\Auth::csrfField() ?>
    
    <div class="form-group">
        <label>I am a</label>
        <div class="role-selector">
            <label class="role-option active">
                <input type="radio" name="role" value="user" checked>
                <i class="fas fa-user"></i>
                <span>User</span>
            </label>
            <label class="role-option">
                <input type="radio" name="role" value="pandit">
                <i class="fas fa-pray"></i>
                <span>Pandit</span>
            </label>
        </div>
    </div>
    
    <div class="form-group">
        <label for="name"><i class="fas fa-user-circle"></i> Full Name</label>
        <input type="text" id="name" name="name" class="form-control" 
               placeholder="Enter your full name" required 
               value="<?= htmlspecialchars($_SESSION['flash']['old']['name'] ?? '') ?>">
    </div>
    
    <div class="form-group">
        <label for="email"><i class="fas fa-envelope"></i> Email Address</label>
        <input type="email" id="email" name="email" class="form-control" 
               placeholder="Enter your email" required
               value="<?= htmlspecialchars($_SESSION['flash']['old']['email'] ?? '') ?>">
    </div>
    
    <div class="form-group">
        <label for="mobile"><i class="fas fa-phone"></i> Mobile Number</label>
        <input type="tel" id="mobile" name="mobile" class="form-control" 
               placeholder="Enter 10-digit mobile number" required pattern="[0-9]{10}"
               value="<?= htmlspecialchars($_SESSION['flash']['old']['mobile'] ?? '') ?>">
    </div>
    
    <div class="form-group">
        <label for="password"><i class="fas fa-lock"></i> Password</label>
        <input type="password" id="password" name="password" class="form-control" 
               placeholder="Minimum 6 characters" required minlength="6">
    </div>
    
    <div class="form-group">
        <label for="password_confirmation"><i class="fas fa-lock"></i> Confirm Password</label>
        <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" 
               placeholder="Confirm your password" required>
    </div>
    
    <div class="pandit-fields">
        <div class="form-group">
            <label for="specialization"><i class="fas fa-star"></i> Specialization</label>
            <input type="text" id="specialization" name="specialization" class="form-control" 
                   placeholder="e.g., Vedic Rituals, Marriage Ceremonies">
        </div>
        
        <div class="form-group">
            <label for="experience_years"><i class="fas fa-calendar-alt"></i> Years of Experience</label>
            <input type="number" id="experience_years" name="experience_years" class="form-control" 
                   placeholder="Years of experience" min="0" max="50">
        </div>
        
        <div class="form-group">
            <label for="bio"><i class="fas fa-info-circle"></i> Brief Bio</label>
            <textarea id="bio" name="bio" class="form-control" rows="3" 
                      placeholder="Tell us about yourself and your experience..."></textarea>
        </div>
    </div>
    
    <button type="submit" class="btn btn-primary">
        <i class="fas fa-user-plus"></i> Create Account
    </button>
</form>

<div class="auth-links">
    <p>Already have an account? <a href="/login">Sign In</a></p>
</div>

<?php unset($_SESSION['flash']['old']); ?>
