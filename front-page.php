<?php
/**
 * The front page template file
 */

get_header();
?>

<?php
// Display test promo banner (remove after testing)
// This will always show the banner for now
?>
<section class="promo-banner" aria-label="<?php esc_attr_e('Promotional Banner', 'watchmodmarket'); ?>">
    <div class="container">
        <p>🎉 Become part of a community and make massive savings
            <a href="<?php echo esc_url(wc_get_page_id('group-buy')); ?>"><?php esc_html_e('Today!', 'watchmodmarket'); ?></a>
        </p>
    </div>
</section>

<section class="hero-section">
    <div class="hero-overlay"></div>
    <div class="hero-content container">
        <?php
        // Get hero content from ACF or theme customizer
        $hero_title = get_field('hero_title') ?: get_theme_mod('hero_title', 'Craft Your Signature Timepiece');
        $hero_text = get_field('hero_tagline') ?: get_theme_mod('hero_tagline', 'Premium Parts. Massive Savings. Unlimited Customization.');
        $cta_primary_text = get_field('cta_primary_text') ?: get_theme_mod('cta_primary_text', 'Shop Now');
        $cta_primary_link = get_field('cta_primary_link') ?: get_theme_mod('cta_primary_link', get_permalink(wc_get_page_id('shop')));
        $cta_secondary_text = get_field('cta_secondary_text') ?: get_theme_mod('cta_secondary_text', 'Custom Build');
        $cta_secondary_link = get_field('cta_secondary_link') ?: get_theme_mod('cta_secondary_link', get_permalink(get_page_by_path('builder')));
        ?>
        <h1><?php echo esc_html($hero_title); ?></h1>
        <p class="tagline"><?php echo esc_html($hero_text); ?></p>
        <div class="cta-button-group">
            <?php 
            // Ensure the primary button links to Shop and secondary to Builder
            $shop_page_url = get_permalink(wc_get_page_id('shop'));
            $builder_page_url = get_permalink(get_page_by_path('builder'));
            ?>
            <a href="<?php echo esc_url($shop_page_url ? $shop_page_url : $cta_primary_link); ?>" class="btn btn-primary"><?php echo esc_html($cta_primary_text); ?></a>
            <a href="<?php echo esc_url($builder_page_url ? $builder_page_url : $cta_secondary_link); ?>" class="btn btn-secondary"><?php echo esc_html($cta_secondary_text); ?></a>
        </div>
    </div>
</section>

<section class="featured-categories">
    <div class="container">
        <h2><?php echo esc_html__('Shop by Category', 'watchmodmarket'); ?></h2>
        <div class="category-grid">
            <?php
            // Get categories from taxonomy or WooCommerce product categories
            $categories = get_terms(array(
                'taxonomy' => 'product_cat',
                'hide_empty' => true,
                'parent' => 0,
                'number' => 4,
            ));
            
            if (!empty($categories) && !is_wp_error($categories)) {
                foreach ($categories as $category) {
                    $thumbnail_id = get_term_meta($category->term_id, 'thumbnail_id', true);
                    $image = wp_get_attachment_url($thumbnail_id);
                    ?>
                    <a href="<?php echo esc_url(get_term_link($category)); ?>" class="category-card">
                        <?php if ($image) : ?>
                            <img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr($category->name); ?>" width="300" height="200" loading="lazy">
                        <?php else : ?>
                            <div class="category-placeholder"><?php echo esc_html($category->name); ?></div>
                        <?php endif; ?>
                        <h3><?php echo esc_html($category->name); ?></h3>
                    </a>
                    <?php
                }
            }
            ?>
        </div>
    </div>
</section>

<section class="bestsellers-section">
    <div class="container">
        <div class="section-header">
            <h2><?php echo esc_html__('Best Sellers', 'watchmodmarket'); ?></h2>
            <a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop')) . '?sort=bestselling'); ?>" class="view-all"><?php echo esc_html__('View All', 'watchmodmarket'); ?></a>
        </div>
        <div class="product-slider">
            <?php
            // Get bestselling products
            $args = array(
                'post_type' => 'product',
                'posts_per_page' => 4,
                'meta_key' => 'total_sales',
                'orderby' => 'meta_value_num',
                'order' => 'DESC',
            );
            
            $bestsellers = new WP_Query($args);
            
            if ($bestsellers->have_posts()) {
                while ($bestsellers->have_posts()) {
                    $bestsellers->the_post();
                    wc_get_template_part('content', 'product');
                }
                wp_reset_postdata();
            }
            ?>
        </div>
    </div>
</section>

<section class="featured-collection">
    <div class="container">
        <div class="section-header">
            <h2><?php echo esc_html__('New Arrivals', 'watchmodmarket'); ?></h2>
            <a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop')) . '?filter=new'); ?>" class="view-all"><?php echo esc_html__('View All', 'watchmodmarket'); ?></a>
        </div>
        <div class="product-grid">
            <?php
            // Get new products
            $args = array(
                'post_type' => 'product',
                'posts_per_page' => 3,
                'orderby' => 'date',
                'order' => 'DESC',
            );
            
            $new_products = new WP_Query($args);
            
            if ($new_products->have_posts()) {
                while ($new_products->have_posts()) {
                    $new_products->the_post();
                    wc_get_template_part('content', 'product');
                }
                wp_reset_postdata();
            }
            ?>
        </div>
    </div>
</section>

