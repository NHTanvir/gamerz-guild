<?php
/**
 * Rank_System class
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
 * @subpackage Rank_System
 * @author NH Tanvir <hi@tanvir.io>
 */
class Rank_System {

	/**
	 * Rank structure: level, name, threshold, privileges
	 */
	public $ranks = [
		1 => [
			'id' => 1,
			'name' => 'Scrubling',
			'threshold' => 0,
			'description' => 'A newly spawned Scrub. Everyone starts as a humble Scrubling, just finding their way in the Scrubverse.',
			'privileges' => [
				'basic_community_access' => true,
				'view_forums' => true,
				'post_forums' => false,
				'custom_avatar' => false,
			]
		],
		2 => [
			'id' => 2,
			'name' => 'Scrub Recruit',
			'threshold' => 50,
			'description' => 'Has survived initiation. This rank might be nicknamed "Noob Recruit".',
			'privileges' => [
				'basic_community_access' => true,
				'view_forums' => true,
				'post_forums' => true,
				'custom_avatar' => true,
				'access_intro_forum' => true,
			]
		],
		3 => [
			'id' => 3,
			'name' => 'Scrub Scout',
			'threshold' => 100,
			'description' => 'Showing potential. At this stage the user is getting involved.',
			'privileges' => [
				'basic_community_access' => true,
				'view_forums' => true,
				'post_forums' => true,
				'create_topics' => true,
				'rsvp_events' => true,
				'discord_role' => 'Scrub Scout',
			]
		],
		4 => [
			'id' => 4,
			'name' => 'Scrub Soldier',
			'threshold' => 200,
			'description' => 'Battle-tested Scrub. Now a full-fledged soldier of the Scrub army.',
			'privileges' => [
				'basic_community_access' => true,
				'view_forums' => true,
				'post_forums' => true,
				'create_topics' => true,
				'rsvp_events' => true,
				'forum_signature' => true,
				'attach_images' => true,
				'discord_role' => 'Scrub Soldier',
			]
		],
		5 => [
			'id' => 5,
			'name' => 'Scrub Strategist',
			'threshold' => 300,
			'description' => 'A thinker among scrubs. They\'ve proven tactical prowess.',
			'privileges' => [
				'basic_community_access' => true,
				'view_forums' => true,
				'post_forums' => true,
				'create_topics' => true,
				'rsvp_events' => true,
				'forum_signature' => true,
				'attach_images' => true,
				'host_events' => true,
				'create_game_squad' => true,
				'profile_badge' => 'Strategist',
				'discord_role' => 'Scrub Strategist',
			]
		],
		6 => [
			'id' => 6,
			'name' => 'Scrub Captain',
			'threshold' => 450,
			'description' => 'Leading the scrub squad.',
			'privileges' => [
				'basic_community_access' => true,
				'view_forums' => true,
				'post_forums' => true,
				'create_topics' => true,
				'rsvp_events' => true,
				'forum_signature' => true,
				'attach_images' => true,
				'host_events' => true,
				'create_game_squad' => true,
				'moderation_lite' => true,
				'create_buddypress_group' => true,
				'discord_role' => 'Scrub Captain',
			]
		],
		7 => [
			'id' => 7,
			'name' => 'Scrub Champion',
			'threshold' => 600,
			'description' => 'An elite champion (still a scrub at heart). They stand out as a top contributor.',
			'privileges' => [
				'basic_community_access' => true,
				'view_forums' => true,
				'post_forums' => true,
				'create_topics' => true,
				'rsvp_events' => true,
				'forum_signature' => true,
				'attach_images' => true,
				'host_events' => true,
				'create_game_squad' => true,
				'moderation_lite' => true,
				'create_buddypress_group' => true,
				'custom_profile_banner' => true,
				'animated_avatar_frame' => true,
				'one_time_discount' => '5% merch discount',
				'discord_role' => 'Scrub Champion',
				'discord_access' => 'Champion\'s Lounge',
			]
		],
		8 => [
			'id' => 8,
			'name' => 'Guild Officer',
			'threshold' => 800,
			'description' => 'Respected Scrub leadership. Promoted by the Scrub High Council.',
			'privileges' => [
				'basic_community_access' => true,
				'view_forums' => true,
				'post_forums' => true,
				'create_topics' => true,
				'rsvp_events' => true,
				'forum_signature' => true,
				'attach_images' => true,
				'host_events' => true,
				'create_game_squad' => true,
				'moderation_lite' => true,
				'create_buddypress_group' => true,
				'custom_profile_banner' => true,
				'animated_avatar_frame' => true,
				'start_forum_polls' => true,
				'special_invitation_links' => true,
				'special_profile_theme' => true,
				'discord_role' => 'Guild Officer',
			]
		],
		9 => [
			'id' => 9,
			'name' => 'Scrub Sage',
			'threshold' => 1100,
			'description' => 'Wise and experienced (the scrub who has "seen some stuff").',
			'privileges' => [
				'basic_community_access' => true,
				'view_forums' => true,
				'post_forums' => true,
				'create_topics' => true,
				'rsvp_events' => true,
				'forum_signature' => true,
				'attach_images' => true,
				'host_events' => true,
				'create_game_squad' => true,
				'moderation_lite' => true,
				'create_buddypress_group' => true,
				'custom_profile_banner' => true,
				'animated_avatar_frame' => true,
				'start_forum_polls' => true,
				'special_invitation_links' => true,
				'special_profile_theme' => true,
				'mentor_status' => true,
				'profile_badge' => 'Sage',
				'secret_lore_forum' => true,
				'discord_role' => 'Scrub Sage',
				'discord_access' => 'Sage Emoji Set',
			]
		],
		10 => [
			'id' => 10,
			'name' => 'Scrub Warlord',
			'threshold' => 1400,
			'description' => 'Wields great influence in battle and banter.',
			'privileges' => [
				'basic_community_access' => true,
				'view_forums' => true,
				'post_forums' => true,
				'create_topics' => true,
				'rsvp_events' => true,
				'forum_signature' => true,
				'attach_images' => true,
				'host_events' => true,
				'create_game_squad' => true,
				'moderation_lite' => true,
				'create_buddypress_group' => true,
				'custom_profile_banner' => true,
				'animated_avatar_frame' => true,
				'start_forum_polls' => true,
				'special_invitation_links' => true,
				'special_profile_theme' => true,
				'mentor_status' => true,
				'secret_lore_forum' => true,
				'priority_support' => true,
				'propose_challenges' => true,
				'custom_title' => true,
				'discord_role' => 'Scrub Warlord',
				'discord_color' => 'red',
			]
		],
		11 => [
			'id' => 11,
			'name' => 'Meme Master',
			'threshold' => 1800,
			'description' => 'Legendary for wit and memes. True mastery includes meme mastery.',
			'privileges' => [
				'basic_community_access' => true,
				'view_forums' => true,
				'post_forums' => true,
				'create_topics' => true,
				'rsvp_events' => true,
				'forum_signature' => true,
				'attach_images' => true,
				'host_events' => true,
				'create_game_squad' => true,
				'moderation_lite' => true,
				'create_buddypress_group' => true,
				'custom_profile_banner' => true,
				'animated_avatar_frame' => true,
				'start_forum_polls' => true,
				'special_invitation_links' => true,
				'special_profile_theme' => true,
				'mentor_status' => true,
				'secret_lore_forum' => true,
				'priority_support' => true,
				'propose_challenges' => true,
				'custom_title' => true,
				'upload_custom_emojis' => true,
				'profile_trophy_case' => true,
				'hall_of_fame' => true,
				'discord_role' => 'Meme Master',
			]
		],
		12 => [
			'id' => 12,
			'name' => 'Scrub Overlord',
			'threshold' => 2300,
			'description' => 'Ruler of scrubs – nearly at mythical status.',
			'privileges' => [
				'basic_community_access' => true,
				'view_forums' => true,
				'post_forums' => true,
				'create_topics' => true,
				'rsvp_events' => true,
				'forum_signature' => true,
				'attach_images' => true,
				'host_events' => true,
				'create_game_squad' => true,
				'moderation_lite' => true,
				'create_buddypress_group' => true,
				'custom_profile_banner' => true,
				'animated_avatar_frame' => true,
				'start_forum_polls' => true,
				'special_invitation_links' => true,
				'special_profile_theme' => true,
				'mentor_status' => true,
				'secret_lore_forum' => true,
				'priority_support' => true,
				'propose_challenges' => true,
				'custom_title' => true,
				'upload_custom_emojis' => true,
				'profile_trophy_case' => true,
				'hall_of_fame' => true,
				'golden_scrub_commendation' => true,
				'free_merch' => 'sticker pack',
				'discord_role' => 'Scrub Overlord',
				'admin_voice_channel' => true,
			]
		],
		13 => [
			'id' => 13,
			'name' => 'Nova Scrub',
			'threshold' => 2900,
			'description' => 'Transcended to cosmic scrubhood.',
			'privileges' => [
				'basic_community_access' => true,
				'view_forums' => true,
				'post_forums' => true,
				'create_topics' => true,
				'rsvp_events' => true,
				'forum_signature' => true,
				'attach_images' => true,
				'host_events' => true,
				'create_game_squad' => true,
				'moderation_lite' => true,
				'create_buddypress_group' => true,
				'custom_profile_banner' => true,
				'animated_avatar_frame' => true,
				'start_forum_polls' => true,
				'special_invitation_links' => true,
				'special_profile_theme' => true,
				'mentor_status' => true,
				'secret_lore_forum' => true,
				'priority_support' => true,
				'propose_challenges' => true,
				'custom_title' => true,
				'upload_custom_emojis' => true,
				'profile_trophy_case' => true,
				'hall_of_fame' => true,
				'golden_scrub_commendation' => true,
				'free_merch' => 'sticker pack',
				'animated_profile_avatar' => true,
				'beta_tester' => true,
				'discord_role' => 'Nova Scrub',
			]
		],
		14 => [
			'id' => 14,
			'name' => 'Scrub Prime',
			'threshold' => 3600,
			'description' => 'The prime example of a Scrub – if aliens found one, it\'d be this.',
			'privileges' => [
				'basic_community_access' => true,
				'view_forums' => true,
				'post_forums' => true,
				'create_topics' => true,
				'rsvp_events' => true,
				'forum_signature' => true,
				'attach_images' => true,
				'host_events' => true,
				'create_game_squad' => true,
				'moderation_lite' => true,
				'create_buddypress_group' => true,
				'custom_profile_banner' => true,
				'animated_avatar_frame' => true,
				'start_forum_polls' => true,
				'special_invitation_links' => true,
				'special_profile_theme' => true,
				'mentor_status' => true,
				'secret_lore_forum' => true,
				'priority_support' => true,
				'propose_challenges' => true,
				'custom_title' => true,
				'upload_custom_emojis' => true,
				'profile_trophy_case' => true,
				'hall_of_fame' => true,
				'golden_scrub_commendation' => true,
				'free_merch' => 'sticker pack',
				'animated_profile_avatar' => true,
				'beta_tester' => true,
				'unique_badge' => 'Community Hero',
				'permanent_discount' => '10% merch discount',
				'discord_role' => 'Scrub Prime',
			]
		],
		15 => [
			'id' => 15,
			'name' => 'Legendary Scrub',
			'threshold' => 4500,
			'description' => 'Mythical status. The king/queen of scrubs.',
			'privileges' => [
				'basic_community_access' => true,
				'view_forums' => true,
				'post_forums' => true,
				'create_topics' => true,
				'rsvp_events' => true,
				'forum_signature' => true,
				'attach_images' => true,
				'host_events' => true,
				'create_game_squad' => true,
				'moderation_lite' => true,
				'create_buddypress_group' => true,
				'custom_profile_banner' => true,
				'animated_avatar_frame' => true,
				'start_forum_polls' => true,
				'special_invitation_links' => true,
				'special_profile_theme' => true,
				'mentor_status' => true,
				'secret_lore_forum' => true,
				'priority_support' => true,
				'propose_challenges' => true,
				'custom_title' => true,
				'upload_custom_emojis' => true,
				'profile_trophy_case' => true,
				'hall_of_fame' => true,
				'golden_scrub_commendation' => true,
				'free_merch' => 'sticker pack',
				'animated_profile_avatar' => true,
				'beta_tester' => true,
				'unique_badge' => 'Community Hero',
				'permanent_discount' => '10% merch discount',
				'unlock_all_customizations' => true,
				'personal_emoji' => true,
				'forum_flair' => true,
				'homepage_shoutout' => true,
				'discord_role' => 'Legendary Scrub',
				'discord_vip_channel' => true,
				'discord_gold_color' => true,
			]
		]
	];

