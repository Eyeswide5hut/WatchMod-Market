<?php
/**
 * Custom template tags for WatchModMarket theme
 *
 * @package WatchModMarket
 */

if (!function_exists('watchmodmarket_posted_on')) :
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

        printf(
            '<span class="posted-on">%1$s <a href="%2$s" rel="bookmark">%3$s</a></span>',
            esc_html__('Posted on', 'watchmodmarket'),
            esc_url(get_permalink()),
            $time_string
        );
    }
endif;

if (!function_exists('watchmodmarket_posted_by')) :
    /**
     * Prints HTML with meta information for the current author.
     */
    function watchmodmarket_posted_by() {
        printf(
            '<span class="byline">%1$s <span class="author vcard"><a class="url fn n" href="%2$s">%3$s</a></span></span>',
            esc_html__('by', 'watchmodmarket'),
            esc_url(get_author_posts_url(get_the_author_meta('ID'))),
            esc_html(get_the_author())
        );
    }
endif;

if (!function_exists('watchmodmarket_entry_footer')) :
    /**
     * Prints HTML with meta information for the categories, tags and comments.
     */
    function watchmodmarket_entry_footer() {
        // Hide category and tag text for pages.
        if ('post' === get_post_type()) {
            /* translators: used between list items, there is a space after the comma */
            $categories_list = get_the_category_list(esc_html__(', ', 'watchmodmarket'));
            if ($categories_list) {
                printf('<span class="cat-links">%1$s %2$s</span>', esc_html__('Posted in', 'watchmodmarket'), $categories_list);
            }

            /* translators: used between list items, there is a space after the comma */
            $tags_list = get_the_tag_list('', esc_html__(', ', 'watchmodmarket'));
            if ($tags_list) {
                printf('<span class="tags-links">%1$s %2$s</span>', esc_html__('Tagged', 'watchmodmarket'), $tags_list);
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
                    get_the_title()
                )
            );
            echo '</span>';
        }

        edit_post_link(
            sprintf(
                wp_kses(
                    /* translators: %s: Name of current post */
                    __('Edit <span class="screen-reader-text">%s</span>', 'watchmodmarket'),
                    array(
                        'span' => array(
                            'class' => array(),
                        ),
                    )
                ),
                get_the_title()
            ),
            '<span class="edit-link">',
            '</span>'
        );
    }
endif;