<?php
/**
 * Watch Builder Functionality for WatchModMarket Theme
 *
 * @package WatchModMarket
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register Watch Builder related post types and taxonomies
 */
function watchmodmarket_register_build_post_type() {
    $labels = array(
        'name'                  => _x('Custom Builds', 'Post type general name', 'watchmodmarket'),
        'singular_name'         => _x('Custom Build', 'Post type singular name', 'watchmodmarket'),
        'menu_name'             => _x('Custom Builds', 'Admin Menu text', 'watchmodmarket'),
        'name_admin_bar'        => _x('Custom Build', 'Add New on Toolbar', 'watchmodmarket'),
        'add_new'               => __('Add New', 'watchmodmarket'),
        'add_new_item'          => __('Add New Build', 'watchmodmarket'),
        'new_item'              => __('New Build', 'watchmodmarket'),
        'edit_item'             => __('Edit Build', 'watchmodmarket'),
        'view_item'             => __('View Build', 'watchmodmarket'),
        'all_items'             => __('All Builds', 'watchmodmarket'),
        'search_items'          => __('Search Builds', 'watchmodmarket'),
        'parent_item_colon'     => __('Parent Builds:', 'watchmodmarket'),
        'not_found'             => __('No builds found.', 'watchmodmarket'),
        'not_found_in_trash'    => __('No builds found in Trash.', 'watchmodmarket'),
        'featured_image'        => _x('Build Image', 'Overrides the "Featured Image" phrase', 'watchmodmarket'),
        'set_featured_image'    => _x('Set build image', 'Overrides the "Set featured image" phrase', 'watchmodmarket'),
        'remove_featured_image' => _x('Remove build image', 'Overrides the "Remove featured image" phrase', 'watchmodmarket'),
        'use_featured_image'    => _x('Use as build image', 'Overrides the "Use as featured image" phrase', 'watchmodmarket'),
        'archives'              => _x('Build archives', 'The post type archive label used in nav menus', 'watchmodmarket'),
        'insert_into_item'      => _x('Insert into build', 'Overrides the "Insert into post" phrase', 'watchmodmarket'),
        'uploaded_to_this_item' => _x('Uploaded to this build', 'Overrides the "Uploaded to this post" phrase', 'watchmodmarket'),
        'filter_items_list'     => _x('Filter builds list', 'Screen reader text for the filter links heading on the post type listing screen', 'watchmodmarket'),
        'items_list_navigation' => _x('Builds list navigation', 'Screen reader text for the pagination heading on the post type listing screen', 'watchmodmarket'),
        'items_list'            => _x('Builds list', 'Screen reader text for the items list heading on the post type listing screen', 'watchmodmarket'),
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array('slug' => 'watch-build'),
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
add_action('init', 'watchmodmarket_register_build_post_type');

/**
 * Add Watch Builder meta fields
 */
function watchmodmarket_add_watch_builder_meta_boxes() {
    add_meta_box(
        'watch_build_components',
        __('Watch Components', 'watchmodmarket'),
        'watchmodmarket_watch_components_callback',
        'watch_build',
        'normal',
        'high'
    );
    
    add_meta_box(
        'watch_build_settings',
        __('Build Settings', 'watchmodmarket'),
        'watchmodmarket_watch_settings_callback',
        'watch_build',
        'side',
        'default'
    );
}
add_action('add_meta_boxes', 'watchmodmarket_add_watch_builder_meta_boxes');

/**
 * Watch Components meta box callback
 */
function watchmodmarket_watch_components_callback($post) {
    wp_nonce_field('watchmodmarket_watch_components_save', 'watchmodmarket_watch_components_nonce');
    
    $components = get_post_meta($post->ID, '_watch_components', true);
    if (!is_array($components)) {
        $components = array(
            'case' => '',
            'dial' => '',
            'hands' => '',
            'movement' => '',
            'strap' => ''
        );
    }
    
    ?>
    <div class="watch-builder-components">
        <p><?php _e('Select the components used in this build:', 'watchmodmarket'); ?></p>
        
        <div class="component-fields">
            <div class="component-field">
                <label for="watch_case"><?php _e('Case:', 'watchmodmarket'); ?></label>
                <select id="watch_case" name="watch_components[case]">
                    <option value=""><?php _e('-- Select Case --', 'watchmodmarket'); ?></option>
                    <?php
                    $args = array(
                        'post_type' => 'product',
                        'posts_per_page' => -1,
                        'tax_query' => array(
                            array(
                                'taxonomy' => 'product_cat',
                                'field' => 'slug',
                                'terms' => 'cases',
                            ),
                        ),
                    );
                    
                    $cases = get_posts($args);
                    foreach ($cases as $case) {
                        echo '<option value="' . esc_attr($case->ID) . '" ' . selected($components['case'], $case->ID, false) . '>' . esc_html($case->post_title) . '</option>';
                    }
                    ?>
                </select>
            </div>
            
            <div class="component-field">
                <label for="watch_dial"><?php _e('Dial:', 'watchmodmarket'); ?></label>
                <select id="watch_dial" name="watch_components[dial]">
                    <option value=""><?php _e('-- Select Dial --', 'watchmodmarket'); ?></option>
                    <?php
                    $args = array(
                        'post_type' => 'product',
                        'posts_per_page' => -1,
                        'tax_query' => array(
                            array(
                                'taxonomy' => 'product_cat',
                                'field' => 'slug',
                                'terms' => 'dials',
                            ),
                        ),
                    );
                    
                    $dials = get_posts($args);
                    foreach ($dials as $dial) {
                        echo '<option value="' . esc_attr($dial->ID) . '" ' . selected($components['dial'], $dial->ID, false) . '>' . esc_html($dial->post_title) . '</option>';
                    }
                    ?>
                </select>
            </div>
            
            <div class="component-field">
                <label for="watch_hands"><?php _e('Hands:', 'watchmodmarket'); ?></label>
                <select id="watch_hands" name="watch_components[hands]">
                    <option value=""><?php _e('-- Select Hands --', 'watchmodmarket'); ?></option>
                    <?php
                    $args = array(
                        'post_type' => 'product',
                        'posts_per_page' => -1,
                        'tax_query' => array(
                            array(
                                'taxonomy' => 'product_cat',
                                'field' => 'slug',
                                'terms' => 'hands',
                            ),
                        ),
                    );
                    
                    $hands = get_posts($args);
                    foreach ($hands as $hand) {
                        echo '<option value="' . esc_attr($hand->ID) . '" ' . selected($components['hands'], $hand->ID, false) . '>' . esc_html($hand->post_title) . '</option>';
                    }
                    ?>
                </select>
            </div>
            
            <div class="component-field">
                <label for="watch_movement"><?php _e('Movement:', 'watchmodmarket'); ?></label>
                <select id="watch_movement" name="watch_components[movement]">
                    <option value=""><?php _e('-- Select Movement --', 'watchmodmarket'); ?></option>
                    <?php
                    $args = array(
                        'post_type' => 'product',
                        'posts_per_page' => -1,
                        'tax_query' => array(
                            array(
                                'taxonomy' => 'product_cat',
                                'field' => 'slug',
                                'terms' => 'movements',
                            ),
                        ),
                    );
                    
                    $movements = get_posts($args);
                    foreach ($movements as $movement) {
                        echo '<option value="' . esc_attr($movement->ID) . '" ' . selected($components['movement'], $movement->ID, false) . '>' . esc_html($movement->post_title) . '</option>';
                    }
                    ?>
                </select>
            </div>
            
            <div class="component-field">
                <label for="watch_strap"><?php _e('Strap:', 'watchmodmarket'); ?></label>
                <select id="watch_strap" name="watch_components[strap]">
                    <option value=""><?php _e('-- Select Strap --', 'watchmodmarket'); ?></option>
                    <?php
                    $args = array(
                        'post_type' => 'product',
                        'posts_per_page' => -1,
                        'tax_query' => array(
                            array(
                                'taxonomy' => 'product_cat',
                                'field' => 'slug',
                                'terms' => 'straps',
                            ),
                        ),
                    );
                    
                    $straps = get_posts($args);
                    foreach ($straps as $strap) {
                        echo '<option value="' . esc_attr($strap->ID) . '" ' . selected($components['strap'], $strap->ID, false) . '>' . esc_html($strap->post_title) . '</option>';
                    }
                    ?>
                </select>
            </div>
        </div>
        
        <div class="total-price">
            <?php 
            $total_price = 0;
            
            // Calculate total price based on selected components
            if (!empty($components['case'])) {
                $case_price = get_post_meta($components['case'], '_price', true);
                $total_price += floatval($case_price);
            }
            
            if (!empty($components['dial'])) {
                $dial_price = get_post_meta($components['dial'], '_price', true);
                $total_price += floatval($dial_price);
            }
            
            if (!empty($components['hands'])) {
                $hands_price = get_post_meta($components['hands'], '_price', true);
                $total_price += floatval($hands_price);
            }
            
            if (!empty($components['movement'])) {
                $movement_price = get_post_meta($components['movement'], '_price', true);
                $total_price += floatval($movement_price);
            }
            
            if (!empty($components['strap'])) {
                $strap_price = get_post_meta($components['strap'], '_price', true);
                $total_price += floatval($strap_price);
            }
            ?>
            <p><?php _e('Total Build Price:', 'watchmodmarket'); ?> 
               <span class="price"><?php echo wc_price($total_price); ?></span>
            </p>
        </div>
    </div>
    <?php
}

/**
 * Watch Settings meta box callback
 */
function watchmodmarket_watch_settings_callback($post) {
    wp_nonce_field('watchmodmarket_watch_settings_save', 'watchmodmarket_watch_settings_nonce');
    
    $visibility = get_post_meta($post->ID, '_build_visibility', true);
    $featured = get_post_meta($post->ID, '_featured', true);
    
    ?>
    <div class="watch-builder-settings">
        <div class="setting-field">
            <label for="build_visibility"><?php _e('Build Visibility:', 'watchmodmarket'); ?></label>
            <select id="build_visibility" name="build_visibility">
                <option value="private" <?php selected($visibility, 'private'); ?>><?php _e('Private (Only me)', 'watchmodmarket'); ?></option>
                <option value="public" <?php selected($visibility, 'public'); ?>><?php _e('Public (Community)', 'watchmodmarket'); ?></option>
            </select>
        </div>
        
        <div class="setting-field">
            <label for="build_featured">
                <input type="checkbox" id="build_featured" name="build_featured" value="yes" <?php checked($featured, 'yes'); ?>>
                <?php _e('Featured Build', 'watchmodmarket'); ?>
            </label>
            <p class="description"><?php _e('Display this build prominently in the community showcase', 'watchmodmarket'); ?></p>
        </div>
    </div>
    <?php
}

/**
 * Save Watch Builder meta fields
 */
function watchmodmarket_save_watch_builder_meta($post_id) {
    // Check if nonce is set
    if (!isset($_POST['watchmodmarket_watch_components_nonce']) || !isset($_POST['watchmodmarket_watch_settings_nonce'])) {
        return;
    }
    
    // Verify the nonces
    if (!wp_verify_nonce($_POST['watchmodmarket_watch_components_nonce'], 'watchmodmarket_watch_components_save') ||
        !wp_verify_nonce($_POST['watchmodmarket_watch_settings_nonce'], 'watchmodmarket_watch_settings_save')) {
        return;
    }
    
    // If this is an autosave, our form has not been submitted
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    // Check the user's permissions
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    // Save watch components
    if (isset($_POST['watch_components'])) {
        $components = array(
            'case' => isset($_POST['watch_components']['case']) ? sanitize_text_field($_POST['watch_components']['case']) : '',
            'dial' => isset($_POST['watch_components']['dial']) ? sanitize_text_field($_POST['watch_components']['dial']) : '',
            'hands' => isset($_POST['watch_components']['hands']) ? sanitize_text_field($_POST['watch_components']['hands']) : '',
            'movement' => isset($_POST['watch_components']['movement']) ? sanitize_text_field($_POST['watch_components']['movement']) : '',
            'strap' => isset($_POST['watch_components']['strap']) ? sanitize_text_field($_POST['watch_components']['strap']) : '',
        );
        
        update_post_meta($post_id, '_watch_components', $components);
    }
    
    // Save visibility setting
    if (isset($_POST['build_visibility'])) {
        update_post_meta($post_id, '_build_visibility', sanitize_text_field($_POST['build_visibility']));
    }
    
    // Save featured setting
    if (isset($_POST['build_featured']) && $_POST['build_featured'] === 'yes') {
        update_post_meta($post_id, '_featured', 'yes');
    } else {
        update_post_meta($post_id, '_featured', 'no');
    }
}
add_action('save_post_watch_build', 'watchmodmarket_save_watch_builder_meta');

/**
 * Enqueue Watch Builder specific styles and scripts
 */
function watchmodmarket_enqueue_watch_builder_assets() {
    if (is_page_template('page-templates/page-watch-builder.php')) {
        // Enqueue the watch builder CSS if it exists
        if (file_exists(WATCHMODMARKET_DIR . '/assets/css/components.css')) {
            wp_enqueue_style(
                'watchmodmarket-builder',
                WATCHMODMARKET_URI . '/assets/css/components.css',
                array('watchmodmarket-main'),
                WATCHMODMARKET_VERSION
            );
        }
        
        // Enqueue the watch builder JS if it exists
        if (file_exists(WATCHMODMARKET_DIR . '/assets/js/watch-builder.js')) {
            wp_enqueue_script(
                'watchmodmarket-builder',
                WATCHMODMARKET_URI . '/assets/js/watch-builder.js',
                array('jquery'),
                WATCHMODMARKET_VERSION,
                true
            );
            
            // Localize the script
            wp_localize_script(
                'watchmodmarket-builder',
                'watchmodmarket_builder',
                array(
                    'ajax_url' => admin_url('admin-ajax.php'),
                    'nonce' => wp_create_nonce('watchmodmarket_ajax'),
                    'i18n' => array(
                        'saving' => __('Saving...', 'watchmodmarket'),
                        'saved' => __('Build Saved!', 'watchmodmarket'),
                        'error' => __('Error saving build', 'watchmodmarket'),
                        'login_required' => __('Please log in to save your build', 'watchmodmarket'),
                    )
                )
            );
        }
    }
}
add_action('wp_enqueue_scripts', 'watchmodmarket_enqueue_watch_builder_assets');

/**
 * AJAX handler for saving watch build
 */
function watchmodmarket_save_watch_build_ajax() {
    // Check nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'watchmodmarket_ajax')) {
        wp_send_json_error(array('message' => __('Security check failed', 'watchmodmarket')));
        wp_die();
    }
    
    // Check if user is logged in
    if (!is_user_logged_in()) {
        wp_send_json_error(array('message' => __('You must be logged in to save builds', 'watchmodmarket')));
        wp_die();
    }
    
    // Get form data
    $build_name = isset($_POST['build_name']) ? sanitize_text_field($_POST['build_name']) : '';
    $build_description = isset($_POST['build_description']) ? sanitize_textarea_field($_POST['build_description']) : '';
    $build_public = isset($_POST['build_public']) && $_POST['build_public'] === '1';
    $components = isset($_POST['components']) ? $_POST['components'] : array();
    
    // Validate data
    if (empty($build_name)) {
        wp_send_json_error(array('message' => __('Build name is required', 'watchmodmarket')));
        wp_die();
    }
    
    if (empty($components) || !is_array($components)) {
        wp_send_json_error(array('message' => __('No components selected', 'watchmodmarket')));
        wp_die();
    }
    
    // Sanitize components
    $sanitized_components = array();
    foreach ($components as $type => $id) {
        $sanitized_components[$type] = intval($id);
    }
    
    // Create new watch build post
    $post_data = array(
        'post_title' => $build_name,
        'post_content' => $build_description,
        'post_status' => 'publish',
        'post_type' => 'watch_build',
        'post_author' => get_current_user_id(),
    );
    
    $post_id = wp_insert_post($post_data);
    
    if (is_wp_error($post_id)) {
        wp_send_json_error(array('message' => $post_id->get_error_message()));
        wp_die();
    }
    
    // Save components as post meta
    update_post_meta($post_id, '_watch_components', $sanitized_components);
    
    // Save visibility
    update_post_meta($post_id, '_build_visibility', $build_public ? 'public' : 'private');
    
    // Try to generate a featured image
    // (This is a placeholder - implementing the actual image generation would depend on theme capabilities)
    
    wp_send_json_success(array(
        'message' => __('Build saved successfully', 'watchmodmarket'),
        'build_id' => $post_id,
        'build_url' => get_permalink($post_id),
    ));
    wp_die();
}
add_action('wp_ajax_watchmodmarket_save_watch_build', 'watchmodmarket_save_watch_build_ajax');
add_action('wp_ajax_nopriv_watchmodmarket_save_watch_build', function() {
    wp_send_json_error(array('message' => __('Please log in to save your watch build', 'watchmodmarket')));
    wp_die();
});

/**
 * Display watch components in single build view
 */
function watchmodmarket_display_watch_components() {
    if (is_singular('watch_build')) {
        global $post;
        
        $components = get_post_meta($post->ID, '_watch_components', true);
        
        if (!is_array($components) || empty($components)) {
            return;
        }
        
        echo '<div class="watch-components-list">';
        echo '<h3>' . __('Build Components', 'watchmodmarket') . '</h3>';
        echo '<div class="components-grid">';
        
        foreach ($components as $type => $id) {
            if (empty($id)) {
                continue;
            }
            
            $product = wc_get_product($id);
            
            if (!$product) {
                continue;
            }
            
            echo '<div class="component-item">';
            echo '<h4 class="component-type">' . esc_html(ucfirst($type)) . '</h4>';
            
            // Display component image
            echo '<div class="component-image">';
            if (has_post_thumbnail($id)) {
                echo get_the_post_thumbnail($id, 'thumbnail');
            } else {
                echo wc_placeholder_img('thumbnail');
            }
            echo '</div>';
            
            // Display component info
            echo '<div class="component-info">';
            echo '<h5 class="component-name"><a href="' . esc_url(get_permalink($id)) . '">' . esc_html($product->get_name()) . '</a></h5>';
            echo '<div class="component-price">' . $product->get_price_html() . '</div>';
            echo '</div>';
            
            // Add to cart button
            echo '<div class="component-actions">';
            echo '<a href="' . esc_url($product->add_to_cart_url()) . '" class="button add_to_cart_button">' . __('Add to Cart', 'watchmodmarket') . '</a>';
            echo '</div>';
            
            echo '</div>';
        }
        
        echo '</div>'; // End components-grid
        
        // Display total price
        $total_price = 0;
        foreach ($components as $type => $id) {
            if (empty($id)) {
                continue;
            }
            
            $price = get_post_meta($id, '_price', true);
            $total_price += floatval($price);
        }
        
        echo '<div class="total-build-price">';
        echo '<span class="total-label">' . __('Total Build Price:', 'watchmodmarket') . '</span>';
        echo '<span class="total-amount">' . wc_price($total_price) . '</span>';
        echo '</div>';
        
        // Add all components to cart button
        echo '<div class="add-build-to-cart">';
        echo '<form method="post" action="' . esc_url(wc_get_cart_url()) . '">';
        
        foreach ($components as $type => $id) {
            if (!empty($id)) {
                echo '<input type="hidden" name="add-to-cart[]" value="' . esc_attr($id) . '">';
            }
        }
        
        echo '<button type="submit" class="button btn-primary">' . __('Add All Parts to Cart', 'watchmodmarket') . '</button>';
        echo '</form>';
        echo '</div>';
        
        echo '</div>'; // End watch-components-list
    }
}
add_action('woocommerce_after_single_product_summary', 'watchmodmarket_display_watch_components', 15);
add_action('watchmodmarket_after_build_content', 'watchmodmarket_display_watch_components', 10);

/**
 * Add "Build Similar" button to single build pages
 */
function watchmodmarket_add_build_similar_button() {
    if (is_singular('watch_build')) {
        global $post;
        
        echo '<div class="build-similar-container">';
        echo '<a href="' . esc_url(get_permalink(get_page_by_path('builder')) . '?template=' . $post->ID) . '" class="button btn-primary build-similar-btn">';
        echo __('Build Similar Watch', 'watchmodmarket');
        echo '</a>';
        echo '</div>';
    }
}
add_action('watchmodmarket_after_build_content', 'watchmodmarket_add_build_similar_button', 20);

/**
 * Add build template functionality to watch builder
 */
function watchmodmarket_load_build_template() {
    if (isset($_GET['template']) && !empty($_GET['template'])) {
        $template_id = absint($_GET['template']);
        $components = get_post_meta($template_id, '_watch_components', true);
        
        if (is_array($components) && !empty($components)) {
            wp_localize_script(
                'watchmodmarket-builder',
                'watchmodmarket_template',
                array(
                    'components' => $components
                )
            );
        }
    }
}
add_action('wp_enqueue_scripts', 'watchmodmarket_load_build_template', 99);

/**
 * Register shortcode to display user's builds
 */
function watchmodmarket_my_builds_shortcode($atts) {
    if (!is_user_logged_in()) {
        return '<p>' . __('Please log in to view your builds.', 'watchmodmarket') . '</p>';
    }
    
    $atts = shortcode_atts(array(
        'count' => 4,
        'columns' => 2,
    ), $atts, 'my_builds');
    
    $count = intval($atts['count']);
    $columns = intval($atts['columns']);
    
    $args = array(
        'post_type' => 'watch_build',
        'posts_per_page' => $count,
        'author' => get_current_user_id(),
    );
    
    $builds_query = new WP_Query($args);
    
    ob_start();
    
    if ($builds_query->have_posts()) {
        echo '<div class="my-builds-grid columns-' . esc_attr($columns) . '">';
        
        while ($builds_query->have_posts()) {
            $builds_query->the_post();
            
            echo '<div class="build-card">';
            
            // Featured image
            echo '<div class="build-image">';
            if (has_post_thumbnail()) {
                the_post_thumbnail('medium');
            } else {
                echo '<img src="' . esc_url(WATCHMODMARKET_URI . '/assets/images/placeholder.jpg') . '" alt="' . esc_attr(get_the_title()) . '">';
            }
            echo '</div>';
            
            // Build info
            echo '<div class="build-content">';
            echo '<h3 class="build-title"><a href="' . esc_url(get_permalink()) . '">' . esc_html(get_the_title()) . '</a></h3>';
            echo '<div class="build-meta">';
            echo '<span class="build-date">' . get_the_date() . '</span>';
            
            $visibility = get_post_meta(get_the_ID(), '_build_visibility', true);
            echo '<span class="build-visibility ' . esc_attr($visibility) . '">' . 
                 ($visibility === 'public' ? __('Public', 'watchmodmarket') : __('Private', 'watchmodmarket')) . 
                 '</span>';
                 
            echo '</div>'; // End build-meta
            
            echo '<div class="build-actions">';
            echo '<a href="' . esc_url(get_permalink()) . '" class="btn btn-secondary">' . __('View', 'watchmodmarket') . '</a>';
            echo '<a href="' . esc_url(get_permalink(get_page_by_path('builder')) . '?template=' . get_the_ID()) . '" class="btn btn-secondary">' . __('Build Similar', 'watchmodmarket') . '</a>';
            echo '</div>';
            
            echo '</div>'; // End build-content
            echo '</div>'; // End build-card
        }
        
        echo '</div>'; // End my-builds-grid
        
        // Display link to all builds if there are more
        if ($builds_query->found_posts > $count) {
            echo '<div class="view-all-builds">';
            echo '<a href="' . esc_url(get_post_type_archive_link('watch_build')) . '" class="btn btn-primary">' . 
                 __('View All My Builds', 'watchmodmarket') . '</a>';
            echo '</div>';
        }
    } else {
        echo '<div class="no-builds-message">';
        echo '<p>' . __('You haven\'t created any watch builds yet.', 'watchmodmarket') . '</p>';
        echo '<a href="' . esc_url(get_permalink(get_page_by_path('builder'))) . '" class="btn btn-primary">' . 
             __('Create Your First Build', 'watchmodmarket') . '</a>';
        echo '</div>';
    }
    
    wp_reset_postdata();
    
    return ob_get_clean();
}
add_shortcode('my_builds', 'watchmodmarket_my_builds_shortcode');