<!-- Enhanced Modern Landing Page Styles -->
<style>
/* ========== MODERN ENHANCEMENT LAYER ========== */

/* Animated gradient mesh background */
.hero::after {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    background:
        radial-gradient(ellipse 600px 600px at 10% 20%, rgba(255,107,53,0.12) 0%, transparent 70%),
        radial-gradient(ellipse 500px 500px at 85% 30%, rgba(107,92,231,0.1) 0%, transparent 70%),
        radial-gradient(ellipse 400px 400px at 50% 80%, rgba(212,175,55,0.08) 0%, transparent 70%);
    animation: meshFloat 12s ease-in-out infinite alternate;
    pointer-events: none;
    z-index: 0;
}

@keyframes meshFloat {
    0% { transform: scale(1) rotate(0deg); }
    50% { transform: scale(1.1) rotate(1deg); }
    100% { transform: scale(1.05) rotate(-0.5deg); }
}

/* Glowing orbs floating in hero */
.hero-orb {
    position: absolute;
    border-radius: 50%;
    filter: blur(60px);
    opacity: 0.4;
    animation: orbFloat 15s ease-in-out infinite;
    pointer-events: none;
    z-index: 0;
}

.hero-orb:nth-child(1) {
    width: 300px; height: 300px;
    background: rgba(255, 107, 53, 0.3);
    top: 10%; left: 5%;
    animation-delay: 0s;
}

.hero-orb:nth-child(2) {
    width: 250px; height: 250px;
    background: rgba(107, 92, 231, 0.25);
    top: 60%; right: 10%;
    animation-delay: -5s;
}

.hero-orb:nth-child(3) {
    width: 200px; height: 200px;
    background: rgba(212, 175, 55, 0.2);
    bottom: 10%; left: 30%;
    animation-delay: -10s;
}

@keyframes orbFloat {
    0%, 100% { transform: translate(0, 0) scale(1); }
    25% { transform: translate(30px, -40px) scale(1.1); }
    50% { transform: translate(-20px, 20px) scale(0.95); }
    75% { transform: translate(15px, 30px) scale(1.05); }
}

/* Text shimmer effect for hero title */
.shimmer-text {
    position: relative;
    display: block;
    overflow: hidden;
}

.shimmer-text::after {
    content: '';
    position: absolute;
    top: 0; left: -100%;
    width: 50%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.15), transparent);
    animation: shimmer 4s ease-in-out infinite;
}

@keyframes shimmer {
    0% { left: -100%; }
    100% { left: 200%; }
}

/* Typing cursor for subtitle */
.typed-cursor {
    display: inline-block;
    width: 3px;
    height: 1.2em;
    background: var(--saffron);
    margin-left: 2px;
    animation: cursorBlink 0.8s step-end infinite;
    vertical-align: text-bottom;
    border-radius: 2px;
}

/* Wrapper for subtitle + cursor to stay on same line */
.hero-subtitle-wrapper {
    display: flex;
    justify-content: center;
    align-items: baseline;
    min-height: 2.5em;
    margin-bottom: 45px;
    text-align: center;
}

.hero-subtitle-wrapper .hero-subtitle {
    margin-bottom: 0;
    display: inline;
}

@keyframes cursorBlink {
    0%, 100% { opacity: 1; }
    50% { opacity: 0; }
}

/* Enhanced hero badge with animated border */
.hero-badge-enhanced {
    position: relative;
    z-index: 1;
    isolation: isolate;
}

.hero-badge-enhanced::before {
    content: '';
    position: absolute;
    top: -2px; left: -2px; right: -2px; bottom: -2px;
    background: conic-gradient(from var(--badge-angle, 0deg), var(--saffron), var(--secondary), var(--gold), var(--primary), var(--saffron));
    border-radius: 50px;
    z-index: -2;
    animation: badgeBorderSpin 3s linear infinite;
}

.hero-badge-enhanced::after {
    content: '';
    position: absolute;
    inset: 2px;
    background: rgba(10, 14, 23, 0.92);
    border-radius: 50px;
    z-index: -1;
}

[data-theme="light"] .hero-badge-enhanced::after {
    background: rgba(255, 248, 240, 0.95);
}

@keyframes badgeBorderSpin {
    0% { filter: hue-rotate(0deg); }
    100% { filter: hue-rotate(360deg); }
}

/* Force hero content center alignment */
.hero .hero-content {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    width: 100%;
    max-width: 900px;
    margin: 0 auto;
}

