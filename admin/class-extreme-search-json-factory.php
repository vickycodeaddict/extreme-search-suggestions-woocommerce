<?php

/**
 * The Json functionality of the plugin.
 *
 * @package    Extreme_Search
 * @subpackage Extreme_Search/admin
 * @author     Vicky <vicky.codeaddict@gmail.com>
 */
class Extreme_Search_Json_Factory {


	private $fields;

	private $fields_attr;

	private $prod_json_path;

	private $cat_json_path;

	private $setting_json_path;

	public function __construct() {

		$this->prod_json_path = plugin_dir_path( dirname( __FILE__ ) ) . 'public/json/'.get_option( 'extreme_search_prod_json_name');
		$this->cat_json_path = plugin_dir_path( dirname( __FILE__ ) ) . 'public/json/'.get_option( 'extreme_search_cat_json_name');
		$this->setting_json_path = plugin_dir_path( dirname( __FILE__ ) ) . 'public/json/es_settings.json';

		$this->fields = get_option( 'wc_settings_tab_extreme_search_section_search_field');
		$this->fields_attr = get_option( 'wc_settings_tab_extreme_search_section_search_terms');

	}

	public function save_settings_json(){
		$setting_json = fopen( $this->setting_json_path, 'wb' );
		$settings = array(
						'display_field' 	=> get_option( 'wc_settings_tab_extreme_search_section_display_field'),
						'display_terms' 	=> get_option( 'wc_settings_tab_extreme_search_section_display_terms'),
						'search_field' 		=> get_option( 'wc_settings_tab_extreme_search_section_search_field'),
						'search_terms' 		=> get_option( 'wc_settings_tab_extreme_search_section_search_terms'),
						'min_char' 			=> get_option( 'wc_settings_tab_extreme_search_section_search_min_char'),
						'cat_disable'		=> get_option( 'wc_settings_tab_extreme_search_suggestion_cat_disable'),
					);
        fwrite( $setting_json, json_encode(apply_filters( 'es_settings_json_extreme_search', $settings),JSON_PRETTY_PRINT) );
        fclose( $setting_json );

        return true;
	}
	
	public function exclude_cat(){
		$exclude_cat = array();
		$uncaterized_obj = get_term_by('name', 'Uncategorized', 'product_cat');;
 
		if ( $uncaterized_obj instanceof WP_Term ) {
		    $exclude_cat[] = $uncaterized_obj->term_id;
		}
		return apply_filters( 'wc_exclude_cat_extreme_search', $exclude_cat);
	}

	public function exclude_products(){
		$products_id_arr = array_filter(array_map('trim', (explode(",", get_option( 'wc_settings_tab_extreme_search_exclude_product')))));
		return apply_filters( 'es_exclude_product_extreme_search', $products_id_arr);
	}

	public function get_categories_arr(){

		$all_cats = array();

		$args = array('taxonomy' => 'product_cat', 'orderby' => 'count', 'exclude' => $this->exclude_cat(), 'hide_empty' => false);
		$terms = get_terms($args);
		if(!empty($terms)){
		    foreach( $terms as $term ) {
		        $thumbnail_id  	=   get_term_meta( $term->term_id , 'thumbnail_id', true );
		        $thumb_src      =   wp_get_attachment_image_src($thumbnail_id , 'woocommerce_gallery_thumbnail');
		        $thumb          =   (isset($thumb_src[0])) ? $thumb_src[0] : '';
		        $data           =   array('icon' => $thumb, 'link' => get_term_link($term->slug, 'product_cat'));
		        $single_cat     =   array('id'=>$term->term_id,'type'=>'c','value' => $term->name,'data'=>$data);
		        $all_cats[]     = 	apply_filters( 'es_single_cat_array_extreme_search', $single_cat);
		    }
		}

		return $all_cats;
	}

