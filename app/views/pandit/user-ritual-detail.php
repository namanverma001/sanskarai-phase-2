<style>
    /* ... existing styles ... */
    .ritual-detail-header {
        background: linear-gradient(135deg, #7C3AED 0%, #4C1D95 100%);
        border-radius: 24px;
        padding: 40px;
        color: white;
        margin-bottom: 35px;
        position: relative;
        box-shadow: 0 10px 25px rgba(124, 58, 237, 0.2);
    }
    /* Rest of valid styles... */

    .step-number {
        width: 44px;
        height: 44px;
        background: #7C3AED;
        color: white;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        flex-shrink: 0;
        box-shadow: 0 4px 6px rgba(124, 58, 237, 0.3);
    }

    .step-card {
        display: flex;
        gap: 20px;
        padding: 25px;
        background: white;
        border-radius: 16px;
        margin-bottom: 20px;
        border: 1px solid #E5E7EB;
        box-shadow: 0 4px 6px rgba(0,0,0,0.02);
        transition: all 0.3s ease;
    }

    .step-card:hover {
        border-color: #8B5CF6;
        box-shadow: 0 10px 15px rgba(139, 92, 246, 0.1);
        transform: translateY(-2px);
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
    href="/pandit/assignments"
    class="btn btn-sm"
    style="background: #E5E7EB; color: #374151; margin-bottom: 20px;"
>
    <i class="fas fa-arrow-left"></i> Back to Assignments
</a>

<div class="ritual-detail-header">
    <div style="display: flex; justify-content: space-between; align-items: flex-start;">
        <div>
            <span style="background: rgba(255,255,255,0.2); padding: 5px 12px; border-radius: 20px; font-size: 0.8rem; margin-bottom: 10px; display: inline-block;">
                <i class="fas fa-user-edit"></i> Managing Client Ritual
            </span>
            <h1 style="margin-top: 5px;">
                <?= htmlspecialchars($ritual['name']) ?>
            </h1>
        </div>
        <div style="text-align: right; background: rgba(0,0,0,0.2); padding: 15px 25px; border-radius: 16px; backdrop-filter: blur(10px);">
            <small style="opacity: 0.8; display: block; margin-bottom: 5px;">Client</small>
            <strong style="font-size: 1.2rem;"><?= htmlspecialchars($assignment['user_name'] ?? 'User') ?></strong>
        </div>
    </div>
</div>

<div class="detail-grid">
    <div class="steps-section">
        <h3>
            <span><i class="fas fa-list-ol"></i> Ritual Steps (<?= count($ritual['steps'] ?? []) ?>)</span>
            <button
                class="btn btn-sm btn-primary"
                onclick="showAddStepModal()"
            >
                <i class="fas fa-plus"></i> Add Step for Client
            </button>
        </h3>

        <?php if (empty($ritual['steps'])): ?>
            <p style="text-align: center; color: #6B7280; padding: 30px;">
                No steps defined yet.
            </p>
        <?php else: ?>
            <?php 
            $lastStepNumber = 0;
            ?>
            <?php foreach ($ritual['steps'] as $index => $step): ?>
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
                    <div class="step-number" style="background: #7C3AED;">
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
                                <i class="fas fa-om"></i>
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
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div>
        <div class="sidebar-card">
            <h4><i class="fas fa-info-circle"></i> Info</h4>
             <!-- Standard Info Block -->
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
            
            <!-- Other fields same as User View -->
             <div class="form-group">
                <label>Description</label>
                <textarea id="stepDescription" name="description" class="form-control" rows="3"></textarea>
            </div>
            <div class="form-group">
                <label>Mantra</label>
                <input type="text" id="stepMantra" name="mantra" class="form-control">
            </div>
            <div class="form-group">
                <label>Mantra Meaning</label>
                <input type="text" id="stepMantraMeaning" name="mantra_meaning" class="form-control">
            </div>
             <div class="form-group">
                <label>Duration</label>
                <input type="number" id="stepDuration" name="duration_minutes" class="form-control" value="5">
            </div>
             <div class="form-group">
                <label>Special Instructions</label>
                <textarea id="stepInstructions" name="special_instructions" class="form-control"></textarea>
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
                    style="flex: 1; background: #7C3AED;"
                >Save</button>
            </div>
        </form>
    </div>
</div>

<script>
    const csrfToken = '<?= \App\Core\Auth::csrfToken() ?>';
    const assignmentId = <?= $assignment['id'] ?>;
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
        document.getElementById('stepNumber').value = '';
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
        // DIFFERENT ROUTES FOR PANDIT
        const url = isAddMode
            ? `/pandit/assignments/${assignmentId}/ritual/steps`
            : `/pandit/client-ritual-steps/${document.getElementById('stepId').value}/update`;

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
            const response = await fetch(`/pandit/client-ritual-steps/${stepId}/delete`, {
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
</script>