<?php

/**
 * Fired during plugin activation
 *
 * @link       https://www.vickycodeaddict.com/extreme-search-suggestion-for-woocommerce/
 * @since      1.0.0
 *
 * @package    Extreme_Search
 * @subpackage Extreme_Search/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Extreme_Search
 * @subpackage Extreme_Search/includes
 * @author     Vicky <vicky.codeaddict@gmail.com>
 */
class Extreme_Search_Activator {

	/**
	 * Update Default Options
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

		if( get_option('wc_settings_tab_extreme_search_selector') === false &&
		    get_option('wc_settings_tab_extreme_search_section_display_field') === false &&
		    get_option('wc_settings_tab_extreme_search_popup_search') === false &&
			get_option('wc_settings_tab_extreme_search_section_display_terms') === false &&
			get_option('wc_settings_tab_extreme_search_section_search_field') === false &&
			get_option('wc_settings_tab_extreme_search_section_search_terms') === false &&
			get_option('wc_settings_tab_extreme_search_exclude_product') === false &&
			get_option('wc_settings_tab_extreme_search_section_search_min_char') === false &&
			get_option('wc_settings_tab_extreme_search_redirect') === false
		){
			update_option( 'wc_settings_tab_extreme_search_selector', 'input[type="search"]' );
			update_option( 'wc_settings_tab_extreme_search_section_display_field', array('sku','price','thumb') );
			update_option( 'wc_settings_tab_extreme_search_popup_search', 'yes' );
			update_option( 'wc_settings_tab_extreme_search_section_display_terms', array() );
			update_option( 'wc_settings_tab_extreme_search_section_search_field', array('sku','category') );
			update_option( 'wc_settings_tab_extreme_search_section_search_terms', array() );
			update_option( 'wc_settings_tab_extreme_search_exclude_product', '' );
			update_option( 'wc_settings_tab_extreme_search_section_search_min_char', '2' );
			update_option( 'wc_settings_tab_extreme_search_redirect', 'yes' );
		}

		if( get_option('extreme_search_prod_json_name') === false &&
			get_option('extreme_search_cat_json_name') === false
		){

			$prod_jsons = glob(plugin_dir_path( dirname( __FILE__ ) ) . 'public/json/es-prod-*');
			foreach($prod_jsons as $file){
				if(is_file($file)) {
					unlink($file);
				}
			}

			$cat_jsons = glob(plugin_dir_path( dirname( __FILE__ ) ) . 'public/json/es-cat-*');
			foreach($cat_jsons as $file){
				if(is_file($file)) {
					unlink($file);
				}
			}


			$characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
			$characters_length = strlen($characters);
		    $rand_str = '';
		    for ($i = 0; $i < 20; $i++) {
		        $rand_str .= $characters[rand(0, $characters_length - 1)];
		    }

		    update_option( 'extreme_search_prod_json_name', 'es-prod-'. $rand_str . '.json' );
		    update_option( 'extreme_search_cat_json_name', 'es-cat-'. $rand_str . '.json' );

		    $json_factory = new Extreme_Search_Json_Factory();
		    $json_factory->regenerate_json();

		    
		}

	    update_option( 'extreme_search_rebuilt', true );

	}

}
