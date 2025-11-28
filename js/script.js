// Main JavaScript for REBOOT 40 Alumni Website

// Update the main initialization function
// Main JavaScript for REBOOT 40 Alumni Website

// Update the main initialization function
document.addEventListener('DOMContentLoaded', function() {
    
    console.log('=== DOM CONTENT LOADED ===');
    
    // Initialize all components
    initPreloader();
    initCountdown();
    initStatsCounter();
    initAlumniSliderWithFallback();
    initNavbar();
    initScheduleTimeline(); // NEW: Initialize timeline schedule
    initGalleryCarousel();
    initMobileMenu();
    initMapFix();
    initLogoSliders();
    initRebootAnimations();
    initCompaniesSection();
    initBackToTopButton(); // Initialize back to top button

    console.log('REBOOT 40 Alumni Website initialized successfully');
});

// Preloader functionality
function initPreloader() {
    const splash = document.getElementById('splash');
    const mainContent = document.getElementById('main-content');
    
    if (!splash || !mainContent) return;
    
    // After the splash animation completes, show the main content
    setTimeout(() => {
        splash.style.display = 'none';
        mainContent.style.display = 'block';
        
        // Trigger page load animation
        document.body.classList.add('loaded');
    }, 3000);
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
function initNavbar() {
    const navbar = document.querySelector('.navbar');
    const navLinks = document.querySelectorAll('.nav-link');
    
    console.log('Initializing navigation...');
    
    // Smooth scrolling for anchor links with enhanced error handling
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            
            const targetId = this.getAttribute('href');
            console.log('Navigation clicked:', targetId);
            
            if (targetId === '#') return;
            
            // Remove # from the beginning
            const cleanTargetId = targetId.substring(1);
            
            // Try multiple methods to find the target element
            let targetElement = null;
            
            // Method 1: Direct ID selector
            targetElement = document.querySelector(targetId);
            
            // Method 2: ID selector with #
            if (!targetElement) {
                targetElement = document.querySelector(`#${cleanTargetId}`);
            }
            
            // Method 3: Class selector
            if (!targetElement) {
                targetElement = document.querySelector(`.${cleanTargetId}`);
            }
            
            // Method 4: Data attribute
            if (!targetElement) {
                targetElement = document.querySelector(`[data-section="${cleanTargetId}"]`);
            }
            
            // Method 5: Find section with matching ID
            if (!targetElement) {
                const sections = document.querySelectorAll('section');
                sections.forEach(section => {
                    if (section.id === cleanTargetId) {
                        targetElement = section;
                    }
                });
            }
            
            if (targetElement) {
                console.log('Target found:', targetElement);
                
                // Get navbar height
                const navbarHeight = navbar ? navbar.offsetHeight : 80;
                
                // Calculate target position with better accuracy
                const targetPosition = targetElement.getBoundingClientRect().top + window.pageYOffset - navbarHeight;
                
                // Scroll to target with multiple fallbacks
                try {
                    // Method 1: Modern smooth scroll
                    window.scrollTo({
                        top: targetPosition,
                        behavior: 'smooth'
                    });
                } catch (error) {
                    // Method 2: Fallback for older browsers
                    window.scrollTo(0, targetPosition);
                }
                
                // Method 3: Element scrollIntoView as final fallback
                setTimeout(() => {
                    if (window.pageYOffset !== targetPosition) {
                        targetElement.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                }, 500);
                
                // Update active nav link
                navLinks.forEach(link => {
                    link.classList.remove('active');
                });
                this.classList.add('active');
                
                // Close mobile menu if open
                const navMenu = document.querySelector('.nav-menu');
                const hamburger = document.querySelector('.hamburger');
                if (navMenu && navMenu.classList.contains('active')) {
                    navMenu.classList.remove('active');
                    document.body.style.overflow = '';
                    if (hamburger) {
                        const icon = hamburger.querySelector('i');
                        if (icon) {
                            icon.className = 'fas fa-bars';
                        }
                    }
                }
                
            } else {
                console.error('Target element not found for:', targetId);
                console.log('Available sections:', Array.from(document.querySelectorAll('section[id]')).map(s => s.id));
                
                // Debug all possible selectors
                console.log('Tried selectors:', {
                    original: targetId,
                    id: `#${cleanTargetId}`,
                    class: `.${cleanTargetId}`,
                    data: `[data-section="${cleanTargetId}"]`
                });
            }
        });
    });
    
    // Update active nav link on scroll
    function updateActiveNavLink() {
        const sections = document.querySelectorAll('section[id]');
        const scrollPosition = window.pageYOffset + 100;
        
        let currentSection = '';
        sections.forEach(section => {
            const sectionTop = section.offsetTop;
            const sectionHeight = section.offsetHeight;
            const sectionId = section.getAttribute('id');
            
            if (scrollPosition >= sectionTop && scrollPosition < sectionTop + sectionHeight) {
                currentSection = sectionId;
            }
        });
        
        if (currentSection) {
            navLinks.forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('href') === `#${currentSection}`) {
                    link.classList.add('active');
                }
            });
        }
    }
    
    // Update active link on scroll
    window.addEventListener('scroll', updateActiveNavLink);
    
    // Initial update
    setTimeout(updateActiveNavLink, 100);
    
    console.log('Navigation initialized successfully');
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
function initAlumniSliderWithFallback() {
    console.log('=== INITIALIZING ALUMNI SLIDER WITH FALLBACK ===');
    
    // Try Swiper first (if available and elements exist)
    if (typeof Swiper !== 'undefined' && document.querySelector('.alumni-slider')) {
        try {
            console.log('Attempting Swiper initialization...');
            
            // Reset any existing styles
            const slides = document.querySelectorAll('.swiper-slide');
            slides.forEach(slide => {
                slide.style = ''; // Reset inline styles
            });
            
            const swiper = new Swiper('.alumni-slider', {
                loop: true,
                autoplay: {
                    delay: 4000,
                    disableOnInteraction: false, // Continue autoplay after manual interaction
                    pauseOnMouseEnter: true
                },
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev'
                },
                speed: 600,
                effect: 'slide',
                grabCursor: true,
                on: {
                    init: () => console.log('Swiper initialized successfully'),
                    autoplayStart: () => console.log('Swiper autoplay started'),
                    slideChange: () => console.log('Swiper slide changed')
                }
            });
            
            console.log('Swiper initialized successfully');
            return swiper;
            
        } catch (err) {
            console.warn('Swiper failed, using custom implementation:', err);
        }
    } else {
        console.log('Swiper not available or slider not found, using custom implementation');
    }
    
    // Fallback to custom implementation
    console.log('Using custom slider implementation');
    return initAlumniSlider();
}

