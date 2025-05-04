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
            'watchmodmarket_ajax',
            array(
                'ajax_url'        => admin_url('admin-ajax.php'),
                'nonce'           => wp_create_nonce('watchmodmarket_ajax'),
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
 * Create directory structure if it doesn't exist
 */
function watchmodmarket_create_directories() {
    $directories = array(
        '/assets',
        '/assets/css',
        '/assets/css/pages',
        '/assets/js',
        '/assets/images',
        '/inc',
        '/template-parts',
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
 * Include required files
 */
$required_files = array(
    '/inc/template-tags.php',    // Custom template tags
    '/inc/customizer.php',       // Customizer settings
);

// WooCommerce support
if (class_exists('WooCommerce')) {
    $required_files[] = '/inc/woocommerce.php';
}

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
            'template' => 'watch-builder.php'
        ),
        'group-buy' => array(
            'title' => 'Timepiece Futures',
            'content' => '<!-- wp:paragraph --><p>Exclusive Group Buys & Pre-Orders for Premium Watch Parts</p><!-- /wp:paragraph -->',
            'template' => 'group-buy.php'
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
                'nonce' => wp_create_nonce('watchmodmarket_nonce'),
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
 * Hide default WooCommerce product count and showing results
 */
add_filter('woocommerce_show_page_title', '__return_false');
remove_action('woocommerce_before_shop_loop', 'woocommerce_result_count', 20);
remove_action('woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30);

/**
 * AJAX handler for Quick View functionality - FIXED VERSION
 */
function watchmodmarket_quick_view_handler() {
    // Verify nonce for security
    check_ajax_referer('watchmodmarket_ajax', 'nonce');
    
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
 * AJAX handler for adding to watchlist
 */
function watchmodmarket_add_to_watchlist() {
    check_ajax_referer('watchmodmarket_ajax', 'nonce');
    
    if (!is_user_logged_in()) {
        wp_send_json_error('You must be logged in to save to your watchlist');
    }
    
    $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
    $user_id = get_current_user_id();
    
    if (!$product_id) {
        wp_send_json_error('Invalid product ID');
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
}
add_action('wp_ajax_add_to_watchlist', 'watchmodmarket_add_to_watchlist');
add_action('wp_ajax_nopriv_add_to_watchlist', 'watchmodmarket_add_to_watchlist');

/**
 * Add schema.org structured data for products
 */
function watchmodmarket_add_product_schema() {
    if (!is_product()) {
        return;
    }
    
    global $product;
    
    // Ensure we have a valid product object
    if (!$product || !is_a($product, 'WC_Product')) {
        return;
    }
    
    $schema = array(
        '@context' => 'https://schema.org',
        '@type' => 'Product',
        'name' => $product->get_name(),
        'description' => $product->get_short_description(),
        'image' => wp_get_attachment_url($product->get_image_id()),
        'sku' => $product->get_sku(),
        'offers' => array(
            '@type' => 'Offer',
            'price' => $product->get_price(),
            'priceCurrency' => get_woocommerce_currency(),
            'availability' => $product->is_in_stock() ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
            'url' => get_permalink($product->get_id())
        )
    );
    
    if ($product->get_average_rating()) {
        $schema['aggregateRating'] = array(
            '@type' => 'AggregateRating',
            'ratingValue' => $product->get_average_rating(),
            'reviewCount' => $product->get_review_count()
        );
    }
    
    echo '<script type="application/ld+json">' . json_encode($schema) . '</script>';
}
add_action('wp_head', 'watchmodmarket_add_product_schema');

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
                'nonce' => wp_create_nonce('watchmodmarket_ajax'),
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

/**
 * Remove WooCommerce default tabs
 */
add_filter('woocommerce_product_tabs', function($tabs) {
    unset($tabs['description']);
    unset($tabs['additional_information']);
    unset($tabs['reviews']);
    return $tabs;
}, 98);

/**
 * Move product rating summary
 */
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10);

/**
 * Remove related products default output
 */
remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20);

/**
 * Modify product review form fields
 */
add_filter('comment_form_fields', function($fields) {
    if (!is_product()) {
        return $fields;
    }
    
    // Reorder fields
    $comment_field = $fields['comment'];
    unset($fields['comment']);
    $fields['comment'] = $comment_field;
    
    return $fields;
});

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

/**
 * Generate responsive image attributes
 */
function watchmodmarket_get_responsive_image($attachment_id, $sizes = array()) {
    $default_sizes = array(
        '(max-width: 600px) 400px',
        '(max-width: 1024px) 800px',
        '1200px'
    );
    
    if (empty($sizes)) {
        $sizes = $default_sizes;
    }
    
    $srcset = wp_get_attachment_image_srcset($attachment_id);
    $sizes_attr = implode(', ', $sizes);
    
    return array(
        'srcset' => $srcset,
        'sizes' => $sizes_attr
    );
}

/**
 * Preload product images for better performance
 * 
 * FIXED: Added check to ensure $product is a WC_Product object before calling get_image_id()
 */
function watchmodmarket_preload_product_images() {
    // Only run on product pages
    if (!is_product()) {
        return;
    }
    
    global $product;
    
    // Check if $product is a WC_Product object
    if (!is_object($product) || !method_exists($product, 'get_image_id')) {
        // Try to get the product from the global post
        global $post;
        if (isset($post->ID)) {
            $product = wc_get_product($post->ID);
        }
        
        // If still not a valid product, exit
        if (!is_object($product) || !method_exists($product, 'get_image_id')) {
            return;
        }
    }
    
    // Get product image ID
    $image_id = $product->get_image_id();
    if (!$image_id) {
        return;
    }
    
    // Get image src
    $image_src = wp_get_attachment_image_src($image_id, 'full');
    if (!$image_src) {
        return;
    }
    
    // Output preload link
    echo '<link rel="preload" href="' . esc_url($image_src[0]) . '" as="image">' . "\n";
    
    // Preload gallery images if available
    $gallery_image_ids = $product->get_gallery_image_ids();
    if (!empty($gallery_image_ids)) {
        foreach ($gallery_image_ids as $gallery_image_id) {
            $gallery_image_src = wp_get_attachment_image_src($gallery_image_id, 'full');
            if ($gallery_image_src) {
                echo '<link rel="preload" href="' . esc_url($gallery_image_src[0]) . '" as="image">' . "\n";
            }
        }
    }
}
add_action('wp_head', 'watchmodmarket_preload_product_images');
/**
 * Add WebP support for product images
 */
function watchmodmarket_enable_webp_uploads() {
    add_filter('mime_types', function($mimes) {
        $mimes['webp'] = 'image/webp';
        return $mimes;
    });
    
    add_filter('file_is_displayable_image', function($result, $path) {
        if (pathinfo($path, PATHINFO_EXTENSION) === 'webp') {
            return true;
        }
        return $result;
    }, 10, 2);
}
add_action('init', 'watchmodmarket_enable_webp_uploads');

/**
 * Generate WebP versions of uploaded images
 */
function watchmodmarket_generate_webp_versions($metadata, $attachment_id) {
    if (!function_exists('imagewebp')) {
        return $metadata;
    }
    
    $mime_type = get_post_mime_type($attachment_id);
    if (strpos($mime_type, 'image/') !== 0 || $mime_type === 'image/webp') {
        return $metadata;
    }
    
    $upload_dir = wp_upload_dir();
    $filepath = $upload_dir['basedir'] . '/' . $metadata['file'];
    
    if (file_exists($filepath)) {
        $webp_filepath = preg_replace('/\.(jpe?g|png)$/i', '.webp', $filepath);
        
        switch ($mime_type) {
            case 'image/jpeg':
                $image = imagecreatefromjpeg($filepath);
                break;
            case 'image/png':
                $image = imagecreatefrompng($filepath);
                break;
            default:
                return $metadata;
        }
        
        if ($image) {
            imagewebp($image, $webp_filepath, 80); // 80% quality
            imagedestroy($image);
            
            // Generate WebP versions for all sizes
            if (!empty($metadata['sizes'])) {
                foreach ($metadata['sizes'] as $size => $size_data) {
                    $size_filepath = dirname($filepath) . '/' . $size_data['file'];
                    $size_webp_filepath = preg_replace('/\.(jpe?g|png)$/i', '.webp', $size_filepath);
                    
                    if (file_exists($size_filepath)) {
                        switch ($mime_type) {
                            case 'image/jpeg':
                                $size_image = imagecreatefromjpeg($size_filepath);
                                break;
                            case 'image/png':
                                $size_image = imagecreatefrompng($size_filepath);
                                break;
                        }
                        
                        if (isset($size_image)) {
                            imagewebp($size_image, $size_webp_filepath, 80);
                            imagedestroy($size_image);
                        }
                    }
                }
            }
        }
    }
    
    return $metadata;
}
add_filter('wp_generate_attachment_metadata', 'watchmodmarket_generate_webp_versions', 10, 2);

/**
 * Add WebP support to image srcset
 */
function watchmodmarket_add_webp_to_srcset($sources, $size_array, $image_src, $image_meta, $attachment_id) {
    if (!function_exists('imagewebp')) {
        return $sources;
    }
    
    foreach ($sources as $width => $source) {
        $webp_url = preg_replace('/\.(jpe?g|png)$/i', '.webp', $source['url']);
        $webp_path = preg_replace('/\.(jpe?g|png)$/i', '.webp', str_replace(wp_upload_dir()['baseurl'], wp_upload_dir()['basedir'], $source['url']));
        
        if (file_exists($webp_path)) {
            $sources[$width]['type'] = 'image/webp';
            $sources[$width]['url'] = $webp_url;
        }
    }
    
    return $sources;
}
add_filter('wp_calculate_image_srcset', 'watchmodmarket_add_webp_to_srcset', 10, 5);

/**
 * Progressive image loading component
 */
function watchmodmarket_progressive_image_loader() {
    ?>
    <script>
    // Progressive image loading
    function loadImage(img, src) {
        return new Promise((resolve, reject) => {
            const tempImg = new Image();
            tempImg.onload = () => {
                img.src = src;
                img.classList.add('loaded');
                resolve();
            };
            tempImg.onerror = reject;
            tempImg.src = src;
        });
    }
    
    // Lazy load images
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    const src = img.dataset.src;
                    
                    if (src) {
                        loadImage(img, src).then(() => {
                            imageObserver.unobserve(img);
                        });
                    }
                }
            });
        }, {
            rootMargin: '50px 0px',
            threshold: 0.01
        });
        
        document.querySelectorAll('img[data-src]').forEach(img => {
            imageObserver.observe(img);
        });
        
        // Preload main product image
        const mainImage = document.querySelector('.current-product-image');
        if (mainImage && mainImage.dataset.src) {
            loadImage(mainImage, mainImage.dataset.src);
        }
    }
    
    // Add image load event handlers
    document.addEventListener('DOMContentLoaded', () => {
        const images = document.querySelectorAll('.product-image img');
        
        images.forEach(img => {
            if (img.complete) {
                img.classList.add('loaded');
            } else {
                img.addEventListener('load', () => {
                    img.classList.add('loaded');
                });
            }
        });
    });
    </script>
    
    <style>
    /* Progressive image loading styles */
    .product-image img {
        opacity: 0;
        transition: opacity 0.3s ease;
        background-color: var(--color-light);
    }
    
    .product-image img.loaded {
        opacity: 1;
    }
    
    /* Blur effect for thumbnails */
    .product-thumbnail img {
        filter: blur(5px);
        transition: filter 0.3s ease;
    }
    
    .product-thumbnail img.loaded {
        filter: none;
    }
    
    /* Loading placeholder */
    .image-loading-placeholder {
        background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
        background-size: 200% 100%;
        animation: loading 1.5s infinite;
    }
    
    @keyframes loading {
        0% {
            background-position: 200% 0;
        }
        100% {
            background-position: -200% 0;
        }
    }
    </style>
    <?php
}
add_action('wp_footer', 'watchmodmarket_progressive_image_loader');

