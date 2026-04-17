<?php
/**
 * @var array $user
 * @var array|null $existingFeedback
 * @var bool $isLogoutFlow
 * @var bool $forceNew
 */

// Features to solicit feedback on
$features = [
    'AI Pandit',
    'Explore Rituals',
    'Cultural Insights',
    'Shopping List',
    'Find Pandit',
    'My Bookings',
    'Muhurat',
    'My Orders',
    'Browse Vendors',
    'Ask Pandit (Q&A)',
    'Invitations',
    'Budget Planner'
];
?>

<div class="card">
    <div class="card-header">
        <h2 class="card-title"><i class="fas fa-comment-dots text-primary me-2"></i> Your Feedback</h2>
        <?php if ($existingFeedback && !$forceNew): ?>
            <a href="/user/feedback?new=1<?= $isLogoutFlow ? '&logout=1' : '' ?>" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Submit Another Response
            </a>
        <?php endif; ?>
    </div>

    <div class="card-body">
        <?php if ($isLogoutFlow): ?>
            <div class="alert alert-info mb-4">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Help us improve!</strong> Please provide your feedback before logging out. We value your thoughts!
            </div>
        <?php endif; ?>

        <?php if ($existingFeedback && !$forceNew): ?>
            <!-- VIEW EXISTING FEEDBACK -->
            <div class="mb-4">
                <p class="text-muted">You have already submitted feedback on <strong><?= date('M j, Y g:i A', strtotime($existingFeedback['created_at'])) ?></strong>. Thank you!</p>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label text-muted">Name</label>
                    <div class="fw-bold"><?= htmlspecialchars($existingFeedback['name']) ?></div>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label text-muted">Email</label>
                    <div class="fw-bold"><?= htmlspecialchars($existingFeedback['email']) ?></div>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label text-muted">Phone</label>
                    <div class="fw-bold"><?= htmlspecialchars($existingFeedback['phone']) ?></div>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label text-muted">Community</label>
                    <div class="fw-bold"><?= htmlspecialchars($existingFeedback['community_name']) ?: 'N/A' ?></div>
                </div>
            </div>

            <hr>

            <h5 class="mt-4 mb-3">Feature Feedback</h5>
            <?php 
            $fbFeatures = [];
            if (!empty($existingFeedback['features_feedback'])) {
                $fbFeatures = is_string($existingFeedback['features_feedback']) ? json_decode($existingFeedback['features_feedback'], true) : $existingFeedback['features_feedback'];
            }
            ?>
            <?php if (empty($fbFeatures)): ?>
                <p class="text-muted">No specific features selected.</p>
            <?php else: ?>
                <ul class="list-group mb-4">
                    <?php foreach ($fbFeatures as $feature => $comment): ?>
                        <li class="list-group-item">
                            <strong><?= htmlspecialchars($feature) ?></strong>
                            <?php if ($comment): ?>
                                <p class="mb-0 mt-1 text-muted"><i class="fas fa-quote-left text-light me-2"></i><?= htmlspecialchars($comment) ?></p>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>

            <div class="mb-4">
                <h5 class="mb-2">What you liked about Sanskar AI:</h5>
                <div class="p-3 bg-light rounded" style="white-space: pre-wrap;"><?= htmlspecialchars($existingFeedback['likes_about']) ?></div>
            </div>

            <div class="mb-4">
                <h5 class="mb-2">What can be improved:</h5>
                <div class="p-3 bg-light rounded" style="white-space: pre-wrap;"><?= htmlspecialchars($existingFeedback['improvements_for']) ?: 'No improvements suggested.' ?></div>
            </div>
            
            <?php if ($isLogoutFlow): ?>
            <form action="/logout" method="POST" class="mt-4">
                <?= \App\Core\Auth::csrfField() ?>
                <!-- Bypass mechanism if they already filled it -->
                <button type="submit" class="btn btn-danger btn-lg">
                    <i class="fas fa-sign-out-alt"></i> Continue to Logout
                </button>
            </form>
            <?php endif; ?>

        <?php else: ?>
            <!-- NEW FEEDBACK FORM -->
            <form action="/user/feedback" method="POST">
                <?= \App\Core\Auth::csrfField() ?>
                
                <?php if ($isLogoutFlow): ?>
                    <input type="hidden" name="is_logout_flow" value="1">
                <?php endif; ?>

                <div class="row">
                    <div class="col-md-6 form-group">
                        <label for="name">Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control" value="<?= htmlspecialchars($user['name'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="email">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" id="email" class="form-control" value="<?= htmlspecialchars($user['email'] ?? '') ?>" required>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-6 form-group">
                        <label for="phone">Phone <span class="text-danger">*</span></label>
                        <input type="text" name="phone" id="phone" class="form-control" value="<?= htmlspecialchars($user['mobile'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="community_name">Community</label>
                        <input type="text" name="community_name" id="community_name" class="form-control" value="<?= htmlspecialchars($user['community_name'] ?? '') ?>" placeholder="e.g. Gujarati, Marathi, etc.">
                    </div>
                </div>

                <hr class="my-4">

                <h5 class="mb-3">Which features are you enjoying? <small class="text-muted">(Select features and provide your comments/rating)</small></h5>
                
                <style>
                    .star-rating {
                      display: inline-flex;
                      flex-direction: row-reverse;
                      justify-content: flex-end;
                    }
                    .star-rating input {
                      display: none;
                    }
                    .star-rating label {
                      color: #ccc;
                      font-size: 1.5rem;
                      padding: 0 4px;
                      cursor: pointer;
                      transition: color 0.2s;
                    }
                    .star-rating input:checked ~ label,
                    .star-rating label:hover,
                    .star-rating label:hover ~ label {
                      color: #ffc107;
                    }
                </style>

                <div id="dynamic-features-container">
                    <!-- Javascript will inject feature blocks here -->
                </div>
                
                <button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="addFeatureFeedback()">
                    <i class="fas fa-plus"></i> Add Another Feature
                </button>

                <hr class="my-4">

                <div class="form-group mt-3">
                    <label for="likes_about" class="fw-bold">What do you like about Sanskar AI? <span class="text-danger">*</span></label>
                    <textarea name="likes_about" id="likes_about" class="form-control mt-2" rows="4" placeholder="Tell us what you enjoyed most..." required></textarea>
                </div>

                <div class="form-group mt-4">
                    <label for="improvements_for" class="fw-bold">What can be improved?</label>
                    <textarea name="improvements_for" id="improvements_for" class="form-control mt-2" rows="4" placeholder="Tell us how we can make your experience better..."></textarea>
                </div>

                <div class="mt-4 pt-3 border-top">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-paper-plane me-2"></i> <?= $isLogoutFlow ? 'Submit & Logout' : 'Submit Feedback' ?>
                    </button>
                    
                    <?php if ($existingFeedback): ?>
                        <a href="/user/feedback<?= $isLogoutFlow ? '?logout=1' : '' ?>" class="btn btn-outline-secondary btn-lg ms-2">Cancel</a>
                    <?php endif; ?>
                </div>
            </form>
        <?php endif; ?>
    </div>
</div>

<script>
const availableFeatures = <?= json_encode($features) ?>;
let featureCount = 0;

function addFeatureFeedback() {
    featureCount++;
    const id = featureCount;
    
    let options = '<option value="">-- Select Feature --</option>';
    availableFeatures.forEach(f => {
        options += `<option value="${f}">${f}</option>`;
    });

    const html = `
    <div class="feature-item p-3 border rounded mb-3 bg-light position-relative" id="feature_block_${id}">
        ${id > 1 ? `<button type="button" class="btn-close position-absolute top-0 end-0 m-2" onclick="document.getElementById('feature_block_${id}').remove()" aria-label="Close"></button>` : ''}
        <div class="row align-items-center">
            <div class="col-md-3 mb-3 mb-md-0">
                <label class="form-label fw-bold small">Feature <span class="text-danger">*</span></label>
                <select name="features_feedback[${id}][name]" class="form-select border-secondary">
                    ${options}
                </select>
            </div>
            <div class="col-md-3 mb-3 mb-md-0">
                <label class="form-label fw-bold small">Rating</label><br>
                <div class="star-rating">
                    <input type="radio" id="star5_${id}" name="features_feedback[${id}][rating]" value="5" />
                    <label for="star5_${id}" title="5 stars"><i class="fas fa-star"></i></label>
                    <input type="radio" id="star4_${id}" name="features_feedback[${id}][rating]" value="4" />
                    <label for="star4_${id}" title="4 stars"><i class="fas fa-star"></i></label>
                    <input type="radio" id="star3_${id}" name="features_feedback[${id}][rating]" value="3" />
                    <label for="star3_${id}" title="3 stars"><i class="fas fa-star"></i></label>
                    <input type="radio" id="star2_${id}" name="features_feedback[${id}][rating]" value="2" />
                    <label for="star2_${id}" title="2 stars"><i class="fas fa-star"></i></label>
                    <input type="radio" id="star1_${id}" name="features_feedback[${id}][rating]" value="1" required />
                    <label for="star1_${id}" title="1 star"><i class="fas fa-star"></i></label>
                </div>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-bold small">Comment (Optional)</label>
                <input type="text" name="features_feedback[${id}][comment]" class="form-control" placeholder="Share your experience...">
            </div>
        </div>
    </div>
    `;
    
    document.getElementById('dynamic-features-container').insertAdjacentHTML('beforeend', html);
}

// Add one feature block immediately
if (document.getElementById('dynamic-features-container')) {
    addFeatureFeedback();
}
</script>
