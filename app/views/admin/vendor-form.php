<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-<?= $isEdit ? 'edit' : 'plus' ?>"></i> 
            <?= $isEdit ? 'Edit Vendor' : 'Add Vendor' ?>
        </h3>
        <a href="/admin/vendors" class="btn btn-sm" style="background: #E5E7EB;">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>
    
    <form method="POST" action="<?= $isEdit ? '/admin/vendors/' . $vendor['id'] : '/admin/vendors' ?>">
        <?= \App\Core\Auth::csrfField() ?>
        
        <!-- Basic Information -->
        <h4 style="margin-bottom: 20px; color: var(--primary);"><i class="fas fa-info-circle"></i> Basic Information</h4>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px;">
            <div class="form-group">
                <label for="name">Vendor/Business Name *</label>
                <input type="text" id="name" name="name" class="form-control" required
                       value="<?= htmlspecialchars($vendor['name'] ?? $_SESSION['_flash']['old']['name'] ?? '') ?>">
            </div>
            
            <div class="form-group">
                <label for="category">Category *</label>
                <select id="category" name="category" class="form-control" required>
                    <option value="">Select Category</option>
                    <?php foreach ($categories as $key => $name): ?>
                    <option value="<?= $key ?>" <?= ($vendor['category'] ?? $_SESSION['_flash']['old']['category'] ?? '') === $key ? 'selected' : '' ?>>
                        <?= htmlspecialchars($name) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="contact_person">Contact Person</label>
                <input type="text" id="contact_person" name="contact_person" class="form-control"
                       value="<?= htmlspecialchars($vendor['contact_person'] ?? $_SESSION['_flash']['old']['contact_person'] ?? '') ?>">
            </div>
            
            <div class="form-group" style="grid-column: 1 / -1;">
                <label for="description">Description</label>
                <textarea id="description" name="description" class="form-control" rows="3"><?= htmlspecialchars($vendor['description'] ?? $_SESSION['_flash']['old']['description'] ?? '') ?></textarea>
            </div>
        </div>
        
        <!-- Contact Information -->
        <h4 style="margin-bottom: 20px; color: var(--primary);"><i class="fas fa-phone-alt"></i> Contact Information</h4>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px;">
            <div class="form-group">
                <label for="phone">Phone Number *</label>
                <input type="tel" id="phone" name="phone" class="form-control" required
                       value="<?= htmlspecialchars($vendor['phone'] ?? $_SESSION['_flash']['old']['phone'] ?? '') ?>">
            </div>
            
            <div class="form-group">
                <label for="alternate_phone">Alternate Phone</label>
                <input type="tel" id="alternate_phone" name="alternate_phone" class="form-control"
                       value="<?= htmlspecialchars($vendor['alternate_phone'] ?? $_SESSION['_flash']['old']['alternate_phone'] ?? '') ?>">
            </div>
            
            <div class="form-group">
                <label for="whatsapp">WhatsApp</label>
                <input type="tel" id="whatsapp" name="whatsapp" class="form-control"
                       value="<?= htmlspecialchars($vendor['whatsapp'] ?? $_SESSION['_flash']['old']['whatsapp'] ?? '') ?>">
            </div>
            
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" class="form-control"
                       value="<?= htmlspecialchars($vendor['email'] ?? $_SESSION['_flash']['old']['email'] ?? '') ?>">
            </div>
            
            <div class="form-group">
                <label for="website">Website</label>
                <input type="url" id="website" name="website" class="form-control" placeholder="https://"
                       value="<?= htmlspecialchars($vendor['website'] ?? $_SESSION['_flash']['old']['website'] ?? '') ?>">
            </div>
        </div>
        
        <!-- Address Information -->
        <h4 style="margin-bottom: 20px; color: var(--primary);"><i class="fas fa-map-marker-alt"></i> Address & Location</h4>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px;">
            <div class="form-group" style="grid-column: 1 / -1;">
                <label for="address_line1">Address Line 1 *</label>
                <input type="text" id="address_line1" name="address_line1" class="form-control" required
                       value="<?= htmlspecialchars($vendor['address_line1'] ?? $_SESSION['_flash']['old']['address_line1'] ?? '') ?>">
            </div>
            
            <div class="form-group" style="grid-column: 1 / -1;">
                <label for="address_line2">Address Line 2</label>
                <input type="text" id="address_line2" name="address_line2" class="form-control"
                       value="<?= htmlspecialchars($vendor['address_line2'] ?? $_SESSION['_flash']['old']['address_line2'] ?? '') ?>">
            </div>
            
            <div class="form-group">
                <label for="city">City *</label>
                <input type="text" id="city" name="city" class="form-control" required
                       value="<?= htmlspecialchars($vendor['city'] ?? $_SESSION['_flash']['old']['city'] ?? '') ?>">
            </div>
            
            <div class="form-group">
                <label for="state">State *</label>
                <input type="text" id="state" name="state" class="form-control" required
                       value="<?= htmlspecialchars($vendor['state'] ?? $_SESSION['_flash']['old']['state'] ?? '') ?>">
            </div>
            
            <div class="form-group">
                <label for="pincode">Pincode *</label>
                <input type="text" id="pincode" name="pincode" class="form-control" required pattern="[0-9]{6}"
                       value="<?= htmlspecialchars($vendor['pincode'] ?? $_SESSION['_flash']['old']['pincode'] ?? '') ?>">
            </div>
            
            <div class="form-group">
                <label for="country">Country</label>
                <input type="text" id="country" name="country" class="form-control"
                       value="<?= htmlspecialchars($vendor['country'] ?? $_SESSION['_flash']['old']['country'] ?? 'India') ?>">
            </div>
        </div>
        
        <!-- GPS Coordinates -->
        <div style="background: #F9FAFB; border-radius: 12px; padding: 20px; margin-bottom: 30px;">
            <h5 style="margin-bottom: 15px; color: var(--dark);"><i class="fas fa-crosshairs"></i> GPS Coordinates (for location-based search)</h5>
            <p style="color: #6B7280; margin-bottom: 15px; font-size: 0.9rem;">
                Enter the latitude and longitude for accurate vendor location. 
                <a href="https://www.latlong.net/" target="_blank" style="color: var(--primary);">Find coordinates here</a>
            </p>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
                <div class="form-group">
                    <label for="latitude">Latitude *</label>
                    <input type="number" id="latitude" name="latitude" class="form-control" required step="any"
                           placeholder="e.g., 28.613939"
                           value="<?= htmlspecialchars($vendor['latitude'] ?? $_SESSION['_flash']['old']['latitude'] ?? '') ?>">
                </div>
                
                <div class="form-group">
                    <label for="longitude">Longitude *</label>
                    <input type="number" id="longitude" name="longitude" class="form-control" required step="any"
                           placeholder="e.g., 77.209021"
                           value="<?= htmlspecialchars($vendor['longitude'] ?? $_SESSION['_flash']['old']['longitude'] ?? '') ?>">
                </div>
                
                <div class="form-group">
                    <label for="service_area_km">Service Area (km)</label>
                    <input type="number" id="service_area_km" name="service_area_km" class="form-control" min="1" max="500"
                           placeholder="50"
                           value="<?= htmlspecialchars($vendor['service_area_km'] ?? $_SESSION['_flash']['old']['service_area_km'] ?? '50') ?>">
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
                           value="<?= htmlspecialchars($vendor['map_url'] ?? $_SESSION['_flash']['old']['map_url'] ?? '') ?>">
                    <small style="color: #6B7280; margin-top: 5px; display: block;">
                        <i class="fas fa-info-circle"></i> Paste the Google Maps share link for accurate directions. 
                        <a href="https://www.google.com/maps" target="_blank" style="color: var(--primary);">Open Google Maps</a> → Search location → Click Share → Copy link
                    </small>
                </div>
            </div>
        </div>
        
        <!-- Pricing Information -->
        <h4 style="margin-bottom: 20px; color: var(--primary);"><i class="fas fa-rupee-sign"></i> Pricing Information</h4>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px;">
            <div class="form-group">
                <label for="min_price">Minimum Price (₹)</label>
                <input type="number" id="min_price" name="min_price" class="form-control" min="0" step="100"
                       placeholder="e.g., 5000"
                       value="<?= htmlspecialchars($vendor['min_price'] ?? $_SESSION['_flash']['old']['min_price'] ?? '') ?>">
            </div>
            
            <div class="form-group">
                <label for="max_price">Maximum Price (₹)</label>
                <input type="number" id="max_price" name="max_price" class="form-control" min="0" step="100"
                       placeholder="e.g., 50000"
                       value="<?= htmlspecialchars($vendor['max_price'] ?? $_SESSION['_flash']['old']['max_price'] ?? '') ?>">
            </div>
            
            <div class="form-group" style="grid-column: 1 / -1;">
                <label for="services_offered">Services Offered</label>
                <textarea id="services_offered" name="services_offered" class="form-control" rows="2"
                          placeholder="e.g., Wedding Photography, Pre-Wedding Shoots, Birthday Events"><?= htmlspecialchars($vendor['services_offered'] ?? $_SESSION['_flash']['old']['services_offered'] ?? '') ?></textarea>
            </div>
        </div>
        
        <!-- Status Options -->
        <h4 style="margin-bottom: 20px; color: var(--primary);"><i class="fas fa-cog"></i> Status Options</h4>
        <div style="display: flex; gap: 30px; margin-bottom: 30px; flex-wrap: wrap;">
            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                <input type="checkbox" name="is_active" <?= ($vendor['is_active'] ?? 1) ? 'checked' : '' ?>>
                <span><i class="fas fa-check-circle" style="color: var(--success);"></i> Active</span>
            </label>
            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                <input type="checkbox" name="is_featured" <?= ($vendor['is_featured'] ?? 0) ? 'checked' : '' ?>>
                <span><i class="fas fa-star" style="color: var(--warning);"></i> Featured</span>
            </label>
            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                <input type="checkbox" name="is_verified" <?= ($vendor['is_verified'] ?? 0) ? 'checked' : '' ?>>
                <span><i class="fas fa-badge-check" style="color: var(--primary);"></i> Verified</span>
            </label>
        </div>
        
        <!-- Submit Buttons -->
        <div style="display: flex; gap: 15px; align-items: center;">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> <?= $isEdit ? 'Update Vendor' : 'Add Vendor' ?>
            </button>
            
            <?php if ($isEdit): ?>
            <button type="button" class="btn btn-danger" onclick="deleteVendor(<?= $vendor['id'] ?>)">
                <i class="fas fa-trash"></i> Delete Vendor
            </button>
            <?php endif; ?>
            
            <a href="/admin/vendors" class="btn" style="background: #E5E7EB;">Cancel</a>
        </div>
    </form>
</div>

<?php if ($isEdit): ?>
<!-- Hidden Delete Form -->
<form id="deleteVendorForm" method="POST" action="/admin/vendors/<?= $vendor['id'] ?>/delete" style="display: none;">
    <?= \App\Core\Auth::csrfField() ?>
</form>
<?php endif; ?>

<script>
function deleteVendor(id) {
    if (confirm('Are you sure you want to DELETE this vendor? This action cannot be undone!')) {
        document.getElementById('deleteVendorForm').submit();
    }
}

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

<style>
.form-group {
    margin-bottom: 0;
}
.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 500;
    color: #374151;
}
.form-control {
    width: 100%;
    padding: 10px 14px;
    border: 2px solid #E5E7EB;
    border-radius: 8px;
    font-size: 1rem;
    transition: border-color 0.3s;
}
.form-control:focus {
    border-color: var(--primary);
    outline: none;
}
textarea.form-control {
    resize: vertical;
}
@media (max-width: 768px) {
    h4 {
        font-size: 1.1rem;
    }
}
</style>
