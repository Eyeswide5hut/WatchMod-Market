<?php
/**
 * WooCommerce setup and configuration
 *
 * @package WatchModMarket
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Create custom product attributes on theme activation
 */
function watchmodmarket_create_product_attributes() {
    if (!class_exists('WooCommerce')) {
        return;
    }
    
    $attributes = array(
        'pa_part-type' => array(
            'name' => 'Part Type',
            'slug' => 'pa_part-type',
            'type' => 'select',
            'has_archives' => true,
            'terms' => array('Dial', 'Hands', 'Crown', 'Case', 'Strap', 'Movement', 'Crystal', 'Bezel')
        ),
        'pa_case-size' => array(
            'name' => 'Case Size',
            'slug' => 'pa_case-size',
            'type' => 'select',
            'has_archives' => true,
            'terms' => array('35mm', '36-38mm', '39-42mm', '43-45mm', '46mm+')
        ),
        'pa_compatible-with' => array(
            'name' => 'Compatible With',
            'slug' => 'pa_compatible-with',
            'type' => 'select',
            'has_archives' => true,
            'terms' => array('Seiko NH', 'ETA 2824', 'Miyota 9015', 'Rolex', 'Omega', 'Tudor', 'Generic')
        ),
        'pa_movement-type' => array(
            'name' => 'Movement Type',
            'slug' => 'pa_movement-type',
            'type' => 'select',
            'has_archives' => true,
            'terms' => array('Automatic', 'Manual', 'Quartz')
        )
    );
    
    foreach ($attributes as $slug => $attr) {
        // Check if attribute exists
        if (!taxonomy_exists($slug)) {
            $args = array(
                'hierarchical' => false,
                'label' => $attr['name'],
                'query_var' => true,
                'rewrite' => array('slug' => $slug),
            );
            register_taxonomy($slug, 'product', $args);
            
            // Add the attribute to WooCommerce
            $attribute_id = wc_create_attribute(array(
                'name' => $attr['name'],
                'slug' => str_replace('pa_', '', $slug),
                'type' => $attr['type'],
                'order_by' => 'name',
                'has_archives' => $attr['has_archives'],
            ));
            
            // Add terms if they don't exist
            if (!is_wp_error($attribute_id) && !empty($attr['terms'])) {
                foreach ($attr['terms'] as $term_name) {
                    if (!term_exists($term_name, $slug)) {
                        wp_insert_term($term_name, $slug);
                    }
                }
            }
        }
    }
}
add_action('init', 'watchmodmarket_create_product_attributes');

/**
 * Enhanced Shop enqueue
 */
function watchmodmarket_enqueue_shop_assets() {
    if (is_shop() || is_product_category() || is_product_tag() || is_product()) {
        // Enqueue enhanced shop CSS
        wp_enqueue_style(
            'watchmodmarket-enhanced-shop',
            WATCHMODMARKET_URI . '/assets/css/shop.css',
            array('watchmodmarket-main'),
            WATCHMODMARKET_VERSION
        );
        
        // Enqueue enhanced shop JavaScript
        wp_enqueue_script(
            'watchmodmarket-enhanced-shop',
            WATCHMODMARKET_URI . '/assets/js/shop.js',
            array('jquery'),
            WATCHMODMARKET_VERSION,
            true
        );
        
        // Localize script
        wp_localize_script(
            'watchmodmarket-enhanced-shop',
            'watchmodmarket_shop',
            array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('watchmodmarket_shop_nonce'),
                'quick_view_title' => __('Quick View', 'watchmodmarket'),
                'add_to_cart' => __('Add to Cart', 'watchmodmarket'),
                'added_to_cart' => __('Added!', 'watchmodmarket'),
                'adding_to_cart' => __('Adding...', 'watchmodmarket'),
            )
        );
    }
}
add_action('wp_enqueue_scripts', 'watchmodmarket_enqueue_shop_assets');

/**
 * Enqueue single product specific scripts
 */
function watchmodmarket_enqueue_single_product_scripts() {
    if (is_product()) {
        wp_enqueue_script(
            'watchmodmarket-single-product',
            get_template_directory_uri() . '/assets/js/single-product.js',
            array('jquery'),
            WATCHMODMARKET_VERSION,
            true
        );
        
        wp_localize_script(
            'watchmodmarket-single-product',
            'watchmodmarket_product',
            array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('watchmodmarket_single_product_nonce'),
                'i18n' => array(
                    'add_to_watchlist' => __('Add to Watchlist', 'watchmodmarket'),
                    'remove_from_watchlist' => __('Remove from Watchlist', 'watchmodmarket'),
                    'added_to_cart' => __('Added to Cart!', 'watchmodmarket'),
                    'adding_to_cart' => __('Adding...', 'watchmodmarket'),
                    'share' => __('Share', 'watchmodmarket'),
                    'link_copied' => __('Link Copied!', 'watchmodmarket')
                )
            )
        );
    }
}
add_action('wp_enqueue_scripts', 'watchmodmarket_enqueue_single_product_scripts');