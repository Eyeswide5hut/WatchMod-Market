<?php
/**
 * Group Buy Functionality for WatchModMarket Theme
 *
 * @package WatchModMarket
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register Group Buy related post types and taxonomies
 */
function watchmodmarket_register_group_buy_taxonomies() {
    // Register tags for group buys if they don't exist
    if (!term_exists('group-buy', 'product_tag')) {
        wp_insert_term(
            'Group Buy',
            'product_tag',
            array(
                'slug' => 'group-buy',
                'description' => 'Products available through group buy campaigns'
            )
        );
    }
    
    if (!term_exists('pre-order', 'product_tag')) {
        wp_insert_term(
            'Pre-Order',
            'product_tag',
            array(
                'slug' => 'pre-order',
                'description' => 'Products available for pre-order'
            )
        );
    }
}
add_action('init', 'watchmodmarket_register_group_buy_taxonomies');

/**
 * Add Group Buy meta fields to product
 */
function watchmodmarket_add_group_buy_fields() {
    global $post;
    
    // Only show these fields for products with group-buy tag
    $terms = get_the_terms($post->ID, 'product_tag');
    $is_group_buy = false;
    $is_preorder = false;
    
    if ($terms && !is_wp_error($terms)) {
        foreach ($terms as $term) {
            if ($term->slug === 'group-buy') {
                $is_group_buy = true;
            }
            if ($term->slug === 'pre-order') {
                $is_preorder = true;
            }
        }
    }
    
    if ($is_group_buy) {
        echo '<div class="options_group show_if_simple show_if_variable">';
        echo '<h4>' . __('Group Buy Settings', 'watchmodmarket') . '</h4>';
        
        // Group Buy Price
        woocommerce_wp_text_input(array(
            'id' => '_groupbuy_price',
            'label' => __('Group Buy Price', 'watchmodmarket'),
            'description' => __('Special price when the group buy target is reached', 'watchmodmarket'),
            'desc_tip' => true,
            'type' => 'text',
        ));
        
        // Group Buy Status
        woocommerce_wp_select(array(
            'id' => '_groupbuy_status',
            'label' => __('Group Buy Status', 'watchmodmarket'),
            'options' => array(
                '' => __('Select...', 'watchmodmarket'),
                'pending' => __('Pending', 'watchmodmarket'),
                'active' => __('Active', 'watchmodmarket'),
                'completed' => __('Completed', 'watchmodmarket'),
                'failed' => __('Failed', 'watchmodmarket'),
            ),
        ));
        
        // Group Buy End Date
        woocommerce_wp_text_input(array(
            'id' => '_groupbuy_end_date',
            'label' => __('End Date', 'watchmodmarket'),
            'placeholder' => 'YYYY-MM-DD',
            'description' => __('The date when this group buy ends', 'watchmodmarket'),
            'desc_tip' => true,
            'type' => 'date',
        ));
        
        // Total Slots
        woocommerce_wp_text_input(array(
            'id' => '_groupbuy_slots_total',
            'label' => __('Total Slots', 'watchmodmarket'),
            'description' => __('Maximum number of participants', 'watchmodmarket'),
            'desc_tip' => true,
            'type' => 'number',
            'custom_attributes' => array(
                'min' => '1',
                'step' => '1',
            ),
        ));
        
        // Slots Filled
        woocommerce_wp_text_input(array(
            'id' => '_groupbuy_slots_filled',
            'label' => __('Slots Filled', 'watchmodmarket'),
            'description' => __('Current number of participants', 'watchmodmarket'),
            'desc_tip' => true,
            'type' => 'number',
            'custom_attributes' => array(
                'min' => '0',
                'step' => '1',
            ),
        ));
        
        echo '</div>';
    }
    
    if ($is_preorder) {
        echo '<div class="options_group show_if_simple show_if_variable">';
        echo '<h4>' . __('Pre-Order Settings', 'watchmodmarket') . '</h4>';
        
        // Pre-Order Status
        woocommerce_wp_select(array(
            'id' => '_preorder_status',
            'label' => __('Pre-Order Status', 'watchmodmarket'),
            'options' => array(
                '' => __('Select...', 'watchmodmarket'),
                'upcoming' => __('Upcoming', 'watchmodmarket'),
                'active' => __('Active', 'watchmodmarket'),
                'completed' => __('Completed', 'watchmodmarket'),
                'cancelled' => __('Cancelled', 'watchmodmarket'),
            ),
        ));
        
        // Release Date
        woocommerce_wp_text_input(array(
            'id' => '_preorder_release_date',
            'label' => __('Estimated Release Date', 'watchmodmarket'),
            'placeholder' => 'YYYY-MM-DD',
            'description' => __('The estimated release date for this product', 'watchmodmarket'),
            'desc_tip' => true,
            'type' => 'date',
        ));
        
        // Deposit Amount
        woocommerce_wp_text_input(array(
            'id' => '_preorder_deposit',
            'label' => __('Deposit Amount', 'watchmodmarket'),
            'description' => __('Required deposit amount', 'watchmodmarket'),
            'desc_tip' => true,
            'type' => 'text',
        ));
        
        // Limited Quantity
        woocommerce_wp_text_input(array(
            'id' => '_preorder_limited_qty',
            'label' => __('Limited Quantity', 'watchmodmarket'),
            'description' => __('Maximum available units (leave empty for unlimited)', 'watchmodmarket'),
            'desc_tip' => true,
            'type' => 'number',
            'custom_attributes' => array(
                'min' => '0',
                'step' => '1',
            ),
        ));
        
        // Exclusive
        woocommerce_wp_checkbox(array(
            'id' => '_preorder_exclusive',
            'label' => __('Exclusive Pre-Order', 'watchmodmarket'),
            'description' => __('Mark this pre-order as exclusive', 'watchmodmarket'),
        ));
        
        echo '</div>';
    }
}
add_action('woocommerce_product_options_general_product_data', 'watchmodmarket_add_group_buy_fields');

