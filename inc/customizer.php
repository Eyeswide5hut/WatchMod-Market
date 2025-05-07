<?php
/**
 * WatchModMarket Customizer Options
 */

function watchmodmarket_customize_register($wp_customize) {
    
    // Add Promo Banner Section
    $wp_customize->add_section('watchmodmarket_promo_banner', array(
        'title'    => __('Promo Banner', 'watchmodmarket'),
        'priority' => 30,
    ));
    
    // Promo Banner Text Setting
    $wp_customize->add_setting('promo_banner_text', array(
        'default'           => 'Free shipping on all orders over $100',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('promo_banner_text', array(
        'label'    => __('Promo Banner Text', 'watchmodmarket'),
        'section'  => 'watchmodmarket_promo_banner',
        'settings' => 'promo_banner_text',
        'type'     => 'text',
    ));
    
    // Promo Banner Link Setting
    $wp_customize->add_setting('promo_banner_link', array(
        'default'           => '#',
        'sanitize_callback' => 'esc_url_raw',
    ));
    
    $wp_customize->add_control('promo_banner_link', array(
        'label'    => __('Promo Banner Link', 'watchmodmarket'),
        'section'  => 'watchmodmarket_promo_banner',
        'settings' => 'promo_banner_link',
        'type'     => 'url',
    ));
    
    // Hero Section
    $wp_customize->add_section('watchmodmarket_hero', array(
        'title'    => __('Hero Section', 'watchmodmarket'),
        'priority' => 31,
    ));
    
    // Hero Title Setting
    $wp_customize->add_setting('hero_title', array(
        'default'           => 'Craft Your Signature Timepiece',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('hero_title', array(
        'label'    => __('Hero Title', 'watchmodmarket'),
        'section'  => 'watchmodmarket_hero',
        'settings' => 'hero_title',
        'type'     => 'text',
    ));
    
    // Hero Tagline Setting
    $wp_customize->add_setting('hero_tagline', array(
        'default'           => 'Premium Parts. Massive Savings. Unlimited Customization.',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('hero_tagline', array(
        'label'    => __('Hero Tagline', 'watchmodmarket'),
        'section'  => 'watchmodmarket_hero',
        'settings' => 'hero_tagline',
        'type'     => 'text',
    ));
    
    // CTA Primary Button Text
    $wp_customize->add_setting('cta_primary_text', array(
        'default'           => 'Shop Now',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('cta_primary_text', array(
        'label'    => __('Primary CTA Text', 'watchmodmarket'),
        'section'  => 'watchmodmarket_hero',
        'settings' => 'cta_primary_text',
        'type'     => 'text',
    ));
    
    // CTA Primary Button Link
    $wp_customize->add_setting('cta_primary_link', array(
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ));
    
    $wp_customize->add_control('cta_primary_link', array(
        'label'    => __('Primary CTA Link', 'watchmodmarket'),
        'section'  => 'watchmodmarket_hero',
        'settings' => 'cta_primary_link',
        'type'     => 'url',
    ));
    
    // CTA Secondary Button Text
    $wp_customize->add_setting('cta_secondary_text', array(
        'default'           => 'Custom Build',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('cta_secondary_text', array(
        'label'    => __('Secondary CTA Text', 'watchmodmarket'),
        'section'  => 'watchmodmarket_hero',
        'settings' => 'cta_secondary_text',
        'type'     => 'text',
    ));
    
    // CTA Secondary Button Link
    $wp_customize->add_setting('cta_secondary_link', array(
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ));
    
    $wp_customize->add_control('cta_secondary_link', array(
        'label'    => __('Secondary CTA Link', 'watchmodmarket'),
        'section'  => 'watchmodmarket_hero',
        'settings' => 'cta_secondary_link',
        'type'     => 'url',
    ));
    
    // Contact Information
    $wp_customize->add_section('watchmodmarket_contact', array(
        'title'    => __('Contact Information', 'watchmodmarket'),
        'priority' => 32,
    ));
    
    // Contact Email
    $wp_customize->add_setting('contact_email', array(
        'default'           => 'support@watchmodmarket.com',
        'sanitize_callback' => 'sanitize_email',
    ));
    
    $wp_customize->add_control('contact_email', array(
        'label'    => __('Contact Email', 'watchmodmarket'),
        'section'  => 'watchmodmarket_contact',
        'settings' => 'contact_email',
        'type'     => 'email',
    ));
    
    // Contact Phone
    $wp_customize->add_setting('contact_phone', array(
        'default'           => '+1 (800) 555-1234',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('contact_phone', array(
        'label'    => __('Contact Phone', 'watchmodmarket'),
        'section'  => 'watchmodmarket_contact',
        'settings' => 'contact_phone',
        'type'     => 'text',
    ));
    
    // Contact Address
    $wp_customize->add_setting('contact_address', array(
        'default'           => '123 Watch Street, Suite 456, New York, NY 10001, USA',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('contact_address', array(
        'label'    => __('Contact Address', 'watchmodmarket'),
        'section'  => 'watchmodmarket_contact',
        'settings' => 'contact_address',
        'type'     => 'text',
    ));
    
    // Contact Hours
    $wp_customize->add_setting('contact_hours', array(
        'default'           => 'Monday-Friday: 9am-6pm EST',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('contact_hours', array(
        'label'    => __('Contact Hours', 'watchmodmarket'),
        'section'  => 'watchmodmarket_contact',
        'settings' => 'contact_hours',
        'type'     => 'text',
    ));
    
    // Social Media
    $wp_customize->add_section('watchmodmarket_social', array(
        'title'    => __('Social Media', 'watchmodmarket'),
        'priority' => 33,
    ));
    
    // Social Media Links
    $social_networks = array(
        'facebook'  => __('Facebook', 'watchmodmarket'),
        'instagram' => __('Instagram', 'watchmodmarket'),
        'twitter'   => __('Twitter', 'watchmodmarket'),
        'youtube'   => __('YouTube', 'watchmodmarket'),
    );
    
    foreach ($social_networks as $network => $label) {
        $wp_customize->add_setting('social_' . $network, array(
            'default'           => 'https://' . $network . '.com/watchmodmarket',
            'sanitize_callback' => 'esc_url_raw',
        ));
        
        $wp_customize->add_control('social_' . $network, array(
            'label'    => $label . ' URL',
            'section'  => 'watchmodmarket_social',
            'settings' => 'social_' . $network,
            'type'     => 'url',
        ));
    }
    
    // Instagram Handle
    $wp_customize->add_setting('instagram_handle', array(
        'default'           => '@watchmodmarket',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('instagram_handle', array(
        'label'    => __('Instagram Handle', 'watchmodmarket'),
        'section'  => 'watchmodmarket_social',
        'settings' => 'instagram_handle',
        'type'     => 'text',
    ));
}
add_action('customize_register', 'watchmodmarket_customize_register');