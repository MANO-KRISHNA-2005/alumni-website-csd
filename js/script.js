document.addEventListener('DOMContentLoaded', function () {
    console.log('=== DOM CONTENT LOADED ===');

    // Main initialization order
    initPreloader();
    initCountdown();
    initStatsCounter();

    // Alumni Slider + Typing
    initAlumniSliderWithFallback();
    setTimeout(() => {
        initAlumniTypingEffect();
    }, 500);

    // Core Website Components
initNavbarScrollEffects(); // NEW: Enhanced navbar scroll effects
    initMobileMenu();
    initScheduleTimeline();
    initGalleryCarousel();
    initMapFix();
    initLogoSliders();
    initRebootAnimations();
    initCompaniesSection();
    initRebootTextAnimation();
    initAlumniMeetTypingEffect();

    // Utility Components
    initScrollReveal?.();
    initParallax?.();
    initImageLoading?.();
    initResizeHandler?.();
    initMobileOptimizations?.();
    initLazyLoading?.();
    initPageTransitions?.();

    // Scroll to Top Button
    const button = document.querySelector('#go-to-top-btn');
    const heroSection = document.querySelector('#home');
    if (button && heroSection) {
        button.addEventListener('click', () => {
            heroSection.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        });
    }

    console.log('REBOOT Alumni website initialized successfully');
});

// Preloader functionality - UPDATED
// Universal Preloader functionality - Works for all sections
function initPreloader() {
    const splash = document.getElementById('splash');
    const mainContent = document.getElementById('main-content');
    
    if (!splash || !mainContent) return;
    
    // Check if we are coming from gallery.html to skip preloader and scroll to a specific section
    if (sessionStorage.getItem('skipPreloader') === 'true') {
        // Get the target section from sessionStorage
        const targetSectionId = sessionStorage.getItem('targetSection');
        
        // Hide preloader immediately
        splash.style.display = 'none';
        mainContent.style.display = 'block';
        document.body.classList.add('loaded');
        
        // Scroll to the target section after a tiny delay to ensure DOM is fully rendered
        setTimeout(() => {
            if (targetSectionId) {
                const targetSection = document.getElementById(targetSectionId);
                if (targetSection) {
                    targetSection.scrollIntoView({ behavior: 'smooth' });
                }
            }
            
            // Clear the flags
            sessionStorage.removeItem('skipPreloader');
            sessionStorage.removeItem('targetSection');
        }, 100);
    } else {
        // Normal preloader behavior
        setTimeout(() => {
            splash.style.display = 'none';
            mainContent.style.display = 'block';
            
            // Trigger page load animation
            document.body.classList.add('loaded');

            // Check if there is a hash in the URL and scroll to that section
            // This handles direct links like index.html#contact
            if (window.location.hash) {
                const hash = window.location.hash.substring(1); // Remove the #
                setTimeout(() => {
                    const targetSection = document.getElementById(hash);
                    if (targetSection) {
                        targetSection.scrollIntoView({ behavior: 'smooth' });
                    }
                }, 100);
            }

            // FIX: Refresh map safely after content is visible
            const mapIframe = document.querySelector('.contact-map iframe');
            if (mapIframe) {
                // Store the original src
                const originalSrc = mapIframe.src;
                
                // Force a clean reload without setting src to empty first
                mapIframe.src = originalSrc;
            }
        }, 3000);
    }
}

// Fix for map loading issues - SIMPLIFIED VERSION
function initMapFix() {
    const mapIframe = document.querySelector('.contact-map iframe');
    if (mapIframe) {
        // ERROR FIX: Removed code that set src='' 
        // That code was causing the "mini website" effect.
        
        // Add error handling just in case
        mapIframe.addEventListener('error', function() {
            console.error('Map failed to load');
            const mapContainer = document.querySelector('.contact-map');
            if (mapContainer) {
                mapContainer.innerHTML = '<div class="map-error" style="color:white; display:flex; justify-content:center; align-items:center; height:100%;">Map could not be loaded. Please check your connection.</div>';
            }
        });
        
        // Add loading attribute for better performance
        mapIframe.setAttribute('loading', 'lazy');
        
        // Add some additional attributes to help with mobile rendering
        mapIframe.setAttribute('frameborder', '0');
        mapIframe.setAttribute('scrolling', 'no');
        mapIframe.setAttribute('marginheight', '0');
        mapIframe.setAttribute('marginwidth', '0');
    }
}

