<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @package    Wp_Menu_Caching
 * @subpackage Wp_Menu_Caching/includes
 */

class Wp_Menu_Caching {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Wp_Menu_Caching_Loader $loader Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $plugin_name The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $version The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->version     = defined( 'WP_MENU_CACHING_VERSION' ) ? WP_MENU_CACHING_VERSION : '1.0.0';
		$this->plugin_name = 'menu-caching';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Wp_Menu_Caching_Loader. Orchestrates the hooks of the plugin.
	 * - Wp_Menu_Caching_i18n. Defines internationalization functionality.
	 * - Wp_Menu_Caching_Admin. Defines all hooks for the admin area.
	 * - Wp_Menu_Caching_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-menu-caching-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-menu-caching-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-menu-caching-admin.php';

		$this->loader = new Wp_Menu_Caching_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Wp_Menu_Caching_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Wp_Menu_Caching_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
	}

	/**
	 * Register all hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Wp_Menu_Caching_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_menu', $plugin_admin, 'dc_menu_caching_create_menu' );

		//Plugin actions on plugin list
		$this->loader->add_filter( 'plugin_action_links', $plugin_admin, 'dc_action_links', 10, 2 );

		$this->loader->add_filter( 'wp_nav_menu', $plugin_admin, 'dc_save_menu_html', PHP_INT_MAX, 2 );
		$this->loader->add_filter( 'pre_wp_nav_menu', $plugin_admin, 'dc_show_cached_menu_html', PHP_INT_MAX, 2 );
		$this->loader->add_action( 'wp_update_nav_menu', $plugin_admin, 'dc_purge_updated_menu_transient', PHP_INT_MAX );
		$this->loader->add_action( 'after_rocket_clean_domain', $plugin_admin, 'dc_purge_all_menu_html_transients' );
		$this->loader->add_action( 'wp_ajax_dc_menu_caching_purge_all', $plugin_admin, 'dc_purge_all_menus_settings_button' );
		$this->loader->add_action( 'wp_ajax_dc_save_nocache_menus', $plugin_admin, 'dc_save_nocache_menus' );

		// enqueue styles-scripts
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
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
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @return    string    The name of the plugin.
	 * @since     1.0.0
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @return    Wp_Menu_Caching_Loader    Orchestrates the hooks of the plugin.
	 * @since     1.0.0
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @return    string    The version number of the plugin.
	 * @since     1.0.0
	 */
	public function get_version() {
		return $this->version;
	}
}