/**
 * Save Group Buy meta fields
 */
function watchmodmarket_save_group_buy_fields($post_id) {
    // Group Buy fields
    if (isset($_POST['_groupbuy_price'])) {
        update_post_meta($post_id, '_groupbuy_price', wc_format_decimal(wc_clean($_POST['_groupbuy_price'])));
    }
    
    if (isset($_POST['_groupbuy_status'])) {
        update_post_meta($post_id, '_groupbuy_status', wc_clean($_POST['_groupbuy_status']));
    }
    
    if (isset($_POST['_groupbuy_end_date'])) {
        update_post_meta($post_id, '_groupbuy_end_date', wc_clean($_POST['_groupbuy_end_date']));
    }
    
    if (isset($_POST['_groupbuy_slots_total'])) {
        update_post_meta($post_id, '_groupbuy_slots_total', absint($_POST['_groupbuy_slots_total']));
    }
    
    if (isset($_POST['_groupbuy_slots_filled'])) {
        update_post_meta($post_id, '_groupbuy_slots_filled', absint($_POST['_groupbuy_slots_filled']));
    }
    
    // Pre-Order fields
    if (isset($_POST['_preorder_status'])) {
        update_post_meta($post_id, '_preorder_status', wc_clean($_POST['_preorder_status']));
    }
    
    if (isset($_POST['_preorder_release_date'])) {
        update_post_meta($post_id, '_preorder_release_date', wc_clean($_POST['_preorder_release_date']));
    }
    
    if (isset($_POST['_preorder_deposit'])) {
        update_post_meta($post_id, '_preorder_deposit', wc_format_decimal(wc_clean($_POST['_preorder_deposit'])));
    }
    
    if (isset($_POST['_preorder_limited_qty'])) {
        update_post_meta($post_id, '_preorder_limited_qty', absint($_POST['_preorder_limited_qty']));
    }
    
    if (isset($_POST['_preorder_exclusive'])) {
        update_post_meta($post_id, '_preorder_exclusive', 'yes');
    } else {
        update_post_meta($post_id, '_preorder_exclusive', 'no');
    }
}
add_action('woocommerce_process_product_meta', 'watchmodmarket_save_group_buy_fields');

/**
 * Enqueue Group Buy specific styles and scripts
 */
function watchmodmarket_enqueue_group_buy_assets() {
    if (is_page_template('page-templates/page-group-buy.php')) {
        // Enqueue the group buy CSS if it exists
        if (file_exists(WATCHMODMARKET_DIR . '/assets/css/group-buy.css')) {
            wp_enqueue_style(
                'watchmodmarket-group-buy',
                WATCHMODMARKET_URI . '/assets/css/group-buy.css',
                array('watchmodmarket-main'),
                WATCHMODMARKET_VERSION
            );
        }
        
        // Enqueue the group buy JS if it exists
        if (file_exists(WATCHMODMARKET_DIR . '/assets/js/group-buy.js')) {
            wp_enqueue_script(
                'watchmodmarket-group-buy',
                WATCHMODMARKET_URI . '/assets/js/group-buy.js',
                array('jquery'),
                WATCHMODMARKET_VERSION,
                true
            );
            
            // Localize the script
            wp_localize_script(
                'watchmodmarket-group-buy',
                'watchmodmarket_group_buy',
                array(
                    'ajax_url' => admin_url('admin-ajax.php'),
                    'nonce' => wp_create_nonce('watchmodmarket_ajax'),
                    'i18n' => array(
                        'joining' => __('Joining...', 'watchmodmarket'),
                        'joined' => __('Joined!', 'watchmodmarket'),
                        'error' => __('Error', 'watchmodmarket'),
                    )
                )
            );
        }
    }
}
add_action('wp_enqueue_scripts', 'watchmodmarket_enqueue_group_buy_assets');

