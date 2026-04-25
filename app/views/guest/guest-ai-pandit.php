<?php $csrfToken = \App\Core\Auth::csrfToken(); ?>

<style>
/* AI Pandit Guest Chat */
.pandit-chat-wrapper { display: flex; flex-direction: column; height: calc(100vh - 200px); min-height: 500px; background: white; border-radius: 20px; overflow: hidden; box-shadow: 0 8px 40px rgba(0,0,0,0.08); }
.chat-header { padding: 16px 24px; background: white; border-bottom: 1px solid #F3E8E0; display: flex; align-items: center; justify-content: space-between; gap: 12px; }
.chat-header-info { display: flex; align-items: center; gap: 14px; }
.pandit-avatar { width: 48px; height: 48px; background: linear-gradient(135deg, #FF6B35 0%, #F59E0B 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; flex-shrink: 0; box-shadow: 0 4px 12px rgba(255,107,53,0.3); }
.pandit-name { font-weight: 600; font-size: 1.1rem; color: #1E1E2E; }
.pandit-status { font-size: 0.8rem; color: #10B981; display: flex; align-items: center; gap: 5px; }
.pandit-status .online-dot { width: 8px; height: 8px; background: #10B981; border-radius: 50%; animation: pulse-dot 2s infinite; }
@keyframes pulse-dot { 0%,100% { opacity: 1; } 50% { opacity: 0.5; } }
.guest-msg-limit { background: rgba(255,107,53,0.08); padding: 6px 14px; border-radius: 20px; font-size: 0.8rem; font-weight: 600; color: #9A3412; display: flex; align-items: center; gap: 5px; }
.chat-messages { flex: 1; overflow-y: auto; padding: 24px; display: flex; flex-direction: column; gap: 16px; scroll-behavior: smooth; }
.message-row { display: flex; gap: 10px; max-width: 85%; animation: fadeInUp 0.3s ease; }
@keyframes fadeInUp { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
.message-row.assistant { align-self: flex-start; }
.message-row.user { align-self: flex-end; flex-direction: row-reverse; }
.msg-avatar { width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 1rem; margin-top: 2px; }
.message-row.assistant .msg-avatar { background: linear-gradient(135deg, #FF6B35, #F59E0B); }
.message-row.user .msg-avatar { background: linear-gradient(135deg, #1A1A2E, #16213E); color: white; font-size: 0.8rem; font-weight: 600; }
.msg-bubble { padding: 14px 18px; border-radius: 18px; font-size: 0.92rem; line-height: 1.6; word-wrap: break-word; }
.message-row.assistant .msg-bubble { background: white; color: #333; border: 1px solid #F3E8E0; border-bottom-left-radius: 6px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); }
.message-row.user .msg-bubble { background: linear-gradient(135deg, #FF6B35 0%, #E55A2B 100%); color: white; border-bottom-right-radius: 6px; box-shadow: 0 2px 12px rgba(255,107,53,0.25); }
.msg-cta { margin-top: 8px; padding: 8px 12px; background: rgba(255,107,53,0.06); border-radius: 8px; font-size: 0.78rem; color: #9A3412; display: flex; align-items: center; gap: 5px; }
.msg-cta a { color: #FF6B35; font-weight: 600; text-decoration: none; }
.msg-cta a:hover { text-decoration: underline; }
.welcome-message { text-align: center; padding: 40px 30px; display: flex; flex-direction: column; align-items: center; justify-content: center; flex: 1; }
.welcome-icon { width: 80px; height: 80px; background: linear-gradient(135deg, #FF6B35, #F59E0B); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; margin-bottom: 20px; box-shadow: 0 8px 30px rgba(255,107,53,0.3); animation: float 3s ease-in-out infinite; }
@keyframes float { 0%,100% { transform: translateY(0); } 50% { transform: translateY(-8px); } }
.welcome-message h2 { font-size: 1.5rem; color: #1E1E2E; margin-bottom: 10px; }
.welcome-message p { font-size: 0.95rem; color: #6B7280; max-width: 400px; line-height: 1.6; margin-bottom: 25px; }
.quick-questions { display: flex; flex-wrap: wrap; gap: 10px; justify-content: center; max-width: 500px; }
.quick-q-btn { padding: 8px 16px; background: white; border: 1px solid #F3E8E0; border-radius: 20px; font-size: 0.82rem; color: #FF6B35; cursor: pointer; transition: all 0.2s; font-family: inherit; font-weight: 500; }
.quick-q-btn:hover { background: #FF6B35; color: white; border-color: #FF6B35; transform: translateY(-1px); }
.typing-indicator { display: none; align-self: flex-start; }
.typing-indicator.active { display: flex; }
.typing-bubble { background: white; border: 1px solid #F3E8E0; padding: 14px 18px; border-radius: 18px; border-bottom-left-radius: 6px; display: flex; align-items: center; gap: 12px; }
.typing-dots { display: flex; gap: 4px; }
.typing-dots span { width: 8px; height: 8px; background: #FF6B35; border-radius: 50%; animation: bounce-dot 1.4s infinite ease-in-out; }
.typing-dots span:nth-child(1) { animation-delay: 0s; } .typing-dots span:nth-child(2) { animation-delay: 0.2s; } .typing-dots span:nth-child(3) { animation-delay: 0.4s; }
@keyframes bounce-dot { 0%,80%,100% { transform: scale(0.6); opacity: 0.4; } 40% { transform: scale(1); opacity: 1; } }
.chat-input-area { padding: 16px 24px; background: white; border-top: 1px solid #F3E8E0; }
.chat-input-container { display: flex; align-items: flex-end; gap: 12px; }
.chat-input { flex: 1; padding: 14px 18px; border: 2px solid #F3E8E0; border-radius: 16px; font-size: 0.95rem; font-family: inherit; resize: none; max-height: 120px; min-height: 50px; outline: none; transition: border-color 0.3s; line-height: 1.4; background: #FDFCFB; }
.chat-input:focus { border-color: #FF6B35; background: white; }
.send-btn { width: 50px; height: 50px; background: linear-gradient(135deg, #FF6B35, #E55A2B); border: none; border-radius: 16px; color: white; font-size: 1.2rem; cursor: pointer; transition: all 0.3s; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.send-btn:hover:not(:disabled) { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(255,107,53,0.4); }
.send-btn:disabled { opacity: 0.5; cursor: not-allowed; }
.limit-reached-bar { padding: 16px 24px; background: linear-gradient(135deg, #FEF3C7, #FDE68A); border-top: 1px solid #F59E0B; text-align: center; }
.limit-reached-bar p { color: #92400E; margin-bottom: 10px; font-weight: 600; }
@media (max-width: 768px) {
    .pandit-chat-wrapper { height: calc(100vh - 160px); border-radius: 0; margin: -20px -15px; width: calc(100% + 30px); }
    .chat-messages { padding: 14px 12px; }
    .message-row { max-width: 92%; }
    .msg-bubble { padding: 10px 14px; font-size: 0.88rem; }
    .chat-input { padding: 12px 14px; font-size: 0.9rem; min-height: 44px; }
    .send-btn { width: 44px; height: 44px; }
}
</style>

<div class="pandit-chat-wrapper">
    <!-- Chat Header -->
    <div class="chat-header">
        <div class="chat-header-info">
            <div class="pandit-avatar">🙏</div>
            <div>
                <div class="pandit-name">AI Pandit</div>
                <div class="pandit-status"><span class="online-dot"></span> Online</div>
            </div>
        </div>
        <div class="guest-msg-limit" id="msgLimit">
            <i class="fas fa-comment"></i>
            <span id="msgRemaining"><?= $chatRemaining ?></span> / <?= $maxChatMessages ?> free messages
        </div>
    </div>

    <!-- Chat Messages -->
    <div class="chat-messages" id="chatMessages">
        <div class="welcome-message" id="welcomeMsg">
            <div class="welcome-icon">🙏</div>
            <h2>Namaste! I'm AI Pandit</h2>
            <p>Ask me anything about Hindu rituals, ceremonies, mantras, or spiritual guidance. Try a question below!</p>
            <div class="quick-questions">
                <button class="quick-q-btn" onclick="askQuickQuestion(this)">What is Satyanarayan Puja?</button>
                <button class="quick-q-btn" onclick="askQuickQuestion(this)">How to do Griha Pravesh?</button>
                <button class="quick-q-btn" onclick="askQuickQuestion(this)">Best mantras for meditation</button>
                <button class="quick-q-btn" onclick="askQuickQuestion(this)">Significance of Navratri</button>
            </div>
        </div>

        <!-- Typing indicator -->
        <div class="typing-indicator" id="typingIndicator">
            <div class="msg-avatar" style="background: linear-gradient(135deg, #FF6B35, #F59E0B);">🙏</div>
            <div class="typing-bubble">
                <div class="typing-dots"><span></span><span></span><span></span></div>
                <span style="font-size: 0.82rem; color: #6B7280; font-style: italic;">AI Pandit is thinking...</span>
            </div>
        </div>
    </div>

    <!-- Chat Input or Limit Reached -->
    <div id="chatInputArea" class="chat-input-area" <?= $chatRemaining <= 0 ? 'style="display:none;"' : '' ?>>
        <div class="chat-input-container">
            <textarea class="chat-input" id="chatInput" placeholder="Ask AI Pandit a question..." rows="1"
                onkeydown="if(event.key==='Enter' && !event.shiftKey){event.preventDefault(); sendMessage();}"></textarea>
            <button class="send-btn" id="sendBtn" onclick="sendMessage()">
                <i class="fas fa-paper-plane"></i>
            </button>
        </div>
    </div>

    <div id="limitReachedBar" class="limit-reached-bar" <?= $chatRemaining > 0 ? 'style="display:none;"' : '' ?>>
        <p>🙏 You've used all your free messages!</p>
        <a href="/signup" class="btn-popup-signup" style="display: inline-flex; padding: 12px 28px; max-width: 320px; margin: 0 auto; text-decoration: none; background: linear-gradient(135deg, #FF6B35, #F59E0B); color: white; border-radius: 12px; font-weight: 700; font-size: 0.95rem; gap: 8px; align-items: center; justify-content: center;">
            <i class="fas fa-user-plus"></i> Create Free Account for Unlimited Chats
        </a>
    </div>
</div>

<script>
const csrfToken = '<?= $csrfToken ?>';
let messageHistory = [];
let messagesRemaining = <?= $chatRemaining ?>;
let chatCount = 0;

function askQuickQuestion(btn) {
    document.getElementById('chatInput').value = btn.textContent.trim();
    sendMessage();
}

async function sendMessage() {
    const input = document.getElementById('chatInput');
    const message = input.value.trim();
    if (!message) return;

    if (messagesRemaining <= 0) {
        document.getElementById('chatInputArea').style.display = 'none';
        document.getElementById('limitReachedBar').style.display = 'block';
        if (window.SanskarGuestPopup) window.SanskarGuestPopup.show();
        return;
    }

    // Hide welcome
    const welcome = document.getElementById('welcomeMsg');
    if (welcome) welcome.style.display = 'none';

    // Add user message
    addMessageBubble('user', message);
    input.value = '';
    input.style.height = 'auto';

    // Show typing
    const typing = document.getElementById('typingIndicator');
    typing.classList.add('active');
    document.getElementById('chatMessages').scrollTop = document.getElementById('chatMessages').scrollHeight;

    // Disable input
    const sendBtn = document.getElementById('sendBtn');
    sendBtn.disabled = true;
    sendBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

    try {
        const formData = new FormData();
        formData.append('csrf_token', csrfToken);
        formData.append('message', message);
        formData.append('history', JSON.stringify(messageHistory));

        const response = await fetch('/try-ai/send', { method: 'POST', body: formData });
        const data = await response.json();

        typing.classList.remove('active');

        if (data.success) {
            messageHistory.push({ role: 'user', content: message });
            messageHistory.push({ role: 'assistant', content: data.answer });

            addMessageBubble('assistant', data.answer, data.cta);
            messagesRemaining = data.remaining;
            chatCount++;

            // Update counter
            document.getElementById('msgRemaining').textContent = messagesRemaining;
            if (messagesRemaining <= 1) {
                document.getElementById('msgLimit').style.background = 'rgba(239,68,68,0.1)';
                document.getElementById('msgLimit').style.color = '#991B1B';
            }

            // Trigger popup after every 2nd message
            if (chatCount % 2 === 0 && window.SanskarGuestPopup) {
                window.SanskarGuestPopup.showAfterDelay(2000);
            }

            if (messagesRemaining <= 0) {
                document.getElementById('chatInputArea').style.display = 'none';
                document.getElementById('limitReachedBar').style.display = 'block';
            }
        } else {
            if (data.limit_reached) {
                document.getElementById('chatInputArea').style.display = 'none';
                document.getElementById('limitReachedBar').style.display = 'block';
                if (window.SanskarGuestPopup) window.SanskarGuestPopup.show();
            } else {
                addMessageBubble('assistant', 'Sorry, something went wrong. Please try again.');
            }
        }
    } catch (error) {
        typing.classList.remove('active');
        addMessageBubble('assistant', 'Connection error. Please try again.');
    } finally {
        sendBtn.disabled = false;
        sendBtn.innerHTML = '<i class="fas fa-paper-plane"></i>';
    }
}

function addMessageBubble(role, content, cta) {
    const messages = document.getElementById('chatMessages');
    const row = document.createElement('div');
    row.className = `message-row ${role}`;

    const avatar = document.createElement('div');
    avatar.className = 'msg-avatar';
    avatar.textContent = role === 'assistant' ? '🙏' : '👤';

    const bubble = document.createElement('div');
    bubble.className = 'msg-bubble';
    bubble.innerHTML = escapeHtml(content).replace(/\n/g, '<br>');

    // Add CTA footer for assistant messages
    if (role === 'assistant' && cta) {
        const ctaDiv = document.createElement('div');
        ctaDiv.className = 'msg-cta';
        ctaDiv.innerHTML = `<i class="fas fa-sparkles"></i> <a href="/signup">${escapeHtml(cta)}</a>`;
        bubble.appendChild(ctaDiv);
    }

    row.appendChild(avatar);
    row.appendChild(bubble);

    // Insert before typing indicator
    const typing = document.getElementById('typingIndicator');
    messages.insertBefore(row, typing);
    messages.scrollTop = messages.scrollHeight;
}

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Auto-resize textarea
document.getElementById('chatInput').addEventListener('input', function() {
    this.style.height = 'auto';
    this.style.height = Math.min(this.scrollHeight, 120) + 'px';
});
</script>
