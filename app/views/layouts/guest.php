<?php
use App\Core\Auth;
$isLoggedIn = Auth::check();
$csrfToken = Auth::csrfToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 64 64'><circle cx='32' cy='32' r='32' fill='%23FF6B35'/><text x='32' y='46' text-anchor='middle' font-size='40' font-family='serif' fill='white'>ॐ</text></svg>">
    <title><?= htmlspecialchars($title ?? 'Explore - Sanskar AI') ?></title>
    <meta name="description" content="Explore Hindu rituals, get AI-powered guidance - no account needed. Try Sanskar AI free!">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --primary:#FF6B35; --primary-dark:#E55A2B; --secondary:#10B981; --accent:#F59E0B; --success:#10B981; --warning:#F59E0B; --danger:#EF4444; --dark:#1E1E2E; --content-bg:#FFF7ED; --saffron:#FF9933; --gold:#D4AF37; }
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:'Poppins',sans-serif; background:var(--content-bg); min-height:100vh; }

        .guest-navbar { position:fixed; top:0; left:0; right:0; z-index:1000; padding:12px 5%; display:flex; justify-content:space-between; align-items:center; background:linear-gradient(135deg,#1A1A2E 0%,#16213E 100%); border-bottom:1px solid rgba(255,255,255,0.1); }
        .guest-navbar .logo { display:flex; align-items:center; gap:12px; font-size:1.4rem; font-weight:700; color:white; text-decoration:none; }
        .guest-navbar .logo i { color:var(--saffron); font-size:1.6rem; }
        .guest-nav-links { display:flex; gap:8px; align-items:center; }
        .guest-nav-links a { color:rgba(255,255,255,0.8); text-decoration:none; font-weight:500; font-size:0.95rem; padding:10px 18px; border-radius:30px; transition:all 0.3s; }
        .guest-nav-links a:hover,.guest-nav-links a.active { color:var(--saffron); background:rgba(255,153,51,0.12); }
        .guest-nav-right { display:flex; align-items:center; gap:12px; }
        .btn-nav-login { color:rgba(255,255,255,0.85); text-decoration:none; font-weight:500; font-size:0.95rem; padding:10px 20px; border-radius:30px; transition:all 0.3s; }
        .btn-nav-login:hover { color:var(--saffron); background:rgba(255,153,51,0.1); }
        .btn-nav-signup { background:linear-gradient(135deg,var(--primary),var(--saffron)); color:white; text-decoration:none; font-weight:600; font-size:0.95rem; padding:10px 24px; border-radius:30px; transition:all 0.3s; border:none; cursor:pointer; box-shadow:0 4px 15px rgba(255,107,53,0.3); }
        .btn-nav-signup:hover { transform:translateY(-2px); box-shadow:0 6px 20px rgba(255,107,53,0.4); }
        .guest-mobile-toggle { display:none; background:none; border:none; color:white; font-size:1.3rem; cursor:pointer; padding:8px; }

        .guest-main { margin-top:70px; padding:20px 5% 30px; min-height:calc(100vh - 70px); max-width:1300px; margin-left:auto; margin-right:auto; }
        .card { background:white; border-radius:16px; padding:25px; box-shadow:0 4px 15px rgba(0,0,0,0.05); margin-bottom:25px; }
        .btn { display:inline-flex; align-items:center; gap:8px; padding:10px 20px; border:none; border-radius:8px; font-size:0.9rem; font-weight:500; cursor:pointer; transition:all 0.3s; text-decoration:none; font-family:inherit; }
        .btn-primary { background:var(--primary); color:white; }
        .btn-primary:hover { background:var(--primary-dark); }
        .btn-success { background:var(--success); color:white; }
        .btn-sm { padding:6px 12px; font-size:0.8rem; }
        .alert { padding:15px 20px; border-radius:10px; margin-bottom:20px; }
        .alert-success { background:#D1FAE5; color:#065F46; }
        .alert-error { background:#FEE2E2; color:#991B1B; }
        .form-control { width:100%; padding:12px 16px; border:2px solid #E5E7EB; border-radius:10px; font-size:1rem; font-family:inherit; }
        .form-control:focus { outline:none; border-color:var(--primary); }

        .guest-cta-banner { background:linear-gradient(135deg,#1A1A2E 0%,#0F3460 100%); color:white; padding:14px 24px; border-radius:14px; margin-bottom:25px; display:flex; align-items:center; justify-content:space-between; gap:15px; flex-wrap:wrap; border:1px solid rgba(255,153,51,0.2); }
        .guest-cta-banner .cta-text { display:flex; align-items:center; gap:10px; font-size:0.95rem; }
        .guest-cta-banner .cta-text i { color:var(--saffron); font-size:1.1rem; }
        .guest-cta-banner .cta-actions { display:flex; gap:10px; flex-shrink:0; }
        .guest-cta-banner .btn-cta-signup { background:linear-gradient(135deg,var(--primary),var(--saffron)); color:white; padding:8px 20px; border-radius:8px; font-weight:600; font-size:0.85rem; text-decoration:none; transition:all 0.3s; }
        .guest-cta-banner .btn-cta-login { background:rgba(255,255,255,0.1); color:white; padding:8px 16px; border-radius:8px; font-size:0.85rem; text-decoration:none; border:1px solid rgba(255,255,255,0.2); }

        .guest-footer { background:linear-gradient(135deg,#1A1A2E 0%,#16213E 100%); color:white; padding:40px 5% 25px; margin-top:60px; }
        .footer-cta-section { text-align:center; padding:40px 20px; background:rgba(255,107,53,0.08); border-radius:20px; margin-bottom:30px; border:1px solid rgba(255,107,53,0.15); }
        .footer-cta-section h3 { font-size:1.5rem; margin-bottom:10px; }
        .footer-cta-section p { color:rgba(255,255,255,0.7); margin-bottom:20px; }
        .footer-bottom { text-align:center; padding-top:20px; border-top:1px solid rgba(255,255,255,0.1); color:rgba(255,255,255,0.5); font-size:0.85rem; }

        /* Badge & other utility classes needed by shared views */
        .badge { display:inline-flex; align-items:center; gap:4px; padding:4px 10px; border-radius:20px; font-size:0.75rem; font-weight:600; }
        .badge-info { background:#EFF6FF; color:#1E40AF; }
        .badge-success { background:#D1FAE5; color:#065F46; }
        .badge-warning { background:#FEF3C7; color:#92400E; }
        .badge-danger { background:#FEE2E2; color:#991B1B; }
        .form-group { margin-bottom:15px; }
        .form-group label { display:block; font-weight:500; margin-bottom:5px; font-size:0.9rem; color:#374151; }
        .card-header { margin-bottom:15px; padding-bottom:10px; border-bottom:1px solid #F3F4F6; }
        .card-title { font-size:1.1rem; font-weight:600; display:flex; align-items:center; gap:8px; }
        .toast-container { position:fixed; bottom:30px; right:30px; z-index:9999; }

        /* Popup */
        .signup-popup-overlay { display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.55); backdrop-filter:blur(4px); z-index:10000; justify-content:center; align-items:center; padding:20px; animation:popupFadeIn 0.3s ease; }
        .signup-popup-overlay.active { display:flex; }
        @keyframes popupFadeIn { from{opacity:0} to{opacity:1} }
        .signup-popup { background:rgba(255,255,255,0.95); backdrop-filter:blur(30px); border-radius:24px; max-width:440px; width:100%; box-shadow:0 25px 80px rgba(0,0,0,0.25); animation:popupSlideUp 0.4s cubic-bezier(0.16,1,0.3,1); overflow:hidden; position:relative; }
        @keyframes popupSlideUp { from{opacity:0;transform:translateY(40px) scale(0.95)} to{opacity:1;transform:translateY(0) scale(1)} }
        .popup-glow { position:absolute; top:-2px; left:-2px; right:-2px; bottom:-2px; background:conic-gradient(from 0deg,var(--primary),var(--saffron),var(--gold),var(--primary)); border-radius:26px; z-index:-1; animation:popupGlowSpin 4s linear infinite; opacity:0.6; }
        @keyframes popupGlowSpin { 0%{filter:hue-rotate(0deg)} 100%{filter:hue-rotate(360deg)} }
        .popup-header { background:linear-gradient(135deg,var(--primary) 0%,var(--saffron) 100%); padding:30px 30px 25px; text-align:center; color:white; position:relative; overflow:hidden; }
        .popup-header::before { content:'ॐ'; position:absolute; right:15px; top:50%; transform:translateY(-50%); font-size:5rem; opacity:0.12; font-family:serif; }
        .popup-avatar { width:60px; height:60px; background:rgba(255,255,255,0.2); border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:2rem; margin:0 auto 15px; animation:popupFloat 3s ease-in-out infinite; }
        @keyframes popupFloat { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-6px)} }
        .popup-header h3 { font-size:1.2rem; font-weight:700; margin-bottom:6px; }
        .popup-header p { font-size:0.9rem; opacity:0.9; line-height:1.5; }
        .popup-body { padding:25px 30px; text-align:center; }
        .popup-message { font-size:0.95rem; color:#374151; line-height:1.7; margin-bottom:20px; }
        .popup-features { display:flex; gap:10px; justify-content:center; flex-wrap:wrap; margin-bottom:22px; }
        .popup-feature-tag { display:inline-flex; align-items:center; gap:5px; padding:5px 12px; border-radius:20px; font-size:0.75rem; font-weight:500; background:#FFF7ED; color:#9A3412; border:1px solid #FED7AA; }
        .popup-feature-tag i { color:var(--primary); }
        .popup-actions { display:flex; flex-direction:column; gap:10px; }
        .btn-popup-signup { display:flex; align-items:center; justify-content:center; gap:10px; width:100%; padding:14px 24px; background:linear-gradient(135deg,var(--primary),var(--saffron)); color:white; border:none; border-radius:12px; font-size:1rem; font-weight:700; cursor:pointer; transition:all 0.3s; font-family:inherit; text-decoration:none; box-shadow:0 6px 20px rgba(255,107,53,0.3); }
        .btn-popup-signup:hover { transform:translateY(-2px); box-shadow:0 8px 30px rgba(255,107,53,0.4); }
        .btn-popup-login { display:flex; align-items:center; justify-content:center; gap:8px; width:100%; padding:12px 24px; background:white; color:var(--dark); border:2px solid #E5E7EB; border-radius:12px; font-size:0.95rem; font-weight:600; cursor:pointer; transition:all 0.3s; font-family:inherit; text-decoration:none; }
        .btn-popup-login:hover { border-color:var(--primary); color:var(--primary); }
        .popup-dismiss { background:none; border:none; color:#9CA3AF; font-size:0.85rem; cursor:pointer; padding:8px; margin-top:5px; transition:color 0.3s; font-family:inherit; }
        .popup-dismiss:hover { color:#6B7280; }

        @media (max-width:768px) {
            .guest-nav-links { display:none; }
            .guest-mobile-toggle { display:block; }
            .guest-nav-links.mobile-open { display:flex; flex-direction:column; position:absolute; top:100%; left:0; right:0; background:#1A1A2E; padding:15px; gap:5px; border-bottom:2px solid var(--saffron); }
            .guest-main { padding:20px 15px; margin-top:65px; }
            .guest-cta-banner { flex-direction:column; text-align:center; }
            .signup-popup { max-width:95%; }
            .guest-nav-right .btn-nav-login { display:none; }
            .guest-nav-right .btn-nav-signup { padding: 8px 16px; font-size: 0.85rem; }
        }
    </style>
</head>
<body>
    <nav class="guest-navbar">
        <a href="/" class="logo"><i class="fas fa-om"></i><span>Sanskar AI</span></a>
        <div class="guest-nav-links" id="guestNavLinks">
            <a href="/explore" class="<?= strpos($_SERVER['REQUEST_URI'], '/explore') === 0 ? 'active' : '' ?>"><i class="fas fa-search"></i> Explore Rituals</a>
            <a href="/try-ai" class="<?= strpos($_SERVER['REQUEST_URI'], '/try-ai') === 0 ? 'active' : '' ?>"><i class="fas fa-robot"></i> Try AI Pandit</a>
            <a href="/#features">Features</a>
        </div>
        <div class="guest-nav-right">
            <?php if ($isLoggedIn): ?>
                <a href="<?= Auth::dashboardUrl() ?>" class="btn-nav-signup"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <?php else: ?>
                <a href="/login" class="btn-nav-login">Login</a>
                <a href="/signup" class="btn-nav-signup"><i class="fas fa-rocket"></i> Sign Up Free</a>
            <?php endif; ?>
            <button class="guest-mobile-toggle" id="guestMobileToggle"><i class="fas fa-bars"></i></button>
        </div>
    </nav>

    <div class="guest-main">
        <?php if (!$isLoggedIn): ?>
        <div class="guest-cta-banner">
            <div class="cta-text"><i class="fas fa-sparkles"></i><span>You're exploring as a guest. <strong>Create a free account</strong> to save rituals & more!</span></div>
            <div class="cta-actions">
                <a href="/signup" class="btn-cta-signup"><i class="fas fa-user-plus"></i> Sign Up Free</a>
                <a href="/login" class="btn-cta-login"><i class="fas fa-sign-in-alt"></i> Login</a>
            </div>
        </div>
        <?php endif; ?>
        <?php if (isset($_SESSION['flash']['success'])): ?>
            <div class="alert alert-success"><?= $_SESSION['flash']['success'] ?></div>
            <?php unset($_SESSION['flash']['success']); ?>
        <?php endif; ?>
        <?php if (isset($_SESSION['flash']['error'])): ?>
            <div class="alert alert-error"><?= $_SESSION['flash']['error'] ?></div>
            <?php unset($_SESSION['flash']['error']); ?>
        <?php endif; ?>
        <?= $content ?>
    </div>

    <footer class="guest-footer">
        <?php if (!$isLoggedIn): ?>
        <div class="footer-cta-section">
            <h3>🙏 Ready to Begin Your Spiritual Journey?</h3>
            <p>Join thousands of families preserving their traditions with Sanskar AI. It's completely free!</p>
            <a href="/signup" class="btn-popup-signup" style="max-width:300px; margin:0 auto;"><i class="fas fa-rocket"></i> Create Free Account</a>
        </div>
        <?php endif; ?>
        <div class="footer-bottom"><p>&copy; <?= date('Y') ?> Sanskar AI. Preserving traditions through technology.</p></div>
    </footer>

    <?php if (!$isLoggedIn): ?>
    <div class="signup-popup-overlay" id="signupPopup">
        <div class="signup-popup">
            <div class="popup-glow"></div>
            <div class="popup-header">
                <div class="popup-avatar" id="popupAvatar">🙏</div>
                <h3 id="popupTitle">Your Spiritual Journey Awaits!</h3>
                <p id="popupSubtitle">Create a free profile to unlock the full power of Sanskar AI</p>
            </div>
            <div class="popup-body">
                <p class="popup-message" id="popupMessage">Sign up to save rituals, get personalized recommendations based on your community & family traditions!</p>
                <div class="popup-features">
                    <span class="popup-feature-tag"><i class="fas fa-bookmark"></i> Save Rituals</span>
                    <span class="popup-feature-tag"><i class="fas fa-robot"></i> Unlimited AI</span>
                    <span class="popup-feature-tag"><i class="fas fa-users"></i> Family Profiles</span>
                    <span class="popup-feature-tag"><i class="fas fa-pray"></i> Book Pandits</span>
                </div>
                <div class="popup-actions">
                    <a href="/signup" class="btn-popup-signup"><i class="fas fa-user-plus"></i> Create Free Account</a>
                    <a href="/login" class="btn-popup-login"><i class="fas fa-sign-in-alt"></i> Already have an account? Login</a>
                    <button class="popup-dismiss" id="popupDismiss">Maybe later</button>
                </div>
            </div>
        </div>
    </div>
    <script>
    (function(){
        const popup=document.getElementById('signupPopup'), dismiss=document.getElementById('popupDismiss');
        const avatar=document.getElementById('popupAvatar'), title=document.getElementById('popupTitle');
        const subtitle=document.getElementById('popupSubtitle'), msg=document.getElementById('popupMessage');
        const variants=[
            {a:'🙏',t:'Your Spiritual Journey Awaits!',s:'Create a free profile to unlock the full power of Sanskar AI',m:'Sign up to save rituals, get personalized recommendations based on your community & family traditions!'},
            {a:'✨',t:'Unlock the Full Experience!',s:'Save rituals, chat history & community-specific recommendations',m:'With a free account you get unlimited AI generations, saved chat history, personalized ritual guidance!'},
            {a:'🪔',t:"Don't Lose Your Progress!",s:'Sign up to keep your AI-generated rituals forever',m:'Everything you discover as a guest will be lost when you leave. Create a free account to save your rituals!'},
            {a:'🕉️',t:'Join 10,000+ Families!',s:"Preserving traditions with Sanskar AI — It's free!",m:'Families across India and the world trust Sanskar AI for ritual guidance. Join them today!'},
            {a:'📿',t:'Just 30 Seconds to Sign Up!',s:'Personalized rituals based on your Gotra, Nakshatra & community',m:'Tell us about your community, and our AI will provide rituals tailored specifically for you!'}
        ];
        let vi=0, timer=null;
        function show(){
            const v=variants[vi%variants.length];
            avatar.textContent=v.a; title.textContent=v.t; subtitle.textContent=v.s; msg.textContent=v.m;
            popup.classList.add('active'); document.body.style.overflow='hidden'; vi++;
        }
        function hide(){
            popup.classList.remove('active'); document.body.style.overflow='';
            clearTimeout(timer); timer=setTimeout(show,60000);
        }
        dismiss.addEventListener('click',hide);
        popup.addEventListener('click',function(e){if(e.target===popup)hide();});
        document.addEventListener('keydown',function(e){if(e.key==='Escape'&&popup.classList.contains('active'))hide();});
        timer=setTimeout(show,30000);
        let scrollDone=false;
        window.addEventListener('scroll',function(){
            if(scrollDone)return;
            if((window.scrollY/(document.body.scrollHeight-window.innerHeight))*100>50){
                scrollDone=true;
                if(!popup.classList.contains('active')){clearTimeout(timer);show();}
            }
        });
        window.SanskarGuestPopup={
            show:function(){if(!popup.classList.contains('active')){clearTimeout(timer);show();}},
            showAfterDelay:function(ms){clearTimeout(timer);timer=setTimeout(show,ms||2000);}
        };
    })();
    </script>
    <?php endif; ?>
    <script>
        const mt=document.getElementById('guestMobileToggle'), nl=document.getElementById('guestNavLinks');
        if(mt&&nl){mt.addEventListener('click',function(){nl.classList.toggle('mobile-open');mt.querySelector('i').className=nl.classList.contains('mobile-open')?'fas fa-times':'fas fa-bars';});}
    </script>
</body>
</html>
