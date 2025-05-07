/**
 * Single Product Page JavaScript
 */

(function($) {
    'use strict';
    
    const SingleProduct = {
        init: function() {
            this.bindEvents();
            this.loadRecentlyViewed();
        },
        
        bindEvents: function() {
            // Product gallery
            $('.product-thumbnail').on('click', this.handleImageSwitch);
            
            // Quantity controls
            $('.qty-button.plus').on('click', this.increaseQuantity);
            $('.qty-button.minus').on('click', this.decreaseQuantity);
            $('.qty-input').on('change input', this.validateQuantity);
            
            // Product tabs
            $('.tab-button').on('click', this.switchTab);
            
            // Add to cart
            $('.add-to-cart-big').on('click', this.handleAddToCart);
            
            // Add to watchlist
            $('.add-to-watchlist').on('click', this.handleWatchlist);
            
            // Share button
            $('.share-product').on('click', this.handleShare);
            
            // Add to builder
            $('.add-to-builder').on('click', this.handleAddToBuilder);
            
            // Zoom effect
            $('.product-main-image').on('click', this.handleImageZoom);
        },
        
        handleImageSwitch: function(e) {
            e.preventDefault();
            
            const $thumb = $(this);
            const imageId = $thumb.data('image-id');
            const $mainImage = $('#product-main-image-' + imageId);
            
            // Update main image
            $('.current-product-image').removeClass('current-product-image');
            $mainImage.addClass('current-product-image');
            
            // Update thumbnail active state
            $('.product-thumbnail').removeClass('active');
            $thumb.addClass('active');
        },
        
        increaseQuantity: function(e) {
            e.preventDefault();
            
            const $input = $(this).siblings('.qty-input');
            const currentVal = parseInt($input.val());
            const max = parseInt($input.attr('max')) || 999;
            
            if (currentVal < max) {
                $input.val(currentVal + 1).trigger('change');
            }
        },
        
        decreaseQuantity: function(e) {
            e.preventDefault();
            
            const $input = $(this).siblings('.qty-input');
            const currentVal = parseInt($input.val());
            const min = parseInt($input.attr('min')) || 1;
            
            if (currentVal > min) {
                $input.val(currentVal - 1).trigger('change');
            }
        },
        
        validateQuantity: function() {
            const $input = $(this);
            const val = parseInt($input.val());
            const min = parseInt($input.attr('min')) || 1;
            const max = parseInt($input.attr('max')) || 999;
            
            if (isNaN(val) || val < min) {
                $input.val(min);
            } else if (val > max) {
                $input.val(max);
            }
        },
        
        switchTab: function(e) {
            e.preventDefault();
            
            const $button = $(this);
            const tabName = $button.data('tab');
            
            // Update tab buttons
            $('.tab-button').removeClass('active');
            $button.addClass('active');
            
            // Update tab content
            $('.tab-content').removeClass('active');
            $('#tab-' + tabName).addClass('active');
            
            // Scroll to tab content on mobile
            if ($(window).width() < 768) {
                $('html, body').animate({
                    scrollTop: $('.product-tabs-wrapper').offset().top - 100
                }, 300);
            }
        },
        
        handleAddToCart: function(e) {
            e.preventDefault();
            
            const $button = $(this);
            const $form = $button.closest('form');
            const productId = $button.val();
            const quantity = $form.find('.qty-input').val();
            
            // Visual feedback
            $button.addClass('loading');
            $button.find('span').text(watchmodmarket_product.i18n.adding_to_cart);
            
            // Add to cart via AJAX
            $.ajax({
                url: watchmodmarket_product.ajax_url,
                type: 'POST',
                data: {
                    action: 'woocommerce_add_to_cart',
                    product_id: productId,
                    quantity: quantity
                },
                success: function(response) {
                    if (response.error) {
                        // Handle error
                        $button.removeClass('loading');
                        $button.find('span').text(response.error_message || 'Error adding to cart');
                    } else {
                        // Success
                        $button.addClass('success');
                        $button.find('span').text(watchmodmarket_product.i18n.added_to_cart);
                        
                        // Update cart count
                        $('.cart-count').text(response.cart_count || '0');
                        
                        // Reset button after delay
                        setTimeout(() => {
                            $button.removeClass('loading success');
                            $button.find('span').text(watchmodmarket_product.i18n.add_to_cart);
                        }, 2000);
                    }
                },
                error: function() {
                    $button.removeClass('loading');
                    $button.find('span').text('Error adding to cart');
                }
            });
        },
        
        handleWatchlist: function(e) {
            e.preventDefault();
            
            const $button = $(this);
            const productId = $button.data('product-id');
            
            $button.prop('disabled', true);
            $button.find('i').removeClass('fa-heart').addClass('fa-spinner fa-spin');
            
            $.ajax({
                url: watchmodmarket_product.ajax_url,
                type: 'POST',
                data: {
                    action: 'add_to_watchlist',
                    product_id: productId,
                    nonce: watchmodmarket_product.nonce
                },
                success: function(response) {
                    if (response.success) {
                        const isAdded = response.data.action === 'added';
                        $button.find('i').removeClass('fa-spinner fa-spin').addClass(isAdded ? 'fa-check' : 'fa-heart');
                        $button.find('span').text(isAdded ? 'Added to Watchlist' : watchmodmarket_product.i18n.add_to_watchlist);
                        
                        if (isAdded) {
                            setTimeout(() => {
                                $button.find('i').removeClass('fa-check').addClass('fa-heart');
                                $button.find('span').text(watchmodmarket_product.i18n.remove_from_watchlist);
                            }, 1500);
                        }
                    }
                },
                complete: function() {
                    $button.prop('disabled', false);
                }
            });
        },
        
        handleShare: function(e) {
            e.preventDefault();
            
            const $button = $(this);
            const url = window.location.href;
            const title = document.title;
            
            if (navigator.share) {
                navigator.share({
                    title: title,
                    url: url
                }).catch(() => {});
            } else {
                // Fallback to copying link
                const $temp = $('<input>');
                $('body').append($temp);
                $temp.val(url).select();
                document.execCommand('copy');
                $temp.remove();
                
                $button.find('span').text(watchmodmarket_product.i18n.link_copied);
                setTimeout(() => {
                    $button.find('span').text(watchmodmarket_product.i18n.share);
                }, 1500);
            }
        },
        
        handleAddToBuilder: function(e) {
            // Optional: Add confirmation dialog
            const confirmed = confirm('Add this part to your watch builder?');
            if (!confirmed) {
                e.preventDefault();
            }
        },
        
        handleImageZoom: function(e) {
            // Implement zoom functionality
            const $image = $(this).find('img');
            const imageUrl = $image.attr('src');
            
            // Create zoom overlay if it doesn't exist
            if (!$('.image-zoom-overlay').length) {
                $('body').append(`
                    <div class="image-zoom-overlay">
                        <div class="zoom-container">
                            <img src="${imageUrl}" alt="Zoomed product image">
                            <button class="zoom-close">&times;</button>
                        </div>
                    </div>
                `);
                
                // Close on click outside or close button
                $('.image-zoom-overlay').on('click', function(e) {
                    if (e.target === this || $(e.target).hasClass('zoom-close')) {
                        $(this).remove();
                    }
                });
            }
        },
        
        loadRecentlyViewed: function() {
            // Get current product ID
            const productId = parseInt($('.product-page-container').data('product-id') || 0);
            
            // Get recently viewed from localStorage
            let recentlyViewed = JSON.parse(localStorage.getItem('recentlyViewed') || '[]');
            
            // Remove current product if in list
            recentlyViewed = recentlyViewed.filter(id => id !== productId);
            
            // Add current product to beginning
            recentlyViewed.unshift(productId);
            
            // Keep only last 5
            recentlyViewed = recentlyViewed.slice(0, 5);
            
            // Save back to localStorage
            localStorage.setItem('recentlyViewed', JSON.stringify(recentlyViewed));
            
            // Load recently viewed products
            this.displayRecentlyViewed(recentlyViewed.slice(1)); // Exclude current product
        },
        
        displayRecentlyViewed: function(productIds) {
            if (productIds.length === 0) {
                $('.recently-viewed').hide();
                return;
            }
            
            $.ajax({
                url: watchmodmarket_product.ajax_url,
                type: 'POST',
                data: {
                    action: 'get_recently_viewed_products',
                    product_ids: productIds
                },
                success: function(response) {
                    if (response.success) {
                        $('#recently-viewed-products').html(response.data);
                    } else {
                        $('.recently-viewed').hide();
                    }
                },
                error: function() {
                    $('.recently-viewed').hide();
                }
            });
        },
        
        // Initialize product image carousel if multiple images
        initImageCarousel: function() {
            const $thumbnails = $('.product-thumbnail');
            
            if ($thumbnails.length > 1) {
                // Add navigation arrows
                $('.product-gallery').append(`
                    <button class="gallery-nav prev" aria-label="Previous image">&lt;</button>
                    <button class="gallery-nav next" aria-label="Next image">&gt;</button>
                `);
                
                // Navigation functionality
                $('.gallery-nav').on('click', function(e) {
                    e.preventDefault();
                    
                    const $current = $('.product-thumbnail.active');
                    let $next;
                    
                    if ($(this).hasClass('prev')) {
                        $next = $current.prev('.product-thumbnail');
                        if (!$next.length) {
                            $next = $thumbnails.last();
                        }
                    } else {
                        $next = $current.next('.product-thumbnail');
                        if (!$next.length) {
                            $next = $thumbnails.first();
                        }
                    }
                    
                    $next.trigger('click');
                });
                
                // Keyboard navigation
                $(document).on('keydown', function(e) {
                    if (e.key === 'ArrowLeft') {
                        $('.gallery-nav.prev').trigger('click');
                    } else if (e.key === 'ArrowRight') {
                        $('.gallery-nav.next').trigger('click');
                    }
                });
            }
        },
        
        // Handle sticky add to cart on scroll
        initStickyCart: function() {
            const $form = $('.product-cart-form');
            const $stickyCart = $('<div class="sticky-cart-container"></div>');
            const $productInfo = $('.product-information');
            
            // Clone cart form for sticky version
            const $clonedForm = $form.clone().addClass('sticky-cart');
            $stickyCart.append($clonedForm);
            $('body').append($stickyCart);
            
            // Show/hide sticky cart on scroll
            $(window).on('scroll', function() {
                const scrollTop = $(window).scrollTop();
                const formOffset = $form.offset().top;
                const formBottom = formOffset + $form.outerHeight();
                
                if (scrollTop > formBottom) {
                    $stickyCart.addClass('visible');
                } else {
                    $stickyCart.removeClass('visible');
                }
            });
        },
        
        // Load related products dynamically
        loadRelatedProducts: function() {
            const $relatedContainer = $('.related-products-grid');
            
            if ($relatedContainer.length) {
                const productId = $('.product-page-container').data('product-id');
                
                $.ajax({
                    url: watchmodmarket_product.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'get_related_products',
                        product_id: productId
                    },
                    beforeSend: function() {
                        $relatedContainer.html('<div class="loading-spinner"></div>');
                    },
                    success: function(response) {
                        if (response.success) {
                            $relatedContainer.html(response.data);
                        }
                    }
                });
            }
        }
    };
    
    // Document ready
    $(document).ready(function() {
        SingleProduct.init();
        SingleProduct.initImageCarousel();
        SingleProduct.initStickyCart();
        SingleProduct.loadRelatedProducts();
    });
    
})(jQuery);

