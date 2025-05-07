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

<!-- Announcement Bar -->
<?php if (get_theme_mod('show_announcement_bar', true)) : ?>
<div class="announcement-bar">
    <div class="container">
        <div class="announcement-content">
            <?php echo esc_html(get_theme_mod('announcement_text', __('Free shipping on orders over $100 | 30-day returns', 'watchmodmarket'))); ?>
            <?php if (get_theme_mod('announcement_link')) : ?>
                <a href="<?php echo esc_url(get_theme_mod('announcement_link')); ?>" class="announcement-link">
                    <?php echo esc_html(get_theme_mod('announcement_link_text', __('Learn More', 'watchmodmarket'))); ?>
                </a>
            <?php endif; ?>
        </div>
        <?php if (get_theme_mod('show_currency_selector', true) || get_theme_mod('show_language_selector', true)) : ?>
        <div class="announcement-extras">
            <?php if (get_theme_mod('show_currency_selector', true)) : ?>
            <div class="currency-selector">
                <select id="currency-selector">
                    <option value="USD">USD</option>
                    <option value="EUR">EUR</option>
                    <option value="GBP">GBP</option>
                    <option value="CAD">CAD</option>
                    <option value="AUD">AUD</option>
                </select>
            </div>
            <?php endif; ?>
            
            <?php if (get_theme_mod('show_language_selector', true)) : ?>
            <div class="language-selector">
                <select id="language-selector">
                    <option value="en">EN</option>
                    <option value="fr">FR</option>
                    <option value="de">DE</option>
                    <option value="es">ES</option>
                    <option value="it">IT</option>
                    <option value="pt">PT</option>
                </select>
            </div>
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
                        
                        <!-- Wishlist Link -->
                        <?php if (function_exists('YITH_WCWL')) : 
                            $wishlist_count = YITH_WCWL()->count_products();
                        ?>
                            <a href="<?php echo esc_url(get_permalink(get_option('yith_wcwl_wishlist_page_id'))); ?>" class="icon-link wishlist-icon" aria-label="<?php esc_attr_e('Wishlist', 'watchmodmarket'); ?>">
                                <i class="fa-solid fa-heart"></i>
                                <?php if ($wishlist_count > 0) : ?>
                                    <span class="wishlist-count"><?php echo esc_html($wishlist_count); ?></span>
                                <?php endif; ?>
                            </a>
                        <?php else : ?>
                            <?php 
                            // Custom wishlist implementation
                            $watchlist_url = get_permalink(get_option('woocommerce_myaccount_page_id'));
                            $watchlist_url = trailingslashit($watchlist_url) . 'watchlist/';
                            
                            // Get custom wishlist count if available
                            $watchlist_count = 0;
                            if (is_user_logged_in()) {
                                $wishlist = get_user_meta(get_current_user_id(), 'watchmodmarket_wishlist', true);
                                if (is_array($wishlist)) {
                                    $watchlist_count = count($wishlist);
                                }
                            }
                            ?>
                            <a href="<?php echo esc_url($watchlist_url); ?>" class="icon-link wishlist-icon" aria-label="<?php esc_attr_e('Wishlist', 'watchmodmarket'); ?>">
                                <i class="fa-solid fa-heart"></i>
                                <?php if ($watchlist_count > 0) : ?>
                                    <span class="wishlist-count"><?php echo esc_html($watchlist_count); ?></span>
                                <?php endif; ?>
                            </a>
                        <?php endif; ?>
                        
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
</html> 
<!-- WooCommerce Notices -->
<?php if (function_exists('woocommerce_output_all_notices')) : ?>
    <div class="wc-notices-wrapper">
        <?php woocommerce_output_all_notices(); ?>
    </div>
<?php endif; ?>


