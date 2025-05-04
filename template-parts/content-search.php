<?php
/**
 * Template part for displaying results in search pages
 *
 * @package WatchModMarket
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class('search-card'); ?>>
    <header class="entry-header">
        <?php the_title(sprintf('<h2 class="entry-title"><a href="%s" rel="bookmark">', esc_url(get_permalink())), '</a></h2>'); ?>

        <?php if ('post' === get_post_type()) : ?>
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
        </div><!-- .entry-meta -->
        <?php endif; ?>
    </header><!-- .entry-header -->

    <?php if (has_post_thumbnail()) : ?>
        <div class="search-thumbnail">
            <a href="<?php the_permalink(); ?>">
                <?php the_post_thumbnail('medium', array('class' => 'search-image')); ?>
            </a>
        </div>
    <?php endif; ?>

    <div class="entry-summary">
        <?php the_excerpt(); ?>
    </div><!-- .entry-summary -->

    <footer class="entry-footer">
        <a href="<?php the_permalink(); ?>" class="read-more"><?php esc_html_e('Read More', 'watchmodmarket'); ?> →</a>
    </footer><!-- .entry-footer -->
</article><!-- #post-<?php the_ID(); ?> -->