	/**
	 * Constructor function
	 */
	public function __construct() {
		$this->init();
	}

	/**
	 * Initialize the rank system
	 */
	public function init() {
		// Hooks have been moved to app/Rank_Hooks.php
	}

	/**
	 * Get user's current rank based on XP
	 */
	public function get_user_rank( $user_id = null ) {
		if ( ! $user_id ) {
			$user_id = get_current_user_id();
		}

		if ( ! $user_id ) {
			return false;
		}

		$xp_system = new XP_System();
		$user_xp = $xp_system->get_user_xp( $user_id );

		// Find the highest rank the user qualifies for
		$current_rank = $this->ranks[1]; // Default to rank 1 if no XP

		foreach ( $this->ranks as $rank ) {
			if ( $user_xp >= $rank['threshold'] ) {
				$current_rank = $rank;
			} else {
				break; // Since ranks are in order, we can break once we find a rank that's too high
			}
		}

		return $current_rank;
	}

	/**
	 * Get user's rank level (number)
	 */
	public function get_user_rank_level( $user_id = null ) {
		$rank = $this->get_user_rank( $user_id );
		return $rank ? $rank['id'] : 1;
	}

	/**
	 * Get user's rank name
	 */
	public function get_user_rank_name( $user_id = null ) {
		$rank = $this->get_user_rank( $user_id );
		return $rank ? $rank['name'] : 'Scrubling';
	}

