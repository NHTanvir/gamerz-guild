<?php
/**
 * Challenges hooks for Gamerz Guild
 */
namespace Codexpert\Gamerz_Guild\App;

use Codexpert\Plugin\Base;
use Codexpert\Gamerz_Guild\Classes\Challenges as Challenges_Class;

/**
 * if accessed directly, exit.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @package Gamerz_Guild
 * @subpackage Challenges_Hooks
 * @author NH Tanvir <hi@tanvir.io>
 */
class Challenges_Hooks extends Base {

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
		// Register custom post type for challenges - this needs to use the base class method
		$this->action( 'init', 'register_challenge_post_type' );

		// Add challenge completion hooks and cron (only if myCRED is active)
		if ( class_exists( 'myCRED' ) ) {
			add_action( 'wp_ajax_complete_challenge', [ new Challenges_Class(), 'handle_challenge_completion' ] );
			add_action( 'wp_ajax_submit_challenge_proof', [ new Challenges_Class(), 'handle_challenge_proof_submission' ] );

			// Cron job to reset weekly challenges
			add_action( 'gamerz_reset_weekly_challenges', [ new Challenges_Class(), 'reset_weekly_challenges' ] );
			if ( ! wp_next_scheduled( 'gamerz_reset_weekly_challenges' ) ) {
				wp_schedule_event( strtotime( 'next monday 00:00:00' ), 'weekly', 'gamerz_reset_weekly_challenges' );
			}
		}
	}

	/**
	 * Register challenge post type
	 */
	public function register_challenge_post_type() {
		$challenges_class = new Challenges_Class();
		$challenges_class->register_challenge_post_type();
	}
}