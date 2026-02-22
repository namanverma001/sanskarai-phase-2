<!-- Shopping List - Ecommerce Style -->
<style>
    .shop-header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 20px 25px; border-radius: 12px; margin-bottom: 20px; color: white; }
    .shop-header h2 { margin: 0; font-size: 1.5rem; }
    .shop-header p { margin: 5px 0 0; opacity: 0.9; font-size: 0.9rem; }
    
    .stat-row { display: flex; gap: 12px; margin-bottom: 20px; flex-wrap: wrap; }
    .stat-box { flex: 1; min-width: 120px; background: white; border-radius: 10px; padding: 12px 15px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); text-align: center; }
    .stat-box .num { font-size: 1.4rem; font-weight: 700; color: #1a202c; }
    .stat-box .label { font-size: 0.75rem; color: #718096; text-transform: uppercase; letter-spacing: 0.5px; }
    .stat-box.primary { border-left: 3px solid #667eea; }
    .stat-box.success { border-left: 3px solid #48bb78; }
    .stat-box.warning { border-left: 3px solid #ed8936; }
    .stat-box.info { border-left: 3px solid #4299e1; }
    
    .product-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 15px; }
    .product-card { background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.06); transition: all 0.2s ease; display: flex; flex-direction: column; }
    .product-card:hover { transform: translateY(-3px); box-shadow: 0 8px 20px rgba(0,0,0,0.1); }
    .product-card.purchased { opacity: 0.6; }
    .product-card.purchased .product-name { text-decoration: line-through; color: #a0aec0; }
    
    .product-img { height: 100px; background: linear-gradient(135deg, #f7fafc 0%, #edf2f7 100%); display: flex; align-items: center; justify-content: center; position: relative; }
    .product-img .letter { font-size: 2.5rem; font-weight: bold; color: #cbd5e0; }
    .product-img .badge-done { position: absolute; top: 8px; right: 8px; background: #48bb78; color: white; padding: 3px 8px; border-radius: 12px; font-size: 0.65rem; font-weight: 600; }
    
    .product-body { padding: 12px; flex: 1; display: flex; flex-direction: column; }
    .product-name { font-weight: 600; font-size: 0.95rem; color: #2d3748; margin-bottom: 4px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .product-meta { font-size: 0.75rem; color: #718096; margin-bottom: 8px; }
    .product-tags { display: flex; gap: 5px; flex-wrap: wrap; margin-bottom: 10px; }
    .product-tags .tag { background: #edf2f7; color: #4a5568; padding: 2px 8px; border-radius: 12px; font-size: 0.7rem; }
    .product-tags .tag.price { background: #ebf8ff; color: #2b6cb0; }
    
    /* Quantity Controls - E-commerce Style */
    .qty-controls { display: flex; align-items: center; gap: 8px; background: #f7fafc; border-radius: 8px; padding: 6px 10px; margin-bottom: 10px; }
    .qty-btn { width: 28px; height: 28px; border: none; border-radius: 6px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; font-size: 0.75rem; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.2s ease; }
    .qty-btn:hover { transform: scale(1.1); box-shadow: 0 2px 8px rgba(102, 126, 234, 0.4); }
    .qty-btn:active { transform: scale(0.95); }
    .qty-btn.minus { background: linear-gradient(135deg, #ed8936 0%, #dd6b20 100%); }
    .qty-btn.minus:hover { box-shadow: 0 2px 8px rgba(237, 137, 54, 0.4); }
    .qty-value { font-weight: 700; font-size: 1rem; color: #2d3748; min-width: 30px; text-align: center; }
    .qty-unit { font-size: 0.75rem; color: #718096; }
    
    .product-actions { margin-top: auto; display: flex; gap: 6px; }
    .product-actions .btn { flex: 1; padding: 6px 10px; font-size: 0.75rem; border-radius: 6px; }
    
    .empty-state { text-align: center; padding: 50px 20px; background: #f7fafc; border-radius: 12px; }
    .empty-state i { font-size: 3rem; color: #cbd5e0; margin-bottom: 15px; }
    .empty-state h5 { color: #718096; margin-bottom: 15px; }
    
    .add-btn { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; }
    .add-btn:hover { background: linear-gradient(135deg, #5a67d8 0%, #6b46c1 100%); }
</style>

<div class="shop-header d-flex justify-content-between align-items-center">
    <div>
        <h2><i class="fas fa-shopping-bag mr-2"></i>Shopping List</h2>
        <p>Manage your puja items and find nearby shops</p>
    </div>
    <button class="btn btn-light" onclick="toggleAddForm()">
        <i class="fas fa-plus" id="addBtnIcon"></i> <span id="addBtnText">Add Item</span>
    </button>
</div>

<!-- Inline Add Item Form (Hidden by default) -->
<div id="addItemForm" style="display: none; background: white; border-radius: 10px; padding: 20px; margin-bottom: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.08);">
    <form method="POST" action="/user/shopping-list">
        <?= \App\Core\Auth::csrfField() ?>
        <div class="row align-items-end">
            <div class="col-md-4 col-lg-3 mb-2">
                <label class="font-weight-bold" style="font-size: 0.85rem;">Item Name</label>
                <input type="text" name="item_name" class="form-control form-control-sm" required placeholder="e.g. Incense Sticks">
            </div>
            <div class="col-6 col-md-2 col-lg-1 mb-2">
                <label style="font-size: 0.85rem;">Qty</label>
                <input type="number" name="quantity" class="form-control form-control-sm" value="1" min="0.1" step="0.1">
            </div>
            <div class="col-6 col-md-2 col-lg-2 mb-2">
                <label style="font-size: 0.85rem;">Unit</label>
                <select name="unit" class="form-control form-control-sm">
                    <option value="piece">Piece</option>
                    <option value="kg">Kg</option>
                    <option value="grams">Grams</option>
                    <option value="packet">Packet</option>
                    <option value="liter">Liter</option>
                    <option value="bunch">Bunch</option>
                </select>
            </div>
            <div class="col-6 col-md-2 col-lg-2 mb-2">
                <label style="font-size: 0.85rem;">Est. Cost ₹</label>
                <input type="number" name="estimated_cost" class="form-control form-control-sm" min="0" placeholder="Optional">
            </div>
            <div class="col-6 col-md-2 col-lg-2 mb-2">
                <button type="submit" class="btn btn-primary btn-sm btn-block"><i class="fas fa-plus"></i> Add</button>
            </div>
        </div>
    </form>
</div>

<script>
function toggleAddForm() {
    const form = document.getElementById('addItemForm');
    const icon = document.getElementById('addBtnIcon');
    const text = document.getElementById('addBtnText');
    
    if (form.style.display === 'none') {
        form.style.display = 'block';
        icon.className = 'fas fa-times';
        text.textContent = 'Cancel';
    } else {
        form.style.display = 'none';
        icon.className = 'fas fa-plus';
        text.textContent = 'Add Item';
    }
}
</script>

<!-- Stats Row -->
<div class="stat-row">
    <div class="stat-box primary">
        <div class="num"><?= $summary['total_items'] ?? 0 ?></div>
        <div class="label">Total</div>
    </div>
    <div class="stat-box success">
        <div class="num"><?= $summary['purchased'] ?? 0 ?></div>
        <div class="label">Bought</div>
    </div>
    <div class="stat-box warning">
        <div class="num"><?= $summary['pending'] ?? 0 ?></div>
        <div class="label">Pending</div>
    </div>
    <div class="stat-box info">
        <div class="num">₹<?= number_format($summary['estimated_total'] ?? 0) ?></div>
        <div class="label">Est. Cost</div>
    </div>
</div>

<!-- Checkout Button -->
<?php if (($summary['pending'] ?? 0) > 0): ?>
<div style="background: linear-gradient(135deg, #48bb78 0%, #38a169 100%); padding: 15px 20px; border-radius: 10px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
    <div style="color: white;">
        <strong><?= $summary['pending'] ?> item<?= $summary['pending'] > 1 ? 's' : '' ?> ready for checkout</strong>
        <span style="opacity: 0.9; margin-left: 10px;">₹<?= number_format($summary['estimated_total'] ?? 0) ?> estimated</span>
    </div>
    <a href="/user/shopping-list/checkout" class="btn btn-light" style="font-weight: 600;">
        <i class="fas fa-shopping-cart"></i> Proceed to Checkout
    </a>
</div>
<?php endif; ?>

<!-- Product Grid -->
<?php if (empty($items)): ?>
    <div class="empty-state">
        <i class="fas fa-shopping-cart"></i>
        <h5>Your shopping list is empty</h5>
        <button class="btn add-btn text-white" data-toggle="modal" data-target="#addItemModal">
            <i class="fas fa-plus"></i> Add Your First Item
        </button>
    </div>
<?php else: ?>
    <div class="product-grid">
        <?php foreach ($items as $item): ?>
        <div class="product-card <?= $item['is_purchased'] ? 'purchased' : '' ?>">
            <div class="product-img">
                <div class="letter"><?= strtoupper(substr($item['item_name'], 0, 1)) ?></div>
                <?php if ($item['is_purchased']): ?>
                    <span class="badge-done"><i class="fas fa-check"></i> Done</span>
                <?php endif; ?>
            </div>
            <div class="product-body">
                <div class="product-name" title="<?= htmlspecialchars($item['item_name']) ?>"><?= htmlspecialchars($item['item_name']) ?></div>
                <?php if (!empty($item['item_name_local'])): ?>
                    <div class="product-meta"><?= htmlspecialchars($item['item_name_local']) ?></div>
                <?php endif; ?>
                
                <!-- Quantity Adjustment -->
                <div class="qty-controls" data-id="<?= $item['id'] ?>">
                    <button type="button" class="qty-btn minus" onclick="adjustQuantity(<?= $item['id'] ?>, -1)">
                        <i class="fas fa-minus"></i>
                    </button>
                    <span class="qty-value" id="qty-<?= $item['id'] ?>"><?= $item['quantity'] ?></span>
                    <span class="qty-unit"><?= $item['unit'] ?></span>
                    <button type="button" class="qty-btn plus" onclick="adjustQuantity(<?= $item['id'] ?>, 1)">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
                
                <?php if ($item['estimated_cost']): ?>
                    <div class="product-meta"><span class="tag price">₹<?= number_format($item['estimated_cost']) ?></span></div>
                <?php endif; ?>
                
                <?php if (!empty($item['ritual_name'])): ?>
                    <div class="product-meta"><i class="fas fa-om text-primary"></i> <?= htmlspecialchars($item['ritual_name']) ?></div>
                <?php endif; ?>
                
                <div class="product-actions">
                    <?php if (!$item['is_purchased']): ?>
                        <form method="POST" action="/user/shopping-list/<?= $item['id'] ?>/purchased" style="flex:1;">
                            <?= \App\Core\Auth::csrfField() ?>
                            <button type="submit" class="btn btn-outline-success btn-block" title="Mark as Bought"><i class="fas fa-check"></i> Bought</button>
                        </form>
                    <?php else: ?>
                        <form method="POST" action="/user/shopping-list/<?= $item['id'] ?>/unpurchased" style="flex:1;">
                            <?= \App\Core\Auth::csrfField() ?>
                            <button type="submit" class="btn btn-outline-warning btn-block" title="Unmark"><i class="fas fa-undo"></i> Unmark</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<!-- Global CSRF Token for JS -->
<input type="hidden" id="global_csrf" value="<?= \App\Core\Auth::csrfToken() ?>">

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
function findNearbyShops(itemName) {
    if (!navigator.geolocation) {
        Swal.fire('Error', 'Geolocation is not supported by your browser', 'error');
        return;
    }

    Swal.fire({
        title: 'Locating you...',
        text: 'Please allow location access.',
        icon: 'info',
        showConfirmButton: false,
        allowOutsideClick: false
    });

    navigator.geolocation.getCurrentPosition(
        (position) => {
            const lat = position.coords.latitude;
            const lng = position.coords.longitude;

            Swal.update({ title: 'Getting Address...', text: 'Converting coordinates...' });

            fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`)
                .then(r => r.json())
                .then(data => {
                    const address = data.display_name || 'Unknown Location';
                    Swal.update({ title: 'Finding Shops...', text: `Searching near your location...` });
                    findShopsInBackend(address, itemName);
                })
                .catch(() => Swal.fire('Error', 'Could not determine your address.', 'error'));
        },
        () => Swal.fire('Error', 'Unable to get your location. Please check permissions.', 'error')
    );
}

function findShopsInBackend(location, item) {
    const csrfToken = document.getElementById('global_csrf').value;

    fetch('/user/shopping-list/find-shops', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `_token=${csrfToken}&location=${encodeURIComponent(location)}&item=${encodeURIComponent(item)}`
    })
    .then(r => r.json())
    .then(data => {
        if (data.success && data.shops.length > 0) {
            let html = '<div style="text-align:left;">';
            data.shops.forEach(shop => {
                html += `
                    <div style="padding:12px; border-bottom:1px solid #eee;">
                        <strong style="color:#667eea;">${shop.name}</strong>
                        <span style="float:right; font-size:0.8rem; color:#718096;">${shop.type}</span>
                        <p style="margin:5px 0; font-size:0.85rem; color:#4a5568;">${shop.location}</p>
                        <small style="color:#48bb78;"><i class="fas fa-check"></i> ${shop.reason}</small>
                        <br><a href="https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(shop.name + ' ' + shop.location)}" target="_blank" class="btn btn-sm btn-outline-primary mt-2"><i class="fas fa-directions"></i> Directions</a>
                    </div>`;
            });
            html += '</div>';
            Swal.fire({ title: `Shops for ${item}`, html: html, width: '500px', showCloseButton: true, showConfirmButton: false });
        } else {
            Swal.fire('No Results', data.error || 'No nearby shops found.', 'warning');
        }
    })
    .catch(() => Swal.fire('Error', 'Server communication failed.', 'error'));
}

// Quantity Adjustment Function
function adjustQuantity(itemId, delta) {
    const qtySpan = document.getElementById('qty-' + itemId);
    let currentQty = parseFloat(qtySpan.textContent);
    let newQty = currentQty + delta;
    
    // Minimum quantity is 0.1
    if (newQty < 0.1) newQty = 0.1;
    
    // Round to 1 decimal place
    newQty = Math.round(newQty * 10) / 10;
    
    // Update UI immediately for responsiveness
    qtySpan.textContent = newQty;
    
    // Send AJAX request
    const csrfToken = document.getElementById('global_csrf').value;
    
    fetch('/user/shopping-list/' + itemId + '/update-quantity', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `_token=${csrfToken}&quantity=${newQty}`
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            // Update with server value (in case of rounding differences)
            qtySpan.textContent = data.quantity;
        } else {
            // Revert on error
            qtySpan.textContent = currentQty;
            Swal.fire('Error', data.error || 'Failed to update quantity.', 'error');
        }
    })
    .catch(() => {
        // Revert on network error
        qtySpan.textContent = currentQty;
        Swal.fire('Error', 'Failed to update quantity.', 'error');
    });
}
</script>