// NEW: Schedule Timeline functionality
// NEW: Schedule Timeline functionality - AUTO-SCROLL WITH STRAIGHT TIME
document.addEventListener('DOMContentLoaded', function() {
    initScheduleTimeline();
});

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
// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    initScheduleTimeline();
});

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
function initMapFix() {
    const mapIframe = document.querySelector('.contact-map iframe');
    if (mapIframe) {
        // Force reload map on mobile devices
        if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
            const originalSrc = mapIframe.src;
            mapIframe.src = '';
            setTimeout(() => {
                mapIframe.src = originalSrc;
            }, 1000);
        }
        
        // Add error handling
        mapIframe.addEventListener('error', function() {
            console.error('Map failed to load');
            // You can add fallback map or error message here
            const mapContainer = document.querySelector('.contact-map');
            if (mapContainer) {
                mapContainer.innerHTML = '<div class="map-error">Map could not be loaded. Please check your connection.</div>';
            }
        });
        
        // Add loading attribute for better performance
        mapIframe.setAttribute('loading', 'lazy');
    }
}

// Initialize logo sliders
function initLogoSliders() {
    const leftToRightSliders = document.querySelectorAll('.left-to-right');
    const rightToLeftSliders = document.querySelectorAll('.right-to-left');
    
    // Set different animation durations for variety
    leftToRightSliders.forEach((slider, index) => {
        const duration = 25 + (index * 5); // 25s, 30s, 35s
        slider.style.animationDuration = `${duration}s`;
    });
    
    rightToLeftSliders.forEach((slider, index) => {
        const duration = 30 + (index * 5); // 30s, 35s, 40s
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
    });
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
function initBackToTopButton() {
    console.log('=== CREATING CORRECTED BACK TO TOP BUTTON ===');
    
    // Remove any existing button first
    const existingBtn = document.getElementById('back-to-top');
    if (existingBtn) {
        existingBtn.remove();
        console.log('Removed existing button');
    }
    
    // Create new button element
    const backToTopBtn = document.createElement('a');
    backToTopBtn.id = 'back-to-top';
    backToTopBtn.href = '#home';
    backToTopBtn.setAttribute('aria-label', 'Back to top');
    
    // Create icon
    const icon = document.createElement('i');
    icon.className = 'fas fa-arrow-up';
    backToTopBtn.appendChild(icon);
    
    // Apply CORRECTED styles - ONLY right and bottom, no left/top
    const buttonStyles = {
        position: 'fixed',
        bottom: '30px',      // ONLY bottom
        right: '30px',       // ONLY right
        width: '60px',
        height: '60px',
        backgroundColor: '#000000',
        border: '3px solid #FFD700',
        borderRadius: '50%',
        display: 'flex',
        alignItems: 'center',
        justifyContent: 'center',
        color: '#FFD700',
        fontSize: '24px',
        fontWeight: 'bold',
        textDecoration: 'none',
        zIndex: '99999',
        opacity: '1',
        visibility: 'visible',
        cursor: 'pointer',
        boxShadow: '0 0 20px rgba(255, 215, 0, 0.5)',
        transition: 'all 0.3s ease',
        
        // EXPLICITLY REMOVE left and top
        left: 'auto',
        top: 'auto',
        transform: 'none'
    };
    
    // Apply all styles
    Object.assign(backToTopBtn.style, buttonStyles);
    
    // Add click event
    backToTopBtn.addEventListener('click', function(e) {
        e.preventDefault();
        console.log('Back to top clicked');
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
    
    // Add hover effect
    backToTopBtn.addEventListener('mouseenter', function() {
        this.style.backgroundColor = '#FFD700';
        this.style.color = '#000000';
        this.style.transform = 'scale(1.1)';
        this.style.boxShadow = '0 0 30px rgba(255, 215, 0, 0.8)';
    });
    
    backToTopBtn.addEventListener('mouseleave', function() {
        this.style.backgroundColor = '#000000';
        this.style.color = '#FFD700';
        this.style.transform = 'scale(1)';
        this.style.boxShadow = '0 0 20px rgba(255, 215, 0, 0.5)';
    });
    
    // Add to the body
    document.body.appendChild(backToTopBtn);
    
    console.log('✅ Corrected back to top button created');
    console.log('Using only bottom: 30px, right: 30px');
    
    return backToTopBtn;
}