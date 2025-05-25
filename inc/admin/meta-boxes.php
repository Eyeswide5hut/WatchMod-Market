<?php
/**
 * Custom Meta Boxes for WatchModMarket theme
 *
 * @package WatchModMarket
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Register custom meta boxes
 */
function watchmodmarket_add_meta_boxes() {
    // Meta box for Watch Parts
    add_meta_box(
        'watchmodmarket_part_details',
        __('Watch Part Details', 'watchmodmarket'),
        'watchmodmarket_part_details_callback',
        'watch_part',
        'normal',
        'high'
    );
    
    // Meta box for Custom Builds
    add_meta_box(
        'watchmodmarket_build_details',
        __('Build Details', 'watchmodmarket'),
        'watchmodmarket_build_details_callback',
        'watch_build',
        'normal',
        'high'
    );
    
    // Featured Build meta box
    add_meta_box(
        'watchmodmarket_featured_build',
        __('Featured Status', 'watchmodmarket'),
        'watchmodmarket_featured_build_callback',
        'watch_build',
        'side',
        'default'
    );
    
    // Group Buy meta box for products
    if (class_exists('WooCommerce')) {
        add_meta_box(
            'watchmodmarket_group_buy',
            __('Group Buy Settings', 'watchmodmarket'),
            'watchmodmarket_group_buy_callback',
            'product',
            'normal',
            'high'
        );
    }
}
add_action('add_meta_boxes', 'watchmodmarket_add_meta_boxes');

/**
 * Watch Part details meta box callback
 */
function watchmodmarket_part_details_callback($post) {
    // Add nonce for security
    wp_nonce_field('watchmodmarket_part_details', 'watchmodmarket_part_details_nonce');
    
    // Get current values
    $part_dimensions = get_post_meta($post->ID, '_part_dimensions', true);
    $part_material = get_post_meta($post->ID, '_part_material', true);
    $part_compatibility = get_post_meta($post->ID, '_part_compatibility', true);
    $part_installation_notes = get_post_meta($post->ID, '_part_installation_notes', true);
    
    ?>
    <div class="watchmodmarket-meta-box">
        <div class="meta-row">
            <label for="part_dimensions"><?php _e('Dimensions (mm)', 'watchmodmarket'); ?></label>
            <input type="text" id="part_dimensions" name="part_dimensions" value="<?php echo esc_attr($part_dimensions); ?>" class="regular-text">
            <p class="description"><?php _e('Format: Width x Height x Depth', 'watchmodmarket'); ?></p>
        </div>
        
        <div class="meta-row">
            <label for="part_material"><?php _e('Material', 'watchmodmarket'); ?></label>
            <input type="text" id="part_material" name="part_material" value="<?php echo esc_attr($part_material); ?>" class="regular-text">
        </div>
        
        <div class="meta-row">
            <label for="part_compatibility"><?php _e('Compatible With', 'watchmodmarket'); ?></label>
            <textarea id="part_compatibility" name="part_compatibility" rows="3" class="large-text"><?php echo esc_textarea($part_compatibility); ?></textarea>
            <p class="description"><?php _e('List watch models this part is compatible with', 'watchmodmarket'); ?></p>
        </div>
        
        <div class="meta-row">
            <label for="part_installation_notes"><?php _e('Installation Notes', 'watchmodmarket'); ?></label>
            <textarea id="part_installation_notes" name="part_installation_notes" rows="5" class="large-text"><?php echo esc_textarea($part_installation_notes); ?></textarea>
        </div>
    </div>
    
    <style>
        .watchmodmarket-meta-box .meta-row {
            margin-bottom: 15px;
        }
        .watchmodmarket-meta-box label {
            display: block;
            font-weight: 600;
            margin-bottom: 5px;
        }
        .watchmodmarket-meta-box input[type="text"],
        .watchmodmarket-meta-box textarea {
            width: 100%;
        }
        .watchmodmarket-meta-box .description {
            font-style: italic;
            color: #666;
        }
    </style>
    <?php
}

