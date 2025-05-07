<?php
/**
 * The template for displaying the footer
 *
 * @package WatchModMarket
 * @version 1.0.0
 */
?>
<footer class="site-footer">
    <!-- Footer Widgets -->
    <div class="footer-widgets">
        <div class="container">
            <div class="footer-grid">
                <!-- Footer Column 1 -->
                <div class="footer-col">
                    <?php if (is_active_sidebar('footer-1')) : ?>
                        <?php dynamic_sidebar('footer-1'); ?>
                    <?php else : ?>
                        <div class="footer-branding">
                            <?php 
                            // Display custom logo or site name
                            if (has_custom_logo()) {
                                the_custom_logo();
                            } else {
                                echo '<h3 class="site-title">' . esc_html(get_bloginfo('name')) . '</h3>';
                            }
                            ?>
                            <p class="site-description"><?php esc_html_e('Premium watch parts and custom building tools for watch enthusiasts, makers, and collectors worldwide.', 'watchmodmarket'); ?></p>
                        </div>
                        
                        <!-- Payment Methods -->
                        <div class="payment-methods">
                            <h4><?php esc_html_e('Secure Payments', 'watchmodmarket'); ?></h4>
                            <div class="payment-icons">
                                <?php 
                                // Payment icons
                                $payment_icons = array(
                                    'visa.svg' => 'Visa',
                                    'mastercard.svg' => 'Mastercard',
                                    'amex.svg' => 'American Express',
                                    'paypal.svg' => 'PayPal',
                                    'apple-pay.svg' => 'Apple Pay'
                                );
                                
                                foreach ($payment_icons as $icon => $label) :
                                    echo '<img src="' . esc_url(get_template_directory_uri() . '/assets/images/payment/' . $icon) . '" alt="' . esc_attr($label) . '" width="40" height="25" loading="lazy">';
                                endforeach;
                                ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Footer Column 2 -->
                <div class="footer-col">
                    <?php if (is_active_sidebar('footer-2')) : ?>
                        <?php dynamic_sidebar('footer-2'); ?>
                    <?php else : ?>
                        <h3><?php esc_html_e('Shop', 'watchmodmarket'); ?></h3>
                        <ul class="footer-links">
                            <li><a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>"><?php esc_html_e('Marketplace', 'watchmodmarket'); ?></a></li>
                            <?php
                            // Product categories
                            $shop_categories = array(
                                'cases' => __('Cases', 'watchmodmarket'),
                                'dials' => __('Dials', 'watchmodmarket'),
                                'movements' => __('Movements', 'watchmodmarket'),
                                'straps' => __('Straps', 'watchmodmarket'),
                                'tools' => __('Tools', 'watchmodmarket')
                            );
                            
                            foreach ($shop_categories as $slug => $name) :
                                echo '<li><a href="' . esc_url(home_url('/shop?category=' . $slug)) . '">' . esc_html($name) . '</a></li>';
                            endforeach;
                            ?>
                        </ul>
                    <?php endif; ?>
                </div>
                
                <!-- Footer Column 3 -->
                <div class="footer-col">
                    <?php if (is_active_sidebar('footer-3')) : ?>
                        <?php dynamic_sidebar('footer-3'); ?>
                    <?php else : ?>
                        <h3><?php esc_html_e('Services', 'watchmodmarket'); ?></h3>
                        <ul class="footer-links">
                            <?php
                            // Service pages
                            $service_pages = array(
                                '/builder' => __('Watch Builder', 'watchmodmarket'),
                                '/group-buy' => __('Group Buys', 'watchmodmarket'),
                                '/community-posts' => __('Community', 'watchmodmarket'),
                                '/workshops' => __('Workshops', 'watchmodmarket'),
                                '/gift-cards' => __('Gift Cards', 'watchmodmarket')
                            );
                            
                            foreach ($service_pages as $slug => $name) :
                                echo '<li><a href="' . esc_url(home_url($slug)) . '">' . esc_html($name) . '</a></li>';
                            endforeach;
                            
                            // Special link for custom builds archive
                            if (post_type_exists('watch_build')) :
                                echo '<li><a href="' . esc_url(get_post_type_archive_link('watch_build')) . '">' . esc_html__('Custom Builds', 'watchmodmarket') . '</a></li>';
                            endif;
                            ?>
                        </ul>
                    <?php endif; ?>
                </div>
                
                <!-- Footer Column 4 -->
                <div class="footer-col">
                    <?php if (is_active_sidebar('footer-4')) : ?>
                        <?php dynamic_sidebar('footer-4'); ?>
                    <?php else : ?>
                        <h3><?php esc_html_e('Help & Support', 'watchmodmarket'); ?></h3>
                        <ul class="footer-links">
                            <?php
                            // Support pages
                            $support_pages = array(
                                '/contact' => __('Contact Us', 'watchmodmarket'),
                                '/shipping-returns' => __('Shipping & Returns', 'watchmodmarket'),
                                '/faq' => __('FAQ', 'watchmodmarket'),
                                '/privacy-policy' => __('Privacy Policy', 'watchmodmarket'),
                                '/terms' => __('Terms of Service', 'watchmodmarket')
                            );
                            
                            foreach ($support_pages as $slug => $name) :
                                echo '<li><a href="' . esc_url(home_url($slug)) . '">' . esc_html($name) . '</a></li>';
                            endforeach;
                            ?>
                        </ul>
                    <?php endif; ?>
                    
                    <!-- Contact Info -->
                    <div class="contact-info">
                        <p>
                            <i class="fa fa-envelope"></i> 
                            <a href="mailto:<?php echo esc_attr(get_theme_mod('contact_email', 'support@watchmodmarket.com')); ?>">
                                <?php echo esc_html(get_theme_mod('contact_email', 'support@watchmodmarket.com')); ?>
                            </a>
                        </p>
                        <p>
                            <i class="fa fa-phone"></i> 
                            <a href="tel:<?php echo esc_attr(get_theme_mod('contact_phone', '+18005551234')); ?>">
                                <?php echo esc_html(get_theme_mod('contact_phone', '+1 (800) 555-1234')); ?>
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Footer -->
    <div class="bottom-footer">
        <div class="container">
            <div class="footer-bottom-wrap">
                <!-- Copyright -->
                <div class="copyright">
                    <p>&copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?>. <?php esc_html_e('All rights reserved.', 'watchmodmarket'); ?></p>
                </div>
                
                <!-- Social Links -->
                <div class="social-links">
                    <?php
                    // Social media links
                    $social_networks = array(
                        'facebook' => __('Facebook', 'watchmodmarket'),
                        'instagram' => __('Instagram', 'watchmodmarket'),
                        'twitter' => __('Twitter', 'watchmodmarket'),
                        'youtube' => __('YouTube', 'watchmodmarket'),
                        'pinterest' => __('Pinterest', 'watchmodmarket')
                    );
                    
                    foreach ($social_networks as $network => $label) :
                        $network_url = get_theme_mod('social_' . $network, 'https://' . $network . '.com/watchmodmarket');
                        if (!empty($network_url)) :
                    ?>
                        <a href="<?php echo esc_url($network_url); ?>" target="_blank" rel="noopener" aria-label="<?php echo esc_attr($label); ?>">
                            <i class="fa fa-<?php echo esc_attr($network); ?>"></i>
                        </a>
                    <?php
                        endif;
                    endforeach;
                    ?>
                </div>
                
                <!-- Footer Mini Links -->
                <div class="footer-links-mini">
                    <?php
                    // Legal pages
                    $legal_pages = array(
                        '/terms' => __('Terms', 'watchmodmarket'),
                        '/privacy-policy' => __('Privacy', 'watchmodmarket'),
                        '/cookies' => __('Cookies', 'watchmodmarket')
                    );
                    
                    foreach ($legal_pages as $slug => $name) :
                        echo '<a href="' . esc_url(home_url($slug)) . '">' . esc_html($name) . '</a>';
                    endforeach;
                    ?>
                </div>
            </div>
        </div>
    </div>
</footer>

<!-- Back to Top Button -->
<button id="back-to-top" aria-label="<?php esc_attr_e('Back to top', 'watchmodmarket'); ?>">
    <i class="fa fa-arrow-up"></i>
</button>

<?php wp_footer(); ?>

<script>
// Essential footer scripts
(function() {
    // Back to Top Button
    const backToTopButton = document.getElementById('back-to-top');
    if (backToTopButton) {
        // Initially hide the button
        backToTopButton.classList.remove('show');
        
        // Show button when scrolling down
        window.addEventListener('scroll', function() {
            if (window.pageYOffset > 300) {
                backToTopButton.classList.add('show');
            } else {
                backToTopButton.classList.remove('show');
            }
        });
        
        // Scroll to top when clicked
        backToTopButton.addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }
    
    // Initialize Instagram slider if Flickity exists
    const instaSlider = document.querySelector('.instagram-feed-slider');
    if (instaSlider && typeof Flickity === 'function') {
        new Flickity(instaSlider, {
            cellAlign: 'left',
            contain: true,
            wrapAround: true,
            autoPlay: 3000,
            prevNextButtons: false,
            pageDots: false
        });
    }
})();
</script>
</body>
</html>