<?php
/**
 * Template Name: Shop Page
 * 
 * The template for displaying the shop page to match the reference design
 */

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
                <span><?php esc_html_e('Shop', 'watchmodmarket'); ?></span>
            </div>
        </nav>
    </div>
</div>

<!-- Shop Hero Section -->
<div class="shop-hero page-header">
    <div class="container">
        <h1><?php esc_html_e('Premium Watch Parts & Accessories', 'watchmodmarket'); ?></h1>
        <p class="tagline"><?php esc_html_e('Professional grade components for watch enthusiasts and horologists', 'watchmodmarket'); ?></p>
        
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

<!-- Main Shop Container -->
<div class="container shop-container">
    <div class="shop-main">
        <!-- Sidebar with Search and Filters -->
        <aside class="shop-sidebar">
            <!-- Search Box -->
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
                                    $name = $category->name;
                                    $count = $category->count;
                                    ?>
                                    <li>
                                        <label>
                                            <input type="checkbox" name="category" value="<?php echo esc_attr($category->slug); ?>">
                                            <?php echo esc_html($name); ?>
                                            <span class="count"><?php echo esc_html($count); ?></span>
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
                            <li>
                                <label>
                                    <input type="checkbox" name="rating" value="5">
                                    ★★★★★
                                    <span class="count">57</span>
                                </label>
                            </li>
                            <li>
                                <label>
                                    <input type="checkbox" name="rating" value="4">
                                    ★★★★☆
                                    <span class="count">6</span>
                                </label>
                            </li>
                            <li>
                                <label>
                                    <input type="checkbox" name="rating" value="3">
                                    ★★★☆☆
                                    <span class="count">59</span>
                                </label>
                            </li>
                            <li>
                                <label>
                                    <input type="checkbox" name="rating" value="2">
                                    ★★☆☆☆
                                    <span class="count">13</span>
                                </label>
                            </li>
                            <li>
                                <label>
                                    <input type="checkbox" name="rating" value="1">
                                    ★☆☆☆☆
                                    <span class="count">8</span>
                                </label>
                            </li>
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
                </div>
            </div>
        </aside>

        <!-- Main Shop Content -->
        <main class="shop-content">
            <!-- Top Bar -->
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

            <!-- Active Filters -->
            <div class="active-filters">
                <span class="filter-tag">Cases <button class="remove-filter">×</button></span>
                <span class="filter-tag">$50 - $200 <button class="remove-filter">×</button></span>
                <span class="filter-tag">In Stock <button class="remove-filter">×</button></span>
                <button class="clear-all"><?php esc_html_e('Clear All', 'watchmodmarket'); ?></button>
            </div>

            <!-- Product Grid -->
            <div class="shop-products product-grid">
                <?php
                $args = array(
                    'post_type' => 'product',
                    'posts_per_page' => 12,
                    'order' => 'DESC',
                    'orderby' => 'date'
                );
                
                $shop_query = new WP_Query($args);
                
                if ($shop_query->have_posts()) {
                    while ($shop_query->have_posts()) {
                        $shop_query->the_post();
                        global $product;
                        ?>
                        <div class="product-card" data-product-id="<?php echo esc_attr($product->get_id()); ?>">
                            <?php
                            // Add badges
                            $is_featured = $product->is_featured();
                            $is_new = (get_post_time('U') > strtotime('-30 days'));
                            
                            if ($is_featured) :
                                echo '<div class="product-badge best-seller">' . esc_html__('BEST SELLER', 'watchmodmarket') . '</div>';
                            elseif ($is_new) :
                                echo '<div class="product-badge new">' . esc_html__('NEW', 'watchmodmarket') . '</div>';
                            endif;
                            ?>
                            
                            <div class="product-image">
                                <a href="<?php the_permalink(); ?>">
                                    <?php
                                    if (has_post_thumbnail()) {
                                        the_post_thumbnail('woocommerce_thumbnail', ['alt' => get_the_title()]);
                                    } else {
                                        // Fixed: Use wc_placeholder_img_src() correctly
                                        $placeholder_src = function_exists('wc_placeholder_img_src') ? wc_placeholder_img_src('woocommerce_thumbnail') : '';
                                        if ($placeholder_src) {
                                            echo '<img src="' . esc_url($placeholder_src) . '" alt="' . esc_attr(get_the_title()) . '" class="wp-post-image" />';
                                        }
                                    }
                                    ?>
                                </a>
                            </div>
                            <div class="product-info">
                                <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                                <div class="product-details">
                                    <?php echo wp_trim_words(get_the_excerpt(), 10); ?>
                                </div>
                                <div class="product-meta">
                                    <span class="watch-label"><?php esc_html_e('WATCH', 'watchmodmarket'); ?></span>
                                    <span class="product-price">
                                        <?php echo $product->get_price_html(); ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                    wp_reset_postdata();
                }
                ?>
            </div>

            <!-- Pagination -->
            <nav class="pagination">
                <a href="#" class="page-link active">1</a>
                <a href="#" class="page-link">2</a>
                <a href="#" class="page-link">3</a>
                <a href="#" class="page-link">4</a>
                <span class="page-dots">...</span>
                <a href="#" class="page-link">16</a>
                <a href="#" class="page-link next">Next</a>
            </nav>
        </main>
    </div>
</div>

<?php
get_footer();