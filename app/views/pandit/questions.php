<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-question-circle"></i> User Questions</h3>
    </div>
    
    <?php if (empty($questions)): ?>
        <p style="text-align: center; color: #6B7280; padding: 30px;">No questions yet</p>
    <?php else: ?>
        <?php foreach ($questions as $q): ?>
        <div style="border: 1px solid #E5E7EB; border-radius: 12px; padding: 20px; margin-bottom: 15px; <?= $q['status'] === 'pending' ? 'background: #FEF3C7;' : '' ?>">
            <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                <strong><?= htmlspecialchars($q['user_name']) ?></strong>
                <span class="badge badge-<?= $q['status'] === 'answered' ? 'success' : 'warning' ?>"><?= ucfirst($q['status']) ?></span>
            </div>
            <p style="margin-bottom: 15px;"><i class="fas fa-question"></i> <?= htmlspecialchars($q['question']) ?></p>
            
            <?php if ($q['answer']): ?>
                <div style="background: #D1FAE5; padding: 15px; border-radius: 8px;">
                    <strong><i class="fas fa-reply"></i> Your Answer:</strong><br>
                    <?= nl2br(htmlspecialchars($q['answer'])) ?>
                </div>
            <?php else: ?>
                <form method="POST" action="/pandit/questions/<?= $q['id'] ?>/answer">
                    <?= \App\Core\Auth::csrfField() ?>
                    <textarea name="answer" class="form-control" rows="3" placeholder="Type your answer..." required></textarea>
                    <button type="submit" class="btn btn-primary" style="margin-top: 10px;">
                        <i class="fas fa-paper-plane"></i> Submit Answer
                    </button>
                </form>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