// Enhanced mobile menu functionality
function initMobileMenu() {
    const hamburger = document.querySelector('.hamburger');
    const navMenu = document.querySelector('.nav-menu');
    const navLinks = document.querySelectorAll('.nav-link');
    
    if (!hamburger || !navMenu) return;
    
    // Toggle mobile menu
    hamburger.addEventListener('click', function(e) {
        e.stopPropagation();
        navMenu.classList.toggle('active');
        document.body.style.overflow = navMenu.classList.contains('active') ? 'hidden' : '';
        
        // Update hamburger icon
        const icon = hamburger.querySelector('i');
        if (icon) {
            icon.className = navMenu.classList.contains('active') ? 'fas fa-times' : 'fas fa-bars';
        }
    });
    
    // Close menu when clicking on links
    navLinks.forEach(link => {
        link.addEventListener('click', function() {
            navMenu.classList.remove('active');
            document.body.style.overflow = '';
            
            // Reset hamburger icon
            const icon = hamburger.querySelector('i');
            if (icon) {
                icon.className = 'fas fa-bars';
            }
        });
    });
    
    // Close menu when clicking outside
    document.addEventListener('click', function(e) {
        if (!navMenu.contains(e.target) && !hamburger.contains(e.target)) {
            navMenu.classList.remove('active');
            document.body.style.overflow = '';
            
            // Reset hamburger icon
            const icon = hamburger.querySelector('i');
            if (icon) {
                icon.className = 'fas fa-bars';
            }
        }
    });
    
    // Close menu on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && navMenu.classList.contains('active')) {
            navMenu.classList.remove('active');
            document.body.style.overflow = '';
            
            const icon = hamburger.querySelector('i');
            if (icon) {
                icon.className = 'fas fa-bars';
            }
        }
    });
}

// Navigation functionality
function initNavbarScrollEffects() {
    const navbar = document.querySelector('.navbar');
    
    if (!navbar) return;
    
    // Only handle the background color transparency
    window.addEventListener('scroll', () => {
        if (window.scrollY > 10) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    });
}
// Countdown timer
function initCountdown() {
    const countdownElement = document.getElementById('countdown');
    if (!countdownElement) return;
    
    const countdownDate = new Date('January 3, 2026 09:00:00').getTime();
    
    function updateCountdown() {
        const now = new Date().getTime();
        const distance = countdownDate - now;
        
        // Time calculations
        const days = Math.floor(distance / (1000 * 60 * 60 * 24));
        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);
        
        // Update display
        const daysElement = document.getElementById('days');
        const hoursElement = document.getElementById('hours');
        const minutesElement = document.getElementById('minutes');
        const secondsElement = document.getElementById('seconds');
        
        if (daysElement) daysElement.textContent = days.toString().padStart(2, '0');
        if (hoursElement) hoursElement.textContent = hours.toString().padStart(2, '0');
        if (minutesElement) minutesElement.textContent = minutes.toString().padStart(2, '0');
        if (secondsElement) secondsElement.textContent = seconds.toString().padStart(2, '0');
        
        // If the countdown is finished
        if (distance < 0) {
            clearInterval(countdownInterval);
            countdownElement.innerHTML = '<div class="countdown-finished">Event Started!</div>';
        }
    }
    
    // Update immediately and then every second
    updateCountdown();
    const countdownInterval = setInterval(updateCountdown, 1000);
}

// Stats counter animation
function initStatsCounter() {
    const statNumbers = document.querySelectorAll('.stat-number');
    
    function startCounting(element) {
        const target = parseInt(element.getAttribute('data-count'));
        const duration = 2000; // 2 seconds
        const step = target / (duration / 16); // 60fps
        let current = 0;
        
        const timer = setInterval(() => {
            current += step;
            if (current >= target) {
                element.textContent = target;
                clearInterval(timer);
            } else {
                element.textContent = Math.floor(current);
            }
        }, 16);
    }
    
    // Intersection Observer to trigger counting when in view
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                startCounting(entry.target);
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.5 });
    
    statNumbers.forEach(stat => {
        observer.observe(stat);
    });
}

