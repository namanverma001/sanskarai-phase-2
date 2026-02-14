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
    <?php foreach (array_slice($history, 0, 5) as $req): ?>
    <div style="border: 1px solid #E5E7EB; border-radius: 10px; padding: 15px; margin-bottom: 15px;">
        <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
            <strong><?= htmlspecialchars($req['request_type']) ?></strong>
            <span style="color: #6B7280; font-size: 0.85rem;"><?= date('M d, H:i', strtotime($req['created_at'])) ?></span>
        </div>
        <p style="color: #6B7280; font-size: 0.9rem; margin-bottom: 10px;">
            <?= htmlspecialchars(substr($req['prompt'], 0, 100)) ?>...
        </p>
        <?php if ($req['response']): ?>
        <div style="background: #F9FAFB; padding: 15px; border-radius: 8px; font-size: 0.9rem;">
            <?= nl2br(htmlspecialchars(substr($req['response'], 0, 300))) ?>...
        </div>
        <?php endif; ?>
    </div>
    <?php endforeach; ?>
    <?php endif; ?>
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
            resultDiv.innerHTML = `
                <div style="background: #D1FAE5; padding: 20px; border-radius: 12px; margin-bottom: 20px;">
                    <h4 style="margin-bottom: 15px;"><i class="fas fa-lightbulb"></i> AI Suggestion</h4>
                    <div style="white-space: pre-wrap;">${data.suggestion}</div>
                </div>
            `;
        } else {
            resultDiv.innerHTML = `<div class="alert alert-error">${data.error || 'Something went wrong'}</div>`;
        }
    } catch (error) {
        resultDiv.innerHTML = '<div class="alert alert-error">Failed to get AI suggestions. Please try again.</div>';
    }
});
</script>
