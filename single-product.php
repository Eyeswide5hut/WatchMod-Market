<?php
/**
 * Single Product Template
 * 
 * This template displays a single product page matching the neo-brutalist design
 */

defined('ABSPATH') || exit;

get_header();
?>

<!-- Breadcrumb -->
<div class="breadcrumb">
    <div class="container">
        <nav class="breadcrumb-nav" aria-label="<?php esc_attr_e('Breadcrumb', 'watchmodmarket'); ?>">
            <div class="breadcrumb-item">
                <a href="<?php echo home_url(); ?>"><?php esc_html_e('Home', 'watchmodmarket'); ?></a>
            </div>
            <div class="breadcrumb-item">
                <a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>"><?php esc_html_e('Shop', 'watchmodmarket'); ?></a>
            </div>
            <?php
            $product_cats = wp_get_post_terms(get_the_ID(), 'product_cat');
            if (!empty($product_cats) && !is_wp_error($product_cats)) {
                $product_cat = array_shift($product_cats);
            ?>
            <div class="breadcrumb-item">
                <a href="<?php echo get_term_link($product_cat); ?>"><?php echo esc_html($product_cat->name); ?></a>
            </div>
            <?php } ?>
            <div class="breadcrumb-item">
                <span><?php the_title(); ?></span>
            </div>
        </nav>
    </div>
</div>

