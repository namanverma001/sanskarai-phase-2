<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-<?= $isEdit ? 'edit' : 'plus' ?>"></i> 
            <?= $isEdit ? 'Edit Ritual' : 'Create Ritual' ?>
        </h3>
        <a href="/admin/rituals" class="btn btn-sm" style="background: #E5E7EB;">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>
    
    <form method="POST" action="<?= $isEdit ? '/admin/rituals/' . $ritual['id'] : '/admin/rituals' ?>">
        <?= \App\Core\Auth::csrfField() ?>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label for="name">Ritual Name *</label>
                <input type="text" id="name" name="name" class="form-control" required
                       value="<?= htmlspecialchars($ritual['name'] ?? '') ?>">
            </div>
            
            <div class="form-group">
                <label for="name_sanskrit">Sanskrit Name</label>
                <input type="text" id="name_sanskrit" name="name_sanskrit" class="form-control"
                       value="<?= htmlspecialchars($ritual['name_sanskrit'] ?? '') ?>">
            </div>
            
            <div class="form-group">
                <label for="category">Category *</label>
                <input type="text" id="category" name="category" class="form-control" required
                       list="categories" value="<?= htmlspecialchars($ritual['category'] ?? '') ?>">
                <datalist id="categories">
                    <?php foreach ($categories ?? [] as $cat): ?>
                    <option value="<?= htmlspecialchars($cat) ?>">
                    <?php endforeach; ?>
                </datalist>
            </div>
            
            <div class="form-group">
                <label for="difficulty">Difficulty *</label>
                <select id="difficulty" name="difficulty" class="form-control" required>
                    <option value="easy" <?= ($ritual['difficulty'] ?? '') === 'easy' ? 'selected' : '' ?>>Easy</option>
                    <option value="medium" <?= ($ritual['difficulty'] ?? 'medium') === 'medium' ? 'selected' : '' ?>>Medium</option>
                    <option value="hard" <?= ($ritual['difficulty'] ?? '') === 'hard' ? 'selected' : '' ?>>Hard</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="duration_minutes">Duration (minutes) *</label>
                <input type="number" id="duration_minutes" name="duration_minutes" class="form-control" required
                       min="1" value="<?= htmlspecialchars($ritual['duration_minutes'] ?? 60) ?>">
            </div>
            
            <div class="form-group">
                <label for="deity">Deity</label>
                <input type="text" id="deity" name="deity" class="form-control"
                       value="<?= htmlspecialchars($ritual['deity'] ?? '') ?>">
            </div>
            
            <div class="form-group">
                <label for="community_name">Community Name</label>
                <input type="text" id="community_name" name="community_name" class="form-control"
                       placeholder="e.g., Shelke, Brahmin, Maratha"
                       value="<?= htmlspecialchars($ritual['community_name'] ?? '') ?>">
            </div>
            
            <div class="form-group">
                <label for="religion">Religion</label>
                <input type="text" id="religion" name="religion" class="form-control"
                       placeholder="e.g., Hinduism, Jainism"
                       value="<?= htmlspecialchars($ritual['religion'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="occasion_type">Occasion Type</label>
                <input type="text" id="occasion_type" name="occasion_type" class="form-control"
                       value="<?= htmlspecialchars($ritual['occasion_type'] ?? '') ?>">
            </div>
            
            <div class="form-group">
                <label for="best_time">Best Time</label>
                <input type="text" id="best_time" name="best_time" class="form-control"
                       value="<?= htmlspecialchars($ritual['best_time'] ?? '') ?>">
            </div>
        </div>
        
        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" class="form-control" rows="4"><?= htmlspecialchars($ritual['description'] ?? '') ?></textarea>
        </div>
        
        <div class="form-group">
            <label for="significance">Significance</label>
            <textarea id="significance" name="significance" class="form-control" rows="3"><?= htmlspecialchars($ritual['significance'] ?? '') ?></textarea>
        </div>
        
        <div style="display: flex; gap: 20px; margin-bottom: 20px;">
            <label style="display: flex; align-items: center; gap: 8px;">
                <input type="checkbox" name="is_active" <?= ($ritual['is_active'] ?? 1) ? 'checked' : '' ?>>
                Active
            </label>
            <label style="display: flex; align-items: center; gap: 8px;">
                <input type="checkbox" name="is_featured" <?= ($ritual['is_featured'] ?? 0) ? 'checked' : '' ?>>
                Featured
            </label>
        </div>
        
        <div style="display: flex; gap: 15px; align-items: center;">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> <?= $isEdit ? 'Update Ritual' : 'Create Ritual' ?>
            </button>
            
            <?php if ($isEdit): ?>
            <button type="button" class="btn btn-danger" onclick="deleteRitual(<?= $ritual['id'] ?>)">
                <i class="fas fa-trash"></i> Delete Ritual
            </button>
            <?php endif; ?>
        </div>
    </form>
