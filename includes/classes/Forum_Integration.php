<?php
/**
 * Forum_Integration class for bbPress
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
 * @subpackage Forum_Integration
 * @author NH Tanvir <hi@tanvir.io>
 */
class Forum_Integration {

	/**
	 * Constructor function
	 */
	public function __construct() {
		$this->init();
	}

	/**
	 * Initialize the forum integration
	 */
	public function init() {
		// Check if bbPress is active
		if ( ! class_exists( 'bbPress' ) ) {
			return;
		}

		// Hooks have been moved to app/Forum_Hooks.php
	}

	/**
	 * Display rank with username
	 */
	public function display_rank_with_username( $name, $user_id ) {
		$rank_system = new Rank_System();
		$rank = $rank_system->get_user_rank( $user_id );
		
		if ( $rank ) {
			return $name . ' <span class="gamerz-rank-badge" style="font-size: 0.8em; color: #888;">(' . esc_html( $rank['name'] ) . ')</span>';
		}
		
		return $name;
	}

	/**
	 * Display rank details below avatar
	 */
	public function display_rank_details() {
		if ( ! is_singular( [ 'forum', 'topic', 'reply' ] ) ) {
			return;
		}

		$user_id = bbp_get_reply_author_id() ?: bbp_get_topic_author_id();
		if ( ! $user_id ) {
			return;
		}

		$rank_system = new Rank_System();
		$rank = $rank_system->get_user_rank( $user_id );
		$xp_system = new XP_System();
		$user_xp = $xp_system->get_user_xp( $user_id );
		$next_rank = $rank_system->get_next_rank( $user_xp );
		
		if ( $rank ) {
			echo '<div class="gamerz-forum-rank" style="margin-top: 5px; font-size: 0.85em; color: #666;">';
			echo '<span class="gamerz-rank-name" style="display: block; margin-bottom: 2px; font-weight: bold; color: #333;">' . esc_html( $rank['name'] ) . '</span>';
			
			if ( $next_rank ) {
				$xp_needed = $next_rank['threshold'] - $user_xp;
				echo '<span class="gamerz-xp-progress" style="display: block; font-size: 0.8em;">';
				echo sprintf( 
					__( '%d / %d XP (%d more needed)', 'gamerz-guild' ), 
					$user_xp, 
					$next_rank['threshold'], 
					$xp_needed 
				);
				echo '</span>';
				
				// Progress bar
				$total_range = $next_rank['threshold'] - $rank['threshold'];
				$current_progress = $user_xp - $rank['threshold'];
				$progress_percent = $total_range > 0 ? ( $current_progress / $total_range ) * 100 : 0;
				
				echo '<div class="gamerz-xp-bar" style="height: 4px; background: #ddd; border-radius: 2px; margin: 3px 0; overflow: hidden;">';
				echo '<div class="gamerz-xp-fill" style="height: 100%; width: ' . esc_attr( $progress_percent ) . '%; background: #0073aa;"></div>';
				echo '</div>';
			} else {
				echo '<span class="gamerz-xp-total" style="display: block; font-size: 0.8em;">' . sprintf( __( '%d XP (Max Rank)', 'gamerz-guild' ), $user_xp ) . '</span>';
			}
			
			echo '</div>';
		}
	}

