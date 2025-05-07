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
                printf(esc_html__('Search Results for: %s', 'watchmodmarket'), '<span class="search-query">' . get_search_query() . '</span>');
                ?>
            </h1>
        </header><!-- .page-header -->

        <!-- Search Form -->
        <div class="search-form-container">
            <?php get_search_form(); ?>
        </div>

        <div class="search-results-container">
            <?php if (have_posts()) : ?>
                <div class="search-results-info">
                    <?php
                    global $wp_query;
                    $total_results = $wp_query->found_posts;
                    printf(
                        esc_html(_n('Found %d result', 'Found %d results', $total_results, 'watchmodmarket')),
                        number_format_i18n($total_results)
                    );
                    ?>
                </div>

                <div class="search-filter-bar">
                    <div class="filter-options">
                        <span><?php echo esc_html__('Filter By:', 'watchmodmarket'); ?></span>
                        <div class="filter-buttons">
                            <button class="filter-btn active" data-filter="all"><?php echo esc_html__('All', 'watchmodmarket'); ?></button>
                            <button class="filter-btn" data-filter="post"><?php echo esc_html__('Articles', 'watchmodmarket'); ?></button>
                            <button class="filter-btn" data-filter="product"><?php echo esc_html__('Products', 'watchmodmarket'); ?></button>
                            <button class="filter-btn" data-filter="page"><?php echo esc_html__('Pages', 'watchmodmarket'); ?></button>
                        </div>
                    </div>
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
                    'screen_reader_text' => __('Posts navigation', 'watchmodmarket'),
                    'aria_label' => __('Posts', 'watchmodmarket'),
                    'class' => 'pagination',
                ));

            else :
                ?>
                <div class="no-results">
                    <p class="no-results-text"><?php esc_html_e('Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'watchmodmarket'); ?></p>
                    
                    <div class="search-suggestions">
                        <h3><?php esc_html_e('Search Suggestions:', 'watchmodmarket'); ?></h3>
                        <ul>
                            <li><?php esc_html_e('Check your spelling.', 'watchmodmarket'); ?></li>
                            <li><?php esc_html_e('Try more general keywords.', 'watchmodmarket'); ?></li>
                            <li><?php esc_html_e('Try different keywords.', 'watchmodmarket'); ?></li>
                            <li><?php esc_html_e('Try fewer keywords.', 'watchmodmarket'); ?></li>
                        </ul>
                    </div>
                    
                    <div class="popular-searches">
                        <h3><?php esc_html_e('Popular Searches:', 'watchmodmarket'); ?></h3>
                        <div class="search-tags">
                            <a href="<?php echo esc_url(add_query_arg('s', 'watch parts', home_url())); ?>"><?php esc_html_e('Watch Parts', 'watchmodmarket'); ?></a>
                            <a href="<?php echo esc_url(add_query_arg('s', 'dials', home_url())); ?>"><?php esc_html_e('Dials', 'watchmodmarket'); ?></a>
                            <a href="<?php echo esc_url(add_query_arg('s', 'cases', home_url())); ?>"><?php esc_html_e('Cases', 'watchmodmarket'); ?></a>
                            <a href="<?php echo esc_url(add_query_arg('s', 'hands', home_url())); ?>"><?php esc_html_e('Hands', 'watchmodmarket'); ?></a>
                            <a href="<?php echo esc_url(add_query_arg('s', 'movements', home_url())); ?>"><?php esc_html_e('Movements', 'watchmodmarket'); ?></a>
                        </div>
                    </div>
                    
                    <div class="browse-categories">
                        <h3><?php esc_html_e('Browse Categories:', 'watchmodmarket'); ?></h3>
                        <?php
                        $categories = get_terms(array(
                            'taxonomy' => 'product_cat',
                            'hide_empty' => true,
                            'parent' => 0,
                            'number' => 5,
                        ));
                        
                        if (!empty($categories) && !is_wp_error($categories)) {
                            echo '<div class="category-grid">';
                            foreach ($categories as $category) {
                                $thumbnail_id = get_term_meta($category->term_id, 'thumbnail_id', true);
                                $image = wp_get_attachment_url($thumbnail_id);
                                ?>
                                <a href="<?php echo esc_url(get_term_link($category)); ?>" class="category-card">
                                    <div class="category-image">
                                        <?php if ($image) : ?>
                                            <img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr($category->name); ?>">
                                        <?php else : ?>
                                            <div class="no-image"><?php echo esc_html(substr($category->name, 0, 1)); ?></div>
                                        <?php endif; ?>
                                    </div>
                                    <h4><?php echo esc_html($category->name); ?></h4>
                                </a>
                                <?php
                            }
                            echo '</div>';
                        }
                        ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main><!-- #main -->

<?php
get_sidebar();
get_footer();