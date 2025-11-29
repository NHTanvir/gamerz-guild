<?php 
/**
 * All AJAX related functions
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
 * @subpackage AJAX
 * @author Codexpert <hi@tanvir.io>
 */
class AJAX extends Base {

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
		// Handle guild member actions - logged in users only
		$this->action( 'wp_ajax_guild_join', 'handle_guild_join' );
		$this->action( 'wp_ajax_guild_leave', 'handle_guild_leave' );
		$this->action( 'wp_ajax_guild_kick_member', 'handle_guild_kick_member' );
		$this->action( 'wp_ajax_guild_promote_member', 'handle_guild_promote_member' );
		$this->action( 'wp_ajax_guild_demote_member', 'handle_guild_demote_member' );
		$this->action( 'wp_ajax_guild_create', 'handle_guild_create' );
		$this->action( 'wp_ajax_get_guild_members', 'handle_get_guild_members' );

		// Also register nopriv hooks (which will just return access denied)
		$this->action( 'wp_ajax_nopriv_guild_join', 'handle_not_logged_in' );
		$this->action( 'wp_ajax_nopriv_guild_leave', 'handle_not_logged_in' );
		$this->action( 'wp_ajax_nopriv_guild_kick_member', 'handle_not_logged_in' );
		$this->action( 'wp_ajax_nopriv_guild_promote_member', 'handle_not_logged_in' );
		$this->action( 'wp_ajax_nopriv_guild_demote_member', 'handle_not_logged_in' );
		$this->action( 'wp_ajax_nopriv_guild_create', 'handle_not_logged_in' );
		$this->action( 'wp_ajax_nopriv_get_guild_members', 'handle_not_logged_in' );

		// Guild edit actions
		$this->action( 'wp_ajax_get_guild_details', 'handle_get_guild_details' );
		$this->action( 'wp_ajax_update_guild', 'handle_update_guild' );
		$this->action( 'wp_ajax_nopriv_get_guild_details', 'handle_not_logged_in' );
		$this->action( 'wp_ajax_nopriv_update_guild', 'handle_not_logged_in' );
	}

	/**
	 * Handle guild join request (proxy to Guild_Member class)
	 */
	public function handle_guild_join() {
		$guild_member = new \Codexpert\Gamerz_Guild\Classes\Guild_Member();
		$guild_member->handle_join_guild();
	}

	/**
	 * Handle guild leave request (proxy to Guild_Member class)
	 */
	public function handle_guild_leave() {
		$guild_member = new \Codexpert\Gamerz_Guild\Classes\Guild_Member();
		$guild_member->handle_leave_guild();
	}

	/**
	 * Handle member kick (proxy to Guild_Member class)
	 */
	public function handle_guild_kick_member() {
		$guild_member = new \Codexpert\Gamerz_Guild\Classes\Guild_Member();
		$guild_member->handle_kick_member();
	}

	/**
	 * Handle member promotion (proxy to Guild_Member class)
	 */
	public function handle_guild_promote_member() {
		$guild_member = new \Codexpert\Gamerz_Guild\Classes\Guild_Member();
		$guild_member->handle_promote_member();
	}

	/**
	 * Handle member demotion (proxy to Guild_Member class)
	 */
	public function handle_guild_demote_member() {
		$guild_member = new \Codexpert\Gamerz_Guild\Classes\Guild_Member();
		$guild_member->handle_demote_member();
	}

	/**
	 * Handle guild creation (proxy to Guild_Member class)
	 */
	public function handle_guild_create() {
		$guild_member = new \Codexpert\Gamerz_Guild\Classes\Guild_Member();
		$guild_member->handle_guild_creation();
	}

	/**
	 * Handle get guild members (proxy to Guild_Member class)
	 */
	public function handle_get_guild_members() {
		$guild_member = new \Codexpert\Gamerz_Guild\Classes\Guild_Member();
		$guild_member->handle_get_guild_members();
	}

	/**
	 * Handle get guild details (proxy to Guild_Member class)
	 */
	public function handle_get_guild_details() {
		$guild_member = new \Codexpert\Gamerz_Guild\Classes\Guild_Member();
		$guild_member->get_guild_details();
	}

	/**
	 * Handle update guild (proxy to Guild_Member class)
	 */
	public function handle_update_guild() {
		$guild_member = new \Codexpert\Gamerz_Guild\Classes\Guild_Member();
		$guild_member->update_guild();
	}

	/**
	 * Handle requests from non-logged-in users
	 */
	public function handle_not_logged_in() {
		wp_die( __( 'You must be logged in to perform this action.', 'gamerz-guild' ) );
	}

}
