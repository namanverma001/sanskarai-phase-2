<?php
// AI Pandit View - Premium Chat UI
// Sessions are available via $sessions variable
$csrfToken = \App\Core\Auth::csrfToken();
?>

<!-- Subscription Status Banner -->
<?php if (isset($subscription) && $subscription): ?>
<div class="subscription-banner d-flex align-items-center justify-content-between mb-3 p-3 rounded" style="background: linear-gradient(135deg, #10B981, #059669); color: white;">
    <div class="d-flex align-items-center gap-2">
        <i class="fas fa-crown"></i>
        <span><strong><?= htmlspecialchars($subscription['plan_name']) ?></strong> - <?= $daysRemaining ?> days remaining</span>
    </div>
    <a href="/user/my-subscription" class="btn btn-sm btn-light">Manage</a>
</div>
<?php endif; ?>

<style>
/* ============ AI Pandit Chat Container ============ */
.pandit-chat-wrapper {
    display: flex;
    height: calc(100vh - 120px);
    min-height: 620px;
    background: white;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 8px 40px rgba(0, 0, 0, 0.08);
    position: relative;
}

/* ============ History Sidebar ============ */
.chat-history-panel {
    width: 280px;
    background: linear-gradient(180deg, #1A1A2E 0%, #16213E 100%);
    color: white;
    display: flex;
    flex-direction: column;
    border-right: 1px solid rgba(255, 255, 255, 0.1);
    transition: transform 0.3s ease;
    z-index: 10;
}

.history-header {
    padding: 20px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
}

.history-header h3 {
    font-size: 1rem;
    font-weight: 600;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 8px;
}

.new-chat-btn {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 8px 14px;
    background: linear-gradient(135deg, #FF6B35, #F59E0B);
    border: none;
    border-radius: 10px;
    color: white;
    font-size: 0.8rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    font-family: inherit;
    white-space: nowrap;
}

.new-chat-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 15px rgba(255, 107, 53, 0.4);
}

.history-list {
    flex: 1;
    overflow-y: auto;
    padding: 10px;
    scrollbar-width: thin;
    scrollbar-color: rgba(255, 255, 255, 0.2) transparent;
}

.history-list::-webkit-scrollbar {
    width: 4px;
}

.history-list::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.2);
    border-radius: 2px;
}

.history-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 14px;
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.2s ease;
    margin-bottom: 4px;
    gap: 8px;
}

.history-item:hover {
    background: rgba(255, 153, 51, 0.15);
}

.history-item.active {
    background: rgba(255, 107, 53, 0.25);
    border: 1px solid rgba(255, 107, 53, 0.3);
}

.history-item-info {
    flex: 1;
    min-width: 0;
}

.history-item-title {
    font-size: 0.85rem;
    font-weight: 500;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    color: rgba(255, 255, 255, 0.9);
}

.history-item-date {
    font-size: 0.7rem;
    color: rgba(255, 255, 255, 0.4);
    margin-top: 3px;
}

.history-item-delete {
    background: none;
    border: none;
    color: rgba(255, 255, 255, 0.3);
    cursor: pointer;
    padding: 4px;
    border-radius: 6px;
    font-size: 0.75rem;
    transition: all 0.2s;
    flex-shrink: 0;
}

.history-item-delete:hover {
    color: #ff6b6b;
    background: rgba(255, 107, 107, 0.15);
}

.history-empty {
    text-align: center;
    padding: 30px 20px;
    color: rgba(255, 255, 255, 0.4);
    font-size: 0.85rem;
}

.history-empty i {
    font-size: 2rem;
    margin-bottom: 10px;
    display: block;
    opacity: 0.5;
}

/* ============ Main Chat Area ============ */
.chat-main {
    flex: 1;
    display: flex;
    flex-direction: column;
    background: #FFF7ED;
    min-width: 0;
}

/* Chat Header */
.chat-header {
    padding: 16px 24px;
    background: white;
    border-bottom: 1px solid #F3E8E0;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
}

.chat-header-info {
    display: flex;
    align-items: center;
    gap: 14px;
}