/**
 * Add AVIF support (if available)
 */
function watchmodmarket_enable_avif_support() {
    // Check if AVIF is supported by PHP
    if (function_exists('imageavif')) {
        add_filter('mime_types', function($mimes) {
            $mimes['avif'] = 'image/avif';
            return $mimes;
        });
        
        add_filter('file_is_displayable_image', function($result, $path) {
            if (pathinfo($path, PATHINFO_EXTENSION) === 'avif') {
                return true;
            }
            return $result;
        }, 10, 2);
    }
}
add_action('init', 'watchmodmarket_enable_avif_support');

/**
 * Add image optimization utility class
 */
class Watchmodmarket_Image_Optimizer {
    
    /**
     * Optimize image for display
     */
    public static function get_optimized_image($attachment_id, $size = 'full', $attr = array()) {
        $default_attr = array(
            'loading' => 'lazy',
            'decoding' => 'async'
        );
        
        $attr = array_merge($default_attr, $attr);
        
        // Check if browser supports modern formats
        $supports_avif = strpos($_SERVER['HTTP_ACCEPT'] ?? '', 'image/avif') !== false;
        $supports_webp = strpos($_SERVER['HTTP_ACCEPT'] ?? '', 'image/webp') !== false;
        
        // Get image sources
        $sources = array();
        $original_url = wp_get_attachment_image_url($attachment_id, $size);
        
        if ($supports_avif) {
            $avif_url = preg_replace('/\.(jpe?g|png|webp)$/i', '.avif', $original_url);
            $avif_path = str_replace(wp_upload_dir()['baseurl'], wp_upload_dir()['basedir'], $avif_url);
            
            if (file_exists($avif_path)) {
                $sources[] = array(
                    'url' => $avif_url,
                    'type' => 'image/avif'
                );
            }
        }
        
        if ($supports_webp) {
            $webp_url = preg_replace('/\.(jpe?g|png)$/i', '.webp', $original_url);
            $webp_path = str_replace(wp_upload_dir()['baseurl'], wp_upload_dir()['basedir'], $webp_url);
            
            if (file_exists($webp_path)) {
                $sources[] = array(
                    'url' => $webp_url,
                    'type' => 'image/webp'
                );
            }
        }
        
        // Add original as fallback
        $sources[] = array(
            'url' => $original_url,
            'type' => wp_get_attachment_mime_type($attachment_id)
        );
        
        // Generate picture element
        $picture = '<picture>';
        foreach ($sources as $source) {
            if ($source['type'] !== 'image/jpeg' && $source['type'] !== 'image/png') {
                $picture .= sprintf('<source srcset="%s" type="%s">', esc_url($source['url']), esc_attr($source['type']));
            }
        }
        
        // Add final img tag
        $img_attrs = '';
        foreach ($attr as $key => $value) {
            $img_attrs .= sprintf(' %s="%s"', $key, esc_attr($value));
        }
        
        $picture .= sprintf('<img src="%s" alt="%s"%s>', 
            esc_url($original_url), 
            esc_attr(get_post_meta($attachment_id, '_wp_attachment_image_alt', true)), 
            $img_attrs
        );
        
        $picture .= '</picture>';
        
        return $picture;
    }
    