</div>

<?php if ($isEdit): ?>
<!-- Hidden Delete Form (outside main form) -->
<form id="deleteRitualForm" method="POST" action="/admin/rituals/<?= $ritual['id'] ?>/delete" style="display: none;">
    <?= \App\Core\Auth::csrfField() ?>
</form>

<script>
function deleteRitual(id) {
    if (confirm('Are you sure you want to DELETE this ritual? This will also delete all steps and items!')) {
        document.getElementById('deleteRitualForm').submit();
    }
}
</script>

<!-- RITUAL STEPS SECTION -->
<div class="card" style="margin-top: 25px;">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-list-ol"></i> Ritual Steps</h3>
        <button type="button" class="btn btn-sm btn-primary" onclick="document.getElementById('addStepForm').style.display = document.getElementById('addStepForm').style.display === 'none' ? 'block' : 'none'">
            <i class="fas fa-plus"></i> Add Step
        </button>
    </div>
    
    <!-- Add Step Form -->
    <div id="addStepForm" style="display: none; padding: 20px; background: #F9FAFB; border-bottom: 1px solid #E5E7EB;">
        <form method="POST" action="/admin/rituals/<?= $ritual['id'] ?>/steps">
            <?= \App\Core\Auth::csrfField() ?>
            <div style="display: grid; grid-template-columns: 80px 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                <div class="form-group">
                    <label>Step #</label>
                    <input type="number" name="step_number" class="form-control" value="<?= count($ritual['steps'] ?? []) + 1 ?>" min="1" required>
                </div>
                <div class="form-group">
                    <label>Title *</label>
                    <input type="text" name="title" class="form-control" required placeholder="e.g., Invoke Lord Ganesha">
                </div>
                <div class="form-group">
                    <label>Duration (min)</label>
                    <input type="number" name="duration_minutes" class="form-control" value="5" min="1">
                </div>
            </div>
            <div class="form-group" style="margin-bottom: 15px;">
                <label>Description</label>
                <textarea name="description" class="form-control" rows="2" placeholder="Describe what happens in this step..."></textarea>
            </div>
            <div class="form-group" style="margin-bottom: 15px;">
                <label>Mantra</label>
                <textarea name="mantra" class="form-control" rows="2" placeholder="Enter mantra if applicable..."></textarea>
            </div>
            <label style="display: flex; align-items: center; gap: 8px; margin-bottom: 15px;">
                <input type="checkbox" name="is_optional"> Optional Step
            </label>
            <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Add Step</button>
            <button type="button" class="btn btn-sm" style="background: #E5E7EB;" onclick="document.getElementById('addStepForm').style.display = 'none'">Cancel</button>
        </form>
    </div>
    
    <?php if (!empty($ritual['steps'])): ?>
    <table class="table">
        <thead><tr><th style="width: 60px;">#</th><th>Title</th><th>Duration</th><th>Optional</th><th style="width: 140px;">Actions</th></tr></thead>
        <tbody>
            <?php foreach ($ritual['steps'] as $step): ?>
            <tr>
                <td><?= $step['step_number'] ?></td>
                <td>
                    <strong><?= htmlspecialchars($step['title']) ?></strong>
                    <?php if (!empty($step['description'])): ?>
                    <br><small style="color: #6B7280;"><?= htmlspecialchars(substr($step['description'], 0, 80)) ?><?= strlen($step['description']) > 80 ? '...' : '' ?></small>
                    <?php endif; ?>
                </td>
                <td><?= $step['duration_minutes'] ?> min</td>
                <td><?= $step['is_optional'] ? '<span class="badge badge-info">Optional</span>' : '' ?></td>
                <td>
                    <div style="display: flex; gap: 5px; align-items: center;">
                        <button type="button" class="btn btn-sm btn-info" onclick="viewStep(<?= htmlspecialchars(json_encode($step)) ?>)" title="View Step">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-warning" onclick="editStep(<?= htmlspecialchars(json_encode($step)) ?>)" title="Edit Step">
                            <i class="fas fa-edit"></i>
                        </button>
                        <form method="POST" action="/admin/ritual-steps/<?= $step['id'] ?>/delete" style="margin: 0;">
                            <?= \App\Core\Auth::csrfField() ?>
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this step?')" title="Delete Step">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
    <p style="text-align: center; color: #6B7280; padding: 30px;">No steps added yet. Click "Add Step" to add ritual steps.</p>
    <?php endif; ?>
