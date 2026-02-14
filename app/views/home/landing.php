<!-- Hero Section -->
<section class="hero">
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
        <div class="hero-badge">
            <i class="fas fa-star"></i>
            <span>Trusted by 10,000+ families across India</span>
        </div>
        
        <h1>Your Spiritual Journey<br>Starts Here</h1>
        
        <p class="hero-subtitle">Connect with verified Pandits, discover sacred rituals, and preserve your family's spiritual heritage with AI-powered guidance for every ceremony.</p>
        
        <div class="hero-buttons">
            <a href="/signup" class="btn btn-primary btn-lg">
                <i class="fas fa-rocket"></i> Get Started Free
            </a>
            <a href="#how-it-works" class="btn btn-outline btn-lg">
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
    
    <!-- Scroll Indicator -->
    <!-- <div class="scroll-indicator">
        <span>Scroll to explore</span>
        <i class="fas fa-chevron-down"></i>
    </div> -->
</section>

<!-- Stats Section -->
<section class="stats">
    <div class="stat-card" data-animate="fade-up" data-delay="0">
        <div class="stat-icon"><i class="fas fa-book-open"></i></div>
        <div class="stat-value" data-count="<?= $stats['rituals'] ?? 100 ?>"><?= $stats['rituals'] ?? 100 ?>+</div>
        <div class="stat-label">Sacred Rituals</div>
    </div>
    <div class="stat-card" data-animate="fade-up" data-delay="100">
        <div class="stat-icon"><i class="fas fa-user-tie"></i></div>
        <div class="stat-value" data-count="<?= $stats['pandits'] ?? 50 ?>"><?= $stats['pandits'] ?? 50 ?>+</div>
        <div class="stat-label">Verified Pandits</div>
    </div>
    <div class="stat-card" data-animate="fade-up" data-delay="200">
        <div class="stat-icon"><i class="fas fa-users"></i></div>
        <div class="stat-value">10K+</div>
        <div class="stat-label">Happy Families</div>
    </div>
    <div class="stat-card" data-animate="fade-up" data-delay="300">
        <div class="stat-icon"><i class="fas fa-scroll"></i></div>
        <div class="stat-value" data-count="<?= $stats['insights'] ?? 200 ?>"><?= $stats['insights'] ?? 200 ?>+</div>
        <div class="stat-label">Cultural Insights</div>
    </div>
</section>

<!-- How It Works Section -->
<section class="how-it-works" id="how-it-works">
    <div class="section-header" data-animate="fade-up">
        <span class="section-tag">Simple & Easy</span>
        <h2>How Sanskar AI Works</h2>
        <p>Get started in just three simple steps and experience the divine journey</p>
    </div>
    
    <div class="steps-container">
        <div class="step-line"></div>
        <div class="step-card" data-animate="fade-up" data-delay="0">
            <div class="step-number">1</div>
            <div class="step-icon"><i class="fas fa-user-plus"></i></div>
            <h3>Create Your Profile</h3>
            <p>Sign up and add your family details including Gotra, Nakshatra, and Kul Devta for personalized recommendations.</p>
        </div>
        <div class="step-card" data-animate="fade-up" data-delay="150">
            <div class="step-number">2</div>
            <div class="step-icon"><i class="fas fa-search"></i></div>
            <h3>Explore Rituals</h3>
            <p>Browse our extensive library of rituals or get AI-powered suggestions based on occasions and auspicious dates.</p>
        </div>
        <div class="step-card" data-animate="fade-up" data-delay="300">
            <div class="step-number">3</div>
            <div class="step-icon"><i class="fas fa-pray"></i></div>
            <h3>Book a Pandit</h3>
            <p>Connect with verified Pandits, schedule the ceremony, and receive step-by-step guidance and shopping lists.</p>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="features" id="features">
    <div class="section-header" data-animate="fade-up">
        <span class="section-tag">Why Choose Sanskar AI</span>
        <h2>Everything You Need for Sacred Ceremonies</h2>
        <p>From ancient wisdom to modern convenience, we bring you a complete platform for all your spiritual needs.</p>
    </div>
    
    <div class="features-grid">
        <div class="feature-card" data-animate="fade-up" data-delay="0">
            <div class="feature-icon"><i class="fas fa-robot"></i></div>
            <h3>AI-Powered Guidance</h3>
            <p>Get personalized ritual recommendations based on your family traditions, auspicious dates, and specific occasions with our intelligent AI system.</p>
            <a href="/signup" class="feature-link">Learn more <i class="fas fa-arrow-right"></i></a>
        </div>
        <div class="feature-card" data-animate="fade-up" data-delay="100">
            <div class="feature-icon"><i class="fas fa-pray"></i></div>
            <h3>Verified Pandits</h3>
            <p>Connect with experienced and verified Pandits who specialize in various rituals. Read reviews, check availability, and book with confidence.</p>
            <a href="/signup" class="feature-link">Find Pandits <i class="fas fa-arrow-right"></i></a>
        </div>
        <div class="feature-card" data-animate="fade-up" data-delay="200">
            <div class="feature-icon"><i class="fas fa-book"></i></div>
            <h3>Step-by-Step Rituals</h3>
            <p>Access detailed guides for each ritual including mantras, significance, required items, and proper procedures explained in simple terms.</p>
            <a href="/signup" class="feature-link">Explore Rituals <i class="fas fa-arrow-right"></i></a>
        </div>
        <div class="feature-card" data-animate="fade-up" data-delay="0">
            <div class="feature-icon"><i class="fas fa-shopping-basket"></i></div>
            <h3>Smart Shopping Lists</h3>
            <p>Automatically generate shopping lists for any ritual. Know exactly what items you need, quantities, and estimated costs before you begin.</p>
            <a href="/signup" class="feature-link">Try it free <i class="fas fa-arrow-right"></i></a>
        </div>
        <div class="feature-card" data-animate="fade-up" data-delay="100">
            <div class="feature-icon"><i class="fas fa-users"></i></div>
            <h3>Family Profiles</h3>
            <p>Store your family's Gotra, Nakshatra, Kul Devta, and other details for personalized suggestions and ceremony planning.</p>
            <a href="/signup" class="feature-link">Get started <i class="fas fa-arrow-right"></i></a>
        </div>
        <div class="feature-card" data-animate="fade-up" data-delay="200">
            <div class="feature-icon"><i class="fas fa-lightbulb"></i></div>
            <h3>Cultural Knowledge</h3>
            <p>Explore our extensive library of cultural insights, festival guides, and spiritual wisdom passed down through generations.</p>
            <a href="/signup" class="feature-link">Discover more <i class="fas fa-arrow-right"></i></a>
        </div>
    </div>
