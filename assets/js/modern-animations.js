// ===== MODERN CAFE BILLING SYSTEM ANIMATIONS =====

// Animation Controller Class
class AnimationController {
    constructor() {
        this.init();
    }

    init() {
        this.setupPageTransitions();
        this.setupCardAnimations();
        this.setupButtonEffects();
        this.setupFormAnimations();
        this.setupCountUpAnimations();
        this.setupParallaxEffects();
        this.setupScrollAnimations();
    }

    // Page Transition Effects
    setupPageTransitions() {
        // Fade in page content
        $('body').addClass('fade-in-up');
        
        // Stagger animation for cards
        $('.modern-card, .stats-card').each(function(index) {
            $(this).css({
                'animation-delay': (index * 0.1) + 's',
                'animation-fill-mode': 'both'
            }).addClass('fade-in-up');
        });

        // Sidebar items animation
        $('.sidebar-list .nav-item').each(function(index) {
            $(this).css({
                'animation-delay': (index * 0.05) + 's',
                'animation-fill-mode': 'both'
            }).addClass('slide-in-left');
        });
    }

    // Card Hover Animations
    setupCardAnimations() {
        $('.modern-card').hover(
            function() {
                $(this).addClass('animate__animated animate__pulse');
            },
            function() {
                $(this).removeClass('animate__animated animate__pulse');
            }
        );

        // 3D Tilt Effect for Cards
        if (typeof VanillaTilt !== 'undefined') {
            VanillaTilt.init(document.querySelectorAll('.modern-card'), {
                max: 5,
                speed: 400,
                glare: true,
                'max-glare': 0.2,
            });
        }
    }

    // Button Click Effects
    setupButtonEffects() {
        $('.btn-modern').on('click', function(e) {
            let btn = $(this);
            
            // Ripple effect
            let ripple = $('<span class="ripple"></span>');
            btn.append(ripple);
            
            let x = e.pageX - btn.offset().left;
            let y = e.pageY - btn.offset().top;
            
            ripple.css({
                top: y + 'px',
                left: x + 'px',
                position: 'absolute',
                background: 'rgba(255, 255, 255, 0.6)',
                width: '0',
                height: '0',
                borderRadius: '50%',
                transform: 'scale(0)',
                animation: 'rippleEffect 0.6s linear',
                pointerEvents: 'none'
            });
            
            setTimeout(() => {
                ripple.remove();
            }, 600);
        });

        // Add ripple effect CSS
        $('<style>')
            .prop('type', 'text/css')
            .html(`
                @keyframes rippleEffect {
                    to {
                        transform: scale(4);
                        opacity: 0;
                    }
                }
                .btn-modern {
                    position: relative;
                    overflow: hidden;
                }
            `)
            .appendTo('head');
    }

    // Form Field Animations
    setupFormAnimations() {
        $('.form-control').on('focus', function() {
            $(this).parent().addClass('focused');
        }).on('blur', function() {
            if (!$(this).val()) {
                $(this).parent().removeClass('focused');
            }
        });

        // Floating labels effect
        $('.form-control').each(function() {
            if ($(this).val()) {
                $(this).parent().addClass('focused');
            }
        });
    }

    // Count Up Animation for Numbers
    setupCountUpAnimations() {
        $('.stats-number').each(function() {
            let $this = $(this);
            let target = parseInt($this.text());
            
            // Create intersection observer for count up
            if ('IntersectionObserver' in window) {
                let observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            this.animateCountUp($this, target);
                            observer.unobserve(entry.target);
                        }
                    });
                });
                observer.observe($this[0]);
            } else {
                // Fallback for older browsers
                this.animateCountUp($this, target);
            }
        });
    }

    animateCountUp($element, target) {
        let current = 0;
        let increment = target / 50;
        let timer = setInterval(() => {
            current += increment;
            if (current >= target) {
                current = target;
                clearInterval(timer);
            }
            $element.text(Math.floor(current));
        }, 20);
    }

    // Parallax Scrolling Effects
    setupParallaxEffects() {
        $(window).scroll(() => {
            let scrolled = $(window).scrollTop();
            let parallaxElements = $('.parallax-element');
            
            parallaxElements.each(function() {
                let speed = $(this).data('speed') || 0.5;
                let yPos = -(scrolled * speed);
                $(this).css('transform', `translateY(${yPos}px)`);
            });
        });
    }

    // Scroll-triggered Animations
    setupScrollAnimations() {
        if ('IntersectionObserver' in window) {
            let observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        let element = $(entry.target);
                        element.addClass('animate__animated animate__fadeInUp');
                    }
                });
            }, {
                threshold: 0.1
            });

            $('.animate-on-scroll').each(function() {
                observer.observe(this);
            });
        }
    }
}

