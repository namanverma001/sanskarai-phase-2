<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-moon"></i> Request Muhurat</h3>
        <a href="/user/mohurat-requests" class="btn btn-sm" style="background:#E5E7EB;color:#374151;">
            <i class="fas fa-arrow-left"></i> Back to Requests
        </a>
    </div>

    <div style="background: linear-gradient(135deg, rgba(99,102,241,0.08), rgba(168,85,247,0.08)); border-radius: 12px; padding: 20px; margin-bottom: 25px;">
        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 10px;">
            <div style="width: 48px; height: 48px; background: linear-gradient(135deg, #6366F1, #A855F7); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.4rem;">
                <i class="fas fa-moon"></i>
            </div>
            <div>
                <h4 style="margin: 0; color: #1E1E2E;">Find Your Auspicious Muhurat</h4>
                <p style="margin: 0; color: #6B7280; font-size: 0.9rem;">Submit your details and our pandits will suggest the perfect auspicious time</p>
            </div>
        </div>
    </div>

    <form method="POST" action="/user/mohurat-requests" id="mohuratForm">
        <?= \App\Core\Auth::csrfField() ?>

        <div class="form-group">
            <label for="pandit_id"><i class="fas fa-pray" style="color: var(--primary);"></i> Select Pandit <span style="color: #EF4444;">*</span></label>
            <select name="pandit_id" id="pandit_id" required
                style="width: 100%; padding: 12px 15px; border: 2px solid #E5E7EB; border-radius: 10px; font-size: 1rem; background: #FAFAFA;">
                <option value="">-- Choose a Pandit --</option>
                <?php foreach ($pandits as $pandit): ?>
                <option value="<?= $pandit['id'] ?>">
                    <?= htmlspecialchars($pandit['name']) ?>
                    <?php if (!empty($pandit['specialization'])): ?> — <?= htmlspecialchars($pandit['specialization']) ?><?php endif; ?>
                    <?php if (!empty($pandit['average_rating']) && $pandit['average_rating'] > 0): ?> (⭐ <?= number_format($pandit['average_rating'], 1) ?>)<?php endif; ?>
                </option>
                <?php endforeach; ?>
            </select>
            <small style="color: #6B7280; display: block; margin-top: 5px;">Your muhurat request will be sent to this pandit only</small>
        </div>

        <div class="form-group">
            <label for="ritual_type"><i class="fas fa-om" style="color: var(--primary);"></i> Ritual / Ceremony Type <span style="color: #EF4444;">*</span></label>
            <input type="text" name="ritual_type" id="ritual_type" required
                placeholder="E.g., Griha Pravesh, Vivah, Satyanarayan Katha, Mundan, etc."
                style="width: 100%; padding: 12px 15px; border: 2px solid #E5E7EB; border-radius: 10px; font-size: 1rem; transition: all 0.3s ease; background: #FAFAFA;">
            <small style="color: #6B7280; display: block; margin-top: 5px;">Specify the type of ritual or ceremony for which you need a muhurat</small>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label for="country"><i class="fas fa-globe" style="color: var(--primary);"></i> Country</label>
                <input type="text" name="country" id="country" value="India"
                    placeholder="Country"
                    style="width: 100%; padding: 12px 15px; border: 2px solid #E5E7EB; border-radius: 10px; font-size: 1rem; background: #FAFAFA;">
            </div>
            <div class="form-group">
                <label for="city"><i class="fas fa-city" style="color: var(--primary);"></i> City</label>
                <input type="text" name="city" id="city"
                    placeholder="E.g., Mumbai, Delhi, Varanasi..."
                    style="width: 100%; padding: 12px 15px; border: 2px solid #E5E7EB; border-radius: 10px; font-size: 1rem; background: #FAFAFA;">
            </div>
        </div>

        <div class="form-group">
            <label for="preferred_month"><i class="fas fa-calendar-alt" style="color: var(--primary);"></i> Preferred Month</label>
            <select name="preferred_month" id="preferred_month"
                style="width: 100%; padding: 12px 15px; border: 2px solid #E5E7EB; border-radius: 10px; font-size: 1rem; background: #FAFAFA;">
                <option value="">-- Select Preferred Month --</option>
                <option value="January">January (Paush / Magha)</option>
                <option value="February">February (Magha / Phalguna)</option>
                <option value="March">March (Phalguna / Chaitra)</option>
                <option value="April">April (Chaitra / Vaishakha)</option>
                <option value="May">May (Vaishakha / Jyeshtha)</option>
                <option value="June">June (Jyeshtha / Ashadha)</option>
                <option value="July">July (Ashadha / Shravana)</option>
                <option value="August">August (Shravana / Bhadrapada)</option>
                <option value="September">September (Bhadrapada / Ashwin)</option>
                <option value="October">October (Ashwin / Kartika)</option>
                <option value="November">November (Kartika / Margashirsha)</option>
                <option value="December">December (Margashirsha / Paush)</option>
            </select>
        </div>

        <div class="form-group">
            <label for="family_id"><i class="fas fa-users" style="color: var(--primary);"></i> Select Family (auto-fills Gotra & Nakshatra)</label>
            <select name="family_id" id="family_id"
                onchange="autoFillFamily(this.value)"
                style="width: 100%; padding: 12px 15px; border: 2px solid #E5E7EB; border-radius: 10px; font-size: 1rem; background: #FAFAFA;">
                <option value="">-- No Family Selected --</option>
                <?php foreach ($families as $fam): ?>
                <option value="<?= $fam['id'] ?>"
                    data-gotra="<?= htmlspecialchars($fam['gotra'] ?? '') ?>"
                    data-nakshatra="<?= htmlspecialchars($fam['nakshatra'] ?? '') ?>">
                    <?= htmlspecialchars($fam['family_name']) ?>
                    <?php if (!empty($fam['gotra'])): ?> (<?= htmlspecialchars($fam['gotra']) ?>)<?php endif; ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label for="gotra"><i class="fas fa-seedling" style="color: var(--primary);"></i> Gotra</label>
                <input type="text" name="gotra" id="gotra"
                    placeholder="E.g., Bharadwaj, Kashyap, Vashistha..."
                    style="width: 100%; padding: 12px 15px; border: 2px solid #E5E7EB; border-radius: 10px; font-size: 1rem; background: #FAFAFA;">
            </div>
            <div class="form-group">
                <label for="nakshatra"><i class="fas fa-star" style="color: var(--primary);"></i> Nakshatra</label>
                <input type="text" name="nakshatra" id="nakshatra"
                    placeholder="E.g., Ashwini, Rohini, Mrigashira..."
                    style="width: 100%; padding: 12px 15px; border: 2px solid #E5E7EB; border-radius: 10px; font-size: 1rem; background: #FAFAFA;">
            </div>
        </div>

        <div class="form-group">
            <label><i class="fas fa-sun" style="color: var(--primary);"></i> Time Preference</label>
            <div style="display: flex; gap: 15px; flex-wrap: wrap;">
                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; padding: 12px 20px; border: 2px solid #E5E7EB; border-radius: 10px; transition: all 0.3s;" class="time-radio-label">
                    <input type="radio" name="time_preference" value="morning" style="accent-color: var(--primary);">
                    <i class="fas fa-sun" style="color: #F59E0B;"></i> Morning (Pratah Kaal)
                </label>
                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; padding: 12px 20px; border: 2px solid #E5E7EB; border-radius: 10px; transition: all 0.3s;" class="time-radio-label">
                    <input type="radio" name="time_preference" value="evening" style="accent-color: var(--primary);">
                    <i class="fas fa-moon" style="color: #6366F1;"></i> Evening (Sayam Kaal)
                </label>
                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; padding: 12px 20px; border: 2px solid #E5E7EB; border-radius: 10px; transition: all 0.3s;" class="time-radio-label">
                    <input type="radio" name="time_preference" value="any" checked style="accent-color: var(--primary);">
                    <i class="fas fa-clock" style="color: #10B981;"></i> Any Time
                </label>
            </div>
        </div>

        <div class="form-group">
            <label for="additional_notes"><i class="fas fa-sticky-note" style="color: var(--primary);"></i> Additional Notes</label>
            <textarea name="additional_notes" id="additional_notes" rows="3"
                placeholder="Any special requirements, family traditions, or additional information for the pandit..."
                style="width: 100%; padding: 12px 15px; border: 2px solid #E5E7EB; border-radius: 10px; font-size: 1rem; background: #FAFAFA; resize: vertical;"></textarea>
        </div>

        <div style="display: flex; gap: 15px; margin-top: 25px;">
            <button type="submit" class="btn btn-primary" style="flex: 1;">
                <i class="fas fa-paper-plane"></i> Submit Muhurat Request
            </button>
            <a href="/user/mohurat-requests" class="btn" style="background: #E5E7EB; color: #374151;">
                <i class="fas fa-times"></i> Cancel
            </a>
        </div>
    </form>
</div>

<style>
    .form-group input:focus,
    .form-group textarea:focus,
    .form-group select:focus {
        outline: none;
        border-color: var(--primary) !important;
        background: white !important;
        box-shadow: 0 0 0 4px rgba(139, 92, 246, 0.1);
    }

    .time-radio-label:has(input:checked) {
        border-color: var(--primary) !important;
        background: rgba(139, 92, 246, 0.05);
    }
</style>

<script>
function autoFillFamily(familyId) {
    const select = document.getElementById('family_id');
    const gotraInput = document.getElementById('gotra');
    const nakshatraInput = document.getElementById('nakshatra');

    if (!familyId) {
        gotraInput.value = '';
        nakshatraInput.value = '';
        return;
    }

    const option = select.querySelector('option[value="' + familyId + '"]');
    if (option) {
        const gotra = option.getAttribute('data-gotra');
        const nakshatra = option.getAttribute('data-nakshatra');
        if (gotra) gotraInput.value = gotra;
        if (nakshatra) nakshatraInput.value = nakshatra;
    }
}
</script>
