<?php
/**
 * @link              https://www.vickycodeaddict.com/extreme-search-suggestion-for-woocommerce/
 * @since             1.0.0
 * @package           Extreme_Search
 *
 * @wordpress-plugin
 * Plugin Name:       Extreme Search Suggestion for WooCommerce
 * Plugin URI:        https://www.vickycodeaddict.com/extreme-search-suggestion-for-woocommerce/
 * Description:       Advance search suggestion with Extreme speed and options.
 * Version:           2.0.0
 * Author:            Vicky
 * Author URI:        https://www.vickycodeaddict.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       extreme-search
 * Domain Path:       /languages
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}
define( 'EXTREME_SEARCH_VERSION', '2.0.0' );

define( 'EXTREME_SEARCH_BASE', plugin_basename(__FILE__) );

define( 'EXTREME_SEARCH_MAX_PRODUCT', 8000 );

define( 'EXTREME_SEARCH_PRODUCT_CHUNK', 200 );

if (!function_exists('is_plugin_active')) {
    include_once(ABSPATH . '/wp-admin/includes/plugin.php');
}

/**
 * Check for the existence of WooCommerce.
 *
 * @since    1.0.0
 */
function extreme_search_check_requirements() {
    if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
        return true;
    } else {
        add_action( 'admin_notices', 'extreme_search_missing_wc_notice' );
        return false;
    }
}

function extreme_search_missing_wc_notice() { 
    $class = 'notice notice-error';
    $message = __( 'Extreme Search requires WooCommerce to be installed.', 'extreme-search' );
 
    printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) ); 
}

add_action( 'plugins_loaded', 'extreme_search_check_requirements' );


/**
 * Activate and Deactivate function.
 *
 * @since    1.0.0
 */
function activate_extreme_search() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-extreme-search-activator.php';
	Extreme_Search_Activator::activate();
}

function deactivate_extreme_search() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-extreme-search-deactivator.php';
	Extreme_Search_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_extreme_search' );
register_deactivation_hook( __FILE__, 'deactivate_extreme_search' );

require plugin_dir_path( __FILE__ ) . 'includes/class-extreme-search.php';

/**
 * Begins execution of the plugin.
 *
 * @since    1.0.0
 */
function run_extreme_search() {

	if (extreme_search_check_requirements()) {
		$plugin = new Extreme_Search();
		$plugin->run();
	}

}
run_extreme_search();