/**
 * Watch Build details meta box callback
 */
function watchmodmarket_build_details_callback($post) {
    // Add nonce for security
    wp_nonce_field('watchmodmarket_build_details', 'watchmodmarket_build_details_nonce');
    
    // Get current values
    $build_parts = get_post_meta($post->ID, '_build_parts', true);
    $build_difficulty = get_post_meta($post->ID, '_build_difficulty', true);
    $build_price = get_post_meta($post->ID, '_build_price', true);
    $build_completion_time = get_post_meta($post->ID, '_build_completion_time', true);
    $build_visibility = get_post_meta($post->ID, '_build_visibility', true) ?: 'public';
    
    ?>
    <div class="watchmodmarket-meta-box">
        <div class="meta-row">
            <label for="build_parts"><?php _e('Parts Used', 'watchmodmarket'); ?></label>
            <textarea id="build_parts" name="build_parts" rows="5" class="large-text"><?php echo esc_textarea($build_parts); ?></textarea>
            <p class="description"><?php _e('List all parts used in this build (one per line)', 'watchmodmarket'); ?></p>
        </div>
        
        <div class="meta-row">
            <label for="build_difficulty"><?php _e('Build Difficulty', 'watchmodmarket'); ?></label>
            <select id="build_difficulty" name="build_difficulty">
                <option value="beginner" <?php selected($build_difficulty, 'beginner'); ?>><?php _e('Beginner', 'watchmodmarket'); ?></option>
                <option value="intermediate" <?php selected($build_difficulty, 'intermediate'); ?>><?php _e('Intermediate', 'watchmodmarket'); ?></option>
                <option value="advanced" <?php selected($build_difficulty, 'advanced'); ?>><?php _e('Advanced', 'watchmodmarket'); ?></option>
                <option value="expert" <?php selected($build_difficulty, 'expert'); ?>><?php _e('Expert', 'watchmodmarket'); ?></option>
            </select>
        </div>
        
        <div class="meta-row">
            <label for="build_price"><?php _e('Total Build Cost', 'watchmodmarket'); ?></label>
            <input type="text" id="build_price" name="build_price" value="<?php echo esc_attr($build_price); ?>" class="regular-text">
            <p class="description"><?php _e('Total cost of all parts (e.g., $349.99)', 'watchmodmarket'); ?></p>
        </div>
        
        <div class="meta-row">
            <label for="build_completion_time"><?php _e('Completion Time', 'watchmodmarket'); ?></label>
            <input type="text" id="build_completion_time" name="build_completion_time" value="<?php echo esc_attr($build_completion_time); ?>" class="regular-text">
            <p class="description"><?php _e('Approximate time to complete the build (e.g., 2 hours)', 'watchmodmarket'); ?></p>
        </div>
        
        <div class="meta-row">
            <label><?php _e('Build Visibility', 'watchmodmarket'); ?></label>
            <label class="radio-label">
                <input type="radio" name="build_visibility" value="public" <?php checked($build_visibility, 'public'); ?>>
                <?php _e('Public - Share with community', 'watchmodmarket'); ?>
            </label>
            <label class="radio-label">
                <input type="radio" name="build_visibility" value="private" <?php checked($build_visibility, 'private'); ?>>
                <?php _e('Private - Only visible to me', 'watchmodmarket'); ?>
            </label>
        </div>
    </div>
    
    <style>
        .watchmodmarket-meta-box .radio-label {
            display: block;
            margin-bottom: 5px;
            font-weight: normal;
        }
    </style>
    <?php
}

/**
 * Featured Build meta box callback
 */
function watchmodmarket_featured_build_callback($post) {
    // Add nonce for security
    wp_nonce_field('watchmodmarket_featured_build', 'watchmodmarket_featured_build_nonce');
    
    // Get current value
    $featured = get_post_meta($post->ID, '_featured', true);
    
    ?>
    <div class="watchmodmarket-meta-box">
        <label for="featured_build">
            <input type="checkbox" id="featured_build" name="featured_build" value="yes" <?php checked($featured, 'yes'); ?>>
            <?php _e('Feature this build on the homepage', 'watchmodmarket'); ?>
        </label>
    </div>
    <?php
}

