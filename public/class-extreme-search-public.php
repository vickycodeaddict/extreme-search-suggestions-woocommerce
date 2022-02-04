<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       #
 * @since      1.0.0
 *
 * @package    Extreme_Search
 * @subpackage Extreme_Search/public
 */

class Extreme_Search_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/extreme-search-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( 'devbridgeAutocomplete', plugin_dir_url( __FILE__ ) . 'js/devbridgeAutocomplete.js', array(), $this->version, true );
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/extreme-search-public.min.js', array( 'jquery', 'devbridgeAutocomplete' ), $this->version, true );
		//wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/extreme-search-public.js', array( 'jquery', 'devbridgeAutocomplete' ), $this->version, true );

		wp_localize_script( $this->plugin_name, 'es_obj', apply_filters( 'es_object_extreme_search', 
				array( 
	            'es_ajax_url' 		=> plugin_dir_url( __FILE__ ) . 'suggestions/',
	            'es_admin_ajax_url' => admin_url('admin-ajax.php'),
	            'es_currency_symbol'=> get_woocommerce_currency_symbol(),
	            'es_woo_placeholder'=> wc_placeholder_img_src(),
	            'es_display_field' 	=> get_option( 'wc_settings_tab_extreme_search_section_display_field'),
				'es_display_terms' 	=> get_option( 'wc_settings_tab_extreme_search_section_display_terms'),
	            'es_selector' 		=> get_option( 'wc_settings_tab_extreme_search_selector'),
	            'es_min_char' 		=> get_option( 'wc_settings_tab_extreme_search_section_search_min_char'),
	            'es_redirect' 		=> get_option( 'wc_settings_tab_extreme_search_redirect'),
	            'es_popup'    		=> get_option( 'wc_settings_tab_extreme_search_popup_search'),
	            'es_trans_sku'     	=> __( 'SKU', 'extreme-search' ),
	            'es_trans_price'    => __( 'Price', 'extreme-search' ),
	            'es_trans_cat'     	=> __( 'Category', 'extreme-search' ),
	            'es_trans_cat_desc'	=> get_option('wc_settings_tab_extreme_search_suggestion_cat_desc', 'Browse Products'),
	            'es_main'     		=> 1,
	        	)
	        )
	    );

	}

	public function es_update_term() {
		$type = sanitize_text_field($_POST['type']);
		$id = sanitize_text_field(absint($_POST['id']));
		if(!empty($id) && !empty($type) && $id != 0){
			if($type == "c"){
				$cat_count = get_term_meta($id,'es_count_search',true);
				$count = (!empty($cat_count)) ? (intval($cat_count) + 1) : 1;
				update_term_meta($id,'es_count_search',$count);
			}else{
				$prod_count = get_post_meta($id,'es_count_search',true);
				$count = (!empty($prod_count)) ? (intval($prod_count) + 1) : 1;
				update_post_meta($id,'es_count_search',$count);
			}
		}
		die();
	}

}
