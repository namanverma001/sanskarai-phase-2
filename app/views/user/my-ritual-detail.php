<style>
    .ritual-detail-header {
        background: linear-gradient(135deg, #10B981 0%, #059669 100%);
        border-radius: 20px;
        padding: 35px;
        color: white;
        margin-bottom: 30px;
        position: relative;
    }

    .ritual-detail-header h1 {
        font-size: 1.8rem;
        margin-bottom: 5px;
    }

    .ritual-detail-header .sanskrit {
        opacity: 0.8;
        font-style: italic;
    }

    .ritual-detail-header .ai-badge {
        display: inline-block;
        background: rgba(139, 92, 246, 0.9);
        padding: 4px 12px;
        border-radius: 15px;
        font-size: 0.75rem;
        margin-top: 10px;
    }

    .detail-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 25px;
    }

    @media (max-width: 1024px) {
        .detail-grid {
            grid-template-columns: 1fr;
        }
    }

    .steps-section {
        background: white;
        border-radius: 16px;
        padding: 25px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
    }

    .steps-section h3 {
        margin-bottom: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .step-card {
        display: flex;
        gap: 15px;
        padding: 20px;
        background: #F9FAFB;
        border-radius: 12px;
        margin-bottom: 15px;
        border: 2px solid transparent;
        transition: all 0.3s;
    }

    .step-card:hover {
        border-color: var(--primary);
    }

    .step-card.editing {
        background: #FEF3C7;
        border-color: var(--accent);
    }

    .step-number {
        width: 40px;
        height: 40px;
        background: var(--primary);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        flex-shrink: 0;
    }

    .step-content {
        flex: 1;
    }

    .step-content h5 {
        font-size: 1rem;
        margin-bottom: 5px;
    }

    .step-content p {
        color: #6B7280;
        font-size: 0.9rem;
        line-height: 1.5;
    }

    .step-mantra {
        background: #FEF3C7;
        padding: 10px;
        border-radius: 6px;
        margin-top: 10px;
        font-style: italic;
        color: #92400E;
    }

    .step-actions {
        display: flex;
        gap: 8px;
        margin-top: 10px;
    }

    .step-actions button {
        padding: 5px 12px;
        border: none;
        border-radius: 5px;
        font-size: 0.8rem;
        cursor: pointer;
    }

    .btn-edit-step {
        background: #E0E7FF;
        color: #3730A3;
    }

    .btn-delete-step {
        background: #FEE2E2;
        color: #991B1B;
    }

    .sidebar-card {
        background: white;
        border-radius: 16px;
        padding: 25px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        margin-bottom: 25px;
    }

    .sidebar-card h4 {
        margin-bottom: 15px;
        color: var(--dark);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .sidebar-card h4 i {
        color: var(--primary);
    }

    .info-row {
        display: flex;
        justify-content: space-between;
        padding: 10px 0;
        border-bottom: 1px solid #E5E7EB;
        font-size: 0.9rem;
    }

    .info-row:last-child {
        border-bottom: none;
    }

    .info-label {
        color: #6B7280;
    }

    .info-value {
        font-weight: 500;
        color: var(--dark);
    }

    .item-list {
        list-style: none;
        padding: 0;
    }

    .item-list li {
        padding: 10px 0;
        border-bottom: 1px solid #E5E7EB;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 10px;
    }

    .item-list li:last-child {
        border-bottom: none;
    }

    .item-name {
        font-weight: 500;
        display: block;
        margin-bottom: 3px;
    }

    .item-name small {
        display: block;
        color: #6B7280;
        font-weight: normal;
        font-size: 0.8rem;
    }

    .item-qty {
        color: #6B7280;
        font-size: 0.85rem;
        display: block;
        margin-top: 2px;
    }

    .action-buttons {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .action-buttons .btn {
        width: 100%;
        text-align: center;
        padding: 14px;
        border-radius: 10px;
        font-weight: 600;
    }

    .btn-start-ritual {
        background: linear-gradient(135deg, var(--primary) 0%, #FF8C42 100%);
        color: white;
    }

    /* Edit Modal */
    .modal-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1000;
        justify-content: center;
        align-items: center;
    }

    .modal-overlay.active {
        display: flex;
    }

    .modal-content {
        background: white;
        border-radius: 16px;
        padding: 30px;
        max-width: 500px;
        width: 90%;
        max-height: 80vh;
        overflow-y: auto;
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .modal-close {
        background: none;
        border: none;
        font-size: 1.5rem;
        cursor: pointer;
        color: #6B7280;
    }
</style>

<a
    href="/user/my-rituals"
    class="btn btn-sm"
    style="background: #E5E7EB; color: #374151; margin-bottom: 20px;"
>
    <i class="fas fa-arrow-left"></i> Back to My Rituals
</a>

<div class="ritual-detail-header">
    <h1>
        <?= htmlspecialchars($ritual['name']) ?>
    </h1>
    <?php if ($ritual['name_sanskrit']): ?>
        <p class="sanskrit">
            <?= htmlspecialchars($ritual['name_sanskrit']) ?>
        </p>
    <?php endif; ?>
    <?php if ($ritual['is_ai_generated']): ?>
        <span class="ai-badge"><i class="fas fa-robot"></i> AI Generated</span>
    <?php endif; ?>
</div>

<div class="detail-grid">
    <div class="steps-section">
        <h3>
            <span><i class="fas fa-list-ol"></i> Ritual Steps (
                <?= count($ritual['steps'] ?? []) ?>)
            </span>
            <button
                class="btn btn-sm btn-primary"
                onclick="showAddStepModal()"
            >
                <i class="fas fa-plus"></i> Add Step
            </button>
        </h3>

        <?php if (empty($ritual['steps'])): ?>
            <p style="text-align: center; color: #6B7280; padding: 30px;">
                No steps defined. Click "Add Step" to create your first step.
            </p>
        <?php else: ?>
            <?php 
            $lastStepNumber = 0;
            ?>
            <?php foreach ($ritual['steps'] as $index => $step): ?>
                <?php if ($index > 0 && $step['step_number'] > $lastStepNumber + 1): ?>
                    <!-- Gap in numbering logic handling if needed -->
                <?php endif; ?>
                
                <?php if ($index > 0): ?>
                     <div class="step-insert-divider" style="text-align: center; margin: 10px 0; opacity: 0; transition: opacity 0.3s;" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0'">
                        <button class="btn btn-sm btn-outline-primary" style="font-size: 0.8rem; border-radius: 20px; background: white;" onclick="showAddStepModal(<?= $step['step_number'] ?>)">
                            <i class="fas fa-plus"></i> Insert Step Here
                        </button>
                    </div>
                <?php endif; ?>

                <div
                    class="step-card"
                    id="step-<?= $step['id'] ?>"
                >
                    <div class="step-number">
                        <?= $step['step_number'] ?>
                    </div>
                    <div class="step-content">
                        <h5>
                            <?= htmlspecialchars($step['title']) ?>
                        </h5>
                        <?php if ($step['title_sanskrit']): ?>
                            <small style="color: #92400E; font-style: italic;">
                                <?= htmlspecialchars($step['title_sanskrit']) ?>
                            </small>
                        <?php endif; ?>
                        <?php if ($step['description']): ?>
                            <p>
                                <?= nl2br(htmlspecialchars($step['description'])) ?>
                            </p>
                        <?php endif; ?>
                        <?php if ($step['mantra']): ?>
                            <div class="step-mantra">
                                <strong>Mantra:</strong>
                                <?= htmlspecialchars($step['mantra']) ?>
                                <?php if ($step['mantra_meaning']): ?>
                                    <br><small>
                                        <?= htmlspecialchars($step['mantra_meaning']) ?>
                                    </small>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                        <?php if ($step['special_instructions']): ?>
                            <p style="margin-top: 10px; font-size: 0.85rem; color: #059669;">
                                <i class="fas fa-info-circle"></i>
                                <?= htmlspecialchars($step['special_instructions']) ?>
                            </p>
                        <?php endif; ?>
                        <div class="step-actions">
                            <button
                                class="btn-edit-step"
                                onclick="editStep(<?= $step['id'] ?>, <?= htmlspecialchars(json_encode($step)) ?>)"
                            >
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <button
                                class="btn-delete-step"
                                onclick="deleteStep(<?= $step['id'] ?>)"
                            >
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </div>
                    </div>
                </div>
                <?php $lastStepNumber = $step['step_number']; ?>
            <?php endforeach; ?>
            
            <!-- Allow inserting after the last step explicitly via the main Add button or here if preferred, but main button covers end append -->
        <?php endif; ?>
    </div>

    <div>
        <div class="sidebar-card">
            <h4><i class="fas fa-info-circle"></i> Ritual Info</h4>
            <div class="info-row">
                <span class="info-label">Duration</span>
                <span class="info-value">
                    <?= $ritual['duration_minutes'] ?> minutes
                </span>
            </div>
            <div class="info-row">
                <span class="info-label">Difficulty</span>
                <span class="info-value">
                    <?= ucfirst($ritual['difficulty']) ?>
                </span>
            </div>
            <?php if ($ritual['category']): ?>
                <div class="info-row">
                    <span class="info-label">Category</span>
                    <span class="info-value">
                        <?= htmlspecialchars($ritual['category']) ?>
                    </span>
                </div>
            <?php endif; ?>
            <?php if ($ritual['deity']): ?>
                <div class="info-row">
                    <span class="info-label">Deity</span>
                    <span class="info-value">
                        <?= htmlspecialchars($ritual['deity']) ?>
                    </span>
                </div>
            <?php endif; ?>
            <?php if ($ritual['best_time']): ?>
                <div class="info-row">
                    <span class="info-label">Best Time</span>
                    <span class="info-value">
                        <?= htmlspecialchars($ritual['best_time']) ?>
                    </span>
                </div>
            <?php endif; ?>
            <?php if ($ritual['community_name']): ?>
                <div class="info-row">
                    <span class="info-label">Community</span>
                    <span class="info-value">
                        <?= htmlspecialchars($ritual['community_name']) ?>
                    </span>
                </div>
            <?php endif; ?>
        </div>

        <?php if (!empty($ritual['items'])): ?>
            <div class="sidebar-card">
                <h4 style="display: flex; justify-content: space-between; align-items: center;">
                    <span><i class="fas fa-shopping-basket"></i> Required Items</span>
                    <button
                        class="btn btn-sm btn-primary"
                        onclick="showAddItemModal()"
                        style="font-size: 0.75rem; padding: 4px 8px;"
                    >
                        <i class="fas fa-plus"></i> Add
                    </button>
                </h4>
                <ul class="item-list">
                    <?php foreach ($ritual['items'] as $item): ?>
                        <li id="item-<?= $item['id'] ?>">
                            <div style="flex: 1;">
                                <span class="item-name">
                                    <?= htmlspecialchars($item['item_name']) ?>
                                    <?php if ($item['item_name_local']): ?>
                                        <small>
                                            <?= htmlspecialchars($item['item_name_local']) ?>
                                        </small>
                                    <?php endif; ?>
                                </span>
                                <span class="item-qty">
                                    <?= $item['quantity'] ?>
                                    <?= $item['unit'] ?>
                                    <?php if ($item['is_mandatory']): ?>
                                        <span
                                            class="badge badge-danger"
                                            style="font-size: 0.6rem;"
                                        >Required</span>
                                    <?php endif; ?>
                                </span>
                            </div>
                            <div style="display: flex; gap: 5px; align-items: center;">
                                <button
                                    class="btn btn-sm edit-item-btn"
                                    data-item-id="<?= $item['id'] ?>"
                                    data-item-name="<?= htmlspecialchars($item['item_name']) ?>"
                                    data-item-name-local="<?= htmlspecialchars($item['item_name_local'] ?? '') ?>"
                                    data-item-quantity="<?= $item['quantity'] ?>"
                                    data-item-unit="<?= $item['unit'] ?>"
                                    data-item-mandatory="<?= $item['is_mandatory'] ?>"
                                    data-item-description="<?= htmlspecialchars($item['description'] ?? '') ?>"
                                    data-item-alternatives="<?= htmlspecialchars($item['alternatives'] ?? '') ?>"
                                    style="font-size: 0.7rem; padding: 2px 6px; background: #DBEAFE; color: #1E40AF;"
                                    title="Edit"
                                >
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button
                                    onclick="deleteItem(<?= $item['id'] ?>)"
                                    class="btn btn-sm"
                                    style="font-size: 0.7rem; padding: 2px 6px; background: #FEE2E2; color: #991B1B;"
                                    title="Delete"
                                >
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php else: ?>
            <div class="sidebar-card">
                <h4 style="display: flex; justify-content: space-between; align-items: center;">
                    <span><i class="fas fa-shopping-basket"></i> Required Items</span>
                    <button
                        class="btn btn-sm btn-primary"
                        onclick="showAddItemModal()"
                        style="font-size: 0.75rem; padding: 4px 8px;"
                    >
                        <i class="fas fa-plus"></i> Add
                    </button>
                </h4>
                <p style="text-align: center; color: #6B7280; padding: 20px; font-size: 0.9rem;">
                    No items added yet. Click "Add" to add items.
                </p>
            </div>
        <?php endif; ?>

        <div class="sidebar-card">
            <div class="action-buttons">
                <a
                    href="/user/my-rituals/<?= $ritual['id'] ?>/start"
                    class="btn btn-start-ritual"
                >
                    <i class="fas fa-play-circle"></i> Start This Ritual
                </a>
                <a
                    href="/user/shopping-list/generate/<?= $ritual['id'] ?>"
                    class="btn btn-success"
                >
                    <i class="fas fa-cart-plus"></i> Add Items to Shopping List
                </a>
                
                <form 
                    action="/user/my-rituals/<?= $ritual['id'] ?>/delete" 
                    method="POST" 
                    onsubmit="return confirm('Are you sure you want to delete this ritual? This cannot be undone.');"
                    style="margin-top: 10px;"
                >
                    <?= \App\Core\Auth::csrfField() ?>
                    <button type="submit" class="btn btn-danger" style="background: #FECACA; color: #991B1B; width: 100%;">
                        <i class="fas fa-trash-alt"></i> Delete Ritual
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Step Modal -->
<div
    class="modal-overlay"
    id="editModal"
>
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modalTitle">Edit Step</h3>
            <button
                class="modal-close"
                onclick="closeModal()"
            >&times;</button>
        </div>
        <form id="stepForm">
            <?= \App\Core\Auth::csrfField() ?>
            <input
                type="hidden"
                id="stepId"
                name="step_id"
            >
            <input
                type="hidden"
                id="stepNumber"
                name="step_number"
            >

            <div class="form-group">
                <label>Title *</label>
                <input
                    type="text"
                    id="stepTitle"
                    name="title"
                    class="form-control"
                    required
                >
            </div>

            <div class="form-group">
                <label>Description</label>
                <textarea
                    id="stepDescription"
                    name="description"
                    class="form-control"
                    rows="3"
                ></textarea>
            </div>

            <div class="form-group">
                <label>Mantra</label>
                <input
                    type="text"
                    id="stepMantra"
                    name="mantra"
                    class="form-control"
                >
            </div>

            <div class="form-group">
                <label>Mantra Meaning</label>
                <input
                    type="text"
                    id="stepMantraMeaning"
                    name="mantra_meaning"
                    class="form-control"
                >
            </div>

            <div class="form-group">
                <label>Duration (minutes)</label>
                <input
                    type="number"
                    id="stepDuration"
                    name="duration_minutes"
                    class="form-control"
                    value="5"
                    min="1"
                >
            </div>

            <div class="form-group">
                <label>Special Instructions</label>
                <textarea
                    id="stepInstructions"
                    name="special_instructions"
                    class="form-control"
                    rows="2"
                ></textarea>
            </div>

            <div style="display: flex; gap: 10px; margin-top: 20px;">
                <button
                    type="button"
                    class="btn"
                    style="background: #E5E7EB; color: var(--dark);"
                    onclick="closeModal()"
                >Cancel</button>
                <button
                    type="submit"
                    class="btn btn-primary"
                    style="flex: 1;"
                >Save</button>
            </div>
        </form>
    </div>
</div>

<!-- Item Modal -->
<div
    class="modal-overlay"
    id="itemModal"
>
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="itemModalTitle">Add Item</h3>
            <button
                class="modal-close"
                onclick="closeItemModal()"
            >&times;</button>
        </div>
        <form id="itemForm">
            <?= \App\Core\Auth::csrfField() ?>
            <input
                type="hidden"
                id="itemId"
                name="item_id"
            >

            <div class="form-group">
                <label>Item Name *</label>
                <input
                    type="text"
                    id="itemName"
                    name="item_name"
                    class="form-control"
                    required
                    placeholder="e.g., Rice, Flowers, Ghee"
                >
            </div>

            <div class="form-group">
                <label>Local Name (Optional)</label>
                <input
                    type="text"
                    id="itemNameLocal"
                    name="item_name_local"
                    class="form-control"
                    placeholder="e.g., चावल, फूल, घी"
                >
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div class="form-group">
                    <label>Quantity *</label>
                    <input
                        type="text"
                        id="itemQuantity"
                        name="quantity"
                        class="form-control"
                        required
                        value="1"
                        placeholder="e.g., 1, 500, 2.5"
                    >
                </div>

                <div class="form-group">
                    <label>Unit *</label>
                    <select
                        id="itemUnit"
                        name="unit"
                        class="form-control"
                    >
                        <option value="piece">Piece</option>
                        <option value="kg">Kg</option>
                        <option value="gram">Gram</option>
                        <option value="liter">Liter</option>
                        <option value="ml">ML</option>
                        <option value="cup">Cup</option>
                        <option value="spoon">Spoon</option>
                        <option value="bunch">Bunch</option>
                        <option value="handful">Handful</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                    <input
                        type="checkbox"
                        id="itemMandatory"
                        name="is_mandatory"
                        value="1"
                        checked
                    >
                    <span>This item is mandatory/required</span>
                </label>
            </div>

            <div class="form-group">
                <label>Description (Optional)</label>
                <textarea
                    id="itemDescription"
                    name="description"
                    class="form-control"
                    rows="2"
                    placeholder="Additional details about the item..."
                ></textarea>
            </div>

            <div class="form-group">
                <label>Alternatives (Optional)</label>
                <input
                    type="text"
                    id="itemAlternatives"
                    name="alternatives"
                    class="form-control"
                    placeholder="e.g., Can use coconut oil instead"
                >
            </div>

            <div style="display: flex; gap: 10px; margin-top: 20px;">
                <button
                    type="button"
                    class="btn"
                    style="background: #E5E7EB; color: var(--dark);"
                    onclick="closeItemModal()"
                >Cancel</button>
                <button
                    type="submit"
                    class="btn btn-primary"
                    style="flex: 1;"
                >Save Item</button>
            </div>
        </form>
    </div>
</div>

<script>
    const csrfToken = '<?= \App\Core\Auth::csrfToken() ?>';
    const ritualId = <?= $ritual['id'] ?>;
    let isAddMode = false;

    function showAddStepModal(insertAt = 0) {
        isAddMode = true;
        document.getElementById('modalTitle').textContent = insertAt > 0 ? 'Insert New Step (before Step ' + insertAt + ')' : 'Add New Step';
        document.getElementById('stepId').value = '';
        document.getElementById('stepNumber').value = insertAt;
        document.getElementById('stepTitle').value = '';
        document.getElementById('stepDescription').value = '';
        document.getElementById('stepMantra').value = '';
        document.getElementById('stepMantraMeaning').value = '';
        document.getElementById('stepDuration').value = '5';
        document.getElementById('stepInstructions').value = '';
        document.getElementById('editModal').classList.add('active');
    }

    function editStep(stepId, stepData) {
        isAddMode = false;
        document.getElementById('modalTitle').textContent = 'Edit Step';
        document.getElementById('stepId').value = stepId;
        document.getElementById('stepNumber').value = ''; // Not relevant for update
        document.getElementById('stepTitle').value = stepData.title || '';
        document.getElementById('stepDescription').value = stepData.description || '';
        document.getElementById('stepMantra').value = stepData.mantra || '';
        document.getElementById('stepMantraMeaning').value = stepData.mantra_meaning || '';
        document.getElementById('stepDuration').value = stepData.duration_minutes || 5;
        document.getElementById('stepInstructions').value = stepData.special_instructions || '';
        document.getElementById('editModal').classList.add('active');
    }

    function closeModal() {
        document.getElementById('editModal').classList.remove('active');
    }

    document.getElementById('stepForm').addEventListener('submit', async function (e) {
        e.preventDefault();

        const formData = new FormData(this);
        const url = isAddMode
            ? `/user/my-rituals/${ritualId}/steps`
            : `/user/my-rituals/steps/${document.getElementById('stepId').value}`;

        try {
            const response = await fetch(url, {
                method: 'POST',
                body: formData
            });
            const data = await response.json();

            if (data.success) {
                location.reload();
            } else {
                alert(data.error || 'Failed to save step');
            }
        } catch (error) {
            alert('Error: ' + error.message);
        }
    });

    async function deleteStep(stepId) {
        if (!confirm('Are you sure you want to delete this step?')) {
            return;
        }

        const formData = new FormData();
        formData.append('csrf_token', csrfToken);

        try {
            const response = await fetch(`/user/my-rituals/steps/${stepId}/delete`, {
                method: 'POST',
                body: formData
            });
            const data = await response.json();

            if (data.success) {
                document.getElementById('step-' + stepId).remove();
            } else {
                alert(data.error || 'Failed to delete step');
            }
        } catch (error) {
            alert('Error: ' + error.message);
        }
    }

    // ============================================================
    // ITEM MANAGEMENT FUNCTIONS
    // ============================================================
    let isAddItemMode = false;

    function showAddItemModal() {
        isAddItemMode = true;
        document.getElementById('itemModalTitle').textContent = 'Add New Item';
        document.getElementById('itemId').value = '';
        document.getElementById('itemName').value = '';
        document.getElementById('itemNameLocal').value = '';
        document.getElementById('itemQuantity').value = '1';
        document.getElementById('itemUnit').value = 'piece';
        document.getElementById('itemMandatory').checked = true;
        document.getElementById('itemDescription').value = '';
        document.getElementById('itemAlternatives').value = '';
        document.getElementById('itemModal').classList.add('active');
    }

    function editItem(itemId, itemData) {
        isAddItemMode = false;
        document.getElementById('itemModalTitle').textContent = 'Edit Item';
        document.getElementById('itemId').value = itemId;
        document.getElementById('itemName').value = itemData.item_name || '';
        document.getElementById('itemNameLocal').value = itemData.item_name_local || '';
        document.getElementById('itemQuantity').value = itemData.quantity || '1';
        document.getElementById('itemUnit').value = itemData.unit || 'piece';
        document.getElementById('itemMandatory').checked = itemData.is_mandatory == 1;
        document.getElementById('itemDescription').value = itemData.description || '';
        document.getElementById('itemAlternatives').value = itemData.alternatives || '';
        document.getElementById('itemModal').classList.add('active');
    }

    // Event listener for edit buttons
    document.addEventListener('click', function(e) {
        if (e.target.closest('.edit-item-btn')) {
            const btn = e.target.closest('.edit-item-btn');
            const itemData = {
                item_name: btn.dataset.itemName,
                item_name_local: btn.dataset.itemNameLocal,
                quantity: btn.dataset.itemQuantity,
                unit: btn.dataset.itemUnit,
                is_mandatory: btn.dataset.itemMandatory,
                description: btn.dataset.itemDescription,
                alternatives: btn.dataset.itemAlternatives
            };
            editItem(btn.dataset.itemId, itemData);
        }
    });

    function closeItemModal() {
        document.getElementById('itemModal').classList.remove('active');
    }

    document.getElementById('itemForm').addEventListener('submit', async function (e) {
        e.preventDefault();

        const formData = new FormData(this);
        const url = isAddItemMode
            ? `/user/my-rituals/${ritualId}/items`
            : `/user/my-rituals/items/${document.getElementById('itemId').value}`;

        try {
            const response = await fetch(url, {
                method: 'POST',
                body: formData
            });
            const data = await response.json();

            if (data.success) {
                location.reload();
            } else {
                alert(data.error || 'Failed to save item');
            }
        } catch (error) {
            alert('Error: ' + error.message);
        }
    });

    async function deleteItem(itemId) {
        if (!confirm('Are you sure you want to delete this item?')) {
            return;
        }

        const formData = new FormData();
        formData.append('csrf_token', csrfToken);

        try {
            const response = await fetch(`/user/my-rituals/items/${itemId}/delete`, {
                method: 'POST',
                body: formData
            });
            const data = await response.json();

            if (data.success) {
                const itemElement = document.getElementById('item-' + itemId);
                if (itemElement) {
                    itemElement.remove();
                }
            } else {
                alert(data.error || 'Failed to delete item');
            }
        } catch (error) {
            alert('Error: ' + error.message);
        }
    }

    // Close modals when clicking outside
    document.getElementById('editModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal();
        }
    });

    document.getElementById('itemModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeItemModal();
        }
    });
</script>