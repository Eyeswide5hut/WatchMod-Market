<?php
/**
 * Template Name: Group Buy Page
 * 
 * The template for displaying group buys and pre-orders
 */

get_header();
?>

<main id="primary" class="site-main group-buy-page">
    <!-- Hero Section -->
    <div class="group-hero">
        <div class="container">
            <h1><?php echo esc_html__('Group Buy Portal', 'watchmodmarket'); ?></h1>
            <p class="tagline"><?php echo esc_html__('Exclusive Group Buys & Pre-Orders for Premium Watch Parts at Unbeatable Prices', 'watchmodmarket'); ?></p>
        </div>
    </div>
    
    <!-- Active Group Buys -->
    <section id="active-buys" class="active-group-buys">
        <div class="container">
            <div class="section-header">
                <h2><?php echo esc_html__('Active Group Buys', 'watchmodmarket'); ?></h2>
                <p class="section-subheading"><?php echo esc_html__('Join these limited-time opportunities before they\'re gone', 'watchmodmarket'); ?></p>
            </div>
            
            <div class="filter-bar">
                <div class="filter-options">
                    <span><?php echo esc_html__('Filter:', 'watchmodmarket'); ?></span>
                    <select id="category-filter">
                        <option value="all"><?php echo esc_html__('All Categories', 'watchmodmarket'); ?></option>
                        <?php
                        // Get product categories
                        $categories = get_terms(array(
                            'taxonomy' => 'product_cat',
                            'hide_empty' => true,
                        ));
                        
                        foreach ($categories as $category) {
                            echo '<option value="' . esc_attr($category->slug) . '">' . esc_html($category->name) . '</option>';
                        }
                        ?>
                    </select>
                    
                    <select id="price-filter">
                        <option value="all"><?php echo esc_html__('All Prices', 'watchmodmarket'); ?></option>
                        <option value="under-50"><?php echo esc_html__('Under $50', 'watchmodmarket'); ?></option>
                        <option value="50-100"><?php echo esc_html__('$50 - $100', 'watchmodmarket'); ?></option>
                        <option value="100-200"><?php echo esc_html__('$100 - $200', 'watchmodmarket'); ?></option>
                        <option value="over-200"><?php echo esc_html__('Over $200', 'watchmodmarket'); ?></option>
                    </select>
                </div>
                
                <div class="sort-options">
                    <span><?php echo esc_html__('Sort by:', 'watchmodmarket'); ?></span>
                    <select id="sort-options">
                        <option value="ending-soon"><?php echo esc_html__('Ending Soon', 'watchmodmarket'); ?></option>
                        <option value="newest"><?php echo esc_html__('Newest', 'watchmodmarket'); ?></option>
                        <option value="popularity"><?php echo esc_html__('Popularity', 'watchmodmarket'); ?></option>
                        <option value="discount"><?php echo esc_html__('Biggest Discount', 'watchmodmarket'); ?></option>
                    </select>
                    
                    <div class="view-options">
                        <button class="view-option active" title="<?php echo esc_attr__('Grid View', 'watchmodmarket'); ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M1 2.5A1.5 1.5 0 0 1 2.5 1h3A1.5 1.5 0 0 1 7 2.5v3A1.5 1.5 0 0 1 5.5 7h-3A1.5 1.5 0 0 1 1 5.5v-3zm8 0A1.5 1.5 0 0 1 10.5 1h3A1.5 1.5 0 0 1 15 2.5v3A1.5 1.5 0 0 1 13.5 7h-3A1.5 1.5 0 0 1 9 5.5v-3zm-8 8A1.5 1.5 0 0 1 2.5 9h3A1.5 1.5 0 0 1 7 10.5v3A1.5 1.5 0 0 1 5.5 15h-3A1.5 1.5 0 0 1 1 13.5v-3zm8 0A1.5 1.5 0 0 1 10.5 9h3a1.5 1.5 0 0 1 1.5 1.5v3a1.5 1.5 0 0 1-1.5 1.5h-3a1.5 1.5 0 0 1-1.5-1.5v-3z"/>
                            </svg>
                        </button>
                        <button class="view-option" title="<?php echo esc_attr__('List View', 'watchmodmarket'); ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5z"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="group-buy-grid">
                <?php
                // Query for active group buys
                $args = array(
                    'post_type' => 'product',
                    'posts_per_page' => 6,
                    'meta_query' => array(
                        array(
                            'key' => '_groupbuy_status',
                            'value' => 'active',
                            'compare' => '=',
                        ),
                    ),
                    'tax_query' => array(
                        array(
                            'taxonomy' => 'product_tag',
                            'field' => 'slug',
                            'terms' => 'group-buy',
                        ),
                    ),
                );
                
                $group_buy_query = new WP_Query($args);
                
                if ($group_buy_query->have_posts()) :
                    while ($group_buy_query->have_posts()) : $group_buy_query->the_post();
                        global $product;
                        
                        // Get group buy meta data
                        $regular_price = $product->get_regular_price();
                        $group_price = get_post_meta(get_the_ID(), '_groupbuy_price', true);
                        $slots_total = get_post_meta(get_the_ID(), '_groupbuy_slots_total', true);
                        $slots_filled = get_post_meta(get_the_ID(), '_groupbuy_slots_filled', true);
                        $end_date = get_post_meta(get_the_ID(), '_groupbuy_end_date', true);
                        $progress_percent = ($slots_filled / $slots_total) * 100;
                        
                        // Calculate days left
                        $days_left = ceil((strtotime($end_date) - current_time('timestamp')) / (60 * 60 * 24));
                        
                        // Fix: Convert strings to floats before calculation
                        $discount_percent = 0;
                        if (!empty($regular_price) && !empty($group_price) && floatval($regular_price) > 0) {
                            $discount_percent = round(((floatval($regular_price) - floatval($group_price)) / floatval($regular_price)) * 100);
                        }
                        ?>
                        
                        <div class="group-buy-card">
                            <div class="group-buy-badge"><?php echo esc_html($discount_percent); ?>% <?php echo esc_html__('OFF', 'watchmodmarket'); ?></div>
                            
                            <div class="group-buy-image">
                                <?php if (has_post_thumbnail()) : ?>
                                    <?php the_post_thumbnail('medium', array('class' => 'card-image')); ?>
                                <?php endif; ?>
                            </div>
                            
                            <div class="group-buy-content">
                                <h3 class="group-buy-title">
                                    <a href="<?php the_permalink(); ?>">
                                        <?php the_title(); ?>
                                    </a>
                                </h3>
                                
                                <div class="group-buy-pricing">
                                    <div class="price-compare">
                                        <span class="regular-price"><?php echo get_woocommerce_currency_symbol() . $regular_price; ?></span>
                                        <span class="group-price"><?php echo get_woocommerce_currency_symbol() . $group_price; ?></span>
                                    </div>
                                    <div class="slots-info">
                                        <span class="slots-count"><?php echo sprintf(esc_html__('%d/%d slots filled', 'watchmodmarket'), $slots_filled, $slots_total); ?></span>
                                    </div>
                                </div>
                                
                                <div class="progress-bar-container">
                                    <div class="progress-bar" style="width: <?php echo esc_attr($progress_percent); ?>%"></div>
                                </div>
                                
                                <div class="group-buy-meta">
                                    <div class="time-left">
                                        <?php if ($days_left > 0) : ?>
                                            <span class="days-count"><?php echo esc_html($days_left); ?></span>
                                            <span class="days-label"><?php echo esc_html(_n('day left', 'days left', $days_left, 'watchmodmarket')); ?></span>
                                        <?php else : ?>
                                            <span class="ending-soon"><?php echo esc_html__('Ending soon!', 'watchmodmarket'); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <a href="#" class="btn btn-primary btn-join" data-product-id="<?php echo esc_attr($product->get_id()); ?>">
                                        <?php echo esc_html__('Join Now', 'watchmodmarket'); ?>
                                    </a>
                                </div>
                                
                                <div class="card-actions">
                                    <button class="btn btn-secondary card-action-btn quick-view-btn" data-product-id="<?php echo esc_attr(get_the_ID()); ?>">
                                        <?php echo esc_html__('Quick View', 'watchmodmarket'); ?>
                                    </button>
                                    <button class="btn btn-secondary card-action-btn wishlist-btn" data-product-id="<?php echo esc_attr(get_the_ID()); ?>">
                                        <?php echo esc_html__('Add to Wishlist', 'watchmodmarket'); ?>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                    <?php
                    endwhile;
                    wp_reset_postdata();
                else :
                    ?>
                    <div class="no-group-buys">
                        <p><?php echo esc_html__('No active group buys found at the moment. Please check back soon!', 'watchmodmarket'); ?></p>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="more-btn-container">
                <a href="<?php echo esc_url(get_permalink(get_page_by_path('group-buys'))); ?>" class="btn btn-secondary">
                    <?php echo esc_html__('View All Group Buys', 'watchmodmarket'); ?>
                </a>
            </div>
        </div>
    </section>
    
    <!-- Upcoming Pre-Orders -->
    <section id="upcoming" class="upcoming-preorders">
        <div class="container">
            <div class="section-header">
                <h2><?php echo esc_html__('Upcoming Pre-Orders', 'watchmodmarket'); ?></h2>
                <p class="section-subheading"><?php echo esc_html__('Reserve these exclusive items before they officially launch', 'watchmodmarket'); ?></p>
            </div>
            
            <div class="preorder-grid">
                <?php
                // Query for upcoming pre-orders
                $args = array(
                    'post_type' => 'product',
                    'posts_per_page' => 3,
                    'meta_query' => array(
                        array(
                            'key' => '_preorder_status',
                            'value' => 'upcoming',
                            'compare' => '=',
                        ),
                    ),
                    'tax_query' => array(
                        array(
                            'taxonomy' => 'product_tag',
                            'field' => 'slug',
                            'terms' => 'pre-order',
                        ),
                    ),
                );
                
                $preorder_query = new WP_Query($args);
                
                if ($preorder_query->have_posts()) :
                    while ($preorder_query->have_posts()) : $preorder_query->the_post();
                        global $product;
                        
                        // Get pre-order meta data
                        $release_date = get_post_meta(get_the_ID(), '_preorder_release_date', true);
                        $preorder_price = get_post_meta(get_the_ID(), '_preorder_price', true);
                        $deposit_amount = get_post_meta(get_the_ID(), '_preorder_deposit', true);
                        $limited_qty = get_post_meta(get_the_ID(), '_preorder_limited_qty', true);
                        $is_exclusive = get_post_meta(get_the_ID(), '_preorder_exclusive', true);
                        
                        // Format release date
                        $release_date_formatted = date_i18n(get_option('date_format'), strtotime($release_date));
                        ?>
                        
                        <div class="preorder-card">
                            <div class="preorder-tag">
                                <?php echo esc_html__('Pre-Order', 'watchmodmarket'); ?>
                            </div>
                            
                            <?php if ($is_exclusive == 'yes') : ?>
                                <div class="exclusive-tag">
                                    <?php echo esc_html__('Exclusive', 'watchmodmarket'); ?>
                                </div>
                            <?php endif; ?>
                            
                            <div class="preorder-image">
                                <?php if (has_post_thumbnail()) : ?>
                                    <?php the_post_thumbnail('medium', array('class' => 'card-image')); ?>
                                <?php endif; ?>
                            </div>
                            
                            <div class="preorder-content">
                                <h3 class="preorder-title">
                                    <a href="<?php the_permalink(); ?>">
                                        <?php the_title(); ?>
                                    </a>
                                </h3>
                                
                                <div class="preorder-excerpt">
                                    <?php the_excerpt(); ?>
                                </div>
                                
                                <div class="preorder-details">
                                    <div class="preorder-price">
                                        <span class="price-label"><?php echo esc_html__('Price', 'watchmodmarket'); ?></span>
                                        <span class="price-value"><?php echo get_woocommerce_currency_symbol() . $preorder_price; ?></span>
                                    </div>
                                    <div class="deposit-amount">
                                        <span class="deposit-label"><?php echo esc_html__('Deposit', 'watchmodmarket'); ?></span>
                                        <span class="deposit-value"><?php echo get_woocommerce_currency_symbol() . $deposit_amount; ?></span>
                                    </div>
                                </div>
                                
                                <div class="release-date">
                                    <span class="date-label"><?php echo esc_html__('Estimated Release', 'watchmodmarket'); ?></span>
                                    <span class="date-value"><?php echo esc_html($release_date_formatted); ?></span>
                                </div>
                                
                                <?php if ($limited_qty) : ?>
                                    <div class="limited-qty">
                                        <span class="limited-label"><?php echo esc_html__('Limited Quantity:', 'watchmodmarket'); ?></span>
                                        <span class="limited-value"><?php echo esc_html($limited_qty); ?> <?php echo esc_html__('units', 'watchmodmarket'); ?></span>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="preorder-footer">
                                    <a href="<?php the_permalink(); ?>" class="btn btn-primary btn-reserve">
                                        <?php echo esc_html__('Reserve Now', 'watchmodmarket'); ?>
                                    </a>
                                    <a href="<?php the_permalink(); ?>" class="preorder-more">
                                        <?php echo esc_html__('Learn More', 'watchmodmarket'); ?> →
                                    </a>
                                </div>
                            </div>
                        </div>
                    
                    <?php
                    endwhile;
                    wp_reset_postdata();
                else :
                    ?>
                    <div class="no-preorders">
                        <p><?php echo esc_html__('No upcoming pre-orders available at the moment. Please check back soon!', 'watchmodmarket'); ?></p>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="more-btn-container">
                <a href="<?php echo esc_url(get_permalink(get_page_by_path('pre-orders'))); ?>" class="btn btn-secondary">
                    <?php echo esc_html__('View All Pre-Orders', 'watchmodmarket'); ?>
                </a>
            </div>
        </div>
    </section>

    <!-- How It Works -->
    <section id="how-it-works" class="how-it-works">
        <div class="container">
            <div class="section-header">
                <h2><?php echo esc_html__('How It Works', 'watchmodmarket'); ?></h2>
                <p class="section-subheading"><?php echo esc_html__('Shop smarter with our community-driven buying programs', 'watchmodmarket'); ?></p>
            </div>
            
            <div class="process-grid">
                <div class="process-item">
                    <div class="process-number">1</div>
                    <div class="process-icon" aria-hidden="true">👥</div>
                    <h3><?php echo esc_html__('Join a Group Buy', 'watchmodmarket'); ?></h3>
                    <p><?php echo esc_html__('Reserve your spot in an active group buy to access exclusive pricing on premium parts. No payment until the minimum order is reached.', 'watchmodmarket'); ?></p>
                </div>
                
                <div class="process-item">
                    <div class="process-number">2</div>
                    <div class="process-icon" aria-hidden="true">💰</div>
                    <h3><?php echo esc_html__('Secure Your Price', 'watchmodmarket'); ?></h3>
                    <p><?php echo esc_html__('Once the target is met, pay the discounted price to confirm your participation. Save 20-40% off retail on premium components.', 'watchmodmarket'); ?></p>
                </div>
                
                <div class="process-item">
                    <div class="process-number">3</div>
                    <div class="process-icon" aria-hidden="true">⏱️</div>
                    <h3><?php echo esc_html__('Track Production', 'watchmodmarket'); ?></h3>
                    <p><?php echo esc_html__('Get regular updates on your order. Most group buys ship in 4-12 weeks. We keep you informed every step of the way.', 'watchmodmarket'); ?></p>
                </div>
                
                <div class="process-item">
                    <div class="process-number">4</div>
                    <div class="process-icon" aria-hidden="true">📦</div>
                    <h3><?php echo esc_html__('Receive Your Parts', 'watchmodmarket'); ?></h3>
                    <p><?php echo esc_html__('Your premium components arrive directly to your door. All parts come with our standard warranty and satisfaction guarantee.', 'watchmodmarket'); ?></p>
                </div>
            </div>
        </div>
    </section>
    
    <!-- FAQ Section -->
    <section id="faq" class="faq-section">
        <div class="container">
            <div class="section-header">
                <h2><?php echo esc_html__('Frequently Asked Questions', 'watchmodmarket'); ?></h2>
                <p class="section-subheading"><?php echo esc_html__('Everything you need to know about Group Buys and Pre-Orders', 'watchmodmarket'); ?></p>
            </div>
            
            <div class="faq-grid">
                <div class="faq-item">
                    <h3 class="faq-question" id="faq1">
                        <button class="faq-toggle" aria-expanded="false" aria-controls="faq-answer-1">
                            <?php echo esc_html__('What is the difference between a Group Buy and a Pre-Order?', 'watchmodmarket'); ?>
                            <span class="toggle-icon">+</span>
                        </button>
                    </h3>
                    <div id="faq-answer-1" class="faq-answer" hidden>
                        <p><?php echo esc_html__('A Group Buy aggregates multiple buyers to achieve bulk pricing discounts on existing products. Pre-Orders secure your place in line for upcoming or limited-edition items before they\'re officially released, often with special early-bird pricing.', 'watchmodmarket'); ?></p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <h3 class="faq-question" id="faq2">
                        <button class="faq-toggle" aria-expanded="false" aria-controls="faq-answer-2">
                            <?php echo esc_html__('How much deposit is required for Pre-Orders?', 'watchmodmarket'); ?>
                            <span class="toggle-icon">+</span>
                        </button>
                    </h3>
                    <div id="faq-answer-2" class="faq-answer" hidden>
                        <p><?php echo esc_html__('Deposit amounts vary by product, but typically range from 20-30% of the total pre-order price. The specific amount is clearly displayed on each pre-order listing. The remaining balance is charged before shipping.', 'watchmodmarket'); ?></p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <h3 class="faq-question" id="faq3">
                        <button class="faq-toggle" aria-expanded="false" aria-controls="faq-answer-3">
                            <?php echo esc_html__('What happens if a Group Buy doesn\'t reach its minimum participants?', 'watchmodmarket'); ?>
                            <span class="toggle-icon">+</span>
                        </button>
                    </h3>
                    <div id="faq-answer-3" class="faq-answer" hidden>
                        <p><?php echo esc_html__('If a Group Buy doesn\'t reach its minimum threshold, you\'ll receive a full refund of your payment. We\'ll notify you via email about the status of your refund, which typically processes within 3-5 business days.', 'watchmodmarket'); ?></p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <h3 class="faq-question" id="faq4">
                        <button class="faq-toggle" aria-expanded="false" aria-controls="faq-answer-4">
                            <?php echo esc_html__('Can I cancel my participation in a Group Buy or Pre-Order?', 'watchmodmarket'); ?>
                            <span class="toggle-icon">+</span>
                        </button>
                    </h3>
                    <div id="faq-answer-4" class="faq-answer" hidden>
                        <p><?php echo esc_html__('For Group Buys, you can cancel and receive a full refund before the end date. Once the Group Buy closes, cancellations aren\'t possible. For Pre-Orders, deposits are generally non-refundable as they secure production capacity, but each listing specifies the exact cancellation policy.', 'watchmodmarket'); ?></p>
                    </div>
                </div>
            </div>
            
            <div class="faq-more">
                <a href="<?php echo esc_url(get_permalink(get_page_by_path('faq'))); ?>" class="btn btn-secondary">
                    <?php echo esc_html__('View All FAQs', 'watchmodmarket'); ?>
                </a>
            </div>
        </div>
    </section>
   
    
    <!-- Trust Badges Section -->
    <section class="trust-badges-section">
        <div class="container">
            <div class="badges-grid">
                <div class="badge-item">
                    <div class="badge-icon">🔒</div>
                    <div class="badge-text">
                        <h3><?php echo esc_html__('Secure Payments', 'watchmodmarket'); ?></h3>
                        <p><?php echo esc_html__('All transactions are encrypted and secure', 'watchmodmarket'); ?></p>
                    </div>
                </div>
                
                <div class="badge-item">
                    <div class="badge-icon">🛡️</div>
                    <div class="badge-text">
                        <h3><?php echo esc_html__('Money-Back Guarantee', 'watchmodmarket'); ?></h3>
                        <p><?php echo esc_html__('Full refund if group buy doesn\'t proceed', 'watchmodmarket'); ?></p>
                    </div>
                </div>
                
                <div class="badge-item">
                    <div class="badge-icon">📦</div>
                    <div class="badge-text">
                        <h3><?php echo esc_html__('Quality Assured', 'watchmodmarket'); ?></h3>
                        <p><?php echo esc_html__('Same warranty as our regular products', 'watchmodmarket'); ?></p>
                    </div>
                </div>
                
                <div class="badge-item">
                    <div class="badge-icon">💯</div>
                    <div class="badge-text">
                        <h3><?php echo esc_html__('98% Success Rate', 'watchmodmarket'); ?></h3>
                        <p><?php echo esc_html__('Nearly all our group buys reach their goal', 'watchmodmarket'); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php
