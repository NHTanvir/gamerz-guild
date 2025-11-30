<?php
/**
 * Forum Integration hooks for Gamerz Guild
 */
namespace Codexpert\Gamerz_Guild\App;

use Codexpert\Plugin\Base;
use Codexpert\Gamerz_Guild\Classes\Forum_Integration as Forum_Class;

/**
 * if accessed directly, exit.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @package Gamerz_Guild
 * @subpackage Forum_Hooks
 * @author NH Tanvir <hi@tanvir.io>
 */
class Forum_Hooks extends Base {

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

		$this->init();
	}

	/**
	 * Initialize the forum integration hooks
	 */
	public function init() {
		// Check if bbPress is active
		if ( ! class_exists( 'bbPress' ) ) {
			return;
		}

		$this->setup_hooks();
	}

	/**
	 * Setup hooks
	 */
	public function setup_hooks() {
		// Show rank in forum posts
		add_filter( 'bbp_get_reply_author_display_name', [ new Forum_Class(), 'display_rank_with_username' ], 10, 2 );
		add_filter( 'bbp_get_topic_author_display_name', [ new Forum_Class(), 'display_rank_with_username' ], 10, 2 );

		// Add rank display below avatar
		add_action( 'bbp_theme_before_reply_author_details', [ new Forum_Class(), 'display_rank_details' ] );
		add_action( 'bbp_theme_before_topic_author_details', [ new Forum_Class(), 'display_rank_details' ] );

		// Add XP info to user profiles in forums
		add_action( 'bbp_template_before_user_profile', [ new Forum_Class(), 'add_xp_to_profile' ] );

		// Handle XP for forum activity
		add_action( 'bbp_new_topic', [ new Forum_Class(), 'award_xp_for_new_topic' ], 10, 2 );
		add_action( 'bbp_new_reply', [ new Forum_Class(), 'award_xp_for_new_reply' ], 10, 2 );
	}
}