	/**
	 * Get next rank after current rank
	 */
	public function get_next_rank( $user_xp = null, $user_id = null ) {
		if ( $user_xp === null && $user_id !== null ) {
			$xp_system = new XP_System();
			$user_xp = $xp_system->get_user_xp( $user_id );
		}

		if ( $user_xp === null && $user_id === null ) {
			$user_id = get_current_user_id();
			$xp_system = new XP_System();
			$user_xp = $xp_system->get_user_xp( $user_id );
		}

		// Find current rank level
		$current_level = 1;
		foreach ( $this->ranks as $rank ) {
			if ( $user_xp >= $rank['threshold'] ) {
				$current_level = $rank['id'];
			} else {
				break;
			}
		}

		// Return the next rank if it exists
		$next_level = $current_level + 1;
		if ( isset( $this->ranks[ $next_level ] ) ) {
			return $this->ranks[ $next_level ];
		}

		return null; // User is at max rank
	}

	/**
	 * Get progress to next rank
	 */
	public function get_rank_progress( $user_id = null ) {
		if ( ! $user_id ) {
			$user_id = get_current_user_id();
		}

		$xp_system = new XP_System();
		$user_xp = $xp_system->get_user_xp( $user_id );
		$current_rank = $this->get_user_rank( $user_id );
		$next_rank = $this->get_next_rank( $user_xp, $user_id );

		$progress = [
			'current_xp' => $user_xp,
			'current_rank' => $current_rank,
			'next_rank' => $next_rank,
			'xp_needed' => 0,
			'xp_in_current_rank' => 0,
			'total_in_rank_range' => 0,
			'progress_percent' => 0,
		];

		if ( $next_rank ) {
			$progress['xp_needed'] = $next_rank['threshold'] - $user_xp;
			$progress['xp_in_current_rank'] = $user_xp - $current_rank['threshold'];
			$progress['total_in_rank_range'] = $next_rank['threshold'] - $current_rank['threshold'];
			
			if ( $progress['total_in_rank_range'] > 0 ) {
				$progress['progress_percent'] = min( 100, ( $progress['xp_in_current_rank'] / $progress['total_in_rank_range'] ) * 100 );
			}
		} else {
			// User is at max rank
			$progress['progress_percent'] = 100;
		}

		return $progress;
	}

