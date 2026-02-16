<div class="page-header">
    <h1 class="page-title">My Profile</h1>
    <p class="text-muted">Manage your personal information and preferences</p>
</div>

<div class="content-grid">
    <div class="profile-card">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-user-edit"></i> Personal Information</h3>
            </div>
            
            <form action="/user/profile" method="POST">
                <?= \App\Core\Auth::csrfField() ?>
                
                <div class="form-grid">
                    <!-- Read-only Fields -->
                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($user['name']) ?>" readonly disabled style="background-color: #f3f4f6; color: #6b7280; cursor: not-allowed;">
                        <small class="text-muted">Name cannot be changed</small>
                    </div>

                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" readonly disabled style="background-color: #f3f4f6; color: #6b7280; cursor: not-allowed;">
                        <small class="text-muted">Email cannot be changed</small>
                    </div>

                    <div class="form-group">
                        <label>Mobile Number</label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($user['mobile'] ?? 'Not set') ?>" readonly disabled style="background-color: #f3f4f6; color: #6b7280; cursor: not-allowed;">
                    </div>

                    <!-- Editable Fields -->
                    <div class="form-group">
                        <label for="community_name">Community Name <span class="text-danger">*</span></label>
                        <input 
                            type="text" 
                            id="community_name" 
                            name="community_name" 
                            class="form-control" 
                            value="<?= htmlspecialchars($user['community_name'] ?? '') ?>" 
                            placeholder="e.g. Brahmin, Rajput, etc."
                            required
                        >
                        <small class="text-muted">Helps us personalize rituals for your community</small>
                    </div>

                    <div class="form-group">
                        <label for="religion">Religion <span class="text-danger">*</span></label>
                        <select id="religion" name="religion" class="form-control" required>
                            <option value="">Select Religion</option>
                            <?php 
                            $religions = ['Hinduism', 'Jainism', 'Sikhism', 'Buddhism', 'Other'];
                            $currentReligion = $user['religion'] ?? 'Hinduism';
                            foreach ($religions as $religion): 
                            ?>
                                <option value="<?= $religion ?>" <?= $currentReligion === $religion ? 'selected' : '' ?>>
                                    <?= $religion ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="kul_devi_devta">Kul Devi/Devta</label>
                        <input 
                            type="text" 
                            id="kul_devi_devta" 
                            name="kul_devi_devta" 
                            class="form-control" 
                            value="<?= htmlspecialchars($user['kul_devi_devta'] ?? '') ?>" 
                            placeholder="e.g. Durga Mata, Khandoba, Kulswamini..."
                        >
                        <small class="text-muted">Your family's traditional deity</small>
                    </div>
                </div>

                <div class="form-actions" style="margin-top: 25px; display: flex; gap: 15px;">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                    <a href="/user/dashboard" class="btn btn-secondary" style="background: transparent; border: 1px solid #d1d5db; color: #374151;">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Sidebar / Additional Info -->
    <div class="profile-sidebar">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-shield-alt"></i> Account Status</h3>
            </div>
            <div class="status-info">
                <div class="info-item">
                    <span class="label">Role:</span>
                    <span class="value badge badge-info"><?= ucfirst($user['role']) ?></span>
                </div>
                <div class="info-item" style="margin-top: 10px;">
                    <span class="label">Status:</span>
                    <span class="value badge badge-success"><?= ucfirst($user['status']) ?></span>
                </div>
                <div class="info-item" style="margin-top: 10px;">
                    <span class="label">Member Since:</span>
                    <span class="value"><?= date('M d, Y', strtotime($user['created_at'])) ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .text-muted {
        color: #6b7280;
        font-size: 0.85rem;
        margin-top: 5px;
        display: block;
    }
    .text-danger {
        color: #ef4444;
    }
    .form-grid {
        display: grid;
        gap: 20px;
    }
    .info-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 0;
        border-bottom: 1px solid #f3f4f6;
    }
    .info-item:last-child {
        border-bottom: none;
    }
    .label {
        font-weight: 500;
        color: #4b5563;
    }
    .value {
        font-weight: 600;
        color: #111827;
    }
    @media (min-width: 768px) {
        .form-grid {
            grid-template-columns: 1fr 1fr;
        }
        .form-group:nth-child(1), .form-group:nth-child(2) {
            grid-column: span 1;
        }
        .form-group:nth-child(3) {
            grid-column: span 2;
        }
    }
</style>