</div>

<!-- RITUAL ITEMS SECTION -->
<div class="card" style="margin-top: 25px;">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-shopping-basket"></i> Required Items</h3>
        <button type="button" class="btn btn-sm btn-primary" onclick="document.getElementById('addItemForm').style.display = document.getElementById('addItemForm').style.display === 'none' ? 'block' : 'none'">
            <i class="fas fa-plus"></i> Add Item
        </button>
    </div>
    
    <!-- Add Item Form -->
    <div id="addItemForm" style="display: none; padding: 20px; background: #F9FAFB; border-bottom: 1px solid #E5E7EB;">
        <form method="POST" action="/admin/rituals/<?= $ritual['id'] ?>/items">
            <?= \App\Core\Auth::csrfField() ?>
            <div style="display: grid; grid-template-columns: 1fr 1fr 100px 100px; gap: 15px; margin-bottom: 15px;">
                <div class="form-group">
                    <label>Item Name *</label>
                    <input type="text" name="item_name" class="form-control" required placeholder="e.g., Cow Ghee">
                </div>
                <div class="form-group">
                    <label>Local Name</label>
                    <input type="text" name="item_name_local" class="form-control" placeholder="e.g., गाय का घी">
                </div>
                <div class="form-group">
                    <label>Quantity</label>
                    <input type="number" name="quantity" class="form-control" value="1" min="0.1" step="0.1">
                </div>
                <div class="form-group">
                    <label>Unit</label>
                    <select name="unit" class="form-control">
                        <option value="piece">Piece</option>
                        <option value="kg">Kg</option>
                        <option value="gm">Grams</option>
                        <option value="liter">Liter</option>
                        <option value="ml">ML</option>
                        <option value="packet">Packet</option>
                        <option value="bunch">Bunch</option>
                    </select>
                </div>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 150px; gap: 15px; margin-bottom: 15px;">
                <div class="form-group">
                    <label>Category</label>
                    <select name="category" class="form-control">
                        <option value="general">General</option>
                        <option value="puja">Puja Items</option>
                        <option value="flowers">Flowers</option>
                        <option value="food">Food/Prasad</option>
                        <option value="clothing">Clothing</option>
                        <option value="decoration">Decoration</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Approx. Cost (₹)</label>
                    <input type="number" name="approximate_cost" class="form-control" step="1" placeholder="100">
                </div>
            </div>
            <label style="display: flex; align-items: center; gap: 8px; margin-bottom: 15px;">
                <input type="checkbox" name="is_mandatory" checked> Mandatory Item
            </label>
            <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Add Item</button>
            <button type="button" class="btn btn-sm" style="background: #E5E7EB;" onclick="document.getElementById('addItemForm').style.display = 'none'">Cancel</button>
        </form>
    </div>
    
    <?php if (!empty($ritual['items'])): ?>
    <table class="table">
        <thead><tr><th>Item</th><th>Quantity</th><th>Category</th><th>Mandatory</th><th style="width: 140px;">Actions</th></tr></thead>
        <tbody>
            <?php foreach ($ritual['items'] as $item): ?>
            <tr>
                <td>
                    <strong><?= htmlspecialchars($item['item_name']) ?></strong>
                    <?php if (!empty($item['item_name_local'])): ?>
                    <br><small style="color: #6B7280;"><?= htmlspecialchars($item['item_name_local']) ?></small>
                    <?php endif; ?>
                </td>
                <td><?= $item['quantity'] ?> <?= $item['unit'] ?></td>
                <td><span class="badge badge-info"><?= ucfirst($item['category']) ?></span></td>
                <td><?= $item['is_mandatory'] ? '<span class="badge badge-success">Yes</span>' : '<span class="badge badge-warning">Optional</span>' ?></td>
                <td>
                    <div style="display: flex; gap: 5px; align-items: center;">
                        <button type="button" class="btn btn-sm btn-info" onclick="viewItem(<?= htmlspecialchars(json_encode($item)) ?>)" title="View Item">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-warning" onclick="editItem(<?= htmlspecialchars(json_encode($item)) ?>)" title="Edit Item">
                            <i class="fas fa-edit"></i>
                        </button>
                        <form method="POST" action="/admin/ritual-items/<?= $item['id'] ?>/delete" style="margin: 0;">
                            <?= \App\Core\Auth::csrfField() ?>
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Remove this item?')" title="Delete Item">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
    <p style="text-align: center; color: #6B7280; padding: 30px;">No items added yet. Click "Add Item" to add required items.</p>
    <?php endif; ?>
