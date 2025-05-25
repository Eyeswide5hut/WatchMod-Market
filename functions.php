<?php
/**
 * WatchModMarket functions and definitions
 *
 * @package WatchModMarket
 * @version 1.0.0
 */

// Define constants
define('WATCHMODMARKET_VERSION', '1.0.0');
define('WATCHMODMARKET_DIR', get_template_directory());
define('WATCHMODMARKET_URI', get_template_directory_uri());

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
    
    // Load header-footer if exists
    if (file_exists(WATCHMODMARKET_DIR . '/assets/css/header-footer.css')) {
        wp_enqueue_style(
            'watchmodmarket-header-footer',
            WATCHMODMARKET_URI . '/assets/css/header-footer.css',
            array('watchmodmarket-main'),
            WATCHMODMARKET_VERSION
        );
    }
    
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
    if (is_front_page() && file_exists(WATCHMODMARKET_DIR . '/assets/css/main.css')) {
        wp_enqueue_style(
            'watchmodmarket-home',
            WATCHMODMARKET_URI . '/assets/css/main.css',
            array('watchmodmarket-main'),
            WATCHMODMARKET_VERSION
        );
    } elseif ((is_page('shop') || is_shop() || is_product() || is_product_category()) && 
              file_exists(WATCHMODMARKET_DIR . '/assets/css/shop.css')) {
        wp_enqueue_style(
            'watchmodmarket-shop',
            WATCHMODMARKET_URI . '/assets/css/shop.css',
            array('watchmodmarket-main'),
            WATCHMODMARKET_VERSION
        );
    } elseif (is_page('builder') && file_exists(WATCHMODMARKET_DIR . '/assets/css/watch-builder.css')) {
        // Watch Builder specific styles
        wp_enqueue_style(
            'watchmodmarket-builder',
            WATCHMODMARKET_URI . '/assets/css/watch-builder.css',
            array('watchmodmarket-main'),
            WATCHMODMARKET_VERSION
        );
    } elseif (is_page(array('faq', 'privacy-policy', 'terms-of-service')) && 
              file_exists(WATCHMODMARKET_DIR . '/assets/css/components.css')) {
        wp_enqueue_style(
            'watchmodmarket-legal',
            WATCHMODMARKET_URI . '/assets/css/components.css',
            array('watchmodmarket-main'),
            WATCHMODMARKET_VERSION
        );
    } elseif (is_page('contact') && file_exists(WATCHMODMARKET_DIR . '/assets/css/components.css')) {
        wp_enqueue_style(
            'watchmodmarket-contact',
            WATCHMODMARKET_URI . '/assets/css/components.css',
            array('watchmodmarket-main'),
            WATCHMODMARKET_VERSION
        );
    }
    
    // Font Awesome
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
    }
    
    // Header Footer JavaScript
    if (file_exists(WATCHMODMARKET_DIR . '/assets/js/header-footer.js')) {
        wp_enqueue_script(
            'watchmodmarket-header-footer',
            WATCHMODMARKET_URI . '/assets/js/header-footer.js',
            array('jquery'),
            WATCHMODMARKET_VERSION,
            true
        );
    }
    
    // Page-specific JS - Enhanced watch builder loading
    if (is_page('builder') || is_page_template('page-templates/page-watch-builder.php')) {
        // Three.js for 3D rendering (load from CDN)
        wp_enqueue_script(
            'threejs',
            'https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js',
            array(),
            'r128',
            true
        );
        
        // Three.js OrbitControls
        wp_enqueue_script(
            'threejs-orbit-controls',
            'https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/controls/OrbitControls.js',
            array('threejs'),
            '0.128.0',
            true
        );
        
        // Enhanced Watch Builder JavaScript
        if (file_exists(WATCHMODMARKET_DIR . '/assets/js/watch-builder.js')) {
            wp_enqueue_script(
                'watchmodmarket-builder',
                WATCHMODMARKET_URI . '/assets/js/watch-builder.js',
                array('jquery', 'threejs'),
                WATCHMODMARKET_VERSION,
                true
            );
        }
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
 * Localize scripts after they are enqueued
 */
function watchmodmarket_localize_scripts() {
    // Only localize if scripts are enqueued
    if (wp_script_is('watchmodmarket-main', 'enqueued')) {
        wp_localize_script(
            'watchmodmarket-main',
            'watchmodmarket_ajax',
            array(
                'ajax_url'        => admin_url('admin-ajax.php'),
                'nonce'           => wp_create_nonce('watchmodmarket_ajax'),
                'builder_nonce'   => wp_create_nonce('watch_builder_nonce'),
                'isLoggedIn'      => is_user_logged_in(),
                'loginUrl'        => wp_login_url(get_permalink()),
                'currencySymbol'  => function_exists('get_woocommerce_currency_symbol') ? get_woocommerce_currency_symbol() : '$',
                'themeUrl'        => get_template_directory_uri(),
                'cartUrl'         => function_exists('wc_get_cart_url') ? wc_get_cart_url() : '/cart/',
                'adding_to_cart'  => __('Adding...', 'watchmodmarket'),
                'added_to_cart'   => __('Added!', 'watchmodmarket'),
                'i18n'            => array(
                    'incompatibleMovement' => __('The selected movement is not compatible with this case.', 'watchmodmarket'),
                    'dialTooBig'          => __('The selected dial is too large for this case.', 'watchmodmarket'),
                    'incompatibleHands'   => __('The selected hands are not compatible with this movement.', 'watchmodmarket'),
                    'loginRequired'       => __('You must be logged in to save builds.', 'watchmodmarket'),
                    'selectSomeParts'     => __('Please select at least one part to save a build.', 'watchmodmarket'),
                    'buildSaved'          => __('Your build has been saved successfully!', 'watchmodmarket'),
                    'saveFailed'          => __('Failed to save your build. Please try again.', 'watchmodmarket'),
                    'selectAllParts'      => __('Please select all required parts', 'watchmodmarket'),
                    'confirmIncompatible' => __('There are compatibility warnings. Continue anyway?', 'watchmodmarket'),
                    'addingToCart'        => __('Adding to cart...', 'watchmodmarket'),
                    'addedToCart'         => __('Added to cart!', 'watchmodmarket'),
                )
            )
        );
    }
    
    // Localize builder script if it's enqueued
    if (wp_script_is('watchmodmarket-builder', 'enqueued')) {
        wp_localize_script(
            'watchmodmarket-builder',
            'wpData',
            array(
                'ajaxUrl'         => admin_url('admin-ajax.php'),
                'nonce'           => wp_create_nonce('watch_builder_nonce'),
                'isLoggedIn'      => is_user_logged_in(),
                'loginUrl'        => wp_login_url(get_permalink()),
                'currencySymbol'  => function_exists('get_woocommerce_currency_symbol') ? get_woocommerce_currency_symbol() : '$',
                'themeUrl'        => get_template_directory_uri(),
                'cartUrl'         => function_exists('wc_get_cart_url') ? wc_get_cart_url() : '/cart/',
                'i18n'            => array(
                    'incompatibleMovement' => __('The selected movement is not compatible with this case.', 'watchmodmarket'),
                    'dialTooBig'          => __('The selected dial is too large for this case.', 'watchmodmarket'),
                    'incompatibleHands'   => __('The selected hands are not compatible with this movement.', 'watchmodmarket'),
                    'loginRequired'       => __('You must be logged in to save builds. Please log in or create an account.', 'watchmodmarket'),
                    'selectSomeParts'     => __('Please select at least one part to save a build.', 'watchmodmarket'),
                    'saveBuild'          => __('Save Your Build', 'watchmodmarket'),
                    'buildName'          => __('Build Name', 'watchmodmarket'),
                    'buildDescription'   => __('Description (Optional)', 'watchmodmarket'),
                    'makePublic'         => __('Make this build public in the community', 'watchmodmarket'),
                    'saveButton'         => __('Save Build', 'watchmodmarket'),
                    'saving'             => __('Saving...', 'watchmodmarket'),
                    'buildSaved'         => __('Your build has been saved successfully!', 'watchmodmarket'),
                    'saveFailed'         => __('Failed to save your build. Please try again.', 'watchmodmarket'),
                    'selectAllParts'     => __('Please select all required parts', 'watchmodmarket'),
                    'confirmIncompatible' => __('There are compatibility warnings. Do you still want to add this build to cart?', 'watchmodmarket'),
                    'addingToCart'        => __('Adding to cart...', 'watchmodmarket'),
                    'addedToCart'         => __('Added to cart!', 'watchmodmarket'),
                )
            )
        );
    }
    
    // Localize header-footer script if it's enqueued and WooCommerce is active
    if (wp_script_is('watchmodmarket-header-footer', 'enqueued') && class_exists('WooCommerce')) {
        wp_localize_script('watchmodmarket-header-footer', 'wc_add_to_cart_params', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'wc_ajax_url' => WC_AJAX::get_endpoint('%%endpoint%%'),
            'nonce' => wp_create_nonce('watchmodmarket_ajax'),
            'is_logged_in' => is_user_logged_in(),
            'login_url' => wp_login_url(get_permalink())
        ));
    }
}
add_action('wp_enqueue_scripts', 'watchmodmarket_localize_scripts', 20);

