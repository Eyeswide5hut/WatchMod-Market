<?php
/**
 * WooCommerce Hooks for WatchModMarket Theme
 *
 * @package WatchModMarket
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Remove default WooCommerce wrappers
 */
remove_action('woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
remove_action('woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);

/**
 * Add custom wrappers
 */
add_action('woocommerce_before_main_content', 'watchmodmarket_wrapper_start', 10);
add_action('woocommerce_after_main_content', 'watchmodmarket_wrapper_end', 10);

function watchmodmarket_wrapper_start() {
    echo '<main id="primary" class="site-main woocommerce-main">';
    echo '<div class="container">';
}

function watchmodmarket_wrapper_end() {
    echo '</div><!-- .container -->';
    echo '</main><!-- #primary -->';
}

/**
 * Remove default WooCommerce styles
 */
add_filter('woocommerce_enqueue_styles', '__return_empty_array');

/**
 * Custom shop loop columns
 */
function watchmodmarket_loop_columns() {
    return 3; // Display 3 products per row
}
add_filter('loop_shop_columns', 'watchmodmarket_loop_columns');

/**
 * Remove unnecessary elements
 */
// Remove default WooCommerce product count and showing results
remove_action('woocommerce_before_shop_loop', 'woocommerce_result_count', 20);
remove_action('woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30);

// Remove default WooCommerce page title
add_filter('woocommerce_show_page_title', '__return_false');

/**
 * Add custom shop title and filter section
 */
function watchmodmarket_shop_header() {
    if (is_shop() || is_product_category() || is_product_tag()) {
        ?>
        <div class="shop-header">
            <h1 class="shop-title">
                <?php
                if (is_product_category()) {
                    single_term_title();
                } elseif (is_product_tag()) {
                    single_term_title();
                } elseif (is_search()) {
                    /* translators: %s: search query */
                    printf(esc_html__('Search Results for: %s', 'watchmodmarket'), '<span>' . get_search_query() . '</span>');
                } else {
                    esc_html_e('Premium Watch Parts & Accessories', 'watchmodmarket');
                }
                ?>
            </h1>
            <?php
            if (is_product_category() || is_product_tag()) {
                the_archive_description('<div class="shop-description">', '</div>');
            } else {
                echo '<div class="shop-description">';
                echo esc_html__('Professional grade components for watch enthusiasts and horologists', 'watchmodmarket');
                echo '</div>';
            }
            ?>
        </div>
        <?php
    }
}
add_action('woocommerce_before_main_content', 'watchmodmarket_shop_header', 15);

/**
 * Add custom filter section before shop loop
 */
function watchmodmarket_shop_filters() {
    if (is_shop() || is_product_category() || is_product_tag()) {
        get_template_part('template-parts/shop/filters');
    }
}
add_action('woocommerce_before_shop_loop', 'watchmodmarket_shop_filters', 15);

/**
 * Customize WooCommerce product display
 */
function watchmodmarket_woocommerce_product_card() {
    // Remove default WooCommerce product title
    remove_action('woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title', 10);
    
    // Add custom product title with design
    add_action('woocommerce_shop_loop_item_title', 'watchmodmarket_template_loop_product_title', 10);
    
    // Remove default WooCommerce product thumbnail
    remove_action('woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10);
    
    // Add custom product thumbnail with design
    add_action('woocommerce_before_shop_loop_item_title', 'watchmodmarket_template_loop_product_thumbnail', 10);
    
    // Remove default WooCommerce product price
    remove_action('woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10);
    
    // Add custom product price with design
    add_action('woocommerce_after_shop_loop_item_title', 'watchmodmarket_template_loop_price', 10);
    
    // Remove default WooCommerce add to cart button
    remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10);
    
    // Add custom add to cart button with design
    add_action('woocommerce_after_shop_loop_item', 'watchmodmarket_template_loop_add_to_cart', 10);
}
add_action('init', 'watchmodmarket_woocommerce_product_card');

/**
 * Custom product title
 */
function watchmodmarket_template_loop_product_title() {
    echo '<h2 class="woocommerce-loop-product__title">' . get_the_title() . '</h2>';
}

/**
 * Custom product thumbnail
 */
function watchmodmarket_template_loop_product_thumbnail() {
    global $product;
    
    echo '<div class="product-image-container">';
    
    echo '<a href="' . esc_url(get_permalink()) . '" class="product-image-link">';
    
    if (has_post_thumbnail()) {
        the_post_thumbnail('woocommerce_thumbnail', array('class' => 'product-thumbnail'));
    } else {
        echo '<img src="' . esc_url(wc_placeholder_img_src()) . '" alt="' . esc_attr__('Product Image', 'watchmodmarket') . '" class="product-thumbnail woocommerce-placeholder" />';
    }
    
    echo '</a>';
    
    // Product badges
    if ($product->is_on_sale()) {
        echo '<div class="product-badge sale">' . esc_html__('Sale', 'watchmodmarket') . '</div>';
    } elseif ($product->is_featured()) {
        echo '<div class="product-badge featured">' . esc_html__('Featured', 'watchmodmarket') . '</div>';
    } elseif (get_post_meta($product->get_id(), '_groupbuy_status', true) === 'active') {
        echo '<div class="product-badge group-buy">' . esc_html__('Group Buy', 'watchmodmarket') . '</div>';
    }
    
    echo '<div class="product-actions">';
    echo '<a href="#" class="quick-view" data-product-id="' . esc_attr($product->get_id()) . '">' . esc_html__('Quick View', 'watchmodmarket') . '</a>';
    echo '<a href="#" class="add-to-wishlist" data-product-id="' . esc_attr($product->get_id()) . '">' . esc_html__('Wishlist', 'watchmodmarket') . '</a>';
    echo '</div>';
    
    echo '</div>';
}

/**
 * Custom product price
 */
function watchmodmarket_template_loop_price() {
    global $product;
    
    // Display product meta info
    echo '<div class="product-meta">';
    
    // Show product categories
    echo '<div class="product-categories">';
    echo wc_get_product_category_list($product->get_id(), ', ', '<span class="product-category">', '</span>');
    echo '</div>';
    
    // Display rating if enabled
    if (wc_review_ratings_enabled()) {
        echo wc_get_rating_html($product->get_average_rating());
    }
    
    // Price
    echo '<div class="price-container">';
    echo $product->get_price_html();
    echo '</div>';
    
    echo '</div>';
}

/**
 * Custom add to cart button
 */
function watchmodmarket_template_loop_add_to_cart() {
    global $product;
    
    echo '<div class="product-footer">';
    
    // View product link
    echo '<a href="' . esc_url(get_permalink()) . '" class="btn btn-secondary view-product">' . esc_html__('View Details', 'watchmodmarket') . '</a>';
    
    // Add to cart button (only if product is purchasable)
    if ($product->is_purchasable() && $product->is_in_stock()) {
        echo '<a href="' . esc_url($product->add_to_cart_url()) . '" data-quantity="1" class="btn btn-primary add-to-cart ajax_add_to_cart" data-product_id="' . esc_attr($product->get_id()) . '" data-product_sku="' . esc_attr($product->get_sku()) . '">' . esc_html__('Add to Cart', 'watchmodmarket') . '</a>';
    }
    
    echo '</div>';
}

/**
 * Customize single product page
 */
function watchmodmarket_single_product_customization() {
    // Remove default WooCommerce tabs
    remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10);
    
    // Add custom tabs layout
    add_action('woocommerce_after_single_product_summary', 'watchmodmarket_product_tabs', 10);
    
    // Move product rating
    remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10);
    add_action('woocommerce_single_product_summary', 'woocommerce_template_single_rating', 5);
    
    // Add custom meta content
    add_action('woocommerce_single_product_summary', 'watchmodmarket_product_meta_content', 25);
    
    // Remove default related products
    remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20);
    
    // Add custom related products
    add_action('woocommerce_after_single_product_summary', 'watchmodmarket_related_products', 20);
    
    // Add recently viewed section
    add_action('woocommerce_after_single_product_summary', 'watchmodmarket_recently_viewed', 30);
}
add_action('init', 'watchmodmarket_single_product_customization');

/**
 * Custom product tabs
 */
function watchmodmarket_product_tabs() {
    global $product;
    
    // Set up tabs array
    $tabs = array(
        'description' => array(
            'title' => __('Description', 'watchmodmarket'),
            'callback' => 'woocommerce_product_description_tab',
            'priority' => 10,
        ),
        'specifications' => array(
            'title' => __('Specifications', 'watchmodmarket'),
            'callback' => 'watchmodmarket_product_specifications_tab',
            'priority' => 20,
        ),
        'compatibility' => array(
            'title' => __('Compatibility', 'watchmodmarket'),
            'callback' => 'watchmodmarket_product_compatibility_tab',
            'priority' => 30,
        ),
        'reviews' => array(
            'title' => sprintf(__('Reviews (%d)', 'watchmodmarket'), $product->get_review_count()),
            'callback' => 'comments_template',
            'priority' => 40,
        ),
    );
    
    // Allow filtering of tabs
    $tabs = apply_filters('watchmodmarket_product_tabs', $tabs);
    
    // // Sort tabs by priority
    uasort($tabs, function($a, $b) {
        return $a['priority'] - $b['priority'];
    });
    
    // Output tabs
    if (!empty($tabs)) {
        ?>
        <div class="product-tabs-wrapper">
            <div class="product-tabs">
                <?php 
                $first = true;
                foreach ($tabs as $key => $tab) : 
                ?>
                    <button class="tab-button <?php echo $first ? 'active' : ''; ?>" data-tab="<?php echo esc_attr($key); ?>">
                        <?php echo esc_html($tab['title']); ?>
                    </button>
                <?php 
                    $first = false;
                endforeach; 
                ?>
            </div>

            <?php 
            $first = true;
            foreach ($tabs as $key => $tab) : 
            ?>
                <div class="tab-content <?php echo $first ? 'active' : ''; ?>" id="tab-<?php echo esc_attr($key); ?>">
                    <?php
                    if (isset($tab['callback']) && is_callable($tab['callback'])) {
                        call_user_func($tab['callback']);
                    }
                    ?>
                </div>
            <?php 
                $first = false;
            endforeach; 
            ?>
        </div>
        <?php
    }
}

/**
 * Product specifications tab content
 */
function watchmodmarket_product_specifications_tab() {
    global $product;
    
    // Get product attributes
    $attributes = $product->get_attributes();
    
    if (empty($attributes)) {
        echo '<p>' . esc_html__('No specifications available for this product.', 'watchmodmarket') . '</p>';
        return;
    }
    
    echo '<table class="specifications-table">';
    echo '<tbody>';
    
    foreach ($attributes as $attribute) {
        if ($attribute->get_visible()) {
            $name = wc_attribute_label($attribute->get_name());
            $values = array();
            
            if ($attribute->is_taxonomy()) {
                $attribute_terms = wc_get_product_terms($product->get_id(), $attribute->get_name(), array('fields' => 'all'));
                foreach ($attribute_terms as $term) {
                    $values[] = $term->name;
                }
            } else {
                $values = $attribute->get_options();
            }
            
            if (!empty($values)) {
                echo '<tr>';
                echo '<th>' . esc_html($name) . '</th>';
                echo '<td>' . esc_html(implode(', ', $values)) . '</td>';
                echo '</tr>';
            }
        }
    }
    
    // Add additional custom meta fields if they exist
    $custom_meta_fields = array(
        '_case_diameter' => __('Case Diameter', 'watchmodmarket'),
        '_dial_diameter' => __('Dial Diameter', 'watchmodmarket'),
        '_movement_type' => __('Movement Type', 'watchmodmarket'),
        '_part_dimensions' => __('Dimensions', 'watchmodmarket'),
        '_part_material' => __('Material', 'watchmodmarket'),
    );
    
    foreach ($custom_meta_fields as $field => $label) {
        $value = get_post_meta($product->get_id(), $field, true);
        if (!empty($value)) {
            echo '<tr>';
            echo '<th>' . esc_html($label) . '</th>';
            echo '<td>' . esc_html($value) . '</td>';
            echo '</tr>';
        }
    }
    
    echo '</tbody>';
    echo '</table>';
}

/**
 * Product compatibility tab content
 */
function watchmodmarket_product_compatibility_tab() {
    global $product;
    
    // Get compatibility information
    $compatibility = get_post_meta($product->get_id(), '_part_compatibility', true);
    $compatible_parts = get_post_meta($product->get_id(), '_compatible_parts', true);
    
    if (empty($compatibility) && empty($compatible_parts)) {
        echo '<p>' . esc_html__('No compatibility information available for this product.', 'watchmodmarket') . '</p>';
        return;
    }
    
    // Display compatibility information
    if (!empty($compatibility)) {
        echo '<div class="compatibility-info">';
        echo '<h3>' . esc_html__('Compatible With', 'watchmodmarket') . '</h3>';
        echo '<div class="compatibility-description">' . wp_kses_post(wpautop($compatibility)) . '</div>';
        echo '</div>';
    }
    
    // Display compatible products
    if (!empty($compatible_parts)) {
        echo '<div class="compatible-products">';
        echo '<h3>' . esc_html__('Recommended Compatible Parts', 'watchmodmarket') . '</h3>';
        
        // Convert comma-separated IDs to array
        $part_ids = array_map('trim', explode(',', $compatible_parts));
        
        if (!empty($part_ids)) {
            echo '<div class="compatible-products-grid">';
            
            foreach ($part_ids as $part_id) {
                $compatible_product = wc_get_product($part_id);
                
                if ($compatible_product && $compatible_product->is_visible()) {
                    echo '<div class="compatible-product">';
                    echo '<a href="' . esc_url(get_permalink($part_id)) . '" class="compatible-product-link">';
                    
                    // Product thumbnail
                    echo '<div class="compatible-product-image">';
                    if (has_post_thumbnail($part_id)) {
                        echo get_the_post_thumbnail($part_id, 'thumbnail');
                    } else {
                        echo wc_placeholder_img('thumbnail');
                    }
                    echo '</div>';
                    
                    // Product info
                    echo '<div class="compatible-product-info">';
                    echo '<h4>' . get_the_title($part_id) . '</h4>';
                    echo '<span class="compatible-product-price">' . $compatible_product->get_price_html() . '</span>';
                    echo '</div>';
                    
                    echo '</a>';
                    echo '</div>';
                }
            }
            
            echo '</div>';
        }
        
        echo '</div>';
    }
}

/**
 * Add custom meta content to product summary
 */
function watchmodmarket_product_meta_content() {
    global $product;
    
    echo '<div class="product-meta-content">';
    
    // Display compatibility badges
    $compatible_brands = array();
    $compatibility_terms = get_the_terms($product->get_id(), 'pa_compatible-with');
    
    if (!empty($compatibility_terms) && !is_wp_error($compatibility_terms)) {
        echo '<div class="compatibility-badges">';
        echo '<h4>' . esc_html__('Compatible With:', 'watchmodmarket') . '</h4>';
        
        foreach ($compatibility_terms as $term) {
            echo '<span class="compatibility-badge">' . esc_html($term->name) . '</span>';
        }
        
        echo '</div>';
    }
    
    // Display SKU
    if ($product->get_sku()) {
        echo '<div class="product-sku">';
        echo '<span class="sku-label">' . esc_html__('SKU:', 'watchmodmarket') . '</span>';
        echo '<span class="sku-value">' . esc_html($product->get_sku()) . '</span>';
        echo '</div>';
    }
    
    // Display categories
    echo '<div class="product-categories-meta">';
    echo wc_get_product_category_list($product->get_id(), ', ', '<span class="posted-in">' . esc_html__('Category:', 'watchmodmarket') . ' ', '</span>');
    echo '</div>';
    
    // Display tags
    echo '<div class="product-tags-meta">';
    echo wc_get_product_tag_list($product->get_id(), ', ', '<span class="tagged-as">' . esc_html__('Tags:', 'watchmodmarket') . ' ', '</span>');
    echo '</div>';
    
    echo '</div>';
}

/**
 * Custom related products display
 */
function watchmodmarket_related_products() {
    global $product;
    
    // Get related products
    $related_products = array();
    
    // First try to get compatible parts if they exist
    $compatible_parts = get_post_meta($product->get_id(), '_compatible_parts', true);
    
    if (!empty($compatible_parts)) {
        $compatible_ids = array_map('trim', explode(',', $compatible_parts));
        $related_products = $compatible_ids;
    }
    
    // If no compatible parts, use WooCommerce's related products function
    if (empty($related_products)) {
        $related_limit = 4;
        $related_ids = wc_get_related_products($product->get_id(), $related_limit);
        $related_products = $related_ids;
    }
    
    // Remove duplicate IDs and limit to 4 products
    $related_products = array_unique(array_slice($related_products, 0, 4));
    
    if (!empty($related_products)) {
        ?>
        <section class="related-products">
            <h2><?php esc_html_e('Related Products', 'watchmodmarket'); ?></h2>
            
            <div class="products related-grid columns-4">
                <?php
                foreach ($related_products as $related_product_id) {
                    $related_product = wc_get_product($related_product_id);
                    
                    if ($related_product && $related_product->is_visible()) {
                        // Set the global product variable for template functions
                        global $post;
                        $post = get_post($related_product_id);
                        setup_postdata($post);
                        
                        // Display product
                        wc_get_template_part('content', 'product');
                    }
                }
                wp_reset_postdata();
                ?>
            </div>
        </section>
        <?php
    }
}

/**
 * Recently viewed products
 */
function watchmodmarket_recently_viewed() {
    // Only show on single product pages
    if (!is_product()) {
        return;
    }
    
    // Get current product
    global $post;
    $current_product_id = $post->ID;
    
    // Get recently viewed products from cookie
    $viewed_products = array();
    
    if (isset($_COOKIE['woocommerce_recently_viewed'])) {
        $viewed_products = (array) explode('|', $_COOKIE['woocommerce_recently_viewed']);
    }
    
    // Remove current product
    $viewed_products = array_diff($viewed_products, array($current_product_id));
    
    // Show only if we have at least one product
    if (empty($viewed_products)) {
        return;
    }
    
    // Limit to 4 products
    $viewed_products = array_slice($viewed_products, 0, 4);
    
    ?>
    <section class="recently-viewed-products">
        <h2><?php esc_html_e('Recently Viewed', 'watchmodmarket'); ?></h2>
        
        <div class="products recently-viewed-grid columns-4">
            <?php
            foreach ($viewed_products as $product_id) {
                $product = wc_get_product($product_id);
                
                if ($product && $product->is_visible()) {
                    // Set the global product variable for template functions
                    global $post;
                    $post = get_post($product_id);
                    setup_postdata($post);
                    
                    // Display product
                    wc_get_template_part('content', 'product');
                }
            }
            wp_reset_postdata();
            ?>
        </div>
    </section>
    <?php
}

/**
 * Track recently viewed products
 */
function watchmodmarket_track_product_view() {
    if (!is_singular('product')) {
        return;
    }
    
    global $post;
    
    if (empty($_COOKIE['woocommerce_recently_viewed'])) {
        $viewed_products = array();
    } else {
        $viewed_products = (array) explode('|', $_COOKIE['woocommerce_recently_viewed']);
    }
    
    // Remove current product from the array
    $viewed_products = array_diff($viewed_products, array($post->ID));
    
    // Add it to the start of the array
    array_unshift($viewed_products, $post->ID);
    
    // Keep only the 15 most recent
    $viewed_products = array_slice($viewed_products, 0, 15);
    
    // Store in cookie
    wc_setcookie('woocommerce_recently_viewed', implode('|', $viewed_products));
}
add_action('template_redirect', 'watchmodmarket_track_product_view', 20);

/**
 * Add "View in Builder" button to products
 */
function watchmodmarket_builder_button() {
    global $product;
    
    // Only add to relevant product categories
    $categories = array('cases', 'dials', 'hands', 'straps', 'movements');
    $product_categories = wp_get_post_terms($product->get_id(), 'product_cat', array('fields' => 'slugs'));
    $should_show = false;
    
    foreach ($categories as $category) {
        if (in_array($category, $product_categories)) {
            $should_show = true;
            break;
        }
    }
    
    if ($should_show) {
        echo '<a href="' . esc_url(home_url('/builder/?part=' . $product->get_id())) . '" class="btn btn-secondary view-in-builder">' . esc_html__('View in Builder', 'watchmodmarket') . '</a>';
    }
}
add_action('woocommerce_single_product_summary', 'watchmodmarket_builder_button', 35);

/**
 * Custom add to cart button text
 */
function watchmodmarket_add_to_cart_text($text, $product) {
    // Custom text for group buy products
    if (get_post_meta($product->get_id(), '_groupbuy_status', true) === 'active') {
        return __('Join Group Buy', 'watchmodmarket');
    }
    
    return $text;
}
add_filter('woocommerce_product_single_add_to_cart_text', 'watchmodmarket_add_to_cart_text', 10, 2);
add_filter('woocommerce_product_add_to_cart_text', 'watchmodmarket_add_to_cart_text', 10, 2);

/**
 * Fix WooCommerce pagination
 */
function watchmodmarket_woocommerce_pagination_args($args) {
    $args['prev_text'] = __('Previous', 'watchmodmarket');
    $args['next_text'] = __('Next', 'watchmodmarket');
    $args['end_size'] = 2;
    $args['mid_size'] = 2;
    
    return $args;
}
add_filter('woocommerce_pagination_args', 'watchmodmarket_woocommerce_pagination_args');

/**
 * Add AJAX add to cart script
 */
function watchmodmarket_add_to_cart_script() {
    if (function_exists('is_product') && (is_shop() || is_product_category() || is_product_tag() || is_product())) {
        wp_enqueue_script('watchmodmarket-ajax-add-to-cart', get_template_directory_uri() . '/assets/js/ajax-add-to-cart.js', array('jquery'), '', true);
        
        wp_localize_script('watchmodmarket-ajax-add-to-cart', 'watchmodmarket_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('watchmodmarket_ajax'),
            'i18n' => array(
                'adding' => esc_html__('Adding...', 'watchmodmarket'),
                'added' => esc_html__('Added!', 'watchmodmarket'),
                'error' => esc_html__('Error', 'watchmodmarket'),
            )
        ));
    }
}
add_action('wp_enqueue_scripts', 'watchmodmarket_add_to_cart_script');

/**
 * Update cart fragment
 */
function watchmodmarket_add_to_cart_fragments($fragments) {
    $cart_count = WC()->cart->get_cart_contents_count();
    
    $fragments['.cart-count'] = '<span class="cart-count">' . esc_html($cart_count) . '</span>';
    
    return $fragments;
}
add_filter('woocommerce_add_to_cart_fragments', 'watchmodmarket_add_to_cart_fragments');