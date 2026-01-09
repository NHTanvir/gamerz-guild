<?php
/**
 * Event_Integration class for The Events Calendar
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
 * @subpackage Event_Integration
 * @author NH Tanvir <hi@tanvir.io>
 */
class Event_Integration {

	/**
	 * Constructor function
	 */
	public function __construct() {
		$this->init();
	}

	/**
	 * Initialize the event integration
	 */
	public function init() {

		if ( ! class_exists( 'Tribe__Events__Main' ) ) {
			return;
		}
	}

	/**
	 * Handle event attendance
	 */
	public function handle_event_attendance( $attendee_id, $event_id ) {
		$attendee = get_post( $attendee_id );
		if ( ! $attendee ) {
			return;
		}

		$user_id = get_post_meta( $attendee_id, '_tribe_tickets_attendee_user_id', true );
		if ( ! $user_id ) {
			return;
		}

		$xp_system = new XP_System();
		$xp_system->award_event_participation( $event_id, $user_id );
	}

	/**
	 * Handle event creation by guild members
	 */
	public function handle_event_creation( $event_id ) {}

	/**
	 * Handle event status change (useful for awarding victory XP)
	 */
	public function handle_event_status_change( $new_status, $old_status, $post ) {
		if ( $post->post_type !== 'tribe_events' ) {
			return;
		}
	}

	/**
	 * Get event participants
	 */
	public function get_event_participants( $event_id ) {

		$attendees = \Tribe__Tickets__Tickets::get_event_attendees( $event_id );
		$user_ids  = [];

		foreach ( $attendees as $attendee ) {
			if ( isset( $attendee['user_id'] ) && $attendee['user_id'] ) {
				$user_ids[] = $attendee['user_id'];
			}
		}

		return array_unique( $user_ids );
	}

	/**
	 * Create a guild event
	 */
	public function create_guild_event( $args = [] ) {

		if ( ! class_exists( 'Tribe__Events__Main' ) ) {
			return new \WP_Error( 'tec_not_active', __( 'The Events Calendar is not active', 'gamerz-guild' ) );
		}

		$defaults = [
			'title'       => '',
			'description' => '',
			'start_date'  => '',
			'end_date'    => '',
			'cost'        => '',
			'guild_id'    => 0,
			'creator_id'  => get_current_user_id(),
			'organizer_id'=> 0,
			'venue_id'    => 0,
		];

		$args = wp_parse_args( $args, $defaults );

		if ( empty( $args['title'] ) || empty( $args['start_date'] ) ) {
			return new \WP_Error( 'missing_fields', __( 'Title and start date are required', 'gamerz-guild' ) );
		}

		// Insert the event post
		$event_id = wp_insert_post( [
			'post_title' => sanitize_text_field( $args['title'] ),
			'post_content' => wp_kses_post( $args['description'] ),
			'post_status' => 'publish',
			'post_type' => 'tribe_events',
			'post_author' => $args['creator_id'],
		] );

		if ( is_wp_error( $event_id ) ) {
			return $event_id;
		}

		// Set event meta using The Events Calendar functions
		update_post_meta( $event_id, '_EventStartDate', $args['start_date'] );
		update_post_meta( $event_id, '_EventEndDate', $args['end_date'] );
		update_post_meta( $event_id, '_EventCost', sanitize_text_field( $args['cost'] ) );
		
		// If we have an organizer
		if ( $args['organizer_id'] ) {
			update_post_meta( $event_id, '_EventOrganizerID', $args['organizer_id'] );
		}
		
		// If we have a venue
		if ( $args['venue_id'] ) {
			update_post_meta( $event_id, '_EventVenueID', $args['venue_id'] );
		}

		// Add guild association
		if ( $args['guild_id'] ) {
			update_post_meta( $event_id, '_gamerz_guild_event', $args['guild_id'] );
		}

		// Trigger action
		do_action( 'gamerz_guild_event_created', $event_id, $args );

		return $event_id;
	}

