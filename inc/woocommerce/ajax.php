<?php
/**
 * WooCommerce AJAX handlers
 *
 * @package WatchModMarket
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * AJAX handler for Quick View functionality
 */
function watchmodmarket_quick_view_handler() {
    // Verify nonce for security
    check_ajax_referer('watchmodmarket_quick_view_nonce', 'nonce');
    
    // Get product ID from request
    $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
    
    // Validate product ID
    if ($product_id <= 0) {
        wp_send_json_error(array('message' => __('Invalid product ID', 'watchmodmarket')));
        wp_die();
    }
    
    // Get product object
    $product = wc_get_product($product_id);
    
    // Verify product exists
    if (!$product) {
        wp_send_json_error(array('message' => __('Product not found', 'watchmodmarket')));
        wp_die();
    }
    
    // Get product image
    $image_id = $product->get_image_id();
    $image_url = $image_id ? wp_get_attachment_image_url($image_id, 'medium_large') : wc_placeholder_img_src('medium_large');
    
    // Get product gallery
    $gallery_images = array();
    $gallery_ids = $product->get_gallery_image_ids();
    if (!empty($gallery_ids)) {
        foreach ($gallery_ids as $gallery_id) {
            $gallery_images[] = wp_get_attachment_image_url($gallery_id, 'medium_large');
        }
    }
    
    // Format product description
    $description = $product->get_short_description();
    if (empty($description)) {
        $description = $product->get_description();
        // Truncate description if it's too long
        if (strlen($description) > 300) {
            $description = substr($description, 0, 300) . '... <a href="' . get_permalink($product_id) . '">' . __('Read More', 'watchmodmarket') . '</a>';
        }
    }
    
    // Get product attributes
    $attributes = array();
    $product_attributes = $product->get_attributes();
    if (!empty($product_attributes)) {
        foreach ($product_attributes as $attribute_name => $attribute) {
            $name = wc_attribute_label($attribute_name);
            $values = array();
            
            if ($attribute->is_taxonomy()) {
                $attribute_taxonomy = $attribute->get_taxonomy_object();
                $attribute_values = wc_get_product_terms($product_id, $attribute_name, array('fields' => 'all'));
                
                foreach ($attribute_values as $attribute_value) {
                    $values[] = $attribute_value->name;
                }
            } else {
                $values = $attribute->get_options();
            }
            
            $attributes[$name] = array(
                'name' => $name,
                'options' => $values,
            );
        }
    }
    
    // Build stock status
    $stock_status = '';
    if ($product->is_in_stock()) {
        $stock_status = '<span class="in-stock">' . __('In Stock', 'watchmodmarket') . '</span>';
        if ($product->managing_stock()) {
            $stock_qty = $product->get_stock_quantity();
            if ($stock_qty) {
                $stock_status .= ' (' . $stock_qty . ' ' . __('available', 'watchmodmarket') . ')';
            }
        }
    } else {
        $stock_status = '<span class="out-of-stock">' . __('Out of Stock', 'watchmodmarket') . '</span>';
    }
    
    // Build product data array
    $product_data = array(
        'id' => $product_id,
        'name' => $product->get_name(),
        'price' => $product->get_price_html(),
        'image' => $image_url,
        'gallery' => $gallery_images,
        'description' => $description,
        'attributes' => $attributes,
        'stock_status' => $stock_status,
        'rating' => $product->get_average_rating(),
        'rating_count' => $product->get_rating_count(),
        'add_to_cart_url' => $product->add_to_cart_url(),
    );
    
    // Send success response with product data
    wp_send_json_success($product_data);
    wp_die();
}
add_action('wp_ajax_watchmodmarket_quick_view_handler', 'watchmodmarket_quick_view_handler');
add_action('wp_ajax_nopriv_watchmodmarket_quick_view_handler', 'watchmodmarket_quick_view_handler');

/**
 * AJAX handler for recently viewed products
 */
function watchmodmarket_get_recently_viewed_products() {
    // No nonce check needed as this is a read-only operation
    
    $product_ids = isset($_POST['product_ids']) ? array_map('intval', $_POST['product_ids']) : array();
    
    if (empty($product_ids)) {
        wp_send_json_error('No products to display');
        wp_die();
    }
    
    $args = array(
        'post_type' => 'product',
        'post__in' => $product_ids,
        'posts_per_page' => count($product_ids),
        'orderby' => 'post__in'
    );
    
    $query = new WP_Query($args);
    
    ob_start();
    
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            wc_get_template_part('content', 'product');
        }
        wp_reset_postdata();
    }
    
    $html = ob_get_clean();
    
    if (empty($html)) {
        wp_send_json_error('No products found');
        wp_die();
    }
    
    wp_send_json_success($html);
    wp_die();
}
add_action('wp_ajax_get_recently_viewed_products', 'watchmodmarket_get_recently_viewed_products');
add_action('wp_ajax_nopriv_get_recently_viewed_products', 'watchmodmarket_get_recently_viewed_products');

