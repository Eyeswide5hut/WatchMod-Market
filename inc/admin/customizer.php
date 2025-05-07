<?php
/**
 * Theme Customizer settings
 *
 * @package WatchModMarket
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Add image optimization settings to customizer
 */
function watchmodmarket_add_image_settings($wp_customize) {
    $wp_customize->add_section('watchmodmarket_image_optimization', array(
        'title' => __('Image Optimization', 'watchmodmarket'),
        'priority' => 50,
    ));
    
    // Enable WebP conversion
    $wp_customize->add_setting('enable_webp_conversion', array(
        'default' => true,
        'type' => 'theme_mod',
        'capability' => 'edit_theme_options',
    ));
    
    $wp_customize->add_control('enable_webp_conversion', array(
        'label' => __('Enable WebP Conversion', 'watchmodmarket'),
        'description' => __('Automatically convert JPEG and PNG images to WebP format for better performance.', 'watchmodmarket'),
        'section' => 'watchmodmarket_image_optimization',
        'type' => 'checkbox',
    ));
    
    // Image quality setting
    $wp_customize->add_setting('image_quality', array(
        'default' => 85,
        'type' => 'theme_mod',
        'capability' => 'edit_theme_options',
    ));
    
    $wp_customize->add_control('image_quality', array(
        'label' => __('Image Quality', 'watchmodmarket'),
        'description' => __('Set the quality for compressed images (1-100).', 'watchmodmarket'),
        'section' => 'watchmodmarket_image_optimization',
        'type' => 'range',
        'input_attrs' => array(
            'min' => 1,
            'max' => 100,
            'step' => 1,
        ),
    ));
}
add_action('customize_register', 'watchmodmarket_add_image_settings');