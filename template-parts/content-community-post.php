<?php
/**
 * Template part for displaying community posts
 *
 * @package WatchModMarket
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class('community-post-card'); ?>>
    <?php if (has_post_thumbnail()) : ?>
        <div class="community-post-image">
            <a href="<?php the_permalink(); ?>">
                <?php the_post_thumbnail('medium', array('class' => 'community-thumbnail fade-in-image')); ?>
            </a>
        </div>
    <?php endif; ?>

    <div class="community-post-content">
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
                        esc_html_x('Posted on %s', 'post date', 'watchmodmarket'),
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
                
                <?php
                // Display post topic if available
                $topics = get_the_terms(get_the_ID(), 'community_topic');
                if ($topics && !is_wp_error($topics)) :
                ?>
                <span class="topic-links">
                    <?php
                    esc_html_e('in ', 'watchmodmarket');
                    $topic_list = array();
                    foreach ($topics as $topic) {
                        $topic_list[] = '<a href="' . esc_url(get_term_link($topic)) . '">' . esc_html($topic->name) . '</a>';
                    }
                    echo implode(', ', $topic_list);
                    ?>
                </span>
                <?php endif; ?>
            </div><!-- .entry-meta -->
        </header><!-- .entry-header -->

        <?php if (is_singular()) : ?>
            <div class="entry-content">
                <?php
                the_content(
                    sprintf(
                        wp_kses(
                            /* translators: %s: Name of current post. Only visible to screen readers */
                            __('Continue reading<span class="screen-reader-text"> "%s"</span>', 'watchmodmarket'),
                            array(
                                'span' => array(
                                    'class' => array(),
                                ),
                            )
                        ),
                        wp_kses_post(get_the_title())
                    )
                );

                wp_link_pages(
                    array(
                        'before' => '<div class="page-links">' . esc_html__('Pages:', 'watchmodmarket'),
                        'after'  => '</div>',
                    )
                );
                ?>
                
                <?php
                // Display attached watch builds if any
                $related_builds = get_post_meta(get_the_ID(), '_related_builds', true);
                if (!empty($related_builds) && is_array($related_builds)) :
                ?>
                <div class="related-builds">
                    <h3><?php esc_html_e('Featured Builds', 'watchmodmarket'); ?></h3>
                    <div class="related-builds-grid">
                        <?php
                        $build_args = array(
                            'post_type' => 'watch_build',
                            'post__in' => $related_builds,
                            'posts_per_page' => -1,
                        );
                        $build_query = new WP_Query($build_args);
                        
                        if ($build_query->have_posts()) :
                            while ($build_query->have_posts()) : $build_query->the_post();
                            ?>
                            <div class="related-build">
                                <?php if (has_post_thumbnail()) : ?>
                                <a href="<?php the_permalink(); ?>" class="related-build-image">
                                    <?php the_post_thumbnail('thumbnail'); ?>
                                </a>
                                <?php endif; ?>
                                <div class="related-build-info">
                                    <h4><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
                                    <span class="related-build-author">
                                        <?php
                                        printf(
                                            /* translators: %s: build author. */
                                            esc_html_x('by %s', 'build author', 'watchmodmarket'),
                                            '<span class="author">' . esc_html(get_the_author()) . '</span>'
                                        );
                                        ?>
                                    </span>
                                </div>
                            </div>
                            <?php
                            endwhile;
                            wp_reset_postdata();
                        endif;
                        ?>
                    </div>
                </div>
                <?php endif; ?>
            </div><!-- .entry-content -->

            <footer class="entry-footer">
                <?php watchmodmarket_entry_footer(); ?>
            </footer><!-- .entry-footer -->
        <?php else : ?>
            <div class="entry-summary">
                <?php the_excerpt(); ?>
            </div><!-- .entry-summary -->
            
            <div class="community-post-meta">
                <?php
                // Display comment count
                $comment_count = get_comments_number();
                ?>
                <span class="comment-count">
                    <i class="fa fa-comment" aria-hidden="true"></i>
                    <?php
                    printf(
                        /* translators: %s: comment count number */
                        esc_html(_nx('%s Comment', '%s Comments', $comment_count, 'comments title', 'watchmodmarket')),
                        number_format_i18n($comment_count)
                    );
                    ?>
                </span>
                
                <?php
                // Display view count if post views plugin is active
                if (function_exists('get_post_views')) {
                    $view_count = get_post_views(get_the_ID());
                    ?>
                    <span class="view-count">
                        <i class="fa fa-eye" aria-hidden="true"></i>
                        <?php
                        printf(
                            /* translators: %s: view count number */
                            esc_html(_nx('%s View', '%s Views', $view_count, 'views title', 'watchmodmarket')),
                            number_format_i18n($view_count)
                        );
                        ?>
                    </span>
                    <?php
                }
                ?>
                
                <?php
                // Display like count if likes plugin is active
                if (function_exists('get_post_likes')) {
                    $like_count = get_post_likes(get_the_ID());
                    ?>
                    <span class="like-count">
                        <i class="fa fa-heart" aria-hidden="true"></i>
                        <?php
                        printf(
                            /* translators: %s: like count number */
                            esc_html(_nx('%s Like', '%s Likes', $like_count, 'likes title', 'watchmodmarket')),
                            number_format_i18n($like_count)
                        );
                        ?>
                    </span>
                    <?php
                }
                ?>
            </div><!-- .community-post-meta -->
            
            <div class="community-post-actions">
                <a href="<?php the_permalink(); ?>" class="btn btn-primary"><?php esc_html_e('Read More', 'watchmodmarket'); ?></a>
            </div>
        <?php endif; ?>
    </div><!-- .community-post-content -->
</article><!-- #post-<?php the_ID(); ?> -->