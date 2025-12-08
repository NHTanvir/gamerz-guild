<?php
/**
 * Rank System hooks for Gamerz Guild
 */
namespace Codexpert\Gamerz_Guild\App;

use Codexpert\Plugin\Base;
use Codexpert\Gamerz_Guild\Classes\Rank_System as Rank_System_Class;

/**
 * if accessed directly, exit.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @package Gamerz_Guild
 * @subpackage Rank_Hooks
 * @author NH Tanvir <hi@tanvir.io>
 */
class Rank_Hooks extends Base {

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
		add_action( 'mycred_post_balance_update', [ new Rank_System_Class(), 'check_rank_progression' ], 10, 3 );
		
		// Add avatar upload restriction based on rank
		add_filter( 'bp_core_pre_avatar_upload', [ $this, 'restrict_avatar_upload_by_rank' ], 10, 1 );
		add_action( 'wp_ajax_bp_upload_avatar', [ $this, 'check_avatar_upload_ajax' ], 5 );
		add_action( 'bp_member_header_meta', [ $this, 'show_rank_restrictions_notice' ] );
		
		// Additional hooks for robust avatar restriction
		add_action( 'admin_init', [ $this, 'restrict_admin_avatar_upload' ] );
		add_action( 'template_redirect', [ $this, 'restrict_avatar_page_access' ] );
		
