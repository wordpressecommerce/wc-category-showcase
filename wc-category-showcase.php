<?php
/**
 * Plugin Name: WooCommerce Category Showcase
 * Plugin URI:  https://pluginever.com/wc-category-showcase
 * Description: WooCommerce extension to showcase categories in a nice slider blocks
 * Version:     1.0.8
 * Author:      PluginEver
 * Author URI:  http://pluginever.com
 * License:     GPLv2+
 * Text Domain: wc-category-showcase
 * Domain Path: /languages
 * WC requires at least: 3.0.0
 * WC tested up to: 3.6.2
 */

/**
 * Copyright (c) 2017 PluginEver (email : support@pluginever.com)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2 or, at
 * your discretion, any later version, as published by the Free
 * Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

// don't call the file directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main initiation class
 *
 * @since 1.0.0
 */
class WC_Category_Showcase {

	/**
	 * Add-on Version
	 *
	 * @since 1.0.0
	 * @var  string
	 */
	public $version = '1.0.8';

	/**
	 * The single instance of the class.
	 *
	 * @var WC_Category_Showcase
	 * @since 1.0.8
	 */
	protected static $instance = null;


	/**
	 * Constructor for the class
	 *
	 * Sets up all the appropriate hooks and actions
	 *
	 * @since 1.0.0
	 *

	 */
	public function __construct() {
		// Localize our plugin
		add_action( 'init', [ $this, 'localization_setup' ] );

		// on activate plugin register hook
		register_activation_hook( __FILE__, array( $this, 'install' ) );

		// on deactivate plugin register hook
		register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );

		// Define constants
		$this->define_constants();

		// Include required files
		$this->includes();

		// Initialize the action hooks
		$this->init_actions();

		// init_plugin classes
		$this->init_plugin();

		// Loaded action
		do_action( 'wc_category_showcase' );
	}

	/**
	 * Initialize plugin for localization
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function localization_setup() {
		$locale = apply_filters( 'plugin_locale', get_locale(), 'wc_category_showcase' );
		load_textdomain( 'wc-category-showcase', WP_LANG_DIR . '/wc-category-showcase/wc-category-showcase-' . $locale . '.mo' );
		load_plugin_textdomain( 'wc-category-showcase', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * Executes during plugin activation
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	function install() {
		//save install date
		if ( false == get_option( 'wccs_install_date' ) ) {
			update_option( 'wccs_install_date', current_time( 'timestamp' ) );
		}


	}

	/**
	 * Executes during plugin deactivation
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	function deactivate() {

	}

	/**
	 * Define constants
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	private function define_constants() {
		define( 'PLVR_WCCS_VERSION', $this->version );
		define( 'PLVR_WCCS_FILE', __FILE__ );
		define( 'PLVR_WCCS_PATH', dirname( PLVR_WCCS_FILE ) );
		define( 'PLVR_WCCS_INCLUDES', PLVR_WCCS_PATH . '/includes' );
		define( 'PLVR_WCCS_ADMIN_PATH', PLVR_WCCS_PATH . '/admin' );
		define( 'PLVR_WCCS_ADMIN_ASSETS', plugins_url( 'admin/assets', PLVR_WCCS_ADMIN_PATH ) );
		define( 'PLVR_WCCS_URL', plugins_url( '', PLVR_WCCS_FILE ) );
		define( 'PLVR_WCCS_ASSETS', PLVR_WCCS_URL . '/assets' );
		define( 'PLVR_WCCS_VIEWS', PLVR_WCCS_PATH . '/views' );
		define( 'PLVR_WCCS_TEMPLATES_DIR', PLVR_WCCS_PATH . '/templates' );
	}

	/**
	 * Include required files
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	private function includes() {
		require PLVR_WCCS_INCLUDES . '/functions.php';
		require PLVR_WCCS_INCLUDES . '/custom-cp.php';
		require PLVR_WCCS_INCLUDES . '/class-shortcode.php';
		require PLVR_WCCS_INCLUDES . '/metabox/class-metabox.php';
		require PLVR_WCCS_ADMIN_PATH . '/class-admin.php';
		require PLVR_WCCS_ADMIN_PATH . '/class-metabox.php';
		require PLVR_WCCS_INCLUDES . '/class-insights.php';
		require PLVR_WCCS_INCLUDES . '/class-tracker.php';
		if ( is_admin() && ! $this->is_pro_installed() ) {
			require PLVR_WCCS_INCLUDES . '/class-promotion.php';
		}
	}

	/**
	 * Do plugin upgrades
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	function plugin_upgrades() {

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		require_once PLVR_WCCS_INCLUDES . '/class-upgrades.php';

		$upgrader = new WCCS_Upgrades();

		if ( $upgrader->needs_update() ) {
			$upgrader->perform_updates();
		}
	}

	/**
	 * Determines if the pro version installed.
	 *
	 * @return bool
	 * @since 1.0.0
	 *
	 */
	public static function is_pro_installed() {
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		return is_plugin_active( 'wc-category-showcase-pro/wc-category-showcase-pro.php' ) == true;
	}

	/**
	 * Init Hooks
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	private function init_actions() {
		add_action( 'wp_enqueue_scripts', array( $this, 'load_assets' ) );
		add_action( 'admin_init', array( $this, 'plugin_upgrades' ) );
		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'plugin_action_links' ) );

	}

	/**
	 * Instantiate classes
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	private function init_plugin() {
		new \Pluginever\WCCS\Shortcode();
		new \Pluginever\WCCCS\Metabox();
		new \Pluginever\WCCS\Tracker();
	}

	/**
	 * Add all the assets required by the plugin
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	function load_assets() {
		wp_register_style( 'wc-category-showcase', PLVR_WCCS_ASSETS . "/css/wc-category-showcase.css", [], date( 'i' ) );
		wp_register_script( 'wc-category-showcase', PLVR_WCCS_ASSETS . "/js/bundle.min.js", [ 'jquery' ], date( 'i' ), true );
		wp_localize_script( 'wc-category-showcase', 'jsobject', [ 'ajaxurl' => admin_url( 'admin-ajax.php' ) ] );
		wp_enqueue_style( 'wc-category-showcase' );
		wp_enqueue_script( 'wc-category-showcase' );
	}


	/**
	 * @param $links
	 *
	 * @return array
	 */
	public function plugin_action_links( $links ) {
		$doc_link     = 'https://www.pluginever.com/docs/woocommerce-category-showcase/';
		$action_links = [];
		if ( ! self::is_pro_installed() ) {
			$action_links['Upgrade'] = '<a target="_blank" href="https://www.pluginever.com/plugins/woocommerce-category-showcase-pro/" title="' . esc_attr( __( 'Upgrade To Pro', 'wc-category-showcase' ) ) . '" style="color:red;font-weight:bold;">' . __( 'Upgrade To Pro', 'wc-category-showcase' ) . '</a>';
		}
		$action_links['Documentation'] = '<a target="_blank" href="' . $doc_link . '" title="' . esc_attr( __( 'View Plugin\'s Documentation', 'wc-category-showcase' ) ) . '">' . __( 'Documentation', 'wc-category-showcase' ) . '</a>';


		return array_merge( $action_links, $links );
	}

	/**
	 * Returns the plugin loader main instance.
	 *
	 * @return \WC_Category_Showcase
	 * @since 1.0.0
	 */
	public static function instance() {

		if ( null === self::$instance ) {

			self::$instance = new self();
		}

		return self::$instance;
	}

}

/**
 * Fire of the plugin
 * since 1.0.0
 * @return object
 */
function wc_category_showcase() {
	return WC_Category_Showcase::instance();
}

wc_category_showcase();