/**
 * AJAX handler for adding to wishlist
 */
function watchmodmarket_add_to_watchlist() {
    check_ajax_referer('watchmodmarket_watchlist_nonce', 'nonce');
    
    if (!is_user_logged_in()) {
        wp_send_json_error('You must be logged in to save to your watchlist');
        wp_die();
    }
    
    $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
    $user_id = get_current_user_id();
    
    if (!$product_id) {
        wp_send_json_error('Invalid product ID');
        wp_die();
    }
    
    // Get existing watchlist
    $watchlist = get_user_meta($user_id, 'watchmodmarket_watchlist', true);
    if (!is_array($watchlist)) {
        $watchlist = array();
    }
    
    // Toggle product in watchlist
    if (in_array($product_id, $watchlist)) {
        $watchlist = array_diff($watchlist, array($product_id));
        $action = 'removed';
        $message = 'Removed from watchlist';
    } else {
        $watchlist[] = $product_id;
        $action = 'added';
        $message = 'Added to watchlist';
    }
    
    // Update user meta
    update_user_meta($user_id, 'watchmodmarket_watchlist', array_unique($watchlist));
    
    wp_send_json_success(array(
        'action' => $action,
        'message' => $message,
        'count' => count($watchlist)
    ));
    wp_die();
}
add_action('wp_ajax_add_to_watchlist', 'watchmodmarket_add_to_watchlist');
add_action('wp_ajax_nopriv_add_to_watchlist', 'watchmodmarket_add_to_watchlist');

/**
 * AJAX handler for quick view
 */
function watchmodmarket_quick_view_ajax() {
    // Check nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'watchmodmarket_quick_view_nonce')) {
        wp_send_json_error(array('message' => 'Invalid nonce'));
        wp_die();
    }
    
    // Check product ID
    if (!isset($_POST['product_id']) || empty($_POST['product_id'])) {
        wp_send_json_error(array('message' => 'Product ID is required'));
        wp_die();
    }
    
    $product_id = absint($_POST['product_id']);
    $product = wc_get_product($product_id);
    
    if (!$product) {
        wp_send_json_error(array('message' => 'Product not found'));
        wp_die();
    }
    
    // Get product data
    $data = array(
        'id'          => $product_id,
        'title'       => $product->get_name(),
        'price'       => $product->get_price_html(),
        'description' => $product->get_short_description(),
        'url'         => get_permalink($product_id),
        'image'       => get_the_post_thumbnail_url($product_id, 'medium'),
    );
    
    wp_send_json_success($data);
    wp_die();
}
add_action('wp_ajax_watchmodmarket_quick_view', 'watchmodmarket_quick_view_ajax');
add_action('wp_ajax_nopriv_watchmodmarket_quick_view', 'watchmodmarket_quick_view_ajax');

/**
 * AJAX handler for adding to cart
 */
function watchmodmarket_add_to_cart_ajax() {
    // Check nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'watchmodmarket_cart_nonce')) {
        wp_send_json_error(array('message' => 'Invalid nonce'));
        wp_die();
    }
    
    // Check product ID
    if (!isset($_POST['product_id']) || empty($_POST['product_id'])) {
        wp_send_json_error(array('message' => 'Product ID is required'));
        wp_die();
    }
    
    $product_id = absint($_POST['product_id']);
    $quantity = isset($_POST['quantity']) ? absint($_POST['quantity']) : 1;
    
    // Add to cart
    $added = WC()->cart->add_to_cart($product_id, $quantity);
    
    if (!$added) {
        wp_send_json_error(array('message' => 'Error adding product to cart'));
        wp_die();
    }
    
    // Get cart count
    $cart_count = WC()->cart->get_cart_contents_count();
    
    wp_send_json_success(array('cart_count' => $cart_count));
    wp_die();
}
add_action('wp_ajax_watchmodmarket_add_to_cart', 'watchmodmarket_add_to_cart_ajax');
add_action('wp_ajax_nopriv_watchmodmarket_add_to_cart', 'watchmodmarket_add_to_cart_ajax');

/**
 * AJAX handler for adding to wishlist
 */