    /**
     * Get optimized image URL
     */
    public static function get_optimized_url($attachment_id, $size = 'full') {
        $original_url = wp_get_attachment_image_url($attachment_id, $size);
        
        // Check for WebP version
        $webp_url = preg_replace('/\.(jpe?g|png)$/i', '.webp', $original_url);
        $webp_path = str_replace(wp_upload_dir()['baseurl'], wp_upload_dir()['basedir'], $webp_url);
        
        if (file_exists($webp_path)) {
            return $webp_url;
        }
        
        return $original_url;
    }
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

wp_enqueue_script('watchmodmarket-ajax-handler', get_template_directory_uri() . '/js/ajax-handler.js', array(), '1.0.0', true);

/**
 * Required functions for Group Buy Page template
 * Add these to your theme's functions.php file
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
    $default_savings = '$24,780';
    
    // If you want to implement actual calculation:
    /*
    $total_savings = 0;
    
    $args = array(
        'post_type'      => 'product',
        'posts_per_page' => -1,
        'meta_query'     => array(
            array(
                'key'     => '_groupbuy_status',
                'value'   => 'active',
                'compare' => '=',
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
        while ($query->have_posts()) {
            $query->the_post();
            global $product;
            
            $regular_price = $product->get_regular_price();
            $group_price = get_post_meta(get_the_ID(), '_groupbuy_price', true);
            $slots_filled = get_post_meta(get_the_ID(), '_groupbuy_slots_filled', true);
            
            // Calculate savings per product
            $savings_per_unit = $regular_price - $group_price;
            $total_savings += $savings_per_unit * $slots_filled;
        }
    }
    
    wp_reset_postdata();
    
    return wc_price($total_savings);
    */
    
