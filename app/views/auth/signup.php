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
        <label for="community_name"><i class="fas fa-users"></i> Community Name</label>
        <input type="text" id="community_name" name="community_name" class="form-control" 
               placeholder="e.g., Brahmin, Maratha, Agarwal, Jat"
               value="<?= htmlspecialchars($_SESSION['flash']['old']['community_name'] ?? '') ?>">
        <small style="color: #9CA3AF; font-size: 0.78rem;">Helps personalize rituals for your community</small>
    </div>
    
    
    <div class="form-group">
        <label for="password"><i class="fas fa-lock"></i> Password</label>
        <div class="password-input-wrapper">
            <input type="password" id="password" name="password" class="form-control" 
                   placeholder="Create a strong password" required minlength="8">
            <button type="button" class="password-toggle" onclick="togglePassword('password', this)" aria-label="Show password">
                <i class="fas fa-eye"></i>
            </button>
        </div>
        <button type="button" class="generate-password-btn" onclick="generateStrongPassword()">
            <i class="fas fa-magic"></i> Auto-generate strong password
        </button>
        <div class="password-criteria" id="passwordCriteria">
            <div class="criteria-item" id="critLen"><i class="fas fa-circle"></i> At least 8 characters</div>
            <div class="criteria-item" id="critUpper"><i class="fas fa-circle"></i> One uppercase letter (A-Z)</div>
            <div class="criteria-item" id="critLower"><i class="fas fa-circle"></i> One lowercase letter (a-z)</div>
            <div class="criteria-item" id="critNum"><i class="fas fa-circle"></i> One number (0-9)</div>
            <div class="criteria-item" id="critSpecial"><i class="fas fa-circle"></i> One special character (!@#$...)</div>
        </div>
    </div>
    
    <div class="form-group">
        <label for="password_confirmation"><i class="fas fa-lock"></i> Confirm Password</label>
        <div class="password-input-wrapper">
            <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" 
                   placeholder="Confirm your password" required>
            <button type="button" class="password-toggle" onclick="togglePassword('password_confirmation', this)" aria-label="Show password">
                <i class="fas fa-eye"></i>
            </button>
        </div>
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
.generate-password-btn {
    background: none;
    border: 1px dashed #FF6B35;
    color: #FF6B35;
    padding: 6px 14px;
    border-radius: 8px;
    cursor: pointer;
    font-size: 0.8rem;
    margin-top: 8px;
    font-family: inherit;
    transition: all 0.2s;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}
.generate-password-btn:hover {
    background: rgba(255, 107, 53, 0.1);
}
.password-criteria {
    margin-top: 10px;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 5px 15px;
}
.criteria-item {
    font-size: 0.78rem;
    color: #9CA3AF;
    display: flex;
    align-items: center;
    gap: 6px;
    transition: color 0.2s;
}
.criteria-item i {
    font-size: 0.45rem;
}
.criteria-item.met {
    color: #10B981;
}
.criteria-item.met i::before {
    content: "\f058";
    font-size: 0.8rem;
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

function generateStrongPassword() {
    const upper = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
    const lower = 'abcdefghjkmnpqrstuvwxyz';
    const nums = '23456789';
    const special = '!@#$&*_-';
    
    let password = '';
    password += upper[Math.floor(Math.random() * upper.length)];
    password += lower[Math.floor(Math.random() * lower.length)];
    password += nums[Math.floor(Math.random() * nums.length)];
    password += special[Math.floor(Math.random() * special.length)];
    
    const all = upper + lower + nums + special;
    for (let i = 0; i < 8; i++) {
        password += all[Math.floor(Math.random() * all.length)];
    }
    
    // Shuffle
    password = password.split('').sort(() => Math.random() - 0.5).join('');
    
    const pwField = document.getElementById('password');
    const confirmField = document.getElementById('password_confirmation');
    pwField.value = password;
    confirmField.value = password;
    
    // Show the generated password
    pwField.type = 'text';
    confirmField.type = 'text';
    document.querySelectorAll('.password-toggle i').forEach(i => i.className = 'fas fa-eye-slash');
    
    // Update criteria
    checkPasswordStrength(password);
}

function checkPasswordStrength(val) {
    const checks = {
        critLen: val.length >= 8,
        critUpper: /[A-Z]/.test(val),
        critLower: /[a-z]/.test(val),
        critNum: /[0-9]/.test(val),
        critSpecial: /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(val)
    };
    for (const [id, passed] of Object.entries(checks)) {
        const el = document.getElementById(id);
        if (el) el.classList.toggle('met', passed);
    }
}

document.getElementById('password').addEventListener('input', function() {
    checkPasswordStrength(this.value);
});
</script>
