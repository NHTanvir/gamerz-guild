<?php
/**
 * Guild_Member class
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
 * @subpackage Guild_Member
 * @author NH Tanvir <hi@tanvir.io>
 */
class Guild_Member {

	public $meta_key_prefix = '_gamerz_guild_';

	/**
	 * Constructor function
	 */
	public function __construct() {
		$this->init();
	}

	/**
	 * Initialize the guild member system
	 */
	public function init() {
		// Hooks are now handled in the AJAX class according to the plugin framework
	}

	/**
	 * Handle guild join request
	 */
	public function handle_join_guild() {
		if ( ! wp_verify_nonce( $_POST['nonce'], 'guild_join_nonce' ) ) {
			wp_die( __( 'Security check failed', 'gamerz-guild' ) );
		}

		if ( ! is_user_logged_in() ) {
			wp_die( __( 'You must be logged in to join a guild', 'gamerz-guild' ) );
		}

		$guild_id = intval( $_POST['guild_id'] );
		$user_id  = get_current_user_id();
		$guild    = new Guild();
		
		$user_guilds = $guild->get_user_guilds( $user_id );
		if ( ! empty( $user_guilds ) ) {
			wp_die( __( 'You are already in a guild', 'gamerz-guild' ) );
		}

		$result = $guild->add_member( $guild_id, $user_id );

		if ( $result === true ) {

			wp_send_json_success( [
				'message'  => __( 'Successfully joined the guild', 'gamerz-guild' ),
				'guild_id' => $guild_id
			] );
		} else if ( is_wp_error( $result ) ) {
			wp_send_json_error( [ 'message' => $result->get_error_message() ] );
		} else {
			wp_send_json_error( [ 'message' => __( 'Failed to join guild', 'gamerz-guild' ) ] );
		}
	}

	/**
	 * Handle guild leave request
	 */
	public function handle_leave_guild() {
		if ( ! wp_verify_nonce( $_POST['nonce'], 'guild_leave_nonce' ) ) {
			wp_die( __( 'Security check failed', 'gamerz-guild' ) );
		}

		if ( ! is_user_logged_in() ) {
			wp_die( __( 'You must be logged in to leave a guild', 'gamerz-guild' ) );
		}

		$guild_id = intval( $_POST['guild_id'] );
		$user_id  = get_current_user_id();
		$guild    = new Guild();

		if ( ! $guild->is_member( $guild_id, $user_id ) ) {
			wp_die( __( 'You are not a member of this guild', 'gamerz-guild' ) );
		}

		if ( $guild->get_user_role( $guild_id, $user_id ) === 'leader' ) {
			$members = $guild->get_members( $guild_id );
			if ( count( $members ) > 1 ) {
				wp_die( __( 'You must transfer leadership before leaving', 'gamerz-guild' ) );
			}
		}

		$result = $guild->remove_member( $guild_id, $user_id );

		if ( $result === true ) {
			wp_send_json_success( [
				'message'  => __( 'Successfully left the guild', 'gamerz-guild' ),
				'guild_id' => $guild_id
			] );
		} else {
			wp_send_json_error( [ 'message' => __( 'Failed to leave guild', 'gamerz-guild' ) ] );
		}
	}

	/**
	 * Handle member kick
	 */
	public function handle_kick_member() {
		if ( ! wp_verify_nonce( $_POST['nonce'], 'guild_kick_nonce' ) ) {
			wp_die( __( 'Security check failed', 'gamerz-guild' ) );
		}

		if ( ! is_user_logged_in() ) {
			wp_die( __( 'You must be logged in to manage guild members', 'gamerz-guild' ) );
		}

		$guild_id        = intval( $_POST['guild_id'] );
		$member_id       = intval( $_POST['member_id'] );
		$current_user_id = get_current_user_id();
		$guild           = new Guild();

		$user_role = $guild->get_user_role( $guild_id, $current_user_id );
		if ( $user_role !== 'leader' && $user_role !== 'officer' ) {
			wp_die( __( 'You do not have permission to kick members', 'gamerz-guild' ) );
		}

		$member_role = $guild->get_user_role( $guild_id, $member_id );
		if ( $member_role === 'leader' ) {
			wp_die( __( 'You cannot kick the guild leader', 'gamerz-guild' ) );
		}

		if ( $current_user_id === $member_id ) {
			wp_die( __( 'You cannot kick yourself', 'gamerz-guild' ) );
		}

		$result = $guild->remove_member( $guild_id, $member_id );

		if ( $result === true ) {

			wp_send_json_success( [
				'message'   => __( 'Successfully kicked member from guild', 'gamerz-guild' ),
				'member_id' => $member_id
			] );
		} else {
			wp_send_json_error( [ 'message' => __( 'Failed to kick member', 'gamerz-guild' ) ] );
		}
	}

