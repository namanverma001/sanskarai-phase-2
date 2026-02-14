<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-calendar-plus"></i> Book Pandit</h3>
    </div>
    
    <div style="display: flex; gap: 20px; margin-bottom: 30px; padding: 20px; background: linear-gradient(135deg, rgba(139, 92, 246, 0.1), rgba(236, 72, 153, 0.1)); border-radius: 12px;">
        <div style="width: 80px; height: 80px; background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 2rem; flex-shrink: 0;">
            <i class="fas fa-user"></i>
        </div>
        <div>
            <h2 style="margin-bottom: 8px;"><?= htmlspecialchars($pandit['name']) ?></h2>
            <p style="color: #6B7280; margin-bottom: 8px;">
                <i class="fas fa-om" style="color: var(--primary);"></i> 
                <?= htmlspecialchars($pandit['specialization'] ?? 'General Puja') ?>
            </p>
            <div style="display: flex; gap: 20px; font-size: 0.9rem; color: #6B7280;">
                <span><i class="fas fa-star" style="color: #F59E0B;"></i> <?= number_format($pandit['average_rating'] ?? 0, 1) ?> Rating</span>
                <span><i class="fas fa-briefcase"></i> <?= $pandit['experience_years'] ?? 0 ?> Years Exp.</span>
                <span><i class="fas fa-check-circle" style="color: #10B981;"></i> <?= $pandit['total_rituals_performed'] ?? 0 ?> Rituals</span>
            </div>
            <?php if (!empty($pandit['languages'])): ?>
            <p style="margin-top: 8px; font-size: 0.9rem; color: #6B7280;">
                <i class="fas fa-language"></i> <?= htmlspecialchars($pandit['languages']) ?>
            </p>
            <?php endif; ?>
        </div>
    </div>
    
    <form method="POST" action="/user/book-pandit" id="bookingForm">
        <?= \App\Core\Auth::csrfField() ?>
        <input type="hidden" name="pandit_id" value="<?= $pandit['id'] ?>">
        
        <div class="form-group">
            <label for="booking_purpose"><i class="fas fa-info-circle"></i> Purpose of Booking <span style="color: #EF4444;">*</span></label>
            <textarea 
                name="booking_purpose" 
                id="booking_purpose" 
                rows="4" 
                required 
                placeholder="Please describe the ritual or ceremony you need. E.g., 'Griha Pravesh puja for our new home', 'Satyanarayan Katha for a family function', 'Wedding ceremony rituals', etc."
                style="resize: vertical;"
            ></textarea>
            <small style="color: #6B7280; display: block; margin-top: 5px;">
                Be specific about your requirements so the pandit can prepare accordingly.
            </small>
        </div>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label for="scheduled_date"><i class="fas fa-calendar"></i> Preferred Date</label>
                <input type="date" name="scheduled_date" id="scheduled_date" min="<?= date('Y-m-d') ?>">
            </div>
            
            <div class="form-group">
                <label for="scheduled_time"><i class="fas fa-clock"></i> Preferred Time</label>
                <input type="time" name="scheduled_time" id="scheduled_time">
            </div>
        </div>
        
        <div class="form-group">
            <label for="venue"><i class="fas fa-map-marker-alt"></i> Venue / Location</label>
            <input type="text" name="venue" id="venue" placeholder="E.g., Home address, Temple name, Community hall, etc.">
        </div>
        
        <div class="form-group">
            <label for="additional_notes"><i class="fas fa-sticky-note"></i> Additional Notes</label>
            <textarea 
                name="additional_notes" 
                id="additional_notes" 
                rows="3" 
                placeholder="Any special requirements, dietary restrictions, or additional information..."
                style="resize: vertical;"
            ></textarea>
        </div>
        
        <div style="display: flex; gap: 15px; margin-top: 25px;">
            <button type="submit" class="btn btn-primary" style="flex: 1;">
                <i class="fas fa-paper-plane"></i> Send Booking Request
            </button>
            <a href="/user/select-pandit" class="btn" style="background: #E5E7EB; color: #374151;">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </form>
</div>

<style>
    .form-group {
        margin-bottom: 20px;
    }
    
    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #374151;
    }
    
    .form-group input,
    .form-group textarea {
        width: 100%;
        padding: 12px 15px;
        border: 2px solid #E5E7EB;
        border-radius: 10px;
        font-size: 1rem;
        transition: all 0.3s ease;
        background: #FAFAFA;
    }
    
    .form-group input:focus,
    .form-group textarea:focus {
        outline: none;
        border-color: var(--primary);
        background: white;
        box-shadow: 0 0 0 4px rgba(139, 92, 246, 0.1);
    }
    
    .form-group textarea::placeholder,
    .form-group input::placeholder {
        color: #9CA3AF;
    }
</style>
