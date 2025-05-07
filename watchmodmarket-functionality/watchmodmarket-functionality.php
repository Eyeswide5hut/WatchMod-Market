<?php
/**
 * Plugin Name: WatchModMarket Functionality
 * Plugin URI: https://watchmodmarket.com
 * Description: Custom post types and taxonomies for WatchModMarket
 * Version: 1.0.0
 * Author: Your Name
 * Author URI: https://yourwebsite.com
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: watchmodmarket-functionality
 * Domain Path: /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

// Include post types
require_once plugin_dir_path(__FILE__) . 'includes/post-types.php';

// Include taxonomies
require_once plugin_dir_path(__FILE__) . 'includes/taxonomies.php';