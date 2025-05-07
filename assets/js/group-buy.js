/**
 * Group Buy Page JavaScript
 * Path: /wp-content/themes/watchmodmarket/assets/js/group-buy.js
 */

(function ($) {
    'use strict';

    // DOM Ready
    $(document).ready(function () {
        initFaqToggle();
        initViewToggle();
        initFiltering();
        initQuickView();
        initWishlist();
        initCountdown();
        initStickyNav();
    });

    /**
     * Initialize FAQ Toggle functionality
     */
    function initFaqToggle() {
        const faqToggles = $('.faq-toggle');

        faqToggles.each(function () {
            $(this).on('click', function () {
                const expanded = $(this).attr('aria-expanded') === 'true';
                const answerId = $(this).attr('aria-controls');
                const answerElement = $('#' + answerId);

                // Toggle current FAQ
                $(this).attr('aria-expanded', !expanded);
                answerElement.attr('hidden', expanded);

                // Change the toggle icon
                if (expanded) {
                    $(this).find('.toggle-icon').text('+');
                } else {
                    $(this).find('.toggle-icon').text('×');
                }
            });
        });
    }

    /**
     * Initialize View Toggle (Grid/List)
     */
    function initViewToggle() {
        $('.view-option').on('click', function () {
            $('.view-option').removeClass('active');
            $(this).addClass('active');

            const gridView = $('.group-buy-grid');

            if ($(this).attr('title') === 'List View') {
                gridView.addClass('list-view');
                // Store preference in localStorage
                localStorage.setItem('group_buy_view', 'list');
            } else {
                gridView.removeClass('list-view');
                // Store preference in localStorage
                localStorage.setItem('group_buy_view', 'grid');
            }
        });

        // Load saved preference
        const savedView = localStorage.getItem('group_buy_view');
        if (savedView === 'list') {
            $('.view-option[title="List View"]').trigger('click');
        }
    }

    /**
     * Initialize Filtering and Sorting
     */
    function initFiltering() {
        // Category filter
        $('#category-filter').on('change', function () {
            applyFilters();
        });

        // Price filter
        $('#price-filter').on('change', function () {
            applyFilters();
        });

        // Sort options
        $('#sort-options').on('change', function () {
            applyFilters();
        });

        // Apply filters function
        function applyFilters() {
            const category = $('#category-filter').val();
            const priceRange = $('#price-filter').val();
            const sortBy = $('#sort-options').val();

            // Create URL with filter parameters
            let currentUrl = new URL(window.location.href);
            let searchParams = currentUrl.searchParams;

            // Set parameters
            if (category !== 'all') {
                searchParams.set('category', category);
            } else {
                searchParams.delete('category');
            }

            if (priceRange !== 'all') {
                searchParams.set('price', priceRange);
            } else {
                searchParams.delete('price');
            }

            if (sortBy) {
                searchParams.set('sort', sortBy);
            }

            // Redirect to filtered URL
            window.location.href = currentUrl.toString();
        }

        // Set initial filter values based on URL parameters
        function setInitialFilterValues() {
            const urlParams = new URLSearchParams(window.location.search);

            // Set category filter
            const category = urlParams.get('category');
            if (category) {
                $('#category-filter').val(category);
            }

            // Set price filter
            const price = urlParams.get('price');
            if (price) {
                $('#price-filter').val(price);
            }

            // Set sort option
            const sort = urlParams.get('sort');
            if (sort) {
                $('#sort-options').val(sort);
            }
        }

        // Initialize filter values
        setInitialFilterValues();
    }

    /**
     * Initialize Quick View functionality
     */
    function initQuickView() {
        // Quick View Button Click
        $('.quick-view-btn').on('click', function () {
            const productId = $(this).data('product-id');
            openQuickView(productId);
        });

        // Close Quick View Modal
        $(document).on('click', '.modal-close', function () {
            closeQuickView();
        });

        // Close modal when clicking outside the content
        $(document).on('click', '.quick-view-modal', function (e) {
            if ($(e.target).hasClass('quick-view-modal')) {
                closeQuickView();
            }
        });

        // Close modal on ESC key
        $(document).keyup(function (e) {
            if (e.key === 'Escape' && $('.quick-view-modal').hasClass('active')) {
                closeQuickView();
            }
        });

        // Open Quick View Modal
        function openQuickView(productId) {
            // If modal doesn't exist, create it
            if (!$('#quick-view-modal').length) {
                createQuickViewModal();
            }

            // Show modal with loading state
            $('#quick-view-modal').addClass('active');
            $('.modal-body').html('<div class="loading">Loading...</div>');

            // AJAX call to get product details
            $.ajax({
                url: watchmodmarket_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'watchmodmarket_quick_view',
                    product_id: productId,
                    nonce: watchmodmarket_ajax.nonce
                },
                success: function (response) {
                    if (response.success) {
                        // Update modal content
                        updateQuickViewContent(response.data);
                    } else {
                        $('.modal-body').html('<p>Error loading product details. Please try again.</p>');
                    }
                },
                error: function () {
                    $('.modal-body').html('<p>Error loading product details. Please try again.</p>');
                }
            });
        }

        // Close Quick View Modal
        function closeQuickView() {
            $('#quick-view-modal').removeClass('active');
        }

        // Create Quick View Modal HTML
        function createQuickViewModal() {
            const modalHtml = `
                <div id="quick-view-modal" class="quick-view-modal">
                    <div class="modal-content">
                        <button class="modal-close">&times;</button>
                        <div class="modal-body">
                            <!-- Content will be loaded via AJAX -->
                        </div>
                    </div>
                </div>
            `;

            $('body').append(modalHtml);
        }

        // Update Quick View Content
        function updateQuickViewContent(product) {
            const modalContent = `
                <div class="product-image">
                    <img src="${product.image}" alt="${product.title}">
                </div>
                <div class="product-details">
                    <h2>${product.title}</h2>
                    <div class="product-price">${product.price}</div>
                    <div class="product-description">${product.description}</div>
                    <div class="product-actions">
                        <a href="${product.url}" class="btn btn-primary">View Full Details</a>
                        <button class="btn btn-secondary add-to-cart-btn" data-product-id="${product.id}">Add to Cart</button>
                    </div>
                </div>
            `;

            $('.modal-body').html(modalContent);

            // Initialize Add to Cart functionality
            $('.add-to-cart-btn').on('click', function () {
                const productId = $(this).data('product-id');
                addToCart(productId);
            });
        }

        // Add to Cart function
        function addToCart(productId) {
            $.ajax({
                url: watchmodmarket_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'watchmodmarket_add_to_cart',
                    product_id: productId,
                    nonce: watchmodmarket_ajax.nonce
                },
                beforeSend: function () {
                    $('.add-to-cart-btn').text('Adding...');
                },
                success: function (response) {
                    if (response.success) {
                        $('.add-to-cart-btn').text('Added to Cart!');

                        // Update cart count if available
                        if ($('.cart-count').length) {
                            $('.cart-count').text(response.data.cart_count);
                        }

                        // Close modal after a short delay
                        setTimeout(function () {
                            closeQuickView();
                        }, 1500);
                    } else {
                        $('.add-to-cart-btn').text('Error');
                    }
                },
                error: function () {
                    $('.add-to-cart-btn').text('Error');
                }
            });
        }
    }

    /**
     * Initialize Wishlist functionality
     */
    function initWishlist() {
        $('.wishlist-btn').on('click', function () {
            const productId = $(this).data('product-id');

            // Toggle wishlist state
            if ($(this).hasClass('in-wishlist')) {
                removeFromWishlist(productId, $(this));
            } else {
                addToWishlist(productId, $(this));
            }
        });

        // Check if products are in wishlist
        checkWishlistStatus();

        // Add to Wishlist function
        function addToWishlist(productId, button) {
            $.ajax({
                url: watchmodmarket_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'watchmodmarket_add_to_wishlist',
                    product_id: productId,
                    nonce: watchmodmarket_ajax.nonce
                },
                beforeSend: function () {
                    button.text('Adding...');
                },
                success: function (response) {
                    if (response.success) {
                        button.addClass('in-wishlist');
                        button.text('In Wishlist');
                    } else {
                        button.text('Add to Wishlist');
                    }
                },
                error: function () {
                    button.text('Add to Wishlist');
                }
            });
        }

        // Remove from Wishlist function
        function removeFromWishlist(productId, button) {
            $.ajax({
                url: watchmodmarket_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'watchmodmarket_remove_from_wishlist',
                    product_id: productId,
                    nonce: watchmodmarket_ajax.nonce
                },
                beforeSend: function () {
                    button.text('Removing...');
                },
                success: function (response) {
                    if (response.success) {
                        button.removeClass('in-wishlist');
                        button.text('Add to Wishlist');
                    } else {
                        button.text('In Wishlist');
                    }
                },
                error: function () {
                    button.text('In Wishlist');
                }
            });
        }

        // Check Wishlist Status
        function checkWishlistStatus() {
            const productIds = [];

            // Collect all product IDs
            $('.wishlist-btn').each(function () {
                productIds.push($(this).data('product-id'));
            });

            if (productIds.length === 0) {
                return;
            }

            $.ajax({
                url: watchmodmarket_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'watchmodmarket_check_wishlist',
                    product_ids: productIds,
                    nonce: watchmodmarket_ajax.nonce
                },
                success: function (response) {
                    if (response.success && response.data.products) {
                        // Update wishlist buttons
                        $.each(response.data.products, function (index, productId) {
                            $('.wishlist-btn[data-product-id="' + productId + '"]')
                                .addClass('in-wishlist')
                                .text('In Wishlist');
                        });
                    }
                }
            });
        }
    }

    /**
     * Initialize Countdown timer
     */
    function initCountdown() {
        const countdownElement = $('.countdown');

        if (countdownElement.length) {
            // Update countdown every second
            setInterval(updateCountdown, 1000);
        }

        function updateCountdown() {
            // Get current time elements
            const daysElement = $('.countdown-item:nth-child(1) .countdown-number');
            const hoursElement = $('.countdown-item:nth-child(2) .countdown-number');
            const minutesElement = $('.countdown-item:nth-child(3) .countdown-number');

            // Get current values
            let days = parseInt(daysElement.text());
            let hours = parseInt(hoursElement.text());
            let minutes = parseInt(minutesElement.text());

            // Decrease time by 1 minute
            minutes--;

            if (minutes < 0) {
                minutes = 59;
                hours--;

                if (hours < 0) {
                    hours = 23;
                    days--;

                    if (days < 0) {
                        // Countdown finished
                        days = 0;
                        hours = 0;
                        minutes = 0;

                        // Refresh page to update status
                        location.reload();
                    }
                }
            }

            // Update countdown elements
            daysElement.text(days);
            hoursElement.text(hours);
            minutesElement.text(minutes);
        }
    }

    /**
     * Initialize Sticky Navigation
     */
    function initStickyNav() {
        const quickNav = $('.quick-nav');

        if (quickNav.length) {
            const quickNavOffset = quickNav.offset().top;

            $(window).on('scroll', function () {
                const scrollTop = $(window).scrollTop();

                if (scrollTop > quickNavOffset) {
                    quickNav.addClass('sticky');
                    $('body').css('padding-top', quickNav.outerHeight());
                } else {
                    quickNav.removeClass('sticky');
                    $('body').css('padding-top', 0);
                }
            });
        }
    }

    /**
     * Smooth Scroll to Sections
     */
    $('.quick-links a, .hero-actions a').on('click', function (e) {
        // Only apply to hash links pointing to the current page
        if (this.hash && this.pathname === location.pathname) {
            e.preventDefault();

            const target = $(this.hash);

            if (target.length) {
                $('html, body').animate({
                    scrollTop: target.offset().top - 100
                }, 500);
            }
        }
    });

})(jQuery);

jQuery(document).ready(function ($) {
    // Group Buy - Add to Cart functionality
    $('.btn-join').on('click', function (e) {
        e.preventDefault();

        const $button = $(this);
        const productId = $button.data('product-id');

        if (!productId) {
            console.error('Product ID not found');
            return;
        }

        $button.addClass('loading').text('Adding...');

        $.ajax({
            url: watchmodmarket_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'watchmodmarket_add_group_buy_to_cart',
                product_id: productId,
                quantity: 1,
                nonce: watchmodmarket_ajax.nonce
            },
            success: function (response) {
                if (response.success) {
                    $button.removeClass('loading').text('Added!');

                    // Update cart count in header
                    $('.cart-count').text(response.data.cart_count);

                    // Optional: redirect to cart
                    if (confirm('Product added to cart! Would you like to view your cart?')) {
                        window.location.href = response.data.cart_url;
                    }

                    setTimeout(function () {
                        $button.text('Join Now');
                    }, 2000);
                } else {
                    alert(response.data.message || 'Error adding product to cart');
                    $button.removeClass('loading').text('Join Now');
                }
            },
            error: function () {
                alert('An error occurred. Please try again.');
                $button.removeClass('loading').text('Join Now');
            }
        });
    });
});