	/**
	 * Check if a user has a specific privilege
	 */
	public function user_has_privilege( $user_id, $privilege ) {
		$rank = $this->get_user_rank( $user_id );
		return isset( $rank['privileges'][ $privilege ] ) && $rank['privileges'][ $privilege ];
	}

	/**
	 * Check rank progression when XP is updated
	 */
	public function check_rank_progression( $user_id, $new_balance, $instance ) {
		// This is called when myCred balance is updated
		// Check if the user has leveled up
		$old_rank = $this->get_user_rank_from_meta( $user_id );
		$new_rank = $this->get_user_rank( $user_id );
		
		// If the rank has changed, trigger level up
		if ( $old_rank && $new_rank && $new_rank['id'] > $old_rank['id'] ) {
			$this->process_rank_up( $user_id, $old_rank, $new_rank );
		} elseif ( ! $old_rank && $new_rank && $new_rank['id'] > 1 ) {
			// User is getting their first rank above level 1
			$rank_1 = $this->ranks[1];
			$this->process_rank_up( $user_id, $rank_1, $new_rank );
		}
		
		// Update stored rank
		$this->update_user_rank_meta( $user_id, $new_rank );
	}

	/**
	 * Process rank up for a user
	 */
	public function process_rank_up( $user_id, $old_rank, $new_rank ) {
		// Trigger rank up action
		do_action( 'gamerz_rank_up', $user_id, $old_rank, $new_rank );
		
		// Add activity log for rank up
		$activity = new Guild_Activity();
		if ( $activity ) {
			$activity_data = [
				'type' => 'rank_up',
				'user_id' => $user_id,
				'title' => sprintf( __( '%s has ranked up to %s!', 'gamerz-guild' ), 
					$this->get_user_display_name( $user_id ),
					$new_rank['name']
				),
				'content' => sprintf( __( '%s has achieved the rank of %s', 'gamerz-guild' ), 
					$this->get_user_display_name( $user_id ),
					$new_rank['name']
				),
				'timestamp' => current_time( 'mysql' ),
			];
			
			// We'll store this in user meta for now, or in a custom post type if needed
			$rank_up_log = get_user_meta( $user_id, '_gamerz_rank_ups', true );
			if ( ! is_array( $rank_up_log ) ) {
				$rank_up_log = [];
			}
			
			$rank_up_log[] = [
				'old_rank' => $old_rank,
				'new_rank' => $new_rank,
				'xp_threshold' => $new_rank['threshold'],
				'timestamp' => current_time( 'mysql' ),
			];
			
			update_user_meta( $user_id, '_gamerz_rank_ups', $rank_up_log );
		}
		
		// Award special privileges for the new rank
		$this->award_rank_privileges( $user_id, $new_rank );
	}

