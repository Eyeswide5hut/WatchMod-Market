<?php
/**
 * The template for displaying the footer
 */
?>
<footer class="site-footer">
    <div class="container">
        <div class="footer-grid">
            <div class="footer-col">
                <?php if (is_active_sidebar('footer-1')) : ?>
                    <?php dynamic_sidebar('footer-1'); ?>
                <?php else : ?>
                    <h3><?php esc_html_e('About WatchModMarket', 'watchmodmarket'); ?></h3>
                    <p><?php esc_html_e('Premium watch parts and custom building tools for watch enthusiasts, makers, and collectors worldwide. Create your perfect timepiece with our parts and community.', 'watchmodmarket'); ?></p>
                    <div class="payment-methods">
                        <img src="<?php echo esc_url(get_template_directory_uri()); ?>/assets/images/payment/visa.svg" alt="Visa" width="40" height="25">
                        <img src="<?php echo esc_url(get_template_directory_uri()); ?>/assets/images/payment/mastercard.svg" alt="Mastercard" width="40" height="25">
                        <img src="<?php echo esc_url(get_template_directory_uri()); ?>/assets/images/payment/amex.svg" alt="American Express" width="40" height="25">
                        <img src="<?php echo esc_url(get_template_directory_uri()); ?>/assets/images/payment/paypal.svg" alt="PayPal" width="40" height="25">
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="footer-col">
                <?php if (is_active_sidebar('footer-2')) : ?>
                    <?php dynamic_sidebar('footer-2'); ?>
                <?php else : ?>
                    <h3><?php esc_html_e('Shop', 'watchmodmarket'); ?></h3>
                    <ul>
                        <li><a href="<?php echo esc_url(home_url('/shop')); ?>"><?php esc_html_e('All Products', 'watchmodmarket'); ?></a></li>
                        <li><a href="<?php echo esc_url(home_url('/shop?category=cases')); ?>"><?php esc_html_e('Cases', 'watchmodmarket'); ?></a></li>
                        <li><a href="<?php echo esc_url(home_url('/shop?category=dials')); ?>"><?php esc_html_e('Dials', 'watchmodmarket'); ?></a></li>
                        <li><a href="<?php echo esc_url(home_url('/shop?category=movements')); ?>"><?php esc_html_e('Movements', 'watchmodmarket'); ?></a></li>
                        <li><a href="<?php echo esc_url(home_url('/shop?category=straps')); ?>"><?php esc_html_e('Straps', 'watchmodmarket'); ?></a></li>
                        <li><a href="<?php echo esc_url(home_url('/shop?category=tools')); ?>"><?php esc_html_e('Tools', 'watchmodmarket'); ?></a></li>
                        <li><a href="<?php echo esc_url(home_url('/shop?category=new')); ?>"><?php esc_html_e('New Arrivals', 'watchmodmarket'); ?></a></li>
                        <li><a href="<?php echo esc_url(home_url('/shop?category=sale')); ?>"><?php esc_html_e('On Sale', 'watchmodmarket'); ?></a></li>
                    </ul>
                <?php endif; ?>
            </div>
            
            <div class="footer-col">
                <?php if (is_active_sidebar('footer-3')) : ?>
                    <?php dynamic_sidebar('footer-3'); ?>
                <?php else : ?>
                    <h3><?php esc_html_e('Customer Service', 'watchmodmarket'); ?></h3>
                    <ul>
                        <li><a href="<?php echo esc_url(home_url('/contact')); ?>"><?php esc_html_e('Contact Us', 'watchmodmarket'); ?></a></li>
                        <li><a href="<?php echo esc_url(home_url('/shipping')); ?>"><?php esc_html_e('Shipping & Returns', 'watchmodmarket'); ?></a></li>
                        <li><a href="<?php echo esc_url(home_url('/faq')); ?>"><?php esc_html_e('FAQ', 'watchmodmarket'); ?></a></li>
                        <li><a href="<?php echo esc_url(home_url('/terms')); ?>"><?php esc_html_e('Terms of Service', 'watchmodmarket'); ?></a></li>
                        <li><a href="<?php echo esc_url(home_url('/privacy-policy')); ?>"><?php esc_html_e('Privacy Policy', 'watchmodmarket'); ?></a></li>
                    </ul>
                <?php endif; ?>
            </div>
            
            <div class="footer-col">
                <?php if (is_active_sidebar('footer-4')) : ?>
                    <?php dynamic_sidebar('footer-4'); ?>
                <?php else : ?>
                    <h3><?php esc_html_e('Watch Builder & Community', 'watchmodmarket'); ?></h3>
                    <ul>
                        <li><a href="<?php echo esc_url(home_url('/builder')); ?>"><?php esc_html_e('Watch Builder', 'watchmodmarket'); ?></a></li>
                        <li><a href="<?php echo esc_url(home_url('/group-buy')); ?>"><?php esc_html_e('Timepiece Futures', 'watchmodmarket'); ?></a></li>
                        <li><a href="<?php echo esc_url(home_url('/community-posts')); ?>"><?php esc_html_e('Community', 'watchmodmarket'); ?></a></li>
                        <li><a href="<?php echo esc_url(get_post_type_archive_link('watch_build')); ?>"><?php esc_html_e('Custom Builds', 'watchmodmarket'); ?></a></li>
                    </ul>
                    
                    <h3 class="mt-4"><?php esc_html_e('Follow Us', 'watchmodmarket'); ?></h3>
                    <div class="social-links">
                        <a href="<?php echo esc_url(get_theme_mod('social_facebook', 'https://facebook.com/watchmodmarket')); ?>" target="_blank" rel="noopener" aria-label="<?php esc_attr_e('Facebook', 'watchmodmarket'); ?>"><i class="fa fa-facebook"></i></a>
                        <a href="<?php echo esc_url(get_theme_mod('social_instagram', 'https://instagram.com/watchmodmarket')); ?>" target="_blank" rel="noopener" aria-label="<?php esc_attr_e('Instagram', 'watchmodmarket'); ?>"><i class="fa fa-instagram"></i></a>
                        <a href="<?php echo esc_url(get_theme_mod('social_twitter', 'https://twitter.com/watchmodmarket')); ?>" target="_blank" rel="noopener" aria-label="<?php esc_attr_e('Twitter', 'watchmodmarket'); ?>"><i class="fa fa-twitter"></i></a>
                        <a href="<?php echo esc_url(get_theme_mod('social_youtube', 'https://youtube.com/watchmodmarket')); ?>" target="_blank" rel="noopener" aria-label="<?php esc_attr_e('YouTube', 'watchmodmarket'); ?>"><i class="fa fa-youtube"></i></a>
                    </div>
                <?php endif; ?>
                
                <div class="contact-info">
                    <p><?php esc_html_e('Email:', 'watchmodmarket'); ?> <a href="mailto:<?php echo esc_attr(get_theme_mod('contact_email', 'support@watchmodmarket.com')); ?>"><?php echo esc_html(get_theme_mod('contact_email', 'support@watchmodmarket.com')); ?></a></p>
                    <p><?php esc_html_e('Phone:', 'watchmodmarket'); ?> <a href="tel:<?php echo esc_attr(get_theme_mod('contact_phone', '+18005551234')); ?>"><?php echo esc_html(get_theme_mod('contact_phone', '+1 (800) 555-1234')); ?></a></p>
                </div>
                
                <div class="newsletter-mini">
                    <h4><?php esc_html_e('Join our newsletter', 'watchmodmarket'); ?></h4>
                    <?php 
                    if (function_exists('mc4wp_show_form')) {
                        mc4wp_show_form();
                    } else {
                    ?>
                    <form class="newsletter-form-mini" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">
                        <input type="hidden" name="action" value="watchmodmarket_newsletter_signup">
                        <?php wp_nonce_field('newsletter_signup', 'newsletter_nonce'); ?>
                        <input type="email" name="email" placeholder="<?php esc_attr_e('Your email', 'watchmodmarket'); ?>" required aria-label="<?php esc_attr_e('Email for newsletter', 'watchmodmarket'); ?>">
                        <button type="submit" aria-label="<?php esc_attr_e('Subscribe', 'watchmodmarket'); ?>">→</button>
                    </form>
                    <?php } ?>
                </div>
            </div>
        </div>
        <div class="copyright">
            <p>&copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?>. <?php esc_html_e('All rights reserved.', 'watchmodmarket'); ?>
               <a href="<?php echo esc_url(home_url('/terms')); ?>"><?php esc_html_e('Terms', 'watchmodmarket'); ?></a> | 
               <a href="<?php echo esc_url(home_url('/privacy-policy')); ?>"><?php esc_html_e('Privacy', 'watchmodmarket'); ?></a>
            </p>
        </div>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>