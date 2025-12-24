<?php
/**
 * Badge_System class
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
 * @subpackage Badge_System
 * @author NH Tanvir <hi@tanvir.io>
 */
class Badge_System {

	/**
	 * Badge structure:
	 * id, name, description, icon, criteria, type, trigger_action
	 */
	public $badges = array(
		'forum_newbie' => array(
			'id'             => 'forum_newbie',
			'name'           => 'Forum Newbie',
			'description'    => 'Every legend begins with a single forum post. Congrats on speaking up in the Scrub forums for the first time!',
			'icon'           => 'dashicons dashicons-format-chat',
			'criteria'       => 'first_forum_post',
			'type'           => 'auto',
			'trigger_action' => 'bbp_new_topic,bbp_new_reply',
		),

		'social_butterfly' => array(
			'id'             => 'social_butterfly',
			'name'           => 'Social Butterfly',
			'description'    => 'Out of your cocoon and into the chatter – you\'re connecting with the community!',
			'icon'           => 'dashicons dashicons-groups',
			'criteria'       => 'social_activity',
			'type'           => 'auto',
			'trigger_action' => 'friends_add_friendship,activity_like',
		),

		'daily_grinder' => array(
			'id'             => 'daily_grinder',
			'name'           => 'Daily Grinder',
			'description'    => 'As reliable as a daily quest – you never miss a day. The grind never stops for this Scrub!',
			'icon'           => 'dashicons dashicons-clock',
			'criteria'       => 'login_streak_30',
			'type'           => 'auto',
			'trigger_action' => 'wp_login',
		),

		'helpful_scrub' => array(
			'id'             => 'helpful_scrub',
			'name'           => 'Helpful Scrub',
			'description'    => 'Always ready to help a fellow scrub – your guidance lights others\' way.',
			'icon'           => 'dashicons dashicons-star-filled',
			'criteria'       => 'helpful_activity',
			'type'           => 'manual',
			'trigger_action' => '',
		),

		'content_creator' => array(
			'id'             => 'content_creator',
			'name'           => 'Content Creator',
			'description'    => 'You\'ve added your own mark to the Scrub Gamerz saga (via video or art). Keep the content coming!',
			'icon'           => 'dashicons dashicons-format-image',
			'criteria'       => 'first_content_submission',
			'type'           => 'auto',
			'trigger_action' => 'content_submission',
		),

		'guide_guru' => array(
			'id'             => 'guide_guru',
			'name'           => 'Guide Guru',
			'description'    => 'Part storyteller, part strategist – you penned a guide to help fellow scrubs level up their game.',
			'icon'           => 'dashicons dashicons-education',
			'criteria'       => 'guide_posts_3',
			'type'           => 'auto',
			'trigger_action' => 'post_published',
		),

		'meme_master' => array(
			'id'             => 'meme_master',
			'name'           => 'Meme Master',
			'description'    => 'Your meme game is strong – laughter echoes through the Scrub halls thanks to you.',
			'icon'           => 'dashicons dashicons-smiley',
			'criteria'       => 'popular_meme',
			'type'           => 'auto',
			'trigger_action' => 'post_reaction',
		),

		'bug_squisher' => array(
			'id'             => 'bug_squisher',
			'name'           => 'Bug Squisher',
			'description'    => 'Found a glitch in the Scrub Matrix and helped fix it. Thanks for making the site better!',
			'icon'           => 'dashicons dashicons-tag',
			'criteria'       => 'bug_report_fixed',
			'type'           => 'manual',
			'trigger_action' => '',
		),

		'squad_up' => array(
			'id'             => 'squad_up',
			'name'           => 'Squad Up',
			'description'    => 'Games are better with friends – you formed a squad with a fellow Scrub and ventured forth together.',
			'icon'           => 'dashicons dashicons-universal-access',
			'criteria'       => 'team_up_with_member',
			'type'           => 'auto',
			'trigger_action' => 'guild_member_added',
		),

		'event_enthusiast' => array(
			'id'             => 'event_enthusiast',
			'name'           => 'Event Enthusiast',
			'description'    => 'You\'re a regular at Scrub events – always there for the party, popcorn in hand!',
			'icon'           => 'dashicons dashicons-calendar-alt',
			'criteria'       => 'attend_5_events',
			'type'           => 'auto',
			'trigger_action' => 'tribe_events_attendee_created',
		),

		'tournament_champion' => array(
			'id'             => 'tournament_champion',
			'name'           => 'Tournament Champion',
			'description'    => 'Victory! You conquered a Scrub Gamerz tournament and emerged on top – eternal glory (and bragging rights) are yours.',
			'icon'           => 'dashicons dashicons-tickets-alt',
			'criteria'       => 'win_tournament',
			'type'           => 'manual',
			'trigger_action' => '',
		),

		'party_starter' => array(
			'id'             => 'party_starter',
			'name'           => 'Party Starter',
			'description'    => 'DJ of the Scrub party – you stepped up and hosted an event, getting everyone together for fun.',
			'icon'           => 'dashicons dashicons-megaphone',
			'criteria'       => 'host_event',
			'type'           => 'manual',
			'trigger_action' => '',
		),

		'lorekeeper' => array(
			'id'             => 'lorekeeper',
			'name'           => 'Lorekeeper',
			'description'    => 'Keeper of the Scrub lore – you\'ve proven your knowledge in trivia and earned the right to inscribe your name in the scrolls.',
			'icon'           => 'dashicons dashicons-book-alt',
			'criteria'       => 'trivia_winner',
			'type'           => 'manual',
			'trigger_action' => '',
		),

		'streamer' => array(
			'id'             => 'streamer',
			'name'           => 'Streamer',
			'description'    => 'Lights, camera, action! You\'ve integrated your stream with Scrub Gamerz – show them how it\'s done live.',
			'icon'           => 'dashicons dashicons-video-alt3',
			'criteria'       => 'stream_to_community',
			'type'           => 'auto',
			'trigger_action' => 'streaming_activity',
		),

		'clip_champ' => array(
			'id'             => 'clip_champ',
			'name'           => 'Clip Champ',
			'description'    => 'Montage maker extraordinaire – you\'ve contributed a ton of highlights for everyone\'s enjoyment.',
			'icon'           => 'dashicons dashicons-format-video',
			'criteria'       => 'share_10_clips',
			'type'           => 'auto',
			'trigger_action' => 'content_submission',
		),

		'streamer_month' => array(
			'id'             => 'streamer_month',
			'name'           => 'Streamer of the Month',
			'description'    => 'Spotlight\'s on you! Chosen as the Streamer of the Month for entertaining the Scrub community.',
			'icon'           => 'dashicons dashicons-format-status',
			'criteria'       => 'monthly_recognition',
			'type'           => 'manual',
			'trigger_action' => '',
		),

		'media_mogul' => array(
			'id'             => 'media_mogul',
			'name'           => 'Media Mogul',
			'description'    => 'Your content cup runneth over – videos, streams, posts… you do it all! The Scrub Gamerz media scene bows to you.',
			'icon'           => 'dashicons dashicons-desktop',
			'criteria'       => 'content_creator_50',
			'type'           => 'manual',
			'trigger_action' => '',
		),

		'recruiter' => array(
			'id'             => 'recruiter',
			'name'           => 'Recruiter',
			'description'    => 'Out there spreading the Scrub gospel – you\'ve brought new recruits into the fold.',
			'icon'           => 'dashicons dashicons-nametag',
			'criteria'       => 'recruit_3_members_to_rank2',
			'type'           => 'auto',
			'trigger_action' => 'user_registration',
		),

		'mentor' => array(
			'id'             => 'mentor',
			'name'           => 'Mentor',
			'description'    => 'From scrub to sensei – you\'ve taken a new gamer under your wing and showed them the ropes.',
			'icon'           => 'dashicons dashicons-id-alt',
			'criteria'       => 'mentor_newbie',
			'type'           => 'manual',
			'trigger_action' => '',
		),

		'peacemaker' => array(
			'id'             => 'peacemaker',
			'name'           => 'Peacemaker',
			'description'    => 'Keeps the salt level down and the GG\'s up – thank you for keeping the peace among scrubs.',
			'icon'           => 'dashicons dashicons-heart',
			'criteria'       => 'conflict_resolution',
			'type'           => 'manual',
			'trigger_action' => '',
		),

		'good_samaritan' => array(
			'id'             => 'good_samaritan',
			'name'           => 'Good Samaritan',
			'description'    => 'Your kindness radiates through the Scrub community – a true Good Samaritan recognized by all.',
			'icon'           => 'dashicons dashicons-thumbs-up',
			'criteria'       => 'positive_impact',
			'type'           => 'manual',
			'trigger_action' => '',
		),

		'og_member' => array(
			'id'             => 'og_member',
			'name'           => 'OG Member',
			'description'    => 'An OG Scrub Gamer – you were here when it all began (and probably have the embarrassing stories to prove it).',
			'icon'           => 'dashicons dashicons-palmtree',
			'criteria'       => 'founding_member',
			'type'           => 'manual',
			'trigger_action' => '',
		)
	];

