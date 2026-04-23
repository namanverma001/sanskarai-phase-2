<div class="card">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
        <h3 class="card-title"><i class="fas fa-pray"></i> Find a Pandit</h3>
        
        <?php if (!empty($searchRadius)): ?>
        <div style="background: rgba(16, 185, 129, 0.1); color: #10B981; padding: 8px 15px; border-radius: 20px; font-size: 0.9rem; font-weight: 500;">
            <i class="fas fa-location-arrow"></i> Showing results within <?= $searchRadius ?> km
        </div>
        <?php endif; ?>
    </div>
    
    <div style="padding: 20px; background: #F9FAFB; border-bottom: 1px solid #E5E7EB; text-align: center;">
        <p style="color: #6B7280; margin-bottom: 15px;">Allow location access to find pandits near you (within 50km).</p>
        <form id="filterForm" method="GET" action="/user/select-pandit">
            <input type="hidden" name="lat" id="lat" value="<?= htmlspecialchars($lat ?? '') ?>">
            <input type="hidden" name="lng" id="lng" value="<?= htmlspecialchars($lng ?? '') ?>">
            
            <button type="button" id="findNearMeBtn" class="btn btn-info btn-lg" style="color: white; padding: 12px 24px; font-size: 1.1rem; border-radius: 8px;">
                <i class="fas fa-location-arrow"></i> Find Pandits Near Me
            </button>
            <?php if (!empty($lat)): ?>
            <div style="margin-top: 15px;">
                <a href="/user/select-pandit?clear=1" class="text-danger" style="font-size: 0.9rem; text-decoration: underline;">
                    <i class="fas fa-times"></i> Clear Location
                </a>
            </div>
            <?php endif; ?>
        </form>
    </div>
    
    <div style="padding: 20px;">
        <?php if (empty($pandits)): ?>
            <div style="text-align: center; padding: 40px 20px;">
                <div style="font-size: 3rem; color: #D1D5DB; margin-bottom: 15px;">
                    <i class="fas fa-search-minus"></i>
                </div>
                <h4 style="color: #4B5563; margin-bottom: 10px;">No pandits found</h4>
                <p style="color: #6B7280;">Try adjusting your filters or expanding your search location.</p>
            </div>
        <?php else: ?>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">
                <?php foreach ($pandits as $pandit): ?>
                <div style="border: 1px solid #E5E7EB; border-radius: 12px; padding: 20px; transition: all 0.3s; position: relative;">
                    <?php if (isset($pandit['distance'])): ?>
                    <div style="position: absolute; top: 15px; right: 15px; background: #EEF2FF; color: var(--primary); padding: 4px 10px; border-radius: 20px; font-size: 0.8rem; font-weight: 600;">
                        <i class="fas fa-map-marker-alt"></i> <?= number_format($pandit['distance'], 1) ?> km away
                    </div>
                    <?php endif; ?>
                    
                    <div style="display: flex; gap: 15px; align-items: start;">
                        <div style="width: 60px; height: 60px; background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.5rem; flex-shrink: 0;">
                            <i class="fas fa-user"></i>
                        </div>
                        <div style="flex: 1; padding-right: <?= isset($pandit['distance']) ? '80px' : '0' ?>;">
                            <h4 style="margin-bottom: 5px; font-size: 1.1rem;"><?= htmlspecialchars($pandit['name']) ?></h4>
                            <p style="color: #6B7280; font-size: 0.85rem; margin-bottom: 5px;">
                                <?= htmlspecialchars($pandit['specialization'] ?? 'General Puja') ?>
                            </p>
                        </div>
                    </div>
                    
                    <div style="margin-top: 15px; display: flex; flex-wrap: wrap; gap: 10px; font-size: 0.85rem;">
                        <span style="background: #FFFBEB; color: #B45309; padding: 4px 8px; border-radius: 6px;">
                            <i class="fas fa-star" style="color: #F59E0B;"></i> <?= number_format($pandit['average_rating'] ?? 0, 1) ?>
                        </span>
                        <span style="background: #F3F4F6; color: #4B5563; padding: 4px 8px; border-radius: 6px;">
                            <i class="fas fa-briefcase"></i> <?= $pandit['experience_years'] ?? 0 ?> yrs exp
                        </span>
                        <span style="background: #ECFDF5; color: #065F46; padding: 4px 8px; border-radius: 6px;">
                            <i class="fas fa-check-circle" style="color: #10B981;"></i> <?= $pandit['total_rituals_performed'] ?? 0 ?> rituals
                        </span>
                        <?php if (!empty($pandit['hourly_rate'])): ?>
                        <span style="background: #F0F9FF; color: #0369A1; padding: 4px 8px; border-radius: 6px;">
                            <i class="fas fa-rupee-sign"></i> <?= number_format($pandit['hourly_rate']) ?>/hr
                        </span>
                        <?php endif; ?>
                    </div>
                    
                    <?php if (!empty($pandit['bio'])): ?>
                    <p style="margin-top: 15px; color: #6B7280; font-size: 0.9rem; line-height: 1.4;">
                        <?= htmlspecialchars(mb_substr($pandit['bio'], 0, 100)) ?><?= mb_strlen($pandit['bio']) > 100 ? '...' : '' ?>
                    </p>
                    <?php endif; ?>
                    
                    <div style="margin-top: 15px; display: flex; gap: 10px; border-top: 1px solid #F3F4F6; padding-top: 15px;">
                        <a href="/user/book-pandit/<?= $pandit['user_id'] ?>" class="btn btn-primary" style="flex: 1; justify-content: center; padding: 8px;">
                            <i class="fas fa-calendar-plus"></i> Book Now
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const latInput = document.getElementById('lat');
    const lngInput = document.getElementById('lng');
    const form = document.getElementById('filterForm');
    const findNearMeBtn = document.getElementById('findNearMeBtn');
    
    function setLocationCookie(lat, lng) {
        document.cookie = `user_lat=${lat}; path=/; max-age=3600`;
        document.cookie = `user_lng=${lng}; path=/; max-age=3600`;
    }
    
    function requestLocation(autoSubmit = false) {
        if (navigator.geolocation) {
            findNearMeBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Locating...';
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    latInput.value = lat;
                    lngInput.value = lng;
                    setLocationCookie(lat, lng);
                    if (autoSubmit) {
                        form.submit();
                    } else {
                        findNearMeBtn.innerHTML = '<i class="fas fa-check"></i> Found';
                        setTimeout(() => form.submit(), 500);
                    }
                },
                function(error) {
                    findNearMeBtn.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Failed';
                    alert('Could not get your location. Please check your browser permissions.');
                },
                { timeout: 10000, maximumAge: 60000 }
            );
        } else {
            alert('Geolocation is not supported by your browser.');
        }
    }

    if (findNearMeBtn) {
        findNearMeBtn.addEventListener('click', function() {
            requestLocation(true);
        });
    }
    
    // Only ask for location if we haven't already captured it
    const hasLocation = latInput.value && lngInput.value;
    const prompted = sessionStorage.getItem('locationPrompted');
    
    if (!hasLocation && !prompted) {
        sessionStorage.setItem('locationPrompted', 'true');
        requestLocation(true);
    }
});
</script>
