<!-- template-parts/shop/filters.php -->

<div class="sidebar-search-box">
    <form role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
        <input type="search" class="search-field" placeholder="<?php esc_attr_e('Search watches, parts...', 'watchmodmarket'); ?>" value="<?php echo get_search_query(); ?>" name="s">
        <button type="submit" class="search-submit">🔍</button>
        <input type="hidden" name="post_type" value="product">
    </form>
</div>

<div class="sidebar-block filter-block">
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
                            the_post_thumbnail('thumbnail', ['alt' => get_the_title()]);
                        } else {
                            // Fixed: Use wc_placeholder_img_src() correctly
                            $placeholder_src = function_exists('wc_placeholder_img_src') ? wc_placeholder_img_src('thumbnail') : '';
                            if ($placeholder_src) {
                                echo '<img src="' . esc_url($placeholder_src) . '" alt="' . esc_attr(get_the_title()) . '" class="wp-post-image" />';
                            }
                        }
                        ?>
                    </div>
                    <div class="featured-product-details">
                        <h4><?php the_title(); ?></h4>
                        <p class="featured-product-description">
                            <?php echo wp_trim_words(get_the_excerpt(), 10); ?>
                        </p>
                        <div class="featured-product-meta">
                            <?php if ($product->get_rating_count() > 0) : ?>
                                <div class="product-rating">
                                    <?php esc_html_e('Rating:', 'watchmodmarket'); ?> <?php echo $product->get_average_rating(); ?>/5
                                </div>
                                <div class="product-reviews">
                                    ★★★★★ (<?php echo $product->get_rating_count(); ?>)
                                </div>
                            <?php endif; ?>
                            <div class="product-price"><?php echo $product->get_price_html(); ?></div>
                        </div>
                    </div>
                </div>
                <?php
            }
            wp_reset_postdata();
        }
        ?>
    </div>

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
                        'number' => 10,
                        'orderby' => 'count',
                        'order' => 'DESC'
                    ]);

                    if (!empty($categories) && !is_wp_error($categories)) {
                        $current_cats = get_query_var('product_cat') ? explode(',', get_query_var('product_cat')) : array();
                        
                        foreach ($categories as $category) {
                            $checked = in_array($category->slug, $current_cats) ? 'checked' : '';
                            ?>
                            <li>
                                <label>
                                    <input type="checkbox" name="category[]" value="<?php echo esc_attr($category->slug); ?>" <?php echo $checked; ?>>
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
                        <input type="number" 
                               id="min-price" 
                               name="min_price" 
                               placeholder="$ 0" 
                               min="0" 
                               max="500" 
                               value="<?php echo isset($_GET['min_price']) ? esc_attr($_GET['min_price']) : '0'; ?>">
                        <span class="price-dash">-</span>
                        <input type="number" 
                               id="max-price" 
                               name="max_price" 
                               placeholder="$ 500" 
                               min="0" 
                               max="500" 
                               value="<?php echo isset($_GET['max_price']) ? esc_attr($_GET['max_price']) : '500'; ?>">
                    </div>
                </div>
            </div>

            <!-- Rating Filter -->
            <div class="filter-group">
                <h4><?php esc_html_e('RATING', 'watchmodmarket'); ?></h4>
                <ul class="filter-list rating-filter">
                    <?php
                    for ($i = 5; $i >= 1; $i--) {
                        $checked = isset($_GET['rating']) && $_GET['rating'] == $i ? 'checked' : '';
                        ?>
                        <li>
                            <label>
                                <input type="checkbox" name="rating" value="<?php echo $i; ?>" <?php echo $checked; ?>>
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
                            <input type="checkbox" name="stock" value="instock" <?php echo isset($_GET['stock']) && $_GET['stock'] == 'instock' ? 'checked' : ''; ?>>
                            <?php esc_html_e('In Stock', 'watchmodmarket'); ?>
                            <span class="count">156</span>
                        </label>
                    </li>
                    <li>
                        <label>
                            <input type="checkbox" name="stock" value="outofstock" <?php echo isset($_GET['stock']) && $_GET['stock'] == 'outofstock' ? 'checked' : ''; ?>>
                            <?php esc_html_e('Out of Stock', 'watchmodmarket'); ?>
                            <span class="count">35</span>
                        </label>
                    </li>
                </ul>
            </div>

            <!-- Filter Actions -->
            <div class="filter-actions">
                <button type="submit" class="btn btn-primary" id="apply-filters">
                    <?php esc_html_e('APPLY FILTERS', 'watchmodmarket'); ?>
                </button>
                <button type="reset" class="btn btn-secondary" id="clear-filters">
                    <?php esc_html_e('CLEAR ALL', 'watchmodmarket'); ?>
                </button>
            </div>
        </form>
    </div>
</div>