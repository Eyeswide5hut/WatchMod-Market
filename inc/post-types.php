<?php
/**
 * Custom Post Types for WatchModMarket theme
 */

// Only register post types if the plugin function doesn't exist
if (!function_exists('watchmodmarket_register_post_types')) {
    // Register Custom Post Types
    function watchmodmarket_register_post_types() {
        
        // Watch Parts CPT
        register_post_type('watch_part', array(
            'labels'             => array(
                'name'               => _x('Watch Parts', 'post type general name', 'watchmodmarket'),
                'singular_name'      => _x('Watch Part', 'post type singular name', 'watchmodmarket'),
                'menu_name'          => _x('Watch Parts', 'admin menu', 'watchmodmarket'),
                'name_admin_bar'     => _x('Watch Part', 'add new on admin bar', 'watchmodmarket'),
                'add_new'            => _x('Add New', 'watch part', 'watchmodmarket'),
                'add_new_item'       => __('Add New Watch Part', 'watchmodmarket'),
                'new_item'           => __('New Watch Part', 'watchmodmarket'),
                'edit_item'          => __('Edit Watch Part', 'watchmodmarket'),
                'view_item'          => __('View Watch Part', 'watchmodmarket'),
                'all_items'          => __('All Watch Parts', 'watchmodmarket'),
                'search_items'       => __('Search Watch Parts', 'watchmodmarket'),
                'parent_item_colon'  => __('Parent Watch Parts:', 'watchmodmarket'),
                'not_found'          => __('No watch parts found.', 'watchmodmarket'),
                'not_found_in_trash' => __('No watch parts found in Trash.', 'watchmodmarket')
            ),
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => array('slug' => 'parts'),
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => null,
            'menu_icon'          => 'dashicons-admin-generic',
            'supports'           => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'),
            'show_in_rest'       => true,
        ));
        
        // Custom Builds CPT
        register_post_type('watch_build', array(
            'labels'             => array(
                'name'               => _x('Custom Builds', 'post type general name', 'watchmodmarket'),
                'singular_name'      => _x('Custom Build', 'post type singular name', 'watchmodmarket'),
                'menu_name'          => _x('Custom Builds', 'admin menu', 'watchmodmarket'),
                'name_admin_bar'     => _x('Custom Build', 'add new on admin bar', 'watchmodmarket'),
                'add_new'            => _x('Add New', 'custom build', 'watchmodmarket'),
                'add_new_item'       => __('Add New Custom Build', 'watchmodmarket'),
                'new_item'           => __('New Custom Build', 'watchmodmarket'),
                'edit_item'          => __('Edit Custom Build', 'watchmodmarket'),
                'view_item'          => __('View Custom Build', 'watchmodmarket'),
                'all_items'          => __('All Custom Builds', 'watchmodmarket'),
                'search_items'       => __('Search Custom Builds', 'watchmodmarket'),
                'parent_item_colon'  => __('Parent Custom Builds:', 'watchmodmarket'),
                'not_found'          => __('No custom builds found.', 'watchmodmarket'),
                'not_found_in_trash' => __('No custom builds found in Trash.', 'watchmodmarket')
            ),
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => array('slug' => 'builds'),
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => null,
            'menu_icon'          => 'dashicons-admin-customizer',
            'supports'           => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'),
            'show_in_rest'       => true,
        ));
        
        // Community Posts CPT (if not using BuddyPress or similar)
        register_post_type('community_post', array(
            'labels'             => array(
                'name'               => _x('Community Posts', 'post type general name', 'watchmodmarket'),
                'singular_name'      => _x('Community Post', 'post type singular name', 'watchmodmarket'),
                'menu_name'          => _x('Community', 'admin menu', 'watchmodmarket'),
                'name_admin_bar'     => _x('Community Post', 'add new on admin bar', 'watchmodmarket'),
                'add_new'            => _x('Add New', 'community post', 'watchmodmarket'),
                'add_new_item'       => __('Add New Community Post', 'watchmodmarket'),
                'new_item'           => __('New Community Post', 'watchmodmarket'),
                'edit_item'          => __('Edit Community Post', 'watchmodmarket'),
                'view_item'          => __('View Community Post', 'watchmodmarket'),
                'all_items'          => __('All Community Posts', 'watchmodmarket'),
                'search_items'       => __('Search Community Posts', 'watchmodmarket'),
                'parent_item_colon'  => __('Parent Community Posts:', 'watchmodmarket'),
                'not_found'          => __('No community posts found.', 'watchmodmarket'),
                'not_found_in_trash' => __('No community posts found in Trash.', 'watchmodmarket')
            ),
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => array('slug' => 'community-posts'),
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => null,
            'menu_icon'          => 'dashicons-groups',
            'supports'           => array('title', 'editor', 'thumbnail', 'excerpt', 'comments', 'custom-fields'),
            'show_in_rest'       => true,
        ));
    }
    add_action('init', 'watchmodmarket_register_post_types');
}

// Flush rewrite rules on theme activation - use a unique function name
function watchmodmarket_theme_rewrite_flush() {
    // If the plugin function exists, use that, otherwise use our own
    if (function_exists('watchmodmarket_register_post_types')) {
        // This will call the plugin's function
        watchmodmarket_register_post_types();
    }
    flush_rewrite_rules();
}
add_action('after_switch_theme', 'watchmodmarket_theme_rewrite_flush');