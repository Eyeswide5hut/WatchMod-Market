<?php
/**
 * Custom template tags for this theme
 *
 * @package WatchModMarket
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Prints HTML with meta information for the current post-date/time.
 */
function watchmodmarket_posted_on() {
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

    $posted_on = sprintf(
        /* translators: %s: post date. */
        esc_html_x('Posted on %s', 'post date', 'watchmodmarket'),
        '<a href="' . esc_url(get_permalink()) . '" rel="bookmark">' . $time_string . '</a>'
    );

    echo '<span class="posted-on">' . $posted_on . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

/**
 * Prints HTML with meta information for the current author.
 */
function watchmodmarket_posted_by() {
    $byline = sprintf(
        /* translators: %s: post author. */
        esc_html_x('by %s', 'post author', 'watchmodmarket'),
        '<span class="author vcard"><a class="url fn n" href="' . esc_url(get_author_posts_url(get_the_author_meta('ID'))) . '">' . esc_html(get_the_author()) . '</a></span>'
    );

    echo '<span class="byline">' . $byline . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

/**
 * Prints HTML with meta information for the categories, tags and comments.
 */
function watchmodmarket_entry_footer() {
    // Hide category and tag text for pages.
    if ('post' === get_post_type()) {
        /* translators: used between list items, there is a space after the comma */
        $categories_list = get_the_category_list(esc_html__(', ', 'watchmodmarket'));
        if ($categories_list) {
            /* translators: 1: list of categories. */
            printf('<span class="cat-links">' . esc_html__('Posted in %1$s', 'watchmodmarket') . '</span>', $categories_list); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        }

        /* translators: used between list items, there is a space after the comma */
        $tags_list = get_the_tag_list('', esc_html_x(', ', 'list item separator', 'watchmodmarket'));
        if ($tags_list) {
            /* translators: 1: list of tags. */
            printf('<span class="tags-links">' . esc_html__('Tagged %1$s', 'watchmodmarket') . '</span>', $tags_list); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        }
    }

    if (!is_single() && !post_password_required() && (comments_open() || get_comments_number())) {
        echo '<span class="comments-link">';
        comments_popup_link(
            sprintf(
                wp_kses(
                    /* translators: %s: post title */
                    __('Leave a Comment<span class="screen-reader-text"> on %s</span>', 'watchmodmarket'),
                    array(
                        'span' => array(
                            'class' => array(),
                        ),
                    )
                ),
                wp_kses_post(get_the_title())
            )
        );
        echo '</span>';
    }

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
        '<span class="edit-link">',
        '</span>'
    );
}

/**
 * Displays an optional post thumbnail.
 *
 * Wraps the post thumbnail in an anchor element on index views, or a div
 * element when on single views.
 */
function watchmodmarket_post_thumbnail() {
    if (post_password_required() || is_attachment() || !has_post_thumbnail()) {
        return;
    }

    if (is_singular()) :
        ?>

        <div class="post-thumbnail">
            <?php the_post_thumbnail(); ?>
        </div><!-- .post-thumbnail -->

    <?php else : ?>

        <a class="post-thumbnail" href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1">
            <?php
            the_post_thumbnail(
                'post-thumbnail',
                array(
                    'alt' => the_title_attribute(
                        array(
                            'echo' => false,
                        )
                    ),
                )
            );
            ?>
        </a>

        <?php
    endif; // End is_singular().
}

/**
 * Prints pagination for archive pages
 */
function watchmodmarket_pagination() {
    the_posts_pagination(array(
        'mid_size'  => 2,
        'prev_text' => __('Previous', 'watchmodmarket'),
        'next_text' => __('Next', 'watchmodmarket'),
    ));
}

/**
 * Display social sharing buttons
 */
function watchmodmarket_social_sharing() {
    // Get current page URL
    $current_url = urlencode(get_permalink());

    // Get current page title
    $current_title = urlencode(get_the_title());
    
    // Get current thumbnail
    $thumbnail_url = get_the_post_thumbnail_url(get_the_ID(), 'full');

    // Build sharing links
    $twitter_url = sprintf('https://twitter.com/intent/tweet?text=%s&url=%s', $current_title, $current_url);
    $facebook_url = sprintf('https://www.facebook.com/sharer/sharer.php?u=%s', $current_url);
    $linkedin_url = sprintf('https://www.linkedin.com/shareArticle?mini=true&url=%s&title=%s', $current_url, $current_title);
    $pinterest_url = $thumbnail_url ? sprintf('https://pinterest.com/pin/create/button/?url=%s&media=%s&description=%s', $current_url, urlencode($thumbnail_url), $current_title) : '';
    $email_url = sprintf('mailto:?subject=%s&body=%s', $current_title, $current_url);

    // Output sharing buttons
    ?>
    <div class="social-sharing">
        <span class="sharing-label"><?php esc_html_e('Share:', 'watchmodmarket'); ?></span>
        <a href="<?php echo esc_url($facebook_url); ?>" target="_blank" rel="noopener noreferrer" class="share-facebook" aria-label="<?php esc_attr_e('Share on Facebook', 'watchmodmarket'); ?>">
            <i class="fa fa-facebook"></i>
        </a>
        <a href="<?php echo esc_url($twitter_url); ?>" target="_blank" rel="noopener noreferrer" class="share-twitter" aria-label="<?php esc_attr_e('Share on Twitter', 'watchmodmarket'); ?>">
            <i class="fa fa-twitter"></i>
        </a>
        <a href="<?php echo esc_url($linkedin_url); ?>" target="_blank" rel="noopener noreferrer" class="share-linkedin" aria-label="<?php esc_attr_e('Share on LinkedIn', 'watchmodmarket'); ?>">
            <i class="fa fa-linkedin"></i>
        </a>
        <?php if ($pinterest_url) : ?>
        <a href="<?php echo esc_url($pinterest_url); ?>" target="_blank" rel="noopener noreferrer" class="share-pinterest" aria-label="<?php esc_attr_e('Share on Pinterest', 'watchmodmarket'); ?>">
            <i class="fa fa-pinterest"></i>
        </a>
        <?php endif; ?>
        <a href="<?php echo esc_url($email_url); ?>" class="share-email" aria-label="<?php esc_attr_e('Share via Email', 'watchmodmarket'); ?>">
            <i class="fa fa-envelope"></i>
        </a>
    </div>
    <?php
}

/**
 * Display related posts based on categories or tags
 */
function watchmodmarket_related_posts() {
    $post_id = get_the_ID();
    $cat_ids = array();
    $tag_ids = array();
    
    // Get all categories and tags
    $categories = get_the_category($post_id);
    $tags = get_the_tags($post_id);
    
    // Fill arrays with IDs
    if ($categories) {
        foreach ($categories as $category) {
            $cat_ids[] = $category->term_id;
        }
    }
    
    if ($tags) {
        foreach ($tags as $tag) {
            $tag_ids[] = $tag->term_id;
        }
    }
    
    // Query args
    $args = array(
        'post_type' => 'post',
        'posts_per_page' => 3,
        'post__not_in' => array($post_id),
        'orderby' => 'rand',
    );
    
    // Set tax_query based on available taxonomy terms
    if (!empty($cat_ids)) {
        $args['category__in'] = $cat_ids;
    }
    
    if (empty($cat_ids) && !empty($tag_ids)) {
        $args['tag__in'] = $tag_ids;
    }
    
    $related_query = new WP_Query($args);
    
    if ($related_query->have_posts()) :
        ?>
        <div class="related-posts">
            <h3><?php esc_html_e('Related Posts', 'watchmodmarket'); ?></h3>
            <div class="related-posts-grid">
                <?php
                while ($related_query->have_posts()) :
                    $related_query->the_post();
                    ?>
                    <div class="related-post">
                        <a href="<?php the_permalink(); ?>" rel="bookmark">
                            <?php if (has_post_thumbnail()) : ?>
                                <?php the_post_thumbnail('thumbnail'); ?>
                            <?php endif; ?>
                            <h4><?php the_title(); ?></h4>
                        </a>
                        <div class="entry-meta">
                            <?php watchmodmarket_posted_on(); ?>
                        </div>
                    </div>
                <?php
                endwhile;
                wp_reset_postdata();
                ?>
            </div>
        </div>
        <?php
    endif;
}