/* 3D tilt card effect */
.tilt-card {
    transform-style: preserve-3d;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.tilt-card:hover {
    transform: perspective(1000px) rotateY(-5deg) rotateX(5deg) translateZ(20px);
    box-shadow: 0 25px 60px rgba(255, 107, 53, 0.2), 0 0 0 1px rgba(255,153,51,0.2);
}

/* Glassmorphism cards enhanced */
.glass-card {
    background: rgba(255, 255, 255, 0.03) !important;
    backdrop-filter: blur(20px) saturate(1.5);
    border: 1px solid rgba(255, 255, 255, 0.08) !important;
    transition: all 0.5s cubic-bezier(0.23, 1, 0.32, 1) !important;
}

[data-theme="light"] .glass-card {
    background: rgba(255, 255, 255, 0.6) !important;
    border: 1px solid rgba(255, 153, 51, 0.15) !important;
}

.glass-card:hover {
    background: rgba(255, 255, 255, 0.07) !important;
    border-color: rgba(255, 153, 51, 0.3) !important;
    box-shadow: 0 20px 60px rgba(0,0,0,0.3), inset 0 1px 0 rgba(255,255,255,0.1) !important;
}

[data-theme="light"] .glass-card:hover {
    background: rgba(255, 255, 255, 0.9) !important;
    box-shadow: 0 20px 60px rgba(255,107,53,0.15), inset 0 1px 0 rgba(255,255,255,1) !important;
}

/* Magnetic hover buttons */
.magnetic-btn {
    position: relative;
    overflow: hidden;
    transition: all 0.4s cubic-bezier(0.23, 1, 0.32, 1) !important;
}

.magnetic-btn::before {
    content: '';
    position: absolute;
    top: 50%; left: 50%;
    width: 0; height: 0;
    background: rgba(255,255,255,0.15);
    border-radius: 50%;
    transform: translate(-50%, -50%);
    transition: width 0.6s, height 0.6s;
}

.magnetic-btn:hover::before {
    width: 400px;
    height: 400px;
}

/* Animated counter display */
.counter-glow {
    text-shadow: 0 0 20px rgba(255, 153, 51, 0.5), 0 0 40px rgba(255, 153, 51, 0.2);
}

/* Floating sacred symbols */
.floating-symbols {
    position: absolute;
    width: 100%;
    height: 100%;
    overflow: hidden;
    pointer-events: none;
    z-index: 0;
}

.sacred-symbol {
    position: absolute;
    font-size: 1.5rem;
    opacity: 0;
    animation: floatSymbol 20s linear infinite;
    color: var(--saffron);
}

.sacred-symbol:nth-child(1) { left: 5%; animation-delay: 0s; font-size: 1.2rem; }
.sacred-symbol:nth-child(2) { left: 15%; animation-delay: 3s; font-size: 1rem; }
.sacred-symbol:nth-child(3) { left: 25%; animation-delay: 7s; font-size: 1.4rem; }
.sacred-symbol:nth-child(4) { left: 45%; animation-delay: 2s; font-size: 0.9rem; }
.sacred-symbol:nth-child(5) { left: 65%; animation-delay: 5s; font-size: 1.3rem; }
.sacred-symbol:nth-child(6) { left: 78%; animation-delay: 8s; font-size: 1.1rem; }
.sacred-symbol:nth-child(7) { left: 90%; animation-delay: 1s; font-size: 1rem; }
.sacred-symbol:nth-child(8) { left: 35%; animation-delay: 11s; font-size: 1.5rem; }
.sacred-symbol:nth-child(9) { left: 55%; animation-delay: 4s; font-size: 0.8rem; }
.sacred-symbol:nth-child(10) { left: 85%; animation-delay: 9s; font-size: 1.2rem; }

@keyframes floatSymbol {
    0% {
        transform: translateY(100vh) rotate(0deg) scale(0);
        opacity: 0;
    }
    5% { opacity: 0.15; transform: translateY(90vh) rotate(20deg) scale(0.8); }
    50% { opacity: 0.08; transform: translateY(40vh) rotate(180deg) scale(1); }
    95% { opacity: 0.12; transform: translateY(-5vh) rotate(340deg) scale(0.9); }
    100% {
        transform: translateY(-10vh) rotate(360deg) scale(0);
        opacity: 0;
    }
}

/* Reveal animations */
.reveal-up {
    opacity: 0;
    transform: translateY(60px);
    transition: all 0.8s cubic-bezier(0.16, 1, 0.3, 1);
}

.reveal-up.revealed {
    opacity: 1;
    transform: translateY(0);
}

.reveal-left {
    opacity: 0;
    transform: translateX(-60px);
    transition: all 0.8s cubic-bezier(0.16, 1, 0.3, 1);
}

.reveal-left.revealed {
    opacity: 1;
    transform: translateX(0);
}

.reveal-right {
    opacity: 0;
    transform: translateX(60px);
    transition: all 0.8s cubic-bezier(0.16, 1, 0.3, 1);
}

.reveal-right.revealed {
    opacity: 1;
    transform: translateX(0);
}

.reveal-scale {
    opacity: 0;
    transform: scale(0.8);
    transition: all 0.8s cubic-bezier(0.16, 1, 0.3, 1);
}

.reveal-scale.revealed {
    opacity: 1;
    transform: scale(1);
}

/* Stagger children */
.stagger-children > *:nth-child(1) { transition-delay: 0.1s; }
.stagger-children > *:nth-child(2) { transition-delay: 0.2s; }
.stagger-children > *:nth-child(3) { transition-delay: 0.3s; }
.stagger-children > *:nth-child(4) { transition-delay: 0.4s; }
.stagger-children > *:nth-child(5) { transition-delay: 0.5s; }
.stagger-children > *:nth-child(6) { transition-delay: 0.6s; }

/* Enhanced feature icon with gradient ring */
.feature-icon-enhanced {
    position: relative;
    width: 70px;
    height: 70px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 20px;
}

.feature-icon-enhanced::before {
    content: '';
    position: absolute;
    inset: -3px;
    border-radius: 20px;
    background: conic-gradient(from 0deg, var(--primary), var(--secondary), var(--gold), var(--primary));
    animation: rotateBorder 4s linear infinite;
    opacity: 0;
    transition: opacity 0.4s;
}

.feature-card:hover .feature-icon-enhanced::before {
    opacity: 1;
}

.feature-icon-enhanced i {
    font-size: 1.8rem;
    z-index: 1;
    background: linear-gradient(135deg, var(--saffron), var(--gold));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    transition: transform 0.4s cubic-bezier(0.23, 1, 0.32, 1);
}

.feature-card:hover .feature-icon-enhanced i {
    transform: scale(1.2) rotate(5deg);
}

/* Marquee for trust brands */
.marquee-container {
    overflow: hidden;
    white-space: nowrap;
    padding: 20px 0;
}

.marquee-track {
    display: inline-flex;
    animation: marqueeScroll 25s linear infinite;
    gap: 60px;
    align-items: center;
}

.marquee-track span {
    font-size: 1.1rem;
    color: var(--text-muted);
    display: flex;
    align-items: center;
    gap: 8px;
    flex-shrink: 0;
}

.marquee-track span i {
    color: var(--saffron);
    font-size: 1.3rem;
}

@keyframes marqueeScroll {
    0% { transform: translateX(0); }
    100% { transform: translateX(-50%); }
}

/* Pulsing glow on CTA */
.pulse-glow {
    animation: pulseGlow 2s ease-in-out infinite;
}

@keyframes pulseGlow {
    0%, 100% { box-shadow: 0 10px 30px rgba(255,107,53,0.3); }
    50% { box-shadow: 0 10px 50px rgba(255,107,53,0.6), 0 0 80px rgba(255,107,53,0.2); }
}

/* Animated section divider */
.section-divider {
    width: 100%;
    height: 1px;
    background: linear-gradient(90deg, transparent, var(--saffron), transparent);
    margin: 0;
    opacity: 0.3;
}

/* Enhanced testimonial hover */
.testimonial-card.glass-card:hover {
    transform: translateY(-10px) !important;
}

.testimonial-card.glass-card .testimonial-rating i {
    transition: transform 0.3s ease;
}

.testimonial-card.glass-card:hover .testimonial-rating i {
    animation: starPop 0.3s ease forwards;
}

.testimonial-card.glass-card:hover .testimonial-rating i:nth-child(2) { animation-delay: 0.05s; }
.testimonial-card.glass-card:hover .testimonial-rating i:nth-child(3) { animation-delay: 0.1s; }
.testimonial-card.glass-card:hover .testimonial-rating i:nth-child(4) { animation-delay: 0.15s; }
.testimonial-card.glass-card:hover .testimonial-rating i:nth-child(5) { animation-delay: 0.2s; }

@keyframes starPop {
    0% { transform: scale(1); }
    50% { transform: scale(1.3); }
    100% { transform: scale(1); }
}

/* Number highlight in steps */
.step-number-enhanced {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.4rem;
    font-weight: 800;
    color: white;
    background: linear-gradient(135deg, var(--primary), var(--saffron));
    box-shadow: 0 8px 25px rgba(255,107,53,0.3);
    position: relative;
    margin: 0 auto 15px;
}

.step-number-enhanced::after {
    content: '';
    position: absolute;
    inset: -4px;
    border-radius: 50%;
    border: 2px dashed rgba(255,153,51,0.3);
    animation: rotateBorder 8s linear infinite;
}

/* Smooth parallax hint */
.parallax-section {
    will-change: transform;
}

/* Mobile responsive enhancements */
@media (max-width: 768px) {
    .hero-orb { display: none; }
    .floating-symbols { display: none; }
    .hero-badge-enhanced::before { display: none; }
    .tilt-card:hover {
        transform: none !important;
    }
}
</style>

<!-- Hero Section -->
<section class="hero">
    <!-- Floating Orbs -->
    <div class="hero-orb"></div>
    <div class="hero-orb"></div>
    <div class="hero-orb"></div>

    <!-- Floating Sacred Symbols -->
    <div class="floating-symbols">
        <span class="sacred-symbol">ॐ</span>
        <span class="sacred-symbol">🪔</span>
        <span class="sacred-symbol">🌺</span>
        <span class="sacred-symbol">☸</span>
        <span class="sacred-symbol">🕉️</span>
        <span class="sacred-symbol">🌸</span>
        <span class="sacred-symbol">✨</span>
        <span class="sacred-symbol">🔱</span>
        <span class="sacred-symbol">🪷</span>
        <span class="sacred-symbol">⚜️</span>
    </div>

    <!-- Decorative Corner Elements -->
    <div class="hero-corner top-left"></div>
    <div class="hero-corner bottom-right"></div>
    
    <!-- Floating Particles -->
    <div class="hero-particles">
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
    </div>
    
    <!-- Large Background Om Symbol -->
    <div class="hero-om-bg">ॐ</div>
    
    <div class="hero-content" data-animate="fade-up">
        <div class="hero-badge hero-badge-enhanced">
            <i class="fas fa-star"></i>
            <span>Trusted by 10,000+ families across India</span>
        </div>
        
        <h1 class="shimmer-text">Your Spiritual Journey<br>Starts Here</h1>
        
        <div class="hero-subtitle-wrapper">
            <p class="hero-subtitle" id="heroSubtitle"></p><span class="typed-cursor"></span>
        </div>
        
        <div class="hero-buttons">
            <a href="/signup" class="btn btn-primary btn-lg magnetic-btn pulse-glow">
                <i class="fas fa-rocket"></i> Get Started Free
            </a>
            <a href="#how-it-works" class="btn btn-outline btn-lg magnetic-btn">
                <i class="fas fa-play-circle"></i> See How It Works
            </a>
        </div>
        
        <!-- Trust Indicators -->
        <div class="trust-badges">
            <div class="trust-item">
                <i class="fas fa-shield-alt"></i>
                <span>100% Secure</span>
            </div>
            <div class="trust-item">
                <i class="fas fa-check-circle"></i>
                <span>Verified Pandits</span>
            </div>
            <div class="trust-item">
                <i class="fas fa-headset"></i>
                <span>24/7 Support</span>
            </div>
        </div>
    </div>
</section>

<!-- Trust Marquee -->
<div class="marquee-container" style="border-top: 1px solid var(--border-color); border-bottom: 1px solid var(--border-color);">
    <div class="marquee-track">
        <span><i class="fas fa-om"></i> Sacred Rituals</span>
        <span><i class="fas fa-pray"></i> Verified Pandits</span>
        <span><i class="fas fa-robot"></i> AI-Powered Guidance</span>
        <span><i class="fas fa-shopping-basket"></i> Smart Shopping</span>
        <span><i class="fas fa-users"></i> Family Profiles</span>
        <span><i class="fas fa-lightbulb"></i> Cultural Insights</span>
        <span><i class="fas fa-calendar-alt"></i> Auspicious Dates</span>
        <span><i class="fas fa-book-open"></i> Step-by-Step Guides</span>
        <!-- Duplicate for seamless loop -->
        <span><i class="fas fa-om"></i> Sacred Rituals</span>
        <span><i class="fas fa-pray"></i> Verified Pandits</span>
        <span><i class="fas fa-robot"></i> AI-Powered Guidance</span>
        <span><i class="fas fa-shopping-basket"></i> Smart Shopping</span>
        <span><i class="fas fa-users"></i> Family Profiles</span>
        <span><i class="fas fa-lightbulb"></i> Cultural Insights</span>
        <span><i class="fas fa-calendar-alt"></i> Auspicious Dates</span>
        <span><i class="fas fa-book-open"></i> Step-by-Step Guides</span>
    </div>
</div>

<!-- Stats Section -->
<section class="stats parallax-section">
    <div class="stat-card glass-card reveal-up" style="transition-delay: 0s;">
        <div class="stat-icon"><i class="fas fa-book-open"></i></div>
        <div class="stat-value counter-glow" data-count="<?= $stats['rituals'] ?? 100 ?>"><?= $stats['rituals'] ?? 100 ?>+</div>
        <div class="stat-label">Sacred Rituals</div>
    </div>
    <div class="stat-card glass-card reveal-up" style="transition-delay: 0.15s;">
        <div class="stat-icon"><i class="fas fa-user-tie"></i></div>
        <div class="stat-value counter-glow" data-count="<?= $stats['pandits'] ?? 50 ?>"><?= $stats['pandits'] ?? 50 ?>+</div>
        <div class="stat-label">Verified Pandits</div>
    </div>
    <div class="stat-card glass-card reveal-up" style="transition-delay: 0.3s;">
        <div class="stat-icon"><i class="fas fa-users"></i></div>
        <div class="stat-value counter-glow">10K+</div>
        <div class="stat-label">Happy Families</div>
    </div>
    <div class="stat-card glass-card reveal-up" style="transition-delay: 0.45s;">
        <div class="stat-icon"><i class="fas fa-scroll"></i></div>
        <div class="stat-value counter-glow" data-count="<?= $stats['insights'] ?? 200 ?>"><?= $stats['insights'] ?? 200 ?>+</div>
        <div class="stat-label">Cultural Insights</div>
    </div>
</section>

<div class="section-divider"></div>

<!-- How It Works Section -->
<section class="how-it-works parallax-section" id="how-it-works">
    <div class="section-header reveal-up">
        <span class="section-tag">Simple & Easy</span>
        <h2>How Sanskar AI Works</h2>
        <p>Get started in just three simple steps and experience the divine journey</p>
    </div>
    
    <div class="steps-container stagger-children">
        <div class="step-line"></div>
        <div class="step-card glass-card tilt-card reveal-up">
            <div class="step-number-enhanced">1</div>
            <div class="step-icon"><i class="fas fa-user-plus"></i></div>
            <h3>Create Your Profile</h3>
            <p>Sign up and add your family details including Gotra, Nakshatra, and Kul Devi/Devta for personalized recommendations.</p>
        </div>
        <div class="step-card glass-card tilt-card reveal-up">
            <div class="step-number-enhanced">2</div>
            <div class="step-icon"><i class="fas fa-search"></i></div>
            <h3>Explore Rituals</h3>
            <p>Browse our extensive library of rituals or get AI-powered suggestions based on occasions and auspicious dates.</p>
        </div>
        <div class="step-card glass-card tilt-card reveal-up">
            <div class="step-number-enhanced">3</div>
            <div class="step-icon"><i class="fas fa-pray"></i></div>
            <h3>Book a Pandit</h3>
            <p>Connect with verified Pandits, schedule the ceremony, and receive step-by-step guidance and shopping lists.</p>
        </div>
    </div>
</section>

<div class="section-divider"></div>

<!-- Features Section -->
<section class="features parallax-section" id="features">
    <div class="section-header reveal-up">
        <span class="section-tag">Why Choose Sanskar AI</span>
        <h2>Everything You Need for Sacred Ceremonies</h2>
        <p>From ancient wisdom to modern convenience, we bring you a complete platform for all your spiritual needs.</p>
    </div>
    
    <div class="features-grid stagger-children">
        <div class="feature-card glass-card tilt-card reveal-up">
            <div class="feature-icon-enhanced"><i class="fas fa-robot"></i></div>
            <h3>AI-Powered Guidance</h3>
            <p>Get personalized ritual recommendations based on your family traditions, auspicious dates, and specific occasions with our intelligent AI system.</p>
            <a href="/signup" class="feature-link magnetic-btn">Learn more <i class="fas fa-arrow-right"></i></a>
        </div>
        <div class="feature-card glass-card tilt-card reveal-up">
            <div class="feature-icon-enhanced"><i class="fas fa-pray"></i></div>
            <h3>Verified Pandits</h3>
            <p>Connect with experienced and verified Pandits who specialize in various rituals. Read reviews, check availability, and book with confidence.</p>
            <a href="/signup" class="feature-link magnetic-btn">Find Pandits <i class="fas fa-arrow-right"></i></a>
        </div>
        <div class="feature-card glass-card tilt-card reveal-up">
            <div class="feature-icon-enhanced"><i class="fas fa-book"></i></div>
            <h3>Step-by-Step Rituals</h3>
            <p>Access detailed guides for each ritual including significance, mantras, required items, and proper procedures explained in simple terms.</p>
            <a href="/signup" class="feature-link magnetic-btn">Explore Rituals <i class="fas fa-arrow-right"></i></a>
        </div>
        <div class="feature-card glass-card tilt-card reveal-up">
            <div class="feature-icon-enhanced"><i class="fas fa-shopping-basket"></i></div>
            <h3>Smart Shopping Lists</h3>
            <p>Automatically generate shopping lists for any ritual. Know exactly what items you need, quantities, and estimated costs before you begin.</p>
            <a href="/signup" class="feature-link magnetic-btn">Try it free <i class="fas fa-arrow-right"></i></a>
        </div>
        <div class="feature-card glass-card tilt-card reveal-up">
            <div class="feature-icon-enhanced"><i class="fas fa-users"></i></div>
            <h3>Family Profiles</h3>
            <p>Store your family's Gotra, Nakshatra, Kul Devi/Devta, and other details for personalized suggestions and ceremony planning.</p>
            <a href="/signup" class="feature-link magnetic-btn">Get started <i class="fas fa-arrow-right"></i></a>
        </div>
        <div class="feature-card glass-card tilt-card reveal-up">
            <div class="feature-icon-enhanced"><i class="fas fa-lightbulb"></i></div>
            <h3>Cultural Knowledge</h3>
            <p>Explore our extensive library of cultural insights, festival guides, and spiritual wisdom passed down through generations.</p>
            <a href="/signup" class="feature-link magnetic-btn">Discover more <i class="fas fa-arrow-right"></i></a>
        </div>
    </div>
</section>

<div class="section-divider"></div>

<!-- Featured Rituals -->
<?php if (!empty($featuredRituals)): ?>
<section class="rituals-showcase parallax-section" id="rituals">
    <div class="section-header reveal-up">
        <span class="section-tag">Popular Rituals</span>
        <h2>Explore Sacred Ceremonies</h2>
        <p>Discover the most sought-after rituals performed by families across India.</p>
    </div>
    
    <div class="rituals-slider stagger-children">
        <?php foreach ($featuredRituals as $index => $ritual): ?>
        <div class="ritual-card glass-card tilt-card reveal-up">
            <div class="ritual-image">
                <div class="ritual-overlay">
                    <i class="fas fa-om"></i>
                </div>
            </div>
            <div class="ritual-content">
                <div class="ritual-category"><?= htmlspecialchars($ritual['category'] ?? 'Puja') ?></div>
                <h3><?= htmlspecialchars($ritual['name']) ?></h3>
                <p><?= htmlspecialchars(substr($ritual['description'] ?? 'A sacred ceremony for spiritual well-being and divine blessings.', 0, 100)) ?>...</p>
                <div class="ritual-meta">
                    <span><i class="fas fa-clock"></i> <?= $ritual['duration_minutes'] ?> mins</span>
                    <span><i class="fas fa-signal"></i> <?= ucfirst($ritual['difficulty']) ?></span>
                </div>
                <a href="/signup" class="btn btn-primary btn-sm magnetic-btn">Learn More</a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    
    <div class="text-center reveal-up" style="margin-top: 40px;">
        <a href="/signup" class="btn btn-outline btn-lg magnetic-btn">
            <i class="fas fa-th-large"></i> View All Rituals
        </a>
    </div>
</section>

<div class="section-divider"></div>
<?php endif; ?>

<!-- Testimonials Section -->
<section class="testimonials parallax-section" id="testimonials">
    <div class="section-header reveal-up">
        <span class="section-tag">What People Say</span>
        <h2>Loved by Thousands of Families</h2>
        <p>See how Sanskar AI has helped families preserve their traditions</p>
    </div>
    
    <div class="testimonials-grid stagger-children">
        <div class="testimonial-card glass-card tilt-card reveal-up">
            <div class="testimonial-rating">
                <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
            </div>
            <p class="testimonial-text">"Sanskar AI made our daughter's Namkaran ceremony perfect. The step-by-step guide and pandit booking made everything so easy. Highly recommended!"</p>
            <div class="testimonial-author">
                <div class="author-avatar">RK</div>
                <div class="author-info">
                    <strong>Rajesh Kumar</strong>
                    <span>Mumbai, Maharashtra</span>
                </div>
            </div>
        </div>
        
        <div class="testimonial-card featured glass-card tilt-card reveal-up">
            <div class="testimonial-badge"><i class="fas fa-crown"></i> Top Review</div>
            <div class="testimonial-rating">
                <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
            </div>
            <p class="testimonial-text">"As a NRI family, we were disconnected from our traditions. Sanskar AI helped us reconnect with our roots. The cultural insights are invaluable, and the pandits are very knowledgeable."</p>
            <div class="testimonial-author">
                <div class="author-avatar">PS</div>
                <div class="author-info">
                    <strong>Priya Sharma</strong>
                    <span>California, USA</span>
                </div>
            </div>
        </div>
        
        <div class="testimonial-card glass-card tilt-card reveal-up">
            <div class="testimonial-rating">
                <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
            </div>
            <p class="testimonial-text">"The AI suggestions for auspicious dates and ritual recommendations are spot-on. It's like having a knowledgeable elder in the family!"</p>
            <div class="testimonial-author">
                <div class="author-avatar">AM</div>
                <div class="author-info">
                    <strong>Anita Mishra</strong>
                    <span>Delhi, India</span>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="section-divider"></div>

<!-- App Features Showcase -->
<section class="app-showcase parallax-section">
    <div class="app-content reveal-left">
        <span class="section-tag">Complete Solution</span>
        <h2>Everything at Your Fingertips</h2>
        <p>From ritual guides to pandit booking, shopping lists to cultural insights.</p>
        
        <div class="app-features-list stagger-children">
            <div class="app-feature reveal-up">
                <div class="app-feature-icon"><i class="fas fa-check-circle"></i></div>
                <div>
                    <strong>Ritual Guides</strong>
                    <p>Significance and mantras</p>
                </div>
            </div>
            <div class="app-feature reveal-up">
                <div class="app-feature-icon"><i class="fas fa-check-circle"></i></div>
                <div>
                    <strong>Pandit Booking</strong>
                    <p>Verified experts</p>
                </div>
            </div>
            <div class="app-feature reveal-up">
                <div class="app-feature-icon"><i class="fas fa-check-circle"></i></div>
                <div>
                    <strong>AI Suggestions</strong>
                    <p>Personalized rituals</p>
                </div>
            </div>
            <div class="app-feature reveal-up">
                <div class="app-feature-icon"><i class="fas fa-check-circle"></i></div>
                <div>
                    <strong>Shopping Lists</strong>
                    <p>Required items</p>
                </div>
            </div>
        </div>
        
        <a href="/signup" class="btn btn-primary btn-lg magnetic-btn pulse-glow">
            <i class="fas fa-rocket"></i> Start Your Journey
        </a>
    </div>
    <div class="app-visual reveal-right">
        <div class="phone-mockup">
            <div class="phone-screen">
                <div class="screen-header">
                    <i class="fas fa-om"></i>
                    <span>Sanskar AI</span>
                </div>
                <div class="screen-content">
                    <div class="screen-card">
                        <i class="fas fa-pray"></i>
                        <span>Find Pandit</span>
                    </div>
                    <div class="screen-card">
                        <i class="fas fa-book"></i>
                        <span>Rituals</span>
                    </div>
                    <div class="screen-card">
                        <i class="fas fa-shopping-cart"></i>
                        <span>Shopping</span>
                    </div>
                    <div class="screen-card">
                        <i class="fas fa-robot"></i>
                        <span>AI Guide</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="section-divider"></div>

<!-- FAQ Section -->
<section class="faq-section parallax-section" id="faq">
    <div class="section-header reveal-up">
        <span class="section-tag">Got Questions?</span>
        <h2>Frequently Asked Questions</h2>
        <p>Find answers to common questions about Sanskar AI</p>
    </div>
    
    <div class="faq-container stagger-children">
        <div class="faq-item glass-card reveal-up">
            <button class="faq-question" onclick="toggleFaq(this)">
                <span>What is Sanskar AI?</span>
                <i class="fas fa-plus"></i>
            </button>
            <div class="faq-answer">
                <p>Sanskar AI is a comprehensive platform that helps families discover, learn, and perform Hindu rituals. We provide AI-powered guidance, connect you with verified Pandits, and offer detailed step-by-step guides for all ceremonies.</p>
            </div>
        </div>
        
        <div class="faq-item glass-card reveal-up">
            <button class="faq-question" onclick="toggleFaq(this)">
                <span>How are Pandits verified on your platform?</span>
                <i class="fas fa-plus"></i>
            </button>
            <div class="faq-answer">
                <p>All Pandits on our platform go through a rigorous verification process. We verify their credentials, experience, and knowledge of rituals. They also receive reviews from users after each ceremony.</p>
            </div>
        </div>
        
        <div class="faq-item glass-card reveal-up">
            <button class="faq-question" onclick="toggleFaq(this)">
                <span>Is Sanskar AI free to use?</span>
                <i class="fas fa-plus"></i>
            </button>
            <div class="faq-answer">
                <p>Yes! Creating an account and accessing ritual guides, cultural insights, and AI suggestions is completely free. You only pay when you book a Pandit for a ceremony.</p>
            </div>
        </div>
        
        <div class="faq-item glass-card reveal-up">
            <button class="faq-question" onclick="toggleFaq(this)">
                <span>Can I use Sanskar AI from outside India?</span>
                <i class="fas fa-plus"></i>
            </button>
            <div class="faq-answer">
                <p>Absolutely! Many NRI families use Sanskar AI to stay connected with their traditions. You can access all guides and insights from anywhere. For ceremonies, we can help connect you with Pandits in your region.</p>
            </div>
        </div>
    </div>
</section>

<div class="section-divider"></div>

<!-- CTA Section -->
<section class="cta parallax-section" id="about">
    <div class="cta-box glass-card reveal-scale">
        <div class="cta-decoration">
            <span class="deco-item">ॐ</span>
            <span class="deco-item">🪔</span>
            <span class="deco-item">🌺</span>
        </div>
        <h2>Begin Your Spiritual Journey Today</h2>
        <p>Join thousands of families who trust Sanskar AI for their sacred ceremonies. Create your free account and discover the beauty of Hindu traditions.</p>
        <div class="cta-buttons">
            <a href="/signup" class="btn btn-primary btn-lg magnetic-btn pulse-glow">
                <i class="fas fa-user-plus"></i> Create Free Account
            </a>
            <a href="#features" class="btn btn-outline btn-lg magnetic-btn">
                <i class="fas fa-info-circle"></i> Learn More
            </a>
        </div>
        <div class="cta-trust">
            <span><i class="fas fa-lock"></i> Secure & Private</span>
            <span><i class="fas fa-credit-card"></i> No Credit Card Required</span>
            <span><i class="fas fa-clock"></i> Setup in 2 Minutes</span>
        </div>
    </div>
</section>

<script>
// ============================================================
// ENHANCED LANDING PAGE SCRIPTS
// ============================================================

// ── Typing Effect for Hero Subtitle ──
(function() {
    const subtitle = document.getElementById('heroSubtitle');
    if (!subtitle) return;
    
    const phrases = [
        'Connect with verified Pandits for every sacred ceremony.',
        'Discover ancient rituals with AI-powered guidance.',
        'Preserve your family\'s spiritual heritage with ease.',
        'Smart shopping lists for all your puja needs.'
    ];
    
    let phraseIndex = 0;
    let charIndex = 0;
    let isDeleting = false;
    let typingSpeed = 40;
    
    function type() {
        const currentPhrase = phrases[phraseIndex];
        
        if (isDeleting) {
            subtitle.textContent = currentPhrase.substring(0, charIndex - 1);
            charIndex--;
            typingSpeed = 20;
        } else {
            subtitle.textContent = currentPhrase.substring(0, charIndex + 1);
            charIndex++;
            typingSpeed = 40;
        }
        
        if (!isDeleting && charIndex === currentPhrase.length) {
            typingSpeed = 2500; // Pause at end
            isDeleting = true;
        } else if (isDeleting && charIndex === 0) {
            isDeleting = false;
            phraseIndex = (phraseIndex + 1) % phrases.length;
            typingSpeed = 400; // Pause before new phrase
        }
        
        setTimeout(type, typingSpeed);
    }
    
    type();
})();

// ── Enhanced Scroll Reveal ──
(function() {
    const revealElements = document.querySelectorAll('.reveal-up, .reveal-left, .reveal-right, .reveal-scale');
    
    const revealObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('revealed');
                // Don't unobserve — let it re-trigger if needed
            }
        });
    }, {
        threshold: 0.1,
        rootMargin: '0px 0px -60px 0px'
    });
    
    revealElements.forEach(el => revealObserver.observe(el));
})();

