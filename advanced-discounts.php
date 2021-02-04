<?php
/**
 * Advanced discounts system for WooCommerce
 *
 * @package           AdvancedDiscounts
 * @author            Anatolii S.
 * @license           GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:       Advanced discounts system for WooCommerce
 * Plugin URI:        https://github.com/SobolevAnatoly/Advanced-discounts-system-for-WooCommerce
 * Description:       Advanced discounts system for WooCommerce. The plugin allows you to set a product discount amount for each customer role.
 * Version:           1.0.0
 * Requires at least: 5.0
 * Requires PHP:      7.2
 * Author:            Anatolii S.
 * Author URI:        https://github.com/SobolevAnatoly
 * Text Domain:       advanced-discounts
 * Domain Path:       /languages
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.txt
 */

if (!defined('ABSPATH')) {
    exit; // Don't access directly.
};

if (defined('ADS_VERSION')) {
    // The user is attempting to activate a second plugin instance.
    require_once ABSPATH . 'wp-admin/includes/plugin.php';
    require_once ABSPATH . 'wp-includes/pluggable.php';
    if (is_plugin_active(plugin_basename(__FILE__))) {
        deactivate_plugins(plugin_basename(__FILE__)); // Deactivate this plugin.
        // Inform that the plugin is deactivated.
        wp_safe_redirect(add_query_arg('deactivate', 'true', remove_query_arg('activate')));
        exit;
    }
}

define('ADS_VERSION', '1.0.0');

define('ADS_REQUIRED_WP_VERSION', '5.0');

define('ADS_REQUIRED_PHP_VERSION', '7.2');

define('ADS_PLUGIN', __FILE__);

define('ADS_PLUGIN_BASENAME', plugin_basename(ADS_PLUGIN));

define('ADS_PLUGIN_NAME', trim(dirname(ADS_PLUGIN_BASENAME), '/'));

define('ADS_PLUGIN_DIR', untrailingslashit(dirname(ADS_PLUGIN)));

define('ADS_PLUGIN_URL', plugin_dir_url(__FILE__));

define('WooCommerce_REQUIRED_VERSION', '4.5');

// Check for required PHP version
if (version_compare(PHP_VERSION, ADS_REQUIRED_PHP_VERSION, '<')) {
    exit(esc_html(sprintf('Advanced discounts system for WooCommerce requires PHP 7.2 or higher. You’re still on %s.', PHP_VERSION)));
}

// Check for required Wordpress version
if (version_compare(get_bloginfo('version'), ADS_REQUIRED_WP_VERSION, '<')) {
    exit(esc_html(sprintf('Advanced discounts system for WooCommerce requires Wordpress 5.0 or higher. You’re still on %s.', get_bloginfo('version'))));
}

/**
 * Check if WooCommerce is installed and active && current version >=
 * according to https://docs.woocommerce.com/document/create-a-plugin/
 **/
if (!in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
    exit('WooCommerce must be installed and activated!');
}

if (defined('WC_VERSION') && version_compare(WC_VERSION, WooCommerce_REQUIRED_VERSION, '<')) {
    exit(esc_html(sprintf('Advanced discounts system for WooCommerce requires WooCommerce ' . WooCommerce_REQUIRED_VERSION . ' or higher. You’re still on %s.', WC_VERSION)));
}

if (file_exists(ADS_PLUGIN_DIR . '/vendor/autoload.php')) {
    require_once ADS_PLUGIN_DIR . '/vendor/autoload.php';
}


if (class_exists('Adsfwc\\Init')) {
    new Adsfwc\Init();
}


