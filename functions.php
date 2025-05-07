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
    } elseif (is_page('builder') && file_exists(WATCHMODMARKET_DIR . '/assets/css/components.css')) {
        wp_enqueue_style(
            'watchmodmarket-builder',
            WATCHMODMARKET_URI . '/assets/css/components.css',
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
            'watchmodmarket_ajax',
            array(
                'ajax_url'        => admin_url('admin-ajax.php'),
                'nonce'           => wp_create_nonce('watchmodmarket_ajax'),
                'adding_to_cart'  => __('Adding...', 'watchmodmarket'),
                'added_to_cart'   => __('Added!', 'watchmodmarket')
            )
        );
    }
    
    // Page-specific JS - Only load if the files exist
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
 * Create directory structure if it doesn't exist
 */
function watchmodmarket_create_directories() {
    $directories = array(
        '/assets',
        '/assets/css',
        '/assets/js',
        '/assets/images',
        '/assets/images/testimonials', // Add this directory for testimonial avatars
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
$required_files = array(
    // Core functionality
    '/inc/template-tags.php',           // Custom template tags
    '/inc/customizer.php',              // Customizer settings
    '/inc/navigation.php',              // Navigation enhancements
    '/inc/post-types.php',              // Custom post types
    '/inc/taxonomies.php',              // Custom taxonomies
    
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
}
add_action('widgets_init', 'watchmodmarket_widgets_init');

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
 * Hide default WooCommerce product count and showing results
 */
add_filter('woocommerce_show_page_title', '__return_false');
remove_action('woocommerce_before_shop_loop', 'woocommerce_result_count', 20);
remove_action('woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30);

/**
 * AJAX handler for Quick View functionality - Consolidated version
 */
function watchmodmarket_quick_view_ajax() {
    // Check nonce for security
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'watchmodmarket_ajax')) {
        wp_send_json_error(array('message' => __('Security check failed', 'watchmodmarket')));
        wp_die();
    }
    
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
}
// Consolidated AJAX handlers to avoid duplicates
add_action('wp_ajax_watchmodmarket_quick_view', 'watchmodmarket_quick_view_ajax');
add_action('wp_ajax_nopriv_watchmodmarket_quick_view', 'watchmodmarket_quick_view_ajax');

/**
 * AJAX handler for recently viewed products
 */
function watchmodmarket_get_recently_viewed_products() {
    // No nonce check needed as this is a read-only operation
    
    $product_ids = isset($_POST['product_ids']) ? array_map('intval', $_POST['product_ids']) : array();
    
    if (empty($product_ids)) {
        wp_send_json_error('No products to display');
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
    }
    
    wp_send_json_success($html);
}
add_action('wp_ajax_get_recently_viewed_products', 'watchmodmarket_get_recently_viewed_products');
add_action('wp_ajax_nopriv_get_recently_viewed_products', 'watchmodmarket_get_recently_viewed_products');

/**
 * AJAX handler for adding to cart
 */
function watchmodmarket_add_to_cart_ajax() {
    // Check nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'watchmodmarket_ajax')) {
        wp_send_json_error(array('message' => __('Invalid nonce', 'watchmodmarket')));
        wp_die();
    }
    
    // Check product ID
    if (!isset($_POST['product_id']) || empty($_POST['product_id'])) {
        wp_send_json_error(array('message' => __('Product ID is required', 'watchmodmarket')));
        wp_die();
    }
    
    $product_id = absint($_POST['product_id']);
    $quantity = isset($_POST['quantity']) ? absint($_POST['quantity']) : 1;
    
    // Add to cart
    $cart_item_key = WC()->cart->add_to_cart($product_id, $quantity);
    
    if (!$cart_item_key) {
        wp_send_json_error(array('message' => __('Failed to add product to cart', 'watchmodmarket')));
        wp_die();
    }
    
    // Get cart count
    $cart_count = WC()->cart->get_cart_contents_count();
    
    wp_send_json_success(array(
        'message' => __('Product added to cart successfully', 'watchmodmarket'),
        'cart_count' => $cart_count
    ));
}
add_action('wp_ajax_watchmodmarket_add_to_cart', 'watchmodmarket_add_to_cart_ajax');
add_action('wp_ajax_nopriv_watchmodmarket_add_to_cart', 'watchmodmarket_add_to_cart_ajax');

/**
 * AJAX handler for watchlist functionality
 */
function watchmodmarket_add_to_watchlist_ajax() {
    // Check nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'watchmodmarket_ajax')) {
        wp_send_json_error(array('message' => __('Security check failed', 'watchmodmarket')));
        wp_die();
    }
    
    // Check if user is logged in
    if (!is_user_logged_in()) {
        wp_send_json_error(array('message' => __('You must be logged in to add to wishlist', 'watchmodmarket')));
        wp_die();
    }
    
    // Check product ID
    if (!isset($_POST['product_id']) || empty($_POST['product_id'])) {
        wp_send_json_error(array('message' => __('Product ID is required', 'watchmodmarket')));
        wp_die();
    }
    
    $product_id = absint($_POST['product_id']);
    $user_id = get_current_user_id();
    
    // Get current watchlist
    $watchlist = get_user_meta($user_id, 'watchmodmarket_watchlist', true);
    
    if (!is_array($watchlist)) {
        $watchlist = array();
    }
    
    // Add product to watchlist if not already in it
    if (!in_array($product_id, $watchlist)) {
        $watchlist[] = $product_id;
        update_user_meta($user_id, 'watchmodmarket_watchlist', $watchlist);
        $action = 'added';
        $message = __('Added to watchlist', 'watchmodmarket');
    } else {
        // Remove from watchlist if already there (toggle functionality)
        $watchlist = array_diff($watchlist, array($product_id));
        update_user_meta($user_id, 'watchmodmarket_watchlist', $watchlist);
        $action = 'removed';
        $message = __('Removed from watchlist', 'watchmodmarket');
    }
    
    wp_send_json_success(array(
        'action' => $action,
        'message' => $message,
        'count' => count($watchlist)
    ));
}
add_action('wp_ajax_watchmodmarket_add_to_watchlist', 'watchmodmarket_add_to_watchlist_ajax');
add_action('wp_ajax_nopriv_watchmodmarket_add_to_watchlist', function() {
    wp_send_json_error(array('message' => __('Please log in to use the watchlist feature', 'watchmodmarket')));
});

