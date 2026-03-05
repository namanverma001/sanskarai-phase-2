<div class="page-header">
    <h1 class="page-title">Rate Your Experience</h1>
    <p class="text-muted">Share your feedback about <?= htmlspecialchars($assignment['pandit_name']) ?></p>
</div>

<div class="content-grid">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-star"></i> Review Pandit</h3>
        </div>
        
        <div class="booking-info" style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 25px;">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                <div>
                    <strong>Pandit:</strong> <?= htmlspecialchars($assignment['pandit_name']) ?>
                </div>
                <div>
                    <strong>Ritual:</strong> <?= htmlspecialchars($assignment['ritual_name'] ?? $assignment['custom_ritual_name'] ?? 'Custom Ritual') ?>
                </div>
                <div>
                    <strong>Date:</strong> <?= date('d M Y', strtotime($assignment['scheduled_date'])) ?>
                </div>
            </div>
        </div>

        <form id="panditReviewForm" action="/user/reviews/pandit" method="POST">
            <?= \App\Core\Auth::csrfField() ?>
            <input type="hidden" name="assignment_id" value="<?= $assignment['id'] ?>">
            
            <!-- Overall Rating -->
            <div class="form-group" style="margin-bottom: 25px;">
                <label class="form-label"><strong>Overall Rating</strong> <span class="text-danger">*</span></label>
                <div class="star-rating-input" data-field="rating_overall">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                    <span class="star" data-value="<?= $i ?>">
                        <i class="far fa-star"></i>
                    </span>
                    <?php endfor; ?>
                </div>
                <input type="hidden" name="rating_overall" id="rating_overall" required>
                <div class="invalid-feedback" id="rating_overall_error"></div>
            </div>

            <!-- Detailed Ratings -->
            <div class="rating-categories" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 25px;">
                <div class="form-group">
                    <label class="form-label">Punctuality</label>
                    <div class="star-rating-input small" data-field="punctuality">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                        <span class="star" data-value="<?= $i ?>"><i class="far fa-star"></i></span>
                        <?php endfor; ?>
                    </div>
                    <input type="hidden" name="punctuality" id="punctuality">
                </div>

                <div class="form-group">
                    <label class="form-label">Knowledge</label>
                    <div class="star-rating-input small" data-field="knowledge">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                        <span class="star" data-value="<?= $i ?>"><i class="far fa-star"></i></span>
                        <?php endfor; ?>
                    </div>
                    <input type="hidden" name="knowledge" id="knowledge">
                </div>

                <div class="form-group">
                    <label class="form-label">Behavior</label>
                    <div class="star-rating-input small" data-field="behavior">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                        <span class="star" data-value="<?= $i ?>"><i class="far fa-star"></i></span>
                        <?php endfor; ?>
                    </div>
                    <input type="hidden" name="behavior" id="behavior">
                </div>

                <div class="form-group">
                    <label class="form-label">Clarity of Explanation</label>
                    <div class="star-rating-input small" data-field="clarity">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                        <span class="star" data-value="<?= $i ?>"><i class="far fa-star"></i></span>
                        <?php endfor; ?>
                    </div>
                    <input type="hidden" name="clarity" id="clarity">
                </div>
            </div>

            <!-- Written Review -->
            <div class="form-group" style="margin-bottom: 25px;">
                <label class="form-label" for="review_text">Your Review (Optional)</label>
                <textarea 
                    name="review_text" 
                    id="review_text" 
                    class="form-control" 
                    rows="4" 
                    placeholder="Share your experience... (10-500 characters)"
                    minlength="10"
                    maxlength="500"
                ></textarea>
                <small class="text-muted">
                    <span id="charCount">0</span>/500 characters (minimum 10 if provided)
                </small>
                <div class="invalid-feedback" id="review_text_error"></div>
            </div>

            <div class="form-actions" style="display: flex; gap: 15px;">
                <button type="submit" class="btn btn-primary" id="submitBtn">
                    <i class="fas fa-paper-plane"></i> Submit Review
                </button>
                <a href="/user/bookings" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<style>
.star-rating-input {
    display: inline-flex;
    gap: 5px;
    cursor: pointer;
}

.star-rating-input .star {
    font-size: 28px;
    color: #d1d5db;
    transition: color 0.2s, transform 0.1s;
}

.star-rating-input .star:hover,
.star-rating-input .star.hover {
    transform: scale(1.1);
}

.star-rating-input .star.active i,
.star-rating-input .star.hover i {
    color: #fbbf24;
}

.star-rating-input .star.active i {
    font-weight: 900;
}

.star-rating-input.small .star {
    font-size: 20px;
}

.invalid-feedback {
    display: none;
    color: #dc2626;
    font-size: 0.875rem;
    margin-top: 5px;
}

.invalid-feedback.show {
    display: block;
}

.form-control.is-invalid {
    border-color: #dc2626;
}

.rating-categories .form-label {
    font-size: 0.9rem;
    color: #6b7280;
    margin-bottom: 8px;
    display: block;
}

/* Success Modal */
.success-modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.6);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 9999;
    animation: fadeIn 0.3s ease;
}

.success-modal-overlay.show {
    display: flex;
}

.success-modal {
    background: white;
    border-radius: 20px;
    padding: 40px 50px;
    text-align: center;
    max-width: 420px;
    width: 90%;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    animation: scaleIn 0.4s ease;
}

.success-modal .icon-circle {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 25px;
    animation: bounceIn 0.6s ease 0.2s both;
}

.success-modal .icon-circle i {
    font-size: 36px;
    color: white;
}

.success-modal h2 {
    color: #1f2937;
    font-size: 1.5rem;
    margin-bottom: 10px;
}