/**
 * Enhanced script loading for watch builder page
 */
function watchmodmarket_builder_scripts() {
    // Only load on builder pages
    if (!is_page('builder') && !is_page_template('page-templates/page-watch-builder.php')) {
        return;
    }
    
    // Add script loading optimization
    add_filter('script_loader_tag', 'watchmodmarket_add_script_attributes', 10, 3);
}
add_action('wp_enqueue_scripts', 'watchmodmarket_builder_scripts', 5);

/**
 * Add async/defer attributes to specific scripts
 */
function watchmodmarket_add_script_attributes($tag, $handle, $src) {
    // Add defer to Three.js scripts for better performance
    if (in_array($handle, array('threejs', 'threejs-orbit-controls'))) {
        $tag = str_replace(' src', ' defer src', $tag);
    }
    
    return $tag;
}

/**
 * Create directory structure if it doesn't exist
 */
function watchmodmarket_create_directories() {
    $directories = array(
        '/assets',
        '/assets/css',
        '/assets/js',
        '/assets/images',
        '/assets/images/testimonials',
        '/assets/models',
        '/assets/models/cases',
        '/assets/models/dials',
        '/assets/models/hands',
        '/assets/models/straps',
        '/assets/models/movements',
        '/inc',
        '/inc/core',
        '/inc/features',
        '/inc/woocommerce',
        '/inc/admin',
        '/inc/ajax',
        '/inc/widgets',
        '/template-parts',
        '/template-parts/shop',
    );
    
    foreach ($directories as $dir) {
        $path = get_template_directory() . $dir;
        if (!file_exists($path)) {
            wp_mkdir_p($path);
        }
    }
}
add_action('after_switch_theme', 'watchmodmarket_create_directories');