// ── FAQ Toggle ──
function toggleFaq(el) {
    const item = el.parentElement;
    const isOpen = item.classList.contains('active');
    
    // Close all
    document.querySelectorAll('.faq-item').forEach(faq => {
        faq.classList.remove('active');
        faq.querySelector('.faq-question i').className = 'fas fa-plus';
    });
    
    // Open clicked if was closed
    if (!isOpen) {
        item.classList.add('active');
        el.querySelector('i').className = 'fas fa-minus';
    }
}

// ── ALSO keep the existing data-animate observer for backward compat ──
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
};

const animateOnScroll = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            const delay = entry.target.getAttribute('data-delay') || 0;
            setTimeout(() => {
                entry.target.classList.add('animated');
            }, delay);
        }
    });
}, observerOptions);

document.querySelectorAll('[data-animate]').forEach(el => {
    animateOnScroll.observe(el);
});

// ── Counter Animation ──
function animateCounter(el) {
    const target = parseInt(el.getAttribute('data-count'));
    if (!target) return;
    
    let current = 0;
    const increment = target / 60;
    const timer = setInterval(() => {
        current += increment;
        if (current >= target) {
            el.textContent = target + '+';
            clearInterval(timer);
        } else {
            el.textContent = Math.floor(current) + '+';
        }
    }, 25);
}