<div class="container product-page-container">
    <?php while (have_posts()) : the_post(); ?>
        
        <?php global $product; ?>

        <div class="product-page-grid">
            <!-- Product Gallery -->
            <div class="product-gallery">
                <div class="product-main-image">
                    <?php
                    $attachment_ids = $product->get_gallery_image_ids();
                    $main_image_id = get_post_thumbnail_id();
                    $all_images = array_merge(array($main_image_id), $attachment_ids);
                    
                    if (!empty($all_images)) {
                        $first_image = reset($all_images);
                        echo wp_get_attachment_image($first_image, 'woocommerce_single', false, array('class' => 'current-product-image', 'id' => 'product-main-image-' . $first_image));
                        
                        if ($product->is_on_sale()) {
                            echo '<div class="product-badge sale">' . esc_html__('Sale', 'watchmodmarket') . '</div>';
                        } elseif ($product->is_featured()) {
                            echo '<div class="product-badge best-seller">' . esc_html__('Best Seller', 'watchmodmarket') . '</div>';
                        } elseif (has_term('new', 'product_tag', $product->get_id())) {
                            echo '<div class="product-badge new">' . esc_html__('New', 'watchmodmarket') . '</div>';
                        }
                        
                        if (!$product->is_in_stock()) {
                            echo '<div class="product-badge out-of-stock">' . esc_html__('Out of Stock', 'watchmodmarket') . '</div>';
                        }
                    }
                    ?>
                </div>
                
                <?php if (count($all_images) > 1) : ?>
                <div class="product-thumbnails">
                    <?php foreach ($all_images as $image_id) : ?>
                        <div class="product-thumbnail <?php echo ($image_id == $first_image) ? 'active' : ''; ?>" data-image-id="<?php echo esc_attr($image_id); ?>">
                            <?php echo wp_get_attachment_image($image_id, 'woocommerce_gallery_thumbnail'); ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>

            <!-- Product Information -->
            <div class="product-information">
                <h1 class="product-title"><?php the_title(); ?></h1>
                
                <?php if ($product->get_rating_count() > 0) : ?>
                <div class="product-rating-bar">
                    <div class="rating-stars-big">
                        <?php
                        $rating = $product->get_average_rating();
                        for ($i = 1; $i <= 5; $i++) {
                            if ($i <= $rating) {
                                echo '<span class="star filled">★</span>';
                            } elseif ($i - 0.5 <= $rating) {
                                echo '<span class="star half">★</span>';
                            } else {
                                echo '<span class="star empty">☆</span>';
                            }
                        }
                        ?>
                    </div>
                    <span class="review-count-text">(<?php echo esc_html($product->get_review_count()); ?> <?php esc_html_e('reviews', 'watchmodmarket'); ?>)</span>
                </div>
                <?php endif; ?>

                <div class="product-price-wrapper">
                    <?php echo $product->get_price_html(); ?>
                    <?php if ($product->is_on_sale()) : ?>
                        <div class="discount-badge">
                            <?php
                            $regular = $product->get_regular_price();
                            $sale = $product->get_sale_price();
                            $discount = round((($regular - $sale) / $regular) * 100);
                            echo sprintf(esc_html__('%d%% OFF', 'watchmodmarket'), $discount);
                            ?>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if ($product->is_in_stock()) : ?>
                    <div class="stock-status in-stock">
                        <i class="fa fa-check-circle"></i>
                        <?php esc_html_e('In Stock', 'watchmodmarket'); ?>
                        <?php if ($product->managing_stock() && $product->get_stock_quantity() <= 5 && $product->get_stock_quantity() > 0) : ?>
                            <span class="low-stock">(<?php echo esc_html__('Low Stock: ', 'watchmodmarket') . $product->get_stock_quantity() . ' ' . esc_html__('left', 'watchmodmarket'); ?>)</span>
                        <?php endif; ?>
                    </div>
                <?php else : ?>
                    <div class="stock-status out-stock">
                        <i class="fa fa-times-circle"></i>
                        <?php esc_html_e('Out of Stock', 'watchmodmarket'); ?>
                    </div>
                <?php endif; ?>

                <!-- Short Description -->
                <div class="product-short-description">
                    <?php echo $product->get_short_description(); ?>
                </div>

                <!-- Product Specifications -->
                <?php
                $attributes = $product->get_attributes();
                if (!empty($attributes)) :
                ?>
                <div class="product-specifications">
                    <h3><?php esc_html_e('Specifications', 'watchmodmarket'); ?></h3>
                    <ul class="specs-list">
                        <?php foreach ($attributes as $attribute) :
                            $taxonomy = $attribute->get_taxonomy();
                            $terms = get_the_terms($product->get_id(), $taxonomy);
                            if ($terms && !is_wp_error($terms)) :
                        ?>
                        <li>
                            <span class="spec-label"><?php echo wc_attribute_label($taxonomy); ?>:</span>
                            <span class="spec-value">
                                <?php
                                $term_names = array_map(function($term) { return $term->name; }, $terms);
                                echo esc_html(implode(', ', $term_names));
                                ?>
                            </span>
                        </li>
                        <?php endif; endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>

                <!-- Add to Cart Form -->
                <form class="product-cart-form" method="post" enctype="multipart/form-data">
                    <div class="quantity-and-cart">
                        <?php if ($product->is_purchasable() && $product->is_in_stock()) : ?>
                        <div class="quantity-selector">
                            <button type="button" class="qty-button minus">-</button>
                            <input type="number" class="qty-input" name="quantity" value="1" min="1" max="<?php echo esc_attr($product->get_max_purchase_quantity()); ?>">
                            <button type="button" class="qty-button plus">+</button>
                        </div>
                        <button type="submit" name="add-to-cart" value="<?php echo esc_attr($product->get_id()); ?>" class="btn btn-primary add-to-cart-big">
                            <i class="fa fa-shopping-cart"></i>
                            <?php esc_html_e('Add to Cart', 'watchmodmarket'); ?>
                        </button>
                        <?php else : ?>
                        <button type="button" class="btn btn-disabled" disabled>
                            <?php esc_html_e('Out of Stock', 'watchmodmarket'); ?>
                        </button>
                        <?php endif; ?>
                    </div>
                    <?php do_action('woocommerce_after_add_to_cart_button'); ?>
                </form>

                <!-- Additional Product Actions -->
                <div class="product-actions">
                    <button class="action-button add-to-watchlist" data-product-id="<?php echo esc_attr($product->get_id()); ?>">
                        <i class="fa fa-heart"></i>
                        <?php esc_html_e('Add to Watchlist', 'watchmodmarket'); ?>
                    </button>
                    <a href="<?php echo esc_url(get_permalink(get_page_by_path('builder'))); ?>?add-part=<?php echo esc_attr($product->get_id()); ?>" class="action-button add-to-builder">
                        <i class="fa fa-plus-circle"></i>
                        <?php esc_html_e('Add to Watch Builder', 'watchmodmarket'); ?>
                    </a>
                    <button class="action-button share-product">
                        <i class="fa fa-share-alt"></i>
                        <?php esc_html_e('Share', 'watchmodmarket'); ?>
                    </button>
                </div>

                <!-- Trust Indicators -->
                <div class="trust-indicators">
                    <div class="trust-item">
                        <i class="fa fa-shield"></i>
                        <span><?php esc_html_e('Secure Payment', 'watchmodmarket'); ?></span>
                    </div>
                    <div class="trust-item">
                        <i class="fa fa-truck"></i>
                        <span><?php esc_html_e('Fast Shipping', 'watchmodmarket'); ?></span>
                    </div>
                    <div class="trust-item">
                        <i class="fa fa-undo"></i>
                        <span><?php esc_html_e('30-Day Returns', 'watchmodmarket'); ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Tabs -->
        <div class="product-tabs-wrapper">
            <div class="product-tabs">
                <button class="tab-button active" data-tab="description"><?php esc_html_e('Description', 'watchmodmarket'); ?></button>
                <button class="tab-button" data-tab="specifications"><?php esc_html_e('Specifications', 'watchmodmarket'); ?></button>
                <button class="tab-button" data-tab="compatibility"><?php esc_html_e('Compatibility', 'watchmodmarket'); ?></button>
                <button class="tab-button" data-tab="reviews"><?php esc_html_e('Reviews', 'watchmodmarket'); ?> (<?php echo $product->get_review_count(); ?>)</button>
            </div>

            <div class="tab-content active" id="tab-description">
                <div class="product-description">
                    <?php the_content(); ?>
                </div>
            </div>

            <div class="tab-content" id="tab-specifications">
                <?php
                $attributes = $product->get_attributes();
                if (!empty($attributes)) :
                ?>
                <table class="specifications-table">
                    <tbody>
                        <?php foreach ($attributes as $attribute) :
                            $taxonomy = $attribute->get_taxonomy();
                            $terms = get_the_terms($product->get_id(), $taxonomy);
                            if ($terms && !is_wp_error($terms)) :
                        ?>
                        <tr>
                            <th><?php echo wc_attribute_label($taxonomy); ?></th>
                            <td>
                                <?php
                                $term_names = array_map(function($term) { return $term->name; }, $terms);
                                echo esc_html(implode(', ', $term_names));
                                ?>
                            </td>
                        </tr>
                        <?php endif; endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>
            </div>

            <div class="tab-content" id="tab-compatibility">
                <?php
                $compatible_products = get_post_meta($product->get_id(), '_compatible_parts', true);
                $compatible_terms = get_the_terms($product->get_id(), 'pa_compatible-with');
                ?>
                <div class="compatibility-info">
                    <?php if ($compatible_terms && !is_wp_error($compatible_terms)) : ?>
                        <h3><?php esc_html_e('Compatible With', 'watchmodmarket'); ?></h3>
                        <ul class="compatibility-list">
                            <?php foreach ($compatible_terms as $term) : ?>
                                <li><i class="fa fa-check"></i> <?php echo esc_html($term->name); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                    
                    <?php if ($compatible_products) : ?>
                        <h3><?php esc_html_e('Works Best With', 'watchmodmarket'); ?></h3>
                        <div class="compatible-products-grid">
                            <?php
                            $compatible_query = new WP_Query(array(
                                'post_type' => 'product',
                                'post__in' => explode(',', $compatible_products),
                                'posts_per_page' => 3
                            ));
                            if ($compatible_query->have_posts()) :
                                while ($compatible_query->have_posts()) : $compatible_query->the_post();
                                    global $product;
                                    wc_get_template_part('content', 'product');
                                endwhile;
                                wp_reset_postdata();
                            endif;
                            ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="tab-content" id="tab-reviews">
                <?php comments_template(); ?>
            </div>
        </div>

        <!-- Related Products -->
        <?php
        $related_products = wc_get_related_products($product->get_id(), 4);
        if (!empty($related_products)) :
        ?>
        <div class="related-products">
            <h2><?php esc_html_e('Related Parts', 'watchmodmarket'); ?></h2>
            <div class="related-products-grid">
                <?php
                foreach ($related_products as $related_product_id) {
                    $post_object = get_post($related_product_id);
                    setup_postdata($GLOBALS['post'] =& $post_object);
                    wc_get_template_part('content', 'product');
                }
                wp_reset_postdata();
                ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Recently Viewed -->
        <div class="recently-viewed">
            <h2><?php esc_html_e('Recently Viewed', 'watchmodmarket'); ?></h2>
            <div class="recently-viewed-grid" id="recently-viewed-products">
                <!-- Will be populated by JavaScript -->
            </div>
        </div>

    <?php endwhile; ?>
