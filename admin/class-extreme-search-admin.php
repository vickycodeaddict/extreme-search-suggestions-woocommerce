<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @package    Extreme_Search
 * @subpackage Extreme_Search/admin
 * @author     Vicky <vicky.codeaddict@gmail.com>
 */
class Extreme_Search_Admin {

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
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}


	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		if($this->is_setting_tab()){

			wp_enqueue_style( $this->plugin_name . '-select2', plugin_dir_url( __FILE__ ) . 'css/select2.min.css', array(), $this->version, 'all' );

			wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/extreme-search-admin.css', array(), $this->version, 'all' );
			
		}

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		if($this->is_setting_tab()){

			wp_enqueue_script( $this->plugin_name . '-select2', plugin_dir_url( __FILE__ ) . 'js/select2.min.js', array( 'jquery' ), $this->version, false );

			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/extreme-search-admin.js', array( 'jquery' ), $this->version, false );
			
		}

	}

	/**
	 * Notice for Donation for plugin.
	 *
	 * @since    1.0.0
	 */
	public function es_notice_donation(){

		if($this->is_setting_tab()){
			add_action('admin_notices', array($this, 'es_notice_donation_html'));
		}

	}

	/**
	 * HTML for Donation Notice.
	 *
	 * @since    1.0.0
	 */
	public function es_notice_donation_html(){

		echo '<div class="notice notice-info es_donate_notice"><p>';
        echo '<strong>'.__( 'Donate: ', 'extreme-search' ).'</strong>';
        echo __( 'Your donation will help encourage and support the plugin’s continued development and better user support. &nbsp; ', 'extreme-search' );
        echo '<a href="https://www.vickycodeaddict.com/donate/" target="_blank" class="button button-primary">'.__('Donate','extreme-search').'</a>';
    	echo '</p></div>';

	}

	/**
	 * Check if current page is setting page of plugin.
	 *
	 * @since    1.0.0
	 */

	public function is_setting_tab(){

		return (
			function_exists('get_current_screen') && 
			get_current_screen()->base == 'woocommerce_page_wc-settings' && 
			isset($_GET['tab']) &&
			esc_attr($_GET['tab']) == 'settings_tab_extreme_search'
		) ? true : false;
	}

	/**
	 * Add Setting link on plugin list.
	 *
	 * @since    1.0.0
	 */
	public function plugin_settings_link($links) { 
	  	$settings_link = '<a href="admin.php?page=wc-settings&tab=settings_tab_extreme_search">Settings</a>'; 
	  	array_unshift($links, $settings_link); 
	  	return $links; 
	}

	public function alternative_submenu_link(){
		add_submenu_page( 'woocommerce', __( 'Extreme Search', 'extreme-search' ), __( 'Extreme Search', 'extreme-search' ), 'manage_options', admin_url( 'admin.php?page=wc-settings&tab=settings_tab_extreme_search')); 
	}

	public function plugin_options_tab($settings_tabs) {

		$settings_tabs['settings_tab_extreme_search'] = __( 'Extreme Search', 'extreme-search' );
        return $settings_tabs;

	}

	public function settings_tab() {

		woocommerce_admin_fields( $this->get_settings() );

	}

	public function get_settings(){
		$attr = wc_get_attribute_taxonomies();
		$attr_arr = wp_list_pluck( $attr, 'attribute_label', 'attribute_name'  );

		$settings = array(
	        'es_section_title_display' => array(
	            'name'     => __( 'Extreme Search Settings', 'extreme-search' ),
	            'type'     => 'title',
	            'desc'     => '',
	            'id'       => 'wc_settings_tab_extreme_search_section_title'
	        ),
	        'es_search_selector' => array(
			    'name'    => __( 'Selector of search input', 'extreme-search' ),
			    'id'      => 'wc_settings_tab_extreme_search_selector',
			    'class'   => 'slector',
			    'std'     => '',
			    'default' => '',
			    'type'    => 'text',
			),
			'es_popup_search' => array(
			    'name'    => __( 'Popup on search box', 'extreme-search' ),
			    'id'      => 'wc_settings_tab_extreme_search_popup_search',
			    'std'     => 'yes',
			    'default' => 'yes', 
			    'type'    => 'checkbox'
			),
	        'es_regenerate' => array(
			    'name'    => __( 'Regenerate on Edit Product', 'extreme-search' ),
			    'desc'    => __( 'Refresh all data on edit product, It will slow down edit product if store has huge product.', 'extreme-search' ),
			    'id'      => 'wc_settings_tab_extreme_search_section_regenerate',
			    'std'     => '',
			    'default' => '', 
			    'type'    => 'checkbox'
			),
			'es_display_field' => array(
			    'name'    => __( 'Suggestion Display Fields', 'extreme-search' ),
			    'id'      => 'wc_settings_tab_extreme_search_section_display_field',
			    'class'   => 'es_select2',
			    'default' => array('sku','price','thumb'),
			    'type'    => 'multiselect',
			    'options' => array(
			      	'sku'    	=> __( 'SKU', 'extreme-search' ),
			      	'price'     => __( 'Price', 'extreme-search' ),
			      	'thumb'  	=> __( 'Thumbnail', 'extreme-search' ),
			      	'cat' 	=> __( 'Category', 'extreme-search' ),
			    ),
			),
			'es_display_terms' => array(
			    'name'    => __( 'Suggestion Display Attributes', 'extreme-search' ),
			    'id'      => 'wc_settings_tab_extreme_search_section_display_terms',
			    'class'   => 'es_select2',
			    'default' => array(),
			    'type'    => 'multiselect',
			    'options' => $attr_arr,
			),
			'es_search_field' => array(
			    'name'    => __( 'Search Fields', 'extreme-search' ),
			    'desc'    => __( 'This fields will used in search/suggestion', 'extreme-search' ),
			    'id'      => 'wc_settings_tab_extreme_search_section_search_field',
			    'class'   => 'es_select2',
			    'default' => array('sku','category'),
			    'type'    => 'multiselect',
			    'options' => array(
			      	'sku'        => __( 'SKU', 'extreme-search' ),
			      	'cat'   => __( 'Category', 'extreme-search' ),
			      	'short_desc' => __( 'Short Description', 'extreme-search' ),
			    ),
			),
			'es_search_terms' => array(
			    'name'    => __( 'Search Attributes', 'extreme-search' ),
			    'id'      => 'wc_settings_tab_extreme_search_section_search_terms',
			    'class'     => 'es_select2',
			    'default' => array(),
			    'type'    => 'multiselect',
			    'options' => $attr_arr,
			),
			'es_search_exclude_product' => array(
			    'name'    => __( 'Exclude Product', 'extreme-search' ),
			    'desc'    => __( 'Comma separated products ID' ),
			    'id'      => 'wc_settings_tab_extreme_search_exclude_product',
			    'class'   => 'pri',
			    'std'     => '',
			    'default' => '',
			    'type'    => 'text',
			),
			'es_search_min_char' => array(
			    'name'    => __( 'Minimum keyword for suggestion', 'extreme-search' ),
			    'id'      => 'wc_settings_tab_extreme_search_section_search_min_char',
			    'class'   => 'min',
			    'std'     => '2',
			    'default' => '2',
			    'type'    => 'number',
			),
			'es_redirect' => array(
			    'name'    => __( 'Redirect on New Tab', 'extreme-search' ),
			    'id'      => 'wc_settings_tab_extreme_search_redirect',
			    'std'     => 'yes',
			    'default' => 'yes', 
			    'type'    => 'checkbox'
			),
			'es_suggestion_cat_disable' => array(
			    'name'    => __( 'Disable Category in Suggestion', 'extreme-search' ),
			    'id'      => 'wc_settings_tab_extreme_search_suggestion_cat_disable',
			    'std'     => '',
			    'default' => '', 
			    'type'    => 'checkbox'
			),
			'es_suggestion_cat_desc' => array(
			    'name'    => __( 'Category Placeholder in Suggestion', 'extreme-search' ),
			    'id'      => 'wc_settings_tab_extreme_search_suggestion_cat_desc',
			    'class'   => 'tx',
			    'std'     => 'Browse Products',
			    'default' => 'Browse Products',
			    'type'    => 'text',
			),
	        'es_section_end' => array(
	            'type' => 'sectionend',
	            'id' => 'wc_settings_tab_extreme_search_section_end'
	        ),
	    );

	    return apply_filters( 'es_settings_tab_extreme_search', $settings );

	}

	public function update_settings() {
	    woocommerce_update_options( $this->get_settings() );
	}

	public function es_dashboard_widgets(){
		global $wp_meta_boxes;
		wp_add_dashboard_widget('es_prod_dashboard_search', 'Most Searched Products', array($this, 'es_prod_dashboard_search'), null, null, 'side', 'high');
		wp_add_dashboard_widget('es_cat_dashboard_search', 'Most Searched Categories', array($this, 'es_cat_dashboard_search'), null, null, 'side', 'high');
	}

	public function es_prod_dashboard_search() {
		$args = array(
		    'post_status' => 'publish',
		    'post_type' => 'product',
		    'posts_per_page' => 10,
		    'meta_key' => 'es_count_search',
		    'orderby' => 'meta_value_num',
		    'order' => 'DESC'
		);
		$the_query = new WP_Query( $args );
		if ($the_query->have_posts()) {
		    echo '<ul>';
		    while ( $the_query->have_posts() ) {
		        $the_query->the_post();
		        echo '<li><a href="'.esc_url(get_permalink()).'">' . esc_html(get_the_title()) . '</a> <strong>('.esc_html(get_post_meta(get_the_ID(),'es_count_search',true)).')</strong></li>';
		    }
		    echo '</ul>';
		}
		wp_reset_postdata();
	}

	public function es_cat_dashboard_search(){

		$args = array(
			'taxonomy' => 'product_cat',
			'orderby' => 'meta_value_num',
			'order' => 'DESC',
			'number' => 10,
			'hide_empty' => false,
			'meta_query' => array(
				array(
					'key' => 'es_count_search',
					'type' => 'NUMERIC',
				)
			),
		);

		$terms = get_terms( $args );

		if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
			echo '<ul>';
		    foreach ( $terms as $term ) {

		    	echo '<li><a href="'.esc_url( get_term_link( $term ) ).'">' . esc_html($term->name) . '</a> <strong>('.esc_html(get_term_meta($term->term_id,'es_count_search',true)).')</strong></li>';

		    }
		    echo '</ul>';
		}

	}

	/**
	 * Add Hit column and make it sortable
	 * of the plugin.
	 *
	 * @since    1.1.0
	 * @access   public
	 */

	public function es_products_hit_column_array( $columns_array ) {

		return array_slice( $columns_array, 0, 8, true )
		+ array( 'es_hit' => __( 'Hit', 'extreme-search' ) )
		+ array_slice( $columns_array, 8, NULL, true );
	}

	public function es_products_hit_column_data( $column_key, $post_id ) {

		if( $column_key  == 'es_hit' ) {
			$es_count_search = get_post_meta($post_id,'es_count_search',true);
			if($es_count_search){
				echo esc_html($es_count_search);
			}
		}
	}

	public function es_products_hit_column_sortable( $columns ) {
		$columns['es_hit'] = 'es_hit';
		return $columns;
	}

	public function es_products_hit_column_orderby( $query ) {
		if( ! is_admin() ){
	        return;
		}
	 
	    $orderby = $query->get( 'orderby');
	 
	    if( 'es_hit' == $orderby ) {
	        $query->set('meta_key','es_count_search');
	        $query->set('orderby','meta_value_num'); 
	    }
	}


}
