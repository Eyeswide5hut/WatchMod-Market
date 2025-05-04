<?php
/**
 * The header for our theme
 *
 * @package WatchModMarket
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <?php if (is_search()) : ?>
    <meta name="robots" content="noindex, nofollow">
    <?php endif; ?>
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e('Skip to content', 'watchmodmarket'); ?></a>

<header id="masthead" class="site-header">
    <div class="container">
        <div class="header-wrapper">
            <div class="site-branding">
                <?php watchmodmarket_custom_logo(); ?>
            </div><!-- .site-branding -->

            <button id="mobile-menu-toggle" class="mobile-menu-toggle" aria-controls="primary-menu" aria-expanded="false">
                <span class="visually-hidden"><?php esc_html_e('Menu', 'watchmodmarket'); ?></span>
                <span aria-hidden="true"></span>
                <span aria-hidden="true"></span>
                <span aria-hidden="true"></span>
            </button>

            <div class="header-actions">
                <div class="search-bar">
                    <?php get_search_form(); ?>
                </div>
                
                <nav id="site-navigation" class="main-nav" aria-label="<?php esc_attr_e('Main navigation', 'watchmodmarket'); ?>">
                    <?php
                    wp_nav_menu(array(
                        'theme_location' => 'primary',
                        'menu_id'        => 'primary-menu',
                        'container'      => false,
                        'items_wrap'     => '<ul id="%1$s" class="%2$s">%3$s</ul>',
                        'fallback_cb'    => 'watchmodmarket_menu_fallback',
                    ));
                    ?>
                </nav><!-- #site-navigation -->
                
                <div class="user-actions">
                    <?php if (class_exists('WooCommerce')) : ?>
                    <a href="<?php echo esc_url(get_permalink(get_option('woocommerce_myaccount_page_id'))); ?>" class="icon-link" aria-label="<?php esc_attr_e('My Account', 'watchmodmarket'); ?>">
                        <i class="fa-solid fa-user"></i>
                    </a>
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

<?php if (function_exists('woocommerce_output_all_notices')) : ?>
    <div class="wc-notices-wrapper">
        <?php woocommerce_output_all_notices(); ?>
    </div>
<?php endif; ?>

<?php
// Display promo banner if it exists in customizer or ACF field
$promo_banner_text = get_theme_mod('promo_banner_text', '');
$promo_banner_link = get_theme_mod('promo_banner_link', '');

if (!empty($promo_banner_text)) :
?>
<section class="promo-banner" aria-label="<?php esc_attr_e('Promotional Banner', 'watchmodmarket'); ?>">
    <div class="container">
        <p><?php echo esc_html($promo_banner_text); ?> 
        <?php if (!empty($promo_banner_link)) : ?>
            <a href="<?php echo esc_url($promo_banner_link); ?>"><?php esc_html_e('Shop Now', 'watchmodmarket'); ?></a>
        <?php endif; ?>
        </p>
    </div>
</section>
<?php endif; ?>

<?php
// Display promo banner if it exists in customizer or ACF field
$promo_banner_text = get_theme_mod('promo_banner_text', '');
$promo_banner_link = get_theme_mod('promo_banner_link', '');

// If there's no customizer banner, show Group Buy promo
if (empty($promo_banner_text)) {
    $promo_banner_text = 'Limited Time Offer: Join our Timepiece Futures Group Buy for exclusive watch parts at up to 40% off!';
    $promo_banner_link = get_permalink(get_page_by_path('group-buy'));
}
?>