</section>

<!-- Featured Rituals -->
<?php if (!empty($featuredRituals)): ?>
<section class="rituals-showcase" id="rituals">
    <div class="section-header" data-animate="fade-up">
        <span class="section-tag">Popular Rituals</span>
        <h2>Explore Sacred Ceremonies</h2>
        <p>Discover the most sought-after rituals performed by families across India.</p>
    </div>
    
    <div class="rituals-slider">
        <?php foreach ($featuredRituals as $index => $ritual): ?>
        <div class="ritual-card" data-animate="fade-up" data-delay="<?= $index * 100 ?>">
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
                <a href="/signup" class="btn btn-primary btn-sm">Learn More</a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    
    <div class="text-center" style="margin-top: 40px;">
        <a href="/signup" class="btn btn-outline btn-lg">
            <i class="fas fa-th-large"></i> View All Rituals
        </a>
    </div>
</section>
<?php endif; ?>

<!-- Testimonials Section -->
<section class="testimonials" id="testimonials">
    <div class="section-header" data-animate="fade-up">
        <span class="section-tag">What People Say</span>
        <h2>Loved by Thousands of Families</h2>
        <p>See how Sanskar AI has helped families preserve their traditions</p>
    </div>
    
    <div class="testimonials-grid">
        <div class="testimonial-card" data-animate="fade-up" data-delay="0">
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
        
        <div class="testimonial-card featured" data-animate="fade-up" data-delay="100">
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
        
        <div class="testimonial-card" data-animate="fade-up" data-delay="200">
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

<!-- App Features Showcase -->
<section class="app-showcase">
    <div class="app-content" data-animate="fade-up">
        <span class="section-tag">Complete Solution</span>
        <h2>Everything at Your Fingertips</h2>
        <p>From ritual guides to pandit booking, shopping lists to cultural insights - all in one place.</p>
        
        <div class="app-features-list">
            <div class="app-feature">
                <div class="app-feature-icon"><i class="fas fa-check-circle"></i></div>
                <div>
                    <strong>100+ Rituals Explained</strong>
                    <p>Detailed guides with mantras and significance</p>
                </div>
            </div>
            <div class="app-feature">
                <div class="app-feature-icon"><i class="fas fa-check-circle"></i></div>
                <div>
                    <strong>Expert Pandits Network</strong>
                    <p>Verified and experienced across all India</p>
                </div>
            </div>
            <div class="app-feature">
                <div class="app-feature-icon"><i class="fas fa-check-circle"></i></div>
                <div>
                    <strong>AI-Powered Suggestions</strong>
                    <p>Personalized recommendations for your family</p>
                </div>
            </div>
            <div class="app-feature">
                <div class="app-feature-icon"><i class="fas fa-check-circle"></i></div>
                <div>
                    <strong>Smart Shopping Lists</strong>
                    <p>Auto-generated with quantities and costs</p>
                </div>
            </div>
        </div>
        
        <a href="/signup" class="btn btn-primary btn-lg">
            <i class="fas fa-rocket"></i> Start Your Journey
        </a>
    </div>
    <div class="app-visual" data-animate="fade-left">
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