/**
 * Include required files - Reorganized structure
 */
function watchmodmarket_load_includes() {
    $required_files = array(
        // Core functionality
        '/inc/template-tags.php',
        '/inc/customizer.php',
        '/inc/navigation.php',
        '/inc/post-types.php',
        '/inc/taxonomies.php',
        
        // WooCommerce (only loaded if WooCommerce is active)
        class_exists('WooCommerce') ? '/inc/woocommerce.php' : null,
    );

    // Filter out null values
    $required_files = array_filter($required_files);

    // Load required files
    foreach ($required_files as $file) {
        $file_path = WATCHMODMARKET_DIR . $file;
        if (file_exists($file_path)) {
            require_once $file_path;
        }
    }
}
add_action('after_setup_theme', 'watchmodmarket_load_includes');

/**
 * Register widget areas.
 */
function watchmodmarket_widgets_init() {
    register_sidebar(
        array(
            'name'          => esc_html__('Sidebar', 'watchmodmarket'),
            'id'            => 'sidebar-1',
            'description'   => esc_html__('Add widgets here.', 'watchmodmarket'),
            'before_widget' => '<section id="%1$s" class="widget %2$s">',
            'after_widget'  => '</section>',
            'before_title'  => '<h2 class="widget-title">',
            'after_title'   => '</h2>',
        )
    );
    
    // Register footer widget areas
    $footer_widget_areas = array(
        'footer-1' => esc_html__('Footer 1', 'watchmodmarket'),
        'footer-2' => esc_html__('Footer 2', 'watchmodmarket'),
        'footer-3' => esc_html__('Footer 3', 'watchmodmarket'),
        'footer-4' => esc_html__('Footer 4', 'watchmodmarket'),
    );
    
    foreach ($footer_widget_areas as $id => $name) {
        register_sidebar(
            array(
                'name'          => $name,
                'id'            => $id,
                'description'   => sprintf(esc_html__('%s footer column.', 'watchmodmarket'), $name),
                'before_widget' => '<div class="footer-widget">',
                'after_widget'  => '</div>',
                'before_title'  => '<h3 class="footer-widget-title">',
                'after_title'   => '</h3>',
            )
        );
    }
    
    // Newsletter widget area
    register_sidebar(array(
        'name'          => __('Footer Newsletter', 'watchmodmarket'),
        'id'            => 'footer-newsletter',
        'description'   => __('Newsletter signup area in footer.', 'watchmodmarket'),
        'before_widget' => '<div class="footer-newsletter-widget">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="newsletter-widget-title">',
        'after_title'   => '</h3>',
    ));
    
    // Instagram feed widget area
    register_sidebar(array(
        'name'          => __('Footer Instagram', 'watchmodmarket'),
        'id'            => 'footer-instagram',
        'description'   => __('Instagram feed area in footer.', 'watchmodmarket'),
        'before_widget' => '<div class="footer-instagram-widget">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="instagram-widget-title">',
        'after_title'   => '</h3>',
    ));
}
add_action('widgets_init', 'watchmodmarket_widgets_init');

/**
 * AJAX handler for adding watch build to cart
 */
