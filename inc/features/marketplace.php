<?php
/**
 * WatchModMarket Marketplace Functions
 *
 * @package WatchModMarket
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Register 'Watch Listing' custom post type
 */
function watchmodmarket_register_watch_listing_post_type() {
    $labels = array(
        'name'                  => _x('Watch Listings', 'Post type general name', 'watchmodmarket'),
        'singular_name'         => _x('Watch Listing', 'Post type singular name', 'watchmodmarket'),
        'menu_name'             => _x('Watch Listings', 'Admin Menu text', 'watchmodmarket'),
        'name_admin_bar'        => _x('Watch Listing', 'Add New on Toolbar', 'watchmodmarket'),
        'add_new'               => __('Add New', 'watchmodmarket'),
        'add_new_item'          => __('Add New Watch Listing', 'watchmodmarket'),
        'new_item'              => __('New Watch Listing', 'watchmodmarket'),
        'edit_item'             => __('Edit Watch Listing', 'watchmodmarket'),
        'view_item'             => __('View Watch Listing', 'watchmodmarket'),
        'all_items'             => __('All Watch Listings', 'watchmodmarket'),
        'search_items'          => __('Search Watch Listings', 'watchmodmarket'),
        'not_found'             => __('No watch listings found.', 'watchmodmarket'),
        'not_found_in_trash'    => __('No watch listings found in Trash.', 'watchmodmarket'),
        'featured_image'        => _x('Watch Image', 'Overrides the "Featured Image" phrase', 'watchmodmarket'),
        'set_featured_image'    => _x('Set watch image', 'Overrides the "Set featured image" phrase', 'watchmodmarket'),
        'remove_featured_image' => _x('Remove watch image', 'Overrides the "Remove featured image" phrase', 'watchmodmarket'),
        'use_featured_image'    => _x('Use as watch image', 'Overrides the "Use as featured image" phrase', 'watchmodmarket'),
        'archives'              => _x('Watch listing archives', 'The post type archive label used in nav menus', 'watchmodmarket'),
        'insert_into_item'      => _x('Insert into watch listing', 'Overrides the "Insert into post"/"Insert into page" phrase', 'watchmodmarket'),
        'uploaded_to_this_item' => _x('Uploaded to this watch listing', 'Overrides the "Uploaded to this post"/"Uploaded to this page" phrase', 'watchmodmarket'),
        'filter_items_list'     => _x('Filter watch listings list', 'Screen reader text for the filter links heading on the post type listing screen', 'watchmodmarket'),
        'items_list_navigation' => _x('Watch listings list navigation', 'Screen reader text for the pagination heading on the post type listing screen', 'watchmodmarket'),
        'items_list'            => _x('Watch listings list', 'Screen reader text for the items list heading on the post type listing screen', 'watchmodmarket'),
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array('slug' => 'watch-listing'),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'menu_icon'          => 'dashicons-clock',
        'supports'           => array('title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments'),
        'show_in_rest'       => true, // Enable Gutenberg editor
    );

    register_post_type('watch_listing', $args);
}
add_action('init', 'watchmodmarket_register_watch_listing_post_type');

/**
 * Register custom taxonomies for watch listings
 */
