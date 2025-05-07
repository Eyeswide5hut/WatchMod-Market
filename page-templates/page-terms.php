<?php
/**
 * Template Name: Terms of Service
 * 
 * The template for displaying the terms of service page
 */

get_header();
?>

<main id="primary" class="site-main">
    <!-- Terms Page Header -->
    <div class="page-header">
        <div class="container">
            <?php the_title('<h1 class="page-title">', '</h1>'); ?>
            <p class="page-subtitle"><?php echo esc_html__('Please read these terms carefully before using our services.', 'watchmodmarket'); ?></p>
        </div>
    </div>

    <div class="container">
        <div class="content-container">
            <div class="entry-content">
                <?php
                // Display the ACF content if available, otherwise show default content
                if (function_exists('get_field') && get_field('terms_content')) {
                    the_field('terms_content');
                } else {
                    // Get the content from the WordPress editor
                    the_content();
                    
                    // If there's no content, show a default terms template
                    if (!has_blocks()) {
                        ?>
                        <div class="table-of-contents">
                            <h3><?php echo esc_html__('Table of Contents', 'watchmodmarket'); ?></h3>
                            <ul>
                                <?php
                                $terms_sections = [
                                    'agreement' => __('1. Agreement to Terms', 'watchmodmarket'),
                                    'services' => __('2. Our Services', 'watchmodmarket'),
                                    'accounts' => __('3. User Accounts', 'watchmodmarket'),
                                    'purchases' => __('4. Purchases and Payments', 'watchmodmarket'),
                                    'products' => __('5. Product Information', 'watchmodmarket'),
                                    'returns' => __('6. Shipping and Returns', 'watchmodmarket'),
                                    'ip' => __('7. Intellectual Property Rights', 'watchmodmarket'),
                                    'prohibited' => __('8. Prohibited Uses', 'watchmodmarket'),
                                    'disclaimer' => __('9. Disclaimer of Warranties', 'watchmodmarket'),
                                    'limitation' => __('10. Limitation of Liability', 'watchmodmarket'),
                                    'indemnification' => __('11. Indemnification', 'watchmodmarket'),
                                    'termination' => __('12. Termination', 'watchmodmarket'),
                                    'governing-law' => __('13. Governing Law', 'watchmodmarket'),
                                    'changes' => __('14. Changes to Terms', 'watchmodmarket'),
                                    'contact' => __('15. Contact Us', 'watchmodmarket'),
                                ];
                                
                                foreach ($terms_sections as $id => $title) :
                                ?>
                                    <li><a href="#<?php echo esc_attr($id); ?>"><?php echo esc_html($title); ?></a></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>

                        <?php
                        // Include terms content
                        foreach ($terms_sections as $id => $title) :
                            include get_template_directory() . "/inc/terms-sections/{$id}.php";
                        endforeach;
                        ?>

                        <p class="last-updated"><?php echo esc_html__('Last Updated: May 1, 2023', 'watchmodmarket'); ?></p>
                        <?php
                    }
                }
                ?>
            </div>
        </div>
    </div>
</main>

<?php
get_footer();
?>