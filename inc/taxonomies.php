<?php
/**
 * Custom Taxonomies for WatchModMarket theme
 */

// Only register taxonomies if the plugin function doesn't exist
if (!function_exists('watchmodmarket_register_taxonomies')) {
    // Register Custom Taxonomies
    function watchmodmarket_register_taxonomies() {
        
        // Part Category Taxonomy
        register_taxonomy('part_category', array('watch_part'), array(
            'labels'            => array(
                'name'              => _x('Part Categories', 'taxonomy general name', 'watchmodmarket'),
                'singular_name'     => _x('Part Category', 'taxonomy singular name', 'watchmodmarket'),
                'search_items'      => __('Search Part Categories', 'watchmodmarket'),
                'all_items'         => __('All Part Categories', 'watchmodmarket'),
                'parent_item'       => __('Parent Part Category', 'watchmodmarket'),
                'parent_item_colon' => __('Parent Part Category:', 'watchmodmarket'),
                'edit_item'         => __('Edit Part Category', 'watchmodmarket'),
                'update_item'       => __('Update Part Category', 'watchmodmarket'),
                'add_new_item'      => __('Add New Part Category', 'watchmodmarket'),
                'new_item_name'     => __('New Part Category Name', 'watchmodmarket'),
                'menu_name'         => __('Part Categories', 'watchmodmarket'),
            ),
            'hierarchical'      => true,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => array('slug' => 'part-category'),
            'show_in_rest'      => true,
        ));
        
        // Brand Taxonomy
        register_taxonomy('brand', array('watch_part', 'watch_build'), array(
            'labels'            => array(
                'name'              => _x('Brands', 'taxonomy general name', 'watchmodmarket'),
                'singular_name'     => _x('Brand', 'taxonomy singular name', 'watchmodmarket'),
                'search_items'      => __('Search Brands', 'watchmodmarket'),
                'all_items'         => __('All Brands', 'watchmodmarket'),
                'parent_item'       => __('Parent Brand', 'watchmodmarket'),
                'parent_item_colon' => __('Parent Brand:', 'watchmodmarket'),
                'edit_item'         => __('Edit Brand', 'watchmodmarket'),
                'update_item'       => __('Update Brand', 'watchmodmarket'),
                'add_new_item'      => __('Add New Brand', 'watchmodmarket'),
                'new_item_name'     => __('New Brand Name', 'watchmodmarket'),
                'menu_name'         => __('Brands', 'watchmodmarket'),
            ),
            'hierarchical'      => false,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => array('slug' => 'brand'),
            'show_in_rest'      => true,
        ));
        
        // Compatibility Taxonomy
        register_taxonomy('compatibility', array('watch_part'), array(
            'labels'            => array(
                'name'              => _x('Compatibility', 'taxonomy general name', 'watchmodmarket'),
                'singular_name'     => _x('Compatibility', 'taxonomy singular name', 'watchmodmarket'),
                'search_items'      => __('Search Compatibility', 'watchmodmarket'),
                'all_items'         => __('All Compatibility', 'watchmodmarket'),
                'parent_item'       => __('Parent Compatibility', 'watchmodmarket'),
                'parent_item_colon' => __('Parent Compatibility:', 'watchmodmarket'),
                'edit_item'         => __('Edit Compatibility', 'watchmodmarket'),
                'update_item'       => __('Update Compatibility', 'watchmodmarket'),
                'add_new_item'      => __('Add New Compatibility', 'watchmodmarket'),
                'new_item_name'     => __('New Compatibility Name', 'watchmodmarket'),
                'menu_name'         => __('Compatibility', 'watchmodmarket'),
            ),
            'hierarchical'      => false,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => array('slug' => 'compatibility'),
            'show_in_rest'      => true,
        ));
        
        // Watch Style Taxonomy
        register_taxonomy('watch_style', array('watch_part', 'watch_build'), array(
            'labels'            => array(
                'name'              => _x('Watch Styles', 'taxonomy general name', 'watchmodmarket'),
                'singular_name'     => _x('Watch Style', 'taxonomy singular name', 'watchmodmarket'),
                'search_items'      => __('Search Watch Styles', 'watchmodmarket'),
                'all_items'         => __('All Watch Styles', 'watchmodmarket'),
                'parent_item'       => __('Parent Watch Style', 'watchmodmarket'),
                'parent_item_colon' => __('Parent Watch Style:', 'watchmodmarket'),
                'edit_item'         => __('Edit Watch Style', 'watchmodmarket'),
                'update_item'       => __('Update Watch Style', 'watchmodmarket'),
                'add_new_item'      => __('Add New Watch Style', 'watchmodmarket'),
                'new_item_name'     => __('New Watch Style Name', 'watchmodmarket'),
                'menu_name'         => __('Watch Styles', 'watchmodmarket'),
            ),
            'hierarchical'      => true,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => array('slug' => 'style'),
            'show_in_rest'      => true,
        ));
        
        // Community Topic Taxonomy
        register_taxonomy('community_topic', array('community_post'), array(
            'labels'            => array(
                'name'              => _x('Topics', 'taxonomy general name', 'watchmodmarket'),
                'singular_name'     => _x('Topic', 'taxonomy singular name', 'watchmodmarket'),
                'search_items'      => __('Search Topics', 'watchmodmarket'),
                'all_items'         => __('All Topics', 'watchmodmarket'),
                'parent_item'       => __('Parent Topic', 'watchmodmarket'),
                'parent_item_colon' => __('Parent Topic:', 'watchmodmarket'),
                'edit_item'         => __('Edit Topic', 'watchmodmarket'),
                'update_item'       => __('Update Topic', 'watchmodmarket'),
                'add_new_item'      => __('Add New Topic', 'watchmodmarket'),
                'new_item_name'     => __('New Topic Name', 'watchmodmarket'),
                'menu_name'         => __('Topics', 'watchmodmarket'),
            ),
            'hierarchical'      => true,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => array('slug' => 'topic'),
            'show_in_rest'      => true,
        ));
    }
    add_action('init', 'watchmodmarket_register_taxonomies');
}