<?php
/**
 * Template part for displaying posts
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class('blog-card'); ?>>
    <?php if (has_post_thumbnail()) : ?>
        <div class="post-thumbnail">
            <a href="<?php the_permalink(); ?>">
                <?php the_post_thumbnail('medium', array('class' => 'card-thumbnail')); ?>
            </a>
        </div>
    <?php endif; ?>

    <div class="blog-card-content">
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
                            $time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time>';
                        }

                        $time_string = sprintf(
                            $time_string,
                            esc_attr(get_the_date(DATE_W3C)),
                            esc_html(get_the_date())
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
                </div><!-- .entry-meta -->
            <?php endif; ?>
        </header><!-- .entry-header -->

        <div class="entry-content">
            <?php
            if (is_singular()) :
                the_content();
            else :
                the_excerpt();
            endif;
            ?>
        </div><!-- .entry-content -->

        <?php if (!is_singular()) : ?>
            <div class="entry-footer">
                <a href="<?php the_permalink(); ?>" class="read-more-link">
                    <?php esc_html_e('Read More', 'watchmodmarket'); ?>
                </a>
                
                <?php if (has_category()) : ?>
                    <div class="cat-links">
                        <?php
                        $categories = get_the_category();
                        $cat_count = count($categories);
                        $i = 0;
                        
                        if ($cat_count > 0) {
                            echo '<span class="screen-reader-text">' . esc_html__('Categories:', 'watchmodmarket') . '</span>';
                            foreach ($categories as $category) {
                                echo '<a href="' . esc_url(get_category_link($category->term_id)) . '">' . esc_html($category->name) . '</a>';
                                if (++$i < $cat_count) {
                                    echo ', ';
                                }
                            }
                        }
                        ?>
                    </div>
                <?php endif; ?>
            </div><!-- .entry-footer -->
        <?php endif; ?>
    </div><!-- .blog-card-content -->
</article><!-- #post-<?php the_ID(); ?> -->