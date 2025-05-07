<?php
/**
 * Theme setup functionality
 *
 * @package WatchModMarket
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Set up theme defaults and register support for various WordPress features.
 */
function watchmodmarket_setup() {
    // Add default posts and comments RSS feed links to head
    add_theme_support('automatic-feed-links');

    // Let WordPress manage the document title
    add_theme_support('title-tag');

    // Enable support for Post Thumbnails on posts and pages
    add_theme_support('post-thumbnails');

    // Register navigation menus
    register_nav_menus(
        array(
            'primary' => esc_html__('Primary Menu', 'watchmodmarket'),
            'footer'  => esc_html__('Footer Menu', 'watchmodmarket'),
        )
    );

    // Switch default core markup to output valid HTML5
    add_theme_support(
        'html5',
        array(
            'search-form',
            'comment-form',
            'comment-list',
            'gallery',
            'caption',
            'style',
            'script',
        )
    );

    // WooCommerce support
    add_theme_support('woocommerce');
    add_theme_support('wc-product-gallery-zoom');
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('wc-product-gallery-slider');
    
    // Add support for block styles
    add_theme_support('wp-block-styles');
    
    // Add support for responsive embeds
    add_theme_support('responsive-embeds');
    
    // Add support for wide and full width blocks
    add_theme_support('align-wide');
    
    // Add support for editor styles
    add_editor_style('assets/css/editor-style.css');
    
    // Add custom logo support
    add_theme_support(
        'custom-logo',
        array(
            'height'      => 100,
            'width'       => 300,
            'flex-width'  => true,
            'flex-height' => true,
        )
    );
}
add_action('after_setup_theme', 'watchmodmarket_setup');

/**
 * Implement custom logo with fallback
 */
function watchmodmarket_custom_logo() {
    if (function_exists('the_custom_logo') && has_custom_logo()) {
        the_custom_logo();
    } else {
        echo '<a href="' . esc_url(home_url('/')) . '" class="logo" rel="home">' . 
             esc_html(get_bloginfo('name')) . '</a>';
    }
}

/**
 * Menu fallback function 
 */
function watchmodmarket_menu_fallback() {
    if (is_user_logged_in() && current_user_can('edit_theme_options')) {
        echo '<ul class="main-menu"><li><a href="' . esc_url(admin_url('nav-menus.php')) . '">' . 
             esc_html__('Create a menu', 'watchmodmarket') . '</a></li></ul>';
    } else {
        echo '<ul class="main-menu"><li><a href="' . esc_url(home_url()) . '">' . 
             esc_html__('Home', 'watchmodmarket') . '</a></li></ul>';
    }
}

/**
 * Create pages on theme activation
 */
function watchmodmarket_create_pages() {
    $pages = array(
        'builder' => array(
            'title' => 'Watch Builder',
            'content' => '<!-- wp:paragraph --><p>Create your perfect timepiece by selecting and combining different watch components. Our interactive tool lets you visualize your design in real-time.</p><!-- /wp:paragraph -->',
            'template' => 'page-watch-builder.php'
        ),
        'group-buy' => array(
            'title' => 'Timepiece Futures',
            'content' => '<!-- wp:paragraph --><p>Exclusive Group Buys & Pre-Orders for Premium Watch Parts</p><!-- /wp:paragraph -->',
            'template' => 'page-group-buy.php'
        )
    );

    foreach ($pages as $slug => $page) {
        $existing_page = get_page_by_path($slug);
        
        // Create the page if it doesn't exist
        if (!$existing_page) {
            $page_id = wp_insert_post(array(
                'post_title' => $page['title'],
                'post_name' => $slug,
                'post_content' => $page['content'],
                'post_status' => 'publish',
                'post_type' => 'page'
            ));
            
            // Set page template if specified
            if (!empty($page['template'])) {
                update_post_meta($page_id, '_wp_page_template', $page['template']);
            }
        }
    }
}
add_action('after_switch_theme', 'watchmodmarket_create_pages');