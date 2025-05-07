<?php
/**
 * Register widget areas for theme
 *
 * @package WatchModMarket
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
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