/**
 * Display Group Buy information on product page
 */
function watchmodmarket_group_buy_product_meta() {
    global $product;
    
    if (!$product) {
        return;
    }
    
    $product_id = $product->get_id();
    
    // Check if product is a group buy
    $terms = get_the_terms($product_id, 'product_tag');
    $is_group_buy = false;
    $is_preorder = false;
    
    if ($terms && !is_wp_error($terms)) {
        foreach ($terms as $term) {
            if ($term->slug === 'group-buy') {
                $is_group_buy = true;
            }
            if ($term->slug === 'pre-order') {
                $is_preorder = true;
            }
        }
    }
    
    if ($is_group_buy) {
        $status = get_post_meta($product_id, '_groupbuy_status', true);
        
        if ($status === 'active') {
            $regular_price = $product->get_regular_price();
            $group_price = get_post_meta($product_id, '_groupbuy_price', true);
            $slots_total = get_post_meta($product_id, '_groupbuy_slots_total', true);
            $slots_filled = get_post_meta($product_id, '_groupbuy_slots_filled', true);
            $end_date = get_post_meta($product_id, '_groupbuy_end_date', true);
            
            if ($group_price && $slots_total) {
                $progress_percent = ($slots_filled / $slots_total) * 100;
                $days_left = ceil((strtotime($end_date) - current_time('timestamp')) / (60 * 60 * 24));
                $discount_percent = round(((floatval($regular_price) - floatval($group_price)) / floatval($regular_price)) * 100);
                
                echo '<div class="group-buy-info">';
                echo '<h3>' . __('Group Buy Active!', 'watchmodmarket') . '</h3>';
                echo '<div class="group-buy-price-compare">';
                echo '<span class="regular-price">' . get_woocommerce_currency_symbol() . $regular_price . '</span>';
                echo '<span class="group-price">' . get_woocommerce_currency_symbol() . $group_price . '</span>';
                echo '<span class="discount-percent">' . sprintf(__('Save %d%%', 'watchmodmarket'), $discount_percent) . '</span>';
                echo '</div>';
                
                echo '<div class="group-buy-progress">';
                echo '<div class="slots-info">' . sprintf(__('%d of %d slots filled', 'watchmodmarket'), $slots_filled, $slots_total) . '</div>';
                echo '<div class="progress-bar-container">';
                echo '<div class="progress-bar" style="width: ' . esc_attr($progress_percent) . '%"></div>';
                echo '</div>';
                echo '</div>';
                
                echo '<div class="group-buy-time-left">';
                if ($days_left > 0) {
                    echo '<div class="days-left">' . sprintf(_n('%d day left', '%d days left', $days_left, 'watchmodmarket'), $days_left) . '</div>';
                } else {
                    echo '<div class="ending-soon">' . __('Ending soon!', 'watchmodmarket') . '</div>';
                }
                echo '<div class="end-date">' . sprintf(__('Ends on: %s', 'watchmodmarket'), date_i18n(get_option('date_format'), strtotime($end_date))) . '</div>';
                echo '</div>';
                
                echo '</div>';
            }
        }
    }
    
    if ($is_preorder) {
        $status = get_post_meta($product_id, '_preorder_status', true);
        
        if ($status === 'upcoming' || $status === 'active') {
            $release_date = get_post_meta($product_id, '_preorder_release_date', true);
            $deposit = get_post_meta($product_id, '_preorder_deposit', true);
            $limited_qty = get_post_meta($product_id, '_preorder_limited_qty', true);
            $is_exclusive = get_post_meta($product_id, '_preorder_exclusive', true);
            
            echo '<div class="pre-order-info">';
            echo '<h3>' . __('Pre-Order Available!', 'watchmodmarket') . '</h3>';
            
            if ($release_date) {
                echo '<div class="release-date">' . sprintf(__('Estimated Release: %s', 'watchmodmarket'), date_i18n(get_option('date_format'), strtotime($release_date))) . '</div>';
            }
            
            if ($deposit) {
                echo '<div class="deposit-amount">' . sprintf(__('Required Deposit: %s', 'watchmodmarket'), get_woocommerce_currency_symbol() . $deposit) . '</div>';
            }
            
            if ($limited_qty) {
                echo '<div class="limited-quantity">' . sprintf(__('Limited to %d units', 'watchmodmarket'), $limited_qty) . '</div>';
            }
            
            if ($is_exclusive === 'yes') {
                echo '<div class="exclusive-tag">' . __('Exclusive Pre-Order', 'watchmodmarket') . '</div>';
            }
            
            echo '</div>';
        }
    }
}
add_action('woocommerce_single_product_summary', 'watchmodmarket_group_buy_product_meta', 15);