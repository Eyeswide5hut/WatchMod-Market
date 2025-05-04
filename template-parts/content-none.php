<?php
/**
 * Template part for displaying a message that posts cannot be found
 */
?>

<section class="no-results not-found">
    <header class="page-header">
        <h1 class="page-title"><?php esc_html_e('Nothing Found', 'watchmodmarket'); ?></h1>
    </header><!-- .page-header -->

    <div class="page-content">
        <?php
        if (is_home() && current_user_can('publish_posts')) :
            printf(
                '<p>' . wp_kses(
                    /* translators: 1: link to WP admin new post page. */
                    __('Ready to share your first post? <a href="%1$s">Get started here</a>.', 'watchmodmarket'),
                    array(
                        'a' => array(
                            'href' => array(),
                        ),
                    )
                ) . '</p>',
                esc_url(admin_url('post-new.php'))
            );

        elseif (is_search()) :
            ?>
            <p><?php esc_html_e('Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'watchmodmarket'); ?></p>
            <?php
            get_search_form();

        else :
            ?>
            <p><?php esc_html_e('It seems we can\'t find what you\'re looking for. Perhaps searching can help.', 'watchmodmarket'); ?></p>
            <?php
            get_search_form();

        endif;
        ?>
        
        <div class="no-results-suggestions">
            <h3><?php esc_html_e('You might be interested in:', 'watchmodmarket'); ?></h3>
            <div class="suggestions-grid">
                <div class="suggestion-item">
                    <h4><?php esc_html_e('Browse Our Shop', 'watchmodmarket'); ?></h4>
                    <p><?php esc_html_e('Discover premium watch parts and accessories.', 'watchmodmarket'); ?></p>
                    <a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>" class="btn btn-secondary"><?php esc_html_e('Shop Now', 'watchmodmarket'); ?></a>
                </div>
                
                <div class="suggestion-item">
                    <h4><?php esc_html_e('Custom Watch Builder', 'watchmodmarket'); ?></h4>
                    <p><?php esc_html_e('Create your own unique timepiece with our watch builder tool.', 'watchmodmarket'); ?></p>
                    <a href="<?php echo esc_url(get_permalink(get_page_by_path('builder'))); ?>" class="btn btn-secondary"><?php esc_html_e('Start Building', 'watchmodmarket'); ?></a>
                </div>
                
                <div class="suggestion-item">
                    <h4><?php esc_html_e('Community Posts', 'watchmodmarket'); ?></h4>
                    <p><?php esc_html_e('See what other watch enthusiasts are creating and discussing.', 'watchmodmarket'); ?></p>
                    <a href="<?php echo esc_url(get_post_type_archive_link('community_post')); ?>" class="btn btn-secondary"><?php esc_html_e('Visit Community', 'watchmodmarket'); ?></a>
                </div>
            </div>
        </div>
    </div><!-- .page-content -->
</section><!-- .no-results -->