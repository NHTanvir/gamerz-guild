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
	 * Get the myCred reference
	 */
	private function get_mycred() {
		if ( function_exists( 'mycred' ) ) {
			return mycred();
		}
		return false;
	}

	/**
	 * Award XP for daily login
	 */
	public function award_daily_login( $user_login, $user ) {
		$mycred = $this->get_mycred();
		if ( ! $mycred ) {
			return;
		}

		// Check if user has already logged in today
		$log_entry = $mycred->get_log(
			[
				'user_id' => $user->ID,
				'reference' => 'daily_login',
				'date' => [
					'date' => date( 'Y-m-d' ),
					'compare' => '='
				]
			]
		);

		// Only award if not already awarded today
		if ( empty( $log_entry ) ) {
			$xp_amount = apply_filters( 'gamerz_daily_login_xp', 5 );
			$mycred->add_creds(
				'daily_login',
				$user->ID,
				$xp_amount,
				__( 'Daily login bonus', 'gamerz-guild' ),
				0,
				[],
				$this->log_type
			);
		}
	}

	/**
	 * Award XP for new forum topic
	 */
	public function award_new_topic( $topic_id, $forum_id ) {
		$mycred = $this->get_mycred();
		if ( ! $mycred ) {
			return;
		}

		$user_id = bbp_get_topic_author_id( $topic_id );
		$xp_amount = apply_filters( 'gamerz_new_topic_xp', 8 );
		
		$mycred->add_creds(
			'new_topic',
			$user_id,
			$xp_amount,
			__( 'New forum topic created', 'gamerz-guild' ),
			$topic_id,
			[],
			$this->log_type
		);
	}

	/**
	 * Award XP for new forum reply
	 */
	public function award_new_reply( $reply_id, $topic_id ) {
		$mycred = $this->get_mycred();
		if ( ! $mycred ) {
			return;
		}

		$user_id = bbp_get_reply_author_id( $reply_id );
		$xp_amount = apply_filters( 'gamerz_new_reply_xp', 5 );
		
		$mycred->add_creds(
			'new_reply',
			$user_id,
			$xp_amount,
			__( 'New forum reply posted', 'gamerz-guild' ),
			$reply_id,
			[],
			$this->log_type
		);
	}

	/**
	 * Award XP for adding a friend
	 */
	public function award_new_friend( $user_id, $friend_id ) {
		$mycred = $this->get_mycred();
		if ( ! $mycred ) {
			return;
		}

		$xp_amount = apply_filters( 'gamerz_new_friend_xp', 2 );
		
		// Award to the user who sent the friend request
		$mycred->add_creds(
			'new_friend',
			$user_id,
			$xp_amount,
			__( 'Added a new friend', 'gamerz-guild' ),
			$friend_id,
			[],
			$this->log_type
		);
	}

	/**
	 * Award XP for liking an activity
	 */
	public function award_activity_like( $activity_id, $user_id ) {
		$mycred = $this->get_mycred();
		if ( ! $mycred ) {
			return;
		}

		// Get the activity author
		$activity = new \BP_Activity_Activity( $activity_id );
		if ( ! $activity->id ) {
			return;
		}

		$xp_amount = apply_filters( 'gamerz_activity_like_xp', 1 );
		
		// Award to the activity author (not the person who liked)
		$mycred->add_creds(
			'activity_like',
			$activity->user_id,
			$xp_amount,
			__( 'Activity liked by another member', 'gamerz-guild' ),
			$activity_id,
			[],
			$this->log_type
		);
	}

	/**
	 * Award XP for liking a comment
	 */
	public function award_comment_like( $comment_id, $user_id ) {
		$mycred = $this->get_mycred();
		if ( ! $mycred ) {
			return;
		}

		$xp_amount = apply_filters( 'gamerz_comment_like_xp', 1 );
		
		// Award to the comment author (not the person who liked)
		// Assuming a standard WordPress comment structure
		$comment = get_comment( $comment_id );
		if ( ! $comment ) {
			return;
		}

		$mycred->add_creds(
			'comment_like',
			$comment->user_id,
			$xp_amount,
			__( 'Comment liked by another member', 'gamerz-guild' ),
			$comment_id,
			[],
			$this->log_type
		);
	}

	/**
	 * Award XP for creating a guild
	 */
	public function award_guild_creation( $guild_id, $args ) {
		$mycred = $this->get_mycred();
		if ( ! $mycred ) {
			return;
		}

		$xp_amount = apply_filters( 'gamerz_guild_creation_xp', 50 );
		
		$mycred->add_creds(
			'guild_creation',
			$args['creator_id'],
			$xp_amount,
			__( 'Created a guild', 'gamerz-guild' ),
			$guild_id,
			[],
			$this->log_type
		);
	}

	/**
	 * Award XP for joining a guild
	 */
	public function award_guild_join( $guild_id, $user_id, $role ) {
		$mycred = $this->get_mycred();
		if ( ! $mycred ) {
			return;
		}

		$xp_amount = apply_filters( 'gamerz_guild_join_xp', 10 );
		
		$mycred->add_creds(
			'guild_join',
			$user_id,
			$xp_amount,
			__( 'Joined a guild', 'gamerz-guild' ),
			$guild_id,
			[],
			$this->log_type
		);
	}

	/**
	 * Award XP for participating in an event
	 */
	public function award_event_participation( $event_id, $user_id ) {
		$mycred = $this->get_mycred();
		if ( ! $mycred ) {
			return;
		}

		$xp_amount = apply_filters( 'gamerz_event_participation_xp', 15 );
		
		$mycred->add_creds(
			'event_participation',
			$user_id,
			$xp_amount,
			sprintf( __( 'Participated in event: %s', 'gamerz-guild' ), get_the_title( $event_id ) ),
			$event_id,
			[],
			$this->log_type
		);
	}

	/**
	 * Award XP for winning an event/tournament
	 */
	public function award_event_victory( $event_id, $user_id ) {
		$mycred = $this->get_mycred();
		if ( ! $mycred ) {
			return;
		}

		$xp_amount = apply_filters( 'gamerz_event_victory_xp', 50 );
		
		$mycred->add_creds(
			'event_victory',
			$user_id,
			$xp_amount,
			sprintf( __( 'Won event: %s', 'gamerz-guild' ), get_the_title( $event_id ) ),
			$event_id,
			[],
			$this->log_type
		);
	}

	/**
	 * Award XP for submitting content
	 */
	public function award_content_submission( $post_id, $user_id ) {
		$mycred = $this->get_mycred();
		if ( ! $mycred ) {
			return;
		}

		$xp_amount = apply_filters( 'gamerz_content_submission_xp', 20 );
		
		$mycred->add_creds(
			'content_submission',
			$user_id,
			$xp_amount,
			sprintf( __( 'Submitted content: %s', 'gamerz-guild' ), get_the_title( $post_id ) ),
			$post_id,
			[],
			$this->log_type
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

		return $mycred->get_users_cred( $user_id, $this->log_type );
	}

	/**
	 * Get all XP logs for a user
	 */
	public function get_user_xp_log( $user_id, $limit = 50 ) {
		$mycred = $this->get_mycred();
		if ( ! $mycred ) {
			return [];
		}

		return $mycred->get_log(
			[
				'user_id' => $user_id,
				'ctype' => $this->log_type,
				'limit' => $limit
			]
		);
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
		$mycred = $this->get_mycred();
		if ( ! $mycred ) {
			return true;
		}

		// Get today's date
		$today = date( 'Y-m-d' );

		$log_entries = $mycred->get_log(
			[
				'user_id' => $user_id,
				'reference' => $action,
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