	/**
	 * Constructor function
	 */
	public function __construct() {
		$this->init();
	}

	/**
	 * Initialize the badge system
	 */
	public function init() {
	}

	/**
	 * Check if user earned Forum Newbie badge
	 */
	public function check_forum_newbie( $post_id, $user_id = null ) {
		if ( ! $user_id ) {
			if ( function_exists( 'bbp_get_reply_author_id' ) && get_post_type( $post_id ) === 'reply' ) {
				$user_id = bbp_get_reply_author_id( $post_id );
			} elseif ( function_exists( 'bbp_get_topic_author_id' ) && get_post_type( $post_id ) === 'topic' ) {
				$user_id = bbp_get_topic_author_id( $post_id );
			}
		}

		if ( $this->user_has_badge( $user_id, 'forum_newbie' ) ) {
			return;
		}

		$this->award_badge( $user_id, 'forum_newbie' );
	}

	/**
	 * Check if user earned Social Butterfly badge
	 */
	public function check_social_butterfly( $item_id, $user_id = null ) {
		// This badge is earned by either making 5 friends or getting 20 likes on posts
		$follower_count = 0;
		$like_count = 0;

		// Check friend count if on a BuddyPress site
		if ( function_exists( 'friends_check_is_friend' ) ) {
			// Get friend count for the user
			$friend_ids = \BP_Friends_Friendship::get_friends( $user_id, 'all', 'DESC', 0, 0, true );
			$follower_count = count( $friend_ids );
		}

		// Check like count (this is a simplified check - would need more complex logic in practice)
		$like_count = get_user_meta( $user_id, '_gamerz_like_count', true );
		if ( ! $like_count ) {
			$like_count = 0;
		}

		// If user has 5 friends OR 20 likes, award badge
		if ( $follower_count >= 5 || $like_count >= 20 ) {
			if ( ! $this->user_has_badge( $user_id, 'social_butterfly' ) ) {
				$this->award_badge( $user_id, 'social_butterfly' );
			}
		}
	}