/**
 * Group Buy meta box callback
 */
function watchmodmarket_group_buy_callback($post) {
    // Add nonce for security
    wp_nonce_field('watchmodmarket_group_buy', 'watchmodmarket_group_buy_nonce');
    
    // Get current values
    $is_group_buy = get_post_meta($post->ID, '_groupbuy_status', true);
    $end_date = get_post_meta($post->ID, '_groupbuy_end_date', true);
    $min_participants = get_post_meta($post->ID, '_groupbuy_min_participants', true);
    $max_participants = get_post_meta($post->ID, '_groupbuy_slots_total', true);
    $participants = get_post_meta($post->ID, '_groupbuy_slots_filled', true) ?: 0;
    $group_price = get_post_meta($post->ID, '_groupbuy_price', true);
    
    ?>
    <div class="watchmodmarket-meta-box">
        <div class="meta-row">
            <label for="is_group_buy"><?php _e('Group Buy Status', 'watchmodmarket'); ?></label>
            <select id="is_group_buy" name="is_group_buy">
                <option value="" <?php selected($is_group_buy, ''); ?>><?php _e('Not a Group Buy', 'watchmodmarket'); ?></option>
                <option value="active" <?php selected($is_group_buy, 'active'); ?>><?php _e('Active Group Buy', 'watchmodmarket'); ?></option>
                <option value="completed" <?php selected($is_group_buy, 'completed'); ?>><?php _e('Completed Group Buy', 'watchmodmarket'); ?></option>
                <option value="cancelled" <?php selected($is_group_buy, 'cancelled'); ?>><?php _e('Cancelled Group Buy', 'watchmodmarket'); ?></option>
            </select>
        </div>
        
        <div class="meta-row">
            <label for="end_date"><?php _e('End Date', 'watchmodmarket'); ?></label>
            <input type="date" id="end_date" name="end_date" value="<?php echo esc_attr($end_date); ?>" class="regular-text">
        </div>
        
        <div class="meta-row">
            <label for="min_participants"><?php _e('Minimum Participants', 'watchmodmarket'); ?></label>
            <input type="number" id="min_participants" name="min_participants" value="<?php echo esc_attr($min_participants); ?>" min="1" class="small-text">
        </div>
        
        <div class="meta-row">
            <label for="max_participants"><?php _e('Maximum Participants (Total Slots)', 'watchmodmarket'); ?></label>
            <input type="number" id="max_participants" name="max_participants" value="<?php echo esc_attr($max_participants); ?>" min="1" class="small-text">
        </div>
        
        <div class="meta-row">
            <label for="participants"><?php _e('Current Participants (Filled Slots)', 'watchmodmarket'); ?></label>
            <input type="number" id="participants" name="participants" value="<?php echo esc_attr($participants); ?>" min="0" class="small-text">
        </div>
        
        <div class="meta-row">
            <label for="group_price"><?php _e('Group Buy Price', 'watchmodmarket'); ?></label>
            <input type="text" id="group_price" name="group_price" value="<?php echo esc_attr($group_price); ?>" class="regular-text">
            <p class="description"><?php _e('Special discounted price for group buy participants', 'watchmodmarket'); ?></p>
        </div>
    </div>
    <?php
}

/**
 * Save meta box data
 */