	/**
	 * Handle member promotion
	 */
	public function handle_promote_member() {
		if ( ! wp_verify_nonce( $_POST['nonce'], 'guild_promote_nonce' ) ) {
			wp_die( __( 'Security check failed', 'gamerz-guild' ) );
		}

		if ( ! is_user_logged_in() ) {
			wp_die( __( 'You must be logged in to manage guild members', 'gamerz-guild' ) );
		}

		$guild_id        = intval( $_POST['guild_id'] );
		$member_id       = intval( $_POST['member_id'] );
		$current_user_id = get_current_user_id();
		$guild           = new Guild();


		$user_role = $guild->get_user_role( $guild_id, $current_user_id );
		if ( $user_role !== 'leader' ) {
			wp_die( __( 'Only the guild leader can promote members', 'gamerz-guild' ) );
		}

		$member_role = $guild->get_user_role( $guild_id, $member_id );
		if ( $member_role === 'leader' ) {
			wp_die( __( 'The guild leader is already at the highest rank', 'gamerz-guild' ) );
		}

		$next_role = 'member';
		switch ( $member_role ) {
			case 'member':
				$next_role = 'officer';
				break;
			case 'officer':
				$next_role = 'leader';
				break;
		}

		update_user_meta( $member_id, "_guild_role_{$guild_id}", $next_role );

		do_action( 'gamerz_guild_member_promoted', $guild_id, $member_id, $next_role );

		wp_send_json_success( [
			'message'   => __( 'Successfully promoted member', 'gamerz-guild' ),
			'member_id' => $member_id,
			'new_role'  => $next_role
		] );
	}

	/**
	 * Handle member demotion
	 */
	public function handle_demote_member() {
		if ( ! wp_verify_nonce( $_POST['nonce'], 'guild_demote_nonce' ) ) {
			wp_die( __( 'Security check failed', 'gamerz-guild' ) );
		}

		if ( ! is_user_logged_in() ) {
			wp_die( __( 'You must be logged in to manage guild members', 'gamerz-guild' ) );
		}

		$guild_id        = intval( $_POST['guild_id'] );
		$member_id       = intval( $_POST['member_id'] );
		$current_user_id = get_current_user_id();
		$guild           = new Guild();

		$user_role = $guild->get_user_role( $guild_id, $current_user_id );
		if ( $user_role !== 'leader' ) {
			wp_die( __( 'Only the guild leader can demote members', 'gamerz-guild' ) );
		}

		$member_role = $guild->get_user_role( $guild_id, $member_id );
		if ( $member_role === 'leader' ) {
			wp_die( __( 'The guild leader cannot be demoted', 'gamerz-guild' ) );
		}

		if ( $member_role === 'member' ) {
			wp_die( __( 'The member is already at the lowest rank', 'gamerz-guild' ) );
		}

		// Determine next role
		$next_role = 'member';
		switch ( $member_role ) {
			case 'officer':
				$next_role = 'member';
				break;
			case 'leader':
				$next_role = 'officer';
				break;
		}

		update_user_meta( $member_id, "_guild_role_{$guild_id}", $next_role );

		do_action( 'gamerz_guild_member_demoted', $guild_id, $member_id, $next_role );

		wp_send_json_success( [
			'message'   => __( 'Successfully demoted member', 'gamerz-guild' ),
			'member_id' => $member_id,
			'new_role'  => $next_role
		] );
	}