	/**
	 * Check if user earned Squad Up badge
	 */
	public function check_squad_up( $guild_id, $user_id, $role ) {
		// For this badge, we need to track when a user groups with another Scrub gamer
		// This could be tracked via guild joining, events, etc.
		// Simplified implementation:
		
		// Check if user already has this badge
		if ( $this->user_has_badge( $user_id, 'squad_up' ) ) {
			return;
		}

		// Award the badge
		$this->award_badge( $user_id, 'squad_up' );
	}

	/**
	 * Check if user earned Event Enthusiast badge
	 */
	public function check_event_enthusiast( $attendee_id, $event_id ) {
		$user_id = get_post_meta( $attendee_id, '_tribe_tickets_attendee_user_id', true );
		if ( ! $user_id ) {
			return;
		}

		// Get event attendance count
		$attendance_count = $this->get_event_attendance_count( $user_id );
		
		// Award badge if attended 5 events
		if ( $attendance_count >= 5 && ! $this->user_has_badge( $user_id, 'event_enthusiast' ) ) {
			$this->award_badge( $user_id, 'event_enthusiast' );
		}
	}

	/**
	 * Get event attendance count for a user
	 */
	private function get_event_attendance_count( $user_id ) {
		// This is a simplified approach - in reality, you'd query The Events Calendar data
		$count = get_user_meta( $user_id, '_gamerz_event_attendance_count', true );
		if ( ! $count ) {
			$count = 1; // First event
		} else {
			$count = intval( $count ) + 1;
		}
		update_user_meta( $user_id, '_gamerz_event_attendance_count', $count );
		return $count;
	}