function watchmodmarket_add_build_to_cart() {
    // Check nonce for security
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'watchmodmarket_ajax')) {
        wp_send_json_error(array('message' => __('Security check failed', 'watchmodmarket')));
    }
    
    // Check if WooCommerce is active
    if (!class_exists('WooCommerce')) {
        wp_send_json_error(array('message' => __('WooCommerce is not active', 'watchmodmarket')));
    }
    
    // Get form data
    $selected_parts = isset($_POST['selected_parts']) ? $_POST['selected_parts'] : array();
    $total_price = isset($_POST['total_price']) ? floatval($_POST['total_price']) : 0;
    $build_data = isset($_POST['build_data']) ? $_POST['build_data'] : array();
    
    // Validate data
    if (empty($selected_parts)) {
        wp_send_json_error(array('message' => __('No parts selected', 'watchmodmarket')));
    }
    
    try {
        $added_products = array();
        
        // Add each selected part to cart
        foreach ($selected_parts as $part_type => $product_id) {
            if (!empty($product_id)) {
                $product_id = intval($product_id);
                $product = wc_get_product($product_id);
                
                if ($product && $product->is_purchasable()) {
                    $cart_item_data = array(
                        'watch_build_part_type' => $part_type,
                        'watch_build_data' => $build_data,
                        'is_watch_build_item' => true
                    );
                    
                    $cart_item_key = WC()->cart->add_to_cart($product_id, 1, 0, array(), $cart_item_data);
                    
                    if ($cart_item_key) {
                        $added_products[] = array(
                            'id' => $product_id,
                            'name' => $product->get_name(),
                            'type' => $part_type
                        );
                    }
                }
            }
        }
        
        if (empty($added_products)) {
            wp_send_json_error(array('message' => __('No products could be added to cart', 'watchmodmarket')));
        }
        
        // Calculate cart totals
        WC()->cart->calculate_totals();
        
        wp_send_json_success(array(
            'message' => sprintf(__('%d parts added to cart successfully!', 'watchmodmarket'), count($added_products)),
            'added_products' => $added_products,
            'cart_url' => wc_get_cart_url(),
            'cart_count' => WC()->cart->get_cart_contents_count(),
            'cart_total' => WC()->cart->get_cart_total()
        ));
        
    } catch (Exception $e) {
        wp_send_json_error(array('message' => __('Failed to add items to cart: ', 'watchmodmarket') . $e->getMessage()));
    }
}
add_action('wp_ajax_add_build_to_cart', 'watchmodmarket_add_build_to_cart');
add_action('wp_ajax_nopriv_add_build_to_cart', 'watchmodmarket_add_build_to_cart');

/**
 * AJAX handler for saving watch builds
 */
function watchmodmarket_save_watch_build() {
    // Check nonce for security
    if (!isset($_POST['security']) || !wp_verify_nonce($_POST['security'], 'watch_builder_nonce')) {
        wp_send_json_error(array('message' => __('Security check failed', 'watchmodmarket')));
    }
    
    // Check if user is logged in
    if (!is_user_logged_in()) {
        wp_send_json_error(array('message' => __('You must be logged in to save builds', 'watchmodmarket')));
    }
    
    // Get form data
    $build_name = isset($_POST['build_name']) ? sanitize_text_field($_POST['build_name']) : '';
    $build_description = isset($_POST['build_description']) ? sanitize_textarea_field($_POST['build_description']) : '';
    $is_public = isset($_POST['build_public']) ? intval($_POST['build_public']) : 0;
    $selected_parts = isset($_POST['selected_parts']) ? $_POST['selected_parts'] : array();
    $total_price = isset($_POST['total_price']) ? floatval($_POST['total_price']) : 0;
    
    // Validate data
    if (empty($build_name)) {
        wp_send_json_error(array('message' => __('Build name is required', 'watchmodmarket')));
    }
    
    if (empty($selected_parts)) {
        wp_send_json_error(array('message' => __('No parts selected', 'watchmodmarket')));
    }
    
    try {
        // Create the build post
        $post_data = array(
            'post_title'   => $build_name,
            'post_content' => $build_description,
            'post_status'  => $is_public ? 'publish' : 'private',
            'post_type'    => 'watch_build',
            'post_author'  => get_current_user_id(),
        );
        
        $build_id = wp_insert_post($post_data);
        
        if (is_wp_error($build_id)) {
            throw new Exception('Failed to create build post');
        }
        
        // Save meta data
        update_post_meta($build_id, '_selected_parts', $selected_parts);
        update_post_meta($build_id, '_build_visibility', $is_public ? 'public' : 'private');
        update_post_meta($build_id, '_build_total_price', $total_price);
        update_post_meta($build_id, '_build_created_date', current_time('mysql'));
        
        wp_send_json_success(array(
            'message' => __('Build saved successfully!', 'watchmodmarket'),
            'build_id' => $build_id,
            'build_url' => get_permalink($build_id),
            'edit_url' => get_edit_post_link($build_id, 'raw')
        ));
        
    } catch (Exception $e) {
        error_log('Watch build save error: ' . $e->getMessage());
        wp_send_json_error(array('message' => __('Failed to save build. Please try again.', 'watchmodmarket')));
    }
}
add_action('wp_ajax_save_watch_build', 'watchmodmarket_save_watch_build');

/**
 * Enhanced Quick View AJAX handler
 */