/**
 * Newsletter Section Template
 * 
 * This can be included in front-page.php or as a template part
 */
?>

<style>
/* Enhanced Newsletter Styles */
.newsletter-section {
    background: linear-gradient(135deg, #f8f8f8 0%, #e8e8e8 100%);
    padding: 80px 0;
    position: relative;
    overflow: hidden;
    border-top: 3px solid var(--color-dark);
    border-bottom: 3px solid var(--color-dark);
}

.newsletter-section::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-image: url('<?php echo esc_url(get_template_directory_uri()); ?>/assets/images/pattern.svg');
    opacity: 0.1;
    pointer-events: none;
}

.newsletter-container {
    display: flex;
    align-items: center;
    gap: 40px;
    position: relative;
    z-index: 2;
}

.newsletter-content {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 30px;
}

.newsletter-text {
    margin-bottom: 20px;
}

.newsletter-text h2 {
    font-size: 2.5rem;
    margin-bottom: 15px;
    color: var(--color-dark);
}

.newsletter-description {
    font-size: 1.1rem;
    color: var(--color-text);
    margin-bottom: 20px;
    line-height: 1.6;
}

.benefits-list {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    margin-top: 20px;
}

.benefit-item {
    display: flex;
    align-items: center;
    gap: 10px;
    background-color: var(--color-white);
    border: 2px solid var(--color-primary);
    border-radius: 6px;
    padding: 10px 15px;
    font-weight: 500;
    transition: transform 0.3s ease;
}