// FIXED Alumni Slider - PROPER AUTO AND MANUAL FUNCTIONALITY
function initAlumniSlider() {
    console.log('=== INITIALIZING ALUMNI SLIDER ===');
    
    const slider = document.querySelector('.alumni-slider');
    const slides = document.querySelectorAll('.swiper-slide');
    const nextBtn = document.querySelector('.swiper-button-next');
    const prevBtn = document.querySelector('.swiper-button-prev');
    
    if (!slider || slides.length === 0) {
        console.error('Alumni slider not found or empty');
        return;
    }
    
    console.log('Found slider and', slides.length, 'slides');
    
    let currentIndex = 0;
    let autoplayInterval = null;
    let isTransitioning = false;
    let touchStartX = 0;
    
    // Initialize slider styles
    function initializeSliderStyles() {
        slides.forEach((slide, i) => {
            slide.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
            slide.style.position = 'absolute';
            slide.style.top = '0';
            slide.style.left = '0';
            slide.style.width = '100%';
            slide.style.height = '100%';
            
            if (i === 0) {
                slide.style.opacity = '1';
                slide.style.transform = 'translateX(0)';
                slide.style.display = 'block';
                slide.style.zIndex = '1';
            } else {
                slide.style.opacity = '0';
                slide.style.transform = 'translateX(100%)';
                slide.style.display = 'block';
                slide.style.zIndex = '0';
            }
        });
    }
    
    // Show slide with smooth transition
    function showSlide(index) {
        if (isTransitioning) return;
        isTransitioning = true;
        
        console.log('Showing slide:', index);
        
        // Calculate new index with wrap-around
        const newIndex = (index + slides.length) % slides.length;
        
        // Get current and next slides
        const currentSlide = slides[currentIndex];
        const nextSlide = slides[newIndex];
        
        // Set z-index for proper stacking
        currentSlide.style.zIndex = '1';
        nextSlide.style.zIndex = '2';
        
        // Fade out current slide
        currentSlide.style.opacity = '0';
        currentSlide.style.transform = 'translateX(-100%)';
        
        // Position and fade in next slide
        nextSlide.style.opacity = '1';
        nextSlide.style.transform = 'translateX(0)';
        nextSlide.style.display = 'block';
        
        // Update current index
        currentIndex = newIndex;
        
        // Reset transitioning flag after animation
        setTimeout(() => {
            // Reset z-index and hide old slides
            slides.forEach((slide, i) => {
                if (i !== currentIndex) {
                    slide.style.zIndex = '0';
                }
                            });
            isTransitioning = false;
        }, 600);
    }
    
    function nextSlide() {
        console.log('Next slide triggered');
        showSlide(currentIndex + 1);
        restartAutoplay();
    }
    
    function prevSlide() {
        console.log('Previous slide triggered');
        showSlide(currentIndex - 1);
        restartAutoplay();
    }
    
    function startAutoplay() {
        console.log('Starting autoplay...');
        stopAutoplay();
        
        autoplayInterval = setInterval(() => {
            if (!isTransitioning) {
                console.log('Autoplay: advancing slide');
                nextSlide();
            }
        }, 4000); // 4 seconds for better UX
    }
    
    function stopAutoplay() {
        if (autoplayInterval) {
            clearInterval(autoplayInterval);
            autoplayInterval = null;
            console.log('Autoplay stopped');
        }
    }
    
    function restartAutoplay() {
        stopAutoplay();
        startAutoplay();
    }
    
    // Button event listeners
    if (nextBtn) {
        nextBtn.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            console.log('Next button clicked');
            nextSlide();
        });
    }
    
    if (prevBtn) {
        prevBtn.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            console.log('Previous button clicked');
            prevSlide();
        });
    }
    
    // Touch support for mobile
    slider.addEventListener('touchstart', (e) => {
        touchStartX = e.touches[0].clientX;
        stopAutoplay();
    }, { passive: true });
    
    slider.addEventListener('touchend', (e) => {
        const touchEndX = e.changedTouches[0].clientX;
        const diff = touchStartX - touchEndX;
        const swipeThreshold = 50;
        
        if (Math.abs(diff) > swipeThreshold) {
            if (diff > 0) {
                // Swipe left - next
                nextSlide();
            } else {
                // Swipe right - previous
                prevSlide();
            }
        }
        
        // Restart autoplay after a delay
        setTimeout(() => {
            startAutoplay();
        }, 100);
    }, { passive: true });
    
    // Pause on hover
    slider.addEventListener('mouseenter', () => {
        console.log('Mouse entered slider - pausing autoplay');
        stopAutoplay();
    });
    
    slider.addEventListener('mouseleave', () => {
        console.log('Mouse left slider - resuming autoplay');
        startAutoplay();
    });
    
    // Keyboard navigation support
    document.addEventListener('keydown', (e) => {
        if (e.key === 'ArrowLeft') {
            prevSlide();
        } else if (e.key === 'ArrowRight') {
            nextSlide();
        }
    });
    
    // Handle page visibility changes
    document.addEventListener('visibilitychange', function() {
        if (document.hidden) {
            stopAutoplay();
        } else {
            startAutoplay();
        }
    });
    
    // Initialize
    console.log('Initializing slider styles');
    initializeSliderStyles();
    
    // Start autoplay after a short delay
    setTimeout(() => {
        console.log('Starting initial autoplay');
        startAutoplay();
    }, 2000);
    
    console.log('=== ALUMNI SLIDER INITIALIZED ===');
    
    // Return public API
    return {
        next: nextSlide,
        prev: prevSlide,
        goTo: showSlide,
        start: startAutoplay,
        stop: stopAutoplay
    };
}

