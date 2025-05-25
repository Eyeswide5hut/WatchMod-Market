<?php
/**
 * Template part for displaying the shop filters
 *
 * @package WatchModMarket
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Get active filters
$active_filters = array();

// Category
$current_category = get_queried_object();
$category_id = is_product_category() ? $current_category->term_id : 0;

// Price filter
$min_price = isset($_GET['min_price']) ? floatval($_GET['min_price']) : '';
$max_price = isset($_GET['max_price']) ? floatval($_GET['max_price']) : '';

if (!empty($min_price) || !empty($max_price)) {
    $price_label = '';
    
    if (!empty($min_price) && !empty($max_price)) {
        $price_label = wc_price($min_price) . ' - ' . wc_price($max_price);
    } elseif (!empty($min_price)) {
        $price_label = __('From', 'watchmodmarket') . ' ' . wc_price($min_price);
    } elseif (!empty($max_price)) {
        $price_label = __('Up to', 'watchmodmarket') . ' ' . wc_price($max_price);
    }
    
    if (!empty($price_label)) {
        $active_filters['price'] = array(
            'label' => __('Price', 'watchmodmarket') . ': ' . $price_label,
            'url' => remove_query_arg(array('min_price', 'max_price'))
        );
    }
}

// Tag filter
$current_tag = isset($_GET['product_tag']) ? sanitize_text_field($_GET['product_tag']) : '';
if (!empty($current_tag)) {
    $tag = get_term_by('slug', $current_tag, 'product_tag');
    if ($tag) {
        $active_filters['tag'] = array(
            'label' => __('Tag', 'watchmodmarket') . ': ' . $tag->name,
            'url' => remove_query_arg('product_tag')
        );
    }
}

// Stock status
$stock_status = isset($_GET['stock_status']) ? sanitize_text_field($_GET['stock_status']) : '';
if (!empty($stock_status)) {
    $status_label = '';
    switch ($stock_status) {
        case 'instock':
            $status_label = __('In Stock', 'watchmodmarket');
            break;
        case 'outofstock':
            $status_label = __('Out of Stock', 'watchmodmarket');
            break;
        case 'onbackorder':
            $status_label = __('On Backorder', 'watchmodmarket');
            break;
    }
    
    if (!empty($status_label)) {
        $active_filters['stock'] = array(
            'label' => __('Stock', 'watchmodmarket') . ': ' . $status_label,
            'url' => remove_query_arg('stock_status')
        );
    }
}

// Part type (custom compatibility attribute)
$part_types = array(
    'case' => __('Cases', 'watchmodmarket'),
    'dial' => __('Dials', 'watchmodmarket'),
    'hands' => __('Hands', 'watchmodmarket'),
    'strap' => __('Straps', 'watchmodmarket'),
    'movement' => __('Movements', 'watchmodmarket')
);

$part_type = isset($_GET['filter_part_type']) ? sanitize_text_field($_GET['filter_part_type']) : '';
if (!empty($part_type) && isset($part_types[$part_type])) {
    $active_filters['part_type'] = array(
        'label' => __('Part Type', 'watchmodmarket') . ': ' . $part_types[$part_type],
        'url' => remove_query_arg('filter_part_type')
    );
}

// Get compatibility filter
$compatibility = isset($_GET['filter_compatible_with']) ? sanitize_text_field($_GET['filter_compatible_with']) : '';
if (!empty($compatibility)) {
    $compatibility_term = get_term_by('slug', $compatibility, 'pa_compatible-with');
    if ($compatibility_term) {
        $active_filters['compatibility'] = array(
            'label' => __('Compatible With', 'watchmodmarket') . ': ' . $compatibility_term->name,
            'url' => remove_query_arg('filter_compatible_with')
        );
    }
}

// Get sorting option
$orderby = isset($_GET['orderby']) ? sanitize_text_field($_GET['orderby']) : '';
$sorting_options = array(
    'popularity' => __('Popularity', 'watchmodmarket'),
    'rating' => __('Rating', 'watchmodmarket'),
    'date' => __('Newest', 'watchmodmarket'),
    'price' => __('Price: Low to High', 'watchmodmarket'),
    'price-desc' => __('Price: High to Low', 'watchmodmarket')
);

if (!empty($orderby) && isset($sorting_options[$orderby])) {
    $active_filters['orderby'] = array(
        'label' => __('Sort', 'watchmodmarket') . ': ' . $sorting_options[$orderby],
        'url' => remove_query_arg('orderby')
    );
}

// Search query
$search_query = get_search_query();
if (!empty($search_query)) {
    $active_filters['search'] = array(
        'label' => __('Search', 'watchmodmarket') . ': ' . $search_query,
        'url' => remove_query_arg('s')
    );
}
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
        <?php if (!empty($active_filters)) : ?>
            <div class="active-filters-section">
                <h4><?php echo esc_html__('Active Filters', 'watchmodmarket'); ?></h4>
                <div class="active-filter-pills">
                    <?php foreach ($active_filters as $key => $filter) : ?>
                        <a href="<?php echo esc_url($filter['url']); ?>" class="filter-pill">
                            <?php echo esc_html($filter['label']); ?>
                            <span class="remove-filter">×</span>
                        </a>
                    <?php endforeach; ?>
                    
                    <?php if (count($active_filters) > 1) : ?>
                        <a href="<?php echo esc_url(remove_query_arg(array_keys($active_filters))); ?>" class="clear-all-filters">
                            <?php echo esc_html__('Clear All', 'watchmodmarket'); ?>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
        
        <!-- Filter Options -->
        <div id="filter-options" class="filter-options">
            <form class="filter-form" method="get" action="<?php echo esc_url(wc_get_page_permalink('shop')); ?>">
                <?php
                // Keep any existing query parameters
                foreach ($_GET as $key => $value) {
                    if (!in_array($key, array('min_price', 'max_price', 'filter_part_type', 'filter_compatible_with', 'stock_status', 'orderby'))) {
                        echo '<input type="hidden" name="' . esc_attr($key) . '" value="' . esc_attr($value) . '">';
                    }
                }
                ?>
                
                <!-- Categories Filter -->
                <div class="filter-group">
                    <h4><?php echo esc_html__('Categories', 'watchmodmarket'); ?></h4>
                    <div class="filter-collapsible">
                        <ul class="filter-list">
                            <?php
                            $categories = get_terms(array(
                                'taxonomy' => 'product_cat',
                                'hide_empty' => true,
                                'parent' => 0,
                            ));
                            
                            if (!empty($categories)) {
                                foreach ($categories as $category) {
                                    $category_link = get_term_link($category);
                                    $is_active = $category_id === $category->term_id;
                                    
                                    echo '<li>';
                                    echo '<a href="' . esc_url($category_link) . '" class="' . ($is_active ? 'active' : '') . '">';
                                    echo esc_html($category->name);
                                    echo '<span class="count">(' . esc_html($category->count) . ')</span>';
                                    echo '</a>';
                                    
                                    // Check for subcategories
                                    $subcategories = get_terms(array(
                                        'taxonomy' => 'product_cat',
                                        'hide_empty' => true,
                                        'parent' => $category->term_id,
                                    ));
                                    
                                    if (!empty($subcategories)) {
                                        echo '<ul class="subcategories">';
                                        foreach ($subcategories as $subcategory) {
                                            $subcategory_link = get_term_link($subcategory);
                                            $is_sub_active = $category_id === $subcategory->term_id;
                                            
                                            echo '<li>';
                                            echo '<a href="' . esc_url($subcategory_link) . '" class="' . ($is_sub_active ? 'active' : '') . '">';
                                            echo esc_html($subcategory->name);
                                            echo '<span class="count">(' . esc_html($subcategory->count) . ')</span>';
                                            echo '</a>';
                                            echo '</li>';
                                        }
                                        echo '</ul>';
                                    }
                                    
                                    echo '</li>';
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
                                    <label for="min-price"><?php echo esc_html__('Min', 'watchmodmarket'); ?></label>
                                    <input type="number" id="min-price" name="min_price" value="<?php echo esc_attr($min_price); ?>" min="0" placeholder="0">
                                </div>
                                <div class="price-separator">-</div>
                                <div class="max-price">
                                    <label for="max-price"><?php echo esc_html__('Max', 'watchmodmarket'); ?></label>
                                    <input type="number" id="max-price" name="max_price" value="<?php echo esc_attr($max_price); ?>" min="0" placeholder="<?php echo esc_attr__('Max', 'watchmodmarket'); ?>">
                                </div>
                            </div>
                            
                            <div class="price-quick-links">
                                <a href="<?php echo esc_url(add_query_arg(array('min_price' => 0, 'max_price' => 50))); ?>" class="price-quick-link"><?php echo esc_html__('Under $50', 'watchmodmarket'); ?></a>
                                <a href="<?php echo esc_url(add_query_arg(array('min_price' => 50, 'max_price' => 100))); ?>" class="price-quick-link"><?php echo esc_html__('$50 - $100', 'watchmodmarket'); ?></a>
                                <a href="<?php echo esc_url(add_query_arg(array('min_price' => 100, 'max_price' => 200))); ?>" class="price-quick-link"><?php echo esc_html__('$100 - $200', 'watchmodmarket'); ?></a>
                                <a href="<?php echo esc_url(add_query_arg(array('min_price' => 200, 'max_price' => ''))); ?>" class="price-quick-link"><?php echo esc_html__('Over $200', 'watchmodmarket'); ?></a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Part Type Filter -->
                <div class="filter-group">
                    <h4><?php echo esc_html__('Part Type', 'watchmodmarket'); ?></h4>
                    <div class="filter-collapsible">
                        <ul class="filter-list checkbox-list">
                            <?php foreach ($part_types as $value => $label) : ?>
                                <li>
                                    <label>
                                        <input type="radio" name="filter_part_type" value="<?php echo esc_attr($value); ?>" <?php checked($part_type, $value); ?>>
                                        <?php echo esc_html($label); ?>
                                    </label>
                                </li>
                            <?php endforeach; ?>
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
                                    echo '<li>';
                                    echo '<label>';
                                    echo '<input type="radio" name="filter_compatible_with" value="' . esc_attr($term->slug) . '" ' . checked($compatibility, $term->slug, false) . '>';
                                    echo esc_html($term->name);
                                    echo '<span class="count">(' . esc_html($term->count) . ')</span>';
                                    echo '</label>';
                                    echo '</li>';
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
                                    <input type="radio" name="stock_status" value="instock" <?php checked($stock_status, 'instock'); ?>>
                                    <?php echo esc_html__('In Stock', 'watchmodmarket'); ?>
                                </label>
                            </li>
                            <li>
                                <label>
                                    <input type="radio" name="stock_status" value="outofstock" <?php checked($stock_status, 'outofstock'); ?>>
                                    <?php echo esc_html__('Out of Stock', 'watchmodmarket'); ?>
                                </label>
                            </li>
                            <li>
                                <label>
                                    <input type="radio" name="stock_status" value="onbackorder" <?php checked($stock_status, 'onbackorder'); ?>>
                                    <?php echo esc_html__('On Backorder', 'watchmodmarket'); ?>
                                </label>
                            </li>
                        </ul>
                    </div>
                </div>
                
                <!-- Sorting Options -->
                <div class="filter-group">
                    <h4><?php echo esc_html__('Sort By', 'watchmodmarket'); ?></h4>
                    <div class="filter-collapsible">
                        <select name="orderby" class="orderby">
                            <option value=""><?php echo esc_html__('Default sorting', 'watchmodmarket'); ?></option>
                            <?php foreach ($sorting_options as $id => $name) : ?>
                                <option value="<?php echo esc_attr($id); ?>" <?php selected($orderby, $id); ?>><?php echo esc_html($name); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <!-- Filter Actions -->
                <div class="filter-actions">
                    <button type="submit" class="btn btn-primary filter-apply-btn">
                        <?php echo esc_html__('Apply Filters', 'watchmodmarket'); ?>
                    </button>
                    
                    <a href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>" class="btn btn-secondary filter-reset-btn">
                        <?php echo esc_html__('Reset All', 'watchmodmarket'); ?>
                    </a>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Featured Product -->
    <div class="shop-sidebar-featured">
        <h3><?php echo esc_html__('Featured Product', 'watchmodmarket'); ?></h3>
        <?php
        // Get a featured product
        $args = array(
            'posts_per_page' => 1,
            'post_type' => 'product',
            'tax_query' => array(
                array(
                    'taxonomy' => 'product_visibility',
                    'field' => 'name',
                    'terms' => 'featured',
                ),
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
            // No featured products found
            echo '<p>' . esc_html__('No featured products available.', 'watchmodmarket') . '</p>';
        }
        ?>
    </div>
    
    <!-- Customer Support Section -->
    <div class="shop-sidebar-support">
        <h3><?php echo esc_html__('Need Help?', 'watchmodmarket'); ?></h3>
        <p><?php echo esc_html__('Our team of watch experts is here to help with product selection and compatibility.', 'watchmodmarket'); ?></p>
        <div class="support-options">
            <a href="<?php echo esc_url(get_permalink(get_page_by_path('contact'))); ?>" class="support-option">
                <div class="support-icon">
                    <i class="fa fa-envelope"></i>
                </div>
                <div class="support-text">
                    <?php echo esc_html__('Contact Us', 'watchmodmarket'); ?>
                </div>
            </a>
            
            <a href="<?php echo esc_url(get_permalink(get_page_by_path('faq'))); ?>" class="support-option">
                <div class="support-icon">
                    <i class="fa fa-question-circle"></i>
                </div>
                <div class="support-text">
                    <?php echo esc_html__('FAQ', 'watchmodmarket'); ?>
                </div>
            </a>
        </div>
    </div>
</div>

<script>
(function() {
    // Toggle filter options on mobile
    const toggleButton = document.querySelector('.toggle-filters');
    const filterOptions = document.getElementById('filter-options');
    
    if (toggleButton && filterOptions) {
        toggleButton.addEventListener('click', function() {
            const expanded = this.getAttribute('aria-expanded') === 'true';
            this.setAttribute('aria-expanded', !expanded);
            
            if (expanded) {
                filterOptions.style.display = 'none';
            } else {
                filterOptions.style.display = 'block';
            }
        });
    }
    
    // Filter group collapse functionality
    const filterHeaders = document.querySelectorAll('.filter-group h4');
    
    filterHeaders.forEach(header => {
        header.addEventListener('click', function() {
            const collapsible = this.nextElementSibling;
            const isOpen = collapsible.style.display !== 'none';
            
            if (isOpen) {
                collapsible.style.display = 'none';
                this.classList.add('collapsed');
            } else {
                collapsible.style.display = 'block';
                this.classList.remove('collapsed');
            }
        });
    });
    
    // Initialize price range inputs
    const minPriceInput = document.getElementById('min-price');
    const maxPriceInput = document.getElementById('max-price');
    
    if (minPriceInput && maxPriceInput) {
        minPriceInput.addEventListener('change', function() {
            // Ensure min price is not greater than max price
            if (maxPriceInput.value && parseInt(this.value) > parseInt(maxPriceInput.value)) {
                this.value = maxPriceInput.value;
            }
        });
        
        maxPriceInput.addEventListener('change', function() {
            // Ensure max price is not less than min price
            if (minPriceInput.value && parseInt(this.value) < parseInt(minPriceInput.value)) {
                this.value = minPriceInput.value;
            }
        });
    }
})();
</script>

<style>
/* Basic Filter Styles */
.shop-filters {
    margin-bottom: 30px;
}

