<?php
/**
 * Template Name: Watch Builder
 * 
 * The template for displaying the watch builder page
 */

get_header();
?>

<div class="builder-interface">
    <div class="container">
        <h1><?php the_title(); ?></h1>
        <p class="builder-intro"><?php echo esc_html__('Create your perfect timepiece by selecting and combining different watch components. Our interactive tool lets you visualize your design in real-time.', 'watchmodmarket'); ?></p>

        <div class="builder-container">
            <div class="parts-panel">
                <div class="parts-panel-header">
                    <h3><?php echo esc_html__('Watch Parts', 'watchmodmarket'); ?></h3>
                </div>
                <div class="parts-tabs" role="tablist">
                    <button class="parts-tab active" id="tab-cases" role="tab" aria-selected="true"
                        aria-controls="section-cases"><?php echo esc_html__('Cases', 'watchmodmarket'); ?></button>
                    <button class="parts-tab" id="tab-dials" role="tab" aria-selected="false"
                        aria-controls="section-dials"><?php echo esc_html__('Dials', 'watchmodmarket'); ?></button>
                    <button class="parts-tab" id="tab-hands" role="tab" aria-selected="false"
                        aria-controls="section-hands"><?php echo esc_html__('Hands', 'watchmodmarket'); ?></button>
                    <button class="parts-tab" id="tab-straps" role="tab" aria-selected="false"
                        aria-controls="section-straps"><?php echo esc_html__('Straps', 'watchmodmarket'); ?></button>
                    <button class="parts-tab" id="tab-movements" role="tab" aria-selected="false"
                        aria-controls="section-movements"><?php echo esc_html__('Movements', 'watchmodmarket'); ?></button>
                </div>
                <div class="parts-content">
                    <?php
                    // Get watch parts from custom post type or WooCommerce products
                    
                    // Cases section
                    ?>
                    <div class="part-section active" id="section-cases" role="tabpanel" aria-labelledby="tab-cases">
                        <?php
                        // Get case products
                        $args = array(
                            'post_type' => 'product',
                            'posts_per_page' => -1,
                            'tax_query' => array(
                                array(
                                    'taxonomy' => 'product_cat',
                                    'field' => 'slug',
                                    'terms' => 'cases',
                                ),
                            ),
                        );
                        
                        $cases_query = new WP_Query($args);
                        
                        if ($cases_query->have_posts()) {
                            $first = true;
                            while ($cases_query->have_posts()) {
                                $cases_query->the_post();
                                global $product;
                                ?>
                                <div class="part-item <?php echo $first ? 'selected' : ''; ?>" data-part-id="case-<?php echo esc_attr($product->get_id()); ?>">
                                    <?php
                                    if (has_post_thumbnail()) {
                                        the_post_thumbnail('thumbnail', array('class' => 'part-thumbnail', 'loading' => 'lazy', 'alt' => get_the_title()));
                                    }
                                    ?>
                                    <div class="part-details">
                                        <div class="part-name"><?php the_title(); ?></div>
                                        <div class="part-specs"><?php echo esc_html($product->get_short_description()); ?></div>
                                    </div>
                                    <div class="part-price"><?php echo $product->get_price_html(); ?></div>
                                </div>
                                <?php
                                $first = false;
                            }
                            wp_reset_postdata();
                        } else {
                            echo '<p>' . esc_html__('No case products found.', 'watchmodmarket') . '</p>';
                        }
                        ?>
                    </div>

                    <!-- Dials Section -->
                    <div class="part-section" id="section-dials" role="tabpanel" aria-labelledby="tab-dials">
                        <?php
                        // Get dial products
                        $args = array(
                            'post_type' => 'product',
                            'posts_per_page' => -1,
                            'tax_query' => array(
                                array(
                                    'taxonomy' => 'product_cat',
                                    'field' => 'slug',
                                    'terms' => 'dials',
                                ),
                            ),
                        );
                        
                        $dials_query = new WP_Query($args);
                        
                        if ($dials_query->have_posts()) {
                            $first = true;
                            while ($dials_query->have_posts()) {
                                $dials_query->the_post();
                                global $product;
                                ?>
                                <div class="part-item <?php echo $first ? 'selected' : ''; ?>" data-part-id="dial-<?php echo esc_attr($product->get_id()); ?>">
                                    <?php
                                    if (has_post_thumbnail()) {
                                        the_post_thumbnail('thumbnail', array('class' => 'part-thumbnail', 'loading' => 'lazy', 'alt' => get_the_title()));
                                    }
                                    ?>
                                    <div class="part-details">
                                        <div class="part-name"><?php the_title(); ?></div>
                                        <div class="part-specs"><?php echo esc_html($product->get_short_description()); ?></div>
                                    </div>
                                    <div class="part-price"><?php echo $product->get_price_html(); ?></div>
                                </div>
                                <?php
                                $first = false;
                            }
                            wp_reset_postdata();
                        } else {
                            echo '<p>' . esc_html__('No dial products found.', 'watchmodmarket') . '</p>';
                        }
                        ?>
                    </div>

                    <!-- Hands Section -->
                    <div class="part-section" id="section-hands" role="tabpanel" aria-labelledby="tab-hands">
                        <?php
                        // Implement for hands section (similar to cases and dials)
                        // ...
                        ?>
                    </div>

                    <!-- Straps Section -->
                    <div class="part-section" id="section-straps" role="tabpanel" aria-labelledby="tab-straps">
                        <?php
                        // Implement for straps section (similar to cases and dials)
                        // ...
                        ?>
                    </div>

                    <!-- Movements Section -->
                    <div class="part-section" id="section-movements" role="tabpanel" aria-labelledby="tab-movements">
                        <?php
                        // Implement for movements section (similar to cases and dials)
                        // ...
                        ?>
                    </div>
                </div>
            </div>

            <div class="watch-preview">
                <div class="preview-controls">
                    <button class="view-control active" data-view="front" aria-label="<?php echo esc_attr__('View watch from front', 'watchmodmarket'); ?>"><?php echo esc_html__('Front View', 'watchmodmarket'); ?></button>
                    <button class="view-control" data-view="side" aria-label="<?php echo esc_attr__('View watch from side', 'watchmodmarket'); ?>"><?php echo esc_html__('Side View', 'watchmodmarket'); ?></button>
                    <button class="view-control" data-view="back" aria-label="<?php echo esc_attr__('View watch from back', 'watchmodmarket'); ?>"><?php echo esc_html__('Back View', 'watchmodmarket'); ?></button>
                    <button class="view-control" data-view="3d" aria-label="<?php echo esc_attr__('View watch in 3D', 'watchmodmarket'); ?>"><?php echo esc_html__('3D View', 'watchmodmarket'); ?></button>
                </div>

                <div class="preview-container">
                    <div class="watch-canvas" id="watch-preview-canvas">
                        <canvas id="watch-3d-render" width="400" height="400" aria-label="<?php echo esc_attr__('3D watch preview', 'watchmodmarket'); ?>"></canvas>
                        <div id="loading-indicator" class="loading-indicator"><?php echo esc_html__('Loading Preview...', 'watchmodmarket'); ?></div>
                        <noscript>
                            <img src="<?php echo esc_url(get_template_directory_uri()); ?>/assets/images/watch-placeholder.jpg" alt="<?php echo esc_attr__('Watch preview placeholder', 'watchmodmarket'); ?>" width="400" height="400">
                            <p><?php echo esc_html__('JavaScript is required for the interactive watch preview. Enable JavaScript or view our', 'watchmodmarket'); ?> <a href="<?php echo esc_url(get_permalink(get_page_by_path('catalog'))); ?>"><?php echo esc_html__('parts catalog', 'watchmodmarket'); ?></a>.</p>
                        </noscript>
                    </div>
                </div>

                <div class="compatibility-alert" id="compatibility-alert">
                    <h4><?php echo esc_html__('Compatibility Warning', 'watchmodmarket'); ?></h4>
                    <p><?php echo esc_html__('Some selected parts may not be fully compatible. Please check the specifications or contact customer support for assistance.', '