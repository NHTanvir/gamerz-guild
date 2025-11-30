<?php
/**
 * XP_System class
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
 * @subpackage XP_System
 * @author NH Tanvir <hi@tanvir.io>
 */
class XP_System {

	public $log_type = 'gamerz_xp';
	
	/** 
	 * Point type to use 
	 */
	private $point_type = 'gamerz_xp';

	/**
	 * Constructor function
	 */
	public function __construct() {
		$this->init();
	}

	/**
	 * Initialize the XP system
	 */
	public function init() {
		// Check if myCred is active
		if ( ! class_exists( 'myCRED' ) ) {
			return;
		}

		// Hooks have been moved to app/XP_Hooks.php
	}

	/**
	 * Get log entries based on criteria
	 */
	private function get_log_entries( $args = [] ) {
		$mycred = $this->get_mycred();
		if ( ! $mycred ) {
			return [];
		}

		// Extract args
		$user_id = isset( $args['user_id'] ) ? (int) $args['user_id'] : 0;
		$reference = isset( $args['reference'] ) ? sanitize_text_field( $args['reference'] ) : '';
		$ctype = isset( $args['ctype'] ) ? sanitize_text_field( $args['ctype'] ) : $this->point_type;
		$date_filter = isset( $args['date'] ) ? $args['date'] : [];

		// Build query
		$where = [];
		$where[] = $mycred->log_table . '.ctype = %s';
		$params = [ $ctype ];

		if ( $user_id ) {
			$where[] = $mycred->log_table . '.user_id = %d';
			$params[] = $user_id;
		}

		if ( $reference ) {
			$where[] = $mycred->log_table . '.ref = %s';
			$params[] = $reference;
		}

		if ( ! empty( $date_filter ) && isset( $date_filter['date'], $date_filter['compare'] ) ) {
			$date = $date_filter['date'];
			$compare = $date_filter['compare'];
			switch ( $compare ) {
				case '=':
					$where[] = 'DATE(FROM_UNIXTIME(' . $mycred->log_table . '.time)) = %s';
					$params[] = $date;
					break;
				default:
					$where[] = 'DATE(FROM_UNIXTIME(' . $mycred->log_table . '.time)) ' . $compare . ' %s';
					$params[] = $date;
					break;
			}
		}

		$where_clause = implode( ' AND ', $where );

		global $wpdb;

		$query = "SELECT * FROM {$mycred->log_table} WHERE {$where_clause} ORDER BY time DESC LIMIT 100";
		$results = $wpdb->get_results( $wpdb->prepare( $query, $params ) );

		return $results;
	}

	/**
	 * Get the myCred reference
	 */
	private function get_mycred() {
		if ( function_exists( 'mycred' ) ) {
			$mycred = mycred();
			
			// Check if the gamerz_xp point type exists, otherwise use default
			$available_types = mycred_get_types();
			if ( ! isset( $available_types[$this->log_type] ) ) {
				// If 'gamerz_xp' doesn't exist, use the default point type
				$default_type_keys = array_keys($available_types);
				if ( ! empty( $default_type_keys ) ) {
					$this->point_type = $default_type_keys[0];
					error_log( "Gamerz Guild: Point type '{$this->log_type}' not found, using default: {$this->point_type}" );
				} else {
					// If no point types exist, use default
					$this->point_type = MYCRED_DEFAULT_TYPE_KEY;
					error_log( "Gamerz Guild: No point types available, using main point type" );
				}
			} else {
				$this->point_type = $this->log_type;
			}
			
			return $mycred;
		}
		return false;
	}

	/**
	 * Award XP for daily login
	 */
	public function award_daily_login( $user_login, $user ) {
		// Check if user has already logged in today
		$log_entries = $this->get_log_entries(
			[
				'user_id' => $user->ID,
				'reference' => 'daily_login',
				'ctype' => $this->point_type,
				'date' => [
					'date' => date( 'Y-m-d' ),
					'compare' => '='
				]
			]
		);

		// Only award if not already awarded today
		if ( empty( $log_entries ) ) {
			$xp_amount = apply_filters( 'gamerz_daily_login_xp', 5 );
			mycred_add(
				'daily_login',
				$user->ID,
				$xp_amount,
				__( 'Daily login bonus', 'gamerz-guild' ),
				0,
				[],
				$this->point_type
			);
		}
	}