	/**
	 * Check if user earned Content Creator badge
	 */
	public function check_content_creator( $post_id ) {
		$user_id = get_post_field( 'post_author', $post_id );
		
		// Check if user already has this badge
		if ( $this->user_has_badge( $user_id, 'content_creator' ) ) {
			return;
		}

		// Award the badge
		$this->award_badge( $user_id, 'content_creator' );
	}

	/**
	 * Award a badge to a user
	 */
	public function award_badge( $user_id, $badge_id ) {
		if ( ! $this->badge_exists( $badge_id ) ) {
			return false;
		}

		// Check if user already has this badge
		if ( $this->user_has_badge( $user_id, $badge_id ) ) {
			return false;
		}

		// Get user's current badges
		$user_badges = $this->get_user_badges( $user_id );
		
		// Add new badge
		$user_badges[] = [
			'id' => $badge_id,
			'awarded_at' => current_time( 'mysql' ),
			'awarded_by' => 'system',
		];

		// Update user meta
		update_user_meta( $user_id, '_gamerz_badges', $user_badges );

		// Trigger action
		do_action( 'gamerz_badge_awarded', $user_id, $badge_id );

		// Add to activity feed
		$activity = new Guild_Activity();
		if ( $activity ) {
			$badge = $this->get_badge( $badge_id );
			$activity_data = [
				'type' => 'badge_awarded',
				'user_id' => $user_id,
				'title' => sprintf( __( '%s earned the %s badge!', 'gamerz-guild' ), 
					$this->get_user_display_name( $user_id ),
					$badge['name']
				),
				'content' => $badge['description'],
				'timestamp' => current_time( 'mysql' ),
			];
		}

		return true;
	}

	/**
	 * Manually award a badge to a user (for admin/manual badges)
	 */
	public function manually_award_badge( $user_id, $badge_id, $awarded_by = null ) {
		if ( ! $awarded_by ) {
			$awarded_by = get_current_user_id();
		}

		if ( ! $this->badge_exists( $badge_id ) ) {
			return false;
		}

		// Check if user already has this badge
		if ( $this->user_has_badge( $user_id, $badge_id ) ) {
			return false;
		}

		// Get user's current badges
		$user_badges = $this->get_user_badges( $user_id );
		
		// Add new badge
		$user_badges[] = [
			'id' => $badge_id,
			'awarded_at' => current_time( 'mysql' ),
			'awarded_by' => $awarded_by,
			'manual' => true,
		];

		// Update user meta
		update_user_meta( $user_id, '_gamerz_badges', $user_badges );

		// Trigger action
		do_action( 'gamerz_badge_manually_awarded', $user_id, $badge_id, $awarded_by );

		return true;
	}

	/**
	 * Revoke a badge from a user
	 */
	public function revoke_badge( $user_id, $badge_id ) {
		$user_badges = $this->get_user_badges( $user_id );
		$filtered_badges = [];

		foreach ( $user_badges as $badge ) {
			if ( $badge['id'] !== $badge_id ) {
				$filtered_badges[] = $badge;
			}
		}

		// Update user meta
		update_user_meta( $user_id, '_gamerz_badges', $filtered_badges );

		// Trigger action
		do_action( 'gamerz_badge_revoked', $user_id, $badge_id );

		return true;
	}

	/**
	 * Get all badges for a user
	 */
	public function get_user_badges( $user_id ) {
		$user_badges = get_user_meta( $user_id, '_gamerz_badges', true );
		if ( ! is_array( $user_badges ) ) {
			return [];
		}

		$badges_with_details = [];
		foreach ( $user_badges as $user_badge ) {
			$badge_details = $this->get_badge( $user_badge['id'] );
			if ( $badge_details ) {
				$badges_with_details[] = array_merge( $user_badge, $badge_details );
			}
		}

		return $badges_with_details;
	}