<!-- FAQ Section -->
<section class="faq-section" id="faq">
    <div class="section-header" data-animate="fade-up">
        <span class="section-tag">Got Questions?</span>
        <h2>Frequently Asked Questions</h2>
        <p>Find answers to common questions about Sanskar AI</p>
    </div>
    
    <div class="faq-container">
        <div class="faq-item" data-animate="fade-up" data-delay="0">
            <button class="faq-question" onclick="toggleFaq(this)">
                <span>What is Sanskar AI?</span>
                <i class="fas fa-plus"></i>
            </button>
            <div class="faq-answer">
                <p>Sanskar AI is a comprehensive platform that helps families discover, learn, and perform Hindu rituals. We provide AI-powered guidance, connect you with verified Pandits, and offer detailed step-by-step guides for all ceremonies.</p>
            </div>
        </div>
        
        <div class="faq-item" data-animate="fade-up" data-delay="100">
            <button class="faq-question" onclick="toggleFaq(this)">
                <span>How are Pandits verified on your platform?</span>
                <i class="fas fa-plus"></i>
            </button>
            <div class="faq-answer">
                <p>All Pandits on our platform go through a rigorous verification process. We verify their credentials, experience, and knowledge of rituals. They also receive reviews from users after each ceremony.</p>
            </div>
        </div>
        
        <div class="faq-item" data-animate="fade-up" data-delay="200">
            <button class="faq-question" onclick="toggleFaq(this)">
                <span>Is Sanskar AI free to use?</span>
                <i class="fas fa-plus"></i>
            </button>
            <div class="faq-answer">
                <p>Yes! Creating an account and accessing ritual guides, cultural insights, and AI suggestions is completely free. You only pay when you book a Pandit for a ceremony.</p>
            </div>
        </div>
        
        <div class="faq-item" data-animate="fade-up" data-delay="300">
            <button class="faq-question" onclick="toggleFaq(this)">
                <span>Can I use Sanskar AI from outside India?</span>
                <i class="fas fa-plus"></i>
            </button>
            <div class="faq-answer">
                <p>Absolutely! Many NRI families use Sanskar AI to stay connected with their traditions. You can access all guides and insights from anywhere. For ceremonies, we can help connect you with Pandits in your region.</p>
            </div>
        </div>
        
        <div class="faq-item" data-animate="fade-up" data-delay="400">
            <button class="faq-question" onclick="toggleFaq(this)">
                <span>How does the AI suggestion feature work?</span>
                <i class="fas fa-plus"></i>
            </button>
            <div class="faq-answer">
                <p>Our AI analyzes your family profile (Gotra, Nakshatra, location), the occasion, and auspicious dates to recommend the most suitable rituals and provide guidance on how to perform them.</p>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta" id="about">
    <div class="cta-box" data-animate="fade-up">
        <div class="cta-decoration">
            <span class="deco-item">ॐ</span>
            <span class="deco-item">🪔</span>
            <span class="deco-item">🌺</span>
        </div>
        <h2>Begin Your Spiritual Journey Today</h2>
        <p>Join thousands of families who trust Sanskar AI for their sacred ceremonies. Create your free account and discover the beauty of Hindu traditions.</p>
        <div class="cta-buttons">
            <a href="/signup" class="btn btn-primary btn-lg">
                <i class="fas fa-user-plus"></i> Create Free Account
            </a>
            <a href="#features" class="btn btn-outline btn-lg">
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
// FAQ Toggle
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

// Scroll Animation
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

// Counter Animation
function animateCounter(el) {
    const target = parseInt(el.getAttribute('data-count'));
    if (!target) return;
    
    let current = 0;
    const increment = target / 50;
    const timer = setInterval(() => {
        current += increment;
        if (current >= target) {
            el.textContent = target + '+';
            clearInterval(timer);
        } else {
            el.textContent = Math.floor(current) + '+';
        }
    }, 30);
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
</script>
