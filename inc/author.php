<?php
/**
 * The template for displaying author archive pages
 *
 * @package WatchModMarket
 */

get_header();

// Get author data
$curauth = (get_query_var('author_name')) ? get_user_by('slug', get_query_var('author_name')) : get_userdata(get_query_var('author'));
?>

<main id="primary" class="site-main">
    <div class="container">
        <header class="author-header">
            <div class="author-info">
                <div class="author-avatar">
                    <?php echo get_avatar($curauth->ID, 120); ?>
                </div>
                <div class="author-bio">
                    <h1 class="author-title"><?php echo esc_html($curauth->display_name); ?></h1>
                    
                    <?php if ($curauth->user_url) : ?>
                        <div class="author-website">
                            <a href="<?php echo esc_url($curauth->user_url); ?>" target="_blank">
                                <i class="fa fa-globe"></i> <?php echo esc_html__('Website', 'watchmodmarket'); ?>
                            </a>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($curauth->description) : ?>
                        <div class="author-description">
                            <?php echo wp_kses_post($curauth->description); ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="author-meta">
                        <div class="post-count">
                            <i class="fa fa-file-text"></i> 
                            <?php 
                            $post_count = count_user_posts($curauth->ID);
                            printf(
                                _n('%s Article', '%s Articles', $post_count, 'watchmodmarket'), 
                                number_format_i18n($post_count)
                            ); 
                            ?>
                        </div>
                        
                        <?php 
                        // Check if author has watch builds
                        $build_count = 0;
                        if (post_type_exists('watch_build')) {
                            $build_count = count_user_posts($curauth->ID, 'watch_build');
                            if ($build_count > 0) :
                            ?>
                                <div class="build-count">
                                    <i class="fa fa-wrench"></i> 
                                    <?php 
                                    printf(
                                        _n('%s Watch Build', '%s Watch Builds', $build_count, 'watchmodmarket'), 
                                        number_format_i18n($build_count)
                                    ); 
                                    ?>
                                </div>
                            <?php 
                            endif;
                        }
                        ?>
                    </div>
                </div>
            </div>
        </header><!-- .author-header -->

        <?php if (have_posts()) : ?>
            <div class="section-title">
                <h2>
                    <?php
                    printf(
                        /* translators: %s: author name */
                        esc_html__('Articles by %s', 'watchmodmarket'),
                        '<span class="author-name">' . esc_html($curauth->display_name) . '</span>'
                    );
                    ?>
                </h2>
            </div>

            <div class="author-content-tabs">
                <button class="tab-button active" data-tab="posts"><?php esc_html_e('Articles', 'watchmodmarket'); ?></button>
                
                <?php if ($build_count > 0) : ?>
                    <button class="tab-button" data-tab="builds"><?php esc_html_e('Watch Builds', 'watchmodmarket'); ?></button>
                <?php endif; ?>
            </div>

            <div class="tab-content active" id="tab-posts">
                <div class="author-posts-grid">
                    <?php
                    /* Start the Loop */
                    while (have_posts()) :
                        the_post();
                        get_template_part('template-parts/content', get_post_type());
                    endwhile;
                    ?>
                </div>

                <?php
                the_posts_pagination(array(
                    'mid_size'  => 2,
                    'prev_text' => __('Previous', 'watchmodmarket'),
                    'next_text' => __('Next', 'watchmodmarket'),
                ));
                ?>
            </div>

            <?php if ($build_count > 0) : ?>
                <div class="tab-content" id="tab-builds">
                    <div class="author-builds-grid">
                        <?php
                        // Query for author's watch builds
                        $args = array(
                            'post_type' => 'watch_build',
                            'author' => $curauth->ID,
                            'posts_per_page' => 6,
                        );
                        
                        $builds_query = new WP_Query($args);
                        
                        if ($builds_query->have_posts()) :
                            while ($builds_query->have_posts()) :
                                $builds_query->the_post();
                                ?>
                                <div class="build-card">
                                    <div class="build-image">
                                        <?php if (has_post_thumbnail()) : ?>
                                            <a href="<?php the_permalink(); ?>">
                                                <?php the_post_thumbnail('medium'); ?>
                                            </a>
                                        <?php else : ?>
                                            <a href="<?php the_permalink(); ?>" class="no-image">
                                                <div class="placeholder-image">
                                                    <i class="fa fa-watch"></i>
                                                </div>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                    <div class="build-content">
                                        <h3 class="build-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                                        <div class="build-meta">
                                            <span class="build-date"><?php echo get_the_date(); ?></span>
                                            
                                            <?php
                                            // Get build category if exists
                                            $terms = get_the_terms(get_the_ID(), 'watch_style');
                                            if (!empty($terms) && !is_wp_error($terms)) :
                                                $term = reset($terms);
                                            ?>
                                                <span class="build-category"><?php echo esc_html($term->name); ?></span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="build-excerpt">
                                            <?php the_excerpt(); ?>
                                        </div>
                                        <a href="<?php the_permalink(); ?>" class="btn btn-secondary"><?php esc_html_e('View Build', 'watchmodmarket'); ?></a>
                                    </div>
                                </div>
                                <?php
                            endwhile;
                            wp_reset_postdata();
                        endif;
                        ?>
                    </div>
                    
                    <?php if ($build_count > 6) : ?>
                        <div class="more-builds">
                            <a href="<?php echo esc_url(add_query_arg('post_type', 'watch_build', get_author_posts_url($curauth->ID))); ?>" class="btn btn-primary"><?php esc_html_e('View All Builds', 'watchmodmarket'); ?></a>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            
        <?php else : ?>
            <div class="no-posts">
                <p>
                    <?php esc_html_e('This author has not published any articles yet.', 'watchmodmarket'); ?>
                </p>
                
                <div class="return-home">
                    <a href="<?php echo esc_url(home_url('/')); ?>" class="btn btn-primary"><?php esc_html_e('Return to Home', 'watchmodmarket'); ?></a>
                </div>
            </div>
        <?php endif; ?>
        
    </div>
</main><!-- #main -->

<script>
jQuery(document).ready(function($) {
    // Tab functionality
    $('.tab-button').on('click', function() {
        const tabId = $(this).data('tab');
        
        // Update active tab
        $('.tab-button').removeClass('active');
        $(this).addClass('active');
        
        // Show corresponding content
        $('.tab-content').removeClass('active');
        $('#tab-' + tabId).addClass('active');
    });
});
</script>

<?php
get_sidebar();
get_footer();