</div>

<!-- VIEW STEP MODAL -->
<div id="viewStepModal" class="modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; overflow: auto;">
    <div class="modal-content" style="background: white; margin: 5% auto; padding: 25px; border-radius: 12px; max-width: 600px; position: relative;">
        <button type="button" onclick="closeModal('viewStepModal')" style="position: absolute; right: 15px; top: 15px; background: none; border: none; font-size: 24px; cursor: pointer; color: #6B7280;">&times;</button>
        <h3 style="margin-bottom: 20px;"><i class="fas fa-list-ol"></i> Step Details</h3>
        <div id="viewStepContent"></div>
    </div>
</div>

<!-- EDIT STEP MODAL -->
<div id="editStepModal" class="modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; overflow: auto;">
    <div class="modal-content" style="background: white; margin: 5% auto; padding: 25px; border-radius: 12px; max-width: 700px; position: relative;">
        <button type="button" onclick="closeModal('editStepModal')" style="position: absolute; right: 15px; top: 15px; background: none; border: none; font-size: 24px; cursor: pointer; color: #6B7280;">&times;</button>
        <h3 style="margin-bottom: 20px;"><i class="fas fa-edit"></i> Edit Step</h3>
        <form id="editStepForm" method="POST">
            <?= \App\Core\Auth::csrfField() ?>
            <div style="display: grid; grid-template-columns: 80px 1fr 100px; gap: 15px; margin-bottom: 15px;">
                <div class="form-group">
                    <label>Step #</label>
                    <input type="number" name="step_number" id="edit_step_number" class="form-control" min="1" required>
                </div>
                <div class="form-group">
                    <label>Title *</label>
                    <input type="text" name="title" id="edit_step_title" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Duration</label>
                    <input type="number" name="duration_minutes" id="edit_step_duration" class="form-control" min="1">
                </div>
            </div>
            <div class="form-group" style="margin-bottom: 15px;">
                <label>Sanskrit Title</label>
                <input type="text" name="title_sanskrit" id="edit_step_title_sanskrit" class="form-control">
            </div>
            <div class="form-group" style="margin-bottom: 15px;">
                <label>Description</label>
                <textarea name="description" id="edit_step_description" class="form-control" rows="3"></textarea>
            </div>
            <div class="form-group" style="margin-bottom: 15px;">
                <label>Mantra</label>
                <textarea name="mantra" id="edit_step_mantra" class="form-control" rows="2"></textarea>
            </div>
            <div class="form-group" style="margin-bottom: 15px;">
                <label>Mantra Meaning</label>
                <textarea name="mantra_meaning" id="edit_step_mantra_meaning" class="form-control" rows="2"></textarea>
            </div>
            <div class="form-group" style="margin-bottom: 15px;">
                <label>Special Instructions</label>
                <textarea name="special_instructions" id="edit_step_special_instructions" class="form-control" rows="2"></textarea>
            </div>
            <label style="display: flex; align-items: center; gap: 8px; margin-bottom: 15px;">
                <input type="checkbox" name="is_optional" id="edit_step_is_optional"> Optional Step
            </label>
            <div style="display: flex; gap: 10px;">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update Step</button>
                <button type="button" class="btn" style="background: #E5E7EB;" onclick="closeModal('editStepModal')">Cancel</button>
            </div>
        </form>
    </div>
