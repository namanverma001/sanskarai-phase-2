<?php
/**
 * Create Invitation Page
 */
?>

<div class="card">
    <div class="card-header">
        <h2 class="card-title"><i class="fas fa-magic"></i> Create Invitation Card</h2>
        <a href="/user/invitations" class="btn btn-sm" style="background: #E5E7EB; color: #374151;">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>

    <p style="color: #6B7280; margin-bottom: 25px; line-height: 1.6;">
        Fill in the details below and our AI will design a beautiful, personalized invitation card for your occasion.
        You'll get a shareable link that you can send to your guests!
    </p>

    <form method="POST" action="/user/invitations" id="invitationForm">
        <?= \App\Core\Auth::csrfField() ?>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <!-- Occasion Type -->
            <div class="form-group">
                <label for="occasion_type"><i class="fas fa-star" style="color: var(--primary);"></i> Occasion Type <span style="color: red;">*</span></label>
                <select name="occasion_type" id="occasion_type" class="form-control" required>
                    <option value="">Select Occasion</option>
                    
                    <?php if (!empty($userRituals)): ?>
                        <optgroup label="🙏 My Rituals">
                            <?php foreach ($userRituals as $ritual): ?>
                                <option value="<?= htmlspecialchars($ritual['name']) ?>">
                                    🕉️ <?= htmlspecialchars($ritual['name']) ?>
                                    <?= !empty($ritual['category']) ? ' (' . htmlspecialchars($ritual['category']) . ')' : '' ?>
                                </option>
                            <?php endforeach; ?>
                        </optgroup>
                    <?php endif; ?>

                    <optgroup label="🎉 Common Occasions">
                        <option value="Wedding">💒 Wedding</option>
                        <option value="Birthday">🎂 Birthday</option>
                        <option value="Housewarming">🏠 Housewarming (Griha Pravesh)</option>
                        <option value="Engagement">💍 Engagement</option>
                        <option value="Anniversary">🎊 Anniversary</option>
                        <option value="Baby Shower">👶 Baby Shower</option>
                        <option value="Festival">🪔 Festival Celebration</option>
                        <option value="Puja">🙏 Puja / Religious Ceremony</option>
                        <option value="Naming Ceremony">📝 Naming Ceremony</option>
                        <option value="Graduation">🎓 Graduation</option>
                        <option value="Retirement">🏖️ Retirement</option>
                        <option value="Corporate Event">🏢 Corporate Event</option>
                        <option value="Other">✨ Other</option>
                    </optgroup>
                </select>
            </div>

            <!-- Occasion Title -->
            <div class="form-group">
                <label for="occasion_title"><i class="fas fa-heading" style="color: var(--primary);"></i> Occasion Title <span style="color: red;">*</span></label>
                <input type="text" name="occasion_title" id="occasion_title" class="form-control" 
                       placeholder="e.g., Sharma Family Wedding Celebration" required>
            </div>

            <!-- Event Date -->
            <div class="form-group">
                <label for="event_date"><i class="fas fa-calendar-alt" style="color: var(--primary);"></i> Event Date & Time</label>
                <input type="datetime-local" name="event_date" id="event_date" class="form-control">
            </div>

            <!-- Venue -->
            <div class="form-group">
                <label for="venue"><i class="fas fa-map-marker-alt" style="color: var(--primary);"></i> Venue / Location</label>
                <input type="text" name="venue" id="venue" class="form-control" 
                       placeholder="e.g., Grand Ballroom, Hotel Taj Palace, New Delhi">
            </div>

            <!-- Google Maps Link -->
            <div class="form-group">
                <label for="google_maps_link"><i class="fas fa-map" style="color: var(--primary);"></i> Google Maps Link (Optional)</label>
                <input type="url" name="google_maps_link" id="google_maps_link" class="form-control" 
                       placeholder="e.g., https://maps.app.goo.gl/...">
            </div>

            <!-- Host Name -->
            <div class="form-group">
                <label for="host_name"><i class="fas fa-user" style="color: var(--primary);"></i> Host Name <span style="color: red;">*</span></label>
                <input type="text" name="host_name" id="host_name" class="form-control" 
                       value="<?= htmlspecialchars($user['name'] ?? '') ?>"
                       placeholder="Your name or family name" required>
            </div>

            <!-- Expiry Duration -->
            <div class="form-group">
                <label for="expiry_duration"><i class="fas fa-hourglass-half" style="color: var(--primary);"></i> Link Active For <span style="color: red;">*</span></label>
                <select name="expiry_duration" id="expiry_duration" class="form-control" required>
                    <option value="1">1 Day</option>
                    <option value="3">3 Days</option>
                    <option value="7" selected>7 Days</option>
                    <option value="15">15 Days</option>
                    <option value="30">30 Days</option>
                    <option value="90">90 Days</option>
                </select>
            </div>
        </div>

        <!-- Personal Message -->
        <div class="form-group">
            <label for="message"><i class="fas fa-heart" style="color: var(--primary);"></i> Personal Message</label>
            <textarea name="message" id="message" class="form-control" rows="3" 
                      placeholder="Write a personal message for your guests... (e.g., We would be honored to have you join us for our special day!)"></textarea>
        </div>

        <!-- Additional Details -->
        <div class="form-group">
            <label for="additional_details"><i class="fas fa-info-circle" style="color: var(--primary);"></i> Additional Details</label>
            <textarea name="additional_details" id="additional_details" class="form-control" rows="3" 
                      placeholder="Any additional details like dress code, RSVP info, parking instructions, etc."></textarea>
        </div>

        <div style="display: flex; gap: 15px; align-items: center; margin-top: 10px;">
            <button type="submit" class="btn btn-primary" id="submitBtn" style="padding: 14px 32px; font-size: 1rem;">
                <i class="fas fa-magic"></i> Generate Invitation Card
            </button>
            <span id="loadingMsg" style="display: none; color: var(--primary); font-weight: 500;">
                <i class="fas fa-spinner fa-spin"></i> AI is designing your invitation... This may take up to 30 seconds.
            </span>
        </div>
    </form>
</div>

<style>
    .form-control:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(255, 107, 53, 0.1);
    }

    select.form-control {
        appearance: none;
        -webkit-appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%236B7280' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 16px center;
        padding-right: 40px;
    }

    @media (max-width: 768px) {
        div[style*="grid-template-columns: 1fr 1fr"] {
            display: flex !important;
            flex-direction: column;
        }
    }
</style>

<script>
    document.getElementById('invitationForm').addEventListener('submit', function (e) {
        const btn = document.getElementById('submitBtn');
        const msg = document.getElementById('loadingMsg');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generating...';
        btn.style.opacity = '0.7';
        msg.style.display = 'inline';
    });
</script>