// ENHANCED Swiper fallback with better error handling
// COMPLETELY REWRITTEN Alumni Typing Effect - Clean and Simple
function initAlumniTypingEffect() {
    console.log('=== INITIALIZING CLEAN ALUMNI TYPING ===');
    
    // Global state
    let currentSlide = null;
    let isTyping = false;
    
    // Very fast typing speeds
    const SPEEDS = {
        name: 8,       // Fast
        details: 4,     // Faster
        desc: 2         // Fastest
    };
    
    // Simple typing function - no promises, no complications
    function typeElement(element, text, speed, callback) {
        if (!element) {
            if (callback) callback();
            return;
        }
        
        element.textContent = '';
        let i = 0;
        
        function typeChar() {
            if (i < text.length) {
                element.textContent = text.substring(0, i + 1);
                i++;
                setTimeout(typeChar, speed);
            } else {
                if (callback) callback();
            }
        }
        
        typeChar();
    }
    
    // Main animation function
    function animateAlumni(slide) {
        // Prevent duplicate animations
        if (isTyping || slide === currentSlide) {
            console.log('Animation skipped - already running or same slide');
            return;
        }
        
        isTyping = true;
        currentSlide = slide;
        
        console.log('Starting animation for new slide');
        
        // Get all elements with null checks
        const nameEl = slide.querySelector('.alumni-name');
        const positionEl = slide.querySelector('.alumni-position');
        const companyEl = slide.querySelector('.alumni-company');
        const expertiseEl = slide.querySelector('.alumni-expertise');
        const experienceEl = slide.querySelector('.alumni-experience');
        const descEl = slide.querySelector('.alumni-desc');
        
        // Store original text
        const name = nameEl ? nameEl.textContent.trim() : '';
        const position = positionEl ? positionEl.textContent.trim() : '';
        const company = companyEl ? companyEl.textContent.trim() : '';
        const expertise = expertiseEl ? expertiseEl.textContent.trim() : '';
        const experience = experienceEl ? experienceEl.textContent.trim() : '';
        const description = descEl ? descEl.textContent.trim() : '';
        
        // Clear all text immediately
        if (nameEl) nameEl.textContent = '';
        if (positionEl) positionEl.textContent = '';
        if (companyEl) companyEl.textContent = '';
        if (expertiseEl) expertiseEl.textContent = '';
        if (experienceEl) experienceEl.textContent = '';
        if (descEl) descEl.textContent = '';
        
        // Sequential typing - simple and reliable
        function startTyping() {
            // 1. Type name
            typeElement(nameEl, name, SPEEDS.name, () => {
                console.log('Name typed');
                
                // 2. Type position
                typeElement(positionEl, position, SPEEDS.details, () => {
                    console.log('Position typed');
                    
                    // 3. Type company
                    typeElement(companyEl, company, SPEEDS.details, () => {
                        console.log('Company typed');
                        
                        // 4. Type expertise
                        typeElement(expertiseEl, expertise, SPEEDS.details, () => {
                            console.log('Expertise typed');
                            
                            // 5. Type experience
                            typeElement(experienceEl, experience, SPEEDS.details, () => {
                                console.log('Experience typed');
                                
                                // 6. Type description
                                typeElement(descEl, description, SPEEDS.desc, () => {
                                    console.log('Description typed - animation complete');
                                    isTyping = false;
                                });
                            });
                        });
                    });
                });
            });
        }
        
        // Start typing immediately
        startTyping();
    }
    
    // Find current active slide
    function getActiveSlide() {
        // Try multiple methods
        let slide = document.querySelector('.swiper-slide-active');
        
        if (!slide) {
            slide = document.querySelector('.swiper-slide[style*="opacity: 1"]');
        }
        
        if (!slide) {
            const slides = document.querySelectorAll('.swiper-slide');
            for (let s of slides) {
                if (s.style.opacity !== '0') {
                    slide = s;
                    break;
                }
            }
        }
        
        return slide;
    }
    
    // Trigger animation with delay
    function triggerWithDelay() {
        setTimeout(() => {
            const slide = getActiveSlide();
            if (slide) {
                animateAlumni(slide);
            }
        }, 200);
    }
    
    // Initialize first slide
    setTimeout(() => {
        const firstSlide = getActiveSlide();
        if (firstSlide) {
            animateAlumni(firstSlide);
        }
    }, 1500);
    
    // Swiper event handling - simplified
    if (typeof Swiper !== 'undefined') {
        // Wait for Swiper to be ready
        const setupSwiperEvents = () => {
            const container = document.querySelector('.alumni-slider');
            if (container && container.swiper) {
                const swiper = container.swiper;
                
                // Only use one reliable event
                swiper.on('slideChange', () => {
                    console.log('Swiper slide changed');
                    triggerWithDelay();
                });
                
                console.log('Swiper events set up');
            }
        };
        
        // Try multiple times
        setupSwiperEvents();
        setTimeout(setupSwiperEvents, 500);
        setTimeout(setupSwiperEvents, 1000);
    }
    
    // Manual navigation
    document.querySelector('.swiper-button-next')?.addEventListener('click', triggerWithDelay);
    document.querySelector('.swiper-button-prev')?.addEventListener('click', triggerWithDelay);
    
    // Touch/swipe
    document.querySelector('.alumni-slider')?.addEventListener('touchend', triggerWithDelay);
    
    // Make globally available
    window.animateAlumni = animateAlumni;
}

// SIMPLIFIED Alumni Slider
function initAlumniSliderWithFallback() {
    console.log('=== INITIALIZING SIMPLIFIED ALUMNI SLIDER ===');
    
    if (typeof Swiper !== 'undefined' && document.querySelector('.alumni-slider')) {
        try {
            // Clean up any existing styles
            document.querySelectorAll('.swiper-slide').forEach(slide => {
                slide.style.cssText = '';
            });
            
            const swiper = new Swiper('.alumni-slider', {
                loop: true,
                autoplay: {
                    delay: 6000, // 6 seconds for typing to complete
                    disableOnInteraction: false,
                    pauseOnMouseEnter: true
                },
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev'
                },
                speed: 400,
                effect: 'slide',
                grabCursor: true,
                on: {
                    init: function() {
                        console.log('Swiper initialized');
                        this.el.swiper = this;
                        
                        // Don't trigger animation here - let the separate init handle it
                    }
                }
            });
            
            console.log('Swiper created successfully');
            return swiper;
            
        } catch (err) {
            console.warn('Swiper failed:', err);
        }
    }
    
    // Fallback
    return initAlumniSlider();
}