	/**
	 * Get all members for a guild with their roles
	 */
	public function get_guild_members_with_roles( $guild_id ) {
		$guild              = new Guild();
		$member_ids         = $guild->get_members( $guild_id );
		$members_with_roles = [];

		foreach ( $member_ids as $member_id ) {
			$user = get_user_by( 'ID', $member_id );
			if ( $user ) {
				$role = $guild->get_user_role( $guild_id, $member_id );
				$members_with_roles[] = [
					'id'           => $member_id,
					'display_name' => $user->display_name,
					'user_login'   => $user->user_login,
					'role'         => $role,
					'join_date'    => get_user_meta( $member_id, "_guild_join_date_{$guild_id}", true ),
				];
			}
		}

		return $members_with_roles;
	}

	/**
	 * Add guild join date for a user
	 */
	public function set_join_date( $guild_id, $user_id ) {
		$join_date = date( 'Y-m-d H:i:s' );
		update_user_meta( $user_id, "_guild_join_date_{$guild_id}", $join_date );
	}

	/**
	 * Get guild join date for a user
	 */
	public function get_join_date( $guild_id, $user_id ) {
		return get_user_meta( $user_id, "_guild_join_date_{$guild_id}", true );
	}

	/**
	 * Get guild role display name
	 */
	public function get_role_display_name( $role ) {
		$roles = [
			'leader'  => __( 'Guild Leader', 'gamerz-guild' ),
			'officer' => __( 'Guild Officer', 'gamerz-guild' ),
			'member'  => __( 'Guild Member', 'gamerz-guild' ),
		];

		return isset( $roles[ $role ] ) ? $roles[ $role ] : $role;
	}

	/**
	 * Handle guild creation request
	 */
	public function handle_guild_creation() {
		if ( ! wp_verify_nonce( $_POST['nonce'], 'guild_create_nonce' ) ) {
			wp_die( __( 'Security check failed', 'gamerz-guild' ) );
		}

		if ( ! is_user_logged_in() ) {
			wp_die( __( 'You must be logged in to create a guild', 'gamerz-guild' ) );
		}

		$user_id     = get_current_user_id();
		$guild       = new Guild();
		$user_guilds = $guild->get_user_guilds( $user_id );
		
		if ( ! empty( $user_guilds ) ) {
			wp_die( __( 'You are already in a guild', 'gamerz-guild' ) );
		}

		$args = [
			'title'       => sanitize_text_field( $_POST['title'] ),
			'description' => wp_kses_post( $_POST['description'] ),
			'tagline'     => sanitize_text_field( $_POST['tagline'] ),
			'max_members' => absint( $_POST['max_members'] ),
			'creator_id'  => $user_id,
			'status'      => 'active'
		];

		if ( empty( $args['title'] ) ) {
			wp_die( __( 'Guild name is required', 'gamerz-guild' ) );
		}


		if ( $args['max_members'] < 5 || $args['max_members'] > 100 ) {
			wp_die( __( 'Maximum members must be between 5 and 100', 'gamerz-guild' ) );
		}

		$guild_id = $guild->create_guild( $args );

		if ( is_wp_error( $guild_id ) ) {
			wp_die( $guild_id->get_error_message() );
		}

		if ( class_exists( 'myCRED' ) ) {
			$mycred    = mycred();
			$xp_amount = apply_filters( 'gamerz_guild_creation_xp', 50 );
			$reference = 'guild_creation';
			$entry      = apply_filters(
				'gamerz_guild_creation_log_entry',
				sprintf( __( 'Created guild "%s"', 'gamerz-guild' ), $args['title'] ),
				$guild_id,
				$args
			);

			$mycred->add_creds(
				$reference,
				$user_id,
				$xp_amount,
				$entry,
				$guild_id,
				[],
				'gamerz_xp'
			);
		}

		wp_send_json_success( [
			'message' => __( 'Guild created successfully!', 'gamerz-guild' ),
			'guild_id' => $guild_id
		] );
	}

	/**
	 * Handle get guild members request
	 */
	public function handle_get_guild_members() {
		if ( ! wp_verify_nonce( $_POST['nonce'], 'guild_members_nonce' ) ) {
			wp_die( __( 'Security check failed', 'gamerz-guild' ) );
		}

		if ( ! is_user_logged_in() ) {
			wp_die( __( 'You must be logged in to manage guild members', 'gamerz-guild' ) );
		}

		$guild_id = intval( $_POST['guild_id'] );
		$user_id  = get_current_user_id();
		$guild    = new Guild();

		$user_role = $guild->get_user_role( $guild_id, $user_id );
		if ( $user_role !== 'leader' ) {
			wp_die( __( 'Only the guild leader can manage members', 'gamerz-guild' ) );
		}

		$members = $this->get_guild_members_with_roles( $guild_id );

		wp_send_json_success( $members );
	}