	/**
	 * Add XP info to user profile
	 */
	public function add_xp_to_profile() {
		$display_user = bbp_get_displayed_user();
		if ( ! $display_user ) {
			return;
		}

		$user_id = $display_user->ID;
		$rank_system = new Rank_System();
		$rank = $rank_system->get_user_rank( $user_id );
		$xp_system = new XP_System();
		$user_xp = $xp_system->get_user_xp( $user_id );
		$next_rank = $rank_system->get_next_rank( $user_xp );
		
		if ( $rank ) {
			echo '<div class="gamerz-profile-xp-info" style="background: #f9f9f9; padding: 15px; border-radius: 5px; margin: 15px 0; border-left: 4px solid #0073aa;">';
			echo '<h4 style="margin-top: 0; color: #333;">' . __( 'Gamification Profile', 'gamerz-guild' ) . '</h4>';
			echo '<p><strong>' . __( 'Rank:', 'gamerz-guild' ) . '</strong> ' . esc_html( $rank['name'] ) . '</p>';
			echo '<p><strong>' . __( 'XP:', 'gamerz-guild' ) . '</strong> ' . esc_html( $user_xp ) . '</p>';
			
			if ( $next_rank ) {
				$xp_needed = $next_rank['threshold'] - $user_xp;
				echo '<p><strong>' . __( 'Next Rank:', 'gamerz-guild' ) . '</strong> ' . esc_html( $next_rank['name'] ) . ' (' . esc_html( $xp_needed ) . ' ' . __( 'XP needed', 'gamerz-guild' ) . ')</p>';
				
				// Progress bar
				$total_range = $next_rank['threshold'] - $rank['threshold'];
				$current_progress = $user_xp - $rank['threshold'];
				$progress_percent = $total_range > 0 ? ( $current_progress / $total_range ) * 100 : 0;
				
				echo '<div style="margin-top: 10px;">';
				echo '<div class="gamerz-profile-xp-bar" style="height: 10px; background: #ddd; border-radius: 5px; overflow: hidden;">';
				echo '<div class="gamerz-profile-xp-fill" style="height: 100%; width: ' . esc_attr( $progress_percent ) . '%; background: linear-gradient(to right, #0073aa, #00a0d2);"></div>';
				echo '</div>';
				echo '<small style="display: block; margin-top: 5px; text-align: center;">' . sprintf( 
					__( '%d%% to next rank', 'gamerz-guild' ), 
					round( $progress_percent, 1 ) 
				) . '</small>';
				echo '</div>';
			}
			
			// Show badges if available
			$badge_system = new Badge_System();
			$badges = $badge_system->get_user_badges( $user_id );
			
			if ( ! empty( $badges ) ) {
				echo '<p><strong>' . __( 'Badges:', 'gamerz-guild' ) . '</strong></p>';
				echo '<div class="gamerz-user-badges" style="margin-top: 5px;">';
				foreach ( $badges as $badge ) {
					$badge_icon = $badge_system->get_badge_icon( $badge['id'] );
					echo '<span class="gamerz-badge" style="display: inline-block; margin: 2px; padding: 5px; background: #fff; border: 1px solid #ddd; border-radius: 3px;">' . esc_html( $badge['name'] ) . '</span>';
				}
				echo '</div>';
			}
			
			echo '</div>';
		}
	}

	/**
	 * Award XP for new forum topic
	 */
	public function award_xp_for_new_topic( $topic_id, $forum_id ) {
		$xp_system = new XP_System();
		$user_id = bbp_get_topic_author_id( $topic_id );
		
		// Use the XP system's method to award XP
		$xp_system->award_new_topic( $topic_id, $forum_id );
	}

	/**
	 * Award XP for new forum reply
	 */
	public function award_xp_for_new_reply( $reply_id, $topic_id ) {
		$xp_system = new XP_System();
		$user_id = bbp_get_reply_author_id( $reply_id );
		
		// Use the XP system's method to award XP
		$xp_system->award_new_reply( $reply_id, $topic_id );
	}

	/**
	 * Get user's forum activity count
	 */
	public function get_user_forum_activity( $user_id ) {
		$topics_count = bbp_get_user_topic_count_raw( $user_id );
		$replies_count = bbp_get_user_reply_count_raw( $user_id );
		
		return [
			'topics' => $topics_count,
			'replies' => $replies_count,
			'total' => $topics_count + $replies_count,
		];
	}

	/**
	 * Get user's top-rated posts in forums
	 */
	public function get_user_top_posts( $user_id, $limit = 3 ) {
		// Get all topics and replies by user
		$topics = get_posts( [
			'post_type' => 'topic',
			'author' => $user_id,
			'post_status' => 'publish',
			'numberposts' => $limit,
			'meta_key' => '_bbp_reply_count', // Sort by reply count as proxy for popularity
			'orderby' => 'meta_value_num',
			'order' => 'DESC',
		] );

		$replies = get_posts( [
			'post_type' => 'reply',
			'author' => $user_id,
			'post_status' => 'publish',
			'numberposts' => $limit,
			'meta_key' => '_bbp_thumbs_up', // Using thumbs up meta as proxy for popularity
			'orderby' => 'meta_value_num',
			'order' => 'DESC',
		] );

		return [
			'topics' => $topics,
			'replies' => $replies,
		];
	}
}