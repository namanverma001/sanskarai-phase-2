<style>
    .explore-header {
        background: linear-gradient(135deg, var(--primary) 0%, #FF8C42 100%);
        border-radius: 20px;
        padding: 40px;
        color: white;
        margin-bottom: 30px;
        position: relative;
        overflow: hidden;
    }

    .explore-header::before {
        content: '🙏';
        position: absolute;
        right: 30px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 6rem;
        opacity: 0.2;
    }

    .explore-header h1 {
        font-size: 2rem;
        margin-bottom: 10px;
    }

    .explore-header p {
        opacity: 0.9;
        font-size: 1rem;
    }

    .search-form-card {
        background: white;
        border-radius: 20px;
        padding: 30px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
        margin-bottom: 30px;
    }

    .search-form-card h3 {
        margin-bottom: 25px;
        color: var(--dark);
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .search-form-card h3 i {
        color: var(--primary);
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 25px;
    }

    .form-field {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .form-field label {
        font-weight: 500;
        color: #374151;
        font-size: 0.9rem;
    }

    .form-field input,
    .form-field select,
    .form-field textarea {
        padding: 12px 16px;
        border: 2px solid #E5E7EB;
        border-radius: 10px;
        font-size: 1rem;
        transition: all 0.3s;
    }

    .form-field input:focus,
    .form-field select:focus,
    .form-field textarea:focus {
        border-color: var(--primary);
        outline: none;
        box-shadow: 0 0 0 3px rgba(255, 107, 53, 0.1);
    }

    /* Combobox style for input with datalist */
    .community-combobox {
        position: relative;
    }

    .community-combobox input {
        width: 100%;
        padding: 12px 40px 12px 16px;
        border: 2px solid #E5E7EB;
        border-radius: 10px;
        font-size: 1rem;
        transition: all 0.3s;
        background-color: white;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%236B7280' d='M2 4l4 4 4-4'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 14px center;
        cursor: text;
    }

    .community-combobox input:focus {
        border-color: var(--primary);
        outline: none;
        box-shadow: 0 0 0 3px rgba(255, 107, 53, 0.1);
    }

    .community-dropdown {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: white;
        border: 2px solid #E5E7EB;
        border-top: none;
        border-radius: 0 0 10px 10px;
        max-height: 200px;
        overflow-y: auto;
        z-index: 100;
        display: none;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .community-dropdown.show {
        display: block;
    }

    .community-dropdown .dropdown-item {
        padding: 12px 16px;
        cursor: pointer;
        transition: background 0.2s;
        font-size: 1rem;
        color: #374151;
    }

    .community-dropdown .dropdown-item:hover,
    .community-dropdown .dropdown-item.highlighted {
        background: linear-gradient(135deg, rgba(255, 107, 53, 0.1) 0%, rgba(255, 140, 66, 0.1) 100%);
        color: var(--primary);
    }

    .community-dropdown .no-results {
        padding: 12px 16px;
        color: #9CA3AF;
        font-style: italic;
    }

    .search-actions {
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
    }

    .btn-search {
        background: linear-gradient(135deg, var(--primary) 0%, #FF8C42 100%);
        color: white;
        padding: 14px 30px;
        border: none;
        border-radius: 10px;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        transition: all 0.3s;
    }

    .btn-search:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(255, 107, 53, 0.3);
    }

    .btn-generate {
        background: linear-gradient(135deg, #10B981 0%, #059669 100%);
        color: white;
        padding: 14px 30px;
        border: none;
        border-radius: 10px;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        transition: all 0.3s;
    }

    .btn-generate:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(16, 185, 129, 0.3);
    }

    .results-section {
        margin-top: 30px;
    }

    .results-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .results-header h3 {
        color: var(--dark);
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .results-count {
        background: #F3F4F6;
        padding: 6px 15px;
        border-radius: 20px;
        font-size: 0.85rem;
        color: #6B7280;
    }

    .rituals-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 25px;
    }

    .ritual-card {
        background: white;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        transition: all 0.3s;
        border: 2px solid transparent;
    }

    .ritual-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
        border-color: var(--primary);
    }

    .ritual-card-header {
        background: linear-gradient(135deg, #FEF3E2 0%, #FFF7ED 100%);
        padding: 20px;
        border-bottom: 1px solid #FED7AA;
    }

    .ritual-card-header h4 {
        font-size: 1.15rem;
        color: var(--dark);
        margin-bottom: 5px;
    }

    .ritual-card-header .sanskrit {
        font-size: 0.85rem;
        color: #92400E;
        font-style: italic;
    }

    .ritual-card-body {
        padding: 20px;
    }

    .ritual-meta-row {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        margin-bottom: 12px;
    }

    .ritual-meta-tag {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 10px;
        border-radius: 15px;
        font-size: 0.75rem;
        font-weight: 500;
    }

    .tag-category {
        background: #FFEDD5;
        color: #9A3412;
    }

    .tag-difficulty-easy {
        background: #D1FAE5;
        color: #065F46;
    }

    .tag-difficulty-medium {
        background: #FEF3C7;
        color: #92400E;
    }

    .tag-difficulty-hard {
        background: #FEE2E2;
        color: #991B1B;
    }

    .tag-duration {
        background: #E0E7FF;
        color: #3730A3;
    }

    .tag-community {
        background: #DBEAFE;
        color: #1E40AF;
    }

    .ritual-description {
        color: #6B7280;
        font-size: 0.9rem;
        line-height: 1.6;
        margin-bottom: 15px;
    }

    .ritual-card-actions {
        display: flex;
        gap: 10px;
    }

    .btn-view {
        flex: 1;
        background: var(--primary);
        color: white;
        border: none;
        padding: 10px;
        border-radius: 8px;
        font-size: 0.85rem;
        cursor: pointer;
        text-align: center;
        text-decoration: none;
        transition: all 0.3s;
    }

    .btn-view:hover {
        background: var(--primary-dark);
    }

    .btn-add {
        background: #10B981;
        color: white;
        border: none;
        padding: 10px 15px;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s;
    }

    .btn-add:hover {
        background: #059669;
    }

    /* Generated Ritual Preview */
    .generated-preview {
        background: linear-gradient(135deg, #D1FAE5 0%, #A7F3D0 100%);
        border-radius: 20px;
        padding: 30px;
        margin-top: 30px;
        border: 2px solid #10B981;
    }

    .generated-preview h3 {
        color: #065F46;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .generated-content {
        background: white;
        border-radius: 15px;
        padding: 25px;
    }

    /* Source Badge */
    .source-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 3px 10px;
        border-radius: 12px;
        font-size: 0.7rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .source-badge.global {
        background: linear-gradient(135deg, #E0E7FF, #C7D2FE);
        color: #3730A3;
    }
    .source-badge.my-ritual {
        background: linear-gradient(135deg, #D1FAE5, #A7F3D0);
        color: #065F46;
    }

    /* ===== FEEDBACK LOOP UI ===== */
    .feedback-section {
        margin-top: 25px;
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        border-radius: 20px;
        padding: 30px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
    }

    .feedback-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 20px;
        flex-wrap: wrap;
        gap: 15px;
    }

    .feedback-header h4 {
        font-size: 1.1rem;
        color: #1F2937;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .round-badge {
        background: linear-gradient(135deg, #F59E0B 0%, #D97706 100%);
        color: white;
        padding: 5px 14px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        animation: pulse-glow 2s ease-in-out infinite;
    }

    @keyframes pulse-glow {
        0%, 100% { box-shadow: 0 0 5px rgba(245, 158, 11, 0.3); }
        50% { box-shadow: 0 0 20px rgba(245, 158, 11, 0.5); }
    }

    .feedback-actions {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }

    .btn-accept {
        background: linear-gradient(135deg, #10B981 0%, #059669 100%);
        color: white;
        border: none;
        padding: 14px 30px;
        border-radius: 12px;
        font-size: 1rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
    }

    .btn-accept:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(16, 185, 129, 0.4);
    }

    .btn-feedback {
        background: linear-gradient(135deg, #6366F1 0%, #4F46E5 100%);
        color: white;
        border: none;
        padding: 14px 30px;
        border-radius: 12px;
        font-size: 1rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 4px 15px rgba(99, 102, 241, 0.3);
    }

    .btn-feedback:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(99, 102, 241, 0.4);
    }

    .feedback-form {
        display: none;
        margin-top: 20px;
        animation: slideDown 0.4s ease;
    }

    .feedback-form.active {
        display: block;
    }

    @keyframes slideDown {
        from { opacity: 0; transform: translateY(-15px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .feedback-textarea {
        width: 100%;
        min-height: 120px;
        padding: 16px 20px;
        border: 2px solid #E5E7EB;
        border-radius: 14px;
        font-size: 1rem;
        font-family: inherit;
        resize: vertical;
        transition: all 0.3s;
        background: #F9FAFB;
    }

    .feedback-textarea:focus {
        border-color: #6366F1;
        outline: none;
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
        background: white;
    }

    .feedback-textarea::placeholder {
        color: #9CA3AF;
    }

    .feedback-hint {
        margin-top: 8px;
        font-size: 0.82rem;
        color: #6B7280;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .btn-regenerate {
        background: linear-gradient(135deg, #F59E0B 0%, #D97706 100%);
        color: white;
        border: none;
        padding: 14px 35px;
        border-radius: 12px;
        font-size: 1rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin-top: 15px;
        box-shadow: 0 4px 15px rgba(245, 158, 11, 0.3);
    }

    .btn-regenerate:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(245, 158, 11, 0.4);
    }

    .btn-regenerate:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }

    .feedback-history {
        margin-top: 15px;
        padding: 15px 20px;
        background: linear-gradient(135deg, #FEF3C7, #FDE68A);
        border-radius: 12px;
        border-left: 4px solid #F59E0B;
    }

    .feedback-history h5 {
        color: #92400E;
        font-size: 0.85rem;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .feedback-history-item {
        color: #78350F;
        font-size: 0.82rem;
        padding: 4px 0;
        border-bottom: 1px dashed rgba(120, 53, 15, 0.15);
    }

    .feedback-history-item:last-child {
        border-bottom: none;
    }

    /* Loading State */
    .loading-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1000;
        justify-content: center;
        align-items: center;
    }

    .loading-overlay.active {
        display: flex;
    }

    .loading-box {
        background: white;
        border-radius: 20px;
        padding: 40px;
        text-align: center;
        max-width: 400px;
    }

    .loading-spinner {
        width: 60px;
        height: 60px;
        border: 4px solid #E5E7EB;
        border-top-color: var(--primary);
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin: 0 auto 20px;
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }

    /* Popular Section */
    .popular-section {
        margin-top: 40px;
    }

    .section-title {
        font-size: 1.3rem;
        color: var(--dark);
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .section-title i {
        color: var(--accent);
    }

    /* Toast Notification */
    .toast {
        position: fixed;
        bottom: 30px;
        right: 30px;
        background: #1E1E2E;
        color: white;
        padding: 15px 25px;
        border-radius: 10px;
        display: none;
        z-index: 1001;
        animation: slideIn 0.3s ease;
    }

    .toast.success {
        background: #10B981;
    }

    .toast.error {
        background: #EF4444;
    }

    .toast.show {
        display: block;
    }

    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }

        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
</style>

<div class="explore-header">
    <h1><i class="fas fa-search"></i> Explore Rituals</h1>
    <p>Search our database or let AI generate authentic rituals for your tradition</p>
</div>

<?php $showAll = isset($_GET['all']) && $_GET['all'] == '1'; ?>

<?php if (!empty($userCommunity) && !$showAll): ?>
<div style="background: linear-gradient(135deg, #D1FAE5 0%, #A7F3D0 100%); border: 2px solid #10B981; border-radius: 12px; padding: 15px 20px; margin-bottom: 20px; display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap;">
    <div style="display: flex; align-items: center; gap: 12px;">
        <i class="fas fa-users" style="font-size: 1.3rem; color: #065F46;"></i>
        <div>
            <strong style="color: #065F46;">Showing rituals for your community: <?= htmlspecialchars($userCommunity) ?></strong>
            <p style="margin: 0; font-size: 0.85rem; color: #047857;">Rituals matching your community (including similar spellings) and universal rituals are displayed.</p>
        </div>
    </div>
    <a href="/user/rituals?all=1" style="background: linear-gradient(135deg, #6366F1 0%, #4F46E5 100%); color: white; padding: 10px 20px; border-radius: 8px; font-size: 0.9rem; font-weight: 600; text-decoration: none; white-space: nowrap; transition: all 0.3s; display: inline-flex; align-items: center; gap: 8px;">
        <i class="fas fa-globe"></i> View All Rituals
    </a>
</div>
<?php elseif (!empty($userCommunity) && $showAll): ?>
<div style="background: linear-gradient(135deg, #E0E7FF 0%, #C7D2FE 100%); border: 2px solid #6366F1; border-radius: 12px; padding: 15px 20px; margin-bottom: 20px; display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap;">
    <div style="display: flex; align-items: center; gap: 12px;">
        <i class="fas fa-globe" style="font-size: 1.3rem; color: #3730A3;"></i>
        <div>
            <strong style="color: #3730A3;">Showing all rituals from all communities</strong>
            <p style="margin: 0; font-size: 0.85rem; color: #4338CA;">You are viewing the complete ritual library.</p>
        </div>
    </div>
    <a href="/user/rituals" style="background: linear-gradient(135deg, #10B981 0%, #059669 100%); color: white; padding: 10px 20px; border-radius: 8px; font-size: 0.9rem; font-weight: 600; text-decoration: none; white-space: nowrap; transition: all 0.3s; display: inline-flex; align-items: center; gap: 8px;">
        <i class="fas fa-users"></i> Show My Community
    </a>
</div>
<?php endif; ?>

<div class="search-form-card">
    <h3><i class="fas fa-filter"></i> Search or Generate Ritual</h3>

    <form id="searchForm">
        <?= \App\Core\Auth::csrfField() ?>
        <div class="form-grid">
            <div class="form-field">
                <label for="community_name"><i class="fas fa-users"></i> Community Name</label>
                <div class="community-combobox">
                    <input
                        type="text"
                        id="community_name"
                        name="community_name"
                        placeholder="Select or type community..."
                        value="<?= htmlspecialchars($userCommunity ?? '') ?>"
                        autocomplete="off"
                    >
                    <div class="community-dropdown" id="communityDropdown">
                        <?php if (!empty($topCommunities)): ?>
                            <?php foreach ($topCommunities as $community): ?>
                                <div class="dropdown-item" data-value="<?= htmlspecialchars($community['community_name']) ?>">
                                    <?= htmlspecialchars($community['community_name']) ?>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="form-field">
                <label for="religion"><i class="fas fa-om"></i> Religion</label>
                <select
                    id="religion"
                    name="religion"
                >
                    <option value="">Select Religion</option>
                    <option
                        value="Hinduism"
                        selected
                    >Hinduism</option>
                    <option value="Buddhism">Buddhism</option>
                    <option value="Jainism">Jainism</option>
                    <option value="Sikhism">Sikhism</option>
                    <option value="Christian">Christian</option>
                    <option value="Other">Other</option>
                </select>
            </div>

            <div class="form-field">
                <label for="ritual_name"><i class="fas fa-pray"></i> Ritual Name</label>
                <div class="community-combobox">
                    <input
                        type="text"
                        id="ritual_name"
                        name="ritual_name"
                        placeholder="e.g., Satyanarayan Puja, Griha Pravesh..."
                        autocomplete="off"
                    >
                    <div class="community-dropdown" id="ritualDropdown">
                        <?php if (!empty($topRitualNames)): ?>
                            <?php foreach ($topRitualNames as $ritual): ?>
                                <div class="dropdown-item" data-value="<?= htmlspecialchars($ritual['ritual_name']) ?>">
                                    <?= htmlspecialchars($ritual['ritual_name']) ?>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="form-field">
                <label for="occasion"><i class="fas fa-calendar-alt"></i> Occasion (Optional)</label>
                <input
                    type="text"
                    id="occasion"
                    name="occasion"
                    placeholder="e.g., Wedding, Housewarming, Festival..."
                >
            </div>
        </div>

        <div
            class="form-field"
            style="margin-bottom: 25px;"
        >
            <label for="additional_info"><i class="fas fa-info-circle"></i> Additional Information (Optional)</label>
            <textarea
                id="additional_info"
                name="additional_info"
                rows="2"
                placeholder="Any specific details about the ritual you're looking for..."
            ></textarea>
        </div>

        <div class="search-actions">
            <button
                type="button"
                class="btn-search"
                onclick="findRitual()"
                style="padding: 14px 40px;"
            >
                <i class="fas fa-search"></i> Find Ritual
            </button>
        </div>
    </form>
</div>

<div
    id="searchResults"
    class="results-section"
    style="display: none;"
>
    <div class="results-header">
        <h3><i class="fas fa-list"></i> Search Results</h3>
        <span
            class="results-count"
            id="resultsCount"
        >0 rituals found</span>
    </div>
    <div
        id="resultsGrid"
        class="rituals-grid"
    >
        <!-- Results will be populated here -->
    </div>
</div>

<div
    id="generatedResult"
    class="generated-preview"
    style="display: none;"
>
    <h3><i class="fas fa-sparkles"></i> AI Generated Ritual</h3>
    <div
        id="generatedContent"
        class="generated-content"
    >
        <!-- Generated ritual will be shown here -->
    </div>

    <!-- Feedback Loop Section -->
    <div class="feedback-section" id="feedbackSection">
        <div class="feedback-header">
            <h4>
                <i class="fas fa-comment-dots" style="color: #6366F1;"></i>
                Is this ritual accurate?
            </h4>
            <span class="round-badge" id="roundBadge" style="display: none;">
                <i class="fas fa-sync-alt"></i>
                <span id="roundText">Round 1</span>
            </span>
        </div>

        <div class="feedback-actions">
            <button class="btn-accept" onclick="acceptGeneratedRitual()" id="btnAccept">
                <i class="fas fa-check-circle"></i> Accept Response
            </button>
            <button class="btn-feedback" onclick="toggleFeedbackForm()" id="btnGiveFeedback">
                <i class="fas fa-edit"></i> Give Feedback
            </button>
        </div>

        <!-- Feedback History (previous rounds) -->
        <div id="feedbackHistory" style="display: none;"></div>

        <!-- Feedback Form -->
        <div class="feedback-form" id="feedbackForm">
            <textarea
                class="feedback-textarea"
                id="feedbackText"
                placeholder="Tell us what needs to change... &#10;&#10;Examples:&#10;• 'Is step me aise nahi hota, pehle X karna chahiye tha'&#10;• 'Mantra galat hai, sahi mantra ye hai...'&#10;• 'Ye step missing hai, add karo...'&#10;• 'Duration zyada hai, 30 min me ho jata hai'"
            ></textarea>
            <p class="feedback-hint">
                <i class="fas fa-lightbulb" style="color: #F59E0B;"></i>
                Be specific — AI will use your exact feedback to improve the ritual
            </p>
            <button class="btn-regenerate" onclick="regenerateWithFeedback()" id="btnRegenerate">
                <i class="fas fa-magic"></i> Regenerate Ritual
            </button>
        </div>
    </div>
</div>

<div class="popular-section">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h3 class="section-title" style="margin-bottom: 0;"><i class="fas fa-book-open"></i> Browse All Rituals</h3>
        <span id="ritualCountDisplay" style="color: #6B7280; font-size: 0.9rem;">
            Showing <span id="currentCount"><?= count($popularRituals ?? []) ?></span> of <span id="totalCount"><?= $totalRitualCount ?? count($popularRituals ?? []) ?></span> rituals
        </span>
    </div>
    <div class="rituals-grid" id="ritualsGrid">
        <?php foreach ($popularRituals ?? [] as $ritual): ?>
            <div class="ritual-card">
                <div class="ritual-card-header">
                    <h4>
                        <?= htmlspecialchars($ritual['name']) ?>
                    </h4>
                    <?php if ($ritual['name_sanskrit']): ?>
                        <span class="sanskrit">
                            <?= htmlspecialchars($ritual['name_sanskrit']) ?>
                        </span>
                    <?php endif; ?>
                </div>
                <div class="ritual-card-body">
                    <div class="ritual-meta-row">
                        <span class="ritual-meta-tag tag-category">
                            <i class="fas fa-tag"></i>
                            <?= htmlspecialchars($ritual['category']) ?>
                        </span>
                        <span class="ritual-meta-tag tag-difficulty-<?= $ritual['difficulty'] ?>">
                            <?= ucfirst($ritual['difficulty']) ?>
                        </span>
                        <span class="ritual-meta-tag tag-duration">
                            <i class="fas fa-clock"></i>
                            <?= $ritual['duration_minutes'] ?> min
                        </span>
                        <?php if (!empty($ritual['community_name'])): ?>
                            <span class="ritual-meta-tag tag-community">
                                <i class="fas fa-users"></i>
                                <?= htmlspecialchars($ritual['community_name']) ?>
                            </span>
                        <?php endif; ?>
                    </div>
                    <p class="ritual-description">
                        <?= htmlspecialchars(substr($ritual['description'] ?? 'Traditional ritual with detailed steps and guidance.', 0, 100)) ?>...
                    </p>
                    <div class="ritual-card-actions">
                        <a
                            href="/user/rituals/<?= $ritual['id'] ?>"
                            class="btn-view"
                        >
                            <i class="fas fa-eye"></i> View Details
                        </a>
                        <button
                            class="btn-add"
                            onclick="addToMyRituals(<?= $ritual['id'] ?>)"
                            title="Add to My Rituals"
                        >
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    
    <!-- Load More Button -->
    <div id="loadMoreContainer" style="text-align: center; margin-top: 30px; <?= ($totalRitualCount ?? 0) <= count($popularRituals ?? []) ? 'display: none;' : '' ?>">
        <button onclick="loadMoreRituals()" id="loadMoreBtn" class="btn-search" style="padding: 14px 40px; background: linear-gradient(135deg, #6B7280 0%, #4B5563 100%);">
            <i class="fas fa-plus-circle"></i> Load More Rituals
        </button>
    </div>
</div>

<!-- Loading Overlay -->
<div
    id="loadingOverlay"
    class="loading-overlay"
>
    <div class="loading-box">
        <div class="loading-spinner"></div>
        <h3 id="loadingText">Searching rituals...</h3>
        <p style="color: #6B7280;">Please wait</p>
    </div>
</div>

<!-- Toast Notification -->
<div
    id="toast"
    class="toast"
></div>

<script>
    const csrfToken = '<?= \App\Core\Auth::csrfToken() ?>';

    // Community combobox functionality
    (function() {
        const input = document.getElementById('community_name');
        const dropdown = document.getElementById('communityDropdown');
        const items = dropdown.querySelectorAll('.dropdown-item');
        const allCommunities = Array.from(items).map(item => item.dataset.value);
        let highlightedIndex = -1;
        let justSelected = false;

        // Show dropdown on focus
        input.addEventListener('focus', function() {
            if (justSelected) {
                justSelected = false;
                return;
            }
            filterAndShowDropdown();
        });

        // Filter on input
        input.addEventListener('input', function() {
            filterAndShowDropdown();
        });

        // Handle keyboard navigation
        input.addEventListener('keydown', function(e) {
            const visibleItems = dropdown.querySelectorAll('.dropdown-item:not([style*="display: none"])');
            
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                highlightedIndex = Math.min(highlightedIndex + 1, visibleItems.length - 1);
                updateHighlight(visibleItems);
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                highlightedIndex = Math.max(highlightedIndex - 1, 0);
                updateHighlight(visibleItems);
            } else if (e.key === 'Enter') {
                e.preventDefault();
                if (highlightedIndex >= 0 && visibleItems[highlightedIndex]) {
                    selectItem(visibleItems[highlightedIndex]);
                }
            } else if (e.key === 'Escape') {
                dropdown.classList.remove('show');
            }
        });

        // Click on dropdown item
        items.forEach(item => {
            item.addEventListener('click', function() {
                selectItem(this);
            });
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.community-combobox')) {
                dropdown.classList.remove('show');
            }
        });

        function filterAndShowDropdown() {
            const filter = input.value.toLowerCase();
            let hasVisible = false;
            highlightedIndex = -1;

            items.forEach(item => {
                const text = item.dataset.value.toLowerCase();
                if (text.includes(filter)) {
                    item.style.display = 'block';
                    hasVisible = true;
                } else {
                    item.style.display = 'none';
                }
                item.classList.remove('highlighted');
            });

            if (hasVisible) {
                dropdown.classList.add('show');
            } else {
                dropdown.classList.remove('show');
            }
        }

        function updateHighlight(visibleItems) {
            visibleItems.forEach((item, index) => {
                if (index === highlightedIndex) {
                    item.classList.add('highlighted');
                    item.scrollIntoView({ block: 'nearest' });
                } else {
                    item.classList.remove('highlighted');
                }
            });
        }

        function selectItem(item) {
            justSelected = true;
            input.value = item.dataset.value;
            dropdown.classList.remove('show');
            input.blur();
        }
    })();

    // Ritual name combobox functionality (with filtering)
    (function() {
        const input = document.getElementById('ritual_name');
        const dropdown = document.getElementById('ritualDropdown');
        const items = dropdown.querySelectorAll('.dropdown-item');
        let highlightedIndex = -1;
        let justSelected = false;

        // Show filtered dropdown on focus
        input.addEventListener('focus', function() {
            if (justSelected) {
                justSelected = false;
                return;
            }
            filterAndShowDropdown();
        });

        // Filter on input
        input.addEventListener('input', function() {
            filterAndShowDropdown();
        });

        // Handle keyboard navigation
        input.addEventListener('keydown', function(e) {
            const visibleItems = dropdown.querySelectorAll('.dropdown-item:not([style*="display: none"])');
            
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                highlightedIndex = Math.min(highlightedIndex + 1, visibleItems.length - 1);
                updateHighlight(visibleItems);
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                highlightedIndex = Math.max(highlightedIndex - 1, 0);
                updateHighlight(visibleItems);
            } else if (e.key === 'Enter') {
                e.preventDefault();
                if (highlightedIndex >= 0 && visibleItems[highlightedIndex]) {
                    selectItem(visibleItems[highlightedIndex]);
                }
            } else if (e.key === 'Escape') {
                dropdown.classList.remove('show');
            }
        });

        // Click on dropdown item
        items.forEach(item => {
            item.addEventListener('click', function() {
                selectItem(this);
            });
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('#ritual_name') && !e.target.closest('#ritualDropdown')) {
                dropdown.classList.remove('show');
            }
        });

        function filterAndShowDropdown() {
            const filter = input.value.toLowerCase();
            let hasVisible = false;
            highlightedIndex = -1;

            items.forEach(item => {
                const text = item.dataset.value.toLowerCase();
                if (text.includes(filter)) {
                    item.style.display = 'block';
                    hasVisible = true;
                } else {
                    item.style.display = 'none';
                }
                item.classList.remove('highlighted');
            });

            if (hasVisible) {
                dropdown.classList.add('show');
            } else {
                dropdown.classList.remove('show');
            }
        }

        function updateHighlight(visibleItems) {
            visibleItems.forEach((item, index) => {
                if (index === highlightedIndex) {
                    item.classList.add('highlighted');
                    item.scrollIntoView({ block: 'nearest' });
                } else {
                    item.classList.remove('highlighted');
                }
            });
        }

        function selectItem(item) {
            justSelected = true;
            input.value = item.dataset.value;
            dropdown.classList.remove('show');
            input.blur();
        }
    })();

    function showLoading(text = 'Loading...') {
        document.getElementById('loadingText').textContent = text;
        document.getElementById('loadingOverlay').classList.add('active');
    }

    function hideLoading() {
        document.getElementById('loadingOverlay').classList.remove('active');
    }

    function showToast(message, type = 'success') {
        const toast = document.getElementById('toast');
        toast.textContent = message;
        toast.className = `toast ${type} show`;
        setTimeout(() => {
            toast.classList.remove('show');
        }, 3000);
    }

    // === Feedback Loop State ===
    let generationSessionId = '';
    let currentRound = 1;
    let feedbackHistoryList = [];

    async function findRitual() {
        const form = document.getElementById('searchForm');
        const formData = new FormData(form);
        const params = new URLSearchParams(formData);

        const ritualName = document.getElementById('ritual_name').value.trim();
        if (!ritualName) {
            showToast('Please enter a ritual name', 'error');
            return;
        }

        // Reset feedback state for new search
        generationSessionId = '';
        currentRound = 1;
        feedbackHistoryList = [];

        showLoading('Searching database & your rituals...');

        try {
            // Step 1: Search in database (global + My Rituals)
            const searchResponse = await fetch('/user/rituals/search?' + params.toString());
            const searchData = await searchResponse.json();

            if (searchData.success && searchData.rituals && searchData.rituals.length > 0) {
                hideLoading();
                displaySearchResults(searchData.rituals, searchData.my_rituals_count || 0, searchData.global_count || 0);
                showToast(`Found ${searchData.rituals.length} ritual(s)!`, 'success');
                return;
            }

            // Step 2: Not found - Generate with AI
            showLoading('Not found in database. AI is generating...');
            
            formData.append('csrf_token', csrfToken);

            const generateResponse = await fetch('/user/rituals/generate', {
                method: 'POST',
                body: formData
            });
            const generateData = await generateResponse.json();

            hideLoading();

            if (generateData.success) {
                // Initialize session for feedback tracking
                generationSessionId = generateData.session_id || crypto.randomUUID().replace(/-/g, '').substring(0, 32);
                currentRound = 1;
                feedbackHistoryList = [];
                displayGeneratedRitual(generateData.ritual);
                showToast('Ritual generated with AI! Review and accept or refine.', 'success');
            } else {
                showToast(generateData.error || 'Generation failed', 'error');
            }
        } catch (error) {
            hideLoading();
            showToast('Error: ' + error.message, 'error');
        }
    }

    function displaySearchResults(rituals, myCount, globalCount) {
        const resultsSection = document.getElementById('searchResults');
        const resultsGrid = document.getElementById('resultsGrid');
        const resultsCount = document.getElementById('resultsCount');

        let countText = `${rituals.length} ritual${rituals.length !== 1 ? 's' : ''} found`;
        if (myCount > 0 && globalCount > 0) {
            countText += ` (${myCount} from My Rituals, ${globalCount} from Global)`;
        }
        resultsCount.textContent = countText;

        if (rituals.length === 0) {
            resultsGrid.innerHTML = `
            <div style="grid-column: 1/-1; text-align: center; padding: 40px; color: #6B7280;">
                <i class="fas fa-search" style="font-size: 3rem; margin-bottom: 15px; opacity: 0.3;"></i>
                <p>No rituals found. Try different search terms or <strong>Generate with AI</strong>!</p>
            </div>
        `;
        } else {
            resultsGrid.innerHTML = rituals.map(ritual => {
                const isMyRitual = ritual.source_type === 'my_ritual';
                const viewUrl = isMyRitual ? `/user/my-rituals/${ritual.id}` : `/user/rituals/${ritual.id}`;
                const sourceBadge = isMyRitual 
                    ? `<span class="source-badge my-ritual"><i class="fas fa-folder-open"></i> My Ritual</span>` 
                    : `<span class="source-badge global"><i class="fas fa-globe"></i> Global</span>`;

                return `
                <div class="ritual-card">
                    <div class="ritual-card-header">
                        <div style="display: flex; justify-content: space-between; align-items: start;">
                            <div>
                                <h4>${escapeHtml(ritual.name)}</h4>
                                ${ritual.name_sanskrit ? `<span class="sanskrit">${escapeHtml(ritual.name_sanskrit)}</span>` : ''}
                            </div>
                            ${sourceBadge}
                        </div>
                    </div>
                    <div class="ritual-card-body">
                        <div class="ritual-meta-row">
                            <span class="ritual-meta-tag tag-category">
                                <i class="fas fa-tag"></i> ${escapeHtml(ritual.category || 'General')}
                            </span>
                            <span class="ritual-meta-tag tag-difficulty-${ritual.difficulty || 'medium'}">
                                ${(ritual.difficulty || 'medium').charAt(0).toUpperCase() + (ritual.difficulty || 'medium').slice(1)}
                            </span>
                            <span class="ritual-meta-tag tag-duration">
                                <i class="fas fa-clock"></i> ${ritual.duration_minutes || 60} min
                            </span>
                            ${ritual.community_name ? `<span class="ritual-meta-tag tag-community">
                                <i class="fas fa-users"></i> ${escapeHtml(ritual.community_name)}
                            </span>` : ''}
                        </div>
                        <p class="ritual-description">
                            ${escapeHtml((ritual.description || 'Traditional ritual with detailed steps.').substring(0, 100))}...
                        </p>
                        <div class="ritual-card-actions">
                            <a href="${viewUrl}" class="btn-view">
                                <i class="fas fa-eye"></i> View Details
                            </a>
                            ${!isMyRitual ? `<button class="btn-add" onclick="addToMyRituals(${ritual.id})" title="Add to My Rituals">
                                <i class="fas fa-plus"></i>
                            </button>` : ''}
                        </div>
                    </div>
                </div>
            `}).join('');
        }

        resultsSection.style.display = 'block';
        document.getElementById('generatedResult').style.display = 'none';
        resultsSection.scrollIntoView({ behavior: 'smooth' });
    }

    function displayGeneratedRitual(ritual) {
        window.generatedRitualData = ritual;
        window.generatedRitualAdded = false;
        
        const generatedSection = document.getElementById('generatedResult');
        const generatedContent = document.getElementById('generatedContent');

        const stepsHtml = (ritual.steps || []).map((step, i) => `
        <div style="display: flex; gap: 15px; margin-bottom: 15px; padding: 15px; background: #F9FAFB; border-radius: 10px;">
            <div style="width: 35px; height: 35px; background: var(--primary); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-weight: 600;">
                ${step.step_number || i + 1}
            </div>
            <div>
                <strong>${escapeHtml(step.title)}</strong>
                ${step.description ? `<p style="color: #6B7280; font-size: 0.9rem; margin-top: 5px;">${escapeHtml(step.description)}</p>` : ''}
                ${step.mantra ? `<p style="background: #FEF3C7; padding: 8px; border-radius: 5px; margin-top: 8px; font-style: italic;"><strong>Mantra:</strong> ${escapeHtml(step.mantra)}</p>` : ''}
            </div>
        </div>
    `).join('');

        const itemsHtml = (ritual.items || []).map(item => `
        <li style="padding: 8px 0; border-bottom: 1px solid #E5E7EB;">
            <strong>${escapeHtml(item.item_name)}</strong> 
            ${item.item_name_local ? `(${escapeHtml(item.item_name_local)})` : ''} 
            - ${item.quantity} ${item.unit}
            ${item.is_mandatory ? '<span class="badge badge-danger" style="font-size: 0.65rem; margin-left: 5px;">Required</span>' : ''}
        </li>
    `).join('');

        generatedContent.innerHTML = `
        <div style="margin-bottom: 20px;">
            <h2 style="font-size: 1.5rem; color: var(--dark);">${escapeHtml(ritual.name)}</h2>
            ${ritual.name_sanskrit ? `<p style="color: #92400E; font-style: italic;">${escapeHtml(ritual.name_sanskrit)}</p>` : ''}
        </div>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)); gap: 15px; margin-bottom: 25px; padding: 20px; background: #F9FAFB; border-radius: 12px;">
            <div style="text-align: center;">
                <i class="fas fa-clock" style="font-size: 1.5rem; color: var(--primary);"></i>
                <p style="font-weight: 600; margin-top: 5px;">${ritual.duration_minutes || 60} min</p>
                <p style="color: #6B7280; font-size: 0.8rem;">Duration</p>
            </div>
            <div style="text-align: center;">
                <i class="fas fa-signal" style="font-size: 1.5rem; color: var(--primary);"></i>
                <p style="font-weight: 600; margin-top: 5px;">${(ritual.difficulty || 'medium').charAt(0).toUpperCase() + (ritual.difficulty || 'medium').slice(1)}</p>
                <p style="color: #6B7280; font-size: 0.8rem;">Difficulty</p>
            </div>
            ${ritual.deity ? `
            <div style="text-align: center;">
                <i class="fas fa-pray" style="font-size: 1.5rem; color: var(--primary);"></i>
                <p style="font-weight: 600; margin-top: 5px;">${escapeHtml(ritual.deity)}</p>
                <p style="color: #6B7280; font-size: 0.8rem;">Deity</p>
            </div>
            ` : ''}
            ${ritual.community_name ? `
            <div style="text-align: center;">
                <i class="fas fa-users" style="font-size: 1.5rem; color: var(--primary);"></i>
                <p style="font-weight: 600; margin-top: 5px;">${escapeHtml(ritual.community_name)}</p>
                <p style="color: #6B7280; font-size: 0.8rem;">Community</p>
            </div>
            ` : ''}
        </div>
        
        ${ritual.description ? `
        <div style="margin-bottom: 25px;">
            <h4 style="margin-bottom: 10px;"><i class="fas fa-info-circle"></i> Description</h4>
            <p style="color: #4B5563; line-height: 1.7;">${escapeHtml(ritual.description)}</p>
        </div>
        ` : ''}
        
        ${ritual.significance ? `
        <div style="margin-bottom: 25px;">
            <h4 style="margin-bottom: 10px;"><i class="fas fa-star"></i> Significance</h4>
            <p style="color: #4B5563; line-height: 1.7;">${escapeHtml(ritual.significance)}</p>
        </div>
        ` : ''}
        
        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 25px;">
            <div>
                <h4 style="margin-bottom: 15px;"><i class="fas fa-list-ol"></i> Steps (${(ritual.steps || []).length})</h4>
                ${stepsHtml || '<p style="color: #6B7280;">No steps defined</p>'}
            </div>
            
            <div>
                <h4 style="margin-bottom: 15px;"><i class="fas fa-shopping-basket"></i> Required Items (${(ritual.items || []).length})</h4>
                <ul style="list-style: none; padding: 0;">
                    ${itemsHtml || '<li style="color: #6B7280;">No items defined</li>'}
                </ul>
            </div>
        </div>
    `;

        // Update round badge
        const roundBadge = document.getElementById('roundBadge');
        if (currentRound > 1) {
            roundBadge.style.display = 'inline-flex';
            document.getElementById('roundText').textContent = `Round ${currentRound}`;
        } else {
            roundBadge.style.display = 'none';
        }

        // Update feedback history display
        updateFeedbackHistory();

        // Reset feedback form
        document.getElementById('feedbackForm').classList.remove('active');
        document.getElementById('feedbackText').value = '';

        generatedSection.style.display = 'block';
        document.getElementById('searchResults').style.display = 'none';
        generatedSection.scrollIntoView({ behavior: 'smooth' });
    }

    function toggleFeedbackForm() {
        const form = document.getElementById('feedbackForm');
        form.classList.toggle('active');
        if (form.classList.contains('active')) {
            document.getElementById('feedbackText').focus();
        }
    }

    function updateFeedbackHistory() {
        const historyDiv = document.getElementById('feedbackHistory');
        if (feedbackHistoryList.length === 0) {
            historyDiv.style.display = 'none';
            return;
        }

        historyDiv.style.display = 'block';
        historyDiv.innerHTML = `
            <div class="feedback-history">
                <h5><i class="fas fa-history"></i> Previous Feedback (${feedbackHistoryList.length} round${feedbackHistoryList.length !== 1 ? 's' : ''})</h5>
                ${feedbackHistoryList.map((fb, i) => `
                    <div class="feedback-history-item">
                        <strong>Round ${i + 1}:</strong> ${escapeHtml(fb)}
                    </div>
                `).join('')}
            </div>
        `;
    }

    async function regenerateWithFeedback() {
        const feedbackText = document.getElementById('feedbackText').value.trim();
        if (!feedbackText) {
            showToast('Please write your feedback first', 'error');
            document.getElementById('feedbackText').focus();
            return;
        }

        if (!window.generatedRitualData) {
            showToast('No ritual to refine', 'error');
            return;
        }

        // Disable the regenerate button
        const btn = document.getElementById('btnRegenerate');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Regenerating...';

        showLoading('Regenerating ritual with your feedback...');

        try {
            const form = document.getElementById('searchForm');
            const formData = new FormData(form);
            formData.append('csrf_token', csrfToken);
            formData.append('previous_response', JSON.stringify(window.generatedRitualData));
            formData.append('user_feedback', feedbackText);
            formData.append('session_id', generationSessionId);
            formData.append('round_number', currentRound);

            const response = await fetch('/user/rituals/regenerate', {
                method: 'POST',
                body: formData
            });
            const data = await response.json();

            hideLoading();

            if (data.success) {
                // Track feedback history
                feedbackHistoryList.push(feedbackText);

                // Update session state
                generationSessionId = data.session_id || generationSessionId;
                currentRound = data.round_number || (currentRound + 1);

                // Display regenerated ritual
                displayGeneratedRitual(data.ritual);
                showToast(`Ritual regenerated (Round ${currentRound})! Review the improvements.`, 'success');
            } else {
                showToast(data.error || 'Regeneration failed', 'error');
            }
        } catch (error) {
            hideLoading();
            showToast('Error: ' + error.message, 'error');
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-magic"></i> Regenerate Ritual';
        }
    }

    async function acceptGeneratedRitual() {
        if (!window.generatedRitualData) {
            showToast('No ritual data available', 'error');
            return;
        }

        if (window.generatedRitualAdded) {
            showToast('This ritual is already in your collection!', 'info');
            return;
        }

        const formData = new FormData();
        formData.append('csrf_token', csrfToken);
        formData.append('ritual_data', JSON.stringify(window.generatedRitualData));
        formData.append('prompt', document.getElementById('ritual_name').value);
        formData.append('session_id', generationSessionId);

        showLoading('Accepting and saving to your collection...');

        try {
            const response = await fetch('/user/rituals/accept-ai', {
                method: 'POST',
                body: formData
            });
            const data = await response.json();

            hideLoading();

            if (data.success) {
                window.generatedRitualAdded = true;
                showToast('🎉 Ritual accepted and added to your collection!', 'success');
                setTimeout(() => {
                    window.location.href = '/user/my-rituals/' + data.user_ritual_id;
                }, 1500);
            } else {
                showToast(data.error || 'Failed to save ritual', 'error');
            }
        } catch (error) {
            hideLoading();
            showToast('Error: ' + error.message, 'error');
        }
    }

    async function addToMyRituals(globalRitualId) {
        const formData = new FormData();
        formData.append('csrf_token', csrfToken);
        formData.append('global_ritual_id', globalRitualId);

        try {
            const response = await fetch('/user/my-rituals/add', {
                method: 'POST',
                body: formData
            });
            const data = await response.json();

            if (data.success) {
                showToast('Ritual added to your collection!', 'success');
            } else if (data.already_added) {
                showToast('This ritual is already in your collection!', 'info');
            } else {
                showToast(data.error || 'Failed to add ritual', 'error');
            }
        } catch (error) {
            showToast('Error: ' + error.message, 'error');
        }
    }

    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Load More Rituals functionality
    let currentOffset = <?= count($popularRituals ?? []) ?>;
    const ritualsPerPage = 6;

    async function loadMoreRituals() {
        const loadMoreBtn = document.getElementById('loadMoreBtn');
        const originalText = loadMoreBtn.innerHTML;
        loadMoreBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';
        loadMoreBtn.disabled = true;

        try {
            const formData = new FormData();
            formData.append('csrf_token', csrfToken);
            formData.append('offset', currentOffset);
            formData.append('limit', ritualsPerPage);

            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('all') === '1') {
                formData.append('show_all', '1');
            }

            const response = await fetch('/user/rituals/load-more', {
                method: 'POST',
                body: formData
            });
            const data = await response.json();

            if (data.success && data.rituals.length > 0) {
                const grid = document.getElementById('ritualsGrid');
                
                data.rituals.forEach(ritual => {
                    const card = createRitualCard(ritual);
                    grid.insertAdjacentHTML('beforeend', card);
                });

                currentOffset += data.rituals.length;
                document.getElementById('currentCount').textContent = currentOffset;

                if (currentOffset >= data.total || data.rituals.length < ritualsPerPage) {
                    document.getElementById('loadMoreContainer').style.display = 'none';
                }
            } else if (data.rituals.length === 0) {
                document.getElementById('loadMoreContainer').style.display = 'none';
                showToast('No more rituals to load', 'info');
            } else {
                showToast(data.error || 'Failed to load rituals', 'error');
            }
        } catch (error) {
            showToast('Error loading rituals: ' + error.message, 'error');
        } finally {
            loadMoreBtn.innerHTML = originalText;
            loadMoreBtn.disabled = false;
        }
    }

    function createRitualCard(ritual) {
        const description = ritual.description || 'Traditional ritual with detailed steps and guidance.';
        const truncatedDesc = description.length > 100 ? description.substring(0, 100) + '...' : description;
        
        return `
            <div class="ritual-card">
                <div class="ritual-card-header">
                    <h4>${escapeHtml(ritual.name)}</h4>
                    ${ritual.name_sanskrit ? `<span class="sanskrit">${escapeHtml(ritual.name_sanskrit)}</span>` : ''}
                </div>
                <div class="ritual-card-body">
                    <div class="ritual-meta-row">
                        <span class="ritual-meta-tag tag-category">
                            <i class="fas fa-tag"></i>
                            ${escapeHtml(ritual.category)}
                        </span>
                        <span class="ritual-meta-tag tag-difficulty-${ritual.difficulty}">
                            ${ritual.difficulty.charAt(0).toUpperCase() + ritual.difficulty.slice(1)}
                        </span>
                        <span class="ritual-meta-tag tag-duration">
                            <i class="fas fa-clock"></i>
                            ${ritual.duration_minutes} min
                        </span>
                        ${ritual.community_name ? `<span class="ritual-meta-tag tag-community">
                            <i class="fas fa-users"></i>
                            ${escapeHtml(ritual.community_name)}
                        </span>` : ''}
                    </div>
                    <p class="ritual-description">${escapeHtml(truncatedDesc)}</p>
                    <div class="ritual-card-actions">
                        <a href="/user/rituals/${ritual.id}" class="btn-view">
                            <i class="fas fa-eye"></i> View Details
                        </a>
                        <button class="btn-add" onclick="addToMyRituals(${ritual.id})" title="Add to My Rituals">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
    }
</script>