	/**
	 * Award privileges for a new rank
	 */
	public function award_rank_privileges( $user_id, $new_rank ) {
		// This function would handle assigning special privileges based on rank
		// For example: assigning Youzify profile themes, Discord roles, etc.
		
		// Check if Uncanny Automator is available for Discord integration
		if ( class_exists( 'Uncanny_Automator\Automator_API' ) ) {
			// Trigger Discord role update via Automator
			// This would need to be configured in the Automator settings
			do_action( 'gamerz_update_discord_role', $user_id, $new_rank['name'] );
		}
		
		// Check if Youzify is available for profile customizations
		if ( class_exists( 'Youzify' ) ) {
			// Apply profile customizations based on rank
			if ( isset( $new_rank['privileges']['special_profile_theme'] ) ) {
				// Assign special profile theme
				update_user_meta( $user_id, 'youzify_profile_theme', 'special_' . sanitize_title( $new_rank['name'] ) );
			}
		}
	}

	/**
	 * Get user rank from meta (stored value)
	 */
	private function get_user_rank_from_meta( $user_id ) {
		$rank_level = get_user_meta( $user_id, '_gamerz_current_rank', true );
		if ( $rank_level && isset( $this->ranks[ $rank_level ] ) ) {
			return $this->ranks[ $rank_level ];
		}
		return null;
	}

	/**
	 * Update user rank in meta
	 */
	private function update_user_rank_meta( $user_id, $rank ) {
		if ( $rank ) {
			update_user_meta( $user_id, '_gamerz_current_rank', $rank['id'] );
			update_user_meta( $user_id, '_gamerz_rank_name', $rank['name'] );
		}
	}

	/**
	 * Get user display name
	 */
	private function get_user_display_name( $user_id ) {
		$user = get_user_by( 'ID', $user_id );
		return $user ? $user->display_name : 'Unknown User';
	}

	/**
	 * Get all ranks
	 */
	public function get_all_ranks() {
		return $this->ranks;
	}

	/**
	 * Get rank by level
	 */
	public function get_rank_by_level( $level ) {
		return isset( $this->ranks[ $level ] ) ? $this->ranks[ $level ] : null;
	}

	/**
	 * Award OG Scrub rank (manual assignment)
	 */
	public function award_og_scrub_rank( $user_id ) {
		// This would be called manually by an admin
		// Special handling for OG Scrub rank
		update_user_meta( $user_id, '_gamerz_og_scrub', 1 );
		update_user_meta( $user_id, '_gamerz_special_rank', 'OG Scrub' );
		
		// OG Scrubs get special privileges similar to high ranks
		$activity = new Guild_Activity();
		if ( $activity ) {
			$activity_data = [
				'type' => 'special_rank_awarded',
				'user_id' => $user_id,
				'title' => sprintf( __( '%s has been awarded OG Scrub rank!', 'gamerz-guild' ), 
					$this->get_user_display_name( $user_id )
				),
				'content' => sprintf( __( '%s has been recognized as an OG Scrub - a founding member!', 'gamerz-guild' ), 
					$this->get_user_display_name( $user_id )
				),
				'timestamp' => current_time( 'mysql' ),
			];
		}
	}

	/**
	 * Check if user is OG Scrub
	 */
	public function is_og_scrub( $user_id ) {
		return (bool) get_user_meta( $user_id, '_gamerz_og_scrub', true );
	}
}