<?php
/**
 * Discord Integration hooks for Gamerz Guild
 */
namespace Codexpert\Gamerz_Guild\App;

use Codexpert\Plugin\Base;
use Codexpert\Gamerz_Guild\Classes\Discord_Integration as Discord_Class;

/**
 * if accessed directly, exit.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @package Gamerz_Guild
 * @subpackage Discord_Hooks
 * @author NH Tanvir <hi@tanvir.io>
 */
class Discord_Hooks extends Base {

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
	 * Initialize the Discord integration hooks
	 */
	public function init() {
		// Get Discord settings
		$webhook_url = get_option( 'gamerz_discord_webhook_url', '' );
		$bot_token = get_option( 'gamerz_discord_bot_token', '' );

		// Only continue if Discord is properly configured
		if ( ! $webhook_url && ! $bot_token ) {
			return;
		}

		$this->setup_hooks();
	}

	/**
	 * Setup hooks
	 */
	public function setup_hooks() {
		// Hook into rank up events
		add_action( 'gamerz_rank_up', [ new Discord_Class(), 'announce_rank_up' ], 10, 3 );

		// Hook into badge awards
		add_action( 'gamerz_badge_awarded', [ new Discord_Class(), 'announce_badge_award' ], 10, 2 );

		// Hook into guild events
		add_action( 'gamerz_guild_created', [ new Discord_Class(), 'announce_guild_creation' ], 10, 2 );
		add_action( 'gamerz_guild_member_added', [ new Discord_Class(), 'announce_guild_join' ], 10, 3 );
		add_action( 'gamerz_badge_manually_awarded', [ new Discord_Class(), 'announce_challenge_completion' ], 10, 3 );

		// Hook for Discord profile fields
		add_action( 'show_user_profile', [ new Discord_Class(), 'add_discord_field' ] );
		add_action( 'edit_user_profile', [ new Discord_Class(), 'add_discord_field' ] );
		add_action( 'personal_options_update', [ new Discord_Class(), 'save_discord_field' ] );
		add_action( 'edit_user_profile_update', [ new Discord_Class(), 'save_discord_field' ] );
	}
}