function watchmodmarket_register_watch_taxonomies() {
    // Watch Brand Taxonomy
    $brand_labels = array(
        'name'              => _x('Watch Brands', 'taxonomy general name', 'watchmodmarket'),
        'singular_name'     => _x('Watch Brand', 'taxonomy singular name', 'watchmodmarket'),
        'search_items'      => __('Search Watch Brands', 'watchmodmarket'),
        'all_items'         => __('All Watch Brands', 'watchmodmarket'),
        'parent_item'       => __('Parent Watch Brand', 'watchmodmarket'),
        'parent_item_colon' => __('Parent Watch Brand:', 'watchmodmarket'),
        'edit_item'         => __('Edit Watch Brand', 'watchmodmarket'),
        'update_item'       => __('Update Watch Brand', 'watchmodmarket'),
        'add_new_item'      => __('Add New Watch Brand', 'watchmodmarket'),
        'new_item_name'     => __('New Watch Brand Name', 'watchmodmarket'),
        'menu_name'         => __('Brands', 'watchmodmarket'),
    );

    $brand_args = array(
        'hierarchical'      => true,
        'labels'            => $brand_labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'watch-brand'),
        'show_in_rest'      => true,
    );

    register_taxonomy('watch_brand', array('watch_listing'), $brand_args);

    // Watch Condition Taxonomy
    $condition_labels = array(
        'name'              => _x('Watch Conditions', 'taxonomy general name', 'watchmodmarket'),
        'singular_name'     => _x('Watch Condition', 'taxonomy singular name', 'watchmodmarket'),
        'search_items'      => __('Search Watch Conditions', 'watchmodmarket'),
        'all_items'         => __('All Watch Conditions', 'watchmodmarket'),
        'parent_item'       => __('Parent Watch Condition', 'watchmodmarket'),
        'parent_item_colon' => __('Parent Watch Condition:', 'watchmodmarket'),
        'edit_item'         => __('Edit Watch Condition', 'watchmodmarket'),
        'update_item'       => __('Update Watch Condition', 'watchmodmarket'),
        'add_new_item'      => __('Add New Watch Condition', 'watchmodmarket'),
        'new_item_name'     => __('New Watch Condition Name', 'watchmodmarket'),
        'menu_name'         => __('Conditions', 'watchmodmarket'),
    );

    $condition_args = array(
        'hierarchical'      => true,
        'labels'            => $condition_labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'watch-condition'),
        'show_in_rest'      => true,
    );

    register_taxonomy('watch_condition', array('watch_listing'), $condition_args);
}
add_action('init', 'watchmodmarket_register_watch_taxonomies');

/**
 * Register meta boxes for watch listing details
 */
function watchmodmarket_register_watch_listing_meta_boxes() {
    add_meta_box(
        'watch_listing_details',
        __('Watch Details', 'watchmodmarket'),
        'watchmodmarket_watch_listing_details_callback',
        'watch_listing',
        'normal',
        'high'
    );

    add_meta_box(
        'watch_listing_price',
        __('Price Information', 'watchmodmarket'),
        'watchmodmarket_watch_listing_price_callback',
        'watch_listing',
        'side',
        'high'
    );
}
add_action('add_meta_boxes', 'watchmodmarket_register_watch_listing_meta_boxes');

/**
 * Callback function for watch details meta box
 */
