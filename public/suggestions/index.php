<?php

/**
 * AJAX validation. We don't use admin ajax to load unnecessory stuff.
 * The live-search functionality of the plugin.
 *
 * @link       https://www.vickycodeaddict.com/extreme-search-suggestion-for-woocommerce/
 * @since      1.0.0
 *
 * @package    Extreme_Search
 * @subpackage Extreme_Search/public
 */

if(empty($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest' || !isset($_SERVER['HTTP_REFERER'])){
	die();
}

class Extreme_Search_Live {

	private $query;

	private $settings;

	private $prod_json;

	private $cat_json;


	public function __construct($query_row) {

		$this->query = $this->clean_string($query_row);

		$this->settings = json_decode(file_get_contents( __DIR__ . '/../json/es_settings.json'), true);

		$prod_arr = array_reverse(json_decode(file_get_contents(glob( __DIR__ . '/../json/es-prod-*')[0]), true));

		$keys = array_column($prod_arr, 'order');
		array_multisort($keys, SORT_ASC, $prod_arr);
		$this->prod_json = $prod_arr;

		$this->cat_json = json_decode(file_get_contents(glob( __DIR__ . '/../json/es-cat-*')[0]), true);

	}

	private function clean_string($str){
	   $str = trim($str);
	   $str = preg_replace("/&#?[a-z0-9]+;/i","",$str);
	   $str = str_replace('\u2033','"',$str);
	   $str = str_replace('?','"',$str);
	   $str = strtolower($str);
	   return $str;
	}

	private function full_text_search($search, $string) {
	    return count(array_intersect(explode(" ",$search), explode(" ", preg_replace("/[^A-Za-z0-9' -]/", "", $string))));
	}

	private function is_cat_enable(){
		if(!empty($this->settings['cat_disable']) && $this->settings['cat_disable'] == "yes"){
			return false;
		}
		return true;
	}

	public function suggestions(){

        $suggestions = array();
        $limit = 15;

        if($this->is_cat_enable()){
	        $cat_limit = 5;
		    foreach ($this->cat_json as $cat) {
		        $name = $this->clean_string($cat['value']);
		        if($name == $this->query || strpos($name, $this->query) === 0){
		            if (!in_array($cat, $suggestions)) {
		                $suggestions[] = $cat;
		            }
		        }
		        if(count($suggestions) >= $cat_limit){
		           break;
		        }
		    }
		}

        
	    foreach ($this->prod_json as $product) {
	        $name = $this->clean_string($product['value']);
	        if($name == $this->query || strpos($name, $this->query) === 0){
	            if (!in_array($product, $suggestions)) {
	                $suggestions[] = $product;
	            }
	        }
	        if(count($suggestions) >= $limit){
	           break;
	        }
	    }


		if(count($suggestions) < $limit){
	    	if(!empty($this->settings['search_field'])){
	    		foreach ($this->settings['search_field'] as $search_field) {
			    	foreach ($this->prod_json as $product) {
			    		if(!empty($product['data']['attr'])){
					        $p_field = $this->clean_string($product['data']['fields'][$search_field]);
					        if($p_field == $this->query || strpos($p_field, $this->query) === 0){
					            if (!in_array($product, $suggestions)) {
					                $suggestions[] = $product;
					            }
					        }
					        if(count($suggestions) >= $limit){
					           break;
					        }
				    	}
			    	}
			    }
			}
		}

		if(count($suggestions) < $limit){
			if(!empty($this->settings['search_terms'])){
	    		foreach ($this->settings['search_terms'] as $search_term) {

			    	foreach ($this->prod_json as $product) {
			    		if(!empty($product['data']['attr'])){
					        $p_field = $this->clean_string($product['data']['attr'][$search_term]);
					        if($p_field == $this->query || strpos($p_field, $this->query) === 0){
					            if (!in_array($product, $suggestions)) {
					                $suggestions[] = $product;
					            }
					        }
					        if(count($suggestions) >= $limit){
					           break;
					        }
				    	}
			    	}

			    }
			}
		}

		if(count($suggestions) < $limit){
			foreach ($this->prod_json as $product) {
		        $name = $this->clean_string($product['value']);
		        if(strpos($name, $this->query) > 0){
		            if (!in_array($product, $suggestions)) {
		                $suggestions[] = $product;
		            }
		        }
		        if(count($suggestions) >= $limit){
		           break;
		        }
		    }

		}


        $result = array('query'=> $this->query,'suggestions' => $suggestions);
        ob_clean();
        echo json_encode($result);
        die();
	}
}

$live_search = new Extreme_Search_Live($_GET['query']);
$live_search->suggestions();