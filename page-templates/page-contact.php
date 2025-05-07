<?php
/**
 * Template Name: Contact Page
 * 
 * The template for displaying the contact page
 */

get_header();
?>

<main id="primary" class="site-main">
    <!-- Contact Page Header -->
    <div class="page-header">
        <div class="container">
            <?php the_title('<h1 class="page-title">', '</h1>'); ?>
            <?php if (get_field('contact_subtitle')) : ?>
                <p class="page-subtitle"><?php echo esc_html(get_field('contact_subtitle')); ?></p>
            <?php else : ?>
                <p class="page-subtitle"><?php echo esc_html__('Get in touch with us about watch parts, orders, or custom projects.', 'watchmodmarket'); ?></p>
            <?php endif; ?>
        </div>
    </div>

    <div class="container">
        <div class="content-container">
            <div class="contact-grid">
                <div class="contact-info">
                    <div class="contact-blocks">
                        <!-- Contact Blocks -->
                        <div class="contact-block">
                            <div class="contact-icon" aria-hidden="true">📍</div>
                            <h3><?php echo esc_html__('Visit Us', 'watchmodmarket'); ?></h3>
                            <p><?php echo esc_html(get_theme_mod('contact_address', '123 Watch Street, Suite 456, New York, NY 10001, USA')); ?></p>
                        </div>

                        <div class="contact-block">
                            <div class="contact-icon" aria-hidden="true">📞</div>
                            <h3><?php echo esc_html__('Call Us', 'watchmodmarket'); ?></h3>
                            <p><?php echo esc_html(get_theme_mod('contact_phone', '+1 (800) 555-1234')); ?></p>
                            <p class="sub-info"><?php echo esc_html(get_theme_mod('contact_hours', 'Monday-Friday: 9am-6pm EST')); ?></p>
                        </div>

                        <div class="contact-block">
                            <div class="contact-icon" aria-hidden="true">✉️</div>
                            <h3><?php echo esc_html__('Email Us', 'watchmodmarket'); ?></h3>
                            <p>
                                <a href="mailto:<?php echo esc_attr(get_theme_mod('contact_email', 'support@watchmodmarket.com')); ?>">
                                    <?php echo esc_html(get_theme_mod('contact_email', 'support@watchmodmarket.com')); ?>
                                </a>
                            </p>
                            <p class="sub-info"><?php echo esc_html__('We typically respond within 24 hours', 'watchmodmarket'); ?></p>
                        </div>

                        <div class="contact-block">
                            <div class="contact-icon" aria-hidden="true">👥</div>
                            <h3><?php echo esc_html__('Follow Us', 'watchmodmarket'); ?></h3>
                            <div class="contact-social">
                                <?php
                                $social_links = [
                                    'facebook' => get_theme_mod('social_facebook', 'https://facebook.com/watchmodmarket'),
                                    'instagram' => get_theme_mod('social_instagram', 'https://instagram.com/watchmodmarket'),
                                    'twitter' => get_theme_mod('social_twitter', 'https://twitter.com/watchmodmarket'),
                                    'youtube' => get_theme_mod('social_youtube', 'https://youtube.com/watchmodmarket'),
                                ];
                                
                                foreach ($social_links as $platform => $url) :
                                    if ($url) :
                                ?>
                                    <a href="<?php echo esc_url($url); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr__('Follow us on ' . ucfirst($platform), 'watchmodmarket'); ?>">
                                        <i class="fa fa-<?php echo esc_attr($platform); ?>"></i>
                                    </a>
                                <?php
                                    endif;
                                endforeach;
                                ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="contact-form-container">
                    <h2><?php echo esc_html__('Send Us a Message', 'watchmodmarket'); ?></h2>
                    
                    <?php
                    // Check if Contact Form 7 is active
                    if (function_exists('wpcf7_contact_form')) {
                        // Get the contact form ID from theme customizer or ACF
                        $contact_form_id = get_theme_mod('contact_form_id', '');
                        
                        // If no ID is set in customizer, try to get it from ACF
                        if (empty($contact_form_id) && function_exists('get_field')) {
                            $contact_form_id = get_field('contact_form_id');
                        }
                        
                        if (!empty($contact_form_id)) {
                            echo do_shortcode('[contact-form-7 id="' . esc_attr($contact_form_id) . '"]');
                        } else {
                            // Fallback if no contact form ID is specified
                            echo do_shortcode('[contact-form-7 id="contact-form" title="Contact Form"]');
                        }
                    } else {
                        // Fallback HTML form if Contact Form 7 is not active
                        ?>
                        <form id="contact-form" class="contact-form" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">
                            <input type="hidden" name="action" value="watchmodmarket_process_contact_form">
                            <?php wp_nonce_field('watchmodmarket_contact_nonce', 'contact_nonce'); ?>
                            
                            <div class="form-group">
                                <label for="contact-name"><?php echo esc_html__('Your Name *', 'watchmodmarket'); ?></label>
                                <input type="text" id="contact-name" name="contact-name" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="contact-email"><?php echo esc_html__('Your Email *', 'watchmodmarket'); ?></label>
                                <input type="email" id="contact-email" name="contact-email" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="contact-subject"><?php echo esc_html__('Subject *', 'watchmodmarket'); ?></label>
                                <input type="text" id="contact-subject" name="contact-subject" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="contact-message"><?php echo esc_html__('Your Message *', 'watchmodmarket'); ?></label>
                                <textarea id="contact-message" name="contact-message" rows="6" required></textarea>
                            </div>
                            
                            <div class="form-submit">
                                <button type="submit" class="btn btn-primary btn-submit"><?php echo esc_html__('Send Message', 'watchmodmarket'); ?></button>
                            </div>
                        </form>
                    <?php } ?>
                </div>
            </div>

            <?php if (get_field('show_map') || get_theme_mod('show_contact_map', true)) : ?>
                <div class="contact-map">
                    <h2><?php echo esc_html__('Find Us', 'watchmodmarket'); ?></h2>
                    <?php
                    // Check if ACF is active and if there's a Google Maps field
                    if (function_exists('get_field') && get_field('map')) {
                        the_field('map');
                    } else {
                        // Otherwise, use an iframe with the address from the theme customizer
                        $map_address = urlencode(get_theme_mod('contact_address', '123 Watch Street, New York, NY 10001'));
                        ?>
                        <div class="map-container">
                            <iframe
                                width="100%"
                                height="450"
                                style="border:3px solid var(--color-dark)"
                                loading="lazy"
                                allowfullscreen
                                src="https://www.google.com/maps/embed/v1/place?key=YOUR_API_KEY_HERE&q=<?php echo $map_address; ?>"
                            ></iframe>
                        </div>
                    <?php } ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php
get_footer();
?>