</div>

<!-- VIEW ITEM MODAL -->
<div id="viewItemModal" class="modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; overflow: auto;">
    <div class="modal-content" style="background: white; margin: 5% auto; padding: 25px; border-radius: 12px; max-width: 500px; position: relative;">
        <button type="button" onclick="closeModal('viewItemModal')" style="position: absolute; right: 15px; top: 15px; background: none; border: none; font-size: 24px; cursor: pointer; color: #6B7280;">&times;</button>
        <h3 style="margin-bottom: 20px;"><i class="fas fa-shopping-basket"></i> Item Details</h3>
        <div id="viewItemContent"></div>
    </div>
</div>

<!-- EDIT ITEM MODAL -->
<div id="editItemModal" class="modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; overflow: auto;">
    <div class="modal-content" style="background: white; margin: 5% auto; padding: 25px; border-radius: 12px; max-width: 600px; position: relative;">
        <button type="button" onclick="closeModal('editItemModal')" style="position: absolute; right: 15px; top: 15px; background: none; border: none; font-size: 24px; cursor: pointer; color: #6B7280;">&times;</button>
        <h3 style="margin-bottom: 20px;"><i class="fas fa-edit"></i> Edit Item</h3>
        <form id="editItemForm" method="POST">
            <?= \App\Core\Auth::csrfField() ?>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                <div class="form-group">
                    <label>Item Name *</label>
                    <input type="text" name="item_name" id="edit_item_name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Local Name</label>
                    <input type="text" name="item_name_local" id="edit_item_name_local" class="form-control">
                </div>
            </div>
            <div style="display: grid; grid-template-columns: 100px 100px 1fr; gap: 15px; margin-bottom: 15px;">
                <div class="form-group">
                    <label>Quantity</label>
                    <input type="number" name="quantity" id="edit_item_quantity" class="form-control" min="0.1" step="0.1">
                </div>
                <div class="form-group">
                    <label>Unit</label>
                    <select name="unit" id="edit_item_unit" class="form-control">
                        <option value="piece">Piece</option>
                        <option value="kg">Kg</option>
                        <option value="gm">Grams</option>
                        <option value="liter">Liter</option>
                        <option value="ml">ML</option>
                        <option value="packet">Packet</option>
                        <option value="bunch">Bunch</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Category</label>
                    <select name="category" id="edit_item_category" class="form-control">
                        <option value="general">General</option>
                        <option value="puja">Puja Items</option>
                        <option value="flowers">Flowers</option>
                        <option value="food">Food/Prasad</option>
                        <option value="clothing">Clothing</option>
                        <option value="decoration">Decoration</option>
                    </select>
                </div>
            </div>
            <div class="form-group" style="margin-bottom: 15px;">
                <label>Approximate Cost (₹)</label>
                <input type="number" name="approximate_cost" id="edit_item_cost" class="form-control" step="1">
            </div>
            <div class="form-group" style="margin-bottom: 15px;">
                <label>Description</label>
                <textarea name="description" id="edit_item_description" class="form-control" rows="2"></textarea>
            </div>
            <label style="display: flex; align-items: center; gap: 8px; margin-bottom: 15px;">
                <input type="checkbox" name="is_mandatory" id="edit_item_is_mandatory"> Mandatory Item
            </label>
            <div style="display: flex; gap: 10px;">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update Item</button>
                <button type="button" class="btn" style="background: #E5E7EB;" onclick="closeModal('editItemModal')">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
function closeModal(id) {
    document.getElementById(id).style.display = 'none';
}

