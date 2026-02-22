<style>
    .vendors-header {
        background: linear-gradient(135deg, #FF6B35 0%, #FF8C42 100%);
        border-radius: 20px;
        padding: 40px;
        color: white;
        margin-bottom: 30px;
        position: relative;
        overflow: hidden;
    }

    .vendors-header::before {
        content: '🏪';
        position: absolute;
        right: 30px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 6rem;
        opacity: 0.2;
    }

    .vendors-header h1 {
        font-size: 2rem;
        margin-bottom: 10px;
    }

    .vendors-header p {
        opacity: 0.9;
        font-size: 1rem;
    }

    .search-filters-card {
        background: white;
        border-radius: 20px;
        padding: 25px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
        margin-bottom: 30px;
    }

    .search-filters-card h3 {
        margin-bottom: 20px;
        color: var(--dark);
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .search-filters-card h3 i {
        color: var(--primary);
    }

    .location-section {
        background: linear-gradient(135deg, #FFF7ED 0%, #FFEDD5 100%);
        border-radius: 15px;
        padding: 20px;
        margin-bottom: 20px;
        border: 2px dashed var(--primary);
    }

    .location-section h4 {
        margin-bottom: 15px;
        color: var(--primary);
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .location-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 15px;
        align-items: end;
    }

    .filter-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
    }

    .form-field {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .form-field label {
        font-weight: 500;
        color: #374151;
        font-size: 0.9rem;
    }

    .form-field input,
    .form-field select {
        padding: 12px 16px;
        border: 2px solid #E5E7EB;
        border-radius: 10px;
        font-size: 1rem;
        transition: all 0.3s;
    }

    .form-field input:focus,
    .form-field select:focus {
        border-color: var(--primary);
        outline: none;
        box-shadow: 0 0 0 3px rgba(255, 107, 53, 0.1);
    }

    .btn-location {
        background: linear-gradient(135deg, var(--primary) 0%, #FF8C42 100%);
        color: white;
        border: none;
        padding: 12px 20px;
        border-radius: 10px;
        cursor: pointer;
        font-weight: 600;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        gap: 8px;
        justify-content: center;
    }

    .btn-location:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(255, 107, 53, 0.3);
    }

    .btn-search {
        background: var(--primary);
        color: white;
        border: none;
        padding: 12px 25px;
        border-radius: 10px;
        cursor: pointer;
        font-weight: 600;
        transition: all 0.3s;
    }

    .btn-search:hover {
        background: var(--primary-dark);
    }

    .location-status {
        margin-top: 10px;
        padding: 10px 15px;
        border-radius: 8px;
        font-size: 0.9rem;
        display: none;
    }

    .location-status.success {
        display: block;
        background: #D1FAE5;
        color: #065F46;
    }

    .location-status.error {
        display: block;
        background: #FEE2E2;
        color: #991B1B;
    }

    .categories-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
        gap: 15px;
        margin-bottom: 30px;
    }

    .category-card {
        background: white;
        border-radius: 15px;
        padding: 20px 15px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s;
        border: 2px solid transparent;
        text-decoration: none;
        color: var(--dark);
    }

    .category-card:hover,
    .category-card.active {
        border-color: var(--primary);
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(255, 107, 53, 0.15);
    }

    .category-card i {
        font-size: 2rem;
        color: var(--primary);
        margin-bottom: 10px;
        display: block;
    }

    .category-card h4 {
        font-size: 0.9rem;
        margin-bottom: 5px;
    }

    .category-card small {
        color: #6B7280;
        font-size: 0.8rem;
    }

    .featured-section {
        margin-bottom: 30px;
    }

    .section-title {
        font-size: 1.4rem;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .section-title i {
        color: var(--warning);
    }

    .featured-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 20px;
    }

    .vendor-card {
        background: white;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        transition: all 0.3s;
        position: relative;
    }

    .vendor-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
    }

    .vendor-card.featured {
        border: 2px solid var(--warning);
    }

    .vendor-card .badge-featured {
        position: absolute;
        top: 15px;
        right: 15px;
        background: linear-gradient(135deg, #F59E0B 0%, #D97706 100%);
        color: white;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .vendor-card .badge-verified {
        position: absolute;
        top: 15px;
        left: 15px;
        background: linear-gradient(135deg, #10B981 0%, #059669 100%);
        color: white;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .vendor-card-header {
        background: linear-gradient(135deg, #F3F4F6 0%, #E5E7EB 100%);
        padding: 25px;
        text-align: center;
    }

    .vendor-card-header i {
        font-size: 3rem;
        color: var(--primary);
    }

    .vendor-card-body {
        padding: 20px;
    }

    .vendor-card h3 {
        font-size: 1.2rem;
        margin-bottom: 8px;
        color: var(--dark);
    }

    .vendor-card .category-badge {
        display: inline-block;
        background: #EFF6FF;
        color: #1D4ED8;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        margin-bottom: 12px;
    }

    .vendor-card .info-row {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 8px;
        color: #6B7280;
        font-size: 0.9rem;
    }

    .vendor-card .info-row i {
        width: 20px;
        color: var(--primary);
    }

    .vendor-card .distance-badge {
        background: linear-gradient(135deg, #ECFDF5 0%, #D1FAE5 100%);
        color: #065F46;
        padding: 8px 15px;
        border-radius: 10px;
        font-size: 0.9rem;
        margin-top: 12px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .vendor-card .rating {
        display: flex;
        align-items: center;
        gap: 5px;
        margin-top: 10px;
    }

    .vendor-card .rating i {
        color: #F59E0B;
        font-size: 0.9rem;
    }

    .vendor-card .rating span {
        color: #6B7280;
        font-size: 0.85rem;
    }

    .vendor-card-actions {
        display: flex;
        gap: 10px;
        margin-top: 15px;
    }

    .vendor-card-actions a,
    .vendor-card-actions button {
        flex: 1;
        padding: 10px;
        border-radius: 10px;
        text-align: center;
        font-weight: 500;
        text-decoration: none;
        transition: all 0.3s;
        border: none;
        cursor: pointer;
        font-size: 0.9rem;
    }

    .btn-view {
        background: var(--primary);
        color: white;
    }

    .btn-view:hover {
        background: var(--primary-dark);
    }

    .btn-call {
        background: #10B981;
        color: white;
    }

    .btn-call:hover {
        background: #059669;
    }

    .btn-whatsapp {
        background: #25D366;
        color: white;
    }

    .btn-whatsapp:hover {
        background: #1DA851;
    }

    .no-vendors {
        text-align: center;
        padding: 60px 20px;
        color: #6B7280;
    }

    .no-vendors i {
        font-size: 4rem;
        color: #E5E7EB;
        margin-bottom: 20px;
    }

    .vendors-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 20px;
    }

    #nearbyVendorsSection {
        display: none;
    }

    .all-vendors-section {
        display: none;
    }

    .all-vendors-section.show {
        display: block;
    }

    .show-all-vendors-btn {
        text-align: center;
        margin: 30px 0;
    }

    .show-all-vendors-btn .btn {
        background: linear-gradient(135deg, var(--primary) 0%, #FF8C42 100%);
        color: white;
        border: none;
        padding: 15px 40px;
        border-radius: 12px;
        cursor: pointer;
        font-weight: 600;
        font-size: 1.1rem;
        transition: all 0.3s;
        display: inline-flex;
        align-items: center;
        gap: 10px;
    }

    .show-all-vendors-btn .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(255, 107, 53, 0.3);
    }

    .loading-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.9);
        z-index: 9999;
        justify-content: center;
        align-items: center;
        flex-direction: column;
    }

    .loading-overlay.show {
        display: flex;
    }

    .loading-spinner {
        width: 50px;
        height: 50px;
        border: 4px solid #E5E7EB;
        border-top-color: var(--primary);
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    @media (max-width: 768px) {
        .vendors-header {
            padding: 25px;
        }
        .vendors-header h1 {
            font-size: 1.5rem;
        }
        .vendors-header::before {
            display: none;
        }
        .categories-grid {
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
        }
        .category-card {
            padding: 15px 10px;
        }
        .category-card i {
            font-size: 1.5rem;
        }
        .category-card h4 {
            font-size: 0.8rem;
        }
    }
</style>

<!-- Loading Overlay -->
<div class="loading-overlay" id="loadingOverlay">
    <div class="loading-spinner"></div>
    <p style="margin-top: 15px; color: var(--dark);">Finding nearby vendors...</p>
</div>

<!-- Header -->
<div class="vendors-header">
    <h1><i class="fas fa-store"></i> Browse Vendors</h1>
    <p>Find photographers, caterers, decorators, and more for your ceremonies</p>
</div>

<!-- Location-Based Search -->
<div class="search-filters-card">
    <div class="location-section">
        <h4><i class="fas fa-map-marker-alt"></i> Find Vendors Near You</h4>
        <div class="location-grid">
            <div class="form-field">
                <label>Distance Radius</label>
                <select id="radiusSelect">
                    <option value="5">Within 5 km</option>
                    <option value="10">Within 10 km</option>
                    <option value="15" selected>Within 15 km</option>
                    <option value="25">Within 25 km</option>
                    <option value="50">Within 50 km</option>
                    <option value="100">Within 100 km</option>
                </select>
            </div>
            <div class="form-field">
                <label>Category (Optional)</label>
                <select id="categorySelect">
                    <option value="">All Categories</option>
                    <?php foreach ($categories as $key => $name): ?>
                    <option value="<?= $key ?>" <?= ($selectedCategory ?? '') === $key ? 'selected' : '' ?>><?= htmlspecialchars($name) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-field">
                <label>&nbsp;</label>
                <button class="btn-location" onclick="getLocationAndSearch()">
                    <i class="fas fa-crosshairs"></i> Use My Location
                </button>
            </div>
        </div>
        <div class="location-status" id="locationStatus"></div>
    </div>

    <h3><i class="fas fa-filter"></i> Or Browse by Category & Search</h3>
    <form method="GET" action="/user/vendors" class="filter-grid">
        <div class="form-field">
            <label>Search Vendor</label>
            <input type="text" name="search" placeholder="Search by name, city..." value="<?= htmlspecialchars($search ?? '') ?>">
        </div>
        <div class="form-field">
            <label>Category</label>
            <select name="category">
                <option value="">All Categories</option>
                <?php foreach ($categories as $key => $name): ?>
                <option value="<?= $key ?>" <?= ($selectedCategory ?? '') === $key ? 'selected' : '' ?>><?= htmlspecialchars($name) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-field">
            <label>&nbsp;</label>
            <button type="submit" class="btn-search"><i class="fas fa-search"></i> Search</button>
        </div>
    </form>
</div>

<!-- Categories Quick Access -->
<div class="categories-grid">
    <?php 
    $categoryIcons = [
        'photographer' => 'fa-camera',
        'catering' => 'fa-utensils',
        'decorator' => 'fa-paint-brush',
        'florist' => 'fa-leaf',
        'music' => 'fa-music',
        'lighting' => 'fa-lightbulb',
        'tent_house' => 'fa-campground',
        'makeup_artist' => 'fa-magic',
        'mehendi_artist' => 'fa-hand-sparkles',
        'videographer' => 'fa-video',
        'invitation_cards' => 'fa-envelope-open-text',
        'travel' => 'fa-car',
        'other' => 'fa-ellipsis-h',
    ];
    $categoryCountsMap = [];
    foreach ($categoryCounts as $cc) {
        $categoryCountsMap[$cc['category']] = $cc['count'];
    }
    ?>
    <?php foreach ($categories as $key => $name): ?>
    <a href="/user/vendors?category=<?= $key ?>" class="category-card <?= ($selectedCategory ?? '') === $key ? 'active' : '' ?>">
        <i class="fas <?= $categoryIcons[$key] ?? 'fa-store' ?>"></i>
        <h4><?= htmlspecialchars($name) ?></h4>
        <small><?= $categoryCountsMap[$key] ?? 0 ?> vendors</small>
    </a>
    <?php endforeach; ?>
</div>

<!-- Show All Vendors Button (Only shown when no filter is active) -->
<?php if (empty($selectedCategory) && empty($search)): ?>
<div class="show-all-vendors-btn">
    <button class="btn" onclick="showAllVendors()">
        <i class="fas fa-store"></i> Show All Vendors
    </button>
</div>
<?php endif; ?>

<!-- Nearby Vendors Section (Hidden by default, shown after location search) -->
<div id="nearbyVendorsSection">
    <h2 class="section-title"><i class="fas fa-map-marker-alt"></i> Vendors Near You</h2>
    <div class="vendors-grid" id="nearbyVendorsGrid">
        <!-- Populated via JavaScript -->
    </div>
</div>

<!-- Featured Vendors -->
<?php if (!empty($featuredVendors)): ?>
<div class="featured-section">
    <h2 class="section-title"><i class="fas fa-star"></i> Featured Vendors</h2>
    <div class="featured-grid">
        <?php foreach ($featuredVendors as $vendor): ?>
        <div class="vendor-card featured">
            <span class="badge-featured"><i class="fas fa-star"></i> Featured</span>
            <?php if ($vendor['is_verified']): ?>
            <span class="badge-verified"><i class="fas fa-check"></i> Verified</span>
            <?php endif; ?>
            <div class="vendor-card-header">
                <i class="fas <?= (!empty($vendor['category']) && isset($categoryIcons[$vendor['category']])) ? $categoryIcons[$vendor['category']] : 'fa-store' ?>"></i>
            </div>
            <div class="vendor-card-body">
                <h3><?= htmlspecialchars($vendor['name']) ?></h3>
                <span class="category-badge"><?= htmlspecialchars((!empty($vendor['category']) && isset($categories[$vendor['category']])) ? $categories[$vendor['category']] : ucfirst($vendor['category'] ?? 'Other')) ?></span>
                <div class="info-row">
                    <i class="fas fa-map-marker-alt"></i>
                    <span><?= htmlspecialchars($vendor['city']) ?>, <?= htmlspecialchars($vendor['state']) ?></span>
                </div>
                <div class="info-row">
                    <i class="fas fa-phone"></i>
                    <span><?= htmlspecialchars($vendor['phone']) ?></span>
                </div>
                <?php if ($vendor['min_price'] || $vendor['max_price']): ?>
                <div class="info-row">
                    <i class="fas fa-rupee-sign"></i>
                    <span>
                        <?php if ($vendor['min_price'] && $vendor['max_price']): ?>
                            ₹<?= number_format($vendor['min_price']) ?> - ₹<?= number_format($vendor['max_price']) ?>
                        <?php elseif ($vendor['min_price']): ?>
                            Starting from ₹<?= number_format($vendor['min_price']) ?>
                        <?php endif; ?>
                    </span>
                </div>
                <?php endif; ?>
                <?php if ($vendor['average_rating'] > 0): ?>
                <div class="rating">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                    <i class="fas fa-star<?= $i <= round($vendor['average_rating']) ? '' : '-half-alt' ?>"></i>
                    <?php endfor; ?>
                    <span><?= number_format($vendor['average_rating'], 1) ?> (<?= $vendor['total_reviews'] ?> reviews)</span>
                </div>
                <?php endif; ?>
                <div class="vendor-card-actions">
                    <a href="/user/vendors/<?= $vendor['id'] ?>" class="btn-view"><i class="fas fa-eye"></i> View</a>
                    <a href="tel:<?= $vendor['phone'] ?>" class="btn-call"><i class="fas fa-phone"></i></a>
                    <?php if (!empty($vendor['whatsapp'])): ?>
                    <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $vendor['whatsapp']) ?>" target="_blank" class="btn-whatsapp"><i class="fab fa-whatsapp"></i></a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<!-- All Vendors -->
<?php 
$sectionTitle = 'All Vendors';
if (!empty($selectedCategory) && is_string($selectedCategory) && isset($categories[$selectedCategory])) {
    $sectionTitle = htmlspecialchars($categories[$selectedCategory]);
}
?>
<div class="all-vendors-section <?= (!empty($selectedCategory) || !empty($search)) ? 'show' : '' ?>">
    <h2 class="section-title"><i class="fas fa-store"></i> <?= $sectionTitle ?></h2>
    
    <?php if (empty($vendors)): ?>
    <div class="no-vendors">
        <i class="fas fa-store-slash"></i>
        <h3>No vendors found</h3>
        <p>Try adjusting your search filters or check back later.</p>
    </div>
    <?php else: ?>
    <div class="vendors-grid">
        <?php foreach ($vendors as $vendor): ?>
        <div class="vendor-card">
            <?php if ($vendor['is_featured']): ?>
            <span class="badge-featured"><i class="fas fa-star"></i> Featured</span>
            <?php endif; ?>
            <?php if ($vendor['is_verified']): ?>
            <span class="badge-verified"><i class="fas fa-check"></i> Verified</span>
            <?php endif; ?>
            <div class="vendor-card-header">
                <i class="fas <?= (!empty($vendor['category']) && isset($categoryIcons[$vendor['category']])) ? $categoryIcons[$vendor['category']] : 'fa-store' ?>"></i>
            </div>
            <div class="vendor-card-body">
                <h3><?= htmlspecialchars($vendor['name']) ?></h3>
                <span class="category-badge"><?= htmlspecialchars((!empty($vendor['category']) && isset($categories[$vendor['category']])) ? $categories[$vendor['category']] : ucfirst($vendor['category'] ?? 'Other')) ?></span>
                <div class="info-row">
                    <i class="fas fa-map-marker-alt"></i>
                    <span><?= htmlspecialchars($vendor['city']) ?>, <?= htmlspecialchars($vendor['state']) ?></span>
                </div>
                <div class="info-row">
                    <i class="fas fa-phone"></i>
                    <span><?= htmlspecialchars($vendor['phone']) ?></span>
                </div>
                <?php if ($vendor['min_price'] || $vendor['max_price']): ?>
                <div class="info-row">
                    <i class="fas fa-rupee-sign"></i>
                    <span>
                        <?php if ($vendor['min_price'] && $vendor['max_price']): ?>
                            ₹<?= number_format($vendor['min_price']) ?> - ₹<?= number_format($vendor['max_price']) ?>
                        <?php elseif ($vendor['min_price']): ?>
                            Starting from ₹<?= number_format($vendor['min_price']) ?>
                        <?php endif; ?>
                    </span>
                </div>
                <?php endif; ?>
                <?php if ($vendor['average_rating'] > 0): ?>
                <div class="rating">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                    <i class="fas fa-star<?= $i <= round($vendor['average_rating']) ? '' : '-half-alt' ?>"></i>
                    <?php endfor; ?>
                    <span><?= number_format($vendor['average_rating'], 1) ?></span>
                </div>
                <?php endif; ?>
                <div class="vendor-card-actions">
                    <a href="/user/vendors/<?= $vendor['id'] ?>" class="btn-view"><i class="fas fa-eye"></i> View Details</a>
                    <a href="tel:<?= $vendor['phone'] ?>" class="btn-call"><i class="fas fa-phone"></i></a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<script>
const categoryIcons = <?= json_encode($categoryIcons ?? []) ?>;
const categories = <?= json_encode($categories) ?>;

function getLocationAndSearch() {
    const statusEl = document.getElementById('locationStatus');
    const loadingOverlay = document.getElementById('loadingOverlay');
    
    if (!navigator.geolocation) {
        statusEl.className = 'location-status error';
        statusEl.textContent = 'Geolocation is not supported by your browser.';
        return;
    }
    
    statusEl.className = 'location-status';
    statusEl.style.display = 'none';
    loadingOverlay.classList.add('show');
    
    navigator.geolocation.getCurrentPosition(
        function(position) {
            const latitude = position.coords.latitude;
            const longitude = position.coords.longitude;
            const radius = document.getElementById('radiusSelect').value;
            const category = document.getElementById('categorySelect').value;
            
            // Make AJAX request to find nearby vendors
            fetch('/user/vendors/nearby', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `latitude=${latitude}&longitude=${longitude}&radius=${radius}&category=${category}`
            })
            .then(response => response.json())
            .then(data => {
                loadingOverlay.classList.remove('show');
                
                if (data.success) {
                    statusEl.className = 'location-status success';
                    statusEl.textContent = `Found ${data.count} vendor(s) within ${data.radius} km of your location.`;
                    displayNearbyVendors(data.vendors);
                } else {
                    statusEl.className = 'location-status error';
                    statusEl.textContent = data.message || 'Failed to find vendors.';
                }
            })
            .catch(error => {
                loadingOverlay.classList.remove('show');
                statusEl.className = 'location-status error';
                statusEl.textContent = 'Error connecting to server.';
                console.error('Error:', error);
            });
        },
        function(error) {
            loadingOverlay.classList.remove('show');
            statusEl.className = 'location-status error';
            switch(error.code) {
                case error.PERMISSION_DENIED:
                    statusEl.textContent = 'Location access denied. Please enable location permissions.';
                    break;
                case error.POSITION_UNAVAILABLE:
                    statusEl.textContent = 'Location information is unavailable.';
                    break;
                case error.TIMEOUT:
                    statusEl.textContent = 'Location request timed out.';
                    break;
                default:
                    statusEl.textContent = 'An unknown error occurred.';
            }
        },
        {
            enableHighAccuracy: true,
            timeout: 10000,
            maximumAge: 0
        }
    );
}

function displayNearbyVendors(vendors) {
    const section = document.getElementById('nearbyVendorsSection');
    const grid = document.getElementById('nearbyVendorsGrid');
    
    if (vendors.length === 0) {
        section.style.display = 'block';
        grid.innerHTML = `
            <div class="no-vendors" style="grid-column: 1 / -1;">
                <i class="fas fa-map-marker-alt"></i>
                <h3>No vendors found nearby</h3>
                <p>Try increasing the search radius or select a different category.</p>
            </div>
        `;
        return;
    }
    
    let html = '';
    vendors.forEach(vendor => {
        const icon = categoryIcons[vendor.category] || 'fa-store';
        const categoryName = categories[vendor.category] || vendor.category;
        const distance = parseFloat(vendor.distance_km).toFixed(1);
        
        html += `
            <div class="vendor-card">
                ${vendor.is_featured ? '<span class="badge-featured"><i class="fas fa-star"></i> Featured</span>' : ''}
                ${vendor.is_verified ? '<span class="badge-verified"><i class="fas fa-check"></i> Verified</span>' : ''}
                <div class="vendor-card-header">
                    <i class="fas ${icon}"></i>
                </div>
                <div class="vendor-card-body">
                    <h3>${escapeHtml(vendor.name)}</h3>
                    <span class="category-badge">${escapeHtml(categoryName)}</span>
                    <div class="distance-badge">
                        <i class="fas fa-route"></i> ${distance} km away
                    </div>
                    <div class="info-row">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>${escapeHtml(vendor.city)}, ${escapeHtml(vendor.state)}</span>
                    </div>
                    <div class="info-row">
                        <i class="fas fa-phone"></i>
                        <span>${escapeHtml(vendor.phone)}</span>
                    </div>
                    <div class="vendor-card-actions">
                        <a href="/user/vendors/${vendor.id}" class="btn-view"><i class="fas fa-eye"></i> View</a>
                        <a href="tel:${vendor.phone}" class="btn-call"><i class="fas fa-phone"></i></a>
                        ${vendor.whatsapp ? `<a href="https://wa.me/${vendor.whatsapp.replace(/[^0-9]/g, '')}" target="_blank" class="btn-whatsapp"><i class="fab fa-whatsapp"></i></a>` : ''}
                    </div>
                </div>
            </div>
        `;
    });
    
    section.style.display = 'block';
    grid.innerHTML = html;
    
    // Scroll to nearby vendors section
    section.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function showAllVendors() {
    const section = document.querySelector('.all-vendors-section');
    section.classList.add('show');
    
    // Hide the show all vendors button
    const button = document.querySelector('.show-all-vendors-btn');
    if (button) {
        button.style.display = 'none';
    }
    
    // Smooth scroll to all vendors section
    section.scrollIntoView({ behavior: 'smooth', block: 'start' });
}
</script>
