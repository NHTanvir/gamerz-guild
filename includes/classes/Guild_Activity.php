<?php
/**
 * Guild_Activity class
 */
namespace Codexpert\Gamerz_Guild\Classes;

/**
 * if accessed directly, exit.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @package Gamerz_Guild
 * @subpackage Guild_Activity
 * @author NH Tanvir <hi@tanvir.io>
 */
class Guild_Activity {

	/**
	 * Constructor function
	 */
	public function __construct() {
		$this->init();
	}

	/**
	 * Initialize the guild activity system
	 */
	public function init() {
		// Hooks have been moved to app/Guild_Activity_Hooks.php
	}

	/**
	 * Log guild creation activity
	 */
	public function activity_guild_created( $guild_id, $args ) {
		$activity_data = [
			'type'     => 'guild_created',
			'guild_id' => $guild_id,
			'user_id'  => $args['creator_id'],
			'title'    => sprintf( __( '%s created a new guild: %s', 'gamerz-guild' ), 
				$this->get_user_display_name( $args['creator_id'] ), 
				get_the_title( $guild_id )
			),
			'content'  => sprintf( __( 'Guild "%s" was created by %s', 'gamerz-guild' ), 
				get_the_title( $guild_id ), 
				$this->get_user_display_name( $args['creator_id'] )
			),
			'timestamp' => current_time( 'mysql' ),
		];

		$this->add_activity( $activity_data );
	}

	/**
	 * Log member joining activity
	 */
	public function activity_member_joined( $guild_id, $user_id, $role ) {
		$activity_data = [
			'type'     => 'member_joined',
			'guild_id' => $guild_id,
			'user_id'  => $user_id,
			'title'    => sprintf( __( '%s joined the guild', 'gamerz-guild' ), 
				$this->get_user_display_name( $user_id )
			),
			'content'  => sprintf( __( '%s has joined the guild as a %s', 'gamerz-guild' ), 
				$this->get_user_display_name( $user_id ),
				ucfirst( $role )
			),
			'timestamp'=> current_time( 'mysql' ),
		];

		$this->add_activity( $activity_data );
		
		$member = new Guild_Member();
		$member->set_join_date( $guild_id, $user_id );
	}

	/**
	 * Log member leaving activity
	 */
	public function activity_member_left( $guild_id, $user_id ) {
		$activity_data = [
			'type'     => 'member_left',
			'guild_id' => $guild_id,
			'user_id'  => $user_id,
			'title'    => sprintf( __( '%s left the guild', 'gamerz-guild' ), 
				$this->get_user_display_name( $user_id )
			),
			'content'  => sprintf( __( '%s has left the guild', 'gamerz-guild' ), 
				$this->get_user_display_name( $user_id )
			),
			'timestamp'=> current_time( 'mysql' ),
		];

		$this->add_activity( $activity_data );
	}

	/**
	 * Log member promotion activity
	 */
	public function activity_member_promoted( $guild_id, $user_id, $new_role ) {
		$member       = new Guild_Member();
		$role_display = $member->get_role_display_name( $new_role );

		$activity_data = [
			'type'     => 'member_promoted',
			'guild_id' => $guild_id,
			'user_id'  => $user_id,
			'title'    => sprintf( __( '%s was promoted in the guild', 'gamerz-guild' ), 
				$this->get_user_display_name( $user_id )
			),
			'content'  => sprintf( __( '%s was promoted to %s', 'gamerz-guild' ), 
				$this->get_user_display_name( $user_id ),
				$role_display
			),
			'timestamp'=> current_time( 'mysql' ),
		];

		$this->add_activity( $activity_data );
	}

	/**
	 * Log member demotion activity
	 */
	public function activity_member_demoted( $guild_id, $user_id, $new_role ) {
		$member       = new Guild_Member();
		$role_display = $member->get_role_display_name( $new_role );

		$activity_data = [
			'type'     => 'member_demoted',
			'guild_id' => $guild_id,
			'user_id'  => $user_id,
			'title'    => sprintf( __( '%s was demoted in the guild', 'gamerz-guild' ), 
				$this->get_user_display_name( $user_id )
			),
			'content'  => sprintf( __( '%s was demoted to %s', 'gamerz-guild' ), 
				$this->get_user_display_name( $user_id ),
				$role_display
			),
			'timestamp'=> current_time( 'mysql' ),
		];

		$this->add_activity( $activity_data );
	}

	/**
	 * Add activity to guild
	 */
	public function add_activity( $activity_data ) {
		// Get existing activities
		$activities = get_post_meta( $activity_data['guild_id'], '_guild_activities', true );
		if ( ! is_array( $activities ) ) {
			$activities = [];
		}

		// Add new activity
		$activities[] = $activity_data;

		// Limit to last 50 activities to prevent DB bloat
		if ( count( $activities ) > 50 ) {
			$activities = array_slice( $activities, -50 );
		}

		// Save activities
		update_post_meta( $activity_data['guild_id'], '_guild_activities', $activities );
	}

	/**
	 * Get guild activities
	 */
	public function get_activities( $guild_id, $limit = 20 ) {
		$activities = get_post_meta( $guild_id, '_guild_activities', true );
		if ( ! is_array( $activities ) ) {
			return [];
		}

		// Reverse to show newest first
		$activities = array_reverse( $activities );

		// Limit results
		return array_slice( $activities, 0, $limit );
	}

	/**
	 * Get user display name
	 */
	private function get_user_display_name( $user_id ) {
		$user = get_user_by( 'ID', $user_id );
		return $user ? $user->display_name : 'Unknown User';
	}

	/**
	 * Format activity timestamp
	 */
	public function format_timestamp( $timestamp ) {
		return human_time_diff( strtotime( $timestamp ), current_time( 'timestamp' ) ) . ' ago';
	}

	/**
	 * Get activity icon based on type
	 */
	public function get_activity_icon( $type ) {
		$icons = [
			'guild_created' => 'dashicons dashicons-groups',
			'member_joined' => 'dashicons dashicons-plus-alt',
			'member_left' => 'dashicons dashicons-minus',
			'member_promoted' => 'dashicons dashicons-arrow-up-alt',
			'member_demoted' => 'dashicons dashicons-arrow-down-alt',
		];

		return isset( $icons[ $type ] ) ? $icons[ $type ] : 'dashicons dashicons-info';
	}
}