    return $default_savings;
}

/**
 * Get the success rate of group buy campaigns
 *
 * @return string Success rate percentage
 */
function watchmodmarket_get_success_rate() {
    // Default value if actual calculation isn't possible yet
    return '98%';
    
    // If you want to implement actual calculation:
    /*
    $args = array(
        'post_type'      => 'product',
        'posts_per_page' => -1,
        'meta_query'     => array(
            array(
                'key'     => '_groupbuy_status',
                'value'   => array('completed', 'failed'),
                'compare' => 'IN',
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
    
    $total_campaigns = 0;
    $successful_campaigns = 0;
    
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $total_campaigns++;
            
            $status = get_post_meta(get_the_ID(), '_groupbuy_status', true);
            if ($status === 'completed') {
                $successful_campaigns++;
            }
        }
    }
    
    wp_reset_postdata();
    
    if ($total_campaigns > 0) {
        $success_rate = ($successful_campaigns / $total_campaigns) * 100;
        return round($success_rate) . '%';
    }
    
    return '0%';
    */
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
 * AJAX handler for quick view
 */
function watchmodmarket_quick_view_ajax() {
    // Check nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'watchmodmarket_nonce')) {
        wp_send_json_error(array('message' => 'Invalid nonce'));
    }
    
    // Check product ID
    if (!isset($_POST['product_id']) || empty($_POST['product_id'])) {
        wp_send_json_error(array('message' => 'Product ID is required'));
    }
    
    $product_id = absint($_POST['product_id']);
    $product = wc_get_product($product_id);
    
    if (!$product) {
        wp_send_json_error(array('message' => 'Product not found'));
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
add_action('wp_ajax_watchmodmarket_quick_view', 'watchmodmarket_quick_view_ajax');
add_action('wp_ajax_nopriv_watchmodmarket_quick_view', 'watchmodmarket_quick_view_ajax');

/**
 * AJAX handler for adding to cart
 */
function watchmodmarket_add_to_cart_ajax() {
    // Check nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'watchmodmarket_nonce')) {
        wp_send_json_error(array('message' => 'Invalid nonce'));
    }
    
    // Check product ID
    if (!isset($_POST['product_id']) || empty($_POST['product_id'])) {
        wp_send_json_error(array('message' => 'Product ID is required'));
    }
    
    $product_id = absint($_POST['product_id']);
    $quantity = isset($_POST['quantity']) ? absint($_POST['quantity']) : 1;
    
    // Add to cart
    $added = WC()->cart->add_to_cart($product_id, $quantity);
    
    if (!$added) {
        wp_send_json_error(array('message' => 'Error adding product to cart'));
    }
    
    // Get cart count
    $cart_count = WC()->cart->get_cart_contents_count();
    
    wp_send_json_success(array('cart_count' => $cart_count));
}
add_action('wp_ajax_watchmodmarket_add_to_cart', 'watchmodmarket_add_to_cart_ajax');
add_action('wp_ajax_nopriv_watchmodmarket_add_to_cart', 'watchmodmarket_add_to_cart_ajax');

/**
 * AJAX handler for adding to wishlist
 * Note: This is a simple implementation. You may need to adjust it based on your wishlist system.
 */
function watchmodmarket_add_to_wishlist_ajax() {
    // Check nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'watchmodmarket_nonce')) {
        wp_send_json_error(array('message' => 'Invalid nonce'));
    }
    
    // Check if user is logged in
    if (!is_user_logged_in()) {
        wp_send_json_error(array('message' => 'User must be logged in to add to wishlist'));
    }
    
    // Check product ID
    if (!isset($_POST['product_id']) || empty($_POST['product_id'])) {
        wp_send_json_error(array('message' => 'Product ID is required'));
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
}
add_action('wp_ajax_watchmodmarket_add_to_wishlist', 'watchmodmarket_add_to_wishlist_ajax');

/**
 * AJAX handler for removing from wishlist
 */
function watchmodmarket_remove_from_wishlist_ajax() {
    // Check nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'watchmodmarket_nonce')) {
        wp_send_json_error(array('message' => 'Invalid nonce'));
    }
    
    // Check if user is logged in
    if (!is_user_logged_in()) {
        wp_send_json_error(array('message' => 'User must be logged in to remove from wishlist'));
    }
    
    // Check product ID
    if (!isset($_POST['product_id']) || empty($_POST['product_id'])) {
        wp_send_json_error(array('message' => 'Product ID is required'));
    }
    
    $product_id = absint($_POST['product_id']);
    $user_id = get_current_user_id();
    
    // Get current wishlist
    $wishlist = get_user_meta($user_id, 'watchmodmarket_wishlist', true);
    
    if (is_array($wishlist)) {
        // Remove product from wishlist
        $wishlist = array_diff($wishlist, array($product_id));
        update_user_meta($user_id, 'watchmodmarket_wishlist', $wishlist);
    }
    
    wp_send_json_success();
}
add_action('wp_ajax_watchmodmarket_remove_from_wishlist', 'watchmodmarket_remove_from_wishlist_ajax');

/**
 * AJAX handler for checking wishlist status
 */
function watchmodmarket_check_wishlist_ajax() {
    // Check nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'watchmodmarket_nonce')) {
        wp_send_json_error(array('message' => 'Invalid nonce'));
    }
    
    // Check if user is logged in
    if (!is_user_logged_in()) {
        wp_send_json_error(array('message' => 'User must be logged in to check wishlist'));
    }
    
    // Check product IDs
    if (!isset($_POST['product_ids']) || empty($_POST['product_ids'])) {
        wp_send_json_error(array('message' => 'Product IDs are required'));
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
}
add_action('wp_ajax_watchmodmarket_check_wishlist', 'watchmodmarket_check_wishlist_ajax');

/**
 * Handle newsletter signup
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
    
    // Process signup (example implementation)
    // In a real implementation, you might want to save to a database or send to a mailing service like Mailchimp
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
 * Localize AJAX variables for the Group Buy page
 */
function watchmodmarket_group_buy_scripts() {
    if (is_page_template('page-group-buy.php')) {
        wp_localize_script('group-buy-script', 'watchmodmarket_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('watchmodmarket_nonce'),
        ));
    }
}
add_action('wp_enqueue_scripts', 'watchmodmarket_group_buy_scripts', 20);