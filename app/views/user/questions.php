<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 25px;">
    <!-- Ask a Question Section -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-question-circle"></i> Ask a Pandit</h3>
        </div>
        
        <p style="color: #6B7280; margin-bottom: 20px;">
            Have questions about rituals, traditions, or spiritual practices? Ask our verified pandits directly.
        </p>
        
        <form method="POST" action="/user/ask-pandit">
            <?= \App\Core\Auth::csrfField() ?>
            
            <div class="form-group">
                <label>Select Pandit</label>
                <select name="pandit_id" class="form-control" required>
                    <option value="">Choose a pandit...</option>
                    <?php foreach ($pandits ?? [] as $pandit): ?>
                    <option value="<?= $pandit['id'] ?>">
                        <?= htmlspecialchars($pandit['name']) ?> 
                        (<?= $pandit['specialization'] ?? 'General' ?> - ★<?= number_format($pandit['average_rating'] ?? 0, 1) ?>)
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label>Related Ritual (Optional)</label>
                <select name="ritual_id" class="form-control">
                    <option value="">General question...</option>
                    <?php foreach ($rituals ?? [] as $ritual): ?>
                    <option value="<?= $ritual['id'] ?>"><?= htmlspecialchars($ritual['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label>Your Question</label>
                <textarea name="question" class="form-control" rows="4" 
                          placeholder="Type your question here..." required></textarea>
            </div>
            
            <button type="submit" class="btn btn-primary" style="width: 100%;">
                <i class="fas fa-paper-plane"></i> Submit Question
            </button>
        </form>
    </div>
    
    <!-- My Questions Section -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-comments"></i> My Questions</h3>
        </div>
        
        <?php if (empty($questions)): ?>
            <p style="text-align: center; color: #6B7280; padding: 30px;">
                <i class="fas fa-inbox" style="font-size: 2rem; display: block; margin-bottom: 10px;"></i>
                You haven't asked any questions yet.
            </p>
        <?php else: ?>
            <div style="max-height: 500px; overflow-y: auto;">
                <?php foreach ($questions as $q): ?>
                <div style="border: 1px solid #E5E7EB; border-radius: 12px; padding: 15px; margin-bottom: 15px; 
                            <?= $q['status'] === 'pending' ? 'border-left: 4px solid #F59E0B;' : 'border-left: 4px solid #10B981;' ?>">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                        <span style="font-weight: 500; color: var(--primary);">
                            <i class="fas fa-user"></i> <?= htmlspecialchars($q['pandit_name']) ?>
                        </span>
                        <span class="badge badge-<?= $q['status'] === 'answered' ? 'success' : 'warning' ?>">
                            <?= ucfirst($q['status']) ?>
                        </span>
                    </div>
                    
                    <?php if (!empty($q['ritual_name'])): ?>
                    <p style="font-size: 0.8rem; color: #6B7280; margin-bottom: 8px;">
                        <i class="fas fa-book"></i> <?= htmlspecialchars($q['ritual_name']) ?>
                    </p>
                    <?php endif; ?>
                    
                    <div style="background: #F3F4F6; padding: 12px; border-radius: 8px; margin-bottom: 10px;">
                        <p style="font-size: 0.9rem; color: var(--dark);">
                            <i class="fas fa-question" style="color: var(--primary);"></i> 
                            <?= htmlspecialchars($q['question']) ?>
                        </p>
                    </div>
                    
                    <?php if ($q['answer']): ?>
                    <div style="background: #D1FAE5; padding: 12px; border-radius: 8px;">
                        <p style="font-size: 0.85rem; font-weight: 500; color: #065F46; margin-bottom: 5px;">
                            <i class="fas fa-reply"></i> Answer:
                        </p>
                        <p style="font-size: 0.9rem; color: #047857;">
                            <?= nl2br(htmlspecialchars($q['answer'])) ?>
                        </p>
                        <p style="font-size: 0.75rem; color: #6B7280; margin-top: 8px;">
                            Answered <?= $q['answered_at'] ? date('M d, Y', strtotime($q['answered_at'])) : '' ?>
                        </p>
                    </div>
                    <?php else: ?>
                    <p style="font-size: 0.8rem; color: #6B7280; font-style: italic;">
                        <i class="fas fa-hourglass-half"></i> Waiting for response...
                    </p>
                    <?php endif; ?>
                    
                    <p style="font-size: 0.75rem; color: #9CA3AF; margin-top: 10px;">
                        Asked <?= date('M d, Y', strtotime($q['created_at'])) ?>
                    </p>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
@media (max-width: 768px) {
    div[style*="grid-template-columns: 1fr 1fr"] {
        grid-template-columns: 1fr !important;
    }
}
</style>