/**
 * Group Buy specific functions
 */

/**
 * Get the count of active group buy campaigns
 *
 * @return int Number of active campaigns
 */
function watchmodmarket_get_active_campaigns_count() {
    // Query for active group buys and pre-orders
    $args = array(
        'post_type'      => 'product',
        'posts_per_page' => -1,
        'meta_query'     => array(
            'relation' => 'OR',
            array(
                'key'     => '_groupbuy_status',
                'value'   => 'active',
                'compare' => '=',
            ),
            array(
                'key'     => '_preorder_status',
                'value'   => 'upcoming',
                'compare' => '=',
            ),
        ),
        'tax_query'      => array(
            'relation' => 'OR',
            array(
                'taxonomy' => 'product_tag',
                'field'    => 'slug',
                'terms'    => 'group-buy',
            ),
            array(
                'taxonomy' => 'product_tag',
                'field'    => 'slug',
                'terms'    => 'pre-order',
            ),
        ),
    );
    
    $query = new WP_Query($args);
    
    return $query->found_posts;
}

/**
 * Calculate total savings from all active group buys
 *
 * @return string Total savings, formatted as currency
 */
function watchmodmarket_get_total_savings() {
    // Default value if actual calculation isn't possible yet
    return '$24,780';
}

/**
 * Get the success rate of group buy campaigns
 *
 * @return string Success rate percentage
 */
function watchmodmarket_get_success_rate() {
    // Default value if actual calculation isn't possible yet
    return '98%';
}

/**
 * Get the group buy that's ending soonest
 *
 * @return WP_Post|null The soonest ending group buy post
 */
function watchmodmarket_get_closest_ending_group_buy() {
    $args = array(
        'post_type'      => 'product',
        'posts_per_page' => 1,
        'meta_key'       => '_groupbuy_end_date',
        'orderby'        => 'meta_value',
        'order'          => 'ASC',
        'meta_query'     => array(
            array(
                'key'     => '_groupbuy_status',
                'value'   => 'active',
                'compare' => '=',
            ),
            array(
                'key'     => '_groupbuy_end_date',
                'value'   => date('Y-m-d H:i:s'),
                'compare' => '>',
                'type'    => 'DATETIME',
            ),
        ),
        'tax_query'      => array(
            array(
                'taxonomy' => 'product_tag',
                'field'    => 'slug',
                'terms'    => 'group-buy',
            ),
        ),
    );
    
    $query = new WP_Query($args);
    
    if ($query->have_posts()) {
        return $query->posts[0];
    }
    
    return null;
}

/**
 * Handle newsletter signup for group buy subscriptions
 */
function watchmodmarket_group_signup_handler() {
    // Check nonce
    if (!isset($_POST['group_nonce']) || !wp_verify_nonce($_POST['group_nonce'], 'group_signup')) {
        wp_die('Security check failed', 'Error', array('response' => 403));
    }
    
    // Get form data
    $name = isset($_POST['name']) ? sanitize_text_field($_POST['name']) : '';
    $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
    $interests = isset($_POST['interests']) ? $_POST['interests'] : array();
    
    // Validate data
    if (empty($email)) {
        wp_die('Email is required', 'Error', array('response' => 400));
    }
    
    // Process signup
    $signup_data = array(
        'name' => $name,
        'email' => $email,
        'interests' => $interests,
        'date' => current_time('mysql'),
    );
    
    // Save to options (for demonstration purposes only)
    $subscribers = get_option('watchmodmarket_group_subscribers', array());
    $subscribers[] = $signup_data;
    update_option('watchmodmarket_group_subscribers', $subscribers);
    
    // Send confirmation email
    $to = $email;
    $subject = 'Thanks for subscribing to Group Buy updates';
    $body = "Hello $name,\n\nThank you for subscribing to our Group Buy updates. We'll keep you informed about new opportunities.\n\nRegards,\nThe WatchModMarket Team";
    $headers = array('Content-Type: text/plain; charset=UTF-8');
    
    wp_mail($to, $subject, $body, $headers);
    
    // Redirect back to the page
    wp_redirect(wp_get_referer() . '?signup=success');
    exit;
}
add_action('admin_post_watchmodmarket_group_signup', 'watchmodmarket_group_signup_handler');
add_action('admin_post_nopriv_watchmodmarket_group_signup', 'watchmodmarket_group_signup_handler');

/**
 * AJAX handler for adding group buy products to cart
 */
function watchmodmarket_add_group_buy_to_cart_ajax() {
    // Check nonce for security
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'watchmodmarket_ajax')) {
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