// Trigger counter animation when stats section is visible
const statsObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            document.querySelectorAll('.stat-value[data-count]').forEach(animateCounter);
            statsObserver.unobserve(entry.target);
        }
    });
}, { threshold: 0.5 });

const statsSection = document.querySelector('.stats');
if (statsSection) statsObserver.observe(statsSection);

// ── Magnetic Button Effect ──
document.querySelectorAll('.magnetic-btn').forEach(btn => {
    btn.addEventListener('mousemove', (e) => {
        const rect = btn.getBoundingClientRect();
        const x = e.clientX - rect.left - rect.width / 2;
        const y = e.clientY - rect.top - rect.height / 2;
        btn.style.transform = `translate(${x * 0.15}px, ${y * 0.15}px)`;
    });
    
    btn.addEventListener('mouseleave', () => {
        btn.style.transform = '';
    });
});

// ── Smooth parallax on scroll ──
let ticking = false;
window.addEventListener('scroll', () => {
    if (!ticking) {
        requestAnimationFrame(() => {
            const scrolled = window.pageYOffset;
            const heroOm = document.querySelector('.hero-om-bg');
            if (heroOm) {
                heroOm.style.transform = `translate(-50%, calc(-50% + ${scrolled * 0.15}px))`;
            }
            ticking = false;
        });
        ticking = true;
    }
});

// ── Nav scroll effect ──
window.addEventListener('scroll', () => {
    const nav = document.querySelector('nav');
    if (nav) {
        if (window.scrollY > 80) {
            nav.classList.add('scrolled');
        } else {
            nav.classList.remove('scrolled');
        }
    }
});
</script>
