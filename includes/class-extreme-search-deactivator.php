<?php

/**
 * Fired during plugin deactivation
 *
 * @link       #
 * @since      1.0.0
 *
 * @package    Extreme_Search
 * @subpackage Extreme_Search/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Extreme_Search
 * @subpackage Extreme_Search/includes
 * @author     Vicky <vicky.codeaddict@gmail.com>
 */
class Extreme_Search_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {

		$prod_json_path = plugin_dir_path( dirname( __FILE__ ) ) . 'public/json/'.get_option( 'extreme_search_prod_json_name');
		$cat_json_path = plugin_dir_path( dirname( __FILE__ ) ) . 'public/json/'.get_option( 'extreme_search_cat_json_name');
	 	if(is_file($prod_json_path)){
	 		unlink($prod_json_path);
	 	}
	 	if(is_file($cat_json_path)){
	 		unlink($cat_json_path);
	 	}

	 	
	 	delete_option( 'extreme_search_prod_json_name' );
	 	delete_option( 'extreme_search_cat_json_name' );
	}

}
