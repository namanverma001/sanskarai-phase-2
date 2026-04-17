<?php
/**
 * @var array $feedbacks
 */
?>

<style>
/* Custom Modal Styling for Admin Dashboard */
.custom-modal-backdrop {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 1040;
    display: none;
    opacity: 0;
    transition: opacity 0.15s linear;
}
.custom-modal-backdrop.show {
    opacity: 1;
}
.custom-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    z-index: 1050;
    display: none;
    overflow-x: hidden;
    overflow-y: auto;
    outline: 0;
}
.custom-modal.show {
    display: block;
}
.custom-modal-dialog {
    position: relative;
    width: auto;
    margin: 1.75rem auto;
    max-width: 800px;
    pointer-events: none;
    transform: translate(0, -50px);
    transition: transform 0.3s ease-out;
}
.custom-modal.show .custom-modal-dialog {
    transform: none;
}
.custom-modal-content {
    position: relative;
    display: flex;
    flex-direction: column;
    width: 100%;
    pointer-events: auto;
    background-color: #fff;
    background-clip: padding-box;
    border: 1px solid rgba(0, 0, 0, 0.2);
    border-radius: 0.5rem;
    outline: 0;
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
}
.custom-modal-header {
    display: flex;
    flex-shrink: 0;
    align-items: center;
    justify-content: space-between;
    padding: 1rem 1.5rem;
    border-bottom: 1px solid #dee2e6;
    border-top-left-radius: calc(0.5rem - 1px);
    border-top-right-radius: calc(0.5rem - 1px);
}
.custom-modal-title {
    margin-bottom: 0;
    line-height: 1.5;
    font-size: 1.25rem;
    font-weight: 600;
}
.custom-modal-body {
    position: relative;
    flex: 1 1 auto;
    padding: 1.5rem;
    color: #4B5563;
}
.custom-modal-footer {
    display: flex;
    flex-shrink: 0;
    flex-wrap: wrap;
    align-items: center;
    justify-content: flex-end;
    padding: 1rem 1.5rem;
    border-top: 1px solid #dee2e6;
    border-bottom-right-radius: calc(0.5rem - 1px);
    border-bottom-left-radius: calc(0.5rem - 1px);
}
.badge-custom {
    display: inline-block;
    padding: 0.35em 0.65em;
    font-size: 0.75em;
    font-weight: 700;
    line-height: 1;
    color: #fff;
    text-align: center;
    white-space: nowrap;
    vertical-align: baseline;
    border-radius: 0.25rem;
    background-color: #6B7280;
}
.btn-close-custom {
    box-sizing: content-box;
    width: 1em;
    height: 1em;
    padding: 0.25em 0.25em;
    color: #000;
    background: transparent;
    border: 0;
    border-radius: 0.25rem;
    opacity: 0.5;
    cursor: pointer;
    font-size: 1.25rem;
}
.btn-close-custom:hover {
    color: #000;
    text-decoration: none;
    opacity: 0.75;
}
.btn-secondary-custom {
    color: #fff;
    background-color: #6c757d;
    border-color: #6c757d;
    display: inline-block;
    font-weight: 400;
    line-height: 1.5;
    text-align: center;
    text-decoration: none;
    vertical-align: middle;
    cursor: pointer;
    user-select: none;
    border: 1px solid transparent;
    padding: 0.375rem 0.75rem;
    font-size: 1rem;
    border-radius: 0.25rem;
    transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out;
}
.btn-secondary-custom:hover {
    background-color: #5c636a;
    border-color: #565e64;
}
</style>