function initScheduleTimeline() {
    console.log('=== INITIALIZING SCHEDULE TIMELINE ===');
    
    // 1. SELECTORS (Updated to match your HTML classes/IDs)
    const wheel = document.getElementById('scheduleWheel');
    const wheelItems = document.querySelectorAll('.wheel-item');
    const cards = document.querySelectorAll('.event-card');
    const timeDisplay = document.getElementById('dynamicTimeDisplay');
    const prevBtn = document.querySelector('.nav-btn.prev');
    const nextBtn = document.querySelector('.nav-btn.next');
    
    // Check if elements exist
    if (!wheel || !wheelItems.length || !cards.length) {
        console.error('Schedule elements missing');
        return;
    }

    let currentIndex = 0;
    const totalItems = wheelItems.length;
    let autoScrollInterval;
    const scrollDelay = 4000; // 4 seconds per slide
    
    // MATCH THIS WITH YOUR CSS: --item-separation: 20deg;
    const itemAngle = 20; 

    // --- MAIN UPDATE FUNCTION ---
    function navigateTo(index) {
        currentIndex = index;

        // A. ROTATE THE WHEEL
        // Item 0 is at 0deg. Item 1 is at 20deg.
        // To make Item 1 active (bring it to 0deg), rotate wheel -20deg.
        const rotation = -(currentIndex * itemAngle);
        wheel.style.setProperty('--current-deg', `${rotation}deg`);

        // B. UPDATE ACTIVE CLASSES (Wheel Items)
        wheelItems.forEach((item, i) => {
            if (i === currentIndex) item.classList.add('active');
            else item.classList.remove('active');
        });

        // C. UPDATE CONTENT CARDS
        cards.forEach((card) => {
            // Get the card's index from HTML data-index
            const cardIndex = parseInt(card.getAttribute('data-index'));
            
            // Reset Progress Bar immediately
            const bar = card.querySelector('.progress-bar');
            if(bar) {
                bar.style.width = '0%';
                bar.style.transition = 'none';
            }

            if (cardIndex === currentIndex) {
                card.classList.add('active');
                
                // Update Time Text Header
                const timeText = card.querySelector('.time-badge').innerText;
                if(timeDisplay) timeDisplay.innerText = timeText;

                // Animate Progress Bar after a tiny delay
                if(bar) {
                    setTimeout(() => {
                        bar.style.transition = `width ${scrollDelay}ms linear`;
                        bar.style.width = '100%';
                    }, 50);
                }
            } else {
                card.classList.remove('active');
            }
        });
    }

    // --- NAVIGATION HELPERS ---
    function nextSlide() {
        let newIndex = (currentIndex + 1) % totalItems;
        navigateTo(newIndex);
    }

    function prevSlide() {
        let newIndex = (currentIndex - 1 + totalItems) % totalItems;
        navigateTo(newIndex);
    }

    // --- AUTO SCROLL LOGIC ---
    function startAutoScroll() {
        if(autoScrollInterval) clearInterval(autoScrollInterval);
        autoScrollInterval = setInterval(nextSlide, scrollDelay);
    }

    function stopAutoScroll() {
        if(autoScrollInterval) clearInterval(autoScrollInterval);
    }

    // --- EVENT LISTENERS ---
    if(nextBtn) {
        nextBtn.addEventListener('click', () => {
            stopAutoScroll();
            nextSlide();
            startAutoScroll();
        });
    }

    if(prevBtn) {
        prevBtn.addEventListener('click', () => {
            stopAutoScroll();
            prevSlide();
            startAutoScroll();
        });
    }

    // Click on wheel items to jump
    wheelItems.forEach((item, index) => {
        item.addEventListener('click', () => {
            stopAutoScroll();
            navigateTo(index);
            startAutoScroll();
        });
    });

    // Keyboard Navigation
    document.addEventListener('keydown', (e) => {
        if (e.key === 'ArrowRight' || e.key === 'ArrowDown') {
            stopAutoScroll();
            nextSlide();
            startAutoScroll();
        }
        if (e.key === 'ArrowLeft' || e.key === 'ArrowUp') {
            stopAutoScroll();
            prevSlide();
            startAutoScroll();
        }
    });

    // --- START ---
    navigateTo(0);
    startAutoScroll();
    console.log('Schedule timeline initialized successfully');
}


// Gallery 3D Carousel functionality
function initGalleryCarousel() {
    const slider = document.querySelector('.slider');
    if (!slider) return;

    function activate(e) {
        const items = document.querySelectorAll('.item');
        if (e.target.matches('.next')) {
            slider.append(items[0]);
        } else if (e.target.matches('.prev')) {
            slider.prepend(items[items.length-1]);
        }
    }

    document.addEventListener('click', activate, false);

    // Auto-rotate the carousel
    setInterval(() => {
        const items = document.querySelectorAll('.item');
        slider.append(items[0]);
    }, 5000);
}

