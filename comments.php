<?php
/**
 * The template for displaying comments
 */

/*
 * If the current post is protected by a password and
 * the visitor has not yet entered the password we will
 * return early without loading the comments.
 */
if (post_password_required()) {
    return;
}
?>

<div id="comments" class="comments-area">

    <?php
    // You can start customizing comments from here
    if (have_comments()) :
        ?>
        <h2 class="comments-title">
            <?php
            $watchmodmarket_comment_count = get_comments_number();
            if ('1' === $watchmodmarket_comment_count) {
                printf(
                    /* translators: 1: title. */
                    esc_html__('One thought on &ldquo;%1$s&rdquo;', 'watchmodmarket'),
                    '<span>' . wp_kses_post(get_the_title()) . '</span>'
                );
            } else {
                printf(
                    /* translators: 1: comment count number, 2: title. */
                    esc_html(_nx('%1$s thoughts on &ldquo;%2$s&rdquo;', '%1$s thoughts on &ldquo;%2$s&rdquo;', $watchmodmarket_comment_count, 'comments title', 'watchmodmarket')),
                    number_format_i18n($watchmodmarket_comment_count),
                    '<span>' . wp_kses_post(get_the_title()) . '</span>'
                );
            }
            ?>
        </h2><!-- .comments-title -->

        <?php the_comments_navigation(); ?>

        <ol class="comment-list">
            <?php
            wp_list_comments(
                array(
                    'style'      => 'ol',
                    'short_ping' => true,
                    'avatar_size' => 60,
                )
            );
            ?>
        </ol><!-- .comment-list -->

        <?php
        the_comments_navigation();

        // If comments are closed and there are comments, let's leave a little note, shall we?
        if (!comments_open()) :
            ?>
            <p class="no-comments"><?php esc_html_e('Comments are closed.', 'watchmodmarket'); ?></p>
            <?php
        endif;

    endif; // Check for have_comments().

    comment_form(
        array(
            'title_reply'          => esc_html__('Join the Discussion', 'watchmodmarket'),
            'title_reply_before'   => '<h3 id="reply-title" class="comment-reply-title">',
            'title_reply_after'    => '</h3>',
            'comment_notes_before' => '<p class="comment-notes">' . esc_html__('Your email address will not be published. Required fields are marked *', 'watchmodmarket') . '</p>',
            'comment_field'        => '<p class="comment-form-comment"><label for="comment">' . esc_html__('Comment', 'watchmodmarket') . '</label><textarea id="comment" name="comment" cols="45" rows="8" maxlength="65525" required="required"></textarea></p>',
            'class_submit'         => 'submit btn btn-primary',
            'submit_button'        => '<button name="%1$s" type="submit" id="%2$s" class="%3$s">%4$s</button>',
            'submit_field'         => '<p class="form-submit">%1$s %2$s</p>',
        )
    );
    ?>

</div><!-- #comments -->