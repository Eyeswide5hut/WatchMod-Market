<?php
/**
 * Template Name: Privacy Policy
 * 
 * The template for displaying the privacy policy page
 */

get_header();
?>

<main id="primary" class="site-main">
    <!-- Privacy Page Header -->
    <div class="page-header">
        <div class="container">
            <?php the_title('<h1 class="page-title">', '</h1>'); ?>
            <p class="page-subtitle"><?php echo esc_html__('Learn about the information we collect and how we use it.', 'watchmodmarket'); ?></p>
        </div>
    </div>

    <div class="container">
        <div class="content-container">
            <div class="entry-content">
                <?php
                // Display the ACF content if available, otherwise show default content
                if (function_exists('get_field') && get_field('privacy_content')) {
                    the_field('privacy_content');
                } else {
                    // Get the content from the WordPress editor
                    the_content();
                    
                    // If there's no content, show a default privacy policy template
                    if (!has_blocks()) {
                        ?>
                        <div class="table-of-contents">
                            <h3><?php echo esc_html__('Table of Contents', 'watchmodmarket'); ?></h3>
                            <ul>
                                <?php
                                $privacy_sections = [
                                    'introduction' => __('1. Introduction', 'watchmodmarket'),
                                    'information-collection' => __('2. Information We Collect', 'watchmodmarket'),
                                    'information-use' => __('3. How We Use Your Information', 'watchmodmarket'),
                                    'information-sharing' => __('4. Information Sharing and Disclosure', 'watchmodmarket'),
                                    'cookies' => __('5. Cookies and Similar Technologies', 'watchmodmarket'),
                                    'data-security' => __('6. Data Security', 'watchmodmarket'),
                                    'user-rights' => __('7. Your Rights and Choices', 'watchmodmarket'),
                                    'children' => __('8. Children\'s Privacy', 'watchmodmarket'),
                                    'international' => __('9. International Data Transfers', 'watchmodmarket'),
                                    'changes' => __('10. Changes to This Privacy Policy', 'watchmodmarket'),
                                    'contact' => __('11. Contact Us', 'watchmodmarket'),
                                ];
                                
                                foreach ($privacy_sections as $id => $title) :
                                ?>
                                    <li><a href="#<?php echo esc_attr($id); ?>"><?php echo esc_html($title); ?></a></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>

                        <?php
                        // Include privacy policy content
                        foreach ($privacy_sections as $id => $title) :
                            include get_template_directory() . "/inc/privacy-sections/{$id}.php";
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