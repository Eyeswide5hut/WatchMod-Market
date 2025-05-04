<?php
/**
 * The template for displaying search results pages
 *
 * @package WatchModMarket
 */

get_header();
?>

<main id="primary" class="site-main">
    <div class="container">
        <header class="page-header">
            <h1 class="page-title">
                <?php
                /* translators: %s: search query. */
                printf(esc_html__('Search Results for: %s', 'watchmodmarket'), '<span>' . get_search_query() . '</span>');
                ?>
            </h1>
        </header><!-- .page-header -->

        <?php if (have_posts()) : ?>

            <div class="search-results-info">
                <?php
                global $wp_query;
                $total_results = $wp_query->found_posts;
                printf(
                    esc_html(_n('%d result found', '%d results found', $total_results, 'watchmodmarket')),
                    number_format_i18n($total_results)
                );
                ?>
            </div>

            <div class="search-results-grid">
                <?php
                /* Start the Loop */
                while (have_posts()) :
                    the_post();

                    /**
                     * Run the loop for the search to output the results.
                     * If you want to overload this in a child theme then include a file
                     * called content-search.php and that will be used instead.
                     */
                    get_template_part('template-parts/content', 'search');

                endwhile;
                ?>
            </div>

            <?php
            the_posts_pagination(array(
                'mid_size'  => 2,
                'prev_text' => __('Previous', 'watchmodmarket'),
                'next_text' => __('Next', 'watchmodmarket'),
            ));

        else :

            get_template_part('template-parts/content', 'none');

        endif;
        ?>
    </div>
</main><!-- #main -->

<?php
get_sidebar();
get_footer();