.benefit-item:hover {
    transform: translateY(-3px);
}

.benefit-item i {
    color: var(--color-primary);
    font-size: 1.2rem;
}

.newsletter-form-container {
    width: 100%;
    background-color: var(--color-white);
    border: 3px solid var(--color-dark);
    border-radius: 8px;
    padding: 30px;
    box-shadow: 5px 5px 0 var(--color-dark);
}

.form-row {
    display: flex;
    gap: 15px;
    margin-bottom: 20px;
}

.form-group {
    flex: 1;
    margin-bottom: 15px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 500;
    color: var(--color-dark);
}

.form-group input {
    width: 100%;
    padding: 12px 15px;
    border: 2px solid var(--color-dark);
    border-radius: 4px;
    font-family: var(--font-body);
    transition: border-color 0.3s ease, background-color 0.3s ease;
}

.form-group input:focus {
    border-color: var(--color-primary);
    background-color: #f8f8ff;
    outline: none;
}

.form-preferences {
    margin-bottom: 20px;
}

.form-preferences p {
    margin-bottom: 10px;
    font-weight: 500;
}

.checkbox-label {
    display: inline-flex;
    align-items: center;
    margin-right: 20px;
    cursor: pointer;
}

.checkbox-label input[type="checkbox"] {
    width: 18px;
    height: 18px;
    margin-right: 8px;
}

