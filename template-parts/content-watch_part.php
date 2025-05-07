<?php
/**
 * Template part for displaying watch parts
 *
 * @package WatchModMarket
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class('part-card'); ?>>
    <?php if (has_post_thumbnail()) : ?>
        <div class="part-image">
            <a href="<?php the_permalink(); ?>">
                <?php the_post_thumbnail('medium', array('class' => 'part-thumbnail fade-in-image')); ?>
            </a>
        </div>
    <?php endif; ?>

    <div class="part-content">
        <header class="entry-header">
            <?php
            if (is_singular()) :
                the_title('<h1 class="entry-title">', '</h1>');
            else :
                the_title('<h2 class="entry-title"><a href="' . esc_url(get_permalink()) . '" rel="bookmark">', '</a></h2>');
            endif;
            ?>

            <?php
            // Get part type taxonomy terms
            $part_types = get_the_terms(get_the_ID(), 'part_category');
            if ($part_types && !is_wp_error($part_types)) :
            ?>
            <div class="part-meta">
                <span class="part-type">
                    <?php
                    echo esc_html($part_types[0]->name);
                    ?>
                </span>
                <?php
                // Get brand taxonomy terms
                $brands = get_the_terms(get_the_ID(), 'brand');
                if ($brands && !is_wp_error($brands)) :
                ?>
                <span class="part-brand">
                    <?php echo esc_html($brands[0]->name); ?>
                </span>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </header><!-- .entry-header -->

        <?php if (is_singular()) : ?>
            <div class="entry-content">
                <?php the_content(); ?>
                
                <?php
                // Display specifications in single view
                $specs = get_post_meta(get_the_ID(), '_part_specifications', true);
                if (!empty($specs) && is_array($specs)) :
                ?>
                <div class="part-specifications">
                    <h3><?php esc_html_e('Specifications', 'watchmodmarket'); ?></h3>
                    <table class="specs-table">
                        <tbody>
                            <?php foreach ($specs as $label => $value) : ?>
                            <tr>
                                <th><?php echo esc_html($label); ?></th>
                                <td><?php echo esc_html($value); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
                
                <?php
                // Display compatibility information
                $compatibility = get_the_terms(get_the_ID(), 'compatibility');
                if ($compatibility && !is_wp_error($compatibility)) :
                ?>
                <div class="part-compatibility">
                    <h3><?php esc_html_e('Compatibility', 'watchmodmarket'); ?></h3>
                    <ul class="compatibility-list">
                        <?php foreach ($compatibility as $item) : ?>
                        <li>
                            <span class="compatibility-icon">✓</span>
                            <span class="compatibility-name"><?php echo esc_html($item->name); ?></span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>
                
                <?php
                // Display related parts
                $related_ids = get_post_meta(get_the_ID(), '_compatible_parts', true);
                if (!empty($related_ids)) :
                    $related_ids = explode(',', $related_ids);
                    if (!empty($related_ids)) :
                    ?>
                    <div class="related-parts">
                        <h3><?php esc_html_e('Compatible Parts', 'watchmodmarket'); ?></h3>
                        <div class="related-parts-grid">
                            <?php
                            $args = array(
                                'post_type' => 'watch_part',
                                'post__in' => $related_ids,
                                'posts_per_page' => -1,
                            );
                            $related_query = new WP_Query($args);
                            
                            if ($related_query->have_posts()) :
                                while ($related_query->have_posts()) : $related_query->the_post();
                                ?>
                                <div class="related-part">
                                    <?php if (has_post_thumbnail()) : ?>
                                    <a href="<?php the_permalink(); ?>" class="related-part-image">
                                        <?php the_post_thumbnail('thumbnail'); ?>
                                    </a>
                                    <?php endif; ?>
                                    <div class="related-part-info">
                                        <h4><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
                                        <?php
                                        // Get part type
                                        $part_types = get_the_terms(get_the_ID(), 'part_category');
                                        if ($part_types && !is_wp_error($part_types)) :
                                            echo '<span class="related-part-type">' . esc_html($part_types[0]->name) . '</span>';
                                        endif;
                                        ?>
                                    </div>
                                </div>
                                <?php
                                endwhile;
                                wp_reset_postdata();
                            endif;
                            ?>
                        </div>
                    </div>
                    <?php
                    endif;
                endif;
                ?>
                
                <?php if (class_exists('WooCommerce')) : ?>
                <div class="part-purchase">
                    <h3><?php esc_html_e('Purchase Options', 'watchmodmarket'); ?></h3>
                    <?php
                    // Get WooCommerce product by part ID
                    $args = array(
                        'post_type' => 'product',
                        'meta_query' => array(
                            array(
                                'key' => '_related_part',
                                'value' => get_the_ID(),
                                'compare' => '=',
                            ),
                        ),
                        'posts_per_page' => 1,
                    );
                    $product_query = new WP_Query($args);
                    
                    if ($product_query->have_posts()) :
                        while ($product_query->have_posts()) : $product_query->the_post();
                            global $product;
                            if ($product) :
                            ?>
                            <div class="product-purchase-info">
                                <div class="product-price">
                                    <?php echo $product->get_price_html(); ?>
                                </div>
                                <div class="product-stock">
                                    <?php
                                    if ($product->is_in_stock()) {
                                        echo '<span class="in-stock">' . esc_html__('In Stock', 'watchmodmarket') . '</span>';
                                    } else {
                                        echo '<span class="out-of-stock">' . esc_html__('Out of Stock', 'watchmodmarket') . '</span>';
                                    }
                                    ?>
                                </div>
                                <div class="add-to-cart">
                                    <?php woocommerce_template_loop_add_to_cart(); ?>
                                </div>
                            </div>
                            <?php
                            endif;
                        endwhile;
                        wp_reset_postdata();
                    else :
                        ?>
                        <p><?php esc_html_e('This part is not available for purchase at this time.', 'watchmodmarket'); ?></p>
                        <?php
                    endif;
                    ?>
                </div>
                <?php endif; ?>
            </div><!-- .entry-content -->
        <?php else : ?>
            <div class="entry-summary">
                <?php the_excerpt(); ?>
            </div><!-- .entry-summary -->
            
            <div class="part-actions">
                <a href="<?php the_permalink(); ?>" class="btn btn-primary"><?php esc_html_e('View Details', 'watchmodmarket'); ?></a>
                
                <?php if (class_exists('WooCommerce')) : ?>
                    <?php
                    // Get WooCommerce product by part ID
                    $args = array(
                        'post_type' => 'product',
                        'meta_query' => array(
                            array(
                                'key' => '_related_part',
                                'value' => get_the_ID(),
                                'compare' => '=',
                            ),
                        ),
                        'posts_per_page' => 1,
                    );
                    $product_query = new WP_Query($args);
                    
                    if ($product_query->have_posts()) :
                        while ($product_query->have_posts()) : $product_query->the_post();
                            global $product;
                            if ($product && $product->is_purchasable() && $product->is_in_stock()) :
                                echo '<a href="' . esc_url($product->add_to_cart_url()) . '" class="btn btn-secondary add-to-cart">' . esc_html__('Add to Cart', 'watchmodmarket') . '</a>';
                            endif;
                        endwhile;
                        wp_reset_postdata();
                    endif;
                    ?>
                <?php endif; ?>
                
                <?php
                // Add "Use in Builder" button
                echo '<a href="' . esc_url(get_permalink(get_page_by_path('builder'))) . '?add-part=' . get_the_ID() . '" class="btn btn-secondary">' . esc_html__('Use in Builder', 'watchmodmarket') . '</a>';
                ?>
            </div>
        <?php endif; ?>
    </div><!-- .part-content -->
</article><!-- #post-<?php the_ID(); ?> -->