</div>

<script>
jQuery(document).ready(function($) {
    // Image gallery
    $('.product-thumbnail').on('click', function() {
        const imageId = $(this).data('image-id');
        const mainImage = $('#product-main-image-' + imageId);
        
        $('.current-product-image').removeClass('current-product-image');
        mainImage.addClass('current-product-image');
        
        $('.product-thumbnail').removeClass('active');
        $(this).addClass('active');
    });
    
    // Quantity buttons
    $('.qty-button.plus').on('click', function() {
        const input = $(this).siblings('.qty-input');
        const max = input.attr('max');
        let value = parseInt(input.val());
        if (!max || value < max) {
            input.val(value + 1);
        }
    });
    
    $('.qty-button.minus').on('click', function() {
        const input = $(this).siblings('.qty-input');
        let value = parseInt(input.val());
        if (value > 1) {
            input.val(value - 1);
        }
    });
    
    // Tabs
    $('.tab-button').on('click', function() {
        const tabName = $(this).data('tab');
        
        $('.tab-button').removeClass('active');
        $(this).addClass('active');
        
        $('.tab-content').removeClass('active');
        $('#tab-' + tabName).addClass('active');
    });
    
    // Recently viewed products
    function updateRecentlyViewed() {
        const productId = <?php echo get_the_ID(); ?>;
        let recentlyViewed = JSON.parse(localStorage.getItem('recentlyViewed') || '[]');
        
        // Remove current product if already in list
        recentlyViewed = recentlyViewed.filter(id => id !== productId);
        
        // Add current product to beginning
        recentlyViewed.unshift(productId);
        
        // Keep only last 5
        recentlyViewed = recentlyViewed.slice(0, 5);
        
        // Save to localStorage
        localStorage.setItem('recentlyViewed', JSON.stringify(recentlyViewed));
        
        // Load and display recently viewed products
        loadRecentlyViewed(recentlyViewed);
    }
    
    function loadRecentlyViewed(productIds) {
        if (productIds.length <= 1) return; // Only current product
        
        $.ajax({
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            method: 'POST',
            data: {
                action: 'get_recently_viewed_products',
                product_ids: productIds.slice(1) // Exclude current product
            },
            success: function(response) {
                if (response.success) {
                    $('#recently-viewed-products').html(response.data);
                }
            }
        });
    }
    
    updateRecentlyViewed();
    
    // Add to watchlist
    $('.add-to-watchlist').on('click', function() {
        const productId = $(this).data('product-id');
        const button = $(this);
        
        $.ajax({
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            method: 'POST',
            data: {
                action: 'add_to_watchlist',
                product_id: productId,
                nonce: '<?php echo wp_create_nonce('watchmodmarket_ajax'); ?>'
            },
            beforeSend: function() {
                button.prop('disabled', true).find('i').removeClass('fa-heart').addClass('fa-spinner fa-spin');
            },
            success: function(response) {
                if (response.success) {
                    button.find('i').removeClass('fa-spinner fa-spin').addClass('fa-check');
                    button.find('span').text('Added to Watchlist');
                    setTimeout(() => {
                        button.find('i').removeClass('fa-check').addClass('fa-heart');
                        button.find('span').text('Remove from Watchlist');
                    }, 1500);
                }
            },
            complete: function() {
                button.prop('disabled', false);
            }
        });
    });
    
    // Share functionality
    $('.share-product').on('click', function() {
        if (navigator.share) {
            navigator.share({
                title: '<?php echo esc_js(get_the_title()); ?>',
                text: '<?php echo esc_js($product->get_short_description()); ?>',
                url: '<?php echo esc_js(get_permalink()); ?>'
            });
        } else {
            // Fallback to copy URL
            const url = '<?php echo esc_js(get_permalink()); ?>';
            const temp = $('<input>');
            $('body').append(temp);
            temp.val(url).select();
            document.execCommand('copy');
            temp.remove();
            
            $(this).find('span').text('Link Copied!');
            setTimeout(() => {
                $(this).find('span').text('Share');
            }, 1500);
        }
    });
});
</script>

<?php get_footer(); ?>