// Add smooth reveal animation for sections on scroll
function initScrollReveal() {
    const sections = document.querySelectorAll('section');
    
    const revealSection = (entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('revealed');
                observer.unobserve(entry.target);
            }
        });
    };
    
    const sectionObserver = new IntersectionObserver(revealSection, {
        threshold: 0.1
    });
    
    sections.forEach(section => {
        sectionObserver.observe(section);
    });
}

// Initialize scroll reveal
initScrollReveal();

// Add parallax effect to hero section
function initParallax() {
    const heroVideo = document.querySelector('.hero-bg-video');
    if (!heroVideo) return;
    
    window.addEventListener('scroll', () => {
        const scrolled = window.pageYOffset;
        const parallaxSpeed = 0.5;
        
        if (scrolled < window.innerHeight) {
            heroVideo.style.transform = `translateY(${scrolled * parallaxSpeed}px)`;
        }
    });
}

// Initialize parallax
initParallax();

// Add dynamic year to footer
function updateFooterYear() {
    const footerYear = document.querySelector('.footer-bottom p');
    if (footerYear) {
        const currentYear = new Date().getFullYear();
        footerYear.innerHTML = footerYear.innerHTML.replace('2025', currentYear);
    }
}

// Update footer year
updateFooterYear();

// Add loading animation for images
function initImageLoading() {
    const images = document.querySelectorAll('img');
    
    images.forEach(img => {
        img.addEventListener('load', function() {
            this.classList.add('loaded');
        });
        
        img.addEventListener('error', function() {
            this.classList.add('error');
        });
    });
}

// Initialize image loading
initImageLoading();

// Add resize handler for responsive adjustments
function initResizeHandler() {
    let resizeTimer;
    
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            // Check if mobile view and adjust if needed
            if (window.innerWidth <= 768) {
                document.body.classList.add('mobile-view');
            } else {
                document.body.classList.remove('mobile-view');
            }
        }, 250);
    });
}

// Initialize resize handler
initResizeHandler();

// Add mobile-specific optimizations
function initMobileOptimizations() {
    // Detect mobile device
    const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
    
    if (isMobile) {
        // Add mobile class to body
        document.body.classList.add('mobile-device');
        
        // Optimize videos for mobile
        const videos = document.querySelectorAll('video');
        videos.forEach(video => {
            video.setAttribute('playsinline', '');
            video.setAttribute('muted', '');
            video.setAttribute('loop', '');
            video.setAttribute('autoplay', '');
        });
    }
}

// Initialize mobile optimizations
initMobileOptimizations();

// Add lazy loading for images
function initLazyLoading() {
    const images = document.querySelectorAll('img[data-src]');
    
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                img.classList.add('loaded');
                observer.unobserve(img);
            }
        });
    }, { threshold: 0.1 });
    
    images.forEach(img => {
        imageObserver.observe(img);
    });
}

// Initialize lazy loading
initLazyLoading();

// Add smooth page transitions
function initPageTransitions() {
    // Add page transition class
    document.body.classList.add('page-transition');
    
    // Remove preloader after page load
    window.addEventListener('load', function() {
        setTimeout(() => {
            document.body.classList.add('loaded');
        }, 100);
    });
}

// Initialize page transitions
initPageTransitions();

// Fix for map loading issues on mobile
// Fix for map loading issues - CORRECTED VERSION
function initMapFix() {
    const mapIframe = document.querySelector('.contact-map iframe');
    const mapContainer = document.querySelector('.contact-map');
    
    if (!mapIframe || !mapContainer) return;
    
    // ERROR FIX: REMOVED the line that sets src=''
    // OLD CODE (CAUSING PROBLEM): mapIframe.src = '';
    // This was causing mobile browsers to load current page in iframe
    
    // Add error handling
    mapIframe.addEventListener('error', function() {
        console.error('Map failed to load');
        mapContainer.innerHTML = '<div class="map-error" style="color:white; display:flex; justify-content:center; align-items:center; height:100%; font-family: Rajdhani, sans-serif;">Map could not be loaded. Please check your connection.</div>';
    });
    
    // Add loading attribute for better performance
    mapIframe.setAttribute('loading', 'lazy');
    
    // Add attributes to help with mobile rendering
    mapIframe.setAttribute('frameborder', '0');
    mapIframe.setAttribute('scrolling', 'no');
    mapIframe.setAttribute('marginheight', '0');
    mapIframe.setAttribute('marginwidth', '0');
    
    // For mobile: ensure proper dimensions
    if (window.innerWidth <= 768) {
        mapIframe.style.width = '100%';
        mapIframe.style.height = '100%';
        mapIframe.style.minHeight = '250px';
    }
}

