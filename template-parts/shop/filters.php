<?php
/**
 * Fixed Template part for displaying the shop filters
 * Replace the content in template-parts/shop/filters.php
 *
 * @package WatchModMarket
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Get active filters from URL
$active_filters = array();
$current_category = get_queried_object();
$category_id = is_product_category() ? $current_category->term_id : 0;

// Price filter
$min_price = isset($_GET['min_price']) ? floatval($_GET['min_price']) : '';
$max_price = isset($_GET['max_price']) ? floatval($_GET['max_price']) : '';

// Other filters
$part_type = isset($_GET['filter_part_type']) ? sanitize_text_field($_GET['filter_part_type']) : '';
$compatibility = isset($_GET['filter_compatible_with']) ? sanitize_text_field($_GET['filter_compatible_with']) : '';
$stock_status = isset($_GET['stock_status']) ? sanitize_text_field($_GET['stock_status']) : '';
$orderby = isset($_GET['orderby']) ? sanitize_text_field($_GET['orderby']) : '';
$search_query = get_search_query();
?>

<div class="shop-filters">
    <div class="filters-wrapper">
        <div class="filter-header">
            <h3><?php echo esc_html__('Filter Products', 'watchmodmarket'); ?></h3>
            <button class="toggle-filters btn-secondary" aria-expanded="true" aria-controls="filter-options">
                <span class="filter-icon">
                    <span class="filter-line"></span>
                    <span class="filter-line"></span>
                    <span class="filter-line"></span>
                </span>
                <span class="filter-text"><?php echo esc_html__('Filters', 'watchmodmarket'); ?></span>
            </button>
        </div>
        
        <!-- Active Filters Section -->
        <div class="active-filters-section" style="<?php echo empty($active_filters) ? 'display: none;' : ''; ?>">
            <h4><?php echo esc_html__('Active Filters', 'watchmodmarket'); ?></h4>
            <div class="active-filter-pills">
                <!-- Active filter pills will be populated by JavaScript -->
            </div>
        </div>
        
        <!-- Filter Options -->
        <div id="filter-options" class="filter-options">
            <form class="filter-form" method="get" action="<?php echo esc_url(wc_get_page_permalink('shop')); ?>" id="shop-filter-form">
                
                <!-- Search Box -->
                <div class="filter-group">
                    <h4><?php echo esc_html__('Search', 'watchmodmarket'); ?></h4>
                    <div class="filter-collapsible">
                        <div class="search-container">
                            <input type="search" 
                                   class="search-field" 
                                   placeholder="<?php esc_attr_e('Search products...', 'watchmodmarket'); ?>" 
                                   value="<?php echo esc_attr($search_query); ?>" 
                                   name="s">
                            <button type="button" class="search-submit">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Categories Filter -->
                <div class="filter-group">
                    <h4><?php echo esc_html__('Categories', 'watchmodmarket'); ?></h4>
                    <div class="filter-collapsible">
                        <ul class="filter-list checkbox-list">
                            <?php
                            $categories = get_terms(array(
                                'taxonomy' => 'product_cat',
                                'hide_empty' => true,
                                'parent' => 0,
                            ));
                            
                            if (!empty($categories) && !is_wp_error($categories)) {
                                foreach ($categories as $category) {
                                    $is_selected = $category_id === $category->term_id;
                                    ?>
                                    <li>
                                        <label>
                                            <input type="checkbox" 
                                                   name="category[]" 
                                                   value="<?php echo esc_attr($category->slug); ?>"
                                                   <?php checked($is_selected); ?>>
                                            <?php echo esc_html($category->name); ?>
                                            <span class="count">(<?php echo esc_html($category->count); ?>)</span>
                                        </label>
                                    </li>
                                    <?php
                                }
                            }
                            ?>
                        </ul>
                    </div>
                </div>

                <!-- Price Range Filter -->
                <div class="filter-group">
                    <h4><?php echo esc_html__('Price Range', 'watchmodmarket'); ?></h4>
                    <div class="filter-collapsible">
                        <div class="price-slider-container">
                            <div class="price-inputs">
                                <div class="min-price">
                                    <label for="min-price"><?php echo esc_html__('Min Price', 'watchmodmarket'); ?></label>
                                    <input type="number" 
                                           id="min-price" 
                                           name="min_price" 
                                           value="<?php echo esc_attr($min_price); ?>" 
                                           min="0" 
                                           placeholder="0"
                                           step="0.01">
                                </div>
                                <div class="price-separator">-</div>
                                <div class="max-price">
                                    <label for="max-price"><?php echo esc_html__('Max Price', 'watchmodmarket'); ?></label>
                                    <input type="number" 
                                           id="max-price" 
                                           name="max_price" 
                                           value="<?php echo esc_attr($max_price); ?>" 
                                           min="0" 
                                           placeholder="<?php echo esc_attr__('Max', 'watchmodmarket'); ?>"
                                           step="0.01">
                                </div>
                            </div>
                            
                            <div class="price-quick-links">
                                <button type="button" class="price-quick-link" data-min="0" data-max="50">
                                    <?php echo esc_html__('Under $50', 'watchmodmarket'); ?>
                                </button>
                                <button type="button" class="price-quick-link" data-min="50" data-max="100">
                                    <?php echo esc_html__('$50 - $100', 'watchmodmarket'); ?>
                                </button>
                                <button type="button" class="price-quick-link" data-min="100" data-max="200">
                                    <?php echo esc_html__('$100 - $200', 'watchmodmarket'); ?>
                                </button>
                                <button type="button" class="price-quick-link" data-min="200" data-max="">
                                    <?php echo esc_html__('Over $200', 'watchmodmarket'); ?>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Part Type Filter -->
                <div class="filter-group">
                    <h4><?php echo esc_html__('Part Type', 'watchmodmarket'); ?></h4>
                    <div class="filter-collapsible">
                        <ul class="filter-list checkbox-list">
                            <?php
                            $part_types = get_terms(array(
                                'taxonomy' => 'pa_part-type',
                                'hide_empty' => true,
                            ));
                            
                            if (!empty($part_types) && !is_wp_error($part_types)) {
                                foreach ($part_types as $type) {
                                    ?>
                                    <li>
                                        <label>
                                            <input type="radio" 
                                                   name="filter_part_type" 
                                                   value="<?php echo esc_attr($type->slug); ?>" 
                                                   <?php checked($part_type, $type->slug); ?>>
                                            <?php echo esc_html($type->name); ?>
                                            <span class="count">(<?php echo esc_html($type->count); ?>)</span>
                                        </label>
                                    </li>
                                    <?php
                                }
                            } else {
                                echo '<li>' . esc_html__('No part types available', 'watchmodmarket') . '</li>';
                            }
                            ?>
                        </ul>
                    </div>
                </div>
                
                <!-- Compatibility Filter -->
                <div class="filter-group">
                    <h4><?php echo esc_html__('Compatible With', 'watchmodmarket'); ?></h4>
                    <div class="filter-collapsible">
                        <ul class="filter-list checkbox-list">
                            <?php
                            $compatibility_terms = get_terms(array(
                                'taxonomy' => 'pa_compatible-with',
                                'hide_empty' => true,
                            ));
                            
                            if (!empty($compatibility_terms) && !is_wp_error($compatibility_terms)) {
                                foreach ($compatibility_terms as $term) {
                                    ?>
                                    <li>
                                        <label>
                                            <input type="radio" 
                                                   name="filter_compatible_with" 
                                                   value="<?php echo esc_attr($term->slug); ?>" 
                                                   <?php checked($compatibility, $term->slug); ?>>
                                            <?php echo esc_html($term->name); ?>
                                            <span class="count">(<?php echo esc_html($term->count); ?>)</span>
                                        </label>
                                    </li>
                                    <?php
                                }
                            } else {
                                echo '<li>' . esc_html__('No compatibility options available', 'watchmodmarket') . '</li>';
                            }
                            ?>
                        </ul>
                    </div>
                </div>
                
                <!-- Stock Status Filter -->
                <div class="filter-group">
                    <h4><?php echo esc_html__('Availability', 'watchmodmarket'); ?></h4>
                    <div class="filter-collapsible">
                        <ul class="filter-list checkbox-list">
                            <li>
                                <label>
                                    <input type="radio" 
                                           name="stock_status" 
                                           value="instock" 
                                           <?php checked($stock_status, 'instock'); ?>>
                                    <?php echo esc_html__('In Stock', 'watchmodmarket'); ?>
                                </label>
                            </li>
                            <li>
                                <label>
                                    <input type="radio" 
                                           name="stock_status" 
                                           value="outofstock" 
                                           <?php checked($stock_status, 'outofstock'); ?>>
                                    <?php echo esc_html__('Out of Stock', 'watchmodmarket'); ?>
                                </label>
                            </li>
                            <li>
                                <label>
                                    <input type="radio" 
                                           name="stock_status" 
                                           value="onbackorder" 
                                           <?php checked($stock_status, 'onbackorder'); ?>>
                                    <?php echo esc_html__('On Backorder', 'watchmodmarket'); ?>
                                </label>
                            </li>
                        </ul>
                    </div>
                </div>
                
                <!-- Rating Filter -->
                <div class="filter-group">
                    <h4><?php echo esc_html__('Rating', 'watchmodmarket'); ?></h4>
                    <div class="filter-collapsible">
                        <ul class="filter-list checkbox-list">
                            <?php for($i = 5; $i >= 1; $i--) : ?>
                                <li>
                                    <label>
                                        <input type="radio" name="rating" value="<?php echo $i; ?>">
                                        <?php echo str_repeat('★', $i) . str_repeat('☆', 5-$i); ?>
                                        <span class="rating-text"><?php echo sprintf(__('%d stars & up', 'watchmodmarket'), $i); ?></span>
                                    </label>
                                </li>
                            <?php endfor; ?>
                        </ul>
                    </div>
                </div>
                
                <!-- Sorting Options -->
                <div class="filter-group">
                    <h4><?php echo esc_html__('Sort By', 'watchmodmarket'); ?></h4>
                    <div class="filter-collapsible">
                        <select name="orderby" class="orderby">
                            <option value=""><?php echo esc_html__('Default sorting', 'watchmodmarket'); ?></option>
                            <option value="popularity" <?php selected($orderby, 'popularity'); ?>><?php echo esc_html__('Popularity', 'watchmodmarket'); ?></option>
                            <option value="rating" <?php selected($orderby, 'rating'); ?>><?php echo esc_html__('Average Rating', 'watchmodmarket'); ?></option>
                            <option value="date" <?php selected($orderby, 'date'); ?>><?php echo esc_html__('Latest', 'watchmodmarket'); ?></option>
                            <option value="price" <?php selected($orderby, 'price'); ?>><?php echo esc_html__('Price: Low to High', 'watchmodmarket'); ?></option>
                            <option value="price-desc" <?php selected($orderby, 'price-desc'); ?>><?php echo esc_html__('Price: High to Low', 'watchmodmarket'); ?></option>
                        </select>
                    </div>
                </div>
                
                <!-- Filter Actions -->
                <div class="filter-actions">
                    <button type="button" class="btn btn-primary filter-apply-btn">
                        <?php echo esc_html__('Apply Filters', 'watchmodmarket'); ?>
                    </button>
                    
                    <button type="button" class="btn btn-secondary filter-reset-btn">
                        <?php echo esc_html__('Reset All', 'watchmodmarket'); ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Featured Product Sidebar -->
    <div class="shop-sidebar-featured">
        <h3><?php echo esc_html__('Featured Product', 'watchmodmarket'); ?></h3>
        <?php
        // Get a featured product
        $args = array(
            'posts_per_page' => 1,
            'post_type' => 'product',
            'meta_query' => array(
                array(
                    'key' => '_featured',
                    'value' => 'yes',
                    'compare' => '='
                )
            ),
        );
        
        $featured_query = new WP_Query($args);
        
        if ($featured_query->have_posts()) {
            while ($featured_query->have_posts()) {
                $featured_query->the_post();
                global $product;
                ?>
                <div class="featured-product">
                    <a href="<?php the_permalink(); ?>" class="featured-product-link">
                        <div class="featured-product-image">
                            <?php
                            if (has_post_thumbnail()) {
                                the_post_thumbnail('thumbnail');
                            } else {
                                echo wc_placeholder_img('thumbnail');
                            }
                            ?>
                        </div>
                        <div class="featured-product-info">
                            <h4><?php the_title(); ?></h4>
                            <div class="featured-product-price">
                                <?php echo $product->get_price_html(); ?>
                            </div>
                            <?php
                            if ($product->get_rating_count() > 0) {
                                echo '<div class="featured-product-rating">';
                                echo wc_get_rating_html($product->get_average_rating(), $product->get_rating_count());
                                echo '<span class="rating-count">(' . $product->get_rating_count() . ')</span>';
                                echo '</div>';
                            }
                            ?>
                        </div>
                    </a>
                    <?php if ($product->is_in_stock()) : ?>
                        <a href="<?php echo esc_url($product->add_to_cart_url()); ?>" class="btn btn-primary featured-add-to-cart">
                            <?php echo esc_html__('Add to Cart', 'watchmodmarket'); ?>
                        </a>
                    <?php endif; ?>
                </div>
                <?php
            }
            wp_reset_postdata();
        } else {
            echo '<p>' . esc_html__('No featured products available.', 'watchmodmarket') . '</p>';
        }
        ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Price quick links
    document.querySelectorAll('.price-quick-link').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            const minPrice = this.dataset.min;
            const maxPrice = this.dataset.max;
            
            document.getElementById('min-price').value = minPrice;
            document.getElementById('max-price').value = maxPrice || '';
            
            // Apply filters after setting price
            if (typeof ShopFilters !== 'undefined') {
                ShopFilters.applyFilters();
            }
        });
    });
    
    // Search submit button
    document.querySelector('.search-submit').addEventListener('click', function(e) {
        e.preventDefault();
        const searchTerm = document.querySelector('.search-field').value;
        
        if (typeof ShopFilters !== 'undefined') {
            ShopFilters.performSearch(searchTerm);
        }
    });
});
</script>