<?php
/**
 * The template for displaying product content within loops
 */

defined('ABSPATH') || exit;

global $product;

// Ensure visibility
if (empty($product) || !$product->is_visible()) {
    return;
}
?>
<div <?php wc_product_class('product-card', $product); ?> data-product-id="<?php echo esc_attr($product->get_id()); ?>" data-category="<?php echo esc_attr($product->get_categories()); ?>" data-price="<?php echo esc_attr($product->get_price()); ?>" data-rating="<?php echo esc_attr($product->get_average_rating()); ?>" data-availability="<?php echo esc_attr($product->is_in_stock() ? 'in-stock' : 'out-of-stock'); ?>">
    
    <?php
    // Display badges
    if ($product->is_on_sale()) {
        echo '<div class="product-badge sale">' . esc_html__('Sale', 'watchmodmarket') . '</div>';
    } elseif ($product->is_featured()) {
        echo '<div class="product-badge best-seller">' . esc_html__('Best Seller', 'watchmodmarket') . '</div>';
    } elseif (has_term('new', 'product_tag', $product->get_id())) {
        echo '<div class="product-badge new">' . esc_html__('New', 'watchmodmarket') . '</div>';
    }
    ?>
    
    <div class="product-image">
        <a href="<?php the_permalink(); ?>">
            <?php
            // Display product image
            if (has_post_thumbnail()) {
                the_post_thumbnail('woocommerce_thumbnail', ['class' => 'product-thumbnail', 'alt' => get_the_title()]);
            } else {
                echo wc_placeholder_img(['class' => 'product-thumbnail', 'alt' => get_the_title()]);
            }
            ?>
        </a>
        <div class="product-actions">
            <button class="quick-view" data-product-id="<?php echo esc_attr($product->get_id()); ?>" aria-label="<?php echo esc_attr__('Quick view of', 'watchmodmarket') . ' ' . esc_attr(get_the_title()); ?>"><?php echo esc_html__('Quick View', 'watchmodmarket'); ?></button>
            <button class="add-to-cart" data-product-id="<?php echo esc_attr($product->get_id()); ?>" data-product-name="<?php echo esc_attr(get_the_title()); ?>" data-product-price="<?php echo esc_attr($product->get_price()); ?>" aria-label="<?php echo esc_attr__('Add', 'watchmodmarket') . ' ' . esc_attr(get_the_title()) . ' ' . esc_attr__('to cart', 'watchmodmarket'); ?>"><?php echo esc_html__('Add to Cart', 'watchmodmarket'); ?></button>
        </div>
    </div>
    
    <div class="product-info">
        <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
        <div class="product-meta">
            <div class="product-rating">
                <?php
                // Display rating
                if (wc_review_ratings_enabled()) {
                    $rating_count = $product->get_rating_count();
                    $review_count = $product->get_review_count();
                    $average = $product->get_average_rating();
                    
                    if ($rating_count > 0) {
                        echo wc_get_rating_html($average, $rating_count);
                        echo '<span class="review-count">(' . esc_html($review_count) . ')</span>';
                    } else {
                        echo '<span class="no-rating">' . esc_html__('No Reviews', 'watchmodmarket') . '</span>';
                    }
                }
                ?>
            </div>
            <div class="product-price">
                <?php echo $product->get_price_html(); ?>
            </div>
        </div>
    </div>
</div>