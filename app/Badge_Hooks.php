<?php
/**
 * Badge System hooks for Gamerz Guild
 */
namespace Codexpert\Gamerz_Guild\App;

use Codexpert\Plugin\Base;
use Codexpert\Gamerz_Guild\Classes\Badge_System;

/**
 * if accessed directly, exit.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @package Gamerz_Guild
 * @subpackage Badge_Hooks
 * @author NH Tanvir <hi@tanvir.io>
 */
class Badge_Hooks extends Base {

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
		$this->action( 'init', 'setup_badge_hooks' );
	}

	/**
	 * Setup badge hooks
	 */
	public function setup_badge_hooks() {
		// Setup all the hooks that were in the Badge_System class
		add_action( 'bbp_new_topic', [ new Badge_System(), 'check_forum_newbie' ], 20, 2 );
		add_action( 'bbp_new_reply', [ new Badge_System(), 'check_forum_newbie' ], 20, 2 );
		add_action( 'friends_add_friendship', [ new Badge_System(), 'check_social_butterfly' ], 20, 2 );
		add_action( 'youzify_activity_liked', [ new Badge_System(), 'check_social_butterfly' ], 20, 2 );
		add_action( 'gamerz_guild_member_added', [ new Badge_System(), 'check_squad_up' ], 20, 3 );
		add_action( 'tribe_events_attendee_created', [ new Badge_System(), 'check_event_enthusiast' ], 20, 2 );
		add_action( 'gamerz_xp_content_submission', [ new Badge_System(), 'check_content_creator' ], 20, 1 );
	}
}