<section id="custom-builds" class="featured-builds">
    <div class="container">
        <h2><?php echo esc_html__('Custom Build Inspiration', 'watchmodmarket'); ?></h2>
        <p class="section-intro"><?php echo esc_html__('Get inspired by these stunning custom watches built by our community.', 'watchmodmarket'); ?></p>
        <div class="design-grid">
            <?php
            // Get featured custom builds
            $args = array(
                'post_type' => 'watch_build',
                'posts_per_page' => 3,
                'meta_key' => '_featured',
                'meta_value' => 'yes',
            );
            
            $featured_builds = new WP_Query($args);
            
            if ($featured_builds->have_posts()) {
                while ($featured_builds->have_posts()) {
                    $featured_builds->the_post();
                    ?>
                    <div class="design-card">
                        <?php if (has_post_thumbnail()) : ?>
                            <?php the_post_thumbnail('large', array('class' => 'design-image')); ?>
                        <?php endif; ?>
                        <?php
                        // Get design tag if exists
                        $tags = get_the_terms(get_the_ID(), 'post_tag');
                        if (!empty($tags) && !is_wp_error($tags)) {
                            $tag = reset($tags);
                            echo '<span class="design-card-tag">' . esc_html($tag->name) . '</span>';
                        }
                        ?>
                        <h3><?php the_title(); ?></h3>
                        <p class="design-info"><?php echo esc_html__('By:', 'watchmodmarket'); ?> <?php the_author(); ?></p>
                        <div class="design-card-buttons">
                            <a href="<?php the_permalink(); ?>" class="btn btn-primary"><?php echo esc_html__('View Parts', 'watchmodmarket'); ?></a>
                            <a href="<?php echo esc_url(get_permalink(get_page_by_path('builder'))); ?>?template=<?php echo esc_attr(get_post_field('post_name')); ?>" class="btn btn-secondary"><?php echo esc_html__('Build Similar', 'watchmodmarket'); ?></a>
                        </div>
                    </div>
                    <?php
                }
                wp_reset_postdata();
            }
            ?>
        </div>
    </div>
</section>

<section class="newsletter-section">
    <div class="container">
        <div class="newsletter-content">
            <h2><?php echo esc_html__('Join Our Newsletter', 'watchmodmarket'); ?></h2>
            <p><?php echo esc_html__('Subscribe to receive exclusive offers, new product alerts, and 10% off your first order.', 'watchmodmarket'); ?></p>
            <?php
            // Check if Mailchimp for WP is active
            if (function_exists('mc4wp_show_form')) {
                mc4wp_show_form();
            } else {
            ?>
            <form class="newsletter-form" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">
                <input type="hidden" name="action" value="watchmodmarket_newsletter_signup">
                <?php wp_nonce_field('newsletter_signup', 'newsletter_nonce'); ?>
                <input type="email" name="email" placeholder="<?php esc_attr_e('Your email address', 'watchmodmarket'); ?>" required>
                <button type="submit" class="btn btn-primary"><?php echo esc_html__('Subscribe', 'watchmodmarket'); ?></button>
            </form>
            <?php } ?>
        </div>
    </div>
</section>

<section class="instagram-feed">
    <div class="container">
        <h2><?php echo esc_html__('Follow Us on Instagram', 'watchmodmarket'); ?> <a href="<?php echo esc_url(get_theme_mod('social_instagram', 'https://instagram.com/watchmodmarket')); ?>" target="_blank" rel="noopener" class="instagram-handle"><?php echo esc_html(get_theme_mod('instagram_handle', '@watchmodmarket')); ?></a></h2>
        <div class="instagram-grid">
            <?php
            // If using an Instagram plugin, display feed here
            if (function_exists('display_instagram')) {
                display_instagram();
            } else {
                // Otherwise, display static content
                for ($i = 1; $i <= 5; $i++) {
                    $image = get_template_directory_uri() . '/assets/images/instagram/insta' . $i . '.jpg';
                    echo '<a href="' . esc_url(get_theme_mod('social_instagram', 'https://instagram.com/watchmodmarket')) . '" target="_blank" rel="noopener" class="instagram-item">';
                    echo '<img src="' . esc_url($image) . '" alt="' . esc_attr__('Instagram Post', 'watchmodmarket') . '" width="200" height="200" loading="lazy">';
                    echo '</a>';
                }
            }
            ?>
        </div>
    </div>
</section>

<section class="usp-section">
    <div class="container">
        <div class="usp-grid">
            <div class="usp-item">
                <div class="usp-icon" aria-hidden="true">🚚</div>
                <h3><?php echo esc_html__('Free Shipping', 'watchmodmarket'); ?></h3>
                <p><?php echo esc_html__('On all orders over $100', 'watchmodmarket'); ?></p>
            </div>
            <div class="usp-item">
                <div class="usp-icon" aria-hidden="true">🔄</div>
                <h3><?php echo esc_html__('Easy Returns', 'watchmodmarket'); ?></h3>
                <p><?php echo esc_html__('30-day money-back guarantee', 'watchmodmarket'); ?></p>
            </div>
            <div class="usp-item">
                <div class="usp-icon" aria-hidden="true">🔒</div>
                <h3><?php echo esc_html__('Secure Checkout', 'watchmodmarket'); ?></h3>
                <p><?php echo esc_html__('100% protected payments', 'watchmodmarket'); ?></p>
            </div>
            <div class="usp-item">
                <div class="usp-icon" aria-hidden="true">💬</div>
                <h3><?php echo esc_html__('Expert Support', 'watchmodmarket'); ?></h3>
                <p><?php echo esc_html__('Available 7 days a week', 'watchmodmarket'); ?></p>
            </div>
        </div>
    </div>
</section>

<?php
get_footer();