	public function build_single_product_arr($product){

		$data = array();

		$product_id = $product->get_id();
    	$thumb =  wp_get_attachment_image_src($product->get_image_id(),'thumbnail');

    	$data['icon'] = (isset($thumb[0])) ? $thumb[0] : '';
        $data['link'] = get_permalink($product_id);
        $data['price'] = $product->get_price();

        $data['fields']['sku'] 			=  (in_array("sku", $this->fields) && $product->get_sku() != '') ? $product->get_sku() : '';
        $data['fields']['short_desc'] 	=  (in_array("short_desc", $this->fields) && $product->get_short_description() != '') ? $product->get_short_description() : '';
        $data['fields']['cat'] 			=  (in_array("cat", $this->fields)) ? strip_tags(wc_get_product_category_list( $product_id)) : '';

        $featured = ($product->is_featured()) ? 4 : 8;

        foreach ($this->fields_attr as $attr) {
        	$data['attr'][$attr] = $product->get_attribute($attr);
        }

        $single_prod = array('id' => $product_id, 'type' => 'p', 'value' => $product->get_title(), 'data' => $data, 'order' => $featured);

        return apply_filters( 'es_single_product_array_extreme_search', $single_prod);
	}

	public function get_products_arr(){
		$all_products = array();

		for ($i = 0; $i < EXTREME_SEARCH_MAX_PRODUCT;) {

		    $args = array(
		        'offset'    => $i,
		        'limit'     => ($i + EXTREME_SEARCH_PRODUCT_CHUNK),
		        'exclude'   => $this->exclude_products(),
		        'status'    => 'publish',
		        'order'     => 'ASC',
		    );
		    $products = wc_get_products($args);
		    if (!empty($products)) {
		        foreach ($products as $product) {
		            $all_products[] = $this->build_single_product_arr($product);
		        }
		    }else{
		    	break;
		    }
		    
		    $i += EXTREME_SEARCH_PRODUCT_CHUNK;
		}

		return $all_products;

	}

	public function regenerate_cat_json() {
		$all_cats = $this->get_categories_arr();

		$cat_json = fopen( $this->cat_json_path, 'wb' );
        fwrite( $cat_json, json_encode($all_cats) );
        fclose( $cat_json );

        return true;
	}

	public function regenerate_product_json() {
		$all_products = $this->get_products_arr();

		$prod_json = fopen( $this->prod_json_path, 'wb' );
        fwrite( $prod_json, json_encode($all_products) );
        fclose( $prod_json );

        return true;
	}


	public function regenerate_json() {
		
		$products_json = $this->regenerate_cat_json();
		$cat_json = $this->regenerate_product_json();

		return ($products_json && $cat_json);

	}

	public function alter_product_json($post_id, $post, $update){

		if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    	if(!(wp_is_post_revision($post_id) || !wp_is_post_autosave($post_id))) return;

		if(get_option('wc_settings_tab_extreme_search_section_regenerate') === 'yes'){
			$this->regenerate_product_json();

		}else{
			$product = wc_get_product( $post_id );
			$id = $product->get_id();

			$all_products = json_decode(file_get_contents($this->prod_json_path), true);

			if($update === true){
				foreach($all_products as $key => $sub_array) {
				    if($sub_array['id'] == $id) {
				        unset($all_products[$key]);
				        break;
				    }
				}
			}

			if ($product->get_status() == 'publish') {
				$all_products[] = $this->build_single_product_arr($product);
			}

			$prod_json = fopen( $this->prod_json_path, 'wb' );
	        fwrite( $prod_json, json_encode($all_products) );
	        fclose( $prod_json );
	        return true;

		}

		/*
		remove_action( 'post_save', array( 'Extreme_Search_Json_Factory', 'alter_product_json' ), 30); 
		*/
	}

	public function update_history_list(){

		if ( isset( $_GET['action'] ) && $_GET['action'] == 'woocommerce_feature_product' && check_admin_referer( 'woocommerce-feature-product' ) && isset( $_GET['product_id'] ) ) {

			$product_id = sanitize_text_field(absint($_GET['product_id']));
			if($product_id && $product_id != 0){
				$history = get_option('es_history');
				do_action( 'wc_before_update_history_extreme_search',$history, $product_id);
				if($history === false){
					update_option('es_history', array($product_id));
				}else{
					$history[] = $product_id;
					update_option('es_history', $history);
				}
				do_action( 'es_after_update_history_extreme_search', $product_id);
			}
		}

	}

	public function clear_history_list(){

		$history = get_option('es_history');
		if($history === false){
			return;
		}else{
			foreach ((array)$history as $p_id) {
				$product = wc_get_product( $p_id );
				if ( $product ) {
					$this->alter_product_json( $p_id, $product, true);
				}
			}
			update_option('es_history', false);
			do_action( 'es_after_clear_history_extreme_search');
		}

	}




}