.success-modal p {
    color: #6b7280;
    font-size: 1rem;
    margin-bottom: 25px;
    line-height: 1.6;
}

.success-modal .stars-display {
    color: #fbbf24;
    font-size: 28px;
    margin-bottom: 20px;
    letter-spacing: 5px;
}

.success-modal .btn-continue {
    background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
    color: white;
    border: none;
    padding: 12px 35px;
    border-radius: 30px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: transform 0.2s, box-shadow 0.2s;
}

.success-modal .btn-continue:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px -5px rgba(99, 102, 241, 0.4);
}

.success-modal .redirect-text {
    font-size: 0.85rem;
    color: #9ca3af;
    margin-top: 15px;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes scaleIn {
    from { transform: scale(0.8); opacity: 0; }
    to { transform: scale(1); opacity: 1; }
}

@keyframes bounceIn {
    0% { transform: scale(0); }
    50% { transform: scale(1.2); }
    100% { transform: scale(1); }
}
</style>

<!-- Success Modal -->
<div class="success-modal-overlay" id="successModal">
    <div class="success-modal">
        <div class="icon-circle">
            <i class="fas fa-check"></i>
        </div>
        <h2>Thank You!</h2>
        <div class="stars-display" id="modalStars"></div>
        <p id="modalMessage">Your review has been submitted successfully. Your feedback helps others make better decisions.</p>
        <button type="button" class="btn-continue" id="modalContinueBtn">Continue</button>
        <p class="redirect-text">Redirecting in <span id="countdown">3</span> seconds...</p>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Star rating functionality
    document.querySelectorAll('.star-rating-input').forEach(container => {
        const field = container.dataset.field;
        const stars = container.querySelectorAll('.star');
        const input = document.getElementById(field);

        stars.forEach((star, index) => {
            star.addEventListener('mouseenter', () => {
                stars.forEach((s, i) => {
                    s.classList.toggle('hover', i <= index);
                });
            });

            star.addEventListener('mouseleave', () => {
                stars.forEach(s => s.classList.remove('hover'));
            });

            star.addEventListener('click', () => {
                const value = star.dataset.value;
                input.value = value;
                
                stars.forEach((s, i) => {
                    s.classList.toggle('active', i < value);
                    const icon = s.querySelector('i');
                    icon.className = i < value ? 'fas fa-star' : 'far fa-star';
                });

                // Clear error
                document.getElementById(field + '_error')?.classList.remove('show');
            });
        });
    });

    // Character counter
    const reviewText = document.getElementById('review_text');
    const charCount = document.getElementById('charCount');
    
    reviewText.addEventListener('input', function() {
        charCount.textContent = this.value.length;
    });

    // Form submission
    const form = document.getElementById('panditReviewForm');
    const submitBtn = document.getElementById('submitBtn');

    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        // Validate overall rating
        const overallRating = document.getElementById('rating_overall').value;
        if (!overallRating) {
            document.getElementById('rating_overall_error').textContent = 'Please select an overall rating.';
            document.getElementById('rating_overall_error').classList.add('show');
            return;
        }

        // Validate review text length if provided
        const reviewTextValue = reviewText.value.trim();
        if (reviewTextValue && reviewTextValue.length < 10) {
            document.getElementById('review_text_error').textContent = 'Review must be at least 10 characters.';
            document.getElementById('review_text_error').classList.add('show');
            reviewText.classList.add('is-invalid');
            return;
        }

        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';

        try {
            const formData = new FormData(form);
            const response = await fetch(form.action, {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                // Show success modal
                showSuccessModal(result.message, result.redirect || '/user/bookings');
            } else {
                if (result.errors) {
                    Object.keys(result.errors).forEach(field => {
                        const errorEl = document.getElementById(field + '_error');
                        if (errorEl) {
                            errorEl.textContent = result.errors[field];
                            errorEl.classList.add('show');
                        }
                    });
                } else {
                    alert(result.error || 'Failed to submit review.');
                }
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Submit Review';
            }
        } catch (error) {
            console.error('Error:', error);
            showErrorToast('An error occurred. Please try again.');
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Submit Review';
        }
    });

    // Success Modal Functions
    function showSuccessModal(message, redirectUrl) {
        const modal = document.getElementById('successModal');
        const modalMessage = document.getElementById('modalMessage');
        const modalStars = document.getElementById('modalStars');
        const countdownEl = document.getElementById('countdown');
        const continueBtn = document.getElementById('modalContinueBtn');
        
        // Show stars based on rating
        const rating = parseInt(document.getElementById('rating_overall').value) || 5;
        modalStars.innerHTML = '★'.repeat(rating) + '☆'.repeat(5 - rating);
        
        modalMessage.textContent = message || 'Your review has been submitted successfully!';
        modal.classList.add('show');
        
        // Countdown and redirect
        let count = 3;
        const timer = setInterval(() => {
            count--;
            countdownEl.textContent = count;
            if (count <= 0) {
                clearInterval(timer);
                window.location.href = redirectUrl;
            }
        }, 1000);
        
        // Manual continue button
        continueBtn.onclick = () => {
            clearInterval(timer);
            window.location.href = redirectUrl;
        };
    }
    
    function showErrorToast(message) {
        // Create toast element
        const toast = document.createElement('div');
        toast.style.cssText = 'position:fixed;top:20px;right:20px;background:#ef4444;color:white;padding:15px 25px;border-radius:10px;z-index:9999;animation:fadeIn 0.3s ease;box-shadow:0 10px 25px rgba(0,0,0,0.2);';
        toast.innerHTML = '<i class="fas fa-exclamation-circle" style="margin-right:10px;"></i>' + message;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 4000);
    }
});
</script>
