<?php
/**
 * WooCommerce customizations for WatchModMarket theme
 */

// Remove default WooCommerce styles
add_filter('woocommerce_enqueue_styles', '__return_empty_array');

// Add theme support for WooCommerce features
function watchmodmarket_woocommerce_setup() {
    add_theme_support('woocommerce', array(
        'thumbnail_image_width' => 300,
        'single_image_width'    => 600,
        'product_grid'          => array(
            'default_rows'    => 3,
            'min_rows'        => 1,
            'max_rows'        => 8,
            'default_columns' => 3,
            'min_columns'     => 1,
            'max_columns'     => 4,
        ),
    ));
}
add_action('after_setup_theme', 'watchmodmarket_woocommerce_setup');

// Customize WooCommerce product display
function watchmodmarket_woocommerce_product_card() {
    // Remove default WooCommerce product title
    remove_action('woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title', 10);
    // Add custom product title with design
    add_action('woocommerce_shop_loop_item_title', 'watchmodmarket_template_loop_product_title', 10);
    
    // Remove default WooCommerce product thumbnail
    remove_action('woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10);
    // Add custom product thumbnail with design
    add_action('woocommerce_before_shop_loop_item_title', 'watchmodmarket_template_loop_product_thumbnail', 10);
    
    // Remove default WooCommerce product price
    remove_action('woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10);
    // Add custom product price with design
    add_action('woocommerce_after_shop_loop_item_title', 'watchmodmarket_template_loop_price', 10);
    
    // Remove default WooCommerce add to cart button
    remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10);
    // Add custom add to cart button with design
    add_action('woocommerce_after_shop_loop_item', 'watchmodmarket_template_loop_add_to_cart', 10);
}
add_action('init', 'watchmodmarket_woocommerce_product_card');

// Custom product title
function watchmodmarket_template_loop_product_title() {
    echo '<h3 class="product-card-title">' . get_the_title() . '</h3>';
}

// Custom product thumbnail
function watchmodmarket_template_loop_product_thumbnail() {
    global $product;
    $image_id = $product->get_image_id();
    
    if ($image_id) {
        echo '<div class="product-image">';
        echo wp_get_attachment_image($image_id, 'woocommerce_thumbnail', false, array('class' => 'product-thumbnail'));
        echo '<div class="product-actions">';
        echo '<button class="quick-view" data-product-id="' . esc_attr($product->get_id()) . '">' . esc_html__('Quick View', 'watchmodmarket') . '</button>';
        echo '<button class="add-to-cart" data-product-id="' . esc_attr($product->get_id()) . '" data-product-name="' . esc_attr($product->get_name()) . '" data-product-price="' . esc_attr($product->get_price()) . '">' . esc_html__('Add to Cart', 'watchmodmarket') . '</button>';
        echo '</div>';
        echo '</div>';
    } else {
        echo '<div class="product-image no-image">';
        echo '<div class="no-image-placeholder">' . esc_html__('No Image', 'watchmodmarket') . '</div>';
        echo '<div class="product-actions">';
        echo '<button class="quick-view" data-product-id="' . esc_attr($product->get_id()) . '">' . esc_html__('Quick View', 'watchmodmarket') . '</button>';
        echo '<button class="add-to-cart" data-product-id="' . esc_attr($product->get_id()) . '" data-product-name="' . esc_attr($product->get_name()) . '" data-product-price="' . esc_attr($product->get_price()) . '">' . esc_html__('Add to Cart', 'watchmodmarket') . '</button>';
        echo '</div>';
        echo '</div>';
    }
    
    // Add badges
    if ($product->is_on_sale()) {
        echo '<div class="product-badge sale">' . esc_html__('Sale', 'watchmodmarket') . '</div>';
    } elseif ($product->is_featured()) {
        echo '<div class="product-badge best-seller">' . esc_html__('Best Seller', 'watchmodmarket') . '</div>';
    } elseif (has_term('new', 'product_tag', $product->get_id())) {
        echo '<div class="product-badge new">' . esc_html__('New', 'watchmodmarket') . '</div>';
    }
}

// Custom product price
function watchmodmarket_template_loop_price() {
    global $product;
    echo '<div class="product-meta">';
    
    // Display rating
    if (wc_review_ratings_enabled()) {
        $rating_count = $product->get_rating_count();
        $review_count = $product->get_review_count();
        $average      = $product->get_average_rating();
        
        if ($rating_count > 0) {
            echo '<div class="product-rating">';
            echo wc_get_rating_html($average, $rating_count);
            echo '<span class="review-count">(' . esc_html($review_count) . ')</span>';
            echo '</div>';
        } else {
            echo '<div class="product-rating">';
            echo '<span class="no-rating">' . esc_html__('No Reviews', 'watchmodmarket') . '</span>';
            echo '</div>';
        }
    }
    
    // Display price
    if ($price_html = $product->get_price_html()) {
        echo '<div class="product-price">' . $price_html . '</div>';
    }
    
    echo '</div>';
}

// Custom add to cart button
function watchmodmarket_template_loop_add_to_cart() {
    global $product;
    
    echo '<div class="product-card-actions">';
    echo '<a href="' . esc_url(get_permalink($product->get_id())) . '" class="btn btn-secondary">' . esc_html__('View Details', 'watchmodmarket') . '</a>';
    
    echo '<button class="btn btn-primary add-to-cart" data-product-id="' . esc_attr($product->get_id()) . '" data-product-name="' . esc_attr($product->get_name()) . '" data-product-price="' . esc_attr($product->get_price()) . '">' . esc_html__('Add to Cart', 'watchmodmarket') . '</button>';
    echo '</div>';
}

// Add AJAX add to cart functionality
function watchmodmarket_ajax_add_to_cart() {
    check_ajax_referer('watchmodmarket_ajax', 'security');
    
    $product_id = absint($_POST['product_id']);
    $quantity = empty($_POST['quantity']) ? 1 : wc_stock_amount($_POST['quantity']);
    
    WC()->cart->add_to_cart($product_id, $quantity);
    
    wp_send_json(array(
        'success' => true,
        'message' => __('Product added to cart!', 'watchmodmarket'),
        'cart_count' => WC()->cart->get_cart_contents_count()
    ));
    
    wp_die();
}
add_action('wp_ajax_watchmodmarket_add_to_cart', 'watchmodmarket_ajax_add_to_cart');
add_action('wp_ajax_nopriv_watchmodmarket_add_to_cart', 'watchmodmarket_ajax_add_to_cart');

// Add AJAX quick view functionality
function watchmodmarket_ajax_quick_view() {
    check_ajax_referer('watchmodmarket_ajax', 'security');
    
    $product_id = absint($_POST['product_id']);
    $product = wc_get_product($product_id);
    
    if (!$product) {
        wp_send_json(array(
            'success' => false,
            'message' => __('Product not found', 'watchmodmarket')
        ));
        wp_die();
    }
    
    ob_start();
    get_template_part('template-parts/quick-view');
    $html = ob_get_clean();
    
    wp_send_json(array(
        'success' => true,
        'html' => $html
    ));
    
    wp_die();
}
add_action('wp_ajax_watchmodmarket_quick_view', 'watchmodmarket_ajax_quick_view');
add_action('wp_ajax_nopriv_watchmodmarket_quick_view', 'watchmodmarket_ajax_quick_view');

// Add to cart script
function watchmodmarket_add_to_cart_script() {
    wp_enqueue_script('watchmodmarket-add-to-cart', get_template_directory_uri() . '/assets/js/add-to-cart.js', array('jquery'), WATCHMODMARKET_VERSION, true);
    
    wp_localize_script('watchmodmarket-add-to-cart', 'watchmodmarket_ajax', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('watchmodmarket_ajax'),
        'adding_to_cart' => __('Adding...', 'watchmodmarket'),
        'added_to_cart' => __('Added!', 'watchmodmarket')
    ));
}
add_action('wp_enqueue_scripts', 'watchmodmarket_add_to_cart_script');