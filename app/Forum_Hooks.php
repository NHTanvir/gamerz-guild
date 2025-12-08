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
		
		// Add rank-based forum restrictions
		add_filter( 'bbp_user_can_publish_topics', [ $this, 'restrict_topic_creation_by_rank' ], 10, 2 );
		add_filter( 'bbp_user_can_publish_replies', [ $this, 'restrict_reply_creation_by_rank' ], 10, 2 );
		add_action( 'bbp_template_redirect', [ $this, 'restrict_forum_access_by_rank' ] );
	}
	
	/**
	 * Restrict topic creation based on user rank
	 */
	public function restrict_topic_creation_by_rank( $can_publish, $user_id ) {
		// If user can already publish (admin/moderator), allow
		if ( $can_publish ) {
			return $can_publish;
		}
		
		// Check if bbPress functions exist
		if ( ! function_exists( 'bbp_is_forum_archive' ) ) {
			return $can_publish;
		}
		
		// For users with low rank, check their privileges
		$rank_system = new \Codexpert\Gamerz_Guild\Classes\Rank_System();
		
		// Check if user has create_topics privilege
		if ( ! $rank_system->user_has_privilege( $user_id, 'create_topics' ) ) {
			return false;
		}
		
		// Check if user has post_forums privilege
		if ( ! $rank_system->user_has_privilege( $user_id, 'post_forums' ) ) {
			return false;
		}
		
		return true; // Allow if they have the proper rank privileges
	}
	
	/**
	 * Restrict reply creation based on user rank
	 */
	public function restrict_reply_creation_by_rank( $can_publish, $user_id ) {
		// If user can already publish (admin/moderator), allow
		if ( $can_publish ) {
			return $can_publish;
		}
		
		// Check if bbPress functions exist
		if ( ! function_exists( 'bbp_is_forum_archive' ) ) {
			return $can_publish;
		}
		
		// For users with low rank, check their privileges
		$rank_system = new \Codexpert\Gamerz_Guild\Classes\Rank_System();
		
		// Check if user has post_forums privilege
		if ( ! $rank_system->user_has_privilege( $user_id, 'post_forums' ) ) {
			return false;
		}
		
		return true; // Allow if they have the proper rank privileges
	}
	
	/**
	 * Restrict forum access based on rank
	 */
	public function restrict_forum_access_by_rank() {
		$user_id = get_current_user_id();
		$rank_system = new \Codexpert\Gamerz_Guild\Classes\Rank_System();
		
		// Check if user is trying to create a new topic (this covers the new topic form)
		$current_url = $_SERVER['REQUEST_URI'] ?? '';
		if ( strpos( $current_url, 'bbp_reply_to' ) !== false || strpos( $_POST['action'] ?? '', 'bbp-topic' ) !== false || ( isset( $_GET['action'] ) && $_GET['action'] === 'new-topic' ) ) {
			if ( ! $rank_system->user_has_privilege( $user_id, 'create_topics' ) ) {
				bbp_add_error( 'rank_restricted_topic', __( 'You need to reach Scrub Scout rank (100 XP) to create new topics.', 'gamerz-guild' ) );
			}
		}
		
		// Only check if on forum pages
		if ( ! bbp_is_single_forum() && ! bbp_is_single_topic() && ! bbp_is_forum_archive() && ! bbp_is_single_view() ) {
			return;
		}
		
		// Check if user has basic forum access privileges
		if ( ! $rank_system->user_has_privilege( $user_id, 'view_forums' ) ) {
			// Redirect or show error
			if ( bbp_is_single_forum() || bbp_is_single_topic() ) {
				bbp_add_error( 'rank_restricted', __( 'Forum access is restricted. You need to earn more XP to access forums.', 'gamerz-guild' ) );
			}
		}
		
		// Check if user has post privileges when trying to post
		if ( ( bbp_is_single_forum() || bbp_is_single_topic() ) && ! empty( $_POST ) ) {
			if ( ! $rank_system->user_has_privilege( $user_id, 'post_forums' ) ) {
				bbp_add_error( 'rank_restricted_post', __( 'You need to reach Scrub Recruit rank (50 XP) to post in forums.', 'gamerz-guild' ) );
			}
			
			if ( isset( $_POST['bbp_topic_content'] ) && ! $rank_system->user_has_privilege( $user_id, 'create_topics' ) ) {
				bbp_add_error( 'rank_restricted_topic', __( 'You need to reach Scrub Scout rank (100 XP) to create new topics.', 'gamerz-guild' ) );
			}
		}
	}
}