.pandit-avatar {
    width: 48px;
    height: 48px;
    background: linear-gradient(135deg, #FF6B35 0%, #F59E0B 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    flex-shrink: 0;
    box-shadow: 0 4px 12px rgba(255, 107, 53, 0.3);
}

.pandit-name {
    font-weight: 600;
    font-size: 1.1rem;
    color: #1E1E2E;
}

.pandit-status {
    font-size: 0.8rem;
    color: #10B981;
    display: flex;
    align-items: center;
    gap: 5px;
}

.pandit-status .online-dot {
    width: 8px;
    height: 8px;
    background: #10B981;
    border-radius: 50%;
    animation: pulse-dot 2s infinite;
}

@keyframes pulse-dot {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}

.chat-header-actions {
    display: flex;
    gap: 8px;
}

.toggle-history-btn {
    display: none;
    align-items: center;
    justify-content: center;
    width: 38px;
    height: 38px;
    background: rgba(255, 107, 53, 0.1);
    border: none;
    border-radius: 10px;
    color: #FF6B35;
    cursor: pointer;
    transition: all 0.2s;
    font-size: 1rem;
}

.toggle-history-btn:hover {
    background: rgba(255, 107, 53, 0.2);
}

/* Chat Messages */
.chat-messages {
    flex: 1;
    overflow-y: auto;
    padding: 24px;
    display: flex;
    flex-direction: column;
    gap: 16px;
    scroll-behavior: smooth;
    scrollbar-width: thin;
    scrollbar-color: rgba(0, 0, 0, 0.1) transparent;
}

.chat-messages::-webkit-scrollbar {
    width: 5px;
}

.chat-messages::-webkit-scrollbar-thumb {
    background: rgba(0, 0, 0, 0.1);
    border-radius: 3px;
}

/* Message Bubbles */
.message-row {
    display: flex;
    gap: 10px;
    max-width: 85%;
    animation: fadeInUp 0.3s ease;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.message-row.assistant {
    align-self: flex-start;
}

.message-row.user {
    align-self: flex-end;
    flex-direction: row-reverse;
}

.msg-avatar {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-size: 1rem;
    margin-top: 2px;
}

.message-row.assistant .msg-avatar {
    background: linear-gradient(135deg, #FF6B35, #F59E0B);
}

.message-row.user .msg-avatar {
    background: linear-gradient(135deg, #1A1A2E, #16213E);
    color: white;
    font-size: 0.8rem;
    font-weight: 600;
}

.msg-bubble {
    padding: 14px 18px;
    border-radius: 18px;
    font-size: 0.92rem;
    line-height: 1.6;
    position: relative;
    word-wrap: break-word;
}

.message-row.assistant .msg-bubble {
    background: white;
    color: #333;
    border: 1px solid #F3E8E0;
    border-bottom-left-radius: 6px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
}

.message-row.user .msg-bubble {
    background: linear-gradient(135deg, #FF6B35 0%, #E55A2B 100%);
    color: white;
    border-bottom-right-radius: 6px;
    box-shadow: 0 2px 12px rgba(255, 107, 53, 0.25);
}

.msg-time {
    font-size: 0.7rem;
    margin-top: 6px;
    opacity: 0.5;
}

.message-row.user .msg-time {
    text-align: right;
    color: rgba(255, 255, 255, 0.7);
}

/* Welcome Message */
.welcome-message {
    text-align: center;
    padding: 40px 30px;
    color: #6B7280;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    flex: 1;
}

.welcome-icon {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #FF6B35, #F59E0B);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.5rem;
    margin-bottom: 20px;
    box-shadow: 0 8px 30px rgba(255, 107, 53, 0.3);
    animation: float 3s ease-in-out infinite;
}

@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-8px); }
}

.welcome-message h2 {
    font-size: 1.5rem;
    color: #1E1E2E;
    margin-bottom: 10px;
    font-weight: 700;
}

.welcome-message p {
    font-size: 0.95rem;
    max-width: 400px;
    line-height: 1.6;
    margin-bottom: 25px;
}

.quick-questions {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    justify-content: center;
    max-width: 500px;
}

.quick-q-btn {
    padding: 8px 16px;
    background: white;
    border: 1px solid #F3E8E0;
    border-radius: 20px;
    font-size: 0.82rem;
    color: #FF6B35;
    cursor: pointer;
    transition: all 0.2s;
    font-family: inherit;
    font-weight: 500;
}

.quick-q-btn:hover {
    background: #FF6B35;
    color: white;
    border-color: #FF6B35;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(255, 107, 53, 0.25);
}

/* Typing Indicator */
.typing-indicator {
    display: none;
    align-self: flex-start;
    max-width: 85%;
}

.typing-indicator.active {
    display: flex;
}

.typing-content {
    display: flex;
    align-items: center;
    gap: 10px;
}

.typing-bubble {
    background: white;
    border: 1px solid #F3E8E0;
    padding: 14px 18px;
    border-radius: 18px;
    border-bottom-left-radius: 6px;
    display: flex;
    align-items: center;
    gap: 12px;
}

.typing-dots {
    display: flex;
    gap: 4px;
}

.typing-dots span {
    width: 8px;
    height: 8px;
    background: #FF6B35;
    border-radius: 50%;
    animation: bounce-dot 1.4s infinite ease-in-out;
}

.typing-dots span:nth-child(1) { animation-delay: 0s; }
.typing-dots span:nth-child(2) { animation-delay: 0.2s; }
.typing-dots span:nth-child(3) { animation-delay: 0.4s; }

@keyframes bounce-dot {
    0%, 80%, 100% {
        transform: scale(0.6);
        opacity: 0.4;
    }
    40% {
        transform: scale(1);
        opacity: 1;
    }
}

.typing-text {
    font-size: 0.82rem;
    color: #6B7280;
    font-style: italic;
}

/* Chat Input Area */
.chat-input-area {
    padding: 16px 24px;
    background: white;
    border-top: 1px solid #F3E8E0;
}

.chat-input-container {
    display: flex;
    align-items: flex-end;
    gap: 12px;
    max-width: 100%;
}

.chat-input {
    flex: 1;
    padding: 14px 18px;
    border: 2px solid #F3E8E0;
    border-radius: 16px;
    font-size: 0.95rem;
    font-family: inherit;
    resize: none;
    max-height: 120px;
    min-height: 50px;
    outline: none;
    transition: border-color 0.3s;
    line-height: 1.4;
    background: #FDFCFB;
}

.chat-input:focus {
    border-color: #FF6B35;
    background: white;
}

.chat-input::placeholder {
    color: #B8A99A;
}

.send-btn {
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, #FF6B35, #E55A2B);
    border: none;
    border-radius: 16px;
    color: white;
    font-size: 1.2rem;
    cursor: pointer;
    transition: all 0.3s;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.send-btn:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(255, 107, 53, 0.4);
}

.send-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.send-btn .fa-spinner {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

/* ============ Mobile History Overlay ============ */
.history-overlay {
    display: none;
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.4);
    z-index: 9;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.history-overlay.active {
    display: block;
    opacity: 1;
}

/* ============ Responsive ============ */
@media (max-width: 1024px) {
    .pandit-chat-wrapper {
        height: calc(100vh - 110px);
        min-height: 500px;
    }

    .chat-history-panel {
        width: 240px;
    }

    .welcome-message p {
        max-width: 350px;
        font-size: 0.9rem;
    }
}

@media (max-width: 768px) {
    .pandit-chat-wrapper {
        height: calc(100vh - 90px);
        min-height: 400px;
        border-radius: 0;
        box-shadow: none;
        margin: -20px -15px;
        width: calc(100% + 30px);
    }

    .chat-history-panel {
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        transform: translateX(-100%);
        width: 260px;
        box-shadow: 5px 0 30px rgba(0, 0, 0, 0.3);
    }

    .chat-history-panel.mobile-open {
        transform: translateX(0);
    }

    .toggle-history-btn {
        display: flex;
    }

    .chat-header {
        padding: 12px 16px;
    }

    .pandit-avatar {
        width: 40px;
        height: 40px;
        font-size: 1.2rem;
    }

    .pandit-name {
        font-size: 1rem;
    }

    .pandit-status {
        font-size: 0.75rem;
    }

    .chat-messages {
        padding: 14px 12px;
    }

    .message-row {
        max-width: 92%;
    }

    .msg-avatar {
        width: 30px;
        height: 30px;
        font-size: 0.85rem;
    }

    .msg-bubble {
        padding: 10px 14px;
        font-size: 0.88rem;
        border-radius: 14px;
    }

    .message-row.assistant .msg-bubble {
        border-bottom-left-radius: 4px;
    }

    .message-row.user .msg-bubble {
        border-bottom-right-radius: 4px;
    }

    .chat-input-area {
        padding: 10px 12px;
    }

    .chat-input {
        padding: 12px 14px;
        font-size: 0.9rem;
        border-radius: 14px;
        min-height: 44px;
    }

    .send-btn {
        width: 44px;
        height: 44px;
        border-radius: 14px;
        font-size: 1rem;
    }

    .welcome-message {
        padding: 30px 20px;
    }

    .welcome-icon {
        width: 65px;
        height: 65px;
        font-size: 2rem;
        margin-bottom: 16px;
    }

    .welcome-message h2 {
        font-size: 1.3rem;
        margin-bottom: 8px;
    }

    .welcome-message p {
        font-size: 0.88rem;
        max-width: 320px;
        margin-bottom: 20px;
    }

    .quick-questions {
        gap: 8px;
        max-width: 100%;
    }

    .quick-q-btn {
        font-size: 0.78rem;
        padding: 7px 14px;
    }

    .typing-bubble {
        padding: 10px 14px;
        border-radius: 14px;
        border-bottom-left-radius: 4px;
    }

    .typing-text {
        font-size: 0.78rem;
    }

    .history-header {
        padding: 16px;
    }

    .history-item {
        padding: 10px 12px;
    }

    .history-item-title {
        font-size: 0.82rem;
    }

    .history-item-date {
        font-size: 0.68rem;
    }
}

@media (max-width: 480px) {
    .pandit-chat-wrapper {
        height: calc(100vh - 85px);
        min-height: 350px;
    }

    .chat-history-panel {
        width: 230px;
    }

    .chat-header {
        padding: 10px 12px;
    }

    .chat-header-info {
        gap: 10px;
    }

    .pandit-avatar {
        width: 36px;
        height: 36px;
        font-size: 1.1rem;
    }

    .pandit-name {
        font-size: 0.92rem;
    }

    .pandit-status {
        font-size: 0.7rem;
    }

    .chat-messages {
        padding: 10px 8px;
        gap: 10px;
    }

    .message-row {
        max-width: 95%;
        gap: 6px;
    }

    .msg-avatar {
        width: 26px;
        height: 26px;
        font-size: 0.7rem;
    }

    .msg-bubble {
        padding: 8px 12px;
        font-size: 0.84rem;
        line-height: 1.5;
    }

    .msg-time {
        font-size: 0.65rem;
    }

    .chat-input-area {
        padding: 8px 10px;
    }

    .chat-input-container {
        gap: 8px;
    }

    .chat-input {
        padding: 10px 12px;
        font-size: 0.85rem;
        border-radius: 12px;
        min-height: 40px;
        border-width: 1.5px;
    }

    .send-btn {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        font-size: 0.95rem;
    }

    .welcome-message {
        padding: 20px 15px;
    }

    .welcome-icon {
        width: 55px;
        height: 55px;
        font-size: 1.6rem;
        margin-bottom: 12px;
    }

    .welcome-message h2 {
        font-size: 1.15rem;
    }

    .welcome-message p {
        font-size: 0.82rem;
        max-width: 280px;
        line-height: 1.5;
        margin-bottom: 16px;
    }

    .quick-questions {
        gap: 6px;
    }

    .quick-q-btn {
        font-size: 0.72rem;
        padding: 6px 10px;
        border-radius: 16px;
    }

    .toggle-history-btn {
        width: 34px;
        height: 34px;
        font-size: 0.9rem;
    }

    .typing-bubble {
        padding: 8px 12px;
        gap: 8px;
    }

    .typing-dots span {
        width: 6px;
        height: 6px;
    }

    .typing-text {
        font-size: 0.72rem;
    }
}
</style>

<div class="pandit-chat-wrapper" id="panditChatWrapper">
    <!-- Mobile History Overlay -->
    <div class="history-overlay" id="historyOverlay" onclick="closeHistoryPanel()"></div>
    <!-- History Sidebar -->
    <div class="chat-history-panel" id="historyPanel">
        <div class="history-header">
            <h3><i class="fas fa-history"></i> Chats</h3>
            <button class="new-chat-btn" onclick="startNewChat()" id="newChatBtn">
                <i class="fas fa-plus"></i> New
            </button>
        </div>
        <div class="history-list" id="historyList">
            <?php if (empty($sessions)): ?>
                <div class="history-empty" id="historyEmpty">
                    <i class="fas fa-comments"></i>
                    <p>No conversations yet.<br>Start chatting with Pandit Ji!</p>
                </div>
            <?php else: ?>
                <?php foreach ($sessions as $session): ?>
                    <div class="history-item" data-session-id="<?= $session['id'] ?>" onclick="loadSession(<?= $session['id'] ?>)">
                        <div class="history-item-info">
                            <div class="history-item-title"><?= htmlspecialchars($session['title']) ?></div>
                            <div class="history-item-date"><?= date('M d, h:i A', strtotime($session['created_at'])) ?></div>
                        </div>
                        <button class="history-item-delete" onclick="event.stopPropagation(); deleteSession(<?= $session['id'] ?>)" title="Delete">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Main Chat Area -->
    <div class="chat-main">
        <!-- Chat Header -->
        <div class="chat-header">
            <div class="chat-header-info">
                <div class="pandit-avatar">🙏</div>
                <div>
                    <div class="pandit-name">Pandit Ji</div>
                    <div class="pandit-status">
                        <span class="online-dot"></span>
                        Online — Ready to guide you
                    </div>
                </div>
            </div>
            <div class="chat-header-actions">
                <button class="toggle-history-btn" onclick="toggleHistoryPanel()" title="Chat History">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </div>

        <!-- Chat Messages -->
        <div class="chat-messages" id="chatMessages">
            <!-- Welcome state (shown when no session is active) -->
            <div class="welcome-message" id="welcomeState">
                <div class="welcome-icon">🕉️</div>
                <h2>Namaste 🙏</h2>
                <p>I am your Pandit Ji. Ask any dharmik question, puja guidance, or ritual-related help — I’m here to support you.</p>
                <div class="quick-questions">
                    <button class="quick-q-btn" onclick="sendQuickQuestion(this)">How to perform Griha Pravesh?</button>
                    <button class="quick-q-btn" onclick="sendQuickQuestion(this)">Satyanarayan Katha: steps</button>
                    <button class="quick-q-btn" onclick="sendQuickQuestion(this)">How to check a shubh muhurat?</button>
                    <button class="quick-q-btn" onclick="sendQuickQuestion(this)">Navratri vrat rules</button>
                    <button class="quick-q-btn" onclick="sendQuickQuestion(this)">What samagri is needed for puja?</button>
                </div>
            </div>

            <!-- Typing Indicator -->
            <div class="message-row assistant typing-indicator" id="typingIndicator">
                <div class="msg-avatar">🙏</div>
                <div class="typing-bubble">
                    <div class="typing-dots">
                        <span></span><span></span><span></span>
                    </div>
                    <span class="typing-text">Pandit Ji is thinking...</span>
                </div>
            </div>
        </div>

        <!-- Chat Input -->
        <div class="chat-input-area">
            <div class="chat-input-container">
                <textarea
                    class="chat-input"
                    id="chatInput"
                    placeholder="Ask Pandit Ji a question..."
                    rows="1"
                    maxlength="2000"
                ></textarea>
                <button class="send-btn" id="sendBtn" onclick="sendMessage()" title="Send">
                    <i class="fas fa-paper-plane" id="sendIcon"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// ============ State ============
let currentSessionId = null;
let isSending = false;
const csrfToken = '<?= $csrfToken ?>';

// ============ DOM Elements ============
const chatMessages = document.getElementById('chatMessages');
const chatInput = document.getElementById('chatInput');
const sendBtn = document.getElementById('sendBtn');
const sendIcon = document.getElementById('sendIcon');
const typingIndicator = document.getElementById('typingIndicator');
const welcomeState = document.getElementById('welcomeState');
const historyPanel = document.getElementById('historyPanel');
const historyList = document.getElementById('historyList');

// ============ Auto-resize Textarea ============
chatInput.addEventListener('input', function() {
    this.style.height = 'auto';
    this.style.height = Math.min(this.scrollHeight, 120) + 'px';
});

// ============ Enter Key Handler ============
chatInput.addEventListener('keydown', function(e) {
    if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        sendMessage();
    }
});

// ============ Send Message ============
async function sendMessage() {
    const message = chatInput.value.trim();
    if (!message || isSending) return;

    isSending = true;
    sendBtn.disabled = true;
    sendIcon.className = 'fas fa-spinner';

    // Hide welcome state
    welcomeState.style.display = 'none';

    // Add user message to chat
    appendMessage('user', message);

    // Clear input
    chatInput.value = '';
    chatInput.style.height = 'auto';

    // Show typing indicator
    typingIndicator.classList.add('active');
    scrollToBottom();

    try {
        const formData = new FormData();
        formData.append('_token', csrfToken);
        formData.append('message', message);
        if (currentSessionId) {
            formData.append('session_id', currentSessionId);
        }

        const response = await fetch('/user/ai-pandit/send', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        const data = await response.json();

        // Hide typing indicator
        typingIndicator.classList.remove('active');

        if (data.success) {
            // Update session ID
            if (!currentSessionId && data.session_id) {
                currentSessionId = data.session_id;
                // Refresh history
                refreshHistory();
            }

            // Add AI response
            appendMessage('assistant', data.answer);

            // Highlight active session in history
            highlightSession(currentSessionId);
        } else {
            appendMessage('assistant', '🙏 Sorry—something went wrong. Please try again in a moment. ' + (data.error || ''));
        }

    } catch (error) {
        typingIndicator.classList.remove('active');
        appendMessage('assistant', '🙏 Sorry—there is a network issue. Please try again.');
        console.error('Chat error:', error);
    }

    isSending = false;
    sendBtn.disabled = false;
    sendIcon.className = 'fas fa-paper-plane';
    chatInput.focus();
}

// ============ Append Message ============
function appendMessage(role, content, time = null) {
    const now = time || new Date().toLocaleTimeString('en-IN', { hour: '2-digit', minute: '2-digit', hour12: true });

    const avatar = role === 'assistant' ? '🙏' : getInitials();
    const formattedContent = formatMessage(content);

    const messageHtml = `
        <div class="message-row ${role}">
            <div class="msg-avatar">${avatar}</div>
            <div>
                <div class="msg-bubble">${formattedContent}</div>
                <div class="msg-time">${now}</div>
            </div>
        </div>
    `;

    // Insert before typing indicator
    typingIndicator.insertAdjacentHTML('beforebegin', messageHtml);
    scrollToBottom();
}

// ============ Format Message ============
function formatMessage(text) {
    // Escape HTML
    let formatted = text.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    // Bold
    formatted = formatted.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
    // Italic
    formatted = formatted.replace(/\*(.*?)\*/g, '<em>$1</em>');
    // Line breaks
    formatted = formatted.replace(/\n/g, '<br>');
    return formatted;
}

// ============ Get User Initials ============
function getInitials() {
    const userName = '<?= htmlspecialchars($user['name'] ?? 'U') ?>';
    const parts = userName.split(' ');
    if (parts.length >= 2) {
        return (parts[0][0] + parts[1][0]).toUpperCase();
    }
    return userName.substring(0, 2).toUpperCase();
}

// ============ Scroll to Bottom ============
function scrollToBottom() {
    requestAnimationFrame(() => {
        chatMessages.scrollTop = chatMessages.scrollHeight;
    });
}

// ============ Quick Question ============
function sendQuickQuestion(btn) {
    chatInput.value = btn.textContent;
    sendMessage();
}

// ============ Start New Chat ============
function startNewChat() {
    currentSessionId = null;
    clearMessages();
    welcomeState.style.display = '';
    chatInput.focus();

    // Close mobile history panel
    closeHistoryPanel();

    // Remove active from all history items
    document.querySelectorAll('.history-item').forEach(item => item.classList.remove('active'));
}

// ============ Clear Messages ============
function clearMessages() {
    const messages = chatMessages.querySelectorAll('.message-row:not(.typing-indicator)');
    messages.forEach(msg => msg.remove());
}

// ============ Load Session ============
async function loadSession(sessionId) {
    try {
        // Close mobile history panel
        closeHistoryPanel();

        clearMessages();
        welcomeState.style.display = 'none';

        // Show loading
        typingIndicator.classList.add('active');

        const response = await fetch(`/user/ai-pandit/session/${sessionId}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });

        const data = await response.json();
        typingIndicator.classList.remove('active');

        if (data.success) {
            currentSessionId = sessionId;

            // Render messages
            data.messages.forEach(msg => {
                const time = new Date(msg.created_at).toLocaleTimeString('en-IN', { hour: '2-digit', minute: '2-digit', hour12: true });
                appendMessage(msg.role, msg.content, time);
            });

            highlightSession(sessionId);
            scrollToBottom();
        } else {
            appendMessage('assistant', 'Session could not be loaded. Please try again.');
        }

    } catch (error) {
        typingIndicator.classList.remove('active');
        console.error('Load session error:', error);
    }
}

// ============ Delete Session ============
async function deleteSession(sessionId) {
    if (!confirm('Do you want to delete this conversation?')) return;

    try {
        const formData = new FormData();
        formData.append('_token', csrfToken);

        const response = await fetch(`/user/ai-pandit/session/${sessionId}/delete`, {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });

        const data = await response.json();

        if (data.success) {
            // Remove from sidebar
            const item = document.querySelector(`.history-item[data-session-id="${sessionId}"]`);
            if (item) {
                item.style.transition = 'all 0.3s';
                item.style.opacity = '0';
                item.style.transform = 'translateX(-20px)';
                setTimeout(() => item.remove(), 300);
            }

            // If it was the current session, reset
            if (currentSessionId === sessionId) {
                startNewChat();
            }

            // Check if list is empty
            setTimeout(() => {
                const remaining = document.querySelectorAll('.history-item');
                if (remaining.length === 0) {
                    historyList.innerHTML = `
                        <div class="history-empty" id="historyEmpty">
                            <i class="fas fa-comments"></i>
                            <p>No conversations yet.<br>Start chatting with Pandit Ji!</p>
                        </div>
                    `;
                }
            }, 350);
        }
    } catch (error) {
        console.error('Delete session error:', error);
    }
}

// ============ Refresh History ============
async function refreshHistory() {
    try {
        const response = await fetch('/user/ai-pandit/history', {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });

        const data = await response.json();

        if (data.success) {
            if (data.sessions.length === 0) {
                historyList.innerHTML = `
                    <div class="history-empty" id="historyEmpty">
                        <i class="fas fa-comments"></i>
                        <p>No conversations yet.<br>Start chatting with Pandit Ji!</p>
                    </div>
                `;
                return;
            }

            historyList.innerHTML = data.sessions.map(s => `
                <div class="history-item ${s.id == currentSessionId ? 'active' : ''}" 
                     data-session-id="${s.id}" onclick="loadSession(${s.id})">
                    <div class="history-item-info">
                        <div class="history-item-title">${escapeHtml(s.title)}</div>
                        <div class="history-item-date">${formatDate(s.created_at)}</div>
                    </div>
                    <button class="history-item-delete" onclick="event.stopPropagation(); deleteSession(${s.id})" title="Delete">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </div>
            `).join('');
        }
    } catch (error) {
        console.error('Refresh history error:', error);
    }
}

// ============ Highlight Active Session ============
function highlightSession(sessionId) {
    document.querySelectorAll('.history-item').forEach(item => {
        item.classList.toggle('active', item.dataset.sessionId == sessionId);
    });
}

// ============ Toggle History Panel (Mobile) ============
function toggleHistoryPanel() {
    const overlay = document.getElementById('historyOverlay');
    historyPanel.classList.toggle('mobile-open');
    overlay.classList.toggle('active');
}

function closeHistoryPanel() {
    const overlay = document.getElementById('historyOverlay');
    historyPanel.classList.remove('mobile-open');
    overlay.classList.remove('active');
}

// ============ Utility Functions ============
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function formatDate(dateStr) {
    const d = new Date(dateStr);
    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    const hours = d.getHours();
    const minutes = d.getMinutes().toString().padStart(2, '0');
    const ampm = hours >= 12 ? 'PM' : 'AM';
    const h12 = hours % 12 || 12;
    return `${months[d.getMonth()]} ${d.getDate()}, ${h12}:${minutes} ${ampm}`;
}

// ============ Focus on Input on Page Load ============
chatInput.focus();
</script>
