<?php
/**
 * WooCommerce product customizations
 *
 * @package WatchModMarket
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Add custom product meta fields
 */
function watchmodmarket_add_custom_product_fields() {
    global $woocommerce, $post;
    
    echo '<div class="options_group">';
    
    // Compatible parts field
    woocommerce_wp_text_input(array(
        'id' => '_compatible_parts',
        'label' => __('Compatible Parts (Product IDs)', 'watchmodmarket'),
        'placeholder' => 'e.g., 123,456,789',
        'description' => __('Enter product IDs of compatible parts, separated by commas', 'watchmodmarket'),
        'desc_tip' => true
    ));
    
    // Diameter field for cases and dials
    woocommerce_wp_text_input(array(
        'id' => '_case_diameter',
        'label' => __('Case Diameter (mm)', 'watchmodmarket'),
        'placeholder' => '40',
        'type' => 'number',
        'custom_attributes' => array('step' => '0.1', 'min' => '0')
    ));
    
    woocommerce_wp_text_input(array(
        'id' => '_dial_diameter',
        'label' => __('Dial Diameter (mm)', 'watchmodmarket'),
        'placeholder' => '38',
        'type' => 'number',
        'custom_attributes' => array('step' => '0.1', 'min' => '0')
    ));
    
    // Movement type
    woocommerce_wp_select(array(
        'id' => '_movement_type',
        'label' => __('Movement Type', 'watchmodmarket'),
        'options' => array(
            '' => __('Select...', 'watchmodmarket'),
            'automatic' => __('Automatic', 'watchmodmarket'),
            'mechanical' => __('Mechanical', 'watchmodmarket'),
            'quartz' => __('Quartz', 'watchmodmarket')
        )
    ));
    
    echo '</div>';
}
add_action('woocommerce_product_options_general_product_data', 'watchmodmarket_add_custom_product_fields');

/**
 * Save custom product meta fields
 */
function watchmodmarket_save_custom_product_fields($post_id) {
    $compatible_parts = isset($_POST['_compatible_parts']) ? sanitize_text_field($_POST['_compatible_parts']) : '';
    update_post_meta($post_id, '_compatible_parts', $compatible_parts);
    
    $case_diameter = isset($_POST['_case_diameter']) ? sanitize_text_field($_POST['_case_diameter']) : '';
    update_post_meta($post_id, '_case_diameter', $case_diameter);
    
    $dial_diameter = isset($_POST['_dial_diameter']) ? sanitize_text_field($_POST['_dial_diameter']) : '';
    update_post_meta($post_id, '_dial_diameter', $dial_diameter);
    
    $movement_type = isset($_POST['_movement_type']) ? sanitize_text_field($_POST['_movement_type']) : '';
    update_post_meta($post_id, '_movement_type', $movement_type);
}
add_action('woocommerce_process_product_meta', 'watchmodmarket_save_custom_product_fields');

/**
 * Optimize product images with lazy loading and modern formats
 */
function watchmodmarket_optimize_product_images() {
    // Add lazy loading for product images
    add_filter('woocommerce_product_get_image', function($image) {
        return str_replace('<img', '<img loading="lazy"', $image);
    });
    
    // Add responsive image sizes for product galleries
    add_image_size('product-large', 1200, 1200, false);
    add_image_size('product-medium', 800, 800, false);
    add_image_size('product-small', 400, 400, false);
}
add_action('init', 'watchmodmarket_optimize_product_images');