	/**
	 * Check if user has a specific badge
	 */
	public function user_has_badge( $user_id, $badge_id ) {
		$user_badges = $this->get_user_badges( $user_id );
		foreach ( $user_badges as $badge ) {
			if ( $badge['id'] === $badge_id ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Get badge details
	 */
	public function get_badge( $badge_id ) {
		return isset( $this->badges[ $badge_id ] ) ? $this->badges[ $badge_id ] : null;
	}

	/**
	 * Check if badge exists
	 */
	public function badge_exists( $badge_id ) {
		return isset( $this->badges[ $badge_id ] );
	}

	/**
	 * Get all available badges
	 */
	public function get_all_badges() {
		return $this->badges;
	}

	/**
	 * Get badge icon
	 */
	public function get_badge_icon( $badge_id ) {
		$badge = $this->get_badge( $badge_id );
		return $badge ? $badge['icon'] : 'dashicons dashicons-awards';
	}

	/**
	 * Get user display name
	 */
	private function get_user_display_name( $user_id ) {
		$user = get_user_by( 'ID', $user_id );
		return $user ? $user->display_name : 'Unknown User';
	}

	/**
	 * Get badges by category
	 */
	public function get_badges_by_category( $category = 'all' ) {
		$categories = [
			'engagement_social' => [
				'forum_newbie',
				'social_butterfly',
				'daily_grinder',
				'helpful_scrub'
			],
			'content_creative' => [
				'content_creator',
				'guide_guru',
				'meme_master',
				'bug_squisher'
			],
			'event_competitive' => [
				'squad_up',
				'event_enthusiast',
				'tournament_champion',
				'party_starter',
				'lorekeeper'
			],
			'streaming_media' => [
				'streamer',
				'clip_champ',
				'streamer_month',
				'media_mogul'
			],
			'community_impact' => [
				'recruiter',
				'mentor',
				'peacemaker',
				'good_samaritan',
				'og_member'
			]
		];

		if ( $category === 'all' ) {
			return $this->badges;
		}

		if ( isset( $categories[ $category ] ) ) {
			$result = [];
			foreach ( $categories[ $category ] as $badge_id ) {
				if ( isset( $this->badges[ $badge_id ] ) ) {
					$result[ $badge_id ] = $this->badges[ $badge_id ];
				}
			}
			return $result;
		}

		return [];
	}

	/**
	 * Get user's badge count
	 */
	public function get_user_badge_count( $user_id ) {
		$user_badges = $this->get_user_badges( $user_id );
		return count( $user_badges );
	}

	/**
	 * Award Weekly Challenge badge
	 */
	public function award_weekly_challenge_badge( $user_id, $challenge_name = null ) {
		$badge_id = 'weekly_challenge';
		$badge_name = 'Weekly Challenge Winner';
		
		if ( $challenge_name ) {
			$badge_id = sanitize_title( 'challenge_' . $challenge_name );
			$badge_name = $challenge_name . ' Challenger';
		}

		// Check if user already has this badge
		if ( $this->user_has_badge( $user_id, $badge_id ) ) {
			return false;
		}

		// Add this badge to the system temporarily if it doesn't exist
		if ( ! $this->badge_exists( $badge_id ) ) {
			$this->badges[ $badge_id ] = [
				'id' => $badge_id,
				'name' => $badge_name,
				'description' => 'Awarded for completing a weekly challenge.',
				'icon' => 'dashicons dashicons-awards',
				'criteria' => 'weekly_challenge',
				'type' => 'auto',
				'trigger_action' => 'weekly_challenge_completed',
			];
		}

		// Award the badge
		$this->award_badge( $user_id, $badge_id );

		// Track total challenges completed
		$challenge_count = get_user_meta( $user_id, '_gamerz_weekly_challenges_count', true );
		if ( ! $challenge_count ) {
			$challenge_count = 1;
		} else {
			$challenge_count++;
		}
		update_user_meta( $user_id, '_gamerz_weekly_challenges_count', $challenge_count );
		
		return true;
	}
}