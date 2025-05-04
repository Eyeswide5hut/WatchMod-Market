<?php
/**
 * Template Name: FAQ Page
 * 
 * The template for displaying the FAQ page
 */

get_header();
?>

<main id="primary" class="site-main">
    <!-- FAQ Page Header -->
    <div class="page-header">
        <div class="container">
            <?php the_title('<h1 class="page-title">', '</h1>'); ?>
            <?php if (function_exists('get_field') && get_field('faq_subtitle')) : ?>
                <p class="page-subtitle"><?php echo esc_html(get_field('faq_subtitle')); ?></p>
            <?php else : ?>
                <p class="page-subtitle"><?php echo esc_html__('Find answers to commonly asked questions about our products, services, and policies.', 'watchmodmarket'); ?></p>
            <?php endif; ?>
        </div>
    </div>

    <div class="container">
        <div class="content-container">
            <div class="faq-search">
                <h2><?php echo esc_html__('How can we help?', 'watchmodmarket'); ?></h2>
                <div class="search-container">
                    <div class="search-bar">
                        <input type="text" id="faq-search-input" placeholder="<?php echo esc_attr__('Search FAQs...', 'watchmodmarket'); ?>" aria-label="<?php echo esc_attr__('Search through frequently asked questions', 'watchmodmarket'); ?>">
                        <button id="faq-search-button" aria-label="<?php echo esc_attr__('Search', 'watchmodmarket'); ?>">
                            <i class="fa fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="faq-quick-links">
                <h3><?php echo esc_html__('Popular Topics', 'watchmodmarket'); ?></h3>
                <div class="quick-links-grid">
                    <a href="#ordering" class="quick-link">
                        <div class="quick-link-icon">🛒</div>
                        <span><?php echo esc_html__('Ordering', 'watchmodmarket'); ?></span>
                    </a>
                    <a href="#shipping" class="quick-link">
                        <div class="quick-link-icon">🚚</div>
                        <span><?php echo esc_html__('Shipping', 'watchmodmarket'); ?></span>
                    </a>
                    <a href="#returns" class="quick-link">
                        <div class="quick-link-icon">↩️</div>
                        <span><?php echo esc_html__('Returns', 'watchmodmarket'); ?></span>
                    </a>
                    <a href="#parts" class="quick-link">
                        <div class="quick-link-icon">⚙️</div>
                        <span><?php echo esc_html__('Watch Parts', 'watchmodmarket'); ?></span>
                    </a>
                    <a href="#builder" class="quick-link">
                        <div class="quick-link-icon">🔧</div>
                        <span><?php echo esc_html__('Watch Builder', 'watchmodmarket'); ?></span>
                    </a>
                    <a href="#account" class="quick-link">
                        <div class="quick-link-icon">👤</div>
                        <span><?php echo esc_html__('My Account', 'watchmodmarket'); ?></span>
                    </a>
                </div>
            </div>

            <div class="entry-content">
                <?php
                // First check for content from the WordPress editor
                if (has_blocks()) {
                    the_content();
                }
                // Then check for ACF repeater field (if ACF is active)
                elseif (function_exists('have_rows') && have_rows('faq_categories')) :
                    while (have_rows('faq_categories')) : the_row();
                        $category_id = sanitize_title(get_sub_field('category_name'));
                        ?>
                        <div id="<?php echo esc_attr($category_id); ?>" class="faq-category">
                            <h2 class="faq-category-title"><?php echo esc_html(get_sub_field('category_name')); ?></h2>
                            
                            <?php if (have_rows('faqs')) : ?>
                                <div class="faq-list">
                                    <?php while (have_rows('faqs')) : the_row(); ?>
                                        <div class="faq-item">
                                            <h3 class="faq-question">
                                                <button class="faq-toggle btn-secondary" aria-expanded="false">
                                                    <?php echo esc_html(get_sub_field('question')); ?>
                                                    <span class="toggle-icon" aria-hidden="true">+</span>
                                                </button>
                                            </h3>
                                            <div class="faq-answer" hidden>
                                                <?php echo wp_kses_post(get_sub_field('answer')); ?>
                                            </div>
                                        </div>
                                    <?php endwhile; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php
                    endwhile;
                else :
                    // Fallback hardcoded FAQs
                    $faq_categories = [
                        'ordering' => __('Ordering', 'watchmodmarket'),
                        'shipping' => __('Shipping', 'watchmodmarket'),
                        'returns' => __('Returns & Refunds', 'watchmodmarket'),
                        'parts' => __('Watch Parts', 'watchmodmarket'),
                        'builder' => __('Watch Builder', 'watchmodmarket'),
                        'account' => __('My Account', 'watchmodmarket'),
                    ];

                    foreach ($faq_categories as $category_id => $category_name) :
                        ?>
                        <div id="<?php echo esc_attr($category_id); ?>" class="faq-category">
                            <h2 class="faq-category-title"><?php echo esc_html($category_name); ?></h2>
                            <div class="faq-list">
                                <?php
                                // Add example FAQ items for each category
                                include get_template_directory() . '/inc/faq-examples.php';
                                get_faq_examples($category_id);
                                ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <div class="faq-contact">
                <h2><?php echo esc_html__('Still have questions?', 'watchmodmarket'); ?></h2>
                <p><?php echo esc_html__('If you couldn\'t find the answer you were looking for, please contact our customer support team.', 'watchmodmarket'); ?></p>
                <div class="faq-contact-options">
                    <a href="mailto:<?php echo esc_attr(get_theme_mod('contact_email', 'support@watchmodmarket.com')); ?>" class="contact-option">
                        <div class="contact-option-icon">✉️</div>
                        <div class="contact-option-text">
                            <h3><?php echo esc_html__('Email Us', 'watchmodmarket'); ?></h3>
                            <p><?php echo esc_html(get_theme_mod('contact_email', 'support@watchmodmarket.com')); ?></p>
                        </div>
                    </a>
                    <a href="tel:<?php echo esc_attr(get_theme_mod('contact_phone', '+18005551234')); ?>" class="contact-option">
                        <div class="contact-option-icon">📞</div>
                        <div class="contact-option-text">
                            <h3><?php echo esc_html__('Call Us', 'watchmodmarket'); ?></h3>
                            <p><?php echo esc_html(get_theme_mod('contact_phone', '+1 (800) 555-1234')); ?></p>
                        </div>
                    </a>
                    <a href="<?php echo esc_url(get_permalink(get_page_by_path('contact'))); ?>" class="contact-option">
                        <div class="contact-option-icon">📝</div>
                        <div class="contact-option-text">
                            <h3><?php echo esc_html__('Contact Form', 'watchmodmarket'); ?></h3>
                            <p><?php echo esc_html__('Fill out our contact form', 'watchmodmarket'); ?></p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</main>

<?php
// Add FAQ JavaScript
wp_enqueue_script('watchmodmarket-faq', get_template_directory_uri() . '/assets/js/faq.js', array('jquery'), WATCHMODMARKET_VERSION, true);

get_footer();
?>