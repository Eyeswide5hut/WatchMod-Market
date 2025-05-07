<?php
/**
 * WooCommerce cart customizations
 *
 * @package WatchModMarket
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Add watch build to cart
 */
function watchmodmarket_add_watch_build_to_cart() {
    if (!isset($_POST['add-build-to-cart']) || empty($_POST['selected_parts'])) {
        return;
    }
    
    $selected_parts = $_POST['selected_parts'];
    
    // Add each selected part to the cart
    foreach ($selected_parts as $part_type => $part_id) {
        if (!empty($part_id)) {
            $product_id = intval(str_replace($part_type . '-', '', $part_id));
            WC()->cart->add_to_cart($product_id, 1);
        }
    }
    
    // Redirect to cart page
    wp_safe_redirect(wc_get_cart_url());
    exit;
}
add_action('template_redirect', 'watchmodmarket_add_watch_build_to_cart');

/**
 * Add custom cart item data for watch builds
 */
function watchmodmarket_add_cart_item_data($cart_item_data, $product_id, $variation_id) {
    // Check if this is part of a watch build
    if (isset($_POST['build_id']) && !empty($_POST['build_id'])) {
        $cart_item_data['build_id'] = intval($_POST['build_id']);
    }
    
    return $cart_item_data;
}
add_filter('woocommerce_add_cart_item_data', 'watchmodmarket_add_cart_item_data', 10, 3);

/**
 * Display build info in cart
 */
function watchmodmarket_display_build_info_in_cart($item_data, $cart_item) {
    if (isset($cart_item['build_id'])) {
        $build_id = $cart_item['build_id'];
        $build = get_post($build_id);
        
        if ($build) {
            $item_data[] = array(
                'key' => __('Watch Build', 'watchmodmarket'),
                'value' => $build->post_title,
                'display' => '<a href="' . get_permalink($build_id) . '">' . $build->post_title . '</a>'
            );
        }
    }
    
    return $item_data;
}
add_filter('woocommerce_get_item_data', 'watchmodmarket_display_build_info_in_cart', 10, 2);

/**
 * Modify cart item price based on bundle or discounts
 */
function watchmodmarket_cart_item_price($price, $cart_item, $cart_item_key) {
    // Check if this is a group buy item
    if (isset($cart_item['groupbuy_price']) && !empty($cart_item['groupbuy_price'])) {
        $groupbuy_price = $cart_item['groupbuy_price'];
        $price = wc_price($groupbuy_price);
    }
    
    return $price;
}
add_filter('woocommerce_cart_item_price', 'watchmodmarket_cart_item_price', 10, 3);

/**
 * Add free shipping for orders over a certain amount
 */
function watchmodmarket_free_shipping_min_amount($rates, $package) {
    $min_amount = 100; // Set your minimum amount
    $cart_total = WC()->cart->subtotal;
    
    if ($cart_total >= $min_amount) {
        foreach ($rates as $rate_id => $rate) {
            if ('flat_rate' === $rate->method_id) {
                unset($rates[$rate_id]);
            }
        }
        
        // Add free shipping if it doesn't exist
        if (!isset($rates['free_shipping'])) {
            $free_shipping = new WC_Shipping_Rate(
                'free_shipping',
                __('Free Shipping', 'watchmodmarket'),
                0,
                array(),
                'free_shipping'
            );
            
            $rates['free_shipping'] = $free_shipping;
        }
    }
    
    return $rates;
}
add_filter('woocommerce_package_rates', 'watchmodmarket_free_shipping_min_amount', 10, 2);

/**
 * Add cross-sells based on compatible parts
 */
function watchmodmarket_custom_cross_sells($cross_sells) {
    $new_cross_sells = array();
    
    foreach (WC()->cart->get_cart() as $cart_item) {
        $product_id = $cart_item['product_id'];
        $compatible_parts = get_post_meta($product_id, '_compatible_parts', true);
        
        if (!empty($compatible_parts)) {
            $compatible_parts_array = explode(',', $compatible_parts);
            $new_cross_sells = array_merge($new_cross_sells, $compatible_parts_array);
        }
    }
    
    if (!empty($new_cross_sells)) {
        $cross_sells = array_unique(array_merge($cross_sells, $new_cross_sells));
    }
    
    return $cross_sells;
}
add_filter('woocommerce_cross_sells_products', 'watchmodmarket_custom_cross_sells');