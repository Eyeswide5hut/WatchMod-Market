<?php
/**
 * WooCommerce hook modifications
 *
 * @package WatchModMarket
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Hide default WooCommerce product count and showing results
 */
add_filter('woocommerce_show_page_title', '__return_false');
remove_action('woocommerce_before_shop_loop', 'woocommerce_result_count', 20);
remove_action('woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30);

/**
 * Remove WooCommerce default tabs
 */
add_filter('woocommerce_product_tabs', function($tabs) {
    unset($tabs['description']);
    unset($tabs['additional_information']);
    unset($tabs['reviews']);
    return $tabs;
}, 98);

/**
 * Move product rating summary
 */
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10);

/**
 * Remove related products default output
 */
remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20);

/**
 * Modify product review form fields
 */
add_filter('comment_form_fields', function($fields) {
    if (!is_product()) {
        return $fields;
    }
    
    // Reorder fields
    $comment_field = $fields['comment'];
    unset($fields['comment']);
    $fields['comment'] = $comment_field;
    
    return $fields;
});

/**
 * Add schema.org structured data for products
 */
function watchmodmarket_add_product_schema() {
    if (!is_product()) {
        return;
    }
    
    global $product;
    
    // Ensure we have a valid product object
    if (!$product || !is_a($product, 'WC_Product')) {
        return;
    }
    
    $schema = array(
        '@context' => 'https://schema.org',
        '@type' => 'Product',
        'name' => $product->get_name(),
        'description' => $product->get_short_description(),
        'image' => wp_get_attachment_url($product->get_image_id()),
        'sku' => $product->get_sku(),
        'offers' => array(
            '@type' => 'Offer',
            'price' => $product->get_price(),
            'priceCurrency' => get_woocommerce_currency(),
            'availability' => $product->is_in_stock() ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
            'url' => get_permalink($product->get_id())
        )
    );
    
    if ($product->get_average_rating()) {
        $schema['aggregateRating'] = array(
            '@type' => 'AggregateRating',
            'ratingValue' => $product->get_average_rating(),
            'reviewCount' => $product->get_review_count()
        );
    }
    
    echo '<script type="application/ld+json">' . json_encode($schema) . '</script>';
}
add_action('wp_head', 'watchmodmarket_add_product_schema');