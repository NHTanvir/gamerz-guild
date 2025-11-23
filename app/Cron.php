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
	}

	/**
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
			return; // Pages already created
		}

		// Create Leaderboard Page
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
			// Update the content to ensure it has the required shortcodes
			wp_update_post( array(
				'ID'           => $leaderboard_page_id,
				'post_content' => '[gamerz_leaderboard]<br>[gamerz_xp_progress]',
			) );
		}

		// Create Weekly Challenges Page
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
			// Update the content to ensure it has the required shortcode
			wp_update_post( array(
				'ID'           => $challenges_page_id,
				'post_content' => '[gamerz_weekly_challenges]',
			) );
		}

		// Create My Challenges Page
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
			// Update the content to ensure it has the required shortcode
			wp_update_post( array(
				'ID'           => $my_challenges_page_id,
				'post_content' => '[gamerz_my_challenges]',
			) );
		}

		// Mark that pages have been created to prevent duplicates
		update_option( 'gamerz_guild_pages_created', true );
	}

	/**
	 * Uninstaller. Runs once when the plugin in deactivated.
	 *
	 * @since 1.0
	 */
	public function uninstall() {
		/**
		 * Remove scheduled hooks
		 */
		wp_clear_scheduled_hook( 'tanvir-daily' );
	}
}