// Toast Notification System
class ToastManager {
    constructor() {
        this.container = this.createContainer();
    }

    createContainer() {
        let container = $('<div id="toast-container"></div>');
        container.css({
            position: 'fixed',
            top: '20px',
            right: '20px',
            zIndex: '9999',
            maxWidth: '400px'
        });
        $('body').append(container);
        return container;
    }

    show(message, type = 'info', duration = 3000) {
        let toast = $(`
            <div class="toast-modern animate__animated animate__slideInRight" role="alert">
                <div class="toast-header">
                    <i class="fas ${this.getIcon(type)} me-2"></i>
                    <strong class="me-auto">${this.getTitle(type)}</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
                </div>
                <div class="toast-body">
                    ${message}
                </div>
            </div>
        `);

        this.container.append(toast);
        
        // Auto remove
        setTimeout(() => {
            toast.addClass('animate__slideOutRight');
            setTimeout(() => toast.remove(), 500);
        }, duration);

        // Manual close
        toast.find('.btn-close').on('click', () => {
            toast.addClass('animate__slideOutRight');
            setTimeout(() => toast.remove(), 500);
        });
    }

    getIcon(type) {
        const icons = {
            success: 'fa-check-circle text-success',
            error: 'fa-exclamation-circle text-danger',
            warning: 'fa-exclamation-triangle text-warning',
            info: 'fa-info-circle text-info'
        };
        return icons[type] || icons.info;
    }

    getTitle(type) {
        const titles = {
            success: 'Success',
            error: 'Error',
            warning: 'Warning',
            info: 'Information'
        };
        return titles[type] || titles.info;
    }
}

// Loading Animation System
class LoadingManager {
    constructor() {
        this.overlay = this.createOverlay();
    }

    createOverlay() {
        return $(`
            <div id="loading-overlay" style="
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(44, 62, 80, 0.9);
                backdrop-filter: blur(10px);
                z-index: 9998;
                display: none;
                align-items: center;
                justify-content: center;
            ">
                <div class="loader-cafe"></div>
            </div>
        `);
    }

    show() {
        if (!$('#loading-overlay').length) {
            $('body').append(this.overlay);
        }
        $('#loading-overlay').fadeIn(300);
    }

    hide() {
        $('#loading-overlay').fadeOut(300);
    }
}

// Particle Background System
class ParticleBackground {
    constructor(container) {
        this.container = container;
        this.particles = [];
        this.init();
    }

    init() {
        this.createCanvas();
        this.createParticles();
        this.animate();
    }

    createCanvas() {
        this.canvas = $('<canvas id="particle-canvas"></canvas>');
        this.canvas.css({
            position: 'fixed',
            top: 0,
            left: 0,
            width: '100%',
            height: '100%',
            pointerEvents: 'none',
            zIndex: -1,
            opacity: 0.3
        });
        $('body').prepend(this.canvas);
        
        this.ctx = this.canvas[0].getContext('2d');
        this.resize();
        
        $(window).resize(() => this.resize());
    }

    resize() {
        this.canvas[0].width = window.innerWidth;
        this.canvas[0].height = window.innerHeight;
    }

    createParticles() {
        for (let i = 0; i < 50; i++) {
            this.particles.push({
                x: Math.random() * window.innerWidth,
                y: Math.random() * window.innerHeight,
                radius: Math.random() * 3 + 1,
                vx: (Math.random() - 0.5) * 2,
                vy: (Math.random() - 0.5) * 2,
                alpha: Math.random() * 0.5 + 0.2
            });
        }
    }

    animate() {
        this.ctx.clearRect(0, 0, this.canvas[0].width, this.canvas[0].height);
        
        this.particles.forEach(particle => {
            particle.x += particle.vx;
            particle.y += particle.vy;
            
            if (particle.x < 0 || particle.x > this.canvas[0].width) particle.vx *= -1;
            if (particle.y < 0 || particle.y > this.canvas[0].height) particle.vy *= -1;
            
            this.ctx.beginPath();
            this.ctx.arc(particle.x, particle.y, particle.radius, 0, Math.PI * 2);
            this.ctx.fillStyle = `rgba(255, 255, 255, ${particle.alpha})`;
            this.ctx.fill();
        });
        
        requestAnimationFrame(() => this.animate());
    }
}

// Enhanced Modal System
class ModalManager {
    constructor() {
        this.setupEnhancedModals();
    }

