<?php
/**
 * WP cleanup functions
 *
 * @package WatchModMarket
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Clean up wp_head() output
 */
function watchmodmarket_head_cleanup() {
    // Remove unnecessary WP head items
    remove_action('wp_head', 'feed_links_extra', 3);
    remove_action('wp_head', 'feed_links', 2);
    remove_action('wp_head', 'rsd_link');
    remove_action('wp_head', 'wlwmanifest_link');
    remove_action('wp_head', 'index_rel_link');
    remove_action('wp_head', 'parent_post_rel_link', 10, 0);
    remove_action('wp_head', 'start_post_rel_link', 10, 0);
    remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0);
    remove_action('wp_head', 'wp_generator');
    remove_action('wp_head', 'wp_shortlink_wp_head', 10, 0);
    remove_action('wp_head', 'rest_output_link_wp_head', 10);
    remove_action('wp_head', 'wp_oembed_add_discovery_links');
    remove_action('template_redirect', 'rest_output_link_header', 11, 0);

    // Remove emoji script
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
    remove_filter('the_content_feed', 'wp_staticize_emoji');
    remove_filter('comment_text_rss', 'wp_staticize_emoji');
    
    // Remove jQuery migrate
    add_action('wp_default_scripts', function ($scripts) {
        if (!is_admin() && isset($scripts->registered['jquery'])) {
            $script = $scripts->registered['jquery'];
            if ($script->deps) {
                // Check if migrate is one of the dependencies
                $script->deps = array_diff($script->deps, array('jquery-migrate'));
            }
        }
    });
}
add_action('init', 'watchmodmarket_head_cleanup');

/**
 * Remove WP block library CSS for non-block pages
 */
function watchmodmarket_dequeue_block_styles() {
    // If not using blocks, remove the CSS
    if (!has_blocks()) {
        wp_dequeue_style('wp-block-library');
        wp_dequeue_style('wp-block-library-theme');
    }
}
add_action('wp_enqueue_scripts', 'watchmodmarket_dequeue_block_styles', 100);

/**
 * Remove version query strings from assets
 */
function watchmodmarket_remove_script_version($src) {
    if (strpos($src, 'ver=') && !strpos($src, 'watchmodmarket')) {
        $src = remove_query_arg('ver', $src);
    }
    return $src;
}
add_filter('script_loader_src', 'watchmodmarket_remove_script_version', 15, 1);
add_filter('style_loader_src', 'watchmodmarket_remove_script_version', 15, 1);

/**
 * Remove unnecessary dashboard widgets
 */
function watchmodmarket_remove_dashboard_widgets() {
    remove_meta_box('dashboard_quick_press', 'dashboard', 'side');
    remove_meta_box('dashboard_primary', 'dashboard', 'side');
}
add_action('wp_dashboard_setup', 'watchmodmarket_remove_dashboard_widgets');

/**
 * Clean up image attributes
 */
function watchmodmarket_clean_image_attributes($html, $id, $caption, $title, $align, $url, $size, $alt) {
    // Remove width/height attributes 
    $html = preg_replace('/(width|height)="\d*"\s/', '', $html);
    return $html;
}
add_filter('image_send_to_editor', 'watchmodmarket_clean_image_attributes', 10, 8);

/**
 * Stop WordPress from modifying .htaccess
 */
function watchmodmarket_remove_htaccess_mods() {
    remove_filter('mod_rewrite_rules', 'wp_htaccess_header');
}
add_action('init', 'watchmodmarket_remove_htaccess_mods');

/**
 * Remove unwanted admin menu items for non-admins
 */
function watchmodmarket_remove_admin_menus() {
    if (!current_user_can('administrator')) {
        remove_menu_page('tools.php'); // Tools
        remove_menu_page('index.php'); // Dashboard
        remove_submenu_page('themes.php', 'themes.php'); // Appearance > Themes
    }
}
add_action('admin_menu', 'watchmodmarket_remove_admin_menus');

/**
 * Remove comments from admin bar
 */
function watchmodmarket_remove_admin_bar_items() {
    global $wp_admin_bar;
    $wp_admin_bar->remove_menu('comments');
}
add_action('wp_before_admin_bar_render', 'watchmodmarket_remove_admin_bar_items');

/**
 * Add custom body classes
 */
function watchmodmarket_body_classes($classes) {
    // Add a class if there is a custom background
    if (get_background_image()) {
        $classes[] = 'has-custom-background';
    }
    
    // Add a class if we're in the customizer preview
    if (is_customize_preview()) {
        $classes[] = 'is-customizer-preview';
    }
    
    return $classes;
}
add_filter('body_class', 'watchmodmarket_body_classes');