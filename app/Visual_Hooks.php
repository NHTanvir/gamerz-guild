<?php
/**
 * Visual Enhancements hooks for Gamerz Guild
 */
namespace Codexpert\Gamerz_Guild\App;

use Codexpert\Plugin\Base;
use Codexpert\Gamerz_Guild\Classes\Visual_Enhancements as Visual_Class;

/**
 * if accessed directly, exit.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @package Gamerz_Guild
 * @subpackage Visual_Hooks
 * @author NH Tanvir <hi@tanvir.io>
 */
class Visual_Hooks extends Base {

	public $plugin;
	public $slug;
	public $name;
	public $version;

	/**
	 * Constructor function
	 */
	public function __construct( $plugin ) {
		$this->plugin	= $plugin;
		$this->slug		= $this->plugin['TextDomain'];
		$this->name		= $this->plugin['Name'];
		$this->version	= $this->plugin['Version'];

		$this->setup_hooks();
	}

	/**
	 * Setup hooks
	 */
	public function setup_hooks() {
		// For enqueuing scripts/styles, we'll use the base class methods that call our local methods
		$this->action( 'wp_enqueue_scripts', 'enqueue_styles' );
		$this->action( 'wp_enqueue_scripts', 'enqueue_scripts' );
		$this->action( 'admin_enqueue_scripts', 'enqueue_admin_styles' );

		// For other hooks, use direct add_action/add_filter to call methods from Visual_Enhancements class
		add_action( 'bp_before_member_header_meta', [ new Visual_Class(), 'add_profile_xp_display' ] );

		// Add visual elements to Youzify profiles if available
		if ( class_exists( 'Youzify' ) ) {
			add_action( 'youzify_profile_header_top', [ new Visual_Class(), 'add_youzify_profile_xp_display' ] );
		}

		// Add XP bar to site header
		add_action( 'wp_head', [ new Visual_Class(), 'add_xp_bar_to_header' ] );

		// Add animations for level ups and achievements
		add_action( 'wp_footer', [ new Visual_Class(), 'add_achievement_animations' ] );

		// Filter avatars to add rank indicators if available
		add_filter( 'get_avatar', [ new Visual_Class(), 'add_rank_avatar_indicator' ], 10, 6 );
	}

	/**
	 * Enqueue styles
	 */
	public function enqueue_styles() {
		$visual = new Visual_Class();
		$visual->enqueue_styles();
	}

	/**
	 * Enqueue scripts
	 */
	public function enqueue_scripts() {
		$visual = new Visual_Class();
		$visual->enqueue_scripts();
	}

	/**
	 * Enqueue admin styles
	 */
	public function enqueue_admin_styles( $hook ) {
		$visual = new Visual_Class();
		$visual->enqueue_admin_styles( $hook );
	}
}