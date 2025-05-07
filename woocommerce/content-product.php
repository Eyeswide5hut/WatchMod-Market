<?php
/**
 * The template for displaying product content within loops
 *
 * @package WatchModMarket
 */

defined('ABSPATH') || exit;

global $product;

// Ensure visibility.
if (empty($product) || !$product->is_visible()) {
    return;
}

// Get product data
$product_id = $product->get_id();
$product_name = $product->get_name();
$product_price = $product->get_price_html();
$product_permalink = $product->get_permalink();
$product_image = wp_get_attachment_image_src(get_post_thumbnail_id($product_id), 'woocommerce_thumbnail');
$product_rating = $product->get_average_rating();
$product_review_count = $product->get_review_count();

// Check if product is on sale
$is_on_sale = $product->is_on_sale();

// Check if product is new (added in the last 30 days)
$is_new = get_post_time('U', false, $product_id) > strtotime('-30 days');

// Check if product is a best seller (based on total sales)
$sales_count = get_post_meta($product_id, 'total_sales', true);
$is_best_seller = $sales_count && $sales_count > 100; // Adjust threshold as needed

// Get product specs
$part_type = get_the_terms($product_id, 'pa_part-type');
$case_size = get_the_terms($product_id, 'pa_case-size');
$compatibility = get_the_terms($product_id, 'pa_compatible-with');
$movement_type = get_the_terms($product_id, 'pa_movement-type');
?>

<div class="product-card" data-product-id="<?php echo esc_attr($product_id); ?>">
    <?php if ($is_on_sale || $is_new || $is_best_seller) : ?>
        <div class="product-badges">
            <?php if ($is_on_sale) : ?>
                <span class="product-badge sale"><?php esc_html_e('Sale', 'watchmodmarket'); ?></span>
            <?php endif; ?>
            <?php if ($is_new) : ?>
                <span class="product-badge new"><?php esc_html_e('New', 'watchmodmarket'); ?></span>
            <?php endif; ?>
            <?php if ($is_best_seller) : ?>
                <span class="product-badge best-seller"><?php esc_html_e('Best Seller', 'watchmodmarket'); ?></span>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <div class="product-image">
        <a href="<?php echo esc_url($product_permalink); ?>">
            <?php if ($product_image) : ?>
                <img class="product-thumbnail" src="<?php echo esc_url($product_image[0]); ?>" alt="<?php echo esc_attr($product_name); ?>">
            <?php else : ?>
                <img class="product-thumbnail" src="<?php echo wc_placeholder_img_src(); ?>" alt="<?php echo esc_attr($product_name); ?>">
            <?php endif; ?>
        </a>
        
        <div class="product-actions">
            <button class="quick-view" data-product-id="<?php echo esc_attr($product_id); ?>">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M8 3C3 3 1 8 1 8s2 5 7 5 7-5 7-5s-2-5-7-5z" stroke="currentColor" stroke-width="2"/>
                    <circle cx="8" cy="8" r="2" stroke="currentColor" stroke-width="2"/>
                </svg>
                <?php esc_html_e('Quick View', 'watchmodmarket'); ?>
            </button>
            <?php if ($product->is_purchasable() && $product->is_in_stock()) : ?>
                <button class="add-to-cart" 
                    data-product-id="<?php echo esc_attr($product_id); ?>"
                    data-product-name="<?php echo esc_attr($product_name); ?>">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M6 13c.6 0 1 .4 1 1s-.4 1-1 1-1-.4-1-1 .4-1 1-1zm5 0c.6 0 1 .4 1 1s-.4 1-1 1-1-.4-1-1 .4-1 1-1zM2 0h1.4l2.6 8H11l3-6H5l.4 1h7.2l-2.4 4H7.4L4.8 1H2z" fill="currentColor"/>
                    </svg>
                    <?php esc_html_e('Add to Cart', 'watchmodmarket'); ?>
                </button>
            <?php endif; ?>
            <button class="add-to-wishlist" data-product-id="<?php echo esc_attr($product_id); ?>">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M8 2l1.4-1.4c1.6-1.6 4.2-1.6 5.8 0s1.6 4.2 0 5.8L8 14 .8 6.4c-1.6-1.6-1.6-4.2 0-5.8s4.2-1.6 5.8 0L8 2z" stroke="currentColor" stroke-width="2" fill="none"/>
                </svg>
                <span aria-hidden="true">🤍</span>
            </button>
        </div>
    </div>

    <div class="product-info">
        <h3>
            <a href="<?php echo esc_url($product_permalink); ?>">
                <?php echo esc_html($product_name); ?>
            </a>
        </h3>
        
        <?php if ($part_type && !is_wp_error($part_type)) : ?>
            <div class="product-specs">
                <?php if ($part_type) : ?>
                    <span class="product-spec type"><?php echo esc_html($part_type[0]->name); ?></span>
                <?php endif; ?>
                <?php if ($case_size && !is_wp_error($case_size)) : ?>
                    <span class="product-spec size"><?php echo esc_html($case_size[0]->name); ?></span>
                <?php endif; ?>
                <?php if ($movement_type && !is_wp_error($movement_type)) : ?>
                    <span class="product-spec movement"><?php echo esc_html($movement_type[0]->name); ?></span>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        
        <?php if ($compatibility && !is_wp_error($compatibility)) : ?>
            <div class="compatibility-info">
                <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="7" cy="7" r="6" stroke="currentColor" stroke-width="2"/>
                    <path d="M5 7l2 2 4-4" stroke="currentColor" stroke-width="2"/>
                </svg>
                <?php 
                $compat_names = array_map(function($term) { return $term->name; }, $compatibility);
                echo esc_html__('Compatible with:', 'watchmodmarket') . ' ' . esc_html(implode(', ', array_slice($compat_names, 0, 3)));
                ?>
            </div>
        <?php endif; ?>

        <div class="product-meta">
            <?php if ($product_rating > 0) : ?>
                <div class="product-rating">
                    <?php if ($product_rating >= 4.5) : ?>
                        ⭐⭐⭐⭐⭐
                    <?php elseif ($product_rating >= 3.5) : ?>
                        ⭐⭐⭐⭐☆
                    <?php elseif ($product_rating >= 2.5) : ?>
                        ⭐⭐⭐☆☆
                    <?php elseif ($product_rating >= 1.5) : ?>
                        ⭐⭐☆☆☆
                    <?php else : ?>
                        ⭐☆☆☆☆
                    <?php endif; ?>
                    <span class="review-count">
                        (<?php 
                            /* translators: %s: number of reviews */
                            printf(_n('%s review', '%s reviews', $product_review_count, 'watchmodmarket'), $product_review_count); 
                        ?>)
                    </span>
                </div>
            <?php else : ?>
                <div class="product-rating">
                    <?php esc_html_e('No reviews yet', 'watchmodmarket'); ?>
                </div>
            <?php endif; ?>
            
            <div class="product-price">
                <?php echo $product_price; ?>
            </div>
        </div>
    </div>
</div>