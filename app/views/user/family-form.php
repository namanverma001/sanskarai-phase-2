<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-<?= $family ? 'edit' : 'plus' ?>"></i> 
            <?= $family ? 'Edit Family' : 'Create Family' ?>
        </h3>
        <a href="/user/families" class="btn btn-sm" style="background: #E5E7EB; color: #374151;">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>
    
    <form method="POST" action="<?= $family ? '/user/families/' . $family['id'] : '/user/families' ?>">
        <?= \App\Core\Auth::csrfField() ?>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label>Family Name *</label>
                <input type="text" name="family_name" class="form-control" required
                       value="<?= htmlspecialchars($family['family_name'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Gotra</label>
                <input type="text" name="gotra" class="form-control"
                       value="<?= htmlspecialchars($family['gotra'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Nakshatra</label>
                <input type="text" name="nakshatra" class="form-control"
                       value="<?= htmlspecialchars($family['nakshatra'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Kul Devi/Devta</label>
                <input type="text" name="kul_devta" class="form-control"
                       value="<?= htmlspecialchars($family['kul_devta'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>City</label>
                <input type="text" name="city" class="form-control"
                       value="<?= htmlspecialchars($family['city'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>State</label>
                <input type="text" name="state" class="form-control"
                       value="<?= htmlspecialchars($family['state'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Country</label>
                <input type="text" name="country" class="form-control"
                       value="<?= htmlspecialchars($family['country'] ?? '') ?>">
            </div>
        </div>
        
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> <?= $family ? 'Update' : 'Create' ?> Family
        </button>
    </form>
</div>

<?php if ($family && !empty($family['members'])): ?>
<div class="card" style="margin-top: 25px;">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-user-friends"></i> Family Members</h3>
    </div>
    <table class="table">
        <thead><tr><th>Name</th><th>Relation</th><th>Gender</th><th>Actions</th></tr></thead>
        <tbody>
            <?php foreach ($family['members'] as $member): ?>
            <tr>
                <td>
                    <?= htmlspecialchars($member['name']) ?>
                    <?php if ($member['is_primary']): ?><span class="badge badge-success">Primary</span><?php endif; ?>
                </td>
                <td><?= htmlspecialchars($member['relation']) ?></td>
                <td><?= ucfirst($member['gender']) ?></td>
                <td>
                    <form method="POST" action="/user/families/members/<?= $member['id'] ?>/delete" style="display: inline;">
                        <?= \App\Core\Auth::csrfField() ?>
                        <button class="btn btn-sm btn-danger" onclick="return confirm('Remove this member?')">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

<?php if ($family): ?>
<div class="card" style="margin-top: 25px;">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-user-plus"></i> Add Family Member</h3>
    </div>
    <form method="POST" action="/user/families/<?= $family['id'] ?>/members">
        <?= \App\Core\Auth::csrfField() ?>
        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr 1fr auto; gap: 15px; align-items: end;">
            <div class="form-group" style="margin: 0;">
                <label>Name</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="form-group" style="margin: 0;">
                <label>Relation</label>
                <input type="text" name="relation" class="form-control" required placeholder="e.g., Son, Wife">
            </div>
            <div class="form-group" style="margin: 0;">
                <label>Gender</label>
                <select name="gender" class="form-control" required>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                    <option value="other">Other</option>
                </select>
            </div>
            <div class="form-group" style="margin: 0;">
                <label>Date of Birth</label>
                <input type="date" name="date_of_birth" class="form-control">
            </div>
            <button type="submit" class="btn btn-success"><i class="fas fa-plus"></i></button>
        </div>
    </form>
</div>
<?php endif; ?>
