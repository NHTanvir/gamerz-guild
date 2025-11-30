<?php
/**
 * Plugin Name: Gamerz Guild
 * Description: gamerz guild
 * Plugin URI: https://tanvir.io
 * Author: NH Tanvir
 * Author URI: https://tanvir.io
 * Version: 0.9
 * Text Domain: gamerz-guild
 * Domain Path: /languages
 */

namespace Codexpert\Gamerz_Guild;
use Codexpert\Plugin\Notice;

/**
 * if accessed directly, exit.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main class for the plugin
 * @package Plugin
 * @author Codexpert <hi@tanvir.io>
 */
final class Plugin {
	
	/**
	 * Plugin instance
	 * 
	 * @access private
	 * 
	 * @var Plugin
	 */
	private static $_instance;

	/**
	 * The constructor method
	 * 
	 * @access private
	 * 
	 * @since 0.9
	 */
	private function __construct() {
		/**
		 * Includes required files
		 */
		$this->include();

		/**
		 * Defines contants
		 */
		$this->define();

		/**
		 * Runs actual hooks
		 */
		$this->hook();
	}

	/**
	 * Includes files
	 *
	 * @access private
	 *
	 * @uses composer
	 * @uses psr-4
	 */
	private function include() {
		require_once( dirname( __FILE__ ) . '/vendor/autoload.php' );
		require_once( dirname( __FILE__ ) . '/includes/main.php' );
	}

	/**
	 * Define variables and constants
	 * 
	 * @access private
	 * 
	 * @uses get_plugin_data
	 * @uses plugin_basename
	 */
	private function define() {

		/**
		 * Define some constants
		 * 
		 * @since 0.9
		 */
		define( 'ITS', __FILE__ );
		define( 'ITS_DIR', dirname( ITS ) );
		define( 'ITS_ASSET', plugins_url( 'assets', ITS ) );
		define( 'ITS_DEBUG', apply_filters( 'gamerz-guild_debug', true ) );

		/**
		 * The plugin data
		 * 
		 * @since 0.9
		 * @var $plugin
		 */
		$this->plugin					= get_plugin_data( ITS );
		$this->plugin['basename']		= plugin_basename( ITS );
		$this->plugin['file']			= ITS;
		$this->plugin['server']			= apply_filters( 'gamerz-guild_server', 'https://tanvir.io/dashboard' );
		$this->plugin['min_php']		= '5.6';
		$this->plugin['min_wp']			= '4.0';
		$this->plugin['icon']			= ITS_ASSET . '/img/icon.png';
		}

	/**
	 * Hooks
	 * 
	 * @access private
	 * 
	 * Executes main plugin features
	 *
	 * To add an action, use $instance->action()
	 * To apply a filter, use $instance->filter()
	 * To register a shortcode, use $instance->register()
	 * To add a hook for logged in users, use $instance->priv()
	 * To add a hook for non-logged in users, use $instance->nopriv()
	 * 
	 * @return void
	 */
	private function hook() {

		if( is_admin() ) :

			/**
			 * Admin facing hooks
			 */
			$admin = new App\Admin( $this->plugin );
			$admin->activate( 'install' );
			$admin->action( 'admin_footer', 'modal' );
			$admin->action( 'plugins_loaded', 'i18n' );
			$admin->action( 'admin_enqueue_scripts', 'enqueue_scripts' );
			$admin->action( 'admin_footer_text', 'footer_text' );

			/**
			 * Settings related hooks
			 */
			$settings = new App\Settings( $this->plugin );
			$settings->action( 'plugins_loaded', 'init_menu' );

			/**
			 * Renders different notices
			 * 
			 * @package Codexpert\Plugin
			 * 
			 * @author Codexpert <hi@tanvir.io>
			 */
			$notice = new Notice( $this->plugin );

		else : // ! is_admin() ?

			/**
			 * Front facing hooks
			 */
			$front = new App\Front( $this->plugin );
			$front->action( 'wp_head', 'head' );
			$front->action( 'wp_footer', 'modal' );
			$front->action( 'wp_enqueue_scripts', 'enqueue_scripts' );

			/**
			 * Shortcode related hooks
			 */
			$shortcode = new App\Shortcode( $this->plugin );
			$shortcode->register( 'my_shortcode', 'my_shortcode' );
			$shortcode->register( 'gamerz_weekly_challenges', 'render_weekly_challenges_shortcode' );
			$shortcode->register( 'gamerz_my_challenges', 'render_my_challenges_shortcode' );
			$shortcode->register( 'gamerz_leaderboard', 'render_leaderboard_shortcode' );
			$shortcode->register( 'gamerz_xp_progress', 'render_xp_progress_shortcode' );
			$shortcode->register( 'gamerz_guild_management', 'render_guild_management_shortcode' );

		endif;

		/**
		 * Cron facing hooks
		 */
		$cron = new App\Cron( $this->plugin );
		$cron->activate( 'install' );
		$cron->deactivate( 'uninstall' );

		/**
		 * Common hooks
		 *
		 * Executes on both the admin area and front area
		 */
		$common = new App\Common( $this->plugin );

		/**
		 * AJAX related hooks
		 */
		$ajax = new App\AJAX( $this->plugin );

		/**
		 * Initialize the new hook classes that replace hooks from includes/classes/
		 */
		new App\Badge_Hooks( $this->plugin );
		new App\Challenges_Hooks( $this->plugin );
		new App\Discord_Hooks( $this->plugin );
		new App\Event_Hooks( $this->plugin );
		new App\Forum_Hooks( $this->plugin );
		new App\Guild_Hooks( $this->plugin );
		new App\Guild_Activity_Hooks( $this->plugin );
		new App\Leaderboard_Hooks( $this->plugin );
		new App\Rank_Hooks( $this->plugin );
		new App\Redemption_Hooks( $this->plugin );
		new App\Visual_Hooks( $this->plugin );
		new App\XP_Hooks( $this->plugin );
	}

	/**
	 * Cloning is forbidden.
	 * 
	 * @access public
	 */
	public function __clone() { }

	/**
	 * Unserializing instances of this class is forbidden.
	 * 
	 * @access public
	 */
	public function __wakeup() { }

	/**
	 * Instantiate the plugin
	 * 
	 * @access public
	 * 
	 * @return $_instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
}

Plugin::instance();