		// Restrict WordPress native profile avatar changes if applicable
		add_action( 'personal_options_update', [ $this, 'restrict_profile_avatar_update' ] );
		add_action( 'edit_user_profile_update', [ $this, 'restrict_profile_avatar_update' ] );
	}
	
	/**
	 * Restrict access to avatar change page based on rank
	 */
	public function restrict_avatar_page_access() {
		// Only check if BuddyPress is active
		if ( ! function_exists( 'bp_is_current_component' ) || ! function_exists( 'bp_is_current_action' ) ) {
			return;
		}
		
		// Check if user is on the BuddyPress change avatar page (multiple possible locations)
		$is_avatar_page = false;
		
		// Check different possible avatar change page routes
		if ( (bp_is_current_component( 'xprofile' ) || bp_is_current_component( 'profile' )) && bp_is_current_action( 'change-avatar' ) ) {
			$is_avatar_page = true;
		}
		
		// Also check for members component with change-avatar action
		if ( bp_is_current_component( 'members' ) && bp_is_current_action( 'change-avatar' ) ) {
			$is_avatar_page = true;
		}
		
		// Check if on specific avatar upload pages/URLs
		$current_url = $_SERVER['REQUEST_URI'] ?? '';
		if ( strpos( $current_url, 'change-avatar' ) !== false || strpos( $current_url, '/avatar/upload' ) !== false ) {
			$is_avatar_page = true;
		}
		
		// Check for URL parameter based access like ?tab=change-avatar
		if ( isset( $_GET['tab'] ) && $_GET['tab'] === 'change-avatar' ) {
			$is_avatar_page = true;
		}
		
		if ( $is_avatar_page ) {
			$user_id = get_current_user_id();
			$rank_system = new Rank_System_Class();
			
			// Check if user has custom_avatar privilege
			if ( ! $rank_system->user_has_privilege( $user_id, 'custom_avatar' ) ) {
				// Redirect to main profile page with notice
				$profile_url = bp_core_get_user_domain( $user_id );
				bp_core_add_message( __( 'Avatar upload is restricted to users with Scrub Recruit rank (50 XP) or higher. Keep earning XP to unlock this feature!', 'gamerz-guild' ), 'error' );
				wp_safe_redirect( $profile_url );
				exit;
			}
		}
	}
	
	/**
	 * Restrict avatar upload based on user rank
	 */
	public function restrict_avatar_upload_by_rank( $args ) {
		// Only apply restriction if BuddyPress is active
		if ( ! function_exists( 'bp_is_active' ) ) {
			return $args;
		}
		
		$user_id = get_current_user_id();
		$rank_system = new Rank_System_Class();
		
		// Check if user has custom_avatar privilege
		if ( ! $rank_system->user_has_privilege( $user_id, 'custom_avatar' ) ) {
			// Prevent avatar upload by showing error
			if ( isset( $_POST['action'] ) && ( $_POST['action'] === 'bp_avatar_upload' || $_POST['action'] === 'bp_cover_image_upload' ) ) {
				wp_die( 
					__( 'Avatar upload is restricted to users with Scrub Recruit rank (50 XP) or higher. Keep earning XP to unlock this feature!', 'gamerz-guild' ),
					__( 'Rank Restriction', 'gamerz-guild' ),
					array( 'response' => 403 )
				);
			}
		}
		
		return $args;
	}
	
	/**
	 * Check avatar upload in AJAX context
	 */
	public function check_avatar_upload_ajax() {
		$user_id = get_current_user_id();
		$rank_system = new Rank_System_Class();
		
		// Check if user has custom_avatar privilege
		if ( ! $rank_system->user_has_privilege( $user_id, 'custom_avatar' ) ) {
			wp_send_json_error( 
				array( 
					'message' => __( 'Avatar upload is restricted to users with Scrub Recruit rank (50 XP) or higher. Keep earning XP to unlock this feature!', 'gamerz-guild' ) 
				) 
			);
		}
	}
	
	/**
	 * Restrict avatar upload in admin area if applicable
	 */
	public function restrict_admin_avatar_upload() {
		if ( isset( $_POST['action'] ) && $_POST['action'] === 'bp_avatar_upload' && ! is_admin() ) {
			$user_id = get_current_user_id();
			$rank_system = new Rank_System_Class();
			
			if ( ! $rank_system->user_has_privilege( $user_id, 'custom_avatar' ) ) {
				wp_die( 
					__( 'Avatar upload is restricted to users with Scrub Recruit rank (50 XP) or higher. Keep earning XP to unlock this feature!', 'gamerz-guild' ),
					__( 'Rank Restriction', 'gamerz-guild' ),
					array( 'response' => 403 )
				);
			}
		}
	}
	
	/**
	 * Restrict profile avatar updates
	 */
	public function restrict_profile_avatar_update( $user_id ) {
		// Only check if it's the current user updating their own profile
		if ( get_current_user_id() != $user_id ) {
			return;
		}
		
		$rank_system = new Rank_System_Class();
		
		// Check if user has custom_avatar privilege
		if ( ! $rank_system->user_has_privilege( $user_id, 'custom_avatar' ) ) {
			// Check if avatar-related fields are being updated
			if ( isset( $_POST['avatar'] ) || ! empty( $_FILES['avatar'] ) ) {
				// Add error message
				global $wp_settings_errors;
				if ( ! $wp_settings_errors ) {
					$wp_settings_errors = array();
				}
				$wp_settings_errors[] = array(
					'code' => 'avatar_restricted',
					'message' => __( 'Avatar upload is restricted to users with Scrub Recruit rank (50 XP) or higher. Keep earning XP to unlock this feature!', 'gamerz-guild' ),
					'type' => 'error'
				);
				
				// Redirect back with error
				$redirect_url = wp_get_referer();
				if ( $redirect_url ) {
					wp_safe_redirect( add_query_arg( 'updated', 'false', $redirect_url ) );
					exit;
				}
			}
		}
	}
	
	/**
	 * Show rank restrictions notice on profile
	 */
	public function show_rank_restrictions_notice() {
		if ( ! is_user_logged_in() ) {
			return;
		}
		
		$user_id = get_current_user_id();
		$rank_system = new Rank_System_Class();
		
		// Check if avatar upload is restricted for this user
		if ( ! $rank_system->user_has_privilege( $user_id, 'custom_avatar' ) ) {
			echo '<div class="gamerz-rank-restriction-notice" style="margin: 10px 0; padding: 10px; background: #fff8e1; border: 1px solid #ffd54f; border-radius: 4px; color: #5d4037;">';
			echo '<strong>' . __( 'Rank Restriction:', 'gamerz-guild' ) . '</strong> ';
			echo __( 'Custom avatar upload is available at Scrub Recruit rank (50 XP earned).', 'gamerz-guild' );
			echo '</div>';
		}
	}
}