	/**
	 * Award XP for new forum topic
	 */
	public function award_new_topic( $topic_id, $forum_id ) {
		$user_id = bbp_get_topic_author_id( $topic_id );
		$xp_amount = apply_filters( 'gamerz_new_topic_xp', 8 );
		
		mycred_add(
			'new_topic',
			$user_id,
			$xp_amount,
			__( 'New forum topic created', 'gamerz-guild' ),
			$topic_id,
			[],
			$this->point_type
		);
	}

	/**
	 * Award XP for new forum reply
	 */
	public function award_new_reply( $reply_id, $topic_id ) {
		$user_id = bbp_get_reply_author_id( $reply_id );
		$xp_amount = apply_filters( 'gamerz_new_reply_xp', 5 );
		
		mycred_add(
			'new_reply',
			$user_id,
			$xp_amount,
			__( 'New forum reply posted', 'gamerz-guild' ),
			$reply_id,
			[],
			$this->point_type
		);
	}

	/**
	 * Award XP for adding a friend
	 */
	public function award_new_friend( $user_id, $friend_id ) {
		$xp_amount = apply_filters( 'gamerz_new_friend_xp', 2 );
		
		// Award to the user who sent the friend request
		mycred_add(
			'new_friend',
			$user_id,
			$xp_amount,
			__( 'Added a new friend', 'gamerz-guild' ),
			$friend_id,
			[],
			$this->point_type
		);
	}

	/**
	 * Award XP for liking an activity
	 */
	public function award_activity_like( $activity_id, $user_id ) {
		// Get the activity author
		$activity = new \BP_Activity_Activity( $activity_id );
		if ( ! $activity->id ) {
			return;
		}

		$xp_amount = apply_filters( 'gamerz_activity_like_xp', 1 );
		
		// Award to the activity author (not the person who liked)
		mycred_add(
			'activity_like',
			$activity->user_id,
			$xp_amount,
			__( 'Activity liked by another member', 'gamerz-guild' ),
			$activity_id,
			[],
			$this->point_type
		);
	}

	/**
	 * Award XP for liking a comment
	 */
	public function award_comment_like( $comment_id, $user_id ) {
		$xp_amount = apply_filters( 'gamerz_comment_like_xp', 1 );
		
		// Award to the comment author (not the person who liked)
		// Assuming a standard WordPress comment structure
		$comment = get_comment( $comment_id );
		if ( ! $comment ) {
			return;
		}

		mycred_add(
			'comment_like',
			$comment->user_id,
			$xp_amount,
			__( 'Comment liked by another member', 'gamerz-guild' ),
			$comment_id,
			[],
			$this->point_type
		);
	}

	/**
	 * Award XP for creating a guild
	 */
	public function award_guild_creation( $guild_id, $args ) {
		$xp_amount = apply_filters( 'gamerz_guild_creation_xp', 50 );
		
		mycred_add(
			'guild_creation',
			$args['creator_id'],
			$xp_amount,
			__( 'Created a guild', 'gamerz-guild' ),
			$guild_id,
			[],
			$this->point_type
		);
	}

	/**
	 * Award XP for joining a guild
	 */
	public function award_guild_join( $guild_id, $user_id, $role ) {
		$xp_amount = apply_filters( 'gamerz_guild_join_xp', 10 );
		
		mycred_add(
			'guild_join',
			$user_id,
			$xp_amount,
			__( 'Joined a guild', 'gamerz-guild' ),
			$guild_id,
			[],
			$this->point_type
		);
	}

