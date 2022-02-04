<?php
/**
 *
 * @since      1.0.0
 * @package    Extreme_Search
 * @subpackage Extreme_Search/includes
 * @author     Vicky <vicky.codeaddict@gmail.com>
 */
class Extreme_Search {

	/**
	 * @since    1.0.0
	 * @access   protected
	 * @var      Extreme_Search_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'EXTREME_SEARCH_VERSION' ) ) {
			$this->version = EXTREME_SEARCH_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'extreme-search';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-extreme-search-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-extreme-search-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-extreme-search-admin.php';

		/**
		 * The class responsible for JSON operation.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-extreme-search-json-factory.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-extreme-search-public.php';

		$this->loader = new Extreme_Search_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Extreme_Search_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Extreme_Search_Admin( $this->get_plugin_name(), $this->get_version() );
		$json_factory = new Extreme_Search_Json_Factory();

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		$this->loader->add_action( 'admin_head', $plugin_admin, 'es_notice_donation' );

		$this->loader->add_filter( 'plugin_action_links_'. EXTREME_SEARCH_BASE, $plugin_admin, 'plugin_settings_link' );

		$this->loader->add_filter( 'woocommerce_settings_tabs_array', $plugin_admin, 'plugin_options_tab', 50 );
		$this->loader->add_action( 'woocommerce_settings_settings_tab_extreme_search', $plugin_admin, 'settings_tab' );
		$this->loader->add_action( 'woocommerce_update_options_settings_tab_extreme_search', $plugin_admin, 'update_settings' );

		$this->loader->add_action( 'admin_menu', $plugin_admin, 'alternative_submenu_link', 100 );

		$this->loader->add_action( 'woocommerce_update_options_settings_tab_extreme_search', $json_factory, 'regenerate_json' );

		$this->loader->add_action( 'woocommerce_update_options_settings_tab_extreme_search', $json_factory, 'save_settings_json' );

		$this->loader->add_action( 'created_term', $json_factory, 'regenerate_cat_json', 10, 0 );
		$this->loader->add_action( 'edited_term', $json_factory, 'regenerate_cat_json', 10, 0 );
		$this->loader->add_action( 'delete_term', $json_factory, 'regenerate_cat_json', 10, 0 );

		$this->loader->add_action( 'save_post_product', $json_factory, 'alter_product_json', 50, 3 );
		
		$this->loader->add_action( 'wp_ajax_woocommerce_feature_product', $json_factory, 'update_history_list', 5, 0 );
		$this->loader->add_action( 'admin_enqueue_scripts', $json_factory, 'clear_history_list', 100, 0 );

		$this->loader->add_action( 'wp_dashboard_setup', $plugin_admin, 'es_dashboard_widgets' );

		$this->loader->add_filter( 'manage_edit-product_columns', $plugin_admin, 'es_products_hit_column_array', 20 );
		$this->loader->add_action( 'manage_product_posts_custom_column', $plugin_admin, 'es_products_hit_column_data', 20, 2 );
		$this->loader->add_filter( 'manage_edit-product_sortable_columns', $plugin_admin, 'es_products_hit_column_sortable' );
		$this->loader->add_action( 'pre_get_posts', $plugin_admin, 'es_products_hit_column_orderby' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Extreme_Search_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		$this->loader->add_action( 'wp_ajax_es_update_term', $plugin_public, 'es_update_term' );
		$this->loader->add_action( 'wp_ajax_nopriv_es_update_term', $plugin_public, 'es_update_term' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 *
	 * @since     1.0.0
	 * @return    Extreme_Search_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