/* Additional CSS for JavaScript components */
const dynamicStyles = `
    .image-zoom-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.9);
        z-index: 9999;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2rem;
    }
    
    .zoom-container {
        position: relative;
        max-width: 90%;
        max-height: 90%;
    }
    
    .zoom-container img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
    }
    
    .zoom-close {
        position: absolute;
        top: -40px;
        right: 0;
        background: none;
        border: none;
        color: white;
        font-size: 2rem;
        cursor: pointer;
        padding: 0 10px;
    }
    
    .gallery-nav {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        background: rgba(0, 0, 0, 0.7);
        color: white;
        border: none;
        padding: 1rem;
        cursor: pointer;
        font-size: 1.5rem;
        z-index: 2;
        transition: background 0.3s ease;
    }
    
    .gallery-nav:hover {
        background: rgba(0, 0, 0, 0.9);
    }
    
    .gallery-nav.prev {
        left: 10px;
    }
    
    .gallery-nav.next {
        right: 10px;
    }
    
    .sticky-cart-container {
        position: fixed;
        bottom: 0;
        left: 0;
        width: 100%;
        background: white;
        border-top: 5px solid #000;
        box-shadow: 0 -5px 15px rgba(0, 0, 0, 0.2);
        transform: translateY(100%);
        transition: transform 0.3s ease;
        z-index: 1000;
    }
    
    .sticky-cart-container.visible {
        transform: translateY(0);
    }
    
    .sticky-cart {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1rem;
        max-width: 1280px;
        margin: 0 auto;
    }
    
    .loading-spinner {
        width: 40px;
        height: 40px;
        margin: 2rem auto;
        border: 4px solid #f3f3f3;
        border-top: 4px solid #000;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
`;

// Add dynamic styles to document
const styleSheet = document.createElement('style');
styleSheet.textContent = dynamicStyles;
document.head.appendChild(styleSheet);