function watchmodmarket_add_to_wishlist_ajax() {
    // Check nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'watchmodmarket_wishlist_nonce')) {
        wp_send_json_error(array('message' => 'Invalid nonce'));
        wp_die();
    }
    
    // Check if user is logged in
    if (!is_user_logged_in()) {
        wp_send_json_error(array('message' => 'User must be logged in to add to wishlist'));
        wp_die();
    }
    
    // Check product ID
    if (!isset($_POST['product_id']) || empty($_POST['product_id'])) {
        wp_send_json_error(array('message' => 'Product ID is required'));
        wp_die();
    }
    
    $product_id = absint($_POST['product_id']);
    $user_id = get_current_user_id();
    
    // Get current wishlist
    $wishlist = get_user_meta($user_id, 'watchmodmarket_wishlist', true);
    
    if (!is_array($wishlist)) {
        $wishlist = array();
    }
    
    // Add product to wishlist if not already in it
    if (!in_array($product_id, $wishlist)) {
        $wishlist[] = $product_id;
        update_user_meta($user_id, 'watchmodmarket_wishlist', $wishlist);
    }
    
    wp_send_json_success();
    wp_die();
}
add_action('wp_ajax_watchmodmarket_add_to_wishlist', 'watchmodmarket_add_to_wishlist_ajax');

/**
 * AJAX handler for removing from wishlist
 */
function watchmodmarket_remove_from_wishlist_ajax() {
    // Check nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'watchmodmarket_wishlist_nonce')) {
        wp_send_json_error(array('message' => 'Invalid nonce'));
        wp_die();
    }
    
    // Check if user is logged in
    if (!is_user_logged_in()) {
        wp_send_json_error(array('message' => 'User must be logged in to remove from wishlist'));
        wp_die();
    }
    
    // Check product ID
    if (!isset($_POST['product_id']) || empty($_POST['product_id'])) {
        wp_send_json_error(array('message' => 'Product ID is required'));
        wp_die();
    }
    
    $product_id = absint($_POST['product_id']);
    $user_id = get_current_user_id();
    
    // Get current wishlist
    $wishlist = get_user_meta($user_id, 'watchmodmarket_wishlist', true);
    
    if (is_array($wishlist)) {
        // Remove product from wishlist
        $watchlist = array_diff($wishlist, array($product_id));
        update_user_meta($user_id, 'watchmodmarket_wishlist', $wishlist);
    }
    
    wp_send_json_success();
    wp_die();
}
add_action('wp_ajax_watchmodmarket_remove_from_wishlist', 'watchmodmarket_remove_from_wishlist_ajax');

/**
 * AJAX handler for checking wishlist status
 */
function watchmodmarket_check_wishlist_ajax() {
    // Check nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'watchmodmarket_wishlist_nonce')) {
        wp_send_json_error(array('message' => 'Invalid nonce'));
        wp_die();
    }
    
    // Check if user is logged in
    if (!is_user_logged_in()) {
        wp_send_json_error(array('message' => 'User must be logged in to check wishlist'));
        wp_die();
    }
    
    // Check product IDs
    if (!isset($_POST['product_ids']) || empty($_POST['product_ids'])) {
        wp_send_json_error(array('message' => 'Product IDs are required'));
        wp_die();
    }
    
    $product_ids = $_POST['product_ids'];
    $user_id = get_current_user_id();
    
    // Get current wishlist
    $wishlist = get_user_meta($user_id, 'watchmodmarket_wishlist', true);
    
    if (!is_array($wishlist)) {
        $wishlist = array();
    }
    
    // Check which products are in wishlist
    $in_wishlist = array();
    
    foreach ($product_ids as $product_id) {
        if (in_array($product_id, $wishlist)) {
            $in_wishlist[] = $product_id;
        }
    }
    
    wp_send_json_success(array('products' => $in_wishlist));
    wp_die();
}
add_action('wp_ajax_watchmodmarket_check_wishlist', 'watchmodmarket_check_wishlist_ajax');

/**
 * AJAX handler for adding group buy products to cart
 */
function watchmodmarket_add_group_buy_to_cart_ajax() {
    // Check nonce for security
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'watchmodmarket_group_buy_cart_nonce')) {
        wp_send_json_error(array('message' => __('Security check failed', 'watchmodmarket')));
        wp_die();
    }
    
    // Get product ID from request
    $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
    
    // Validate product ID
    if ($product_id <= 0) {
        wp_send_json_error(array('message' => __('Invalid product ID', 'watchmodmarket')));
        wp_die();
    }
    
    // Add product to cart
    $cart_item_key = WC()->cart->add_to_cart($product_id, $quantity);
    
    if (!$cart_item_key) {
        wp_send_json_error(array('message' => __('Failed to add product to cart', 'watchmodmarket')));
        wp_die();
    }
    
    wp_send_json_success(array(
        'message' => __('Product added to cart successfully', 'watchmodmarket'),
        'cart_count' => WC()->cart->get_cart_contents_count(),
        'cart_url' => wc_get_cart_url()
    ));
    wp_die();
}
add_action('wp_ajax_watchmodmarket_add_group_buy_to_cart', 'watchmodmarket_add_group_buy_to_cart_ajax');
add_action('wp_ajax_nopriv_watchmodmarket_add_group_buy_to_cart', 'watchmodmarket_add_group_buy_to_cart_ajax');