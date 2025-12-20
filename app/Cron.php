<?php
/**
 * All cron related functions
 */
namespace Codexpert\Gamerz_Guild\App;
use Codexpert\Plugin\Base;

/**
 * if accessed directly, exit.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @package Plugin
 * @subpackage Cron
 * @author Codexpert <hi@tanvir.io>
 */
class Cron extends Base {

	public $plugin;
	public $slug;
	public $name;
	public $server;
	public $version;

	/**
	 * Constructor function
	 */
	public function __construct( $plugin ) {
		$this->plugin	= $plugin;
		$this->slug		= $this->plugin['TextDomain'];
		$this->name		= $this->plugin['Name'];
		$this->server	= $this->plugin['server'];
		$this->version	= $this->plugin['Version'];
		
		$this->setup_hooks();
	}

	/**
	 * Setup hooks
	 */
	public function setup_hooks() {
		add_action( 'gamerz_weekly_leaderboard_discord', [ $this, 'send_weekly_leaderboard_to_discord' ] );
		add_action( 'gamerz_cleanup_expired_items', [ $this, 'cleanup_expired_items' ] );
	}

	/**
	 * Send weekly leaderboard to Discord
	 */
	public function send_weekly_leaderboard_to_discord() {
		$webhook_url  = get_option( 'gamerz_discord_webhook_url', '' );
		$bot_token    = get_option( 'gamerz_discord_bot_token', '' );
		
		if ( ! $webhook_url && ! $bot_token ) {
			return;
		}

		if ( class_exists( '\Codexpert\Gamerz_Guild\Classes\Discord_Integration' ) ) {
			$discord = new \Codexpert\Gamerz_Guild\Classes\Discord_Integration();
			$discord->send_leaderboard_to_discord();
		}
	}

	/**
	 * Cleanup expired items (cosmetics, access levels, etc.)
	 */
	public function cleanup_expired_items() {
		// Get all users
		$users = get_users( [ 'fields' => 'ID' ] );
		
		foreach ( $users as $user ) {
			$user_id = $user->ID;
			
			if ( class_exists( '\Codexpert\Gamerz_Guild\Classes\Redemption_System' ) ) {
				$redemption = new \Codexpert\Gamerz_Guild\Classes\Redemption_System();
				$redemption->deactivate_expired_cosmetics( $user_id );
				
				$this->cleanup_expired_access_levels( $user_id );
			}
		}
	}

	/**
	 * Cleanup expired access levels for a user
	 */
	private function cleanup_expired_access_levels( $user_id ) {
		$access_levels = get_user_meta( $user_id, '_gamerz_access_levels', true );
		if ( ! is_array( $access_levels ) ) {
			return;
		}
		
		$updated = false;
		foreach ( $access_levels as $key => $access ) {
			if ( isset( $access['expires_at'] ) && $access['expires_at'] ) {
				$expiry_time = strtotime( $access['expires_at'] );
				if ( $expiry_time < time() && $access['active'] ) {
					$access_levels[ $key ]['active'] = false;
					$updated = true;
				}
			}
		}
		
		if ( $updated ) {
			update_user_meta( $user_id, '_gamerz_access_levels', $access_levels );
		}
	}

	/***
	 * Installer. Runs once when the plugin in activated.
	 *
	 * @since 1.0
	 */
	public function install() {
		/**
		 * Schedule an event to sync help docs
		 */
		if ( ! wp_next_scheduled( 'tanvir-daily' ) ) {
		    wp_schedule_event( time(), 'daily', 'tanvir-daily' );
		}

		/**
		 * Schedule weekly leaderboard announcement to Discord
		 */
		if ( ! wp_next_scheduled( 'gamerz_weekly_leaderboard_discord' ) ) {
			wp_schedule_event( strtotime( 'next monday 10:00:00' ), 'weekly', 'gamerz_weekly_leaderboard_discord' );
		}
		
		/**
		 * Schedule daily cleanup of expired items (cosmetics, access levels, etc.)
		 */
		if ( ! wp_next_scheduled( 'gamerz_cleanup_expired_items' ) ) {
			wp_schedule_event( time(), 'daily', 'gamerz_cleanup_expired_items' );
		}

		/**
		 * Create required pages for the plugin
		 */
		$this->create_required_pages();
	}

	/**
	 * Create required pages for the plugin
	 *
	 * @since 1.0
	 */
	private function create_required_pages() {
		// Check if we've already created the pages
		$pages_created = get_option( 'gamerz_guild_pages_created', false );

		if ( $pages_created ) {
			return;
		}

		$leaderboard_page = get_page_by_path( 'leaderboard', OBJECT, 'page' );
		if ( ! $leaderboard_page ) {
			$leaderboard_page_id = wp_insert_post( array(
				'post_title'     => 'Leaderboard',
				'post_content'   => '[gamerz_leaderboard]<br>[gamerz_xp_progress]',
				'post_status'    => 'publish',
				'post_type'      => 'page',
				'post_name'      => 'leaderboard',
				'comment_status' => 'closed'
			) );
		} else {
			$leaderboard_page_id = $leaderboard_page->ID;
			wp_update_post( array(
				'ID'           => $leaderboard_page_id,
				'post_content' => '[gamerz_leaderboard]<br>[gamerz_xp_progress]',
			) );
		}

		$challenges_page = get_page_by_path( 'weekly-challenges', OBJECT, 'page' );
		if ( ! $challenges_page ) {
			$challenges_page_id = wp_insert_post( array(
				'post_title'     => 'Weekly Challenges',
				'post_content'   => '[gamerz_weekly_challenges]',
				'post_status'    => 'publish',
				'post_type'      => 'page',
				'post_name'      => 'weekly-challenges',
				'comment_status' => 'closed'
			) );
		} else {
			$challenges_page_id = $challenges_page->ID;
			wp_update_post( array(
				'ID'           => $challenges_page_id,
				'post_content' => '[gamerz_weekly_challenges]',
			) );
		}

		$my_challenges_page = get_page_by_path( 'my-challenges', OBJECT, 'page' );
		if ( ! $my_challenges_page ) {
			$my_challenges_page_id = wp_insert_post( array(
				'post_title'     => 'My Challenges',
				'post_content'   => '[gamerz_my_challenges]',
				'post_status'    => 'publish',
				'post_type'      => 'page',
				'post_name'      => 'my-challenges',
				'comment_status' => 'closed'
			) );
		} else {
			$my_challenges_page_id = $my_challenges_page->ID;
			wp_update_post( array(
				'ID'           => $my_challenges_page_id,
				'post_content' => '[gamerz_my_challenges]',
			) );
		}

		update_option( 'gamerz_guild_pages_created', true );
	}

	/***
	 * Uninstaller. Runs once when the plugin in deactivated.
	 *
	 * @since 1.0
	 */
	public function uninstall() {
		/**
		 * Remove scheduled hooks
		 */
		wp_clear_scheduled_hook( 'tanvir-daily' );
		wp_clear_scheduled_hook( 'gamerz_weekly_leaderboard_discord' );
		wp_clear_scheduled_hook( 'gamerz_cleanup_expired_items' );
		wp_clear_scheduled_hook( 'gamerz_reset_weekly_challenges' );
	}
}