	/**
	 * Check if an event is a guild event
	 */
	public function is_guild_event( $event_id ) {
		$guild_id = get_post_meta( $event_id, '_gamerz_guild_event', true );
		return ! empty( $guild_id );
	}

	/**
	 * Get guild events
	 */
	public function get_guild_events( $guild_id, $args = [] ) {
		$defaults = [
			'post_type' => 'tribe_events',
			'post_status' => 'publish',
			'meta_key' => '_gamerz_guild_event',
			'meta_value' => $guild_id,
			'posts_per_page' => -1,
			'orderby' => 'meta_value',
			'order' => 'ASC',
		];

		$query_args = wp_parse_args( $args, $defaults );
		return get_posts( $query_args );
	}

	/**
	 * Register member to guild event
	 */
	public function register_member_to_guild_event( $event_id, $user_id ) {
		// For The Events Calendar, we rely on their ticketing system
		// This is a simplified approach - in practice you'd use their API
		$attendee_data = [
			'event_id' => $event_id,
			'user_id' => $user_id,
			'product_id' => 0, // Default ticket product
			'attendee_status' => 'attending',
			'payment_status' => 'completed',
		];

		// Trigger action for when someone registers for a guild event
		do_action( 'gamerz_guild_member_registered_event', $event_id, $user_id, $attendee_data );

		// Award XP for registering
		$xp_system = new XP_System();
		$xp_system->award_event_participation( $event_id, $user_id );

		return true;
	}

	/**
	 * Mark event as completed and award rewards
	 */
	public function complete_event( $event_id, $winners = [] ) {
		// This would be called when an event is completed
		// Award additional XP to the winners
		$xp_system = new XP_System();

		foreach ( $winners as $user_id ) {
			$xp_system->award_event_victory( $event_id, $user_id );
		}
	}

	/**
	 * Get upcoming events for a guild
	 */
	public function get_upcoming_guild_events( $guild_id, $limit = 5 ) {
		$events = $this->get_guild_events( $guild_id, [
			'meta_query' => [
				[
					'key' => '_EventStartDate',
					'value' => date( 'Y-m-d H:i:s' ),
					'compare' => '>=',
				]
			],
			'posts_per_page' => $limit,
			'orderby' => 'meta_value',
			'meta_key' => '_EventStartDate',
			'order' => 'ASC',
		] );

		return $events;
	}

	/**
	 * Get past events for a guild
	 */
	public function get_past_guild_events( $guild_id, $limit = 5 ) {
		$events = $this->get_guild_events( $guild_id, [
			'meta_query' => [
				[
					'key' => '_EventEndDate',
					'value' => date( 'Y-m-d H:i:s' ),
					'compare' => '<',
				]
			],
			'posts_per_page' => $limit,
			'orderby' => 'meta_value',
			'meta_key' => '_EventEndDate',
			'order' => 'DESC',
		] );

		return $events;
	}

	/**
	 * Get guild event attendance stats
	 */
	public function get_guild_event_stats( $guild_id ) {
		$all_events = $this->get_guild_events( $guild_id );
		$upcoming_events = $this->get_upcoming_guild_events( $guild_id );
		$past_events = $this->get_past_guild_events( $guild_id );

		$total_attendees = 0;
		foreach ( $all_events as $event ) {
			$attendees = $this->get_event_participants( $event->ID );
			$total_attendees += count( $attendees );
		}

		return [
			'total_events' => count( $all_events ),
			'upcoming_events' => count( $upcoming_events ),
			'past_events' => count( $past_events ),
			'total_attendees' => $total_attendees,
		];
	}

	/**
	 * Award XP for event participation
	 */
	public function award_event_participation( $event_id, $user_id ) {
		$xp_system = new XP_System();
		$xp_system->award_event_participation( $event_id, $user_id );
	}

	/**
	 * Award XP for event victory
	 */
	public function award_event_victory( $event_id, $user_id ) {
		$xp_system = new XP_System();
		$xp_system->award_event_victory( $event_id, $user_id );
	}
}