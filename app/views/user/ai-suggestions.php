<style>
    .query-card {
        border: 1px solid #E5E7EB;
        border-radius: 14px;
        padding: 20px;
        margin-bottom: 15px;
        transition: all 0.3s;
        background: white;
    }

    .query-card:hover {
        border-color: #C4B5FD;
        box-shadow: 0 4px 15px rgba(99, 102, 241, 0.08);
    }

    .query-type-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        background: linear-gradient(135deg, #EEF2FF, #E0E7FF);
        color: #4338CA;
    }

    .query-prompt {
        color: #4B5563;
        font-size: 0.9rem;
        margin: 10px 0;
        line-height: 1.5;
    }

    .ritual-preview-card {
        background: linear-gradient(135deg, #F0FDF4 0%, #ECFDF5 100%);
        border: 1px solid #BBF7D0;
        border-radius: 12px;
        padding: 16px;
        margin-top: 12px;
    }

    .ritual-preview-card h5 {
        color: #065F46;
        font-size: 1rem;
        margin-bottom: 4px;
    }

    .ritual-preview-card .sanskrit-text {
        color: #059669;
        font-size: 0.85rem;
        font-style: italic;
    }

    .ritual-preview-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 10px;
    }

    .ritual-preview-meta span {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 3px 10px;
        border-radius: 15px;
        font-size: 0.75rem;
        font-weight: 500;
        background: white;
        color: #374151;
        border: 1px solid #D1D5DB;
    }

    .ritual-preview-desc {
        color: #4B5563;
        font-size: 0.85rem;
        margin-top: 10px;
        line-height: 1.6;
    }

    .btn-view-detail {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 16px;
        border-radius: 8px;
        font-size: 0.85rem;
        font-weight: 600;
        background: linear-gradient(135deg, #6366F1 0%, #4F46E5 100%);
        color: white;
        border: none;
        cursor: pointer;
        transition: all 0.3s;
        text-decoration: none;
        margin-top: 12px;
    }

    .btn-view-detail:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
    }

    /* Modal Styles */
    .query-modal-overlay {
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
        padding: 20px;
    }

    .query-modal-overlay.active {
        display: flex;
    }

    .query-modal {
        background: white;
        border-radius: 20px;
        max-width: 700px;
        width: 100%;
        max-height: 85vh;
        overflow-y: auto;
        box-shadow: 0 25px 60px rgba(0, 0, 0, 0.2);
        animation: modalSlideIn 0.3s ease;
    }

    @keyframes modalSlideIn {
        from { transform: translateY(30px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }

    .query-modal-header {
        padding: 24px 24px 16px;
        border-bottom: 1px solid #E5E7EB;
        display: flex;
        justify-content: space-between;
        align-items: center;
        position: sticky;
        top: 0;
        background: white;
        border-radius: 20px 20px 0 0;
        z-index: 1;
    }

    .query-modal-header h3 {
        font-size: 1.15rem;
        color: var(--dark);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .query-modal-close {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        border: none;
        background: #F3F4F6;
        color: #6B7280;
        font-size: 1.1rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
    }

    .query-modal-close:hover {
        background: #E5E7EB;
        color: #1F2937;
    }

    .query-modal-body {
        padding: 24px;
    }

    .modal-section {
        margin-bottom: 20px;
    }

    .modal-section-label {
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #6B7280;
        margin-bottom: 8px;
    }

    .modal-prompt-text {
        background: #F9FAFB;
        border: 1px solid #E5E7EB;
        border-radius: 10px;
        padding: 14px;
        color: #1F2937;
        font-size: 0.9rem;
        line-height: 1.6;
        white-space: pre-wrap;
    }
</style>

<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-robot"></i> AI Ritual Suggestions</h3>
    </div>
    
    <div style="background: linear-gradient(135deg, #FF6B35 0%, #F59E0B 100%); color: white; padding: 30px; border-radius: 12px; margin-bottom: 25px;">
        <h3 style="margin-bottom: 15px;">Get Personalized Recommendations</h3>
        <p style="margin-bottom: 20px; opacity: 0.9;">Tell us about your occasion and get AI-powered ritual suggestions tailored for you.</p>
        
        <form id="aiForm" method="POST" action="/user/ai-suggestions">
            <?= \App\Core\Auth::csrfField() ?>
            <div style="display: flex; gap: 15px;">
                <input type="text" name="occasion" class="form-control" 
                       placeholder="e.g., New home, Marriage, Baby naming, Festival..." 
                       style="flex: 1; border: none;">
                <button type="submit" class="btn" style="background: white; color: #FF6B35;">
                    <i class="fas fa-magic"></i> Get Suggestions
                </button>
            </div>
        </form>
    </div>
    
    <div id="aiResult"></div>
    
    <?php if (!empty($history)): ?>
    <h4 style="margin-bottom: 15px;"><i class="fas fa-history"></i> Recent Queries</h4>
    <?php foreach (array_slice($history, 0, 5) as $idx => $req): ?>
    <?php
        // Try to parse JSON response for display
        $parsedResponse = null;
        $ritualName = '';
        $ritualSanskrit = '';
        $ritualCategory = '';
        $ritualDuration = '';
        $ritualDesc = '';
        if ($req['response']) {
            $parsedResponse = json_decode($req['response'], true);
            if (is_array($parsedResponse)) {
                $ritualName = $parsedResponse['name'] ?? '';
                $ritualSanskrit = $parsedResponse['name_sanskrit'] ?? '';
                $ritualCategory = $parsedResponse['category'] ?? '';
                $ritualDuration = $parsedResponse['duration_minutes'] ?? '';
                $ritualDesc = $parsedResponse['description'] ?? '';
            }
        }
        $isJson = is_array($parsedResponse) && !empty($ritualName);
        
        // Determine type label
        $typeLabels = [
            'ritual_generation' => ['icon' => 'fa-magic', 'label' => 'Ritual Generation'],
            'ritual_regeneration' => ['icon' => 'fa-sync-alt', 'label' => 'Ritual Regeneration'],
            'suggestion' => ['icon' => 'fa-lightbulb', 'label' => 'AI Suggestion'],
            'chat' => ['icon' => 'fa-comments', 'label' => 'AI Chat'],
        ];
        $typeInfo = $typeLabels[$req['request_type']] ?? ['icon' => 'fa-robot', 'label' => ucwords(str_replace('_', ' ', $req['request_type']))];
    ?>
    <div class="query-card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
            <span class="query-type-badge">
                <i class="fas <?= $typeInfo['icon'] ?>"></i>
                <?= $typeInfo['label'] ?>
            </span>
            <span style="color: #9CA3AF; font-size: 0.8rem;">
                <i class="fas fa-clock"></i> <?= date('M d, H:i', strtotime($req['created_at'])) ?>
            </span>
        </div>

        <p class="query-prompt">
            <?= htmlspecialchars(substr($req['prompt'], 0, 120)) ?><?= strlen($req['prompt']) > 120 ? '...' : '' ?>
        </p>

        <?php if ($isJson): ?>
            <!-- Parsed Ritual Preview -->
            <div class="ritual-preview-card">
                <h5><i class="fas fa-om" style="color: #10B981;"></i> <?= htmlspecialchars($ritualName) ?></h5>
                <?php if ($ritualSanskrit): ?>
                    <span class="sanskrit-text"><?= htmlspecialchars($ritualSanskrit) ?></span>
                <?php endif; ?>
                <div class="ritual-preview-meta">
                    <?php if ($ritualCategory): ?>
                        <span><i class="fas fa-tag"></i> <?= htmlspecialchars($ritualCategory) ?></span>
                    <?php endif; ?>
                    <?php if ($ritualDuration): ?>
                        <span><i class="fas fa-clock"></i> <?= htmlspecialchars($ritualDuration) ?> min</span>
                    <?php endif; ?>
                    <?php if (!empty($parsedResponse['difficulty'])): ?>
                        <span><i class="fas fa-signal"></i> <?= ucfirst($parsedResponse['difficulty']) ?></span>
                    <?php endif; ?>
                    <?php if (!empty($parsedResponse['deity'])): ?>
                        <span><i class="fas fa-pray"></i> <?= htmlspecialchars($parsedResponse['deity']) ?></span>
                    <?php endif; ?>
                </div>
                <?php if ($ritualDesc): ?>
                    <p class="ritual-preview-desc"><?= htmlspecialchars(substr($ritualDesc, 0, 150)) ?><?= strlen($ritualDesc) > 150 ? '...' : '' ?></p>
                <?php endif; ?>
            </div>
        <?php elseif ($req['response']): ?>
            <!-- Plain text response preview -->
            <div style="background: #F9FAFB; padding: 14px; border-radius: 10px; font-size: 0.9rem; color: #4B5563; line-height: 1.6; margin-top: 8px;">
                <?= nl2br(htmlspecialchars(substr($req['response'], 0, 200))) ?><?= strlen($req['response']) > 200 ? '...' : '' ?>
            </div>
        <?php endif; ?>

        <?php if ($req['response']): ?>
            <button class="btn-view-detail" onclick="openQueryDetail(<?= $idx ?>)">
                <i class="fas fa-eye"></i> View Details
            </button>
        <?php endif; ?>
    </div>

    <!-- Hidden data for modal -->
    <script>
        if (!window.queryData) window.queryData = {};
        window.queryData[<?= $idx ?>] = {
            type: <?= json_encode($typeInfo['label']) ?>,
            typeIcon: <?= json_encode($typeInfo['icon']) ?>,
            date: <?= json_encode(date('M d, Y \a\t H:i', strtotime($req['created_at']))) ?>,
            prompt: <?= json_encode($req['prompt']) ?>,
            response: <?= json_encode($req['response']) ?>,
            parsed: <?= $isJson ? $req['response'] : 'null' ?>
        };
    </script>
    <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Query Detail Modal -->
<div class="query-modal-overlay" id="queryModal" onclick="if(event.target===this) closeQueryModal()">
    <div class="query-modal">
        <div class="query-modal-header">
            <h3 id="modalTitle"><i class="fas fa-robot"></i> Query Details</h3>
            <button class="query-modal-close" onclick="closeQueryModal()">&times;</button>
        </div>
        <div class="query-modal-body" id="modalBody">
            <!-- Populated by JS -->
        </div>
    </div>
</div>

<script>
document.getElementById('aiForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const form = this;
    const resultDiv = document.getElementById('aiResult');
    resultDiv.innerHTML = '<p style="text-align: center; padding: 30px;"><i class="fas fa-spinner fa-spin"></i> Getting AI suggestions...</p>';
    
    try {
        const response = await fetch('/user/ai-suggestions', {
            method: 'POST',
            body: new FormData(form)
        });
        const data = await response.json();
        
        if (data.success) {
            // Try to parse as JSON ritual data
            let parsed = null;
            try { parsed = JSON.parse(data.suggestion); } catch(e) { parsed = null; }

            if (parsed && parsed.name) {
                resultDiv.innerHTML = renderRitualResult(parsed);
            } else {
                resultDiv.innerHTML = `
                    <div style="background: #D1FAE5; padding: 20px; border-radius: 12px; margin-bottom: 20px;">
                        <h4 style="margin-bottom: 15px;"><i class="fas fa-lightbulb"></i> AI Suggestion</h4>
                        <div style="white-space: pre-wrap; line-height: 1.7; color: #1F2937;">${escapeHtml(data.suggestion)}</div>
                    </div>
                `;
            }
        } else {
            resultDiv.innerHTML = `<div class="alert alert-error">${data.error || 'Something went wrong'}</div>`;
        }
    } catch (error) {
        resultDiv.innerHTML = '<div class="alert alert-error">Failed to get AI suggestions. Please try again.</div>';
    }
});

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function renderRitualResult(ritual) {
    let stepsHtml = '';
    if (ritual.steps && ritual.steps.length > 0) {
        stepsHtml = '<div style="margin-top: 15px;"><h5 style="color: #065F46; margin-bottom: 10px;"><i class="fas fa-list-ol"></i> Steps</h5>';
        ritual.steps.forEach((step, i) => {
            stepsHtml += `
                <div style="display: flex; gap: 10px; margin-bottom: 10px; padding: 10px; background: white; border-radius: 8px;">
                    <div style="width: 28px; height: 28px; background: #10B981; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.8rem; flex-shrink: 0;">${step.step_number || (i+1)}</div>
                    <div>
                        <strong style="font-size: 0.9rem;">${escapeHtml(step.title)}</strong>
                        ${step.description ? `<p style="margin: 4px 0 0; font-size: 0.85rem; color: #4B5563;">${escapeHtml(step.description)}</p>` : ''}
                        ${step.mantra ? `<p style="margin-top: 6px; padding: 6px 10px; background: #FEF3C7; border-radius: 6px; font-style: italic; font-size: 0.8rem; color: #92400E;"><strong>Mantra:</strong> ${escapeHtml(step.mantra)}</p>` : ''}
                    </div>
                </div>
            `;
        });
        stepsHtml += '</div>';
    }

    let itemsHtml = '';
    if (ritual.items && ritual.items.length > 0) {
        itemsHtml = '<div style="margin-top: 15px;"><h5 style="color: #065F46; margin-bottom: 10px;"><i class="fas fa-shopping-basket"></i> Required Items</h5><div style="display: grid; grid-template-columns: 1fr 1fr; gap: 6px;">';
        ritual.items.forEach(item => {
            itemsHtml += `<div style="display: flex; align-items: center; gap: 5px; padding: 6px 10px; background: white; border-radius: 6px; font-size: 0.85rem;"><i class="fas fa-check-circle" style="color: #10B981; font-size: 0.7rem;"></i> ${escapeHtml(item.item_name || item.name || '')} ${item.quantity ? `(${item.quantity} ${item.unit || ''})` : ''}</div>`;
        });
        itemsHtml += '</div></div>';
    }

    return `
        <div style="background: linear-gradient(135deg, #F0FDF4 0%, #ECFDF5 100%); border: 2px solid #10B981; border-radius: 16px; padding: 24px; margin-bottom: 20px;">
            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
                <i class="fas fa-om" style="font-size: 1.3rem; color: #059669;"></i>
                <h3 style="color: #065F46; margin: 0;">${escapeHtml(ritual.name)}</h3>
            </div>
            ${ritual.name_sanskrit ? `<p style="color: #059669; font-style: italic; margin-bottom: 12px;">${escapeHtml(ritual.name_sanskrit)}</p>` : ''}
            
            <div style="display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 15px;">
                ${ritual.category ? `<span style="padding: 4px 12px; background: white; border-radius: 20px; font-size: 0.8rem; font-weight: 500; border: 1px solid #D1D5DB;"><i class="fas fa-tag"></i> ${escapeHtml(ritual.category)}</span>` : ''}
                ${ritual.duration_minutes ? `<span style="padding: 4px 12px; background: white; border-radius: 20px; font-size: 0.8rem; font-weight: 500; border: 1px solid #D1D5DB;"><i class="fas fa-clock"></i> ${ritual.duration_minutes} min</span>` : ''}
                ${ritual.difficulty ? `<span style="padding: 4px 12px; background: white; border-radius: 20px; font-size: 0.8rem; font-weight: 500; border: 1px solid #D1D5DB;"><i class="fas fa-signal"></i> ${escapeHtml(ritual.difficulty.charAt(0).toUpperCase() + ritual.difficulty.slice(1))}</span>` : ''}
                ${ritual.deity ? `<span style="padding: 4px 12px; background: white; border-radius: 20px; font-size: 0.8rem; font-weight: 500; border: 1px solid #D1D5DB;"><i class="fas fa-pray"></i> ${escapeHtml(ritual.deity)}</span>` : ''}
            </div>

            ${ritual.description ? `<p style="color: #4B5563; line-height: 1.7; margin-bottom: 12px;">${escapeHtml(ritual.description)}</p>` : ''}
            ${ritual.significance ? `<p style="color: #6B7280; font-size: 0.9rem; line-height: 1.6; margin-bottom: 12px;"><strong>Significance:</strong> ${escapeHtml(ritual.significance)}</p>` : ''}
            ${stepsHtml}
            ${itemsHtml}
        </div>
    `;
}

function openQueryDetail(idx) {
    const data = window.queryData[idx];
    if (!data) return;

    const modal = document.getElementById('queryModal');
    const title = document.getElementById('modalTitle');
    const body = document.getElementById('modalBody');

    title.innerHTML = `<i class="fas ${data.typeIcon}"></i> ${escapeHtml(data.type)}`;

    let responseHtml = '';
    if (data.parsed) {
        responseHtml = renderRitualResult(data.parsed);
    } else if (data.response) {
        responseHtml = `<div style="background: #F9FAFB; padding: 16px; border-radius: 10px; white-space: pre-wrap; line-height: 1.7; color: #1F2937; font-size: 0.9rem;">${escapeHtml(data.response)}</div>`;
    }

    body.innerHTML = `
        <div class="modal-section">
            <div class="modal-section-label"><i class="fas fa-clock"></i> Date</div>
            <p style="color: #4B5563; font-size: 0.9rem;">${escapeHtml(data.date)}</p>
        </div>

        <div class="modal-section">
            <div class="modal-section-label"><i class="fas fa-question-circle"></i> Your Query</div>
            <div class="modal-prompt-text">${escapeHtml(data.prompt)}</div>
        </div>

        <div class="modal-section">
            <div class="modal-section-label"><i class="fas fa-robot"></i> AI Response</div>
            ${responseHtml}
        </div>
    `;

    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeQueryModal() {
    document.getElementById('queryModal').classList.remove('active');
    document.body.style.overflow = '';
}

// Close on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeQueryModal();
});
</script>
