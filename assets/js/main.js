/**
 * WatchModMarket Main JavaScript - Modernized Version
 * 
 * This file contains core functionality for the WatchModMarket theme
 * It has been refactored to reduce jQuery dependency and use modern JS patterns
 */

// Use a module pattern to avoid polluting the global namespace
const WatchModMarket = (function () {
    'use strict';

    // Store DOM references to avoid repeated queries
    const domElements = {};

    // Store configuration and state
    const config = {
        breakpoints: {
            mobile: 768,
            tablet: 992
        },
        selectors: {
            header: '.site-header',
            mobileMenuToggle: '#mobile-menu-toggle',
            headerActions: '.header-actions',
            menuItems: '.main-menu li a',
            searchForm: '.search-bar',
            backToTop: '.back-to-top',
            quantityBtns: '.quantity-btn',
            newsletterForm: '.newsletter-form'
        },
        classes: {
            active: 'active',
            show: 'show',
            menuOpen: 'menu-open',
            visible: 'visible'
        }
    };

    /**
     * Initialize the application
     */
    function init() {
        // Cache DOM elements
        cacheElements();

        // Initialize components
        initHeaderFunctions();
        initQuantitySelector();
        initBackToTop();
        initSmoothScroll();
        initNewsletterForm();

        // WooCommerce related functionality (conditionally loaded)
        if (typeof window.woocommerce_params !== 'undefined') {
            initShoppingCart();
            initQuickView();
            initShopFilters();
        }

        console.log('WatchModMarket JS initialized');
    }

    /**
     * Cache DOM elements for better performance
     */
    function cacheElements() {
        const selectors = config.selectors;

        // Store element references to avoid repeated DOM queries
        Object.keys(selectors).forEach(key => {
            const elements = document.querySelectorAll(selectors[key]);
            domElements[key] = elements.length === 1 ? elements[0] : elements;
        });
    }

    /**
     * Header & Navigation Functionality
     */
    function initHeaderFunctions() {
        const menuToggle = domElements.mobileMenuToggle;
        const headerActions = domElements.headerActions;

        if (!menuToggle || !headerActions) return;

        // Mobile Menu Toggle
        menuToggle.addEventListener('click', function () {
            const isExpanded = this.getAttribute('aria-expanded') === 'true';

            // Toggle aria-expanded state
            this.setAttribute('aria-expanded', !isExpanded);

            // Toggle menu visibility
            headerActions.classList.toggle(config.classes.show);

            // Add body class to prevent scrolling when menu is open
            document.body.classList.toggle(config.classes.menuOpen);
        });

        // Close menu when clicking outside
        document.addEventListener('click', function (event) {
            if (!event.target.closest(config.selectors.mobileMenuToggle) &&
                !event.target.closest(config.selectors.headerActions) &&
                headerActions.classList.contains(config.classes.show)) {

                menuToggle.setAttribute('aria-expanded', 'false');
                headerActions.classList.remove(config.classes.show);
                document.body.classList.remove(config.classes.menuOpen);
            }
        });

        // Close menu when escape key is pressed
        document.addEventListener('keyup', function (event) {
            if (event.key === 'Escape' && headerActions.classList.contains(config.classes.show)) {
                menuToggle.setAttribute('aria-expanded', 'false');
                headerActions.classList.remove(config.classes.show);
                document.body.classList.remove(config.classes.menuOpen);
            }
        });

        // Handle window resize - reset menu on large screens
        window.addEventListener('resize', function () {
            if (window.innerWidth > config.breakpoints.tablet &&
                headerActions.classList.contains(config.classes.show)) {
                menuToggle.setAttribute('aria-expanded', 'false');
                headerActions.classList.remove(config.classes.show);
                document.body.classList.remove(config.classes.menuOpen);
            }
        });
    }

    /**
     * Quantity Selector Functionality
     */
    function initQuantitySelector() {
        const quantityBtns = document.querySelectorAll('.quantity-btn');

        if (!quantityBtns.length) return;

        quantityBtns.forEach(btn => {
            btn.addEventListener('click', function () {
                const input = this.closest('.quantity').querySelector('.quantity-input');
                const currentValue = parseInt(input.value) || 0;
                const min = parseInt(input.getAttribute('min')) || 1;
                const max = parseInt(input.getAttribute('max')) || 999;

                if (this.classList.contains('plus') && currentValue < max) {
                    input.value = currentValue + 1;
                } else if (this.classList.contains('minus') && currentValue > min) {
                    input.value = currentValue - 1;
                }

                // Trigger change event for other scripts that might be listening
                const event = new Event('change', { bubbles: true });
                input.dispatchEvent(event);
            });
        });

        // Validate quantity on input
        document.querySelectorAll('.quantity-input').forEach(input => {
            input.addEventListener('change', function () {
                const val = parseInt(this.value);
                const min = parseInt(this.getAttribute('min')) || 1;
                const max = parseInt(this.getAttribute('max')) || 999;

                if (isNaN(val) || val < min) {
                    this.value = min;
                } else if (val > max) {
                    this.value = max;
                }
            });
        });
    }

    /**
     * Back to Top Button
     */
    function initBackToTop() {
        const backToTop = document.querySelector('.back-to-top');

        if (!backToTop) return;

        // Show/hide button based on scroll position
        window.addEventListener('scroll', function () {
            if (window.scrollY > 300) {
                backToTop.classList.add(config.classes.visible);
            } else {
                backToTop.classList.remove(config.classes.visible);
            }
        });

        // Smooth scroll to top when clicked
        backToTop.addEventListener('click', function (e) {
            e.preventDefault();
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }

    /**
     * Smooth Scroll for Anchor Links
     */
    function initSmoothScroll() {
        document.querySelectorAll('a[href^="#"]:not([href="#"])').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();

                const targetId = this.getAttribute('href');
                const targetElement = document.querySelector(targetId);

                if (targetElement) {
                    const headerOffset = 80; // Offset for fixed header
                    const elementPosition = targetElement.getBoundingClientRect().top;
                    const offsetPosition = elementPosition + window.pageYOffset - headerOffset;

                    window.scrollTo({
                        top: offsetPosition,
                        behavior: 'smooth'
                    });

                    // Update URL hash without page jump
                    if (history.pushState) {
                        history.pushState(null, null, targetId);
                    }
                }
            });
        });
    }

    /**
     * Newsletter Form Submission
     */
    function initNewsletterForm() {
        const newsletterForms = document.querySelectorAll('.newsletter-form');

        if (!newsletterForms.length) return;

        newsletterForms.forEach(form => {
            form.addEventListener('submit', function (e) {
                e.preventDefault();

                const emailInput = this.querySelector('input[type="email"]');
                const email = emailInput.value;

                // Simple validation
                if (!email || !email.includes('@')) {
                    emailInput.classList.add('error');
                    return;
                }

                emailInput.classList.remove('error');

                // Disable form while submitting
                const submitButton = this.querySelector('button');
                submitButton.disabled = true;
                submitButton.textContent = 'Subscribing...';

                // AJAX submission using Fetch API
                fetch(this.action, {
                    method: 'POST',
                    body: new FormData(this),
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        // Show success message
                        this.innerHTML = '<p class="success-message">Thank you for subscribing!</p>';
                    })
                    .catch(error => {
                        // Reset form
                        submitButton.disabled = false;
                        submitButton.textContent = 'Subscribe';

                        // Show error message
                        const errorMessage = document.createElement('p');
                        errorMessage.className = 'error-message';
                        errorMessage.textContent = 'Error submitting form. Please try again.';

                        emailInput.insertAdjacentElement('afterend', errorMessage);

                        // Remove error message after 3 seconds
                        setTimeout(() => {
                            form.querySelector('.error-message').remove();
                        }, 3000);
                    });
            });
        });
    }

    /**
     * Shopping Cart Functionality
     */
    function initShoppingCart() {
        const addToCartButtons = document.querySelectorAll('.add-to-cart');

        if (!addToCartButtons.length) return;

        addToCartButtons.forEach(button => {
            button.addEventListener('click', function (e) {
                e.preventDefault();

                const productId = this.dataset.productId;
                const originalText = this.textContent;

                // Change button text while loading
                this.textContent = watchmodmarket_ajax.adding_to_cart;
                this.classList.add('loading');

                // AJAX call to add to cart using Fetch API
                fetch(watchmodmarket_ajax.ajax_url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({
                        action: 'watchmodmarket_add_to_cart',
                        product_id: productId,
                        security: watchmodmarket_ajax.nonce
                    })
                })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            // Update cart count
                            document.querySelectorAll('.cart-count').forEach(el => {
                                el.textContent = data.cart_count;
                            });

                            // Show notification
                            const notification = document.getElementById('cart-notification');
                            if (notification) {
                                notification.removeAttribute('hidden');
                                notification.classList.add('active');

                                // Hide notification after 3 seconds
                                setTimeout(() => {
                                    notification.classList.remove('active');
                                    notification.setAttribute('hidden', true);
                                }, 3000);
                            }

                            // Reset button
                            this.textContent = watchmodmarket_ajax.added_to_cart;
                            this.classList.remove('loading');
                            this.classList.add('added');

                            // Reset button text after 2 seconds
                            setTimeout(() => {
                                this.textContent = originalText;
                                this.classList.remove('added');
                            }, 2000);
                        }
                    })
                    .catch(error => {
                        this.textContent = originalText;
                        this.classList.remove('loading');
                        alert('Error adding product to cart. Please try again.');
                    });
            });
        });

        // Notification close button
        document.querySelectorAll('.notification-close').forEach(button => {
            button.addEventListener('click', function () {
                const notification = document.getElementById('cart-notification');
                if (notification) {
                    notification.classList.remove('active');
                    notification.setAttribute('hidden', true);
                }
            });
        });
    }

    /**
     * Quick View Modal
     */
    function initQuickView() {
        // Quick View Button
        document.querySelectorAll('.quick-view').forEach(button => {
            button.addEventListener('click', function (e) {
                e.preventDefault();

                const productCard = this.closest('.product-card');
                if (!productCard) return;

                const productId = productCard.dataset.productId;
                const modal = document.getElementById('quick-view-modal');
                const content = document.getElementById('quick-view-content');

                if (!modal || !content) return;

                // Show modal with loading state
                modal.classList.add('show');
                modal.removeAttribute('aria-hidden');
                content.innerHTML = '<div class="loading-spinner">Loading...</div>';

                // AJAX call to get product data using Fetch API
                fetch(watchmodmarket_ajax.ajax_url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({
                        action: 'watchmodmarket_quick_view',
                        product_id: productId,
                        security: watchmodmarket_ajax.nonce
                    })
                })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            content.innerHTML = data.html;

                            // Initialize quantity selector in modal
                            initQuantitySelector();
                        } else {
                            content.innerHTML = '<p class="error-message">Error loading product data. Please try again.</p>';
                        }
                    })
                    .catch(error => {
                        content.innerHTML = '<p class="error-message">Error loading product data. Please try again.</p>';
                    });
            });
        });

        // Close modal
        document.querySelectorAll('.modal-close').forEach(closeButton => {
            closeButton.addEventListener('click', function () {
                const modal = document.getElementById('quick-view-modal');
                if (modal) {
                    modal.classList.remove('show');
                    modal.setAttribute('aria-hidden', 'true');
                }
            });
        });

        // Close modal when clicking outside
        document.addEventListener('click', function (event) {
            const modal = document.getElementById('quick-view-modal');
            if (modal && modal.classList.contains('show') &&
                !event.target.closest('.modal-content') &&
                !event.target.closest('.quick-view')) {
                modal.classList.remove('show');
                modal.setAttribute('aria-hidden', 'true');
            }
        });

        // Close modal when escape key is pressed
        document.addEventListener('keyup', function (event) {
            if (event.key === 'Escape') {
                const modal = document.getElementById('quick-view-modal');
                if (modal && modal.classList.contains('show')) {
                    modal.classList.remove('show');
                    modal.setAttribute('aria-hidden', 'true');
                }
            }
        });
    }

    /**
     * Shop Filters
     */
    function initShopFilters() {
        // Toggle filter groups
        document.querySelectorAll('.filter-group h4').forEach(header => {
            header.addEventListener('click', function () {
                this.parentElement.classList.toggle('collapsed');
            });
        });

        // Toggle filter block on mobile
        const filterToggleBtn = document.querySelector('.filter-toggle-btn');
        if (filterToggleBtn) {
            filterToggleBtn.addEventListener('click', function () {
                this.classList.toggle('active');
                this.setAttribute('aria-expanded', this.classList.contains('active'));

                const filterBlock = document.getElementById('filter-block');
                if (filterBlock) {
                    filterBlock.classList.toggle('show');
                }
            });
        }

        // Shop view toggle (grid/list)
        document.querySelectorAll('.shop-view button').forEach(button => {
            button.addEventListener('click', function () {
                const view = this.classList.contains('view-grid') ? 'grid' : 'list';

                // Update active state
                document.querySelectorAll('.shop-view button').forEach(btn => {
                    btn.classList.remove('active');
                });
                this.classList.add('active');

                // Update view class
                const shopProducts = document.querySelector('.shop-products');
                if (shopProducts) {
                    shopProducts.classList.remove('grid-view', 'list-view');
                    shopProducts.classList.add(view + '-view');
                }

                // Save preference in localStorage
                localStorage.setItem('wmm_shop_view', view);
            });
        });

        // Load saved view preference
        const savedView = localStorage.getItem('wmm_shop_view');
        if (savedView) {
            const viewButton = document.querySelector('.view-' + savedView);
            if (viewButton) {
                // Use click event to ensure all handlers are triggered
                viewButton.click();
            }
        }

        // Price slider
        const priceSlider = document.querySelector('.price-slider');
        if (priceSlider) {
            const minInput = document.getElementById('price-min');
            const maxInput = document.getElementById('price-max');

            if (minInput && maxInput) {
                priceSlider.addEventListener('input', function () {
                    const value = this.value;
                    maxInput.value = value;
                });

                minInput.addEventListener('change', function () {
                    const minValue = parseInt(this.value) || 0;
                    const maxValue = parseInt(maxInput.value) || 100;

                    if (minValue > maxValue) {
                        this.value = maxValue;
                    }
                });

                maxInput.addEventListener('change', function () {
                    const maxValue = parseInt(this.value) || 100;
                    const minValue = parseInt(minInput.value) || 0;

                    if (maxValue < minValue) {
                        this.value = minValue;
                    }

                    priceSlider.value = maxValue;
                });
            }
        }

        // Clear filters
        document.querySelectorAll('.btn-clear, .clear-all-filters').forEach(button => {
            button.addEventListener('click', function (e) {
                e.preventDefault();

                // Reset all checkboxes
                document.querySelectorAll('.filter-list input[type="checkbox"]')
                    .forEach(checkbox => {
                        checkbox.checked = false;
                    });

                // Reset price inputs
                const minPrice = document.getElementById('price-min');
                const maxPrice = document.getElementById('price-max');
                const priceSlider = document.querySelector('.price-slider');

                if (minPrice) {
                    minPrice.value = minPrice.dataset.min || 0;
                }

                if (maxPrice) {
                    maxPrice.value = maxPrice.dataset.max || 100;
                }

                if (priceSlider) {
                    priceSlider.value = maxPrice ? maxPrice.value : 100;
                }

                // If form exists, submit it
                const filterForm = document.getElementById('shop-filters-form');
                if (filterForm) {
                    filterForm.submit();
                }
            });
        });

        // Remove individual filter
        document.querySelectorAll('.remove-filter').forEach(button => {
            button.addEventListener('click', function () {
                const filter = this.dataset.filter;
                const value = this.dataset.value;

                // Find and uncheck the corresponding checkbox
                const checkbox = document.querySelector(
                    `.filter-list input[type="checkbox"][name="${filter}"][value="${value}"]`
                );

                if (checkbox) {
                    checkbox.checked = false;

                    // If form exists, submit it
                    const filterForm = document.getElementById('shop-filters-form');
                    if (filterForm) {
                        filterForm.submit();
                    }
                }
            });
        });
    }

    // Public API
    return {
        init: init
    };
})();

// Initialize the application when DOM is fully loaded
document.addEventListener('DOMContentLoaded', function () {
    WatchModMarket.init();
});