function watchmodmarket_quick_view_ajax() {
    // Check nonce for security
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'watchmodmarket_ajax')) {
        wp_send_json_error(array('message' => __('Security check failed', 'watchmodmarket')));
    }
    
    // Get product ID from request
    $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
    
    // Validate product ID
    if ($product_id <= 0) {
        wp_send_json_error(array('message' => __('Invalid product ID', 'watchmodmarket')));
    }
    
    // Get product object
    $product = wc_get_product($product_id);
    
    // Verify product exists
    if (!$product) {
        wp_send_json_error(array('message' => __('Product not found', 'watchmodmarket')));
    }
    
    // Get product data
    $data = array(
        'id'          => $product_id,
        'title'       => $product->get_name(),
        'price'       => $product->get_price_html(),
        'description' => $product->get_short_description(),
        'url'         => get_permalink($product_id),
        'image'       => get_the_post_thumbnail_url($product_id, 'medium'),
        'gallery'     => array(),
        'attributes'  => array(),
        'rating'      => $product->get_average_rating(),
        'review_count' => $product->get_review_count(),
        'stock_status' => $product->is_in_stock() ? 'instock' : 'outofstock',
    );
    
    // Get gallery images
    $attachment_ids = $product->get_gallery_image_ids();
    foreach ($attachment_ids as $attachment_id) {
        $data['gallery'][] = wp_get_attachment_image_url($attachment_id, 'medium');
    }
    
    // Get product attributes
    $attributes = $product->get_attributes();
    foreach ($attributes as $attribute) {
        if ($attribute->get_visible()) {
            $data['attributes'][] = array(
                'name' => wc_attribute_label($attribute->get_name()),
                'value' => $product->get_attribute($attribute->get_name())
            );
        }
    }
    
    wp_send_json_success($data);
}
add_action('wp_ajax_watchmodmarket_quick_view', 'watchmodmarket_quick_view_ajax');
add_action('wp_ajax_nopriv_watchmodmarket_quick_view', 'watchmodmarket_quick_view_ajax');

/**
 * AJAX handler for newsletter signup
 */
function watchmodmarket_newsletter_signup() {
    // Verify nonce
    if (!wp_verify_nonce($_POST['newsletter_nonce'], 'newsletter_signup')) {
        wp_send_json_error(array('message' => __('Security check failed', 'watchmodmarket')));
    }
    
    $email = sanitize_email($_POST['email']);
    
    if (!is_email($email)) {
        wp_send_json_error(array('message' => __('Please enter a valid email address', 'watchmodmarket')));
    }
    
    // Here you would integrate with your email service provider
    // For example: Mailchimp, ConvertKit, etc.
    
    // For demo purposes, we'll just store in WordPress
    $subscribers = get_option('watchmodmarket_newsletter_subscribers', array());
    
    if (!in_array($email, $subscribers)) {
        $subscribers[] = $email;
        update_option('watchmodmarket_newsletter_subscribers', $subscribers);
        
        wp_send_json_success(array('message' => __('Thank you for subscribing!', 'watchmodmarket')));
    } else {
        wp_send_json_error(array('message' => __('You are already subscribed', 'watchmodmarket')));
    }
}
add_action('wp_ajax_newsletter_signup', 'watchmodmarket_newsletter_signup');
add_action('wp_ajax_nopriv_newsletter_signup', 'watchmodmarket_newsletter_signup');

/**
 * Display custom cart item data for watch build items
 */
function watchmodmarket_display_cart_item_custom_data($item_data, $cart_item) {
    if (isset($cart_item['watch_build_part_type'])) {
        $item_data[] = array(
            'key'     => __('Part Type', 'watchmodmarket'),
            'value'   => ucfirst($cart_item['watch_build_part_type']),
            'display' => '',
        );
    }
    
    if (isset($cart_item['is_watch_build_item']) && $cart_item['is_watch_build_item']) {
        $item_data[] = array(
            'key'     => __('From', 'watchmodmarket'),
            'value'   => __('Watch Builder', 'watchmodmarket'),
            'display' => '',
        );
    }
    
    return $item_data;
}
add_filter('woocommerce_get_item_data', 'watchmodmarket_display_cart_item_custom_data', 10, 2);

/**
 * Save custom cart item data to order items
 */
function watchmodmarket_save_cart_item_custom_data($item, $cart_item_key, $values, $order) {
    if (isset($values['watch_build_part_type'])) {
        $item->add_meta_data('_watch_build_part_type', $values['watch_build_part_type']);
    }
    
    if (isset($values['watch_build_data'])) {
        $item->add_meta_data('_watch_build_data', $values['watch_build_data']);
    }
    
    if (isset($values['is_watch_build_item'])) {
        $item->add_meta_data('_is_watch_build_item', $values['is_watch_build_item']);
    }
}
add_action('woocommerce_checkout_create_order_line_item', 'watchmodmarket_save_cart_item_custom_data', 10, 4);

/**
 * Display custom order item meta in admin
 */
function watchmodmarket_display_order_item_meta($item_id, $item, $order) {
    $part_type = $item->get_meta('_watch_build_part_type');
    $is_build_item = $item->get_meta('_is_watch_build_item');
    
    if ($part_type) {
        echo '<p><strong>' . __('Part Type:', 'watchmodmarket') . '</strong> ' . ucfirst($part_type) . '</p>';
    }
    
    if ($is_build_item) {
        echo '<p><strong>' . __('Source:', 'watchmodmarket') . '</strong> ' . __('Watch Builder', 'watchmodmarket') . '</p>';
    }
}
add_action('woocommerce_after_order_item_name', 'watchmodmarket_display_order_item_meta', 10, 3);

