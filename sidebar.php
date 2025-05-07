<?php
/**
 * The sidebar containing the main widget area
 */

if (!is_active_sidebar('sidebar-1')) {
    return;
}
?>

<aside id="secondary" class="widget-area sidebar">
    <?php dynamic_sidebar('sidebar-1'); ?>
    
    <?php if (!is_active_sidebar('sidebar-1')) : ?>
        <!-- Default sidebar content when no widgets are added -->
        <div class="widget default-widget">
            <h3 class="widget-title"><?php esc_html_e('Quick Links', 'watchmodmarket'); ?></h3>
            <ul>
                <li><a href="<?php echo esc_url(get_permalink(get_page_by_path('shop'))); ?>"><?php esc_html_e('Shop All Parts', 'watchmodmarket'); ?></a></li>
                <li><a href="<?php echo esc_url(get_permalink(get_page_by_path('builder'))); ?>"><?php esc_html_e('Watch Builder', 'watchmodmarket'); ?></a></li>
                <li><a href="<?php echo esc_url(get_permalink(get_page_by_path('community'))); ?>"><?php esc_html_e('Community', 'watchmodmarket'); ?></a></li>
                <li><a href="<?php echo esc_url(get_permalink(get_page_by_path('contact'))); ?>"><?php esc_html_e('Contact Us', 'watchmodmarket'); ?></a></li>
            </ul>
        </div>
        
        <div class="widget featured-builds-widget">
            <h3 class="widget-title"><?php esc_html_e('Featured Builds', 'watchmodmarket'); ?></h3>
            <?php
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
                    <div class="mini-build">
                        <a href="<?php the_permalink(); ?>">
                            <?php if (has_post_thumbnail()) : ?>
                                <?php the_post_thumbnail('thumbnail'); ?>
                            <?php endif; ?>
                            <div class="mini-build-info">
                                <h4><?php the_title(); ?></h4>
                                <span class="build-author"><?php esc_html_e('By:', 'watchmodmarket'); ?> <?php the_author(); ?></span>
                            </div>
                        </a>
                    </div>
                    <?php
                }
                wp_reset_postdata();
            } else {
                echo '<p>' . esc_html__('No featured builds found.', 'watchmodmarket') . '</p>';
            }
            ?>
        </div>
    <?php endif; ?>
</aside><!-- #secondary -->