.privacy-note {
    font-size: 0.85rem;
    color: #666;
    margin-bottom: 20px;
}

.btn-subscribe {
    width: 100%;
    padding: 14px 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    font-size: 1.1rem;
    font-weight: 700;
    text-transform: uppercase;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.btn-subscribe:hover {
    transform: translate(-3px, -3px);
    box-shadow: 5px 5px 0 var(--color-dark);
}

.newsletter-image {
    flex: 1;
    position: relative;
    display: none; /* Hide on small screens */
}

.newsletter-image img {
    max-width: 100%;
    height: auto;
    border: 3px solid var(--color-dark);
    border-radius: 8px;
    box-shadow: 8px 8px 0 var(--color-dark);
    transform: rotate(2deg);
}

.newsletter-testimonials {
    margin-top: 50px;
    padding-top: 30px;
    border-top: 1px solid #ddd;
}

.testimonial-slider {
    display: flex;
    overflow-x: auto;
    gap: 30px;
    padding: 10px 0;
    scrollbar-width: none; /* Firefox */
}

.testimonial-slider::-webkit-scrollbar {
    display: none; /* Chrome, Safari, Edge */
}

.testimonial-item {
    display: flex;
    align-items: center;
    gap: 20px;
    background-color: var(--color-white);
    border: 2px solid var(--color-dark);
    border-radius: 8px;
    padding: 20px;
    min-width: 350px;
    box-shadow: 3px 3px 0 var(--color-dark);
}

.testimonial-avatar img {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    border: 2px solid var(--color-primary);
}

.testimonial-content p {
    font-style: italic;
    margin-bottom: 10px;
}

.testimonial-author {
    display: block;
    font-weight: 500;
    color: var(--color-dark);
}

/* Media Queries */
@media (min-width: 768px) {
    .form-row {
        flex-direction: row;
    }
}

@media (min-width: 992px) {
    .newsletter-image {
        display: block;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Simple testimonial slider 
    const testimonialSlider = document.querySelector('.testimonial-slider');
    
    if (testimonialSlider && testimonialSlider.children.length > 1) {
        let currentSlide = 0;
        const slides = testimonialSlider.children;
        const totalSlides = slides.length;
        
        // Auto-scroll testimonials
        setInterval(() => {
            currentSlide = (currentSlide + 1) % totalSlides;
            testimonialSlider.scrollTo({
                left: slides[currentSlide].offsetLeft - testimonialSlider.offsetLeft,
                behavior: 'smooth'
            });
        }, 5000);
    }
    
    // Form validation with visual feedback
    const newsletterForm = document.querySelector('.newsletter-form');
    if (newsletterForm) {
        newsletterForm.addEventListener('submit', function(e) {
            let valid = true;
            const nameInput = document.getElementById('newsletter-name');
            const emailInput = document.getElementById('newsletter-email');
            
            // Validate name
            if (!nameInput.value.trim()) {
                nameInput.style.borderColor = 'red';
                valid = false;
            } else {
                nameInput.style.borderColor = '';
            }
            
            // Validate email
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailPattern.test(emailInput.value)) {
                emailInput.style.borderColor = 'red';
                valid = false;
            } else {
                emailInput.style.borderColor = '';
            }
            
            if (!valid) {
                e.preventDefault();
            }
        });
    }
});
</script>

<?php
// Include scripts specifically for this page
wp_enqueue_style('group-buy-style', get_template_directory_uri() . '/assets/css/group-buy.css', array(), '1.0.0');
wp_enqueue_script('group-buy-script', get_template_directory_uri() . '/assets/js/group-buy.js', array('jquery'), '1.0.0', true);

get_footer();
?>