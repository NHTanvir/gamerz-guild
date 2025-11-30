<?php
/**
 * XP System hooks for Gamerz Guild
 */
namespace Codexpert\Gamerz_Guild\App;

use Codexpert\Plugin\Base;
use Codexpert\Gamerz_Guild\Classes\XP_System as XP_Class;

/**
 * if accessed directly, exit.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @package Gamerz_Guild
 * @subpackage XP_Hooks
 * @author NH Tanvir <hi@tanvir.io>
 */
class XP_Hooks extends Base {

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
		// Add XP for various actions - use direct WordPress functions to call XP_System methods
		add_action( 'wp_login', [ new XP_Class(), 'award_daily_login' ], 10, 2 );
		add_action( 'bbp_new_topic', [ new XP_Class(), 'award_new_topic' ], 10, 2 );
		add_action( 'bbp_new_reply', [ new XP_Class(), 'award_new_reply' ], 10, 2 );
		add_action( 'friends_add_friendship', [ new XP_Class(), 'award_new_friend' ], 10, 2 );
		add_action( 'gamerz_guild_created', [ new XP_Class(), 'award_guild_creation' ], 10, 2 );
		add_action( 'gamerz_guild_member_added', [ new XP_Class(), 'award_guild_join' ], 10, 3 );

		// Handle reactions/likes (using Youzify or other plugins)
		if ( class_exists( 'Youzify' ) ) {
			add_action( 'youzify_activity_liked', [ new XP_Class(), 'award_activity_like' ], 10, 2 );
			add_action( 'youzify_comment_liked', [ new XP_Class(), 'award_comment_like' ], 10, 2 );
		}
	}
}