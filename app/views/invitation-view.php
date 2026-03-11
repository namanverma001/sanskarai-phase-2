<?php
/**
 * Public Invitation View Page
 * ============================
 * Standalone page (no layout) — shown to guests when they open the shared link.
 * If expired: shows expiry message.
 * If active: prompts for name, then renders the personalized invitation.
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $expired ? 'Invitation Expired' : htmlspecialchars($invitation['occasion_title'] ?? 'You are Invited!') ?> - Sanskar AI</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="icon" type="image/svg+xml"
        href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 64 64'><circle cx='32' cy='32' r='32' fill='%23FF6B35'/><text x='32' y='46' text-anchor='middle' font-size='40' font-family='serif' fill='white'>ॐ</text></svg>">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #1A1A2E 0%, #16213E 50%, #0F3460 100%);
            overflow: hidden;
        }

        /* Expired state */
        .expired-container {
            text-align: center;
            padding: 60px 30px;
            z-index: 1;
            animation: fadeIn 0.8s ease;
        }

        .expired-icon {
            font-size: 5rem;
            margin-bottom: 20px;
            opacity: 0.6;
        }

        .expired-container h1 {
            color: white;
            font-size: 2rem;
            margin-bottom: 12px;
        }

        .expired-container p {
            color: rgba(255, 255, 255, 0.6);
            font-size: 1.1rem;
            max-width: 400px;
            line-height: 1.7;
        }

        .expired-container a {
            display: inline-block;
            margin-top: 25px;
            padding: 12px 28px;
            background: linear-gradient(135deg, #FF6B35, #F59E0B);
            color: white;
            text-decoration: none;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .expired-container a:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(255, 107, 53, 0.4);
        }

        /* Name prompt */
        .name-prompt {
            position: relative;
            z-index: 1;
            text-align: center;
            padding: 50px 30px;
            max-width: 520px;
            width: 90%;
            animation: fadeIn 0.8s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .prompt-card {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 24px;
            padding: 50px 40px;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.3);
        }

        .prompt-card .envelope-icon {
            font-size: 3.5rem;
            margin-bottom: 20px;
            display: block;
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.08); }
        }

        .prompt-card h1 {
            color: white;
            font-size: 1.6rem;
            margin-bottom: 8px;
            font-weight: 700;
        }

        .prompt-card .occasion-name {
            color: #FF9933;
            font-size: 1.15rem;
            font-weight: 500;
            margin-bottom: 25px;
        }

        .prompt-card p.subtitle {
            color: rgba(255, 255, 255, 0.6);
            margin-bottom: 30px;
            line-height: 1.6;
            font-size: 0.95rem;
        }

        .name-input-wrapper {
            position: relative;
            margin-bottom: 20px;
        }

        .name-input-wrapper input {
            width: 100%;
            padding: 16px 20px;
            border: 2px solid rgba(255, 255, 255, 0.2);
            border-radius: 14px;
            background: rgba(255, 255, 255, 0.08);
            color: white;
            font-size: 1.1rem;
            font-family: 'Poppins', sans-serif;
            outline: none;
            transition: all 0.3s;
            text-align: center;
        }

        .name-input-wrapper input::placeholder {
            color: rgba(255, 255, 255, 0.4);
        }

        .name-input-wrapper input:focus {
            border-color: #FF9933;
            box-shadow: 0 0 0 4px rgba(255, 153, 51, 0.2);
        }

        .view-btn {
            display: inline-block;
            width: 100%;
            padding: 16px 32px;
            background: linear-gradient(135deg, #FF6B35, #F59E0B);
            color: white;
            border: none;
            border-radius: 14px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            font-family: 'Poppins', sans-serif;
            transition: all 0.3s;
            letter-spacing: 0.5px;
        }

        .view-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(255, 107, 53, 0.4);
        }

        .view-btn:active {
            transform: translateY(-1px);
        }

        .powered-by {
            margin-top: 25px;
            color: rgba(255, 255, 255, 0.3);
            font-size: 0.8rem;
        }

        .powered-by a {
            color: rgba(255, 153, 51, 0.5);
            text-decoration: none;
        }

        /* Full-page invitation iframe */
        .invitation-fullscreen {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            z-index: 100;
            display: none;
            background: #000;
            margin: 0;
            padding: 0;
            overflow: hidden;
        }

        .invitation-fullscreen iframe {
            width: 100vw;
            height: 100vh;
            border: none;
            display: block;
            margin: 0;
            padding: 0;
        }

        @media (max-width: 480px) {
            .prompt-card {
                padding: 35px 25px;
            }
            .prompt-card h1 {
                font-size: 1.3rem;
            }
        }
    </style>
</head>
<body>

    <?php if ($expired): ?>
        <!-- Expired State -->
        <div class="expired-container">
            <div class="expired-icon">⏳</div>
            <h1>Invitation Expired</h1>
            <p>This invitation link is no longer active. The host may have set a time limit or removed it.</p>
            <a href="/">Visit Sanskar AI</a>
        </div>
    <?php else: ?>
        <!-- Name Prompt -->
        <div class="name-prompt" id="namePrompt">
            <div class="prompt-card">
                <span class="envelope-icon">💌</span>
                <h1>You're Invited!</h1>
                <p class="occasion-name"><?= htmlspecialchars($invitation['occasion_title']) ?></p>
                <p class="subtitle">Please enter your name to view your personalized invitation card</p>
                <div class="name-input-wrapper">
                    <input type="text" id="guestName" placeholder="Enter your name" autofocus 
                           onkeypress="if(event.key==='Enter')viewInvitation()">
                </div>
                <button class="view-btn" onclick="viewInvitation()">
                    ✨ View My Invitation
                </button>
                <p class="powered-by">Powered by <a href="/">Sanskar AI</a></p>
            </div>
        </div>

        <!-- Full-screen Invitation Display -->
        <div class="invitation-fullscreen" id="invitationDisplay">
            <iframe id="invFrame" sandbox="allow-scripts allow-same-origin"></iframe>
        </div>

        <script>
            // Store the AI-generated HTML
            const rawHtml = <?= json_encode($invitation['generated_html']) ?>;

            function viewInvitation() {
                const nameInput = document.getElementById('guestName');
                let guestName = nameInput.value.trim();

                if (!guestName) {
                    guestName = 'Honoured Guest';
                }

                // Replace placeholder with actual name
                const personalizedHtml = rawHtml.replace(/\{GUEST_NAME\}/g, guestName);

                // Show the invitation
                const display = document.getElementById('invitationDisplay');
                const iframe = document.getElementById('invFrame');
                
                display.style.display = 'block';
                
                const doc = iframe.contentDocument || iframe.contentWindow.document;
                doc.open();
                doc.write(personalizedHtml);
                doc.close();

                // Hide the prompt with animation
                const prompt = document.getElementById('namePrompt');
                prompt.style.transition = 'opacity 0.5s ease';
                prompt.style.opacity = '0';
                setTimeout(() => { prompt.style.display = 'none'; }, 500);
            }
        </script>
    <?php endif; ?>
</body>
</html>
