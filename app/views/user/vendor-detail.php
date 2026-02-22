<style>
    .vendor-detail-header {
        background: linear-gradient(135deg, #FF6B35 0%, #FF8C42 100%);
        border-radius: 20px;
        padding: 40px;
        color: white;
        margin-bottom: 30px;
        position: relative;
        overflow: hidden;
    }

    .vendor-detail-header .back-link {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: rgba(255,255,255,0.9);
        text-decoration: none;
        margin-bottom: 20px;
        font-size: 0.95rem;
        transition: all 0.3s;
    }

    .vendor-detail-header .back-link:hover {
        color: white;
    }

    .vendor-info-main {
        display: flex;
        gap: 25px;
        align-items: flex-start;
    }

    .vendor-icon-large {
        width: 100px;
        height: 100px;
        background: rgba(255,255,255,0.2);
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .vendor-icon-large i {
        font-size: 3rem;
        color: white;
    }

    .vendor-info-text h1 {
        font-size: 2rem;
        margin-bottom: 10px;
    }

    .vendor-badges {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        margin-bottom: 10px;
    }

    .vendor-badges .badge {
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 500;
    }

    .badge-category {
        background: rgba(255,255,255,0.2);
        color: white;
    }

    .badge-featured {
        background: linear-gradient(135deg, #F59E0B 0%, #D97706 100%);
        color: white;
    }

    .badge-verified {
        background: linear-gradient(135deg, #10B981 0%, #059669 100%);
        color: white;
    }

    .vendor-rating {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 1rem;
    }

    .vendor-rating i {
        color: #FCD34D;
    }

    .content-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 30px;
    }

    @media (max-width: 992px) {
        .content-grid {
            grid-template-columns: 1fr;
        }
    }

    .detail-card {
        background: white;
        border-radius: 20px;
        padding: 25px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
        margin-bottom: 25px;
    }

    .detail-card h3 {
        font-size: 1.2rem;
        margin-bottom: 20px;
        color: var(--dark);
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .detail-card h3 i {
        color: var(--primary);
    }

    .info-list {
        list-style: none;
    }

    .info-list li {
        padding: 15px 0;
        border-bottom: 1px solid #F3F4F6;
        display: flex;
        align-items: flex-start;
        gap: 15px;
    }

    .info-list li:last-child {
        border-bottom: none;
    }

    .info-list li i {
        width: 20px;
        color: var(--primary);
        margin-top: 3px;
    }

    .info-list li .label {
        font-weight: 500;
        color: #6B7280;
        font-size: 0.85rem;
        display: block;
        margin-bottom: 3px;
    }

    .info-list li .value {
        color: var(--dark);
    }

    .info-list li .value a {
        color: var(--primary);
        text-decoration: none;
    }

    .info-list li .value a:hover {
        text-decoration: underline;
    }

    .description-text {
        color: #4B5563;
        line-height: 1.8;
    }

    .services-list {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .services-list .service-tag {
        background: #EFF6FF;
        color: #1D4ED8;
        padding: 8px 15px;
        border-radius: 20px;
        font-size: 0.9rem;
    }

    .price-range {
        background: linear-gradient(135deg, #FFF7ED 0%, #FFEDD5 100%);
        border-radius: 15px;
        padding: 20px;
        text-align: center;
    }

    .price-range h4 {
        color: #9A3412;
        margin-bottom: 5px;
    }

    .price-range .price {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--primary);
    }

    .contact-actions {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .contact-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        padding: 14px 20px;
        border-radius: 12px;
        font-weight: 600;
        font-size: 1rem;
        text-decoration: none;
        transition: all 0.3s;
        border: none;
        cursor: pointer;
    }

    .btn-call {
        background: linear-gradient(135deg, #10B981 0%, #059669 100%);
        color: white;
    }

    .btn-call:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(16, 185, 129, 0.4);
    }

    .btn-whatsapp {
        background: linear-gradient(135deg, #25D366 0%, #1DA851 100%);
        color: white;
    }

    .btn-whatsapp:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(37, 211, 102, 0.4);
    }

    .btn-email {
        background: linear-gradient(135deg, #3B82F6 0%, #2563EB 100%);
        color: white;
    }

    .btn-email:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(59, 130, 246, 0.4);
    }

    .btn-website {
        background: linear-gradient(135deg, #6366F1 0%, #4F46E5 100%);
        color: white;
    }

    .btn-website:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(99, 102, 241, 0.4);
    }

    .map-container {
        position: relative;
        height: 250px;
        border-radius: 15px;
        overflow: hidden;
        background: #E5E7EB;
    }

    .map-container iframe {
        width: 100%;
        height: 100%;
        border: none;
    }

    .map-placeholder {
        width: 100%;
        height: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        color: #6B7280;
    }

    .map-placeholder i {
        font-size: 3rem;
        margin-bottom: 10px;
        color: #9CA3AF;
    }

    .similar-vendors-section {
        margin-top: 40px;
    }

    .similar-vendors-section h2 {
        font-size: 1.4rem;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .similar-vendors-section h2 i {
        color: var(--primary);
    }

    .similar-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 20px;
    }

    .similar-card {
        background: white;
        border-radius: 16px;
        padding: 20px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        transition: all 0.3s;
    }

    .similar-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
    }

    .similar-card h4 {
        font-size: 1.1rem;
        margin-bottom: 8px;
        color: var(--dark);
    }

    .similar-card .info-row {
        display: flex;
        align-items: center;
        gap: 8px;
        color: #6B7280;
        font-size: 0.9rem;
        margin-bottom: 5px;
    }

    .similar-card .info-row i {
        width: 16px;
        color: var(--primary);
    }

    .similar-card a {
        display: inline-block;
        margin-top: 12px;
        color: var(--primary);
        text-decoration: none;
        font-weight: 500;
    }

    .similar-card a:hover {
        text-decoration: underline;
    }

    @media (max-width: 768px) {
        .vendor-detail-header {
            padding: 25px;
        }
        .vendor-info-main {
            flex-direction: column;
            align-items: center;
            text-align: center;
        }
        .vendor-info-text h1 {
            font-size: 1.5rem;
        }
        .vendor-badges {
            justify-content: center;
        }
        .vendor-rating {
            justify-content: center;
        }
    }
</style>

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
$icon = $categoryIcons[$vendor['category']] ?? 'fa-store';
?>

<!-- Header -->
<div class="vendor-detail-header">
    <a href="/user/vendors" class="back-link">
        <i class="fas fa-arrow-left"></i> Back to Vendors
    </a>
    
    <div class="vendor-info-main">
        <div class="vendor-icon-large">
            <i class="fas <?= $icon ?>"></i>
        </div>
        <div class="vendor-info-text">
            <h1><?= htmlspecialchars($vendor['name']) ?></h1>
            <div class="vendor-badges">
                <span class="badge badge-category">
                    <i class="fas <?= $icon ?>"></i>
                    <?= htmlspecialchars($categories[$vendor['category']] ?? ucfirst($vendor['category'])) ?>
                </span>
                <?php if ($vendor['is_featured']): ?>
                <span class="badge badge-featured"><i class="fas fa-star"></i> Featured</span>
                <?php endif; ?>
                <?php if ($vendor['is_verified']): ?>
                <span class="badge badge-verified"><i class="fas fa-check-circle"></i> Verified</span>
                <?php endif; ?>
            </div>
            <?php if ($vendor['average_rating'] > 0): ?>
            <div class="vendor-rating">
                <?php for ($i = 1; $i <= 5; $i++): ?>
                <i class="fas fa-star<?= $i <= round($vendor['average_rating']) ? '' : '-half-alt' ?>"></i>
                <?php endfor; ?>
                <span><?= number_format($vendor['average_rating'], 1) ?> (<?= $vendor['total_reviews'] ?> reviews)</span>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="content-grid">
    <!-- Left Column - Details -->
    <div class="left-column">
        <!-- Description -->
        <?php if (!empty($vendor['description'])): ?>
        <div class="detail-card">
            <h3><i class="fas fa-info-circle"></i> About</h3>
            <p class="description-text"><?= nl2br(htmlspecialchars($vendor['description'])) ?></p>
        </div>
        <?php endif; ?>

        <!-- Services Offered -->
        <?php if (!empty($vendor['services_offered'])): ?>
        <div class="detail-card">
            <h3><i class="fas fa-concierge-bell"></i> Services Offered</h3>
            <div class="services-list">
                <?php 
                $services = explode(',', $vendor['services_offered']);
                foreach ($services as $service):
                    $service = trim($service);
                    if (!empty($service)):
                ?>
                <span class="service-tag"><?= htmlspecialchars($service) ?></span>
                <?php 
                    endif;
                endforeach; 
                ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Contact Information -->
        <div class="detail-card">
            <h3><i class="fas fa-address-card"></i> Contact Information</h3>
            <ul class="info-list">
                <?php if (!empty($vendor['contact_person'])): ?>
                <li>
                    <i class="fas fa-user"></i>
                    <div>
                        <span class="label">Contact Person</span>
                        <span class="value"><?= htmlspecialchars($vendor['contact_person']) ?></span>
                    </div>
                </li>
                <?php endif; ?>
                <li>
                    <i class="fas fa-phone"></i>
                    <div>
                        <span class="label">Phone</span>
                        <span class="value">
                            <a href="tel:<?= $vendor['phone'] ?>"><?= htmlspecialchars($vendor['phone']) ?></a>
                        </span>
                    </div>
                </li>
                <?php if (!empty($vendor['alternate_phone'])): ?>
                <li>
                    <i class="fas fa-phone-alt"></i>
                    <div>
                        <span class="label">Alternate Phone</span>
                        <span class="value">
                            <a href="tel:<?= $vendor['alternate_phone'] ?>"><?= htmlspecialchars($vendor['alternate_phone']) ?></a>
                        </span>
                    </div>
                </li>
                <?php endif; ?>
                <?php if (!empty($vendor['whatsapp'])): ?>
                <li>
                    <i class="fab fa-whatsapp"></i>
                    <div>
                        <span class="label">WhatsApp</span>
                        <span class="value">
                            <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $vendor['whatsapp']) ?>" target="_blank"><?= htmlspecialchars($vendor['whatsapp']) ?></a>
                        </span>
                    </div>
                </li>
                <?php endif; ?>
                <?php if (!empty($vendor['email'])): ?>
                <li>
                    <i class="fas fa-envelope"></i>
                    <div>
                        <span class="label">Email</span>
                        <span class="value">
                            <a href="mailto:<?= $vendor['email'] ?>"><?= htmlspecialchars($vendor['email']) ?></a>
                        </span>
                    </div>
                </li>
                <?php endif; ?>
                <?php if (!empty($vendor['website'])): ?>
                <li>
                    <i class="fas fa-globe"></i>
                    <div>
                        <span class="label">Website</span>
                        <span class="value">
                            <a href="<?= htmlspecialchars($vendor['website']) ?>" target="_blank"><?= htmlspecialchars($vendor['website']) ?></a>
                        </span>
                    </div>
                </li>
                <?php endif; ?>
            </ul>
        </div>

        <!-- Address -->
        <div class="detail-card">
            <h3><i class="fas fa-map-marker-alt"></i> Address</h3>
            <ul class="info-list">
                <li>
                    <i class="fas fa-building"></i>
                    <div>
                        <span class="label">Full Address</span>
                        <span class="value">
                            <?= htmlspecialchars($vendor['address_line1']) ?>
                            <?php if (!empty($vendor['address_line2'])): ?>
                            <br><?= htmlspecialchars($vendor['address_line2']) ?>
                            <?php endif; ?>
                        </span>
                    </div>
                </li>
                <li>
                    <i class="fas fa-city"></i>
                    <div>
                        <span class="label">City, State</span>
                        <span class="value"><?= htmlspecialchars($vendor['city']) ?>, <?= htmlspecialchars($vendor['state']) ?></span>
                    </div>
                </li>
                <li>
                    <i class="fas fa-map-pin"></i>
                    <div>
                        <span class="label">Pincode</span>
                        <span class="value"><?= htmlspecialchars($vendor['pincode']) ?></span>
                    </div>
                </li>
                <?php if ($vendor['service_area_km']): ?>
                <li>
                    <i class="fas fa-route"></i>
                    <div>
                        <span class="label">Service Area</span>
                        <span class="value"><?= $vendor['service_area_km'] ?> km radius</span>
                    </div>
                </li>
                <?php endif; ?>
            </ul>
            
            <!-- Map -->
            <div class="map-container" style="margin-top: 20px;">
                <?php if ($vendor['latitude'] && $vendor['longitude']): ?>
                <iframe 
                    src="https://www.openstreetmap.org/export/embed.html?bbox=<?= $vendor['longitude'] - 0.01 ?>,<?= $vendor['latitude'] - 0.01 ?>,<?= $vendor['longitude'] + 0.01 ?>,<?= $vendor['latitude'] + 0.01 ?>&layer=mapnik&marker=<?= $vendor['latitude'] ?>,<?= $vendor['longitude'] ?>" 
                    loading="lazy">
                </iframe>
                <?php else: ?>
                <div class="map-placeholder">
                    <i class="fas fa-map-marked-alt"></i>
                    <span>Map not available</span>
                </div>
                <?php endif; ?>
            </div>
            
            <?php 
            // Use map_url if available, otherwise fallback to lat/lng
            $directionsUrl = !empty($vendor['map_url']) 
                ? $vendor['map_url'] 
                : ($vendor['latitude'] && $vendor['longitude'] 
                    ? "https://www.google.com/maps/search/?api=1&query={$vendor['latitude']},{$vendor['longitude']}" 
                    : null);
            ?>
            <?php if ($directionsUrl): ?>
            <a href="<?= htmlspecialchars($directionsUrl) ?>" 
               target="_blank" 
               class="contact-btn" 
               style="margin-top: 15px; background: linear-gradient(135deg, #EF4444 0%, #DC2626 100%);">
                <i class="fas fa-directions"></i> Get Directions
            </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Right Column - Sidebar -->
    <div class="right-column">
        <!-- Pricing -->
        <?php if ($vendor['min_price'] || $vendor['max_price']): ?>
        <div class="detail-card">
            <h3><i class="fas fa-rupee-sign"></i> Pricing</h3>
            <div class="price-range">
                <h4>Price Range</h4>
                <div class="price">
                    <?php if ($vendor['min_price'] && $vendor['max_price']): ?>
                        ₹<?= number_format($vendor['min_price']) ?> - ₹<?= number_format($vendor['max_price']) ?>
                    <?php elseif ($vendor['min_price']): ?>
                        Starting from ₹<?= number_format($vendor['min_price']) ?>
                    <?php elseif ($vendor['max_price']): ?>
                        Up to ₹<?= number_format($vendor['max_price']) ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Quick Contact -->
        <div class="detail-card">
            <h3><i class="fas fa-bolt"></i> Quick Contact</h3>
            <div class="contact-actions">
                <a href="tel:<?= $vendor['phone'] ?>" class="contact-btn btn-call">
                    <i class="fas fa-phone"></i> Call Now
                </a>
                <?php if (!empty($vendor['whatsapp'])): ?>
                <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $vendor['whatsapp']) ?>?text=Hi, I found you on Sanskar AI. I'm interested in your services." 
                   target="_blank" class="contact-btn btn-whatsapp">
                    <i class="fab fa-whatsapp"></i> WhatsApp
                </a>
                <?php endif; ?>
                <?php if (!empty($vendor['email'])): ?>
                <a href="mailto:<?= $vendor['email'] ?>?subject=Inquiry from Sanskar AI" class="contact-btn btn-email">
                    <i class="fas fa-envelope"></i> Send Email
                </a>
                <?php endif; ?>
                <?php if (!empty($vendor['website'])): ?>
                <a href="<?= htmlspecialchars($vendor['website']) ?>" target="_blank" class="contact-btn btn-website">
                    <i class="fas fa-globe"></i> Visit Website
                </a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Business Hours (placeholder) -->
        <div class="detail-card">
            <h3><i class="fas fa-clock"></i> Availability</h3>
            <p style="color: #6B7280; text-align: center; padding: 20px 0;">
                <i class="fas fa-info-circle"></i><br>
                Contact the vendor directly to check availability for your event.
            </p>
        </div>
    </div>
</div>

<!-- Similar Vendors -->
<?php if (!empty($similarVendors)): ?>
<div class="similar-vendors-section">
    <h2><i class="fas fa-store"></i> Similar Vendors in <?= htmlspecialchars($vendor['city']) ?></h2>
    <div class="similar-grid">
        <?php foreach ($similarVendors as $sv): ?>
        <div class="similar-card">
            <h4><?= htmlspecialchars($sv['name']) ?></h4>
            <div class="info-row">
                <i class="fas fa-map-marker-alt"></i>
                <span><?= htmlspecialchars($sv['city']) ?></span>
            </div>
            <div class="info-row">
                <i class="fas fa-phone"></i>
                <span><?= htmlspecialchars($sv['phone']) ?></span>
            </div>
            <?php if ($sv['min_price']): ?>
            <div class="info-row">
                <i class="fas fa-rupee-sign"></i>
                <span>From ₹<?= number_format($sv['min_price']) ?></span>
            </div>
            <?php endif; ?>
            <a href="/user/vendors/<?= $sv['id'] ?>">View Details <i class="fas fa-arrow-right"></i></a>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>
