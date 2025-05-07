<?php
/**
 * The template for displaying all single posts
 */

get_header();
?>

<main id="primary" class="site-main">
    <div class="container">
        <?php
        while (have_posts()) :
            the_post();
            ?>

            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <header class="entry-header">
                    <?php
                    if (is_singular()) :
                        the_title('<h1 class="entry-title">', '</h1>');
                    else :
                        the_title('<h2 class="entry-title"><a href="' . esc_url(get_permalink()) . '" rel="bookmark">', '</a></h2>');
                    endif;

                    if ('post' === get_post_type()) :
                        ?>
                        <div class="entry-meta">
                            <span class="posted-on">
                                <?php
                                $time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
                                if (get_the_time('U') !== get_the_modified_time('U')) {
                                    $time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
                                }

                                $time_string = sprintf(
                                    $time_string,
                                    esc_attr(get_the_date(DATE_W3C)),
                                    esc_html(get_the_date()),
                                    esc_attr(get_the_modified_date(DATE_W3C)),
                                    esc_html(get_the_modified_date())
                                );

                                printf(
                                    '<a href="%1$s" rel="bookmark">%2$s</a>',
                                    esc_url(get_permalink()),
                                    $time_string
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
                            <?php if (has_category()) : ?>
                                <span class="cat-links">
                                    <?php
                                    /* translators: used between list items, there is a space after the comma */
                                    $categories_list = get_the_category_list(esc_html__(', ', 'watchmodmarket'));
                                    if ($categories_list) {
                                        /* translators: 1: list of categories. */
                                        printf('<span class="screen-reader-text">' . esc_html__('Categories:', 'watchmodmarket') . '</span> %1$s', $categories_list);
                                    }
                                    ?>
                                </span>
                            <?php endif; ?>
                        </div><!-- .entry-meta -->
                    <?php endif; ?>
                </header><!-- .entry-header -->

                <?php if (has_post_thumbnail()) : ?>
                    <div class="post-thumbnail">
                        <?php the_post_thumbnail('full', array('class' => 'featured-image')); ?>
                    </div>
                <?php endif; ?>

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
                </div><!-- .entry-content -->

                <footer class="entry-footer">
                    <?php
                    // Display tags if they exist
                    if (has_tag()) :
                        /* translators: used between list items, there is a space after the comma */
                        $tags_list = get_the_tag_list('', esc_html_x(', ', 'list item separator', 'watchmodmarket'));
                        if ($tags_list) {
                            /* translators: 1: list of tags. */
                            printf('<div class="tags-links">' . esc_html__('Tagged: %1$s', 'watchmodmarket') . '</div>', $tags_list);
                        }
                    endif;

                    // Edit post link
                    edit_post_link(
                        sprintf(
                            wp_kses(
                                /* translators: %s: Name of current post. Only visible to screen readers */
                                __('Edit <span class="screen-reader-text">%s</span>', 'watchmodmarket'),
                                array(
                                    'span' => array(
                                        'class' => array(),
                                    ),
                                )
                            ),
                            wp_kses_post(get_the_title())
                        ),
                        '<div class="edit-link">',
                        '</div>'
                    );
                    ?>
                </footer><!-- .entry-footer -->
            </article><!-- #post-<?php the_ID(); ?> -->

            <?php
            // If comments are open or we have at least one comment, load up the comment template.
            if (comments_open() || get_comments_number()) :
                comments_template();
            endif;

            // Previous/next post navigation.
            the_post_navigation(
                array(
                    'prev_text' => '<span class="nav-subtitle">' . esc_html__('Previous:', 'watchmodmarket') . '</span> <span class="nav-title">%title</span>',
                    'next_text' => '<span class="nav-subtitle">' . esc_html__('Next:', 'watchmodmarket') . '</span> <span class="nav-title">%title</span>',
                )
            );

        endwhile; // End of the loop.
        ?>
    </div>
</main><!-- #main -->

<?php
get_sidebar();
get_footer();