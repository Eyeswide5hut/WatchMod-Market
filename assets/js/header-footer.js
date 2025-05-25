/**
 * WatchModMarket Header & Footer JavaScript
 * Version: 1.0.0
 * Consolidated and optimized script
 */

(function () {
    'use strict';

    // Global namespace
    window.WatchModMarket = window.WatchModMarket || {};

    // Configuration
    const config = {
        scrollThreshold: 100,
        backToTopThreshold: 300,
        throttleDelay: 100,
        messageTimeout: 5000
    };

    // DOM Elements - cached for performance
    const elements = {
        // Header elements
        header: null,
        mobileMenuToggle: null,
        headerActions: null,
        mainNav: null,

        // Footer elements
        backToTopButton: null,
        socialLinks: null,
        footerCols: null,
        newsletterForms: null,
        paymentIcons: null,
        footerLinks: null,

        // Other elements
        instaSlider: null,
        currencySelector: null,
        languageSelector: null,
        addToCartButtons: null,
        wishlistButtons: null
    };

    /**
     * Initialize the application
     */
    function init() {
        // Cache DOM elements
        cacheElements();

        // Initialize modules
        initHeader();
        initFooter();
        initSharedFeatures();

        // Set up global event listeners
        setupGlobalEvents();

        console.log('WatchModMarket scripts initialized');
    }

    /**
     * Cache all DOM elements
     */
    function cacheElements() {
        // Header elements
        elements.header = document.getElementById('masthead');
        elements.mobileMenuToggle = document.getElementById('mobile-menu-toggle');
        elements.headerActions = document.querySelector('.header-actions');
        elements.mainNav = document.querySelector('.main-nav');

        // Footer elements
        elements.backToTopButton = document.getElementById('back-to-top');
        elements.socialLinks = document.querySelectorAll('.social-links a');
        elements.footerCols = document.querySelectorAll('.footer-col');
        elements.newsletterForms = document.querySelectorAll('.newsletter-form, .footer-newsletter form');
        elements.paymentIcons = document.querySelectorAll('.payment-icon');
        elements.footerLinks = document.querySelectorAll('.footer-links a');

        // Other elements
        elements.instaSlider = document.querySelector('.instagram-feed-slider');
        elements.currencySelector = document.getElementById('currency-selector');
        elements.languageSelector = document.getElementById('language-selector');
        elements.addToCartButtons = document.querySelectorAll('.add-to-cart');
        elements.wishlistButtons = document.querySelectorAll('.add-to-wishlist');
    }

    /**
     * Initialize header functionality
     */
    function initHeader() {
        setupMobileMenu();
        setupStickyHeader();
        setupDropdownMenus();
        setupCurrencySelector();
        setupLanguageSelector();
        setupInstagramSlider();
    }

    /**
     * Initialize footer functionality
     */
    function initFooter() {
        setupBackToTop();
        setupSocialLinks();
        setupFooterAnimations();
        setupContactForms();
        setupPaymentIcons();
        enhanceFooterAccessibility();
        optimizeFooter();
    }

    /**
     * Initialize shared features
     */
    function initSharedFeatures() {
        setupCartActions();
    }

    /**
     * Mobile menu toggle
     */
    function setupMobileMenu() {
        if (!elements.mobileMenuToggle || !elements.headerActions) return;

        elements.mobileMenuToggle.addEventListener('click', function () {
            const isExpanded = this.getAttribute('aria-expanded') === 'true';

            // Toggle classes
            this.classList.toggle('active');
            elements.headerActions.classList.toggle('show');
            document.body.classList.toggle('menu-open');

            // Update accessibility attributes
            this.setAttribute('aria-expanded', !isExpanded);
        });

        // Close menu on window resize
        window.addEventListener('resize', throttle(function () {
            if (window.innerWidth >= 992 && elements.headerActions.classList.contains('show')) {
                elements.mobileMenuToggle.classList.remove('active');
                elements.headerActions.classList.remove('show');
                document.body.classList.remove('menu-open');
                elements.mobileMenuToggle.setAttribute('aria-expanded', 'false');
            }
        }, config.throttleDelay));
    }

    /**
     * Sticky header with show/hide on scroll
     */
    function setupStickyHeader() {
        if (!elements.header) return;

        let lastScrollTop = 0;

        window.addEventListener('scroll', throttle(function () {
            const currentScrollTop = window.pageYOffset || document.documentElement.scrollTop;

            if (currentScrollTop > lastScrollTop && currentScrollTop > config.scrollThreshold) {
                // Scrolling down
                elements.header.classList.add('scrolled-down');
                elements.header.classList.remove('scrolled-up');
            } else if (currentScrollTop < lastScrollTop) {
                // Scrolling up
                elements.header.classList.remove('scrolled-down');
                elements.header.classList.add('scrolled-up');
            }

            // Reset when at top
            if (currentScrollTop === 0) {
                elements.header.classList.remove('scrolled-down', 'scrolled-up');
            }

            lastScrollTop = currentScrollTop;
        }, config.throttleDelay));
    }

    /**
     * Back to top button with scroll progress
     */
    function setupBackToTop() {
        if (!elements.backToTopButton) return;

        // Create progress circle if it doesn't exist
        createProgressCircle();

        window.addEventListener('scroll', throttle(function () {
            const scrollTop = document.documentElement.scrollTop;
            const scrollHeight = document.documentElement.scrollHeight - document.documentElement.clientHeight;
            const scrollPercent = (scrollTop / scrollHeight) * 100;

            // Show/hide button
            if (window.pageYOffset > config.backToTopThreshold) {
                elements.backToTopButton.classList.add('show');
            } else {
                elements.backToTopButton.classList.remove('show');
            }

            // Update progress circle
            updateProgressCircle(scrollPercent);
        }, config.throttleDelay));

        // Click handler
        elements.backToTopButton.addEventListener('click', function (e) {
            e.preventDefault();

            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });

            // Focus management for accessibility
            setTimeout(function () {
                const focusTarget = document.querySelector('h1, .site-title, #content');
                if (focusTarget) {
                    focusTarget.focus();
                }
            }, 500);
        });
    }

    /**
     * Create progress circle for back to top button
     */
    function createProgressCircle() {
        const container = elements.backToTopButton.parentNode;
        if (!container || container.querySelector('.progress-ring')) return;

        const svg = document.createElement('svg');
        svg.className = 'progress-ring';
        svg.setAttribute('width', '50');
        svg.setAttribute('height', '50');

        const circle = document.createElement('circle');
        circle.className = 'progress-ring-circle';
        circle.setAttribute('stroke', 'rgba(255,92,0,0.8)');
        circle.setAttribute('stroke-width', '3');
        circle.setAttribute('fill', 'transparent');
        circle.setAttribute('r', '20');
        circle.setAttribute('cx', '25');
        circle.setAttribute('cy', '25');

        svg.appendChild(circle);
        container.insertBefore(svg, elements.backToTopButton);

        // Set up circle properties
        const radius = 20;
        const circumference = radius * 2 * Math.PI;
        circle.style.strokeDasharray = `${circumference} ${circumference}`;
        circle.style.strokeDashoffset = circumference;
        circle.style.transform = 'rotate(-90deg)';
        circle.style.transformOrigin = '50% 50%';
        circle.style.transition = 'stroke-dashoffset 0.35s';

        // Store circle reference
        elements.progressCircle = circle;
        elements.circumference = circumference;
    }

    /**
     * Update progress circle
     */
    function updateProgressCircle(percent) {
        if (!elements.progressCircle) return;

        const offset = elements.circumference - (percent / 100) * elements.circumference;
        elements.progressCircle.style.strokeDashoffset = offset;
    }

    /**
     * Dropdown menus for navigation
     */
    function setupDropdownMenus() {
        if (!elements.mainNav) return;

        const menuItems = elements.mainNav.querySelectorAll('li.menu-item-has-children');

        menuItems.forEach(function (item) {
            const toggleBtn = document.createElement('button');
            toggleBtn.className = 'submenu-toggle';
            toggleBtn.setAttribute('aria-expanded', 'false');
            toggleBtn.innerHTML = '<span class="screen-reader-text">Toggle submenu</span><span aria-hidden="true">+</span>';

            item.appendChild(toggleBtn);

            toggleBtn.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();

                const submenu = item.querySelector('.sub-menu');
                const expanded = this.getAttribute('aria-expanded') === 'true';

                if (submenu) {
                    submenu.classList.toggle('active');
                    this.setAttribute('aria-expanded', !expanded);
                    this.querySelector('[aria-hidden="true"]').textContent = expanded ? '+' : '-';
                }
            });
        });
    }

    /**
     * Currency selector
     */
    function setupCurrencySelector() {
        if (!elements.currencySelector) return;

        elements.currencySelector.addEventListener('change', function () {
            const currency = this.value;
            localStorage.setItem('watchmodmarket_currency', currency);

            // In a real implementation, this would use AJAX
            window.location.reload();
        });

        // Set initial value
        const savedCurrency = localStorage.getItem('watchmodmarket_currency');
        if (savedCurrency && elements.currencySelector.querySelector(`option[value="${savedCurrency}"]`)) {
            elements.currencySelector.value = savedCurrency;
        }
    }

    /**
     * Language selector
     */
    function setupLanguageSelector() {
        if (!elements.languageSelector) return;

        elements.languageSelector.addEventListener('change', function () {
            const language = this.value;
            localStorage.setItem('watchmodmarket_language', language);

            // Redirect to language-specific URL
            window.location.href = window.location.pathname + '?lang=' + language;
        });

        // Set initial value
        const savedLanguage = localStorage.getItem('watchmodmarket_language');
        if (savedLanguage && elements.languageSelector.querySelector(`option[value="${savedLanguage}"]`)) {
            elements.languageSelector.value = savedLanguage;
        }
    }

    /**
     * Instagram slider setup
     */
    function setupInstagramSlider() {
        if (!elements.instaSlider || typeof Flickity !== 'function') return;

        new Flickity(elements.instaSlider, {
            cellAlign: 'left',
            contain: true,
            wrapAround: true,
            autoPlay: 3000,
            prevNextButtons: false,
            pageDots: false,
            draggable: true
        });
    }

    /**
     * Social links enhancement
     */
    function setupSocialLinks() {
        elements.socialLinks.forEach(function (link) {
            // Hover effects
            link.addEventListener('mouseenter', function () {
                this.style.transform = 'translateY(-3px) scale(1.1)';
            });

            link.addEventListener('mouseleave', function () {
                this.style.transform = 'translateY(-3px)';
            });

            // Click tracking
            link.addEventListener('click', function () {
                const platform = this.getAttribute('aria-label') || 'Unknown';

                // Analytics tracking
                if (typeof gtag !== 'undefined') {
                    gtag('event', 'social_click', {
                        'social_platform': platform,
                        'event_category': 'Social Media',
                        'event_label': platform
                    });
                }

                console.log('Social media link clicked:', platform);
            });
        });
    }

    /**
     * Footer animations
     */
    function setupFooterAnimations() {
        if (!('IntersectionObserver' in window)) {
            // Fallback for browsers without IntersectionObserver
            elements.footerCols.forEach(function (element) {
                element.style.opacity = '1';
                element.style.transform = 'translateY(0)';
            });
            return;
        }

        const footerObserver = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '50px'
        });

        elements.footerCols.forEach(function (element, index) {
            // Set initial state
            element.style.opacity = '0';
            element.style.transform = 'translateY(30px)';
            element.style.transition = `opacity 0.6s ease ${index * 0.1}s, transform 0.6s ease ${index * 0.1}s`;

            footerObserver.observe(element);
        });
    }

    /**
     * Contact forms enhancement
     */
    function setupContactForms() {
        elements.newsletterForms.forEach(function (form) {
            form.addEventListener('submit', function (e) {
                const emailInput = form.querySelector('input[type="email"]');
                const submitButton = form.querySelector('button[type="submit"]');

                if (!emailInput || !isValidEmail(emailInput.value)) {
                    e.preventDefault();
                    showFormMessage(form, 'Please enter a valid email address.', 'error');
                    return;
                }

                // Show loading state
                if (submitButton) {
                    const originalText = submitButton.textContent;
                    submitButton.textContent = 'Subscribing...';
                    submitButton.disabled = true;

                    // Reset after delay (for demo)
                    setTimeout(function () {
                        submitButton.textContent = originalText;
                        submitButton.disabled = false;
                        showFormMessage(form, 'Thank you for subscribing!', 'success');
                    }, 2000);
                }
            });
        });
    }

    /**
     * Payment icons animation
     */
    function setupPaymentIcons() {
        elements.paymentIcons.forEach(function (icon) {
            icon.addEventListener('mouseenter', function () {
                this.style.transform = 'translateY(-2px) rotate(5deg)';
            });

            icon.addEventListener('mouseleave', function () {
                this.style.transform = 'translateY(0) rotate(0deg)';
            });
        });
    }

    /**
     * Footer accessibility enhancement
     */
    function enhanceFooterAccessibility() {
        elements.footerLinks.forEach(function (link) {
            link.addEventListener('focus', function () {
                this.style.outline = '2px solid #FF5C00';
                this.style.outlineOffset = '2px';
            });

            link.addEventListener('blur', function () {
                this.style.outline = 'none';
            });
        });
    }

    /**
     * Footer performance optimization
     */
    function optimizeFooter() {
        const socialImages = document.querySelectorAll('.social-links img');

        if ('IntersectionObserver' in window && socialImages.length > 0) {
            const imageObserver = new IntersectionObserver(function (entries) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        if (img.dataset.src) {
                            img.src = img.dataset.src;
                            img.removeAttribute('data-src');
                            imageObserver.unobserve(img);
                        }
                    }
                });
            });

            socialImages.forEach(function (img) {
                imageObserver.observe(img);
            });
        }
    }

    /**
     * Cart functionality
     */
    function setupCartActions() {
        // Add to cart buttons
        elements.addToCartButtons.forEach(function (button) {
            button.addEventListener('click', function (e) {
                e.preventDefault();

                const productId = this.getAttribute('data-product-id');
                if (!productId) return;

                handleAddToCart(this, productId);
            });
        });

        // Wishlist buttons
        elements.wishlistButtons.forEach(function (button) {
            button.addEventListener('click', function (e) {
                e.preventDefault();

                const productId = this.getAttribute('data-product-id');
                if (!productId) return;

                handleWishlistToggle(this, productId);
            });
        });
    }

    /**
     * Handle add to cart
     */
    function handleAddToCart(button, productId) {
        const originalText = button.innerHTML;
        button.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Adding...';
        button.disabled = true;

        // Check if WooCommerce AJAX is available
        if (typeof wc_add_to_cart_params === 'undefined') {
            console.warn('WooCommerce AJAX parameters not available');
            resetButton(button, originalText);
            return;
        }

        fetch(wc_add_to_cart_params.wc_ajax_url.toString().replace('%%endpoint%%', 'add_to_cart'), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'product_id=' + productId + '&quantity=1',
        })
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    console.error('Add to cart error:', data.error);
                    button.innerHTML = 'Error';
                    setTimeout(() => resetButton(button, originalText), 1000);
                } else {
                    button.innerHTML = '<i class="fa fa-check"></i> Added!';
                    updateCartCount(data.cart_count);
                    setTimeout(() => resetButton(button, originalText), 1500);
                }
            })
            .catch(error => {
                console.error('Add to cart fetch error:', error);
                button.innerHTML = 'Error';
                setTimeout(() => resetButton(button, originalText), 1000);
            });
    }

    /**
     * Handle wishlist toggle
     */
    function handleWishlistToggle(button, productId) {
        // Check if user is logged in
        if (typeof wc_add_to_cart_params !== 'undefined' && !wc_add_to_cart_params.is_logged_in) {
            window.location.href = wc_add_to_cart_params.login_url;
            return;
        }

        const originalHTML = button.innerHTML;
        button.innerHTML = '<i class="fa fa-spinner fa-spin"></i>';
        button.disabled = true;

        // Use AJAX to toggle wishlist
        if (typeof wc_add_to_cart_params !== 'undefined') {
            fetch(wc_add_to_cart_params.ajax_url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=watchmodmarket_toggle_wishlist&product_id=' + productId + '&nonce=' + wc_add_to_cart_params.nonce,
            })
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        console.error('Wishlist error:', data.error);
                        button.innerHTML = originalHTML;
                    } else {
                        // Update button state
                        if (data.action === 'added') {
                            button.innerHTML = '<i class="fa fa-heart"></i>';
                            button.classList.add('in-wishlist');
                        } else {
                            button.innerHTML = '<i class="fa fa-heart-o"></i>';
                            button.classList.remove('in-wishlist');
                        }
                        updateWishlistCount(data.count);
                    }
                    button.disabled = false;
                })
                .catch(error => {
                    console.error('Wishlist fetch error:', error);
                    button.innerHTML = originalHTML;
                    button.disabled = false;
                });
        }
    }

    /**
     * Setup global event listeners
     */
    function setupGlobalEvents() {
        // Error handling
        window.addEventListener('error', function (e) {
            console.warn('WatchModMarket script error:', e.message);
        });

        // Handle escape key for modals/menus
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                // Close mobile menu if open
                if (elements.headerActions && elements.headerActions.classList.contains('show')) {
                    elements.mobileMenuToggle.click();
                }
            }
        });
    }

    /**
     * Utility Functions
     */
    function throttle(func, limit) {
        let inThrottle;
        return function () {
            const args = arguments;
            const context = this;
            if (!inThrottle) {
                func.apply(context, args);
                inThrottle = true;
                setTimeout(() => inThrottle = false, limit);
            }
        }
    }

    function debounce(func, wait) {
        let timeout;
        return function () {
            const context = this;
            const args = arguments;
            clearTimeout(timeout);
            timeout = setTimeout(function () {
                func.apply(context, args);
            }, wait);
        };
    }

    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    function showFormMessage(form, message, type) {
        // Remove existing messages
        const existingMessage = form.querySelector('.form-message');
        if (existingMessage) {
            existingMessage.remove();
        }

        // Create new message
        const messageElement = document.createElement('div');
        messageElement.className = `form-message form-message--${type}`;
        messageElement.textContent = message;

        // Style the message
        Object.assign(messageElement.style, {
            padding: '0.5rem',
            marginTop: '0.5rem',
            borderRadius: '4px',
            fontSize: '0.9rem',
            backgroundColor: type === 'error' ? '#ffebee' : '#e8f5e8',
            color: type === 'error' ? '#c62828' : '#2e7d32',
            border: type === 'error' ? '1px solid #ef5350' : '1px solid #4caf50'
        });

        form.appendChild(messageElement);

        // Remove message after timeout
        setTimeout(function () {
            if (messageElement.parentNode) {
                messageElement.remove();
            }
        }, config.messageTimeout);
    }

    function resetButton(button, originalText) {
        button.innerHTML = originalText;
        button.disabled = false;
    }

    function updateCartCount(count) {
        const cartCount = document.querySelector('.cart-count');
        if (cartCount && count !== undefined) {
            cartCount.textContent = count;
            if (count > 0) {
                cartCount.style.display = 'flex';
            }
        }
    }

    function updateWishlistCount(count) {
        const wishlistCount = document.querySelector('.wishlist-count');
        if (wishlistCount && count !== undefined) {
            if (count > 0) {
                wishlistCount.textContent = count;
                wishlistCount.style.display = 'flex';
            } else {
                wishlistCount.style.display = 'none';
            }
        }
    }

    // Expose public API
    window.WatchModMarket = {
        showMessage: showFormMessage,
        isValidEmail: isValidEmail,
        scrollToTop: function () {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        },
        throttle: throttle,
        debounce: debounce
    };

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

})();