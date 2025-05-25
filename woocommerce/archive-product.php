<?php
/**
 * The Template for displaying product archives, including the main shop page
 */

defined('ABSPATH') || exit;

get_header('shop');

// Remove WooCommerce content hooks that might interfere
remove_action('woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
remove_action('woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);
?>

<!-- Breadcrumb -->
<div class="breadcrumb">
    <div class="container">
        <nav class="breadcrumb-nav" aria-label="<?php esc_attr_e('Breadcrumb', 'watchmodmarket'); ?>">
            <div class="breadcrumb-item">
                <a href="<?php echo home_url(); ?>"><?php esc_html_e('Home', 'watchmodmarket'); ?></a>
            </div>
            <div class="breadcrumb-item">
                <span><?php esc_html_e('Shop', 'watchmodmarket'); ?></span>
            </div>
        </nav>
    </div>
</div>

<!-- Shop Hero Section -->
<div class="shop-hero page-header">
    <div class="container">
        <h1>
            <?php
            if (is_product_category()) {
                single_cat_title();
            } elseif (is_search()) {
                /* translators: %s: search query */
                printf(esc_html__('Search Results for: %s', 'watchmodmarket'), '<span>' . get_search_query() . '</span>');
            } else {
                esc_html_e('Premium Watch Parts & Accessories', 'watchmodmarket');
            }
            ?>
        </h1>
        <p class="tagline">
            <?php
            if (is_product_category()) {
                the_archive_description();
            } else {
                esc_html_e('Professional grade components for watch enthusiasts and horologists', 'watchmodmarket');
            }
            ?>
        </p>
        
        <div class="shop-hero-features">
            <div class="hero-feature">
                <span class="hero-feature-icon">⭐</span>
                <span><?php esc_html_e('Premium Quality', 'watchmodmarket'); ?></span>
            </div>
            <div class="hero-feature">
                <span class="hero-feature-icon">🔒</span>
                <span><?php esc_html_e('Secure Payment', 'watchmodmarket'); ?></span>
            </div>
            <div class="hero-feature">
                <span class="hero-feature-icon">🚚</span>
                <span><?php esc_html_e('Fast Shipping', 'watchmodmarket'); ?></span>
            </div>
        </div>
    </div>
</div>

<div class="container shop-container">
    <div class="shop-main">
        <aside class="shop-sidebar">
            <button class="filter-toggle-btn" aria-expanded="false" aria-controls="filter-block">
                <?php esc_html_e('Filter Products', 'watchmodmarket'); ?> <span aria-hidden="true">▼</span>
            </button>

            <div id="filter-block" class="filter-block">
                <!-- Search Box -->
                <div class="sidebar-search-box">
                    <form role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
                        <input type="search" class="search-field" placeholder="<?php esc_attr_e('Search watches, parts...', 'watchmodmarket'); ?>" value="<?php echo get_search_query(); ?>" name="s">
                        <button type="submit" class="search-submit">🔍</button>
                        <input type="hidden" name="post_type" value="product">
                    </form>
                </div>

                <div class="sidebar-block">
                    <!-- Featured Products Header -->
                    <div class="featured-products-header">
                        <h3><?php esc_html_e('BEST SELLER', 'watchmodmarket'); ?></h3>
                    </div>
                    
                    <!-- Best Seller Product -->
                <div class="featured-product-wrapper">
                    <?php
                    $args = array(
                        'post_type' => 'product',
                        'posts_per_page' => 1,
                        'meta_key' => 'total_sales',
                        'orderby' => 'meta_value_num',
                        'order' => 'DESC',
                    );
                    
                    $featured_products = new WP_Query($args);
                    
                    if ($featured_products->have_posts()) {
                        while ($featured_products->have_posts()) {
                            $featured_products->the_post();
                            global $product;
                            ?>
                            <div class="featured-product-item">
                                <div class="featured-product-image">
                                    <?php
                                    if (has_post_thumbnail()) {
                                        the_post_thumbnail('thumbnail', array('alt' => get_the_title()));
                                    } else {
                                        // Fallback placeholder
                                        echo '<img src="' . esc_url(wc_placeholder_img_src('thumbnail')) . '" alt="' . esc_attr(get_the_title()) . '" class="wp-post-image" />';
                                    }
                                    ?>
                                </div>
                                <div class="featured-product-details">
                                    <h4><?php the_title(); ?></h4>
                                    <p class="featured-product-description">
                                        <?php echo wp_trim_words(get_the_excerpt(), 10); ?>
                                    </p>
                                    <div class="featured-product-meta">
                                        <?php if ($product && $product->get_rating_count() > 0) : ?>
                                            <div class="product-rating">
                                                <?php esc_html_e('Rating:', 'watchmodmarket'); ?> <?php echo esc_html($product->get_average_rating()); ?>/5
                                            </div>
                                            <div class="product-reviews">
                                                ★★★★★ (<?php echo esc_html($product->get_rating_count()); ?>)
                                            </div>
                                        <?php endif; ?>
                                        <div class="product-price"><?php echo wp_kses_post($product->get_price_html()); ?></div>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                        wp_reset_postdata();
                    } else {
                        ?>
                        <div class="featured-product-item">
                            <div class="featured-product-image">
                                <img src="<?php echo esc_url(wc_placeholder_img_src('thumbnail')); ?>" alt="<?php esc_attr_e('No product available', 'watchmodmarket'); ?>" />
                            </div>
                            <div class="featured-product-details">
                                <h4><?php esc_html_e('Premium Watch Parts', 'watchmodmarket'); ?></h4>
                                <p class="featured-product-description">
                                    <?php esc_html_e('Discover our collection of premium watch components and accessories.', 'watchmodmarket'); ?>
                                </p>
                                <div class="featured-product-meta">
                                    <div class="product-price"><?php esc_html_e('From $29.99', 'watchmodmarket'); ?></div>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                    ?>

                    <!-- Filter Section -->
                    <div class="filter-section">
                        <div class="filter-header">
                            <h3><?php esc_html_e('FILTER BY', 'watchmodmarket'); ?></h3>
                        </div>
                        
                        <form method="get" id="shop-filter-form">
                            <!-- Categories Filter -->
                            <div class="filter-group">
                                <h4><?php esc_html_e('CATEGORIES', 'watchmodmarket'); ?></h4>
                                <ul class="filter-list">
                                    <?php
                                    $categories = get_terms([
                                        'taxonomy' => 'product_cat',
                                        'hide_empty' => true,
                                        'parent' => 0,
                                    ]);

                                    if (!empty($categories) && !is_wp_error($categories)) {
                                        foreach ($categories as $category) {
                                            ?>
                                            <li>
                                                <label>
                                                    <input type="checkbox" name="category[]" value="<?php echo esc_attr($category->slug); ?>">
                                                    <?php echo esc_html($category->name); ?>
                                                    <span class="count"><?php echo esc_html($category->count); ?></span>
                                                </label>
                                            </li>
                                            <?php
                                        }
                                    }
                                    ?>
                                </ul>
                            </div>

                            <!-- Price Range Filter -->
                            <div class="filter-group">
                                <h4><?php esc_html_e('PRICE RANGE', 'watchmodmarket'); ?></h4>
                                <div class="price-range-container">
                                    <div class="price-inputs-row">
                                        <input type="number" id="min-price" placeholder="$ 0" min="0" max="500" value="0">
                                        <span class="price-dash">-</span>
                                        <input type="number" id="max-price" placeholder="$ 500" min="0" max="500" value="500">
                                    </div>
                                </div>
                            </div>

                            <!-- Rating Filter -->
                            <div class="filter-group">
                                <h4><?php esc_html_e('RATING', 'watchmodmarket'); ?></h4>
                                <ul class="filter-list rating-filter">
                                    <?php
                                    for($i = 5; $i >= 1; $i--) {
                                        ?>
                                        <li>
                                            <label>
                                                <input type="checkbox" name="rating" value="<?php echo $i; ?>">
                                                <?php echo str_repeat('★', $i) . str_repeat('☆', 5-$i); ?>
                                                <span class="count"><?php echo rand(5, 60); ?></span>
                                            </label>
                                        </li>
                                        <?php
                                    }
                                    ?>
                                </ul>
                            </div>

                            <!-- Availability Filter -->
                            <div class="filter-group">
                                <h4><?php esc_html_e('AVAILABILITY', 'watchmodmarket'); ?></h4>
                                <ul class="filter-list">
                                    <li>
                                        <label>
                                            <input type="checkbox" name="stock" value="instock">
                                            <?php esc_html_e('In Stock', 'watchmodmarket'); ?>
                                            <span class="count">156</span>
                                        </label>
                                    </li>
                                    <li>
                                        <label>
                                            <input type="checkbox" name="stock" value="outofstock">
                                            <?php esc_html_e('Out of Stock', 'watchmodmarket'); ?>
                                            <span class="count">35</span>
                                        </label>
                                    </li>
                                </ul>
                            </div>

                            <!-- Filter Actions -->
                            <div class="filter-actions">
                                <button type="button" class="btn btn-primary" id="apply-filters">
                                    <?php esc_html_e('APPLY FILTERS', 'watchmodmarket'); ?>
                                </button>
                                <button type="button" class="btn btn-secondary" id="clear-filters">
                                    <?php esc_html_e('CLEAR ALL', 'watchmodmarket'); ?>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </aside>

        <main id="main-content" class="shop-content">
            <div class="shop-top-bar">
                <div class="shop-results-text">
                    <?php esc_html_e('Showing', 'watchmodmarket'); ?> 
                    <strong>1-12</strong> 
                    <?php esc_html_e('of', 'watchmodmarket'); ?> 
                    <strong>191</strong> 
                    <?php esc_html_e('products', 'watchmodmarket'); ?>
                </div>
                <div class="shop-view-options">
                    <div class="sort-by">
                        <?php esc_html_e('Sort by:', 'watchmodmarket'); ?>
                        <select name="orderby" id="orderby" class="orderby">
                            <option value="featured"><?php esc_html_e('Featured', 'watchmodmarket'); ?></option>
                            <option value="popularity"><?php esc_html_e('Popularity', 'watchmodmarket'); ?></option>
                            <option value="rating"><?php esc_html_e('Rating', 'watchmodmarket'); ?></option>
                            <option value="date"><?php esc_html_e('Latest', 'watchmodmarket'); ?></option>
                            <option value="price"><?php esc_html_e('Price: Low to High', 'watchmodmarket'); ?></option>
                            <option value="price-desc"><?php esc_html_e('Price: High to Low', 'watchmodmarket'); ?></option>
                        </select>
                    </div>
                    <div class="view-toggle">
                        <button class="btn grid-view active" aria-label="Grid view">⊞</button>
                        <button class="btn list-view" aria-label="List view">≡</button>
                    </div>
                </div>
            </div>

            <div id="active-filters" class="active-filters" aria-live="polite">
                <!-- Active filters will be added here dynamically -->
            </div>

            <?php
            if (woocommerce_product_loop()) {
                woocommerce_product_loop_start();

                if (wc_get_loop_prop('total')) {
                    while (have_posts()) {
                        the_post();
                        // Use our enhanced product card template
                        wc_get_template_part('content', 'product');
                    }
                }

                woocommerce_product_loop_end();

                woocommerce_pagination();
            } else {
                ?>
                <div id="no-results" aria-live="polite">
                    <p><?php esc_html_e('No products match the selected filters.', 'watchmodmarket'); ?></p>
                    <button class="btn btn-secondary" id="clear-filters-no-results"><?php esc_html_e('Clear Filters', 'watchmodmarket'); ?></button>
                </div>
                <?php
            }
            ?>
        </main>
    </div>
</div>

<?php
get_footer('shop');