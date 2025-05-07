<?php
/**
 * Template part for displaying custom watch builds
 *
 * @package WatchModMarket
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class('build-card'); ?>>
    <?php if (has_post_thumbnail()) : ?>
        <div class="build-image">
            <a href="<?php the_permalink(); ?>">
                <?php the_post_thumbnail('medium', array('class' => 'build-thumbnail fade-in-image')); ?>
            </a>
            <?php 
            $visibility = get_post_meta(get_the_ID(), '_build_visibility', true);
            $featured = get_post_meta(get_the_ID(), '_featured', true);
            
            if ($featured === 'yes') : ?>
                <div class="build-badge featured"><?php echo esc_html__('Featured', 'watchmodmarket'); ?></div>
            <?php endif; ?>
            
            <div class="build-badge visibility <?php echo esc_attr($visibility); ?>">
                <?php echo $visibility === 'public' ? esc_html__('Public', 'watchmodmarket') : esc_html__('Private', 'watchmodmarket'); ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="build-content">
        <header class="entry-header">
            <?php
            if (is_singular()) :
                the_title('<h1 class="entry-title">', '</h1>');
            else :
                the_title('<h2 class="entry-title"><a href="' . esc_url(get_permalink()) . '" rel="bookmark">', '</a></h2>');
            endif;
            ?>

            <div class="entry-meta">
                <span class="posted-on">
                    <?php
                    printf(
                        /* translators: %s: post date. */
                        esc_html_x('Created on %s', 'post date', 'watchmodmarket'),
                        '<a href="' . esc_url(get_permalink()) . '" rel="bookmark">' . esc_html(get_the_date()) . '</a>'
                    );
                    ?>
                </span>
                <span class="byline">
                    <?php
                    printf(
                        /* translators: %s: post author. */
                        esc_html_x('by %s', 'post author', 'watchmodmarket'),
                        '<span class="author vcard"><a class="url fn n" href="' . esc_url(get_author_posts_url(get_the_author_meta('ID'))) . '">' . esc_html(get_the_author()) . '</a></span>'
                    );
                    ?>
                </span>
            </div><!-- .entry-meta -->
        </header><!-- .entry-header -->

        <?php if (is_singular()) : ?>
            <div class="entry-content">
                <?php
                the_content();

                // If on single page, show component details
                if (function_exists('watchmodmarket_display_watch_components')) {
                    do_action('watchmodmarket_after_build_content');
                }
                ?>
            </div><!-- .entry-content -->
        <?php else : ?>
            <div class="entry-summary">
                <?php the_excerpt(); ?>
                
                <?php 
                // Display build components preview
                $components = get_post_meta(get_the_ID(), '_watch_components', true);
                if (is_array($components) && !empty($components)) {
                    echo '<div class="component-preview">';
                    
                    // Count the number of components used
                    $component_count = count(array_filter($components));
                    echo '<span class="component-count">' . 
                         sprintf(esc_html(_n('%d component', '%d components', $component_count, 'watchmodmarket')), $component_count) . 
                         '</span>';
                    
                    // Calculate total price
                    $total_price = 0;
                    foreach ($components as $id) {
                        if (!empty($id)) {
                            $price = get_post_meta($id, '_price', true);
                            if ($price) {
                                $total_price += floatval($price);
                            }
                        }
                    }
                    
                    if ($total_price > 0) {
                        echo '<span class="build-price">' . wc_price($total_price) . '</span>';
                    }
                    
                    echo '</div>';
                }
                ?>
            </div><!-- .entry-summary -->
            
            <div class="build-actions">
                <a href="<?php the_permalink(); ?>" class="btn btn-primary"><?php esc_html_e('View Build', 'watchmodmarket'); ?></a>
                <a href="<?php echo esc_url(get_permalink(get_page_by_path('builder')) . '?template=' . get_the_ID()); ?>" class="btn btn-secondary"><?php esc_html_e('Build Similar', 'watchmodmarket'); ?></a>
            </div>
        <?php endif; ?>
    </div><!-- .build-content -->
</article><!-- #post-<?php the_ID(); ?> -->