	/**
	 * Award XP for participating in an event
	 */
	public function award_event_participation( $event_id, $user_id ) {
		$xp_amount = apply_filters( 'gamerz_event_participation_xp', 15 );
		
		mycred_add(
			'event_participation',
			$user_id,
			$xp_amount,
			sprintf( __( 'Participated in event: %s', 'gamerz-guild' ), get_the_title( $event_id ) ),
			$event_id,
			[],
			$this->point_type
		);
	}

	/**
	 * Award XP for winning an event/tournament
	 */
	public function award_event_victory( $event_id, $user_id ) {
		$xp_amount = apply_filters( 'gamerz_event_victory_xp', 50 );
		
		mycred_add(
			'event_victory',
			$user_id,
			$xp_amount,
			sprintf( __( 'Won event: %s', 'gamerz-guild' ), get_the_title( $event_id ) ),
			$event_id,
			[],
			$this->point_type
		);
	}

	/**
	 * Award XP for submitting content
	 */
	public function award_content_submission( $post_id, $user_id ) {
		$xp_amount = apply_filters( 'gamerz_content_submission_xp', 20 );
		
		mycred_add(
			'content_submission',
			$user_id,
			$xp_amount,
			sprintf( __( 'Submitted content: %s', 'gamerz-guild' ), get_the_title( $post_id ) ),
			$post_id,
			[],
			$this->point_type
		);
	}

	/**
	 * Get a user's total XP
	 */
	public function get_user_xp( $user_id ) {
		$mycred = $this->get_mycred();
		if ( ! $mycred ) {
			return 0;
		}

		// Use the proper myCRED function to get user's points
		if ( function_exists( 'mycred_get_users_balance' ) ) {
			return mycred_get_users_balance( $user_id, $this->point_type );
		} else {
			// Fallback to the direct method if the function doesn't exist
			return $mycred->get_users_balance( $user_id, $this->point_type );
		}
	}

	/**
	 * Get all XP logs for a user
	 */
	public function get_user_xp_log( $user_id, $limit = 50 ) {
		$logs = $this->get_log_entries(
			[
				'user_id' => $user_id,
				'ctype' => $this->point_type,
			]
		);
		
		// Filter to only return requested number of results
		$logs = array_slice($logs, 0, $limit);
		
		// Convert the result to the expected format
		$result = [];
		foreach ( $logs as $log ) {
			$result[] = [
				'user_id' => $log->user_id,
				'cred' => $log->creds,
				'ref' => $log->ref,
				'ref_id' => $log->ref_id,
				'log_entry' => $log->entry,
				'time' => $log->time,
			];
		}
		
		return $result;
	}

	/**
	 * Get XP for a specific action
	 */
	public function get_action_xp( $action ) {
		$xp_values = [
			'daily_login' => 5,
			'new_topic' => 8,
			'new_reply' => 5,
			'new_friend' => 2,
			'activity_like' => 1,
			'comment_like' => 1,
			'guild_creation' => 50,
			'guild_join' => 10,
			'event_participation' => 15,
			'event_victory' => 50,
			'content_submission' => 20,
		];

		return apply_filters( 'gamerz_xp_' . $action, isset( $xp_values[ $action ] ) ? $xp_values[ $action ] : 0 );
	}

	/**
	 * Check if a user can earn XP for an action today (for daily caps)
	 */
	public function check_daily_cap( $user_id, $action ) {

		// Get today's date
		$today = date( 'Y-m-d' );

		$log_entries = $this->get_log_entries(
			[
				'user_id' => $user_id,
				'reference' => $action,
				'ctype' => $this->point_type,
				'date' => [
					'date' => $today,
					'compare' => '='
				]
			]
		);

		// Check limits based on action
		$limits = apply_filters( 'gamerz_daily_action_limits', [
			'new_reply' => 5, // Max 5 replies per day
			'new_topic' => 2, // Max 2 topics per day
		] );

		if ( isset( $limits[ $action ] ) ) {
			$max = $limits[ $action ];
			return count( $log_entries ) < $max;
		}

		return true;
	}
}