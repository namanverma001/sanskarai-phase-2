<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-user-check"></i> Pending Pandit Approvals</h3>
    </div>
    
    <?php if (empty($pandits)): ?>
        <div style="text-align: center; padding: 50px; color: #6B7280;">
            <i class="fas fa-check-circle" style="font-size: 3rem; margin-bottom: 20px; color: #10B981;"></i>
            <p>No pending pandit applications!</p>
        </div>
    <?php else: ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Specialization</th>
                    <th>Experience</th>
                    <th>Applied</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pandits as $pandit): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($pandit['name']) ?></strong></td>
                    <td><?= htmlspecialchars($pandit['email']) ?></td>
                    <td><?= htmlspecialchars($pandit['specialization'] ?? 'N/A') ?></td>
                    <td><?= $pandit['experience_years'] ?? 0 ?> years</td>
                    <td><?= date('M d, Y', strtotime($pandit['profile_created_at'] ?? $pandit['created_at'])) ?></td>
                    <td>
                        <form method="POST" action="/admin/pandits/<?= $pandit['id'] ?>/approve" style="display: inline;">
                            <?= \App\Core\Auth::csrfField() ?>
                            <button type="submit" class="btn btn-sm btn-success">
                                <i class="fas fa-check"></i> Approve
                            </button>
                        </form>
                        <form method="POST" action="/admin/pandits/<?= $pandit['id'] ?>/reject" style="display: inline;">
                            <?= \App\Core\Auth::csrfField() ?>
                            <input type="hidden" name="reason" value="Application does not meet requirements.">
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Reject this application?')">
                                <i class="fas fa-times"></i> Reject
                            </button>
                        </form>
                    </td>
                </tr>
                <?php if (!empty($pandit['bio'])): ?>
                <tr>
                    <td colspan="6" style="background: #F9FAFB; padding: 15px;">
                        <strong>Bio:</strong> <?= htmlspecialchars($pandit['bio']) ?>
                    </td>
                </tr>
                <?php endif; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
