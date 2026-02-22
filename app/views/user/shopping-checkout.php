<!-- Shopping Checkout - E-commerce Flow -->
<style>
    .checkout-header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 25px; border-radius: 12px; margin-bottom: 20px; color: white; }
    .checkout-header h2 { margin: 0; font-size: 1.5rem; }
    .checkout-header p { margin: 5px 0 0; opacity: 0.9; }
    
    .checkout-grid { display: grid; grid-template-columns: 1fr 400px; gap: 20px; }
    @media (max-width: 900px) { .checkout-grid { grid-template-columns: 1fr; } }
    
    .checkout-card { background: white; border-radius: 12px; padding: 20px; box-shadow: 0 2px 12px rgba(0,0,0,0.08); margin-bottom: 20px; }
    .checkout-card h4 { margin: 0 0 15px; color: #2d3748; display: flex; align-items: center; gap: 10px; }
    .checkout-card h4 i { color: #667eea; }
    
    .cart-item { display: flex; align-items: center; padding: 12px 0; border-bottom: 1px solid #edf2f7; }
    .cart-item:last-child { border-bottom: none; }
    .cart-item .letter { width: 45px; height: 45px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 1.2rem; margin-right: 12px; }
    .cart-item .details { flex: 1; }
    .cart-item .name { font-weight: 600; color: #2d3748; }
    .cart-item .qty { font-size: 0.85rem; color: #718096; }
    .cart-item .price { font-weight: 600; color: #667eea; }
    
    .location-box { background: linear-gradient(135deg, #f7fafc 0%, #edf2f7 100%); border-radius: 10px; padding: 20px; text-align: center; }
    .location-box i { font-size: 2.5rem; color: #667eea; margin-bottom: 10px; }
    .location-box p { color: #4a5568; margin-bottom: 15px; }
    .location-box .address { background: white; padding: 10px 15px; border-radius: 8px; margin-top: 10px; font-size: 0.9rem; color: #2d3748; display: none; }
    
    .shop-list { max-height: 300px; overflow-y: auto; }
    .shop-item { padding: 15px; border: 2px solid #edf2f7; border-radius: 10px; margin-bottom: 10px; cursor: pointer; transition: all 0.2s ease; }
    .shop-item:hover { border-color: #667eea; background: #f7fafc; }
    .shop-item.selected { border-color: #667eea; background: linear-gradient(135deg, #ebf4ff 0%, #f0e7ff 100%); }
    .shop-item .shop-name { font-weight: 600; color: #2d3748; }
    .shop-item .shop-type { font-size: 0.8rem; color: #667eea; background: #ebf4ff; padding: 2px 8px; border-radius: 12px; float: right; }
    .shop-item .shop-location { font-size: 0.85rem; color: #718096; margin-top: 5px; }
    .shop-item .shop-reason { font-size: 0.8rem; color: #48bb78; margin-top: 5px; }
    .shop-item .shop-actions { display: flex; gap: 10px; margin-top: 12px; }
    .shop-item .btn-direction { background: linear-gradient(135deg, #4299e1 0%, #3182ce 100%); color: white; border: none; padding: 8px 15px; border-radius: 6px; font-size: 0.8rem; cursor: pointer; transition: all 0.2s; }
    .shop-item .btn-direction:hover { transform: scale(1.05); box-shadow: 0 3px 10px rgba(66, 153, 225, 0.4); }
    .shop-item .btn-select { background: linear-gradient(135deg, #48bb78 0%, #38a169 100%); color: white; border: none; padding: 8px 15px; border-radius: 6px; font-size: 0.8rem; cursor: pointer; flex: 1; transition: all 0.2s; }
    .shop-item .btn-select:hover { transform: scale(1.02); }
    .shop-item.selected .btn-select { background: #667eea; }
    
    .map-container { height: 250px; border-radius: 10px; overflow: hidden; background: #edf2f7; display: flex; align-items: center; justify-content: center; }
    .map-container iframe { width: 100%; height: 100%; border: none; }
    .map-placeholder { color: #a0aec0; text-align: center; }
    .map-placeholder i { font-size: 3rem; margin-bottom: 10px; display: block; }
    
    .summary-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #edf2f7; }
    .summary-row:last-child { border-bottom: none; }
    .summary-row.total { font-weight: 700; font-size: 1.2rem; color: #2d3748; border-top: 2px solid #667eea; margin-top: 10px; padding-top: 15px; }
    
    .checkout-btn { background: linear-gradient(135deg, #48bb78 0%, #38a169 100%); border: none; padding: 15px 30px; font-size: 1rem; font-weight: 600; border-radius: 10px; width: 100%; }
    .checkout-btn:hover { background: linear-gradient(135deg, #38a169 0%, #2f855a 100%); }
    .checkout-btn:disabled { background: #cbd5e0; cursor: not-allowed; }
    
    .step-indicator { display: flex; gap: 10px; margin-bottom: 20px; }
    .step { flex: 1; padding: 12px; background: #edf2f7; border-radius: 8px; text-align: center; font-size: 0.85rem; color: #718096; }
    .step.active { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
    .step.done { background: #48bb78; color: white; }
    .step i { margin-right: 5px; }
</style>

<div class="checkout-header">
    <h2><i class="fas fa-shopping-cart mr-2"></i>Checkout</h2>
    <p>Complete your puja items order</p>
</div>

<!-- Step Indicator -->
<div class="step-indicator">
    <div class="step done" id="step1"><i class="fas fa-check"></i> Cart</div>
    <div class="step active" id="step2"><i class="fas fa-map-marker-alt"></i> Location</div>
    <div class="step" id="step3"><i class="fas fa-store"></i> Select Shop</div>
    <div class="step" id="step4"><i class="fas fa-credit-card"></i> Confirm</div>
</div>

<div class="checkout-grid">
    <!-- Main Column -->
    <div class="checkout-main">
        <!-- Location Section -->
        <div class="checkout-card" id="locationSection">
            <h4><i class="fas fa-map-marker-alt"></i> Your Location</h4>
            <div class="location-box">
                <i class="fas fa-location-arrow"></i>
                <p>Allow location access to find nearby puja shops</p>
                <button class="btn btn-primary" id="allowLocationBtn" onclick="requestLocation()">
                    <i class="fas fa-crosshairs"></i> Allow Location
                </button>
                <div class="address" id="userAddress"></div>
            </div>
        </div>
        
        <!-- Shop Selection -->
        <div class="checkout-card" id="shopSection" style="display: none;">
            <h4><i class="fas fa-store"></i> Select a Shop</h4>
            <div id="shopLoading" style="text-align: center; padding: 30px; display: none;">
                <i class="fas fa-spinner fa-spin fa-2x" style="color: #667eea;"></i>
                <p style="margin-top: 10px; color: #718096;">Finding nearby shops...</p>
            </div>
            <div class="shop-list" id="shopList"></div>
        </div>
    </div>
    
    <!-- Sidebar -->
    <div class="checkout-sidebar">
        <!-- Order Summary -->
        <div class="checkout-card">
            <h4><i class="fas fa-receipt"></i> Order Summary</h4>
            
            <div class="cart-items">
                <?php foreach ($items as $item): ?>
                <div class="cart-item">
                    <div class="letter"><?= strtoupper(substr($item['item_name'], 0, 1)) ?></div>
                    <div class="details">
                        <div class="name"><?= htmlspecialchars($item['item_name']) ?></div>
                        <div class="qty"><?= $item['quantity'] ?> <?= $item['unit'] ?></div>
                    </div>
                    <div class="price">₹<?= number_format($item['estimated_cost'] * $item['quantity']) ?></div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <hr style="margin: 15px 0;">
            
            <div class="summary-row">
                <span>Items</span>
                <span><?= $summary['pending'] ?? count($items) ?></span>
            </div>
            <div class="summary-row total">
                <span>Estimated Total</span>
                <span>₹<?= number_format($summary['estimated_total'] ?? 0) ?></span>
            </div>
        </div>
        
        <!-- Place Order -->
        <div class="checkout-card">
            <textarea id="orderNotes" class="form-control mb-3" placeholder="Add notes (optional)" rows="2"></textarea>
            <button class="btn btn-success checkout-btn" id="placeOrderBtn" disabled onclick="placeOrder()">
                <i class="fas fa-check-circle"></i> Place Order
            </button>
            <p style="font-size: 0.8rem; color: #718096; margin-top: 10px; text-align: center;">
                <i class="fas fa-info-circle"></i> Items will be marked as purchased
            </p>
        </div>
        
        <a href="/user/shopping-list" class="btn btn-outline-secondary btn-block">
            <i class="fas fa-arrow-left"></i> Back to Cart
        </a>
    </div>
</div>

<!-- Hidden Data -->
<input type="hidden" id="global_csrf" value="<?= \App\Core\Auth::csrfToken() ?>">
<input type="hidden" id="user_lat" value="">
<input type="hidden" id="user_lng" value="">
<input type="hidden" id="selected_shop_name" value="">
<input type="hidden" id="selected_shop_location" value="">
<input type="hidden" id="selected_shop_type" value="">

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
let userLocation = null;
let selectedShop = null;

function requestLocation() {
    if (!navigator.geolocation) {
        Swal.fire('Error', 'Geolocation is not supported by your browser', 'error');
        return;
    }
    
    const btn = document.getElementById('allowLocationBtn');
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Getting Location...';
    btn.disabled = true;
    
    navigator.geolocation.getCurrentPosition(
        (position) => {
            const lat = position.coords.latitude;
            const lng = position.coords.longitude;
            
            document.getElementById('user_lat').value = lat;
            document.getElementById('user_lng').value = lng;
            
            // Reverse geocode to get address
            fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`)
                .then(r => r.json())
                .then(data => {
                    userLocation = {
                        lat: lat,
                        lng: lng,
                        address: data.display_name || 'Location detected'
                    };
                    
                    btn.innerHTML = '<i class="fas fa-check"></i> Location Detected';
                    btn.className = 'btn btn-success';
                    
                    const addressDiv = document.getElementById('userAddress');
                    addressDiv.textContent = userLocation.address;
                    addressDiv.style.display = 'block';
                    
                    // Update steps
                    document.getElementById('step2').className = 'step done';
                    document.getElementById('step2').innerHTML = '<i class="fas fa-check"></i> Location';
                    document.getElementById('step3').className = 'step active';
                    
                    // Find shops
                    findNearbyShops();
                })
                .catch(() => {
                    userLocation = { lat: lat, lng: lng, address: 'Location detected' };
                    btn.innerHTML = '<i class="fas fa-check"></i> Location Detected';
                    findNearbyShops();
                });
        },
        (error) => {
            btn.innerHTML = '<i class="fas fa-crosshairs"></i> Allow Location';
            btn.disabled = false;
            Swal.fire('Error', 'Unable to get your location. Please check permissions.', 'error');
        }
    );
}

function findNearbyShops() {
    document.getElementById('shopSection').style.display = 'block';
    document.getElementById('shopLoading').style.display = 'block';
    document.getElementById('shopList').innerHTML = '';
    
    const csrfToken = document.getElementById('global_csrf').value;
    
    fetch('/user/shopping-list/find-nearby-shops', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `_token=${csrfToken}&location=${encodeURIComponent(userLocation.address)}&latitude=${userLocation.lat}&longitude=${userLocation.lng}`
    })
    .then(r => r.json())
    .then(data => {
        document.getElementById('shopLoading').style.display = 'none';
        
        if (data.success && data.shops && data.shops.length > 0) {
            let html = '';
            data.shops.forEach((shop, index) => {
                const shopNameEsc = escapeHtml(shop.name);
                const shopLocEsc = escapeHtml(shop.location);
                const shopTypeEsc = escapeHtml(shop.type || 'Shop');
                html += `
                    <div class="shop-item" id="shop-${index}">
                        <span class="shop-type">${shop.type || 'Shop'}</span>
                        <div class="shop-name">${shop.name}</div>
                        <div class="shop-location"><i class="fas fa-map-marker-alt"></i> ${shop.location}</div>
                        <div class="shop-reason"><i class="fas fa-check-circle"></i> ${shop.reason || 'Recommended'}</div>
                        <div class="shop-actions">
                            <button class="btn-direction" onclick="openShopDirections('${shopNameEsc}', '${shopLocEsc}')"><i class="fas fa-directions"></i> Directions</button>
                            <button class="btn-select" onclick="selectShop(${index}, '${shopNameEsc}', '${shopLocEsc}', '${shopTypeEsc}')"><i class="fas fa-check"></i> Select This Shop</button>
                        </div>
                    </div>
                `;
            });
            document.getElementById('shopList').innerHTML = html;
        } else {
            document.getElementById('shopList').innerHTML = `
                <div style="text-align: center; padding: 30px; color: #718096;">
                    <i class="fas fa-store-slash fa-2x" style="margin-bottom: 10px;"></i>
                    <p>No shops found nearby. Try a different location.</p>
                </div>
            `;
        }
    })
    .catch(() => {
        document.getElementById('shopLoading').style.display = 'none';
        document.getElementById('shopList').innerHTML = `
            <div style="text-align: center; padding: 30px; color: #e53e3e;">
                <i class="fas fa-exclamation-triangle fa-2x" style="margin-bottom: 10px;"></i>
                <p>Failed to find shops. Please try again.</p>
            </div>
        `;
    });
}

function selectShop(index, name, location, type) {
    // Deselect all
    document.querySelectorAll('.shop-item').forEach(el => el.classList.remove('selected'));
    
    // Select this one
    document.getElementById('shop-' + index).classList.add('selected');
    
    selectedShop = { name, location, type };
    document.getElementById('selected_shop_name').value = name;
    document.getElementById('selected_shop_location').value = location;
    document.getElementById('selected_shop_type').value = type;
    
    // Update steps
    document.getElementById('step3').className = 'step done';
    document.getElementById('step3').innerHTML = '<i class="fas fa-check"></i> Shop Selected';
    document.getElementById('step4').className = 'step active';
    
    // Enable place order
    document.getElementById('placeOrderBtn').disabled = false;
    
    // Show map
    showMap(name, location);
}

function showMap(shopName, shopLocation) {
    const query = encodeURIComponent(shopName + ' ' + shopLocation);
    const mapHtml = `<iframe src="https://www.openstreetmap.org/export/embed.html?bbox=72.8,19.0,72.9,19.1&layer=mapnik&marker=19.0,72.85" allowfullscreen></iframe>`;
    
    // Use Google Maps embed for better results
    const googleMapUrl = `https://www.google.com/maps/embed/v1/search?key=AIzaSyBFw0Qbyq9zTFTd-tUY6dZWTgaQzuU17R8&q=${query}`;
    
    // Fallback to OpenStreetMap search
    document.getElementById('mapContainer').innerHTML = `
        <iframe src="https://www.openstreetmap.org/export/embed.html?bbox=72.5,18.8,73.2,19.3&layer=mapnik" style="width:100%;height:100%;"></iframe>
    `;
    
    document.getElementById('directionsBtn').style.display = 'block';
}

function openDirections() {
    if (selectedShop) {
        const query = encodeURIComponent(selectedShop.name + ' ' + selectedShop.location);
        window.open(`https://www.google.com/maps/search/?api=1&query=${query}`, '_blank');
    }
}

// Open directions for a specific shop (from shop card button)
function openShopDirections(shopName, shopLocation) {
    const query = encodeURIComponent(shopName + ' ' + shopLocation);
    window.open(`https://www.google.com/maps/dir/?api=1&destination=${query}`, '_blank');
}

function placeOrder() {
    if (!selectedShop) {
        Swal.fire('Error', 'Please select a shop first.', 'warning');
        return;
    }
    
    const btn = document.getElementById('placeOrderBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Placing Order...';
    
    const csrfToken = document.getElementById('global_csrf').value;
    const notes = document.getElementById('orderNotes').value;
    
    fetch('/user/shopping-list/place-order', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `_token=${csrfToken}&shop_name=${encodeURIComponent(selectedShop.name)}&shop_location=${encodeURIComponent(selectedShop.location)}&shop_type=${encodeURIComponent(selectedShop.type)}&latitude=${document.getElementById('user_lat').value}&longitude=${document.getElementById('user_lng').value}&user_address=${encodeURIComponent(userLocation?.address || '')}&notes=${encodeURIComponent(notes)}`
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Order Placed!',
                text: 'Your order has been confirmed.',
                showConfirmButton: true,
                confirmButtonText: 'View Order'
            }).then(() => {
                window.location.href = '/user/orders/' + data.order_id;
            });
        } else {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-check-circle"></i> Place Order';
            Swal.fire('Error', data.error || 'Failed to place order.', 'error');
        }
    })
    .catch(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-check-circle"></i> Place Order';
        Swal.fire('Error', 'Network error. Please try again.', 'error');
    });
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text || '';
    return div.innerHTML.replace(/'/g, "\\'").replace(/"/g, '\\"');
}
</script>