function viewStep(step) {
    let html = `
        <table style="width: 100%; border-collapse: collapse;">
            <tr><td style="padding: 8px 0; font-weight: bold; width: 150px;">Step Number:</td><td>${step.step_number}</td></tr>
            <tr><td style="padding: 8px 0; font-weight: bold;">Title:</td><td>${step.title || '-'}</td></tr>
            <tr><td style="padding: 8px 0; font-weight: bold;">Sanskrit Title:</td><td>${step.title_sanskrit || '-'}</td></tr>
            <tr><td style="padding: 8px 0; font-weight: bold;">Duration:</td><td>${step.duration_minutes} minutes</td></tr>
            <tr><td style="padding: 8px 0; font-weight: bold;">Optional:</td><td>${step.is_optional ? 'Yes' : 'No'}</td></tr>
            <tr><td style="padding: 8px 0; font-weight: bold; vertical-align: top;">Description:</td><td>${step.description || '-'}</td></tr>
            <tr><td style="padding: 8px 0; font-weight: bold; vertical-align: top;">Mantra:</td><td style="font-style: italic;">${step.mantra || '-'}</td></tr>
            <tr><td style="padding: 8px 0; font-weight: bold; vertical-align: top;">Mantra Meaning:</td><td>${step.mantra_meaning || '-'}</td></tr>
            <tr><td style="padding: 8px 0; font-weight: bold; vertical-align: top;">Special Instructions:</td><td>${step.special_instructions || '-'}</td></tr>
        </table>
    `;
    document.getElementById('viewStepContent').innerHTML = html;
    document.getElementById('viewStepModal').style.display = 'block';
}

function editStep(step) {
    document.getElementById('editStepForm').action = '/admin/ritual-steps/' + step.id + '/update';
    document.getElementById('edit_step_number').value = step.step_number || 1;
    document.getElementById('edit_step_title').value = step.title || '';
    document.getElementById('edit_step_title_sanskrit').value = step.title_sanskrit || '';
    document.getElementById('edit_step_duration').value = step.duration_minutes || 5;
    document.getElementById('edit_step_description').value = step.description || '';
    document.getElementById('edit_step_mantra').value = step.mantra || '';
    document.getElementById('edit_step_mantra_meaning').value = step.mantra_meaning || '';
    document.getElementById('edit_step_special_instructions').value = step.special_instructions || '';
    document.getElementById('edit_step_is_optional').checked = step.is_optional == 1;
    document.getElementById('editStepModal').style.display = 'block';
}

function viewItem(item) {
    let html = `
        <table style="width: 100%; border-collapse: collapse;">
            <tr><td style="padding: 8px 0; font-weight: bold; width: 130px;">Item Name:</td><td>${item.item_name}</td></tr>
            <tr><td style="padding: 8px 0; font-weight: bold;">Local Name:</td><td>${item.item_name_local || '-'}</td></tr>
            <tr><td style="padding: 8px 0; font-weight: bold;">Quantity:</td><td>${item.quantity} ${item.unit || ''}</td></tr>
            <tr><td style="padding: 8px 0; font-weight: bold;">Category:</td><td>${item.category || '-'}</td></tr>
            <tr><td style="padding: 8px 0; font-weight: bold;">Approx. Cost:</td><td>${item.approximate_cost ? '₹' + item.approximate_cost : '-'}</td></tr>
            <tr><td style="padding: 8px 0; font-weight: bold;">Mandatory:</td><td>${item.is_mandatory ? 'Yes' : 'No'}</td></tr>
            <tr><td style="padding: 8px 0; font-weight: bold; vertical-align: top;">Description:</td><td>${item.description || '-'}</td></tr>
        </table>
    `;
    document.getElementById('viewItemContent').innerHTML = html;
    document.getElementById('viewItemModal').style.display = 'block';
}

function editItem(item) {
    document.getElementById('editItemForm').action = '/admin/ritual-items/' + item.id + '/update';
    document.getElementById('edit_item_name').value = item.item_name || '';
    document.getElementById('edit_item_name_local').value = item.item_name_local || '';
    document.getElementById('edit_item_quantity').value = item.quantity || 1;
    document.getElementById('edit_item_unit').value = item.unit || 'piece';
    document.getElementById('edit_item_category').value = item.category || 'general';
    document.getElementById('edit_item_cost').value = item.approximate_cost || '';
    document.getElementById('edit_item_description').value = item.description || '';
    document.getElementById('edit_item_is_mandatory').checked = item.is_mandatory == 1;
    document.getElementById('editItemModal').style.display = 'block';
}

// Close modal when clicking outside
window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.style.display = 'none';
    }
}
</script>
<?php endif; ?>
