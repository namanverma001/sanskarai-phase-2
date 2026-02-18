<style>
    .ritual-progress-header {
        background: linear-gradient(135deg, var(--primary) 0%, #FF8C42 100%);
        border-radius: 20px;
        padding: 30px;
        color: white;
        margin-bottom: 30px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .ritual-progress-header h1 {
        font-size: 1.5rem;
        margin-bottom: 5px;
    }

    .progress-indicator {
        text-align: right;
    }

    .progress-indicator .progress-text {
        font-size: 0.9rem;
        opacity: 0.9;
        margin-bottom: 8px;
    }

    .progress-bar {
        width: 200px;
        height: 10px;
        background: rgba(255, 255, 255, 0.3);
        border-radius: 10px;
        overflow: hidden;
    }

    .progress-fill {
        height: 100%;
        background: white;
        border-radius: 10px;
        transition: width 0.5s ease;
    }

    .steps-container {
        display: flex;
        gap: 30px;
    }

    .steps-sidebar {
        width: 280px;
        flex-shrink: 0;
    }

    .steps-main {
        flex: 1;
        min-width: 0;
    }

    @media (max-width: 1024px) {
        .steps-container {
            flex-direction: column;
        }

        .steps-sidebar {
            width: 100%;
            order: 2;
        }

        .steps-main {
            order: 1;
        }
    }

    .step-list {
        background: white;
        border-radius: 16px;
        padding: 20px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
    }

    .step-list h4 {
        margin-bottom: 15px;
        color: var(--dark);
    }

    .step-list-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s;
        margin-bottom: 8px;
    }

    .step-list-item:hover {
        background: #F9FAFB;
    }

    .step-list-item.active {
        background: #FFEDD5;
        border: 2px solid var(--primary);
    }

    .step-list-item.completed {
        background: #D1FAE5;
    }

    .step-list-item.completed .step-num {
        background: #10B981;
    }

    .step-num {
        width: 32px;
        height: 32px;
        background: #E5E7EB;
        color: var(--dark);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 0.85rem;
        flex-shrink: 0;
    }

    .step-list-item.active .step-num {
        background: var(--primary);
        color: white;
    }

    .step-list-name {
        flex: 1;
        font-size: 0.9rem;
    }

    .step-check {
        color: #10B981;
        font-size: 1.2rem;
    }

    .current-step {
        background: white;
        border-radius: 20px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        overflow: hidden;
    }

    .current-step-header {
        background: linear-gradient(135deg, #FEF3E2 0%, #FFF7ED 100%);
        padding: 25px;
        border-bottom: 2px solid #FED7AA;
    }

    .step-badge {
        display: inline-block;
        background: var(--primary);
        color: white;
        padding: 4px 12px;
        border-radius: 15px;
        font-size: 0.8rem;
        font-weight: 600;
        margin-bottom: 10px;
    }

    .current-step-header h2 {
        font-size: 1.4rem;
        color: var(--dark);
        margin-bottom: 5px;
    }

    .current-step-header .sanskrit {
        color: #92400E;
        font-style: italic;
    }

    .current-step-body {
        padding: 30px;
    }

    .step-description {
        color: #4B5563;
        line-height: 1.8;
        font-size: 1.05rem;
        margin-bottom: 25px;
    }

    .step-mantra-box {
        background: linear-gradient(135deg, #FEF3C7 0%, #FDE68A 100%);
        border: 2px solid #F59E0B;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 25px;
    }

    .step-mantra-box h5 {
        color: #92400E;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .step-mantra-box .mantra-text {
        font-size: 1.2rem;
        color: #78350F;
        font-style: italic;
        margin-bottom: 10px;
    }

    .step-mantra-box .mantra-meaning {
        color: #92400E;
        font-size: 0.9rem;
    }

    .step-instructions {
        background: #D1FAE5;
        border-radius: 10px;
        padding: 15px;
        margin-bottom: 25px;
        display: flex;
        gap: 10px;
        align-items: flex-start;
    }

    .step-instructions i {
        color: #059669;
        margin-top: 2px;
    }

    .step-instructions p {
        color: #065F46;
        flex: 1;
    }

    .step-items {
        background: #F3F4F6;
        border-radius: 10px;
        padding: 15px;
        margin-bottom: 25px;
    }

    .step-items h5 {
        margin-bottom: 10px;
        color: var(--dark);
    }

    .step-items p {
        color: #6B7280;
    }

    .step-actions {
        display: flex;
        gap: 15px;
        margin-top: 30px;
    }

    .btn-prev,
    .btn-next {
        padding: 14px 30px;
        border: none;
        border-radius: 10px;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 10px;
        transition: all 0.3s;
    }

    .btn-prev {
        background: #E5E7EB;
        color: var(--dark);
    }

    .btn-next {
        background: linear-gradient(135deg, #10B981 0%, #059669 100%);
        color: white;
        flex: 1;
        justify-content: center;
    }

    .btn-next:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(16, 185, 129, 0.3);
    }

    .btn-complete {
        background: linear-gradient(135deg, var(--primary) 0%, #FF8C42 100%);
        color: white;
        flex: 1;
        justify-content: center;
        padding: 16px 30px;
        border: none;
        border-radius: 10px;
        font-size: 1.1rem;
        font-weight: 700;
        cursor: pointer;
    }

    /* Floating Chatbot */
    .chatbot-toggle {
        position: fixed;
        bottom: 30px;
        right: 30px;
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, #8B5CF6 0%, #6D28D9 100%);
        border: none;
        border-radius: 50%;
        color: white;
        font-size: 1.5rem;
        cursor: pointer;
        box-shadow: 0 8px 30px rgba(139, 92, 246, 0.4);
        z-index: 999;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .chatbot-toggle:hover {
        transform: scale(1.1);
    }

    .chatbot-toggle.has-notification::after {
        content: '';
        position: absolute;
        top: 5px;
        right: 5px;
        width: 12px;
        height: 12px;
        background: #EF4444;
        border-radius: 50%;
        border: 2px solid white;
    }

    .chatbot-panel {
        position: fixed;
        bottom: 100px;
        right: 30px;
        width: 380px;
        max-height: 500px;
        background: white;
        border-radius: 20px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
        z-index: 1000;
        display: none;
        flex-direction: column;
        overflow: hidden;
    }

    .chatbot-panel.active {
        display: flex;
    }

    .chatbot-header {
        background: linear-gradient(135deg, #8B5CF6 0%, #6D28D9 100%);
        color: white;
        padding: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .chatbot-header h4 {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .chatbot-header h4 i {
        font-size: 1.2rem;
    }

    .chatbot-close {
        background: none;
        border: none;
        color: white;
        font-size: 1.5rem;
        cursor: pointer;
        opacity: 0.8;
    }

    .chatbot-close:hover {
        opacity: 1;
    }

    .chatbot-messages {
        flex: 1;
        padding: 20px;
        overflow-y: auto;
        max-height: 300px;
    }

    .chat-message {
        margin-bottom: 15px;
        display: flex;
        gap: 10px;
    }

    .chat-message.bot {
        flex-direction: row;
    }

    .chat-message.user {
        flex-direction: row-reverse;
    }

    .chat-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 0.9rem;
    }

    .chat-message.bot .chat-avatar {
        background: linear-gradient(135deg, #8B5CF6 0%, #6D28D9 100%);
        color: white;
    }

    .chat-message.user .chat-avatar {
        background: var(--primary);
        color: white;
    }

    .chat-bubble {
        padding: 12px 16px;
        border-radius: 15px;
        max-width: 75%;
        line-height: 1.5;
    }

    .chat-message.bot .chat-bubble {
        background: #F3F4F6;
        color: var(--dark);
        border-bottom-left-radius: 5px;
    }

    .chat-message.user .chat-bubble {
        background: linear-gradient(135deg, var(--primary) 0%, #FF8C42 100%);
        color: white;
        border-bottom-right-radius: 5px;
    }

    .chatbot-input {
        padding: 15px;
        border-top: 1px solid #E5E7EB;
        display: flex;
        gap: 10px;
    }

    .chatbot-input input {
        flex: 1;
        padding: 12px 16px;
        border: 2px solid #E5E7EB;
        border-radius: 25px;
        font-size: 0.95rem;
    }

    .chatbot-input input:focus {
        outline: none;
        border-color: #8B5CF6;
    }

    .chatbot-input button {
        width: 45px;
        height: 45px;
        background: linear-gradient(135deg, #8B5CF6 0%, #6D28D9 100%);
        border: none;
        border-radius: 50%;
        color: white;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .chatbot-input button:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .typing-indicator {
        display: flex;
        gap: 5px;
        padding: 10px;
    }

    .typing-indicator span {
        width: 8px;
        height: 8px;
        background: #9CA3AF;
        border-radius: 50%;
        animation: typing 1s infinite ease-in-out;
    }

    .typing-indicator span:nth-child(2) {
        animation-delay: 0.2s;
    }

    .typing-indicator span:nth-child(3) {
        animation-delay: 0.4s;
    }

    @keyframes typing {

        0%,
        100% {
            transform: translateY(0);
        }

        50% {
            transform: translateY(-5px);
        }
    }

    /* Completion Modal */
    .completion-modal {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.7);
        z-index: 2000;
        display: none;
        justify-content: center;
        align-items: center;
    }

    .completion-modal.active {
        display: flex;
    }

    .completion-content {
        background: white;
        border-radius: 20px;
        padding: 50px;
        text-align: center;
        max-width: 400px;
    }

    .completion-icon {
        font-size: 5rem;
        margin-bottom: 20px;
    }

    .completion-content h2 {
        color: var(--dark);
        margin-bottom: 15px;
    }

    .completion-content p {
        color: #6B7280;
        margin-bottom: 25px;
    }
</style>

<div class="ritual-progress-header">
    <div>
        <h1><i class="fas fa-pray"></i> <?= htmlspecialchars($ritual['name']) ?></h1>
        <p style="opacity: 0.9;">Follow each step mindfully</p>
    </div>
    <div class="progress-indicator">
        <p class="progress-text">Step <span id="currentStepNum">1</span> of <?= count($ritual['steps']) ?></p>
        <div class="progress-bar">
            <div
                class="progress-fill"
                id="progressFill"
                style="width: <?= 100 / max(1, count($ritual['steps'])) ?>%"
            ></div>
        </div>
    </div>
</div>

<div class="steps-container">
    <div class="steps-sidebar">
        <div class="step-list">
            <h4><i class="fas fa-list"></i> All Steps</h4>
            <?php foreach ($ritual['steps'] as $index => $step): ?>
                <div
                    class="step-list-item <?= $index === 0 ? 'active' : '' ?>"
                    data-step="<?= $index ?>"
                    onclick="goToStep(<?= $index ?>)"
                >
                    <span class="step-num"><?= $step['step_number'] ?></span>
                    <span
                        class="step-list-name"><?= htmlspecialchars(substr($step['title'], 0, 30)) ?><?= strlen($step['title']) > 30 ? '...' : '' ?></span>
                    <i
                        class="fas fa-check step-check"
                        style="display: none;"
                    ></i>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="steps-main">
        <div class="current-step">
            <?php foreach ($ritual['steps'] as $index => $step): ?>
                <div
                    class="step-content-item"
                    id="stepContent<?= $index ?>"
                    style="<?= $index !== 0 ? 'display: none;' : '' ?>"
                >
                    <div class="current-step-header">
                        <span class="step-badge">Step <?= $step['step_number'] ?></span>
                        <h2><?= htmlspecialchars($step['title']) ?></h2>
                        <?php if ($step['title_sanskrit']): ?>
                            <p class="sanskrit"><?= htmlspecialchars($step['title_sanskrit']) ?></p>
                        <?php endif; ?>
                    </div>
                    <div class="current-step-body">
                        <?php if ($step['description']): ?>
                            <p class="step-description"><?= nl2br(htmlspecialchars($step['description'])) ?></p>
                        <?php endif; ?>

                        <?php if ($step['mantra']): ?>
                            <div class="step-mantra-box">
                                <h5>Mantra</h5>
                                <p class="mantra-text"><?= htmlspecialchars($step['mantra']) ?></p>
                                <?php if ($step['mantra_meaning']): ?>
                                    <p class="mantra-meaning"><strong>Meaning:</strong>
                                        <?= htmlspecialchars($step['mantra_meaning']) ?></p>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($step['special_instructions']): ?>
                            <div class="step-instructions">
                                <i class="fas fa-info-circle"></i>
                                <p><?= htmlspecialchars($step['special_instructions']) ?></p>
                            </div>
                        <?php endif; ?>

                        <?php if ($step['items_needed']): ?>
                            <div class="step-items">
                                <h5><i class="fas fa-shopping-basket"></i> Items Needed for This Step</h5>
                                <p><?= htmlspecialchars($step['items_needed']) ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>

            <div style="padding: 0 30px 30px;">
                <div class="step-actions">
                    <button
                        class="btn-prev"
                        id="prevBtn"
                        onclick="prevStep()"
                        style="display: none;"
                    >
                        <i class="fas fa-arrow-left"></i> Previous
                    </button>
                    <button
                        class="btn-next"
                        id="nextBtn"
                        onclick="completeAndNext()"
                    >
                        Mark Complete & Next <i class="fas fa-arrow-right"></i>
                    </button>
                    <button
                        class="btn-complete"
                        id="completeBtn"
                        onclick="completeRitual()"
                        style="display: none;"
                    >
                        <i class="fas fa-check-circle"></i> Complete Ritual 🙏
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Floating Chatbot Button -->
<button
    class="chatbot-toggle"
    id="chatbotToggle"
    onclick="toggleChatbot()"
>
    <i class="fas fa-robot"></i>
</button>

<!-- Chatbot Panel -->
<div
    class="chatbot-panel"
    id="chatbotPanel"
>
    <div class="chatbot-header">
        <h4><i class="fas fa-robot"></i> Ritual Assistant</h4>
        <button
            class="chatbot-close"
            onclick="toggleChatbot()"
        >&times;</button>
    </div>
    <div
        class="chatbot-messages"
        id="chatMessages"
    >
        <div class="chat-message bot">
            <div class="chat-avatar"><i class="fas fa-robot"></i></div>
            <div class="chat-bubble">
                Namaste! 🙏 I'm here to help you with the <strong><?= htmlspecialchars($ritual['name']) ?></strong>.
                Ask me anything - about the steps, items, alternatives, or meaning of mantras!
            </div>
        </div>
    </div>
    <div class="chatbot-input">
        <input
            type="text"
            id="chatInput"
            placeholder="Ask a question..."
            onkeypress="if(event.key==='Enter') sendMessage()"
        >
        <button
            onclick="sendMessage()"
            id="chatSendBtn"
        >
            <i class="fas fa-paper-plane"></i>
        </button>
    </div>
</div>

<!-- Completion Modal -->
<div
    class="completion-modal"
    id="completionModal"
>
    <div class="completion-content">
        <div class="completion-icon">🙏</div>
        <h2>Ritual Completed!</h2>
        <p>May this ritual bring you peace, prosperity, and divine blessings.</p>
        <a
            href="/user/my-rituals"
            class="btn btn-primary"
            style="width: 100%;"
        >
            <i class="fas fa-home"></i> Back to My Rituals
        </a>
    </div>
</div>

<script>
    const csrfToken = '<?= \App\Core\Auth::csrfToken() ?>';
    const sessionId = '<?= $sessionId ?>';
    const ritualId = <?= $ritual['id'] ?>;
    const totalSteps = <?= count($ritual['steps']) ?>;
    const steps = <?= json_encode($ritual['steps']) ?>;

    let currentStep = 0;
    let completedSteps = new Set();

    function updateUI() {
        // Update progress
        const progress = ((completedSteps.size) / totalSteps) * 100;
        document.getElementById('progressFill').style.width = progress + '%';
        document.getElementById('currentStepNum').textContent = currentStep + 1;

        // Update step list
        document.querySelectorAll('.step-list-item').forEach((item, index) => {
            item.classList.remove('active');
            if (index === currentStep) {
                item.classList.add('active');
            }
            if (completedSteps.has(index)) {
                item.classList.add('completed');
                item.querySelector('.step-check').style.display = 'block';
            }
        });

        // Show/hide step content
        document.querySelectorAll('.step-content-item').forEach((item, index) => {
            item.style.display = index === currentStep ? 'block' : 'none';
        });

        // Show/hide buttons
        document.getElementById('prevBtn').style.display = currentStep > 0 ? 'flex' : 'none';

        if (currentStep === totalSteps - 1) {
            document.getElementById('nextBtn').style.display = 'none';
            document.getElementById('completeBtn').style.display = 'flex';
        } else {
            document.getElementById('nextBtn').style.display = 'flex';
            document.getElementById('completeBtn').style.display = 'none';
        }
    }

    function goToStep(stepIndex) {
        currentStep = stepIndex;
        updateUI();
    }

    function prevStep() {
        if (currentStep > 0) {
            currentStep--;
            updateUI();
        }
    }

    async function completeAndNext() {
        // Mark current step as complete
        completedSteps.add(currentStep);

        // Send to server
        const formData = new FormData();
        formData.append('csrf_token', csrfToken);
        formData.append('session_id', sessionId);
        formData.append('step_number', steps[currentStep].step_number);

        try {
            await fetch('/user/ritual/complete-step', {
                method: 'POST',
                body: formData
            });
        } catch (e) {
            console.error('Failed to save step completion:', e);
        }

        // Move to next step
        if (currentStep < totalSteps - 1) {
            currentStep++;
        }

        updateUI();
    }

    async function completeRitual() {
        // Mark final step as complete
        completedSteps.add(currentStep);

        const formData = new FormData();
        formData.append('csrf_token', csrfToken);
        formData.append('session_id', sessionId);

        try {
            await fetch('/user/ritual/complete', {
                method: 'POST',
                body: formData
            });
        } catch (e) {
            console.error('Failed to complete ritual:', e);
        }

        // Show completion modal
        document.getElementById('completionModal').classList.add('active');
    }

    // Chatbot
    let isChatOpen = false;

    function toggleChatbot() {
        isChatOpen = !isChatOpen;
        document.getElementById('chatbotPanel').classList.toggle('active', isChatOpen);
        if (isChatOpen) {
            document.getElementById('chatInput').focus();
        }
    }

    async function sendMessage() {
        const input = document.getElementById('chatInput');
        const message = input.value.trim();

        if (!message) return;

        // Add user message
        addChatMessage(message, 'user');
        input.value = '';

        // Disable input
        input.disabled = true;
        document.getElementById('chatSendBtn').disabled = true;

        // Show typing indicator
        const typingId = showTyping();

        try {
            const formData = new FormData();
            formData.append('csrf_token', csrfToken);
            formData.append('ritual_id', ritualId);
            formData.append('step_number', steps[currentStep].step_number);
            formData.append('question', message);

            const response = await fetch('/user/ritual/chat', {
                method: 'POST',
                body: formData
            });
            const data = await response.json();

            removeTyping(typingId);

            if (data.success) {
                addChatMessage(data.answer, 'bot');
            } else {
                addChatMessage('Sorry, I couldn\'t process your question. Please try again.', 'bot');
            }
        } catch (error) {
            removeTyping(typingId);
            addChatMessage('Sorry, there was an error. Please try again.', 'bot');
        }

        // Re-enable input
        input.disabled = false;
        document.getElementById('chatSendBtn').disabled = false;
        input.focus();
    }

    function addChatMessage(text, type) {
        const messagesDiv = document.getElementById('chatMessages');
        const icon = type === 'bot' ? 'fa-robot' : 'fa-user';

        // Convert newlines to <br>
        const formattedText = text.replace(/\n/g, '<br>');

        const messageHtml = `
        <div class="chat-message ${type}">
            <div class="chat-avatar"><i class="fas ${icon}"></i></div>
            <div class="chat-bubble">${formattedText}</div>
        </div>
    `;

        messagesDiv.insertAdjacentHTML('beforeend', messageHtml);
        messagesDiv.scrollTop = messagesDiv.scrollHeight;
    }

    function showTyping() {
        const messagesDiv = document.getElementById('chatMessages');
        const id = 'typing-' + Date.now();

        const html = `
        <div class="chat-message bot" id="${id}">
            <div class="chat-avatar"><i class="fas fa-robot"></i></div>
            <div class="chat-bubble">
                <div class="typing-indicator">
                    <span></span><span></span><span></span>
                </div>
            </div>
        </div>
    `;

        messagesDiv.insertAdjacentHTML('beforeend', html);
        messagesDiv.scrollTop = messagesDiv.scrollHeight;
        return id;
    }

    function removeTyping(id) {
        const element = document.getElementById(id);
        if (element) element.remove();
    }

    // Initialize
    updateUI();
</script>