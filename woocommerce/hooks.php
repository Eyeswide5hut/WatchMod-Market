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
 * Remove unnecessary elements
 */
// Remove default WooCommerce product count and showing results
remove_action('woocommerce_before_shop_loop', 'woocommerce_result_count', 20);
remove_action('woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30);

// Remove default WooCommerce page title
add_filter('woocommerce_show_page_title', '__return_false');

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
    echo '<h3 class="product-card-title">' . get_the_title() . '</h3>';
}

/**
 * Custom product thumbnail
 */
function watchmodmarket_template_loop_product_thumbnail() {
    global $product;
    $image_id = $product->get_image_id();
    
    echo '<div class="product-image">';
    
    if ($image_id) {
        echo wp_get_attachment_image($image_id, 'woocommerce_thumbnail', false, array('class' => 'product-thumbnail fade-in-image'));
    } else {
        echo wc_placeholder_img(array('class' => 'product-thumbnail'));
    }
    
    echo '<div class="product-actions">';
    echo '<button class="quick-view" data-product-id="' . esc_attr($product->get_id()) . '">' . esc_html__('Quick View', 'watchmodmarket') . '</button>';
    
    if ($product->is_purchasable() && $product->is_in_stock()) {
        echo '<button class="add-to-cart" data-product-id="' . esc_attr($product->get_id()) . '" data-product-name="' . esc_attr($product->get_name()) . '" data-product-price="' . esc_attr($product->get_price()) . '">' . esc_html__('Add to Cart', 'watchmodmarket') . '</button>';
    }
    
    echo '</div>'; // End product-actions
    
    // Add product badges
    if ($product->is_on_sale()) {
        echo '<div class="product-badge sale">' . esc_html__('Sale', 'watchmodmarket') . '</div>';
    } elseif ($product->is_featured()) {
        echo '<div class="product-badge best-seller">' . esc_html__('Best Seller', 'watchmodmarket') . '</div>';
    } elseif (has_term('new', 'product_tag', $product->get_id())) {
        echo '<div class="product-badge new">' . esc_html__('New', 'watchmodmarket') . '</div>';
    }
    
    echo '</div>'; // End product-image
}

/**
 * Custom product price
 */
function watchmodmarket_template_loop_price() {
    global $product;
    
    echo '<div class="product-meta">';
    
    // Rating
    if (wc_review_ratings_enabled()) {
        $rating_count = $product->get_rating_count();
        $review_count = $product->get_review_count();
        $average = $product->get_average_rating();
        
        echo '<div class="product-rating">';
        if ($rating_count > 0) {
            echo wc_get_rating_html($average, $rating_count);
            echo '<span class="review-count">(' . esc_html($review_count) . ')</span>';
        } else {
            echo '<span class="no-rating">' . esc_html__('No Reviews', 'watchmodmarket') . '</span>';
        }
        echo '</div>';
    }
    
    // Price
    echo '<div class="product-price">' . $product->get_price_html() . '</div>';
    
    echo '</div>'; // End product-meta
}

/**
 * Custom add to cart button
 */
function watchmodmarket_template_loop_add_to_cart() {
    global $product;
    
    echo '<div class="product-card-actions">';
    echo '<a href="' . esc_url(get_permalink($product->get_id())) . '" class="btn btn-secondary">' . esc_html__('View Details', 'watchmodmarket') . '</a>';
    
    if ($product->is_purchasable() && $product->is_in_stock()) {
        echo '<button class="btn btn-primary add-to-cart" data-product-id="' . esc_attr($product->get_id()) . '" data-product-name="' . esc_attr($product->get_name()) . '" data-product-price="' . esc_attr($product->get_price()) . '">' . esc_html__('Add to Cart', 'watchmodmarket') . '</button>';
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
    
    // Remove related products default output
    remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20);
    
    // Add custom related products layout
    add_action('woocommerce_after_single_product_summary', 'watchmodmarket_related_products', 20);
}
add_action('init', 'watchmodmarket_single_product_customization');

/**
 * Custom product tabs
 */
function watchmodmarket_product_tabs() {
    global $product;
    
    // Define tabs
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
    
    // Sort tabs by priority
    uasort($tabs, function($a, $b) {
        return $a['priority'] - $b['priority'];
    });
    
    if (!empty($tabs)) :
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
                if (isset($tab['callback'])) {
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
    endif;
}

/**
 * Product specifications tab content
 */
function watchmodmarket_product_specifications_tab() {
    global $product;
    
    $attributes = $product->get_attributes();
    
    if (!empty($attributes)) :
    ?>
    <table class="specifications-table">
        <tbody>
            <?php foreach ($attributes as $attribute) :
                if ($attribute->get_visible()) :
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
                    
                    // Output