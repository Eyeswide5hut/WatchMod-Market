<?php
/**
 * AJAX handlers for cart functionality
 */

// Don't allow direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add to cart AJAX handler
 */
function watchmodmarket_add_to_cart_ajax() {
    check_ajax_referer('watchmodmarket_nonce', 'nonce');
    
    $product_id = isset($_POST['product_id']) ? absint($_POST['product_id']) : 0;
    $quantity = isset($_POST['quantity']) ? absint($_POST['quantity']) : 1;
    
    if (!$product_id) {
        wp_send_json_error(array('message' => __('Invalid product ID', 'watchmodmarket')));
    }
    
    // Add to cart
    $cart_item_key = WC()->cart->add_to_cart($product_id, $quantity);
    
    if (!$cart_item_key) {
        wp_send_json_error(array('message' => __('Failed to add product to cart', 'watchmodmarket')));
    }
    
    wp_send_json_success(array(
        'message' => __('Product added to cart', 'watchmodmarket'),
        'cart_count' => WC()->cart->get_cart_contents_count()
    ));
}
add_action('wp_ajax_watchmodmarket_add_to_cart', 'watchmodmarket_add_to_cart_ajax');
add_action('wp_ajax_nopriv_watchmodmarket_add_to_cart', 'watchmodmarket_add_to_cart_ajax');

/**
 * Quick view AJAX handler
 */
function watchmodmarket_quick_view_ajax() {
    check_ajax_referer('watchmodmarket_nonce', 'nonce');
    
    $product_id = isset($_POST['product_id']) ? absint($_POST['product_id']) : 0;
    
    if (!$product_id) {
        wp_send_json_error(array('message' => __('Invalid product ID', 'watchmodmarket')));
    }
    
    $product = wc_get_product($product_id);
    
    if (!$product) {
        wp_send_json_error(array('message' => __('Product not found', 'watchmodmarket')));
    }
    
    $data = array(
        'id' => $product_id,
        'title' => $product->get_name(),
        'price' => $product->get_price_html(),
        'description' => $product->get_short_description(),
        'url' => get_permalink($product_id),
        'image' => wp_get_attachment_image_url(get_post_thumbnail_id($product_id), 'medium'),
    );
    
    wp_send_json_success($data);
}
add_action('wp_ajax_watchmodmarket_quick_view', 'watchmodmarket_quick_view_ajax');
add_action('wp_ajax_nopriv_watchmodmarket_quick_view', 'watchmodmarket_quick_view_ajax');