/**
 * SWAMITIME SOLUTIONS LTD - Main JavaScript
 */

document.addEventListener('DOMContentLoaded', function() {
    
    // ===== Scroll to Top Button =====
    const scrollTopBtn = document.getElementById('scroll-top');
    if (scrollTopBtn) {
        window.addEventListener('scroll', function() {
            if (window.scrollY > 500) {
                scrollTopBtn.classList.add('visible');
            } else {
                scrollTopBtn.classList.remove('visible');
            }
        });
        scrollTopBtn.addEventListener('click', function() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }
    
    // ===== Navbar Scroll Effect =====
    const navbar = document.querySelector('.navbar');
    if (navbar) {
        window.addEventListener('scroll', function() {
            if (window.scrollY > 50) {
                navbar.classList.add('navbar-scrolled');
            } else {
                navbar.classList.remove('navbar-scrolled');
            }
        });
    }
    
    // ===== Smooth Scroll for Anchor Links =====
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const targetId = this.getAttribute('href');
            if (targetId === '#') return;
            const target = document.querySelector(targetId);
            if (target) {
                e.preventDefault();
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });
    
    // ===== Mobile Menu Active Link =====
    const currentPath = window.location.pathname;
    document.querySelectorAll('.navbar .nav-link, .offcanvas .nav-link').forEach(link => {
        if (link.getAttribute('href') === currentPath || 
            (currentPath !== '/' && link.getAttribute('href') !== '/' && currentPath.includes(link.getAttribute('href')))) {
            link.classList.add('active');
        }
    });
    
    // ===== Counter Animation (for trust metrics) =====
    function animateCounters() {
        document.querySelectorAll('.metric-number[data-count]').forEach(counter => {
            const target = parseInt(counter.getAttribute('data-count'));
            const duration = 2000;
            const step = target / (duration / 16);
            let current = 0;
            
            const updateCounter = () => {
                current += step;
                if (current < target) {
                    counter.textContent = Math.floor(current);
                    requestAnimationFrame(updateCounter);
                } else {
                    counter.textContent = target;
                }
            };
            updateCounter();
        });
    }
    
    // Intersection Observer for counters
    const counterObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                animateCounters();
                counterObserver.unobserve(entry.target);
            }
        });
    }, { threshold: 0.5 });
    
    const metricSection = document.querySelector('.trust-metrics-section');
    if (metricSection) counterObserver.observe(metricSection);
    
    // ===== Lazy Loading Images =====
    if ('loading' in HTMLImageElement.prototype) {
        document.querySelectorAll('img[loading="lazy"]').forEach(img => {
            img.src = img.dataset.src;
        });
    } else {
        // Fallback lazy loading
        const lazyImages = document.querySelectorAll('img[data-src]');
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.removeAttribute('data-src');
                    observer.unobserve(img);
                }
            });
        });
        lazyImages.forEach(img => imageObserver.observe(img));
    }
    
    // ===== Form Validation =====
    const contactForm = document.getElementById('contact-form');
    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            let isValid = true;
            const requiredFields = contactForm.querySelectorAll('[required]');
            
            requiredFields.forEach(field => {
                const formGroup = field.closest('.form-group') || field.parentElement;
                const errorEl = formGroup.querySelector('.invalid-feedback');
                
                if (!field.value.trim()) {
                    isValid = false;
                    field.classList.add('is-invalid');
                    if (errorEl) errorEl.textContent = 'This field is required';
                } else {
                    field.classList.remove('is-invalid');
                }
                
                // Email validation
                if (field.type === 'email' && field.value.trim()) {
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailRegex.test(field.value)) {
                        isValid = false;
                        field.classList.add('is-invalid');
                        if (errorEl) errorEl.textContent = 'Please enter a valid email address';
                    }
                }
                
                // Phone validation
                if (field.type === 'tel' && field.value.trim()) {
                    const phoneRegex = /^[\d\s\-\+\(\)]{7,20}$/;
                    if (!phoneRegex.test(field.value)) {
                        isValid = false;
                        field.classList.add('is-invalid');
                        if (errorEl) errorEl.textContent = 'Please enter a valid phone number';
                    }
                }
            });
            
            // GDPR consent check
            const gdprCheckbox = contactForm.querySelector('#gdpr-consent');
            if (gdprCheckbox && !gdprCheckbox.checked) {
                isValid = false;
                const gdprGroup = gdprCheckbox.closest('.form-check');
                const errorEl = gdprGroup.querySelector('.invalid-feedback');
                if (errorEl) errorEl.textContent = 'You must agree to the privacy policy';
                gdprCheckbox.classList.add('is-invalid');
            }
            
            if (!isValid) {
                e.preventDefault();
                // Scroll to first error
                const firstError = contactForm.querySelector('.is-invalid');
                if (firstError) firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });
        
        // Clear validation on input
        contactForm.querySelectorAll('input, textarea, select').forEach(field => {
            field.addEventListener('input', function() {
                this.classList.remove('is-invalid');
            });
        });
    }
    
    // ===== FAQ Accordion Enhancement =====
    // Bootstrap handles accordion natively, but add URL hash support
    if (window.location.hash && document.querySelector(window.location.hash)) {
        const targetAccordion = document.querySelector(window.location.hash);
        const bsCollapse = new bootstrap.Collapse(targetAccordion, { toggle: true });
    }
    
    // ===== Copy to Clipboard (for any code blocks) =====
    document.querySelectorAll('.copy-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const target = document.querySelector(this.dataset.target);
            if (target) {
                navigator.clipboard.writeText(target.textContent).then(() => {
                    const original = this.innerHTML;
                    this.innerHTML = '<i class="bi bi-check"></i> Copied';
                    setTimeout(() => { this.innerHTML = original; }, 2000);
                });
            }
        });
    });
    
    // ===== Back to top visibility on scroll =====
    // Already handled above
    
    // ===== Search functionality (if search bar present) =====
    const searchInput = document.getElementById('blog-search');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const filter = this.value.toLowerCase();
            document.querySelectorAll('.blog-card, .searchable-item').forEach(item => {
                const text = item.textContent.toLowerCase();
                item.style.display = text.includes(filter) ? '' : 'none';
            });
        });
    }
    
    // ===== Newsletter Form =====
    const newsletterForm = document.getElementById('newsletter-form');
    if (newsletterForm) {
        newsletterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const emailInput = this.querySelector('input[type="email"]');
            const email = emailInput.value.trim();
            
            if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                emailInput.classList.add('is-invalid');
                return;
            }
            
            // AJAX submission
            const formData = new FormData();
            formData.append('email', email);
            formData.append('action', 'newsletter_subscribe');
            
            fetch('/api/newsletter.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                const messageEl = document.getElementById('newsletter-message');
                if (messageEl) {
                    messageEl.textContent = data.message || 'Thank you for subscribing!';
                    messageEl.className = data.success ? 'text-success' : 'text-danger';
                    messageEl.style.display = 'block';
                }
                if (data.success) {
                    emailInput.value = '';
                }
            })
            .catch(() => {
                const messageEl = document.getElementById('newsletter-message');
                if (messageEl) {
                    messageEl.textContent = 'Something went wrong. Please try again.';
                    messageEl.className = 'text-danger';
                    messageEl.style.display = 'block';
                }
            });
        });
    }
    
    // ===== Service Filter (if on services page) =====
    const serviceFilter = document.querySelector('.service-filter');
    if (serviceFilter) {
        serviceFilter.addEventListener('change', function() {
            const category = this.value;
            document.querySelectorAll('.service-card-wrapper').forEach(card => {
                if (!category || card.dataset.category === category) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    }
    
    // ===== Active nav link based on scroll position (for single page sections) =====
    // Only on homepage
    if (document.querySelector('.hero-section')) {
        const sections = document.querySelectorAll('section[id]');
        window.addEventListener('scroll', function() {
            let current = '';
            sections.forEach(section => {
                const sectionTop = section.offsetTop - 100;
                if (window.scrollY >= sectionTop) {
                    current = section.getAttribute('id');
                }
            });
            
            document.querySelectorAll('.navbar .nav-link').forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('href') === '#' + current) {
                    link.classList.add('active');
                }
            });
        });
    }
});