	/**
	 * Get guild details
	 */
	public function get_guild_details() {
		if ( ! wp_verify_nonce( $_POST['nonce'], 'guild_details_nonce' ) ) {
			wp_die( __( 'Security check failed', 'gamerz-guild' ) );
		}

		if ( ! is_user_logged_in() ) {
			wp_die( __( 'You must be logged in to manage guilds', 'gamerz-guild' ) );
		}

		$guild_id  = intval( $_POST['guild_id'] );
		$user_id   = get_current_user_id();
		$guild     = new Guild();
		$user_role = $guild->get_user_role( $guild_id, $user_id );

		if ( $user_role !== 'leader' ) {
			wp_die( __( 'Only the guild leader can edit guild details', 'gamerz-guild' ) );
		}

		$guild_post = get_post( $guild_id );
		if ( ! $guild_post ) {
			wp_die( __( 'Guild not found', 'gamerz-guild' ) );
		}

		$meta = [
			'_guild_tagline'     => get_post_meta( $guild_id, '_guild_tagline', true ),
			'_guild_description' => get_post_meta( $guild_id, '_guild_description', true ),
			'_guild_max_members' => get_post_meta( $guild_id, '_guild_max_members', true ),
			'_guild_creator_id'  => get_post_meta( $guild_id, '_guild_creator_id', true ),
			'_guild_status'      => get_post_meta( $guild_id, '_guild_status', true ),
		];

		$guild_data = [
			'ID'           => $guild_post->ID,
			'post_title'   => $guild_post->post_title,
			'post_content' => $guild_post->post_content,
			'post_status'  => $guild_post->post_status,
			'meta'         => $meta,
		];

		wp_send_json_success( $guild_data );
	}

	/**
	 * Update guild details
	 */
	public function update_guild() {
		if ( ! wp_verify_nonce( $_POST['nonce'], 'guild_update_nonce' ) ) {
			wp_die( __( 'Security check failed', 'gamerz-guild' ) );
		}

		if ( ! is_user_logged_in() ) {
			wp_die( __( 'You must be logged in to manage guilds', 'gamerz-guild' ) );
		}

		$guild_id   = intval( $_POST['guild_id'] );
		$user_id    = get_current_user_id();
		$guild      = new Guild();
		$user_role  = $guild->get_user_role( $guild_id, $user_id );
		if ( $user_role !== 'leader' ) {
			wp_die( __( 'Only the guild leader can update guild details', 'gamerz-guild' ) );
		}

		$update_data = [
			'ID'           => $guild_id,
			'post_title'   => sanitize_text_field( $_POST['title'] ),
			'post_content' => wp_kses_post( $_POST['description'] ),
		];

		if ( empty( $update_data['post_title'] ) ) {
			wp_die( __( 'Guild name is required', 'gamerz-guild' ) );
		}

		$updated = wp_update_post( $update_data );

		if ( is_wp_error( $updated ) ) {
			wp_die( $updated->get_error_message() );
		}

		// Update meta fields
		if ( isset( $_POST['tagline'] ) ) {
			update_post_meta( $guild_id, '_guild_tagline', sanitize_text_field( $_POST['tagline'] ) );
		}

		if ( isset( $_POST['description'] ) ) {
			update_post_meta( $guild_id, '_guild_description', wp_kses_post( $_POST['description'] ) );
		}

		if ( isset( $_POST['max_members'] ) ) {
			$max_members = absint( $_POST['max_members'] );
			if ( $max_members < 5 || $max_members > 100 ) {
				$max_members = 20; 
			}
			update_post_meta( $guild_id, '_guild_max_members', $max_members );
		}

		wp_send_json_success( [
			'message'  => __( 'Guild updated successfully!', 'gamerz-guild' ),
			'guild_id' => $guild_id
		] );
	}

	public function handle_not_logged_in() {
		wp_die( __( 'You must be logged in to perform this action.', 'gamerz-guild' ) );
	}
}