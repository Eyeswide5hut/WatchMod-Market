<?php
/**
 * The header for our theme
 *
 * @package WatchModMarket
 * @version 1.0.0
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php if (is_search()) : ?>
    <meta name="robots" content="noindex, nofollow">
    <?php endif; ?>
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e('Skip to content', 'watchmodmarket'); ?></a>

<!-- Improved Announcement Bar -->
<?php if (get_theme_mod('show_announcement_bar', true)) : ?>
<div class="announcement-bar <?php echo esc_attr(get_theme_mod('announcement_style', 'default')); ?>">
    <div class="container">
        <div class="announcement-content">
            <p>📦 New Product Launch: Premium Swiss Movements Now Available 
                        <a href="#" class="announcement-link">Explore</a>
            </p>
        </div>
        
        <?php if (get_theme_mod('show_announcement_extras', true)) : ?>
        <div class="announcement-extras">
            <?php if (get_theme_mod('show_currency_selector', true)) : ?>
            <div class="currency-selector">
                <select id="currency-selector" aria-label="<?php esc_attr_e('Select Currency', 'watchmodmarket'); ?>">
                    <option value="USD" <?php selected(get_theme_mod('default_currency', 'USD'), 'USD'); ?>>USD</option>
                    <option value="EUR" <?php selected(get_theme_mod('default_currency', 'USD'), 'EUR'); ?>>EUR</option>
                    <option value="GBP" <?php selected(get_theme_mod('default_currency', 'USD'), 'GBP'); ?>>GBP</option>
                    <option value="CAD" <?php selected(get_theme_mod('default_currency', 'USD'), 'CAD'); ?>>CAD</option>
                    <option value="AUD" <?php selected(get_theme_mod('default_currency', 'USD'), 'AUD'); ?>>AUD</option>
                </select>
            </div>
            <?php endif; ?>
            
            <?php if (get_theme_mod('show_language_selector', true)) : ?>
            <div class="language-selector">
                <select id="language-selector" aria-label="<?php esc_attr_e('Select Language', 'watchmodmarket'); ?>">
                    <option value="en" <?php selected(get_locale(), 'en'); ?>>EN</option>
                    <option value="fr" <?php selected(get_locale(), 'fr'); ?>>FR</option>
                    <option value="de" <?php selected(get_locale(), 'de'); ?>>DE</option>
                    <option value="es" <?php selected(get_locale(), 'es'); ?>>ES</option>
                    <option value="it" <?php selected(get_locale(), 'it'); ?>>IT</option>
                    <option value="pt" <?php selected(get_locale(), 'pt'); ?>>PT</option>
                </select>
            </div>
            <?php endif; ?>
            
            <?php if (get_theme_mod('announcement_dismissible', false)) : ?>
            <button class="announcement-close" aria-label="<?php esc_attr_e('Close announcement', 'watchmodmarket'); ?>">×</button>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>

<header id="masthead" class="site-header">
    <div class="container">
        <div class="header-wrapper">
            <div class="site-branding">
                <?php 
                if (function_exists('the_custom_logo') && has_custom_logo()) {
                    the_custom_logo();
                } else {
                    echo '<a href="' . esc_url(home_url('/')) . '" class="logo" rel="home">' . 
                         esc_html(get_bloginfo('name')) . '</a>';
                }
                ?>
            </div><!-- .site-branding -->

            <button id="mobile-menu-toggle" class="mobile-menu-toggle" aria-controls="primary-menu" aria-expanded="false">
                <span class="visually-hidden"><?php esc_html_e('Menu', 'watchmodmarket'); ?></span>
                <span aria-hidden="true"></span>
                <span aria-hidden="true"></span>
                <span aria-hidden="true"></span>
            </button>

            <div class="header-actions">
                <nav id="site-navigation" class="main-nav" aria-label="<?php esc_attr_e('Main navigation', 'watchmodmarket'); ?>">
                    <?php
                    wp_nav_menu(array(
                        'theme_location' => 'primary',
                        'menu_id'        => 'primary-menu',
                        'container'      => false,
                        'fallback_cb'    => 'watchmodmarket_default_menu'
                    ));
                    ?>
                </nav><!-- #site-navigation -->
                
                <div class="user-actions">
                    <?php if (class_exists('WooCommerce')) : ?>
                        
                        <!-- Account Link -->
                        <a href="<?php echo esc_url(get_permalink(get_option('woocommerce_myaccount_page_id'))); ?>" class="icon-link" aria-label="<?php esc_attr_e('My Account', 'watchmodmarket'); ?>">
                            <i class="fa-solid fa-user"></i>
                        </a>
                        
                        <!-- Cart Link -->
                        <a href="<?php echo esc_url(wc_get_cart_url()); ?>" class="icon-link cart-icon" aria-label="<?php esc_attr_e('Shopping Cart', 'watchmodmarket'); ?>">
                            <i class="fa-solid fa-shopping-cart"></i>
                            <span class="cart-count"><?php echo esc_html(WC()->cart->get_cart_contents_count()); ?></span>
                        </a>
                    <?php endif; ?>
                </div>
            </div><!-- .header-actions -->
        </div><!-- .header-wrapper -->
    </div><!-- .container -->
</header><!-- #masthead -->

<!-- WooCommerce Notices -->
<?php if (function_exists('woocommerce_output_all_notices')) : ?>
    <div class="wc-notices-wrapper">
        <?php woocommerce_output_all_notices(); ?>
    </div>
<?php endif; ?>

<script>

// Add this to the end of your header.php or in a separate JS file
document.addEventListener('DOMContentLoaded', function() {
    const announcementBar = document.querySelector('.announcement-bar');
    
    if (!announcementBar) {
        console.log('Announcement bar not found in DOM');
        return;
    }

    console.log('Announcement bar found and initializing...');
    
    // Handle close button
    const closeButton = announcementBar.querySelector('.announcement-close');
    if (closeButton) {
        closeButton.addEventListener('click', function() {
            console.log('Closing announcement bar');
            announcementBar.style.transform = 'translateY(-100%)';
            announcementBar.style.transition = 'transform 0.3s ease';
            setTimeout(function() {
                announcementBar.style.display = 'none';
            }, 300);
            localStorage.setItem('announcement_dismissed_' + new Date().toISOString().split('T')[0], 'true');
        });
        
        // Check if dismissed today
        const today = new Date().toISOString().split('T')[0];
        if (localStorage.getItem('announcement_dismissed_' + today) === 'true') {
            announcementBar.style.display = 'none';
        }
    }

    // Handle currency selector
    const currencySelector = document.getElementById('currency-selector');
    if (currencySelector) {
        currencySelector.addEventListener('change', function() {
            console.log('Currency changed to:', this.value);
            localStorage.setItem('watchmodmarket_currency', this.value);
            
            // Only make AJAX call if the global variable exists
            if (typeof watchmodmarket_ajax !== 'undefined') {
                fetch(watchmodmarket_ajax.ajax_url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'action=update_currency&currency=' + this.value + '&nonce=' + watchmodmarket_ajax.nonce
                })
                .then(response => response.json())
                .then(data => console.log('Currency update response:', data))
                .catch(error => console.error('Currency update error:', error));
            }
        });
        
        // Set saved currency
        const savedCurrency = localStorage.getItem('watchmodmarket_currency');
        if (savedCurrency) {
            currencySelector.value = savedCurrency;
        }
    }

    // Handle language selector
    const languageSelector = document.getElementById('language-selector');
    if (languageSelector) {
        languageSelector.addEventListener('change', function() {
            console.log('Language changed to:', this.value);
            localStorage.setItem('watchmodmarket_language', this.value);
            // Add your language change logic here
        });
        
        // Set saved language
        const savedLanguage = localStorage.getItem('watchmodmarket_language');
        if (savedLanguage) {
            languageSelector.value = savedLanguage;
        }
    }
    
    // Auto-hide functionality (if enabled via customizer)
    const autoHideTime = announcementBar.dataset.autoHide;
    if (autoHideTime && parseInt(autoHideTime) > 0) {
        setTimeout(function() {
            announcementBar.style.opacity = '0.8';
            announcementBar.classList.add('auto-hiding');
        }, parseInt(autoHideTime) * 1000);
    }
});
</script>