.filter-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}

.filter-header h3 {
    margin: 0;
    font-size: 1.25rem;
    font-weight: 700;
    text-transform: uppercase;
}

.toggle-filters {
    display: none; /* Hide on desktop, show on mobile */
    padding: 8px 12px;
    background-color: var(--color-secondary);
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-weight: 600;
    align-items: center;
    gap: 8px;
}

.filter-icon {
    display: flex;
    flex-direction: column;
    gap: 3px;
}

.filter-line {
    width: 16px;
    height: 2px;
    background-color: white;
}

/* Active Filters */
.active-filters-section {
    margin-bottom: 20px;
    padding: 15px;
    background-color: var(--color-background-light);
    border-radius: 4px;
}

.active-filters-section h4 {
    margin: 0 0 10px;
    font-size: 1rem;
    font-weight: 600;
}

.active-filter-pills {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.filter-pill {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 6px 12px;
    background-color: var(--color-white);
    border: 2px solid var(--color-primary);
    border-radius: 20px;
    font-size: 0.9rem;
    font-weight: 500;
    color: var(--color-text);
    text-decoration: none;
    transition: all 0.2s ease;
}

.filter-pill:hover {
    background-color: var(--color-primary-light);
}

.remove-filter {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 16px;
    height: 16px;
    background-color: var(--color-text-light);
    color: white;
    border-radius: 50%;
    font-size: 12px;
    font-weight: 700;
    line-height: 1;
}

.clear-all-filters {
    padding: 6px 12px;
    background-color: var(--color-secondary);
    color: white;
    border-radius: 20px;
    font-size: 0.9rem;
    font-weight: 500;
    text-decoration: none;
    transition: all 0.2s ease;
}

.clear-all-filters:hover {
    opacity: 0.9;
}

/* Filter Groups */
.filter-group {
    margin-bottom: 20px;
    border-bottom: 1px solid var(--color-border);
    padding-bottom: 15px;
}

.filter-group h4 {
    margin: 0 0 10px;
    padding: 0 0 5px;
    font-size: 1rem;
    font-weight: 700;
    cursor: pointer;
    position: relative;
}

.filter-group h4::after {
    content: '-';
    position: absolute;
    right: 0;
    top: 0;
    font-size: 1.2rem;
    font-weight: 700;
}

.filter-group h4.collapsed::after {
    content: '+';
}

.filter-collapsible {
    display: block;
}

/* Filter Lists */
.filter-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.filter-list li {
    margin-bottom: 8px;
}

.filter-list a {
    display: flex;
    justify-content: space-between;
    align-items: center;
    color: var(--color-text);
    text-decoration: none;
    padding: 5px 0;
    transition: color 0.2s ease;
}

.filter-list a:hover {
    color: var(--color-primary);
}

.filter-list a.active {
    color: var(--color-primary);
    font-weight: 600;
}

.count {
    color: var(--color-text-light);
    font-size: 0.9em;
}

/* Checkbox Lists */
.checkbox-list label {
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
}

.checkbox-list input[type="checkbox"],
.checkbox-list input[type="radio"] {
    margin: 0;
}

/* Price Range */
.price-slider-container {
    padding: 10px 0;
}

.price-inputs {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 15px;
}

.min-price,
.max-price {
    flex: 1;
}

.price-inputs label {
    display: block;
    margin-bottom: 5px;
    font-size: 0.9em;
    color: var(--color-text-light);
}

.price-inputs input {
    width: 100%;
    padding: 8px;
    border: 1px solid var(--color-border);
    border-radius: 4px;
}

.price-separator {
    margin-top: 20px;
    font-weight: 600;
}

.price-quick-links {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-top: 10px;
}

.price-quick-link {
    padding: 5px 10px;
    background-color: var(--color-background-light);
    border-radius: 4px;
    font-size: 0.85rem;
    color: var(--color-text);
    text-decoration: none;
    transition: all 0.2s ease;
}

.price-quick-link:hover {
    background-color: var(--color-primary-light);
    color: var(--color-primary);
}

/* Sorting */
.orderby {
    width: 100%;
    padding: 10px;
    border: 1px solid var(--color-border);
    border-radius: 4px;
    background-color: white;
}

/* Filter Actions */
.filter-actions {
    display: flex;
    gap: 10px;
    margin-top: 20px;
}

.filter-apply-btn,
.filter-reset-btn {
    flex: 1;
    padding: 10px;
    border: none;
    border-radius: 4px;
    font-weight: 600;
    text-align: center;
    cursor: pointer;
    transition: all 0.2s ease;
}

.filter-apply-btn {
    background-color: var(--color-primary);
    color: white;
}

.filter-apply-btn:hover {
    background-color: var(--color-primary-dark);
}

.filter-reset-btn {
    background-color: var(--color-secondary);
    color: white;
    text-decoration: none;
}

.filter-reset-btn:hover {
    background-color: var(--color-secondary-dark);
}

/* Featured Product */
.shop-sidebar-featured {
    margin-top: 30px;
    padding: 20px;
    background-color: var(--color-background-light);
    border-radius: 4px;
}

.shop-sidebar-featured h3 {
    margin: 0 0 15px;
    font-size: 1.25rem;
    font-weight: 700;
    text-transform: uppercase;
}

.featured-product {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.featured-product-link {
    display: flex;
    gap: 15px;
    color: var(--color-text);
    text-decoration: none;
}

.featured-product-image {
    width: 80px;
    height: 80px;
    overflow: hidden;
    border-radius: 4px;
    border: 1px solid var(--color-border);
}

.featured-product-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.featured-product-info {
    flex: 1;
}

.featured-product-info h4 {
    margin: 0 0 5px;
    font-size: 1rem;
    font-weight: 600;
}

.featured-product-price {
    font-weight: 700;
    color: var(--color-primary);
    margin-bottom: 5px;
}

.featured-product-rating {
    display: flex;
    align-items: center;
    gap: 5px;
}

.rating-count {
    font-size: 0.85rem;
    color: var(--color-text-light);
}

.featured-add-to-cart {
    display: block;
    padding: 10px;
    background-color: var(--color-primary);
    color: white;
    border-radius: 4px;
    text-align: center;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.2s ease;
}

.featured-add-to-cart:hover {
    background-color: var(--color-primary-dark);
}

/* Support Section */
.shop-sidebar-support {
    margin-top: 30px;
    padding: 20px;
    background-color: var(--color-background-light);
    border-radius: 4px;
}

.shop-sidebar-support h3 {
    margin: 0 0 10px;
    font-size: 1.25rem;
    font-weight: 700;
    text-transform: uppercase;
}

.shop-sidebar-support p {
    margin: 0 0 15px;
    color: var(--color-text);
}

.support-options {
    display: flex;
    gap: 10px;
}

.support-option {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 15px;
    background-color: white;
    border-radius: 4px;
    text-decoration: none;
    color: var(--color-text);
    transition: all 0.2s ease;
}

.support-option:hover {
    transform: translateY(-3px);
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
}

.support-icon {
    font-size: 24px;
    color: var(--color-primary);
    margin-bottom: 10px;
}

.support-text {
    font-weight: 600;
    text-align: center;
}

/* Subcategories */
.subcategories {
    margin-left: 15px;
    padding-left: 10px;
    border-left: 1px solid var(--color-border);
    list-style: none;
}

/* Responsive Styles */
@media (max-width: 991px) {
    .toggle-filters {
        display: flex;
    }
    
    #filter-options {
        display: none;
    }
    
    .shop-sidebar-featured,
    .shop-sidebar-support {
        margin-top: 20px;
    }
}

@media (max-width: 576px) {
    .filter-actions {
        flex-direction: column;
    }
    
    .price-inputs {
        flex-direction: column;
        gap: 15px;
    }
    
    .price-separator {
        display: none;
    }
    
    .support-options {
        flex-direction: column;
    }
}
</style>