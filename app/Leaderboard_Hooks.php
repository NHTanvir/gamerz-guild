<?php
/**
 * Leaderboard hooks for Gamerz Guild
 */
namespace Codexpert\Gamerz_Guild\App;

use Codexpert\Plugin\Base;
use Codexpert\Gamerz_Guild\Classes\Leaderboard as Leaderboard_Class;

/**
 * if accessed directly, exit.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @package Gamerz_Guild
 * @subpackage Leaderboard_Hooks
 * @author NH Tanvir <hi@tanvir.io>
 */
class Leaderboard_Hooks extends Base {

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
		// AJAX for updating leaderboards (only if myCRED is active)
		if ( class_exists( 'myCRED' ) ) {
			add_action( 'wp_ajax_update_leaderboard', [ new Leaderboard_Class(), 'update_leaderboard_ajax' ] );
		}
	}
}