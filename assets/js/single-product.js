/**
 * Single Product Page JavaScript
 */

(function ($) {
    'use strict';

    const SingleProduct = {
        init: function () {
            this.bindEvents();
            this.loadRecentlyViewed();
        },

        bindEvents: function () {
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

        handleImageSwitch: function (e) {
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

        increaseQuantity: function (e) {
            e.preventDefault();

            const $input = $(this).siblings('.qty-input');
            const currentVal = parseInt($input.val());
            const max = parseInt($input.attr('max')) || 999;

            if (currentVal < max) {
                $input.val(currentVal + 1).trigger('change');
            }
        },

        decreaseQuantity: function (e) {
            e.preventDefault();

            const $input = $(this).siblings('.qty-input');
            const currentVal = parseInt($input.val());
            const min = parseInt($input.attr('min')) || 1;

            if (currentVal > min) {
                $input.val(currentVal - 1).trigger('change');
            }
        },

        validateQuantity: function () {
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

        switchTab: function (e) {
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

        handleAddToCart: function (e) {
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
                success: function (response) {
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
                error: function () {
                    $button.removeClass('loading');
                    $button.find('span').text('Error adding to cart');
                }
            });
        },

        handleWatchlist: function (e) {
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
                success: function (response) {
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
                complete: function () {
                    $button.prop('disabled', false);
                }
            });
        },

        handleShare: function (e) {
            e.preventDefault();

            const $button = $(this);
            const url = window.location.href;
            const title = document.title;

            if (navigator.share) {
                navigator.share({
                    title: title,
                    url: url
                }).catch(() => { });
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

        handleAddToBuilder: function (e) {
            // Optional: Add confirmation dialog
            const confirmed = confirm('Add this part to your watch builder?');
            if (!confirmed) {
                e.preventDefault();
            }
        },

        handleImageZoom: function (e) {
            // Simple zoom effect - can be enhanced with a library like panzoom
            const $image = $(this).find('img');
            const offset = $(this).offset();
            const x = e.pageX - offset.left;
            const y = e.pageY - offset.top;

            $image.css({
                'transform-origin': `${x}px ${y}px`,
                'transform': 'scale(2)',
                'transition': 'transform 0.3s ease'
            });

            $(this).on('mouseleave', function () {
                $image.css({
                    'transform': 'scale(1)',
                    'transform-origin': 'center'
                });
            });
        },

        loadRecentlyViewed: function () {
            // Get recently viewed products from localStorage
            let recentlyViewed = JSON.parse(localStorage.getItem('recentlyViewed') || '[]');

            // Add current product if not already in list
            const currentProductId = watchmodmarket_product.current_product_id;
            if (currentProductId) {
                recentlyViewed = recentlyViewed.filter(id => id !== currentProductId);
                recentlyViewed.unshift(currentProductId);
                recentlyViewed = recentlyViewed.slice(0, 5);
                localStorage.setItem('recentlyViewed', JSON.stringify(recentlyViewed));
            }

            // Load and display recently viewed products
            if (recentlyViewed.length > 1) {
                this.displayRecentlyViewed(recentlyViewed.slice(1)); // Exclude current product
            } else {
                $('.recently-viewed').hide();
            }
        },

        displayRecentlyViewed: function (productIds) {
            $.ajax({
                url: watchmodmarket_product.ajax_url,
                type: 'POST',
                data: {
                    action: 'get_recently_viewed_products',
                    product_ids: productIds
                },
                success: function (response) {
                    if (response.success) {
                        $('#recently-viewed-products').html(response.data);
                    } else {
                        $('.recently-viewed').hide();
                    }
                },
                error: function () {
                    $('.recently-viewed').hide();
                }
            });
        },

        // Product variations handler (for variable products)
        initVariations: function () {
            if (!$('.variations_form').length) return;

            const $form = $('.variations_form');
            const $addToCart = $('.add-to-cart-big');

            $form.on('found_variation', function (event, variation) {
                // Update price
                $('.product-price-wrapper .price').html(variation.price_html);

                // Update stock status
                const $stock = $('.stock-status');
                if (variation.is_in_stock) {
                    $stock.removeClass('out-stock').addClass('in-stock')
                        .html('<i class="fa fa-check-circle"></i> In Stock');
                    $addToCart.prop('disabled', false).removeClass('btn-disabled');
                } else {
                    $stock.removeClass('in-stock').addClass('out-stock')
                        .html('<i class="fa fa-times-circle"></i> Out of Stock');
                    $addToCart.prop('disabled', true).addClass('btn-disabled');
                }

                // Update main image
                if (variation.image.src) {
                    $('.product-main-image img').attr('src', variation.image.src);
                }
            });

            $form.on('reset_data', function () {
                // Reset to original state
                $('.product-main-image img').attr('src', $('.product-thumbnail:first img').attr('src'));
            });
        },

        // Sticky add to cart bar for mobile
        initStickyCart: function () {
            if ($(window).width() > 768) return;

            const $productInfo = $('.product-information');
            const $stickyCart = $('<div class="sticky-add-to-cart">' +
                '<div class="container">' +
                '<div class="sticky-cart-content">' +
                '<div class="sticky-product-info">' +
                '<img src="' + $('.product-thumbnail:first img').attr('src') + '" alt="" class="sticky-product-thumb">' +
                '<div class="sticky-product-details">' +
                '<h3>' + $('.product-title').text() + '</h3>' +
                '<div class="sticky-price">' + $('.product-price-wrapper .price').html() + '</div>' +
                '</div>' +
                '</div>' +
                '<button class="btn btn-primary sticky-add-btn">' + watchmodmarket_product.i18n.add_to_cart + '</button>' +
                '</div>' +
                '</div>' +
                '</div>');

            $('body').append($stickyCart);

            // Show/hide sticky bar on scroll
            $(window).on('scroll', function () {
                const productBottom = $productInfo.offset().top + $productInfo.outerHeight();
                const scrollTop = $(window).scrollTop();

                if (scrollTop > productBottom) {
                    $stickyCart.addClass('active');
                } else {
                    $stickyCart.removeClass('active');
                }
            });

            // Handle sticky add to cart click
            $stickyCart.on('click', '.sticky-add-btn', function (e) {
                e.preventDefault();
                $('html, body').animate({
                    scrollTop: $('.add-to-cart-big').offset().top - 100
                }, 500);
            });
        },

        // Initialize product image gallery for touch devices
        initTouchGallery: function () {
            if (!('ontouchstart' in window)) return;

            const $gallery = $('.product-main-image');
            let touchStartX, touchEndX;

            $gallery.on('touchstart', function (e) {
                touchStartX = e.changedTouches[0].screenX;
            });

            $gallery.on('touchend', function (e) {
                touchEndX = e.changedTouches[0].screenX;
                const difference = touchStartX - touchEndX;

                // Threshold for swipe
                if (Math.abs(difference) > 50) {
                    const $currentThumb = $('.product-thumbnail.active');
                    let $nextThumb;

                    if (difference > 0) {
                        // Swipe left - next image
                        $nextThumb = $currentThumb.next('.product-thumbnail');
                        if (!$nextThumb.length) {
                            $nextThumb = $('.product-thumbnail:first');
                        }
                    } else {
                        // Swipe right - previous image
                        $nextThumb = $currentThumb.prev('.product-thumbnail');
                        if (!$nextThumb.length) {
                            $nextThumb = $('.product-thumbnail:last');
                        }
                    }

                    if ($nextThumb.length) {
                        $nextThumb.click();
                    }
                }
            });
        }
    };

    // Initialize when DOM is ready
    $(document).ready(function () {
        SingleProduct.init();
        SingleProduct.initVariations();
        SingleProduct.initStickyCart();
        SingleProduct.initTouchGallery();
    });

})(jQuery);