function watchmodmarket_save_meta_box_data($post_id) {
    // Check if this is an autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    // Watch Part details
    if (isset($_POST['watchmodmarket_part_details_nonce']) && wp_verify_nonce($_POST['watchmodmarket_part_details_nonce'], 'watchmodmarket_part_details')) {
        if (isset($_POST['part_dimensions'])) {
            update_post_meta($post_id, '_part_dimensions', sanitize_text_field($_POST['part_dimensions']));
        }
        if (isset($_POST['part_material'])) {
            update_post_meta($post_id, '_part_material', sanitize_text_field($_POST['part_material']));
        }
        if (isset($_POST['part_compatibility'])) {
            update_post_meta($post_id, '_part_compatibility', sanitize_textarea_field($_POST['part_compatibility']));
        }
        if (isset($_POST['part_installation_notes'])) {
            update_post_meta($post_id, '_part_installation_notes', sanitize_textarea_field($_POST['part_installation_notes']));
        }
    }
    
    // Watch Build details
    if (isset($_POST['watchmodmarket_build_details_nonce']) && wp_verify_nonce($_POST['watchmodmarket_build_details_nonce'], 'watchmodmarket_build_details')) {
        if (isset($_POST['build_parts'])) {
            update_post_meta($post_id, '_build_parts', sanitize_textarea_field($_POST['build_parts']));
        }
        if (isset($_POST['build_difficulty'])) {
            update_post_meta($post_id, '_build_difficulty', sanitize_text_field($_POST['build_difficulty']));
        }
        if (isset($_POST['build_price'])) {
            update_post_meta($post_id, '_build_price', sanitize_text_field($_POST['build_price']));
        }
        if (isset($_POST['build_completion_time'])) {
            update_post_meta($post_id, '_build_completion_time', sanitize_text_field($_POST['build_completion_time']));
        }
        if (isset($_POST['build_visibility'])) {
            update_post_meta($post_id, '_build_visibility', sanitize_text_field($_POST['build_visibility']));
        }
    }
    
    // Featured Build
    if (isset($_POST['watchmodmarket_featured_build_nonce']) && wp_verify_nonce($_POST['watchmodmarket_featured_build_nonce'], 'watchmodmarket_featured_build')) {
        $featured = isset($_POST['featured_build']) ? 'yes' : 'no';
        update_post_meta($post_id, '_featured', $featured);
    }
    
    // Group Buy
    if (isset($_POST['watchmodmarket_group_buy_nonce']) && wp_verify_nonce($_POST['watchmodmarket_group_buy_nonce'], 'watchmodmarket_group_buy')) {
        if (isset($_POST['is_group_buy'])) {
            update_post_meta($post_id, '_groupbuy_status', sanitize_text_field($_POST['is_group_buy']));
        }
        if (isset($_POST['end_date'])) {
            update_post_meta($post_id, '_groupbuy_end_date', sanitize_text_field($_POST['end_date']));
        }
        if (isset($_POST['min_participants'])) {
            update_post_meta($post_id, '_groupbuy_min_participants', intval($_POST['min_participants']));
        }
        if (isset($_POST['max_participants'])) {
            update_post_meta($post_id, '_groupbuy_slots_total', intval($_POST['max_participants']));
        }
        if (isset($_POST['participants'])) {
            update_post_meta($post_id, '_groupbuy_slots_filled', intval($_POST['participants']));
        }
        if (isset($_POST['group_price'])) {
            update_post_meta($post_id, '_groupbuy_price', sanitize_text_field($_POST['group_price']));
        }
    }
}
add_action('save_post', 'watchmodmarket_save_meta_box_data');

/**
 * Add styles for meta boxes
 */
function watchmodmarket_admin_meta_box_styles() {
    global $post_type;
    
    if (in_array($post_type, array('watch_part', 'watch_build', 'product'))) {
        ?>
        <style>
            .watchmodmarket-meta-box {
                margin-top: 10px;
            }
            .watchmodmarket-meta-box .meta-row {
                margin-bottom: 15px;
            }
            .watchmodmarket-meta-box label {
                display: block;
                font-weight: 600;
                margin-bottom: 5px;
            }
            .watchmodmarket-meta-box input[type="text"],
            .watchmodmarket-meta-box textarea {
                width: 100%;
            }
            .watchmodmarket-meta-box .description {
                font-style: italic;
                color: #666;
                margin-top: 3px;
            }
            .watchmodmarket-meta-box .radio-label {
                display: block;
                margin-bottom: 5px;
                font-weight: normal;
            }
        </style>
        <?php
    }
}
add_action('admin_head', 'watchmodmarket_admin_meta_box_styles');