    setupEnhancedModals() {
        // Override default modal behavior
        $(document).on('show.bs.modal', '.modal', function() {
            $(this).addClass('animate__animated animate__zoomIn');
        });

        $(document).on('hide.bs.modal', '.modal', function() {
            $(this).removeClass('animate__zoomIn').addClass('animate__zoomOut');
        });

        $(document).on('hidden.bs.modal', '.modal', function() {
            $(this).removeClass('animate__animated animate__zoomOut');
        });
    }

    showConfirm(message, onConfirm, onCancel = null) {
        let modal = $(`
            <div class="modal fade" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content modern-card">
                        <div class="modal-header border-0">
                            <h5 class="modal-title">
                                <i class="fas fa-question-circle text-warning me-2"></i>
                                Confirmation
                            </h5>
                        </div>
                        <div class="modal-body">
                            <p class="mb-0">${message}</p>
                        </div>
                        <div class="modal-footer border-0">
                            <button type="button" class="btn btn-modern btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-modern btn-danger" id="confirm-btn">Confirm</button>
                        </div>
                    </div>
                </div>
            </div>
        `);

        $('body').append(modal);
        modal.modal('show');

        modal.find('#confirm-btn').on('click', () => {
            if (onConfirm) onConfirm();
            modal.modal('hide');
        });

        modal.on('hidden.bs.modal', () => {
            if (onCancel) onCancel();
            modal.remove();
        });
    }
}

// Initialize all systems when document is ready
$(document).ready(function() {
    // Initialize animation controller
    window.animationController = new AnimationController();
    
    // Initialize toast manager
    window.toastManager = new ToastManager();
    
    // Initialize loading manager
    window.loadingManager = new LoadingManager();
    
    // Initialize particle background
    window.particleBackground = new ParticleBackground();
    
    // Initialize modal manager
    window.modalManager = new ModalManager();
    
    // Enhanced preloader
    setTimeout(() => {
        $('#preloader').addClass('animate__animated animate__fadeOut');
        setTimeout(() => {
            $('#preloader').remove();
        }, 500);
    }, 1000);
    
    // Override default alert_toast function
    window.alert_toast = function(message, type = 'info') {
        window.toastManager.show(message, type);
    };
    
    // Override default start_load and end_load functions
    window.start_load = function() {
        window.loadingManager.show();
    };
    
    window.end_load = function() {
        window.loadingManager.hide();
    };
    
    // Override default _conf function
    window._conf = function(message, callback, params = []) {
        window.modalManager.showConfirm(message, () => {
            if (typeof callback === 'string') {
                window[callback](...params);
            } else if (typeof callback === 'function') {
                callback(...params);
            }
        });
    };
    
    // Add smooth scrolling
    $('a[href^="#"]').on('click', function(e) {
        e.preventDefault();
        let target = $(this.hash);
        if (target.length) {
            $('html, body').animate({
                scrollTop: target.offset().top - 100
            }, 800, 'easeInOutCubic');
        }
    });
    
    // Add mobile sidebar toggle
    if (window.innerWidth <= 768) {
        $('body').prepend(`
            <button class="btn btn-modern d-md-none" id="sidebar-toggle" style="
                position: fixed;
                top: 20px;
                left: 20px;
                z-index: 1051;
                border-radius: 50%;
                width: 50px;
                height: 50px;
                padding: 0;
            ">
                <i class="fas fa-bars"></i>
            </button>
        `);
        
        $('#sidebar-toggle').on('click', function() {
            $('#sidebar').toggleClass('show');
        });
        
        // Close sidebar when clicking outside
        $(document).on('click', function(e) {
            if (!$(e.target).closest('#sidebar, #sidebar-toggle').length) {
                $('#sidebar').removeClass('show');
            }
        });
    }
    
    // Add keyboard shortcuts
    $(document).keydown(function(e) {
        // Ctrl/Cmd + / for quick search
        if ((e.ctrlKey || e.metaKey) && e.which === 191) {
            e.preventDefault();
            let searchInput = $('input[type="search"], input[name="search"]').first();
            if (searchInput.length) {
                searchInput.focus();
            }
        }
        
        // Escape to close modals
        if (e.which === 27) {
            $('.modal.show').modal('hide');
        }
    });
    
    // Add contextual help tooltips
    $('[data-toggle="tooltip"]').tooltip({
        trigger: 'hover focus'
    });
    
    // Performance monitoring
    if (window.performance && window.performance.mark) {
        window.performance.mark('app-initialized');
        console.log('🚀 Modern Cafe Billing System initialized successfully!');
    }
});

// Export for global access
window.CafeBillingAnimations = {
    AnimationController,
    ToastManager,
    LoadingManager,
    ParticleBackground,
    ModalManager
};