function watchmodmarket_watch_listing_details_callback($post) {
    // Add nonce for security
    wp_nonce_field('watchmodmarket_watch_details_save', 'watchmodmarket_watch_details_nonce');

    // Get existing values
    $brand = get_post_meta($post->ID, '_watch_brand', true);
    $model = get_post_meta($post->ID, '_watch_model', true);
    $year = get_post_meta($post->ID, '_watch_year', true);
    $case_material = get_post_meta($post->ID, '_watch_case_material', true);
    $case_size = get_post_meta($post->ID, '_watch_case_size', true);
    $movement = get_post_meta($post->ID, '_watch_movement', true);
    $condition = get_post_meta($post->ID, '_watch_condition', true);
    $box_papers = get_post_meta($post->ID, '_watch_box_papers', true);
    $warranty = get_post_meta($post->ID, '_watch_warranty', true);
    $seller_notes = get_post_meta($post->ID, '_watch_seller_notes', true);
    
    // Output fields
    ?>
    <div class="watch-details-fields">
        <div class="watch-detail-row">
            <div class="watch-detail-field">
                <label for="watch_brand"><?php _e('Brand', 'watchmodmarket'); ?></label>
                <input type="text" id="watch_brand" name="watch_brand" value="<?php echo esc_attr($brand); ?>">
            </div>
            
            <div class="watch-detail-field">
                <label for="watch_model"><?php _e('Model/Reference', 'watchmodmarket'); ?></label>
                <input type="text" id="watch_model" name="watch_model" value="<?php echo esc_attr($model); ?>">
            </div>
            
            <div class="watch-detail-field">
                <label for="watch_year"><?php _e('Year', 'watchmodmarket'); ?></label>
                <input type="text" id="watch_year" name="watch_year" value="<?php echo esc_attr($year); ?>">
            </div>
        </div>
        
        <div class="watch-detail-row">
            <div class="watch-detail-field">
                <label for="watch_case_material"><?php _e('Case Material', 'watchmodmarket'); ?></label>
                <input type="text" id="watch_case_material" name="watch_case_material" value="<?php echo esc_attr($case_material); ?>">
            </div>
            
            <div class="watch-detail-field">
                <label for="watch_case_size"><?php _e('Case Size (mm)', 'watchmodmarket'); ?></label>
                <input type="text" id="watch_case_size" name="watch_case_size" value="<?php echo esc_attr($case_size); ?>">
            </div>
            
            <div class="watch-detail-field">
                <label for="watch_movement"><?php _e('Movement', 'watchmodmarket'); ?></label>
                <input type="text" id="watch_movement" name="watch_movement" value="<?php echo esc_attr($movement); ?>">
            </div>
        </div>
        
        <div class="watch-detail-row">
            <div class="watch-detail-field">
                <label for="watch_condition"><?php _e('Condition', 'watchmodmarket'); ?></label>
                <select id="watch_condition" name="watch_condition">
                    <option value="new" <?php selected($condition, 'new'); ?>><?php _e('New', 'watchmodmarket'); ?></option>
                    <option value="like-new" <?php selected($condition, 'like-new'); ?>><?php _e('Like New', 'watchmodmarket'); ?></option>
                    <option value="excellent" <?php selected($condition, 'excellent'); ?>><?php _e('Excellent', 'watchmodmarket'); ?></option>
                    <option value="good" <?php selected($condition, 'good'); ?>><?php _e('Good', 'watchmodmarket'); ?></option>
                    <option value="fair" <?php selected($condition, 'fair'); ?>><?php _e('Fair', 'watchmodmarket'); ?></option>
                </select>
            </div>
            
            <div class="watch-detail-field">
                <label for="watch_box_papers"><?php _e('Box & Papers', 'watchmodmarket'); ?></label>
                <select id="watch_box_papers" name="watch_box_papers">
                    <option value="both" <?php selected($box_papers, 'both'); ?>><?php _e('Box & Papers', 'watchmodmarket'); ?></option>
                    <option value="box-only" <?php selected($box_papers, 'box-only'); ?>><?php _e('Box Only', 'watchmodmarket'); ?></option>
                    <option value="papers-only" <?php selected($box_papers, 'papers-only'); ?>><?php _e('Papers Only', 'watchmodmarket'); ?></option>
                    <option value="none" <?php selected($box_papers, 'none'); ?>><?php _e('None', 'watchmodmarket'); ?></option>
                </select>
            </div>
            
            <div class="watch-detail-field">
                <label for="watch_warranty"><?php _e('Warranty', 'watchmodmarket'); ?></label>
                <input type="text" id="watch_warranty" name="watch_warranty" value="<?php echo esc_attr($warranty); ?>" placeholder="<?php _e('e.g., Manufacturer warranty until 2025', 'watchmodmarket'); ?>">
            </div>
        </div>
        
        <div class="watch-detail-row full-width">
            <div class="watch-detail-field">
                <label for="watch_seller_notes"><?php _e('Seller Notes', 'watchmodmarket'); ?></label>
                <textarea id="watch_seller_notes" name="watch_seller_notes" rows="4"><?php echo esc_textarea($seller_notes); ?></textarea>
            </div>
        </div>
    </div>
    <?php
}

/**
 * Callback function for watch price meta box
 */
