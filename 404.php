<?php
/**
 * The template for displaying 404 pages (not found)
 */

get_header();
?>

<main id="primary" class="site-main">
    <div class="container">
        <section class="error-404 not-found">
            <div class="error-container">
                <div class="error-icon">
                    <img src="<?php echo esc_url(get_template_directory_uri()); ?>/assets/images/broken-watch.svg" alt="<?php esc_attr_e('Broken watch', 'watchmodmarket'); ?>" width="150" height="150">
                </div>
                <header class="page-header">
                    <h1 class="page-title"><?php esc_html_e('404', 'watchmodmarket'); ?></h1>
                    <h2 class="page-subtitle"><?php esc_html_e('Page Not Found', 'watchmodmarket'); ?></h2>
                </header><!-- .page-header -->

                <div class="page-content">
                    <p><?php esc_html_e('It looks like the watch part you\'re looking for is missing! Perhaps it\'s been discontinued or moved to a different section.', 'watchmodmarket'); ?></p>
                    <div class="error-actions">
                        <a href="<?php echo esc_url(home_url('/')); ?>" class="btn btn-primary"><?php esc_html_e('Back to Home', 'watchmodmarket'); ?></a>
                        <a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>" class="btn btn-secondary"><?php esc_html_e('Browse Shop', 'watchmodmarket'); ?></a>
                    </div>
                </div><!-- .page-content -->
                
                <div class="error-suggestions">
                    <h3><?php esc_html_e('You might be interested in:', 'watchmodmarket'); ?></h3>
                    
                    <div class="suggestions-grid">
                        <?php
                        // Display featured products
                        $args = array(
                            'post_type' => 'product',
                            'posts_per_page' => 3,
                            'meta_key' => '_featured',
                            'meta_value' => 'yes',
                        );
                        
                        $featured_products = new WP_Query($args);
                        
                        if ($featured_products->have_posts()) :
                            while ($featured_products->have_posts()) :
                                $featured_products->the_post();
                                global $product;
                                ?>
                                <div class="suggestion-product">
                                    <a href="<?php the_permalink(); ?>">
                                        <?php if (has_post_thumbnail()) : ?>
                                            <?php the_post_thumbnail('medium', array('class' => 'suggestion-image')); ?>
                                        <?php endif; ?>
                                        <h4><?php the_title(); ?></h4>
                                        <div class="suggestion-price"><?php echo $product->get_price_html(); ?></div>
                                    </a>
                                </div>
                                <?php
                            endwhile;
                            wp_reset_postdata();
                        endif;
                        ?>
                    </div>
                </div>
                
                <div class="error-search">
                    <h3><?php esc_html_e('Try searching for something else:', 'watchmodmarket'); ?></h3>
                    <?php get_search_form(); ?>
                </div>
            </div>
        </section><!-- .error-404 -->
    </div>
</main><!-- #main -->

<?php
get_footer();