/**
 * Add custom post type for watch builds if it doesn't exist
 */
function watchmodmarket_register_watch_build_post_type() {
    if (post_type_exists('watch_build')) {
        return;
    }
    
    $labels = array(
        'name'                  => _x('Watch Builds', 'Post type general name', 'watchmodmarket'),
        'singular_name'         => _x('Watch Build', 'Post type singular name', 'watchmodmarket'),
        'menu_name'             => _x('Watch Builds', 'Admin Menu text', 'watchmodmarket'),
        'name_admin_bar'        => _x('Watch Build', 'Add New on Toolbar', 'watchmodmarket'),
        'add_new'               => __('Add New', 'watchmodmarket'),
        'add_new_item'          => __('Add New Watch Build', 'watchmodmarket'),
        'new_item'              => __('New Watch Build', 'watchmodmarket'),
        'edit_item'             => __('Edit Watch Build', 'watchmodmarket'),
        'view_item'             => __('View Watch Build', 'watchmodmarket'),
        'all_items'             => __('All Watch Builds', 'watchmodmarket'),
        'search_items'          => __('Search Watch Builds', 'watchmodmarket'),
        'parent_item_colon'     => __('Parent Watch Builds:', 'watchmodmarket'),
        'not_found'             => __('No watch builds found.', 'watchmodmarket'),
        'not_found_in_trash'    => __('No watch builds found in Trash.', 'watchmodmarket'),
        'featured_image'        => _x('Watch Build Image', 'Overrides the "Featured Image" phrase', 'watchmodmarket'),
        'set_featured_image'    => _x('Set watch build image', 'Overrides the "Set featured image" phrase', 'watchmodmarket'),
        'remove_featured_image' => _x('Remove watch build image', 'Overrides the "Remove featured image" phrase', 'watchmodmarket'),
        'use_featured_image'    => _x('Use as watch build image', 'Overrides the "Use as featured image" phrase', 'watchmodmarket'),
        'archives'              => _x('Watch Build archives', 'The post type archive label', 'watchmodmarket'),
        'insert_into_item'      => _x('Insert into watch build', 'Overrides the "Insert into post" phrase', 'watchmodmarket'),
        'uploaded_to_this_item' => _x('Uploaded to this watch build', 'Overrides the "Uploaded to this post" phrase', 'watchmodmarket'),
        'filter_items_list'     => _x('Filter watch builds list', 'Screen reader text for the filter links', 'watchmodmarket'),
        'items_list_navigation' => _x('Watch builds list navigation', 'Screen reader text for the pagination', 'watchmodmarket'),
        'items_list'            => _x('Watch builds list', 'Screen reader text for the items list', 'watchmodmarket'),
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array('slug' => 'watch-builds'),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'menu_icon'          => 'dashicons-admin-customizer',
        'supports'           => array('title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments'),
        'show_in_rest'       => true,
    );

    register_post_type('watch_build', $args);
}
add_action('init', 'watchmodmarket_register_watch_build_post_type');

/**
 * Add admin column for watch builds
 */
function watchmodmarket_add_watch_build_columns($columns) {
    $new_columns = array();
    
    foreach ($columns as $key => $value) {
        $new_columns[$key] = $value;
        
        if ($key === 'title') {
            $new_columns['build_parts'] = __('Parts', 'watchmodmarket');
            $new_columns['build_price'] = __('Total Price', 'watchmodmarket');
            $new_columns['build_visibility'] = __('Visibility', 'watchmodmarket');
        }
    }
    
    return $new_columns;
}
add_filter('manage_watch_build_posts_columns', 'watchmodmarket_add_watch_build_columns');

/**
 * Populate custom columns for watch builds
 */
function watchmodmarket_populate_watch_build_columns($column, $post_id) {
    switch ($column) {
        case 'build_parts':
            $selected_parts = get_post_meta($post_id, '_selected_parts', true);
            if (is_array($selected_parts)) {
                $part_count = count(array_filter($selected_parts));
                if ($part_count == 1) {
                    echo '1 ' . __('part', 'watchmodmarket');
                } else {
                    echo $part_count . ' ' . __('parts', 'watchmodmarket');
                }   
            } else {
                echo __('No parts', 'watchmodmarket');
            }
            break;
            
        case 'build_price':
            $total_price = get_post_meta($post_id, '_build_total_price', true);
            if ($total_price) {
                $currency_symbol = function_exists('get_woocommerce_currency_symbol') ? get_woocommerce_currency_symbol() : '$';
                echo $currency_symbol . number_format($total_price, 2);
            } else {
                echo __('N/A', 'watchmodmarket');
            }
            break;
            
        case 'build_visibility':
            $visibility = get_post_meta($post_id, '_build_visibility', true);
            $status = get_post_status($post_id);
            
            if ($status === 'private') {
                echo '<span style="color: #999;">' . __('Private', 'watchmodmarket') . '</span>';
            } elseif ($status === 'publish') {
                echo '<span style="color: #46b450;">' . __('Public', 'watchmodmarket') . '</span>';
            } else {
                echo '<span style="color: #ffb900;">' . ucfirst($status) . '</span>';
            }
            break;
    }
}
add_action('manage_watch_build_posts_custom_column', 'watchmodmarket_populate_watch_build_columns', 10, 2);

