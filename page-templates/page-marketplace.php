<?php
/**
 * Template Name: Marketplace Page
 * 
 * The template for displaying the watch marketplace page
 */

get_header();
?>

<main id="primary" class="site-main">
    <div class="container marketplace-container">
        <!-- Marketplace Banner -->
        <div class="marketplace-banner">
            <div class="marketplace-banner-content">
                <h1><?php echo esc_html__('Watch Marketplace', 'watchmodmarket'); ?></h1>
                <p class="marketplace-tagline"><?php echo esc_html__('Buy and sell watches directly from the community', 'watchmodmarket'); ?></p>
                <a href="<?php echo esc_url(get_permalink(get_page_by_path('sell-watch'))); ?>" class="btn btn-primary btn-large">
                    <?php echo esc_html__('Sell Your Watch Here!', 'watchmodmarket'); ?>
                </a>
            </div>
        </div>

        <!-- Marketplace Benefits -->
        <div class="marketplace-benefits">
            <h2><?php echo esc_html__('Why Sell With Us', 'watchmodmarket'); ?></h2>
            <div class="benefits-grid">
                <div class="benefit-item">
                    <div class="benefit-icon">💰</div>
                    <h3><?php echo esc_html__('Lower Fees', 'watchmodmarket'); ?></h3>
                    <p><?php echo esc_html__('Only 5% commission compared to 10-15% on other platforms', 'watchmodmarket'); ?></p>
                </div>
                <div class="benefit-item">
                    <div class="benefit-icon">👥</div>
                    <h3><?php echo esc_html__('Targeted Audience', 'watchmodmarket'); ?></h3>
                    <p><?php echo esc_html__('Connect with serious watch enthusiasts who appreciate value', 'watchmodmarket'); ?></p>
                </div>
                <div class="benefit-item">
                    <div class="benefit-icon">🛡️</div>
                    <h3><?php echo esc_html__('Secure Transactions', 'watchmodmarket'); ?></h3>
                    <p><?php echo esc_html__('Protected payments and identity verification for peace of mind', 'watchmodmarket'); ?></p>
                </div>
            </div>
        </div>

        <!-- Marketplace Filter -->
        <div class="marketplace-filters">
            <form class="filter-form" method="get">
                <div class="filters-row">
                    <div class="filter-group">
                        <label for="brand-filter"><?php echo esc_html__('Brand', 'watchmodmarket'); ?></label>
                        <select id="brand-filter" name="brand">
                            <option value=""><?php echo esc_html__('All Brands', 'watchmodmarket'); ?></option>
                            <option value="rolex"><?php echo esc_html__('Rolex', 'watchmodmarket'); ?></option>
                            <option value="omega"><?php echo esc_html__('Omega', 'watchmodmarket'); ?></option>
                            <option value="seiko"><?php echo esc_html__('Seiko', 'watchmodmarket'); ?></option>
                            <option value="tudor"><?php echo esc_html__('Tudor', 'watchmodmarket'); ?></option>
                            <option value="grand-seiko"><?php echo esc_html__('Grand Seiko', 'watchmodmarket'); ?></option>
                            <option value="other"><?php echo esc_html__('Other Brands', 'watchmodmarket'); ?></option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="price-min"><?php echo esc_html__('Price Range', 'watchmodmarket'); ?></label>
                        <div class="price-inputs">
                            <input type="number" id="price-min" name="price_min" placeholder="<?php echo esc_attr__('Min $', 'watchmodmarket'); ?>" min="0">
                            <span class="price-separator">-</span>
                            <input type="number" id="price-max" name="price_max" placeholder="<?php echo esc_attr__('Max $', 'watchmodmarket'); ?>" min="0">
                        </div>
                    </div>
                    <div class="filter-group">
                        <label for="condition-filter"><?php echo esc_html__('Condition', 'watchmodmarket'); ?></label>
                        <select id="condition-filter" name="condition">
                            <option value=""><?php echo esc_html__('Any Condition', 'watchmodmarket'); ?></option>
                            <option value="new"><?php echo esc_html__('New', 'watchmodmarket'); ?></option>
                            <option value="like-new"><?php echo esc_html__('Like New', 'watchmodmarket'); ?></option>
                            <option value="excellent"><?php echo esc_html__('Excellent', 'watchmodmarket'); ?></option>
                            <option value="good"><?php echo esc_html__('Good', 'watchmodmarket'); ?></option>
                            <option value="fair"><?php echo esc_html__('Fair', 'watchmodmarket'); ?></option>
                        </select>
                    </div>
                    <div class="filter-actions">
                        <button type="submit" class="btn btn-secondary"><?php echo esc_html__('Apply Filters', 'watchmodmarket'); ?></button>
                        <a href="<?php echo esc_url(get_permalink()); ?>" class="reset-filters"><?php echo esc_html__('Reset', 'watchmodmarket'); ?></a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Marketplace Listings -->
        <div class="marketplace-listings">
            <h2 class="section-title"><?php echo esc_html__('Available Watches', 'watchmodmarket'); ?></h2>
            
            <?php
            // Get the marketplace listings
            $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
            
            // Get filter values
            $brand = isset($_GET['brand']) ? sanitize_text_field($_GET['brand']) : '';
            $price_min = isset($_GET['price_min']) ? floatval($_GET['price_min']) : 0;
            $price_max = isset($_GET['price_max']) ? floatval($_GET['price_max']) : 0;
            $condition = isset($_GET['condition']) ? sanitize_text_field($_GET['condition']) : '';
            
            // Set up meta query
            $meta_query = array('relation' => 'AND');
            
            if (!empty($brand)) {
                $meta_query[] = array(
                    'key' => '_watch_brand',
                    'value' => $brand,
                    'compare' => '='
                );
            }
            
            if ($price_min > 0 && $price_max > 0) {
                $meta_query[] = array(
                    'key' => '_watch_price',
                    'value' => array($price_min, $price_max),
                    'type' => 'NUMERIC',
                    'compare' => 'BETWEEN'
                );
            } elseif ($price_min > 0) {
                $meta_query[] = array(
                    'key' => '_watch_price',
                    'value' => $price_min,
                    'type' => 'NUMERIC',
                    'compare' => '>='
                );
            } elseif ($price_max > 0) {
                $meta_query[] = array(
                    'key' => '_watch_price',
                    'value' => $price_max,
                    'type' => 'NUMERIC',
                    'compare' => '<='
                );
            }
            
            if (!empty($condition)) {
                $meta_query[] = array(
                    'key' => '_watch_condition',
                    'value' => $condition,
                    'compare' => '='
                );
            }
            
            $args = array(
                'post_type' => 'watch_listing',
                'posts_per_page' => 10,
                'paged' => $paged,
                'meta_query' => $meta_query,
                'meta_key' => '_watch_price',
                'orderby' => 'meta_value_num',
                'order' => 'DESC'
            );
            
            $listings_query = new WP_Query($args);
            
            if ($listings_query->have_posts()) :
                echo '<div class="listings-grid">';
                
                while ($listings_query->have_posts()) : $listings_query->the_post();
                    $price = get_post_meta(get_the_ID(), '_watch_price', true);
                    $brand = get_post_meta(get_the_ID(), '_watch_brand', true);
                    $model = get_post_meta(get_the_ID(), '_watch_model', true);
                    $year = get_post_meta(get_the_ID(), '_watch_year', true);
                    $condition = get_post_meta(get_the_ID(), '_watch_condition', true);
                    $seller_id = get_post_meta(get_the_ID(), '_watch_seller_id', true);
                    $seller_name = get_the_author_meta('display_name', $seller_id);
                    $seller_rating = get_user_meta($seller_id, '_seller_rating', true);
                    
                    // Format seller rating
                    $rating_display = '';
                    if (!empty($seller_rating)) {
                        $rating_display = number_format($seller_rating, 1) . '/5.0';
                    } else {
                        $rating_display = 'New Seller';
                    }
                    ?>
                    
                    <div class="listing-card">
                        <?php if (has_post_thumbnail()) : ?>
                            <div class="listing-image">
                                <a href="<?php the_permalink(); ?>">
                                    <?php the_post_thumbnail('medium'); ?>
                                </a>
                            </div>
                        <?php endif; ?>
                        
                        <div class="listing-content">
                            <h3 class="listing-title">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h3>
                            
                            <div class="listing-meta">
                                <?php if (!empty($brand)) : ?>
                                    <span class="listing-brand"><?php echo esc_html($brand); ?></span>
                                <?php endif; ?>
                                
                                <?php if (!empty($model)) : ?>
                                    <span class="listing-model"><?php echo esc_html($model); ?></span>
                                <?php endif; ?>
                                
                                <?php if (!empty($year)) : ?>
                                    <span class="listing-year"><?php echo esc_html($year); ?></span>
                                <?php endif; ?>
                                
                                <?php if (!empty($condition)) : ?>
                                    <span class="listing-condition"><?php echo esc_html(ucfirst($condition)); ?></span>
                                <?php endif; ?>
                            </div>
                            
                            <div class="listing-description">
                                <?php the_excerpt(); ?>
                            </div>
                            
                            <div class="listing-footer">
                                <div class="listing-price">
                                    <?php echo '$' . number_format($price, 2); ?>
                                </div>
                                
                                <div class="listing-seller">
                                    <span class="seller-name">Seller: <?php echo esc_html($seller_name); ?></span>
                                    <span class="seller-rating">★ <?php echo esc_html($rating_display); ?></span>
                                </div>
                                
                                <div class="listing-actions">
                                    <a href="<?php echo esc_url(get_permalink() . '?action=message'); ?>" class="btn btn-secondary">
                                        <i class="fa fa-comment"></i> Message Seller
                                    </a>
                                    <a href="<?php echo esc_url(get_permalink() . '?action=offer'); ?>" class="btn btn-primary">
                                        <i class="fa fa-tag"></i> Make Offer
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                <?php endwhile;
                
                echo '</div>'; // End listings-grid
                
                // Display pagination
                echo '<div class="marketplace-pagination">';
                    echo paginate_links(array(
                        'total' => $listings_query->max_num_pages,
                        'current' => $paged,
                        'prev_text' => '&laquo; Previous',
                        'next_text' => 'Next &raquo;',
                    ));
                echo '</div>';
                
                wp_reset_postdata();
                
            else :
                ?>
                <div class="no-listings">
                    <div class="no-listings-icon">🔍</div>
                    <h3><?php echo esc_html__('No watch listings found', 'watchmodmarket'); ?></h3>
                    <p><?php echo esc_html__('There are currently no watches that match your search criteria. Try adjusting your filters or check back soon.', 'watchmodmarket'); ?></p>
                    <a href="<?php echo esc_url(get_permalink(get_page_by_path('sell-watch'))); ?>" class="btn btn-primary">
                        <?php echo esc_html__('Be the first to list a watch!', 'watchmodmarket'); ?>
                    </a>
                </div>
            <?php endif; ?>
        </div>

        <!-- Marketplace Info -->
        <div class="marketplace-info">
            <div class="info-box">
                <h3><?php echo esc_html__('How Our Marketplace Works', 'watchmodmarket'); ?></h3>
                <div class="info-steps">
                    <div class="info-step">
                        <div class="step-number">1</div>
                        <h4><?php echo esc_html__('List Your Watch', 'watchmodmarket'); ?></h4>
                        <p><?php echo esc_html__('Create a listing with photos, details, and your asking price', 'watchmodmarket'); ?></p>
                    </div>
                    <div class="info-step">
                        <div class="step-number">2</div>
                        <h4><?php echo esc_html__('Connect With Buyers', 'watchmodmarket'); ?></h4>
                        <p><?php echo esc_html__('Receive offers and messages from interested buyers', 'watchmodmarket'); ?></p>
                    </div>
                    <div class="info-step">
                        <div class="step-number">3</div>
                        <h4><?php echo esc_html__('Secure Transaction', 'watchmodmarket'); ?></h4>
                        <p><?php echo esc_html__('Use our secure payment system for a protected exchange', 'watchmodmarket'); ?></p>
                    </div>
                </div>
                <div class="info-cta">
                    <a href="<?php echo esc_url(get_permalink(get_page_by_path('marketplace-guide'))); ?>" class="btn btn-secondary">
                        <?php echo esc_html__('View Full Marketplace Guide', 'watchmodmarket'); ?>
                    </a>
                </div>
            </div>
        </div>
    </div>
</main>

<?php
// Add marketplace-specific CSS
wp_enqueue_style('watchmodmarket-marketplace', get_template_directory_uri() . '/assets/css/marketplace.css', array(), WATCHMODMARKET_VERSION);

// Add marketplace-specific JS
wp_enqueue_script('watchmodmarket-marketplace', get_template_directory_uri() . '/assets/js/marketplace.js', array('jquery'), WATCHMODMARKET_VERSION, true);

get_footer();
?>