function watchmodmarket_watch_listing_price_callback($post) {
    // Add nonce for security
    wp_nonce_field('watchmodmarket_watch_price_save', 'watchmodmarket_watch_price_nonce');

    // Get existing values
    $price = get_post_meta($post->ID, '_watch_price', true);
    $price_negotiable = get_post_meta($post->ID, '_watch_price_negotiable', true);
    $shipping_included = get_post_meta($post->ID, '_watch_shipping_included', true);
    $accepts_trades = get_post_meta($post->ID, '_watch_accepts_trades', true);
    
    // Output fields
    ?>
    <div class="watch-price-fields">
        <div class="watch-price-field">
            <label for="watch_price"><?php _e('Price ($)', 'watchmodmarket'); ?></label>
            <input type="number" id="watch_price" name="watch_price" value="<?php echo esc_attr($price); ?>" min="0" step="0.01">
        </div>
        
        <div class="watch-price-field checkbox">
            <label>
                <input type="checkbox" id="watch_price_negotiable" name="watch_price_negotiable" value="1" <?php checked($price_negotiable, '1'); ?>>
                <?php _e('Price Negotiable', 'watchmodmarket'); ?>
            </label>
        </div>
        
        <div class="watch-price-field checkbox">
            <label>
                <input type="checkbox" id="watch_shipping_included" name="watch_shipping_included" value="1" <?php checked($shipping_included, '1'); ?>>
                <?php _e('Shipping Included', 'watchmodmarket'); ?>
            </label>
        </div>
        
        <div class="watch-price-field checkbox">
            <label>
                <input type="checkbox" id="watch_accepts_trades" name="watch_accepts_trades" value="1" <?php checked($accepts_trades, '1'); ?>>
                <?php _e('Accepts Trades', 'watchmodmarket'); ?>
            </label>
        </div>
    </div>
    <?php
}

/**
 * Save watch listing meta data
 */
function watchmodmarket_save_watch_listing_meta($post_id) {
    // Skip autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    // Check if our nonce is set for details
    if (isset($_POST['watchmodmarket_watch_details_nonce']) && wp_verify_nonce($_POST['watchmodmarket_watch_details_nonce'], 'watchmodmarket_watch_details_save')) {
        // Save watch details
        if (isset($_POST['watch_brand'])) {
            update_post_meta($post_id, '_watch_brand', sanitize_text_field($_POST['watch_brand']));
        }
        
        if (isset($_POST['watch_model'])) {
            update_post_meta($post_id, '_watch_model', sanitize_text_field($_POST['watch_model']));
        }
        
        if (isset($_POST['watch_year'])) {
            update_post_meta($post_id, '_watch_year', sanitize_text_field($_POST['watch_year']));
        }
        
        if (isset($_POST['watch_case_material'])) {
            update_post_meta($post_id, '_watch_case_material', sanitize_text_field($_POST['watch_case_material']));
        }
        
        if (isset($_POST['watch_case_size'])) {
            update_post_meta($post_id, '_watch_case_size', sanitize_text_field($_POST['watch_case_size']));
        }
        
        if (isset($_POST['watch_movement'])) {
            update_post_meta($post_id, '_watch_movement', sanitize_text_field($_POST['watch_movement']));
        }
        
        if (isset($_POST['watch_condition'])) {
            update_post_meta($post_id, '_watch_condition', sanitize_text_field($_POST['watch_condition']));
        }
        
        if (isset($_POST['watch_box_papers'])) {
            update_post_meta($post_id, '_watch_box_papers', sanitize_text_field($_POST['watch_box_papers']));
        }
        
        if (isset($_POST['watch_warranty'])) {
            update_post_meta($post_id, '_watch_warranty', sanitize_text_field($_POST['watch_warranty']));
        }
        
        if (isset($_POST['watch_seller_notes'])) {
            update_post_meta($post_id, '_watch_seller_notes', sanitize_textarea_field($_POST['watch_seller_notes']));
        }
    }
    
    // Check if our nonce is set for price
    if (isset($_POST['watchmodmarket_watch_price_nonce']) && wp_verify_nonce($_POST['watchmodmarket_watch_price_nonce'], 'watchmodmarket_watch_price_save')) {
        // Save price details
        if (isset($_POST['watch_price'])) {
            update_post_meta($post_id, '_watch_price', sanitize_text_field($_POST['watch_price']));
        }
        
        $price_negotiable = isset($_POST['watch_price_negotiable']) ? '1' : '0';
        update_post_meta($post_id, '_watch_price_negotiable', $price_negotiable);
        
        $shipping_included = isset($_POST['watch_shipping_included']) ? '1' : '0';
        update_post_meta($post_id, '_watch_shipping_included', $shipping_included);
        
        $accepts_trades = isset($_POST['watch_accepts_trades']) ? '1' : '0';
        update_post_meta($post_id, '_watch_accepts_trades', $accepts_trades);
    }
}
add_action('save_post_watch_listing', 'watchmodmarket_save_watch_listing_meta');