/**
 * Add theme customizer options for footer
 */
function watchmodmarket_footer_customizer($wp_customize) {
    // Footer Section
    $wp_customize->add_section('footer_settings', array(
        'title'    => __('Footer Settings', 'watchmodmarket'),
        'priority' => 130,
    ));
    
    // Contact Email
    $wp_customize->add_setting('contact_email', array(
        'default'           => 'support@watchmodmarket.com',
        'sanitize_callback' => 'sanitize_email',
    ));
    
    $wp_customize->add_control('contact_email', array(
        'label'   => __('Contact Email', 'watchmodmarket'),
        'section' => 'footer_settings',
        'type'    => 'email',
    ));
    
    // Contact Phone
    $wp_customize->add_setting('contact_phone', array(
        'default'           => '+1 (800) 555-1234',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('contact_phone', array(
        'label'   => __('Contact Phone', 'watchmodmarket'),
        'section' => 'footer_settings',
        'type'    => 'text',
    ));
    
    // Social Media Links
    $social_networks = array(
        'facebook'  => 'Facebook',
        'instagram' => 'Instagram', 
        'twitter'   => 'Twitter',
        'youtube'   => 'YouTube',
        'pinterest' => 'Pinterest'
    );
    
    foreach ($social_networks as $network => $label) {
        $wp_customize->add_setting('social_' . $network, array(
            'default'           => '',
            'sanitize_callback' => 'esc_url_raw',
        ));
        
        $wp_customize->add_control('social_' . $network, array(
            'label'   => sprintf(__('%s URL', 'watchmodmarket'), $label),
            'section' => 'footer_settings',
            'type'    => 'url',
        ));
    }
    
    // Copyright Text
    $wp_customize->add_setting('footer_copyright', array(
        'default'           => '',
        'sanitize_callback' => 'wp_kses_post',
    ));
    
    $wp_customize->add_control('footer_copyright', array(
        'label'   => __('Custom Copyright Text', 'watchmodmarket'),
        'section' => 'footer_settings',
        'type'    => 'textarea',
        'description' => __('Leave empty to use default copyright text.', 'watchmodmarket'),
    ));
}
add_action('customize_register', 'watchmodmarket_footer_customizer');

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
 * Default menu for header
 */
function watchmodmarket_default_menu() {
    $menu_items = array(
        home_url('/') => __('Home', 'watchmodmarket'),
    );
    
    // Only add WooCommerce shop link if WooCommerce is active
    if (function_exists('wc_get_page_id')) {
        $menu_items[get_permalink(wc_get_page_id('shop'))] = __('Shop', 'watchmodmarket');
    }
    
    $menu_items[get_permalink(get_page_by_path('builder'))] = __('Builder', 'watchmodmarket');
    $menu_items[get_permalink(get_page_by_path('group-buy'))] = __('Group Buys', 'watchmodmarket');
    $menu_items[get_permalink(get_page_by_path('contact'))] = __('Contact', 'watchmodmarket');
    
    echo '<ul id="primary-menu" class="main-menu">';
    foreach ($menu_items as $url => $label) {
        if ($url) {
            echo '<li><a href="' . esc_url($url) . '">' . esc_html($label) . '</a></li>';
        }
    }
    echo '</ul>';
}

/**
 * Flush rewrite rules on theme activation
 */
function watchmodmarket_rewrite_flush() {
    flush_rewrite_rules();
}
add_action('after_switch_theme', 'watchmodmarket_rewrite_flush');

/**
 * Create pages on theme activation
 */
function watchmodmarket_create_pages() {
    $pages = array(
        'builder' => array(
            'title' => 'Watch Builder',
            'content' => '<!-- wp:paragraph --><p>Create your perfect timepiece by selecting and combining different watch components. Our interactive tool lets you visualize your design in real-time.</p><!-- /wp:paragraph -->',
            'template' => 'page-templates/page-watch-builder.php'
        ),
        'group-buy' => array(
            'title' => 'Timepiece Futures',
            'content' => '<!-- wp:paragraph --><p>Exclusive Group Buys & Pre-Orders for Premium Watch Parts</p><!-- /wp:paragraph -->',
            'template' => 'page-templates/page-group-buy.php'
        ),
        'faq' => array(
            'title' => 'Frequently Asked Questions',
            'content' => '<!-- wp:paragraph --><p>Find answers to commonly asked questions about our products, services, and policies.</p><!-- /wp:paragraph -->',
            'template' => 'page-templates/page-faq.php'
        ),
        'contact' => array(
            'title' => 'Contact Us',
            'content' => '<!-- wp:paragraph --><p>Get in touch with us about watch parts, orders, or custom projects.</p><!-- /wp:paragraph -->',
            'template' => 'page-templates/page-contact.php'
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
            if (function_exists('wc_create_attribute')) {
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
}
add_action('init', 'watchmodmarket_create_product_attributes');

/**
 * Add footer body class for styling
 */
function watchmodmarket_footer_body_class($classes) {
    // Add class if footer widgets are active
    $footer_widget_areas = array('footer-1', 'footer-2', 'footer-3', 'footer-4');
    $has_widgets = false;
    
    foreach ($footer_widget_areas as $widget_area) {
        if (is_active_sidebar($widget_area)) {
            $has_widgets = true;
            break;
        }
    }
    
    if ($has_widgets) {
        $classes[] = 'has-footer-widgets';
    }
    
    return $classes;
}
add_filter('body_class', 'watchmodmarket_footer_body_class');

/**
 * Footer schema markup for SEO
 */
function watchmodmarket_footer_schema() {
    $schema = array(
        '@context' => 'https://schema.org',
        '@type' => 'Organization',
        'name' => get_bloginfo('name'),
        'url' => home_url(),
        'description' => get_bloginfo('description'),
        'contactPoint' => array(
            '@type' => 'ContactPoint',
            'telephone' => get_theme_mod('contact_phone', '+1-800-555-1234'),
            'email' => get_theme_mod('contact_email', 'support@watchmodmarket.com'),
            'contactType' => 'customer service'
        )
    );
    
    // Add social media links if available
    $social_links = array();
    $social_networks = array('facebook', 'instagram', 'twitter', 'youtube', 'pinterest');
    
    foreach ($social_networks as $network) {
        $url = get_theme_mod('social_' . $network);
        if (!empty($url)) {
            $social_links[] = $url;
        }
    }
    
    if (!empty($social_links)) {
        $schema['sameAs'] = $social_links;
    }
    
    echo '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_SLASHES) . '</script>';
}
add_action('wp_footer', 'watchmodmarket_footer_schema');

/**
 * Generate footer navigation menu
 */
function watchmodmarket_footer_nav_menu() {
    $menu_items = array(
        home_url('/terms/') => __('Terms', 'watchmodmarket'),
        get_privacy_policy_url() => __('Privacy', 'watchmodmarket'),
        home_url('/cookies/') => __('Cookies', 'watchmodmarket'),
        home_url('/sitemap/') => __('Sitemap', 'watchmodmarket'),
    );
    
    echo '<nav class="footer-nav" role="navigation">';
    echo '<ul class="footer-nav-menu">';
    
    foreach ($menu_items as $url => $label) {
        if (!empty($url)) {
            echo '<li><a href="' . esc_url($url) . '">' . esc_html($label) . '</a></li>';
        }
    }
    
    echo '</ul>';
    echo '</nav>';
}

/**
 * Enhanced back to top button with progress indicator
 */
function watchmodmarket_back_to_top_with_progress() {
    ?>
    <div id="back-to-top-container" class="back-to-top-container">
        <svg class="progress-ring" width="50" height="50">
            <circle class="progress-ring-circle" 
                    stroke="rgba(255,92,0,0.3)" 
                    stroke-width="3" 
                    fill="transparent" 
                    r="20" 
                    cx="25" 
                    cy="25"/>
        </svg>
        <button id="back-to-top" class="back-to-top" aria-label="<?php esc_attr_e('Back to top', 'watchmodmarket'); ?>">
            <i class="fa fa-arrow-up" aria-hidden="true"></i>
        </button>
    </div>
    
    <style>
    .back-to-top-container {
        position: fixed;
        right: 1.5rem;
        bottom: 1.5rem;
        z-index: 1000;
    }
    
    .progress-ring {
        position: absolute;
        top: 0;
        left: 0;
        transform: rotate(-90deg);
    }
    
    .progress-ring-circle {
        transition: stroke-dasharray 0.35s;
        stroke-dasharray: 0 126;
        stroke-linecap: round;
    }
    </style>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const circle = document.querySelector('.progress-ring-circle');
        if (!circle) return;
        
        const radius = circle.r.baseVal.value;
        const circumference = radius * 2 * Math.PI;
        
        circle.style.strokeDasharray = `${circumference} ${circumference}`;
        circle.style.strokeDashoffset = circumference;
        
        function setProgress(percent) {
            const offset = circumference - percent / 100 * circumference;
            circle.style.strokeDashoffset = offset;
        }
        
        window.addEventListener('scroll', function() {
            const scrollTop = document.documentElement.scrollTop;
            const scrollHeight = document.documentElement.scrollHeight - document.documentElement.clientHeight;
            const scrollPercent = scrollHeight > 0 ? (scrollTop / scrollHeight) * 100 : 0;
            
            setProgress(scrollPercent);
        });
    });
    </script>
    <?php
}
add_action('wp_footer', 'watchmodmarket_back_to_top_with_progress');

/**
 * Hide default WooCommerce elements
 */
if (class_exists('WooCommerce')) {
    add_filter('woocommerce_show_page_title', '__return_false');
    remove_action('woocommerce_before_shop_loop', 'woocommerce_result_count', 20);
    remove_action('woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30);
}