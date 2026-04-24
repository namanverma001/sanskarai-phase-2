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

        <h4 style="margin-top: 30px; margin-bottom: 20px; color: var(--primary);"><i class="fas fa-map-marker-alt"></i> Location Information</h4>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label>City</label>
                <input type="text" name="city" class="form-control" value="<?= htmlspecialchars($profile['city'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Pincode</label>
                <input type="text" name="pincode" class="form-control" value="<?= htmlspecialchars($profile['pincode'] ?? '') ?>">
            </div>
        </div>

        <div style="background: #F9FAFB; border-radius: 12px; padding: 20px; margin-top: 20px;">
            <h5 style="margin-bottom: 15px; color: var(--dark);"><i class="fas fa-crosshairs"></i> GPS Coordinates (for location-based search)</h5>
            <p style="color: #6B7280; margin-bottom: 15px; font-size: 0.9rem;">
                Enter your exact latitude and longitude so users can find you. 
            </p>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
                <div class="form-group">
                    <label>Latitude</label>
                    <input type="number" id="latitude" name="latitude" class="form-control" step="any"
                           value="<?= htmlspecialchars($profile['latitude'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>Longitude</label>
                    <input type="number" id="longitude" name="longitude" class="form-control" step="any"
                           value="<?= htmlspecialchars($profile['longitude'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>Service Area (km)</label>
                    <input type="number" name="service_area_km" class="form-control" min="1" max="500"
                           value="<?= htmlspecialchars($profile['service_area_km'] ?? '50') ?>">
                </div>
                <div class="form-group">
                    <label>&nbsp;</label>
                    <button type="button" class="btn btn-info" onclick="getCurrentLocation()" style="width: 100%;">
                        <i class="fas fa-location-arrow"></i> Get Current Location
                    </button>
                </div>
            </div>
            
            <!-- Map URL for Directions -->
            <div style="margin-top: 20px;">
                <div class="form-group">
                    <label for="map_url"><i class="fas fa-directions" style="color: var(--primary);"></i> Google Maps URL (for Directions)</label>
                    <input type="url" id="map_url" name="map_url" class="form-control"
                           placeholder="https://maps.google.com/... or https://goo.gl/maps/..."
                           value="<?= htmlspecialchars($profile['map_url'] ?? '') ?>">
                    <small style="color: #6B7280; margin-top: 5px; display: block;">
                        <i class="fas fa-info-circle"></i> Paste the Google Maps share link for accurate directions. 
                        <a href="https://www.google.com/maps" target="_blank" style="color: var(--primary);">Open Google Maps</a> → Search location → Click Share → Copy link
                    </small>
                </div>
            </div>
        </div>

        <script>
        function getCurrentLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        document.getElementById('latitude').value = position.coords.latitude.toFixed(8);
                        document.getElementById('longitude').value = position.coords.longitude.toFixed(8);
                    },
                    function(error) {
                        alert('Error getting location: ' + error.message);
                    }
                );
            } else {
                alert('Geolocation is not supported by this browser.');
            }
        }
        </script>
        
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
