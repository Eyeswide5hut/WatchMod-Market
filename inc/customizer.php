<?php
/**
 * WatchModMarket Customizer Options
 */

function watchmodmarket_customize_register($wp_customize) {
    
    // Add Promo Banner Section
    $wp_customize->add_section('watchmodmarket_promo_banner', array(
        'title'    => __('Promo Banner', 'watchmodmarket'),
        'priority' => 30,
    ));
    
    // Promo Banner Text Setting
    $wp_customize->add_setting('promo_banner_text', array(
        'default'           => 'Free shipping on all orders over $100',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('promo_banner_text', array(
        'label'    => __('Promo Banner Text', 'watchmodmarket'),
        'section'  => 'watchmodmarket_promo_banner',
        'settings' => 'promo_banner_text',
        'type'     => 'text',
    ));
    
    // Promo Banner Link Setting
    $wp_customize->add_setting('promo_banner_link', array(
        'default'           => '#',
        'sanitize_callback' => 'esc_url_raw',
    ));
    
    $wp_customize->add_control('promo_banner_link', array(
        'label'    => __('Promo Banner Link', 'watchmodmarket'),
        'section'  => 'watchmodmarket_promo_banner',
        'settings' => 'promo_banner_link',
        'type'     => 'url',
    ));
    
    // Hero Section
    $wp_customize->add_section('watchmodmarket_hero', array(
        'title'    => __('Hero Section', 'watchmodmarket'),
        'priority' => 31,
    ));
    
    // Hero Title Setting
    $wp_customize->add_setting('hero_title', array(
        'default'           => 'Craft Your Signature Timepiece',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('hero_title', array(
        'label'    => __('Hero Title', 'watchmodmarket'),
        'section'  => 'watchmodmarket_hero',
        'settings' => 'hero_title',
        'type'     => 'text',
    ));
    
    // Hero Tagline Setting
    $wp_customize->add_setting('hero_tagline', array(
        'default'           => 'Premium Parts. Massive Savings. Unlimited Customization.',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('hero_tagline', array(
        'label'    => __('Hero Tagline', 'watchmodmarket'),
        'section'  => 'watchmodmarket_hero',
        'settings' => 'hero_tagline',
        'type'     => 'text',
    ));
    
    // CTA Primary Button Text
    $wp_customize->add_setting('cta_primary_text', array(
        'default'           => 'Shop Now',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('cta_primary_text', array(
        'label'    => __('Primary CTA Text', 'watchmodmarket'),
        'section'  => 'watchmodmarket_hero',
        'settings' => 'cta_primary_text',
        'type'     => 'text',
    ));
    
    // CTA Primary Button Link
    $wp_customize->add_setting('cta_primary_link', array(
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ));
    
    $wp_customize->add_control('cta_primary_link', array(
        'label'    => __('Primary CTA Link', 'watchmodmarket'),
        'section'  => 'watchmodmarket_hero',
        'settings' => 'cta_primary_link',
        'type'     => 'url',
    ));
    
    // CTA Secondary Button Text
    $wp_customize->add_setting('cta_secondary_text', array(
        'default'           => 'Custom Build',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('cta_secondary_text', array(
        'label'    => __('Secondary CTA Text', 'watchmodmarket'),
        'section'  => 'watchmodmarket_hero',
        'settings' => 'cta_secondary_text',
        'type'     => 'text',
    ));
    
    // CTA Secondary Button Link
    $wp_customize->add_setting('cta_secondary_link', array(
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ));
    
    $wp_customize->add_control('cta_secondary_link', array(
        'label'    => __('Secondary CTA Link', 'watchmodmarket'),
        'section'  => 'watchmodmarket_hero',
        'settings' => 'cta_secondary_link',
        'type'     => 'url',
    ));
    
    // Contact Information
    $wp_customize->add_section('watchmodmarket_contact', array(
        'title'    => __('Contact Information', 'watchmodmarket'),
        'priority' => 32,
    ));
    
    // Contact Email
    $wp_customize->add_setting('contact_email', array(
        'default'           => 'support@watchmodmarket.com',
        'sanitize_callback' => 'sanitize_email',
    ));
    
    $wp_customize->add_control('contact_email', array(
        'label'    => __('Contact Email', 'watchmodmarket'),
        'section'  => 'watchmodmarket_contact',
        'settings' => 'contact_email',
        'type'     => 'email',
    ));
    
    // Contact Phone
    $wp_customize->add_setting('contact_phone', array(
        'default'           => '+1 (800) 555-1234',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('contact_phone', array(
        'label'    => __('Contact Phone', 'watchmodmarket'),
        'section'  => 'watchmodmarket_contact',
        'settings' => 'contact_phone',
        'type'     => 'text',
    ));
    
    // Contact Address
    $wp_customize->add_setting('contact_address', array(
        'default'           => '123 Watch Street, Suite 456, New York, NY 10001, USA',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('contact_address', array(
        'label'    => __('Contact Address', 'watchmodmarket'),
        'section'  => 'watchmodmarket_contact',
        'settings' => 'contact_address',
        'type'     => 'text',
    ));
    
    // Contact Hours
    $wp_customize->add_setting('contact_hours', array(
        'default'           => 'Monday-Friday: 9am-6pm EST',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('contact_hours', array(
        'label'    => __('Contact Hours', 'watchmodmarket'),
        'section'  => 'watchmodmarket_contact',
        'settings' => 'contact_hours',
        'type'     => 'text',
    ));
    
    // Social Media
    $wp_customize->add_section('watchmodmarket_social', array(
        'title'    => __('Social Media', 'watchmodmarket'),
        'priority' => 33,
    ));
    
    // Social Media Links
    $social_networks = array(
        'facebook'  => __('Facebook', 'watchmodmarket'),
        'instagram' => __('Instagram', 'watchmodmarket'),
        'twitter'   => __('Twitter', 'watchmodmarket'),
        'youtube'   => __('YouTube', 'watchmodmarket'),
    );
    
    foreach ($social_networks as $network => $label) {
        $wp_customize->add_setting('social_' . $network, array(
            'default'           => 'https://' . $network . '.com/watchmodmarket',
            'sanitize_callback' => 'esc_url_raw',
        ));
        
        $wp_customize->add_control('social_' . $network, array(
            'label'    => $label . ' URL',
            'section'  => 'watchmodmarket_social',
            'settings' => 'social_' . $network,
            'type'     => 'url',
        ));
    }
    
    // Instagram Handle
    $wp_customize->add_setting('instagram_handle', array(
        'default'           => '@watchmodmarket',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('instagram_handle', array(
        'label'    => __('Instagram Handle', 'watchmodmarket'),
        'section'  => 'watchmodmarket_social',
        'settings' => 'instagram_handle',
        'type'     => 'text',
    ));
}
add_action('customize_register', 'watchmodmarket_customize_register');

function watchmodmarket_announcement_customizer($wp_customize) {
    
    // Add Announcement Bar Section
    $wp_customize->add_section('announcement_bar_section', array(
        'title'    => __('Announcement Bar', 'watchmodmarket'),
        'priority' => 30,
        'description' => __('Customize the announcement bar that appears at the top of your site.', 'watchmodmarket'),
    ));

    // Show/Hide Announcement Bar
    $wp_customize->add_setting('show_announcement_bar', array(
        'default'           => true,
        'sanitize_callback' => 'watchmodmarket_sanitize_checkbox',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('show_announcement_bar', array(
        'label'   => __('Show Announcement Bar', 'watchmodmarket'),
        'section' => 'announcement_bar_section',
        'type'    => 'checkbox',
    ));

    // Announcement Text
    $wp_customize->add_setting('announcement_text', array(
        'default'           => __('🎉 Free shipping on orders over $100 | 30-day returns', 'watchmodmarket'),
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('announcement_text', array(
        'label'       => __('Announcement Text', 'watchmodmarket'),
        'section'     => 'announcement_bar_section',
        'type'        => 'text',
        'description' => __('Enter the text to display in the announcement bar.', 'watchmodmarket'),
    ));

    // Announcement Link URL
    $wp_customize->add_setting('announcement_link', array(
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('announcement_link', array(
        'label'       => __('Announcement Link URL', 'watchmodmarket'),
        'section'     => 'announcement_bar_section',
        'type'        => 'url',
        'description' => __('Optional: Add a link URL for the announcement button.', 'watchmodmarket'),
    ));

    // Announcement Link Text
    $wp_customize->add_setting('announcement_link_text', array(
        'default'           => __('Shop Now!', 'watchmodmarket'),
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('announcement_link_text', array(
        'label'       => __('Announcement Link Text', 'watchmodmarket'),
        'section'     => 'announcement_bar_section',
        'type'        => 'text',
        'description' => __('Text for the announcement button.', 'watchmodmarket'),
    ));

    // Announcement Style
    $wp_customize->add_setting('announcement_style', array(
        'default'           => 'default',
        'sanitize_callback' => 'watchmodmarket_sanitize_select',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('announcement_style', array(
        'label'   => __('Announcement Style', 'watchmodmarket'),
        'section' => 'announcement_bar_section',
        'type'    => 'select',
        'choices' => array(
            'default'  => __('Default (Orange)', 'watchmodmarket'),
            'sale'     => __('Sale (Red with pulse)', 'watchmodmarket'),
            'info'     => __('Info (Blue)', 'watchmodmarket'),
            'success'  => __('Success (Green)', 'watchmodmarket'),
            'centered' => __('Centered Layout', 'watchmodmarket'),
        ),
    ));

    // Show Extras (Currency/Language selectors)
    $wp_customize->add_setting('show_announcement_extras', array(
        'default'           => true,
        'sanitize_callback' => 'watchmodmarket_sanitize_checkbox',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('show_announcement_extras', array(
        'label'       => __('Show Currency & Language Selectors', 'watchmodmarket'),
        'section'     => 'announcement_bar_section',
        'type'        => 'checkbox',
        'description' => __('Show currency and language selectors on desktop.', 'watchmodmarket'),
    ));

    // Show Currency Selector
    $wp_customize->add_setting('show_currency_selector', array(
        'default'           => true,
        'sanitize_callback' => 'watchmodmarket_sanitize_checkbox',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('show_currency_selector', array(
        'label'   => __('Show Currency Selector', 'watchmodmarket'),
        'section' => 'announcement_bar_section',
        'type'    => 'checkbox',
    ));

    // Default Currency
    $wp_customize->add_setting('default_currency', array(
        'default'           => 'USD',
        'sanitize_callback' => 'watchmodmarket_sanitize_select',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('default_currency', array(
        'label'   => __('Default Currency', 'watchmodmarket'),
        'section' => 'announcement_bar_section',
        'type'    => 'select',
        'choices' => array(
            'USD' => __('USD - US Dollar', 'watchmodmarket'),
            'EUR' => __('EUR - Euro', 'watchmodmarket'),
            'GBP' => __('GBP - British Pound', 'watchmodmarket'),
            'CAD' => __('CAD - Canadian Dollar', 'watchmodmarket'),
            'AUD' => __('AUD - Australian Dollar', 'watchmodmarket'),
            'JPY' => __('JPY - Japanese Yen', 'watchmodmarket'),
            'CHF' => __('CHF - Swiss Franc', 'watchmodmarket'),
        ),
    ));

    // Show Language Selector
    $wp_customize->add_setting('show_language_selector', array(
        'default'           => true,
        'sanitize_callback' => 'watchmodmarket_sanitize_checkbox',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('show_language_selector', array(
        'label'   => __('Show Language Selector', 'watchmodmarket'),
        'section' => 'announcement_bar_section',
        'type'    => 'checkbox',
    ));

    // Dismissible Announcement
    $wp_customize->add_setting('announcement_dismissible', array(
        'default'           => false,
        'sanitize_callback' => 'watchmodmarket_sanitize_checkbox',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('announcement_dismissible', array(
        'label'       => __('Allow Users to Dismiss', 'watchmodmarket'),
        'section'     => 'announcement_bar_section',
        'type'        => 'checkbox',
        'description' => __('Add a close button to allow users to dismiss the announcement.', 'watchmodmarket'),
    ));

    // Auto-hide after time
    $wp_customize->add_setting('announcement_auto_hide', array(
        'default'           => 0,
        'sanitize_callback' => 'absint',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('announcement_auto_hide', array(
        'label'       => __('Auto Hide After (seconds)', 'watchmodmarket'),
        'section'     => 'announcement_bar_section',
        'type'        => 'number',
        'description' => __('Automatically hide the announcement after X seconds. Set to 0 to disable.', 'watchmodmarket'),
        'input_attrs' => array(
            'min'  => 0,
            'max'  => 300,
            'step' => 5,
        ),
    ));
}
add_action('customize_register', 'watchmodmarket_announcement_customizer');

/**
 * Sanitize checkbox values
 */
function watchmodmarket_sanitize_checkbox($checked) {
    return ((isset($checked) && true == $checked) ? true : false);
}

/**
 * Sanitize select values
 */
function watchmodmarket_sanitize_select($input, $setting) {
    $input = sanitize_key($input);
    $choices = $setting->manager->get_control($setting->id)->choices;
    return (array_key_exists($input, $choices) ? $input : $setting->default);
}

/**
 * AJAX handler for currency updates
 */
function watchmodmarket_update_currency() {
    // Check nonce for security
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'watchmodmarket_ajax')) {
        wp_send_json_error(array('message' => __('Security check failed', 'watchmodmarket')));
        return;
    }

    $currency = sanitize_text_field($_POST['currency']);
    
    // Validate currency
    $allowed_currencies = array('USD', 'EUR', 'GBP', 'CAD', 'AUD', 'JPY', 'CHF');
    if (!in_array($currency, $allowed_currencies)) {
        wp_send_json_error(array('message' => __('Invalid currency', 'watchmodmarket')));
        return;
    }

    // Store currency in session/cookie
    if (!session_id()) {
        session_start();
    }
    $_SESSION['selected_currency'] = $currency;
    
    // Also set a cookie for longer persistence
    setcookie('watchmodmarket_currency', $currency, time() + (86400 * 30), '/'); // 30 days

    // If WooCommerce is active, you can update WooCommerce currency
    if (class_exists('WooCommerce')) {
        // Note: This requires additional WooCommerce multi-currency plugin support
        // For demonstration purposes only
        do_action('watchmodmarket_currency_changed', $currency);
    }

    wp_send_json_success(array(
        'message' => sprintf(__('Currency updated to %s', 'watchmodmarket'), $currency),
        'currency' => $currency
    ));
}
add_action('wp_ajax_update_currency', 'watchmodmarket_update_currency');
add_action('wp_ajax_nopriv_update_currency', 'watchmodmarket_update_currency');

/**
 * Get current currency
 */
function watchmodmarket_get_current_currency() {
    // Check session first
    if (session_id() && isset($_SESSION['selected_currency'])) {
        return $_SESSION['selected_currency'];
    }
    
    // Check cookie
    if (isset($_COOKIE['watchmodmarket_currency'])) {
        return sanitize_text_field($_COOKIE['watchmodmarket_currency']);
    }
    
    // Fall back to theme option
    return get_theme_mod('default_currency', 'USD');
}

/**
 * Output inline JavaScript for announcement bar functionality
 */
function watchmodmarket_announcement_inline_script() {
    if (!get_theme_mod('show_announcement_bar', true)) {
        return;
    }
    
    $auto_hide = get_theme_mod('announcement_auto_hide', 0);
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const announcementBar = document.querySelector('.announcement-bar');
        
        if (!announcementBar) return;
        
        // Auto-hide functionality
        <?php if ($auto_hide > 0) : ?>
        setTimeout(function() {
            announcementBar.style.opacity = '0.8';
            announcementBar.classList.add('auto-hiding');
        }, <?php echo intval($auto_hide * 1000); ?>);
        <?php endif; ?>
        
        // Handle dismissal
        const closeButton = announcementBar.querySelector('.announcement-close');
        if (closeButton) {
            closeButton.addEventListener('click', function() {
                announcementBar.style.transform = 'translateY(-100%)';
                announcementBar.style.transition = 'transform 0.3s ease';
                setTimeout(function() {
                    announcementBar.style.display = 'none';
                }, 300);
                localStorage.setItem('announcement_dismissed_<?php echo date('Y-m-d'); ?>', 'true');
            });
            
            // Check if dismissed today
            if (localStorage.getItem('announcement_dismissed_<?php echo date('Y-m-d'); ?>') === 'true') {
                announcementBar.style.display = 'none';
            }
        }
    });
    </script>
    <?php
}
add_action('wp_footer', 'watchmodmarket_announcement_inline_script');

/**
 * Add body classes for announcement bar
 */
function watchmodmarket_announcement_body_classes($classes) {
    if (get_theme_mod('show_announcement_bar', true)) {
        $classes[] = 'has-announcement-bar';
        $classes[] = 'announcement-' . get_theme_mod('announcement_style', 'default');
    }
    return $classes;
}
add_filter('body_class', 'watchmodmarket_announcement_body_classes');