<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-robot"></i> AI Ritual Generator</h3>
        <a href="/admin/rituals" class="btn btn-sm" style="background: #E5E7EB;">
            <i class="fas fa-arrow-left"></i> Back to Rituals
        </a>
    </div>
    
    <div style="padding: 20px; background: #F0FDF4; border-bottom: 1px solid #BBF7D0;">
        <p style="color: #166534; margin: 0;">
            <i class="fas fa-info-circle"></i> 
            <strong>Admin AI Generation:</strong> Generate rituals using AI and save them directly to the global rituals database. 
            After generation, you'll be redirected to the edit page to review and modify the details.
        </p>
    </div>
    
    <form method="POST" action="/admin/rituals/generate" style="padding: 20px;" id="aiGenerateForm">
        <?= \App\Core\Auth::csrfField() ?>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label for="ritual_name"><i class="fas fa-pray"></i> Ritual Name *</label>
                <input type="text" id="ritual_name" name="ritual_name" class="form-control" required
                       placeholder="e.g., Satyanarayan Puja, Griha Pravesh..."
                       value="<?= htmlspecialchars($old['ritual_name'] ?? '') ?>">
            </div>
            
            <div class="form-group">
                <label for="community_name"><i class="fas fa-users"></i> Community/Region</label>
                <input type="text" id="community_name" name="community_name" class="form-control"
                       placeholder="e.g., Bengali, Gujarati, Tamil..."
                       value="<?= htmlspecialchars($old['community_name'] ?? '') ?>">
            </div>
            
            <div class="form-group">
                <label for="religion"><i class="fas fa-om"></i> Religion</label>
                <select id="religion" name="religion" class="form-control">
                    <option value="Hinduism" <?= ($old['religion'] ?? 'Hinduism') === 'Hinduism' ? 'selected' : '' ?>>Hinduism</option>
                    <option value="Buddhism" <?= ($old['religion'] ?? '') === 'Buddhism' ? 'selected' : '' ?>>Buddhism</option>
                    <option value="Jainism" <?= ($old['religion'] ?? '') === 'Jainism' ? 'selected' : '' ?>>Jainism</option>
                    <option value="Sikhism" <?= ($old['religion'] ?? '') === 'Sikhism' ? 'selected' : '' ?>>Sikhism</option>
                    <option value="Other" <?= ($old['religion'] ?? '') === 'Other' ? 'selected' : '' ?>>Other</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="occasion"><i class="fas fa-calendar-alt"></i> Occasion (Optional)</label>
                <input type="text" id="occasion" name="occasion" class="form-control"
                       placeholder="e.g., Wedding, Housewarming, Festival..."
                       value="<?= htmlspecialchars($old['occasion'] ?? '') ?>">
            </div>
        </div>
        
        <div class="form-group" style="margin-top: 20px;">
            <label for="additional_info"><i class="fas fa-info-circle"></i> Additional Instructions (Optional)</label>
            <textarea id="additional_info" name="additional_info" class="form-control" rows="3"
                      placeholder="Any specific details about the ritual you want to generate..."><?= htmlspecialchars($old['additional_info'] ?? '') ?></textarea>
        </div>
        
        <div style="margin-top: 25px; display: flex; gap: 15px;">
            <button type="submit" class="btn btn-success" style="padding: 12px 30px;" id="generateBtn">
                <i class="fas fa-magic"></i> Generate Ritual with AI
            </button>
            <a href="/admin/rituals/create" class="btn" style="background: #E5E7EB; padding: 12px 30px;">
                <i class="fas fa-edit"></i> Create Manually Instead
            </a>
        </div>
    </form>
</div>

<div class="card" style="margin-top: 25px;">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-lightbulb"></i> Tips for Better AI Generation</h3>
    </div>
    <div style="padding: 20px;">
        <ul style="margin: 0; padding-left: 20px; color: #4B5563; line-height: 2;">
            <li>Be specific with the <strong>ritual name</strong> (e.g., "Satyanarayan Puja" rather than just "Puja")</li>
            <li>Include <strong>community/region</strong> for culturally accurate rituals (e.g., "Bengali", "South Indian")</li>
            <li>Mention the <strong>occasion</strong> if it's ritual-specific (e.g., "Wedding ceremony", "Birthday")</li>
            <li>Use <strong>additional instructions</strong> for specific requirements (e.g., "Include morning prayers", "Focus on family rituals")</li>
            <li>After generation, you can <strong>edit all details</strong> including steps and items on the edit page</li>
        </ul>
    </div>
</div>

<!-- Loading Overlay -->
<div id="loadingOverlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); z-index: 9999; justify-content: center; align-items: center;">
    <div style="background: white; padding: 40px 60px; border-radius: 16px; text-align: center; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);">
        <div style="width: 60px; height: 60px; border: 4px solid #E5E7EB; border-top-color: #10B981; border-radius: 50%; animation: spin 1s linear infinite; margin: 0 auto 20px;"></div>
        <h3 style="color: #1F2937; margin-bottom: 10px;"><i class="fas fa-robot"></i> AI is Generating Ritual</h3>
        <p style="color: #6B7280; margin-bottom: 5px;">This may take up to 1-2 minutes...</p>
        <p style="color: #9CA3AF; font-size: 0.85rem;">Please don't close this page</p>
    </div>
</div>

<style>
@keyframes spin {
    to { transform: rotate(360deg); }
}
</style>

<script>
document.getElementById('aiGenerateForm').addEventListener('submit', function(e) {
    // Show loading overlay
    document.getElementById('loadingOverlay').style.display = 'flex';
    
    // Disable the submit button to prevent double submission
    document.getElementById('generateBtn').disabled = true;
    document.getElementById('generateBtn').innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generating...';
});
</script>
