/**
 * Header & Footer JavaScript
 * WatchModMarket Theme
 * Version: 1.0.0
 */

(function () {
    'use strict';

    // DOM Elements
    const header = document.getElementById('masthead');
    const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
    const headerActions = document.querySelector('.header-actions');
    const mainNav = document.querySelector('.main-nav');
    const backToTopButton = document.getElementById('back-to-top');
    const instaSlider = document.querySelector('.instagram-feed-slider');
    const currencySelector = document.getElementById('currency-selector');
    const languageSelector = document.getElementById('language-selector');

    /**
     * Initialize all functionality
     */
    function init() {
        setupMobileMenu();
        setupStickyHeader();
        setupBackToTop();
        setupInstagramSlider();
        setupCurrencySelector();
        setupLanguageSelector();
        setupDropdownMenus();
        setupCartActions();
    }

    /**
     * Mobile menu toggle
     */
    function setupMobileMenu() {
        if (!mobileMenuToggle || !headerActions) return;

        mobileMenuToggle.addEventListener('click', function () {
            mobileMenuToggle.classList.toggle('active');
            headerActions.classList.toggle('show');

            // Accessibility
            const expanded = mobileMenuToggle.getAttribute('aria-expanded') === 'true' || false;
            mobileMenuToggle.setAttribute('aria-expanded', !expanded);

            // Prevent body scroll when menu is open
            document.body.classList.toggle('menu-open');
        });

        // Close menu on window resize (desktop view)
        window.addEventListener('resize', function () {
            if (window.innerWidth >= 992 && headerActions.classList.contains('show')) {
                mobileMenuToggle.classList.remove('active');
                headerActions.classList.remove('show');
                document.body.classList.remove('menu-open');
                mobileMenuToggle.setAttribute('aria-expanded', 'false');
            }
        });
    }

    /**
     * Sticky header with show/hide on scroll
     */
    function setupStickyHeader() {
        if (!header) return;

        let lastScrollTop = 0;
        const scrollThreshold = 100; // How many pixels to scroll before showing/hiding

        window.addEventListener('scroll', function () {
            const currentScrollTop = window.pageYOffset || document.documentElement.scrollTop;

            // Determine scroll direction and distance
            if (currentScrollTop > lastScrollTop && currentScrollTop > scrollThreshold) {
                // Scrolling down and past threshold
                header.classList.add('scrolled-down');
                header.classList.remove('scrolled-up');
            } else if (currentScrollTop < lastScrollTop) {
                // Scrolling up
                header.classList.remove('scrolled-down');
                header.classList.add('scrolled-up');
            }

            // Reset when back at top
            if (currentScrollTop === 0) {
                header.classList.remove('scrolled-down', 'scrolled-up');
            }

            lastScrollTop = currentScrollTop;
        });
    }

    /**
     * Back to top button
     */
    function setupBackToTop() {
        if (!backToTopButton) return;

        // Show/hide button based on scroll position
        window.addEventListener('scroll', function () {
            if (window.pageYOffset > 300) {
                backToTopButton.classList.add('show');
            } else {
                backToTopButton.classList.remove('show');
            }
        });

        // Scroll to top when clicked
        backToTopButton.addEventListener('click', function () {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }

    /**
     * Instagram slider with Flickity if available
     */
    function setupInstagramSlider() {
        if (!instaSlider || typeof Flickity !== 'function') return;

        new Flickity(instaSlider, {
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
     * Currency selector
     */
    function setupCurrencySelector() {
        if (!currencySelector) return;

        currencySelector.addEventListener('change', function () {
            const currency = this.value;

            // Store selected currency in cookie or local storage
            localStorage.setItem('watchmodmarket_currency', currency);

            // Reload page to apply currency change
            // In a real implementation, this would use AJAX
            window.location.reload();
        });

        // Set initial value from storage
        const savedCurrency = localStorage.getItem('watchmodmarket_currency');
        if (savedCurrency) {
            currencySelector.value = savedCurrency;
        }
    }

    /**
     * Language selector
     */
    function setupLanguageSelector() {
        if (!languageSelector) return;

        languageSelector.addEventListener('change', function () {
            const language = this.value;

            // Store selected language in cookie or local storage
            localStorage.setItem('watchmodmarket_language', language);

            // Redirect to language-specific URL
            // In a real implementation, this would use proper language URLs
            window.location.href = window.location.pathname + '?lang=' + language;
        });

        // Set initial value from storage
        const savedLanguage = localStorage.getItem('watchmodmarket_language');
        if (savedLanguage) {
            languageSelector.value = savedLanguage;
        }
    }

    /**
     * Dropdown menus for desktop navigation
     */
    function setupDropdownMenus() {
        if (!mainNav) return;

        const menuItems = mainNav.querySelectorAll('li.menu-item-has-children');

        // Add toggle functionality for mobile view
        menuItems.forEach(function (item) {
            // Create toggle button for mobile
            const toggleBtn = document.createElement('button');
            toggleBtn.className = 'submenu-toggle';
            toggleBtn.setAttribute('aria-expanded', 'false');
            toggleBtn.innerHTML = '<span class="screen-reader-text">Toggle submenu</span><span aria-hidden="true">+</span>';

            // Add toggle button to menu item
            item.appendChild(toggleBtn);

            // Toggle submenu on click
            toggleBtn.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();

                const submenu = item.querySelector('.sub-menu');
                const expanded = toggleBtn.getAttribute('aria-expanded') === 'true' || false;

                // Toggle submenu
                if (submenu) {
                    submenu.classList.toggle('active');
                    toggleBtn.setAttribute('aria-expanded', !expanded);
                    toggleBtn.querySelector('[aria-hidden="true"]').textContent = expanded ? '+' : '-';
                }
            });
        });
    }

    /**
     * Setup cart functionality
     */
    function setupCartActions() {
        // Quick add to cart buttons
        const addToCartButtons = document.querySelectorAll('.add-to-cart');
        if (addToCartButtons.length > 0) {
            addToCartButtons.forEach(function (button) {
                button.addEventListener('click', function (e) {
                    e.preventDefault();

                    const productId = this.getAttribute('data-product-id');
                    if (!productId) return;

                    // Add loading state
                    const originalText = this.innerHTML;
                    this.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Adding...';
                    this.disabled = true;

                    // Use the WooCommerce AJAX add to cart
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
                                console.error('Error:', data.error);
                                this.innerHTML = 'Error';
                                setTimeout(() => {
                                    this.innerHTML = originalText;
                                    this.disabled = false;
                                }, 1000);
                            } else {
                                // Success
                                this.innerHTML = '<i class="fa fa-check"></i> Added!';

                                // Update cart count
                                const cartCount = document.querySelector('.cart-count');
                                if (cartCount && data.cart_count) {
                                    cartCount.textContent = data.cart_count;
                                }

                                // Reset button after delay
                                setTimeout(() => {
                                    this.innerHTML = originalText;
                                    this.disabled = false;
                                }, 1500);
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            this.innerHTML = 'Error';
                            setTimeout(() => {
                                this.innerHTML = originalText;
                                this.disabled = false;
                            }, 1000);
                        });
                });
            });
        }

        // Wishlist toggle buttons
        const wishlistButtons = document.querySelectorAll('.add-to-wishlist');
        if (wishlistButtons.length > 0) {
            wishlistButtons.forEach(function (button) {
                button.addEventListener('click', function (e) {
                    e.preventDefault();

                    // Check if user is logged in
                    if (!wc_add_to_cart_params.is_logged_in) {
                        window.location.href = wc_add_to_cart_params.login_url;
                        return;
                    }

                    const productId = this.getAttribute('data-product-id');
                    if (!productId) return;

                    // Add loading state
                    const originalHTML = this.innerHTML;
                    this.innerHTML = '<i class="fa fa-spinner fa-spin"></i>';
                    this.disabled = true;

                    // Use AJAX to toggle wishlist status
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
                                console.error('Error:', data.error);
                                this.innerHTML = originalHTML;
                                this.disabled = false;
                            } else {
                                // Success - update button state based on toggle result
                                if (data.action === 'added') {
                                    this.innerHTML = '<i class="fa fa-heart"></i>';
                                    this.classList.add('in-wishlist');
                                } else {
                                    this.innerHTML = '<i class="fa fa-heart-o"></i>';
                                    this.classList.remove('in-wishlist');
                                }

                                // Update wishlist count if available
                                const wishlistCount = document.querySelector('.wishlist-count');
                                if (wishlistCount && data.count !== undefined) {
                                    if (data.count > 0) {
                                        wishlistCount.textContent = data.count;
                                        wishlistCount.style.display = 'flex';
                                    } else {
                                        wishlistCount.style.display = 'none';
                                    }
                                }

                                this.disabled = false;
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            this.innerHTML = originalHTML;
                            this.disabled = false;
                        });
                });
            });
        }
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();