function initLogoSliders() {
    console.log('=== INITIALIZING ENHANCED LOGO SLIDERS ===');
    
    const leftToRightSliders = document.querySelectorAll('.left-to-right');
    const rightToLeftSliders = document.querySelectorAll('.right-to-left');
    
    // Set different animation durations for variety
    leftToRightSliders.forEach((slider, index) => {
        const duration = 20 + (index * 3); // Faster for mobile
        slider.style.animationDuration = `${duration}s`;
    });
    
    rightToLeftSliders.forEach((slider, index) => {
        const duration = 25 + (index * 3); // Faster for mobile
        slider.style.animationDuration = `${duration}s`;
    });
    
    // Pause animation on hover
    const allSliders = document.querySelectorAll('.logo-slider-track');
    allSliders.forEach(slider => {
        slider.addEventListener('mouseenter', () => {
            slider.style.animationPlayState = 'paused';
        });
        
        slider.addEventListener('mouseleave', () => {
            slider.style.animationPlayState = 'running';
        });
        
        // Touch support for mobile
        slider.addEventListener('touchstart', () => {
            slider.style.animationPlayState = 'paused';
        });
        
        slider.addEventListener('touchend', () => {
            setTimeout(() => {
                slider.style.animationPlayState = 'running';
            }, 1000); // Resume after 1 second
        });
    });
    
    // Adjust for mobile viewport
    function adjustForMobile() {
        if (window.innerWidth <= 768) {
            // Make logos smaller on mobile
            document.querySelectorAll('.company-logo').forEach(logo => {
                logo.style.transform = 'scale(0.85)';
            });
            
            // Reduce gap between logos
            allSliders.forEach(slider => {
                slider.style.gap = '15px';
            });
        } else {
            // Reset for desktop
            document.querySelectorAll('.company-logo').forEach(logo => {
                logo.style.transform = 'scale(1)';
            });
            
            allSliders.forEach(slider => {
                slider.style.gap = '40px';
            });
        }
    }
    
    // Initial adjustment
    adjustForMobile();
    
    // Adjust on resize
    window.addEventListener('resize', adjustForMobile);
    
    console.log('Enhanced logo sliders initialized');
}

// NEW: REBOOT Explanation Border Animation
function initRebootAnimations() {
    const rebootLetters = document.querySelectorAll('.reboot-letter');
    
    rebootLetters.forEach((letter, index) => {
        // Create border animation elements
        const borderAnimation = createBorderAnimation();
        letter.appendChild(borderAnimation);
        
        // Stagger the animation start times
        setTimeout(() => {
            borderAnimation.style.animation = 'borderScroll 3s linear infinite';
        }, index * 500); // Start each letter's animation 500ms apart
    });
}

function createBorderAnimation() {
    const borderContainer = document.createElement('div');
    borderContainer.className = 'border-animation';
    
    // Create four lines for the border
    const lines = ['top', 'right', 'bottom', 'left'];
    
    lines.forEach(position => {
        const line = document.createElement('div');
        line.className = `border-line border-${position}`;
        borderContainer.appendChild(line);
    });
    
    return borderContainer;
}

// Enhanced Companies Section Functionality
function initCompaniesSection() {
    console.log('=== INITIALIZING COMPANIES SECTION ===');
    
    const loadMoreBtn = document.getElementById('loadMoreCompanies');
    const logoScrollers = document.querySelectorAll('.logo-scroller');
    
    if (loadMoreBtn) {
        loadMoreBtn.addEventListener('click', function() {
            console.log('Load More Companies clicked');
            
            // Create a temporary loading state
            const originalText = this.textContent;
            this.textContent = 'Loading...';
            this.disabled = true;
            
            // Simulate loading more companies
            setTimeout(() => {
                // In a real implementation, you would fetch more data here
                // For now, we'll just show an alert and reset button
                alert('More companies would be loaded here in a real implementation!');
                
                this.textContent = originalText;
                this.disabled = false;
                
                // You could also dynamically add more logo tracks here
                // addMoreCompanies();
                
            }, 1000);
        });
    }
    
    // Enhanced hover effects for logo items
    const logoItems = document.querySelectorAll('.company-logo-item');
    logoItems.forEach(item => {
        item.addEventListener('mouseenter', function() {
            this.style.zIndex = '10';
        });
        
        item.addEventListener('mouseleave', function() {
            this.style.zIndex = '1';
        });
    });
    
    // Pause/play animations based on visibility
    const observerOptions = {
        threshold: 0.1
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            const tracks = entry.target.querySelectorAll('.logo-track');
            if (entry.isIntersecting) {
                tracks.forEach(track => {
                    track.style.animationPlayState = 'running';
                });
            } else {
                tracks.forEach(track => {
                    track.style.animationPlayState = 'paused';
                });
            }
        });
    }, observerOptions);
    
    logoScrollers.forEach(scroller => {
        observer.observe(scroller);
    });
    
    // Add random animation delays for visual interest
    logoScrollers.forEach((scroller, index) => {
        const track = scroller.querySelector('.logo-track');
        if (track) {
            // Add slight delay based on index for staggered start
            track.style.animationDelay = `${index * 2}s`;
        }
    });
    
    console.log('Companies section initialized successfully');
}

