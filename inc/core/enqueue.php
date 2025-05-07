<?php
/**
 * Enqueue scripts and styles
 *
 * @package WatchModMarket
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Enqueue header and footer assets
 */
function watchmodmarket_enqueue_header_footer_assets() {
    // CSS files
    wp_enqueue_style(
        'watchmodmarket-header-styles',
        get_template_directory_uri() . '/assets/css/header-styles.css',
        array(),
        WATCHMODMARKET_VERSION
    );
    
    wp_enqueue_style(
        'watchmodmarket-footer-styles',
        get_template_directory_uri() . '/assets/css/footer-styles.css',
        array(),
        WATCHMODMARKET_VERSION
    );
    
    // JavaScript file
    wp_enqueue_script(
        'watchmodmarket-header-footer',
        get_template_directory_uri() . '/assets/js/header-footer.js',
        array('jquery'),
        WATCHMODMARKET_VERSION,
        true
    );
}
add_action('wp_enqueue_scripts', 'watchmodmarket_enqueue_header_footer_assets');

/**
 * Enqueue scripts and styles.
 */
function watchmodmarket_scripts() {
    // Main stylesheet (required by WordPress)
    wp_enqueue_style(
        'watchmodmarket-style',
        get_stylesheet_uri(),
        array(),
        WATCHMODMARKET_VERSION
    );
    
    // Core CSS files
    wp_enqueue_style(
        'watchmodmarket-main',
        WATCHMODMARKET_URI . '/assets/css/main.css',
        array(),
        WATCHMODMARKET_VERSION
    );
    
    // Load components if exists
    if (file_exists(WATCHMODMARKET_DIR . '/assets/css/components.css')) {
        wp_enqueue_style(
            'watchmodmarket-components',
            WATCHMODMARKET_URI . '/assets/css/components.css',
            array('watchmodmarket-main'),
            WATCHMODMARKET_VERSION
        );
    }
    
    // Navigation styles
    if (file_exists(WATCHMODMARKET_DIR . '/assets/css/navigation.css')) {
        wp_enqueue_style(
            'watchmodmarket-navigation',
            WATCHMODMARKET_URI . '/assets/css/navigation.css',
            array('watchmodmarket-main'),
            WATCHMODMARKET_VERSION
        );
    }
    
    // Blog styles
    if ((is_singular('post') || is_home() || is_archive() || is_search()) && 
        file_exists(WATCHMODMARKET_DIR . '/assets/css/blog.css')) {
        wp_enqueue_style(
            'watchmodmarket-blog',
            WATCHMODMARKET_URI . '/assets/css/blog.css',
            array('watchmodmarket-main'),
            WATCHMODMARKET_VERSION
        );
    }
    
    // Conditionally load page-specific CSS
    if (is_front_page()) {
        // Try new structure first, then fall back to old
        if (file_exists(WATCHMODMARKET_DIR . '/assets/css/pages/home.css')) {
            wp_enqueue_style(
                'watchmodmarket-home',
                WATCHMODMARKET_URI . '/assets/css/pages/home.css',
                array('watchmodmarket-main'),
                WATCHMODMARKET_VERSION
            );
        } elseif (file_exists(WATCHMODMARKET_DIR . '/assets/css/index.css')) {
            wp_enqueue_style(
                'watchmodmarket-home',
                WATCHMODMARKET_URI . '/assets/css/index.css',
                array('watchmodmarket-main'),
                WATCHMODMARKET_VERSION
            );
        }
    } elseif (is_page('shop') || is_shop() || is_product() || is_product_category()) {
        if (file_exists(WATCHMODMARKET_DIR . '/assets/css/shop.css')) {
            wp_enqueue_style(
                'watchmodmarket-shop',
                WATCHMODMARKET_URI . '/assets/css/shop.css',
                array('watchmodmarket-main'),
                WATCHMODMARKET_VERSION
            );
        }
    } elseif (is_page('builder')) {
        if (file_exists(WATCHMODMARKET_DIR . '/assets/css/builder.css')) {
            wp_enqueue_style(
                'watchmodmarket-builder',
                WATCHMODMARKET_URI . '/assets/css/builder.css',
                array('watchmodmarket-main'),
                WATCHMODMARKET_VERSION
            );
        }
    } elseif (is_page(array('faq', 'privacy-policy', 'terms-of-service'))) {
        // Try new structure first, then fall back to old
        if (file_exists(WATCHMODMARKET_DIR . '/assets/css/pages/legal.css')) {
            wp_enqueue_style(
                'watchmodmarket-legal',
                WATCHMODMARKET_URI . '/assets/css/pages/legal.css',
                array('watchmodmarket-main'),
                WATCHMODMARKET_VERSION
            );
        } elseif (file_exists(WATCHMODMARKET_DIR . '/assets/css/faq-privacy-terms.css')) {
            wp_enqueue_style(
                'watchmodmarket-legal',
                WATCHMODMARKET_URI . '/assets/css/faq-privacy-terms.css',
                array('watchmodmarket-main'),
                WATCHMODMARKET_VERSION
            );
        }
    } elseif (is_page('contact')) {
        // Try new structure first, then fall back to old
        if (file_exists(WATCHMODMARKET_DIR . '/assets/css/pages/contact.css')) {
            wp_enqueue_style(
                'watchmodmarket-contact',
                WATCHMODMARKET_URI . '/assets/css/pages/contact.css',
                array('watchmodmarket-main'),
                WATCHMODMARKET_VERSION
            );
        } elseif (file_exists(WATCHMODMARKET_DIR . '/assets/css/contact.css')) {
            wp_enqueue_style(
                'watchmodmarket-contact',
                WATCHMODMARKET_URI . '/assets/css/contact.css',
                array('watchmodmarket-main'),
                WATCHMODMARKET_VERSION
            );
        }
    }
    
    // Font Awesome (already linked in header, but better to enqueue)
    wp_enqueue_style(
        'font-awesome',
        'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css',
        array(),
        '6.4.0'
    );
    
    // Main JavaScript
    if (file_exists(WATCHMODMARKET_DIR . '/assets/js/main.js')) {
        wp_enqueue_script(
            'watchmodmarket-main',
            WATCHMODMARKET_URI . '/assets/js/main.js',
            array('jquery'),
            WATCHMODMARKET_VERSION,
            true
        );
        
        // Localize script for AJAX functionality
        wp_localize_script(
            'watchmodmarket-main',
            'watchmodmarket_main_ajax',
            array(
                'ajax_url'        => admin_url('admin-ajax.php'),
                'nonce'           => wp_create_nonce('watchmodmarket_main_nonce'),
                'adding_to_cart'  => __('Adding...', 'watchmodmarket'),
                'added_to_cart'   => __('Added!', 'watchmodmarket')
            )
        );
    }
    
    // Page-specific JS
    if (is_page('builder') && file_exists(WATCHMODMARKET_DIR . '/assets/js/watch-builder.js')) {
        wp_enqueue_script(
            'watchmodmarket-builder',
            WATCHMODMARKET_URI . '/assets/js/watch-builder.js',
            array('jquery'),
            WATCHMODMARKET_VERSION,
            true
        );
    } elseif ((is_page('shop') || is_shop()) && file_exists(WATCHMODMARKET_DIR . '/assets/js/shop.js')) {
        wp_enqueue_script(
            'watchmodmarket-shop',
            WATCHMODMARKET_URI . '/assets/js/shop.js',
            array('jquery'),
            WATCHMODMARKET_VERSION,
            true
        );
    } elseif (is_page('faq') && file_exists(WATCHMODMARKET_DIR . '/assets/js/faq.js')) {
        wp_enqueue_script(
            'watchmodmarket-faq',
            WATCHMODMARKET_URI . '/assets/js/faq.js',
            array('jquery'),
            WATCHMODMARKET_VERSION,
            true
        );
    }
    
    // Load comment-reply.js if needed
    if (is_singular() && comments_open() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }
}
add_action('wp_enqueue_scripts', 'watchmodmarket_scripts');

/**
 * Enqueue AJAX handler script
 */
function watchmodmarket_enqueue_ajax_handler() {
    wp_enqueue_script(
        'watchmodmarket-ajax-handler',
        get_template_directory_uri() . '/assets/js/ajax-handler.js',
        array('jquery'),
        WATCHMODMARKET_VERSION,
        true
    );
}
add_action('wp_enqueue_scripts', 'watchmodmarket_enqueue_ajax_handler');