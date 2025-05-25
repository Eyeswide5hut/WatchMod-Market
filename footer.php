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
                <!-- Footer Column 1 - Branding & Payment -->
                <div class="footer-col footer-col--main">
                    <?php if (is_active_sidebar('footer-1')) : ?>
                        <?php dynamic_sidebar('footer-1'); ?>
                    <?php else : ?>
                        <div class="footer-branding">
                            <h3 class="footer-brand-title">WATCH MOD MARKET</h3>
                            <p class="site-description">Premium watch parts and custom building tools for watch enthusiasts, makers, and collectors worldwide.</p>
                        </div>
                        
                        <!-- Payment Methods -->
                        <div class="payment-methods">
                            <h4>Secure Payments</h4>
                            <div class="payment-icons">
                                <span class="payment-icon">VISA</span>
                                <span class="payment-icon">MC</span>
                                <span class="payment-icon">AMEX</span>
                                <span class="payment-icon">PP</span>
                                <span class="payment-icon">AP</span>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Footer Column 2 - Shop -->
                <div class="footer-col">
                    <?php if (is_active_sidebar('footer-2')) : ?>
                        <?php dynamic_sidebar('footer-2'); ?>
                    <?php else : ?>
                        <h3>Shop</h3>
                        <ul class="footer-links">
                            <?php if (class_exists('WooCommerce')) : ?>
                                <li><a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>">Marketplace</a></li>
                            <?php endif; ?>
                            <li><a href="<?php echo esc_url(home_url('/product-category/cases/')); ?>">Cases</a></li>
                            <li><a href="<?php echo esc_url(home_url('/product-category/dials/')); ?>">Dials</a></li>
                            <li><a href="<?php echo esc_url(home_url('/product-category/movements/')); ?>">Movements</a></li>
                            <li><a href="<?php echo esc_url(home_url('/product-category/straps/')); ?>">Straps</a></li>
                            <li><a href="<?php echo esc_url(home_url('/product-category/tools/')); ?>">Tools</a></li>
                        </ul>
                    <?php endif; ?>
                </div>
                
                <!-- Footer Column 3 - Services -->
                <div class="footer-col">
                    <?php if (is_active_sidebar('footer-3')) : ?>
                        <?php dynamic_sidebar('footer-3'); ?>
                    <?php else : ?>
                        <h3>Services</h3>
                        <ul class="footer-links">
                            <li><a href="<?php echo esc_url(get_permalink(get_page_by_path('builder'))); ?>">Watch Builder</a></li>
                            <li><a href="<?php echo esc_url(get_permalink(get_page_by_path('group-buy'))); ?>">Group Buys</a></li>
                            <li><a href="<?php echo esc_url(get_permalink(get_page_by_path('community'))); ?>">Community</a></li>
                            <li><a href="<?php echo esc_url(home_url('/workshops/')); ?>">Workshops</a></li>
                            <li><a href="<?php echo esc_url(home_url('/gift-cards/')); ?>">Gift Cards</a></li>
                            <?php if (post_type_exists('watch_build')) : ?>
                                <li><a href="<?php echo esc_url(get_post_type_archive_link('watch_build')); ?>">Custom Builds</a></li>
                            <?php endif; ?>
                        </ul>
                    <?php endif; ?>
                </div>
                
                <!-- Footer Column 4 - Help & Support -->
                <div class="footer-col">
                    <?php if (is_active_sidebar('footer-4')) : ?>
                        <?php dynamic_sidebar('footer-4'); ?>
                    <?php else : ?>
                        <h3>Help & Support</h3>
                        <ul class="footer-links">
                            <li><a href="<?php echo esc_url(get_permalink(get_page_by_path('contact'))); ?>">Contact Us</a></li>
                            <li><a href="<?php echo esc_url(home_url('/shipping-returns/')); ?>">Shipping & Returns</a></li>
                            <li><a href="<?php echo esc_url(get_permalink(get_page_by_path('faq'))); ?>">FAQ</a></li>
                            <li><a href="<?php echo esc_url(get_privacy_policy_url()); ?>">Privacy Policy</a></li>
                            <li><a href="<?php echo esc_url(home_url('/terms/')); ?>">Terms of Service</a></li>
                        </ul>
                        
                        <!-- Contact Info -->
                        <div class="contact-info">
                            <p>
                                <i class="fa fa-envelope" aria-hidden="true"></i>
                                <a href="mailto:<?php echo esc_attr(get_theme_mod('contact_email', 'support@watchmodmarket.com')); ?>">
                                    <?php echo esc_html(get_theme_mod('contact_email', 'support@watchmodmarket.com')); ?>
                                </a>
                            </p>
                            <p>
                                <i class="fa fa-phone" aria-hidden="true"></i>
                                <a href="tel:<?php echo esc_attr(str_replace([' ', '(', ')', '-'], '', get_theme_mod('contact_phone', '+18005551234'))); ?>">
                                    <?php echo esc_html(get_theme_mod('contact_phone', '+1 (800) 555-1234')); ?>
                                </a>
                            </p>
                        </div>
                    <?php endif; ?>
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
                    <p>&copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?>. All rights reserved.</p>
                </div>
                
                <!-- Social Links -->
                <div class="social-links">
                    <?php
                    $social_networks = array(
                        'facebook' => array('label' => 'Facebook', 'icon' => 'fa-facebook'),
                        'instagram' => array('label' => 'Instagram', 'icon' => 'fa-instagram'),
                        'twitter' => array('label' => 'Twitter', 'icon' => 'fa-twitter'),
                        'youtube' => array('label' => 'YouTube', 'icon' => 'fa-youtube'),
                        'pinterest' => array('label' => 'Pinterest', 'icon' => 'fa-pinterest')
                    );
                    
                    foreach ($social_networks as $network => $details) :
                        $network_url = get_theme_mod('social_' . $network, '');
                        if (!empty($network_url)) :
                    ?>
                        <a href="<?php echo esc_url($network_url); ?>" target="_blank" rel="noopener" aria-label="<?php echo esc_attr($details['label']); ?>">
                            <i class="fa <?php echo esc_attr($details['icon']); ?>" aria-hidden="true"></i>
                        </a>
                    <?php
                        endif;
                    endforeach;
                    ?>
                </div>
                
                <!-- Footer Mini Links -->
                <div class="footer-links-mini">
                    <a href="<?php echo esc_url(home_url('/terms/')); ?>">Terms</a>
                    <a href="<?php echo esc_url(get_privacy_policy_url()); ?>">Privacy</a>
                    <a href="<?php echo esc_url(home_url('/cookies/')); ?>">Cookies</a>
                </div>
            </div>
        </div>
    </div>
</footer>



<?php wp_footer(); ?>

<script>
// Back to Top Button functionality
document.addEventListener('DOMContentLoaded', function() {
    const backToTopButton = document.getElementById('back-to-top');
    
    if (backToTopButton) {
        // Show/hide button based on scroll position
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
});
</script>
</body>
</html>