// Performance monitoring
function initPerformanceMonitoring() {
    // Log performance metrics
    window.addEventListener('load', function() {
        const perfData = window.performance.timing;
        const pageLoadTime = perfData.loadEventEnd - perfData.navigationStart;
        console.log(`Page load time: ${pageLoadTime}ms`);
        
        // You can send this data to analytics here
    });
}
// Initialize performance monitoring
initPerformanceMonitoring();
// CORRECTED BACK TO TOP BUTTON - ONLY RIGHT & BOTTOM
// Go to Top Section functionality using scrollIntoView

// Text Animation for REBOOT section
// Text Animation for REBOOT section - Fixed version
function initRebootTextAnimation() {
    const changeboxes = document.querySelectorAll('.changebox');
    if (!changeboxes.length) return;
    
    changeboxes.forEach((changebox, boxIndex) => {
        const words = changebox.querySelectorAll('span');
        let currentIndex = 0;
        
        // Set initial active word with delay for staggered start
        setTimeout(() => {
            words[currentIndex].classList.add('active');
        }, boxIndex * 300);
        
        // Start animation for this specific changebox
        setInterval(() => {
            const currentWord = words[currentIndex];
            const nextIndex = (currentIndex + 1) % words.length;
            const nextWord = words[nextIndex];
            
            // Add exiting class to current word
            currentWord.classList.add('exiting');
            currentWord.classList.remove('active');
            
            // Add active class to next word
            nextWord.classList.add('active');
            nextWord.classList.remove('exiting');
            
            // Clean up classes after animation
            setTimeout(() => {
                currentWord.classList.remove('exiting');
            }, 600);
            
            currentIndex = nextIndex;
        }, 2000 + (boxIndex * 400)); // Staggered timing
    });
}

// Initialize when DOM is loaded

// Typing effect for Alumni Meet title
function initAlumniMeetTypingEffect() {
    const alumniTitle = document.querySelector('.alumni-meet-title');
    if (!alumniTitle) return;
    
    const originalText = alumniTitle.textContent;
    alumniTitle.textContent = '';
    
    let charIndex = 0;
    let isDeleting = false;
    let typingSpeed = 100;
    let deletingSpeed = 50;
    let pauseTime = 2000;
    let pauseAfterType = false;
    
    function typeWriter() {
        if (!isDeleting && charIndex < originalText.length) {
            // Typing characters
            alumniTitle.textContent = originalText.substring(0, charIndex + 1);
            charIndex++;
            
            // Check if we've reached the end of the text
            if (charIndex === originalText.length) {
                pauseAfterType = true;
                setTimeout(() => {
                    isDeleting = true;
                    typeWriter();
                }, pauseTime);
            } else {
                setTimeout(typeWriter, typingSpeed);
            }
        } else if (isDeleting && charIndex > 0) {
            // Deleting characters
            alumniTitle.textContent = originalText.substring(0, charIndex - 1);
            charIndex--;
            
            // Check if we've deleted all characters
            if (charIndex === 0) {
                isDeleting = false;
                pauseAfterType = false;
                setTimeout(typeWriter, 500); // Shorter pause before typing again
            } else {
                setTimeout(typeWriter, deletingSpeed);
            }
        } else if (!isDeleting && charIndex === 0) {
            // Start typing from the beginning
            setTimeout(typeWriter, 500);
        }
    }
    
    // Start the typing effect
    setTimeout(typeWriter, 1000); // Delay before starting the effect
}
// FAST Typing effect for all Alumni Names
function initAlumniNamesTypingEffect() {
    const alumniNames = document.querySelectorAll('.alumni-name');
    if (!alumniNames.length) return;
    
    alumniNames.forEach((nameElement, index) => {
        const originalText = nameElement.textContent;
        nameElement.textContent = '';
        
        let charIndex = 0;
        let isDeleting = false;
        let typingSpeed = 25;      // Very fast typing
        let deletingSpeed = 15;      // Ultra-fast deleting
        let pauseTime = 1000;       // Short pause
        let pauseAfterType = false;
        
        function typeWriter() {
            if (!isDeleting && charIndex < originalText.length) {
                // Fast typing characters
                nameElement.textContent = originalText.substring(0, charIndex + 1);
                charIndex++;
                
                if (charIndex === originalText.length) {
                    pauseAfterType = true;
                    setTimeout(() => {
                        isDeleting = true;
                        typeWriter();
                    }, pauseTime);
                } else {
                    setTimeout(typeWriter, typingSpeed);
                }
            } else if (isDeleting && charIndex > 0) {
                // Fast deleting characters
                nameElement.textContent = originalText.substring(0, charIndex - 1);
                charIndex--;
                
                if (charIndex === 0) {
                    isDeleting = false;
                    pauseAfterType = false;
                    setTimeout(typeWriter, 400); // Brief pause before typing again
                } else {
                    setTimeout(typeWriter, deletingSpeed);
                }
            } else if (!isDeleting && charIndex === 0) {
                setTimeout(typeWriter, 400);
            }
        }
        
        // Stagger the start times for each name
        setTimeout(() => {
            typeWriter();
        }, index * 300); // Each name starts 300ms after the previous one
    });
}