<div class="custom-modal-backdrop" id="modalBackdrop"></div>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
        <h2 class="card-title m-0"><i class="fas fa-comment-dots text-primary me-2"></i> User Feedback Logs</h2>
        
        <a href="/admin/feedbacks/export" class="btn btn-success btn-sm">
            <i class="fas fa-file-csv me-2"></i> Export to CSV
        </a>
    </div>
    
    <div class="card-body">
        <?php if (empty($feedbacks)): ?>
            <div class="alert alert-info border border-info">
                <i class="fas fa-info-circle me-2"></i> No user feedbacks found.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 text-sm">
                    <thead class="bg-light">
                        <tr>
                            <th scope="col" style="width: 50px;">ID</th>
                            <th scope="col">User</th>
                            <th scope="col">Community</th>
                            <th scope="col">Likes / Improvements</th>
                            <th scope="col">Features Noted</th>
                            <th scope="col">Date</th>
                            <th scope="col" class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($feedbacks as $fb): ?>
                            <tr>
                                <td>#<?= $fb['id'] ?></td>
                                <td>
                                    <div class="fw-bold"><?= htmlspecialchars($fb['name']) ?></div>
                                    <div class="text-muted small"><?= htmlspecialchars($fb['email']) ?></div>
                                    <div class="text-muted small"><?= htmlspecialchars($fb['phone']) ?></div>
                                </td>
                                <td>
                                    <?= htmlspecialchars($fb['community_name'] ?: 'N/A') ?>
                                </td>
                                <td style="max-width: 300px;">
                                    <div class="mb-1">
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success">Likes</span>
                                        <small class="d-inline-block text-truncate align-middle" style="max-width: 200px;" title="<?= htmlspecialchars($fb['likes_about']) ?>">
                                            <?= htmlspecialchars($fb['likes_about']) ?>
                                        </small>
                                    </div>
                                    <div>
                                        <span class="badge bg-warning bg-opacity-10 text-warning border border-warning">Impr.</span>
                                        <small class="d-inline-block text-truncate align-middle" style="max-width: 200px;" title="<?= htmlspecialchars($fb['improvements_for']) ?>">
                                            <?= htmlspecialchars($fb['improvements_for']) ?: 'N/A' ?>
                                        </small>
                                    </div>
                                    
                                    <!-- View Modal Toggle -->
                                    <a href="#" class="small text-primary mt-1 d-inline-block" onclick="event.preventDefault(); document.getElementById('modalBackdrop').style.display='block'; setTimeout(() => document.getElementById('modalBackdrop').classList.add('show'), 10); document.getElementById('viewFeedbackModal<?= $fb['id'] ?>').style.display='block'; setTimeout(() => document.getElementById('viewFeedbackModal<?= $fb['id'] ?>').classList.add('show'), 10);">View Full Response</a>
                                    
                                    <!-- Modal -->
                                    <div class="custom-modal" id="viewFeedbackModal<?= $fb['id'] ?>" tabindex="-1">
                                      <div class="custom-modal-dialog">
                                        <div class="custom-modal-content">
                                          <div class="custom-modal-header">
                                            <h5 class="custom-modal-title">Feedback from <span style="color: var(--primary);"><?= htmlspecialchars($fb['name']) ?></span></h5>
                                            <button type="button" class="btn-close-custom" aria-label="Close" onclick="closeModal(<?= $fb['id'] ?>)"><i class="fas fa-times"></i></button>
                                          </div>
                                          <div class="custom-modal-body text-wrap">
                                            <div style="margin-bottom: 1.5rem; line-height: 1.6;">
                                                <strong>Email:</strong> <?= htmlspecialchars($fb['email']) ?><br>
                                                <strong>Phone:</strong> <?= htmlspecialchars($fb['phone']) ?><br>
                                                <strong>Community:</strong> <?= htmlspecialchars($fb['community_name'] ?: 'N/A') ?><br>
                                                <strong>Date:</strong> <?= date('F j, Y, g:i a', strtotime($fb['created_at'])) ?>
                                            </div>
                                            
                                            <h6 style="border-bottom: 1px solid #e5e7eb; padding-bottom: 0.5rem; font-weight: 600; margin-bottom: 1rem; color: #111827;">Features Feedbacks</h6>
                                            <?php 
                                            $featuresStrList = '';
                                            if (!empty($fb['features_feedback'])) {
                                                $features = is_string($fb['features_feedback']) ? json_decode($fb['features_feedback'], true) : $fb['features_feedback'];
                                                if (is_array($features)) {
                                                    foreach ($features as $fName => $fVal) {
                                                        echo '<p style="margin-bottom: 0.5rem;"><strong style="color: #111827;">'.htmlspecialchars($fName).':</strong> '.htmlspecialchars($fVal).'</p>';
                                                    }
                                                }
                                            } else {
                                                echo '<p style="color: #6B7280; font-style: italic;">No specific features selected.</p>';
                                            }
                                            ?>
                                            
                                            <h6 style="border-bottom: 1px solid #e5e7eb; padding-bottom: 0.5rem; font-weight: 600; margin-top: 1.5rem; margin-bottom: 1rem; color: #111827;">What they liked</h6>
                                            <div style="background-color: #f3f4f6; padding: 1rem; border-radius: 0.375rem; white-space: pre-wrap; font-size: 0.95em; border: 1px solid #e5e7eb;"><?= htmlspecialchars($fb['likes_about']) ?></div>
                                            
                                            <h6 style="border-bottom: 1px solid #e5e7eb; padding-bottom: 0.5rem; font-weight: 600; margin-top: 1.5rem; margin-bottom: 1rem; color: #111827;">Suggested Improvements</h6>
                                            <div style="background-color: #f3f4f6; padding: 1rem; border-radius: 0.375rem; white-space: pre-wrap; font-size: 0.95em; border: 1px solid #e5e7eb;"><?= htmlspecialchars($fb['improvements_for']) ?: 'None' ?></div>
                                            
                                          </div>
                                          <div class="custom-modal-footer">
                                            <button type="button" class="btn-secondary-custom" onclick="closeModal(<?= $fb['id'] ?>)">Close</button>
                                          </div>
                                        </div>
                                      </div>
                                    </div>
                                </td>
                                <td>
                                    <?php 
                                    if (!empty($fb['features_feedback'])) {
                                        $features = is_string($fb['features_feedback']) ? json_decode($fb['features_feedback'], true) : $fb['features_feedback'];
                                        if (is_array($features)) {
                                            foreach (array_keys($features) as $fName) {
                                                echo '<span class="badge bg-secondary mb-1">'.htmlspecialchars($fName).'</span><br>';
                                            }
                                        }
                                    } else {
                                        echo '<span class="text-muted small">None</span>';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <div class="small fw-semibold"><?= date('M j, Y', strtotime($fb['created_at'])) ?></div>
                                    <div class="small text-muted"><?= date('g:i A', strtotime($fb['created_at'])) ?></div>
                                </td>
                                <td class="text-end">
                                    <form action="/admin/feedbacks/<?= $fb['id'] ?>/delete" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this feedback?');">
                                        <?= \App\Core\Auth::csrfField() ?>
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Feedback">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
function closeModal(id) {
    var modal = document.getElementById('viewFeedbackModal' + id);
    var backdrop = document.getElementById('modalBackdrop');
    
    modal.classList.remove('show');
    backdrop.classList.remove('show');
    
    setTimeout(function() {
        modal.style.display = 'none';
        backdrop.style.display = 'none';
    }, 150);
}

// Close explicitly if clicked on backdrop
window.onclick = function(event) {
    if (event.target.classList.contains('custom-modal')) {
        var openModals = document.querySelectorAll('.custom-modal.show');
        openModals.forEach(function(modal) {
            var id = modal.id.replace('viewFeedbackModal', '');
            closeModal(id);
        });
    }
}
</script>
